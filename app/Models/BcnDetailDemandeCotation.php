<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BcnDetailDemandeCotation extends Model
{
    use HasFactory;
    protected $fillable = ['detail_demande_cotations_id','services_id'];
}