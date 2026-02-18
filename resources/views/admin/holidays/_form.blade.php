<div class="form-group">
    <label>Date <span class="text-danger">*</span></label>
    <input type="date"
           name="date"
           class="form-control @error('date') is-invalid @enderror"
           value="{{ old('date', isset($holiday) ? $holiday->date?->format('Y-m-d') : '') }}"
           required>
    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Name <span class="text-danger">*</span></label>
    <input type="text"
           name="name"
           class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $holiday->name ?? '') }}"
           placeholder="e.g. Labour Day"
           required>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label>Reason</label>
    <input type="text"
           name="reason"
           class="form-control @error('reason') is-invalid @enderror"
           value="{{ old('reason', $holiday->reason ?? '') }}"
           placeholder="Optional...">
    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
