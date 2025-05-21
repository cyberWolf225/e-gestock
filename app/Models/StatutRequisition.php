<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutRequisition extends Model
{
    protected $fillable = ['requisitions_id','type_statut_requisitions_id','date_debut','date_fin','profils_id','commentaire'];

}
