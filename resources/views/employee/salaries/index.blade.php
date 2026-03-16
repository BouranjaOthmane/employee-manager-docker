@extends('adminlte::page')

@section('title', 'My Salaries')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">My Salaries</h1>
            <small class="text-muted">
                Payroll history and monthly salary details
            </small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>

            <a href="{{ route('employee.profile') }}" class="btn btn-outline-primary">
                <i class="fas fa-user mr-1"></i> My Profile
            </a>
        </div>
    </div>
@endsection

@section('content')

<div class="row">

{{-- SALARY TOTALS --}}
<div class="col-12">
    <div class="row">

        <div class="col-md-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($totals['base'], 2) }}</h3>
                    <p>Total Base Salary</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totals['bonus'], 2) }}</h3>
                    <p>Total Bonuses</p>
                </div>
                <div class="icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($totals['deduction'], 2) }}</h3>
                    <p>Total Deductions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-minus-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($totals['net'], 2) }}</h3>
                    <p>Total Net Salary</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- SALARY TABLE --}}
<div class="col-12">
    <div class="card card-outline card-success">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-money-bill-wave mr-1"></i>
                Salary History
            </h3>

            <div class="card-tools">
                <span class="badge badge-info p-2">
                    {{ $salaries->total() }} records
                </span>
            </div>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">

                    <thead class="thead-light">
                        <tr>
                            <th>Month</th>
                            <th>Base Salary</th>
                            <th>Bonus</th>
                            <th>Deduction</th>
                            <th>Net Salary</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($salaries as $salary)

                            <tr>
                                <td>
                                    {{ $salary->month?->format('Y-m') }}
                                </td>

                                <td>
                                    {{ number_format((float) $salary->base_salary, 2) }}
                                </td>

                                <td>
                                    {{ number_format((float) $salary->bonus, 2) }}
                                </td>

                                <td>
                                    {{ number_format((float) $salary->deduction, 2) }}
                                </td>

                                <td>
                                    <strong>
                                        {{ number_format((float) $salary->net_salary, 2) }}
                                    </strong>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No salary records available.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </div>

        @if($salaries->hasPages())
            <div class="card-footer">
                {{ $salaries->links() }}
            </div>
        @endif

    </div>
</div>

</div>

@endsection