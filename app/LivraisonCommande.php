<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LivraisonCommande extends Model
{
    protected $fillable = ['cotation_fournisseurs_id','profils_id'];
}
