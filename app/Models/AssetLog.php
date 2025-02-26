<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'action',
        'user_id',
        'details'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



       // Relationship to the user to whom the asset was issued
       public function issuedToUser()
       {
           return $this->belongsTo(User::class, 'issued_to');
       }
}
