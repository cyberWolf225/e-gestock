<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMaitriseStock extends Model
{
    use HasFactory;
    protected $fillable = ['libelle'];
}
