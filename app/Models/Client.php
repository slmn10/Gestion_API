<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

    ];

    public function adresses()
    {
        return $this->hasMany(AdresseClient::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
