<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatutCreditBudgetaire extends Model
{
    protected $fillable = ['type_operations_id','operations_id','credit_budgetaires_id','type_statut_c_budgetaires_id','montant','flag_actif','date_debut','date_fin','profils_id','commentaire'];
}
