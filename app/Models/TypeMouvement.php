<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMouvement extends Model
{
    use HasFactory;
    protected $fillable = ['libelle'];
}
