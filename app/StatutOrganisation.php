<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutOrganisation extends Model
{
    protected $fillable = ['organisations_id','type_statut_organisations_id','profils_id','profils_ids','date_debut','date_fin'];

}
