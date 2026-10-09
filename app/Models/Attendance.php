<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'institution_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'latitude_in',
        'longitude_in',
        'latitude_out',
        'longitude_out',
        'photo_in',
        'photo_out',
        'device_info',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'latitude_in' => 'float',
        'longitude_in' => 'float',
        'latitude_out' => 'float',
        'longitude_out' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
