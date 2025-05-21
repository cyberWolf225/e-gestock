<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutDemandeFond extends Model
{
    protected $fillable = ['demande_fonds_id','type_statut_demande_fonds_id','date_debut','date_fin','profils_id','commentaire'];
}
