<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeConsolide extends Model
{
    protected $fillable = ['demandes_ids','demandes_id','qte','requisitions_id','magasin_stocks_id','profils_id','montant','prixu','intitule'];

}
