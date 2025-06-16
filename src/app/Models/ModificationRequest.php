<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'date',
        'clock_in',
        'clock_out',
        'rests',
        'remarks',
        'status',
    ];

    protected $casts = [
        'rests' => 'array',
    ];


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function attendance() {
        return $this->belongsTo(Attendance::class);
    }
}
