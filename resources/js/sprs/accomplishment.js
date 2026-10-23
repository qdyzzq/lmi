/**
 * resources/js/sprs/accomplishment.js
 *
 * Renders the Accomplishment Tracker table and wires it to the save
 * endpoint.
 *
 * Reads everything from window.SPRS, which the Blade view sets from the
 * controller. Nothing about the form structure lives here — sections,
 * rows and field names all come from config/sprs_sections.php, so adding
 * a row to the form is a config change, not a JS change.
 */
(function () {
  'use strict';

  const {
    sections,     // { key: {label, description, rows|groups, dynamic} }
    monthly,      // { section: { field: { month: value } } }
    meta,         // { section: { updated_at } }
    year,         // year being viewed
    openYear,
    openMonth,    // 1-12
    years,        // options for the dropdown
    canEdit,
    canSubmit,
    status,
    officeId,
  } = window.SPRS;

  const MONTHS = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  const OPEN_INDEX = openMonth - 1;          // JS months are 0-based
  const KEYS = Object.keys(sections);

  let activeKey = KEYS[0];
  let showPast = false;
  let showFuture = false;

  const saver = window.SprsSectionSave.createSectionSaver(
    { officeId, year: openYear, month: openMonth },
    { onStatus: handleStatus }
  );

  // Seed the optimistic lock with what the server rendered, so the very
  // first save can already detect a conflict.
  Object.entries(meta).forEach(([key, m]) => {
    if (m.updated_at) saver.setSeen(key, m.updated_at);
  });

  /* ---------------------------------------------------------------- */
  /* month column state                                                */
  /* ---------------------------------------------------------------- */

  const isArchiveYear = () => year !== openYear;

  function colState(i) {
    if (isArchiveYear()) return 'locked';
    if (i === OPEN_INDEX) return canEdit ? 'open' : 'locked';
    return i < OPEN_INDEX ? 'locked' : 'future';
  }

  function visibleMonths() {
    if (isArchiveYear()) return MONTHS.map((_, i) => i);

    return MONTHS.map((_, i) => i).filter((i) => {
      // The current month is always shown, even once it's read-only.
      // colState() calls it 'locked' after submitting, which used to
      // bundle it in with January–July and hide it behind the "View
      // submitted months" toggle — so opening upcoming months made the
      // month you're actually working on disappear.
      if (i === OPEN_INDEX) return true;

      const s = colState(i);
      if (s === 'locked') return showPast;
      return showFuture;
    });
  }

  /* ---------------------------------------------------------------- */
  /* values                                                            */
  /* ---------------------------------------------------------------- */

  const valueAt = (field, monthIndex) =>
    monthly[activeKey]?.[field]?.[monthIndex + 1] ?? null;

  /**
   * Mirror what's currently typed into the in-memory store.
   *
   * Called on every keystroke, not just on a successful save. Switching
   * tabs destroys the table and rebuilds it from `monthly`, so if this
   * only ran on the server's response, anything typed in the last few
   * seconds — or anything that failed to save — would vanish from the
   * screen when you came back.
   */
  function stashTyped() {
    const tbody = document.getElementById('table-body');
    if (!tbody || sections[activeKey].dynamic) return;

    tbody.querySelectorAll('[name]').forEach((input) => {
      const field = input.getAttribute('name');
      let value;

      if (input.type === 'checkbox') {
        value = input.checked;
      } else {
        const raw = input.value.trim();
        value = raw === '' ? null : Number(raw);
      }

      monthly[activeKey] = monthly[activeKey] || {};
      monthly[activeKey][field] = monthly[activeKey][field] || {};
      monthly[activeKey][field][openMonth] = value;
    });
  }

  /** Year-to-date: every month held for this field, stored plus live. */
  function total(field) {
    const byMonth = monthly[activeKey]?.[field] || {};
    let sum = 0;
    let any = false;

    Object.entries(byMonth).forEach(([m, v]) => {
      if (Number(m) === openMonth && !isArchiveYear()) return; // live below
      if (v !== null && v !== undefined) { sum += Number(v); any = true; }
    });

    if (!isArchiveYear()) {
      const input = document.querySelector(`#table-body [name="${field}"]`);
      const live = input ? input.value.trim() : '';
      if (live !== '') { sum += Number(live) || 0; any = true; }
    }

    return any ? sum : null;
  }

  function refreshTotals() {
    document.querySelectorAll('#table-body [data-total-for]').forEach((cell) => {
      const t = total(cell.dataset.totalFor);
      cell.textContent = t === null ? '—' : t;
    });

    // Computed header rows recalculate from their children.
    document.querySelectorAll('#table-body [data-sum-of]').forEach((cell) => {
      const fields = cell.dataset.sumOf.split(',');
      const v = sumOf(fields, Number(cell.dataset.month));
      cell.textContent = v === null ? '' : v;
    });

    document.querySelectorAll('#table-body [data-ytd-sum-of]').forEach((cell) => {
      if (!cell.dataset.ytdSumOf) return;
      const v = sumOfYtd(cell.dataset.ytdSumOf.split(','));
      cell.textContent = v === null ? '' : v;
    });
  }

  /* ---------------------------------------------------------------- */
  /* rendering                                                         */
  /* ---------------------------------------------------------------- */

  const TAB_BASE = 'px-1.5 py-3 bg-transparent border-0 border-b-[3px] mr-3.5 text-[15px] font-semibold';
  const TAB_ON  = 'text-teal-900 border-teal-700';
  const TAB_OFF = 'text-ink-600 border-transparent hover:text-teal-700';

  const esc = (s) => String(s).replace(/[&<>"']/g, (c) =>
    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  function renderTabs() {
    document.getElementById('tabs').innerHTML = KEYS.map((key) => `
      <button type="button" data-tab="${key}"
        class="${TAB_BASE} ${key === activeKey ? TAB_ON : TAB_OFF}">${esc(sections[key].label)}</button>
    `).join('');
  }

  function renderCell(row, monthIndex) {
    const state = colState(monthIndex);
    const field = row.field;
    const stored = valueAt(field, monthIndex);

    if (state === 'open') {
      if (row.kind === 'boolean') {
        return `<td class="bg-teal-50 p-2.5 text-center border-t border-gray-200">
          <input type="checkbox" name="${field}" ${stored ? 'checked' : ''}
            class="w-6 h-6 accent-teal-700 cursor-pointer">
        </td>`;
      }

      // Empty stays empty, never 0 — "no activity" and "not filled in"
      // are different facts and a reviewer has to tell them apart.
      const v = stored === null ? '' : stored;

      return `<td class="bg-teal-50 p-2.5 text-center border-t border-gray-200">
        <input type="number" min="0" name="${field}" value="${v}" placeholder="–"
          class="w-[88px] h-12 text-center text-base font-bold text-teal-900 border-2 border-teal-600 rounded-lg bg-white focus:outline-none focus:ring-4 focus:ring-teal-600/15">
      </td>`;
    }

    if (state === 'locked') {
      const shown = stored === null
        ? `<span class="text-ink-400">${row.total ? '—' : '–'}</span>`
        : (row.kind === 'boolean' ? (stored ? 'Yes' : 'No') : stored);

      return `<td class="bg-amber-50 px-2.5 py-3.5 text-center border-t border-gray-200 text-ink-600">${shown}</td>`;
    }

    return `<td class="bg-gray-50 px-2.5 py-3.5 text-center border-t border-gray-200 text-ink-400"><span>·</span></td>`;
  }

  /**
   * Any row without a `field` — nothing here is ever typed into.
   *
   * Three appearances, and which one depends on where the row sits:
   *
   *   title      a heading with no group above it. Dark teal, like the
   *              group bars, because it *is* the section's title.
   *   subtitle   a heading inside a group — the group bar is already the
   *              title, so this is one level down. Pale teal.
   *   computed   an ordinary row whose value is derived, e.g. "Total
   *              Female Reached with LMI" = Female Youth + Female
   *              Non-Youth. Styled like the rows around it.
   *
   * Titles never carry a figure. A heading that summed its own children
   * would be doing two jobs at once, and on the paper form those rows
   * are labels — the only calculated rows are the ones that say so.
   *
   * Cells stay empty when there's nothing to show. An em-dash reads as
   * "no data was entered", which is misleading on a row that was never
   * an input.
   */
  function renderStaticRow(row, idxs, inGroup) {
    const isHeading  = !!row.header;
    const isSubtitle = isHeading && inGroup;
    const isTitle    = isHeading && !inGroup;

    const rowCls = isTitle
      ? 'bg-teal-700 text-white font-bold'
      : isSubtitle
        ? 'bg-teal-100 font-semibold'
        : 'font-semibold';

    const cellBg = isTitle ? 'bg-teal-700 ' : isSubtitle ? 'bg-teal-100 ' : '';
    const textCls = isTitle ? 'text-white' : 'text-teal-900';

    const labelCls = isHeading
      ? `text-left pl-5 py-2.5 ${textCls} border-t border-gray-200 text-[13.5px]`
      : 'text-left pl-9 py-3.5 text-[13.5px] text-teal-900 border-t border-gray-200';

    const cells = idxs.map((i) => {
      const v = row.sum_of ? sumOf(row.sum_of, i) : null;

      // Blank, not a dash — see the note above.
      const shown = v === null ? '' : v;
      const attrs = row.sum_of
        ? ` data-sum-of="${row.sum_of.join(',')}" data-month="${i}"`
        : '';

      return `<td class="${cellBg}px-2.5 py-2.5 text-center border-t border-gray-200 ${textCls} font-bold"${attrs}>${shown}</td>`;
    }).join('');

    const ytd = row.sum_of ? sumOfYtd(row.sum_of) : null;

    return `<tr class="${rowCls}">
      <td class="${labelCls}">${esc(row.label)}</td>
      ${cells}
      <td class="${cellBg}px-2.5 py-2.5 text-center border-t border-gray-200 ${textCls} font-bold"
          data-ytd-sum-of="${row.sum_of ? row.sum_of.join(',') : ''}">${ytd === null ? '' : ytd}</td>
    </tr>`;
  }

  /** Sum a derived row's source fields for one month, live values included. */
  function sumOf(fields, monthIndex) {
    let sum = 0;
    let any = false;

    fields.forEach((field) => {
      let v = null;

      if (monthIndex === OPEN_INDEX && !isArchiveYear()) {
        const input = document.querySelector(`#table-body [name="${field}"]`);
        if (input) {
          const raw = input.value.trim();
          v = raw === '' ? null : Number(raw);
        } else {
          v = valueAt(field, monthIndex);
        }
      } else {
        v = valueAt(field, monthIndex);
      }

      if (v !== null && v !== undefined) { sum += Number(v); any = true; }
    });

    return any ? sum : null;
  }

  /** Same, across the whole year. */
  function sumOfYtd(fields) {
    let sum = 0;
    let any = false;

    fields.forEach((field) => {
      const t = total(field);
      if (t !== null) { sum += t; any = true; }
    });

    return any ? sum : null;
  }

  function renderRow(row, idxs, inGroup = false) {
    if (!row.field) return renderStaticRow(row, idxs, inGroup);

    const cells = idxs.map((i) => renderCell(row, i)).join('');

    return `<tr>
      <td class="text-left pl-9 py-3.5 text-[13.5px] text-ink-600 border-t border-gray-200">${esc(row.label)}</td>
      ${cells}
      <td class="px-2.5 py-3.5 text-center border-t border-gray-200 text-ink-400" data-total-for="${row.field}">—</td>
    </tr>`;
  }

  function renderTable() {
    const section = sections[activeKey];
    const idxs = visibleMonths();

    // Rows here belong to one month and are user-created, so there's no
    // stable row to lay across twelve columns — an activity recorded in
    // July may simply not exist in August. Two columns instead of a grid.
    if (section.dynamic) {
      document.getElementById('table-head').innerHTML = `
        <th class="text-left pl-5 py-3.5 font-semibold text-white bg-teal-900">Activity</th>
        <th class="px-2.5 py-3.5 text-center font-semibold text-white bg-teal-700 w-[140px]">${MONTHS[OPEN_INDEX]}</th>
        <th class="px-2.5 py-3.5 w-[60px] bg-teal-900"></th>`;

      const tb = document.getElementById('table-body');
      tb.dataset.section = activeKey;
      tb.innerHTML = renderActivities();
      renderJump(section);
      return;
    }

    const head = ['<th class="text-left pl-5 py-3.5 font-semibold text-white bg-teal-900 min-w-[260px]">Item</th>'];

    idxs.forEach((i) => {
      const state = colState(i);
      const bg = state === 'open' ? 'bg-teal-700 text-base' : state === 'locked' ? 'bg-green-700' : 'bg-gray-500';
      const tag = state === 'open' ? 'Editable' : state === 'locked' ? 'Submitted' : 'Not open';
      const suffix = isArchiveYear() ? ` ${year}` : '';

      head.push(`<th class="px-2.5 py-3.5 text-center whitespace-nowrap font-semibold text-white ${bg}">
        ${MONTHS[i]}${suffix}
        <span class="block text-[9.5px] font-medium tracking-wide uppercase opacity-80 mt-0.5">${tag}</span>
      </th>`);
    });

    head.push('<th class="px-2.5 py-3.5 text-center font-semibold text-white bg-teal-900">Total</th>');
    document.getElementById('table-head').innerHTML = head.join('');

    const colspan = idxs.length + 2;
    let body = '';

    if (section.groups) {
      section.groups.forEach((g, gi) => {
        body += `<tr id="group-${gi}"><td colspan="${colspan}" class="bg-teal-700 text-white font-bold text-[13.5px] px-5 py-2.5 text-left">${esc(g.header)}</td></tr>`;
        g.rows.forEach((r) => { body += renderRow(r, idxs, true); });
      });
    } else if (section.dynamic) {
      body += renderActivities();
    } else {
      (section.rows || []).forEach((r) => { body += renderRow(r, idxs); });
    }

    const tbody = document.getElementById('table-body');
    tbody.dataset.section = activeKey;
    tbody.innerHTML = body;

    renderJump(section);
    refreshTotals();
  }

  /* ---------------------------------------------------------------- */
  /* other activities                                                  */
  /* ---------------------------------------------------------------- */

  /**
   * User-created rows: a name and a count for the open month.
   *
   * `monthly.other_activities` holds them as a list of
   * {id, activity_name, count}. New rows have no id until the server
   * assigns one.
   */
  function activities() {
    if (!Array.isArray(monthly.other_activities)) monthly.other_activities = [];
    return monthly.other_activities;
  }

  function renderActivities() {
    const rows = activities();

    if (rows.length === 0 && !canEdit) {
      return `<tr><td colspan="3" class="px-5 py-8 text-center text-ink-400 text-sm">
        No other activities were recorded for ${MONTHS[OPEN_INDEX]}.
      </td></tr>`;
    }

    let html = rows.map((row, i) => {
      if (!canEdit) {
        return `<tr>
          <td class="text-left pl-5 py-3.5 text-[14px] text-ink-900 border-t border-gray-200">${esc(row.activity_name)}</td>
          <td class="px-2.5 py-3.5 text-center border-t border-gray-200 text-ink-600">${row.count ?? '–'}</td>
          <td class="border-t border-gray-200"></td>
        </tr>`;
      }

      return `<tr>
        <td class="text-left pl-5 py-2.5 border-t border-gray-200">
          <input type="text" data-activity-name="${i}" value="${esc(row.activity_name)}"
            class="w-full h-10 px-3 text-[14px] text-ink-900 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-teal-600">
        </td>
        <td class="px-2.5 py-2.5 text-center border-t border-gray-200 bg-teal-50">
          <input type="number" min="0" data-activity-count="${i}" value="${row.count ?? ''}" placeholder="–"
            class="w-[88px] h-10 text-center text-base font-bold text-teal-900 border-2 border-teal-600 rounded-lg bg-white focus:outline-none focus:ring-4 focus:ring-teal-600/15">
        </td>
        <td class="px-2.5 py-2.5 text-center border-t border-gray-200">
          <button type="button" data-activity-remove="${i}" title="Remove"
            class="w-8 h-8 rounded-lg text-red-700 hover:bg-red-100 font-bold">&times;</button>
        </td>
      </tr>`;
    }).join('');

    if (rows.length === 0) {
      html += `<tr><td colspan="3" class="px-5 py-6 text-center text-ink-400 text-sm">
        Nothing added yet.
      </td></tr>`;
    }

    if (canEdit) {
      html += `<tr><td colspan="3" class="px-5 py-4 border-t border-gray-200">
        <button type="button" id="activity-add"
          class="flex items-center gap-2 text-teal-700 font-semibold text-sm border-2 border-dashed border-teal-600 rounded-lg px-4 py-2.5 hover:bg-teal-50">
          <span class="text-lg leading-none">+</span> Add activity
        </button>
      </td></tr>`;
    }

    return html;
  }

  /** Read the inputs back into the in-memory list. */
  function stashActivities() {
    const rows = activities();

    document.querySelectorAll('#table-body [data-activity-name]').forEach((input) => {
      const i = Number(input.dataset.activityName);
      if (rows[i]) rows[i].activity_name = input.value;
    });

    document.querySelectorAll('#table-body [data-activity-count]').forEach((input) => {
      const i = Number(input.dataset.activityCount);
      if (!rows[i]) return;
      const raw = input.value.trim();
      rows[i].count = raw === '' ? null : Number(raw);
    });
  }

  /**
   * Send the whole list. Rows the server doesn't receive are deleted, so
   * a partial payload would silently drop activities — the client always
   * sends everything.
   *
   * Unnamed rows are skipped rather than rejected: someone who clicks Add
   * and then tabs away shouldn't get an error.
   */
  async function saveActivities() {
    if (!canEdit) return;

    stashActivities();

    const payload = activities()
      .filter((r) => (r.activity_name || '').trim() !== '')
      .map((r) => ({ id: r.id ?? null, activity_name: r.activity_name.trim(), count: r.count ?? null }));

    const hint = document.getElementById('save-hint');
    hint.textContent = 'Saving…';

    try {
      const office = new URLSearchParams(window.location.search).get('office');
      const url = `/sprs/accomplishment/${openYear}/${openMonth}/activities`
                + (office ? `?office=${encodeURIComponent(office)}` : '');

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ activities: payload }),
      });

      const body = await response.json().catch(() => ({}));

      if (!response.ok) {
        hint.textContent = `Not saved — ${body.message || response.status}`;
        return;
      }

      // Copy the new ids back, but don't rebuild the table.
      //
      // Re-rendering here destroyed the row you'd just added: clicking
      // "Add activity" blurs the previous input, which fires a save, and
      // the response then replaced the list with the server's — which
      // has no blank row in it, because blanks are filtered out before
      // sending. It also threw away keyboard focus mid-typing.
      //
      // The in-memory list is already right. The only thing the server
      // knows that we don't is the id of each newly created row, and
      // both lists hold the named rows in the same order.
      const named = activities().filter((r) => (r.activity_name || '').trim() !== '');

      body.activities.forEach((serverRow, i) => {
        if (named[i]) named[i].id = serverRow.id;
      });

      hint.textContent = `All changes saved · ${new Date().toLocaleTimeString()}`;
    } catch {
      hint.textContent = 'Offline — activities not saved yet';
    }
  }

  let activityTimer = null;

  document.getElementById('table-body').addEventListener('click', (e) => {
    const add = e.target.closest('#activity-add');

    if (add) {
      // Any pending debounce belongs to the row being left. Cancelling it
      // avoids two saves racing while the table is being rebuilt.
      clearTimeout(activityTimer);

      stashActivities();
      activities().push({ id: null, activity_name: '', count: null });
      renderTable();

      const inputs = document.querySelectorAll('#table-body [data-activity-name]');
      inputs[inputs.length - 1]?.focus();
      return;
    }

    const remove = e.target.closest('[data-activity-remove]');

    if (remove) {
      stashActivities();
      const i = Number(remove.dataset.activityRemove);
      const row = activities()[i];

      if (row?.activity_name && !confirm(`Remove "${row.activity_name}"?`)) return;

      activities().splice(i, 1);
      renderTable();
      saveActivities();
    }
  });

  document.getElementById('table-body').addEventListener('input', (e) => {
    if (!e.target.matches('[data-activity-name], [data-activity-count]')) return;
    stashActivities();
    clearTimeout(activityTimer);
    document.getElementById('save-hint').textContent = 'Unsaved changes';
    activityTimer = setTimeout(saveActivities, 3000);
  });

  document.getElementById('table-body').addEventListener('focusout', (e) => {
    if (!e.target.matches('[data-activity-name], [data-activity-count]')) return;
    clearTimeout(activityTimer);
    saveActivities();
  });

  function renderJump(section) {
    const row = document.getElementById('jump-row');

    if (section.groups) {
      row.classList.remove('hidden');
      row.classList.add('flex');
      document.getElementById('jump-select').innerHTML =
        section.groups.map((g, gi) => `<option value="${gi}">${esc(g.header)}</option>`).join('');
    } else {
      row.classList.add('hidden');
      row.classList.remove('flex');
    }
  }

  function renderChrome() {
    document.getElementById('cat-desc').textContent = sections[activeKey].description || '';

    const banner = document.getElementById('archive-banner');
    const expand = document.getElementById('expand-row');
    const hint   = document.getElementById('save-hint');
    const label  = MONTHS[OPEN_INDEX];

    if (isArchiveYear()) {
      banner.classList.remove('hidden');
      banner.innerHTML = `<b class="font-bold">Viewing ${year}.</b> This year is closed — all months are shown for reference only and can't be edited.`;
      expand.classList.add('hidden');
      hint.textContent = `Viewing ${year} — read only.`;
      return;
    }

    banner.classList.add('hidden');
    expand.classList.remove('hidden');
    expand.classList.add('flex');

    if (!canEdit) {
      banner.classList.remove('hidden');
      banner.innerHTML = `<b class="font-bold">Read only.</b> This month has already been submitted, or your role doesn't file reports.`;
      hint.textContent = 'Read only.';
      return;
    }

    hint.textContent = `Only ${label} is open for entry right now.`;
  }

  function renderToggles() {
    const past = document.getElementById('btn-past');
    const future = document.getElementById('btn-future');
    const base = 'bg-transparent border-0 text-[13.5px] font-semibold underline py-1.5 ';

    const pastLabel = OPEN_INDEX > 0 ? `${MONTHS[0]}–${MONTHS[OPEN_INDEX - 1]}` : '';
    const futureLabel = OPEN_INDEX < 11 ? `${MONTHS[OPEN_INDEX + 1]}–${MONTHS[11]}` : '';

    past.className = base + (showPast ? 'text-yellow-700' : 'text-teal-700');
    future.className = base + (showFuture ? 'text-yellow-700' : 'text-teal-700');

    past.textContent = showPast ? 'Hide submitted months' : `View submitted months (${pastLabel})`;
    future.textContent = showFuture ? 'Hide upcoming months' : `View upcoming months (${futureLabel})`;

    past.classList.toggle('hidden', OPEN_INDEX === 0);
    future.classList.toggle('hidden', OPEN_INDEX === 11);
  }

  /* ---------------------------------------------------------------- */
  /* saving                                                            */
  /* ---------------------------------------------------------------- */

  let debounce = null;

  function save() {
    clearTimeout(debounce);
    debounce = null;

    const tbody = document.getElementById('table-body');

    // Other Activities has its own endpoint — a list of rows, not a fixed
    // field set — so the section save doesn't apply.
    if (sections[activeKey].dynamic) {
      saveActivities();
      return;
    }

    if (!canEdit) return;

    stashTyped();
    saver.save(activeKey, tbody).catch(() => {});
  }

  function handleStatus(section, state, detail) {
    const hint = document.getElementById('save-hint');

    if (state === 'saving')  hint.textContent = 'Saving…';
    if (state === 'pending') hint.textContent = 'Unsaved changes';

    if (state === 'saved') {
      // Autosaves are constant — a toast on each one is noise, and on tab
      // switch it looks like something happened that the person didn't ask
      // for. The hint line carries them; the toast is for the button only.
      // Keep the local copy of the data in step with the server so the
      // Total column stays right after a save.
      Object.entries(detail.values || {}).forEach(([field, v]) => {
        monthly[section] = monthly[section] || {};
        monthly[section][field] = monthly[section][field] || {};
        monthly[section][field][openMonth] = v;
      });

      // The hint line is the only save indicator now that the manual
      // button is gone. No toast — autosaves fire constantly and a
      // popup on each one is noise.
      hint.textContent = `All changes saved · ${new Date().toLocaleTimeString()}`;
    }

    if (state === 'conflict') {
      hint.textContent = 'Conflict — reload';
      alert(detail.message);
    }

    if (state === 'rejected') {
      // A real answer from the server, not a connection problem. Surfaced
      // loudly because retrying won't fix it.
      hint.textContent = `Not saved — ${detail.message}`;
      console.error('[sprs] save rejected', detail.status, detail.message);
    }

    if (state === 'offline') {
      hint.textContent = 'Offline — kept on this device, will retry';
    }
  }

  /* ---------------------------------------------------------------- */
  /* events                                                            */
  /* ---------------------------------------------------------------- */

  document.getElementById('tabs').addEventListener('click', (e) => {
    const btn = e.target.closest('[data-tab]');
    if (!btn) return;

    save();                       // flush the tab being left
    activeKey = btn.dataset.tab;
    renderTabs();
    renderChrome();
    renderTable();
  });

  const tbody = document.getElementById('table-body');

  // Delegated, because the tbody is rebuilt on every render.
  tbody.addEventListener('input', (e) => {
    // Activity rows have their own handler further up — without this the
    // two race each other and both schedule a save.
    if (e.target.matches('[data-activity-name], [data-activity-count]')) return;

    stashTyped();          // keep the screen right even if the save fails
    refreshTotals();

    // Straight to localStorage, before the debounce. Closing the tab
    // inside the next three seconds now costs nothing — the recovery
    // banner will offer it back on the next visit.
    if (canEdit && !sections[activeKey].dynamic) {
      saver.buffer(activeKey, tbody);
    }

    clearTimeout(debounce);
    document.getElementById('save-hint').textContent = 'Unsaved changes';
    debounce = setTimeout(save, 3000);
  });

  tbody.addEventListener('focusout', (e) => {
    if (e.target.matches('[data-activity-name], [data-activity-count]')) return;
    save();
  });

  /* ---------------------------------------------------------------- */
  /* submit                                                            */
  /* ---------------------------------------------------------------- */

  /**
   * Which fields are still blank, per tab.
   *
   * Read from the in-memory store rather than asking the server: it
   * already holds every tab's values, stashTyped() keeps the open tab
   * current, and a round trip would only tell us what we know.
   *
   * A blank is not an error. An office with no overseas placements
   * this month leaves those blank and is right to. The point is that
   * nobody submits a gap they didn't realise was there.
   */
  function emptyReport() {
    return Object.entries(sections)
      .map(([key, section]) => {
        // Other Activities has no fixed fields, so an empty tab isn't a
        // gap — most offices have nothing extra to report. But a row that
        // was named and then left without a number is a gap, and it's the
        // easy one to miss: you type the activity, get interrupted, and
        // the count never gets filled in.
        if (section.dynamic) {
          const rows = (monthly[key] || []).filter(
            (r) => (r.activity_name || '').trim() !== ''
          );

          const empty = rows
            .filter((r) => r.count === null || r.count === undefined || r.count === '')
            .map((r) => ({ label: r.activity_name }));

          return { key, label: section.label, total: rows.length, empty, optional: true };
        }

        const rows = section.groups
          ? section.groups.flatMap((g) => g.rows)
          : (section.rows || []);

        const fields = rows.filter((r) => r.field);

        const empty = fields.filter((r) => {
          const v = monthly[key]?.[r.field]?.[openMonth];
          return v === null || v === undefined || v === '';
        });

        return { key, label: section.label, total: fields.length, empty, optional: false };
      });
  }

  function renderSubmitModal(report) {
    // "11 of 72 fields" counts the fixed form only. Activities are
    // user-created, so folding them into that denominator would make it
    // move around for reasons nobody can see.
    const fixed = report.filter((r) => !r.optional);

    const totalFields = fixed.reduce((n, r) => n + r.total, 0);
    const totalEmpty  = fixed.reduce((n, r) => n + r.empty.length, 0);
    const filled      = totalFields - totalEmpty;

    document.getElementById('submit-modal-summary').textContent = totalEmpty === 0
      ? `All ${totalFields} fields have a value. This month will be locked once submitted.`
      : `${filled} of ${totalFields} fields have a value. ${totalEmpty} are still blank — check the list below before submitting.`;

    document.getElementById('submit-modal-body').innerHTML = report.map((r) => {
      // Nothing added at all — not a gap, just a tab this office didn't
      // need. Shown greyed so it's clear it was considered, not skipped.
      if (r.optional && r.total === 0) {
        return `<div class="flex items-center justify-between py-2.5 border-b border-hairline">
          <span class="text-[14px] font-medium text-ink-600">${esc(r.label)}</span>
          <span class="text-[12.5px] text-ink-400">None added</span>
        </div>`;
      }

      if (r.empty.length === 0) {
        return `<div class="flex items-center justify-between py-2.5 border-b border-hairline">
          <span class="text-[14px] font-medium text-ink-900">${esc(r.label)}</span>
          <span class="text-[12.5px] font-semibold text-green-800">Complete</span>
        </div>`;
      }

      const items = r.empty
        .map((f) => `<li class="text-[12.5px] text-ink-600 py-0.5">${esc(f.label)}</li>`)
        .join('');

      const count = r.optional
        ? `${r.empty.length} with no number`
        : `${r.empty.length} blank`;

      return `<details class="border-b border-hairline">
        <summary class="flex items-center justify-between py-2.5 cursor-pointer list-none">
          <span class="text-[14px] font-medium text-ink-900">${esc(r.label)}</span>
          <span class="text-[12.5px] font-semibold text-yellow-700">${count} &#9662;</span>
        </summary>
        <ul class="pb-3 pl-3">${items}</ul>
      </details>`;
    }).join('');

    // Nothing entered anywhere is a misclick, not a decision.
    const confirmBtn = document.getElementById('submit-confirm');
    const nothingEntered = filled === 0;

    confirmBtn.disabled = nothingEntered;
    // Set here rather than left to the Blade, because this runs on every
    // open and would otherwise overwrite whatever the markup declared.
    confirmBtn.className = nothingEntered
      ? 'px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-500 bg-gray-200 cursor-not-allowed'
      : 'px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-green-700 hover:bg-green-800';
    confirmBtn.textContent = nothingEntered ? 'Nothing to submit' : 'Submit';
  }

  const submitModal = document.getElementById('submit-modal');

  /**
   * Flush the open tab before showing anything.
   *
   * The last few keystrokes may still be sitting in the debounce, so
   * without this the modal could report a field as blank that the person
   * just filled in.
   */
  async function openSubmitModal() {
    const btn = document.getElementById('submit-btn');
    if (btn) btn.disabled = true;

    try {
      if (canEdit && !sections[activeKey].dynamic) {
        stashTyped();
        await saver.save(activeKey, document.getElementById('table-body'));
      }
    } catch {
      if (btn) btn.disabled = false;
      alert('Your latest changes could not be saved, so nothing was submitted. Check your connection and try again.');
      return;
    }

    if (btn) btn.disabled = false;
    renderSubmitModal(emptyReport());
    submitModal.classList.remove('hidden');
  }

  document.getElementById('submit-btn')?.addEventListener('click', openSubmitModal);

  document.getElementById('submit-cancel')?.addEventListener('click', () => {
    submitModal.classList.add('hidden');
  });

  submitModal?.addEventListener('click', (e) => {
    if (e.target === submitModal) submitModal.classList.add('hidden');
  });

  document.getElementById('submit-confirm')?.addEventListener('click', async () => {
    const confirmBtn = document.getElementById('submit-confirm');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Submitting…';

    try {
      const office = new URLSearchParams(window.location.search).get('office');
      const url = `/sprs/accomplishment/${openYear}/${openMonth}/submit`
                + (office ? `?office=${encodeURIComponent(office)}` : '');

      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
      });

      const body = await response.json().catch(() => ({}));

      if (!response.ok) {
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Submit';
        alert(body.message || `Submit failed (${response.status})`);
        return;
      }

      window.location.reload();
    } catch {
      confirmBtn.disabled = false;
      confirmBtn.textContent = 'Submit';
      alert("Could not reach the server. Your data is saved — try submitting again when you're back online.");
    }
  });

  document.getElementById('btn-past').addEventListener('click', () => {
    showPast = !showPast; renderToggles(); renderTable();
  });

  document.getElementById('btn-future').addEventListener('click', () => {
    showFuture = !showFuture; renderToggles(); renderTable();
  });

  // A different year needs different data, so reload rather than trying
  // to hold twelve months of every year in memory.
  //
  // Rebuild from the current URL rather than replacing the query string
  // outright — an FO viewing another office's form has ?office= set, and
  // dropping it would silently bounce them to their own submission.
  document.getElementById('year-select').addEventListener('change', (e) => {
    const url = new URL(window.location);
    url.searchParams.set('year', e.target.value);
    window.location = url;
  });

  document.getElementById('jump-select').addEventListener('change', (e) => {
    document.getElementById('group-' + e.target.value)
      ?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  // Warn before losing a pending debounce.
  window.addEventListener('beforeunload', (e) => {
    if (debounce) { e.preventDefault(); e.returnValue = ''; }
  });

  /* ---------------------------------------------------------------- */
  /* recovery                                                          */
  /* ---------------------------------------------------------------- */

  /**
   * Unsent work from a previous visit on this device.
   *
   * Never restored automatically. localStorage can be older than the
   * server — someone edits the same office from another PC, and the copy
   * sitting here is now yesterday's. Silently replaying it would wipe
   * their work, so the person is shown both and chooses.
   */
  /**
   * Whether a buffered copy actually holds something different from what
   * the server sent for this month.
   *
   * Compared loosely on purpose: null, undefined and '' all mean "not
   * answered", and a value that came back as 5 shouldn't count as changed
   * against a buffered '5'.
   */
  function differsFromSaved(section, values) {
    if (!values || typeof values !== 'object') return false;

    // The repeating tab is a list, not a field map — compare by shape.
    if (sections[section]?.dynamic) {
      return JSON.stringify(values) !== JSON.stringify(monthly[section] || []);
    }

    return Object.entries(values).some(([field, local]) => {
      const saved = monthly[section]?.[field]?.[openMonth];

      const blank = (v) => v === null || v === undefined || v === '';

      if (blank(local) && blank(saved)) return false;
      if (blank(local) !== blank(saved)) return true;

      return Number(local) !== Number(saved);
    });
  }

  function checkPendingLocal() {
    const serverTimes = {};
    Object.entries(meta).forEach(([key, m]) => { serverTimes[key] = m.updated_at; });

    // Anything buffered whose values already match what's on screen is
    // not unsaved work — it's a leftover key. That happens after a 409,
    // where the local copy is kept on purpose, or if a save landed but
    // the response never arrived. Prompting to "restore" data the person
    // is already looking at is worse than saying nothing.
    const pending = saver.pendingAll(serverTimes).filter((item) => {
      if (differsFromSaved(item.section, item.values)) return true;

      saver.discardLocal(item.section);
      return false;
    });

    if (!pending.length) return;

    const banner = document.getElementById('recovery-banner');
    if (!banner) return;

    const label = (k) => sections[k]?.label || k;
    const when = new Date(pending[0].savedAt).toLocaleString();
    const names = pending.map((p) => label(p.section)).join(', ');
    const anyStale = pending.some((p) => p.stale);

    banner.querySelector('[data-recovery-text]').innerHTML = anyStale
      ? `<b class="font-semibold">Unsaved changes from ${when} on this device</b> — ${names}.
         Some of these sections have since been changed by someone else. Restoring will replace their version.`
      : `<b class="font-semibold">You have unsaved changes from ${when}</b> on this device — ${names}.
         They never reached the server.`;

    banner.classList.remove('hidden');

    banner.querySelector('[data-recovery-restore]').addEventListener('click', async () => {
      for (const item of pending) {
        Object.entries(item.values).forEach(([field, value]) => {
          monthly[item.section] = monthly[item.section] || {};
          monthly[item.section][field] = monthly[item.section][field] || {};
          monthly[item.section][field][openMonth] = value;
        });
      }

      banner.classList.add('hidden');
      renderTable();

      // Push each restored section straight to the server, so recovered
      // work isn't sitting in localStorage a second time.
      for (const item of pending) {
        activeKey = item.section;
        renderTable();
        try {
          await saver.save(item.section, document.getElementById('table-body'));
        } catch {
          break;   // still offline — leave the rest buffered
        }
      }

      activeKey = KEYS[0];
      renderTabs();
      renderChrome();
      renderTable();
    });

    banner.querySelector('[data-recovery-discard]').addEventListener('click', () => {
      if (!confirm('Discard the unsaved changes on this device? This cannot be undone.')) return;
      pending.forEach((p) => saver.discardLocal(p.section));
      banner.classList.add('hidden');
    });
  }

  renderTabs();
  renderChrome();
  renderToggles();
  renderTable();
  checkPendingLocal();
})();