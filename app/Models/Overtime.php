<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overtime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','date','hours','description',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
