<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'gross_pay',
        'pay_period',
        'effective_date',
    ] ;

    public function user()
    {
        return $this->belongsTo(User::class);
    }



}
