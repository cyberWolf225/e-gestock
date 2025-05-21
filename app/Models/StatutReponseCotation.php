<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutReponseCotation extends Model
{
    use HasFactory;
    protected $fillable = ['reponse_cotations_id','type_statuts_id','profils_id','date_debut','date_fin','commentaire'];
}
