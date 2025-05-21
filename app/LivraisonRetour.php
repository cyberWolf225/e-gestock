<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LivraisonRetour extends Model
{
    protected $fillable = ['profils_id','retours_id','statut','qte','observation','montant','prixu'];

    public function getRouteKeyName(){
        return 'id';
    }
}
