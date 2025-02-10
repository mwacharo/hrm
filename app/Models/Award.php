<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Award extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'award_type_id',
        'user_id',
        'notes',
        'points',
        'period'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function awardType()
    {
        return $this->belongsTo(AwardType::class);
    }
}
