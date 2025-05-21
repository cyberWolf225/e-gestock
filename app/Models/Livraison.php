<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $fillable = ['profils_id','demandes_id','statut','qte','montant','prixu','mouvements_id','qte_recue','date_reception','commentaire'];

    public function getRouteKeyName(){
        return 'id';
    }

}
