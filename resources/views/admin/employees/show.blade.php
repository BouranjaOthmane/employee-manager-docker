@extends('adminlte::page')

@section('title', 'Employee Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">{{ $employee->full_name }}</h1>
            <small class="text-muted">
                {{ $employee->position?->title ?? '—' }}
                •
                @if ($employee->status === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-secondary">Inactive</span>
                @endif
            </small>
        </div>

        <div>
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Flash message --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif



    <div class="row">
        {{-- LEFT CARD --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <h5 class="mb-1">{{ $employee->full_name }}</h5>
                    <div class="text-muted mb-3">{{ $employee->position?->title ?? '—' }}</div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <strong>Email:</strong> {{ $employee->email ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Phone:</strong> {{ $employee->phone ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Hire date:</strong> {{ $employee->hire_date?->format('Y-m-d') ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>CIN:</strong> {{ $employee->cin ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>CNSS:</strong> {{ $employee->cnss ?? '—' }}
                        </li>
                    </ul>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="badge badge-info p-2">Docs: {{ $employee->documents?->count() ?? 0 }}</span>
                        <span class="badge badge-warning p-2">Vacations: {{ $employee->vacations?->count() ?? 0 }}</span>
                        <span class="badge badge-success p-2">Salaries: {{ $employee->salaries?->count() ?? 0 }}</span>
                        <a href="{{ route('admin.employees.calendar.show', $employee) }}" class="btn btn-info">
                            <i class="fas fa-calendar-alt mr-1"></i> Calendar
                        </a>

                    </div>
                </div>

                <div class="card-footer">
                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST"
                        onsubmit="return confirm('Delete this employee?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-1"></i> Delete Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT TABS --}}
        <div class="col-md-8">
            <div class="card card-outline card-secondary">
                @php $tab = request('tab', 'overview'); @endphp

                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'overview') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=overview">
                                <i class="fas fa-info-circle mr-1"></i> Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'documents') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=documents">
                                <i class="fas fa-folder-open mr-1"></i> Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'vacations') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=vacations">
                                <i class="fas fa-plane-departure mr-1"></i> Vacations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'salaries') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=salaries">
                                <i class="fas fa-money-bill-wave mr-1"></i> Salaries
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    @if ($tab === 'documents')
                        @include('admin.employees.tabs.documents')
                    @elseif ($tab === 'vacations')
                        @include('admin.employees.tabs.vacations')
                    @elseif ($tab === 'salaries')
                        @include('admin.employees.tabs.salaries')
                    @else
                        @include('admin.employees.tabs.overview')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
