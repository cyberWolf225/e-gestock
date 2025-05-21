<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationDepot extends Model
{
    protected $fillable = ['ref_depot','organisations_id','date_debut','date_fin','flag_actif'];
}
