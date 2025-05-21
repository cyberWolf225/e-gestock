<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutCotationService extends Model
{
    protected $fillable = ['cotation_services_id','type_statut_cot_services_id','date_debut','date_fin','profils_id','commentaire'];
}
