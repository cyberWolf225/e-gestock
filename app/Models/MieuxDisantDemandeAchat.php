<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MieuxDisantDemandeAchat extends Model
{
    use HasFactory;
    protected $fillable = ['mieux_disants_id','demande_achats_id'];
}
