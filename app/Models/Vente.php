<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'amount',
        'message',
        'date',
        'produit_id',
        'created_by',    // ID de l'utilisateur créateur
        'updated_by',    // ID de l'utilisateur modificateur
        'deleted_by',    // ID de l'utilisateur ayant supprimé l'entrée
    ];

    public static function findById($id)
    {
        return self::where('id', $id)->firstOrFail();
    }

    // Relation avec le modèle User pour l'utilisateur créateur
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation avec le modèle User pour l'utilisateur modificateur
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relation avec le modèle User pour l'utilisateur qui a marqué comme supprimée
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
