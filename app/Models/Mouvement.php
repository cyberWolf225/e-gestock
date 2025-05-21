<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mouvement extends Model
{
    protected $fillable = ['type_mouvements_id','magasin_stocks_id','profils_id','qte','prix_unit','montant_ht','taxe','montant_ttc','date_mouvement'] ;

    public function getRouteKeyName(){
        return 'magasin_stocks_id';
    }
}
