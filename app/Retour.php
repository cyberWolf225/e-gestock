<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retour extends Model
{
    protected $fillable = ['livraisons_id','observation','qte_retour','prixu_retour','montant_retour'];

    public function getRouteKeyName(){
        return 'id';
    }
}
