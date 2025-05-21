<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MieuxDisantTravaux extends Model
{
    use HasFactory;
    protected $fillable = ['mieux_disants_id','travauxes_id'];
}
