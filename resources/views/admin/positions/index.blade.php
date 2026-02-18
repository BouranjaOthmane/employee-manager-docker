@extends('adminlte::page')

@section('title', 'Positions')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Positions</h1>

        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Position
        </a>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-1"></i> Search</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.positions.index') }}">
                <div class="row">
                    <div class="col-md-10">
                        <div class="input-group">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Search by title..."
                                   value="{{ request('q') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 mt-2 mt-md-0">
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary btn-block">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-briefcase mr-1"></i> Positions List
            </h3>

            <div class="card-tools">
                <span class="badge badge-info p-2">Total: {{ $positions->total() }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 90px;">#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th style="width: 140px;">Employees</th>
                            <th class="text-right" style="width: 170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $position)
                            <tr>
                                <td class="text-muted">#{{ $position->id }}</td>
                                <td><strong>{{ $position->title }}</strong></td>
                                <td class="text-muted">
                                    {{ \Illuminate\Support\Str::limit($position->description, 80) ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge badge-secondary p-2">
                                        {{ $position->employees_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('admin.positions.edit', $position) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('admin.positions.destroy', $position) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this position?')">
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    No positions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($positions->hasPages())
            <div class="card-footer">
                {{ $positions->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
