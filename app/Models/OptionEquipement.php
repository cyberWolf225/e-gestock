<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionEquipement extends Model
{
    use HasFactory;
    protected $fillable = ['ref_equipement','options_id','valeur_option'];
}