<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComiteReception extends Model
{
    protected $fillable = ['agents_id','demande_achats_id','profils_id','retrait_profils_id','flag_actif'];
}
