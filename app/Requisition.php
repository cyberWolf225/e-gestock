<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable = ['code_structure','num_bc','exercice','intitule','code_gestion','profils_id','flag_consolide'];
    
    public function profil(){
        return $this->belongsTo('App\Profil');
    }

    public function demandes(){
        return $this->hasMany('App\Demande');
    }
}
