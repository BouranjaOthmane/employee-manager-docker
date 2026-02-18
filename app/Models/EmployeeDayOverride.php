<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDayOverride extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
