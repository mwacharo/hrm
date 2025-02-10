<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name','date','completed', 'unit_id','is_recurring'
    ];


    protected $casts = [
        'holiday_date' => 'datetime',
    ];
}
