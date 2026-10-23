@extends('layouts.app')

@section('crumb', 'Accounts / Manage users')
@section('page-title', 'Create account')

@section('content')

<form method="POST" action="#">
  @csrf {{-- Laravel CSRF token, safe even before the controller/route exist --}}

  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <div class="text-sm font-bold text-slate-900 mb-4">Account details</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="name" class="block text-xs font-semibold text-slate-500 mb-1.5">Full name</label>
        <input type="text" id="name" name="name" placeholder="e.g. Jomari Santos"
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
      </div>
      <div>
        <label for="email" class="block text-xs font-semibold text-slate-500 mb-1.5">Email address</label>
        <input type="email" id="email" name="email" placeholder="name@sprs.gov.ph"
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
      <div>
        <label for="password" class="block text-xs font-semibold text-slate-500 mb-1.5">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••"
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
      </div>
      <div>
        <label for="password_confirmation" class="block text-xs font-semibold text-slate-500 mb-1.5">Confirm password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••"
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
      </div>
    </div>

    <div>
      <label for="avatar" class="block text-xs font-semibold text-slate-500 mb-1.5">Profile photo</label>
      <input type="file" id="avatar" name="avatar" accept="image/*"
        class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
      <div class="text-xs text-slate-500 mt-1.5">Optional — nullable in the database, shows initials until uploaded.</div>
    </div>
  </div>

  <div class="bg-white border border-slate-200 rounded-xl p-6 mt-4.5">
    <div class="text-sm font-bold text-slate-900 mb-4">Role &amp; office assignment</div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label for="role" class="block text-xs font-semibold text-slate-500 mb-1.5">Role</label>
        <select id="role" name="role"
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
          <option value="">Select a role</option>
          <option value="1">JPO</option>
          <option value="2">Peso_City/Municipality</option>
          <option value="3">Peso_Province</option>
          <option value="4">FO</option>
          <option value="5">RO</option>
          <option value="super_admin">Super Admin</option>
        </select>
        <div class="text-xs text-slate-500 mt-1.5">Determines which provinces and offices show up below.</div>
      </div>
      <div>
        <label for="province_id" class="block text-xs font-semibold text-slate-500 mb-1.5">Province</label>
        <select id="province_id" name="province_id" disabled
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white disabled:bg-slate-50 disabled:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
          <option value="">Select a role first</option>
        </select>
        <div class="text-xs text-slate-500 mt-1.5" id="provinceHint">RO and Super Admin aren't tied to a single province.</div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
      <div>
        <label for="office_id" class="block text-xs font-semibold text-slate-500 mb-1.5">Office</label>
        <select id="office_id" name="office_id" disabled
          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white disabled:bg-slate-50 disabled:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
          <option value="">Select a province first</option>
        </select>
        <div class="text-xs text-slate-500 mt-1.5" id="officeHint">Not required for Super Admin — it isn't scoped to any office.</div>
      </div>
    </div>
  </div>

  <div class="bg-white border border-slate-200 rounded-xl p-6 mt-4.5">
    <div class="text-sm font-bold text-slate-900 mb-4">Status</div>
    <div class="max-w-56">
      <label for="is_active" class="block text-xs font-semibold text-slate-500 mb-1.5">Account status</label>
      <select id="is_active" name="is_active"
        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
        <option value="1" selected>Active</option>
        <option value="0">Inactive</option>
      </select>
      <div class="text-xs text-slate-500 mt-1.5">Inactive accounts can't sign in, but their submission history is kept.</div>
    </div>
  </div>

  <div class="mt-5 flex gap-2.5">
    <button type="submit" class="px-4 py-2.5 rounded-lg text-sm font-semibold bg-slate-900 text-white hover:opacity-90">
      Create account
    </button>
    <a href="#" class="px-4 py-2.5 rounded-lg text-sm font-semibold bg-white border border-slate-300 text-slate-800 hover:bg-slate-50">
      Cancel
    </a>
  </div>
</form>

<script>
  // Sample data — placeholder only, will come from provinces_type and
  // offices tables once the backend is wired up.
  const provinces = [
    { id: 'DSFO', name: 'DSFO — Davao del Sur Field Office' },
    { id: 'DOFO', name: 'DOFO — Davao Oriental Field Office' },
    { id: 'DORFO', name: 'DORFO — Davao Oriental Field Office (Region)' },
    { id: 'DOCFO', name: 'DOCFO — Davao Occidental Field Office' },
    { id: 'DNFO', name: 'DNFO — Davao de Norte Field Office' },
    { id: 'DCFO', name: 'DCFO — Davao City Field Office' },
  ];

  const officesByRoleAndProvince = {
    '1': { DSFO: ['JPO — Cor Jesu College, Inc.', 'JPO — UM Digos College'], DOFO: ['JPO — Mati Doctors College'] },
    '2': { DSFO: ['PESO Digos City', 'PESO Bansalan'], DOFO: ['PESO Mati City'] },
    '3': { DSFO: ['PESO Davao del Sur'], DOFO: ['PESO Davao Oriental'] },
    '4': { DSFO: ['FO — Davao del Sur'], DOFO: ['FO — Davao Oriental'] },
    '5': { _national: ['Regional Office XI'] },
  };

  const roleSelect = document.getElementById('role');
  const provinceSelect = document.getElementById('province_id');
  const provinceHint = document.getElementById('provinceHint');
  const officeSelect = document.getElementById('office_id');
  const officeHint = document.getElementById('officeHint');

  function resetOffice(message) {
    officeSelect.innerHTML = `<option value="">${message}</option>`;
    officeSelect.disabled = true;
  }

  roleSelect.addEventListener('change', () => {
    const val = roleSelect.value;
    resetOffice('Select a province first');

    if (val === 'super_admin') {
      provinceSelect.innerHTML = '<option value="">Not applicable</option>';
      provinceSelect.disabled = true;
      provinceHint.textContent = "Not required — Super Admin isn't scoped to any province.";
      resetOffice('Not applicable');
      officeHint.textContent = "Not required for Super Admin — it isn't scoped to any office.";
      return;
    }

    if (val === '5') {
      provinceSelect.innerHTML = '<option value="">Not applicable</option>';
      provinceSelect.disabled = true;
      provinceHint.textContent = 'RO sees every province — no single province to pick.';
      officeSelect.disabled = false;
      officeSelect.innerHTML = officesByRoleAndProvince['5']._national
        .map(name => `<option value="${name}">${name}</option>`).join('');
      officeHint.textContent = 'Sample list — will be pulled from the offices table.';
      return;
    }

    if (!val) {
      provinceSelect.innerHTML = '<option value="">Select a role first</option>';
      provinceSelect.disabled = true;
      provinceHint.textContent = "RO and Super Admin aren't tied to a single province.";
      return;
    }

    provinceSelect.disabled = false;
    provinceSelect.innerHTML = '<option value="">Select a province</option>' +
      provinces.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
    provinceHint.textContent = 'Filters the office list below to that province only.';
  });

  provinceSelect.addEventListener('change', () => {
    const role = roleSelect.value;
    const prov = provinceSelect.value;
    const list = officesByRoleAndProvince[role]?.[prov];

    if (!prov || !list) {
      resetOffice('Select a province first');
      return;
    }

    officeSelect.disabled = false;
    officeSelect.innerHTML = '<option value="">Select an office</option>' +
      list.map(name => `<option value="${name}">${name}</option>`).join('');
    officeHint.textContent = `Offices under ${prov} only — sample data, will come from the offices table.`;
  });
</script>

@endsection
