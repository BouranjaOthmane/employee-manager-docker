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
        <i class="fas fa-plane-departure mr-1"></i> Congés
    </h5>

    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#createVacation"
            aria-expanded="false" aria-controls="createVacation">
        <i class="fas fa-plus mr-1"></i> Ajouter un congé
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
                        <label>Date de début <span class="text-danger">*</span></label>
                        <input type="date"
                               name="start_date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date') }}"
                               required>
                        @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Date de fin <span class="text-danger">*</span></label>
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
                            <option value="paid" @selected($t==='paid')>Payé</option>
                            <option value="unpaid" @selected($t==='unpaid')>Non payé</option>
                            <option value="sick" @selected($t==='sick')>Maladie</option>
                            <option value="other" @selected($t==='other')>Autre</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group col-md-3">
                        <label>Statut</label>
                        <input type="text" class="form-control" value="En attente" disabled>
                        <small class="text-muted">Les nouvelles demandes sont en attente par défaut.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Motif (optionnel)</label>
                    <textarea name="reason"
                              rows="2"
                              class="form-control @error('reason') is-invalid @enderror"
                              placeholder="Motif...">{{ old('reason') }}</textarea>
                    @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Enregistrer le congé
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
                <th>Statut</th>
                <th>Motif</th>
                <th>Informations d’approbation</th>
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
                            {{ $vac->start_date->diffInDays($vac->end_date) + 1 }} jour(s)
                        </small>
                    @endif
                </td>

                <td class="text-capitalize">
                    <span class="badge badge-light p-2">{{ $vac->type }}</span>
                </td>

                <td>
                    @if($vac->status === 'approved')
                        <span class="badge badge-success">Approuvé</span>
                    @elseif($vac->status === 'rejected')
                        <span class="badge badge-danger">Refusé</span>
                    @else
                        <span class="badge badge-warning">En attente</span>
                    @endif
                </td>

                <td class="text-muted">
                    {{ $vac->reason ?? '—' }}
                </td>

                <td class="text-muted">
                    @if($vac->approved_at)
                        <div><strong>Par :</strong> {{ $vac->approvedBy?->name ?? ('Utilisateur #'.$vac->approved_by) }}</div>
                        <div><strong>Le :</strong> {{ $vac->approved_at->format('Y-m-d H:i') }}</div>
                    @else
                        —
                    @endif
                </td>

                <td class="text-right">
                    @if($vac->status === 'pending')
                        <form class="d-inline" method="POST" action="{{ route('admin.vacations.approve', $vac) }}"
                              onsubmit="return confirm('Approuver ce congé ?')">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Approuver
                            </button>
                        </form>

                        <form class="d-inline" method="POST" action="{{ route('admin.vacations.reject', $vac) }}"
                              onsubmit="return confirm('Refuser ce congé ?')">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-times"></i> Refuser
                            </button>
                        </form>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    Aucun congé pour le moment.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
