<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SignataireDemandeAchat extends Model
{
     protected $fillable = ['profil_fonctions_id','demande_achats_id','flag_actif'];
}