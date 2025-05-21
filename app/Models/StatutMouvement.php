<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatutMouvement extends Model
{
    use HasFactory;
    protected $fillable = ['mouvements_id','type_statut_mouvements_id','profils_id','date_debut','date_fin','commentaire'];
}