<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatairePerdiem extends Model
{
    use HasFactory;
    protected $fillable = ['profil_fonctions_id','perdiems_id','flag_actif'];

}