<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetatilAchatNonStockable extends Model
{
    protected $fillable = ['achat_non_stockables_id','services_id','qte','prix_unit','remise','montant_ht','montant_ttc'];

}
