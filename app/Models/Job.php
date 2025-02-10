<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Job extends Model
{
    use SoftDeletes;

    protected $fillable =[
        'title','department_id','location','vacancies',
        'experience','age','salary_from','salary_to','type',
        'status','start_date','expire_date',
        'description',
    ];

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function JobApplicants(){
        return $this->hasMany(JobApplicant::class);
    }
}
