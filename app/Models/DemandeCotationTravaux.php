<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCotationTravaux extends Model
{
    use HasFactory;
    protected $fillable = ['demande_cotations_id','travauxes_id'];
}
