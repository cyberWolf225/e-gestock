<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutDepot extends Model
{
    protected $fillable = ['depots_id','type_statut_depots_id','profils_id','date_debut','date_fin','commentaire'];
}
