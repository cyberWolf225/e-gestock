<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perdiem extends Model
{
    use HasFactory;
    protected $fillable = ['num_pdm','libelle','num_or','code_gestion','exercice','ref_fam','code_structure','solde_avant_op','credit_budgetaires_id','montant_total','flag_engagement'];

}
