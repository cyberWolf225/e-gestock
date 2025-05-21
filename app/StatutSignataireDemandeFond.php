<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutSignataireDemandeFond extends Model
{
    protected $fillable = ['signataire_fonds_id','type_statut_sign_id','profils_id','date_debut','date_fin','commentaire'];

}
