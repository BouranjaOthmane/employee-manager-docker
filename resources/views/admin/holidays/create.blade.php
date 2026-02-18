@extends('adminlte::page')

@section('title', 'Create Holiday')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Create Holiday</h1>
        <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
@endsection

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus mr-1"></i> New Holiday</h3>
        </div>

        <form method="POST" action="{{ route('admin.holidays.store') }}">
            @csrf
            <div class="card-body">
                @include('admin.holidays._form')
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save</button>
            </div>
        </form>
    </div>
@endsection
