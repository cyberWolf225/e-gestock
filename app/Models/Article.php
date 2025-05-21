<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['ref_articles','design_article','type_articles_id','ref_fam','code_unite','ref_taxe','flag_actif'];
    
    public function famille(){
        return $this->belongsTo('App\Famille');
    }

    public function demandes(){
        return $this->hasMany('App\Demande');
    }

    public function getRouteKeyName(){
        return 'id';
    }
}
