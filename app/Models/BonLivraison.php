<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonLivraison extends Model
{
    protected $fillable = ['livraison_commandes_id','profils_id','piece','flag_actif','name','sequence'];
}
