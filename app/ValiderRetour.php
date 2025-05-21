<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValiderRetour extends Model
{
    protected $fillable = ['profils_id','retours_id','flag_valide','qte_validee','montant_retour_valide','prixu_retour_valide'];

    public function getRouteKeyName(){
        return 'id';
    }
}
