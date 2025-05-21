<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSubDashboard extends Model
{
    use HasFactory;
    protected $fillable = ['name','link','status','position','sub_dashboards_id'];

}