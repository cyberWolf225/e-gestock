<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Agent;
use App\Jobs\SendEmail;
use App\Models\Famille;
use App\Models\Gestion;
use App\Models\Structure;
use App\Models\DemandeFond;
use App\Models\PieceJointe;
use Illuminate\Http\Request;
use App\Models\MoyenPaiement;
use App\Models\TypeOperation;
use App\Models\CotationService;
use App\Models\CreditBudgetaire;
use App\Models\StatutDemandeFond;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\TypeStatutSignataire;
use Illuminate\Support\Facades\Http;
use App\Models\SignataireDemandeFond;
use App\Models\TypeStatutDemandeFond;
use Illuminate\Support\Facades\Crypt;
use App\Models\DemandeFondBonCommande;
use App\Models\StatutCreditBudgetaire;
use Illuminate\Support\Facades\Session;
use App\Models\TypeStatutCreditBudgetaire;
use App\Models\StatutSignataireDemandeFond;
use Illuminate\Contracts\Encryption\DecryptException;

class DemandeFondController extends Controller
{
    private $controller3;
    public function __construct(Controller3 $controller3)
    {
        $this->middleware('auth');
        $this->controller3 = $controller3;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){

        $this->storeSessionBackground($request);
        if(Session::has('profils_id')){

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        
        
        $etape = "index";
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $code_structure = $this->structure_connectee();

        $libelle = null;
        $acces_create = null;
        if ($type_profils_name === 'Pilote AEE') {
            $acces_create = 1;
            $libelle = ['Soumis pour validation','Édité'];
        }elseif ($type_profils_name === 'Administrateur fonctionnel') {
            $libelle = ['Soumis pour validation'];
        }elseif ($type_profils_name === 'Gestionnaire des achats') {
            $acces_create = 1;
            $libelle = ['Imputé (Gestionnaire des achats)'];
        }elseif ($type_profils_name === 'Responsable des achats') {
            $libelle = ['Transmis (Responsable des achats)'];
        }elseif ($type_profils_name === 'Responsable DMP') {
            $libelle = ['Transmis (Responsable DMP)','Demande de cotation (Transmis Responsable DMP)'];
        }elseif ($type_profils_name === 'Responsable contrôle budgetaire') {
            $libelle = ['Transmis (Responsable contrôle budgetaire)'];
        }elseif ($type_profils_name === 'Chef Département DCG') {
            $libelle = ['Transmis (Chef Département Contrôle Budgétaire)'];
        }elseif ($type_profils_name === 'Responsable DCG') {
            $libelle = ['Transmis (Responsable DCG)'];
        }elseif ($type_profils_name === 'Directeur Général Adjoint') {
            $libelle = ['Transmis (Directeur Général Adjoint)'];
        }elseif ($type_profils_name === 'Directeur Général') {
            $libelle = ['Transmis (Directeur Général)'];
        }elseif ($type_profils_name === 'Responsable DFC') {
            $libelle = ['Transmis (Responsable DFC)'];
        }elseif ($type_profils_name === 'Agent Cnps') {
            $libelle = ['Accordé'];
        }

        

        $demande_fonds = $this->getDemandeFonds($type_profils_name,$code_structure,$libelle);


        return view('demande_fonds.index',[
            'type_profils_name'=>$type_profils_name,
            'demande_fonds'=>$demande_fonds,
            'acces_create'=>$acces_create
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
        $controllerTravaux = new ControllerTravaux();
        $disponible = null;
        
        $structures = [];

        if(Session::has('profils_id')){ 

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $nbre_bc = 0;

        
        // determination du profil de l'emetteur

        $etape = "create";
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);  
        
        $beneficiaires = null;

        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $ref_depot = $infoUserConnect->ref_depot;
                

                if($type_profils_name === 'Pilote AEE') {

                    $code_structure = $infoUserConnect->code_structure;

                    $structures = $this->getStructuresByRefDepot($ref_depot,$code_structure);

                    $beneficiaires = $this->getBeneficiaires($type_profils_name,$code_structure);
                }elseif($type_profils_name === 'Gestionnaire des achats'){
                    $structures = $this->getStructuresByRefDepot($ref_depot);

                    $beneficiaires = $this->getBeneficiaires($type_profils_name);
                }

                
            }
        }

        if ($beneficiaires === null) {
            return redirect()->back()->with('error','Bénéficiaire non determiné');
        }

        $exercices = $this->getExercice();

         if($exercices === null){
            return redirect()->back()->with('error','Exercice clôturé ou non ouvert');
        }

        $charge_suivis = $this->getChargeSuivi();        
        
        $familles = DB::table('familles')->get();
        $gestions = $this->getGestion();
        
        return view('demande_fonds.create',[
            'structures' => $structures,
            'beneficiaires' => $beneficiaires,
            'charge_suivis'=>$charge_suivis,
            'exercices'=>$exercices,
            'familles'=>$familles,
            'gestions'=>$gestions,
            'disponible'=>$disponible,
            'nbre_bc'=>$nbre_bc
        ]);
    }
    
    public function create_credit($crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null)
    {
        $controllerTravaux = new ControllerTravaux();

        $decrypted_ref_fam = null;
        $decrypted_code_structure = null;
        $decrypted_code_gestion = null;
        try {
            $decrypted_ref_fam = Crypt::decryptString($crypted_ref_fam);
            $decrypted_code_structure = Crypt::decryptString($crypted_code_structure);
            $decrypted_code_gestion = Crypt::decryptString($crypted_code_gestion);
        } catch (DecryptException $e) {
            //
        }

        

        $famille = Famille::where('ref_fam',$decrypted_ref_fam)->first();
        if($famille === null){
            return redirect('/demande_fonds/create')->with('error', 'Veuillez saisir un compte valide');
        }

        $structure = Structure::where('code_structure',$decrypted_code_structure)->first();
        if($structure === null){
            return redirect('/demande_fonds/create')->with('error', 'Veuillez saisir une structure valide');
        }

        $gestion = Gestion::where('code_gestion',$decrypted_code_gestion)->first();
        if($gestion === null){
            return redirect('/demande_fonds/create')->with('error', 'Veuillez saisir une gestion valide');
        }

        //dd($famille,$structure,$gestion);

        if (Session::has('profils_id')) {

            $etape = "create";

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Pilote AEE','Gestionnaire des achats'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $nbre_bc = 0;
        $structures = [];

        $beneficiaires = null;

        $disponible = null;

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
        if ($infoUserConnect != null) {

                $ref_depot = $infoUserConnect->ref_depot;

                if($type_profils_name === 'Pilote AEE') {
                    $code_structure = $infoUserConnect->code_structure;

                    $structures = $this->getStructuresByRefDepot($ref_depot,$code_structure);

                    $beneficiaires = $this->getBeneficiaires($type_profils_name,$code_structure);
                }elseif($type_profils_name === 'Gestionnaire des achats'){
                    $structures = $this->getStructuresByRefDepot($ref_depot);

                    $beneficiaires = $this->getBeneficiaires($type_profils_name);
                }

                
            
        }

        $exercices = $this->getExercice();

        //dd($famille->ref_fam,$code_structure,$exercices->exercice);
        

        if($famille != null && $structure != null && $exercices != null && $gestion != null){
            
            try {
                
                 $param = $exercices->exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;

                 //http://127.0.0.1:8000/api/credit_budgetaire_b/2023-G-9108-630100
     
                 $this->storeCreditBudgetaireByWebService($param,$structure->ref_depot);
                 $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercices->exercice);
     
                 
             } catch (\Throwable $th) {
                 
             }
        }

        $credit_budgetaires_select = null;

        if($famille != null){
            $credit_budgetaires_select = $this->getFamilleById($famille->id);
        }

        $charge_suivis = $this->getChargeSuivi();

        $gestions = $this->getGestion();
                        
        $griser = null;

        $familles = $this->getFamille();
        $sections = [];
        if ($structure != null) {
            $sections = $this->getSectionById($structure->code_structure);
        }

        return view('demande_fonds.create',[
            'gestions'=>$gestions,
            'griser'=>$griser,
            'structures'=>$structures,
            'exercices'=>$exercices,
            'familles'=>$familles,
            'disponible'=>$disponible,
            'beneficiaires'=>$beneficiaires,
            'charge_suivis'=>$charge_suivis,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'structure_default'=>$structure,
            'gestion_default'=>$gestion,
            'sections'=>$sections,
            'nbre_bc'=>$nbre_bc
        ]);
    }

    public function crypt($ref_fam,$code_structure,$code_gestion){
        
        return redirect('demande_fonds/create/'.Crypt::encryptString($ref_fam).'/'.Crypt::encryptString($code_structure).'/'.Crypt::encryptString($code_gestion).'');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $controllerTravaux = new ControllerTravaux();
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $validate = $request->validate([
            'submit' => ['required','string'],
        ]);

        

        if (isset($request->submit)) {
            if ($request->submit === 'enregistrer') {
                // determination du profil de l'emetteur

                $etape = "store";
                $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
                $profils = $this->controlAcces($type_profils_name, $etape, auth()->user()->id);

                if ($profils!=null) {
                    $profils_id = $profils->id;
                } else {
                    return redirect()->back()->with('error', 'Accès refusé');
                }
                
                $this->validate_request($request);

                

                $credit_budgetaire = $this->getCreditBudgetaireById2($request->credit_budgetaires_id);

                if ($credit_budgetaire!=null) {
                    $ref_fam = $credit_budgetaire->ref_fam;
                    $solde_avant_op = $credit_budgetaire->credit;
                    $credit_budgetaires_id = $credit_budgetaire->credit_budgetaires_id;
                    $exercice = $credit_budgetaire->exercice;
                } else {
                    return redirect()->back()->with('error', 'Compte à imputer introuvable');
                }

                // transformation du montant

                $montant = filter_var($request->montant, FILTER_SANITIZE_NUMBER_INT);
                
                try {
                    $montant = $montant * 1;
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Veuillez saisir un montant correct');
                }

                if (gettype($montant)!='integer') {
                    return redirect()->back()->with('error', 'Veuillez saisir un montant correct');
                }

                // controle du montant
                
                if ($solde_avant_op > 0) {
                    if ($montant > $solde_avant_op) {
                        return redirect()->back()->with('error', "Le montant de l'opération ne peut être supérieur au solde disponible avant l'opération");
                    }
                } else {
                    return redirect()->back()->with('error', 'Solde insuffisant');
                }

                

                // determination du n° demande
                //n° d'ordre

                $num_dem = $this->getLastNumDem($exercice,$request->code_structure);

                // determination du profil du bénéficiaire
                // determination du crédit budgetaire
                
                $profils_id_beneficiare = $this->setBeneficiaire($request->agents_id_beneficiaire);

                if ($profils_id_beneficiare != null) {
                
                    $demande_fonds_id = $this->storeDemandeFond($num_dem, $request->code_section, $profils_id_beneficiare, $solde_avant_op, $request->agents_id, $credit_budgetaires_id, $ref_fam, $exercice, $request->intitule, $montant, $request->observation,$request->code_gestion);

                    if ($demande_fonds_id!=null) {
                        $commentaire = null;

                        if (isset($request->piece)) {
                            if (count($request->piece) > 0) {
                                foreach ($request->piece as $item => $value) {
                                    if (isset($request->piece[$item])) {
                                        $piece =  $request->piece[$item]->store('piece_jointe', 'public');
        
                                        $name = $request->piece[$item]->getClientOriginalName();
        
                                        $libelle = "Demande de fonds";
                                        $flag_actif = 1;
                                        $piece_jointes_id = null;
        
                                        $this->piece_jointe($demande_fonds_id, $profils_id, $libelle, $piece, $flag_actif, $name, $piece_jointes_id);
                                    }
                                }
                            }
                        }

                        // statut de la demande de fonds
                        if($type_profils_name === 'Gestionnaire des achats'){
                            $libelle = 'Imputé (Gestionnaire des achats)';
                        }elseif($type_profils_name === 'Pilote AEE'){
                            $libelle = 'Soumis pour validation';
                        }
                        

                        if (isset($request->commentaire)) {
                            $commentaire = $request->commentaire;
                        } else {
                            $commentaire = null;
                        }

                        $this->storeStatutDemandeFond($libelle, $demande_fonds_id, $profils_id, $commentaire);

                        $subject = 'Création de demande de fonds';
                        $email = auth()->user()->email;
                            
                        $this->sendEmailUserConnect($email, $subject, $demande_fonds_id);
                        //

                        return redirect('demande_fonds/index')->with('success', 'Demande de fonds enregistré');
                    }else {
                        return redirect()->back()->with('error', 'Enregistrement de la demande de fonds echoué');
                    }

                }else {
                    return redirect()->back()->with('error', 'Bénéfificiaire invalide');
                }

            }
        }

        
         
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DemandeFond  $demandeFond
     * @return \Illuminate\Http\Response
     */

    public function show($demandeFond)
    {
        $controllerTravaux = new ControllerTravaux();
        $famille = null;
        $structure = null;
        $gestion = null;
        $disponible = null;
        $disponible_display = null;
        $structures = [];
        $beneficiaires = [];

        $nbre_bc = 0;

        $route = null;

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeFond);
        } catch (DecryptException $e) {
            //
        }

        $demandeFond = DemandeFond::findOrFail($decrypted); 

        $entete = 'Demande de fonds';
        $value_bouton = null;
        $bouton = null;
        $verou_commentaire = null;
        $bouton2 = null;
        $value_bouton2 = null;
        $bouton3 = null;
        $value_bouton3 = null;
        $griser = null;
        $edition_pilote = null;
        $edition_charge_suivi = null;

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        $statut_demande_fond = $this->getLastStatutDemandeFond($demandeFond->id);

        if ($statut_demande_fond!=null) {
            $libelle = $statut_demande_fond->libelle;
            $commentaire = $statut_demande_fond->commentaire;
            $profil_commentaire = $statut_demande_fond->name;
            $nom_prenoms_commentaire = $statut_demande_fond->nom_prenoms;
        }
        
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $num_bc = null;

        $demande_fond_bon_commande = $this->getDemandeFondBonCommandeById($demandeFond->id);

        $etape = "show";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$demandeFond);
        
        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {

                    $ref_depot = $infoUserConnect->ref_depot;

                    if($type_profils_name === 'Pilote AEE') {
                        $code_structure = $infoUserConnect->code_structure;

                        $structures = $this->getStructuresByRefDepot($ref_depot,$code_structure);

                        $beneficiaires = $this->getBeneficiaires($type_profils_name,$code_structure);
                    }elseif($type_profils_name === 'Gestionnaire des achats'){
                        $structures = $this->getStructuresByRefDepot($ref_depot);

                        $beneficiaires = $this->getBeneficiaires($type_profils_name);
                    }

                    
                
            }
        }

        $section = $this->getSectionByCodeSection($demandeFond->code_section);

        $sections = [];


        $demande_fond = $this->getDemandeFond($demandeFond->id);

        $charge_suivi_save = $this->getChargeSuiviFirst($demandeFond->id);       
        
        if ($demande_fond_bon_commande!=null) {
            $num_bc = $demande_fond_bon_commande->num_bc;

            $operations_id_crypt = Crypt::encryptString($demande_fond_bon_commande->operations_id);


            if($demande_fond_bon_commande->libelle === "Demande d'achats"){

                /*$route = 'demande_achats/edit/'.$operations_id_crypt;
                if($griser != null){
                    $route = 'demande_achats/show/'.$operations_id_crypt;
                }*/
                $statut_demande_achat = $this->getLastStatutDemandeAchat($demande_fond_bon_commande->operations_id);

                if ($statut_demande_achat!=null) {

                    $cotation_fournisseurs_id_crypt = null;

                    $selection_adjudication = DB::table('selection_adjudications as sa')
                    ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
                    ->where('cf.demande_achats_id',$demande_fond_bon_commande->operations_id)
                    ->first();
                    if ($selection_adjudication!=null) {

                        $cotation_fournisseurs_id_crypt = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);
                    }

                    $route = $this->getRouteBc($demande_fond_bon_commande->libelle,$statut_demande_achat->libelle,$type_profils_name,$operations_id_crypt,$cotation_fournisseurs_id_crypt);

                }

            }elseif($demande_fond_bon_commande->libelle === "Commande non stockable"){

                /*$route = 'travaux/edit/'.$operations_id_crypt;
                if($griser != null){
                    $route = 'travaux/show/'.$operations_id_crypt;
                }*/

                $statut_travauxe = $controllerTravaux->getLastStatutTravaux($demande_fond_bon_commande->operations_id);

                if ($statut_travauxe!=null) {

                    $route = $this->getRouteBc($demande_fond_bon_commande->libelle,$statut_travauxe->libelle,$type_profils_name,$operations_id_crypt);
                }

                

            }

            
        }

        
        $num_bc_desactiver = null;

        $piece_jointes = $this->getPieceJointe($demandeFond->id);

        $libelle_moyen_paiement = $this->getMoyenPaiement($demandeFond->id);

        $edit_signataire = $this->editDemandeFond($demandeFond->id);

        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
        ->join('fonctions as f','f.id','=','pf.fonctions_id')
        ->where('u.flag_actif',1)
        ->where('pf.flag_actif',1)
        ->select(DB::raw('a.mle'),DB::raw('a.nom_prenoms'),DB::raw('f.libelle'))
        ->groupBy('f.libelle','a.mle','a.nom_prenoms')
        ->get();
        
        $signataires = $this->getSignataireDemandeFond($demandeFond->id);

        $credit_budgetaires_select = null;

        if($famille != null){
            $credit_budgetaires_select = $this->getFamilleById($famille->id);
        }

        $charge_suivis = $this->getChargeSuivi();

        $gestions = $this->getGestion();

        $familles = $this->getFamille();
        $active_commentaire = 0;
        $griser = 1;

        return view('demande_fonds.show',[
            'demande_fond' => $demande_fond,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'num_bc'=>$num_bc,
            'num_bc_desactiver'=>$num_bc_desactiver,
            'charge_suivi_save'=>$charge_suivi_save,
            'charge_suivis'=>$charge_suivis,
            'piece_jointes'=>$piece_jointes,
            'type_profils_name'=>$type_profils_name,
            'entete'=>$entete,
            'value_bouton'=>$value_bouton,
            'bouton'=>$bouton,
            'verou_commentaire'=>$verou_commentaire,
            'value_bouton2'=>$value_bouton2,
            'bouton2'=>$bouton2,
            'value_bouton3'=>$value_bouton3,
            'bouton3'=>$bouton3,
            'griser'=>$griser,
            'libelle_moyen_paiement'=>$libelle_moyen_paiement,
            'edit_signataire'=>$edit_signataire,
            'agents'=>$agents,
            'signataires'=>$signataires,
            'edition_pilote'=>$edition_pilote,
            'edition_charge_suivi'=>$edition_charge_suivi,
            'gestions'=>$gestions,
            'gestion'=>$gestion,
            'structure'=>$structure,
            'structures'=>$structures,
            'beneficiaires'=>$beneficiaires,
            'sections'=>$sections,
            'familles'=>$familles,
            'famille'=>$famille,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'disponible'=>$disponible,
            'disponible_display'=>$disponible_display,
            'route'=>$route,
            'nbre_bc'=>$nbre_bc,
            'active_commentaire'=>$active_commentaire

        ]);

    }
    /**
     * Show the form for editing the specified resource.

     *
     * @param  \App\DemandeFond  $demandeFond
     * @return \Illuminate\Http\Response
     */
    public function edit($demandeFond, Request $request, $limited=null,$crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null)
    {
        $controllerTravaux = new ControllerTravaux();
        $famille = null;
        $structure = null;
        $gestion = null;
        $disponible = null;
        $disponible_display = null;
        $structures = [];
        $beneficiaires = [];

        $nbre_bc = 0;

        $route = null;
        $flag_engagement = 0;

        if($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null){

            $disponible_display = 1;

            $famille = Famille::where('ref_fam',$crypted_ref_fam)->first();
            
            
            if($famille === null){
                return redirect('/demande_fonds/edit/'.$demandeFond)->with('error', 'Veuillez saisir un compte valide');
            }

            $structure = Structure::where('code_structure',$crypted_code_structure)->first();
            if($structure === null){
                return redirect('/demande_fonds/edit/'.$demandeFond)->with('error', 'Veuillez saisir une structure valide');
            }

            $gestion = Gestion::where('code_gestion',$crypted_code_gestion)->first();
            if($gestion === null){
                return redirect('/demande_fonds/edit/'.$demandeFond)->with('error', 'Veuillez saisir une gestion valide');
            }


        }
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeFond);
        } catch (DecryptException $e) {
            //
        }

        $demandeFond = DemandeFond::findOrFail($decrypted); 

        $demande_fond = $this->getDemandeFond($demandeFond->id);
        if($demande_fond != null){
            $flag_engagement = $demande_fond->flag_engagement;
        }

        $entete = 'Demande de fonds';
        $value_bouton = null;
        $bouton = null;
        $verou_commentaire = null;
        $bouton2 = null;
        $value_bouton2 = null;
        $bouton3 = null;
        $value_bouton3 = null;
        $griser = null;
        $edition_pilote = null;
        $edition_charge_suivi = null;
        $credit_budgetaires_credit = null;

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        $statut_demande_fond = $this->getLastStatutDemandeFond($demandeFond->id);

        if ($statut_demande_fond!=null) {
            $libelle = $statut_demande_fond->libelle;
            $commentaire = $statut_demande_fond->commentaire;
            $profil_commentaire = $statut_demande_fond->name;
            $nom_prenoms_commentaire = $statut_demande_fond->nom_prenoms;
        }
        
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));


        $url_complet = $request->url();
        
        $tableau = explode('demande_fonds/',$url_complet);

        $url2 = null;
        if (isset($tableau[1])) {
            $url2 = $tableau[1];
        }
        
        $tableau2 = null;

        if ($url2 != null) {
            $tableau2 = explode('/',$url2);
        }

        $page_view = null;

        if (isset($tableau2[0])) {
            $page_view = $tableau2[0];
        }

        $num_bc = null;

        $demande_fond_bon_commande = $this->getDemandeFondBonCommandeById($demandeFond->id);
        $information_sur_bon_de_commande_rattache = "Le bouton 'viser' n'apparait pas parceque vous avez une action de controlle à effectuer sur le bon de commande rattaché à cette demande... Pour accéder à ce bon : Veuillez cliquer sur le lien du bon de commande rattaché.";

        $affiche_info = null;
        if ($type_profils_name === 'Pilote AEE') {

            if ($libelle != 'Soumis pour validation' && $libelle != 'Annulé' && $libelle != 'Annulé (Responsable des achats)' && $libelle != 'Annulé (Gestionnaire des achats)' && $libelle != 'Édité') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Soumis pour validation' or $libelle === 'Annulé' or $libelle === 'Annulé (Responsable des achats)' or $libelle === 'Annulé (Gestionnaire des achats)' or $libelle === 'Édité') {
                
                if ($page_view === 'edit') {
                    $entete = 'Modification de la demande de fonds';

                    $verou_commentaire = 1;
                    //$edition_pilote = 1;

                    $bouton = 'Modifier';
                    $value_bouton = 'modifier';

                    if ($libelle === 'Édité') {

                        $verou_commentaire = null;
                        $bouton = 'Retirer';
                        $value_bouton = 'retirer';
                        $griser = 1;
                        
                    }
                }elseif ($page_view === 'send') {
                    $entete = 'Transmission de la demande au Gestionnaire des achats';

                    //$verou_commentaire = 1;

                    $bouton = 'Transmettre';
                    $value_bouton = 'imputer_g_achat';
                }
                

                if ($libelle != 'Annulé' && $libelle != 'Édité') {
                    $bouton2 = 'Annuler';
                    $value_bouton2 = 'annuler_benef';
                }

            }



        }elseif ($type_profils_name === 'Gestionnaire des achats') {

            if ($libelle != 'Imputé (Gestionnaire des achats)' && $libelle != 'Annulé (Gestionnaire des achats)' && $libelle != 'Édité (Gestionnaire des achats)' && $libelle != 'Annulé (Responsable des achats)' && $libelle != 'Validé' && $libelle != 'Édité') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Imputé (Gestionnaire des achats)' or $libelle === 'Annulé (Gestionnaire des achats)' or $libelle === 'Édité (Gestionnaire des achats)' or $libelle === 'Annulé (Responsable des achats)' or $libelle === 'Validé' or $libelle === 'Édité') {

                $entete = 'Édition de la demande de fonds';

                $bouton = 'Éditer';


                if ( $libelle === 'Validé' or $libelle === 'Édité') {
                    $value_bouton = 'edition_g_achat2'; 

                    $griser = 1;
                }else{
                    $value_bouton = 'edition_g_achat'; 
                    $bouton3 = 'Transmettre';
                    $value_bouton3 = 'transmettre_r_achat'; 

                    if ($libelle != 'Annulé (Gestionnaire des achats)') {
                        $bouton2 = 'Annuler';
                        $value_bouton2 = 'annuler_g_achat';
                    }
                }

                                
            }

        }elseif ($type_profils_name === 'Responsable des achats') {

            if ($libelle != 'Transmis (Responsable des achats)' && $libelle != 'Visé (Responsable des achats)' && $libelle != 'Demande de cotation' && $libelle != 'Demande de cotation (Annulé)' && $libelle != 'Demande de cotation (Validé)' && $libelle != 'Annulé (Responsable DMP)' && $libelle != 'Transmis pour cotation' && $libelle != 'Coté' && $libelle != 'Fournisseur sélectionné' && $libelle !='Rejeté (Responsable DMP)') {
                return redirect()->back()->with('error','Accès refusé');
            } 

            if ($libelle === 'Transmis (Responsable des achats)' or $libelle === 'Visé (Responsable des achats)' or $libelle === 'Demande de cotation' or $libelle === 'Demande de cotation (Annulé)' or $libelle === 'Demande de cotation (Validé)' or $libelle === 'Annulé (Responsable DMP)' or $libelle === 'Transmis pour cotation' or $libelle === 'Coté' or $libelle === 'Fournisseur sélectionné' or $libelle ==='Rejeté (Responsable DMP)') {

                $entete = 'Visa de la demande de fonds';

                if($libelle === 'Coté'){
                    $entete = 'Sélection de la cotation du mieux disant';
                }elseif ($libelle === "Fournisseur sélectionné" or $libelle==='Rejeté (Responsable DMP)'){
                    $entete = 'Transfert de la cotation du mieux disant au Responsable DMP pour validation';
                }

                if ($demande_fond_bon_commande!=null) {

                    if($demande_fond_bon_commande->libelle === "Commande non stockable"){

                        if ($libelle != 'Visé (Responsable des achats)') {
                            $bouton = 'Viser';
                            $value_bouton = 'viser_r_achat';
                        }

                    }

                    if($demande_fond_bon_commande->libelle === "Demande d'achats"){
                        $affiche_info = 1;
                    }
                }else{
                    if ($libelle != 'Visé (Responsable des achats)') {
                        $bouton = 'Viser';
                        $value_bouton = 'viser_r_achat';
                    }
                }
               
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_r_achat';
                $griser = 1;
                
            }

        }elseif ($type_profils_name === 'Responsable DMP') {

            if ($libelle != 'Transmis (Responsable DMP)' && $libelle != 'Demande de cotation (Transmis Responsable DMP)' && $libelle != 'Signé (Responsable DMP)' && $libelle != 'Annulé (Responsable contrôle budgetaire)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable DMP)' or $libelle === 'Demande de cotation (Transmis Responsable DMP)' or $libelle === 'Signé (Responsable DMP)' or $libelle === 'Annulé (Responsable contrôle budgetaire)') {

                $entete = 'Visa de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'signer_r_cmp';

                
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_r_cmp';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Responsable contrôle budgetaire') {

            if ($libelle != 'Transmis (Responsable Contrôle Budgétaire)' && $libelle != 'Visé (Responsable Contrôle Budgétaire)' && $libelle != 'Annulé (Chef Département Contrôle Budgétaire)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable Contrôle Budgétaire)' or $libelle === 'Visé (Responsable Contrôle Budgétaire)' or $libelle === 'Annulé (Chef Département Contrôle Budgétaire)') {

                $entete = 'Visa de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'viser_r_cb';
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_r_cb';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Chef Département DCG') {

            if ($libelle != 'Transmis (Chef Département Contrôle Budgétaire)' && $libelle != 'Visé (Chef Département Contrôle Budgétaire)' && $libelle != 'Annulé (Responsable DCG)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Chef Département Contrôle Budgétaire)' or $libelle === 'Visé (Chef Département Contrôle Budgétaire)' or $libelle === 'Annulé (Responsable DCG)') {

                $entete = 'Visa de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'viser_d_dcg';
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_d_dcg';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Responsable DCG') {

            if ($libelle != 'Transmis (Responsable DCG)' && $libelle != 'Signé (Responsable DCG)' && $libelle != 'Annulé (Directeur Général Adjoint)' && $libelle != 'Annulé (Responsable DFC)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable DCG)' or $libelle === 'Signé (Responsable DCG)' or $libelle === 'Annulé (Directeur Général Adjoint)' or $libelle === 'Annulé (Responsable DFC)') {

                $entete = 'Signature de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'viser_r_dcg';
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_r_dcg';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Directeur Général Adjoint') {

            if ($libelle != 'Transmis (Directeur Général Adjoint)' && $libelle != 'Visé (Directeur Général Adjoint)' && $libelle != 'Annulé (Directeur Général)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Directeur Général Adjoint)' or $libelle === 'Visé (Directeur Général Adjoint)' or $libelle === 'Annulé (Directeur Général)') {

                $entete = 'Visa de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'viser_r_dgaaf';
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_r_dgaaf';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Directeur Général') {

            if ($libelle != 'Transmis (Directeur Général)' && $libelle != 'Visé (Directeur Général)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Directeur Général)' or $libelle === 'Visé (Directeur Général)') {

                $entete = 'Visa de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'viser_r_dg';
                $bouton2 = 'Rejeter';
                $value_bouton2 = 'rejeter_r_dg';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Responsable DFC') {

            if ($libelle != 'Transmis (Responsable DFC)' && $libelle != 'Visé (Responsable DFC)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable DFC)' or $libelle === 'Visé (Responsable DFC)') {

                $entete = 'Visa de la demande de fonds';
                $bouton = 'Viser';
                $value_bouton = 'viser_r_dfc';
                //$bouton2 = 'Rejeter';
                //$value_bouton2 = 'rejeter_r_dfc';
                $griser = 1;

                
            }

        }elseif ($type_profils_name === 'Agent Cnps') {
            
            if ($demandeFond->terminer === 1) {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle != 'Accordé' && $libelle != 'Dépense justifiée') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Accordé' or $libelle === 'Dépense justifiée') {

                
                $entete = 'Justification de l\'utilisation des fonds accordés';
                $bouton = 'Enregistrer';
                $value_bouton = 'justifier';

                if ($demandeFond->terminer === 0) {
                    $bouton2 = 'Terminer';
                    $value_bouton2 = 'terminer';
                }

                $griser = 1;
                $edition_charge_suivi = 1;
                // $edition_pilote = 1;

                

                
            }

        }else {
            return redirect()->back()->with('error','Accès refusé');
        }

        $etape = "edit";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$demandeFond);
        
        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {

                    $ref_depot = $infoUserConnect->ref_depot;

                    if($type_profils_name === 'Pilote AEE') {
                        $code_structure = $infoUserConnect->code_structure;

                        $structures = $this->getStructuresByRefDepot($ref_depot,$code_structure);

                        $beneficiaires = $this->getBeneficiaires($type_profils_name,$code_structure);
                    }elseif($type_profils_name === 'Gestionnaire des achats'){
                        $structures = $this->getStructuresByRefDepot($ref_depot);

                        $beneficiaires = $this->getBeneficiaires($type_profils_name);
                    }

                    
                
            }
        }

        $section = $this->getSectionByCodeSection($demandeFond->code_section);

        $sections = [];

        
        $demande_achats = [];
        $travauxes = [];
        

        if($demandeFond != null && $crypted_ref_fam === null && $crypted_code_structure === null && $crypted_code_gestion === null){

            
            if($demandeFond != null){
                try {

                    if($section != null){
                        $param = $demandeFond->exercice.'-'.$demandeFond->code_gestion.'-'.$section->code_structure.'-'.$demandeFond->ref_fam;
                        
                        if($flag_engagement === 0){
                            $this->storeCreditBudgetaireByWebService($param, $demandeFond->ref_depot);
                        }
                        
                    } 
                    
                    

                } catch (\Throwable $th) {
                }
            }

            if ($section != null) {
                $disponible = $this->getCreditBudgetaireDisponible($demandeFond->ref_fam, $section->code_structure, $demandeFond->code_gestion, $demandeFond->exercice);

                if($disponible != null){
                    $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;                
                }

                $sections = $this->getSectionById($section->code_structure);
                
            }

            $disponible_display = 1;

            $demande_achats = $this->getDemandeAchatByexercice($demandeFond->exercice);
            $travauxes = $this->getTravauxByexercice($demandeFond->exercice);

            $nbre_bc = count($demande_achats) + count($travauxes);

        }

        $exercices = $this->getExercice();

        if($exercices === null){
            return redirect()->back()->with('error','Exercice clôturé ou non ouvert');
        }


        if($famille != null && $structure != null && $exercices != null && $gestion != null){
            try {
                $param = $exercices->exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;

                if($flag_engagement === 0){
                   $this->storeCreditBudgetaireByWebService($param, $structure->ref_depot); 
                }
                

                $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam, $structure->code_structure, $gestion->code_gestion, $exercices->exercice);

                if($disponible != null){
                    $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;                
                }

                if ($structure != null) {
                    $sections = $this->getSectionById($structure->code_structure);
                }

                $demande_achats = $this->getDemandeAchatByexercice($exercices->exercice);
                $travauxes = $this->getTravauxByexercice($exercices->exercice);
                 
            } catch (\Throwable $th) {
                
            }

        }

        if($flag_engagement === 1){
            $credit_budgetaires_credit = $demande_fond->solde_avant_op;
        }

        $charge_suivi_save = $this->getChargeSuiviFirst($demandeFond->id);       
        
        if ($demande_fond_bon_commande!=null) {
            $num_bc = $demande_fond_bon_commande->num_bc;

            $operations_id_crypt = Crypt::encryptString($demande_fond_bon_commande->operations_id);


            if($demande_fond_bon_commande->libelle === "Demande d'achats"){

                /*$route = 'demande_achats/edit/'.$operations_id_crypt;
                if($griser != null){
                    $route = 'demande_achats/show/'.$operations_id_crypt;
                }*/
                $statut_demande_achat = $this->getLastStatutDemandeAchat($demande_fond_bon_commande->operations_id);

                if ($statut_demande_achat!=null) {

                    $cotation_fournisseurs_id_crypt = null;

                    $selection_adjudication = DB::table('selection_adjudications as sa')
                    ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
                    ->where('cf.demande_achats_id',$demande_fond_bon_commande->operations_id)
                    ->first();
                    if ($selection_adjudication!=null) {

                        $cotation_fournisseurs_id_crypt = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);
                    }

                    $route = $this->getRouteBc($demande_fond_bon_commande->libelle,$statut_demande_achat->libelle,$type_profils_name,$operations_id_crypt,$cotation_fournisseurs_id_crypt);

                }

            }elseif($demande_fond_bon_commande->libelle === "Commande non stockable"){

                /*$route = 'travaux/edit/'.$operations_id_crypt;
                if($griser != null){
                    $route = 'travaux/show/'.$operations_id_crypt;
                }*/

                $statut_travauxe = $controllerTravaux->getLastStatutTravaux($demande_fond_bon_commande->operations_id);

                if ($statut_travauxe!=null) {

                    $route = $this->getRouteBc($demande_fond_bon_commande->libelle,$statut_travauxe->libelle,$type_profils_name,$operations_id_crypt);
                }

                

            }

            
        }

        
        $num_bc_desactiver = null;

        $piece_jointes = $this->getPieceJointe($demandeFond->id);

        $libelle_moyen_paiement = $this->getMoyenPaiement($demandeFond->id);

        $edit_signataire = $this->editDemandeFond($demandeFond->id);

        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
        ->join('fonctions as f','f.id','=','pf.fonctions_id')
        ->where('u.flag_actif',1)
        ->where('pf.flag_actif',1)
        ->select(DB::raw('a.mle'),DB::raw('a.nom_prenoms'),DB::raw('f.libelle'))
        ->groupBy('f.libelle','a.mle','a.nom_prenoms')
        ->get();
        
        $signataires = $this->getSignataireDemandeFond($demandeFond->id);

        $credit_budgetaires_select = null;

        if($famille != null){
            $credit_budgetaires_select = $this->getFamilleById($famille->id);
        }

        $charge_suivis = $this->getChargeSuivi();

        $gestions = $this->getGestion();

        $familles = $this->getFamille();
        $active_commentaire = 0;
        if(isset($value_bouton) or isset($value_bouton2) or isset($value_bouton3)){
            $active_commentaire = 1;
        }

        return view('demande_fonds.edit',[
            'demande_fond' => $demande_fond,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'num_bc'=>$num_bc,
            'num_bc_desactiver'=>$num_bc_desactiver,
            'charge_suivi_save'=>$charge_suivi_save,
            'charge_suivis'=>$charge_suivis,
            'piece_jointes'=>$piece_jointes,
            'type_profils_name'=>$type_profils_name,
            'entete'=>$entete,
            'value_bouton'=>$value_bouton,
            'bouton'=>$bouton,
            'verou_commentaire'=>$verou_commentaire,
            'value_bouton2'=>$value_bouton2,
            'bouton2'=>$bouton2,
            'value_bouton3'=>$value_bouton3,
            'bouton3'=>$bouton3,
            'griser'=>$griser,
            'libelle_moyen_paiement'=>$libelle_moyen_paiement,
            'edit_signataire'=>$edit_signataire,
            'agents'=>$agents,
            'signataires'=>$signataires,
            'edition_pilote'=>$edition_pilote,
            'edition_charge_suivi'=>$edition_charge_suivi,
            'gestions'=>$gestions,
            'gestion'=>$gestion,
            'structure'=>$structure,
            'structures'=>$structures,
            'beneficiaires'=>$beneficiaires,
            'sections'=>$sections,
            'familles'=>$familles,
            'famille'=>$famille,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'disponible'=>$disponible,
            'disponible_display'=>$disponible_display,
            'demande_achats'=>$demande_achats,
            'travauxes'=>$travauxes,
            'route'=>$route,
            'nbre_bc'=>$nbre_bc,
            'active_commentaire'=>$active_commentaire,
            'information_sur_bon_de_commande_rattache'=>$information_sur_bon_de_commande_rattache,
            'affiche_info'=>$affiche_info,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit

        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DemandeFond  $demandeFond
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $controllerPerdiem = new ControllerPerdiem();
        $controllerTravaux = new ControllerTravaux();
        $signatureController = new SignatureController();
        $type_operations_libelle_signature = "Demande de fonds" ;
        $flag_engagement = 0;
        $ordre_engagement = null;
        $montant_acompte = null;
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        

        $request->validate([
            'submit' => ['required','string'],
        ]);

        if(isset($request->moyen_paiment)){
            if ($request->moyen_paiment === 'Chèque') {
                $validate = $request->validate([
                'num_bc'=>['required','string']
                ]);
            }
        }

        if(isset($request->demande_fonds_id)){
            $demande_fonds_id = $request->demande_fonds_id;
        }

        if(isset($request->credit)){
            $solde_avant_op = $request->credit;
        }

        $demande_fond = $this->getDemandeFond($request->demande_fonds_id);
        if($demande_fond != null){
            if($demande_fond->flag_engagement === 1){
                $solde_avant_op = $demande_fond->solde_avant_op;
            }
        }

        $type_profils_name_valide = null;

        if (isset($request->submit)) {

            $etape = $request->submit;

            if ($etape === "modifier" or $etape === "annuler_benef" or $etape === "imputer_g_achat" or $etape === "retirer") {
                $type_profils_name_valide = "Pilote AEE";
            }elseif ($etape === "annuler_g_achat" or $etape === "edition_g_achat" or $etape === "transmettre_g_achat" or $etape === "transmettre_r_achat" or $etape === "edition_g_achat2") {
                $type_profils_name_valide = "Gestionnaire des achats";
            }elseif ($etape === "rejeter_r_achat" or $etape === "viser_r_achat") {
                $type_profils_name_valide = "Responsable des achats";
            }elseif ($etape === "signer_r_cmp" or $etape === "rejeter_r_cmp") {
                $type_profils_name_valide = "Responsable DMP";
            }elseif ($etape === "viser_r_cb" or $etape === "rejeter_r_cb") {
                $type_profils_name_valide = "Responsable contrôle budgetaire";
            }elseif ($etape === "viser_d_dcg" or $etape === "rejeter_d_dcg") {
                $type_profils_name_valide = "Chef Département DCG";
            }elseif ($etape === "viser_r_dcg" or $etape === "rejeter_r_dcg") {
                $type_profils_name_valide = "Responsable DCG";
            }elseif ($etape === "viser_r_dgaaf" or $etape === "rejeter_r_dgaaf") {
                $type_profils_name_valide = "Directeur Général Adjoint";
            }elseif ($etape === "viser_r_dg" or $etape === "rejeter_r_dg") {
                $type_profils_name_valide = "Directeur Général";
            }elseif ($etape === "viser_r_dfc" or $etape === "rejeter_r_dfc") {
                $type_profils_name_valide = "Responsable DFC";
            }elseif ($etape === "justifier" or $etape === "terminer") {
                $type_profils_name_valide = "Agent Cnps";
            }

        }


        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $libelle_old = null;

        $statut_demande_fond_old = $this->getLastStatutDemandeFond($request->demande_fonds_id);

        if ($statut_demande_fond_old!=null) {
            $libelle_old = $statut_demande_fond_old->libelle;
        }

        $liaison = $this->getDemandeFondBonCommandeById($request->demande_fonds_id);

        $num_bc = null;

        if(isset($request->moyen_paiment)){
            if($request->moyen_paiment === "Espèce"){
                if($liaison != null){
                    $num_bc = $liaison->num_bc;
                }
            }
        }

        if (isset($request->submit)) {

            $montant = filter_var($request->montant, FILTER_SANITIZE_NUMBER_INT);
            
            try {
                $montant = $montant * 1;
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Veuillez saisir un montant correct');
            }

            if (gettype($montant)!='integer') {
                return redirect()->back()->with('error', 'Veuillez saisir un montant correct');
            }

            
            if ($request->submit === 'modifier' or $request->submit === 'imputer_g_achat'){
                // determination du profil de l'emetteur
                if ($request->submit === 'modifier') {
                    $etape = "modifier";
                }elseif ($request->submit === 'imputer_g_achat') {
                    $etape = "imputer_g_achat";
                }

                $profils = $this->controlAcces($type_profils_name_valide,$etape,auth()->user()->id,$request);

                
                
                if($profils===null){
                    return redirect()->back()->with('error','Accès refusé');
                }else{
                    $profils_id = $profils->id;
                }

                $this->validate_request($request);

                $credit_budgetaire = $this->getCreditBudgetaireById2($request->credit_budgetaires_id);

                if ($credit_budgetaire!=null) {
                    $ref_fam = $credit_budgetaire->ref_fam;
                    $credit_budgetaires_id = $credit_budgetaire->credit_budgetaires_id;
                    $exercice = $credit_budgetaire->exercice;
                    //$solde_avant_op = $credit_budgetaire->credit - $credit_budgetaire->consommation_non_interfacee;
                }else{
                    return redirect()->back()->with('error','Compte à imputer introuvable');
                }

                $demande_fonds_id = $request->demande_fonds_id;
                
                if ($solde_avant_op > 0) {
                    if ($montant > $solde_avant_op) {
                        return redirect()->back()->with('error','Solde insuffisant');
                    }
                }else{
                    return redirect()->back()->with('error','Solde insuffisant');
                }
               
                // determination du crédit budgetaire

                $ref_fam = $request->ref_fam;   
                
                if ($request->submit === 'modifier') {
                    $message = 'Modification de la demande de fonds echouée';
                }elseif ($request->submit === 'imputer_g_achat') {
                    $message = 'Imputation echouée';
                }
                
                $profils_id_beneficiare = $this->setBeneficiaire($request->agents_id_beneficiaire);

                if ($profils_id_beneficiare != null) {

                    $num_dem = null;

                    
                    if($demande_fond != null){

                        $explode_num_dem = explode('/',explode('DF', $demande_fond->num_dem)[0])[1];
                        if($explode_num_dem === $request->code_structure){
                            
                            $num_dem = $demande_fond->num_dem;

                        }else{
                            $exercices = $this->getExercice();

                            $num_dem = $this->getLastNumDem($exercices->exercice,$request->code_structure,$request->demande_fonds_id);
                        

                        }

                    }

                    if($num_dem === null){
                        return redirect()->back()->with('error','Numéro DF introuvable');
                    }

                    $demande_fonds_id = $this->setDemandeFond($demande_fonds_id, $request->code_section, $profils_id_beneficiare, $solde_avant_op, $request->agents_id, $credit_budgetaires_id, $ref_fam, $exercice, $request->intitule, $montant, $request->observation,$request->code_gestion,$num_dem);
                
            
                    if ($demande_fonds_id!=null) {

                    // statut de la demande de fonds
                    

                        // détacher fichier joint
                        $piece_jointes = DB::table('piece_jointes as pj')
                        ->join('type_operations as to', 'to.id', '=', 'pj.type_operations_id')
                        ->where('to.libelle', 'Demande de fonds')
                        ->where('pj.subject_id', $demande_fonds_id)
                        ->select('pj.id')
                        ->get();
                        foreach ($piece_jointes as $piece_jointe) {
                            if (isset($request->piece_jointes_id[$piece_jointe->id])) {
                                $flag_actif = 1;

                                $piece_jointes_id= $piece_jointe->id;

                                if (isset($request->piece_flag_actif[$piece_jointes_id])) {
                                    $flag_actif = 1;
                                } else {
                                    $flag_actif = 0;
                                }

                                
                                $libelle = "Demande de fonds";
                                $piece = null;
                                $name = null;

                                $this->piece_jointe($demande_fonds_id, $profils_id, $libelle, $piece, $flag_actif, $name, $piece_jointes_id);
                            }
                        }

                        if (isset($request->piece)) {
                            if (count($request->piece) > 0) {
                                foreach ($request->piece as $item => $value) {
                                    if (isset($request->piece[$item])) {
                                        $piece =  $request->piece[$item]->store('piece_jointe', 'public');
        
                                        $name = $request->piece[$item]->getClientOriginalName();
        
                                        $libelle = "Demande de fonds";

                                        $piece_jointes_id = null;

                                        $flag_actif = 1;

                                        if (isset($request->piece_jointes_id[$item])) {
                                            $piece_jointes_id= $request->piece_jointes_id[$item];

                                            if (isset($request->piece_flag_actif[$request->piece_jointes_id[$item]])) {
                                                $flag_actif = 1;
                                            } else {
                                                $flag_actif = 0;
                                            }
                                        }

                                        
                                        
                                        
                                        $this->piece_jointe($demande_fonds_id, $profils_id, $libelle, $piece, $flag_actif, $name, $piece_jointes_id);
                                    }
                                }
                            }
                        }

                        // statut de la demande de fonds
                        

                        if ($request->submit === 'modifier') {
                            
                            $libelle = 'Soumis pour validation';
                            $subject = 'Modification de demande de fonds';

                            $email = auth()->user()->email;
                        
                            $this->sendEmailUserConnect($email, $subject, $demande_fonds_id);

                            $message = 'Demande de fonds modifiée';
                        } elseif ($request->submit === 'imputer_g_achat') {
                            $libelle = 'Imputé (Gestionnaire des achats)';
                            $subject = 'Imputation de la demande de fonds au Gestionnaire des achats';
                            $message = 'Imputation reussie';
                            // utilisateur connecté
                            $email = auth()->user()->email;
                            $this->notifUserConnectDF($email, $subject, $demande_fonds_id);
                            //

                            // Responsable DMP
                            $this->notif_respo_cmp($subject, $demande_fonds_id);
                            //

                            // Gestionnaire des achats
                            $this->notif_gestionnaire_achat($subject, $demande_fonds_id);
                            //

                            // Responsable des achats
                            $this->notif_respo_achat($subject, $demande_fonds_id);
                            //
                        }

                        if (isset($request->commentaire)) {
                            $commentaire = $request->commentaire;
                        } else {
                            $commentaire = null;
                        }

                        $this->storeStatutDemandeFond($libelle, $demande_fonds_id, $profils_id, $commentaire);

                       
                        

                        
                    
                        //

                        return redirect('demande_fonds/index')->with('success', $message);
                    } else {
                        return redirect()->back()->with('error', $message);
                    }
                }else{
                    return redirect()->back()->with('error', $message);
                }


            }elseif ($request->submit === 'annuler_benef' or $request->submit === 'annuler_g_achat' or $request->submit === 'rejeter_r_achat' or $request->submit === 'rejeter_r_cmp' or $request->submit === 'rejeter_r_cb' or $request->submit === 'rejeter_d_dcg' or $request->submit === 'rejeter_r_dcg' or $request->submit === 'rejeter_r_dgaaf' or $request->submit === 'rejeter_r_dg' or $request->submit === 'rejeter_r_dfc'){

                $request->validate([
                    'commentaire' => ['required','string'],
                ]);
            
                // determination du profil de l'emetteur
                if ($request->submit === 'annuler_benef') {
                    $etape = "annuler_benef";
                }elseif ($request->submit === 'annuler_g_achat') {
                    $etape = "annuler_g_achat";
                }elseif ($request->submit === 'rejeter_r_achat') {
                    $etape = "rejeter_r_achat";
                }elseif ($request->submit === 'rejeter_r_cmp') {
                    $etape = "rejeter_r_cmp";
                }elseif ($request->submit === 'rejeter_r_cb') {
                    $etape = "rejeter_r_cb";
                }elseif ($request->submit === 'rejeter_d_dcg') {
                    $etape = "rejeter_d_dcg";
                }elseif ($request->submit === 'rejeter_r_dcg') {
                    $etape = "rejeter_r_dcg";
                }elseif ($request->submit === 'rejeter_r_dgaaf') {
                    $etape = "rejeter_r_dgaaf";
                }elseif ($request->submit === 'rejeter_r_dg') {
                    $etape = "rejeter_r_dg";
                }elseif ($request->submit === 'rejeter_r_dfc') {
                    $etape = "rejeter_r_dfc";
                }
                
                $profils = $this->controlAcces($type_profils_name_valide,$etape,auth()->user()->id,$request);

                
                
                if($profils===null){
                    return redirect()->back()->with('error','Accès refusé');
                }else{
                    $profils_id = $profils->id;
                }

                $this->validate_request($request);

                $credit_budgetaire = $this->getCreditBudgetaireById2($request->credit_budgetaires_id);

                if ($credit_budgetaire!=null) {
                    $ref_fam = $credit_budgetaire->ref_fam;
                    $credit_budgetaires_id = $credit_budgetaire->credit_budgetaires_id;
                    $exercice = $credit_budgetaire->exercice;
                    $solde_avant_op = $credit_budgetaire->credit;
                }else{
                    return redirect()->back()->with('error','Compte à imputer introuvable');
                }

                $demande_fonds_id = $request->demande_fonds_id;
                
                if ($solde_avant_op > 0) {
                    if ($montant > $solde_avant_op) {
                        return redirect()->back()->with('error','Solde insuffisant');
                    }
                }else{
                    return redirect()->back()->with('error','Solde insuffisant');
                }

                // determination du crédit budgetaire

                if ($request->submit === 'annuler_benef') {
                    if ($solde_avant_op === null) {
                        return redirect()->back()->with('error', 'Annulation impossible');
                    }
                }
            
                if ($credit_budgetaire!=null) {

                    if (isset($request->commentaire)) {
                        $commentaire = $request->commentaire;
                    }else {
                        $commentaire = null;
                    }
                    
                    $demande_fonds_id = DemandeFond::where('id',$request->demande_fonds_id)->first()->id;

                    if ($request->submit === 'annuler_benef') {
                        DemandeFond::where('id', $demande_fonds_id)->update([
                        'observation' => $request->observation,
                        ]);

                    
                    }


                    // statut de la demande de fonds
                        if ($request->submit === 'annuler_benef') {
                            $libelle = 'Annulé';
                        }elseif ($request->submit === 'annuler_g_achat') {
                            $libelle = 'Annulé (Gestionnaire des achats)';

                            $libelle_bc = 'Annulé (Gestionnaire des achats)';
                            
                        }elseif ($request->submit === 'rejeter_r_achat') {
                            $libelle = 'Annulé (Responsable des achats)';
                            $libelle_bc = 'Annulé (Responsable des achats)';

                        }elseif ($request->submit === 'rejeter_r_cmp') {

                            if($libelle_old === 'Demande de cotation (Transmis Responsable DMP)'){
                                $libelle = 'Demande de cotation (Annulé)';
    
                                $libelle_bc = 'Demande de cotation (Annulé)';
                            }else{
                                $libelle = 'Annulé (Responsable DMP)';
                                $libelle_bc = 'Rejeté (Responsable DMP)';
                            }

                            
                        }elseif ($request->submit === 'rejeter_r_cb') {
                            $libelle = 'Annulé (Responsable contrôle budgetaire)';
                            $libelle_bc = 'Rejeté (Responsable Contrôle Budgétaire)';
                        }elseif ($request->submit === 'rejeter_d_dcg') {
                            $libelle = 'Annulé (Chef Département Contrôle Budgétaire)';
                            $libelle_bc = 'Rejeté (Chef Département DCG)';
                        }elseif ($request->submit === 'rejeter_r_dcg') {
                            $libelle = 'Annulé (Responsable DCG)';
                            $libelle_bc = 'Rejeté (Responsable DCG)';
                        }elseif ($request->submit === 'rejeter_r_dgaaf') {
                            $libelle = 'Annulé (Directeur Général Adjoint)';
                            $libelle_bc = 'Rejeté (Directeur Général Adjoint)';
                        }elseif ($request->submit === 'rejeter_r_dg') {
                            $libelle = 'Annulé (Directeur Général)';
                            $libelle_bc = 'Rejeté (Directeur Général)';
                        }elseif ($request->submit === 'rejeter_r_dfc') {
                            $libelle = 'Annulé (Responsable DFC)'; 
                            //$libelle_bc = 'Annulé (Responsable DFC)'; 
                        }

                        $this->storeStatutDemandeFond($libelle,$demande_fonds_id,$profils_id,$commentaire);

                        if(isset($libelle_bc)){
                            
                            if(isset($request->num_bc)){
                                $num_bc = $request->num_bc;
                            }
                            
                            if($num_bc != null){
                                $this->storeStatutBC($libelle_bc,$profils_id,$num_bc,$commentaire);
                            }
                            
                        }

                        if ($request->submit === 'annuler_g_achat') {
                            if (isset($request->moyen_paiment)) {
                                if ($request->moyen_paiment != null) {
                                    // determiner l'moyen_paiements_id
                                    $moyen_paiement = MoyenPaiement::where('libelle', $request->moyen_paiment)->first();
                                    if ($moyen_paiement!=null) {
                                        $moyen_paiements_id = $moyen_paiement->id;

                                        if ($request->moyen_paiment === 'Espèce') {
                                            if (isset($liaison)) {
                                                $detacher_bc = 1;
                                            }
                                        }


                                        if ($request->moyen_paiment === 'Chèque') {
                                            $ratacher_bc = 1;
                                        }
                                    } else {
                                        $moyen_paiements_id = MoyenPaiement::create([
                                            'libelle'=>$request->moyen_paiment
                                        ])->id;
                                    }

                                    $flag_actif_liaison = null;

                                    if (isset($ratacher_bc) or isset($detacher_bc)) {
                                        if (isset($ratacher_bc)) {
                                            $flag_actif_liaison = 1;
                                        }

                                        if (isset($detacher_bc)) {
                                            $flag_actif_liaison = 0;
                                        }

                                        if (isset($request->moyen_paiment)) {
                                            if ($request->moyen_paiment === "Chèque") {
                                                if (isset($request->num_bc)) {
                                                    $operation = null;

                                                    if (isset(explode('BCN', $request->num_bc)[1])) {
                                                        $operation = $controllerTravaux->getTravauxByNumBc($request->num_bc);
                                                        $type_operations_libelle = "Commande non stockable";
                                                    } elseif (isset(explode('BC', $request->num_bc)[1])) {
                                                        $operation = $this->controller3->getDemandeAchatByNumBc($request->num_bc);

                                                        $type_operations_libelle = "Demande d'achats";
                                                    }
                                                } else {
                                                    return redirect()->back()->with('error', 'Veuillez saisir un N° de bon de commande valide');
                                                }

                                                if ($operation != null && $flag_actif_liaison != null) {
                                                    $this->storeDemandeFondBonCommande($demande_fonds_id, $operation->id, $type_operations_libelle, $flag_actif_liaison);
                                                } else {
                                                    return redirect()->back()->with('error', 'Veuillez saisir un N° de bon de commande valide');
                                                }
                                            } elseif ($request->moyen_paiment === "Espèce") {
                                                if (isset($liaison)) {
                                                    $flag_actif_liaison = 0;
                                                    $this->storeDemandeFondBonCommande($liaison->demande_fonds_id, $liaison->operations_id, $liaison->libelle, $flag_actif_liaison);
                                                }
                                            }
                                        }
                                    }

                                    DemandeFond::where('id', $request->demande_fonds_id)
                                    ->update([
                                    'moyen_paiements_id' =>$moyen_paiements_id
                                    ]);
                                }
                            }
                        }


                    //

                    $subject = 'Annulation de demande de fonds';

                    $this->notif_beneficiaire($subject,$demande_fonds_id);


                    return redirect('demande_fonds/index')->with('success', 'Demande de fonds annulée');
                } else {
                    return redirect()->back()->with('error', 'Annulation de la demande de fonds echouée');
                }
            }elseif ($request->submit === 'edition_g_achat' or $request->submit === 'transmettre_g_achat' or $request->submit === 'transmettre_r_achat' or $request->submit === 'viser_r_achat' or $request->submit === 'signer_r_cmp' or $request->submit === 'viser_r_cb' or $request->submit === 'viser_d_dcg' or $request->submit === 'viser_r_dcg' or $request->submit === 'viser_r_dgaaf' or $request->submit === 'viser_r_dg' or $request->submit === 'viser_r_dfc' or $request->submit === 'edition_g_achat2' or $request->submit === 'retirer' or $request->submit === 'justifier' or $request->submit === 'terminer'){
                
                // determination du profil de l'emetteur
                if ($request->submit === 'edition_g_achat' or $request->submit === 'transmettre_r_achat'){
                    $etape = "edition_g_achat";
                }elseif ($request->submit === 'transmettre_g_achat'){
                    $etape = "transmettre_g_achat";
                }elseif ($request->submit === 'viser_r_achat') {
                    $etape = "viser_r_achat";
                }elseif ($request->submit === 'signer_r_cmp') {
                    $etape = "signer_r_cmp";
                }elseif ($request->submit === 'viser_r_cb') {
                    $etape = "viser_r_cb";
                }elseif ($request->submit === 'viser_d_dcg') {
                    $etape = "viser_d_dcg";
                }elseif ($request->submit === 'viser_r_dcg') {
                    $etape = "viser_r_dcg";
                }elseif ($request->submit === 'viser_r_dgaaf') {
                    $etape = "viser_r_dgaaf";
                }elseif ($request->submit === 'viser_r_dg') {
                    $etape = "viser_r_dg";
                }elseif ($request->submit === 'viser_r_dfc') {
                    $etape = "viser_r_dfc";
                }elseif ($request->submit === 'edition_g_achat2'){
                    $etape = "edition_g_achat2";
                }elseif ($request->submit === 'retirer'){
                    $etape = "retirer";
                }elseif ($request->submit === 'justifier'){
                    $etape = "justifier";
                }elseif ($request->submit === 'terminer'){
                    $etape = "terminer";
                }                
                
                $profils = $this->controlAcces($type_profils_name_valide,$etape,auth()->user()->id,$request);
                
                if($profils===null){
                    return redirect()->back()->with('error','Accès refusé');
                }else{
                    $profils_id = $profils->id;
                }
                
                $this->validate_request($request);

                $exercices = $this->getExercice();

                $solde_avant_op = $request->credit;
                $exercice = $exercices->exercice;
                $credit_budgetaires_id = $request->credit_budgetaires_id;
                

                if ($request->submit === 'edition_g_achat' or $request->submit === 'transmettre_g_achat' or $request->submit === 'transmettre_r_achat' or $request->submit === 'edition_g_achat2'){

                    $profils_id_beneficiare = $this->setBeneficiaire($request->agents_id_beneficiaire);

                    if ($profils_id_beneficiare != null) {

                        $num_dem = null;

                        $demande_fond = $this->getDemandeFond($request->demande_fonds_id);
                        if($demande_fond != null){

                            $explode_num_dem = explode('/',explode('DF', $demande_fond->num_dem)[0])[1];
                            if($explode_num_dem === $request->code_structure){
                                
                                $num_dem = $demande_fond->num_dem;

                            }else{
                                $exercices = $this->getExercice();

                                $num_dem = $this->getLastNumDem($exercices->exercice,$request->code_structure,$request->demande_fonds_id);

                            }

                        }                        

                        if($num_dem === null){
                            return redirect()->back()->with('error','Numéro DF introuvable');
                        }

                        $demande_fonds_id = $this->setDemandeFond($request->demande_fonds_id, $request->code_section, $profils_id_beneficiare, $solde_avant_op, $request->agents_id, $credit_budgetaires_id, $request->ref_fam, $exercice, $request->intitule, $montant, $request->observation,$request->code_gestion,$num_dem);
                    

                    }else{
                        return redirect()->back()->with('error', 'Bénéficiaire introuvable');
                    }
                    //
                    
                    $piece_jointes = DB::table('piece_jointes as pj')
                    ->join('type_operations as to', 'to.id', '=', 'pj.type_operations_id')
                    ->where('to.libelle', 'Demande de fonds')
                    ->where('pj.subject_id', $demande_fonds_id)
                    ->select('pj.id')
                    ->get();
                    foreach ($piece_jointes as $piece_jointe) {
                        if (isset($request->piece_jointes_id[$piece_jointe->id])) {
                            $flag_actif = 1;

                            $piece_jointes_id= $piece_jointe->id;

                            if (isset($request->piece_flag_actif[$piece_jointes_id])) {
                                $flag_actif = 1;
                            } else {
                                $flag_actif = 0;
                            }

                            
                            $libelle = "Demande de fonds";
                            $piece = null;
                            $name = null;

                            $this->piece_jointe($demande_fonds_id, $profils_id, $libelle, $piece, $flag_actif, $name, $piece_jointes_id);
                        }
                    }

                    

                    if (isset($request->piece)) {
                        if (count($request->piece) > 0) {
                            foreach ($request->piece as $item => $value) {
                                if (isset($request->piece[$item])) {
                                    $piece =  $request->piece[$item]->store('piece_jointe', 'public');
    
                                    $name = $request->piece[$item]->getClientOriginalName();
    
                                    $libelle = "Demande de fonds";

                                    $piece_jointes_id = null;

                                    $flag_actif = 1;

                                    if (isset($request->piece_jointes_id[$item])) {
                                        $piece_jointes_id= $request->piece_jointes_id[$item];

                                        if (isset($request->piece_flag_actif[$request->piece_jointes_id[$item]])) {
                                            $flag_actif = 1;
                                        } else {
                                            $flag_actif = 0;
                                        }
                                    }

                                    
                                    
                                    
                                    $this->piece_jointe($demande_fonds_id, $profils_id, $libelle, $piece, $flag_actif, $name, $piece_jointes_id);
                                }
                            }
                        }
                    }

                    
                    if ($request->submit === 'edition_g_achat2') {

                        /*
                        if(isset($request->num_bc)) {
                            
                            if(isset($request->mle_signataire)) {   
                                if(count($request->mle_signataire) > 0) {

                                    $profil_fonction_array = null;
                                    if(isset($request->profil_fonctions_id)){
                                        if(count($request->profil_fonctions_id) > 0){
                                            $profil_fonction_array = $request->profil_fonctions_id;

                                            if($profil_fonction_array != null){

                                                if(isset(explode('BCN', $request->num_bc)[1])) {

                                                    $travauxe_explode = $this->getTravauxByNumBc($request->num_bc);

                                                    if($travauxe_explode != null) {

                                                        $type_operations_libelle_explode = "Commande non stockable";

                                                        $this->setSignataire($type_operations_libelle_explode,$travauxe_explode->id,$profil_fonction_array);
                                                    }

                                                }elseif(isset(explode('BC', $request->num_bc)[1])) {
                                                    $demande_achat_explode = $this->controller3->getDemandeAchatByNumBc($request->num_bc);

                                                    if($demande_achat_explode != null) {

                                                        $type_operations_libelle_explode = "Demande d'achats";

                                                        $this->setSignataire($type_operations_libelle_explode,$demande_achat_explode->id,$profil_fonction_array);
                                                    }
                                                }
                                
                                            }
                                        }

                                    }

                                    foreach ($request->mle_signataire as $key => $value) {
                                        $mle = null;
                                        if(isset(explode('M', $value)[1])) {
                                            $mle = explode('M', $value)[1];
                                        }else{
                                            $mle = $value;
                                        }
                                        
                                        if(isset($mle)){

                                            $profil_fonction = $this->getProfilFonctionByMle($mle);
                                            
                                            if($profil_fonction != null) {
                                                $num_bc = $request->num_bc;

                                                if(isset($num_bc)) {
                                                    if(isset(explode('BCN', $num_bc)[1])) {

                                                        $travauxe_explode = $this->getTravauxByNumBc($num_bc);
                                                        
                                                        if($travauxe_explode != null) {

                                                            $this->storeSignataireTravaux2($profil_fonction->id, $travauxe_explode->id, Session::get('profils_id'));

                                                        }



                                                    } elseif(isset(explode('BC', $num_bc)[1])) {

                                                        $demande_achat_explode = $this->controller3->getDemandeAchatByNumBc($num_bc);

                                                        if($demande_achat_explode != null) {
                                                            $this->storeSignataireDemandeAchat2($profil_fonction->id, $demande_achat_explode->id, Session::get('profils_id'));
                                                        }



                                                    }
                                                }
                                            }
                                        }

                                        
                                    }


                                }
                            }
                        }

                        $this->storeSignataireDemandeFond($request,$demande_fonds_id,$profils_id);*/
                        
                    }
                }
                // statut de la demande de fonds

                    $demande_fond_bon_commande_verif = DemandeFondBonCommande::where('demande_fonds_id',$demande_fonds_id)
                    ->where('flag_actif',1)
                    ->first();

                    $libelle = null;
                    $ordre_de_signature = null;

                    if ($request->submit === 'edition_g_achat'){
                        $libelle = 'Imputé (Gestionnaire des achats)';
                        $libelle_bc = 'Soumis pour validation';

                    }elseif ($request->submit === 'transmettre_r_achat'){
                        $libelle = 'Édité (Gestionnaire des achats)';

                        $libelle_send = 'Transmis (Responsable des achats)';
                        $libelle_bc = 'Transmis (Responsable des achats)';
                    }elseif ($request->submit === 'transmettre_g_achat'){

                        if ($libelle_old != 'Édité (Gestionnaire des achats)') {

                            $libelle_new_old = 'Édité (Gestionnaire des achats)';
                            $this->storeStatutDemandeFond($libelle_new_old,$demande_fonds_id,$profils_id,$request->commentaire);

                        }

                        $libelle = 'Transmis (Responsable des achats)';
                    }elseif ($request->submit === 'viser_r_achat'){
                        $libelle = 'Visé (Responsable des achats)';

                        $libelle_send = 'Transmis (Responsable DMP)';
                        $libelle_bc = 'Transmis (Responsable DMP)';
                    }elseif ($request->submit === 'signer_r_cmp'){

                        if($libelle_old === 'Demande de cotation (Transmis Responsable DMP)'){
                            $libelle = 'Demande de cotation (Validé)';

                            $libelle_bc = 'Demande de cotation (Validé)';
                        }else{
                                $libelle = 'Signé (Responsable DMP)';

                                $libelle_send = 'Transmis (Responsable Contrôle Budgétaire)';
                                $libelle_bc = 'Transmis (Responsable Contrôle Budgétaire)';

                                $edit_signataire = 1;

                                $ordre_engagement = 1;
                        }
                        
                    }elseif ($request->submit === 'viser_r_cb') {
                        $libelle = 'Visé (Responsable Contrôle Budgétaire)';

                        $libelle_send = 'Transmis (Chef Département Contrôle Budgétaire)';
                        $libelle_bc = 'Transmis (Chef Département DCG)';
                        
                    }elseif ($request->submit === 'viser_d_dcg') {
                        $libelle = 'Visé (Chef Département Contrôle Budgétaire)';

                        $libelle_send = 'Transmis (Responsable DCG)';
                        $libelle_bc = 'Transmis (Responsable DCG)';
                    }elseif ($request->submit === 'viser_r_dcg') {

                        //$libelle = 'Signé (Responsable DCG)';
                        $montant = filter_var($request->montant, FILTER_SANITIZE_NUMBER_INT);
            
                        $info = 'Montant';
                        $error = $this->setInt($montant,$info);

                        if (isset($error)) {
                            return redirect()->back()->with('error',$error);
                        }

                        $libelle = 'Signé (Responsable DCG)';
                        $libelle_send = 'Transmis (Directeur Général Adjoint)';
                        $libelle_bc = 'Transmis (Directeur Général Adjoint)';

                        if($demande_fond_bon_commande_verif === null && $montant <= 250000){
                            
                            $libelle = 'Signé (Responsable DCG)';
                            $libelle_send = 'Validé';
                            $libelle_bc = 'Validé';
                            $libelle_view = 'Transmis (Directeur Général Adjoint)';
                            $libelle_view2 = 'Transmis (Directeur Général)';
                            $edit_signataire = 1;
                            $ordre_de_signature = 1;
                            
                        }

                    }elseif ($request->submit === 'viser_r_dgaaf') {
                        $montant = filter_var($request->montant, FILTER_SANITIZE_NUMBER_INT);
            
                        $info = 'Montant';
                        $error = $this->setInt($montant,$info);

                        if (isset($error)) {
                            return redirect()->back()->with('error',$error);
                        }


                        if ($montant <= 5000000) {
                            //$libelle_send = 'Transmis (Responsable DFC)';
                            $libelle_bc = 'Validé';
                            $libelle = 'Validé';

                            $libelle_view2 = 'Transmis (Directeur Général)';
        
                            $edit_signataire = 1;

                            $ordre_de_signature = 1;
                            if(isset($request->num_bc)){
                                $ordre_de_signature_du_bon_de_commande_rattache = 1;
                            }
                        }
                        
                        if ($montant > 5000000){
                            $libelle = 'Visé (Directeur Général Adjoint)';
                            $libelle_send = 'Transmis (Directeur Général)';
                            $libelle_bc = 'Transmis (Directeur Général)';
                        }


                    }elseif ($request->submit === 'viser_r_dg') {
                        $libelle = 'Validé';
                        $libelle_bc = 'Validé';
                        $edit_signataire = 1;
                        $ordre_de_signature = 1;

                        if(isset($request->num_bc)){
                            $ordre_de_signature_du_bon_de_commande_rattache = 1;
                        }
                    }elseif ($request->submit === 'viser_r_dfc') {
                        $libelle = 'Visé (Responsable DFC)';

                        //$libelle_send = 'Validé';
                    }elseif ($request->submit === 'edition_g_achat2'){
                        $libelle = 'Édité';
                        $libelle_bc = 'Édité';
                    }elseif ($request->submit === 'retirer'){

                        
                        $profils_id_beneficiare = $this->setBeneficiaire($request->agents_id_beneficiaire);

                        if ($profils_id_beneficiare != null) {

                            $this->setDemandeFond($request->demande_fonds_id, $request->code_section, $profils_id_beneficiare, $solde_avant_op, $request->agents_id, $credit_budgetaires_id, $request->ref_fam, $exercice, $request->intitule, $montant, $request->observation,$request->code_gestion,$request->num_dem);

                        }
                        

                        // détacher fichier joint
                        $piece_jointes = DB::table('piece_jointes as pj')
                        ->join('type_operations as to','to.id','=','pj.type_operations_id')
                        ->where('to.libelle','Demande de fonds')
                        ->where('pj.subject_id',$demande_fonds_id)
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

                                
                                $libelle = "Demande de fonds";
                                $piece = null;
                                $name = null;

                                $this->piece_jointe($demande_fonds_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id);
                            }
                        }

                        if (isset($request->piece)) {

                            if (count($request->piece) > 0) {
        
                                foreach ($request->piece as $item => $value) {
                                    if (isset($request->piece[$item])) {
        
                                        $piece =  $request->piece[$item]->store('piece_jointe','public');
        
                                        $name = $request->piece[$item]->getClientOriginalName();
        
                                        $libelle = "Demande de fonds";

                                        $piece_jointes_id = null;

                                        $flag_actif = 1;

                                        if (isset($request->piece_jointes_id[$item])) {

                                            $piece_jointes_id= $request->piece_jointes_id[$item];

                                            if (isset($request->piece_flag_actif[$request->piece_jointes_id[$item]])) {
                                                $flag_actif = 1;
                                            }else{
                                                $flag_actif = 0;
                                            }

                                        }

                                        
                                        
                                        
                                        $this->piece_jointe($demande_fonds_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id);
                                        
                                        
                                    }
                                }
                                
        
                            }
        
                        }

                        $libelle = 'Accordé';
                    }elseif ($request->submit === 'justifier' or $request->submit === 'terminer'){

                        if ($request->submit === 'terminer'){
                            DemandeFond::where('id',$demande_fonds_id)->update([
                                'terminer'=>1
                            ]);
                        }
                        // détacher fichier joint
                        $piece_jointes = DB::table('piece_jointes as pj')
                        ->join('type_operations as to','to.id','=','pj.type_operations_id')
                        ->where('to.libelle','Demande de fonds')
                        ->where('pj.subject_id',$demande_fonds_id)
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

                                
                                $libelle = "Demande de fonds";
                                $piece = null;
                                $name = null;

                                $this->piece_jointe($demande_fonds_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id,$type_profils_name);
                            }
                        }

                        if (isset($request->piece)) {

                            if (count($request->piece) > 0) {
        
                                foreach ($request->piece as $item => $value) {
                                    if (isset($request->piece[$item])) {
        
                                        $piece =  $request->piece[$item]->store('piece_jointe','public');
        
                                        $name = $request->piece[$item]->getClientOriginalName();
        
                                        $libelle = "Demande de fonds";

                                        $piece_jointes_id = null;

                                        $flag_actif = 1;

                                        if (isset($request->piece_jointes_id[$item])) {

                                            $piece_jointes_id= $request->piece_jointes_id[$item];

                                            if (isset($request->piece_flag_actif[$request->piece_jointes_id[$item]])) {
                                                $flag_actif = 1;
                                            }else{
                                                $flag_actif = 0;
                                            }

                                        }
                                        
                                        $this->piece_jointe($demande_fonds_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id,$type_profils_name);
                                        
                                        
                                    }
                                }
                                
        
                            }
        
                        }

                        $libelle = 'Dépense justifiée';
                    }

                    $ordre_de_signature_bcs = null;
                    $ordre_de_signature_bcn = null;

                    if(isset($edit_signataire)){
                        if($edit_signataire === 1){
                            
                            $info_user_connect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

                            if ($info_user_connect != null) {

                                $profil_fonction = $this->getProfilFonctionByAgentId($info_user_connect->agents_id);

                                if($profil_fonction != null){

                                    $this->storeSignataireDemandeFond2($profil_fonction->id, $demande_fonds_id, Session::get('profils_id'));

                                    if(isset($ordre_de_signature_du_bon_de_commande_rattache)){
                                        if($ordre_de_signature_du_bon_de_commande_rattache === 1){
                                            $num_bc = $request->num_bc;
                                            if(isset(explode('BCN', $num_bc)[1])) {

                                                $travauxe_explode = $controllerTravaux->getTravauxByNumBc($num_bc);
                                                
                                                if($travauxe_explode != null) {

                                                    $controllerTravaux->storeSignataireTravaux2($profil_fonction->id, $travauxe_explode->id, Session::get('profils_id'));
                                                    $ordre_de_signature_bcn = 1;
                                                    $operations_id = $travauxe_explode->id;
                                                }

                                            } elseif(isset(explode('BC', $num_bc)[1])) {

                                                $demande_achat_explode = $this->controller3->getDemandeAchatByNumBc($num_bc);

                                                if($demande_achat_explode != null) {

                                                    
                                                    $this->storeSignataireDemandeAchat2($profil_fonction->id, $demande_achat_explode->id, Session::get('profils_id'));
                                                    $ordre_de_signature_bcs = 1;
                                                    $operations_id = $demande_achat_explode->id;
                                                }
                                            }
                                        }
                                    }
                                }
                                
                            }
                            
                        }
                    }

                    if($ordre_de_signature === 1){
                        
                        $demande_fond = $this->getDemandeFond($demande_fonds_id);
                        
                        if($demande_fond != null){

                            $data_ordre_signature = [
                                'type_operations_libelle'=>$type_operations_libelle_signature,
                                'operations_id'=>$demande_fonds_id,
                                'reference'=>$demande_fond->num_dem,
                                'extension'=>'pdf',
                                'signature'=>1
                            ];  
                            $signatureController->OrdreDeSignature($data_ordre_signature);
                        }
                        
                    }

                    if($ordre_de_signature_bcs === 1){
                        $type_operations_libelle_signature = "Demande d'achats";
                    }

                    if($ordre_de_signature_bcn === 1){
                        $type_operations_libelle_signature = "Commande non stockable";
                    }

                    if(isset($operations_id) && isset($type_operations_libelle_signature)){
                        $data_ordre_signature = [
                            'type_operations_libelle'=>$type_operations_libelle_signature,
                            'operations_id'=>$operations_id,
                            'reference'=>$num_bc,
                            'extension'=>'pdf',
                            'signature'=>1
                        ];                        
                        $signatureController->OrdreDeSignature($data_ordre_signature);
                    }
                    


                    if ($libelle === null) {
                        return redirect()->back()->with('error','Statut introuvable');
                    }
                    

                    if (isset($libelle_view)) {
                        $this->storeStatutDemandeFond($libelle_view,$demande_fonds_id,$profils_id,$request->commentaire);
                    }

                    if (isset($libelle_view2)) {
                        $this->storeStatutDemandeFond($libelle_view2,$demande_fonds_id,$profils_id,$request->commentaire);
                    }

                    $this->storeStatutDemandeFond($libelle,$demande_fonds_id,$profils_id,$request->commentaire);

                    if (isset($libelle_send)) {
                        $this->storeStatutDemandeFond($libelle_send,$demande_fonds_id,$profils_id,$request->commentaire);
                    }

                    if(isset($libelle_bc) && isset($request->num_bc)){

                        if(isset($request->num_bc)){
                            $num_bc = $request->num_bc;
                        }
                        
                        if($num_bc != null){

                            if (isset($libelle_view)) {
                                $this->storeStatutBC($libelle_view,$profils_id,$num_bc,$request->commentaire);
                            }

                            if (isset($libelle_view2)) {
                                $this->storeStatutBC($libelle_view2,$profils_id,$num_bc,$request->commentaire);
                            }

                            $this->storeStatutBC($libelle_bc,$profils_id,$num_bc,$request->commentaire);
                        }
                        
                    }

                    if(isset($request->moyen_paiment)){
                        if($request->moyen_paiment != null){
                        // determiner l'moyen_paiements_id
                            $moyen_paiement = MoyenPaiement::where('libelle',$request->moyen_paiment)->first();
                            if ($moyen_paiement!=null) {

                                $moyen_paiements_id = $moyen_paiement->id;

                                if ($request->moyen_paiment === 'Espèce') {
                                    if (isset($liaison)) {
                                        $detacher_bc = 1;
                                    }
                                }


                                if ($request->moyen_paiment === 'Chèque') {
                                        
                                    $ratacher_bc = 1;
                                        
                                }
                                
                            }else{

                                $moyen_paiements_id = MoyenPaiement::create([
                                    'libelle'=>$request->moyen_paiment
                                ])->id;

                            }             

                            $flag_actif_liaison = null;

                            if (isset($ratacher_bc) or isset($detacher_bc)) {

                                if (isset($ratacher_bc)) {
                                    $flag_actif_liaison = 1;
                                }

                                if (isset($detacher_bc)) {
                                    $flag_actif_liaison = 0;
                                }

                                if(isset($request->moyen_paiment)){
                                    if($request->moyen_paiment === "Chèque"){
                                        if(isset($request->num_bc)){

                                            $operation = null;
                
                                            if(isset(explode('BCN',$request->num_bc)[1])){
                
                                                $operation = $controllerTravaux->getTravauxByNumBc($request->num_bc);
                                                $type_operations_libelle = "Commande non stockable";
                
                                            }elseif(isset(explode('BC',$request->num_bc)[1])){
                
                                                $operation = $this->controller3->getDemandeAchatByNumBc($request->num_bc);
                
                                                $type_operations_libelle = "Demande d'achats";
                
                                            }
                
                                        }else{
                                            return redirect()->back()->with('error','Veuillez saisir un N° de bon de commande valide');
                                        }

                                        if($operation != null && $flag_actif_liaison != null){

                                            $this->storeDemandeFondBonCommande($demande_fonds_id,$operation->id,$type_operations_libelle,$flag_actif_liaison);
                
                                        }else{
                                            return redirect()->back()->with('error','Veuillez saisir un N° de bon de commande valide');
                                        }
                                    }elseif($request->moyen_paiment === "Espèce"){
                                        if(isset($liaison)){
                
                                            $flag_actif_liaison = 0;
                                            $this->storeDemandeFondBonCommande($liaison->demande_fonds_id,$liaison->operations_id,$liaison->libelle,$flag_actif_liaison);
                                        }
                                    }
                                }
                            }

                            DemandeFond::where('id',$request->demande_fonds_id)
                            ->update([
                            'moyen_paiements_id' =>$moyen_paiements_id
                            ]);  
                        }
                    }

                    $statutDemandeFond = 1;
                
                if ($statutDemandeFond!=null) {


                        $subject = null;

                        if ($request->submit === 'edition_g_achat'){
                            $subject = 'Édition de la demande de fonds';
                        }elseif ($request->submit === 'transmettre_r_achat'){
                            $subject = 'Transmission de la demande au Responsable des achats';
                        }elseif ($request->submit === 'transmettre_g_achat'){
                            $subject = 'Transmission de la demande au Responsable des achats';
                        }elseif ($request->submit === 'viser_r_achat'){
                            $subject = 'Demande de fonds visée (Responsable des achats)';
                        }elseif ($request->submit === 'signer_r_cmp'){
                            $subject = 'Demande de fonds signée (Responsable DMP)';
                        }elseif ($request->submit === 'viser_r_cb') {
                            $subject = 'Demande de fonds visée (Responsable Contrôle Budgétaire)';
                        }elseif ($request->submit === 'viser_d_dcg') {
                            $subject = 'Demande de fonds visée (Chef Département Contrôle Budgétaire)';
                        }elseif ($request->submit === 'viser_r_dcg') {
                            $subject = 'Demande de fonds signée (Responsable DCG)';
                        }elseif ($request->submit === 'viser_r_dgaaf') {
                            $subject = 'Demande de fonds visée (Directeur Général Adjoint)';
                        }elseif ($request->submit === 'viser_r_dg') {
                            $subject = 'Demande de fonds visée (Directeur Général)';
                        }elseif ($request->submit === 'viser_r_dfc') {
                            $subject = 'Demande de fonds visée (Responsable DFC)';
                        }elseif ($request->submit === 'edition_g_achat2') {
                            $subject = 'Demande de fonds éditée';
                        }elseif ($request->submit === 'retirer') {
                            $subject = 'Demande de fonds accordée';
                        }elseif ($request->submit === 'justifier' or $request->submit === 'terminer') {
                            $subject = 'Demande de fonds [Dépense justifiée]';
                        }

                        if ($subject === null) {
                            return redirect()->back()->with('error','Sujet introuvable');
                        }

                    // utilisateur connecté
                        $email = auth()->user()->email;
                        $this->notifUserConnectDF($email,$subject,$demande_fonds_id);
                    //

                    // Bénéficiaire
                        $this->notif_beneficiaire($subject,$demande_fonds_id);
                    //

                    // Responsable DMP
                        $this->notif_respo_cmp($subject,$demande_fonds_id);
                    //

                    // Responsable des achats
                        $this->notif_respo_achat($subject,$demande_fonds_id);
                    //
                    return redirect('demande_fonds/index')->with('success', 'Demande de fonds éditée');
                } else {
                    return redirect()->back()->with('error', 'Édition de la demande de fonds echouée');
                }
            }

            if(!isset($request->num_bc)){
                if($ordre_engagement != null){
                    $type_piece = "ENG";
                    if($ordre_engagement === 2){
                        //Désengagement
                        $flag_engagement = 0;
                        $signe = -1;
                    }

                    if($ordre_engagement === 1){
                        //Engagement
                        $flag_engagement = 1;
                        $signe = 1;
                    }

                    $compte = $demande_fond->ref_fam;
                    $exercice_engagement = $demande_fond->exercice;
                    $code_structure = $demande_fond->code_structure;
                    $code_gestion = $demande_fond->code_gestion;
                    $montant_acompte = null;

                    $data_engagement = [
                        'type_operations_libelle'=>$type_operations_libelle_signature,
                        'operations_id'=>$demande_fonds_id,
                        'solde_avant_op'=>$solde_avant_op,
                        'flag_engagement'=>$flag_engagement,
                        'this'=>$controllerPerdiem,
                        'signe'=>$signe,
                        'type_piece'=>$type_piece,
                        'montant_acompte'=>$montant_acompte,
                        'compte'=>$compte,
                        'exercice'=>$exercice_engagement,
                        'code_structure'=>$code_structure,
                        'code_gestion'=>$code_gestion,
                    ];

                    $this->procedureEngagementEtEcritureComptable($data_engagement);
                }
            }
        }

    }

    public function destroy(DemandeFond $demandeFond)
    {
        //
    }

    public function notifUserConnectDF($email,$subject,$demande_fonds_id){
        // utilisateur connecté
            $details = [
                'email' => $email,
                'subject' => $subject,
                'demande_fonds_id' => $demande_fonds_id,
                'link' => URL::to('/'),
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }

    public function notif_respo_cmp($subject,$demande_fonds_id){
        // Responsable DMP
                    
                $profil_responsable_cmps = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->where('tp.name', 'Responsable DMP')
                ->select('u.email')
                ->where('p.flag_actif',1)
                ->get();

            foreach ($profil_responsable_cmps as $profil_responsable_cmp) {

                $details = [
                    'email' => $profil_responsable_cmp->email,
                    'subject' => $subject,
                    'demande_fonds_id' => $demande_fonds_id,
                    'link' => URL::to('/'),
                ];

                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);

            }
        //
    }

    public function notif_beneficiaire($subject,$demande_fonds_id){
        //bénéficiaire
            $demande_fond = DB::table('demande_fonds as df')
            ->join('familles as f','f.ref_fam','=','df.ref_fam')
            ->join('profils as p','p.id','=','df.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->join('sections as s','s.code_section','=','df.code_section')
            ->select('u.email')
            ->where('df.id',$demande_fonds_id)
            ->first();

            if ($demande_fond!=null) {
                $details = [
                    'email' => $demande_fond->email,
                    'subject' => $subject,
                    'demande_fonds_id' => $demande_fonds_id,
                    'link' => URL::to('/'),
                ];

                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);
            }

        //
    }

    public function notif_respo_achat($subject,$demande_fonds_id){
        // responsable des achats
                    
            $profil_responsable_achats = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('sections as s', 's.id', '=', 'ase.sections_id')
            ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->where('tp.name', 'Responsable des achats')
            ->select('u.email')
            ->where('p.flag_actif',1)
            ->where('tsase.libelle','Activé')
            ->whereIn('st.ref_depot',function($query) use($demande_fonds_id){
                $query->select(DB::raw('st2.ref_depot'))
                    ->from('demande_fonds as df')
                    ->join('sections as s2', 's2.code_section', '=', 'df.code_section')
                    ->join('structures as st2', 'st2.code_structure', '=', 's2.code_structure')
                    ->where('df.id',$demande_fonds_id)
                    ->whereRaw('st.ref_depot = st2.ref_depot');
            })
            ->get();

            foreach ($profil_responsable_achats as $profil_responsable_achat) {

                $details = [
                    'email' => $profil_responsable_achat->email,
                    'subject' => $subject,
                    'demande_fonds_id' => $demande_fonds_id,
                    'link' => URL::to('/'),
                ];

                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);

            }
        //
    }

    public function notif_gestionnaire_achat($subject,$demande_fonds_id){
        // Gestionnaire des achats
                    
            $profil_gestionnaire_achats = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('sections as s', 's.id', '=', 'ase.sections_id')
            ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->where('tp.name', 'Gestionnaire des achats')
            ->select('u.email')
            ->where('p.flag_actif',1)
            ->where('tsase.libelle','Activé')
            ->whereIn('st.ref_depot',function($query) use($demande_fonds_id){
                $query->select(DB::raw('st2.ref_depot'))
                    ->from('demande_fonds as df')
                    ->join('sections as s2', 's2.code_section', '=', 'df.code_section')
                    ->join('structures as st2', 'st2.code_structure', '=', 's2.code_structure')
                    ->where('df.id',$demande_fonds_id)
                    ->whereRaw('st.ref_depot = st2.ref_depot');
            })
            ->get();

            foreach ($profil_gestionnaire_achats as $profil_gestionnaire_achat) {

                $details = [
                    'email' => $profil_gestionnaire_achat->email,
                    'subject' => $subject,
                    'demande_fonds_id' => $demande_fonds_id,
                    'link' => URL::to('/'),
                ];

                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);

            }
        //
    }

    public function storeSessionBackground($request){
        $request->session()->put('backgroundImage','container-infographie4');
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

    public function validate_request($request){
        
        if (isset($request->submit)) {
            if ($request->submit === 'enregistrer') {

                $validate = $request->validate([
                    'code_section' => ['required','numeric'],
                    'nom_section' => ['required','string'],
                    'agents_id' => ['required','numeric'],
                    'mle' => ['required','string'],
                    'nom_prenoms' => ['required','string'],
                    'mle_charge' => ['required','string'],
                    'nom_prenoms_charge' => ['required','string'],
                    'credit'=>['required','numeric'],
                    'credit_budgetaires_id'=>['required','numeric'],
                    'ref_fam' => ['required','numeric'],
                    'design_fam' => ['required','string'],
                    'intitule' => ['required','string'],
                    'montant' => ['required','string'],
                    'observation' => ['nullable','string'],
                    'code_gestion' => ['required','string']
                ]);

            }elseif ($request->submit === 'edition_g_achat' or $request->submit === 'transmettre_g_achat') {
                $validate = $request->validate([
                    'num_dem' => ['required','string'],
                    'code_section' => ['required','numeric'],
                    'nom_section' => ['required','string'],
                    'agents_id_beneficiaire' => ['required','numeric'],
                    'mle' => ['required','string'],
                    'nom_prenoms' => ['required','string'],
                    'ref_fam' => ['required','numeric'],
                    'design_fam' => ['required','string'],
                    'intitule' => ['required','string'],
                    'montant' => ['required','string'],
                    'observation' => ['nullable','string'],
                    'agents_id' => ['required','numeric'],
                    'mle_charge' => ['required','string'],
                    'nom_prenoms_charge' => ['required','string'],
                    'credit'=>['required','numeric'],
                    'credit_budgetaires_id'=>['required','numeric'],
                    'commentaire' => ['nullable','string'],
                    'moyen_paiment' => ['required','string'],
                    'num_bc' => ['nullable','string']
                ]);
            }elseif ($request->submit === 'edition_g_achat2') {
                $validate = $request->validate([
                    'num_dem' => ['required','string'],
                    'code_section' => ['required','numeric'],
                    'nom_section' => ['required','string'],
                    'mle' => ['required','string'],
                    'nom_prenoms' => ['required','string'],
                    'ref_fam' => ['required','numeric'],
                    'design_fam' => ['required','string'],
                    'intitule' => ['required','string'],
                    'montant' => ['required','string'],
                    'observation' => ['nullable','string'],
                    'agents_id' => ['required','numeric'],
                    'mle_charge' => ['required','string'],
                    'nom_prenoms_charge' => ['required','string'],
                    'credit'=>['required','numeric'],
                    'credit_budgetaires_id'=>['required','numeric'],
                    'commentaire' => ['nullable','string'],
                    'moyen_paiment' => ['nullable','string'],
                    'num_bc' => ['nullable','string'],
                    'profil_fonctions_id' => ['required','array'],
                    'mle_signataire' => ['required','array'],
                    'nom_prenoms_signataire' => ['required','array'],
                ]);
            }else {
                $validate = $request->validate([
                    'num_dem' => ['required','string'],
                    'code_section' => ['required','numeric'],
                    'nom_section' => ['required','string'],
                    'mle' => ['required','string'],
                    'nom_prenoms' => ['required','string'],
                    'ref_fam' => ['required','numeric'],
                    'design_fam' => ['required','string'],
                    'intitule' => ['required','string'],
                    'montant' => ['required','string'],
                    'observation' => ['nullable','string'],
                    'agents_id' => ['required','numeric'],
                    'mle_charge' => ['required','string'],
                    'nom_prenoms_charge' => ['required','string'],
                    'credit'=>['required','numeric'],
                    'credit_budgetaires_id'=>['required','numeric'],
                    'code_gestion' => ['required','string']
                ]);
            }
        }
        
    }

    public function storeDemandeFond($num_dem,$code_section,$profils_id,$solde_avant_op,$agents_id,$credit_budgetaires_id,$ref_fam,$exercice,$intitule,$montant,$observation,$code_gestion){
                    
        $demande_fonds_id = DemandeFond::create([
            'num_dem' => $num_dem,
            'code_section' => $code_section,
            'profils_id' => $profils_id,
            'solde_avant_op' => $solde_avant_op,
            'agents_id' => $agents_id,
            'credit_budgetaires_id' => $credit_budgetaires_id,
            'ref_fam' => $ref_fam,
            'exercice' => $exercice,
            'intitule' => $intitule,
            'montant' => $montant,
            'observation' => $observation,
            'code_gestion' => $code_gestion,
        ])->id;
        return $demande_fonds_id;
    }

    public function sendEmailUserConnect($email,$subject,$demande_fonds_id){
        $details = [
            'email' => $email,
            'subject' => $subject,
            'demande_fonds_id' => $demande_fonds_id,
            'link' => URL::to('/'),
        ];

        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
        dispatch($emailJob);
    }

    public function engagement_depense($demande_fonds_id,$montant,$commentaire,$profils_id){
        //engagement des depenses

            //TypeOperation
                $libelle_type_operation = "Demande de fonds";

                $type_operation = DB::table('type_operations')
                                    ->where('libelle',$libelle_type_operation)
                                    ->first();
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                }else{

                    $type_operations_id = TypeOperation::create([
                        'libelle'=>$libelle_type_operation
                    ])->id;

                }
            //

            //TypeStatutcreditBudgetaire
                $libelle_type_statut_credit_budgetaire = "Engagement";

                $type_statut_credit_budgetaire = DB::table('type_statut_credit_budgetaires')
                                    ->where('libelle',$libelle_type_statut_credit_budgetaire)
                                    ->first();
                if ($type_statut_credit_budgetaire!=null) {

                    $type_statut_c_budgetaires_id = $type_statut_credit_budgetaire->id;

                }else{

                    $type_statut_c_budgetaires_id = TypeStatutCreditBudgetaire::create([
                        'libelle'=>$libelle_type_statut_credit_budgetaire
                    ])->id;

                }
            //

            if (isset($type_operations_id) && isset($type_statut_c_budgetaires_id)) {

                $credit_budgetaire = DB::table('demande_fonds as df')
                    ->join('credit_budgetaires as cb','cb.id','=','df.credit_budgetaires_id')
                    ->where('df.id',$demande_fonds_id)
                    ->select('cb.id','cb.credit','cb.consommation')
                    ->first();

                    
                    if ($credit_budgetaire!=null) {

                        $credit_budgetaires_id = $credit_budgetaire->id;

                        $credit = $credit_budgetaire->credit;

                        $consommation = $credit_budgetaire->consommation;

                        $montant = filter_var($montant, FILTER_SANITIZE_NUMBER_INT);
                        
                        $montant = (-1 * $montant);
                        
                        $data = [
                            'type_operations_id' =>$type_operations_id,
                            'operations_id' =>$demande_fonds_id,
                            'credit_budgetaires_id' =>$credit_budgetaires_id,
                            'type_statut_c_budgetaires_id' =>$type_statut_c_budgetaires_id,
                            'montant' =>$montant,
                            'flag_actif' =>1,
                            'date_debut' =>date("Y-m-d"),
                            'date_fin' =>date("Y-m-d"),
                            'profils_id' =>$profils_id,
                            'commentaire' =>$commentaire,
                        ];

                        // $statut_credit_budgetaire_control = StatutCreditBudgetaire::where('operations_id',$demande_fonds_id)
                        // ->where('credit_budgetaires_id',$credit_budgetaires_id)
                        // ->where('type_statut_c_budgetaires_id',$type_statut_c_budgetaires_id)
                        // ->first();
                        // if ($statut_credit_budgetaire_control!=null) {

                        //     StatutCreditBudgetaire::where('id',$statut_credit_budgetaire_control->id)
                        //     ->update($data);

                        //     $statut_credit_budgetaire = StatutCreditBudgetaire::where('id',$statut_credit_budgetaire_control->id)
                        //     ->first();
                             
                        // }else{
                        //     $statut_credit_budgetaire = StatutCreditBudgetaire::create($data);
                        // }

                        $statut_credit_budgetaire = StatutCreditBudgetaire::create($data);
                        

                        if ($statut_credit_budgetaire!=null) {
                            
                            //determination de a consommation

                            $consommation = $consommation + $montant;
                            $credit = $credit + $montant;

                            CreditBudgetaire::where('id',$credit_budgetaires_id)->update([
                                'consommation'=>$consommation,
                                'credit'=>$credit,
                            ]);
                            
                        }
                    }

            }
        //
    }

    public function engagement_depense_annulation($demande_fonds_id,$montant,$commentaire,$profils_id){
        //engagement des depenses

            //TypeOperation
                $libelle_type_operation = "Demande de fonds";

                $type_operation = DB::table('type_operations')
                                    ->where('libelle',$libelle_type_operation)
                                    ->first();
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                }else{

                    $type_operations_id = TypeOperation::create([
                        'libelle'=>$libelle_type_operation
                    ])->id;

                }
            //

            //TypeStatutcreditBudgetaire
                $libelle_type_statut_credit_budgetaire = "Annulation";

                $type_statut_credit_budgetaire = DB::table('type_statut_credit_budgetaires')
                                    ->where('libelle',$libelle_type_statut_credit_budgetaire)
                                    ->first();
                if ($type_statut_credit_budgetaire!=null) {

                    $type_statut_c_budgetaires_id = $type_statut_credit_budgetaire->id;

                }else{

                    $type_statut_c_budgetaires_id = TypeStatutCreditBudgetaire::create([
                        'libelle'=>$libelle_type_statut_credit_budgetaire
                    ])->id;

                }
            //

            if (isset($type_operations_id) && isset($type_statut_c_budgetaires_id)) {

                $credit_budgetaire = DB::table('demande_fonds as df')
                    ->join('credit_budgetaires as cb','cb.id','=','df.credit_budgetaires_id')
                    ->where('df.id',$demande_fonds_id)
                    ->select('cb.id','cb.credit','cb.consommation')
                    ->whereNotNull('df.solde_avant_op')
                    ->first();
                    if ($credit_budgetaire!=null) {

                        $credit_budgetaires_id = $credit_budgetaire->id;

                        $credit = $credit_budgetaire->credit;

                        $consommation = $credit_budgetaire->consommation;

                        $montant = filter_var($montant, FILTER_SANITIZE_NUMBER_INT);
                        
                        $montant = (1 * $montant);
                        
                        $data = [
                            'type_operations_id' =>$type_operations_id,
                            'operations_id' =>$demande_fonds_id,
                            'credit_budgetaires_id' =>$credit_budgetaires_id,
                            'type_statut_c_budgetaires_id' =>$type_statut_c_budgetaires_id,
                            'montant' =>$montant,
                            'flag_actif' =>1,
                            'date_debut' =>date("Y-m-d"),
                            'date_fin' =>date("Y-m-d"),
                            'profils_id' =>$profils_id,
                            'commentaire' =>$commentaire,
                        ];

                        // $statut_credit_budgetaire_control = StatutCreditBudgetaire::where('operations_id',$demande_fonds_id)
                        // ->where('credit_budgetaires_id',$credit_budgetaires_id)
                        // ->where('type_statut_c_budgetaires_id',$type_statut_c_budgetaires_id)
                        // ->first();
                        // if ($statut_credit_budgetaire_control!=null) {

                        //     StatutCreditBudgetaire::where('id',$statut_credit_budgetaire_control->id)
                        //     ->update($data);

                        //     $statut_credit_budgetaire = StatutCreditBudgetaire::where('id',$statut_credit_budgetaire_control->id)
                        //     ->first();
                             
                        // }else{
                        //     $statut_credit_budgetaire = StatutCreditBudgetaire::create($data);
                        // }

                        $statut_credit_budgetaire = StatutCreditBudgetaire::create($data);
                        

                        if ($statut_credit_budgetaire!=null) {
                            
                            //determination de a consommation
                            

                                $consommation = $consommation + $montant;
                                $credit = $credit + $montant;

                                CreditBudgetaire::where('id',$credit_budgetaires_id)->update([
                                    'consommation'=>$consommation,
                                    'credit'=>$credit,
                                ]);

                                DemandeFond::where('id',$demande_fonds_id)->update([
                                    'solde_avant_op'=>null
                                ]);
                            
                        }
                    }

            }
        //
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

    public function getDemandeFonds($type_profils_name,$code_structure,$libelle){

        $demande_fonds = null;

        if ($type_profils_name === 'Agent Cnps') {
            
            $demande_fonds = DB::table('demande_fonds as df')
            ->join('familles as f', 'f.ref_fam', '=', 'df.ref_fam')
            ->join('profils as p', 'p.id', '=', 'df.profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->select('a.mle', 'a.nom_prenoms', 'f.ref_fam', 'f.design_fam', 'df.num_dem', 'df.intitule', 'df.id', 'df.montant', 'df.solde_avant_op')
            ->orderByDesc('df.id')
            ->whereIn('df.id', function ($query) use ($libelle) {
                $query->select(DB::raw('sdf.demande_fonds_id'))
                    ->from('statut_demande_fonds as sdf')
                    ->join('type_statut_demande_fonds as tsdf', 'tsdf.id', '=', 'sdf.type_statut_demande_fonds_id')
                    ->whereIn('tsdf.libelle', $libelle)
                    ->whereRaw('df.id = sdf.demande_fonds_id');
            })
            /*->whereIn('df.code_section', function ($query) use ($code_structure) {
                $query->select(DB::raw('s.code_section'))
                    ->from('agent_sections as ase')
                    ->join('sections as s', 's.id', '=', 'ase.sections_id')
                    ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
                    ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                    ->where('tsas.libelle', 'Activé')
                    ->where('ase.agents_id', auth()->user()->agents_id)
                    ->where('st.code_structure', $code_structure)
                    ->whereRaw('df.code_section = s.code_section');
            })*/
            ->whereIn('df.agents_id', function ($query) use ($code_structure) {
                $query->select(DB::raw('ase.agents_id'))
                    ->from('agent_sections as ase')
                    ->join('sections as s', 's.id', '=', 'ase.sections_id')
                    ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
                    ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                    ->where('tsas.libelle', 'Activé')
                    ->where('ase.agents_id', auth()->user()->agents_id)
                    ->where('st.code_structure', $code_structure)
                    ->whereRaw('df.agents_id = ase.agents_id');
            })
            ->get();

        }elseif ($type_profils_name === 'Pilote AEE') {
            $demande_fonds = DB::table('demande_fonds as df')
            ->join('familles as f', 'f.ref_fam', '=', 'df.ref_fam')
            ->join('profils as p', 'p.id', '=', 'df.profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('sections as s', 's.code_section', '=', 'df.code_section')
            ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
            ->select('a.mle', 'a.nom_prenoms', 'f.ref_fam', 'f.design_fam', 'df.num_dem', 'df.intitule', 'df.id', 'df.montant', 'df.solde_avant_op')
            ->orderByDesc('df.id')
            ->whereIn('df.id', function ($query) use ($libelle) {
                $query->select(DB::raw('sdf.demande_fonds_id'))
                    ->from('statut_demande_fonds as sdf')
                    ->join('type_statut_demande_fonds as tsdf', 'tsdf.id', '=', 'sdf.type_statut_demande_fonds_id')
                    ->whereIn('tsdf.libelle', $libelle)
                    ->whereRaw('df.id = sdf.demande_fonds_id');
            })
            ->whereIn('st.code_structure', function ($query) {
                $query->select(DB::raw('sst.code_structure'))
                    ->from('agent_sections as ase')
                    ->join('sections as ss', 'ss.id', '=', 'ase.sections_id')
                    ->join('structures as sst', 'sst.code_structure', '=', 'ss.code_structure')
                    ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                    ->where('tsas.libelle', 'Activé')
                    ->where('ase.agents_id', auth()->user()->agents_id)
                    ->whereRaw('st.code_structure = sst.code_structure');
            })
            ->get();
        }else {
            $demande_fonds = DB::table('demande_fonds as df')
            ->join('familles as f', 'f.ref_fam', '=', 'df.ref_fam')
            ->join('profils as p', 'p.id', '=', 'df.profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('sections as s', 's.code_section', '=', 'df.code_section')
            ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
            ->select('a.mle', 'a.nom_prenoms', 'f.ref_fam', 'f.design_fam', 'df.num_dem', 'df.intitule', 'df.id', 'df.montant', 'df.solde_avant_op')
            ->orderByDesc('df.id')
            ->whereIn('df.id', function ($query) use ($libelle) {
                $query->select(DB::raw('sdf.demande_fonds_id'))
                    ->from('statut_demande_fonds as sdf')
                    ->join('type_statut_demande_fonds as tsdf', 'tsdf.id', '=', 'sdf.type_statut_demande_fonds_id')
                    ->whereIn('tsdf.libelle', $libelle)
                    ->whereRaw('df.id = sdf.demande_fonds_id');
            })
            ->whereIn('st.ref_depot', function ($query) {
                $query->select(DB::raw('sst.ref_depot'))
                    ->from('agent_sections as ase')
                    ->join('sections as ss', 'ss.id', '=', 'ase.sections_id')
                    ->join('structures as sst', 'sst.code_structure', '=', 'ss.code_structure')
                    ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                    ->where('tsas.libelle', 'Activé')
                    ->where('ase.agents_id', auth()->user()->agents_id)
                    ->whereRaw('st.ref_depot = sst.ref_depot');
            })
            ->get();

        }
        return $demande_fonds;

    }

    public function piece_jointe($demande_fonds_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id,$type_profils_name = null){

        
        $type_operation = TypeOperation::where('libelle',$libelle)->first();
        if ($type_operation!=null) {
            $type_operations_id = $type_operation->id;
        }else{
            $type_operations_id = TypeOperation::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($piece_jointes_id)) {

            if ($type_profils_name === 'Agent Cnps') {
                $piece_jointe = DB::table('piece_jointes as pj')
                ->join('profils as p','p.id','=','pj.profils_id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->where('pj.id',$piece_jointes_id)
                ->select('tp.name','pj.id as piece_jointes_id')
                ->first();

                if ($piece_jointe) {
                    if ($piece_jointe->name === $type_profils_name) {
                        PieceJointe::where('id',$piece_jointe->piece_jointes_id)->update([
                            'profils_id'=>$profils_id,
                            'flag_actif'=>$flag_actif
                        ]);
                    }
                }
            }else{
                PieceJointe::where('id',$piece_jointes_id)->update([
                    'profils_id'=>$profils_id,
                    'flag_actif'=>$flag_actif
                ]);
            }
            

            
        }else{
            PieceJointe::create([
                'type_operations_id'=>$type_operations_id,
                'profils_id'=>$profils_id,
                'subject_id'=>$demande_fonds_id,
                'name'=>$name,
                'piece'=>$piece,
                'flag_actif'=>$flag_actif
    
            ]);
        }
        
    }
    public function getAllCreditBudgetaire($exercice,$code_structure){
            
        $credit_budgetaires = DB::table('credit_budgetaires as cb')
        ->join('familles as f','f.ref_fam','=','cb.ref_fam')
        ->where('exercice',$exercice)
        ->where('code_structure',$code_structure)
        ->select('f.ref_fam','f.design_fam','cb.id as credit_budgetaires_id','cb.credit')
        ->get();

        return $credit_budgetaires;
    }
    public function getStructure($code_structure){

        
        $structure = DB::table('agents as a')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                    ->where('tsase.libelle','Activé')
                    ->where('a.id',auth()->user()->agents_id)
                    ->where('st.ref_depot',function($query) use($code_structure){
                        $query->select(DB::raw('sst.ref_depot'))
                              ->from('structures as sst')
                              ->where('sst.code_structure',$code_structure)
                              ->whereRaw('st.ref_depot = sst.ref_depot');
                    })
                    ->first();
        
        return $structure;

        // dd($code_structure,$structure);

    }

    public function controlAcces($type_profils_name,$etape,$users_id,$request=null){

        $profils = null;
        if ($etape === "index") {
            $profils = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Agent Cnps','Pilote AEE','Responsable des achats','Gestionnaire des achats','Responsable DMP','Responsable contrôle budgetaire','Signataire','Agent Cnps','Secrétaire DMP','Chef Département DCG','Responsable DCG','Secrétaire DCG','Responsable DFC','Directeur Général Adjoint','Directeur Général','Secrétaire DFC','Gestionnaire des achats','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        }elseif ($etape === "create" or $etape === "store") {

            $profils = DB::table('profils as p')
                ->join('type_profils as tp','tp.id', '=', 'p.type_profils_id')
                ->join('users as u','u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->whereIn('tp.name', ['Pilote AEE','Gestionnaire des achats'])
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('u.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->select('p.id','s.ref_depot')
                ->first();
                
        }elseif ($etape === "show" or $etape === "edit" or $etape === "modifier" or $etape === "justifier" or $etape === "terminer") {

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
                ->whereIn('tp.name', ['Agent Cnps'])
                ->limit(1)
                ->select('p.id', 'se.code_section')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                /*->whereIn('s.code_structure',function($query) use($request){
                    $query->select(DB::raw('ss.code_structure'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('ss.code_structure = s.code_structure');
                })*/
                ->whereIn('a.id',function($query) use($request){
                    $query->select(DB::raw('df.agents_id'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('a.id = df.agents_id');
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
                ->whereIn('tp.name', ['Pilote AEE','Secrétaire DMP','Responsable DMP','Responsable des achats','Gestionnaire des achats','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Secrétaire DCG','Responsable DFC','Directeur Général Adjoint','Directeur Général','Secrétaire DFC','Gestionnaire des achats','Administrateur fonctionnel'])
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.ref_depot',function($query) use($request){
                    $query->select(DB::raw('sst.ref_depot'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->join('structures as sst','sst.code_structure','=','ss.code_structure')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('s.ref_depot = sst.ref_depot');
                })
                ->first();

                
            }
            
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
                ->whereIn('tp.name', ['Pilote AEE','Secrétaire DMP','Responsable DMP','Responsable des achats','Gestionnaire des achats','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Secrétaire DCG','Responsable DFC','Directeur Général Adjoint','Directeur Général','Secrétaire DFC','Gestionnaire des achats'])
                ->limit(1)
                ->select('p.id', 'se.code_section')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.ref_depot',function($query) use($request){
                    $query->select(DB::raw('sst.ref_depot'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->join('structures as sst','sst.code_structure','=','ss.code_structure')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('s.ref_depot = sst.ref_depot');
                })
                ->first();

        }

        return $profils;
    }
    public function getPieceJointe($demande_fonds_id){

        $piece_jointes = DB::table('piece_jointes as pj')
        ->join('type_operations as to','to.id','=','pj.type_operations_id')
        ->where('to.libelle','Demande de fonds')
        ->where('pj.subject_id',$demande_fonds_id)
        ->select('pj.id','pj.piece','pj.name','pj.flag_actif')
        ->where('flag_actif',1)
        ->get(); 

        return $piece_jointes;
    }
    public function getChargeSuiviFirst($demande_fonds_id){

        $charge_suivi = DB::table('agents as a')
                          ->join('demande_fonds as df','df.agents_id','=','a.id')
                          ->where('df.id',$demande_fonds_id)
                          ->select('a.id','a.mle','a.nom_prenoms')
                          ->first();

        return $charge_suivi;

    }
    public function getStructureDemandeFond($demande_fonds_id){
        $code_structure = null;

        $demande_fond = DB::table('demande_fonds as df')
        ->where('df.id',$demande_fonds_id)
        ->join('familles as f','f.ref_fam','=','df.ref_fam')
        ->join('profils as p','p.id','=','df.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->join('sections as s','s.code_section','=','df.code_section')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->select('st.code_structure')
        ->first();

        if ($demande_fond!=null) {
            $code_structure = $demande_fond->code_structure;
        }

        return $code_structure;
    }
    public function getMoyenPaiement($demande_fonds_id){
        $libelle_moyen_paiement = null;
        $moyen_paiement = DB::table('demande_fonds as df')
        ->join('moyen_paiements as mp','mp.id','=','df.moyen_paiements_id')
        ->where('df.id',$demande_fonds_id)
        ->select('mp.libelle')
        ->first();

        if ($moyen_paiement!=null) {
            $libelle_moyen_paiement = $moyen_paiement->libelle;
        }

        return $libelle_moyen_paiement;
        
    }
    public function setInt($saisie,$info){
        $error = null;

        try {
            $saisie = $saisie * 1;
        } catch (\Throwable $th) {
            $error = $info." : Une valeur non numérique rencontrée.";
        }
        
        if (gettype($saisie)!='integer') {
            $error = $info." : Une valeur non numérique rencontrée.";
        }

        return $error;
    }
    public function editDemandeFond($demande_fonds_id){

        $statut_demande_fond = DB::table('statut_demande_fonds as sdf')
        ->join('type_statut_demande_fonds as tsdf','tsdf.id','=','sdf.type_statut_demande_fonds_id')
        ->join('profils as p','p.id','=','sdf.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('sdf.demande_fonds_id',$demande_fonds_id)
        ->whereIn('tsdf.libelle',['Validé'])
        ->orderByDesc('sdf.id')
        ->limit(1)
        ->first();

        return $statut_demande_fond;
    }
    
    public function getTypeStatutSignataire($libelle){
    
        $type_statut_sign_id = null;
        $type_statut_signataire = TypeStatutSignataire::where('libelle',$libelle)
        ->first();
        if ($type_statut_signataire!=null) {
            $type_statut_sign_id = $type_statut_signataire->id;
        }else{
            $type_statut_sign_id = TypeStatutSignataire::create([
                'libelle'=>$libelle
            ])->id;
        }
    
        return $type_statut_sign_id;
        
    
    }
    public function getStructureConnectee($agents_id){
        
        $structure = DB::table('agents as a')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                    ->where('tsase.libelle','Activé')
                    ->where('a.id',$agents_id)
                    ->select('st.nom_structure')
                    ->first();
        return $structure;
    }
    public function setBeneficiaire($agents_id_beneficiaire){
        $profils_id_beneficiare = null;

        $agent = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->where('a.id',$agents_id_beneficiaire)
        ->where('p.flag_actif',1)
        ->select('p.id')
        ->first();

        if($agent!=null){
            $profils_id_beneficiare = $agent->id;
        }

        return $profils_id_beneficiare;

    }

}
