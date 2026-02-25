<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="fas fa-folder-open mr-1"></i> Documents</h5>
    <button class="btn btn-primary" data-toggle="collapse" data-target="#uploadDoc">
        <i class="fas fa-upload mr-1"></i> Télécharger
    </button>
</div>

<div id="uploadDoc" class="collapse mb-3">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.employees.documents.store', $employee) }}"
                  enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Type</label>
                        <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                            @php $typeVal = old('type'); @endphp
                            <option value="contract" @selected($typeVal==='contract')>Contrat</option>
                            <option value="cin" @selected($typeVal==='cin')>CIN</option>
                            <option value="cnss" @selected($typeVal==='cnss')>CNSS</option>
                            <option value="diploma" @selected($typeVal==='diploma')>Diplôme</option>
                            <option value="other" @selected($typeVal==='other')>Autre</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Titre (optionnel)</label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="ex : Contrat de travail 2026">
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Fichier (PDF/JPG/PNG)</label>
                        <input type="file" name="file"
                               class="form-control-file @error('file') is-invalid @enderror"
                               required>
                        @error('file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Enregistrer le document
                </button>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>Titre</th>
                <th>Ajouté le</th>
                <th class="text-right" style="width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($employee->documents as $doc)
            <tr>
                <td><span class="badge badge-info text-uppercase">{{ $doc->type }}</span></td>
                <td>{{ $doc->title ?? '—' }}</td>
                <td class="text-muted">{{ $doc->created_at?->format('Y-m-d H:i') }}</td>
                <td class="text-right">
                    <a href="{{ route('admin.documents.download', $doc) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i>
                    </a>

                    <form action="{{ route('admin.documents.destroy', $doc) }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Supprimer ce document ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-3">Aucun document pour le moment.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>