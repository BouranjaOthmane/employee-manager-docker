@extends('adminlte::page')

@section('title', 'Employee Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">{{ $employee->full_name }}</h1>
            <small class="text-muted">
                {{ $employee->position?->title ?? '—' }}
                •
                @if ($employee->status === 'active')
                    <span class="badge badge-success">Actif</span>
                @else
                    <span class="badge badge-secondary">Inactif</span>
                @endif
            </small>
        </div>

        <div>
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Flash message --}}
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif
    @if (session('temp_password'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Temporary password:</strong> {{ session('temp_password') }}
            <br>
            <small>Save it now. It will not be shown again.</small>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif



    <div class="row">
        {{-- LEFT CARD --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <h5 class="mb-1">{{ $employee->full_name }}</h5>
                    <div class="text-muted mb-3">{{ $employee->position?->title ?? '—' }}</div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <strong>Email :</strong> {{ $employee->email ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Téléphone :</strong> {{ $employee->phone ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Date d'embauche :</strong> {{ $employee->hire_date?->format('Y-m-d') ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>CIN :</strong> {{ $employee->cin ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>CNSS :</strong> {{ $employee->cnss ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Email de connexion :</strong> {{ $employee->user?->email ?? '—' }}
                        </li>
                        <li class="list-group-item px-0">
                            <strong>Accès employé :</strong>
                            @if ($employee->user)
                                <span class="badge badge-success">Activé</span>
                            @else
                                <span class="badge badge-secondary">Non créé</span>
                            @endif
                        </li>

                    </ul>
                    @if ($employee->user)
                        <form action="{{ route('admin.employees.reset-password', $employee) }}" method="POST"
                            onsubmit="return confirm('Reset this employee password?')">
                            @csrf
                            <button class="btn btn-outline-danger btn-block mt-3">
                                <i class="fas fa-key mr-1"></i> Reset Password
                            </button>
                        </form>
                    @endif


                    <hr>

                    <div class="d-flex justify-content-between">
                        <span class="badge badge-info p-2">Documents : {{ $employee->documents?->count() ?? 0 }}</span>
                        <span class="badge badge-warning p-2">Congés : {{ $employee->vacations?->count() ?? 0 }}</span>
                        <span class="badge badge-success p-2">Salaires : {{ $employee->salaries?->count() ?? 0 }}</span>
                    </div>
                </div>

                <div class="card-footer">
                    <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST"
                        onsubmit="return confirm('Supprimer cet employé ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-block">
                            <i class="fas fa-trash mr-1"></i> Supprimer l'employé
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT TABS --}}
        <div class="col-md-8">
            <div class="card card-outline card-secondary">
                @php $tab = request('tab', 'overview'); @endphp

                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'overview') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=overview">
                                <i class="fas fa-info-circle mr-1"></i> Aperçu
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'documents') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=documents">
                                <i class="fas fa-folder-open mr-1"></i> Documents
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'vacations') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=vacations">
                                <i class="fas fa-plane-departure mr-1"></i> Congés
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if ($tab === 'salaries') active @endif"
                                href="{{ route('admin.employees.show', $employee) }}?tab=salaries">
                                <i class="fas fa-money-bill-wave mr-1"></i> Salaires
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.employees.calendar.show', $employee) }}" class="nav-link">
                                <i class="fas fa-calendar-alt mr-1"></i> Calendrier
                            </a>
                        </li>

                    </ul>
                </div>

                <div class="card-body">
                    @if ($tab === 'documents')
                        @include('admin.employees.tabs.documents')
                    @elseif ($tab === 'vacations')
                        @include('admin.employees.tabs.vacations')
                    @elseif ($tab === 'salaries')
                        @include('admin.employees.tabs.salaries')
                    @else
                        @include('admin.employees.tabs.overview')
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
