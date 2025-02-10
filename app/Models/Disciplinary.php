<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disciplinary extends Model
{
    use SoftDeletes;

    protected $fillable =[
        'violation',
        'user_id',
        'comment',
        'document',
        'violation_date',
        'type_of_disciplinary'

    ];

    public function user(){
        return $this->belongsTo(User::class);
    }


    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
