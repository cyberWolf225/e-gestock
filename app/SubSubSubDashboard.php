<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSubSubDashboard extends Model
{
    protected $fillable = ['name','link','status','sub_sub_dashboards_id','position',];

    public function getRouteKeyName(){
        return 'id';
    }
    
    public function subsubdashboard(){
        return $this->belongsTo('App\SubSubDashboard');
    }

}
