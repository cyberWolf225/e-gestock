<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutOrganisationDepot extends Model
{
    protected $fillable = ['organisation_depots_id','type_statut_org_depots_id','profils_id','date_debut','date_fin','commentaire'];
}
