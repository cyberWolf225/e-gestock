<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FournisseurDemandeCotation extends Model
{
    use HasFactory;
    protected $fillable = ['demande_cotations_id','organisations_id','flag_actif'];
}