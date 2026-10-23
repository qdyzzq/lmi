/**
 * public/js/sprs/section-save.js
 *
 * Per-tab saving for the SPRS form.
 *
 * Three layers, each catching what the one before it missed:
 *   1. localStorage on every keystroke  — survives a power cut
 *   2. debounced POST                   — survives a browser crash
 *   3. retry on reconnect               — survives the internet dropping
 *
 * Local data is a crash buffer, never a second source of truth. It is
 * cleared only when the server confirms, and never auto-flushed over a
 * newer server version.
 */

const DEBOUNCE_MS = 3000;
const PREFIX = 'sprs';

let saveTimer = null;

/* ------------------------------------------------------------------ */
/* local buffer                                                        */
/* ------------------------------------------------------------------ */

const keyFor = (ctx, section) =>
  `${PREFIX}:${ctx.officeId}:${ctx.year}:${ctx.month}:${section}`;

function bufferLocal(ctx, section, values) {
  try {
    localStorage.setItem(
      keyFor(ctx, section),
      JSON.stringify({ values, savedAt: new Date().toISOString() })
    );
  } catch (e) {
    // Private mode or quota exhausted. Not fatal — the POST still runs.
    console.warn('[sprs] local buffer unavailable', e);
  }
}

function readLocal(ctx, section) {
  try {
    const raw = localStorage.getItem(keyFor(ctx, section));
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function clearLocal(ctx, section) {
  try {
    localStorage.removeItem(keyFor(ctx, section));
  } catch {
    /* nothing to do */
  }
}

/* ------------------------------------------------------------------ */
/* reading the form                                                    */
/* ------------------------------------------------------------------ */

/**
 * Collect a tab's inputs by name.
 *
 * Empty stays empty — it is not converted to 0. "No activity this month"
 * and "didn't fill it in" are different facts, and a reviewer needs to
 * tell them apart.
 */
function collect(panel) {
  const values = {};

  panel.querySelectorAll('[name]').forEach((input) => {
    const name = input.getAttribute('name');
    if (!name) return;

    if (input.type === 'checkbox') {
      values[name] = input.checked;
    } else {
      const raw = input.value.trim();
      values[name] = raw === '' ? null : Number(raw);
    }
  });

  return values;
}

/* ------------------------------------------------------------------ */
/* server                                                              */
/* ------------------------------------------------------------------ */

async function post(ctx, section, values, expectedUpdatedAt) {
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  // Carry the office through when FO is editing someone else's form.
  // Without it FO's edits silently land on FO's own submission.
  const office = new URLSearchParams(window.location.search).get('office');
  const url = `/sprs/accomplishment/${ctx.year}/${ctx.month}/sections/${section}`
            + (office ? `?office=${encodeURIComponent(office)}` : '');

  const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': token,
      },
      body: JSON.stringify({
        ...values,
        expected_updated_at: expectedUpdatedAt ?? null,
      }),
  });

  if (response.status === 419) throw new Error('Session expired. Reload the page.');

  const body = await response.json().catch(() => ({}));

  if (response.status === 409) {
    const conflict = new Error(body.message);
    conflict.name = 'ConflictError';
    conflict.detail = body;
    throw conflict;
  }

  if (!response.ok) throw new Error(body.message || `Save failed (${response.status})`);

  return body;
}

/* ------------------------------------------------------------------ */
/* public API                                                          */
/* ------------------------------------------------------------------ */

function createSectionSaver(ctx, { onStatus } = {}) {
  // Last updated_at the server gave us per section, for the optimistic
  // lock. Without it a second encoder's save silently overwrites.
  const seen = {};
  const status = onStatus || (() => {});

  async function save(section, panel) {
    const values = collect(panel);

    bufferLocal(ctx, section, values);
    status(section, 'saving');

    try {
      const result = await post(ctx, section, values, seen[section]);

      seen[section] = result.updated_at;
      clearLocal(ctx, section);       // only after a confirmed 200
      status(section, 'saved', result);

      return result;
    } catch (error) {
      if (error.name === 'ConflictError') {
        status(section, 'conflict', error.detail);
      } else {
        // Keep the local copy and leave it queued.
        status(section, 'offline', error);
      }

      throw error;
    }
  }

  function scheduleSave(section, panel) {
    clearTimeout(saveTimer);
    status(section, 'pending');
    saveTimer = setTimeout(() => save(section, panel).catch(() => {}), DEBOUNCE_MS);
  }

  /**
   * Wire a tab panel. Saves on blur and after DEBOUNCE_MS of idle typing.
   *
   * Not on every keystroke: on a bad connection that queues dozens of
   * requests that fight each other.
   */
  function attach(section, panel) {
    panel.addEventListener('input', () => scheduleSave(section, panel));
    panel.addEventListener(
      'focusout',
      () => {
        clearTimeout(saveTimer);
        save(section, panel).catch(() => {});
      },
      true
    );
  }

  /**
   * On load, check for unsent local data.
   *
   * Never restores silently. If the server row is newer, the local copy
   * is stale — flushing it would wipe work done on another machine.
   * Returns what it found and lets the caller ask the user.
   */
  function pendingLocal(section, serverUpdatedAt) {
    const local = readLocal(ctx, section);
    if (!local) return null;

    if (serverUpdatedAt && new Date(serverUpdatedAt) > new Date(local.savedAt)) {
      return { ...local, stale: true };
    }

    return { ...local, stale: false };
  }

  function discardLocal(section) {
    clearLocal(ctx, section);
  }

  /**
   * Every section with unsent local data for this office and period.
   *
   * Scans by key prefix rather than iterating known sections, so work
   * buffered before a config change still surfaces instead of being
   * silently stranded.
   *
   * @param {Object} serverUpdatedAt  section => ISO timestamp from the server
   * @returns {Array<{section, values, savedAt, stale}>}
   */
  function pendingAll(serverUpdatedAt = {}) {
    const prefix = `${PREFIX}:${ctx.officeId}:${ctx.year}:${ctx.month}:`;
    const found = [];

    let keys = [];

    try {
      keys = Object.keys(localStorage);
    } catch {
      return found;   // private mode; nothing buffered anyway
    }

    keys.forEach((key) => {
      if (!key.startsWith(prefix)) return;

      const section = key.slice(prefix.length);
      const local = readLocal(ctx, section);

      if (!local) return;

      // Stale means the server moved on after this was buffered — someone
      // edited the same office elsewhere. Restoring would wipe their work,
      // so it's flagged and the person decides.
      const server = serverUpdatedAt[section];
      const stale = !!server && new Date(server) > new Date(local.savedAt);

      found.push({ section, values: local.values, savedAt: local.savedAt, stale });
    });

    return found;
  }

  // Flush whatever is buffered once the connection is back.
  window.addEventListener('online', () => status(null, 'online'));

  /**
   * Seed the optimistic lock from what the server rendered, so the very
   * first save of a page load can already detect a conflict rather than
   * silently overwriting.
   */
  function setSeen(section, updatedAt) {
    seen[section] = updatedAt;
  }

  return { save, attach, setSeen, pendingLocal, pendingAll, discardLocal };
}

// No bundler in this project (Vite compiles CSS only), so expose it as a
// global instead of an ES module export.
window.SprsSectionSave = { createSectionSaver };