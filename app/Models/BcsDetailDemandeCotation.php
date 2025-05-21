<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BcsDetailDemandeCotation extends Model
{
    use HasFactory;
    protected $fillable = ['detail_demande_cotations_id','ref_articles','description_articles_id'];
}