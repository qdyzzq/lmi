<?php

namespace App\Http\Controllers\Sprs;

use App\Http\Controllers\Controller;
use App\Models\Sprs\Office;
use App\Models\Sprs\ProvinceType;
use App\Models\Sprs\RoleType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function create()
    {
        return view('sprs.superadmin.create_account', [
            'roleTypes' => RoleType::all(),
            'offices'   => Office::all(),
            'provinces' => ProvinceType::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'regex:/^(09|\+639|639)\d{9}$/', 'unique:users,phone_number'],
            'password'     => ['required', 'string', 'min:4', 'confirmed'],
            'role'         => ['required'], // role_types id ('1'..'5') or the literal 'super_admin'
            'province_id'  => ['nullable', 'integer', 'exists:provinces_type,id'],
            'office_id'    => ['nullable', 'integer', 'exists:offices,id'],
        ], [
            'phone_number.regex' => 'Enter a valid PH mobile number (e.g. 09171234567 or 639171234567).',
        ]);

        // Normalize to a single consistent format (639XXXXXXXXX) regardless
        // of how it was typed — the OTP SMS sender expects one format.
        $phoneNumber = preg_replace('/^(?:\+?63|0)/', '63', $validated['phone_number']);

        $isSuperAdmin = $request->input('role') === 'super_admin';

        if ($isSuperAdmin) {
            $officeId = null;
        } else {
            if (! $request->filled('office_id')) {
                throw ValidationException::withMessages([
                    'office_id' => 'Please select an office for this role.',
                ]);
            }

            $office = Office::find($request->input('office_id'));

            if (! $office) {
                throw ValidationException::withMessages([
                    'office_id' => 'That office could not be found.',
                ]);
            }

            // Don't just trust the frontend's role→office filtering —
            // confirm the chosen office actually matches the chosen role.
            if ((string) $office->office_type !== (string) $request->input('role')) {
                throw ValidationException::withMessages([
                    'office_id' => 'That office does not match the selected role.',
                ]);
            }

            // RO has no province (province_id is NULL on that office), so this
            // check only applies to roles that actually go through the
            // Province step in the form.
            if (! is_null($office->province_id)
                && $request->filled('province_id')
                && (string) $office->province_id !== (string) $request->input('province_id')
            ) {
                throw ValidationException::withMessages([
                    'office_id' => 'That office does not belong to the selected province.',
                ]);
            }

            $officeId = $office->id;
        }

        User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone_number' => $phoneNumber,
            'password'     => Hash::make($validated['password']),
            'office_id'    => $officeId,
            'role'         => $isSuperAdmin ? 'super_admin' : null,
            'is_active'    => true, // every new account starts active — deactivation is an edit-screen action, not a creation-time choice
        ]);

        return redirect()
            ->route('sprs.superadmin.create_account')
            ->with('success', 'Account created successfully.');
    }
}