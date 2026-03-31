<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreaktimeApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_application_id', 'break_start', 'break_end'
    ];

    public function attendanceApplication()
    {
        return $this->belongsTo(AttendanceApplication::class);
    }
}