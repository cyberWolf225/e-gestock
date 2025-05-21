<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consommation extends Model
{
    protected $fillable = ['distributions_id','demandes_id','qte','prixu','montant','profils_id','commentaire'];

}
