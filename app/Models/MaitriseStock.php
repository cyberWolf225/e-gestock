<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaitriseStock extends Model
{
    protected $fillable = ['magasin_stocks_id','type_maitrise_stocks_id','periodes_id','valeur'];
}
