<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AwardType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'frequency',
        'status',
        'target',
        'created_by',
        'reward',
    ];
}
