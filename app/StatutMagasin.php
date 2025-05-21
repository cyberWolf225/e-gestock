<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutMagasin extends Model
{
    protected $fillable = ['magasins_id','type_statut_magasins_id','profils_id','date_debut','date_fin','commentaire'];

}