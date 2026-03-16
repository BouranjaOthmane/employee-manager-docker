@extends('adminlte::page')

@section('title', 'Notifications')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Notifications</h1>
            <small class="text-muted">Admin and HR system notifications</small>
        </div>

        <div>
            <form method="POST" action="{{ route('admin.notifications.readAll') }}">
                @csrf
                <button class="btn btn-primary">
                    <i class="fas fa-check-double mr-1"></i> Mark all as read
                </button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Success" dismissable>
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    <div class="card card-outline card-primary">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <div class="list-group-item {{ $isUnread ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    {{ $data['title'] ?? 'Notification' }}
                                    @if($isUnread)
                                        <span class="badge badge-warning ml-2">New</span>
                                    @endif
                                </h6>

                                <p class="mb-1 text-muted">
                                    {{ $data['message'] ?? '' }}
                                </p>

                                @if(!empty($data['employee_name']))
                                    <small class="text-muted d-block">
                                        Employee: {{ $data['employee_name'] }}
                                    </small>
                                @endif

                                @if(!empty($data['start_date']) && !empty($data['end_date']))
                                    <small class="text-muted d-block">
                                        Dates: {{ $data['start_date'] }} → {{ $data['end_date'] }}
                                    </small>
                                @endif

                                <small class="text-muted">
                                    {{ $notification->created_at?->format('Y-m-d H:i') }}
                                </small>
                            </div>

                            <div class="text-right">
                                <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-primary">
                                        Open
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-4">
                        No notifications available.
                    </div>
                @endforelse
            </div>
        </div>

        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection