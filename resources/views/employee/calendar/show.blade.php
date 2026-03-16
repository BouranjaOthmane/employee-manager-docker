@extends('adminlte::page')

@section('title', 'My Calendar')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">My Calendar</h1>
            <small class="text-muted">
                {{ $employee->first_name }} {{ $employee->last_name }} • {{ $current->format('F Y') }}
            </small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a class="btn btn-outline-secondary"
               href="{{ route('employee.calendar.show', ['month' => $prevMonth]) }}">
                <i class="fas fa-chevron-left"></i>
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('employee.calendar.show') }}">
                Today
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('employee.calendar.show', ['month' => $nextMonth]) }}">
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="{{ route('employee.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
    <style>
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; }
        .cal-head { font-weight: 600; text-align: center; color: #6c757d; }
        .cal-cell {
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 10px;
            padding: 10px;
            min-height: 90px;
            position: relative;
        }
        .cal-cell.out { opacity: .35; }
        .cal-empty { background: rgba(0,0,0,.02); }
        .cal-daynum { font-weight: 700; }
        .cal-badge {
            position: absolute;
            bottom: 8px;
            left: 10px;
            right: 10px;
            font-size: 12px;
            padding: 6px 8px;
            border-radius: 8px;
            text-align: center;
        }
        .bg-working { background: #28a745; color: #fff; }
        .bg-off { background: #dc3545; color: #fff; }
        .bg-vacation { background: #ffc107; color: #212529; }

        .legend { display: flex; gap: 12px; flex-wrap: wrap; }
        .legend span { display: inline-flex; align-items: center; gap: 6px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
    </style>

    <div class="card card-outline card-secondary">
        <div class="card-body">

            <div class="legend mb-3">
                <span><i class="dot" style="background:#28a745"></i> Working</span>
                <span><i class="dot" style="background:#dc3545"></i> Days off</span>
                <span><i class="dot" style="background:#ffc107"></i> Vacation</span>
                <span class="text-muted">• Read only calendar</span>
            </div>

            <div class="cal-grid mb-2">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dow)
                    <div class="cal-head">{{ $dow }}</div>
                @endforeach
            </div>

            <div class="cal-grid">
                @foreach ($days as $day)
                    @php
                        $type = $day['type'];
                        $cls = $type === 'working'
                            ? 'bg-working'
                            : ($type === 'off'
                                ? 'bg-off'
                                : ($type === 'vacation' ? 'bg-vacation' : ''));
                    @endphp

                    <div class="cal-cell @if(!$day['in_month']) out @endif @if($type === 'empty') cal-empty @endif"
                         title="{{ $day['tooltip'] ?? '' }}">

                        <div class="cal-daynum">{{ $day['date']->day }}</div>

                        @if($type !== 'empty')
                            <div class="cal-badge {{ $cls }}">
                                {{ $day['label'] }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection