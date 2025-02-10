<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = ['message', 'sender', 'recipient', 'status', 'read_at'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender');
    }

    // Rename the method to avoid conflict
    public function recipientUser()
    {
        return $this->belongsTo(User::class, 'recipient');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender');
    }
}
