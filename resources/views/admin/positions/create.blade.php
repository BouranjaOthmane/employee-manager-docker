@extends('adminlte::page')

@section('title', 'Create Position')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Create Position</h1>

        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus mr-1"></i> New Position</h3>
        </div>

        <form method="POST" action="{{ route('admin.positions.store') }}">
            @csrf

            <div class="card-body">
                @include('admin.positions._form')
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </form>
    </div>
@endsection
