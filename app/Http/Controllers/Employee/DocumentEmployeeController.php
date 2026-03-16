<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentEmployeeController extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403, 'Employee profile not linked to this user.');

        $documents = EmployeeDocument::query()
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate(10);

        return view('employee.documents.index', compact('employee', 'documents'));
    }

    public function download(EmployeeDocument $document): StreamedResponse
    {
        $employee = auth()->user()->employee;

        abort_unless($employee, 403, 'Employee profile not linked to this user.');
        abort_unless($document->employee_id === $employee->id, 403, 'Unauthorized document access.');

        return Storage::disk('public')->download($document->file_path);
    }
}