<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'course_id',
        'parent_id',
        'subject',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // The original message in a thread
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    // All replies to this message
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id')->orderBy('created_at');
    }

    // Sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Course tag
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Check if message is unread
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    // Scope — only root messages (not replies)
    public function scopeThreads($query)
    {
        return $query->whereNull('parent_id');
    }

    // Scope — messages involving a user (sent or received)
    public function scopeForUser($query, int $userId)
    {
        return $query->where('sender_id', $userId)
                     ->orWhere('receiver_id', $userId);
    }
}