{{-- resources/views/admin/employees/tabs/vacations.blade.php --}}

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success:</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">
        <i class="fas fa-plane-departure mr-1"></i> Vacations
    </h5>

    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#createVacation"
            aria-expanded="false" aria-controls="createVacation">
        <i class="fas fa-plus mr-1"></i> Add Vacation
    </button>
</div>

{{-- Create Vacation Form --}}
<div id="createVacation" class="collapse mb-3">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.employees.vacations.store', $employee) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Start date <span class="text-danger">*</span></label>
                        <input type="date"
                               name="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date') }}"
                               required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>End date <span class="text-danger">*</span></label>
                        <input type="date"
                               name="end_date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date') }}"
                               required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Type <span class="text-danger">*</span></label>
                        @php $t = old('type', 'paid'); @endphp
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="paid" @selected($t==='paid')>Paid</option>
                            <option value="unpaid" @selected($t==='unpaid')>Unpaid</option>
                            <option value="sick" @selected($t==='sick')>Sick</option>
                            <option value="other" @selected($t==='other')>Other</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Status</label>
                        <input type="text" class="form-control" value="pending" disabled>
                        <small class="text-muted">New requests are pending by default.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reason (optional)</label>
                    <textarea name="reason"
                              rows="2"
                              class="form-control @error('reason') is-invalid @enderror"
                              placeholder="Reason...">{{ old('reason') }}</textarea>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Vacation
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Vacations Table --}}
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead class="thead-light">
            <tr>
                <th>Dates</th>
                <th>Type</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Approved info</th>
                <th class="text-right" style="width: 200px;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($employee->vacations as $vac)
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
                    {{ $vac->reason ?? '—' }}
                </td>

                <td class="text-muted">
                    @if($vac->approved_at)
                        <div><strong>By:</strong> {{ $vac->approvedBy?->name ?? ('User #'.$vac->approved_by) }}</div>
                        <div><strong>At:</strong> {{ $vac->approved_at->format('Y-m-d H:i') }}</div>
                    @else
                        —
                    @endif
                </td>

                <td class="text-right">
                    {{-- @role('admin|hr') --}}
                        @if($vac->status === 'pending')
                            <form class="d-inline" method="POST" action="{{ route('admin.vacations.approve', $vac) }}"
                                  onsubmit="return confirm('Approve this vacation?')">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>

                            <form class="d-inline" method="POST" action="{{ route('admin.vacations.reject', $vac) }}"
                                  onsubmit="return confirm('Reject this vacation?')">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    {{-- @else --}}
                        {{-- <span class="text-muted">No access</span> --}}
                    {{-- @endrole --}}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    No vacations yet.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
