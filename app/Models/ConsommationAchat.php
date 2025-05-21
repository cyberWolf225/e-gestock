<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsommationAchat extends Model
{
    use HasFactory;
    protected $fillable = ['detail_livraisons_id','livraisons_id','qte','qte_distribuee'];

}