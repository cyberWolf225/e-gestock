<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsommationDistribution extends Model
{
    use HasFactory;
    protected $fillable = ['consommation_achats_id','consommations_id','qte'];
}