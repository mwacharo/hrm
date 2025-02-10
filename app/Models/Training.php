<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    use SoftDeletes;

    use HasFactory;

    protected $table = 'trainings'; // Set the table name to 'trainings'

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'venue',
        'instructor',
        'capacity',
        'status',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }


}
