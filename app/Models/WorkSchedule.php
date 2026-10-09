<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'day_of_week',
        'time_in',
        'time_out',
        'late_tolerance_minutes',
        'is_active',
    ];

    protected $casts = [
        'late_tolerance_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
