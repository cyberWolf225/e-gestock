<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCotationDemandeAchat extends Model
{
    use HasFactory;
    protected $fillable = ['demande_cotations_id','demande_achats_id'];
}
