@extends('adminlte::page')

@section('title', 'Vacations')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Vacations</h1>

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

    @if (session('error'))
        <x-adminlte-alert theme="danger" title="Error" dismissable>
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    {{-- Filters --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filters</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.vacations.index') }}">
                <div class="row">
                    <div class="col-md-3">
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

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All</option>
                                <option value="pending"  @selected(request('status')==='pending')>Pending</option>
                                <option value="approved" @selected(request('status')==='approved')>Approved</option>
                                <option value="rejected" @selected(request('status')==='rejected')>Rejected</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                <option value="">All</option>
                                <option value="paid"   @selected(request('type')==='paid')>Paid</option>
                                <option value="unpaid" @selected(request('type')==='unpaid')>Unpaid</option>
                                <option value="sick"   @selected(request('type')==='sick')>Sick</option>
                                <option value="other"  @selected(request('type')==='other')>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>From</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label>To</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button class="btn btn-primary btn-block" type="submit">
                                <i class="fas fa-check"></i>
                            </button>
                            <a href="{{ route('admin.vacations.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plane-departure mr-1"></i> Requests</h3>
            <div class="card-tools">
                <span class="badge badge-info p-2">Total: {{ $vacations->total() }}</span>
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
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Approved</th>
                        <th class="text-right" style="width: 190px;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($vacations as $vac)
                        <tr>
                            <td>
                                <a href="{{ route('admin.employees.show', $vac->employee_id) }}?tab=vacations">
                                    {{ $vac->employee?->first_name }} {{ $vac->employee?->last_name }}
                                </a>
                            </td>

                            <td>
                                {{ $vac->start_date?->format('Y-m-d') }}
                                →
                                {{ $vac->end_date?->format('Y-m-d') }}
                            </td>

                            <td class="text-capitalize">
                                <span class="badge badge-light p-2">{{ $vac->type }}</span>
                            </td>

                            <td>
                                @if($vac->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($vac->status === 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>

                            <td class="text-muted">{{ \Illuminate\Support\Str::limit($vac->reason, 60) ?? '—' }}</td>

                            <td class="text-muted">
                                @if($vac->approved_at)
                                    <div><strong>By:</strong> {{ $vac->approvedBy?->name ?? ('User #'.$vac->approved_by) }}</div>
                                    <div><strong>At:</strong> {{ $vac->approved_at->format('Y-m-d H:i') }}</div>
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-right">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('admin.employees.show', $vac->employee_id) }}?tab=vacations">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @role('admin|hr')
                                    @if($vac->status === 'pending')
                                        <form class="d-inline" method="POST" action="{{ route('admin.vacations.approve', $vac) }}"
                                              onsubmit="return confirm('Approve this vacation?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>

                                        <form class="d-inline" method="POST" action="{{ route('admin.vacations.reject', $vac) }}"
                                              onsubmit="return confirm('Reject this vacation?')">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No vacation requests found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($vacations->hasPages())
            <div class="card-footer">
                {{ $vacations->links() }}
            </div>
        @endif
    </div>
@endsection
