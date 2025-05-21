<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTravaux extends Model
{
    protected $fillable = ['travauxes_id','services_id','qte','prix_unit','remise','montant_ht','montant_ttc'];

}
