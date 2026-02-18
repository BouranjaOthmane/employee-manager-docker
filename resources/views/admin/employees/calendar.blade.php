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
            cursor: pointer;
            transition: transform .06s ease-in-out;
        }
        .cal-cell:hover { transform: translateY(-1px); }
        .cal-cell.out { opacity: .35; }
        .cal-daynum { font-weight:700; }
        .cal-badge {
            position:absolute; bottom:8px; left:10px; right:10px;
            font-size: 12px; padding: 6px 8px; border-radius: 8px;
            text-align:center;
        }

        /* Status colors */
        .bg-working { background:#28a745; color:#fff; }     /* green */
        .bg-off { background:#dc3545; color:#fff; }         /* red */
        .bg-vacation { background:#ffc107; color:#212529; } /* yellow */

        .legend { display:flex; gap:12px; flex-wrap:wrap; }
        .legend span { display:inline-flex; align-items:center; gap:6px; }
        .dot { width:12px; height:12px; border-radius:50%; display:inline-block; }
    </style>

    <div class="card card-outline card-secondary">
        <div class="card-body">

            <div class="legend mb-3">
                <span><i class="dot" style="background:#28a745"></i> Working</span>
                <span><i class="dot" style="background:#dc3545"></i> Days off</span>
                <span><i class="dot" style="background:#ffc107"></i> Vacation</span>
                <span class="text-muted">• Click a day to update</span>
            </div>

            <div class="cal-grid mb-2">
                @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
                    <div class="cal-head">{{ $dow }}</div>
                @endforeach
            </div>

            <div class="cal-grid">
                @foreach ($days as $day)
                    @php
                        $type = $day['type']; // working/off/vacation
                        $cls = $type === 'working' ? 'bg-working' : ($type === 'off' ? 'bg-off' : 'bg-vacation');
                        $dateStr = $day['date']->format('Y-m-d');
                    @endphp

                    <div class="cal-cell @if(!$day['in_month']) out @endif"
                         data-date="{{ $dateStr }}"
                         data-tooltip="{{ $day['tooltip'] }}">
                        <div class="cal-daynum">{{ $day['date']->day }}</div>

                        <div class="cal-badge {{ $cls }}">
                            {{ $day['label'] }}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="dayModal" tabindex="-1" role="dialog" aria-labelledby="dayModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="dayModalLabel">Update Day Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="mb-2 text-muted">
                        Date: <strong id="modalDateText">—</strong>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select id="modalStatus" class="form-control">
                            <option value="working">Working (green)</option>
                            <option value="off">Day off (red)</option>
                            <option value="vacation">Vacation (yellow)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Reason (optional)</label>
                        <input type="text" id="modalReason" class="form-control" placeholder="e.g. Special off day">
                    </div>

                    <div class="alert alert-info py-2 mb-0">
                        This will create a <strong>manual override</strong> for that employee/day.
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" id="modalRemoveBtn">
                        Remove override
                    </button>

                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="modalSaveBtn">
                            <i class="fas fa-save mr-1"></i> Save
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const employeeId = @json($employee->id);
            const csrf = @json(csrf_token());

            let selectedDate = null;

            function urlShow(date) {
                return @json(route('admin.employees.calendar.day.show', $employee)) + '?date=' + encodeURIComponent(date);
            }

            function urlStore() {
                return @json(route('admin.employees.calendar.day.store', $employee));
            }

            function urlDelete() {
                return @json(route('admin.employees.calendar.day.destroy', $employee));
            }

            async function fetchDay(date) {
                const res = await fetch(urlShow(date), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error('Failed to fetch day info');
                return await res.json();
            }

            async function saveDay(date, status, reason) {
                const res = await fetch(urlStore(), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ date, status, reason })
                });
                if (!res.ok) throw new Error('Failed to save');
                return await res.json();
            }

            async function removeDay(date) {
                const res = await fetch(urlDelete(), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ date })
                });
                if (!res.ok) throw new Error('Failed to remove');
                return await res.json();
            }

            document.querySelectorAll('.cal-cell').forEach(cell => {
                cell.addEventListener('click', async () => {
                    selectedDate = cell.dataset.date;
                    document.getElementById('modalDateText').textContent = selectedDate;

                    // Default values
                    document.getElementById('modalStatus').value = 'working';
                    document.getElementById('modalReason').value = '';

                    // Load existing override (if any)
                    try {
                        const data = await fetchDay(selectedDate);
                        if (data.override) {
                            document.getElementById('modalStatus').value = data.override.status;
                            document.getElementById('modalReason').value = data.override.reason || '';
                            document.getElementById('modalRemoveBtn').disabled = false;
                        } else {
                            document.getElementById('modalRemoveBtn').disabled = true;
                        }
                    } catch (e) {
                        document.getElementById('modalRemoveBtn').disabled = true;
                    }

                    $('#dayModal').modal('show');
                });
            });

            document.getElementById('modalSaveBtn').addEventListener('click', async () => {
                if (!selectedDate) return;

                const status = document.getElementById('modalStatus').value;
                const reason = document.getElementById('modalReason').value;

                try {
                    await saveDay(selectedDate, status, reason);
                    // simplest: reload the page to re-render colors
                    window.location.reload();
                } catch (e) {
                    alert('Error saving day. Check logs.');
                }
            });

            document.getElementById('modalRemoveBtn').addEventListener('click', async () => {
                if (!selectedDate) return;
                if (!confirm('Remove override for this day?')) return;

                try {
                    await removeDay(selectedDate);
                    window.location.reload();
                } catch (e) {
                    alert('Error removing override. Check logs.');
                }
            });
        })();
    </script>
@endsection
