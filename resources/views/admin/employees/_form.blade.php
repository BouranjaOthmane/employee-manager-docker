@php
    $isEdit = isset($employee);
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>First name <span class="text-danger">*</span></label>
            <input type="text"
                   name="first_name"
                   class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name', $employee->first_name ?? '') }}"
                   required>
            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Last name <span class="text-danger">*</span></label>
            <input type="text"
                   name="last_name"
                   class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name', $employee->last_name ?? '') }}"
                   required>
            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $employee->email ?? '') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Phone</label>
            <input type="text"
                   name="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $employee->phone ?? '') }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label>Position</label>
            <select name="position_id" class="form-control @error('position_id') is-invalid @enderror">
                <option value="">— Select position —</option>
                @foreach ($positions as $pos)
                    <option value="{{ $pos->id }}"
                        @selected(old('position_id', $employee->position_id ?? '') == $pos->id)>
                        {{ $pos->title }}
                    </option>
                @endforeach
            </select>
            @error('position_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Hire date</label>
            <input type="date"
                   name="hire_date"
                   class="form-control @error('hire_date') is-invalid @enderror"
                   value="{{ old('hire_date', optional($employee->hire_date ?? null)->format('Y-m-d')) }}">
            @error('hire_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Status <span class="text-danger">*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                @php $statusVal = old('status', $employee->status ?? 'active'); @endphp
                <option value="active" @selected($statusVal === 'active')>Active</option>
                <option value="inactive" @selected($statusVal === 'inactive')>Inactive</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>CIN</label>
            <input type="text"
                   name="cin"
                   class="form-control @error('cin') is-invalid @enderror"
                   value="{{ old('cin', $employee->cin ?? '') }}">
            @error('cin') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>CNSS</label>
            <input type="text"
                   name="cnss"
                   class="form-control @error('cnss') is-invalid @enderror"
                   value="{{ old('cnss', $employee->cnss ?? '') }}">
            @error('cnss') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>
