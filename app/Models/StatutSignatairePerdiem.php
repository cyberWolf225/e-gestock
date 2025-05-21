<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutSignatairePerdiem extends Model
{
    use HasFactory;
    protected $fillable = ['signataire_perdiems_id','type_statut_sign_id','profils_id','date_debut','date_fin','commentaire'];

}