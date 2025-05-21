<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $fillable = ['entnum','denomination','type_organisations_id','contacts','num_contribuable','adresse','sigle'];

    public function getRouteKeyName(){
        return 'id';
    }
}
