<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'name',
    'address',
    'phone',
  ];

  public function users()
  {
    return $this->hasMany(User::class);
  }


  public function offices()
  {
    return $this->hasMany(Office::class);
  }
}
