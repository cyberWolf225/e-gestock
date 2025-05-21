<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdjudicationCommande extends Model
{
    protected $fillable = ['selection_adjudications_id','profils_id'];
}
