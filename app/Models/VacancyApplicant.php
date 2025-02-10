<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacancyApplicant extends Model
{
    protected $fillable = [
        'name','email','cv','message'
    ];

    public function vacancy(){
        return $this->belongsTo(Vacancy::class);
    }
}
