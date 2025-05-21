<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutProfilFonction extends Model
{
    protected $fillable = ['profil_fonctions_id','type_statut_profil_fonctions_id','profils_id','date_debut','date_fin','commentaire'];
}