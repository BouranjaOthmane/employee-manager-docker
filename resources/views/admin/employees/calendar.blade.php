@extends('adminlte::page')

@section('title', 'Employee Calendar')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Calendar — {{ $employee->full_name }}</h1>
            <small class="text-muted">{{ $current->format('F Y') }}</small>
        </div>

        <div class="d-flex" style="gap:8px;">
            <a class="btn btn-outline-secondary"
               href="{{ route('admin.employees.calendar.show', $employee) }}?month={{ $prevMonth }}">
                <i class="fas fa-chevron-left"></i>
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('admin.employees.calendar.show', $employee) }}">
                Today
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('admin.employees.calendar.show', $employee) }}?month={{ $nextMonth }}">
                <i class="fas fa-chevron-right"></i>
            </a>

            <a class="btn btn-primary"
               href="{{ route('admin.employees.show', $employee) }}?tab=overview">
                <i class="fas fa-user mr-1"></i> Profile
            </a>
        </div>
    </div>
@endsection

@section('content')
    <style>
        .cal-grid { display:grid; grid-template-columns: repeat(7, 1fr); gap:10px; }
        .cal-head { font-weight:600; text-align:center; color:#6c757d; }
        .cal-cell {
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 10px;
            padding: 10px;
            min-height: 90px;
            position: relative;
        }
        .cal-cell.out { opacity: .35; }
        .cal-daynum { font-weight:700; }
        .cal-badge {
            position:absolute; bottom:8px; left:10px; right:10px;
            font-size: 12px; padding: 6px 8px; border-radius: 8px;
            color: #fff; text-align:center;
        }
        .bg-working { background:#28a745; }   /* green */
        .bg-off { background:#dc3545; }       /* red */
        .bg-vacation { background:#ffc107; color:#212529; } /* yellow */
        .legend { display:flex; gap:12px; flex-wrap:wrap; }
        .legend span { display:inline-flex; align-items:center; gap:6px; }
        .dot { width:12px; height:12px; border-radius:50%; display:inline-block; }
    </style>

    <div class="card card-outline card-secondary">
        <div class="card-body">

            <div class="legend mb-3">
                <span><i class="dot" style="background:#28a745"></i> Working</span>
                <span><i class="dot" style="background:#dc3545"></i> Days off (weekend/holiday)</span>
                <span><i class="dot" style="background:#ffc107"></i> Vacation</span>
            </div>

            <div class="cal-grid mb-2">
                @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                    <div class="cal-head">{{ $dow }}</div>
                @endforeach
            </div>

            <div class="cal-grid">
                @foreach ($days as $day)
                    @php
                        $type = $day['type'];
                        $cls = $type === 'working' ? 'bg-working' : ($type === 'off' ? 'bg-off' : 'bg-vacation');
                    @endphp

                    <div class="cal-cell @if(!$day['in_month']) out @endif" title="{{ $day['tooltip'] }}">
                        <div class="cal-daynum">{{ $day['date']->day }}</div>

                        <div class="cal-badge {{ $cls }}">
                            {{ $day['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection
