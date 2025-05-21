<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailCotation extends Model
{
    protected $fillable = ['cotation_fournisseurs_id','ref_articles','qte','prix_unit','remise','montant_ht','montant_ttc','echantillon'];
}
