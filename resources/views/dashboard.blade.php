@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Dashboard</h1>
            <small class="text-muted">
                Overview of Employees, Vacations, Salaries & Holidays
            </small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-primary">
                <i class="fas fa-users mr-1"></i> Employees
            </a>
            <a href="{{ route('admin.vacations.index') }}" class="btn btn-outline-warning">
                <i class="fas fa-plane-departure mr-1"></i> Vacations
            </a>
            <a href="{{ route('admin.salaries.index') }}" class="btn btn-outline-success">
                <i class="fas fa-money-bill-wave mr-1"></i> Payroll
            </a>
            <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-info">
                <i class="fas fa-calendar-day mr-1"></i> Holidays
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Flash messages --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
    @if (session('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    {{-- QUICK STATS (you can wire real numbers later) --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['employees'] ?? '—' }}</h3>
                    <p>Employees</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('admin.employees.index') }}" class="small-box-footer">
                    View employees <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['positions'] ?? '—' }}</h3>
                    <p>Positions</p>
                </div>
                <div class="icon"><i class="fas fa-briefcase"></i></div>
                <a href="{{ route('admin.positions.index') }}" class="small-box-footer">
                    View positions <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['vacations_pending'] ?? '—' }}</h3>
                    <p>Pending vacations</p>
                </div>
                <div class="icon"><i class="fas fa-plane-departure"></i></div>
                <a href="{{ route('admin.vacations.index', ['status' => 'pending']) }}" class="small-box-footer">
                    Review requests <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['payroll_net_this_month'] ?? '—' }}</h3>
                    <p>Net payroll (this month)</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <a href="{{ route('admin.salaries.index', ['month' => now()->format('Y-m')]) }}" class="small-box-footer">
                    View payroll <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="row">
        {{-- LEFT: Vacations to approve --}}
        <div class="col-md-7">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bell mr-1"></i> Pending vacation requests
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.vacations.index', ['status' => 'pending']) }}" class="btn btn-tool">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Employee</th>
                                    <th>Dates</th>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th class="text-right" style="width: 190px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($pendingVacations ?? []) as $vac)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.employees.show', $vac->employee_id) }}?tab=vacations">
                                                {{ $vac->employee?->first_name }} {{ $vac->employee?->last_name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $vac->start_date?->format('Y-m-d') }} →
                                            {{ $vac->end_date?->format('Y-m-d') }}
                                        </td>
                                        <td class="text-capitalize">
                                            <span class="badge badge-light p-2">{{ $vac->type }}</span>
                                        </td>
                                        <td class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($vac->reason, 40) ?? '—' }}</td>
                                        <td class="text-right">
                                            
                                                @if ($vac->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.vacations.approve', $vac) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>

                                                    <form method="POST" action="{{ route('admin.vacations.reject', $vac) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif

                                                
                                                
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No pending requests ✅</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer text-muted">
                    Tip: Pending requests appear here so HR/Admin can approve fast.
                </div>
            </div>
        </div>

        {{-- RIGHT: Holidays + Quick links --}}
        <div class="col-md-5">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-day mr-1"></i> Upcoming holidays
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.holidays.index') }}" class="btn btn-tool">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse(($upcomingHolidays ?? []) as $h)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $h->name }}</strong>
                                    <div class="text-muted small">{{ $h->reason ?? '—' }}</div>
                                </div>
                                <span class="badge badge-info p-2">{{ $h->date?->format('Y-m-d') }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">No upcoming holidays</li>
                        @endforelse
                    </ul>
                </div>

                <div class="card-footer">
                    <a href="{{ route('admin.holidays.create') }}" class="btn btn-info btn-block">
                        <i class="fas fa-plus mr-1"></i> Add Holiday
                    </a>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-1"></i> Quick actions
                    </h3>
                </div>

                <div class="card-body">
                    <div class="d-flex flex-column" style="gap:10px;">
                        <a href="{{ route('admin.employees.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus mr-1"></i> Add Employee
                        </a>

                        <a href="{{ route('admin.positions.create') }}" class="btn btn-outline-info">
                            <i class="fas fa-briefcase mr-1"></i> Add Position
                        </a>

                        <a href="{{ route('admin.salaries.index', ['month' => now()->format('Y-m')]) }}"
                            class="btn btn-outline-success">
                            <i class="fas fa-receipt mr-1"></i> Payroll this month
                        </a>

                        <a href="{{ route('admin.vacations.index') }}" class="btn btn-outline-warning">
                            <i class="fas fa-plane mr-1"></i> All vacation requests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Optional: Latest salaries records --}}
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clock mr-1"></i> Latest salary records
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.salaries.index') }}" class="btn btn-tool">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Net</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($latestSalaries ?? []) as $sal)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.employees.show', $sal->employee_id) }}?tab=salaries">
                                        {{ $sal->employee?->first_name }} {{ $sal->employee?->last_name }}
                                    </a>
                                </td>
                                <td>{{ $sal->month?->format('Y-m') }}</td>
                                <td><strong>{{ number_format((float) $sal->net_salary, 2) }}</strong></td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($sal->note, 60) ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No salary records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
