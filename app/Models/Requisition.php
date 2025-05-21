<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $fillable = ['code_structure','num_bc','exercice','intitule','code_gestion','profils_id','flag_consolide','type_beneficiaire','departements_id'];
    
    public function profil(){
        return $this->belongsTo('App\Profil');
    }

    public function demandes(){
        return $this->hasMany('App\Demande');
    }
}
