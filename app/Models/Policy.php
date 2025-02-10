<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use SoftDeletes;
    protected $fillable =[
        'title',
        'description',
        'effective_date',
        'revision_date',
        'status',
        'visibility',
        'policy_id',
        'attachement'
    ];

  
}
