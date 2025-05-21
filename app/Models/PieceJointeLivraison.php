<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PieceJointeLivraison extends Model
{
    use HasFactory;
    protected $fillable = ['type_operations_id','profils_id','subject_id','piece','flag_actif','name'];

}
