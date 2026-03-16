<?php

namespace App\Notifications;

use App\Models\Vacation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewVacationRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Vacation $vacation
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $employee = $this->vacation->employee;

        return [
            'title' => 'New vacation request',
            'message' => 'A new vacation request has been submitted by '
                . ($employee?->first_name ?? '')
                . ' '
                . ($employee?->last_name ?? ''),
            'vacation_id' => $this->vacation->id,
            'employee_id' => $this->vacation->employee_id,
            'employee_name' => trim(($employee?->first_name ?? '') . ' ' . ($employee?->last_name ?? '')),
            'start_date' => optional($this->vacation->start_date)->format('Y-m-d'),
            'end_date' => optional($this->vacation->end_date)->format('Y-m-d'),
            'type' => $this->vacation->type,
            'reason' => $this->vacation->reason,
            'url' => route('admin.employees.show', $this->vacation->employee_id) . '?tab=vacations',
        ];
    }
}