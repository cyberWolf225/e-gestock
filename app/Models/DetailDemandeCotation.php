<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDemandeCotation extends Model
{
    use HasFactory;
    protected $fillable = ['demande_cotations_id','code_unite','qte_demandee','qte_accordee','flag_valide','echantillon'];
}