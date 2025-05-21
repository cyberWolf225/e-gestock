<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotationFournisseur extends Model
{
    protected $fillable = ['organisations_id','demande_achats_id','acompte','montant_total_brut','taux_remise_generale','remise_generale','montant_total_net','tva','montant_total_ttc','assiete_bnc','taux_bnc','net_a_payer','delai','periodes_id','date_echeance','taux_acompte','montant_acompte','devises_id','taux_de_change'];
    
}
