<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Immobilisation extends Model
{
    use HasFactory;
    protected $fillable = ['code_structure','num_bc','exercice','intitule','code_gestion','profils_id','flag_valide','flag_valide_stock','flag_valide_r_cmp','flag_valide_r_l'];

}