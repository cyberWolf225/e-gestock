<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = ['demande_consolides_id','livraisons_id','qte','prixu','montant','profils_id','qte_recue','date_reception','commentaire'];
}
