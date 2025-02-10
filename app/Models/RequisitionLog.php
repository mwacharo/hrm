<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionLog extends Model
{
    use HasFactory;


    protected $fillable = [
        'requisition_id',
        'user_id',
        'action',
        'details',
    ];

    // public function requisition()
    // {
    //     return $this->belongsTo(Requisition::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
