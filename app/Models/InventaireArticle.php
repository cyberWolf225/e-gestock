<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventaireArticle extends Model
{
    protected $fillable = ['inventaires_id','magasin_stocks_id','profils_id','qte_theo','qte_phys','ecart','cmup_inventaire','montant_inventaire','justificatif','flag_valide','flag_integre','mouvements_id'];
    
    public function getRouteKeyName(){
        return 'id';
    }
    
}
