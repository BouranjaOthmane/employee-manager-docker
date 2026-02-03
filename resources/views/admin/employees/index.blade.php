{{-- resources/views/admin/employees/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Employees')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Employees</h1>

        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus mr-1"></i> Add Employee
        </a>
    </div>
@endsection

@section('content')

    {{-- Flash message --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    {{-- Filters --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-1"></i> Search & Filters
            </h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.employees.index') }}">
                <div class="row">

                    {{-- Search --}}
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Search</label>
                            <div class="input-group">
                                <input type="text"
                                       name="q"
                                       class="form-control"
                                       placeholder="Name, Email, Phone, CIN, CNSS..."
                                       value="{{ request('q') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Position filter --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Position</label>
                            <select name="position_id" class="form-control">
                                <option value="">All Positions</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" @selected(request('position_id') == $pos->id)>
                                        {{ $pos->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status filter --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="active" @selected(request('status') === 'active')>Active</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                Apply
                            </button>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Employees table --}}
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-1"></i>
                Employees List
            </h3>

            <div class="card-tools">
                <span class="badge badge-info p-2">
                    Total: {{ $employees->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Hire Date</th>
                            <th style="width: 220px;" class="text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="text-muted">#{{ $employee->id }}</td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                        <small class="text-muted">
                                            CIN: {{ $employee->cin ?? '—' }} | CNSS: {{ $employee->cnss ?? '—' }}
                                        </small>
                                    </div>
                                </td>

                                <td>
                                    {{ $employee->position?->title ?? '—' }}
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span><i class="fas fa-phone mr-1 text-muted"></i> {{ $employee->phone ?? '—' }}</span>
                                        <span><i class="fas fa-envelope mr-1 text-muted"></i> {{ $employee->email ?? '—' }}</span>
                                    </div>
                                </td>

                                <td>
                                    @if ($employee->status === 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    {{ optional($employee->hire_date)->format('Y-m-d') ?? '—' }}
                                </td>

                                <td class="text-right">
                                    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('admin.employees.index', $employee) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-folder-open"></i>
                                    </a>

                                    <form action="{{ route('admin.employees.destroy', $employee) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this employee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> No employees found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($employees->hasPages())
            <div class="card-footer">
                {{ $employees->withQueryString()->links() }}
            </div>
        @endif
    </div>

@endsection
