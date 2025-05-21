<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComptabilisationEcriture extends Model
{
    use HasFactory;
    protected $fillable = ['type_piece','reference_piece','compte','code_gestion','exercice','montant','date_transaction','mle','code_structure','code_section','ref_depot','acompte'];

}
