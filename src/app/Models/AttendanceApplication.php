<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id', 'user_id', 'work_date', 'clock_in', 'clock_out', 'remarks', 'status'
    ];

    public function breaktimes()
    {
        return $this->hasMany(BreaktimeApplication::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}