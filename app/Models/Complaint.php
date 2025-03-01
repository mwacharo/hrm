<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject',
        'description',
        'is_anonymous',
        'user_id',
        'category',
        'status',
        'priority',
        'date_opened',
        'closed_date',
        'attachment',
        'comments',
        'assignedTo',
        'resolution',
        'links',
        'attachments',
    ];


    // protected $casts = [
    //     'attachments' => 'array',
    //     'links' => 'array',
    // ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'complaint_user')->withPivot('role')->withTimestamps();
    }

    public function addressedTo(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'complaint_user')
            ->wherePivot('role', 'addressed_to');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'complaint_user')
            ->wherePivot('role', 'follower');
    }
}
