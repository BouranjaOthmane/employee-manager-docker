<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'month', 'base_salary', 'bonus', 'deduction', 'net_salary', 'note'];

    protected $casts = [
        'month' => 'date',
    ];
      public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee::class);
    }
}
