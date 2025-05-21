<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Famille;
use App\Models\Gestion;
use App\Models\Perdiem;
use App\Models\Structure;
use Illuminate\Http\Request;
use App\Models\DetailPerdiem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class PerdiemController extends ControllerPerdiem
{
    private $controller3;
    public function __contruct(Controller3 $controller3) {
        $this->middleware('auth');
        $this->controller3 = $controller3;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Session::has('profils_id')){

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        

        $type_profils_lists = ['Gestionnaire des achats','Responsable DMP','Administrateur fonctionnel','Responsable des achats','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Directeur Général','Responsable Caisse'];
        
        $etape = "index";
        
        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if ($type_profil != null) {
            $type_profils_name = $type_profil->name;
        }else{
            return redirect()->back()->with('error','Profil introuvable');
        }

        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $code_structure = null;
        $user_connect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

        if ($user_connect != null) {
            $code_structure = $user_connect->code_structure;
        }
        
        $libelle = null;
        $acces_create = null;
        if ($type_profils_name === 'Gestionnaire des achats') {
            $acces_create = 1;
            $libelle = 'Soumis pour validation';
        }elseif ($type_profils_name === 'Administrateur fonctionnel') {
            $libelle = 'Soumis pour validation';
        }elseif ($type_profils_name === 'Responsable des achats') {
            $libelle = 'Transmis (Responsable des achats)';
        }elseif ($type_profils_name === 'Responsable DMP') {
            $libelle = 'Transmis (Responsable DMP)';
        }elseif ($type_profils_name === 'Responsable contrôle budgetaire') {
            $libelle = 'Transmis (Responsable contrôle budgetaire)';
        }elseif ($type_profils_name === 'Chef Département DCG') {
            $libelle = 'Transmis (Chef Département DCG)';
        }elseif ($type_profils_name === 'Responsable DCG') {
            $libelle = 'Transmis (Responsable DCG)';
        }elseif ($type_profils_name === 'Directeur Général Adjoint') {
            $libelle = 'Transmis (Directeur Général Adjoint)';
        }elseif ($type_profils_name === 'Responsable DFC') {
            $libelle = 'Transmis (Responsable DFC)';
        }elseif ($type_profils_name === 'Responsable Caisse') {
            $libelle = 'Accord pour paiement';
        }

        $perdiems = $this->getPerdiems($type_profils_name,$code_structure,$libelle);

        return view('perdiems.index',[
            'acces_create'=>$acces_create,
            'code_structure'=>$code_structure,
            'perdiems'=>$perdiems,
            'type_profils_name'=>$type_profils_name
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function create($structure = null, $code_gestion = null)
    {
        $exercice = $this->getExercice();

        $credit_budgetaires = [];

        if ($structure != null) {
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($structure);
            } catch (DecryptException $e) {
                //
            }

            $structure = Structure::findOrFail($decrypted);
            if ($exercice != null && $code_gestion != null) {

                //$param = $exercice->exercice.'-'.$code_gestion.'-'.$structure->code_structure.'-'.$demandeFond->ref_fam;


                $param = $exercice->exercice.'-'.$code_gestion.'-B-'.$structure->code_structure;
                $ref_depot = $structure->ref_depot;
                $code_structure = $structure->code_structure; 

                $this->storeCreditBudgetaireByWebService($param,$ref_depot,$code_structure);
            }
            

            $credit_budgetaires = $this->getCreditBudgetairesByStructure($structure->code_structure);

        }       
        
        



        if(Session::has('profils_id')){ 

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_lists = ['Gestionnaire des achats'];
        
        $etape = "create";
        
        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if ($type_profil != null) {
            $type_profils_name = $type_profil->name;
        }else{
            return redirect()->back()->with('error','Profil introuvable');
        }

        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        //$code_structure = null;
        $user_connect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

        if ($user_connect != null) {
            //$code_structure = $user_connect->code_structure;
        }

        $familles = Famille::all();

        $gestions = DB::table('gestions')
                        ->get();

        $gestion_default = DB::table('gestions')
                        ->where('code_gestion','G')
                        ->first();

        

        $num_bc = null;

        if ($exercice != null) {
            $num_bc = count(DB::table('perdiems')->where('exercice',$exercice->exercice)->get()) + 1;
        }

        $structures = $this->getStructures();

        $agents = DB::table('agents as a')
                    ->join('users as u','u.agents_id','=','a.id')
                    ->where('u.flag_actif',1)
                    ->whereIn('a.id', function($query){
                        $query->select(DB::raw('ase.agents_id'))
                              ->from('agent_sections as ase')
                              ->whereRaw('a.id = ase.agents_id');
                    })
                    ->get();

        return view('perdiems.create',[
            'familles'=>$familles,
            'gestions'=>$gestions,
            'gestion_default'=>$gestion_default,
            'credit_budgetaires'=>$credit_budgetaires,
            'num_bc'=>$num_bc,
            'structures'=>$structures,
            'structure_perdiem'=>$structure
            ,'agents'=>$agents
        ]);
    }
    */
    public function create(){
        $exercice = $this->getExercice();        

        if(Session::has('profils_id')){ 

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_lists = ['Gestionnaire des achats'];
        
        $etape = "create";
        
        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if ($type_profil != null) {
            $type_profils_name = $type_profil->name;
        }else{
            return redirect()->back()->with('error','Profil introuvable');
        }

        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        $familles = Famille::all();
        $gestions = DB::table('gestions')
                        ->get();
        $gestion_default = DB::table('gestions')
                        ->where('code_gestion','G')
                        ->first();
        $num_bc = null;
        if ($exercice != null) {
            $num_bc = count(DB::table('perdiems')->where('exercice',$exercice->exercice)->get()) + 1;
        }

        $structures = $this->getStructures();

        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->where('u.flag_actif',1)
        ->whereIn('a.id', function($query){
            $query->select(DB::raw('ase.agents_id'))
                    ->from('agent_sections as ase')
                    ->whereRaw('a.id = ase.agents_id');
        })
        ->get();


        $credit_budgetaires_select = null;        

        $gestions = $this->getGestion();

        $familles = $this->getFamille();
        $credit_budgetaires_credit = null;

        return view('perdiems.create',[
            'familles'=>$familles,
            'gestions'=>$gestions,
            'gestion_default'=>$gestion_default,
            'num_bc'=>$num_bc,
            'structures'=>$structures,
            'agents'=>$agents,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit,

        ]);

    }

    public function create_credit($crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null){
        try {

            $decrypted_ref_fam = Crypt::decryptString($crypted_ref_fam);
            $decrypted_code_structure = Crypt::decryptString($crypted_code_structure);
            $decrypted_code_gestion = Crypt::decryptString($crypted_code_gestion);

        } catch (DecryptException $e) {
            //
        }
        $famille = Famille::where('ref_fam',$decrypted_ref_fam)->first();
        if($famille === null){
            return redirect('/perdiems/create')->with('error', 'Veuillez saisir un compte valide');
        }

        $structure = Structure::where('code_structure',$decrypted_code_structure)->first();
        if($structure === null){
            return redirect('/perdiems/create')->with('error', 'Veuillez saisir une structure valide');
        }

        $gestion = Gestion::where('code_gestion',$decrypted_code_gestion)->first();
        if($gestion === null){
            return redirect('/perdiems/create')->with('error', 'Veuillez saisir une gestion valide');
        }

        $exercices = $this->getExercice();        
        $credit_budgetaires_credit = null;
        if($famille != null && $structure != null && $exercices != null && $gestion != null){
            
            try {
                $param = $exercices->exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;

                $this->storeCreditBudgetaireByWebService($param,$structure->ref_depot);

                $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercices->exercice);

                if($disponible != null){
                    $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;
                }
                
             } catch (\Throwable $th) {
                 
             }
        }


        //old

        $exercice = $this->getExercice();        

        if(Session::has('profils_id')){ 

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_lists = ['Gestionnaire des achats'];
        
        $etape = "create";
        
        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if ($type_profil != null) {
            $type_profils_name = $type_profil->name;
        }else{
            return redirect()->back()->with('error','Profil introuvable');
        }

        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        //$code_structure = null;
        $user_connect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

        if ($user_connect != null) {
            //$code_structure = $user_connect->code_structure;
        }

        $familles = Famille::all();

        $gestions = DB::table('gestions')
                        ->get();

        $gestion_default = DB::table('gestions')
                        ->where('code_gestion','G')
                        ->first();

        

        $num_bc = null;

        if ($exercice != null) {
            $num_bc = count(DB::table('perdiems')->where('exercice',$exercice->exercice)->get()) + 1;
        }

        $structures = $this->getStructures();

        $agents = DB::table('agents as a')
                    ->join('users as u','u.agents_id','=','a.id')
                    ->where('u.flag_actif',1)
                    ->whereIn('a.id', function($query){
                        $query->select(DB::raw('ase.agents_id'))
                              ->from('agent_sections as ase')
                              ->whereRaw('a.id = ase.agents_id');
                    })
                    ->get();


        $credit_budgetaires_select = null;

        if($famille != null){
            $credit_budgetaires_select = $this->getFamilleById($famille->id);
        }

        $gestions = $this->getGestion();

        $familles = $this->getFamille();
        $sections = [];
        if ($structure != null) {
            $sections = $this->getSectionById($structure->code_structure);
        }

        return view('perdiems.create',[
            'familles'=>$familles,
            'gestions'=>$gestions,
            'gestion_default'=>$gestion_default,
            'num_bc'=>$num_bc,
            'structures'=>$structures,
            'structure_perdiem'=>$structure,
            'gestion_perdiem'=>$gestion,
            'agents'=>$agents,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'sections'=>$sections,
            'disponible'=>$disponible,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit

        ]);

    }

    public function crypt($ref_fam,$code_structure,$code_gestion){
        
        return redirect('perdiems/create_credit/'.Crypt::encryptString($ref_fam).'/'.Crypt::encryptString($code_structure).'/'.Crypt::encryptString($code_gestion).'');

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
            'num_or'=>['required','numeric'],//OK
            'perdiems_intitule'=>['required','string'],//OK
            'code_gestion'=>['required','string'],//OK
            'libelle_gestion'=>['required','string'],//OK
            'code_structure'=>['required','numeric'],//OK
            'nom_structure'=>['required','string'],//OK
            'credit_budgetaires_id'=>['required','numeric'],//OK
            //'credit'=>['required','numeric'],//OK
            'ref_fam'=>['required','numeric'],//OK
            'design_fam'=>['required','string'],//OK
            'solde_avant_op'=>['required','string'],
            'nom_prenoms'=>['required','array'],//OK
            'montant_bis'=>['required','array'],//OK
            'montant_total'=>['required','string'],//OK
            'submit'=>['required','string'],
        ]);

        $exercice = $this->getExercice();

        if ($exercice === null) {
            return redirect()->back()->with('error','Exercice introuvable');
        }

        $num_or = count(DB::table('perdiems')->where('exercice',$exercice->exercice)->get()) + 1;

        $num_pdm = $this->getLastNumPdm($exercice->exercice,$request->code_structure);

        $perdiem = $this->storePerdiem($num_pdm,$request->perdiems_intitule,$num_or,$request->code_gestion,$exercice->exercice,$request->ref_fam,$request->code_structure,filter_var($request->solde_avant_op, FILTER_SANITIZE_NUMBER_INT),$request->credit_budgetaires_id,filter_var($request->montant_total, FILTER_SANITIZE_NUMBER_INT));


        $detail_perdiem = null;
        if ($perdiem != null) {
            
            if (count($request->nom_prenoms) > 0 ) {
                foreach ($request->nom_prenoms as $key => $value) {
    
                    $piece_identite = null;
    
                    $piece_identite_name = null;
    
                    if (isset($request->piece_identite[$key])) {
            
                        $piece_identite =  $request->piece_identite[$key]->store('piece_identite','public');
    
                        $piece_identite_name = $request->piece_identite[$key]->getClientOriginalName();   
                        
                    }
    
                    $detail_perdiem = $this->storeDetailPerdiem($perdiem->id,$request->nom_prenoms[$key],$request->montant_bis[$key],$piece_identite,$piece_identite_name);
                    
                }
            }

            if (isset($request->piece)) {

                if (count($request->piece) > 0) {

                    foreach ($request->piece as $item => $value) {
                        if (isset($request->piece[$item])) {

                            $piece =  $request->piece[$item]->store('piece_jointe','public');

                            $name = $request->piece[$item]->getClientOriginalName();

                            $libelle = "Perdiems";
                            $flag_actif = 1;
                            $piece_jointes_id = null;
                            
                            $dataPiece = [
                                'subject_id'=>$perdiem->id,
                                'profils_id'=>Session::get('profils_id'),
                                'libelle'=>$libelle,
                                'piece'=>$piece,
                                'flag_actif'=>$flag_actif,
                                'name'=>$name,
                                'piece_jointes_id'=>$piece_jointes_id,
                            ];

                            $this->controller3->procedureStorePieceJointe($dataPiece);
                            
                            
                        }
                    }
                    

                }

            }
            
        }else{

            return redirect()->back()->with('error','Enregistrement non effectué');
            
        }

        if ($detail_perdiem != null) {

            $libelle = 'Soumis pour validation';

            $this->storeTypeStatutPerdiem($libelle);
            $type_statut_perdiem = $this->getTypeStatutPerdiem($libelle);

            if ($type_statut_perdiem != null) {

                $commentaire = null;
                $date_debut = date('Y-m-d');
                $date_fin = null;

                $this->storeStatutPerdiem($perdiem->id,$type_statut_perdiem->id,$date_debut,$date_fin,Session::get('profils_id'),$commentaire);
                
            }

            return redirect('/perdiems/index')->with('success','Enregistrement effectué');

        }else{

            return redirect()->back()->with('error','Enregistrement non effectué');

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Perdiem  $perdiem
     * @return \Illuminate\Http\Response
     */
    public function show($perdiem)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($perdiem);
        } catch (DecryptException $e) {
            //
        }

        $perdiem = Perdiem::findOrFail($decrypted);

        $detail_perdiems = [];
        $piece_jointes = [];
        $structure = null;

        if ($perdiem != null) {
            $perdiem = $this->getPerdiem($perdiem->id);
            $detail_perdiems = $this->getDetailPerdiems($perdiem->id);

            $type_piece = "Perdiems";

            $piece_jointes = $this->getPieceJointes($perdiem->id, $type_piece);
            
            $structure = $this->getStructureByCode($perdiem->code_structure);
        }

        $exercice = $this->getExercice();

        $credit_budgetaires = [];

        $verou_compte = null;      

        if(Session::has('profils_id')){ 

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_lists = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Administrateur fonctionnel','Responsable contrôle budgetaire','Chef Département DCG','Directeur Général Adjoint','Responsable DFC','Responsable DCG','Responsable Caisse'];
        
        $etape = "create";
        
        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if ($type_profil != null) {
            $type_profils_name = $type_profil->name;
        }else{
            return redirect()->back()->with('error','Profil introuvable');
        }

        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        //$code_structure = null;
        $user_connect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

        if ($user_connect != null) {
            //$code_structure = $user_connect->code_structure;
        }

        $familles = Famille::all();

        $gestions = DB::table('gestions')
                        ->get();

        $gestion_default = DB::table('gestions')
                        ->where('code_gestion','G')
                        ->first();
        $gestion_new = null;
        

        $num_bc = null;

        if ($exercice != null) {
            $num_bc = count(DB::table('perdiems')->where('exercice',$exercice->exercice)->get()) + 1;
        }

        $structures = $this->getStructures();

        $agents = DB::table('agents as a')
                    ->join('users as u','u.agents_id','=','a.id')
                    ->where('u.flag_actif',1)
                    ->whereIn('a.id', function($query){
                        $query->select(DB::raw('ase.agents_id'))
                              ->from('agent_sections as ase')
                              ->whereRaw('a.id = ase.agents_id');
                    })
                    ->get();

        $signataires = $this->getAgentFonctionsSignatairePerdiem($perdiem->id);

        return view('perdiems.show',[
            'familles'=>$familles,
            'gestions'=>$gestions,
            'gestion_default'=>$gestion_default,
            'credit_budgetaires'=>$credit_budgetaires,
            'num_bc'=>$num_bc,
            'structures'=>$structures,
            'structure_perdiem'=>$structure
            ,'agents'=>$agents,
            'detail_perdiems'=>$detail_perdiems,
            'perdiem'=>$perdiem,
            'piece_jointes'=>$piece_jointes,
            'gestion_new'=>$gestion_new,
            'verou_compte'=>$verou_compte,
            'signataires'=>$signataires
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Perdiem  $perdiem
     * @return \Illuminate\Http\Response
     */
    public function edit($perdiem, Request $request, $limited=null,$crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null)
    {
        $famille = null;
        $structure = null;
        $gestion = null;
        $disponible = null;
        $disponible_display = null;
        $structures = [];
        $flag_engagement = 0;

        if($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null){

            $disponible_display = 1;

            $famille = Famille::where('ref_fam',$crypted_ref_fam)->first();
            
            
            if($famille === null){
                return redirect('/perdiems/edit/'.$perdiem)->with('error', 'Veuillez saisir un compte valide');
            }

            $structure = Structure::where('code_structure',$crypted_code_structure)->first();
            if($structure === null){
                return redirect('/perdiems/edit/'.$perdiem)->with('error', 'Veuillez saisir une structure valide');
            }

            $gestion = Gestion::where('code_gestion',$crypted_code_gestion)->first();
            if($gestion === null){
                return redirect('/perdiems/edit/'.$perdiem)->with('error', 'Veuillez saisir une gestion valide');
            }


        }

        $decrypted_perdiem = null;
        try {
            $decrypted_perdiem = Crypt::decryptString($perdiem);
        } catch (DecryptException $e) {
            //
        }

        $perdiem = Perdiem::findOrFail($decrypted_perdiem);
        $detail_perdiems = [];
        $piece_jointes = [];
        if ($perdiem != null) {

            $perdiem = $this->getPerdiem($perdiem->id);
            $detail_perdiems = $this->getDetailPerdiems($perdiem->id);

            $type_piece = "Perdiems";

            $piece_jointes = $this->getPieceJointes($perdiem->id, $type_piece);

            if(isset($perdiem->flag_engagement)){
                $flag_engagement = $perdiem->flag_engagement;
            }
        }

        $exercice = $this->getExercice();

        $credit_budgetaires = [];

        $verou_compte = 1;

        $credit_budgetaires_credit = null;


        if($flag_engagement === 0){

            if($perdiem != null && $crypted_ref_fam === null && $crypted_code_structure === null && $crypted_code_gestion === null){

                
                if($perdiem != null){
                    try {

                        $param = $perdiem->exercice.'-'.$perdiem->code_gestion.'-'.$perdiem->code_structure.'-'.$perdiem->ref_fam;
        
                        $this->storeCreditBudgetaireByWebService($param, $perdiem->ref_depot);                    

                    } catch (\Throwable $th) {
                    }
                }

                $disponible = $this->getCreditBudgetaireDisponible($perdiem->ref_fam, $perdiem->code_structure, $perdiem->code_gestion, $perdiem->exercice);

                if($disponible != null){
                    $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;                
                }
                    
                $disponible_display = 1;
            }elseif($perdiem != null && $crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null){

            
                if($perdiem != null){
                    try {

                        $param = $exercice->exercice.'-'.$crypted_code_gestion.'-'.$crypted_code_structure.'-'.$crypted_ref_fam;
        
                        $this->storeCreditBudgetaireByWebService($param, $perdiem->ref_depot);                    

                    } catch (\Throwable $th) {
                    }
                }

                
                $disponible = $this->getCreditBudgetaireDisponible($crypted_ref_fam, $crypted_code_structure, $crypted_code_gestion, $exercice->exercice);

                if($disponible != null){
                    $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;                
                }
                    
                $disponible_display = 1;
            }

        }

        if($flag_engagement === 1){
            $disponible_display = 1;
            $credit_budgetaires_credit = $perdiem->solde_avant_op;
            
        }        

        if(Session::has('profils_id')){ 

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        

        $type_profils_lists = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Responsable Caisse'];
        
        $etape = "create";
        
        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if ($type_profil != null) {
            $type_profils_name = $type_profil->name;
        }else{
            return redirect()->back()->with('error','Profil introuvable');
        }

        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null);

        //dd($profil);
        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        
        //$code_structure = null;
        $user_connect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

        if ($user_connect != null) {
            //$code_structure = $user_connect->code_structure;
        }

        $familles = Famille::all();

        $gestions = DB::table('gestions')
                        ->get();

        $gestion_default = DB::table('gestions')
                        ->where('code_gestion','G')
                        ->first();

        $structures = $this->getStructures();

        $agents = DB::table('agents as a')
            ->join('users as u','u.agents_id','=','a.id')
            ->where('u.flag_actif',1)
            ->whereIn('a.id', function($query){
                $query->select(DB::raw('ase.agents_id'))
                        ->from('agent_sections as ase')
                        ->whereRaw('a.id = ase.agents_id');
            })
            ->get();

        $statut_perdiem = $this->getLastStatutPerdiem($perdiem->id);
        
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }

        $value_bouton = null;
        $bouton = null;
        $alert_bouton = null; 

        $value_bouton2 = null;
        $bouton2 = null;
        $alert_bouton2 = null; 

        $value_bouton3 = null;
        $bouton3 = null;
        $alert_bouton3 = null; 
        $griser = 1;
        $edite_signataire = null;

        $entete = "MODIFICATION DE PERDIEMS";
        if ($type_profils_name === 'Gestionnaire des achats') {
            
            if ($statut_perdiem->libelle === 'Soumis pour validation' or $statut_perdiem->libelle === 'Annulé (Gestionnaire des achats)') {

                $value_bouton = "modifier";
                $bouton = "Modifier";
                $alert_bouton = "Faut-il modifier ?"; 

                $value_bouton2 = "annuler";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 

                $value_bouton3 = "transmettre_ra";
                $bouton3 = "Transmettre";
                $alert_bouton = "Faut-il transmettre la demande au Responsable des achats ?"; 

                $griser = null;


            }elseif ($statut_perdiem->libelle === 'Validé' or $statut_perdiem->libelle === 'Édité') {
                $value_bouton = "editer";
                $bouton = "Éditer";
                $alert_bouton = "Faut-il éditer la demande ?"; 

                $value_bouton2 = null;
                $bouton2 = null;
                $alert_bouton = ""; 

                $value_bouton3 = "transmettre_comptabilite";
                $bouton3 = "Transmettre";
                $alert_bouton = "Faut-il transmettre la demande au Responsable DFC ?"; 

                
                $griser = 1;

                $edite_signataire = 1;


            }
        }elseif ($type_profils_name === 'Responsable des achats') {

            if ($statut_perdiem->libelle === 'Transmis (Responsable des achats)' or $statut_perdiem->libelle === 'Annulé (Responsable DMP)') {

                $entete = "VALIDATION DE PERDIEMS";
                $value_bouton = "valider_ra";
                $bouton = "Valider";
                $alert_bouton = "Faut-il valider ?"; 

                $value_bouton2 = "annuler_ra";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 

            }
        }elseif ($type_profils_name === 'Responsable DMP') {
            if ($statut_perdiem->libelle === 'Transmis (Responsable DMP)' or $statut_perdiem->libelle === 'Annulé (Responsable contrôle budgetaire)') {

                $entete = "VALIDATION DE PERDIEMS";

                $value_bouton = "valider_r_cmp";
                $bouton = "Valider";
                $alert_bouton = "Faut-il valider ?"; 

                $value_bouton2 = "annuler_r_cmp";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 

            }
        }elseif ($type_profils_name === 'Responsable contrôle budgetaire') {
            if ($statut_perdiem->libelle === 'Transmis (Responsable contrôle budgetaire)' or $statut_perdiem->libelle === 'Annulé (Chef Département DCG)') {

                $entete = "VALIDATION DE PERDIEMS";

                $value_bouton = "valider_r_b_dcg";
                $bouton = "Valider";
                $alert_bouton = "Faut-il valider ?"; 

                $value_bouton2 = "annuler_r_b_dcg";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 

            }
        }elseif ($type_profils_name === 'Chef Département DCG') {
            if ($statut_perdiem->libelle === 'Transmis (Chef Département DCG)' or $statut_perdiem->libelle === 'Annulé (Responsable DCG)') {

                $entete = "VALIDATION DE PERDIEMS";

                $value_bouton = "valider_c_d_dcg";
                $bouton = "Valider";
                $alert_bouton = "Faut-il valider ?"; 

                $value_bouton2 = "annuler_c_d_dcg";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 

            }
        }elseif ($type_profils_name === 'Responsable DCG') {

            if ($statut_perdiem->libelle === 'Transmis (Responsable DCG)' or $statut_perdiem->libelle === 'Annulé (Directeur Général Adjoint)') {

                $entete = "VALIDATION DE PERDIEMS";

                $value_bouton = "valider_r_dcg";
                $bouton = "Valider";
                $alert_bouton = "Faut-il valider ?"; 
                $value_bouton2 = "annuler_r_dcg";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 
            }

        }elseif ($type_profils_name === 'Directeur Général Adjoint') {

            if ($statut_perdiem->libelle === 'Transmis (Directeur Général Adjoint)') {

                $entete = "VALIDATION DE PERDIEMS";

                $value_bouton = "valider_r_dgaaf";
                $bouton = "Valider";
                $alert_bouton = "Faut-il valider ?"; 
                $value_bouton2 = "annuler_r_dgaaf";
                $bouton2 = "Annuler";
                $alert_bouton = "Faut-il annuler la demande ?"; 
            }

        }elseif ($type_profils_name === 'Responsable DFC') {

            if ($statut_perdiem->libelle === 'Transmis (Responsable DFC)') {

                $entete = "AUTORISATION DE PAIEMENT DE PERDIEMS";

                $value_bouton = "valider_r_dfc";
                $bouton = "Autoriser";
                $alert_bouton = "Faut-il valider ?"; 
                /*$value_bouton2 = "annuler_r_dfc";
                $bouton2 = "Annuler";
                $alert_bouton2 = "Faut-il annuler la demande ?"; */
            }

        }elseif ($type_profils_name === 'Responsable Caisse') {

            if ($statut_perdiem->libelle === 'Accord pour paiement') {

                $entete = "PAIEMENT DE PERDIEMS";

                $value_bouton = "valider_r_caisse";
                $bouton = "Payer";
                $alert_bouton = "Faut-il valider ?"; 
                /*$value_bouton2 = "annuler_r_caisse";
                $bouton2 = "Annuler";
                $alert_bouton2 = "Faut-il annuler la demande ?"; */
            }

        }

        $signataires = $this->getAgentFonctionsSignatairePerdiem($perdiem->id);
        $agent_fonctions = $this->getAgentFonctions();    

        return view('perdiems.edit',[
            'familles'=>$familles,
            'gestions'=>$gestions,
            'gestion_default'=>$gestion_default,
            'credit_budgetaires'=>$credit_budgetaires,
            'structures'=>$structures,
            'structure_perdiem'=>$structure,
            'agents'=>$agents,
            'detail_perdiems'=>$detail_perdiems,
            'perdiem'=>$perdiem,
            'piece_jointes'=>$piece_jointes,
            'verou_compte'=>$verou_compte,
            'statut_perdiem'=>$statut_perdiem,
            'type_profils_name'=>$type_profils_name,
            'value_bouton'=>$value_bouton,
            'value_bouton2'=>$value_bouton2,
            'value_bouton3'=>$value_bouton3,
            'bouton'=>$bouton,
            'bouton2'=>$bouton2,
            'bouton3'=>$bouton3,
            'alert_bouton'=>$alert_bouton,
            'alert_bouton2'=>$alert_bouton2,
            'alert_bouton3'=>$alert_bouton3,
            'griser'=>$griser,
            'disponible_display'=>$disponible_display,
            'disponible'=>$disponible,
            'famille_perdiem'=>$famille,
            'gestion_perdiem'=>$gestion,
            'entete'=>$entete,
            'edite_signataire'=>$edite_signataire,
            'signataires'=>$signataires,
            'agent_fonctions'=>$agent_fonctions,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Perdiem  $perdiem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perdiem $perdiem)
    {
        $signatureController = new SignatureController();
        $type_operations_libelle = 'Perdiems';

        $request->validate([
            'perdiems_id'=>['required','numeric'],
            'num_or'=>['required','numeric'],
            'perdiems_intitule'=>['required','string'],
            'code_gestion'=>['required','string'],
            'libelle_gestion'=>['required','string'],
            'code_structure'=>['required','numeric'],
            'nom_structure'=>['required','string'],
            'credit_budgetaires_id'=>['required','numeric'],
            'ref_fam'=>['required','numeric'],
            'design_fam'=>['required','string'],
            'solde_avant_op'=>['required','string'],
            'nom_prenoms'=>['required','array'],
            'montant_bis'=>['required','array'],
            'montant_total'=>['required','string'],
            'submit'=>['required','string'],
        ]);

        $libelle = null;
        $success = null;
        $error = null;

        $solde_avant_op = filter_var($request->solde_avant_op, FILTER_SANITIZE_NUMBER_INT);

        $exercice = $this->getExercice();

        if ($exercice === null) {
            return redirect()->back()->with('error','Exercice introuvable');
        }

        $num_or = $request->num_or;

        $signataires = $this->getAgentFonctionsSignatairePerdiem($request->perdiems_id);

        $nombre_de_signataire_old = count($signataires);

        $perdiem = $this->getPerdiem($request->perdiems_id);        
        if($perdiem != null){
            $ref_fam = $perdiem->ref_fam;
            $exercice_pdm = $perdiem->exercice;
            $code_structure = $perdiem->code_structure;
            $code_gestion = $perdiem->code_gestion;
        }

        if ($request->submit === 'modifier' or $request->submit === 'annuler' or $request->submit === 'transmettre_ra') {

            $num_pdm = $this->getLastNumPdm($exercice->exercice,$request->code_structure,$request->perdiems_id);

            $this->setPerdiem($num_pdm,$request->perdiems_id,$request->perdiems_intitule,$num_or,$request->code_gestion,$exercice->exercice,$request->ref_fam,$request->code_structure,$solde_avant_op,$request->credit_budgetaires_id,filter_var($request->montant_total, FILTER_SANITIZE_NUMBER_INT));

            $detail_perdiem = null;

            if ($perdiem != null) {

                if (isset($request->detail_perdiems_id)) {

                    if (count($request->detail_perdiems_id) > 0) {

                        $this->deleteDetailPerdiemByArrayNotIn($perdiem->id,$request->detail_perdiems_id);
                        
                    }

                }
                
                if (count($request->nom_prenoms) > 0 ) {

                    foreach ($request->nom_prenoms as $key => $value) {
        
                        $piece_identite = null;
        
                        $piece_identite_name = null;

                        $setDetailPerdiem =  1;
        
                        if (isset($request->piece_identite[$key])) {
                
                            $piece_identite =  $request->piece_identite[$key]->store('piece_identite','public');
        
                            $piece_identite_name = $request->piece_identite[$key]->getClientOriginalName();   
                            
                        }else{
                                        
                            if (isset($request->piece_identite_flag[$key])) {

                                if ($request->piece_identite_flag[$key] == 1) {

                                    $setDetailPerdiem =  2;

                                }elseif ($request->piece_identite_flag[$key] == 0){

                                    $setDetailPerdiem =  1;
                                    $piece_identite = null;
                                    $piece_identite_name = null;
                                    
                                } 
                                
                            }else{
                                $piece_identite = null;
                                $piece_identite_name = null;
                            }
                        }
                        
                        if (isset($request->detail_perdiems_id[$key])) {
                            
                            if ($setDetailPerdiem === 1) {
                                $detail_perdiem = $this->setDetailPerdiem($request->detail_perdiems_id[$key],$perdiem->id,$request->nom_prenoms[$key],$request->montant_bis[$key],$piece_identite,$piece_identite_name);
                            }elseif ($setDetailPerdiem === 2) {
                                $detail_perdiem = $this->setDetailPerdiem2($request->detail_perdiems_id[$key],$perdiem->id,$request->nom_prenoms[$key],$request->montant_bis[$key]);
                            }
                            
                        }else{
                            $detail_perdiem = $this->storeDetailPerdiem($perdiem->id,$request->nom_prenoms[$key],$request->montant_bis[$key],$piece_identite,$piece_identite_name);
                        }
        
                        
                        
                    }
                }

                $piece_jointes = DB::table('piece_jointes as pj')
                ->join('type_operations as to','to.id','=','pj.type_operations_id')
                ->where('to.libelle','Perdiems')
                ->where('pj.subject_id',$request->perdiems_id)
                ->select('pj.id')
                ->get();
                foreach ($piece_jointes as $piece_jointe) {
                    
                    if (isset($request->piece_jointes_id[$piece_jointe->id])) {

                        $flag_actif = 1;

                        $piece_jointes_id= $piece_jointe->id;

                        if (isset($request->piece_flag_actif[$piece_jointes_id])) {
                            $flag_actif = 1;
                        }else{
                            $flag_actif = 0;
                        }

                        
                        $libelle_piece = "Perdiems";
                        $piece = null;
                        $name = null;
                        
                        $dataPiece = [
                            'subject_id'=>$perdiem->id,
                            'profils_id'=>Session::get('profils_id'),
                            'libelle'=>$libelle_piece,
                            'piece'=>$piece,
                            'flag_actif'=>$flag_actif,
                            'name'=>$name,
                            'piece_jointes_id'=>$piece_jointes_id,
                        ];

                        $this->controller3->procedureStorePieceJointe($dataPiece);
                        
                    }
                }

                if (isset($request->piece)) {

                    if (count($request->piece) > 0) {

                        foreach ($request->piece as $item => $value) {
                            if (isset($request->piece[$item])) {

                                $piece =  $request->piece[$item]->store('piece_jointe','public');

                                $name = $request->piece[$item]->getClientOriginalName();

                                $libelle = "Perdiems";
                                $flag_actif = 1;
                                $piece_jointes_id = null;

                                if (isset($request->piece_jointes_id[$item])) {

                                    $piece_jointes_id= $request->piece_jointes_id[$item];

                                    if (isset($request->piece_flag_actif[$request->piece_jointes_id[$item]])) {
                                        $flag_actif = 1;
                                    }else{
                                        $flag_actif = 0;
                                    }

                                }
                                $dataPiece = [
                                    'subject_id'=>$perdiem->id,
                                    'profils_id'=>Session::get('profils_id'),
                                    'libelle'=>$libelle,
                                    'piece'=>$piece,
                                    'flag_actif'=>$flag_actif,
                                    'name'=>$name,
                                    'piece_jointes_id'=>$piece_jointes_id,
                                ];

                                $this->controller3->procedureStorePieceJointe($dataPiece);
                                
                            }
                        }
                        

                    }

                }
                
            }else{
                return redirect()->back()->with('error',$error);
            }
        }else{
            $detail_perdiem = 1;
        }

        if ($detail_perdiem != null) {

            if (isset($request->submit)) {

                $data = [
                    'profils_id'=>Session::get('profils_id'),
                    'users_id'=>auth()->user()->id,
                    'perdiems_id'=>$perdiem->id
                ];

                if ($request->submit === 'annuler' or $request->submit === 'annuler_ra' or $request->submit === 'annuler_r_cmp' or $request->submit === 'annuler_c_d_dcg' or $request->submit === 'annuler_r_b_dcg' or $request->submit === "annuler_r_dcg" or $request->submit === 'annuler_r_dgaaf'){
                    $request->validate([
                        'commentaire'=>['required','string'],
                    ]);
                }
                if ($request->submit === 'annuler') {
                    $libelle = 'Annulé (Gestionnaire des achats)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Gestionnaire des achats';

                    $type_profils_names = ['Gestionnaire des achats'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'modifier') {
                    $libelle = 'Soumis pour validation';
                    $success = "Modification effectuée";
                    $error = "Modification non effectuée";

                    $subject = 'Modification de la demande de perdiems par le Gestionnaire des achats';

                    $type_profils_names = ['Gestionnaire des achats'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'transmettre_ra') {

                    
                    $libelle = 'Transmis (Responsable des achats)';
                    $success = "Transmission effectuée";
                    $error = "Transmission non effectuée";

                    $subject = 'Transmission de la demande de perdiems';

                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'annuler_ra') {

                    $libelle = 'Annulé (Responsable des achats)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Responsable des achat';                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_ra') {
                    $libelle = 'Validé (Responsable des achats)';
                    $libelle_send = 'Transmis (Responsable DMP)';
                    $success = "Validation effectuée";
                    $error = "Validation non effectuée";

                    $subject = 'Transmission de la demande de perdiems au Responsable DMP';

                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'annuler_r_cmp') {

                    $libelle = 'Annulé (Responsable DMP)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Responsable DMP';    
                    
                    if($perdiem->flag_engagement === 1){
                        $data_solde_avant_operation = [
                            'type_operations_libelle'=>$type_operations_libelle,
                            'operations_id'=>$perdiem->id,
                            'solde_avant_op'=>$solde_avant_op,
                            'flag_engagement'=>0
                        ];
                        $this->setSoldeAvantOperation($data_solde_avant_operation);
                        
                        $type_piece = "ENG";
                        $signe = -1;
                        $data_all = [
                            'profils_id'=>Session::get('profils_id'),
                            'type_operations_libelle'=>$type_operations_libelle,
                            'operations_id'=>$perdiem->id,
                            'type_piece'=>$type_piece,
                            'signe'=>$signe,
                            'flag_acompte'=>0
                        ];

                        $data_comptabilisation = $this->procedureComptabilisationEcriture($data_all);
                        if($data_comptabilisation != null){
                            $this->storeComptabilisationEcriture($data_comptabilisation);
                        }
                        $data_param = [
                            'type_piece'=>$type_piece,
                            'compte'=>$ref_fam,
                            'exercice'=>$exercice_pdm,
                            'code_structure'=>$code_structure,
                            'code_gestion'=>$code_gestion,
                        ];
                        $operation_non_interfacee = $this->getOperationEncoursNonComptabilisee($data_param); 

                        $this->setCreditBudgetaireConsoNonComptabiliseByArray($operation_non_interfacee);
                    }

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_r_cmp') {
                    
                    $libelle = 'Validé (Responsable DMP)';
                    $libelle_send = 'Transmis (Responsable contrôle budgetaire)';
                    $success = "Validation effectuée";
                    $error = "Validation non effectuée";

                    $subject = 'Transmission de la demande de perdiems au Responsable contrôle budgetaire';

                    $data_solde_avant_operation = [
                        'type_operations_libelle'=>$type_operations_libelle,
                        'operations_id'=>$perdiem->id,
                        'solde_avant_op'=>$solde_avant_op,
                        'flag_engagement'=>1
                    ];
                    $this->setSoldeAvantOperation($data_solde_avant_operation);

                    $type_piece = "ENG";
                    $signe = 1;
                    $data_all = [
                        'profils_id'=>Session::get('profils_id'),
                        'type_operations_libelle'=>$type_operations_libelle,
                        'operations_id'=>$perdiem->id,
                        'type_piece'=>$type_piece,
                        'signe'=>$signe,
                        'flag_acompte'=>0
                    ];

                    $data_comptabilisation = $this->procedureComptabilisationEcriture($data_all);
                    
                    if($data_comptabilisation != null){
                        $this->storeComptabilisationEcriture($data_comptabilisation);
                    }
                    $data_param = [
                        'type_piece'=>$type_piece,
                        'compte'=>$ref_fam,
                        'exercice'=>$exercice_pdm,
                        'code_structure'=>$code_structure,
                        'code_gestion'=>$code_gestion,
                    ];
                    $operation_non_interfacee = $this->getOperationEncoursNonComptabilisee($data_param); 

                    $this->setCreditBudgetaireConsoNonComptabiliseByArray($operation_non_interfacee);
                    $this->procedureStoreSignatairePerdiemParValidateur($data);

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'annuler_r_b_dcg') {

                    $libelle = 'Annulé (Responsable contrôle budgetaire)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Responsable contrôle budgetaire';                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_r_b_dcg') {
                    $libelle = 'Validé (Responsable contrôle budgetaire)';
                    $libelle_send = 'Transmis (Chef Département DCG)';
                    $success = "Validation effectuée";
                    $error = "Validation non effectuée";

                    $subject = 'Transmission de la demande de perdiems au Chef Département DCG';

                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'annuler_c_d_dcg') {

                    $libelle = 'Annulé (Chef Département DCG)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Chef Département DCG';                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_c_d_dcg') {
                    $libelle = 'Validé (Chef Département DCG)';
                    $libelle_send = 'Transmis (Responsable DCG)';
                    $success = "Validation effectuée";
                    $error = "Validation non effectuée";

                    $subject = 'Transmission de la demande de perdiems au Responsable DCG';

                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'annuler_r_dcg') {

                    $libelle = 'Annulé (Responsable DCG)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Responsable DCG';                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_r_dcg') {

                    $montant_total = filter_var($request->montant_total,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
               
                    $info = 'Montant total';
                    $error = $this->setDecimal($montant_total,$info);

                    if (isset($error)) {
                        return redirect()->back()->with('error',$error);
                    }

                    $this->procedureStoreSignatairePerdiemParValidateur($data);
                    

                    if ($montant_total > 250000) {

                        $libelle = 'Validé (Responsable DCG)';
                        $libelle_send = 'Transmis (Directeur Général Adjoint)';
                        $success = "Validation effectuée";
                        $error = "Validation non effectuée";

                        $subject = 'Transmission de la demande de perdiems au Directeur Général Adjoint';                        

                        $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];
                        
    
                    }
                    
                    if ($montant_total <= 250000){

                        $libelle = 'Validé';
                        //$libelle_send = 'Transmis (Directeur Général Adjoint)';
                        $success = "Demande de perdiems validée";
                        $error = "Demande de perdiems non validée";

                        $subject = 'Demande de perdiems validée';

                        $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];

                        $data = [
                            'type_operations_libelle'=>$type_operations_libelle,
                            'operations_id'=>$perdiem->id,
                            'reference'=>$perdiem->num_pdm,
                            'extension'=>'pdf',
                            'signature'=>1
                        ];                        
                        $signatureController->OrdreDeSignature($data);
                    }

                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);
                    

                }elseif ($request->submit === 'annuler_r_dgaaf') {

                    $libelle = 'Annulé (Directeur Général Adjoint)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Directeur Général Adjoint';                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_r_dgaaf') {
                    $libelle = 'Validé';
                    //$libelle_send = 'Transmis (Responsable DFC)';
                    $success = "Validation effectuée";
                    $error = "Validation non effectuée";

                    //$subject = 'Transmission de la demande de perdiems au Responsable DFC';
                    $subject = 'Demande de perdiems validée';

                    $this->procedureStoreSignatairePerdiemParValidateur($data);

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];

                    $data = [
                        'type_operations_libelle'=>$type_operations_libelle,
                        'operations_id'=>$perdiem->id,
                        'reference'=>$perdiem->num_pdm,
                        'extension'=>'pdf',
                        'signature'=>1
                    ];
                    $signatureController->OrdreDeSignature($data);

                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);
                }elseif($request->submit === 'editer'){

                    $libelle = 'Édité';
                    //$libelle_send = 'Transmis (Responsable DFC)';
                    $success = "Demande de perdiems éditée";
                    $error = "Demande de perdiems non éditée";

                    $signature = $this->verificationSignataire($signataires,$request->profil_fonctions_id);
                    if($signature != 0){
                        $this->storeSignatairePerdiem($request,$perdiem->id,Session::get('profils_id'));
                    }

                    $subject = "Demande de perdiems éditée";

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];

                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif($request->submit === 'transmettre_comptabilite'){

                    $libelle = 'Édité';
                    $libelle_send = 'Transmis (Responsable DFC)';
                    $success = "Transmission de la demande de perdiems au Responsable DFC";
                    $error = "Echec de la transmission de la demande de perdiems au Responsable DFC";

                    $this->storeSignatairePerdiem($request,$perdiem->id,Session::get('profils_id'));

                    $subject = 'Transmission de la demande de perdiems au Responsable DFC';

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'annuler_r_dfc') {

                    $libelle = 'Annulé (Responsable DFC)';
                    $success = "Annulation effectuée";
                    $error = "Annulation non effectuée";

                    $subject = 'Annulation de la demande de perdiems par le Responsable DFC';                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_r_dfc') {
                    $libelle = 'Accord pour paiement';
                    //$libelle_send = 'Transmis (Responsable Caisse)';
                    $success = "Validation effectuée";
                    $error = "Validation non effectuée";

                    $subject = 'Transmission de la demande de perdiems à la caisse pour paiement';

                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Responsable Caisse'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }elseif ($request->submit === 'valider_r_caisse') {
                    $libelle = 'Payé';
                    //$libelle_send = 'Transmis (Responsable Caisse)';
                    $success = "Paiement effectué";
                    $error = "Paiement non effectué";

                    $subject = 'Paiement de perdiems effectué';

                    

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Responsable Caisse'];
                    $this->notifPerdiems($subject,$perdiem->id,$type_profils_names);

                }
            }

            $this->storeTypeStatutPerdiem($libelle);
            $type_statut_perdiem = $this->getTypeStatutPerdiem($libelle);

            if ($type_statut_perdiem != null) {

                $commentaire = $request->commentaire;
                $date_debut = date('Y-m-d');
                $date_fin = null;

                $this->setLastStatutPerdiem($perdiem->id);

                $this->storeStatutPerdiem($perdiem->id,$type_statut_perdiem->id,$date_debut,$date_fin,Session::get('profils_id'),$commentaire);
                
            }

            if(isset($libelle_send)){
                $this->storeTypeStatutPerdiem($libelle_send);
                $type_statut_perdiem = $this->getTypeStatutPerdiem($libelle_send);

                if ($type_statut_perdiem != null) {

                    $commentaire = $request->commentaire;
                    $date_debut = date('Y-m-d');
                    $date_fin = null;

                    $this->setLastStatutPerdiem($perdiem->id);

                    $this->storeStatutPerdiem($perdiem->id,$type_statut_perdiem->id,$date_debut,$date_fin,Session::get('profils_id'),$commentaire);
                    
                }
            }

            return redirect('/perdiems/index')->with('success',$success);

        }else{

            return redirect()->back()->with('error',$error);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Perdiem  $perdiem
     * @return \Illuminate\Http\Response
     */
    public function destroy(Perdiem $perdiem)
    {
        //
    }
}
