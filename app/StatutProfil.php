<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutProfil extends Model
{
    protected $fillable = ['profils_id','type_statut_profils_id','date_debut','date_fin','profils_ids','commentaire'];

}
