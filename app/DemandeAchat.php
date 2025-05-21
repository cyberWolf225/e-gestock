<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemandeAchat extends Model
{
    protected $fillable = ['num_bc','profils_id','ref_fam','credit_budgetaires_id','ref_depot','intitule','code_gestion','exercice','type_achats_id','taux_acompte'];
    
    // public function getRouteKeyName(){
    //     return 'id';
    // }
}
