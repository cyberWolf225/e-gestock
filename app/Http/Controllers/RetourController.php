<?php

namespace App\Http\Controllers;

use App\Models\Retour;
use App\Models\Demande;
use App\Models\Livraison;
use Illuminate\Http\Request;
use App\Models\ValiderRetour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RetourController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Pilote AEE']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $livraisons = [];

        // determination de la structure de l'agent connecté

        $strucs = DB::select("SELECT st.code_structure FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st WHERE a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND u.id = '".$user_id."'   ");

        foreach ($strucs as $struc) {
            if ($struc!=null) {
                $livraisons = DB::table('requisitions')
                ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
                ->join('livraisons','livraisons.demandes_id','=','demandes.id')
                ->join('retours','retours.livraisons_id','=','livraisons.id')
                ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                ->join('profils','profils.id','=','requisitions.profils_id')
                ->join('users','users.id', '=', 'profils.users_id')
                ->join('agents','agents.id', '=', 'users.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','agents.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->where('valider_requisitions.flag_valide',1)
                ->where('livraisons.statut',1)
                ->where('st.code_structure',$struc->code_structure)
                ->select('num_dem','num_bc','requisitions.exercice','intitule','requisitions.code_gestion','mle','nom_prenoms','demandes.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte_demandee','magasin_stocks.cmup','valider_requisitions.profils_id','valider_requisitions.qte_validee','valider_requisitions.flag_valide','valider_requisitions.id as valider_requisitions_id','livraisons.qte','livraisons.profils_id as profils_id_livreur','livraisons.id as livraisons_id','qte_retour','retours.id as retours_id')
                ->paginate(5);
            }
        }

        

        return view('retours.index',[
        'livraisons' => $livraisons,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Livraison $livraison)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Agent Cnps']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }else{
            $profils_id = $profil->id;
        }


        $requisitions = DB::table('requisitions')
                            ->join('gestions','gestions.code_gestion','=','requisitions.code_gestion')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->where('demandes.id',$livraison->demandes_id)
                            ->where('demandes.profils_id',$profils_id)
                            ->first();
        if ($requisitions===null) {
            return redirect()->back();
        }

        $demandes = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                            ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->join('valider_requisitions','valider_requisitions.demandes_id', '=', 'demandes.id')
                            ->join('livraisons','livraisons.demandes_id', '=', 'demandes.id')
                            ->where('demandes.id',$livraison->demandes_id)
                            ->where('demandes.profils_id',$profils_id)
                            ->where('valider_requisitions.flag_valide',1) // réquisition validée
                            ->where('livraisons.statut',1) // réquisition livrée
                            ->select('demandes.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte_demandee','magasin_stocks.cmup','livraisons.qte','livraisons.id as livraisons_id')
                            ->get();
        if(count($demandes) == 0){
            return redirect()->back();
        }

        return view('retours.create',[
            'demandes' => $demandes,
            'requisitions' => $requisitions,
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
        
        $agents_id = auth()->user()->id;
        $profils = DB::table('agents')
                        ->join('users','users.agents_id','=','agents.id')
                        ->join('profils','profils.users_id','=','users.id')
                        ->join('type_profils','type_profils.id','=','profils.type_profils_id')
                        ->where('agents.id',$agents_id)
                        ->whereIn('type_profils.name', (['Agent Cnps']))
                        ->select('profils.id')
                        ->first();
        if ($profils != null) {
            $profils_id = $profils->id;
        }else {
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }
        
        
            $validate = $request->validate([
                'demandes_id'=>['required','array'],
                'qte'=>['required','array'],
                'observation'=>['required','array'],
                'cmup'=>['required','array'],
                'montant'=>['required','array']
            ]);


            if (count($request->demandes_id) > 0) {
                foreach ($request->demandes_id as $item => $value) {
                    if (isset($request->approvalcd[$request->demandes_id[$item]])) {

                        $qte[$item] = str_replace(' ','',$request->qte[$item]);
                        if ($qte[$item] === null) {
                            $qte[$item] = 0;
                        }
                        $qte_livree[$item] = Livraison::where('demandes_id', $request->demandes_id[$item])->first()->qte;
                        
                        if ($qte[$item] <= 0) {
                            return redirect()->back()->with('error', 'La quantité retournée ne peut être nulle ou négative');
                        }
                        if ($qte_livree[$item] < $qte[$item]) {
                            return redirect()->back()->with('error', 'La quantité retournée ne peut être supérieure à la quantité livrée');
                        }
                    }
                    
                }
            }

            $resultat = 0;
            if (count($request->demandes_id) > 0) {
                foreach ($request->demandes_id as $item => $value) {
                    if (isset($request->approvalcd[$request->demandes_id[$item]])) {
                        
                        $qte[$item] = str_replace(' ','',$request->qte[$item]);
                        $cmup[$item] = str_replace(' ','',$request->cmup[$item]);
                        $montant[$item] = str_replace(' ','',$request->montant[$item]);
                    //identifier la réquisition
                    if (!isset($requisitions_id)) {
                        $requisition = Demande::where('id',$request->demandes_id[$item])->first();
                        if ($requisition!=null) {
                            $requisitions_id = $requisition->requisitions_id;
                        }
                    }

                    $livraisons_id = null;

                    $livraison = DB::table('livraisons')
                                    ->where('demandes_id', $request->demandes_id[$item])
                                    ->first();
                    if ($livraison != null) {
                        $livraisons_id = $livraison->id;
                        $qte_livree = $livraison->qte;
                    }

                    if ($qte[$item]<1) {
                        return redirect()->back()->with('error', 'Echec de l\'enregistrement du retour d\'articles. Veuillez saisir une quantité valide');
                    }
                    if ($qte[$item]>$qte_livree) {
                        return redirect()->back()->with('error', 'Echec de l\'enregistrement du retour d\'articles. Veuillez saisir une quantité valide');
                    }

                    

                    if ($livraisons_id != null) {
                        if (isset($request->approvalcd[$request->demandes_id[$item]])) {
                            $data = [
                                'livraisons_id' => $livraisons_id,
                                'observation' => $request->observation[$item],
                                'qte_retour' => $qte[$item],
                                'prixu_retour' => $cmup[$item],
                                'montant_retour' => $montant[$item],
                            ];

                            $retour = Retour::where('livraisons_id', $livraisons_id)->first();

                            if ($retour === null) {
                                Retour::create($data);
                                $message = 'Retour enregistré avec succes';
                                $resultat = 1;
                            } else {
                                Retour::where('livraisons_id', $livraisons_id)->update($data);
                                $message = 'Retour mis à jour avec succes';
                                $resultat = 1;
                            }
                        } else {
                            Retour::where('livraisons_id', $livraisons_id)->delete();
                            $resultat = 1;
                            $message = 'Retour annulé avec succes';
                        }
                    }
                    }
                }

                
            }

            if ($resultat == 1) {
                if (isset($requisitions_id)) {
                    
                    $email = auth()->user()->email;
                    $subject = "Retour de la réquisition";
    
                    $datas = [
                        'requisitions_id'=>$requisitions_id,
                        'subject'=>$subject,
                        'retour' => 1
                    ];
    
                    Mail::to($email)->send(new \App\Mail\SendMailCreateRequisition($datas));

                    // determinier le mail du pilote AEE
                    $strucs = DB::select("SELECT * FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st, profils as p, type_profils as tp WHERE u.id = p.users_id AND p.type_profils_id = tp.id AND a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND tp.name like 'Pilote AEE' AND st.code_structure IN (SELECT st.code_structure FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st WHERE a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND u.email = '".$email."') ");

                    foreach ($strucs as $struc) {
                        if ($struc->email!=null) {
                            Mail::to($struc->email)->send(new \App\Mail\SendMailCreateRequisition($datas));
                        }
                        
                    }
    
                }
                
                return redirect()->back()->with('success',$message);
            }else{
                return redirect()->back()->with('error','Echec de l\'enregistrement du retour d\'articles');
            }
            


        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Retour  $retour
     * @return \Illuminate\Http\Response
     */
    public function show(Retour $retour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Retour  $retour
     * @return \Illuminate\Http\Response
     */
    public function edit(Retour $retour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Retour  $retour
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Retour $retour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Retour  $retour
     * @return \Illuminate\Http\Response
     */
    public function destroy(Retour $retour)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $valider_retour = ValiderRetour::where('retours_id',$retour->id)->first();
        if($valider_retour === null){
            $delete_retour = Retour::where('id',$retour->id)->delete();
        }else{
            return redirect()->back()->with('error','Suppression echouée. Retour déjà traité');
        }
        

        if($delete_retour!=null){
            return redirect()->back()->with('success','Suppression reussie');
        }else{
            return redirect()->back()->with('error','Suppression echouée');
        }
    }
}

