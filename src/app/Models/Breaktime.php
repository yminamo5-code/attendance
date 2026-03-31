<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breaktime extends Model
{
    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];
}