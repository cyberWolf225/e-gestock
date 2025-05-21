<?php

namespace App\Http\Controllers; 

use App\Models\DetailImmobilisation;
use Illuminate\Http\Request;
use App\Models\Immobilisation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ImmobilisationController extends Controller
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
    public function index(Request $request)
    {
        $sans_hierarchie = 1;
        $acces_create = null;
        $this->storeSessionBackground($request);

        $type_profils_name = null;

        if (Session::has('profils_id')) {
            
            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Administrateur fonctionnel','Responsable section entretien','Responsable Logistique','Gestionnaire entretien','Pilote AEE','Responsable des stocks','Gestionnaire des stocks','Responsable DMP'];

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }


        

        $affiche_demandeur = 1;
        $immobilisations = [];

        

            if ($type_profils_name === "Administrateur fonctionnel") {

                $libelle = "Soumis pour validation";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure); 

            }elseif ($type_profils_name === "Pilote AEE") {

                $libelle = "Soumis pour validation";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure);

                $acces_create = 1;

            }elseif ($type_profils_name === "Responsable section entretien" or $type_profils_name === "Responsable Logistique") {

                $libelle = "Transmis (Responsable section entretien)";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure);

            }elseif ($type_profils_name === "Gestionnaire entretien") {

                $libelle = "Transmis (Gestionnaire entretien)";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure);

            }elseif ($type_profils_name === "Responsable des stocks") {

                $libelle = "Transmis (Responsable des stocks)";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure);

            }elseif ($type_profils_name === "Gestionnaire des stocks") {

                $libelle = "Transmis (Gestionnaire des stocks)";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure);

            }elseif ($type_profils_name === "Responsable DMP") {

                $libelle = "Transmis (Responsable DMP)";
                $immobilisations = $this->getImmobilisations($libelle,$type_profils_name,$code_structure);

            }else{
                return redirect()->back()->with('error','Accès refusé');
            }
    
        

        return view('immobilisations.index',[
            'affiche_demandeur'=>$affiche_demandeur,
            'immobilisations'=>$immobilisations,
            'type_profils_name'=>$type_profils_name,
            'sans_hierarchie'=>$sans_hierarchie,
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
        $infoUserConnect = null;
        
        if (Session::has('profils_id')) {
            
            $etape = "create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }

            $type_profils_lists = ['Pilote AEE'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $articles = $this->getArticleMagasinStock();
                      
        $gestions = $this->getGestion();

        $gestion_defaults = null;

        $agents = $this->getAgentsByStructure($code_structure);

        // dd($agents);

        $gestion_default = $this->getGestionDefault();

        if ($gestion_default!=null) {
            $gestion_defaults = $gestion_default->code_gestion.' - '.$gestion_default->libelle_gestion;
        }

        return view('immobilisations.create',[
            'articles' => $articles,
            'gestions' => $gestions,
            'gestion_defaults'=>$gestion_defaults,
            'agents'=>$agents,
            'infoUserConnect'=>$infoUserConnect
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
            
            $etape = "store";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }

            $type_profils_lists = ['Pilote AEE'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $request->validate([
            'intitule'=>['required','string'],
            'gestion'=>['required','string'],
            'beneficiaire'=>['required','array'],
            'description_beneficiaire'=>['required','array'],
            'type_beneficiaire'=>['required','array'],
            'ref_articles'=>['required','array'],
            'design_article'=>['required','array'],
            'cmup'=>['required','array'],
            'qte_stock'=>['required','array'],
            'qte'=>['required','array'],
            'montant'=>['required','array'],
            'echantillon'=>['nullable','array'],
            'commentaire'=>['nullable','string'],
        ]);

        //Generate N° Bon de commande

        $exercice = date('Y');

        $requisitions_sequence_id = count(DB::table('requisitions')->where('exercice',$exercice)->get());
        $immobilisations_sequence_id = count(DB::table('immobilisations')->where('exercice',$exercice)->get());

        $sequence_id = $requisitions_sequence_id + $immobilisations_sequence_id + 1;

        $sequence_id = str_pad($sequence_id, 2, "0", STR_PAD_LEFT);

        $type_bc = 'BCI';

        $num_bc = $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);

        $gestion = explode(' - ',$request->gestion);
        $code_gestion = $gestion[0];

        $immobilisation = $this->storeImmobilisation($code_structure,$num_bc,$exercice,$request->intitule,$code_gestion,Session::get('profils_id'));

        $statut_immobilisation = null;

        if ($immobilisation != null) {

            $immobilisations_id = $immobilisation->id;
        
            $this->storeDetailImmobilisation($etape, $request, $immobilisations_id);

            if (isset($request->piece)) {
                if (count($request->piece) > 0) {
                    foreach ($request->piece as $item => $value) {
                        if (isset($request->piece[$item])) {
                            $piece =  $request->piece[$item]->store('piece_jointe', 'public');

                            $name = $request->piece[$item]->getClientOriginalName();

                            $libelle = "Immobilisation";
                            $flag_actif = 1;
                            $piece_jointes_id = null;
                            
                            $dataPiece = [
                                'subject_id'=>$immobilisations_id,
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

        

        

            $libelle = "Soumis pour validation";

            $this->storeTypeStatutRequisition($libelle);

            $type_statut_requisition = $this->getTypeStatutRequisition($libelle);

            if ($type_statut_requisition != null) {
                $type_statut_requisitions_id = $type_statut_requisition->id;

                $this->setLastStatutImmobilisation($immobilisations_id);

                $statut_immobilisation = $this->storeStatutImmobilisation($immobilisations_id, Session::get('profils_id'), $type_statut_requisitions_id, $request->commentaire);
            }
        }

        if ($statut_immobilisation != null) {

            // notifier l'émetteur
            $subject = 'Enregistrement d\'une demande d\'équipements';

            // utilisateur connecté
                $email = auth()->user()->email;
                $this->notifImmobilisation($email,$subject,$immobilisations_id);
            //

            return redirect('/immobilisations/index')->with('success','Enregistrement effectué');
        }else{
            return redirect()->back()->with('error','Enregistrement échoué');
        }
        
        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Immobilisation  $immobilisation
     * @return \Illuminate\Http\Response
     */
    public function show($immobilisation)
    {
       

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($immobilisation);
        } catch (DecryptException $e) {
            //
        }

        $immobilisation = Immobilisation::findOrFail($decrypted);

        $infoUserConnect = null;
        
        if (Session::has('profils_id')) {
            
            $etape = "show";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }

            $type_profils_lists = ['Pilote AEE','Administrateur fonctionnel','Responsable section entretien','Responsable Logistique','Pilote AEE','Responsable des stocks','Gestionnaire des stocks'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$immobilisation);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $articles = $this->getArticleMagasinStock();
                      
        $gestions = $this->getGestion();

        $gestion_defaults = null;

        $agents = $this->getAgentsByStructure($code_structure);

        // dd($agents);

        $gestion_default = $this->getGestionDefault();

        if ($gestion_default!=null) {
            $gestion_defaults = $gestion_default->code_gestion.' - '.$gestion_default->libelle_gestion;
        }

        $immobilisation = $this->getImmobilisation($immobilisation->id);  
        
        
        $immobilisations = $this->getDetailImmobilisations($immobilisation->id);  
        

        $type_piece = "Immobilisation";

        $piece_jointes = $this->getPieceJointes($immobilisation->id, $type_piece);

        $statut_immobilisation = $this->getLastStatutImmobilisation($immobilisation->id);

        return view('immobilisations.show',[
            'articles' => $articles,
            'gestions' => $gestions,
            'gestion_defaults'=>$gestion_defaults,
            'agents'=>$agents,
            'infoUserConnect'=>$infoUserConnect,
            'piece_jointes'=>$piece_jointes,
            'immobilisation'=>$immobilisation,
            'immobilisations'=>$immobilisations,
            'statut_immobilisation'=>$statut_immobilisation
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Immobilisation  $immobilisation
     * @return \Illuminate\Http\Response
     */
    public function edit($immobilisation)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($immobilisation);
        } catch (DecryptException $e) {
            //
        }

        $immobilisation = Immobilisation::findOrFail($decrypted);

        $infoUserConnect = null;
        
        if (Session::has('profils_id')) {
            
            $etape = "immobilisation_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }

            $type_profils_lists = ['Pilote AEE','Responsable section entretien','Gestionnaire entretien','Responsable des stocks','Gestionnaire des stocks','Responsable DMP','Responsable Logistique'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$immobilisation);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $articles = $this->getArticleMagasinStock();
                      
        $gestions = $this->getGestion();

        $gestion_defaults = null;

        $agents = $this->getAgentsByStructure($code_structure);

        // dd($agents);

        $gestion_default = $this->getGestionDefault();

        if ($gestion_default!=null) {
            $gestion_defaults = $gestion_default->code_gestion.' - '.$gestion_default->libelle_gestion;
        }

        $immobilisation = $this->getImmobilisation($immobilisation->id);  
        
        
        $immobilisations = $this->getDetailImmobilisations($immobilisation->id);          

        $type_piece = "Immobilisation";

        $piece_jointes = $this->getPieceJointes($immobilisation->id, $type_piece);

        $statut_immobilisation = $this->getLastStatutImmobilisation($immobilisation->id);

        $display_button1 = 'none'; 
        $display_button2 = 'none';
        $display_button3 = 'none';
        $title = 'EXPRESSION DU BESOIN D\'EQUIPEMENT';
        $route_action = '';
        $verrou_saisie = 1;

        $button1 = null; 
        $value_button1 = null;
        $title_button1 = null;
        $confirm_button1 = null;

        $button2 = null; 
        $value_button2 = null;
        $title_button2 = null;
        $confirm_button2 = null;

        $button3 = null; 
        $value_button3 = null;
        $title_button3 = null;
        $confirm_button3 = null;

        $champ_observation = null;
        $champ_observation_actif = 0;

        $champ_qte_sortie = null;
        $champ_qte_sortie_actif = null;

        $champ_stiker = null;

        $champ_echantillon = 0;
        $button_remove_row = 0;
        $champ_piece_jointe = 0;


        foreach ($immobilisations as $immob) {

            if ($immob->observations != null) {
                $champ_observation = 1;
            }

            if ($immob->qte_sortie != null) {
                $champ_qte_sortie = 1;
            }
            
        }

        $detail_immobilisation = DB::table('detail_immobilisations as di')
        ->where('di.immobilisations_id',$immobilisation->id)
        ->select(DB::raw('SUM(di.qte_sortie) as qte_totale_sortie'))
        ->groupBy('di.immobilisations_id')
        ->first();

        if ($detail_immobilisation != null) {

            $qte_affectee = count(DB::table('affectations as a')
            ->join('detail_immobilisations as di','di.id','=','a.detail_immobilisations_id')
            ->where('di.immobilisations_id',$immobilisation->id)
            ->get());

            if($qte_affectee != 0){

                $champ_stiker = 1;   

            }
        }

        

        if ($type_profils_name === 'Pilote AEE') {

            

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Soumis pour validation" or $statut_immobilisation->libelle === "Annulé (Pilote AEE)" or $statut_immobilisation->libelle === "Invalidé (Responsable section entretien)") {

                    $route_action = 'update';

                    $champ_echantillon = 1;
                    $button_remove_row = 1;
                    $champ_piece_jointe = 1;


                    $button1 = 'Modifier'; 
                    $value_button1 = 'modifier_pilote';
                    $title_button1 = 'Enregistrer votre demande';
                    $confirm_button1 = "Faut-il enregistrer les modifications la demande d\'équipements ?";

                    $button2 = 'Annuler'; 
                    $value_button2 = 'annuler_pilote';
                    $title_button2 = 'Annuler votre demande';
                    $confirm_button2 = "Faut-il annuler la demande d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_pilote';
                    $title_button3 = 'Transmettre l\'expression du besoin au Responsable de la section entretien';
                    $confirm_button3 = "Faut-il transmettre la demande d\'équipements au Responsable de la section entretien ?";


                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = '';

                    $verrou_saisie = null;

                }elseif ($statut_immobilisation->libelle === "Mise à disposition (Partiel)" or $statut_immobilisation->libelle === "Mise à disposition") {

                    $title = 'VALIDATION DE LA RÉCEPTION D\'ÉQUIPEMENTS';
                    $route_action = 'store_analyse';

                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_pilote';
                    $title_button1 = 'Valider la réception d\'équipements';
                    $confirm_button1 = "Faut-il valider la réception d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_pilote';
                    $title_button2 = 'Invalider la réception d\'équipements';
                    $confirm_button2 = "Faut-il invalider la réception d\'équipements ?";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";


                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';

                    // $verrou_saisie = null;

                }

            }

            

            
        }elseif ($type_profils_name === 'Responsable section entretien') {


            $route_action = 'store_analyse';

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Responsable section entretien)" or $statut_immobilisation->libelle === "Annulé (Gestionnaire entretien)") {

                    $title = 'ANALYSE DE L\'EXPRESSION DU BESOIN D\'EQUIPEMENT';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_rse';
                    $title_button1 = 'Valider l\'expression du besoin';
                    $confirm_button1 = "Faut-il valider la demande d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_rse';
                    $title_button2 = 'Invalider l\'expression du besoin';
                    $confirm_button2 = "Faut-il invalider la demande d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_rse';
                    $title_button3 = 'Transmettre l\'expression du besoin au Gestionnaire entretien';
                    $confirm_button3 = "Faut-il transmettre la demande d\'équipements au Gestionnaire de la section entretien ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';

                }elseif ($statut_immobilisation->libelle === "Enquête de vérification (Transmis)" or $statut_immobilisation->libelle === "Enquête de vérification (Validé)" or $statut_immobilisation->libelle === "Invalidé (Responsable des stocks)") {

                    $title = 'EXPRESSION DU BESOIN D\'EQUIPEMENT AU STOCK';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_enquete_rse';
                    $title_button1 = 'Valider l\'enquête de vérification du besoin';
                    $confirm_button1 = "Faut-il valider l\'enquête de vérification du besoin ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_enquete_rse';
                    $title_button2 = 'Invalider l\'enquête de vérification du besoin';
                    $confirm_button2 = "Faut-il invalider l\'enquête de vérification du besoin ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_exp_rse';
                    $title_button3 = 'Transmettre l\'expression du besoin au stock';
                    $confirm_button3 = "Faut-il transmettre l\'expression du besoin au stock ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = '';

                }elseif ($statut_immobilisation->libelle === "Sortie partielle (Stock)" or $statut_immobilisation->libelle === "Sortie totale (Stock)") {
                    $title = 'VALIDATION DE LA RÉCEPTION D\'ÉQUIPEMENTS';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_r_l';
                    $title_button1 = 'Valider la réception d\'équipements';
                    $confirm_button1 = "Faut-il valider la réception d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_r_l';
                    $title_button2 = 'Invalider la réception d\'équipements';
                    $confirm_button2 = "Faut-il invalider la réception d\'équipements ?";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';
                }
    
                

            }
        }elseif ($type_profils_name === 'Gestionnaire entretien') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Gestionnaire entretien)" or $statut_immobilisation->libelle === "Enquête de vérification" or $statut_immobilisation->libelle === "Enquête de vérification (Invalidé)") {

                    $title = 'ENQUÊTE DE VÉRIFICATION DU BESOIN';


                    $button1 = 'Enregistrer'; 
                    $value_button1 = 'enregistrer_ge';
                    $title_button1 = 'Enregistrer l\'enquête de vérification du besoin';
                    $confirm_button1 = "Faut-il enregistrer l\'enquête de vérification du besoin ?";

                    $button2 = ''; 
                    $value_button2 = '';
                    $title_button2 = '';
                    $confirm_button2 = "";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_ge';
                    $title_button3 = 'Transmettre l\'enquête de vérification du besoin au Responsable de la section entretien';
                    $confirm_button3 = "Faut-il transmettre l\'enquête de vérification du besoin au Responsable de la section entretien ?";


                    $display_button1 = ''; 
                    $display_button2 = 'none';
                    $display_button3 = '';

                }elseif ($statut_immobilisation->libelle === "Réception équipement (Logistique)"  or $statut_immobilisation->libelle === "Mise à disposition (Partiel)" or $statut_immobilisation->libelle === "Réception invalidée (Pilote AEE)") {

                    $title = 'PRÉPARATION ET MISE À DISPOSITION D\'ÉQUIPEMENTS';
                    $champ_stiker = 1;


                    $button1 = ''; 
                    $value_button1 = '';
                    $title_button1 = '';
                    $confirm_button1 = "";

                    $button2 = ''; 
                    $value_button2 = '';
                    $title_button2 = '';
                    $confirm_button2 = "";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";


                    $display_button1 = 'none'; 
                    $display_button2 = 'none';
                    $display_button3 = 'none';

                }

            }
        }elseif ($type_profils_name === 'Responsable des stocks') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Responsable des stocks)" or $statut_immobilisation->libelle === "Invalidé (Gestionnaire des stocks)") {

                    $title = 'ANALYSE DE L\'EXPRESSION DU BESOIN D\'EQUIPEMENT';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_rs';
                    $title_button1 = 'Valider l\'expression du besoin';
                    $confirm_button1 = "Faut-il valider la demande d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_rs';
                    $title_button2 = 'Invalider l\'expression du besoin';
                    $confirm_button2 = "Faut-il invalider la demande d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_rs';
                    $title_button3 = 'Transmettre l\'expression du besoin au Gestionnaire entretien';
                    $confirm_button3 = "Faut-il transmettre la demande d\'équipements au Gestionnaire de la section entretien ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';

                }elseif ($statut_immobilisation->libelle === "Demande d'accord (Transmis)" or $statut_immobilisation->libelle === "Demande d'accord (Validé)") {
                    $title = 'ANALYSE DE LA DEMANDE D\'ACCORD POUR ATTRIBUTION';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_rs2';
                    $title_button1 = 'Valider la demande d\'accord pour attribution';
                    $confirm_button1 = "Faut-il valider la demande d\'accord pour attribution ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_rs2';
                    $title_button2 = 'Invalider l\'expression pour attribution';
                    $confirm_button2 = "Faut-il invalider la demande d\'accord pour attribution ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_rs2';
                    $title_button3 = 'Transmettre la demande d\'accord pour attribution au Responsable DMP';
                    $confirm_button3 = "Faut-il transmettre la demande d\'accord pour attribution au Responsable DMP ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = '';
                }

            }
        }elseif ($type_profils_name === 'Gestionnaire des stocks') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if (isset($type_profils_name)) {
                    if ($type_profils_name === 'Gestionnaire des stocks') {

                        $detail_immobilisation = $this->getDetailImmobilisationTotauxMouvementNotNull($immobilisation->id);

                        if ($detail_immobilisation != null) {
                            if($detail_immobilisation->qte_totale_demande > $detail_immobilisation->qte_totale_sortie){

                                $statut_immobilisation->libelle = "Sortie partielle (Stock)";

                            }
                        }

                    }
                }


                if ($statut_immobilisation->libelle === "Transmis (Gestionnaire des stocks)" or $statut_immobilisation->libelle === "Demande d'accord" or $statut_immobilisation->libelle === "Demande d'accord (Invalidé)") {

                    $champ_observation = 1;

                    $champ_observation_actif = 1;

                    $title = 'ÉLABORATION DE LA DEMANDE D\'ACCORD POUR ATTRIBUTION';


                    $button1 = 'Enregistrer'; 
                    $value_button1 = 'enregistrer_gs';
                    $title_button1 = 'Enregistrer la demande d\'accord pour attribution';
                    $confirm_button1 = "Faut-il enregistrer la demande d'accord pour attribution ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_gs';
                    $title_button2 = 'Invalider l\'expression du besoin d\'équipements';
                    $confirm_button2 = "Faut-il invalider l'expression du besoin d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_gs';
                    $title_button3 = 'Transmettre la demande d\'accord pour attribution au Responsable des stocks';
                    $confirm_button3 = "Faut-il transmettre la demande d'accord pour attribution au Responsable des stocks ?";

                    $display_button1 = ''; 
                    $display_button2 = 'none';
                    $display_button3 = '';

                }elseif($statut_immobilisation->libelle === "Accord pour attribution" or $statut_immobilisation->libelle === "Sortie partielle (Stock)" or $statut_immobilisation->libelle === "Réception invalidée (Logistique)"){
                    $champ_observation = 1;

                    $champ_observation_actif = 1;

                    $champ_qte_sortie = 1;

                    $champ_qte_sortie_actif = 1;

                    $title = 'SORTIE DES ÉQUIPEMENTS DU STOCK';


                    $button1 = 'Enregistrer'; 
                    $value_button1 = 'livrer_gs';
                    $title_button1 = 'Sortir les équipements du stocks';
                    $confirm_button1 = "Faut-il sortir les équipements du stocks ?";

                    $button2 = ''; 
                    $value_button2 = '';
                    $title_button2 = '';
                    $confirm_button2 = "";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";

                    $display_button1 = ''; 
                    $display_button2 = 'none';
                    $display_button3 = 'none';
                }

            }
        }elseif ($type_profils_name === 'Responsable DMP') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Responsable DMP)") {
                    $title = 'ANALYSE DE LA DEMANDE D\'ACCORD POUR ATTRIBUTION';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_r_cmp';
                    $title_button1 = 'Valider la demande d\'accord pour attribution';
                    $confirm_button1 = "Faut-il valider la demande d'accord pour attribution ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_r_cmp';
                    $title_button2 = 'Invalider l\'expression pour attribution';
                    $confirm_button2 = "Faut-il invalider la demande d'accord pour attribution ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_r_cmp';
                    $title_button3 = 'Transmettre la demande d\'accord pour attribution au Gestionnaire des stocks';
                    $confirm_button3 = "Faut-il transmettre la demande d'accord pour attribution au Gestionnaire des stocks ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';
                }

            }
        }elseif ($type_profils_name === 'Responsable Logistique') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Sortie partielle (Stock)" or $statut_immobilisation->libelle === "Sortie totale (Stock)") {
                    $title = 'VALIDATION DE LA RÉCEPTION D\'ÉQUIPEMENTS';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_r_l';
                    $title_button1 = 'Valider la réception d\'équipements';
                    $confirm_button1 = "Faut-il valider la réception d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_r_l';
                    $title_button2 = 'Invalider la réception d\'équipements';
                    $confirm_button2 = "Faut-il invalider la réception d\'équipements ?";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';
                }

            }
        }


        return view('immobilisations.edit',[
            'articles' => $articles,
            'gestions' => $gestions,
            'gestion_defaults'=>$gestion_defaults,
            'agents'=>$agents,
            'infoUserConnect'=>$infoUserConnect,
            'piece_jointes'=>$piece_jointes,
            'immobilisation'=>$immobilisation,
            'immobilisations'=>$immobilisations,
            'statut_immobilisation'=>$statut_immobilisation,
            'type_profils_name'=>$type_profils_name,
            'display_button1'=>$display_button1,
            'display_button2'=>$display_button2,
            'display_button3'=>$display_button3,
            'button1'=>$button1,
            'button2'=>$button2,
            'button3'=>$button3,
            'value_button1'=>$value_button1,
            'value_button2'=>$value_button2,
            'value_button3'=>$value_button3,
            'title_button1'=>$title_button1,
            'title_button2'=>$title_button2,
            'title_button3'=>$title_button3,
            'confirm_button1'=>$confirm_button1,
            'confirm_button2'=>$confirm_button2,
            'confirm_button3'=>$confirm_button3,
            'title'=>$title,
            'route_action'=>$route_action,
            'verrou_saisie'=>$verrou_saisie,
            'champ_observation'=>$champ_observation,
            'champ_observation_actif'=>$champ_observation_actif,
            'champ_qte_sortie' => $champ_qte_sortie,
            'champ_qte_sortie_actif' => $champ_qte_sortie_actif,
            'champ_stiker'=>$champ_stiker,
            'champ_echantillon'=>$champ_echantillon,
            'button_remove_row'=>$button_remove_row,
            'champ_piece_jointe'=>$champ_piece_jointe,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Immobilisation  $immobilisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $request->validate([
            'intitule'=>['required','string'],
            'gestion'=>['required','string'],
            'beneficiaire'=>['required','array'],
            'description_beneficiaire'=>['required','array'],
            'type_beneficiaire'=>['required','array'],
            'ref_articles'=>['required','array'],
            'design_article'=>['required','array'],
            'cmup'=>['required','array'],
            'qte_stock'=>['required','array'],
            'qte'=>['required','array'],
            'montant'=>['required','array'],
            'echantillon'=>['nullable','array'],
            'commentaire'=>['nullable','string'],
            'id'=>['required','numeric'],
            'submit'=>['required','string'],
        ]);
        
        if (Session::has('profils_id')) {
            
            $etape = "immobilisation_update";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {

            }

            $type_profils_lists = ['Pilote AEE'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        

        //Generate N° Bon de commande

        $gestion = explode(' - ',$request->gestion);
        $code_gestion = $gestion[0];

        $immobilisation = $this->setImmobilisation($request->id,$request->intitule,$code_gestion,Session::get('profils_id'));

        $statut_immobilisation = null;

        if ($immobilisation != null) {

            $immobilisations_id = $request->id;
        
            $this->storeDetailImmobilisation($etape, $request, $immobilisations_id);

            $type_piece = "Immobilisation";
                        
            $piece_jointes = $this->getPieceJointes($request->id, $type_piece);

            foreach ($piece_jointes as $piece_jointe) {
                
                if (isset($request->piece_jointes_id[$piece_jointe->id])) {

                    $flag_actif = 1;

                    $piece_jointes_id= $piece_jointe->id;

                    if (isset($request->piece_flag_actif[$piece_jointes_id])) {
                        $flag_actif = 1;
                    }else{
                        $flag_actif = 0;
                    }

                    
                    $libelle = "Immobilisation";
                    $piece = null;
                    $name = null;

                    $dataPiece = [
                        'subject_id'=>$request->id,
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

            if (isset($request->piece)) {

                if (count($request->piece) > 0) {

                    foreach ($request->piece as $item => $value) {
                        if (isset($request->piece[$item])) {

                            $piece =  $request->piece[$item]->store('piece_jointe','public');

                            $name = $request->piece[$item]->getClientOriginalName();

                            $libelle = "Immobilisation";

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

                            $dataPiece = [
                                'subject_id'=>$request->id,
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
        
            if (isset($request->submit)) {
                if ($request->submit === "annuler_pilote") {

                    $libelle = "Annulé (Pilote AEE)";
                    $message_success = "Annulation effectuée";
                    $message_error = "Annulation échouée";
                    $subject = 'Annulation d\'une demande d\'équipements';

                }elseif ($request->submit === "modifier_pilote") {

                    $libelle = "Soumis pour validation";
                    $message_success = "Modification effectuée";
                    $message_error = "Modification échouée";
                    $subject = 'Modification d\'une demande d\'équipements';

                }elseif ($request->submit === "transmettre_pilote") {

                    $libelle = "Transmis (Responsable section entretien)";
                    $message_success = "Transmission effectuée";
                    $message_error = "Transmission échouée";
                    $subject = 'Transmission d\'une demande d\'équipements au Responsable de la Section entretien';

                }
            }
        

            $this->storeTypeStatutRequisition($libelle);

            $type_statut_requisition = $this->getTypeStatutRequisition($libelle);

            if ($type_statut_requisition != null) {
                $type_statut_requisitions_id = $type_statut_requisition->id;

                $this->setLastStatutImmobilisation($immobilisations_id);

                $statut_immobilisation = $this->storeStatutImmobilisation($immobilisations_id, Session::get('profils_id'), $type_statut_requisitions_id, $request->commentaire);
            }
        }

        if ($statut_immobilisation != null) {

            // notifier l'émetteur
            

            // utilisateur connecté
                $email = auth()->user()->email;
                $this->notifImmobilisation($email,$subject,$immobilisations_id);
            //

            if ($request->submit === "transmettre_pilote") {

                $type_profils_names = ['Responsable section entretien','Responsable Logistique'];
                $this->notifImmobilisations($subject,$immobilisations_id,$type_profils_names);

            }

            return redirect('/immobilisations/index')->with('success',$message_success);
        }else{
            return redirect()->back()->with('error',$message_error);
        }
        
        

    }

    public function store_analyse(Request $request)
    {
        $request->validate([
            'intitule'=>['required','string'],
            'gestion'=>['required','string'],
            'beneficiaire'=>['required','array'],
            'description_beneficiaire'=>['required','array'],
            'type_beneficiaire'=>['required','array'],
            'ref_articles'=>['required','array'],
            'design_article'=>['required','array'],
            'cmup'=>['required','array'],
            'qte_stock'=>['required','array'],
            'qte'=>['required','array'],
            'montant'=>['required','array'],
            'echantillon'=>['nullable','array'],
            'commentaire'=>['nullable','string'],
            'id'=>['required','numeric'],
            'submit'=>['required','string'],
        ]);
        
        if (Session::has('profils_id')) {
            
            $etape = "immobilisation_update";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {

            }

            $type_profils_lists = ['Responsable section entretien','Gestionnaire entretien','Gestionnaire entretien','Responsable des stocks','Gestionnaire des stocks','Responsable DMP','Responsable Logistique','Pilote AEE'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        //Generate N° Bon de commande

        $flag_valide = null;
        $champ_observation = null;

        if (isset($request->submit)) {

            if ($request->submit === "valider_rse") {

                $flag_valide = 1;
                $type_flag = 'flag_valide';

                $libelle = "Validé (Responsable section entretien)";
                $libelle_send = "Transmis (Gestionnaire entretien)";
                $message_success = "Validation de l'expression effectuée";
                $message_error = "Validation de l'expression échouée";
                $subject = "Validation de l'expression du besoin d'équipements";

            } elseif ($request->submit === "invalider_rse") {

                $flag_valide = 0;
                $type_flag = 'flag_valide';

                $libelle = "Invalidé (Responsable section entretien)";
                $message_success = "Invalidation de l'expression effectuée";
                $message_error = "Invalidation de l'expression échouée";
                $subject = "Invalidation de l'expression du besoin d'équipements";

            } elseif ($request->submit === "enregistrer_ge") {

                $libelle = "Enquête de vérification";
                $message_success = "Enregistrement de l'enquête de vérification du besoin";
                $message_error = "Echec de l'enregistrement de l'enquête de vérification du besoin";
                $subject = "Enregistrement de l'enquête de vérification du besoin";

            } elseif ($request->submit === "transmettre_ge") {

                $libelle = "Enquête de vérification (Transmis)";
                $message_success = "Transmission de l'enquête de vérification du besoin";
                $message_error = "Echec de la transmission de l'enquête de vérification du besoin";
                $subject = "Transmission de l'enquête de vérification du besoin";

            } elseif ($request->submit === "valider_enquete_rse") {

                $flag_valide = 1;
                $type_flag = 'flag_valide';

                $libelle = "Enquête de vérification (Validé)";
                $message_success = "Validation de l'enquête de vérification effectuée";
                $message_error = "Validation de l'enquête de vérification échouée";
                $subject = "Validation de l'enquête de vérification de besoin d'équipements";

            } elseif ($request->submit === "invalider_enquete_rse") {

                $flag_valide = 0;
                $type_flag = 'flag_valide';

                $libelle = "Enquête de vérification (Invalidé)";
                $message_success = "Invalidation de l'enquête de vérification effectuée";
                $message_error = "Invalidation de l'enquête de vérification échouée";
                $subject = "Invalidation de l'enquête de vérification de besoin d'équipements";

            } elseif ($request->submit === "transmettre_exp_rse") {

                $flag_valide = 1;
                $type_flag = 'flag_valide';

                $libelle = "Transmis (Responsable des stocks)";
                $message_success = "Transmission de l'expression du besoin au stock";
                $message_error = "Echec de la transmission de l'expression du besoin au stock";
                $subject = "Transmission de l'expression du besoin au stock";

            } elseif ($request->submit === "valider_rs") {

                $flag_valide = 1;
                $type_flag = 'flag_valide_stock';

                $libelle = "Validé (Responsable des stocks)";
                $libelle_send = "Transmis (Gestionnaire des stocks)";
                $message_success = "Validation de l'expression effectuée";
                $message_error = "Validation de l'expression échouée";
                $subject = "Validation de l'expression du besoin d'équipements par le Responsable des stocks";

            } elseif ($request->submit === "invalider_rs") {

                $flag_valide = 0;
                $type_flag = 'flag_valide_stock';

                $libelle = "Invalidé (Responsable des stocks)";
                $message_success = "Invalidation de l'expression effectuée";
                $message_error = "Invalidation de l'expression échouée";
                $subject = "Invalidation de l'expression du besoin d'équipements par le Responsable des stocks";

            } elseif ($request->submit === "enregistrer_gs") {

                $libelle = "Demande d'accord";
                $message_success = "Enregistrement de la demande d'accord pour attribution";
                $message_error = "Echec de l'enregistrement de la demande d'accord pour attribution";
                $subject = "Enregistrement de la demande d'accord pour attribution";

                $champ_observation = 1;

            } elseif ($request->submit === "invalider_gs") {

                $libelle = "Invalidé (Gestionnaire des stocks)";
                $message_success = "Invalidation de l'expression effectuée";
                $message_error = "Invalidation de l'expression échouée";
                $subject = "Invalidation de l'expression du besoin d'équipements par le Gestionnaire des stocks";

                $champ_observation = 1;


            } elseif ($request->submit === "transmettre_gs") {

                $libelle = "Demande d'accord (Transmis)";
                $message_success = "Transmission de la demande d'accord pour attribution";
                $message_error = "Echec de la transmission de la demande d'accord pour attribution";
                $subject = "Transmission de la demande d'accord pour attribution";

                $champ_observation = 1;

            } elseif ($request->submit === "valider_rs2") {

                $libelle = "Demande d'accord (Validé)";
                $message_success = "Validation de la demande d'accord pour attribution";
                $message_error = "Validation de la demande d'accord pour attribution échouée";
                $subject = "Validation de la demande d'accord pour attribution par le Responsable des stocks";

            } elseif ($request->submit === "invalider_rs2") {

                $libelle = "Demande d'accord (Invalidé)";
                $message_success = "Invalidation de la demande d'accord pour attribution";
                $message_error = "Invalidation de la demande d'accord pour attribution échouée";
                $subject = "Invalidation de la demande d'accord pour attribution par le Responsable des stocks";

            } elseif ($request->submit === "transmettre_rs2") {

                $libelle = "Transmis (Responsable DMP)";
                $message_success = "Transmission de la demande d'accord pour attribution au Responsable DMP";
                $message_error = "Echec de la transmission de la demande d'accord pour attribution au Responsable DMP";
                $subject = "Transmission de la demande d'accord pour attribution au Responsable DMP";

            } elseif ($request->submit === "valider_r_cmp") {

                $flag_valide = 1;
                $type_flag = 'flag_valide_r_cmp';

                $libelle = "Accord pour attribution";
                $message_success = "Validation de la demande d'accord pour attribution";
                $message_error = "Validation de la demande d'accord pour attribution échouée";
                $subject = "Validation de la demande d'accord pour attribution par le Responsable DMP";

            } elseif ($request->submit === "invalider_r_cmp") {

                $flag_valide = 0;
                $type_flag = 'flag_valide_r_cmp';

                $libelle = "Demande d'accord (Invalidé)";
                $message_success = "Invalidation de la demande d'accord pour attribution";
                $message_error = "Invalidation de la demande d'accord pour attribution échouée";
                $subject = "Invalidation de la demande d'accord pour attribution par le Responsable DMP";

            } elseif ($request->submit === "livrer_gs") {

                

                $champ_observation = 1;

                if (count($request->detail_immobilisations_id) > 0) {
                    
                    foreach ($request->qte_sortie as $key => $value) {

                        $this->setDetailImmobilisationQteSortie($request->id,$request->detail_immobilisations_id[$key],$request->qte_sortie[$key]);

                        $create_mouvement = 1;
                    }
                }

                

                $detail_immobilisation = $this->getDetailImmobilisationTotaux($request->id);

                

                if ($detail_immobilisation != null) {

                    if ($detail_immobilisation->qte_totale_demande === $detail_immobilisation->qte_totale_sortie) {
                        $libelle = "Sortie totale (Stock)";

                        $message_success = "Sortie totale des équipements du stock";
                        $message_error = "Echec de la sortie totale des équipements du stock";
                        $subject = "Sortie totale des équipements du stock";
                    }else{

                        if ($detail_immobilisation->qte_totale_sortie == 0) {
                            $libelle = null;
                        }else{
                            $libelle = "Sortie partielle (Stock)";

                            $message_success = "Sortie partielle des équipements du stock";
                            $message_error = "Echec de la sortie partielle des équipements du stock";
                            $subject = "Sortie partielle des équipements du stock";
                        }
                        
                    }

                }

            } elseif ($request->submit === "valider_r_l") {

                $flag_valide = 1;
                $type_flag = 'flag_valide_r_l';

                $libelle = "Réception équipement (Logistique)";
                $message_success = "Validation de la réception d'équipements";
                $message_error = "Validation de la réception d'équipements échouée";
                $subject = "Validation de la réception d'équipements par la Logistique";

            } elseif ($request->submit === "invalider_r_l") {

                $flag_valide = 0;
                $type_flag = 'flag_valide_r_l';

                $libelle = "Réception invalidée (Logistique)";
                $message_success = "Invalidation de la réception d'équipements";
                $message_error = "Invalidation de la réception d'équipements échouée";
                $subject = "Invalidation de la réception d'équipements par la Logistique";

            } elseif ($request->submit === "mise_a_disposition_ge") {

                $statut_equipement = null;

                foreach ($request->magasin_stocks_id as $key => $value) {

                    $autorisation_store = 0;

                    foreach ($request->affectation as $item => $value_affectation) {
                        if ($value_affectation == $key ) {
                            $autorisation_store = 1;
                        }
                    }

                    if ($autorisation_store === 1) {

                        if(isset($request->type_affectations_libelle[$key])){

                            $type_affectations_libelle = $request->type_affectations_libelle[$key];
    
                            $this->storeTypeAffectation($type_affectations_libelle);
    
                            $type_affectation = $this->getTypeAffectation($type_affectations_libelle);
    
                            if ($type_affectation != null) {
                                $type_affectations_id = $type_affectation->id;
                                $ref_equipement = null;



                                $exercice = date('Y');

                                $sequence_id = count(DB::table('equipement_immobilisers')->where('exercice',$exercice)->get())  + 1;

                                $sequence_id = str_pad($sequence_id, 2, "0", STR_PAD_LEFT);

                                $type_bc = 'IMMO';

                                $code_structure = null;

                                if (isset($request->type_beneficiaire[$key])) {

                                    if ($request->type_beneficiaire[$key] === "Agent") {

                                        $info_user = $this->getInfoUserByMle($request->beneficiaire[$key]);

                                        if ($info_user != null) {
                                            $code_structure = $info_user->code_structure;
                                        }

                                    }elseif ($request->type_beneficiaire[$key] === "Structure") {
                                        $code_structure = $request->beneficiaire[$key];
                                    }
                                }

                                //Vérifier l'existance du numéro sticker
                                $equipement_immobiliser = null;
                                $enregistrement_sticker = null;
                                $enregistrement_serie = null;

                                if (isset($request->stiker[$key])) {

                                    $options_libelle = 'N° Sticker';

                                    $enregistrement_sticker = 1;

                                }

                                
                                
                                if (isset($request->serie[$key])) {

                                    $options_libelle = 'N° Serie';

                                    $enregistrement_serie = 1;

                                }

                                if (isset($request->ref_equipement[$key])) {

                                    $equipement_immobiliser = $this->getEquipementImmobiliser($request->ref_equipement[$key]);

                                }

                                

                                if ($equipement_immobiliser != null) {

                                    $ref_equipement = $equipement_immobiliser->ref_equipement;

                                }else{

                                    $ref_equipement = $this->generateImmobilisation($exercice,$code_structure,$type_bc,$sequence_id);                                    

                                    // Création d'équipement immobilisé

                                    $equipement = $this->storeEquipementImmobiliser($ref_equipement,$request->magasin_stocks_id[$key],$exercice);

                                    

                                    $ref_equipement = $equipement->ref_equipement;

                                }
                                
                                
                                $options_libelle = 'N° Sticker';

                                $this->storeOption($options_libelle);
                                $option = $this->getOption($options_libelle);

                                if (isset($enregistrement_sticker)) {

                                    if ($option != null) {
                                        $valeur_option = $request->stiker[$key];
                                        $this->storeOptionEquipement($ref_equipement,$option->id,$valeur_option);
                                    }

                                }else{

                                    $this->deleteOptionEquipement($ref_equipement,$option->id);

                                } 
                                
                                $options_libelle = 'N° Serie';

                                $this->storeOption($options_libelle);

                                $option = $this->getOption($options_libelle);

                                if (isset($enregistrement_serie)) {                                    

                                    if ($option != null) {

                                        $valeur_option = $request->serie[$key];
                                        $this->storeOptionEquipement($ref_equipement,$option->id,$valeur_option);

                                    }

                                }else{

                                    $this->deleteOptionEquipement($ref_equipement,$option->id);

                                }

                                if ($ref_equipement != null && $type_affectations_id != null) {

                                    $flag_actif = 1;

                                    $date_debuts = $this->storeDateOperation(date('Y-m-d'));
                                    
                                    $date_debut = $date_debuts->date;

                                    $date_fin = null;

                                    $affectation = $this->storeAffection($ref_equipement,$request->index[$key],$request->detail_immobilisations_id[$key],$type_affectations_id,$date_debut,$date_fin,$flag_actif);

                                    if ($affectation != null) {

                                        $type_statut_equipements_libelle = 'Affecter';

                                        $this->storeTypeStatutEquipement($type_statut_equipements_libelle);

                                        $type_statut_equipement = $this->getTypeStatutEquipement($type_statut_equipements_libelle);

                                        $this->setLastStatutEquipement($ref_equipement);

                                        if ($type_statut_equipement != null) {

                                            $commentaire = null;

                                            $statut_equipement = $this->storeStatutEquipement($ref_equipement,Session::get('profils_id'),$type_statut_equipement->id,$commentaire);

                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($statut_equipement != null) {

                    $qte_total = (Int) $this->getCountDetailImmobilisation($request->id);

                    $qte_total_affecte = $this->getCountAffectationByImmobilisationId($request->id);

                    

                    if ($qte_total_affecte < $qte_total) {

                        $libelle = 'Mise à disposition (Partiel)';

                        $message_success = "Mise à disposition partielle d'équipements";
                        $message_error = "Mise à disposition partielle d'équipements échouée";
                        $subject = "Mise à disposition partielle d'équipements par le Gestionnaire entretien";

                    }elseif ($qte_total_affecte === $qte_total) {

                        $libelle = 'Mise à disposition';

                        $message_success = "Mise à disposition d'équipements";
                        $message_error = "Mise à disposition d'équipements échouée";
                        $subject = "Mise à disposition d'équipements par le Gestionnaire entretien";

                    }
                }
            } elseif ($request->submit === "invalider_pilote"){

                $libelle = "Réception invalidée (Pilote AEE)";
                $message_success = "Invalidation de la réception d'équipements";
                $message_error = "Invalidation de la réception d'équipements échouée";
                $subject = "Invalidation de la réception d'équipements par le Pilote AEE";

            } elseif ($request->submit === "valider_pilote"){

                $libelle = "Réception validée (Pilote AEE)";
                $message_success = "Validation de la réception d'équipements";
                $message_error = "Validation de la réception d'équipements échouée";
                $subject = "Validation de la réception d'équipements par le Pilote AEE";

            }
        }

        if (isset($champ_observation)) {

            if ($champ_observation === 1) {

                if (count($request->detail_immobilisations_id) > 0) {
                    foreach ($request->observations as $key => $value) {
                        $this->setDetailImmobilisationObservation($request->id,$request->detail_immobilisations_id[$key],$request->observations[$key]);
                    }
                }
                
                
            }

        }
        

        $immobilisation = null;
        
        if (isset($flag_valide)) {
        
            if ($type_profils_name === "Responsable section entretien" or $type_profils_name === "Responsable des stocks" or $type_profils_name === "Responsable DMP" or $type_profils_name === "Responsable Logistique") {
            
                $immobilisation = $this->setFlagValideImmobilisation($request->id,$flag_valide,$type_flag);
            }
            
        }else{
            $immobilisation = $this->getImmobilisation($request->id);
        }
        

        $statut_immobilisation = null;

        if ($immobilisation != null && $libelle != null) {

            $immobilisations_id = $request->id;

            $this->storeTypeStatutRequisition($libelle);

            $type_statut_requisition = $this->getTypeStatutRequisition($libelle);

            if ($type_statut_requisition != null) {
                $type_statut_requisitions_id = $type_statut_requisition->id;

                $this->setLastStatutImmobilisation($immobilisations_id);

                $statut_immobilisation = $this->storeStatutImmobilisation($immobilisations_id, Session::get('profils_id'), $type_statut_requisitions_id, $request->commentaire);
            }

            if (isset($libelle_send)) {
                $this->storeTypeStatutRequisition($libelle_send);

                $type_statut_requisition = $this->getTypeStatutRequisition($libelle_send);

                if ($type_statut_requisition != null) {
                    $type_statut_requisitions_id = $type_statut_requisition->id;

                    $this->setLastStatutImmobilisation($immobilisations_id);

                    $statut_immobilisation = $this->storeStatutImmobilisation($immobilisations_id, Session::get('profils_id'), $type_statut_requisitions_id, $request->commentaire);
                }
            }
        }

        if ($statut_immobilisation != null) {

            if (isset($create_mouvement)) {
                $mouvements_libelle = "Sortie du stock";

                $type_mouvements_id = $this->getTypeMouvement($mouvements_libelle);

                if ($type_mouvements_id != null) {

                    if (count($request->detail_immobilisations_id) > 0) {
                        foreach ($request->qte_sortie as $key => $value) {

                            if ($request->qte_sortie[$key] > 0) {
                                $taux = 1;
                                $taux_de_change =  1;

                                $qte_mvt = -1 * $request->qte_sortie[$key];
                                $montant_ht = $request->cmup[$key] * $qte_mvt;
                                $montant_ttc = $taux * $montant_ht;

                                $mouvements_id = $this->storeMouvement($type_mouvements_id, $request->magasin_stocks_id[$key], Session::get('profils_id'), $qte_mvt, $request->cmup[$key], $montant_ht, $taux, $montant_ttc, $taux_de_change);

                                $this->setMagasinStock3($request->magasin_stocks_id[$key]);

                                $this->setDetailImmobilisationMouvement($request->id, $request->detail_immobilisations_id[$key], $mouvements_id);
                            }


                        }
                    }
                }
            }

            // notifier l'émetteur
            

            // utilisateur connecté
                $email = auth()->user()->email;
                $this->notifImmobilisation($email,$subject,$immobilisations_id);
            //
            $type_profils_names = [];
            if ($request->submit === "valider_rse") {

                $type_profils_names = ['Responsable Logistique','Gestionnaire entretien'];

            }elseif ($request->submit === "invalider_rse") {

                $type_profils_names = ['Responsable Logistique'];

            }elseif ($request->submit === "transmettre_ge") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien'];

            }elseif ($request->submit === "valider_enquete_rse" or $request->submit === "invalider_enquete_rse" or $request->submit === "transmettre_exp_rse") {

                $type_profils_names = ['Responsable Logistique','Gestionnaire entretien','Responsable des stocks'];

            }elseif ($request->submit === "valider_rs") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Gestionnaire des stocks'];

            }elseif ($request->submit === "invalider_rs") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien'];

            }elseif ($request->submit === "enregistrer_gs" or $request->submit === "invalider_gs" or $request->submit === "transmettre_gs") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Responsable des stocks'];

            }elseif ($request->submit === "valider_rs2" or $request->submit === "invalider_rs2") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Gestionnaire des stocks'];

            }elseif ($request->submit === "transmettre_rs2") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Responsable des stocks'];

            }elseif ($request->submit === "valider_r_cmp" or $request->submit === "invalider_r_cmp") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Gestionnaire des stocks','Gestionnaire des stocks'];

            }elseif ($request->submit === "livrer_gs") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Gestionnaire des stocks','Responsable DMP'];

            }elseif ($request->submit === "valider_r_l" or $request->submit === "invalider_r_l") {

                $type_profils_names = ['Responsable section entretien','Gestionnaire entretien','Gestionnaire des stocks','Gestionnaire des stocks','Responsable DMP'];

            }elseif ($request->submit === "mise_a_disposition_ge") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire des stocks','Gestionnaire des stocks','Responsable DMP','Pilote AEE'];

            }elseif ($request->submit === "invalider_pilote" or $request->submit === "valider_pilote") {

                $type_profils_names = ['Responsable Logistique','Responsable section entretien','Gestionnaire entretien','Gestionnaire des stocks','Responsable des stocks','Responsable DMP'];

            }

            if ( count($type_profils_names) > 0 ) {

                $this->notifImmobilisations($subject,$immobilisations_id,$type_profils_names);
                $this->notifPiloteImmobilisations($subject,$immobilisations_id);

            }

            $this->setImmobilisation2($immobilisations_id);

            if ($request->submit === "mise_a_disposition_ge") {
                return redirect()->back()->with('success',$message_success);
            }else{
                return redirect('/immobilisations/index')->with('success',$message_success);
            }
        }else{
            return redirect()->back()->with('error',$message_error);
        }
    }

    public function stiker($immobilisation)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($immobilisation);
        } catch (DecryptException $e) {
            //
        }

        $immobilisation = Immobilisation::findOrFail($decrypted);

        $infoUserConnect = null;
        
        if (Session::has('profils_id')) {
            
            $etape = "immobilisation_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }

            $type_profils_lists = ['Pilote AEE','Responsable section entretien','Gestionnaire entretien','Responsable des stocks','Gestionnaire des stocks','Responsable DMP','Responsable Logistique'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$immobilisation);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $articles = $this->getArticleMagasinStock();
                      
        $gestions = $this->getGestion();

        $gestion_defaults = null;

        $agents = $this->getAgentsByStructure($code_structure);

        // dd($agents);

        $gestion_default = $this->getGestionDefault();

        if ($gestion_default!=null) {
            $gestion_defaults = $gestion_default->code_gestion.' - '.$gestion_default->libelle_gestion;
        }

        $immobilisation = $this->getImmobilisation($immobilisation->id);  
        
        
        $immobilisations = $this->getDetailImmobilisations($immobilisation->id);          

        $type_piece = "Immobilisation";

        $piece_jointes = $this->getPieceJointes($immobilisation->id, $type_piece);

        $statut_immobilisation = $this->getLastStatutImmobilisation($immobilisation->id);

        $display_button1 = 'none'; 
        $display_button2 = 'none';
        $display_button3 = 'none';
        $title = 'EXPRESSION DU BESOIN D\'EQUIPEMENT';
        $route_action = '';
        $verrou_saisie = 1;

        $button1 = null; 
        $value_button1 = null;
        $title_button1 = null;
        $confirm_button1 = null;

        $button2 = null; 
        $value_button2 = null;
        $title_button2 = null;
        $confirm_button2 = null;

        $button3 = null; 
        $value_button3 = null;
        $title_button3 = null;
        $confirm_button3 = null;

        $champ_observation = null;
        $champ_observation_actif = 0;

        $champ_qte_sortie = null;
        $champ_qte_sortie_actif = null;

        $champ_stiker = null;

        $champ_echantillon = 0;
        $button_remove_row = 0;
        $champ_piece_jointe = 0;
        $view_equipement_immobilise = 0;

        foreach ($immobilisations as $immob) {

            if ($immob->observations != null) {
                $champ_observation = 1;
            }

            if ($immob->qte_sortie != null) {
                $champ_qte_sortie = 1;
            }
            
        }

        

        if ($type_profils_name === 'Pilote AEE') {

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Soumis pour validation" or $statut_immobilisation->libelle === "Annulé (Pilote AEE)" or $statut_immobilisation->libelle === "Invalidé (Responsable section entretien)") {

                    $route_action = 'update';

                    $button1 = 'Modifier'; 
                    $value_button1 = 'modifier_pilote';
                    $title_button1 = 'Enregistrer votre demande';
                    $confirm_button1 = "Faut-il enregistrer les modifications la demande d\'équipements ?";

                    $button2 = 'Annuler'; 
                    $value_button2 = 'annuler_pilote';
                    $title_button2 = 'Annuler votre demande';
                    $confirm_button2 = "Faut-il annuler la demande d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_pilote';
                    $title_button3 = 'Transmettre l\'expression du besoin au Responsable de la section entretien';
                    $confirm_button3 = "Faut-il transmettre la demande d\'équipements au Responsable de la section entretien ?";


                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = '';

                    $verrou_saisie = null;

                }elseif ($statut_immobilisation->libelle === "Mise à disposition (Partiel)" or $statut_immobilisation->libelle === "Mise à disposition") {

                    $route_action = 'store_analyse';

                    $title = 'VALIDATION DE LA RÉCEPTION D\'ÉQUIPEMENTS';
                    $route_action = 'store_analyse';

                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_pilote';
                    $title_button1 = 'Valider la réception d\'équipements';
                    $confirm_button1 = "Faut-il valider la réception d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_pilote';
                    $title_button2 = 'Invalider la réception d\'équipements';
                    $confirm_button2 = "Faut-il invalider la réception d\'équipements ?";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";


                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';

                    $view_equipement_immobilise = 1;

                    // $verrou_saisie = null;

                }

            }

            

            
        }elseif ($type_profils_name === 'Responsable section entretien') {


            $route_action = 'store_analyse';

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Responsable section entretien)" or $statut_immobilisation->libelle === "Annulé (Gestionnaire entretien)") {

                    $title = 'ANALYSE DE L\'EXPRESSION DU BESOIN D\'EQUIPEMENT';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_rse';
                    $title_button1 = 'Valider l\'expression du besoin';
                    $confirm_button1 = "Faut-il valider la demande d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_rse';
                    $title_button2 = 'Invalider l\'expression du besoin';
                    $confirm_button2 = "Faut-il invalider la demande d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_rse';
                    $title_button3 = 'Transmettre l\'expression du besoin au Gestionnaire entretien';
                    $confirm_button3 = "Faut-il transmettre la demande d\'équipements au Gestionnaire de la section entretien ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';

                }elseif ($statut_immobilisation->libelle === "Enquête de vérification (Transmis)" or $statut_immobilisation->libelle === "Enquête de vérification (Validé)" or $statut_immobilisation->libelle === "Invalidé (Responsable des stocks)") {

                    $title = 'EXPRESSION DU BESOIN D\'EQUIPEMENT AU STOCK';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_enquete_rse';
                    $title_button1 = 'Valider l\'enquête de vérification du besoin';
                    $confirm_button1 = "Faut-il valider l\'enquête de vérification du besoin ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_enquete_rse';
                    $title_button2 = 'Invalider l\'enquête de vérification du besoin';
                    $confirm_button2 = "Faut-il invalider l\'enquête de vérification du besoin ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_exp_rse';
                    $title_button3 = 'Transmettre l\'expression du besoin au stock';
                    $confirm_button3 = "Faut-il transmettre l\'expression du besoin au stock ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = '';

                }

            }
        }elseif ($type_profils_name === 'Gestionnaire entretien') {

            $route_action = 'store_analyse';

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Réception équipement (Logistique)"  or $statut_immobilisation->libelle === "Mise à disposition (Partiel)" or $statut_immobilisation->libelle === "Réception invalidée (Pilote AEE)") {

                    $title = 'PRÉPARATION ET MISE À DISPOSITION D\'ÉQUIPEMENTS';
                    $champ_stiker = 1;


                    $button1 = 'Mettre à disposition'; 
                    $value_button1 = 'mise_a_disposition_ge';
                    $title_button1 = 'Mise à disposition des équipements';
                    $confirm_button1 = "Faut-il mettre à disposition les équipements ?";

                    $button2 = ''; 
                    $value_button2 = '';
                    $title_button2 = '';
                    $confirm_button2 = "";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";


                    $display_button1 = ''; 
                    $display_button2 = 'none';
                    $display_button3 = 'none';

                    $verrou_saisie = null;

                }

            }
        }elseif ($type_profils_name === 'Responsable des stocks') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Responsable des stocks)" or $statut_immobilisation->libelle === "Invalidé (Gestionnaire des stocks)") {

                    $title = 'ANALYSE DE L\'EXPRESSION DU BESOIN D\'EQUIPEMENT';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_rs';
                    $title_button1 = 'Valider l\'expression du besoin';
                    $confirm_button1 = "Faut-il valider la demande d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_rs';
                    $title_button2 = 'Invalider l\'expression du besoin';
                    $confirm_button2 = "Faut-il invalider la demande d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_rs';
                    $title_button3 = 'Transmettre l\'expression du besoin au Gestionnaire entretien';
                    $confirm_button3 = "Faut-il transmettre la demande d\'équipements au Gestionnaire de la section entretien ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';

                }elseif ($statut_immobilisation->libelle === "Demande d'accord (Transmis)" or $statut_immobilisation->libelle === "Demande d'accord (Validé)") {
                    $title = 'ANALYSE DE LA DEMANDE D\'ACCORD POUR ATTRIBUTION';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_rs2';
                    $title_button1 = 'Valider la demande d\'accord pour attribution';
                    $confirm_button1 = "Faut-il valider la demande d\'accord pour attribution ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_rs2';
                    $title_button2 = 'Invalider l\'expression pour attribution';
                    $confirm_button2 = "Faut-il invalider la demande d\'accord pour attribution ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_rs2';
                    $title_button3 = 'Transmettre la demande d\'accord pour attribution au Responsable DMP';
                    $confirm_button3 = "Faut-il transmettre la demande d\'accord pour attribution au Responsable DMP ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = '';
                }

            }
        }elseif ($type_profils_name === 'Gestionnaire des stocks') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if (isset($type_profils_name)) {
                    if ($type_profils_name === 'Gestionnaire des stocks') {

                        $detail_immobilisation = $this->getDetailImmobilisationTotauxMouvementNotNull($immobilisation->id);

                        if ($detail_immobilisation != null) {
                            if($detail_immobilisation->qte_totale_demande > $detail_immobilisation->qte_totale_sortie){

                                $statut_immobilisation->libelle = "Sortie partielle (Stock)";

                            }
                        }

                    }
                }


                if ($statut_immobilisation->libelle === "Transmis (Gestionnaire des stocks)" or $statut_immobilisation->libelle === "Demande d'accord" or $statut_immobilisation->libelle === "Demande d'accord (Invalidé)") {

                    $champ_observation = 1;

                    $champ_observation_actif = 1;

                    $title = 'ÉLABORATION DE LA DEMANDE D\'ACCORD POUR ATTRIBUTION';


                    $button1 = 'Enregistrer'; 
                    $value_button1 = 'enregistrer_gs';
                    $title_button1 = 'Enregistrer la demande d\'accord pour attribution';
                    $confirm_button1 = "Faut-il enregistrer la demande d'accord pour attribution ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_gs';
                    $title_button2 = 'Invalider l\'expression du besoin d\'équipements';
                    $confirm_button2 = "Faut-il invalider l'expression du besoin d\'équipements ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_gs';
                    $title_button3 = 'Transmettre la demande d\'accord pour attribution au Responsable des stocks';
                    $confirm_button3 = "Faut-il transmettre la demande d'accord pour attribution au Responsable des stocks ?";

                    $display_button1 = ''; 
                    $display_button2 = 'none';
                    $display_button3 = '';

                }elseif($statut_immobilisation->libelle === "Accord pour attribution" or $statut_immobilisation->libelle === "Sortie partielle (Stock)" or $statut_immobilisation->libelle === "Réception invalidée (Logistique)"){
                    $champ_observation = 1;

                    $champ_observation_actif = 1;

                    $champ_qte_sortie = 1;

                    $champ_qte_sortie_actif = 1;

                    $title = 'SORTIE DES ÉQUIPEMENTS DU STOCK';


                    $button1 = 'Enregistrer'; 
                    $value_button1 = 'livrer_gs';
                    $title_button1 = 'Sortir les équipements du stocks';
                    $confirm_button1 = "Faut-il sortir les équipements du stocks ?";

                    $button2 = ''; 
                    $value_button2 = '';
                    $title_button2 = '';
                    $confirm_button2 = "";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";

                    $display_button1 = ''; 
                    $display_button2 = 'none';
                    $display_button3 = 'none';
                }

            }
        }elseif ($type_profils_name === 'Responsable DMP') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Transmis (Responsable DMP)") {
                    $title = 'ANALYSE DE LA DEMANDE D\'ACCORD POUR ATTRIBUTION';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_r_cmp';
                    $title_button1 = 'Valider la demande d\'accord pour attribution';
                    $confirm_button1 = "Faut-il valider la demande d'accord pour attribution ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_r_cmp';
                    $title_button2 = 'Invalider l\'expression pour attribution';
                    $confirm_button2 = "Faut-il invalider la demande d'accord pour attribution ?";
                    
                    $button3 = 'Transmettre'; 
                    $value_button3 = 'transmettre_r_cmp';
                    $title_button3 = 'Transmettre la demande d\'accord pour attribution au Gestionnaire des stocks';
                    $confirm_button3 = "Faut-il transmettre la demande d'accord pour attribution au Gestionnaire des stocks ?";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';
                }

            }
        }elseif ($type_profils_name === 'Responsable Logistique') {

            $route_action = 'store_analyse';

            

            if($statut_immobilisation != null){

                if ($statut_immobilisation->libelle === "Sortie partielle (Stock)" or $statut_immobilisation->libelle === "Sortie totale (Stock)") {
                    $title = 'VALIDATION DE LA RÉCEPTION D\'ÉQUIPEMENTS';


                    $button1 = 'Valider'; 
                    $value_button1 = 'valider_r_l';
                    $title_button1 = 'Valider la réception d\'équipements';
                    $confirm_button1 = "Faut-il valider la réception d\'équipements ?";

                    $button2 = 'Invalider'; 
                    $value_button2 = 'invalider_r_l';
                    $title_button2 = 'Invalider la réception d\'équipements';
                    $confirm_button2 = "Faut-il invalider la réception d\'équipements ?";
                    
                    $button3 = ''; 
                    $value_button3 = '';
                    $title_button3 = '';
                    $confirm_button3 = "";

                    $display_button1 = ''; 
                    $display_button2 = '';
                    $display_button3 = 'none';
                }

            }
        }
        

        return view('immobilisations.stiker',[
            'articles' => $articles,
            'gestions' => $gestions,
            'gestion_defaults'=>$gestion_defaults,
            'agents'=>$agents,
            'infoUserConnect'=>$infoUserConnect,
            'piece_jointes'=>$piece_jointes,
            'immobilisation'=>$immobilisation,
            'immobilisations'=>$immobilisations,
            'statut_immobilisation'=>$statut_immobilisation,
            'type_profils_name'=>$type_profils_name,
            'display_button1'=>$display_button1,
            'display_button2'=>$display_button2,
            'display_button3'=>$display_button3,
            'button1'=>$button1,
            'button2'=>$button2,
            'button3'=>$button3,
            'value_button1'=>$value_button1,
            'value_button2'=>$value_button2,
            'value_button3'=>$value_button3,
            'title_button1'=>$title_button1,
            'title_button2'=>$title_button2,
            'title_button3'=>$title_button3,
            'confirm_button1'=>$confirm_button1,
            'confirm_button2'=>$confirm_button2,
            'confirm_button3'=>$confirm_button3,
            'title'=>$title,
            'route_action'=>$route_action,
            'verrou_saisie'=>$verrou_saisie,
            'champ_observation'=>$champ_observation,
            'champ_observation_actif'=>$champ_observation_actif,
            'champ_qte_sortie' => $champ_qte_sortie,
            'champ_qte_sortie_actif' => $champ_qte_sortie_actif,
            'champ_stiker'=>$champ_stiker,
            'champ_echantillon'=>$champ_echantillon,
            'button_remove_row'=>$button_remove_row,
            'champ_piece_jointe'=>$champ_piece_jointe,
            'view_equipement_immobilise'=>$view_equipement_immobilise
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Immobilisation  $immobilisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Immobilisation $immobilisation)
    {
        //
    }

    public function storeSessionBackground($request){
        $request->session()->put('backgroundImage','container-infographie2');
    }

    

    
}
