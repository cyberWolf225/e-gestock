<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSubDashboard extends Model
{
    protected $fillable = ['name','link','status','sub_dashboards_id','position',];

    public function getRouteKeyName(){
        return 'id';
    }
    
    public function subdashboard(){
        return $this->belongsTo('App\SubDashboard');
    }

    public function subsubsubdashboards(){
        return $this->hasMany('App\SubSubSubDashboard');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($subsubdashboard) { // before delete() method call this
             $subsubdashboard->subsubsubdashboards()->delete();
             // do the rest of the cleanup...
        });
    }

}
