<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignataireTravaux extends Model
{
    protected $fillable = ['profil_fonctions_id','travauxes_id','flag_actif'];
}
