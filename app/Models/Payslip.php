<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payslip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        "user_id",
        "salary_id",
        "pay_period",
        "Earnings",
        "deductions",
        "net_pay",
    ] ;
}
