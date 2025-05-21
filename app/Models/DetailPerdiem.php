<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPerdiem extends Model
{
    use HasFactory;
    protected $fillable = ['perdiems_id','nom_prenoms','montant','piece','piece_name'];
}
