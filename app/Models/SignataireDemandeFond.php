<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignataireDemandeFond extends Model
{
    protected $fillable = ['profil_fonctions_id','demande_fonds_id','flag_actif'];
}