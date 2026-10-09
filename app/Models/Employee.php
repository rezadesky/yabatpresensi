<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'institution_id',
        'nip_nidn',
        'name',
        'email',
        'phone',
        'gender',
        'position',
        'employment_status',
        'is_active',
        'address',
        'join_date',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'join_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
