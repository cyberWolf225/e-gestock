<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailCotationService extends Model
{
    protected $fillable = ['cotation_services_id','services_id','code_unite','qte','prix_unit','remise','montant_ht','montant_ttc'];


}
