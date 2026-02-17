<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeDocumentController extends Controller
{
    public function index(Employee $employee)
    {
        $employee->load(['documents' => fn ($q) => $q->latest()]);
        return view('admin.employees.documents.index', compact('employee'));
    }

    public function store(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'title' => ['nullable', 'string', 'max:190'],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        $path = $request->file('file')->store("employee-docs/{$employee->id}", 'public');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(EmployeeDocument $document): StreamedResponse
    {
        return Storage::disk('public')->download($document->file_path);
    }

    public function destroy(EmployeeDocument $document): RedirectResponse
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
