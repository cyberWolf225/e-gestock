<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    protected $fillable = ['code','libelle','symbole'];
}