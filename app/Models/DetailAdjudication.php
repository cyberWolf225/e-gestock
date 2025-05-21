<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailAdjudication extends Model
{
    protected $fillable = ['cotation_fournisseurs_id','critere_adjudications_id','valeur'];
}
