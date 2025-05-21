<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupeUser extends Model
{
    protected $fillable = ['dashboards_id','sub_dashboards_id','sub_sub_dashboards_id','type_profils_id','profils_id'];
}