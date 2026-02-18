@extends('adminlte::page')

@section('title', 'Salaries')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Salaries (Payroll)</h1>

        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-users mr-1"></i> Employees
        </a>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    {{-- Filters --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.salaries.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Month</label>
                            <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                            <small class="text-muted">Leave empty to show all months.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee</label>
                            <select name="employee_id" class="form-control">
                                <option value="">All employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" @selected((string)request('employee_id') === (string)$emp->id)>
                                        {{ $emp->first_name }} {{ $emp->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fas fa-check"></i> Apply
                            </button>
                            <a href="{{ route('admin.salaries.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Totals --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-layer-group"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Records</span>
                    <span class="info-box-number">{{ (int)($totals->records ?? 0) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-wallet"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Base Total</span>
                    <span class="info-box-number">{{ number_format((float)($totals->base_total ?? 0), 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-plus-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Bonus Total</span>
                    <span class="info-box-number">{{ number_format((float)($totals->bonus_total ?? 0), 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Net Total</span>
                    <span class="info-box-number">{{ number_format((float)($totals->net_total ?? 0), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-bill-wave mr-1"></i> Salary Records</h3>
            <div class="card-tools">
                <span class="badge badge-info p-2">Total: {{ $salaries->total() }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Base</th>
                            <th>Bonus</th>
                            <th>Deduction</th>
                            <th>Net</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($salaries as $sal)
                        <tr>
                            <td>
                                <a href="{{ route('admin.employees.show', $sal->employee_id) }}?tab=salaries">
                                    {{ $sal->employee?->first_name }} {{ $sal->employee?->last_name }}
                                </a>
                            </td>
                            <td>{{ $sal->month?->format('Y-m') }}</td>
                            <td>{{ number_format((float)$sal->base_salary, 2) }}</td>
                            <td>{{ number_format((float)$sal->bonus, 2) }}</td>
                            <td>{{ number_format((float)$sal->deduction, 2) }}</td>
                            <td><strong>{{ number_format((float)$sal->net_salary, 2) }}</strong></td>
                            <td class="text-muted">{{ \Illuminate\Support\Str::limit($sal->note, 50) ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No salary records found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-right">Totals (filtered)</th>
                            <th>{{ number_format((float)($totals->base_total ?? 0), 2) }}</th>
                            <th>{{ number_format((float)($totals->bonus_total ?? 0), 2) }}</th>
                            <th>{{ number_format((float)($totals->deduction_total ?? 0), 2) }}</th>
                            <th>{{ number_format((float)($totals->net_total ?? 0), 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($salaries->hasPages())
            <div class="card-footer">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>
@endsection
