<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivraisonCommande extends Model
{
    protected $fillable = ['num_bl','cotation_fournisseurs_id','profils_id','montant_total_brut','taux_remise_generale','remise_generale','montant_total_net','tva','montant_total_ttc','assiete_bnc','taux_bnc','net_a_payer','taux_de_change','validation'];
}
