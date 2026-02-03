<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = [
        'position_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cin',
        'cnss',
        'hire_date',
        'status',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
