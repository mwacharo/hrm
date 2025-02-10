<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject', 'description', 'author', 'publish_date', 'expiration_date',
        'is_active', 'attachment', 'priority', 'status',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author');
    }

}
