<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutLivraisonCommande extends Model
{
    use HasFactory;
    protected $fillable = ['livraison_commandes_id','type_statut_demande_achats_id','date_debut','date_fin','profils_id','commentaire'];
}
