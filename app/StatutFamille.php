<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutFamille extends Model
{
    //
    protected $fillable = ['familles_id','profils_id','type_statut_familles_id','date_debut','date_fin','commentaire'];
}
