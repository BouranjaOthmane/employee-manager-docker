<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Notifications\VacationStatusNotification;

class VacationApprovalController extends Controller
{
    public function approve(Request $request, Vacation $vacation): RedirectResponse
    {
        if ($vacation->status !== 'pending') {
            return back()->with('error', 'Only pending vacations can be approved.');
        }

        $vacation->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // Notify employee user if linked
        $employeeUser = $vacation->employee?->user;
        if ($employeeUser) {
            $employeeUser->notify(new VacationStatusNotification($vacation->fresh(['employee'])));
        }

        return redirect()
            ->to(route('admin.employees.show', $vacation->employee_id) . '?tab=vacations')
            ->with('success', 'Vacation approved successfully.');
    }

    public function reject(Request $request, Vacation $vacation): RedirectResponse
    {
        if ($vacation->status !== 'pending') {
            return back()->with('error', 'Only pending vacations can be rejected.');
        }

        $vacation->update([
            'status' => 'rejected',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        // Notify employee user if linked
        $employeeUser = $vacation->employee?->user;
        if ($employeeUser) {
            $employeeUser->notify(new VacationStatusNotification($vacation->fresh(['employee'])));
        }

        return redirect()
            ->to(route('admin.employees.show', $vacation->employee_id) . '?tab=vacations')
            ->with('success', 'Vacation rejected successfully.');
    }
}
