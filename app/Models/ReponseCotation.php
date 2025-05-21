<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReponseCotation extends Model
{
    use HasFactory;
    protected $fillable = ['fournisseur_demande_cotations_id','devises_id','montant_total_brut','taux_remise_generale','remise_generale','montant_total_net','tva','montant_total_ttc','assiete_bnc','taux_bnc','net_a_payer','acompte','taux_acompte','montant_acompte','taux_de_change'];
}