<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubDashboard extends Model
{
    protected $fillable = ['name','link','status','dashboards_id','position',];

    public function getRouteKeyName(){
        return 'id';
    }

    public function dashboard(){
        return $this->belongsTo('App\Dashboard');
    }

    public function subsubdashboards(){
        return $this->hasMany('App\SubSubDashboard');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($subdashboard) { // before delete() method call this
             $subdashboard->subsubdashboards()->delete();
             // do the rest of the cleanup...
        });
    }

}
