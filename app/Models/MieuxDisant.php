<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MieuxDisant extends Model
{
    use HasFactory;
    protected $fillable = ['reponse_cotations_id','montant_total_brut','remise_generale','montant_total_net','montant_total_ttc','assiete_bnc','net_a_payer','montant_acompte'];
}