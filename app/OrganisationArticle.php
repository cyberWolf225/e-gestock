<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationArticle extends Model
{
    protected $fillable = ['organisations_id','ref_fam','flag_actif'];
}
