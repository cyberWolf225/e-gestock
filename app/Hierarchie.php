<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hierarchie extends Model
{
    protected $fillable = ['agents_id','agents_id_n1','agents_id_n2','profils_id','flag_actif'];

}