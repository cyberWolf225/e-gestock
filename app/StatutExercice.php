<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutExercice extends Model
{
    protected $fillable = ['exercice','type_statut_exercices_id','date_debut','date_fin'];
}
