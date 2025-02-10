<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LeaveType extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'name','days','comment'
    ];

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'leave_type_id');
    }

    public static function boot()
{
    parent::boot();

    static::created(function ($leaveType) {
        Log::info('LeaveType created: ' . $leaveType->id);

        $users = \App\Models\User::all();
        foreach ($users as $user) {
            LeaveBalance::updateOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $leaveType->id],
                ['balance' => $leaveType->days]
            );
        }
    });
}

}
