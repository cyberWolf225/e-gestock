<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;
    protected $fillable = ['name','status','link','position',];

    public function getRouteKeyName(){
        return 'id';
    }

    public function subdashboards(){
        return $this->hasMany('App\SubDashboard');
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($dashboard) { // before delete() method call this
             $dashboard->subdashboards()->delete();
             // do the rest of the cleanup...
        });
    }
}
