<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagasinStock extends Model
{
    protected $fillable = ['qte','cmup','montant','stock_securite','stock_alert','stock_mini','ref_articles','ref_magasin'];

    public function getRouteKeyName(){
        return 'id';
    }
}
