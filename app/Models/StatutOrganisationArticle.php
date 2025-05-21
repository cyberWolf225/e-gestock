<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutOrganisationArticle extends Model
{
    protected $fillable = ['organisation_articles_id','type_statut_org_articles_id','profils_id','date_debut','date_fin','commentaire'];

}
