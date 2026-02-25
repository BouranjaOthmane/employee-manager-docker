{{-- resources/views/admin/employees/index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Employees')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Employés</h1>

        <a href="{{ route('admin.employees.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus mr-1"></i> Ajouter un employé
        </a>
    </div>
@endsection

@section('content')

    {{-- Flash message --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    {{-- Filters --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-1"></i> Recherche & Filtres
            </h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.employees.index') }}">
                <div class="row">

                    {{-- Search --}}
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Recherche</label>
                            <div class="input-group">
                                <input type="text" name="q" class="form-control"
                                    placeholder="Nom, Email, Téléphone, CIN, CNSS..." value="{{ request('q') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Position filter --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Poste</label>
                            <select name="position_id" class="form-control">
                                <option value="">Tous les postes</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" @selected(request('position_id') == $pos->id)>
                                        {{ $pos->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status filter --}}
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Statut</label>
                            <select name="status" class="form-control">
                                <option value="">Tous</option>
                                <option value="active" @selected(request('status') === 'active')>Actif</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Inactif</option>
                            </select>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                Appliquer
                            </button>
                            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                                Réinitialiser
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Employees table --}}
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users mr-1"></i>
                Liste des employés
            </h3>

            <div class="card-tools">
                <span class="badge badge-info p-2">
                    Total : {{ $employees->total() }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Employé</th>
                            <th>Poste</th>
                            <th>Contact</th>
                            <th>Statut</th>
                            <th>Date d'embauche</th>
                            <th style="width: 220px;" class="text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="text-muted">#{{ $employee->id }}</td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                        <small class="text-muted">
                                            CIN : {{ $employee->cin ?? '—' }} | CNSS : {{ $employee->cnss ?? '—' }}
                                        </small>
                                    </div>
                                </td>

                                <td>
                                    {{ $employee->position?->title ?? '—' }}
                                </td>

                                <td>
                                    <div class="d-flex flex-column">
                                        <span><i class="fas fa-phone mr-1 text-muted"></i>
                                            {{ $employee->phone ?? '—' }}</span>
                                        <span><i class="fas fa-envelope mr-1 text-muted"></i>
                                            {{ $employee->email ?? '—' }}</span>
                                    </div>
                                </td>

                                <td>
                                    @if ($employee->status === 'active')
                                        <span class="badge badge-success">Actif</span>
                                    @else
                                        <span class="badge badge-secondary">Inactif</span>
                                    @endif
                                </td>

                                <td>
                                    {{ optional($employee->hire_date)->format('Y-m-d') ?? '—' }}
                                </td>

                                <td class="text-right">
                                    <a href="{{ route('admin.employees.show', $employee) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.employees.edit', $employee) }}"
                                        class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="{{ route('admin.employees.calendar.show', $employee) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-calendar-alt"></i>
                                    </a>

                                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Supprimer cet employé ?')">
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
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> Aucun employé trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
