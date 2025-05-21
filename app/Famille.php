<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Famille extends Model
{
    protected $fillable = ['ref_fam','design_fam','flag_actif'];
    
    public function getRouteKeyName(){
        return 'id';
    }

    public function articles(){
        return $this->hasMany('App\Article');
    }
}
