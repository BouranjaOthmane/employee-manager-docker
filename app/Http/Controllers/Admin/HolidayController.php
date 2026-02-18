<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HolidayController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $holidays = Holiday::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('reason', 'like', "%{$q}%")
                      ->orWhere('date', 'like', "%{$q}%");
            })
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        return view('admin.holidays.index', compact('holidays'));
    }

    public function create(): View
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'   => ['required', 'date'],
            'name'   => ['required', 'string', 'max:190'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        Holiday::create($data);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday created successfully.');
    }

    public function edit(Holiday $holiday): View
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $data = $request->validate([
            'date'   => ['required', 'date'],
            'name'   => ['required', 'string', 'max:190'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $holiday->update($data);

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday deleted successfully.');
    }
}
