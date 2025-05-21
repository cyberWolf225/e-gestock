<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValiderRequisition extends Model
{
    protected $fillable = ['profils_id','demandes_id','qte','flag_valide','prixu','montant'];

    public function getRouteKeyName(){
        return 'id';
    }
}
