<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeFondBonCommande extends Model
{
    use HasFactory;
    protected $fillable = ['demande_fonds_id','type_operations_id','operations_id','flag_actif'];
}