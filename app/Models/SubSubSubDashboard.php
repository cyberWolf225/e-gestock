<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubSubDashboard extends Model
{
    use HasFactory;
    protected $fillable = ['name','link','status','position','sub_sub_dashboards_id'];

}