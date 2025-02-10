<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'leave_id',
        'action',
        'user_id'
    ];

    public function leave(){

        return $this->belongsTo(Leave::class);
    }

    public function user(){

        return $this->belongsTo(User::class);
    }
}
