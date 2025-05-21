<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditBudgetaire extends Model
{
    protected $fillable = ['ref_depot','code_structure','ref_fam','exercice','code_gestion','credit_initiale','consommation','credit'];
    public function getRouteKeyName(){
        return 'id';
    }
}
