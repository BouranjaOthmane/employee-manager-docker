<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeePasswordController extends Controller
{
    public function reset(Employee $employee): RedirectResponse
    {
        $user = $employee->user;

        if (!$user) {
            return back()->with('error', 'No user account is linked to this employee.');
        }

        $temporaryPassword = Str::password(10);

        $user->update([
            'password' => Hash::make($temporaryPassword),
        ]);

        return redirect()
            ->route('admin.employees.show', $employee)
            ->with('success', 'Password reset successfully.')
            ->with('temp_password', $temporaryPassword);
    }
}