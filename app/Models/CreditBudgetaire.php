<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditBudgetaire extends Model
{
    protected $fillable = ['ref_depot','code_structure','code_gestion','ref_fam','exercice','credit_initiale','consommation','credit','consommation_non_interfacee'];
    public function getRouteKeyName(){
        return 'id';
    }
}
