<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['course_id', 'title', 'day', 'time'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}