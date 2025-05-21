<?php

namespace App\Http\Controllers;

use App\Models\Retour;
use App\Models\Demande;
use App\Models\Livraison;
use App\Models\Mouvement;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use App\Models\TypeMouvement;
use App\Models\ValiderRetour;
use App\Models\LivraisonRetour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LivraisonRetourController extends Controller
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
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $livraison_retours = DB::table('requisitions')
        ->join('demandes','demandes.requisitions_id','=','requisitions.id')
        ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
        ->join('livraisons','livraisons.demandes_id','=','demandes.id')
        ->join('retours','retours.livraisons_id','=','livraisons.id')
        ->join('valider_retours','valider_retours.retours_id','=','retours.id')
        ->join('livraison_retours','livraison_retours.retours_id','=','retours.id')
        ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
        ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
        ->join('profils','profils.id','=','requisitions.profils_id')
        ->join('users','users.id', '=', 'profils.users_id')
        ->join('agents','agents.id', '=', 'users.agents_id')
        ->where('livraison_retours.statut',1)
        ->select('num_dem','num_bc','exercice','intitule','code_gestion','mle','nom_prenoms','demandes.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte_demandee','magasin_stocks.cmup','valider_requisitions.profils_id','valider_requisitions.qte_validee','valider_requisitions.flag_valide','valider_requisitions.id as valider_requisitions_id','livraisons.id as livraisons_id','livraison_retours.qte as qte_livree','retours.qte_retour','livraison_retours.profils_id as profils_id_livreur','livraison_retours.id as livraison_retours_id','valider_retours.id as valider_retours_id')
        ->paginate(5);

        return view('livraison_retours.index',[
            'livraison_retours' => $livraison_retours,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(ValiderRetour $valider_retour)
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
        
        $retour = Retour::where('id',$valider_retour->retours_id)->first();
        $livraison = Livraison::where('id',$retour->livraisons_id)->first();
        $demande = Demande::where('id',$livraison->demandes_id)->first();
        

        $requisitions = DB::table('requisitions')
                            ->join('gestions','gestions.code_gestion','=','requisitions.code_gestion')
                            ->where('requisitions.id',$demande->requisitions_id)
                            ->first();
                            //dd($requisitions);

        $demandes = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('livraisons','livraisons.demandes_id','=','demandes.id')
                            ->join('retours','retours.livraisons_id','=','livraisons.id')
                            ->join('valider_retours','valider_retours.retours_id','=','retours.id')
                            ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                            ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->join('valider_requisitions','valider_requisitions.demandes_id', '=', 'demandes.id')
                            ->where('retours.id',$valider_retour->retours_id)
                            ->where('valider_retours.flag_valide',1)
                            ->select('retours.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','retours.qte_retour','magasin_stocks.cmup','valider_retours.qte_validee')
                            ->get();
                            
        
                   
        return view('livraison_retours.create',[
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
        

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if ($profil != null) {
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }

        
            $validate = $request->validate([
                'retours_id'=>['required','array'],
                'qte'=>['required','array'],
                'cmup'=>['required','array'],
                'montant'=>['required','array'],
                'observation'=>['required','array'],
            ]);

           
            
            
            $mouvement = null;
            $subject = "";
            if (count($request->retours_id) > 0) {

                if (count($request->retours_id) > 0) {
                    foreach ($request->retours_id as $item => $value) { 
                        if (isset($request->approvalcd[$request->retours_id[$item]])) {

                            $qte[$item] = str_replace(' ','',$request->qte[$item]);

                        if ($qte[$item] === null) {
                            $qte[$item] = 0;
                        }
                        $qte_validee[$item] = ValiderRetour::where('retours_id', $request->retours_id[$item])->first()->qte_validee;

                        if ($qte_validee[$item] < $qte[$item]) {
                            return redirect()->back()->with('error', 'La quantité livrée ne peut être supérieure à la quantité validée');
                        }

                        if ($qte[$item] <= 0) {
                            return redirect()->back()->with('error', 'La quantité livrée ne peut être nulle ou négative');
                        }

                        }
                    }
                }
               
                foreach ($request->retours_id as $item => $value) {
                    
                    if (isset($request->approvalcd[$request->retours_id[$item]])) {
                        $statut = true;
                    }else{
                        $statut = false;
                    }

                    $qte[$item] = str_replace(' ','',$request->qte[$item]);
                    $montant[$item] = str_replace(' ','',$request->montant[$item]);
                    $cmup[$item] = str_replace(' ','',$request->cmup[$item]);

                    if (!isset($requisitions_id)) {
                        $requisition = ValiderRetour::where('r.id',$request->retours_id[$item])
                        ->join('retours as r','r.id','=','valider_retours.retours_id')
                        ->join('livraisons as l','l.id','=','r.livraisons_id')
                        ->join('demandes as d','d.id','=','l.demandes_id')
                        ->first();
                        if ($requisition!=null) {
                            $requisitions_id = $requisition->requisitions_id;
                        }
                    }

                    //identification du magasin stock
                    $magasin_stocks = ValiderRetour::where('r.id',$request->retours_id[$item])
                    ->join('retours as r','r.id','=','valider_retours.retours_id')
                    ->join('livraisons as l','l.id','=','r.livraisons_id')
                    ->join('demandes as d','d.id','=','l.demandes_id')
                    ->first();
                    if ($magasin_stocks!=null) {
                        $magasin_stocks_id = $magasin_stocks->magasin_stocks_id;
                    }
                

                    $data = [
                            'retours_id' => $request->retours_id[$item],
                            'profils_id' => $profils_id,
                            'statut' => $statut,
                            'observation' => $request->observation[$item],
                            'qte' => $qte[$item],
                            'prixu' => $cmup[$item],
                            'montant' => $montant[$item],
                    ];


                    $retour_verif = Retour::where('id', $request->retours_id[$item])->first();
                    if ($retour_verif!=null) {
                        $montant_retour[$item] = $cmup[$item] * $retour_verif->qte_retour;
                    }else{
                        $montant_retour[$item] = 0;
                    }

                    $retour_valide_verif = ValiderRetour::where('retours_id', $request->retours_id[$item])->first();
                    if ($retour_valide_verif!=null) {
                        $montant_retour_valide[$item] = $cmup[$item] * $retour_valide_verif->qte_validee;
                    }else{
                        $montant_retour_valide[$item] = 0;
                    }

                    $data_retour = [
                        'prixu_retour' => $cmup[$item],
                        'montant_retour' => $montant_retour[$item],
                    ];

                    $data_valide_retour = [
                        'prixu_retour_valide' => $cmup[$item],
                        'montant_retour_valide' => $montant_retour_valide[$item],
                    ];


                    
                    $livraison_retour = LivraisonRetour::where('retours_id',$request->retours_id[$item])
                        ->first();
                    if ($livraison_retour === null) {
                        
                        $livree = LivraisonRetour::create($data);
                        
                        Retour::where('id', $request->retours_id[$item])->update($data_retour);
                        ValiderRetour::where('retours_id', $request->retours_id[$item])->update($data_valide_retour);

                        $libelle = "Sortie de stock";
                        $subject = "Livraison du retour de Réquisitions";

                    }else{
                        
                        $livree = LivraisonRetour::where('retours_id',$request->retours_id[$item])->update($data);
                        $livree = LivraisonRetour::where('retours_id',$request->retours_id[$item])->first();
                        
                        Retour::where('id', $request->retours_id[$item])->update($data_retour);
                        ValiderRetour::where('retours_id', $request->retours_id[$item])->update($data_valide_retour);


                        $subject = "Modification de la livraison du retour de Réquisitions";
                        $qte_precedante = $livraison_retour->qte;

                        $ecart = $qte_precedante - $qte[$item];

                        if ($ecart < 0) {
                            $libelle = "Sortie de stock";
                        }elseif($ecart==0){
                            $libelle = "Aucun mouvement de stock";
                        }else{
                            $libelle = "Entrée en stock, après modification de la quantité livrée";
                        }
                        
                    }


                    //Mouvement de stock
                if ($livree!=null) {


                    $taxe = null;

                    if($taxe === null){
                        $taux = 1;
                    }else{
                        $taux = 1 + ($taxe/100);
                    }


                    //traitement du type de mouvement
                    $type_mouvements = TypeMouvement::where('libelle',$libelle)->first();
                    if ($type_mouvements!=null) {
                        $type_mouvements_id = $type_mouvements->id;
                    }else{
                        $type_mouvements_id = TypeMouvement::create([
                            'libelle' => $libelle,
                        ])->id;
                    }
                    if ($statut===true) {
                        # code...
                    
                        if (!isset($ecart)) {
                            $qte_mvt = -1 * $livree->qte;
                            $montant_ht = $request->cmup[$item] * $qte_mvt;
                            $montant_ttc = $taux * $montant_ht;

                            $mouvement = Mouvement::create([
                                'type_mouvements_id'=>$type_mouvements_id,
                                'magasin_stocks_id'=>$magasin_stocks_id,
                                'profils_id'=>$profils_id,
                                'qte'=>$qte_mvt,
                                'prix_unit'=>$request->cmup[$item],
                                'montant_ht'=>$montant_ht,
                                'taxe'=>$taxe,
                                'montant_ttc'=>$montant_ttc,
                            ]);

                            //actualiser la quantité en stock
                            $mouvement = DB::table('mouvements')
                            ->select(DB::raw('SUM(montant_ttc) as montant_stock'),
                            DB::raw('SUM(qte) as qte_stock'))
                            ->where('magasin_stocks_id',$magasin_stocks_id)
                            ->groupBy('magasin_stocks_id')
                            ->first();
                            if ($mouvement!=null) {
                                $montant_stock = $mouvement->montant_stock;
                                $qte_stock = $mouvement->qte_stock;

                                $magasin_stock = MagasinStock::where('id', $magasin_stocks_id)->update([
                                    'qte' => $qte_stock,
                                    'montant' => $montant_stock,
                                ]);
                            }
                            
                        }

                        if (isset($ecart)) {

                            if (isset($statut_precedant)) {
                                if ($statut_precedant==0) {
                                    $qte_mvt = -1 * $livree->qte;
                                    $montant_ht = $request->cmup[$item] * $qte_mvt;
                                    $montant_ttc = $taux * $montant_ht;
        
                                    $mouvement = Mouvement::create([
                                        'type_mouvements_id'=>$type_mouvements_id,
                                        'magasin_stocks_id'=>$magasin_stocks_id,
                                        'profils_id'=>$profils_id,
                                        'qte'=>$qte_mvt,
                                        'prix_unit'=>$request->cmup[$item],
                                        'montant_ht'=>$montant_ht,
                                        'taxe'=>$taxe,
                                        'montant_ttc'=>$montant_ttc,
                                    ]);

                                    //actualiser la quantité en stock
                                    $mouvement = DB::table('mouvements')
                                    ->select(DB::raw('SUM(montant_ttc) as montant_stock'),
                                    DB::raw('SUM(qte) as qte_stock'))
                                    ->where('magasin_stocks_id',$magasin_stocks_id)
                                    ->groupBy('magasin_stocks_id')
                                    ->first();
                                    if ($mouvement!=null) {
                                        $montant_stock = $mouvement->montant_stock;
                                        $qte_stock = $mouvement->qte_stock;

                                        $magasin_stock = MagasinStock::where('id', $magasin_stocks_id)->update([
                                            'qte' => $qte_stock,
                                            'montant' => $montant_stock,
                                        ]);
                                    }
                                }else{
                                    $montant_ht = $request->cmup[$item] * $ecart;
                                    $montant_ttc = $taux * $montant_ht;
        
                                    $mouvement = Mouvement::create([
                                        'type_mouvements_id'=>$type_mouvements_id,
                                        'magasin_stocks_id'=>$magasin_stocks_id,
                                        'profils_id'=>$profils_id,
                                        'qte'=>$ecart,
                                        'prix_unit'=>$request->cmup[$item],
                                        'montant_ht'=>$montant_ht,
                                        'taxe'=>$taxe,
                                        'montant_ttc'=>$montant_ttc,
                                    ]);

                                    //actualiser la quantité en stock
                                    $mouvement = DB::table('mouvements')
                                    ->select(DB::raw('SUM(montant_ttc) as montant_stock'),
                                    DB::raw('SUM(qte) as qte_stock'))
                                    ->where('magasin_stocks_id',$magasin_stocks_id)
                                    ->groupBy('magasin_stocks_id')
                                    ->first();
                                    if ($mouvement!=null) {
                                        $montant_stock = $mouvement->montant_stock;
                                        $qte_stock = $mouvement->qte_stock;

                                        $magasin_stock = MagasinStock::where('id', $magasin_stocks_id)->update([
                                            'qte' => $qte_stock,
                                            'montant' => $montant_stock,
                                        ]);
                                    }
                                }
                            }

                                
                        }

                        
                        
                    }

            }


                    
                }

                
            }

            if ($mouvement != null) {

                if (isset($requisitions_id)) {
                    
                    $email = auth()->user()->email;
                    
    
                    $datas = [
                        'requisitions_id'=>$requisitions_id,
                        'subject'=>$subject,
                        'retour_livre' => 1
                    ];
    
                    Mail::to($email)->send(new \App\Mail\SendMailCreateRequisition($datas));

                    // determinier le mail du demandeur
                $strucs_demandeurs = DB::select("SELECT * FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st, profils as p, type_profils as tp, requisitions as r WHERE u.id = p.users_id AND p.type_profils_id = tp.id AND a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND r.profils_id = p.id AND tp.name like 'Agent Cnps' AND r.id = '".$requisitions_id."' ");

                foreach ($strucs_demandeurs as $strucs_demandeur) {
                    if ($strucs_demandeur->email!=null) {
                        Mail::to($strucs_demandeur->email)->send(new \App\Mail\SendMailCreateRequisition($datas));





                        // determinier le mail du pilote AEE
                        $strucs = DB::select("SELECT * FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st, profils as p, type_profils as tp WHERE u.id = p.users_id AND p.type_profils_id = tp.id AND a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND tp.name like 'Pilote AEE' AND st.code_structure IN (SELECT st.code_structure FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st WHERE a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND u.email = '".$strucs_demandeur->email."') ");

                        foreach ($strucs as $struc) {
                            if ($struc->email!=null) {
                                Mail::to($struc->email)->send(new \App\Mail\SendMailCreateRequisition($datas));
                            }
                            
                        }


                    }
                }
    
                }
                
                return redirect()->back()->with('success','Enregistrement réussi');
            }else{
                return redirect()->back()->with('error','Enregistrement echoué');
            }
            


        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LivraisonRetour  $livraisonRetour
     * @return \Illuminate\Http\Response
     */
    public function show(LivraisonRetour $livraisonRetour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LivraisonRetour  $livraisonRetour
     * @return \Illuminate\Http\Response
     */
    public function edit(LivraisonRetour $livraisonRetour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LivraisonRetour  $livraisonRetour
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LivraisonRetour $livraisonRetour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LivraisonRetour  $livraisonRetour
     * @return \Illuminate\Http\Response
     */
    public function destroy(LivraisonRetour $livraisonRetour)
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
        
        $delete_livraisonRetour = LivraisonRetour::where('id',$livraisonRetour->id)->delete();
        if ($delete_livraisonRetour!=null) {
            return redirect()->back()->with('success','Livraison supprimée');
        }else{
            return redirect()->back()->with('error','Echec de la suppression');
        }
    }
}
