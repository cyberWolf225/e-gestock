<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatutDotationBudgetaire extends Model
{
    protected $fillable = ['type_operations_id','operations_id','dotation_budgetaires_id','type_statut_d_budgetaires_id','montant','flag_actif','date_debut','date_fin','profils_id','commentaire'];

}

