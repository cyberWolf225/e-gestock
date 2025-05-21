<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $fillable = ['users_id','type_profils_id','flag_actif'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function type_profil(){
        return $this->belongsTo('App\TypeProfil');
    }

    public function requisitions(){
        return $this->hasMany('App\Requisition');
    }

    public function getRouteKeyName(){
        return 'id';
    }
}
