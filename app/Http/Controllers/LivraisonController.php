<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\SendEmail;
use App\Models\Demande;
use App\Models\Livraison;
use App\Models\Mouvement;
use App\Models\Requisition;
use App\Models\Consommation;
use App\Models\Distribution;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use App\Models\TypeMouvement;
use App\Models\DemandeConsolide;
use App\Models\StatutRequisition;
use App\Models\ValiderRequisition;
use Illuminate\Support\Facades\DB;
use App\Models\TypeStatutRequisition;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class LivraisonController extends Controller
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

        $valider_requisitions = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
                            ->join('livraisons','livraisons.demandes_id','=','demandes.id')
                            ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                            ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->where('livraisons.statut',1)
                            ->select('num_dem','num_bc','exercice','intitule','code_gestion','mle','nom_prenoms','demandes.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte_demandee','magasin_stocks.cmup','valider_requisitions.profils_id','valider_requisitions.qte_validee','valider_requisitions.flag_valide','valider_requisitions.id as valider_requisitions_id','livraisons.id as livraisons_id')
                            ->paginate(10);
        
        return view('livraisons.index',[
            'valider_requisitions' => $valider_requisitions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($requisition)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisition);
        } catch (DecryptException $e) {
            //
        }

        $requisition = Requisition::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            
            $etape = "create";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$requisition->id);
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        
                
        $ref_depot = null;
        $error = null;
        $table = null;
        $livaison = null;

        $submit = null;
        $libelle = null;
        $type_profils_name = [];
        $type_profils_name = null;



        $valider_reception = 0;
        $demandes = [];

        $requisitions_id = $requisition->id;

        $depot = $this->depot_requisition($requisitions_id);
        if ($depot!=null) {
            $ref_depot = $depot->ref_depot;
        }
        
        $statut_requisition = $this->dernier_statut_requisition($requisitions_id);

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        if ($statut_requisition!=null) {
            $libelle = $statut_requisition->libelle;
        }

        
        if ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks') {
            if ($libelle != 'Soumis pour livraison' && $libelle != 'Livraison partielle'  && $libelle != 'Livraison partielle [ Confirmé ]') {
                return redirect()->back()->with('error','Accès refusé');
            }
        }elseif($type_profils_name === 'Pilote AEE'){
            if ($libelle != 'Livraison totale' && $libelle != 'Livraison partielle' && $libelle != 'Livraison partielle [ Confirmé ]') {
                return redirect()->back()->with('error','Accès refusé');
            }
        }
        if($type_profils_name === 'Pilote AEE'){
            $profil = $this->acces_validateur($requisitions_id,$libelle,$type_profils_name,$ref_depot,$submit);
            
            //dd('ici',$profil,$libelle);
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }
        }

        
        $profils_id = $this->profil_connect($type_profils_name);

        
        if ($profils_id === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        if ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks') {
            
            $demandes = $this->demande($requisitions_id,$type_profils_name);

        }elseif ($type_profils_name === "Pilote AEE") {
            $valider_reception = 1;
            $demandes = $this->demande($requisitions_id,$type_profils_name);
        }

        // pilote
        
        // $profil_pilote = $this->profil_aee($requisitions_id);

        // dd($demandes,$type_profils_name,$profils_id,$profil,$libelle,$statut_requisition,$ref_depot);

        

        //le responsable des stocks
        // $profil_respo_stock = DB::table('users as u')
        //         ->join('profils as p','p.users_id','=','u.id')
        //         ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        //         ->join('responsable_depots as rd','rd.profils_id','=','p.id')
        //         ->join('statut_responsable_depots as srd','srd.responsable_depots_id','=','rd.id')
        //         ->join('type_statut_responsable_depots as tsrd','tsrd.id','=','srd.type_statut_r_dep_id')
        //         ->join('structures as st','st.ref_depot','=','rd.ref_depot')
        //         ->join('requisitions as r','r.code_structure','=','st.code_structure')
        //         ->where('tp.name','Responsable des stocks')
        //         ->where('tsrd.libelle','Activé')
        //         ->where('r.id',$requisitions_id)
        //         ->where('p.users_id',auth()->user()->id)
        //         ->select('p.id','rd.ref_depot')
        //         ->first();
        
        //le gestionnaire des stocks
        // $gestionnaire_stocks = DB::table('users as u')
        //         ->join('profils as p', 'p.users_id', '=', 'u.id')
        //         ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
        //         ->join('agents as a', 'a.id', '=', 'u.agents_id')
        //         ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
        //         ->join('sections as s', 's.id', '=', 'ase.sections_id')
        //         ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
        //         ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
        //         ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
        //         ->where('tp.name', 'Gestionnaire des stocks')
        //         ->where('tsas.libelle', 'Activé')
        //         ->select('p.id as profils_id')
        //         ->where('p.flag_actif', 1)
        //         ->where('p.users_id',auth()->user()->id)
        //         ->where('st.ref_depot', function($query) use($requisitions_id){
        //             $query->select(DB::raw('sst.ref_depot'))
        //                 ->from('requisitions as rr')
        //                 ->join('structures as sst','sst.code_structure','=','rr.code_structure')
        //                 ->whereRaw('st.ref_depot = sst.ref_depot')
        //                 ->where('rr.id',$requisitions_id);
        //         })
        //         ->first();

        //l' Agent Cnps
        // $profil_agent = DB::table('users as u')
        //     ->join('profils as p','p.users_id','=','u.id')
        //     ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        //     ->join('demandes as d','d.profils_id','=','p.id')
        //     ->join('livraisons as l','l.demandes_id','=','d.id')
        //     ->join('requisitions as r','r.id','=','d.requisitions_id')
        //     ->where('tp.name','Agent Cnps')
        //     ->where('r.id',$requisitions_id)
        //     ->where('p.users_id',auth()->user()->id)
        //     ->select('p.id')
        //     ->where('p.flag_actif',1)
        //     ->where('l.statut',1)
        //     ->first();
        
        

        
        
            // if ($profil_agent!=null) {

            //     $valider_reception = 1;

            //     $demandes = DB::table('requisitions as r')
            //                 ->join('demandes as d','d.requisitions_id','=','r.id')
            //                 ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
            //                 ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            //                 ->join('structures as st','st.code_structure','=','r.code_structure')
            //                 ->join('livraisons as l', 'l.demandes_id', '=', 'd.id')
            //                 ->join('profils as p', 'p.id', '=', 'd.profils_id')
            //                 ->join('users as u', 'u.id', '=', 'p.users_id')
            //                 ->join('agents as ag', 'ag.id', '=', 'u.agents_id')
            //                 ->where('r.id', $requisition->id)
            //                 ->where('l.statut', 1)
            //                 ->where('u.id', auth()->user()->id)
            //                 ->whereRaw('l.qte != l.qte_recue')
            //                 ->select('d.id', 'a.ref_articles', 'a.design_article', 'ms.cmup', 'd.qte as qte_demandee', 'ms.cmup', 'l.qte as qte_validee', 'ms.qte', 'ag.mle', 'ag.nom_prenoms', 'l.id as livraisons_id', 'l.qte_recue')
            //                 ->whereIn('l.id', function ($query) {
            //                     $query->select(DB::raw('ll.id'))
            //                           ->from('demandes as dd')
            //                           ->join('livraisons as ll', 'll.demandes_id', '=', 'dd.id')
            //                           ->where('ll.statut', 1)
            //                           ->whereRaw('dd.id = d.id')
            //                           ->whereRaw('ll.id = l.id');
            //                 })
            //                 ->get();

            // }elseif ($gestionnaire_stocks!=null or $profil_respo_stock!=null) {

            //     $demandes = DB::table('requisitions as r')
            //                 ->join('demandes as d','d.requisitions_id','=','r.id')
            //                 ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
            //                 ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            //                 ->join('structures as st','st.code_structure','=','r.code_structure')
            //                 ->join('valider_requisitions as vr', 'vr.demandes_id', '=', 'd.id')
            //                 ->join('profils as p', 'p.id', '=', 'd.profils_id')
            //                 ->join('users as u', 'u.id', '=', 'p.users_id')
            //                 ->join('agents as ag', 'ag.id', '=', 'u.agents_id')
            //                 ->where('r.id', $requisition->id)
            //                 ->where('vr.flag_valide', 1) // réquisition validée
            //                 ->select('d.id', 'a.ref_articles', 'a.design_article', 'ms.cmup', 'd.qte as qte_demandee', 'ms.cmup', 'vr.qte as qte_validee', 'ms.qte', 'ag.mle', 'ag.nom_prenoms')
            //                 ->whereIn('vr.id', function ($query) {
            //                     $query->select(DB::raw('vrr.id'))
            //                           ->from('demandes as dd')
            //                           ->join('valider_requisitions as vrr', 'vrr.demandes_id', '=', 'dd.id')
            //                           ->join('profils as pp', 'pp.id', '=', 'vrr.profils_id')
            //                           ->join('type_profils as tpp', 'tpp.id', '=', 'pp.type_profils_id')
            //                           ->join('statut_requisitions as sr', 'sr.requisitions_id', '=', 'dd.requisitions_id')
            //                           ->join('type_statut_requisitions as tsr', 'tsr.id', '=', 'sr.type_statut_requisitions_id')
            //                           ->where('vrr.flag_valide', 1)
            //                           ->whereIn('tsr.libelle', ['Validé (Responsable des stocks)'])
            //                           ->whereIn('tpp.name', ['Responsable des stocks'])
            //                           ->whereRaw('dd.id = d.id')
            //                           ->whereRaw('vrr.id = vr.id');
            //                 })
            //                 ->get();
                            
            // }
            

        $requisitions = DB::table('requisitions as r')
                            ->join('gestions as g','g.code_gestion','=','r.code_gestion')
                            ->join('structures as st','st.code_structure','=','r.code_structure')
                            ->where('r.id',$requisition->id)
                            ->select('st.*','g.*','r.*')
                            ->first();
        
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        $libelle = null;

        $statut_requisition = DB::table('statut_requisitions as sr')
        ->where('sr.requisitions_id', $requisition->id)
        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
        ->join('profils as p','p.id','=','sr.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->orderByDesc('id')
        ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
        ->limit(1)
        ->first();

        if ($statut_requisition!=null) {
            $commentaire = $statut_requisition->commentaire;
            $libelle = $statut_requisition->libelle;
            $profil_commentaire = $statut_requisition->name;
            $nom_prenoms_commentaire = $statut_requisition->nom_prenoms;

        }



        $confirmation_commentee = DB::table('livraisons as l')
        ->join('demandes as d','d.id','=','l.demandes_id')
        ->where('d.requisitions_id', $requisition->id)
        ->whereNotIn('l.commentaire',['NULL'])
        ->first();

        return view('livraisons.create',[
            'demandes' => $demandes,
            'requisitions' => $requisitions,
            'valider_reception'=>$valider_reception,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            // 'profil_agent'=>$profil_agent,
            'confirmation_commentee'=>$confirmation_commentee,
            'type_profils_name'=>$type_profils_name

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
        

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back();
        }
        
        
        $ref_depot = null;
        $error = null;
        $table = null;
        $livaison = null;
        $valider_reception = null;
        
        $validate = $request->validate([
            'submit'=>['required','string'],
        ]);

        $submit = $request->submit;

        $this->validate_request($request,$submit);

        
        

        $requisitions_id = $request->requisitions_id;

        $depot = $this->depot_requisition($requisitions_id);
        if ($depot!=null) {
            $ref_depot = $depot->ref_depot;
        }

        

        

        
        $statut_requisition = $this->dernier_statut_requisition($requisitions_id);

        if ($statut_requisition!=null) {
            $libelle = $statut_requisition->libelle;
        }

        

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        if ($type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {
            if ($submit!='livrer') {
                return redirect()->back()->with('error','Vous n\'avez pas le profile requis pour effectuer cette opération');
            }
        }elseif ($type_profils_name === 'Pilote AEE') {
            if ($submit!='reception') {
                return redirect()->back()->with('error','Vous n\'avez pas le profile requis pour effectuer cette opération');
            }
        }

        
        if($type_profils_name === 'Pilote AEE'){
            $profil = $this->acces_validateur($requisitions_id,$libelle,$type_profils_name,$ref_depot,$submit);
            
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }
        }

        $profils_id = $this->profil_connect($type_profils_name);

        if ($profils_id === null) {
            return redirect()->back();
        }

        

        if ($submit === "livrer") {
            if (isset($request->approvalcd)) {
                foreach ($request->demandes_id as $item => $value) {
                    $error = $this->valide_saisie($request, $item);
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez sélectionner au moins un article à livrer');
            }
        }elseif ($submit === "reception") {
            if (isset($request->approvalcd)) {
                foreach ($request->livraisons_id as $item => $value) {
                    $error = $this->valide_saisie($request, $item);
                }
            } else {
                return redirect()->back()->with('error', 'Veuillez sélectionner au moins un article');
            }
        }

        

        if ($error!=null) {
            return redirect()->back()->with('error',$error);
        }


        if ($submit === "livrer") {

            if (isset($request->approvalcd)) {
                foreach ($request->demandes_id as $item => $value) {
                    if (isset($request->approvalcd[$item])) {
                        $livaison = $this->livraison($request, $item, $profils_id);
                    }
                }
            }

        }elseif ($submit === "reception") {

            if (isset($request->approvalcd)) {
                if (count($request->livraisons_id) > 0) {
                    foreach ($request->livraisons_id as $item => $value) {

                        if (isset($request->approvalcd[$item])) {

                            $valider_reception = $this->valider_reception($request, $item, $profils_id);

                        }

                    }
                }
            }
            
            

        }


        if ($submit === "livrer") {
            if ($livaison!=null) {

                $table = "livraisons";
                
                $libelle_statut = $this->statut_valider_requisition($requisitions_id,$table);

                $message = "Demande d'article [ ".$libelle_statut." ]";
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);
    
            }else {
                return redirect()->back()->with('error','echec de la livraison');
            }
        }elseif ($submit === "reception") {
            if ($valider_reception!=null) {

                $dernier_statut_livraison = $this->statut_livraison($request->requisitions_id);
                
                $libelle_statut = $dernier_statut_livraison->libelle." [ Confirmé ]";
                
                $message = "Demande d'article [ ".$libelle_statut." ]";
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);
    
            }else {
                return redirect()->back()->with('error','echec de la confirmation');
            }
        }
        
        if ($statut_requisition_store!=null) {
            $this->notification_demandeur_consolide2($requisitions_id,$message);
            $responsable = 3;
            $this->notification_hierarchie_consolide2($requisitions_id,$responsable,$message);
            $this->notification_pilote2($requisitions_id,$message);
            $this->notification_responsable_stock($requisitions_id,$message);

            if($type_profils_name === "Responsable des stocks" or $type_profils_name === "Gestionnaire des stocks"){

            }

            return redirect('/requisitions/index')->with('success',$message);
        }else{
            return redirect()->back()->with('error','Echec de l\'opération');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Livraison  $livraison
     * @return \Illuminate\Http\Response
     */
    public function show(Livraison $livraison)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Livraison  $livraison
     * @return \Illuminate\Http\Response
     */
    public function edit(Livraison $livraison)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Livraison  $livraison
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Livraison $livraison)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Livraison  $livraison
     * @return \Illuminate\Http\Response
     */
    public function destroy(Livraison $livraison)
    {
        //
    }

    public function distribution($requisition){

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisition);
        } catch (DecryptException $e) {
            //
        }

        $requisition = Requisition::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            
            $etape = "distribution";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$requisition->id);
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $demandes = [];
        
        $demandes = DB::table('requisitions as r')
        ->join('demandes as d','d.requisitions_id_consolide','=','r.id')
        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
        ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
        ->join('articles as ar','ar.ref_articles','=','ms.ref_articles')
        ->join('profils as p','p.id','=','d.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->select('a.mle','a.nom_prenoms','ar.ref_articles','ar.design_article','dc.qte','dc.id as demande_consolides_id','dc.demandes_id','dc.demandes_ids','r.id as requisitions_id')
        ->where('r.id',$requisition->id)
        ->get();

        $valider_requisitions = DB::table('requisitions as r')
        ->join('demandes as d','d.requisitions_id','=','r.id')
        ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
        ->select('vr.qte','d.id as demandes_ids')
        ->where('r.id',$requisition->id)
        ->get();
        
        // dd($demandes,$requisition);
        
        $requisitions = DB::table('requisitions as r')
                            ->join('gestions as g','g.code_gestion','=','r.code_gestion')
                            ->join('structures as st','st.code_structure','=','r.code_structure')
                            ->where('r.id',$requisition->id)
                            ->select('st.*','g.*','r.*')
                            ->first();
        
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        $libelle = null;

        $statut_requisition = DB::table('statut_requisitions as sr')
        ->where('sr.requisitions_id', $requisition->id)
        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
        ->join('profils as p','p.id','=','sr.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->orderByDesc('id')
        ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
        ->limit(1)
        ->first();

        if ($statut_requisition!=null) {
            $commentaire = $statut_requisition->commentaire;
            $libelle = $statut_requisition->libelle;
            $profil_commentaire = $statut_requisition->name;
            $nom_prenoms_commentaire = $statut_requisition->nom_prenoms;

        }

        $statut_distribution = $this->statut_distribution($requisition->id);
        if ($statut_distribution!=null) {
            $commentaire = $statut_distribution->commentaire;
            $profil_commentaire = $statut_distribution->name;
            $nom_prenoms_commentaire = $statut_distribution->nom_prenoms;
        }

        $stock_pilotes = DB::table('livraisons as l')
        ->join('demandes as d','d.id','=','l.demandes_id')
        ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->where('d.requisitions_id',$requisition->id)
        ->where('l.statut',1)
        ->groupBy('l.demandes_id')
        ->select(DB::raw('sum(l.qte_recue) as qte_recue'),DB::raw('l.demandes_id'),DB::raw('a.ref_articles'),DB::raw('a.design_article'))
        ->get();

        $stock_distribues = DB::table('livraisons as l')
        ->join('demandes as d','d.id','=','l.demandes_id')
        ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->join('distributions as dt','dt.livraisons_id','=','l.id')
        ->where('d.requisitions_id',$requisition->id)
        ->where('l.statut',1)
        ->where('dt.flag_reception', '!=' , 2)
        ->groupBy('l.demandes_id')
        ->select(DB::raw('sum(dt.qte) as qte_distribuee'),DB::raw('l.demandes_id'),DB::raw('a.ref_articles'),DB::raw('a.design_article'))
        ->get();

        $distributions_annulees = DB::table('requisitions as r')
            ->join('demandes as d','d.requisitions_id_consolide','=','r.id')
            ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
            ->join('distributions as dt','dt.demande_consolides_id','=','dc.id')
            ->join('livraisons as l','l.id','=','dt.livraisons_id')
            ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
            ->join('articles as ar','ar.ref_articles','=','ms.ref_articles')
            ->join('profils as p','p.id','=','d.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->select('a.mle','a.nom_prenoms','ar.ref_articles','ar.design_article','dc.qte','dc.id as demande_consolides_id','dc.demandes_id','dc.demandes_ids','r.id as requisitions_id','dt.qte','dt.updated_at')
            ->where('r.id',$requisition->id)
            ->where('dt.flag_reception',2)
            ->get();

        return view('livraisons.distribution',[
            'requisitions' => $requisitions,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'demandes'=>$demandes,
            'statut_distribution'=>$statut_distribution,
            'stock_pilotes'=>$stock_pilotes,
            'stock_distribues'=>$stock_distribues,
            'valider_requisitions'=>$valider_requisitions,
            'distributions_annulees'=>$distributions_annulees
        ]);
    }

    public function distributions_store(Request $request){
        
        if (Session::has('profils_id')) {
            
            $etape = "distributions_store";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$request->requisitions_id);
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $validate = $request->validate([
            'submit'=>['required','string']
        ]);

       

        $error = null;
        $submit = $request->submit;
        $distributions_id = null;

        if ($submit === "distribution") {
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            if ($type_profils_name != 'Pilote AEE') {
                return redirect()->back()->with('error','Accès refusé');
            }

        }

        $profils_id = $this->profil_connect($type_profils_name);

        if ($profils_id === null) {
            return redirect()->back();
        }


        $this->validate_request($request,$submit);

        

        if (isset($request->approvalcd)) {

            if (count($request->approvalcd)>0) {

                foreach ($request->approvalcd as $item => $value) {

                    if (isset($request->approvalcd[$item])) {
                            
                        if (isset($request->qte_livree[$item])) {
                            
                            if (!empty($request->qte_livree[$item])) {
                                
                                $error = $this->control_saisie_distribution($request->demande_consolides_id[$item],$request->qte_demandee[$item],$request->qte_validee[$item],$request->qte_livree[$item],$submit);
    
                            }

                        }

                    }

                }

            }

        }else{
            return redirect()->back()->with('error','Veuillez sélectionner au moins un article');
        }
        
       if ($error != null) {
        return redirect()->back()->with('error',$error);
       }

       

        if (isset($request->approvalcd)) {

            if (count($request->approvalcd)>0) {

                foreach ($request->approvalcd as $item => $value) {

                    if (isset($request->approvalcd[$item])) {

                                
                        if (isset($request->qte_livree[$item])) {

                                
                            if (!empty($request->qte_livree[$item])) {

                                
        
                                $demande = $this->getDemande($request->demandes_ids[$item]);

                                if($demande != null){
                                    $this->storeValiderRequisition2($profils_id,$request->demandes_id[$item],$request->qte_validee[$item],$demande->prixu);
                                }

                                $distributions_id = $this->store_distribution($request->demande_consolides_id[$item],$request->qte_livree[$item],$profils_id,$request->demandes_id[$item],$request->commentaire);
        
                            }
    
                                
    
                        }

                            

                    }

                }

            }

        }


        if ($distributions_id != null) {

            $libelle_statut_distribution = $this->libelle_statut_distribution($request,$request->requisitions_id,$profils_id);

            return redirect('/requisitions/index')->with('success','Distribution effectuée');

        }else{
            return redirect()->back()->with('error','Distribution echoué');
        }
        

        



        
    }

    public function reception($requisition){
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisition);
        } catch (DecryptException $e) {
            //
        }

        $requisition = Requisition::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            
            $etape = "reception";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$requisition->id);
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $demandes = [];
        $demandes = DB::table('requisitions as r')
        ->join('demandes as d','d.requisitions_id','=','r.id')
        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
        ->join('distributions as di','di.demande_consolides_id','=','dc.id')
        ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
        ->join('articles as ar','ar.ref_articles','=','ms.ref_articles')
        ->join('profils as p','p.id','=','r.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->select('a.mle','a.nom_prenoms','ar.ref_articles','ar.design_article','di.qte','dc.id as demande_consolides_id','dc.demandes_id','dc.demandes_ids','di.id as distributions_id','di.created_at')
        ->where('r.id',$requisition->id)
        ->where('di.flag_reception', '!=' , 2)
        ->get();
        
        $requisitions = DB::table('requisitions as r')
                            ->join('gestions as g','g.code_gestion','=','r.code_gestion')
                            ->join('structures as st','st.code_structure','=','r.code_structure')
                            ->where('r.id',$requisition->id)
                            ->select('st.*','g.*','r.*')
                            ->first();
        
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        $libelle = null;

        $statut_requisition = DB::table('statut_requisitions as sr')
        ->where('sr.requisitions_id', $requisition->id)
        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
        ->join('profils as p','p.id','=','sr.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->orderByDesc('id')
        ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
        ->limit(1)
        ->first();

        if ($statut_requisition!=null) {
            $commentaire = $statut_requisition->commentaire;
            $libelle = $statut_requisition->libelle;
            $profil_commentaire = $statut_requisition->name;
            $nom_prenoms_commentaire = $statut_requisition->nom_prenoms;

        }

        $statut_distribution = $this->statut_distribution($requisition->id);
        if ($statut_distribution!=null) {
            $commentaire = $statut_distribution->commentaire;
            $profil_commentaire = $statut_distribution->name;
            $nom_prenoms_commentaire = $statut_distribution->nom_prenoms;
        }

        return view('livraisons.reception',[
            'requisitions' => $requisitions,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'demandes'=>$demandes,
            'statut_distribution'=>$statut_distribution
        ]);
    }

    public function receptions_store(Request $request){

        $validate = $request->validate([
            'submit'=>['required','string'],
            'requisitions_id'=>['required','numeric']
        ]);

        if (Session::has('profils_id')) {
            
            $etape = "receptions_store";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$request->requisitions_id);
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        
        $error = null;
        $submit = $request->submit;
        $response = null;

        if($submit === "annuler_consommation") {
            $validate = $request->validate([
                'commentaire'=>['required','string']
            ]);
        }

        if ($submit === "consommation" or $submit === "annuler_consommation") {

            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
            if ($type_profils_name != 'Agent Cnps') {
                return redirect()->back()->with('error','Accès refusé');
            }

        }

        $profils_id = $this->profil_connect($type_profils_name);

        if ($profils_id === null) {
            return redirect()->back();
        }

        $this->validate_request($request,$submit);        

        if (isset($request->approvalcd)) {

            if (count($request->approvalcd)>0) {

                foreach ($request->approvalcd as $item => $value) {

                    if (isset($request->approvalcd[$item])) {
                                
                        if (isset($request->qte_validee[$item])) {

                            $error = $this->control_saisie_distribution($request->demande_consolides_id[$item],null,$request->qte_validee[$item],$request->qte_livree[$item],$submit);
    
                        }

                    }

                }

            }

        }else{
            return redirect()->back()->with('error','Veuillez sélectionner au moins un article');
        }
        
       if ($error != null) {
        return redirect()->back()->with('error',$error);
       }


       

       

        if (isset($request->approvalcd)) {

            if (count($request->approvalcd)>0) {

                foreach ($request->approvalcd as $item => $value) {

                    if (isset($request->approvalcd[$item])) {


                                
                        if (isset($request->qte_livree[$item])) {

                            if ($submit === "consommation") {
                                $flag_reception = 1;
                            }elseif($submit === "annuler_consommation") {
                                $flag_reception = 2;
                            }
                                
                            $response = $this->store_consommation($request->demande_consolides_id[$item],$request->qte_livree[$item],$profils_id,$request->commentaire,$request->distributions_id[$item],$request->demandes_id[$item],$flag_reception); 
                            
                            if(isset($flag_reception)){

                                if($flag_reception === 1){

                                    if($response != null){

                                        $type_piece = "DE_STOCK_CONSO";
                                        $this->comptabilisationDistributionConsommation($response,$type_piece);

                                    }

                                }

                            }
    
                        }                        

                    }

                }

            }

        }

        if ($response != null) {

            $this->libelle_statut_consommation($request,$request->requisitions_id,$profils_id);

            return redirect('/requisitions/index')->with('success','Réception enregistrée');

        }else{
            return redirect()->back()->with('error','Echec de la confirmation');
        }
        
    }

    public function validate_request($request,$submit = null){

        if (isset($submit)) {

            if ($submit === "livrer") {

                $validate = $request->validate([
                    'intitule'=>['required','string'],
                    'gestion'=>['required','string'],
                    'ref_articles' => ['required','array'],
                    'demandes_id' => ['required','array'],
                    'design_article' => ['required','array'],
                    'cmup' => ['required','array'],
                    'qte_stock' => ['required','array'],
                    'qte_validee' => ['required','array'],
                    'qte' => ['required','array'],
                    'montant' => ['required','array'],
                    'commentaire' => ['nullable','string'],
                    'approvalcd' => ['nullable','array'],
                    'submit' => ['required','string'],
                    'requisitions_id' => ['required','numeric'],
                ]);

            }elseif ($submit === "reception"){
                $validate = $request->validate([
                    'requisitions_id' => ['required','numeric'],
                    'intitule'=>['required','string'],
                    'gestion'=>['required','string'],
                    'livraisons_id'=>['required','array'],
                    'ref_articles' => ['required','array'],
                    'demandes_id' => ['required','array'],
                    'design_article' => ['required','array'],
                    'cmup' => ['required','array'],
                    'qte_stock' => ['required','array'],
                    'qte_validee' => ['required','array'],
                    'qte' => ['required','array'],
                    'montant' => ['required','array'],
                    'commentaire' => ['nullable','string'],
                    'approvalcd' => ['nullable','array'],
                    'submit' => ['required','string'],
                    
                ]);
            }elseif ($submit === "distribution") {
                $validate = $request->validate([
                    'requisitions_id'=>['required','numeric'],
                    'intitule'=>['required','string'],
                    'gestion'=>['required','string'],
                    'demande_consolides_id'=>['required','array'],
                    'demandes_ids'=>['required','array'],
                    'demandes_id'=>['required','array'],
                    'qte_validee'=>['required','array'],
                    'qte_livree'=>['nullable','array'],
                    'approvalcd'=>['nullable','array'],
                    'commentaire'=>['nullable','string'],
                    'submit'=>['required','string']
                ]);
            }elseif ($submit === "consommation") {

                $validate = $request->validate([
                    'distributions_id'=>['required','array'],
                    'requisitions_id'=>['required','numeric'],
                    'intitule'=>['required','string'],
                    'gestion'=>['required','string'],
                    'demande_consolides_id'=>['required','array'],
                    'demandes_ids'=>['required','array'],
                    'demandes_id'=>['required','array'],
                    'qte_validee'=>['required','array'],
                    'qte_livree'=>['nullable','array'],
                    'approvalcd'=>['nullable','array'],
                    'commentaire'=>['nullable','string'],
                    'submit'=>['required','string']
                ]);
            }

        }
        

    }

    public function depot_requisition($requisitions_id){

        $depot = DB::table('requisitions as r')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->where('r.id', $requisitions_id)
                ->first();
            
        return $depot;
        
    }

    public function dernier_statut_requisition($requisitions_id){

        $requisition = DB::table('requisitions as r')
            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
            ->join('profils as p','p.id','=','sr.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
            ->where('r.id',$requisitions_id)
            ->orderByDesc('sr.id')
            ->limit(1)
            ->first();
        
        return $requisition;
    }

    public function acces_validateur($requisitions_id,$libelle,$type_profils_name,$ref_depot = null,$submit = null){
        
        
        $profil = null;

        if (isset($submit)) {
            if ($submit === "livrer") {

                $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
                if ($infoUserConnect != null) {
                    
                    $ref_depot = $infoUserConnect->ref_depot;

                    if($ref_depot === 83) {
                        $profil = DB::table('users as u')
                        ->join('profils as p','p.users_id','=','u.id')
                        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('agent_sections as ase','ase.agents_id','=','a.id')
                        ->join('sections as s','s.id','=','ase.sections_id')
                        ->join('structures as st','st.code_structure','=','s.code_structure')
                        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                        ->where('tp.name',$type_profils_name)
                        ->where('tsas.libelle','Activé')
                        ->where('p.flag_actif',1)
                        ->where('a.id',auth()->user()->agents_id)
                        ->where('st.ref_depot',$ref_depot)
                        ->whereIn('st.ref_depot', function($query) use($requisitions_id,$libelle){
                            $query->select(DB::raw('dp.ref_depot'))
                                  ->from('requisitions as r')
                                  ->join('structures as st2','st2.code_structure','=','r.code_structure')
                                  ->join('demandes as d', 'd.requisitions_id', '=', 'r.id')
                                  ->join('magasin_stocks as ms', 'ms.id', '=', 'd.magasin_stocks_id')
                                  ->join('magasins as m', 'm.ref_magasin', '=', 'ms.ref_magasin')
                                  ->join('depots as dp', 'dp.id', '=', 'm.depots_id')
                                  ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                                  ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                                  ->whereIn('tsr.libelle',[$libelle])
                                  ->whereRaw('dp.ref_depot = st.ref_depot')
                                  ->where('r.id',$requisitions_id);
                        })
                        ->first();
                    }else{
                        $profil = DB::table('users as u')
                        ->join('profils as p','p.users_id','=','u.id')
                        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('agent_sections as ase','ase.agents_id','=','a.id')
                        ->join('sections as s','s.id','=','ase.sections_id')
                        ->join('structures as st','st.code_structure','=','s.code_structure')
                        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                        ->where('tp.name',$type_profils_name)
                        ->where('tsas.libelle','Activé')
                        ->where('p.flag_actif',1)
                        ->where('a.id',auth()->user()->agents_id)
                        ->where('st.ref_depot',$ref_depot)
                        ->whereIn('st.ref_depot', function($query) use($requisitions_id,$libelle){
                            $query->select(DB::raw('st2.ref_depot'))
                                  ->from('requisitions as r')
                                  ->join('structures as st2','st2.code_structure','=','r.code_structure')
                                  ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                                  ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                                  ->whereIn('tsr.libelle',[$libelle])
                                  ->whereRaw('st2.ref_depot = st.ref_depot')
                                  ->where('r.id',$requisitions_id);
                        })
                        ->first();
                    }
                }
                
            }elseif ($submit === "reception"){
                $profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                    ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('structures as st','st.code_structure','=','r.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    ->where('tp.name',$type_profils_name)
                    ->where('tsas.libelle','Activé')
                    ->whereIn('tsr.libelle',[$libelle])
                    ->where('p.flag_actif',1)
                    ->whereRaw('st.code_structure = r.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->where('r.id', $requisitions_id)
                    ->first();
            }
        }else{
            if ($libelle === "Soumis pour livraison") {

               //dd($libelle,$type_profils_name);

                /*$profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                    ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('structures as st','st.code_structure','=','r.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    //->where('tp.name',$type_profils_name)
                    ->where('tsas.libelle','Activé')
                    ->whereIn('tsr.libelle',[$libelle])
                    ->where('p.flag_actif',1)
                    ->whereRaw('st.code_structure = r.code_structure')
                    //->where('p.users_id', auth()->user()->id)
                    ->where('r.id', $requisitions_id)
                    ->first();*/

                    $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
                    if ($infoUserConnect != null) {

                        $ref_depot = $infoUserConnect->ref_depot;

                        if($ref_depot === 83) {
                            $profil = DB::table('profils as p')
                            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                            ->join('users as u', 'u.id', '=', 'p.users_id')
                            ->join('agents as a', 'a.id', '=', 'u.agents_id')
                            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                            ->where('p.users_id', auth()->user()->id)
                            ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                            ->limit(1)
                            //->select('p.id', 'se.code_section', 's.ref_depot')
                            ->where('p.flag_actif',1)
                            ->where('p.id',Session::get('profils_id'))
                            ->where('tsase.libelle','Activé')
                            ->whereIn('s.ref_depot',function($query) use($requisitions_id,$libelle){
                                $query->select(DB::raw('dp.ref_depot'))
                                        ->from('requisitions as r')
                                        ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                        ->join('demandes as d', 'd.requisitions_id', '=', 'r.id')
                                        ->join('magasin_stocks as ms', 'ms.id', '=', 'd.magasin_stocks_id')
                                        ->join('magasins as m', 'm.ref_magasin', '=', 'ms.ref_magasin')
                                        ->join('depots as dp', 'dp.id', '=', 'm.depots_id')
                                        ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                                        ->whereIn('tsr.libelle',[$libelle])
                                        ->where('r.id',$requisitions_id)
                                        ->whereRaw('dp.ref_depot = s.ref_depot');
                            })
                            ->first();
                        }else{
                            $profil = DB::table('profils as p')
                            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                            ->join('users as u', 'u.id', '=', 'p.users_id')
                            ->join('agents as a', 'a.id', '=', 'u.agents_id')
                            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                            ->where('p.users_id', auth()->user()->id)
                            ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                            ->limit(1)
                            //->select('p.id', 'se.code_section', 's.ref_depot')
                            ->where('p.flag_actif',1)
                            ->where('p.id',Session::get('profils_id'))
                            ->where('tsase.libelle','Activé')
                            ->whereIn('s.ref_depot',function($query) use($requisitions_id,$libelle){
                                $query->select(DB::raw('st.ref_depot'))
                                        ->from('requisitions as r')
                                        ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                        ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                                        ->whereIn('tsr.libelle',[$libelle])
                                        ->where('r.id',$requisitions_id)
                                        ->whereRaw('st.ref_depot = s.ref_depot');
                            })
                            ->first();
                        }
                    }

                    

            }elseif ($libelle === "Livraison totale") {
                $profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                    ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('structures as st','st.code_structure','=','r.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    ->where('tp.name',$type_profils_name)
                    ->where('tsas.libelle','Activé')
                    ->whereIn('tsr.libelle',[$libelle])
                    ->where('p.flag_actif',1)
                    ->whereRaw('st.code_structure = r.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->where('r.id', $requisitions_id)
                    ->first();
            }elseif ($libelle === "Livraison partielle") {
               
                /*$profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                    ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('structures as st','st.code_structure','=','r.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    ->where('tp.name',$type_profils_name)
                    ->where('tsas.libelle','Activé')
                    ->whereIn('tsr.libelle',[$libelle])
                    ->where('p.flag_actif',1)
                    ->whereRaw('st.code_structure = r.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->where('r.id', $requisitions_id)
                    ->first();*/

                    $profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                    ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('structures as st','st.code_structure','=','r.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    //->where('tp.name',$type_profils_name)
                    ->where('tsas.libelle','Activé')
                    ->whereIn('tsr.libelle',[$libelle])
                    ->where('p.flag_actif',1)
                    ->whereRaw('st.code_structure = r.code_structure')
                    //->where('p.users_id', auth()->user()->id)
                    ->where('r.id', $requisitions_id)
                    ->first();

            }elseif ($libelle === "Livraison partielle [ Confirmé ]") {
                // $profil = DB::table('users as u')
                //     ->join('profils as p','p.users_id','=','u.id')
                //     ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                //     ->join('agents as a','a.id','=','u.agents_id')
                //     ->join('agent_sections as ase','ase.agents_id','=','a.id')
                //     ->join('sections as s','s.id','=','ase.sections_id')
                //     ->join('requisitions as r','r.code_structure','=','s.code_structure')
                //     ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                //     ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                //     ->join('structures as st','st.code_structure','=','r.code_structure')
                //     ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                //     ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                //     ->where('tp.name',$type_profils_name)
                //     ->where('tsas.libelle','Activé')
                //     ->whereIn('tsr.libelle',[$libelle])
                //     ->where('p.flag_actif',1)
                //     ->whereRaw('st.code_structure = r.code_structure')
                //     ->where('p.users_id', auth()->user()->id)
                //     ->where('r.id', $requisitions_id)
                //     ->first();

                    $profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                    ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('structures as st','st.code_structure','=','r.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    //->where('tp.name',$type_profils_name)
                    ->where('tsas.libelle','Activé')
                    ->whereIn('tsr.libelle',[$libelle])
                    ->where('p.flag_actif',1)
                    ->whereRaw('st.code_structure = r.code_structure')
                    //->where('p.users_id', auth()->user()->id)
                    ->where('r.id', $requisitions_id)
                    ->first();
            }
        }
        
        
        return $profil;

    }

    public function valide_saisie($request,$item){
        $error = null;

        
        if ($request->demandes_id[$item]!=null) {
            $qte_controlle[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
            
            try {
                $qte_controlle[$item] = $qte_controlle[$item] * 1;
            } catch (\Throwable $th) {

                $error = 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique';
            }

            if (gettype($qte_controlle[$item])!='integer') {
                $error = 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique';
            }
        

            if ($qte_controlle[$item] === null) {
                $qte_controlle[$item] = 0;
            }

            $valid = ValiderRequisition::where('demandes_id', $request->demandes_id[$item])
            ->whereIn('valider_requisitions.id', function ($query) {
            $query->select(DB::raw('vr.id'))
                ->from('demandes as d')
                ->join('valider_requisitions as vr', 'vr.demandes_id', '=', 'd.id')
                ->join('profils as p', 'p.id', '=', 'vr.profils_id')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('statut_requisitions as sr', 'sr.requisitions_id', '=', 'd.requisitions_id')
                ->join('type_statut_requisitions as tsr', 'tsr.id', '=', 'sr.type_statut_requisitions_id')
                ->where('vr.flag_valide', 1)
                ->whereIn('tsr.libelle', ['Partiellement validé (Responsable des stocks)','Validé (Responsable des stocks)'])
                ->whereIn('tp.name', ['Responsable des stocks'])
                ->whereRaw('vr.demandes_id = valider_requisitions.demandes_id')
                ->whereRaw('vr.id = valider_requisitions.id');
                })
                ->first();

                if ($valid!=null) {
                    $qte_validee[$item] = $valid->qte;
                }else{
                    $qte_validee[$item] = 0;
                }

                


            if ($qte_validee[$item] < $qte_controlle[$item]) {
                $error = 'La quantité livrée ne peut être supérieure à la quantité validée';
            }
        }
            

        return $error;
    }

    public function livraison($request,$item,$profils_id){
        $mouvement = null;
        if ($request->demandes_id[$item]!=null){
            if (isset($request->approvalcd[$item])) {
                $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                try {
                    $qte[$item] = $qte[$item] * 1;
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                }

                if (gettype($qte[$item])!='integer') {
                    return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                }


                $montant[$item] = filter_var($request->montant[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                try {
                    $montant[$item] = $montant[$item] * 1;
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir un montant entier numérique');
                }

                if (gettype($montant[$item])!='integer') {
                    return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir un montant entier numérique');
                }


                $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                try {
                    $cmup[$item] = $cmup[$item] * 1;
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique');
                }

                if (gettype($cmup[$item])!='integer') {
                    return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique');
                }

                
                //identifier la réquisition
                if (!isset($requisitions_id)) {
                    $requisition = Demande::where('id', $request->demandes_id[$item])->first();
                    if ($requisition!=null) {
                        $requisitions_id = $requisition->requisitions_id;
                    }
                }

                //identification du magasin stock
                $magasin_stocks_id = Demande::where('id', $request->demandes_id[$item])->first()->magasin_stocks_id;

                //statut de la validation de la réquisition
                if (isset($request->approvalcd[$item])) {
                    $statut = true;
                } else {
                    $statut = false;
                    $qte[$item] = 0;
                    $montant[$item] = 0;
                }

                //données de la validation de la réquisition
                $data = [
                    'demandes_id' => $request->demandes_id[$item],
                    'profils_id' => $profils_id,
                    'statut' => $statut,
                    'qte' => $qte[$item],
                    'prixu' => $cmup[$item],
                    'montant' => $montant[$item],
                ];

                $dem_verif = Demande::where('id', $request->demandes_id[$item])->first();
                if ($dem_verif!=null) {
                    $montant_demande[$item] = $cmup[$item] * $dem_verif->qte_demandee;
                } else {
                    $montant_demande[$item] = 0;
                }

                $dem_valide_verif = ValiderRequisition::where('demandes_id', $request->demandes_id[$item])->first();
                if ($dem_valide_verif!=null) {
                    $montant_valide[$item] = $cmup[$item] * $dem_valide_verif->qte;
                } else {
                    $montant_valide[$item] = 0;
                }

                $data_demande = [
                    'prixu' => $cmup[$item],
                    'montant' => $montant_demande[$item],
                ];

                $data_valide = [
                    'prixu' => $cmup[$item],
                    'montant' => $montant_valide[$item],
                ];


                //vérification de l'existance du statut de cette réquisition
                
                //si inexistant
                $livree = Livraison::create($data);

                if($livree != null && isset($magasin_stocks_id)){
                    if($magasin_stocks_id != null){

                        $magasin_stock = $this->getMagasinStockById($magasin_stocks_id);

                        if($magasin_stock != null){

                            $detail_livraison_disponibles = $this->getDetailLivraisonDisponible($magasin_stock->ref_magasin,$magasin_stock->ref_articles);

                            $u = 1;
                            
                            foreach($detail_livraison_disponibles as $detail_livraison_disponible){

                                $qte_achatee = $detail_livraison_disponible->qte;
                                $qte_consommee = $detail_livraison_disponible->qte_consommee;

                                $qte_en_stock = $qte_achatee - $qte_consommee;
                                $qte_impute = null;
                                $break = null;
                                $ref_articles[$u] = $detail_livraison_disponible->ref_articles;
                                
                                $qte_reste_a_impute[$u] = null;

                                if(isset($qte_reste_a_impute[$u-1])){
                                    if($ref_articles[$u-1] != $ref_articles[$u]){
                                        $qte_reste_a_impute[$u-1] = null;
                                    }
                                }

                                if(isset($qte_reste_a_impute[$u-1])){

                                    if($qte_en_stock >= $qte_reste_a_impute[$u-1]){

                                        $qte_consommee = $qte_consommee + $qte_reste_a_impute[$u-1];
                                        $qte_impute = $qte_reste_a_impute[$u-1];

                                        $break = 1;
    
                                    }else{
    
                                        $qte_consommee = $qte_consommee + $qte_en_stock;

                                        $qte_impute = $qte_en_stock;
    
                                        $qte_reste_a_impute[$u] = $qte_reste_a_impute[$u-1] - $qte_impute;
    
                                    }

                                }else{

                                    if($qte_en_stock >= $qte[$item]){

                                        $qte_consommee = $qte_consommee + $qte[$item];
                                        $qte_impute = $qte[$item];

                                        $break = 1;
    
                                    }else{

                                        $qte_consommee = $qte_consommee + $qte_en_stock;
                                        $qte_impute = $qte_en_stock;
    
                                        $qte_reste_a_impute[$u] = $qte[$item] - $qte_impute;
    
                                    }

                                }
                                

                                if(isset($detail_livraison_disponible->detail_livraisons_id) && isset($livree->id) && isset($qte_impute)){
                                    $this->storeConsommationAchat($detail_livraison_disponible->detail_livraisons_id,$livree->id,$qte_impute);
                                }
                                
                                if(isset($detail_livraison_disponible->detail_livraisons_id) && isset($qte_consommee)){
                                    $this->setDetailLivraison($detail_livraison_disponible->detail_livraisons_id,$qte_consommee);
                                }                                


                                $u++;
                                if($break === 1){
                                    break;
                                }
                            }

                        }

                    }
                    
                    $type_piece = "DE_STOCK";
                
                    $this->comptabilisationDistribution($livree->id,$type_piece);

                }

                Demande::where('id', $request->demandes_id[$item])->update($data_demande);
                ValiderRequisition::where('demandes_id', $request->demandes_id[$item])->update($data_valide);
                $libelle = "Sortie du stock";
            



                //Mouvement de stock
                if ($livree!=null) {

                    $taxe = null;

                    if ($taxe === null) {
                        $taux = 1;
                    } else {
                        $taux = 1 + ($taxe/100);
                    }


                    //traitement du type de mouvement
                    $type_mouvements = TypeMouvement::where('libelle', $libelle)->first();
                    if ($type_mouvements!=null) {
                        $type_mouvements_id = $type_mouvements->id;
                    } else {
                        $type_mouvements_id = TypeMouvement::create([
                                'libelle' => $libelle,
                            ])->id;
                    }
                    if ($statut===true) {
                        # code...
                        
                        $qte_mvt = -1 * $livree->qte;
                        $montant_ht = $cmup[$item] * $qte_mvt;
                        $montant_ttc = $taux * $montant_ht;
    
                        $mouvement = Mouvement::create([
                                    'type_mouvements_id'=>$type_mouvements_id,
                                    'magasin_stocks_id'=>$magasin_stocks_id,
                                    'profils_id'=>$profils_id,
                                    'qte'=>$qte_mvt,
                                    'prix_unit'=>$cmup[$item],
                                    'montant_ht'=>$montant_ht,
                                    'taxe'=>$taxe,
                                    'montant_ttc'=>$montant_ttc,
                                ]);
                        
                                Mouvement::where('id', $mouvement->id)->update([
                                    'created_at'=>$request->date_livraison,
                                    'updated_at'=>$request->date_livraison,
                                ]);

                        $data_consomateur = [
                                'mouvements_id' => $mouvement->id,
                            ];


                        Livraison::where('id', $livree->id)->update($data_consomateur);

                        //actualiser la quantité en stock
                        $mouvement = DB::table('mouvements')
                                ->select(
                                    DB::raw('SUM(montant_ttc) as montant_stock'),
                                    DB::raw('SUM(qte) as qte_stock')
                                )
                                ->where('magasin_stocks_id', $magasin_stocks_id)
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

                        Livraison::where('id', $livree->id)->update([
                            'created_at'=>$request->date_livraison,
                            'updated_at'=>$request->date_livraison,
                        ]);

                        MagasinStock::where('id', $magasin_stocks_id)->update([
                            'updated_at'=>$request->date_livraison,
                        ]);

                        
                    }
                }
            }
        }

        return $mouvement;
    }

    public function profil_connect($type_profils_name){

        $profils_id = null;

        $profil = DB::table('profils as p')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->where('u.id',auth()->user()->id)
            ->where('p.flag_actif',1)
            ->where('tp.name',$type_profils_name)
            ->select('p.id as profils_id')
            ->first();
        
        if ($profil != null) {
            $profils_id = $profil->profils_id;
        }
        
        return $profils_id;
    }

    public function statut_valider_requisition($requisitions_id,$table){

        $libelle = null;

        if (isset($table)) {
            if ($table === "valider_requisitions") {

                $nbre_total = count(
                    Demande::where('requisitions_id',$requisitions_id)->get()
                );

                $demande = Demande::where('requisitions_id',$requisitions_id)
                ->select(DB::raw('SUM(qte) as qte'))
                ->groupBy('requisitions_id')
                ->first();
                if ($demande!=null) {
                    $nbre_qte_total = $demande->qte;
                }

                $valider_requisition = DB::table('demandes as d')
                ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                ->where('vr.flag_valide',1)
                ->select(DB::raw('SUM(vr.qte) as qte'))
                ->where('d.requisitions_id',$requisitions_id)->get();
                if ($valider_requisition!=null) {
                    $nbre_qte_valide = $demande->qte;
                }

                $nbre_valide = count(
                    DB::table('demandes as d')
                    ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                    ->where('vr.flag_valide',1)
                    ->where('d.requisitions_id',$requisitions_id)->get()
                );

                if ($nbre_total === $nbre_valide) {
                    $libelle = "Validé (Responsable des stocks)";

                    if ($nbre_qte_total === $nbre_qte_valide) {
                        $libelle = "Validé (Responsable des stocks)";
                    }elseif ($nbre_qte_total > $nbre_qte_valide) {
                        $libelle = "Partiellement validé (Responsable des stocks)";
                    }


                }elseif ($nbre_total > $nbre_valide) {
                    $libelle = "Partiellement validé (Responsable des stocks)";
                }
            }elseif ($table === "livraisons") {

                // nombre d'article validé
                $valider_requisition = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                ->join('profils as p', 'p.id', '=', 'vr.profils_id')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->where('vr.flag_valide', 1)
                ->where('tp.name', 'Responsable des stocks')
                ->where('r.id',$requisitions_id)
                ->select(DB::raw('SUM(vr.qte) as qte_totale_validee'))
                ->groupBy('r.id')
                ->first();
                if ($valider_requisition!=null) {
                    $qte_totale_validee = $valider_requisition->qte_totale_validee;
                }else{
                    $qte_totale_validee = 0;
                }


                $livraison = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->join('livraisons as l','l.demandes_id','=','d.id')
                ->join('profils as p', 'p.id', '=', 'l.profils_id')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->where('l.statut', 1)
                ->whereIn('tp.name', ['Responsable des stocks','Gestionnaire des stocks'])
                ->where('r.id',$requisitions_id)
                ->select(DB::raw('SUM(l.qte) as qte_totale_livree'))
                ->groupBy('r.id')
                ->first();
                if ($livraison!=null) {
                    $qte_totale_livree = $livraison->qte_totale_livree;
                }else{
                    $qte_totale_livree = 0;
                }


                if ($qte_totale_validee > $qte_totale_livree) {
                    $libelle = "Livraison partielle";
                }elseif ($qte_totale_validee === $qte_totale_livree) {
                    $libelle = "Livraison totale";
                }

            }
        }

        return $libelle;

    }

    public function statut_requisition($request,$requisitions_id,$profils_id,$libelle){
        
        $type_statut_requisition = TypeStatutRequisition::where('libelle', $libelle)->first();
    
        if ($type_statut_requisition===null) {

            $type_statut_requisitions_id = TypeStatutRequisition::create([
                'libelle'=>$libelle
            ])->id;

        }else{

            $type_statut_requisitions_id = $type_statut_requisition->id;

        }



        StatutRequisition::where('requisitions_id',$requisitions_id)
        ->orderByDesc('id')
        ->limit(1)
        ->update([
            'date_fin'=>date('Y-m-d'),
        ]);


        $statut_requisition_store = StatutRequisition::create([
            'profils_id'=>$profils_id,
            'requisitions_id'=>$requisitions_id,
            'type_statut_requisitions_id'=>$type_statut_requisitions_id,
            'date_debut'=>date('Y-m-d'),
            'commentaire'=>$request->commentaire,
        ]);

        return $statut_requisition_store;
            
        

            
    }

    public function notification_responsable_stock($requisitions_id,$subject){

        $responsable_stock = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Responsable des stocks')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('r.id', $requisitions_id)
                ->get();
                foreach ($responsable_stock as $pilote) {
    
                    $details = [
                        'email' => $pilote->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }
            
                return $responsable_stock;
        
    }

    public function notification_demandeur_consolide2($requisitions_id_consolide,$subject){

        $demandes = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
        ->groupBy('requisitions_id')
        ->select(DB::raw('requisitions_id'))
        ->get();

        foreach ($demandes as $demande) {
            $requisitions_id = $demande->requisitions_id;

            //nombre total d'articles de la demande du demandeur

            if (isset($subject)) {

                $requisition = DB::table('requisitions as r')
                ->join('profils as p','p.id','=','r.profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->where('r.id',$requisitions_id)
                ->select('u.email')
                ->first();
                if ($requisition!=null) {
    
                    $details = [
                        'email' => $requisition->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }

            }

            
        }
        

        
    }

    public function notification_pilote2($requisitions_id,$subject){

               $pilotes = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Pilote AEE')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('r.id', $requisitions_id)
                ->get();
                foreach ($pilotes as $pilote) {
    
                    $details = [
                        'email' => $pilote->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }
            
        
    }

    public function notification_hierarchie_consolide2($requisitions_id,$responsable,$subject){

                if ($responsable === 1) {

                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n1')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n1'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
                }elseif ($responsable === 2) {
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n2')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n2'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
                    
                }elseif ($responsable === 3) {
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n1')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n1'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n2')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n2'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
                }

    }

    
    public function profil_aee($requisitions_id){

        $pilote_aee = DB::table('users as u')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->join('agent_sections as ase','ase.agents_id','=','a.id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('requisitions as r','r.code_structure','=','s.code_structure')
        ->join('structures as st','st.code_structure','=','r.code_structure')
        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
        ->where('tp.name','Pilote AEE')
        ->where('tsas.libelle','Activé')
        ->where('p.flag_actif',1)
        ->whereRaw('st.code_structure = r.code_structure')
        ->where('p.users_id', auth()->user()->id)
        ->where('r.id', $requisitions_id)
        ->first();
        

        return $pilote_aee;

    }
    public function valider_reception($request, $item, $profils_id){

        $livraison = null;
        if ($request->livraisons_id[$item]!=null) {

            $libelles = Livraison::where('id',$request->livraisons_id[$item])->first();
            if ($libelles!=null) {

                $qte_recue = $libelles->qte_recue + $request->qte[$item];

                $livraison = Livraison::where('id',$request->livraisons_id[$item])
                ->update([
                    'qte_recue'=>$qte_recue,
                    'date_reception'=>date('Y-m-d H:i:s'),
                    'commentaire'=>$request->commentaire,
                ]);

                $type_piece = "ENT_STOCK_RECU"; // ou ENT_STOCK_SORT
                
                $this->comptabilisationDistribution($request->livraisons_id[$item],$type_piece);

            }
        }

        return $livraison;
    }

    public function statut_livraison($requisitions_id){

        $requisition = DB::table('requisitions as r')
            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
            ->join('profils as p','p.id','=','sr.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
            ->where('r.id',$requisitions_id)
            ->whereIn('tsr.libelle',['Livraison partielle','Livraison totale'])
            ->orderByDesc('sr.id')
            ->limit(1)
            ->first();
        
        return $requisition;
    }

    public function control_saisie_distribution($demande_consolides_id,$qte_demandee,$qte_validee,$qte_livree,$submit){
        
        $error = null;

        if ($submit === "distribution") {
            
            $qte_recue = 0;
            $qte_validee = 0;
            $distribution = Distribution::where('demande_consolides_id',$demande_consolides_id)
            ->groupBy('demande_consolides_id')
            ->where('flag_reception', '!=' , 2)
            ->select(DB::raw('SUM(qte) as qte_recue'))
            ->first();

            if ($distribution!=null) {
                $qte_recue = $distribution->qte_recue;
            }

            $demande_consolide = DemandeConsolide::where('id',$demande_consolides_id)->first();
            if ($demande_consolide!=null) {

                $qte_validee = $demande_consolide->qte;

            }else{
                $error = "Demande introuvable";
            }

            $qte_potentielle = $qte_recue + $qte_livree;

            if ( $qte_potentielle > $qte_validee) {
                $error = "La quantité livrée ne peut être supérieure à la quantité validée";
            }

            if ( $qte_demandee < $qte_validee) {
                $error = "La quantité validée ne peut être supérieure à la quantité demandée";
            }

        }elseif ($submit === "consommation" or $submit === "annuler_consommation") {
            $qte_recue = 0;
            $qte_consomme = 0;

            $distribution = Distribution::where('demande_consolides_id',$demande_consolides_id)
            ->groupBy('demande_consolides_id')
            ->where('flag_reception', '!=' , 2)
            ->select(DB::raw('SUM(qte) as qte_recue'))
            ->first();

            if ($distribution!=null) {
                $qte_recue = $distribution->qte_recue;
            }
            
            $consommation = DB::table('distributions as d')
                ->join('consommations as c','c.distributions_id','=','d.id')
                ->where('d.demande_consolides_id',$demande_consolides_id)
                ->groupBy('d.demande_consolides_id')
                ->where('d.flag_reception', '!=' , 2)
                ->select(DB::raw('SUM(c.qte) as qte_consomme'))
                ->first();
            if ($consommation!=null) {
                $qte_consomme = $consommation->qte_consomme;
            }

            $qte_potentielle = $qte_consomme + $qte_livree;

            //dd('$qte_recue',$qte_recue,'$qte_potentielle',$qte_potentielle,'$qte_consomme',$qte_consomme,'$qte_livree',$qte_livree);

            if ( $qte_potentielle > $qte_recue) {
                $error = "La quantité reçue ne peut être supérieure à la quantité livrée";
            }
        }

        return $error;
    }

    public function store_distribution($demande_consolides_id,$qte,$profils_id,$demandes_id,$commentaire){


        //compter nombre d'articles deja recu
        $qte_recue = 0;
        $qte_validee = 0;
        $distributions_id = null;
        $distribution = Distribution::where('demande_consolides_id',$demande_consolides_id)
        ->groupBy('demande_consolides_id')
        ->where('flag_reception', '!=' , 2)
        ->select(DB::raw('SUM(qte) as qte_recue'))
        ->first();

        if ($distribution!=null) {
            $qte_recue = $distribution->qte_recue;
        }

        $qte_potentielle = $qte_recue + $qte;
        // quantité demandé

        $demande_consolide = DemandeConsolide::where('id',$demande_consolides_id)->first();
        if ($demande_consolide!=null) {

            $qte_validee = $demande_consolide->qte;

        }

        if ($qte_potentielle > $qte_validee) {
            
        }else{

            //recuperer le prixu et le montant
            $prix_unit = 0;

            $mouvement = DB::table('demande_consolides as dc')
            ->join('demandes as d','d.id','=','dc.demandes_ids')
            ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
            ->join('livraisons as l','l.demandes_id','=','vr.demandes_id')
            ->join('mouvements as m','m.id','=','l.mouvements_id')
            ->where('dc.id',$demande_consolides_id)
            ->select('m.prix_unit','l.id')
            ->first();

            if ($mouvement!=null) {
                $prix_unit = $mouvement->prix_unit;
                $livraisons_id = $mouvement->id;

                $montant = $prix_unit * $qte;

                $distribution_controle = Distribution::where('demande_consolides_id',$demande_consolides_id)
                ->where('livraisons_id',$livraisons_id)
                ->where('flag_reception',2)
                ->first();
                if($distribution_controle != null){

                    $distributions_id = $distribution_controle->id;

                    Distribution::where('id',$distribution_controle->id)
                    ->update([
                        'demande_consolides_id'=>$demande_consolides_id,
                        'livraisons_id'=>$livraisons_id,
                        'qte'=>$qte,
                        'prixu'=>$prix_unit,
                        'montant'=>$montant,
                        'profils_id'=>$profils_id,
                        'commentaire'=>$commentaire,
                        'qte_recue'=>0,
                        'flag_reception'=>0,
                        'date_reception'=>null,
                    ]);

                    Consommation::where('distributions_id',$distributions_id)->delete();

                }else{
                    $distributions_id = Distribution::create([
                        'demande_consolides_id'=>$demande_consolides_id,
                        'livraisons_id'=>$livraisons_id,
                        'qte'=>$qte,
                        'prixu'=>$prix_unit,
                        'montant'=>$montant,
                        'profils_id'=>$profils_id,
                        'commentaire'=>$commentaire,
                    ])->id;
                }

                if(isset($distributions_id)){

                    $requisition = DB::table('distributions as di')
                    ->join('demande_consolides as dc','dc.id','=','di.demande_consolides_id')
                    ->join('livraisons as l','l.id','=','di.livraisons_id')
                    ->join('demandes as d','d.id','=','dc.demandes_id')
                    ->join('requisitions as r','r.id','=','d.requisitions_id')
                    ->where('dc.id',$demande_consolides_id)
                    ->where('l.id',$livraisons_id)
                    ->where('d.id',$demandes_id)
                    ->where('r.type_beneficiaire','Structure')
                    ->first();

                    if($requisition != null){ 

                        $flag_reception = 1;

                        $response = $this->store_consommation($demande_consolides_id,$qte,$profils_id,$commentaire,$distributions_id,$demandes_id,$flag_reception);

                        if(isset($flag_reception)){

                            if($flag_reception === 1){

                                if($response != null){

                                    $type_piece = "DE_STOCK_CONSO";
                                    $this->comptabilisationDistributionConsommation($response,$type_piece);

                                }

                            }

                        }
                    }
                }
                
                
                
                
            }

            




        }

        


        return $distributions_id;

    }

    public function statut_distribution($requisitions_id){
        $distribution = DB::table('requisitions as r')
            ->join('demandes as d','d.requisitions_id','=','r.id')
            ->join('demande_consolides as dc','dc.demandes_ids','=','d.id')
            ->join('distributions as ds','ds.demande_consolides_id','=','dc.id')
            ->join('profils as p','p.id','=','ds.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->orderByDesc('ds.id')
            ->select('ds.commentaire','name','nom_prenoms')
            ->where('r.id',$requisitions_id)
            ->first();
        return $distribution;
    }

    public function libelle_statut_distribution($request,$requisitions_id,$profils_id){
        //quantité validée

        $libelle = null;
        $demandes = DB::table('requisitions as r')
        ->join('demandes as d','d.requisitions_id_consolide','=','r.id')
        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
        ->where('r.id',$requisitions_id)
        ->select('d.requisitions_id','r.id as requisitions_id_consolide',DB::raw('SUM(dc.qte) as qte_total_validee'))
        ->groupBy('d.requisitions_id','r.id')
        ->get();

        foreach ($demandes as $demande) {

            if (isset($qte_total_validee)) {
                unset($qte_total_validee);
            }

            $qte_total_validee = $demande->qte_total_validee;

            if (isset($qte_total_recue)) {
                unset($qte_total_recue);
            }
            $distribution = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id_consolide','=','r.id')
                ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                ->join('distributions as di','di.demande_consolides_id','=','dc.id')
                ->where('r.id',$requisitions_id)
                ->select('d.requisitions_id','r.id as requisitions_id_consolide',DB::raw('SUM(dc.qte) as qte_total_recue'))
                ->groupBy('d.requisitions_id','r.id')
                ->where('d.requisitions_id',$demande->requisitions_id)
                ->first();

                if ($distribution!=null) {
                    $qte_total_recue = $distribution->qte_total_recue;
                }else{
                    $qte_total_recue = 0;
                }

                if ($qte_total_recue!=0) {

                    if ($qte_total_validee > $qte_total_recue) {
                        $libelle = "Livraison partielle";
                    }elseif ($qte_total_validee === $qte_total_recue) {
                        $libelle = "Livraison totale";
                    }

                }

                if (isset($libelle)) {
                $this->statut_requisition($request,$demande->requisitions_id,$profils_id,$libelle);
                }
                
                
        }
        
    }

    public function libelle_statut_consommation($request,$requisitions_id,$profils_id){
        //quantité validée

        $libelle = null;
        $demandes = DB::table('requisitions as r')
        ->join('demandes as d','d.requisitions_id','=','r.id')
        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
        ->join('distributions as di','di.demande_consolides_id','=','dc.id')
        ->where('r.id',$requisitions_id)
        ->select('d.requisitions_id','r.id as requisitions_id_consolide',DB::raw('SUM(di.qte) as qte_total_distribuee'))
        ->groupBy('d.requisitions_id','r.id')
        ->get();

        foreach ($demandes as $demande) {

            if (isset($qte_total_distribuee)) {
                unset($qte_total_distribuee);
            }

            $qte_total_distribuee = $demande->qte_total_distribuee;

            if (isset($qte_total_consommee)) {
                unset($qte_total_consommee);
            }
            $distribution = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                ->join('distributions as di','di.demande_consolides_id','=','dc.id')
                ->join('consommations as c','c.distributions_id','=','di.id')
                ->where('r.id',$requisitions_id)
                ->select('d.requisitions_id','r.id as requisitions_id_consolide',DB::raw('SUM(c.qte) as qte_total_consommee'))
                ->groupBy('d.requisitions_id','r.id')
                ->where('d.requisitions_id',$demande->requisitions_id)
                ->first();

                if ($distribution!=null) {
                    $qte_total_consommee = $distribution->qte_total_consommee;
                }else{
                    $qte_total_consommee = 0;
                }

                if ($qte_total_consommee!=0) {

                    if ($qte_total_distribuee > $qte_total_consommee) {
                        $libelle = "Réception partielle";
                    }elseif ($qte_total_distribuee === $qte_total_consommee) {
                        $libelle = "Réception totale";
                    }

                }

                if (isset($libelle)) {
                $this->statut_requisition($request,$demande->requisitions_id,$profils_id,$libelle);
                }
                
                
        }
        
    }
    
    public function store_consommation($demande_consolides_id,$qte,$profils_id,$commentaire,$distributions_id,$demandes_id,$flag_reception){
        
        $response = null;

        $mouvement = DB::table('demande_consolides as dc')
        ->join('demandes as d','d.id','=','dc.demandes_ids')
        ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
        ->join('livraisons as l','l.demandes_id','=','vr.demandes_id')
        ->join('mouvements as m','m.id','=','l.mouvements_id')
        ->where('dc.id',$demande_consolides_id)
        ->select('m.prix_unit')
        ->first();

        if ($mouvement!=null) {

            $prix_unit = $mouvement->prix_unit;

            $montant = $prix_unit * $qte;

            $qte_recue = $qte;

            if($flag_reception === 1){
                
                
                $consommations_id = Consommation::create([
                        'distributions_id'=>$distributions_id,
                        'demandes_id'=>$demandes_id,
                        'qte'=>$qte,
                        'prixu'=>$prix_unit,
                        'montant'=>$montant,
                        'profils_id'=>$profils_id,
                        'commentaire'=>$commentaire,
                    ])->id;

                $response = $consommations_id;

                //store Consommation Distibution
                $distribution = Distribution::where('id',$distributions_id)->first();

                if($distribution != null) {
                    $this->storeConsommationDistribution($consommations_id, $distribution->livraisons_id, $qte);
                }
                
            }elseif($flag_reception === 2){
                $qte_recue = 0;

                $response = 1;
            }
            

            $distribution = Distribution::where('id',$distributions_id)->first();

            if($distribution != null){
                
                Distribution::where('id',$distribution->id)
                ->update([
                    'qte_recue'=>$qte_recue,
                    'flag_reception'=>$flag_reception,
                    'date_reception'=>date('Y-m-d H:i:s'),
                ]);

            }
        }

        return $response;

    }

    public function getTypeProfilName($profils_id){

        $type_profils_name = null;

        $type_profil = DB::table('type_profils as tp')
        ->join('profils as p', 'p.type_profils_id', '=', 'tp.id')
        ->where('p.id',$profils_id)
        ->first();

        if ($type_profil!=null) {
            $type_profils_name = $type_profil->name;
        }

        return $type_profils_name;
    }

    public function controlAcces($type_profils_name,$etape,$users_id,$requisitions_id=null){

        $profils = null;
        if ($etape === "create" or $etape === "store") {

            if ($type_profils_name === 'Agent Cnps') {

                $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Agent Cnps')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('p.id',function($query) use($requisitions_id){
                    $query->select(DB::raw('r.profils_id'))
                            ->from('requisitions as r')
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('r.profils_id = p.id');
                })
                ->first();

            }elseif ($type_profils_name === 'Pilote AEE'){
                $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Pilote AEE')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.code_structure',function($query) use($requisitions_id){
                    $query->select(DB::raw('r.code_structure'))
                            ->from('requisitions as r')
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('r.code_structure = s.code_structure');
                })
                ->first();
            }elseif ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks'){

                $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
                if ($infoUserConnect != null) {

                    $ref_depot = $infoUserConnect->ref_depot;

                    if($ref_depot === 83) {
                        $profils = DB::table('profils as p')
                        ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                        ->join('users as u', 'u.id', '=', 'p.users_id')
                        ->join('agents as a', 'a.id', '=', 'u.agents_id')
                        ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                        ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                        ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                        ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                        ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                        ->where('p.users_id', auth()->user()->id)
                        ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                        ->limit(1)
                        ->select('p.id', 'se.code_section', 's.ref_depot')
                        ->where('p.flag_actif',1)
                        ->where('p.id',Session::get('profils_id'))
                        ->where('tsase.libelle','Activé')
                        ->whereIn('s.ref_depot',function($query) use($requisitions_id){
                            $query->select(DB::raw('dp.ref_depot'))
                                    ->from('requisitions as r')
                                    ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                    ->join('demandes as d', 'd.requisitions_id', '=', 'r.id')
                                    ->join('magasin_stocks as ms', 'ms.id', '=', 'd.magasin_stocks_id')
                                    ->join('magasins as m', 'm.ref_magasin', '=', 'ms.ref_magasin')
                                    ->join('depots as dp', 'dp.id', '=', 'm.depots_id')
                                    ->where('r.id',$requisitions_id)
                                    ->whereRaw('dp.ref_depot = s.ref_depot');
                        })
                        ->first();
                    }else{
                        $profils = DB::table('profils as p')
                        ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                        ->join('users as u', 'u.id', '=', 'p.users_id')
                        ->join('agents as a', 'a.id', '=', 'u.agents_id')
                        ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                        ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                        ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                        ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                        ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                        ->where('p.users_id', auth()->user()->id)
                        ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                        ->limit(1)
                        ->select('p.id', 'se.code_section', 's.ref_depot')
                        ->where('p.flag_actif',1)
                        ->where('p.id',Session::get('profils_id'))
                        ->where('tsase.libelle','Activé')
                        ->whereIn('s.ref_depot',function($query) use($requisitions_id){
                            $query->select(DB::raw('st.ref_depot'))
                                    ->from('requisitions as r')
                                    ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                    ->where('r.id',$requisitions_id)
                                    ->whereRaw('st.ref_depot = s.ref_depot');
                        })
                        ->first();
                    }
                }
                
            }          
            
            
        }elseif ($etape === "distribution" or $etape === "distributions_store") {

            if ($type_profils_name === 'Pilote AEE'){
                $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Pilote AEE')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.code_structure',function($query) use($requisitions_id){
                    $query->select(DB::raw('r.code_structure'))
                            ->from('requisitions as r')
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('r.code_structure = s.code_structure');
                })
                ->first();
            }        
            
            
        }elseif ($etape === "reception" or $etape === "receptions_store") {

            if ($type_profils_name === 'Agent Cnps'){
                $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Agent Cnps')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('p.id',function($query) use($requisitions_id){
                    $query->select(DB::raw('r.profils_id'))
                            ->from('requisitions as r')
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('r.profils_id = p.id');
                })
                ->first();
            }        
            
            
        }

        return $profils;
    }

    


}
