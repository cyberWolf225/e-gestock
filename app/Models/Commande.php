<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = ['num_bc','periodes_id','delai','date_echeance','demande_achats_id','profils_id','date_livraison_prevue','date_livraison_effective','solde_avant_op','solde_apres_op'];
    
    public function getRouteKeyName(){
        return 'id';
    }
}
