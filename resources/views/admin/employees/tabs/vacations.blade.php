<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="fas fa-plane-departure mr-1"></i> Vacations</h5>
    <button class="btn btn-primary" data-toggle="collapse" data-target="#createVacation">
        <i class="fas fa-plus mr-1"></i> Add Vacation
    </button>
</div>

<div id="createVacation" class="collapse mb-3">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.employees.vacations.store', $employee) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Start date</label>
                        <input type="date" name="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date') }}" required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>End date</label>
                        <input type="date" name="end_date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date') }}" required>
                        @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Type</label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            @php $t = old('type','paid'); @endphp
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
                    <textarea name="reason" rows="2"
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

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Dates</th>
            <th>Type</th>
            <th>Status</th>
            <th>Reason</th>
        </tr>
        </thead>
        <tbody>
        @forelse($employee->vacations as $vac)
            <tr>
                <td>
                    {{ $vac->start_date?->format('Y-m-d') }}
                    →
                    {{ $vac->end_date?->format('Y-m-d') }}
                    <small class="text-muted d-block">
                        {{ $vac->start_date && $vac->end_date ? $vac->start_date->diffInDays($vac->end_date) + 1 : '' }}
                        day(s)
                    </small>
                </td>
                <td class="text-capitalize">{{ $vac->type }}</td>
                <td>
                    @if($vac->status === 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($vac->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                    @endif
                </td>
                <td class="text-muted">{{ $vac->reason ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-3">No vacations yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
