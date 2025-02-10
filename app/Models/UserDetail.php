<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "user_id", "kin", "kin_contact", "gender", "payment_mode", "bank_name", "bank_branch", "bank_account", "mpesa_no", "nhif_no","national_id", "nssf_no", "kra_pin","marital_status","spouse","spouse_no","staffID","nationality","country","region"
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}

