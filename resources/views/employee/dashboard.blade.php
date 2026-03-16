@extends('adminlte::page')

@section('title', 'Employee Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">My Dashboard</h1>
            <small class="text-muted">
                Welcome {{ $employee->first_name }} {{ $employee->last_name }}
            </small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('employee.notifications.index') }}" class="btn btn-outline-danger position-relative">
                <i class="fas fa-bell"></i>
                @if (auth()->user()->unreadNotifications()->count() > 0)
                    <span class="badge badge-danger position-absolute" style="top:-6px; right:-6px;">
                        {{ auth()->user()->unreadNotifications()->count() }}
                    </span>
                @endif
            </a>
            <a href="{{ route('employee.profile') }}" class="btn btn-outline-primary">
                <i class="fas fa-user mr-1"></i> My Profile
            </a>
            <a href="{{ route('employee.vacations.index') }}" class="btn btn-outline-warning">
                <i class="fas fa-plane-departure mr-1"></i> My Vacations
            </a>
            <a href="{{ route('employee.salaries.index') }}" class="btn btn-outline-success">
                <i class="fas fa-money-bill-wave mr-1"></i> My Salaries
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

    {{-- QUICK STATS --}}
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['vacations_pending'] }}</h3>
                    <p>Pending Vacations</p>
                </div>
                <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                <a href="{{ route('employee.vacations.index') }}" class="small-box-footer">
                    View vacations <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['vacations_approved'] }}</h3>
                    <p>Approved Vacations</p>
                </div>
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <a href="{{ route('employee.vacations.index') }}" class="small-box-footer">
                    View vacations <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['salary_records'] }}</h3>
                    <p>Salary Records</p>
                </div>
                <div class="icon"><i class="fas fa-wallet"></i></div>
                <a href="{{ route('employee.salaries.index') }}" class="small-box-footer">
                    View salaries <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- LEFT --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-id-card mr-1"></i> My Information
                    </h3>
                </div>

                <div class="card-body">
                    <p><strong>Full Name:</strong><br>{{ $employee->first_name }} {{ $employee->last_name }}</p>
                    <p><strong>Position:</strong><br>{{ $employee->position?->title ?? '—' }}</p>
                    <p><strong>Email:</strong><br>{{ $employee->email ?? '—' }}</p>
                    <p><strong>Phone:</strong><br>{{ $employee->phone ?? '—' }}</p>
                    <p><strong>Hire Date:</strong><br>{{ $employee->hire_date?->format('Y-m-d') ?? '—' }}</p>
                    <p><strong>Status:</strong><br>
                        @if ($employee->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </p>
                </div>

                <div class="card-footer">
                    <a href="{{ route('employee.profile') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-user mr-1"></i> View Full Profile
                    </a>
                </div>
            </div>

            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day mr-1"></i> Upcoming Holidays
                    </h3>
                </div>

                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($upcomingHolidays as $holiday)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $holiday->name }}</strong>
                                    <div class="text-muted small">{{ $holiday->reason ?? '—' }}</div>
                                </div>
                                <span class="badge badge-info p-2">
                                    {{ $holiday->date?->format('Y-m-d') }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-3">
                                No upcoming holidays
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-md-8">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plane-departure mr-1"></i> My Latest Vacations
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.vacations.index') }}" class="btn btn-tool">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Dates</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestVacations as $vac)
                                    <tr>
                                        <td>
                                            {{ $vac->start_date?->format('Y-m-d') }}
                                            →
                                            {{ $vac->end_date?->format('Y-m-d') }}
                                        </td>
                                        <td class="text-capitalize">{{ $vac->type }}</td>
                                        <td>
                                            @if ($vac->status === 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($vac->status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($vac->reason, 50) ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No vacation requests yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave mr-1"></i> My Latest Salaries
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('employee.salaries.index') }}" class="btn btn-tool">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Month</th>
                                    <th>Base</th>
                                    <th>Bonus</th>
                                    <th>Deduction</th>
                                    <th>Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestSalaries as $salary)
                                    <tr>
                                        <td>{{ $salary->month?->format('Y-m') }}</td>
                                        <td>{{ number_format((float) $salary->base_salary, 2) }}</td>
                                        <td>{{ number_format((float) $salary->bonus, 2) }}</td>
                                        <td>{{ number_format((float) $salary->deduction, 2) }}</td>
                                        <td><strong>{{ number_format((float) $salary->net_salary, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No salary records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
