<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
    ];

    public function breaktimes()
    {
        return $this->hasMany(Breaktime::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}