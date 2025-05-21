<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    protected $fillable = ['ref_depot','design_dep','tel_dep','adr_dep','code_structure','principal','code_ville'];

    public function structure(){
        return $this->belongsTo('App\Structure');
    }
    public function magasins(){
        return $this->hasMany('App\Magasin');
    }

    public function getRouteKeyName(){
        return 'id';
    }
}

