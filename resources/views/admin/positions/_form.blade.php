<div class="form-group">
    <label>Intitulé <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $position->title ?? '') }}" placeholder="ex : Responsable RH" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
        placeholder="Description optionnelle...">{{ old('description', $position->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>