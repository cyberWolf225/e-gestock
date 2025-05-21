<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutEquipement extends Model
{
    use HasFactory;
    protected $fillable = ['ref_equipement','type_statut_equipements_id','date_debut','date_fin','profils_id','commentaire'];

}
