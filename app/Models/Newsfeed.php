<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Newsfeed Model
class Newsfeed extends Model {
    protected $fillable = ['user_id', 'content', 'type'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function announcement() {
        return $this->belongsTo(Announcement::class, 'content_id')->where('type', 'announcement');
    }

   

    public function policy() {
        return $this->belongsTo(Policy::class, 'content_id')->where('type', 'policy');
    }

    public function training() {
        return $this->belongsTo(Training::class, 'content_id')->where('type', 'training');
    }
}

