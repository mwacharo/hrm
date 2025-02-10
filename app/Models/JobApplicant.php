<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplicant extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name','email','cv','message','phone','status'
    ];

    public function Job(){
        return $this->belongsTo(Job::class);
    }
}
