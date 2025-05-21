<?php

namespace App\Http\Controllers;

use Carbon\Carbon; 
use App\Models\Taxe;
use App\Models\Devise;
use App\Jobs\SendEmail;
use App\Models\Famille;
use App\Models\Gestion;
use App\Models\Periode;
use App\Models\Service;
use App\Models\Travaux;
use App\Models\Structure;
use App\Models\PieceJointe;
use Illuminate\Http\Request;
use App\Models\DetailTravaux;
use App\Models\StatutTravaux;
use App\Models\TypeOperation;
use App\Models\InterfaceAddGl;
use App\Models\CreditBudgetaire;
use App\Models\SignataireTravaux;
use App\Models\TypeStatutTravaux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\TypeStatutSignataire;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\Models\StatutSignataireTravaux;
use Illuminate\Support\Facades\Session;
use AmrShawky\LaravelCurrency\Facade\Currency;
use Illuminate\Contracts\Encryption\DecryptException;

class TravauxController extends ControllerTravaux
{
    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->storeSessionBackground($request);

        $travauxes = [];
        $acces_create = null;
        if (Session::has('profils_id')) {

            $etape = "index";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $libelle = null;
            if ($type_profils_name === 'Gestionnaire des achats') {
                $libelle = 'Soumis pour validation';
                $travauxes = $this->data($libelle,$type_profils_name);
                $acces_create = 1;
                
            }elseif ($type_profils_name === 'Administrateur fonctionnel') {
                $libelle = 'Soumis pour validation';
                $travauxes = $this->data($libelle,$type_profils_name);
                
            }elseif ($type_profils_name === "Responsable des achats") {
                $libelle = "Transmis (Responsable des achats)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable DMP") {
                $libelle = "Transmis (Responsable DMP)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable contrôle budgetaire") {
                $libelle = "Transmis (Responsable Contrôle Budgétaire)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Chef Département DCG") {
                $libelle = "Transmis (Chef Département DCG)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable DCG") {
                $libelle = "Transmis (Responsable DCG)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Directeur Général Adjoint") {
                $libelle = "Transmis (Directeur Général Adjoint)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Directeur Général") {
                $libelle = "Transmis (Directeur Général)";
                $travauxes = $this->data($libelle,$type_profils_name);
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $travaux_edite = DB::table('travauxes as da')
                ->join('statut_travauxes as sda','sda.travauxes_id','=','da.id')
                ->join('type_statut_travauxes as tsda','tsda.id','=','sda.type_statut_travauxes_id')
                ->where('tsda.libelle','Édité')
                ->limit(1)
                ->first();

            return view('travaux.index',[
                'travaux_edite'=>$travaux_edite,
                'travauxes'=>$travauxes,
                'type_profils_name'=>$type_profils_name,
                'acces_create'=>$acces_create,
            ]);

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $structures = [];
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $etape = "create";

        $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }else{

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $ref_depot = $infoUserConnect->ref_depot;

                $structures = $this->getStructuresByRefDepot($ref_depot);
            }
        }

        $periodes = Periode::all();

        $taxes = Taxe::all();

        $organisations = [];

         //générer le N° Bon de commande
         
 
         $num_bc = "";
 
         $services = Service::all();

         $familles = DB::table('familles')->get();

         $nbre_ligne = 0;

         
         $gestions = $this->getGestion();

         $exercices = $this->getExercice();

         $credit_budgetaires_credit = null;

         $section = DB::table('sections as s')
                        ->join('agent_sections as ase','ase.sections_id','=','s.id')
                        ->join('structures as st','st.code_structure','=','s.code_structure')
                        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                        ->where('tsas.libelle','Activé')
                        ->where('ase.agents_id',auth()->user()->agents_id)
                        ->orderByDesc('sas.id')
                        ->limit(1)
                        ->first();

         $credit_budgetaires = DB::table('familles as f')
                        ->join('credit_budgetaires as cb','cb.ref_fam','=','f.ref_fam')
                        ->join('articles as a','a.ref_fam','=','cb.ref_fam')
                        ->select('f.id','f.ref_fam','f.design_fam')
                        ->distinct('f.id','f.ref_fam','f.design_fam')
                        ->where('cb.ref_depot',$ref_depot)
                        ->where('cb.exercice',$exercices->exercice)
                        ->get();

