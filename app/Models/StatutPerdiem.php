<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutPerdiem extends Model
{
    use HasFactory;
    protected $fillable = ['perdiems_id','type_statut_perdiems_id','profils_id','date_debut','date_fin','commentaire'];

}