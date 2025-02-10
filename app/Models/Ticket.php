<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'priority',
        'user_id',
        'status',
        'entry_channel',
        'due_date',
        'date_opened',
        'closed_date',
        'attachements',
        'comments',
        'addressed_to',
        'resolution',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function addressedTo()
    {
        return $this->belongsTo(User::class, 'addressed_to');
    }

    public function followers()
    {
        return $this->hasMany(TicketFollower::class);
    }

}
