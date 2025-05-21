<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCotation extends Model
{
    use HasFactory;
    protected $fillable = ['num_dem','intitule','type_operations_id','credit_budgetaires_id','ref_depot','periodes_id','delai','date_echeance','flag_actif','taux_acompte'];
}
