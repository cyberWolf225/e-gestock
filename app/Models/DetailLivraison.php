<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLivraison extends Model
{
    protected $fillable = ['livraison_commandes_id','detail_cotations_id','qte','qte_frs','prix_unit','remise','montant_ht','montant_ttc','qte_consommee'];
}
