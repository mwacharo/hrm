<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'addressed_to',
        'resolution',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function addressedTo(){
        return $this->belongsTo(User::class, 'addressed_to');
    }
}
