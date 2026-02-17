<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="fas fa-money-bill-wave mr-1"></i> Salaries</h5>
    <button class="btn btn-primary" data-toggle="collapse" data-target="#createSalary">
        <i class="fas fa-plus mr-1"></i> Add Salary
    </button>
</div>

<div id="createSalary" class="collapse mb-3">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.employees.salaries.store', $employee) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Month</label>
                        <input type="month"
                               name="month"
                               class="form-control @error('month') is-invalid @enderror"
                               value="{{ old('month', now()->format('Y-m')) }}"
                               required>
                        @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">We save as first day of month.</small>
                    </div>

                    <div class="form-group col-md-3">
                        <label>Base salary</label>
                        <input type="number" step="0.01" min="0"
                               name="base_salary"
                               class="form-control @error('base_salary') is-invalid @enderror"
                               value="{{ old('base_salary') }}" required>
                        @error('base_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Bonus</label>
                        <input type="number" step="0.01" min="0"
                               name="bonus"
                               class="form-control @error('bonus') is-invalid @enderror"
                               value="{{ old('bonus', 0) }}">
                        @error('bonus') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Deduction</label>
                        <input type="number" step="0.01" min="0"
                               name="deduction"
                               class="form-control @error('deduction') is-invalid @enderror"
                               value="{{ old('deduction', 0) }}">
                        @error('deduction') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Note (optional)</label>
                    <textarea name="note" rows="2"
                              class="form-control @error('note') is-invalid @enderror"
                              placeholder="Note...">{{ old('note') }}</textarea>
                    @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Salary
                </button>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Month</th>
            <th>Base</th>
            <th>Bonus</th>
            <th>Deduction</th>
            <th>Net</th>
            <th>Note</th>
        </tr>
        </thead>
        <tbody>
        @forelse($employee->salaries as $sal)
            <tr>
                <td>{{ $sal->month?->format('Y-m') }}</td>
                <td>{{ number_format($sal->base_salary, 2) }}</td>
                <td>{{ number_format($sal->bonus, 2) }}</td>
                <td>{{ number_format($sal->deduction, 2) }}</td>
                <td><strong>{{ number_format($sal->net_salary, 2) }}</strong></td>
                <td class="text-muted">{{ $sal->note ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-3">No salary records yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
