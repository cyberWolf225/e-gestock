<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailDemandeAchat extends Model
{
    protected $fillable = ['demande_achats_id','ref_articles','qte_demandee','qte_accordee','flag_valide','profils_id'];
    
}
