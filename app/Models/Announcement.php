<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['course_id', 'posted_by', 'title', 'body'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}