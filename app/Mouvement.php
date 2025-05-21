<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = ['type_mouvements_id','magasin_stocks_id','profils_id','qte','prix_unit','montant_ht','taxe','montant_ttc'] ;

    public function getRouteKeyName(){
        return 'magasin_stocks_id';
    }
}
