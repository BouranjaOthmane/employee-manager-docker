@extends('adminlte::page')

@section('title', 'My Vacations')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">My Vacations</h1>
            <small class="text-muted">
                Request and track your vacation status
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
        {{-- Request form --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-1"></i> New Vacation Request
                    </h3>
                </div>

                <form method="POST" action="{{ route('employee.vacations.store') }}">
                    @csrf

                    <div class="card-body">
                        <div class="form-group">
                            <label>Start date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="start_date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date') }}"
                                   required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>End date <span class="text-danger">*</span></label>
                            <input type="date"
                                   name="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}"
                                   required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>Type <span class="text-danger">*</span></label>
                            @php $typeVal = old('type', 'paid'); @endphp
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="paid" @selected($typeVal === 'paid')>Paid</option>
                                <option value="unpaid" @selected($typeVal === 'unpaid')>Unpaid</option>
                                <option value="sick" @selected($typeVal === 'sick')>Sick</option>
                                <option value="other" @selected($typeVal === 'other')>Other</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="reason"
                                      rows="3"
                                      class="form-control @error('reason') is-invalid @enderror"
                                      placeholder="Optional reason...">{{ old('reason') }}</textarea>
                            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="alert alert-info mb-0">
                            New requests are submitted with
                            <strong>pending</strong> status.
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i> Send Request
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- History table --}}
        <div class="col-md-8">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plane-departure mr-1"></i> Vacation History
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info p-2">Total: {{ $vacations->total() }}</span>
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
                                    <th>Approved Info</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vacations as $vac)
                                    <tr>
                                        <td>
                                            {{ $vac->start_date?->format('Y-m-d') }}
                                            →
                                            {{ $vac->end_date?->format('Y-m-d') }}

                                            @if($vac->start_date && $vac->end_date)
                                                <small class="text-muted d-block">
                                                    {{ $vac->start_date->diffInDays($vac->end_date) + 1 }} day(s)
                                                </small>
                                            @endif
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

                                        <td class="text-muted">
                                            {{ \Illuminate\Support\Str::limit($vac->reason, 60) ?? '—' }}
                                        </td>

                                        <td class="text-muted">
                                            @if($vac->approved_at)
                                                <div><strong>By:</strong> {{ $vac->approvedBy?->name ?? ('User #'.$vac->approved_by) }}</div>
                                                <div><strong>At:</strong> {{ $vac->approved_at->format('Y-m-d H:i') }}</div>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No vacation requests yet.
                                        </td>
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
        </div>
    </div>
@endsection