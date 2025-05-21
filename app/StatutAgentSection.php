<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutAgentSection extends Model
{
    protected $fillable = ['agent_sections_id','type_statut_agent_sections_id','date_debut','date_fin','profils_id','commentaire'];

}
