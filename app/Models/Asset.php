<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category',
        'issuance_date',
        'office_id',
        'department_id',
        'serial_no',
        'condition',
        'warranty',
        'purchase_cost',
        'purchase_date',
        'issued_to',
        'issued_by',
        'repair_cost',
        'is_assigned',
        'comment'
    ];



    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function issuedTo()
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

}