         $credit_budgetaires_select = null;
         $griser = 1;
         $devises = Devise::all();
        return view('travaux.create',[
            'periodes'=>$periodes,
            'taxes'=>$taxes,
            'organisations'=>$organisations,
            'num_bc'=>$num_bc,
            'services'=>$services,
            'familles'=>$familles,
            'nbre_ligne'=>$nbre_ligne,
            'gestions'=>$gestions,
            'section'=>$section,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'nbre_ligne'=>$nbre_ligne,
            'exercices'=>$exercices,
            'devises'=>$devises,
            'structures'=>$structures,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit

        ]);
    }

    public function create_credit($crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null)
    {

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
            return redirect('/travaux/create')->with('error', 'Veuillez saisir un compte valide');
        }

        $structure = Structure::where('code_structure',$decrypted_code_structure)->first();
        if($structure === null){
            return redirect('/travaux/create')->with('error', 'Veuillez saisir une structure valide');
        }

        $gestion = Gestion::where('code_gestion',$decrypted_code_gestion)->first();
        if($gestion === null){
            return redirect('/travaux/create')->with('error', 'Veuillez saisir une gestion valide');
        }

        $structures = [];
        if (Session::has('profils_id')) {
            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $ref_depot = $infoUserConnect->ref_depot;

                $structures = $this->getStructuresByRefDepot($ref_depot);

            }
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $etape = "create";

        $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $ref_depot = $profil->ref_depot;
        }

        $exercices = $this->getExercice();

        $credit_budgetaires_credit = null;
        $disponible = null;
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

        $affiche_zone_struture = 1;

        $gestions = DB::table('gestions')
                        ->get();
        $gestion_default = DB::table('gestions')
                            ->where('code_gestion','G')
                            ->first();

        $periodes = Periode::all();

        $taxes = Taxe::all();

        $organisations = $this->getOrganisationActifs(); 

         //générer le N° Bon de commande
         
 
         $num_bc = "";
 
         $services = Service::all();

         $familles = DB::table('familles')->get();

         $nbre_ligne = 1000;

         $section = DB::table('sections as s')
                        ->join('agent_sections as ase','ase.sections_id','=','s.id')
                        ->join('structures as st','st.code_structure','=','s.code_structure')
                        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                        ->where('tsas.libelle','Activé')
                        ->where('ase.agents_id',auth()->user()->agents_id)
                        ->orderByDesc('sas.id')
                        ->limit(1)
                        ->first();

         $credit_budgetaires = DB::table('familles as f')
                        ->join('credit_budgetaires as cb','cb.ref_fam','=','f.ref_fam')
                        ->join('articles as a','a.ref_fam','=','cb.ref_fam')
                        ->select('f.id','f.ref_fam','f.design_fam')
                        ->distinct('f.id','f.ref_fam','f.design_fam')
                        ->where('cb.ref_depot',$ref_depot)
                        ->where('cb.exercice',$exercices->exercice)
                        ->get();
        
        $credit_budgetaires_select = DB::table('familles')
                        ->where('id',$famille->id)
                        ->first();

         $griser = null;
         $devises = Devise::all();

         

        return view('travaux.create',[
            'periodes'=>$periodes,
            'taxes'=>$taxes,
            'organisations'=>$organisations,
            'num_bc'=>$num_bc,
            'services'=>$services,
            'familles'=>$familles,
            'nbre_ligne'=>$nbre_ligne,
            'gestions'=>$gestions,
            'section'=>$section,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'exercices'=>$exercices,
            'gestion_default'=>$gestion_default,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'structures'=>$structures,
            'devises'=>$devises,
            'disponible'=>$disponible,
            'structure_default'=>$structure,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit

        ]);
    }

    public function crypt($ref_fam,$code_structure,$code_gestion){
        
        return redirect('travaux/create/'.Crypt::encryptString($ref_fam).'/'.Crypt::encryptString($code_structure).'/'.Crypt::encryptString($code_gestion).'');

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
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));   

        $etape = "store";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);

        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $profils_id = $profils->id;
            $ref_depot = $profils->ref_depot;
        }        
        
        $validate = $request->validate([
            'code_structure'=>['required','string'],
            'nom_structure'=>['required','string'],
            'code_gestion'=>['required','string'],
            'libelle_gestion'=>['required','string'],
            'code_devise'=>['required','string'],
            'libelle_devise'=>['required','string'],
            'credit_budgetaires_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'periodes_id'=>['required','string'],
            'valeur'=>['required','string'],
            'delai'=>['required','string'],
            'date_echeance'=>['required','string'],
            'ref_fam'=>['required','string'],
            'organisations_id'=>['required','string'],
            'denomination'=>['required','string'],
            'libelle_service'=>['required','array'],
            'qte'=>['required','array'],
            'prix_unit'=>['required','array'],
            'remise'=>['nullable','array'],
            'montant_ht'=>['required','array'],
            'montant_total_brut'=>['required','string'],
            'taux_remise_generale'=>['nullable','string'],
            'remise_generale'=>['nullable','string'],
            'montant_total_net'=>['required','string'],
            'tva'=>['nullable','string'],
            'montant_tva'=>['nullable','string'],
            'montant_total_ttc'=>['required','string'],
            'net_a_payer'=>['required','string'],
            'taux_acompte'=>['nullable','string'],
            'montant_acompte'=>['nullable','string'],
            'date_livraison_prevue'=>['nullable','date'],
        ]);
        
        
        $error = $this->validate_saisie($request);
        
        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_brut = filter_var($request->montant_total_brut,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
        
        $info = 'Montant total brut';
        $error = $this->setDecimal($montant_total_brut,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }


        $remise_generale = filter_var($request->remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Remise générale';
        $error = $this->setDecimal($remise_generale,$info);

        if (isset($error)) {
            $remise_generale = null;
        }

        $taux_remise_generale = filter_var($request->taux_remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Taux remise générale';
        $error = $this->setDecimal($taux_remise_generale,$info);

        if (isset($error)) {
            $taux_remise_generale = null;
        }



        $montant_total_net = filter_var($request->montant_total_net,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            
        $info = 'Montant total net';
        $error = $this->setDecimal($montant_total_net,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_ttc = filter_var($request->montant_total_ttc,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            
        $info = 'Montant total ttc';
        $error = $this->setDecimal($montant_total_ttc,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }


        $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
               
        $info = 'Net à payer';
        $error = $this->setDecimal($net_a_payer,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }


        $date_echeance = null;
        $block = explode('/',$request->date_echeance);

        if (isset($block[0]) && isset($block[1]) && isset($block[2])) {
            $jour = $block[0];
            $mois = $block[1];
            $annee = $block[2];


            $date_echeance = $annee.'-'.$mois.'-'.$jour;
        }

        $date_livraison_prevue = $request->date_livraison_prevue;


        $date_retrait = $request->date_retrait;
        

        $periode = Periode::where('libelle_periode',$request->periodes_id)->first();

        if ($periode!=null) {
            $periodes_id = $periode->id;
        }else{
            return redirect()->back()->with('error','Echéance incorrecte');
        }


        $exercices = $this->getExercice();

        //générer le N° Bon de commande
        $num_bc = $this->getLastNumBcn($exercices->exercice,$request->code_structure); 

        

        if (isset($request->acompte)) {
            $taux_acompte = $request->taux_acompte;

            $montant_acompte = filter_var($request->montant_acompte,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

            $info = 'Montant acompte';
            $error = $this->setDecimal($montant_acompte,$info);

            if (isset($error)) {
                return redirect()->back()->with('error',$error);
            }


            if ($montant_acompte!=0) {
                $acompte = true;
            }else{
                $acompte = false;
            }
        }else{
            $montant_acompte = null;
            $acompte = false;
            $taux_acompte = null;
        }

        $devises_id = null;
        $taux_de_change = null;

        if (isset($request->code_devise)) {

            $devises_id = $this->getDevise($request->code_devise,$request->libelle_devise);

            $taux_de_change = $this->thmx_currency_convert($request->code_devise);

        
        }

        if ($devises_id === null) {
            return redirect()->back()->with('error','Veuillez saisir une devise valide');
        }

        

        $data = [
            'num_bc'=>$num_bc,
            'intitule'=>$request->intitule,
            'exercice'=>$exercices->exercice,
            'organisations_id'=>$request->organisations_id,
            'credit_budgetaires_id'=>$request->credit_budgetaires_id,
            'devises_id'=>$devises_id,
            'code_structure'=>$request->code_structure,
            'code_gestion'=>$request->code_gestion,
            'ref_depot'=>$ref_depot,
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$request->tva,
            'montant_total_ttc'=>$montant_total_ttc,
            'net_a_payer'=>$net_a_payer,
            'acompte'=>$acompte,
            'taux_acompte'=>$taux_acompte,
            'montant_acompte'=>$montant_acompte,
            'delai'=>$request->delai,
            'periodes_id'=>$periodes_id,
            'date_echeance'=>$date_echeance,
            'ref_fam'=>$request->ref_fam,
            'date_livraison_prevue'=>$date_livraison_prevue,
            'date_retrait'=>$date_retrait,
            'taux_de_change'=>$taux_de_change,
        ];

        $travauxes_id = Travaux::create($data)->id;

        
        $this->storeDetailTravaux($request,$travauxes_id);

        

        if ($travauxes_id!=null) {

            //statut bon de commande

            $libelle = 'Soumis pour validation';

            $commentaire = null;

            if (isset($request->commentaire)) {
                $commentaire = $request->commentaire;
            }
            $this->storeStatutTravaux($libelle,$travauxes_id,$profils_id,$commentaire);

            if (isset($request->piece)) {

                if (count($request->piece) > 0) {

                    foreach ($request->piece as $item => $value) {
                        if (isset($request->piece[$item])) {

                            $piece =  $request->piece[$item]->store('piece_jointe','public');

                            $name = $request->piece[$item]->getClientOriginalName();

                            $libelle_piece = "Commande non stockable";
                            $flag_actif = 1;
                            $piece_jointes_id = null;

                            $this->piece_jointe($travauxes_id,$profils_id,$libelle_piece,$piece,$flag_actif,$name,$piece_jointes_id);
                            
                            
                        }
                    }
                    

                }

            }

            // $travauxes_id = Crypt::encryptString($travauxes_id);
            $subject = "Bon de commande enregistré";
            $type_profils_names = ['Gestionnaire des achats'];

            $this->sendMailUsers($subject,$travauxes_id,$type_profils_names);

            return redirect('/travaux/index/')->with('success','Bon de commande enregistré');
        }else{
            return redirect()->back()->with('error','Echec de l\'enregistrement');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Travaux  $travaux
     * @return \Illuminate\Http\Response
     */
    public function show($travaux)
    {
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($travaux);
        } catch (DecryptException $e) {
            //
        }

        $travaux = Travaux::findOrFail($decrypted);

        

        /*$travauxe = DB::table('travauxes as t')
            ->join('structures as s', 's.code_structure', '=', 't.code_structure')
            ->join('gestions as g', 'g.code_gestion', '=', 't.code_gestion')
            ->join('organisations as o', 'o.id', '=', 't.organisations_id')
            ->join('devises as d', 'd.id', '=', 't.devises_id')
            ->join('periodes as p', 'p.id', '=', 't.periodes_id')
            ->join('familles as f', 'f.ref_fam', '=', 't.ref_fam')
            ->select('t.id as travauxes_id', 't.num_bc', 't.intitule', 't.ref_fam', 't.code_structure', 't.ref_depot', 't.code_gestion', 't.exercice', 't.montant_total_brut', 't.remise_generale', 't.montant_total_net', 't.tva', 't.montant_total_ttc', 't.net_a_payer', 't.acompte', 't.taux_acompte', 't.montant_acompte', 't.delai', 't.date_echeance', 's.nom_structure', 'f.design_fam', 't.credit_budgetaires_id', 'p.libelle_periode', 'o.id as organisations_id', 'o.denomination', 'd.code', 'd.libelle as devises_libelle', 't.created_at', 'p.valeur', 't.date_livraison_prevue', 't.date_retrait')
            ->where('t.id', $travaux->id)
            ->first();*/

            $travauxe = $this->getTravauxById($travaux->id);
            $detail_travauxes = $this->getDetailTravauxById($travaux->id);

        /*$detail_travauxes = DB::table('travauxes as t')
            ->join('detail_travauxes as dt','dt.travauxes_id','=','t.id')
            ->join('services as s','s.id','=','dt.services_id')
            ->select('s.libelle','dt.qte','dt.prix_unit','dt.remise','dt.montant_ht','dt.montant_ttc')
            ->where('t.id',$travaux->id)
            ->where('dt.flag_valide',1)
            ->get();*/

        $signataires = $this->getSignatairesTravaux($travaux->id);

            

        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

       
        
        $famille = Famille::where('ref_fam',$travaux->ref_fam)->first();

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $etape = "show";

        $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$travaux->id);

        $famille = Famille::where('ref_fam',$travaux->ref_fam)->first();
        
        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $ref_depot = $profil->ref_depot;
        }


        $exercices = $this->getExercice();

        $affiche_zone_struture = 1;

        $gestions = DB::table('gestions')
                        ->get();
        $gestion_default = DB::table('gestions')
                            ->where('code_gestion','G')
                            ->first();

        $periodes = Periode::all();

        $taxes = Taxe::all();

        $organisations = $this->getOrganisationActifs(); 

         //générer le N° Bon de commande
         
 
         $num_bc = "";
 
         $services = Service::all();

         $familles = DB::table('familles')->get();

         $nbre_ligne = 1000;

         $exercices = $this->getExercice();

         $section = DB::table('sections as s')
                        ->join('agent_sections as ase','ase.sections_id','=','s.id')
                        ->join('structures as st','st.code_structure','=','s.code_structure')
                        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                        ->where('tsas.libelle','Activé')
                        ->where('ase.agents_id',auth()->user()->agents_id)
                        ->orderByDesc('sas.id')
                        ->limit(1)
                        ->first();

         $credit_budgetaires = DB::table('familles as f')
                        ->join('credit_budgetaires as cb','cb.ref_fam','=','f.ref_fam')
                        ->join('articles as a','a.ref_fam','=','cb.ref_fam')
                        ->select('f.id','f.ref_fam','f.design_fam')
                        ->distinct('f.id','f.ref_fam','f.design_fam')
                        ->where('cb.ref_depot',$ref_depot)
                        ->where('cb.exercice',$exercices->exercice)
                        ->get();
        
        $credit_budgetaires_select = DB::table('familles')
                        ->where('id',$famille->id)
                        ->first();

        $structures = [];
        if ($section!=null) {

            $structures = DB::table('structures as s')
                            ->join('credit_budgetaires as cb','cb.code_structure','=','s.code_structure')
                            ->where('s.ref_depot',$ref_depot)
                            ->where('cb.ref_fam',$famille->ref_fam)
                            ->where('cb.exercice',$exercices->exercice)
                            ->whereRaw('cb.credit > 0')
                            ->select('s.code_structure','s.nom_structure','cb.id as credit_budgetaires_id')
                            ->get();

        }

         $griser = null;
         $devises = Devise::all();
         $piece_jointes = $this->getPieceJointesTravaux($travaux->id);
        return view('travaux.show',[
            'periodes'=>$periodes,
            'taxes'=>$taxes,
            'organisations'=>$organisations,
            'num_bc'=>$num_bc,
            'services'=>$services,
            'familles'=>$familles,
            'nbre_ligne'=>$nbre_ligne,
            'gestions'=>$gestions,
            'section'=>$section,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'exercices'=>$exercices,
            'gestion_default'=>$gestion_default,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'structures'=>$structures,
            'devises'=>$devises,
            'travauxe'=>$travauxe,
            'detail_travauxes'=>$detail_travauxes,
            'signataires'=>$signataires,
            'piece_jointes'=>$piece_jointes

        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Travaux  $travaux
     * @return \Illuminate\Http\Response
     */
    public function edit($travaux, Request $request, $limited=null,$crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null)
    {
        $famille = null;
        $structure = null;
        $gestion = null;
        $disponible = null;
        $disponible_display = null;
        $flag_engagement = 0;

        if($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null){

            $disponible_display = 1;

            $famille = Famille::where('ref_fam',$crypted_ref_fam)->first();
            
            
            if($famille === null){
                return redirect('/travaux/edit/'.$travaux)->with('error', 'Veuillez saisir un compte valide');
            }

            $structure = Structure::where('code_structure',$crypted_code_structure)->first();
            if($structure === null){
                return redirect('/travaux/edit/'.$travaux)->with('error', 'Veuillez saisir une structure valide');
            }

            $gestion = Gestion::where('code_gestion',$crypted_code_gestion)->first();
            if($gestion === null){
                return redirect('/travaux/edit/'.$travaux)->with('error', 'Veuillez saisir une gestion valide');
            }


        }

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($travaux);
        } catch (DecryptException $e) {
            //
        }

        $travaux = Travaux::findOrFail($decrypted);

        $travauxe = $this->getTravauxById($travaux->id);
        $detail_travauxes = $this->getDetailTravauxById($travaux->id);  
        
        if(isset($travauxe->flag_engagement)){
            $flag_engagement = $travauxe->flag_engagement;
        }

        
        if (Session::has('profils_id')) {
            # code...
        } else {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        

        $structures = [];

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $etape = "edit";

        $profil = $this->controlAcces($type_profils_name, $etape, auth()->user()->id, $travaux->id);     
        
        if ($profil === null) {
            return redirect()->back()->with('error', 'Accès refusé');
        } else {
            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

            if ($infoUserConnect != null) {
                $ref_depot = $infoUserConnect->ref_depot;
                $structures = $this->getStructuresByRefDepot($ref_depot);
            }
        }

        $credit_budgetaires_credit = null;

        if($travauxe != null && $crypted_ref_fam === null && $crypted_code_structure === null && $crypted_code_gestion === null){

            
            if($travauxe != null){
                try {
                    $param = $travauxe->exercice.'-'.$travauxe->code_gestion.'-'.$travauxe->code_structure.'-'.$travauxe->ref_fam;
        
                    $this->storeCreditBudgetaireByWebService($param, $travauxe->ref_depot);

                } catch (\Throwable $th) {
                }
            }

            $disponible = $this->getCreditBudgetaireDisponible($travauxe->ref_fam, $travauxe->code_structure, $travauxe->code_gestion, $travauxe->exercice);

            if($disponible != null){
                $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;                
            }

            $disponible_display = 1;

        }



        $exercices = $this->getExercice();

        if ($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null) {

            if (isset($famille) && isset($structure) && isset($exercices) && isset($gestion)) {
                try {
                    $param = $exercices->exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;

                    //$this->storeCreditBudgetaireByWebService($param, $structure->ref_depot);

                    $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam, $structure->code_structure, $gestion->code_gestion, $exercices->exercice);

                    if($disponible != null){
                        $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;                
                    }
               

                } catch (\Throwable $th) {
                }
            }

        } 

        if($flag_engagement === 1){
            $disponible_display = 1;
            $credit_budgetaires_credit = $travauxe->solde_avant_op;
            
        } 

        $affiche_zone_struture = 1;

        $gestions = DB::table('gestions')
                        ->get();
        $gestion_default = DB::table('gestions')
                            ->where('code_gestion','G')
                            ->first();

        $periodes = Periode::all();

        $taxes = Taxe::all();

        $organisations = $this->getOrganisationActifs(); 

         //générer le N° Bon de commande
         
 
         $num_bc = "";
 
         $services = Service::all();

         $familles = DB::table('familles')->get();

         $nbre_ligne = 1000;

         $exercices = $this->getExercice();

         $section = DB::table('sections as s')
                        ->join('agent_sections as ase','ase.sections_id','=','s.id')
                        ->join('structures as st','st.code_structure','=','s.code_structure')
                        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                        ->where('tsas.libelle','Activé')
                        ->where('ase.agents_id',auth()->user()->agents_id)
                        ->orderByDesc('sas.id')
                        ->limit(1)
                        ->first();

        $compte_budgetaires_select = null;

        if(isset($famille)){
            $compte_budgetaires_select = DB::table('familles')
            ->where('id',$famille->id)
            ->first();
        }elseif(isset($travauxe)){
            $compte_budgetaires_select = DB::table('familles')
            ->where('ref_fam',$travauxe->ref_fam)
            ->first();
        }
        

         $griser = null;
         $devises = Devise::all();

        $entete = "BON DE COMMANDE NON STOCKABLE";

        $value_bouton = null;
        $bouton = null;

        $value_bouton2 = null;
        $bouton2 = null;

        $value_bouton3 = null;
        $bouton3 = null;

        $url_complet = $request->url();
        
        $tableau = explode('travaux/',$url_complet);

        $url1 = $tableau[0];
        $url2 = $tableau[1];

        $tableau2 = explode('/',$url2);

        $page_view = $tableau2[0];

        // dd($url1,$url2,$page_view,$url_complet);

        $statut_travauxe = $this->getLastStatutTravaux($travaux->id);

        if ($statut_travauxe!=null) {
            $libelle = $statut_travauxe->libelle;
            $commentaire = $statut_travauxe->commentaire;
            $profil_commentaire = $statut_travauxe->name;
            $nom_prenoms_commentaire = $statut_travauxe->nom_prenoms;
        }

        $griser_button = 1;

        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
        ->join('fonctions as f','f.id','=','pf.fonctions_id')
        ->where('u.flag_actif',1)
        ->where('pf.flag_actif',1)
        ->select(DB::raw('a.mle'),DB::raw('a.nom_prenoms'),DB::raw('f.libelle'))
        ->groupBy('f.libelle','a.mle','a.nom_prenoms')
        ->get();

        $signataires = $this->getSignatairesTravaux($travaux->id);

            $edit_signataire = null;

        if ($type_profils_name === "Gestionnaire des achats") { 
            
            if ($libelle != "Soumis pour validation" && $libelle != "Rejeté (Responsable des achats)" && $libelle != "Annulé (Gestionnaire des achats)" && $libelle != "Annulé (Responsable des achats)" && $libelle != "Validé" && $libelle != "Édité" && $libelle != "Retiré (Frs.)") {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === "Soumis pour validation" or $libelle === "Rejeté (Responsable des achats)" or $libelle === "Annulé (Gestionnaire des achats)" or $libelle === "Annulé (Responsable des achats)" or $libelle === "Validé" or $libelle === "Édité" or $libelle === "Retiré (Frs.)") {
                $entete = "MODIFICATION DU BON DE COMMANDE";
                $griser_button = null;

                if ($libelle != "Annulé (Gestionnaire des achats)") {
                    $value_bouton2 = "annuler_g_achat";
                    $bouton2 = "Annuler";
                }

                if ($libelle === 'Validé' or $libelle === "Édité" or $libelle === "Retiré (Frs.)") {
                    $value_bouton = "editer";
                    $bouton = "Éditer";

                    if ($libelle === "Édité") {
                        $value_bouton3 = "livrer";
                        $bouton3 = "Livrer";
                    }
                    
                    $griser = 1;
                    $griser_button = null;
                    $edit_signataire = 1;

                    $value_bouton2 = null;
                    $bouton2 = null;

                } else {

                    if ($page_view === 'edit') {
                        $value_bouton = "modifier";
                        $bouton = "Modifier";
                    } elseif ($page_view === 'send') {
                        $entete = "TRANSMISSION DU BON DE COMMANDE AU RESPONSABLE DES ACHATS";

                        $value_bouton = "tr_r_achat";
                        $bouton = "Transmettre";
                    }

                }

                

            }

            
        }elseif ($type_profils_name === 'Responsable des achats') {

            
            
            if ( $libelle != 'Transmis (Responsable des achats)' && $libelle != 'Rejeté (Responsable DMP)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Responsable des achats)' or $libelle === 'Rejeté (Responsable DMP)'){

                $entete = "VALIDATION DU BON DE COMMANDE";

                

                

                if ($libelle != 'Annulé (Responsable des achats)'){
                    $value_bouton2 = "annuler_r_achat";
                    $bouton2 = "Annuler";
                }

                

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_achat";
                $bouton = "Valider";

                
                

                
            }

        }elseif ($type_profils_name === 'Responsable DMP') {

            
            
            if ( $libelle != 'Transmis (Responsable DMP)' && $libelle != 'Rejeté (Responsable Contrôle Budgétaire)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Responsable DMP)' or $libelle === 'Rejeté (Responsable Contrôle Budgétaire)'){

                $entete = "VISA DU BON DE COMMANDE";

                if ($libelle != 'Rejeté (Responsable DMP)'){
                    $value_bouton2 = "annuler_r_cmp";
                    $bouton2 = "Rejeté";
                }

                

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_cmp";
                $bouton = "Valider";

                
                

                
            }

        }elseif ($type_profils_name === 'Responsable contrôle budgetaire') {

            
            
            if ( $libelle != 'Transmis (Responsable Contrôle Budgétaire)' && $libelle != 'Rejeté (Chef Département DCG)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Responsable Contrôle Budgétaire)' or $libelle === 'Rejeté (Chef Département DCG)'){

                $entete = "VISA DU BON DE COMMANDE";

                if ($libelle != 'Rejeté (Responsable Contrôle Budgétaire)'){
                    $value_bouton2 = "annuler_r_cb";
                    $bouton2 = "Rejeté";
                }

                

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_cb";
                $bouton = "Valider";

                
                

                
            }

        }elseif ($type_profils_name === 'Chef Département DCG') {

            
            
            if ( $libelle != 'Transmis (Chef Département DCG)' && $libelle != 'Rejeté (Responsable DCG)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Chef Département DCG)' or $libelle === 'Rejeté (Responsable DCG)'){

                $entete = "VISA DU BON DE COMMANDE";

                if ($libelle != 'Rejeté (Chef Département DCG)'){
                    $value_bouton2 = "annuler_r_dep";
                    $bouton2 = "Rejeté";
                }

                

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_dep";
                $bouton = "Valider";

                
                

                
            }

        }elseif ($type_profils_name === 'Responsable DCG') {

            
            
            if ( $libelle != 'Transmis (Responsable DCG)' && $libelle != 'Rejeté (Directeur Général Adjoint)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Responsable DCG)' or $libelle === 'Rejeté (Directeur Général Adjoint)'){

                $entete = "VISA DU BON DE COMMANDE";

                if ($libelle != 'Rejeté (Responsable DCG)'){
                    $value_bouton2 = "annuler_r_dcg";
                    $bouton2 = "Rejeté";
                }

                

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_dcg";
                $bouton = "Valider";

                
                

                
            }

        }elseif ($type_profils_name === 'Directeur Général Adjoint') {

            
            
            if ( $libelle != 'Transmis (Directeur Général Adjoint)' && $libelle != 'Rejeté (Directeur Général)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Directeur Général Adjoint)' or $libelle === 'Rejeté (Directeur Général)'){

                $entete = "VISA DU BON DE COMMANDE";

                if ($libelle != 'Rejeté (Directeur Général Adjoint)'){
                    $value_bouton2 = "annuler_r_dgaaf";
                    $bouton2 = "Rejeté";
                }

                

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_dgaaf";
                $bouton = "Valider";

                
                

                
            }

        }elseif ($type_profils_name === 'Directeur Général') {

            
            
            if ( $libelle != 'Transmis (Directeur Général)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Transmis (Directeur Général)'){

                $entete = "VISA DU BON DE COMMANDE";

                if ($libelle != 'Rejeté (Directeur Général)'){
                    $value_bouton2 = "annuler_r_dg";
                    $bouton2 = "Rejeté";
                }

                $griser = 1;
                $griser_button = null;

                $value_bouton = "visa_r_dg";
                $bouton = "Valider";
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        
        $piece_jointes = $this->getPieceJointesTravaux($travaux->id);

        return view('travaux.edit',[
            'periodes'=>$periodes,
            'taxes'=>$taxes,
            'organisations'=>$organisations,
            'num_bc'=>$num_bc,
            'services'=>$services,
            'familles'=>$familles,
            'nbre_ligne'=>$nbre_ligne,
            'gestions'=>$gestions,
            'section'=>$section,
            'compte_budgetaires_select'=>$compte_budgetaires_select,
            'griser'=>$griser,
            'exercices'=>$exercices,
            'gestion_default'=>$gestion_default,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'structures'=>$structures,
            'devises'=>$devises,
            'travauxe'=>$travauxe,
            'detail_travauxes'=>$detail_travauxes,
            'bouton'=>$bouton,
            'bouton2'=>$bouton2,
            'value_bouton'=>$value_bouton,
            'value_bouton2'=>$value_bouton2,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'entete'=>$entete,
            'griser_button'=>$griser_button,
            'edit_signataire'=>$edit_signataire,
            'agents'=>$agents,
            'signataires'=>$signataires,
            'value_bouton3'=>$value_bouton3,
            'bouton3'=>$bouton3,
            'statut_travauxes_libelle'=>$libelle,
            'piece_jointes'=>$piece_jointes,
            'disponible'=>$disponible,
            'disponible_display'=>$disponible_display,
            'structure'=>$structure,
            'famille'=>$famille,
            'gestion'=>$gestion,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Travaux  $travaux
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $controllerPerdiem = new ControllerPerdiem();
        $signatureController = new SignatureController();
        $type_operations_libelle = "Commande non stockable" ;
        $edit_signataire_demande_de_fonds = null;
        $edit_signataire_bcn = null;
        $ordre_de_signature = null;
        $ordre_de_signature_demande_de_fonds = null;
        $ordre_engagement = null;

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $validate = $request->validate([
            'travauxes_id'=>['required','numeric'],
        ]);

        $etape = "update";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$request->travauxes_id);

        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $profils_id = $profils->id;
            $ref_depot = $profils->ref_depot;
        }
        
        $validate = $request->validate([
            'code_structure'=>['required','string'],
            'nom_structure'=>['required','string'],
            'code_gestion'=>['required','string'],
            'libelle_gestion'=>['required','string'],
            'code_devise'=>['required','string'],
            'libelle_devise'=>['required','string'],
            'credit_budgetaires_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'periodes_id'=>['required','string'],
            'valeur'=>['required','string'],
            'delai'=>['required','string'],
            'date_echeance'=>['required','string'],
            'ref_fam'=>['required','string'],
            'organisations_id'=>['required','string'],
            'denomination'=>['required','string'],
            'libelle_service'=>['required','array'],
            'qte'=>['required','array'],
            'prix_unit'=>['required','array'],
            'remise'=>['nullable','array'],
            'montant_ht'=>['required','array'],
            'montant_total_brut'=>['required','string'],
            'taux_remise_generale'=>['nullable','string'],
            'remise_generale'=>['nullable','string'],
            'montant_total_net'=>['required','string'],
            'tva'=>['nullable','string'],
            'montant_tva'=>['nullable','string'],
            'montant_total_ttc'=>['required','string'],
            'net_a_payer'=>['required','string'],
            'taux_acompte'=>['nullable','string'],
            'montant_acompte'=>['nullable','string'],
            'travauxes_id'=>['required','numeric'],
        ]);

        if ($request->submit === 'annuler_g_achat' or $request->submit === 'annuler_r_achat' or $request->submit === 'annuler_r_cmp' or $request->submit === 'annuler_r_cb' or $request->submit === 'annuler_r_dep' or $request->submit === 'annuler_r_dcg' or $request->submit === 'annuler_r_dgaaf' or $request->submit === 'annuler_r_dg') {
            $validate = $request->validate([
                'commentaire'=>['required','string'],
            ]);
        }

        $error = $this->validate_saisie($request);
        
        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_brut = filter_var($request->montant_total_brut,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total brut';
        $error = $this->setDecimal($montant_total_brut,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $taux_remise_generale = filter_var($request->taux_remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Taux remise générale';
        $error = $this->setDecimal($taux_remise_generale,$info);

        if (isset($error)) {
            $taux_remise_generale = null;
        }
        
        $remise_generale = filter_var($request->remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Remise générale';
        $error = $this->setDecimal($remise_generale,$info);

        if (isset($error)) {
            $remise_generale = null;
        }

        $montant_total_net = filter_var($request->montant_total_net,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            
        $info = 'Montant total net';
        $error = $this->setDecimal($montant_total_net,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_ttc = filter_var($request->montant_total_ttc,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            
        $info = 'Montant total ttc';
        $error = $this->setDecimal($montant_total_ttc,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
               
        $info = 'Net à payer';
        $error = $this->setDecimal($net_a_payer,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }


        $date_echeance = null;
        $block = explode('/',$request->date_echeance);

        if (isset($block[0]) && isset($block[1]) && isset($block[2])) {

            $jour = $block[0];
            $mois = $block[1];
            $annee = $block[2];

            $date_echeance = $annee.'-'.$mois.'-'.$jour;

        }

        $date_livraison_prevue =  $request->date_livraison_prevue;

        $date_retrait = $request->date_retrait;

        $periode = Periode::where('libelle_periode',$request->periodes_id)->first();

        if ($periode!=null) {
            $periodes_id = $periode->id;
        }else{
            return redirect()->back()->with('error','Echéance incorrecte');
        }

        if (isset($request->taux_acompte)) {
            
            if (isset($request->acompte)) {
                $taux_acompte = $request->taux_acompte;

                $montant_acompte = filter_var($request->montant_acompte,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
    
                $info = 'Montant acompte';
                $error = $this->setDecimal($montant_acompte,$info);
    
                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }
    
    
                $acompte = true;
            }else{
                $montant_acompte = null;
                $acompte = false;
                $taux_acompte = null;
            }
            
            
        }else{
            $montant_acompte = null;
            $acompte = false;
            $taux_acompte = null;
        }


        $num_bc = null;

        $taux_de_change = 1;
        $travaux = $this->getTravauxById($request->travauxes_id);
        if($travaux != null){

            $explode_num_bc = explode('/',explode('BCN', $travaux->num_bc)[0])[1];
            if($explode_num_bc === $request->code_structure){
                
                $num_bc = $travaux->num_bc;

            }else{
                $exercices = $this->getExercice();

                $num_bc = $this->getLastNumBcn($exercices->exercice,$request->code_structure,$request->travauxes_id);
            

            }
            $taux_de_change = $travaux->taux_de_change;

            if($travaux->acompte == 1){
                $montant_acompte = $travaux->montant_acompte * $taux_de_change;
            }

        }

        if($num_bc === null){
            return redirect()->back()->with('error','Numéro BCN introuvable');
        }

        $devises_id = null;
        $taux_de_change = null;

        if (isset($request->code_devise)) {

            $devises_id = $this->getDevise($request->code_devise,$request->libelle_devise);

            $taux_de_change = $this->thmx_currency_convert($request->code_devise);

           // $taux_de_change = 1;
        
        }

        if ($devises_id === null) {
            return redirect()->back()->with('error','Veuillez saisir une devise valide');
        }

        if($request->submit === 'editer'){
            $data = [
                'intitule'=>$request->intitule,
                'organisations_id'=>$request->organisations_id,
                'credit_budgetaires_id'=>$request->credit_budgetaires_id,
                'devises_id'=>$devises_id,
                'code_structure'=>$request->code_structure,
                'code_gestion'=>$request->code_gestion,
                'ref_depot'=>$ref_depot,
                'montant_total_brut'=>$montant_total_brut,
                'taux_remise_generale'=>$taux_remise_generale,
                'remise_generale'=>$remise_generale,
                'montant_total_net'=>$montant_total_net,
                'tva'=>$request->tva,
                'montant_total_ttc'=>$montant_total_ttc,
                'net_a_payer'=>$net_a_payer,
                'acompte'=>$acompte,
                'taux_acompte'=>$taux_acompte,
                'montant_acompte'=>$montant_acompte,
                'delai'=>$request->delai,
                'periodes_id'=>$periodes_id,
                'date_echeance'=>$date_echeance,
                'date_livraison_prevue'=>$date_livraison_prevue,
                'date_retrait'=>$date_retrait,
                'num_bc'=>$num_bc
            ];
        }else{
            $data = [
                'intitule'=>$request->intitule,
                'organisations_id'=>$request->organisations_id,
                'credit_budgetaires_id'=>$request->credit_budgetaires_id,
                'devises_id'=>$devises_id,
                'code_structure'=>$request->code_structure,
                'code_gestion'=>$request->code_gestion,
                'ref_depot'=>$ref_depot,
                'montant_total_brut'=>$montant_total_brut,
                'taux_remise_generale'=>$taux_remise_generale,
                'remise_generale'=>$remise_generale,
                'montant_total_net'=>$montant_total_net,
                'tva'=>$request->tva,
                'montant_total_ttc'=>$montant_total_ttc,
                'net_a_payer'=>$net_a_payer,
                'acompte'=>$acompte,
                'taux_acompte'=>$taux_acompte,
                'montant_acompte'=>$montant_acompte,
                'delai'=>$request->delai,
                'periodes_id'=>$periodes_id,
                'date_echeance'=>$date_echeance,
                'date_livraison_prevue'=>$date_livraison_prevue,
                'date_retrait'=>$date_retrait,
                'taux_de_change'=>$taux_de_change,
                'num_bc'=>$num_bc
            ];
        }
        

        $travauxes_id = $request->travauxes_id;

        $travauxes_credit_budgetaire = $this->getTravauxCreditBudgetaireById($travauxes_id);

        $libelle = null;
        $subject = null;
        $type_profils_names = [];
        $edit_piece = null;     
        
        if($travauxes_credit_budgetaire != null){
            $solde_avant_op = $travauxes_credit_budgetaire->credit - $travauxes_credit_budgetaire->consommation_non_interfacee;
        }

        if (isset($request->submit)) {

            $taux_de_change = 1;
            $net_a_payers = 0;

            if(isset($request->net_a_payer)){
                $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                try {
                    $taux_de_change = Travaux::where('id',$travauxes_id)->first()->taux_de_change;
                } catch (\Throwable $th) {
                    //throw $th;
                }
    
                $net_a_payers = $net_a_payer * $taux_de_change;
            }

            

            if ($request->submit === 'modifier' or $request->submit === 'tr_r_achat') {
                $edit_piece = 1;
                
                if ($request->submit === 'modifier') {
                    $libelle = 'Soumis pour validation';
                    $libelle_df = 'Imputé (Gestionnaire des achats)';

                    
                }elseif ($request->submit === 'tr_r_achat') {
                    $libelle = 'Transmis (Responsable des achats)';
                    $libelle_df = 'Transmis (Responsable des achats)';
                    $subject = "Bon de commande transmis au Responsable des achats" ;
                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats'];

                    
                }

                Travaux::where('id',$request->travauxes_id)->update($data);

                try {

                    DetailTravaux::where('travauxes_id',$travauxes_id)->delete();

                } catch (\Throwable $th) {
                    
                }

                $this->storeDetailTravaux($request,$travauxes_id);

            }elseif ($request->submit === 'annuler_g_achat') {

                $libelle = 'Annulé (Gestionnaire des achats)';
                $libelle_df = 'Annulé (Gestionnaire des achats)';

            }elseif ($request->submit === 'annuler_r_achat') {

                $libelle = 'Annulé (Responsable des achats)';
                $libelle_df = 'Annulé (Responsable des achats)';


                $subject = "Bon de commande annulé par le Responsable des Achats" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats'];

            }elseif ($request->submit === 'visa_r_achat') {

                $libelle = 'Validé (Responsable des achats)';
                $libelle_send = 'Transmis (Responsable DMP)';
                $libelle_df = 'Transmis (Responsable DMP)';


                $subject = "Bon de commande transmis au Responsable DMP" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];

            }elseif ($request->submit === 'annuler_r_cmp') {

                $libelle = 'Rejeté (Responsable DMP)';
                $libelle_df = 'Annulé (Responsable DMP)';

                $subject = "Bon de commande annulé par la Responsable DMP" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];
                
                if($travaux->flag_engagement === 1){
                    $ordre_engagement = 2;
                }
                
            }elseif ($request->submit === 'visa_r_cmp') {

                $libelle = 'Validé (Responsable DMP)';
                $libelle_send = 'Transmis (Responsable Contrôle Budgétaire)';
                $libelle_df = 'Transmis (Responsable Contrôle Budgétaire)';

                $subject = "Bon de commande transmis au Responsable DMP" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire'];

                $edit_signataire_demande_de_fonds = 1;

                if($travaux->flag_engagement === 0){
                    $ordre_engagement = 1;
                }
                

            }elseif ($request->submit === 'annuler_r_cb') {

                $libelle = 'Rejeté (Responsable Contrôle Budgétaire)';
                $libelle_df = 'Annulé (Responsable contrôle budgetaire)';

                $subject = "Bon de commande annulé par le Responsable Contrôle Budgétaire" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire'];

            }elseif ($request->submit === 'visa_r_cb') {

                $libelle = 'Validé (Responsable Contrôle Budgétaire)';
                $libelle_send = 'Transmis (Chef Département DCG)';
                $libelle_df = 'Transmis (Chef Département Contrôle Budgétaire)';

                $subject = "Bon de commande transmis au Chef Département DCG" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG'];

            }elseif ($request->submit === 'annuler_r_dep') {

                $libelle = 'Rejeté (Chef Département DCG)';
                $libelle_df = 'Annulé (Chef Département Contrôle Budgétaire)';

                $subject = "Bon de commande annulé par le Chef Département DCG" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG'];

            }elseif ($request->submit === 'visa_r_dep') {

                $libelle = 'Validé (Chef Département DCG)';
                $libelle_send = 'Transmis (Responsable DCG)';
                $libelle_df = 'Transmis (Responsable DCG)';

                $subject = "Bon de commande transmis au Responsable DCG" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];

            }elseif ($request->submit === 'annuler_r_dcg') {

                $libelle = 'Rejeté (Responsable DCG)';
                $libelle_df = 'Annulé (Responsable DCG)';

                $subject = "Bon de commande annulé par le Responsable DCG" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];

            }elseif ($request->submit === 'visa_r_dcg') {

                $libelle = 'Validé (Responsable DCG)';

                $libelle_send = 'Transmis (Directeur Général Adjoint)';
                $libelle_df = 'Transmis (Directeur Général Adjoint)';

                $subject = "Bon de commande transmis au DGAAF" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];

            }elseif ($request->submit === 'annuler_r_dgaaf') {

                $libelle = 'Rejeté (Directeur Général Adjoint)';
                $libelle_df = 'Annulé (Directeur Général Adjoint)';

                $subject = "Bon de commande annulé par le DGAAF" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];

            }elseif ($request->submit === 'visa_r_dgaaf') {

                $libelle = 'Validé (Directeur Général Adjoint)';

                if ($net_a_payers > 5000000) {
                    $libelle_send = 'Transmis (Directeur Général)';
                    $libelle_df = 'Transmis (Directeur Général)';

                    $subject = "Bon de commande transmis au DG" ;

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général'];

                }else{
                    $libelle_send = "Validé";
                    $libelle_df = "Validé";

                    $subject = "Bon de commande validé" ;

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général'];

                    $edit_signataire_demande_de_fonds = 1;
                    $edit_signataire_bcn = 1;
                    $ordre_de_signature = 1;
                    $ordre_de_signature_demande_de_fonds = 1;
                }

            }elseif ($request->submit === 'annuler_r_dg') {

                $libelle = 'Rejeté (Directeur Général)';
                $libelle_df = 'Annulé (Directeur Général)';

                $subject = "Bon de commande annulé par le DG" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général'];

            }elseif ($request->submit === 'visa_r_dg') {                

                $libelle = 'Validé (Directeur Général)';
                $libelle_send = "Validé";
                $libelle_df = "Validé";
                $subject = "Bon de commande validé" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général'];

                $edit_signataire_demande_de_fonds = 1;
                $edit_signataire_bcn = 1;
                $ordre_de_signature = 1;
                $ordre_de_signature_demande_de_fonds = 1;                    
                

            }elseif ($request->submit === 'editer') {
                $edit_piece = 1;
                
                if (isset($request->profil_fonctions_id) && isset($request->mle) && isset($request->nom_prenoms)) {

                    $signataires = $this->getSignatairesTravaux($travauxes_id);

                    $signature = $controllerPerdiem->verificationSignataire($signataires,$request->profil_fonctions_id);
                    if($signature != 0){
                        $this->storeSignataireTravaux($request,$travauxes_id,$profils_id);
                    }

                    $libelle = 'Édité';
                    $libelle_df = 'Édité';

                    $this->storeTypeOperation($type_operations_libelle);

                    $type_operation = $this->getTypeOperation($type_operations_libelle);
                    if ($type_operation!=null) {

                        $type_operations_id = $type_operation->id;

                        $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($travauxes_id,$type_operations_id);

                        if($demande_fond_bon_commande != null){                        

                            if(isset($libelle_df)){

                                if (isset($request->mle)) {

                                    if (count($request->mle) > 0) {

                                        
                                        $profil_fonction_array = null;
                                        if(isset($request->profil_fonctions_id)){
                                            if(count($request->profil_fonctions_id) > 0) {
                                                $profil_fonction_array = $request->profil_fonctions_id;

                                                if($profil_fonction_array != null) {
                                                    $type_operations_libelle_explode = "Demande de fonds";

                                                    $this->setSignataire($type_operations_libelle_explode,$demande_fond_bon_commande->demande_fonds_id,$profil_fonction_array);
                                                }
                                            }

                                        }

                                        foreach ($request->mle as $item => $value) {

                                                $profil_fonction = $this->getProfilFonctionByMle($value);

                                                if($profil_fonction != null){

                                                    $this->storeSignataireDemandeFond2($profil_fonction->id,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'));

                                                }
                                            

                                        }

                                    }

                                }

                            }
                            

                        }
                    }

                }                

                $subject = "Bon de commande édité" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général'];


                
                Travaux::where('id',$request->travauxes_id)->update($data);

                try {

                    DetailTravaux::where('travauxes_id',$travauxes_id)->delete();

                } catch (\Throwable $th) {
                    
                }

                $this->storeDetailTravaux($request,$travauxes_id);
                /*

                    $data_path = [
                        'type_operations_libelle'=>'bcn',
                        'reference'=>$travaux->num_bc,
                        'extension'=>'pdf'
                    ];
                    
                    $public_path = $controllerPerdiem->publicPath($data_path);
                    
                    if(@file_exists($public_path) === false){
                        $signature = 1;
                    }

                    if($signature != 0){

                        $printController = new PrintController();
                        $printController->printTravaux(Crypt::encryptString($travauxes_id));

                    }
                */


            }elseif ($request->submit === 'livrer') {

                $edit_piece = 1;
                $libelle = 'Livré';

                $taux_de_change = 1;

                $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                try {
                    $taux_de_change = Travaux::where('id',$travauxes_id)->first()->taux_de_change;
                } catch (\Throwable $th) {
                    //throw $th;
                }
                

                $net_a_payers = $net_a_payer * $taux_de_change;

                if($travaux != null){
                        
                    $user = $this->getInfoUserByProfilId(Session::get('profils_id'));

                    if($user != null){
                        $type_piece = "ENG";
                        $reference_piece = $travaux->num_bc;
                        $compte = $travaux->ref_fam;

                        $montant_comptabilisation = -1 * $net_a_payers;
                        
                        $date_transaction = date('Y-m-d H:i:s');
                        $mle = $user->mle;
                        $code_structure = $travaux->code_structure;
                        $code_section = $travaux->code_structure."01";

                        $flag_acompte = 0;

                        $data_comptabilisation = [
                            'type_piece'=>$type_piece,
                            'reference_piece'=>$reference_piece,
                            'compte'=>$compte,
                            'montant'=>$montant_comptabilisation,
                            'date_transaction'=>$date_transaction,
                            'mle'=>$mle,
                            'code_structure'=>$code_structure,
                            'code_section'=>$code_section,
                            'ref_depot'=>$user->ref_depot,
                            'acompte'=>$flag_acompte,
                            'exercice'=>$travaux->exercice,
                            'code_gestion'=>$travaux->code_gestion,
                        ];

                        $this->storeComptabilisationEcriture($data_comptabilisation);

                    }
                    
                }


                $subject = "Bon de commande livré" ;

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général'];

            }

            
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

                $data_engagement = [
                    'type_operations_libelle'=>$type_operations_libelle,
                    'operations_id'=>$travauxes_id,
                    'solde_avant_op'=>$solde_avant_op,
                    'flag_engagement'=>$flag_engagement,
                    'this'=>$controllerPerdiem,
                    'signe'=>$signe,
                    'type_piece'=>$type_piece,
                    'montant_acompte'=>$montant_acompte,
                    'compte'=>$travaux->ref_fam,
                    'exercice'=>$travaux->exercice,
                    'code_structure'=>$travaux->code_structure,
                    'code_gestion'=>$travaux->code_gestion,
                ];

                $this->procedureEngagementEtEcritureComptable($data_engagement);
            }
            
            if(isset($edit_piece)){
                // détacher fichier joint
                $piece_jointes = DB::table('piece_jointes as pj')
                ->join('type_operations as to','to.id','=','pj.type_operations_id')
                ->where('to.libelle','Commande non stockable')
                ->where('pj.subject_id',$travauxes_id)
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

                        
                        $libelle_piece = "Commande non stockable";
                        $piece = null;
                        $name = null;

                        $this->piece_jointe($travauxes_id,$profils_id,$libelle_piece,$piece,$flag_actif,$name,$piece_jointes_id);
                    }
                }      

                if (isset($request->piece)) {

                    if (count($request->piece) > 0) {

                        foreach ($request->piece as $item => $value) {
                            if (isset($request->piece[$item])) {

                                $piece =  $request->piece[$item]->store('piece_jointe','public');

                                $name = $request->piece[$item]->getClientOriginalName();

                                $libelle_piece = "Commande non stockable";

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

                                
                                
                                
                                $this->piece_jointe($travauxes_id,$profils_id,$libelle_piece,$piece,$flag_actif,$name,$piece_jointes_id);
                                
                                
                            }
                        }
                        

                    }

                }
            }

            $info_user_connect =  $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($info_user_connect != null) {

                $profil_fonction = $this->getProfilFonctionByAgentId($info_user_connect->agents_id);

                if($profil_fonction != null){
                    $profil_fonctions_id = $profil_fonction->id;
                }
            }

            if($edit_signataire_demande_de_fonds === 1){

                $this->storeTypeOperation($type_operations_libelle);

                $type_operation = $this->getTypeOperation($type_operations_libelle);
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                    $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($travauxes_id,$type_operations_id);

                    if($demande_fond_bon_commande != null){                        
                        $demande_fonds_id = $demande_fond_bon_commande->demande_fonds_id;
                    }
                }
                
            }

            if($edit_signataire_demande_de_fonds === 1 && isset($libelle_df) && isset($profil_fonctions_id) && isset($demande_fonds_id)){
                $this->storeSignataireDemandeFond2($profil_fonctions_id,$demande_fonds_id,Session::get('profils_id'));
            }

            if($edit_signataire_bcn === 1 && isset($profil_fonctions_id)){
                $this->storeSignataireTravaux2($profil_fonctions_id, $travauxes_id, Session::get('profils_id'));
            }

            if($ordre_de_signature === 1){
                $data_ordre_signature = [
                    'type_operations_libelle'=>$type_operations_libelle,
                    'operations_id'=>$travauxes_id,
                    'reference'=>$travaux->num_bc,
                    'extension'=>'pdf',
                    'signature'=>1
                ];                        
                $signatureController->OrdreDeSignature($data_ordre_signature);
            }

            if($ordre_de_signature_demande_de_fonds === 1 && isset($demande_fonds_id)){

                $demande_fond = $this->getDemandeFond($demande_fonds_id);
                if($demande_fond != null){
                    $type_operations_libelle_demande_fonds = "Demande de fonds";
                    $data_ordre_signature = [
                        'type_operations_libelle'=>$type_operations_libelle_demande_fonds,
                        'operations_id'=>$demande_fond->id,
                        'reference'=>$demande_fond->num_dem,
                        'extension'=>'pdf',
                        'signature'=>1
                    ];
    
                    $signatureController->OrdreDeSignature($data_ordre_signature);
                }
                

            }

        }

        $this->sendMailUsers($subject,$travauxes_id,$type_profils_names);

        if ($travauxes_id!=null) {

            //statut bon de commande

            $commentaire = null;

            if (isset($request->commentaire)) {
                $commentaire = $request->commentaire;
            }

            if (isset($libelle)) {
                $this->storeStatutTravaux($libelle, $travauxes_id, $profils_id, $commentaire);
            }
            if(isset($libelle_send)){
                $this->storeStatutTravaux($libelle_send,$travauxes_id,$profils_id,$commentaire);
            }

            $this->storeTypeOperation($type_operations_libelle);

            $type_operation = $this->getTypeOperation($type_operations_libelle);
            if ($type_operation!=null) {

                $type_operations_id = $type_operation->id;

                $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($request->travauxes_id,$type_operations_id);

                if($demande_fond_bon_commande != null) {

                    if(isset($libelle_df)) {
                        $this->storeStatutDemandeFond($libelle_df, $demande_fond_bon_commande->demande_fonds_id, Session::get('profils_id'), $commentaire);
                    }

                }
            }
            
            $travauxes_id = Crypt::encryptString($request->travauxes_id);
            
            return redirect('/travaux/index/')->with('success','Bon de commande modifié');

        }else{
            return redirect()->back()->with('error','Echec de la modification');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Travaux  $travaux
     * @return \Illuminate\Http\Response
     */
    public function destroy(Travaux $travaux)
    {
        //
    }
}
