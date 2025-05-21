<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    use HasFactory;
    protected $fillable = ['ref_equipement','index','detail_immobilisations_id','type_affectations_id','date_debut','date_fin','flag_actif','flag_reception']; 
}