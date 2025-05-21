<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    protected $fillable = ['ref_magasin','design_magasin','depots_id',];
    public function depot(){
        return $this->belongsTo('App\Structure');
    }

    public function getRouteKeyName(){
        return 'id';
    }
}
