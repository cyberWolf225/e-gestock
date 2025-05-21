<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailImmobilisation extends Model
{
    use HasFactory;

    protected $fillable = ['qte','immobilisations_id','magasin_stocks_id','beneficiaire','montant','prixu','intitule','immobilisations_id_consolide','type_beneficiaire','echantillon','observations','qte_sortie'];

}