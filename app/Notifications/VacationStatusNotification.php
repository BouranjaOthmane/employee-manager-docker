<?php

namespace App\Notifications;

use App\Models\Vacation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VacationStatusNotification extends Notification
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
        $status = $this->vacation->status;
        $employee = $this->vacation->employee;

        return [
            'title' => $status === 'approved'
                ? 'Vacation approved'
                : 'Vacation rejected',

            'message' => $status === 'approved'
                ? 'Your vacation request has been approved.'
                : 'Your vacation request has been rejected.',

            'status' => $status,
            'vacation_id' => $this->vacation->id,
            'employee_id' => $this->vacation->employee_id,
            'start_date' => optional($this->vacation->start_date)->format('Y-m-d'),
            'end_date' => optional($this->vacation->end_date)->format('Y-m-d'),
            'type' => $this->vacation->type,
            'reason' => $this->vacation->reason,
            'url' => route('employee.vacations.index'),
        ];
    }
}