<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailDemandeAchat extends Model
{
    protected $fillable = ['demande_achats_id','ref_articles','qte_demandee','qte_accordee','flag_valide','profils_id','description_articles_id','echantillon'];
    
}
