<?php

namespace App\Http\Controllers;

use App\Models\CreditBudgetaire;
use Illuminate\Http\Request;

class CreditBudgetaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $depots = $this->getDepots();
        $familles = $this->getFamille();
        $structures = $this->getStructures();

        $credit_budgetaires = $this->getCreditBudgetaires();

        
        return view('credit_budgetaires.create',[
            'depots'=>$depots,
            'familles'=>$familles,
            'structures'=>$structures,
            'credit_budgetaires'=>$credit_budgetaires
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'ref_depot'=>['required','numeric'],
            'code_structure'=>['required','numeric'],
            'ref_fam'=>['required','numeric'],
            'exercice'=>['required','numeric'],
            'credit'=>['required','numeric'],
        ]);

        $credit_budgetaire = $this->getCreditBudgetaire($request->code_structure,$request->ref_fam,$request->exercice,$request->ref_depot,$request->credit);
        
        if($credit_budgetaire != null){
            return redirect()->back()->with('success','Enregistrement reussi');
        }else{
            return redirect()->back()->with('success','Enregistrement echoué');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CreditBudgetaire  $creditBudgetaire
     * @return \Illuminate\Http\Response
     */
    public function show(CreditBudgetaire $creditBudgetaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CreditBudgetaire  $creditBudgetaire
     * @return \Illuminate\Http\Response
     */
    public function edit(CreditBudgetaire $creditBudgetaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CreditBudgetaire  $creditBudgetaire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CreditBudgetaire $creditBudgetaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CreditBudgetaire  $creditBudgetaire
     * @return \Illuminate\Http\Response
     */
    public function destroy(CreditBudgetaire $creditBudgetaire)
    {
        //
    }
}
