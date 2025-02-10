<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
      protected $fillable = [
        'goal_type_id','subject','target',
        'start_date','end_date','description','status','progress'
    ];

    public function goal_type(){
        return $this->belongsTo(GoalType::class);
    }
}
