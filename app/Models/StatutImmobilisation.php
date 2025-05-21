<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutImmobilisation extends Model
{
    use HasFactory;

    protected $fillable = ['immobilisations_id','type_statut_requisitions_id','date_debut','date_fin','profils_id','commentaire'];

}

