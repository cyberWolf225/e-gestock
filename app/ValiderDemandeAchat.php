<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValiderDemandeAchat extends Model
{
    protected $fillable = ['detail_demande_achats_id','profils_id','qte_validee','flag_valide'];
}
