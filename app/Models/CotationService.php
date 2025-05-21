<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotationService extends Model
{
    protected $fillable = ['num_bc','organisations_id','demande_fonds_id','acompte','montant_total_brut','remise_generale','montant_total_net','tva','montant_total_ttc','net_a_payer','delai','periodes_id','date_echeance','taux_acompte','montant_acompte','flag_actif','date_livraison_prevue','date_retrait'];

}
