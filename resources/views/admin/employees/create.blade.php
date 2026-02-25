@extends('adminlte::page')

@section('title', 'Créer un employé')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Créer un employé</h1>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-plus mr-1"></i> Nouvel employé</h3>
        </div>

        <form method="POST" action="{{ route('admin.employees.store') }}">
            @csrf

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Veuillez corriger les erreurs ci-dessous.</strong>
                    </div>
                @endif

                @include('admin.employees._form', ['positions' => $positions])
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Enregistrer l'employé
                </button>
            </div>
        </form>
    </div>
@endsection