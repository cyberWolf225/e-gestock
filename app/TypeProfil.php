<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeProfil extends Model
{
    protected $fillable = ['name'];
    
    public function profils(){
        return $this->hasMany('App\Profil');
    }
}
