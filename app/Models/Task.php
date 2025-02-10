<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name','start_date','end_date','leader','department_id',
        'description','files','progress','status'
    ];

    protected $casts = [
        'team' => 'array',
        'files' => 'array',
    ];



    public function leader()
    {
        return $this->belongsTo(User::class, 'leader');
    }

    public function department(){
        return $this->belongsTo(Department::class);
    }

    public function user($id){
        $user = User::where('id', '=',$id)->first();
        return $user;
    }
}
