<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PieceJointe extends Model
{
    protected $fillable = ['type_operations_id','profils_id','subject_id','piece','flag_actif','name'];

    public function getRouteKeyName(){
        return 'id';
    }
}