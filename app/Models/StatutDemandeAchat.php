<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutDemandeAchat extends Model
{
    protected $fillable = ['demande_achats_id','type_statut_demande_achats_id','date_debut','date_fin','profils_id','commentaire'];
}
