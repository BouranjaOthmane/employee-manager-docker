<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDayOverride;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeCalendarDayController extends Controller
{
    public function show(Request $request, Employee $employee): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $override = EmployeeDayOverride::query()
            ->where('employee_id', $employee->id)
            ->where('date', $data['date'])
            ->first();

        return response()->json([
            'date' => $data['date'],
            'override' => $override ? [
                'status' => $override->status,
                'reason' => $override->reason,
            ] : null,
        ]);
    }

    public function storeOrUpdate(Request $request, Employee $employee): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', 'in:working,off,vacation'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $override = EmployeeDayOverride::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $data['date']],
            [
                'status' => $data['status'],
                'reason' => $data['reason'] ?? null,
                'created_by' => $request->user()?->id,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Day updated',
            'override' => [
                'status' => $override->status,
                'reason' => $override->reason,
            ],
        ]);
    }

    public function destroy(Request $request, Employee $employee): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        EmployeeDayOverride::query()
            ->where('employee_id', $employee->id)
            ->where('date', $data['date'])
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Override removed',
        ]);
    }
}
