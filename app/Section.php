<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['code_section','code_structure','nom_section','code_gestion'];
}
