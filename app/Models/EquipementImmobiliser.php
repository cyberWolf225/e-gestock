<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipementImmobiliser extends Model
{
    use HasFactory;
    protected $fillable = ['ref_equipement','magasin_stocks_id','no_sticker','no_serie','exercice'];
}