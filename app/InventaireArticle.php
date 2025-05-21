<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventaireArticle extends Model
{
    protected $fillable = ['inventaires_id','magasin_stocks_id','profils_id','qte_theo','qte_phys','ecart','justificatif','flag_valide','flag_integre','mouvements_id'];
    
    public function getRouteKeyName(){
        return 'id';
    }
    
}
