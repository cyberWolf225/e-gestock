<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReponseCotation extends Model
{
    use HasFactory;
    protected $fillable = ['reponse_cotations_id','detail_demande_cotations_id','qte','prix_unit','remise','montant_ht','montant_ttc','echantillon'];
}