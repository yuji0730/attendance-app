<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
    ];

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function modificationRequest()
    {
        return $this->hasOne(ModificationRequest::class);
    }


}
