<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\SendEmail;
use App\Models\Demande;
use App\Models\Hierarchie;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\StatutRequisition;
use Illuminate\Support\Facades\DB;
use App\Models\TypeStatutRequisition;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class RequisitionController extends Controller
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
    public function index(Request $request)
    {
        
        $sans_hierarchie = 1;
        $this->storeSessionBackground($request);

        $type_profils_name = null;

        if (Session::has('profils_id')) {
            
            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Agent Cnps','Responsable N+1','Responsable N+2','Pilote AEE','Responsable des stocks','Gestionnaire des stocks','Administrateur fonctionnel'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
                $ref_depot = $infoUserConnect->ref_depot;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }


        

        $affiche_demandeur = 1;
        $requisitions = [];
        $requisitions_all = [];

        $acces_create = null;

        

            if ($type_profils_name === "Agent Cnps") {

                $affiche_demandeur = null;

                $libelle = "Soumis pour validation";
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name);

                $sans_hierarchie = Hierarchie::where('agents_id',auth()->user()->agents_id)
                ->where('flag_actif',1)
                ->first();

                $acces_create = 1;


            }elseif ($type_profils_name === "Responsable N+1") {
                $libelle = "Transmis (Responsable N+1)";
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable N+2") {
                $libelle = "Transmis (Responsable N+2)";
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Pilote AEE") {
                $libelle = "Transmis (Pilote AEE)"; 
                $libelle2 = "Consolidée (Pilote AEE)";
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name,$code_structure,$libelle2);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name,$code_structure,$libelle2);
                $acces_create = 1;
            }elseif ($type_profils_name === "Responsable des stocks") {

                $libelle = "Transmis (Responsable des stocks)";
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name,$code_structure);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name,$code_structure);

                $acces_create = 1;
            }elseif ($type_profils_name === "Gestionnaire des stocks") {
                //$libelle = "Soumis pour livraison";
                $libelle = "Transmis (Responsable des stocks)";
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name,$code_structure);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name,$code_structure);
                $acces_create = 1;
            }elseif ($type_profils_name === "Administrateur fonctionnel") {

                $libelle = ['Soumis pour validation','Transmis (Responsable des stocks)'];
                $requisitions = $this->RequisitionDataCentDernier($libelle,$type_profils_name,$code_structure);
                $requisitions_all = $this->RequisitionData($libelle,$type_profils_name,$code_structure);

            }else{
                return redirect()->back()->with('error','Accès refusé');
            }
            $titre = "LISTE DES DEMANDES D'ARTICLES";
            if(count($requisitions_all)>100){
                $titre = "LISTE DES 100 dernières DEMANDES D'ARTICLES";
            }
            

        return view('requisitions.index',[
            'affiche_demandeur'=>$affiche_demandeur,
            'requisitions'=>$requisitions,
            'type_profils_name'=>$type_profils_name,
            'sans_hierarchie'=>$sans_hierarchie,
            'acces_create'=>$acces_create,
            'titre'=>$titre,
            'requisitions_all'=>$requisitions_all
        ]);
    }
    public function index_all(Request $request)
    {
        
        $sans_hierarchie = 1;
        $this->storeSessionBackground($request);

        $type_profils_name = null;

        if (Session::has('profils_id')) {
            
            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Agent Cnps','Responsable N+1','Responsable N+2','Pilote AEE','Responsable des stocks','Gestionnaire des stocks','Administrateur fonctionnel'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
                $ref_depot = $infoUserConnect->ref_depot;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }


        

        $affiche_demandeur = 1;
        $requisitions = [];
        $requisitions_all = [];

        $acces_create = null;

        

            if ($type_profils_name === "Agent Cnps") {

                $affiche_demandeur = null;

                $libelle = "Soumis pour validation";
                $requisitions = $this->RequisitionData($libelle,$type_profils_name);

                $sans_hierarchie = Hierarchie::where('agents_id',auth()->user()->agents_id)
                ->where('flag_actif',1)
                ->first();

                $acces_create = 1;


            }elseif ($type_profils_name === "Responsable N+1") {
                $libelle = "Transmis (Responsable N+1)";
                $requisitions = $this->RequisitionData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable N+2") {
                $libelle = "Transmis (Responsable N+2)";
                $requisitions = $this->RequisitionData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Pilote AEE") {
                $libelle = "Transmis (Pilote AEE)"; 
                $libelle2 = "Consolidée (Pilote AEE)";
                $requisitions = $this->RequisitionData($libelle,$type_profils_name,$code_structure,$libelle2);
                $acces_create = 1;
            }elseif ($type_profils_name === "Responsable des stocks") {

                $libelle = "Transmis (Responsable des stocks)";
                $requisitions = $this->RequisitionData($libelle,$type_profils_name,$code_structure);

                $acces_create = 1;
            }elseif ($type_profils_name === "Gestionnaire des stocks") {
                //$libelle = "Soumis pour livraison";
                $libelle = "Transmis (Responsable des stocks)";
                $requisitions = $this->RequisitionData($libelle,$type_profils_name,$code_structure);
                $acces_create = 1;
            }elseif ($type_profils_name === "Administrateur fonctionnel") {

                $libelle = ['Soumis pour validation','Transmis (Responsable des stocks)'];
                $requisitions = $this->RequisitionData($libelle,$type_profils_name,$code_structure);

            }else{
                return redirect()->back()->with('error','Accès refusé');
            }
    
        
            $titre = "LISTE DES DEMANDES D'ARTICLES";
        return view('requisitions.index',[
            'affiche_demandeur'=>$affiche_demandeur,
            'requisitions'=>$requisitions,
            'type_profils_name'=>$type_profils_name,
            'sans_hierarchie'=>$sans_hierarchie,
            'acces_create'=>$acces_create,
            'titre'=>$titre,
            'requisitions_all'=>$requisitions_all
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $structures = [];
        $sections = [];
        $ref_depot = null;
        
        if (Session::has('profils_id')) {

            $etape = "create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Agent Cnps','Pilote AEE','Responsable des stocks','Gestionnaire des stocks'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $structures = $this->getStructuresByRefDepot($infoUserConnect->ref_depot,null,$type_profils_name);
                $departements = $this->getDepartements();
                $ref_depot = $infoUserConnect->ref_depot;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        

        $articles = $this->getArticleMagasinStock();
                      
        $gestions = $this->getGestion();

        $gestion_defaults = null;

        $intitule_default = "Demande d'articles";

        $gestion_default = $this->getGestionDefault();

        if ($gestion_default!=null) {
            $gestion_defaults = $gestion_default->code_gestion.' - '.$gestion_default->libelle_gestion;
        }

        $exercice = $this->getExercice();     
        
        

        return view('requisitions.create',[
            'articles' => $articles,
            'gestions' => $gestions,
            'gestion_defaults'=>$gestion_defaults,
            'intitule_default'=>$intitule_default,
            'exercice'=>$exercice,
            'structures'=>$structures,
            'type_profils_name'=>$type_profils_name,
            'departements'=>$departements,
            'ref_depot'=>$ref_depot
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
            'intitule' => ['required','string'],
            'gestion' => ['required','string'],
            'ref_articles' => ['required','array'],
            'magasin_stocks_id' => ['required','array'],
            'design_article' => ['required','array'],
            'cmup' => ['required','array'],
            'qte' => ['required','array'],
            'montant' => ['required','array'],
            'commentaire' => ['nullable','string']
        ]);
        

        $departements_id = null;

        if (Session::has('profils_id')) {
            
            $etape = "store";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Agent Cnps','Pilote AEE','Responsable des stocks','Gestionnaire des stocks'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if($profil!=null){
                $profils_id = $profil->id;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            if($type_profils_name === "Responsable des stocks" or $type_profils_name === "Gestionnaire des stocks"){

                $request->validate([
                    'departement_demande' => ['nullable','string'],
                    'structure_demande' => ['required','string'],
                    'mle_pilote_demande' => ['required','string'],
                    'date_demande' => ['required','date']
                ]);

                if(isset($request->departement_demande)){

                    $this->storeDepartement($request->departement_demande);

                    $departement = $this->getDepartement($request->departement_demande);

                    if($departement != null){
                        $departements_id = $departement->id;
                    }
                }
            }

            if($type_profils_name === "Responsable des stocks" or $type_profils_name === "Gestionnaire des stocks"){

                $structure = $this->getStructureByName($request->structure_demande);
                if($structure != null){
                    $code_structure = $structure->code_structure;
                    $ref_depot = $structure->ref_depot;
                }else{
                    return redirect()->back()->with('error', 'Structure introuvable');
                }

                $agent = $this->getAgent3($request->mle_pilote_demande);
                if($agent != null){

                    $type_profils_name_pilote = "Pilote AEE";

                    $agent_pilote = $this->getAgentByMle($agent->mle,$type_profils_name_pilote);

                    if($agent_pilote != null){
                        $profils_id = $agent_pilote->profils_id;
                    }else{
                        $type_profil = $this->getTypeProfilByName($type_profils_name_pilote);

                        if ($type_profil!=null) {
                            $type_profils_id = $type_profil->id;
                        }else{
                            $this->storeTypeProfilByName($type_profils_name_pilote);
        
                            $type_profil = $this->getTypeProfilByName($type_profils_name_pilote);
        
                            if ($type_profil!=null) {
                                $type_profils_id = $type_profil->id;
                            }
                        }
                        
        
                        $prof = $this->storeProfil($agent->users_id,$type_profils_id);
                        if($prof != null){
                            $profils_id = $prof->id;
                        }
                    }

                }else{
                    return redirect()->back()->with('error', 'Agent introuvable');
                }

                

            }else{
                $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
                if ($infoUserConnect != null) {
                    $code_structure = $infoUserConnect->code_structure;
                    $ref_depot = $infoUserConnect->ref_depot;
                }
            }

            

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        if (isset($code_structure)) {
            
        }else{
            return redirect()->back()->with('error','Structure introuvable');
        }

        if (isset($ref_depot)) {
            
        }else{
            return redirect()->back()->with('error','Depot introuvable');
        }

        

        $exercice = date("Y");

        $exercices = $this->getExercice();
        if($exercices != null){
            $exercice = $exercices->exercice;
        }

        $num_bc = $this->getLastNumBci($exercice, $code_structure, $ref_depot);
        

        $intitule = $request->intitule;
        $gestion = explode(' - ',$request->gestion);
        $code_gestion = $gestion[0];
        
        $error = $this->controleSaisieRequisition($etape,$request);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        if ($type_profils_name === 'Pilote AEE' or $type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {

            $type_beneficiaire = "Structure";

        }else{

            $type_beneficiaire = null;

        }
        
        $requisition = $this->storeRequisition($code_structure,$num_bc,$exercice,$intitule,$code_gestion,$profils_id,$type_beneficiaire);

        $requisitions_id = $requisition->id;

        if($departements_id != null){
            $this->setRequisitionDepartement($requisitions_id,$departements_id);
        }

        $error = $this->storeDetailDemande($etape,$request,$requisitions_id,$profils_id);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $libelle = 'Soumis pour validation';

        if ($type_profils_name === 'Pilote AEE') {

            $libelle = "Transmis (Pilote AEE)";
            
        }elseif ($type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {

            $this->setDetailDemandesByIdRequisition($requisitions_id,$request->date_demande);

            $libelle = "Transmis (Responsable des stocks)";
            
        }

        $this->storeTypeStatutRequisition($libelle);

        $statut_requisition = null;

        $type_statut_requisition = $this->getTypeStatutRequisition($libelle);

        if ($type_statut_requisition != null) {
            $type_statut_requisitions_id = $type_statut_requisition->id;

            $this->setLastStatutRequisition($requisitions_id);

           $statut_requisition = $this->storeStatutRequisition($requisitions_id, Session::get('profils_id'), $type_statut_requisitions_id, $request->commentaire);
        }


        if ($statut_requisition != null) {


            if ($type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {
                Requisition::where('id',$requisitions_id)->update([
                    'created_at' => $request->date_demande,
                    'updated_at' => $request->date_demande,
                ]);

                Demande::where('requisitions_id',$requisitions_id)->update([
                    'created_at' => $request->date_demande,
                    'updated_at' => $request->date_demande,
                ]);

                StatutRequisition::where('requisitions_id',$requisitions_id)->update([
                    'created_at' => $request->date_demande,
                    'updated_at' => $request->date_demande,
                ]);
            }
            
            $subject = "Enregistrement de demande d'articles";

            $this->notifRequisition(auth()->user()->email,$subject,$requisitions_id);


            return redirect('/requisitions/index')->with('success','Enregistrement reussi');
        }else{
            return redirect()->back()->with('error','Enregistrement échoué');
        }
        



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function show($requisition)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisition);
        } catch (DecryptException $e) {
            //
        }

        $requisition = Requisition::findOrFail($decrypted);

        if (Session::has('profils_id')) {

            
            
            $etape = "show";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$requisition->id);
            
            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        
        $requisitions = DB::table('requisitions as r')
                            ->join('gestions as g','g.code_gestion','=','r.code_gestion')
                            ->join('structures as st','st.code_structure','=','r.code_structure')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id', '=', 'p.users_id')
                            ->join('agents as a','a.id', '=', 'u.agents_id')
                            ->where('r.id',$requisition->id)
                            ->select('r.*','g.code_gestion','g.libelle_gestion','a.nom_prenoms','a.mle','st.nom_structure')
                            ->first();

        $magasins = DB::table('requisitions')
                        ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                        ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                        ->join('magasins','magasins.ref_magasin','=','magasin_stocks.ref_magasin')
                        ->join('depots','depots.id','=','magasins.depots_id')
                        ->where('requisitions.id',$requisition->id)
                        ->first(); 
               

        $demandes = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                            ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->where('requisitions.id',$requisition->id)
                            ->select('demandes.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte','magasin_stocks.cmup','requisitions.id as requisitions_id','requisitions.flag_consolide')
                            ->get();

        // nature de la requisition
        $requisition_validee = null;
        $requisition_livree = null;
        
        if (isset($requisition->flag_consolide)) {
                
                $requisition_validee = DB::table('requisitions as r')
                            ->join('demandes as d','d.requisitions_id','=','r.id')
                            ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                            ->join('profils as p', 'p.id', '=', 'vr.profils_id')
                            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                            ->where('tp.name', 'Responsable des stocks')
                            ->where('vr.flag_valide',1)
                            ->where('r.id',$requisition->id)
                            ->first();
            
                if($requisition_validee === null){

                    $requisition_validee = DB::table('requisitions as r')
                    ->join('demandes as d','d.requisitions_id','=','r.id')
                    ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                    ->select('dc.qte as qte_validee')
                    ->where('r.id',$requisition->id)
                    ->first();

                }

                $requisition_livree = DB::table('requisitions as r')
                            ->join('demandes as d','d.requisitions_id','=','r.id')
                            ->join('livraisons as l','l.demandes_id','=','d.id')
                            ->where('r.id',$requisition->id)
                            ->first();
                if ($requisition_livree === null) {

                    $requisition_livree = DB::table('requisitions as r')
                        ->join('demandes as d','d.requisitions_id','=','r.id')
                        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                        ->join('distributions as di','di.demande_consolides_id','=','dc.id')
                        // ->join('consommations as c','c.distributions_id','=','di.id')
                        ->where('r.id',$requisition->id)
                        ->first();

                }
            
        }
        

        

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
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
            $libelle = $statut_requisition->libelle;
            $commentaire = $statut_requisition->commentaire;
            $profil_commentaire = $statut_requisition->name;
            $nom_prenoms_commentaire = $statut_requisition->nom_prenoms;

        }
                   
        return view('requisitions.show',[
            'demandes' => $demandes,
            'requisitions' => $requisitions,
            'magasins' => $magasins,
            'libelle' => $libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'requisition_validee'=>$requisition_validee,
            'requisition_livree'=>$requisition_livree,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function edit($requisition, Request $request)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisition);
        } catch (DecryptException $e) {
            //
        }

        $requisition = Requisition::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            $etape = "edit";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$requisition->id);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }



        $profil_agent = 1;

        $requisitions_id = $requisition->id;

        $requisitions = $this->getRequisitions($requisitions_id);
        
        $demandes = $this->getDemandes($requisitions_id);
        
        $articles = $this->getArticles($profil->ref_depot);        
                          
        $gestions = DB::table('gestions')->get();

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        
        $statut_requisition = $this->getStatutRequisition($requisitions_id);
        
        if ($statut_requisition!=null) {
            $libelle = $statut_requisition->libelle;
            $commentaire = $statut_requisition->commentaire;
            $profil_commentaire = $statut_requisition->name;
            $nom_prenoms_commentaire = $statut_requisition->nom_prenoms;

        }

        $hierarchie = $this->hierarchie(auth()->user()->id);

        $entete = 'Demande d\'articles';
        $value_bouton2 = null;
        $bouton2 = null;
        $value_bouton = null;
        $bouton = null;
        $vue = 1;


        $url_complet = $request->url();
        
        $tableau = explode('requisitions/',$url_complet);

        $url1 = $tableau[0];
        $url2 = $tableau[1];

        $tableau2 = explode('/',$url2);

        $page_view = $tableau2[0];

        if ($page_view === 'send') {
            if ($libelle === "Soumis pour validation" or $libelle === "Annulé (Agent Cnps)" or $libelle === "Annulé (Responsable N+1)" or $libelle === "Annulé (Pilote AEE)") {

                $vue = null;

                if ($hierarchie!=null) {
                    $entete = "Transmission la demande au RESPONSABLE DIRECT";
        
                    $value_bouton2 = "annuler_agent";
                    $bouton2 = "Annuler";
        
                    $value_bouton = "transfert_respo_direct";
                    $bouton = "Transférer";
        
                }else{
                    $entete = "Transmission la demande au pilote AEE";
        
                    $value_bouton2 = "annuler_agent";
                    $bouton2 = "Annuler";
        
                    $value_bouton = "transfert_pilote";
                    $bouton = "Transférer";
        
                }

            }
        }elseif ($page_view === 'edit') {
            if ($libelle === "Soumis pour validation" or $libelle === "Annulé (Agent Cnps)" or $libelle === "Annulé (Responsable N+1)" or $libelle === "Annulé (Pilote AEE)") {

                $vue = null;

                $entete = 'Modification de la demande d\'articles';


                if ($libelle === "Soumis pour validation" or $libelle === "Annulé (Responsable N+1)" or $libelle === "Annulé (Pilote AEE)") {
                    
                    $value_bouton2 = "annuler_agent";
                    $bouton2 = "Annuler";

                    $value_bouton = "modifier";
                    $bouton = "Modifier";

                }elseif ($libelle === "Annulé (Agent Cnps)" or $libelle === "Annulé (Pilote AEE)") {

                    $value_bouton = "modifier";
                    $bouton = "Modifier";

                }   
            }
        }        

                   
        return view('requisitions.edit',[
            'demandes' => $demandes,
            'requisitions' => $requisitions,
            'articles' => $articles,
            'gestions' => $gestions,
            'profil_agent' => $profil_agent,
            'libelle' => $libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'entete'=>$entete,
            'value_bouton2'=>$value_bouton2,
            'value_bouton'=>$value_bouton,
            'bouton2'=>$bouton2,
            'bouton'=>$bouton,
            'vue'=>$vue

        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Requisition $requisition)
    {

        $validate = $request->validate([
            'requisitions_id'=>['required','numeric']
        ]);
        if (Session::has('profils_id')) {
            $etape = "update";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$request->requisitions_id);


            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }else{
                $profils_id = $profil->id;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        

        

        $code_structure = $this->structure_connectee();

        if (isset($code_structure)) {
            
        }else{
            return redirect()->back()->with('error','Structure introuvable');
        }



        

        $exercice = date("Y");

        $intitule = $request->intitule;
        $gestion = explode(' - ',$request->gestion);
        $code_gestion = $gestion[0];

        $error = $this->controle_saisie($etape,$request);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        

        $requisitions_id = $request->requisitions_id;

        Requisition::where('id',$requisitions_id)->update([
            'code_structure' => $code_structure,
            'exercice' => $exercice,
            'intitule' => $intitule,
            'code_gestion' => $code_gestion,
            'profils_id' => $profils_id,
        ]);

        $error = $this->storeDetailRequisition($etape,$request,$requisitions_id,$profils_id);

        
        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $subject = "";

        if (isset($request->submit)) {
            if ($request->submit === "modifier") {
                $libelle = 'Soumis pour validation';
                $subject = "Modification de demande d'articles";
            }elseif ($request->submit === "annuler_agent") {
                $libelle = 'Annulé (Agent Cnps)';
                $subject = "Annulation de demande d'articles";
            }elseif ($request->submit === "transfert_respo_direct") {
                $libelle = 'Transmis (Responsable N+1)';
                $subject = "Transmission de demande d'articles au RESPONSABLE DIRECT";
            }elseif ($request->submit === "transfert_pilote") {
                $libelle = 'Transmis (Pilote AEE)';
                $subject = "Transmission de demande d'articles au Pilote AEE";
            }
        }
        

        $this->statut_requisition($etape,$request,$requisitions_id,$profils_id,$libelle);


        $this->notification_demandeur($requisitions_id,$subject);

        if ($request->submit === "transfert_respo_direct") {
            $responsable = 1;
            $this->notification_hierarchie($requisitions_id,$responsable,$subject);

        }


        return redirect('/requisitions/index')->with('success',$subject);



    }    
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Requisition  $requisition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Requisition $requisition)
    {

    }

    public function consommation($famille_crypt=null,$structure_crypt=null,$article_crypt=null,$periode_debut_crypt=null,$periode_fin_crypt=null){

        $titre = null;
        $consommations = []; 

        $famille = null;

        if($famille_crypt != null){

            $decrypted_famille = null;

            try {
                $decrypted_famille = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $famille = $this->getFamilleByRefFam($decrypted_famille);

        }

        $structure = null;

        if($structure_crypt != null){

            $decrypted_structure = null;
            
            try {
                $decrypted_structure = Crypt::decryptString($structure_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $structure = $this->getStructureByCode($decrypted_structure);

        }

        $article = null;

        if($article_crypt != null){

            $decrypted_article = null;
            
            try {
                $decrypted_article = Crypt::decryptString($article_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $article = $this->getArticleByRef($decrypted_article);

        }

        $periode_debut = null;

        if($periode_debut_crypt != null){

            $periode_debut = null;
            
            try {
                $periode_debut = Crypt::decryptString($periode_debut_crypt);
            } catch (DecryptException $e) {
                //
            }


        }

        $periode_fin = null;

        if($periode_fin_crypt != null){

            $periode_fin = null;
            
            try {
                $periode_fin = Crypt::decryptString($periode_fin_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }

        if($famille != null && $structure != null && $periode_debut != null && $periode_fin != null){
            if($article != null){
                $consommations = $this->getConsomations($famille->ref_fam,$structure->code_structure,$periode_debut,$periode_fin,$article->ref_articles);
            }else{
                $consommations = $this->getConsomations($famille->ref_fam,$structure->code_structure,$periode_debut,$periode_fin);
                
            }
            
        }

        
        $structures = $this->getStructures();
        $familles = $this->getFamilles();

        return view('requisitions.consommation',[
            'consommations'=>$consommations,
            'titre'=>$titre,
            'articles'=>$articles,
            'structures'=>$structures,
            'familles'=>$familles,
            'famille'=>$famille,
            'structure'=>$structure,
            'article'=>$article,
            'periode_debut'=>$periode_debut,
            'periode_fin'=>$periode_fin,
        ]);
    }


    public function crypt($famille=null,$structure=null,$article=null,$periode_debut=null,$periode_fin=null){

        //dd($famille,$structure,$article,$periode_debut,$periode_fin);

        $crypt_famille = null;
        if(isset($famille)){
            $crypt_famille = Crypt::encryptString($famille);
        }

        $crypt_structure = null;
        if(isset($structure)){
            $crypt_structure = Crypt::encryptString($structure);
        }

        $crypt_article = null;
        if(isset($article)){
            $crypt_article = Crypt::encryptString($article);
        }

        $crypt_periode_debut = null;
        if(isset($periode_debut)){
            $crypt_periode_debut = Crypt::encryptString($periode_debut);
        }

        $crypt_periode_fin = null;
        if(isset($periode_fin)){
            $crypt_periode_fin = Crypt::encryptString($periode_fin);
        }

        return redirect('requisitions/consommation/'.$crypt_famille.'/'.$crypt_structure.'/'.$crypt_article.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
    }

    public function crypt_post(Request $request){

        
        $request->validate([
            'cst'=>['required','numeric'],
            'nst'=>['required','string'],
            'rf'=>['required','numeric'],
            'df'=>['required','string'],
            'pd'=>['required','date'],
            'pf'=>['required','date','after_or_equal:pd'],
            'submit'=>['required','string'],
        ]);
        $famille=null;
        $structure=null;
        $article=null;
        $periode_debut=null;
        $periode_fin=null;   
        

        if(isset($request->rf)){ $famille = $request->rf; }

        if(isset($request->cst)){ $structure = $request->cst; }

        if(isset($request->art)){ $article = $request->art; }

        if(isset($request->pd)){ $periode_debut = $request->pd; }

        if(isset($request->pf)){ $periode_fin = $request->pf; }
        
        $crypt_famille = Crypt::encryptString($famille);
    

        $crypt_structure = Crypt::encryptString($structure);
    

        $crypt_article = Crypt::encryptString($article);
    

        $crypt_periode_debut = Crypt::encryptString($periode_debut);
    

        $crypt_periode_fin = Crypt::encryptString($periode_fin);
            
        if(isset($request->submit)){
            if($request->submit === 'soumettre'){
                return redirect('requisitions/consommation/'.$crypt_famille.'/'.$crypt_structure.'/'.$crypt_article.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
            }elseif($request->submit === 'imprimer'){
                return redirect('prints/requisitions/print_consommation/'.$crypt_famille.'/'.$crypt_structure.'/'.$crypt_article.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
            }
        }
        
    }

    public function recap_consommation($famille_crypt=null,$periode_debut_crypt=null,$periode_fin_crypt=null){

        $array_famille_crypts = [];
        if($famille_crypt != null){
            for ($i=0; $i < 5; $i++) { 
                if(isset(explode('@fy@',$famille_crypt)[$i])){
                    $array_famille_crypts[$i] = explode('@fy@',$famille_crypt)[$i];
                }
            }
        }

        $familles_consernees = [];
        if(count($array_famille_crypts) > 0){
            foreach ($array_famille_crypts as $key => $value) {
                try {
                    $familles_consernees[$key] = Crypt::decryptString($array_famille_crypts[$key]);
                } catch (DecryptException $e) {
                    //
                }
            }
        }
        

        $titre = null;

        $famille = null;

        if($famille_crypt != null){

            $decrypted_famille = null;

            try {
                $decrypted_famille = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $famille = $this->getFamilleByRefFam($decrypted_famille);

        }



        $periode_debut = null;

        if($periode_debut_crypt != null){

            $periode_debut = null;
            
            try {
                $periode_debut = Crypt::decryptString($periode_debut_crypt);
            } catch (DecryptException $e) {
                //
            }


        }

        $periode_fin = null;

        if($periode_fin_crypt != null){

            $periode_fin = null;
            
            try {
                $periode_fin = Crypt::decryptString($periode_fin_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }

        

        
        $structures = $this->getStructures();
        $familles = $this->getFamilles();

        return view('requisitions.recap_consommation',[
            'titre'=>$titre,
            'articles'=>$articles,
            'structures'=>$structures,
            'familles'=>$familles,
            'famille'=>$famille,
            'periode_debut'=>$periode_debut,
            'periode_fin'=>$periode_fin,
            'familles_consernees'=>$familles_consernees,
        ]);
    }

    public function crypt_recap($famille=null,$periode_debut=null,$periode_fin=null){

        //dd($famille,$structure,$article,$periode_debut,$periode_fin);

        $crypt_famille = null;
        if(isset($famille)){
            $crypt_famille = Crypt::encryptString($famille);
        }

        $crypt_periode_debut = null;
        if(isset($periode_debut)){
            $crypt_periode_debut = Crypt::encryptString($periode_debut);
        }

        $crypt_periode_fin = null;
        if(isset($periode_fin)){
            $crypt_periode_fin = Crypt::encryptString($periode_fin);
        }

        return redirect('requisitions/recap_consommation/'.$crypt_famille.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
    }

    public function crypt_post_recap(Request $request){
        
        $request->validate([
            'rf'=>['required','array'],
            'df'=>['required','array'],
            'pd'=>['required','date'],
            'pf'=>['required','date','after_or_equal:pd'],
            'submit'=>['required','string'],
        ]);
        
        

        $famille=null;
        $periode_debut=null;
        $periode_fin=null;  
        
        $crypt_famille = '';

        foreach ($request->rf as $key => $value) {
            $crypt_familles = Crypt::encryptString($request->rf[$key]);
            $crypt_famille = $crypt_famille.''.$crypt_familles.'@fy@';
        }
        
        //dd($request,$decrypted_famille,$crypt_famille);
        if(isset($request->rf)){ $famille = $request->rf; }

        if(isset($request->pd)){ $periode_debut = $request->pd; }

        if(isset($request->pf)){ $periode_fin = $request->pf; }

        $crypt_periode_debut = Crypt::encryptString($periode_debut);

        $crypt_periode_fin = Crypt::encryptString($periode_fin);
            
        if(isset($request->submit)){
            if($request->submit === 'soumettre'){
                return redirect('requisitions/recap_consommation/'.$crypt_famille.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
            }elseif($request->submit === 'imprimer'){
                return redirect('prints/req/recap/'.$crypt_famille.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
            }
        }
        
    }


    public function structure_connectee(){

        $code_structure = null;

        $structure = DB::table('sections as s')
            ->join('agent_sections as ase','ase.sections_id','=','s.id')
            ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
            ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
            ->join('structures as st','st.code_structure','=','s.code_structure')
            ->where('ase.agents_id',auth()->user()->agents_id)
            ->where('tsas.libelle','Activé')
            ->first();
        
        if ($structure!=null) {
            $code_structure = $structure->code_structure;
        }
        return $code_structure;

    }

    public function validation($etape,$request){

        if (isset($etape)) {
            if ($etape === "store") {

                $validate = $request->validate([
                    'intitule' => ['required','string'],
                    'gestion' => ['required','string'],
                    'ref_articles' => ['required','array'],
                    'magasin_stocks_id' => ['required','array'],
                    'design_article' => ['required','array'],
                    'cmup' => ['required','array'],
                    'qte' => ['required','array'],
                    'montant' => ['required','array'],
                    'commentaire' => ['nullable','string']
                ]);

            }elseif ($etape === "update"){

                $validate = $request->validate([
                    'intitule' => ['required','string'],
                    'requisitions_id' => ['required','numeric'],
                    'gestion' => ['required','string'],
                    'ref_articles' => ['required','array'],
                    'magasin_stocks_id' => ['required','array'],
                    'design_article' => ['required','array'],
                    'cmup' => ['required','array'],
                    'qte' => ['required','array'],
                    'montant' => ['required','array'],
                    'commentaire' => ['required','string'],
                    'num_bc' => ['required','string'],
                    'submit' => ['required','string'],
                ]);

            }
        }
        

    }

    public function controle_saisie($etape,$request){
        $error = null;

        if (isset($etape)) {
            if ($etape === "store" or $etape === "update") {

                
                if (isset($request->ref_articles)) {
                    if (count($request->ref_articles) > 0) {
                        foreach ($request->ref_articles as $item => $value) {
        
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
        
                            if (gettype($qte[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
                            
        
        
        
                            $montant[$item] = filter_var($request->montant[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $montant[$item] = $montant[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir montant entier numérique";
                            }
        
                            if (gettype($montant[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir montant entier numérique";
                            }
        
        
                            $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $cmup[$item] = $cmup[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
        
                            if (gettype($cmup[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }

                            

                            if ($request->ref_articles[$item]!=null && $request->design_article[$item]!=null && $qte[$item]!=null) {
        
                                if ($qte[$item] <= 0) {
                                    $error = "La quantité demande ne peut être nulle ou négative";
                                }
        
                                /*if ($montant[$item] <= 0) {
                                    $error = "Le montant demande ne peut être nulle ou négative";
                                }*/
                                
                            }else{
                                $error = "Veuillez remplir tous les champs";
                            }
        
                        }
                    }
                }

                return $error;
                
            }
        }
        
    }

    public function storeDetailRequisition($etape,$request,$requisitions_id,$profils_id){
        $error = null;

        if (isset($etape)) {
            if ($etape === "store" or $etape === "update") {
                if (count($request->ref_articles) > 0) {

                    if ($etape === "update") {
                        Demande::where('requisitions_id',$requisitions_id)->delete();
                    }

                    foreach ($request->ref_articles as $item => $value) {
        
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {

                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
        
                            if (gettype($qte[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
                            
        
        
        
                            $montant[$item] = filter_var($request->montant[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $montant[$item] = $montant[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir montant entier numérique";
                            }
        
                            if (gettype($montant[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir montant entier numérique";
                            }
        
        
                            $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $cmup[$item] = $cmup[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
        
                            if (gettype($cmup[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
                        
                        if (!isset($error)) {
                            $data = [
                                'requisitions_id' => $requisitions_id,
                                'magasin_stocks_id' => $request->magasin_stocks_id[$item],
                                'qte' => $qte[$item], 
                                'prixu' => $cmup[$item], 
                                'montant' => $montant[$item], 
                                'profils_id' => $profils_id, 
                            ];

                            $demande = Demande::create($data);
                            
                            
                        }
                        
                        
                                       
        
                    }
                    
                }
            }
        }

        return $error;
        
    }

    public function statut_requisition($etape,$request,$requisitions_id,$profils_id,$libelle){
        
        if (isset($etape)) {
            if ($etape === "store" or $etape === "update") {
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
    
    
                StatutRequisition::create([
                    'profils_id'=>$profils_id,
                    'requisitions_id'=>$requisitions_id,
                    'type_statut_requisitions_id'=>$type_statut_requisitions_id,
                    'date_debut'=>date('Y-m-d'),
                    'commentaire'=>$request->commentaire,
                ]);
            }
        }

            
    }

    public function notification_demandeur($requisitions_id,$subject){

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

    public function hierarchie($user_id){
        $hierarchie = DB::table('users as u')
            ->join('hierarchies as h','h.agents_id','=','u.agents_id')
            ->where('u.id',$user_id)
            ->where('h.flag_actif',1)
            ->first();

        return $hierarchie;
    }

    public function acces_validateur($requisition_id,$libelle){

        $profil = DB::table('requisitions as r')
                ->select('r.id as id','r.num_bc as num_bc','r.updated_at as updated_at','r.intitule as intitule')
                
                ->orWhereIn('r.id', function($query) use($libelle,$requisition_id){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n1',auth()->user()->agents_id)
                        ->where('sr.requisitions_id',$requisition_id)
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('r.id = sr.requisitions_id');
                })
                ->orWhereIn('r.id', function($query) use($libelle,$requisition_id){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n2',auth()->user()->agents_id)
                        ->where('sr.requisitions_id',$requisition_id)
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('r.id = sr.requisitions_id');
                })
                ->first();

        return $profil;

    }

    public function demandeur($requisitions_id){

        $demandeur_profils_id = null;

        $requisition = DB::table('requisitions as r')
            ->join('profils as p','p.id','=','r.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->where('r.id',$requisitions_id)
            ->select('u.id')
            ->first();
        if ($requisition!=null) {
            if ($requisition->id === auth()->user()->id) {
                $demandeur_profils_id = $requisition->id;
            }
        }

        return $demandeur_profils_id;
    }

    public function notification_hierarchie($requisitions_id,$responsable,$subject){

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

    public function create_numero_bc($exercice){
        $sequence_id = count(DB::table('requisitions')->where('exercice',$exercice)->get()) + 1;
        
        $num_bc = 'BCI'.date("Y").''.$sequence_id;

        return $num_bc;
    }

    public function pilote_aee($requisition_id){

        $pilote_aee = DB::table('users as u')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->join('agent_sections as ase','ase.agents_id','=','a.id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->join('requisitions as r','r.code_structure','=','st.code_structure')
        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
        ->where('tp.name','Pilote AEE')
        ->where('tsas.libelle','Activé')
        ->where('p.flag_actif',1)
        ->whereRaw('st.code_structure = r.code_structure')
        ->where('p.users_id', auth()->user()->id)
        ->where('r.id', $requisition_id)
        ->first();
        

        return $pilote_aee;

    }

    public function profil_aee($code_structure){

        $data = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule')->orderByDesc('requisitions.id')->where('requisitions.code_structure',$code_structure)
        ->whereIn('requisitions.id', function($query){
            $query->select(DB::raw('sr.requisitions_id'))
                  ->from('statut_requisitions as sr')
                  ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                  ->where('tsr.libelle','Transmis (Pilote AEE)')
                  ->whereRaw('requisitions.id = sr.requisitions_id');
        })
        ->orWhereIn('requisitions.id', function($query){
            $query->select(DB::raw('sr2.requisitions_id'))
                  ->from('statut_requisitions as sr2')
                  ->join('type_statut_requisitions as tsr2','tsr2.id','=','sr2.type_statut_requisitions_id')
                  ->join('demandes as d','d.requisitions_id','=','sr2.requisitions_id')
                  ->join('profils as p','p.id','=','d.profils_id')
                  ->where('p.users_id',auth()->user()->id)
                  ->where('tsr2.libelle','Soumis pour validation')
                  ->whereRaw('requisitions.id = sr2.requisitions_id');
        })
        ->orWhereIn('requisitions.id', function($query){
            $query->select(DB::raw('r3.id'))
                  ->from('requisitions as r3')
                  ->join('profils as p','p.id','=','r3.profils_id')
                  ->where('p.users_id',auth()->user()->id)
                  ->whereRaw('requisitions.id = r3.id');
        })
        ->get();

        return $data;
    }

    public function profil_agent(){
        $data = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule')
            ->orderByDesc('requisitions.id')
            ->whereIn('requisitions.id', function($query){
                $query->select(DB::raw('r.id'))
                        ->from('requisitions as r')
                        ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('profils as p','p.id','=','r.profils_id')
                        ->where('p.users_id',auth()->user()->id)
                        ->where('tsr.libelle','Soumis pour validation')
                        ->whereRaw('requisitions.id = r.id');
            })
            ->orWhereIn('requisitions.id', function($query){
                $query->select(DB::raw('r.id'))
                        ->from('requisitions as r')
                        ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','r.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n1',auth()->user()->agents_id)
                        ->where('tsr.libelle','Transmis (Responsable N+1)')
                        ->whereRaw('requisitions.id = r.id');
            })
            ->orWhereIn('requisitions.id', function($query){
                $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n2',auth()->user()->agents_id)
                        ->where('tsr.libelle','Transmis (Responsable N+1)')
                        ->whereRaw('requisitions.id = sr.requisitions_id');
            })
            ->get();
            return $data;
    }

    public function profil_responsable_stock($ref_depot){
        $data = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule')->orderByDesc('requisitions.id')
        ->join('structures as s','s.code_structure','=','requisitions.code_structure')
        ->where('s.ref_depot',$ref_depot)
        ->whereIn('requisitions.id', function($query){
            $query->select(DB::raw('sr.requisitions_id'))
                  ->from('statut_requisitions as sr')
                  ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                  ->where('tsr.libelle',['Transmis (Responsable des stocks)'])
                  ->whereRaw('requisitions.id = sr.requisitions_id');
        })
        ->orWhereIn('requisitions.id', function($query){
            $query->select(DB::raw('sr.requisitions_id'))
                  ->from('statut_requisitions as sr')
                  ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                  ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                  ->join('profils as p','p.id','=','d.profils_id')
                  ->where('p.users_id',auth()->user()->id)
                  ->where('tsr.libelle','Soumis pour validation')
                  ->whereRaw('requisitions.id = sr.requisitions_id');
        })
        ->get();

        return $data;
    }

    public function profil_gestionnaire_stock($ref_depot){

        $data = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule')->orderByDesc('requisitions.id')
        ->join('structures as s','s.code_structure','=','requisitions.code_structure')
        ->where('s.ref_depot',$ref_depot)
        ->whereIn('requisitions.id', function($query){
            $query->select(DB::raw('sr.requisitions_id'))
                    ->from('statut_requisitions as sr')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                    ->join('profils as p','p.id','=','d.profils_id')
                    ->where('p.users_id',auth()->user()->id)
                    ->where('tsr.libelle','Soumis pour validation')
                    ->whereRaw('requisitions.id = sr.requisitions_id');
        })
        ->orWhereIn('requisitions.id', function($query){
            $query->select(DB::raw('sr.requisitions_id'))
                  ->from('statut_requisitions as sr')
                  ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                  ->where('tsr.libelle',['Soumis pour livraison'])
                  ->whereRaw('requisitions.id = sr.requisitions_id');
        })
        ->get();
        return $data;
    }

    public function dernier_statut_requisition($requisition_id){
        $statut = null;

        $statut_requisition = DB::table('statut_requisitions as sr')
                ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                ->where('sr.requisitions_id',$requisition_id)
                ->orderByDesc('sr.id')
                ->limit(1)
                ->first();
                if ($statut_requisition!=null) {
                    $statut = $statut_requisition->libelle;
                }
        return $statut;
    }

    public function action_profil_agent($requisition_id){

        $data = DB::table('requisitions as r')
        ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
        ->join('demandes as d','d.requisitions_id','=','r.id')
        ->join('profils as p','p.id','=','r.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('p.flag_actif',1)
        ->where('u.id',auth()->user()->id)
        ->where('tsr.libelle','Soumis pour validation')
        ->where('r.id',$requisition_id)
        ->where('tp.name','Agent Cnps')
        ->first();

        return $data;

    }

    public function action_responsable_stock($requisitions_id,$ref_depot){

        $responsable_stock = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Responsable des stocks')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->where('a.id',auth()->user()->agents_id)
                ->where('st.ref_depot',$ref_depot)
                ->whereIn('st.ref_depot', function($query) use($requisitions_id){
                    $query->select(DB::raw('st2.ref_depot'))
                          ->from('requisitions as r')
                          ->join('structures as st2','st2.code_structure','=','r.code_structure')
                          ->whereRaw('st2.ref_depot = st.ref_depot')
                          ->where('r.id',$requisitions_id);
                })
                ->first();
            
        return $responsable_stock;
        
    }

    public function action_gestionnaire_stock($requisitions_id,$ref_depot){

        $gestionnaire_stock = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    ->where('tp.name','Gestionnaire des stocks')
                    ->where('tsas.libelle','Activé')
                    ->where('p.flag_actif',1)
                    ->where('a.id',auth()->user()->agents_id)
                    ->where('st.ref_depot',$ref_depot)
                    ->whereIn('st.ref_depot', function($query) use($requisitions_id){
                        $query->select(DB::raw('st2.ref_depot'))
                            ->from('requisitions as r')
                            ->join('structures as st2','st2.code_structure','=','r.code_structure')
                            ->whereRaw('st2.ref_depot = st.ref_depot')
                            ->where('r.id',$requisitions_id);
                    })
                    ->first();
            
        return $gestionnaire_stock;
        
    }

    public function depot_requisition($requisitions_id){

        $depot = DB::table('requisitions as r')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->where('r.id', $requisitions_id)
                ->first();
            
        return $depot;
        
    }

    public function getSessionData(Request $request){

        $type_profils_name = null;

        if ($request->session()->has('type_profils_name')) {
            $type_profils_name = $request->session()->get('type_profils_name');
        }

        return $type_profils_name;
        
    }

    public function storeSessionBackground($request){
        $request->session()->put('backgroundImage','container-infographie2');
    }

    public function data($libelle,$type_profils_name,$code_structure=null,$libelle2=null){

        $requisitions = [];
        if (isset($type_profils_name)) {
            if ($type_profils_name === "Agent Cnps") {

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.id')
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->where('p.users_id',auth()->user()->id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();               

            }elseif($type_profils_name === "Responsable N+1"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.id')
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n1',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

            }elseif($type_profils_name === "Responsable N+2"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.id')
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n2',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }elseif($type_profils_name === "Pilote AEE"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.id')
                ->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->orWhereIn('requisitions.id', function($query) use($libelle2){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle2)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }elseif($type_profils_name === "Responsable des stocks"){

                
                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.id')
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

               // dd($requisitions);
            }elseif($type_profils_name === "Gestionnaire des stocks"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.id')
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }
        }

        return $requisitions;

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
        if ($etape === "index") {

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
                ->whereIn('tp.name', ['Administrateur fonctionnel','Agent Cnps','Responsable N+1','Responsable N+2','Pilote AEE','Responsable des stocks','Gestionnaire des stocks'])
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->first();

        }elseif ($etape === "create" or $etape === "store") {

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
            ->whereIn('tp.name', ['Pilote AEE','Agent Cnps'])
            ->limit(1)
            ->select('p.id', 'se.code_section', 's.ref_depot')
            ->where('p.flag_actif',1)
            ->where('p.id',Session::get('profils_id'))
            ->where('tsase.libelle','Activé')
            ->first();
                
        }elseif ($etape === "edit" or $etape === "update") {

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
            ->whereIn('tp.name', ['Pilote AEE','Agent Cnps'])
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
            
            
        }elseif ($etape === "show") {
            if ($type_profils_name === 'Administrateur fonctionnel') {
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
                ->where('tp.name', 'Administrateur fonctionnel')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->first();
            }elseif ($type_profils_name === 'Agent Cnps') {

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

                    if($ref_depot === 83){
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

                

            }elseif ($type_profils_name === 'Responsable N+1') {

                $profils = DB::table('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                    ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->whereIn('tp.name', ['Responsable N+1'])
                    ->limit(1)
                    ->select('p.id', 'se.code_section', 's.ref_depot')
                    ->where('p.flag_actif',1)
                    ->where('p.id',Session::get('profils_id'))
                    ->where('tsase.libelle','Activé')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->where('h.flag_actif',1)
                    ->where('h.agents_id_n1',auth()->user()->agents_id)
                    ->whereIn('h.agents_id',function($query) use($requisitions_id){
                        $query->select(DB::raw('aa.id'))
                                ->from('requisitions as r')
                                ->join('profils as pp','pp.id','=','r.profils_id')
                                ->join('users as uu','uu.id','=','pp.users_id')
                                ->join('agents as aa','aa.id','=','uu.agents_id')
                                ->where('r.id',$requisitions_id)
                                ->whereRaw('h.agents_id = aa.id');
                    })
                    ->first();
                

            }elseif ($type_profils_name === 'Responsable N+2') {

                $profils = DB::table('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                    ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->whereIn('tp.name', ['Responsable N+2'])
                    ->limit(1)
                    ->select('p.id', 'se.code_section', 's.ref_depot')
                    ->where('p.flag_actif',1)
                    ->where('p.id',Session::get('profils_id'))
                    ->where('tsase.libelle','Activé')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->where('h.flag_actif',1)
                    ->where('h.agents_id_n2',auth()->user()->agents_id)
                    ->whereIn('h.agents_id',function($query) use($requisitions_id){
                        $query->select(DB::raw('aa.id'))
                                ->from('requisitions as r')
                                ->join('profils as pp','pp.id','=','r.profils_id')
                                ->join('users as uu','uu.id','=','pp.users_id')
                                ->join('agents as aa','aa.id','=','uu.agents_id')
                                ->where('r.id',$requisitions_id)
                                ->whereRaw('h.agents_id = aa.id');
                    })
                    ->first();
                

            }
            
            
            
        }

        return $profils;
    }

    public function getRequisitions($requisitions_id){
            
        $requisitions = DB::table('requisitions as r')
        ->join('gestions as g','g.code_gestion','=','r.code_gestion')
        ->join('structures as st','st.code_structure','=','r.code_structure')
        ->select('st.*','g.*','r.*')
        ->where('r.id',$requisitions_id)
        ->first();

        return $requisitions;
    }

    public function getDemandes($requisitions_id){

        $demandes = DB::table('requisitions as r')
                        ->join('demandes as d','d.requisitions_id','=','r.id')
                        ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                        ->where('r.id',$requisitions_id)
                        ->orderBy('d.id', 'ASC')
                        ->select('d.id','a.ref_articles','a.design_article','ms.cmup','d.qte','ms.cmup','ms.qte as qte_stock','d.magasin_stocks_id')

                        ->whereIn('d.id', function($query)
                        {
                            $query->select(DB::raw('dd.id'))
                                ->from('demandes as dd')
                                ->join('profils as p','p.id','=','dd.profils_id')
                                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                ->join('users as u','u.id','=','p.users_id')
                                ->where('tp.name','Agent Cnps')
                                ->where('u.id',auth()->user()->id)
                                ->whereRaw('dd.id = d.id');
                        })

                        ->get();
        return $demandes;
    }

    public function getArticles($ref_depot){

        $articles = DB::table('articles as a')
                        ->join('magasin_stocks as ms','ms.ref_articles','=','a.ref_articles')
                        ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                        ->join('depots as d','d.id','=','m.depots_id')
                        ->select('a.ref_articles','a.design_article', 'ms.cmup', 'ms.id','ms.qte')
                        ->whereRaw('ms.qte > 0')
                        ->where('d.ref_depot',$ref_depot)
                        ->get();
                        
        return $articles;
    }

    public function getStatutRequisition($requisitions_id){
        $statut_requisition = DB::table('statut_requisitions as sr')
        ->where('sr.requisitions_id', $requisitions_id)
        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
        ->join('profils as p','p.id','=','sr.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->orderByDesc('id')
        ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
        ->limit(1)
        ->first();

        return $statut_requisition;
    }

    

    
}
