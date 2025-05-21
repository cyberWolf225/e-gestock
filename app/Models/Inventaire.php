<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaire extends Model
{
    protected $fillable = ['debut_per','fin_per','flag_valide','flag_integre','ref_depot'];
    
    public function getRouteKeyName(){
        return 'id';
    }

}
