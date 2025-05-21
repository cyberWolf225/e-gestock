<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutInventaire extends Model
{
    protected $fillable = ['inventaire_articles_id','type_statut_inventaires_id','date_debut','date_fin','profils_id','commentaire'];
}
