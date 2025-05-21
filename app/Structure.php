<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Structure extends Model
{
    protected $fillable = ['code_structure','nom_structure','ref_depot'];

    protected $primaryKey = 'code_structure';
}
