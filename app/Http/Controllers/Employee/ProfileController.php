<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $employee = auth()->user()->employee?->load('position');

        abort_unless($employee, 403, 'Employee profile not linked to this user.');

        return view('employee.profile', compact('employee'));
    }
}