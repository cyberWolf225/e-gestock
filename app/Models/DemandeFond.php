<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeFond extends Model
{
    protected $fillable = ['num_dem','code_section','profils_id','ref_fam','solde_avant_op','exercice','intitule','montant','observation','credit_budgetaires_id','moyen_paiements_id','agents_id','terminer','code_gestion','flag_engagement'];

    public function getRouteKeyName(){
        return 'id';
    }
} 
