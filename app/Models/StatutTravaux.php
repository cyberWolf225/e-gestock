<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutTravaux extends Model
{
    protected $fillable = ['travauxes_id','type_statut_travauxes_id','date_debut','date_fin','profils_id','commentaire'];

}
