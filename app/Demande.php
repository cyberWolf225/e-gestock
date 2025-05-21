<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    protected $fillable = ['qte','requisitions_id','magasin_stocks_id','profils_id','montant','prixu','intitule','requisitions_id_consolide'];

    public function requisition(){
        return $this->belongsTo('App\Requisition');
    }

    public function article(){
        return $this->belongsTo('App\Article');
    }

    
}
