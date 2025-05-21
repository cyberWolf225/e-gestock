<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Agent extends Model
{
    protected $fillable = ['mle','nom_prenoms',];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function getRouteKeyName(){
        return 'id';
    }
}
