<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailFacture extends Model
{
    protected $fillable = ['factures_id','ref_articles','qte','prix_unit','remise','montant_ht','montant_ttc'];
}
