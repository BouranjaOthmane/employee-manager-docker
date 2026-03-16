@extends('adminlte::page')

@section('title', 'My Documents')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">My Documents</h1>
            <small class="text-muted">
                Download your personal and work-related files
            </small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('employee.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>

            <a href="{{ route('employee.profile') }}" class="btn btn-outline-primary">
                <i class="fas fa-user mr-1"></i> My Profile
            </a>
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-folder-open mr-1"></i> Information
                    </h3>
                </div>

                <div class="card-body">
                    <p class="mb-2">
                        Here you can download your files such as:
                    </p>

                    <ul class="mb-3">
                        <li>Contract</li>
                        <li>CIN copy</li>
                        <li>CNSS documents</li>
                        <li>Diplomas</li>
                        <li>Other HR files</li>
                    </ul>

                    <div class="alert alert-info mb-0">
                        Total documents:
                        <strong>{{ $documents->total() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-1"></i> My Files
                    </h3>

                    <div class="card-tools">
                        <span class="badge badge-info p-2">
                            {{ $documents->total() }} file(s)
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Added</th>
                                    <th class="text-right" style="width: 120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary text-uppercase p-2">
                                                {{ $document->type }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $document->title ?? '—' }}
                                        </td>

                                        <td class="text-muted">
                                            {{ $document->created_at?->format('Y-m-d H:i') }}
                                        </td>

                                        <td class="text-right">
                                            <a href="{{ route('employee.documents.download', $document) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No documents available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($documents->hasPages())
                    <div class="card-footer">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection