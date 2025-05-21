<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivraisonValider extends Model
{
    protected $fillable = ['profils_id','livraison_commandes_id','detail_livraisons_id','qte','prix_unit','remise','montant_ht','montant_ttc'];
}
