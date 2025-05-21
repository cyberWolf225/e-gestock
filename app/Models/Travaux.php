<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Travaux extends Model
{
    protected $fillable = ['num_bc','ref_fam','credit_budgetaires_id','exercice','organisations_id','devises_id','taux_de_change','code_gestion','code_structure','ref_depot','intitule','acompte','montant_total_brut','remise_generale','montant_total_net','tva','montant_total_ttc','net_a_payer','delai','periodes_id','date_echeance','taux_acompte','montant_acompte','flag_actif','date_livraison_prevue','date_livraison','date_retrait','solde_avant_op','taux_remise_generale','flag_engagement'];

}
