@extends('adminlte::page')

@section('title', 'Edit Employee')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Modifier l’employé</h1>
        <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Modifier l’employé</h3>
        </div>

        <form method="POST" action="{{ route('admin.employees.update', $employee) }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                @include('admin.employees._form', ['employee' => $employee, 'positions' => $positions])
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
@endsection
