@extends('adminlte::page')

@section('title', 'Holidays')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Holidays</h1>

        <a href="{{ route('admin.holidays.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Holiday
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
        <div class="card-body">
            <form method="GET" action="{{ route('admin.holidays.index') }}">
                <div class="row">
                    <div class="col-md-10">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control"
                                   placeholder="Search date, name, reason..."
                                   value="{{ request('q') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2 mt-2 mt-md-0">
                        <a href="{{ route('admin.holidays.index') }}" class="btn btn-outline-secondary btn-block">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-day mr-1"></i> Holidays List</h3>
            <div class="card-tools">
                <span class="badge badge-info p-2">Total: {{ $holidays->total() }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Reason</th>
                            <th class="text-right" style="width: 170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($holidays as $holiday)
                        <tr>
                            <td><strong>{{ $holiday->date?->format('Y-m-d') }}</strong></td>
                            <td>{{ $holiday->name }}</td>
                            <td class="text-muted">{{ $holiday->reason ?? '—' }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.holidays.edit', $holiday) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.holidays.destroy', $holiday) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this holiday?')">
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
                            <td colspan="4" class="text-center text-muted py-4">No holidays found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($holidays->hasPages())
            <div class="card-footer">
                {{ $holidays->links() }}
            </div>
        @endif
    </div>
@endsection
