@extends('adminlte::page')

@section('title', 'My Profile')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">My Profile</h1>
            <small class="text-muted">
                Personal and professional information
            </small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>

            <a href="{{ route('employee.calendar.show') }}" class="btn btn-outline-info">
                <i class="fas fa-calendar-alt mr-1"></i> My Calendar
            </a>
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <div class="row">
        {{-- LEFT CARD --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-body box-profile">
                    <div class="text-center mb-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width:90px;height:90px;background:#e9ecef;font-size:32px;font-weight:700;color:#495057;">
                            {{ strtoupper(substr($employee->first_name ?? 'E', 0, 1)) }}{{ strtoupper(substr($employee->last_name ?? '', 0, 1)) }}
                        </div>
                    </div>

                    <h3 class="profile-username text-center mb-1">
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </h3>

                    <p class="text-muted text-center mb-3">
                        {{ $employee->position?->title ?? '—' }}
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Status</b>
                            <span class="float-right">
                                @if($employee->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>Hire date</b>
                            <span class="float-right">
                                {{ $employee->hire_date?->format('Y-m-d') ?? '—' }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>Email</b>
                            <span class="float-right text-muted">
                                {{ $employee->email ?? '—' }}
                            </span>
                        </li>

                        <li class="list-group-item">
                            <b>Phone</b>
                            <span class="float-right text-muted">
                                {{ $employee->phone ?? '—' }}
                            </span>
                        </li>
                    </ul>

                    <a href="{{ route('employee.vacations.index') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plane-departure mr-1"></i> My Vacations
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT CONTENT --}}
        <div class="col-md-8">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card mr-1"></i> Personal Information
                    </h3>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">First Name</label>
                            <div class="font-weight-bold">
                                {{ $employee->first_name ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1">Last Name</label>
                            <div class="font-weight-bold">
                                {{ $employee->last_name ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Email</label>
                            <div class="font-weight-bold">
                                {{ $employee->email ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1">Phone</label>
                            <div class="font-weight-bold">
                                {{ $employee->phone ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">CIN</label>
                            <div class="font-weight-bold">
                                {{ $employee->cin ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1">CNSS</label>
                            <div class="font-weight-bold">
                                {{ $employee->cnss ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase mr-1"></i> Professional Information
                    </h3>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Position</label>
                            <div class="font-weight-bold">
                                {{ $employee->position?->title ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1">Status</label>
                            <div class="font-weight-bold">
                                @if($employee->status === 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mb-0">
                        <div class="col-md-6">
                            <label class="text-muted mb-1">Hire Date</label>
                            <div class="font-weight-bold">
                                {{ $employee->hire_date?->format('Y-m-d') ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted mb-1">Employee ID</label>
                            <div class="font-weight-bold">
                                #{{ $employee->id }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-1"></i> Quick Access
                    </h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('employee.vacations.index') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-plane-departure mr-1"></i> My Vacations
                            </a>
                        </div>

                        <div class="col-md-4 mb-2">
                            <a href="{{ route('employee.salaries.index') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-money-bill-wave mr-1"></i> My Salaries
                            </a>
                        </div>

                        <div class="col-md-4 mb-2">
                            <a href="{{ route('employee.calendar.show') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-calendar-alt mr-1"></i> My Calendar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection