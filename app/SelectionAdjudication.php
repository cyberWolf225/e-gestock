<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SelectionAdjudication extends Model
{
    protected $fillable = ['cotation_fournisseurs_id','profils_id'];

    public function getRouteKeyName(){
        return 'id';
    }
}
