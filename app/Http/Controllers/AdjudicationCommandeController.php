<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AdjudicationCommande;
use App\Models\SelectionAdjudication;

class AdjudicationCommandeController extends Controller
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
    public function create(SelectionAdjudication $selectionAdjudication)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        
        $profils = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', ['Responsable des achats'])
                      ->limit(1)
                      ->select('profils.id')
                      ->get();
        foreach($profils as $profil){
            $profils_id = $profil->id;
        }

        if (!isset($profils_id)) {
            return redirect()->back()->with('error','Vous n\'avez pas le profile requis pour cette opération');
        }
        
        $data = [
            'selection_adjudications_id'=>$selectionAdjudication->id,
            'profils_id'=>$profils_id,
        ];
        $adjudication_commande = AdjudicationCommande::where('selection_adjudications_id',$selectionAdjudication->id)->first();
        if ($adjudication_commande!=null) {
            $adjudication_commandes = AdjudicationCommande::where('selection_adjudications_id',$selectionAdjudication->id)->update($data);
        }else{
            $adjudication_commandes = AdjudicationCommande::create($data);
        }

        if ($adjudication_commandes!=null) {
            return redirect()->back()->with('success','Bon de commande généré');
        }else{
            return redirect()->back()->with('error','Echec de la création du bon de commande');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AdjudicationCommande  $adjudicationCommande
     * @return \Illuminate\Http\Response
     */
    public function show(AdjudicationCommande $adjudicationCommande)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AdjudicationCommande  $adjudicationCommande
     * @return \Illuminate\Http\Response
     */
    public function edit(AdjudicationCommande $adjudicationCommande)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AdjudicationCommande  $adjudicationCommande
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdjudicationCommande $adjudicationCommande)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AdjudicationCommande  $adjudicationCommande
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdjudicationCommande $adjudicationCommande)
    {
        //
    }
}
