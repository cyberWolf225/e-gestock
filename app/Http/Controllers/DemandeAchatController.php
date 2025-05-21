<?php

namespace App\Http\Controllers;

use App\Models\Famille;
use App\Models\Gestion;
use App\Models\Structure;
use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\PreselectionSoumissionnaire;
use Illuminate\Contracts\Encryption\DecryptException;
 
class DemandeAchatController extends Controller
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

        // dd(Hash::make('1234567890'));
        $this->storeSessionBackground($request);
        
        $demande_achats = [];
        $acces_create = null;
        if (Session::has('profils_id')) {

            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $libelle = null;
            if ($type_profils_name === 'Gestionnaire des achats' or $type_profils_name === 'Administrateur fonctionnel') {
                $libelle = 'Soumis pour validation';
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);

                if ($type_profils_name === 'Gestionnaire des achats') {
                    $acces_create = 1;
                }

            }elseif ($type_profils_name === "Responsable des achats") {
                $libelle = "Transmis (Responsable des achats)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Fournisseur") {
                $libelle = "Transmis pour cotation";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable DMP") {
                $libelle = "Demande de cotation (Transmis Responsable DMP)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable contrôle budgetaire") {
                $libelle = "Transmis (Responsable Contrôle Budgétaire)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Chef Département DCG") {
                $libelle = "Transmis (Chef Département DCG)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable DCG") {
                $libelle = "Transmis (Responsable DCG)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Directeur Général Adjoint") {
                $libelle = "Transmis (Directeur Général Adjoint)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Directeur Général") {
                $libelle = "Transmis (Directeur Général)";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Comite Réception") {
                
                $libelle = "Livraison partielle";
                $libelle2 = "Livraison totale";
                $demande_achats = $this->demandeAchatData($libelle,$type_profils_name,$libelle2);
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $demande_achat_edite = DB::table('demande_achats as da')
                ->join('statut_demande_achats as sda','sda.demande_achats_id','=','da.id')
                ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                ->where('tsda.libelle','Édité')
                ->limit(1)
                ->first();
                
            return view('demande_achats.index',[
                'demande_achat_edite'=>$demande_achat_edite,
                'demande_achats'=>$demande_achats,
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

            $etape = "create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }
            
            $type_profils_lists = ['Gestionnaire des achats'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $ref_depot = $infoUserConnect->ref_depot;

                $structures = $this->getStructuresByRefDepot($ref_depot);
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $gestions = $this->getGestion();        

        $exercices = $this->getExercice();        
        
        $familles = $this->getFamille();   
        
        $credit_budgetaires = $this->getCreditBudgetaireByRefDepotByExercice($ref_depot,$exercices->exercice);
        
        $nbre_ligne = 0;     

        $articles = $this->getArticle();        
        
        $credit_budgetaires_select = null;
        $griser = 1;

        $description_articles = $this->getDescriptionArticles();

        return view('demande_achats.create',[
            'articles'=>$articles,
            'gestions'=>$gestions,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'nbre_ligne'=>$nbre_ligne,
            'exercices'=>$exercices,
            'familles'=>$familles,
            'structures'=>$structures,
            'structure_default'=>null,
            'disponible'=>null,
            'description_articles'=>$description_articles
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
            return redirect('/demande_achats/create')->with('error', 'Veuillez saisir un compte valide');
        }

        $structure = Structure::where('code_structure',$decrypted_code_structure)->first();
        if($structure === null){
            return redirect('/demande_achats/create')->with('error', 'Veuillez saisir une structure valide');
        }

        $gestion = Gestion::where('code_gestion',$decrypted_code_gestion)->first();
        if($gestion === null){
            return redirect('/demande_achats/create')->with('error', 'Veuillez saisir une gestion valide');
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

            $type_profils_lists = ['Gestionnaire des achats'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        $structures = [];

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
        if ($infoUserConnect != null) {
            $ref_depot = $infoUserConnect->ref_depot;

            $structures = $this->getStructuresByRefDepot($ref_depot);
        }

        $exercices = $this->getExercice();

        //dd($famille->ref_fam,$code_structure,$exercices->exercice);
        
        if($famille != null && $structure != null && $exercices != null && $gestion != null){
            try {
                
                 $param = $exercices->exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;
     
                 $this->storeCreditBudgetaireByWebService($param,$structure->ref_depot);
             } catch (\Throwable $th) {
                 
             }
        } 
        

        $affiche_zone_struture = 1;

        $gestions = $this->getGestion();
        
        $credit_budgetaires_select = $this->getFamilleById($famille->id);
                        
        $griser = null;

        //$articles = $this->getArticleByFamilleId($famille->id);    
        $articles = $this->getArticleConcerners($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercices->exercice);

        $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercices->exercice);

        $nbre_ligne = $this->countArticleByFamilleId($famille->id);

        $familles = $this->getFamille();

        $description_articles = $this->getDescriptionArticles();

        return view('demande_achats.create',[
            'articles'=>$articles,
            'gestions'=>$gestions,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'nbre_ligne'=>$nbre_ligne,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'structures'=>$structures,
            'gestion_default'=>$gestion,
            'exercices'=>$exercices,
            'familles'=>$familles,
            'disponible'=>$disponible,
            'structure_default'=>$structure,
            'description_articles'=>$description_articles
        ]);
    }

    public function crypt($ref_fam,$code_structure,$code_gestion){
        
        return redirect('demande_achats/create/'.Crypt::encryptString($ref_fam).'/'.Crypt::encryptString($code_structure).'/'.Crypt::encryptString($code_gestion).'');

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
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Gestionnaire des achats'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
        if ($infoUserConnect != null) {
            $ref_depot = $infoUserConnect->ref_depot;
        }

        $request->validate([
            'code_structure'=>['required','numeric'],
            'credit_budgetaires_id'=>['required','numeric'],
            'nom_structure'=>['required','string'],
            'ref_fam'=>['required','numeric'],
            'design_fam'=>['required','string'],
            'code_gestion'=>['required','string'],
            'nom_gestion'=>['required','string'],
            'intitule'=>['required','string'],
            'ref_articles'=>['required','array'],
            'design_article'=>['required','array'],
            'qte'=>['required','array'],
        ]);

        $credit_budgetaire = $this->getCreditBudgetaireById($request->credit_budgetaires_id);

        if ($credit_budgetaire!=null) {
            $credit_budgetaires_id = $credit_budgetaire->id;
            $exercice = $credit_budgetaire->exercice;
        }else{
            return redirect()->back()->with('error','Crédit budgétaire introuvable');
        }

        $detail_demande_achat = null;
        
        
        if(isset($request->ref_articles)){
            if (count($request->ref_articles) > 0) {
                foreach ($request->ref_articles as $item => $value) {
                    $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                    try {
                        $qte[$item] = $qte[$item] * 1;
                    } catch (\Throwable $th) {
                        return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                    }
                    
                    if (gettype($qte[$item])!='integer') {
                        return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                    }
                    

                    if (($request->ref_articles[$item] != null) and ($request->design_article[$item] != null) and ($qte[$item] == null) or ($qte[$item] <= 0)) {
                        return redirect()->back()->with('error', 'Veuillez saisir une quantité valide');
                    }
                }

                $num_bc = $this->getLastNumBc($exercice,$request->code_structure);  
                
                // demande d'achat
                $dataStoreDemandeAchat = [
                    'num_bc'=>$num_bc,
                    'ref_fam'=>$request->ref_fam,
                    'ref_depot'=>$ref_depot,
                    'profils_id'=>Session::get('profils_id'),
                    'intitule'=>$request->intitule,
                    'code_gestion'=>$request->code_gestion,
                    'exercice'=>$exercice,
                    'credit_budgetaires_id'=>$credit_budgetaires_id
                ];
                $this->controller3->procedureStoreDemandeAchat($dataStoreDemandeAchat);

                $demande_achats_id = null;

                $demande_achat = $this->controller3->getDemandeAchatByNumBc($num_bc);

                if ($demande_achat != null) {
                    $demande_achats_id = $demande_achat->id;
                }
                

                if (isset($request->piece)) {

                    if (count($request->piece) > 0) {

                        foreach ($request->piece as $item => $value) {
                            if (isset($request->piece[$item])) {

                                $piece =  $request->piece[$item]->store('piece_jointe','public');

                                $name = $request->piece[$item]->getClientOriginalName();

                                $libelle = "Demande d'achats";
                                $flag_actif = 1;
                                $piece_jointes_id = null;

                                $dataPiece = [
                                    'subject_id'=>$demande_achats_id,
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
                

                

                //statut demande achat
                if ($demande_achats_id != null) {

                    $libelle = "Soumis pour validation";

                    $this->storeTypeStatutDemandeAchat($libelle);

                    $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

                    if ($type_statut_demande_achat != null) {
                        $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                        $this->setLastStatutDemandeAchat($demande_achats_id);

                        $dataStatutDemandeAchat = [
                            'demande_achats_id'=>$demande_achats_id,
                            'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>trim($request->commentaire),
                            'profils_id'=>Session::get('profils_id'),
                        ];

                        $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                        //store detail demande

                        foreach ($request->ref_articles as $item => $value) {
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
        
                            if (($request->ref_articles[$item] != null) and ($request->design_article[$item] != null) and ($qte[$item] != null) and ($qte[$item] > 0)) {

                                if (isset($request->echantillon[$item])) {

                                    $echantillon[$item] =  $request->echantillon[$item]->store('echantillonnage','public');

                                    
                                    
                                }else{
                                    $echantillon[$item] = null;
                                }

                                if (isset($request->description_articles_libelle[$item])) {

                                    $this->storeDescriptionArticle($request->description_articles_libelle[$item]);

                                    $description_article = $this->getDescriptionArticle($request->description_articles_libelle[$item]);

                                    if($description_article != null){
                                        $description_articles_id = $description_article->id;
                                    }else{
                                        $description_articles_id = null;
                                    }
                                    
                                }else{
                                    $description_articles_id = null;
                                }
                                
                                $dataDetail = [
                                    'demande_achats_id' => $demande_achats_id,
                                    'ref_articles' => $request->ref_articles[$item],
                                    'qte_demandee' => $qte[$item],
                                    'profils_id'=>Session::get('profils_id'),
                                    'description_articles_id'=>$description_articles_id,
                                    'echantillon'=>$echantillon[$item],
                                    'chargement_bon_de_commande'=>null
                                ];

                                $detail_demande_achat = $this->controller3->procedureStoreDetailDemandeAchat($dataDetail);
                                                        
                            }
                        }


                    }

                }
            }
        }

        if ($detail_demande_achat!=null) {

            $demande_achats_id = $detail_demande_achat->demande_achats_id;
            // notifier l'émetteur
                $subject = 'Enregistrement d\'une demande d\'achats';

            // utilisateur connecté
                $email = auth()->user()->email;
                $this->notifDemandeAchat($email,$subject,$demande_achats_id);
            //
                

            return redirect('demande_achats/index')->with('success','Demande d\'achat effectuée');
        }else{
            return redirect()->back()->with('error','Echec de la demande d\'achat');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DemandeAchat  $demandeAchat
     * @return \Illuminate\Http\Response
     */
    public function show($demandeAchat)
    {
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeAchat);
        } catch (DecryptException $e) {
            //
        }

        $demandeAchat = DemandeAchat::findOrFail($decrypted);
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $signataires = [];

        
        $etape = "show";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception','Pilote AEE','Agent Cnps'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }


        $libelle = null;
        $livraison_commandes = [];

        $type_statut_demande_achats_libelles = ['Fournisseur sélectionné','Transféré au Responsable DCG','Visa (DCG)','Visa (DG)','Édité','Retiré (Frs.)','Livraison partielle','Livraison totale','Livraison partiellement validée','Livraison totalement validée'];

        $statut_demande_achat = $this->getStatutDemandeAchatGroupe($demandeAchat->id,$type_statut_demande_achats_libelles);       

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
        }

        $libelle_last = null;

        $statut_demande_achat_last = $this->getLastStatutDemandeAchat($demandeAchat->id);        

        if ($statut_demande_achat_last!=null) {
            $libelle_last = $statut_demande_achat_last->libelle;
        }

        $demande_achats = $this->getDemandeAchats($demandeAchat->id); 

        $demande_achat_info = $this->getDemandeAchat($demandeAchat->id);

        $type_statut_demande_achats_libelles = ['Partiellement validé','Validé','Partiellement validé (Responsable des achats)','Validé (Responsable des achats)','Demande de cotation (Transmis Responsable DMP)','Demande de cotation (Annulé)','Transmis pour cotation','Fournisseur sélectionné','Transféré au Responsable DCG','Visa (DCG)','Visa (DG)','Édité','Retiré (Frs.)','Livraison partielle','Livraison totale','Livraison partiellement validée','Livraison totalement validée'];

        $statut_demande_achat2 = $this->getStatutDemandeAchatGroupe($demandeAchat->id,$type_statut_demande_achats_libelles);

        if ($statut_demande_achat2!=null) {

            if ($libelle_last!='Soumis pour validation') {

                $demande_achats = $this->getDemandeAchatValiders($demandeAchat->id);

            }
            
        }
        $cotation_fournisseur = $this->getLastCotationFournisseur($demandeAchat->id);        

        if ($cotation_fournisseur!=null) {

            $demande_achats = $this->getDetailCotationFournisseurs($demandeAchat->id,$cotation_fournisseur->cotation_fournisseurs_id); 

            $demande_achat_info = $this->getDetailCotationFournisseur($demandeAchat->id,$cotation_fournisseur->cotation_fournisseurs_id);
            
            $signataires = $this->getSignataires($demandeAchat->id);

            $livraison_commandes = $this->getDetailLivraisons($cotation_fournisseur->cotation_fournisseurs_id);

        }

        $affiche_zone_struture = 1;

        $credit_budgetaire_structure = $this->getCreditBudgetaireById($demandeAchat->credit_budgetaires_id);

        if($credit_budgetaire_structure === null){
            if(isset($demande_achat_info->num_bc)){
                $code_struc = explode('BC',explode('/',$demande_achat_info->num_bc)[1])[0];
                $credit_budgetaire_structure = $this->getStructureByCode($code_struc);
            }
        }

        $type_piece = "Demande d'achats";

        $piece_jointes = $this->getPieceJointes($demandeAchat->id, $type_piece);

        $griser = 1;

        // dernier statut de la demande
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;


        $statut_demande_achat_last = $this->getLastStatutDemandeAchat($demandeAchat->id);

        if ($statut_demande_achat_last!=null) {

            $commentaire = $statut_demande_achat_last->commentaire;
            $profil_commentaire = $statut_demande_achat_last->name;
            $nom_prenoms_commentaire = $statut_demande_achat_last->nom_prenoms;

        }


        //affichage du print
            $title_print = null;
            $href_print = null;
            $display_print = null;

            $type_statut_demande_achats_libelles = ['Édité'];

            $statut_demande_achat_edit = $this->getStatutDemandeAchatGroupe($demandeAchat->id,$type_statut_demande_achats_libelles);

            if ($statut_demande_achat_edit!=null) {
                $statut_demande_achat_edit_id = $statut_demande_achat_edit->id;

                $type_statut_demande_achats_libelles = ['Annulé (Responsable des achats)','Annulé (Fournisseur)'];

                $statut_demande_achat_annule = $this->getStatutDemandeAchatGroupe($demandeAchat->id,$type_statut_demande_achats_libelles);
                
                if ($statut_demande_achat_annule!=null) {
                    $statut_demande_achat_annule_id = $statut_demande_achat_annule->id;
                }else{
                    $statut_demande_achat_annule_id = 0;
                }

                if ($statut_demande_achat_edit_id < $statut_demande_achat_annule_id) {
                    $statut_demande_achat_edit_id = null;
                }

            }else{
                $statut_demande_achat_edit_id = null;
            }

            if ($statut_demande_achat_edit_id !=null) {
                $title_print = "Imprimer le bon de commande";
                $href_print = "/print/da/".Crypt::encryptString($demandeAchat->id);
                $display_print = "";
            }
        //

        $echantillonnage_cnps = $this->controlEchantionnageCnps($demandeAchat->id); 
        
        $statut = $this->getCategorieDemandeAchat($demandeAchat->id);

        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);

        $colonne_description_article = count($this->getDetailDemandeAchatDescription($demandeAchat->id)); 

        return view('demande_achats.show',[
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'libelle'=>$libelle,
            'livraison_commandes'=>$livraison_commandes,
            'piece_jointes'=>$piece_jointes,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'griser'=>$griser,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'title_print'=>$title_print,
            'href_print'=>$href_print,
            'display_print'=>$display_print,
            'echantillonnage_cnps'=>$echantillonnage_cnps,
            'statut'=>$statut,
            'libelle_last'=>$libelle_last,
            'statut_demande_achats'=>$statut_demande_achats,
            'type_profils_name'=>$type_profils_name,
            'signataires'=>$signataires,
            'colonne_description_article'=>$colonne_description_article
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DemandeAchat  $demandeAchat
     * @return \Illuminate\Http\Response
     */

    public function edit($demandeAchat, Request $request,$limited=null,$crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null)
    {
        $famille = null;
        $structure = null;
        $gestion = null;
        $disponible = null;
        $disponible_display = null;

        if($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null){

            $disponible_display = 1;

            $famille = Famille::where('ref_fam',$crypted_ref_fam)->first();
            
            if($famille === null){
                return redirect('/demande_achats/edit/'.$demandeAchat)->with('error', 'Veuillez saisir un compte valide');
            }

            $structure = Structure::where('code_structure',$crypted_code_structure)->first();
            if($structure === null){
                return redirect('/demande_achats/edit/'.$demandeAchat)->with('error', 'Veuillez saisir une structure valide');
            }

            $gestion = Gestion::where('code_gestion',$crypted_code_gestion)->first();
            if($gestion === null){
                return redirect('/demande_achats/edit/'.$demandeAchat)->with('error', 'Veuillez saisir une gestion valide');
            }


        }

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeAchat);
        } catch (DecryptException $e) {
            //
        }

        $demandeAchat = DemandeAchat::findOrFail($decrypted);

        if (Session::has('profils_id')) {

            $etape = "edit";

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Gestionnaire des achats'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);

        $structures = [];

        if ($infoUserConnect != null) {
            $ref_depot = $infoUserConnect->ref_depot;
            $structures = $this->getStructuresByRefDepot($ref_depot);
        }

        $articles = $this->getArticleByFamilleRef($demandeAchat->ref_fam);
        $demande_achats = $this->getDemandeAchats($demandeAchat->id);
        $gestions = $this->getGestion();
        $info = $this->getDemandeAchat($demandeAchat->id);
       
        $credit_budgetaires = $this->getFamille();
        $credit_budgetaires_select = $this->getDemandeAchat($demandeAchat->id);
        $griser = null;
        $exercices = $this->getExercice();


        if($info != null && isset($famille)){

            if($info->ref_fam != $famille->ref_fam){
                $demande_achats = [];
            }

        }

        

        if($info != null && $crypted_ref_fam === null && $crypted_code_structure === null && $crypted_code_gestion === null){

            $disponible = $this->getCreditBudgetaireDisponible($info->ref_fam, $info->code_structure, $info->code_gestion, $info->exercice);

            $disponible_display = 1;

        }

        

        if ($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null) {

            if (isset($famille) && isset($structure) && isset($exercices) && isset($gestion)) {
                try {
                    $param = $exercices->exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;

                    $this->storeCreditBudgetaireByWebService($param, $structure->ref_depot);

                    $articles = $this->getArticleConcerners($famille->ref_fam, $structure->code_structure, $gestion->code_gestion, $exercices->exercice);

                    $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam, $structure->code_structure, $gestion->code_gestion, $exercices->exercice);

                    $demande_achats = $this->getDemandeAchats($demandeAchat->id,$famille->ref_fam);                    

                } catch (\Throwable $th) {
                }
            }
        }   

        $type_statut_demande_achats_libelles = ['Soumis pour validation','Partiellement validé','Rejeté','Rejeté (Responsable des achats)','Annulé (Responsable des achats)'];

        $statut_demande_achat = $this->getStatutDemandeAchatGroupe($demandeAchat->id,$type_statut_demande_achats_libelles);

        if ($statut_demande_achat === null) {
            return redirect()->back()->with('error','Modification impossible. Demande d\'achat déjà traitée');
        }

        

        

        

        // dernier statut de la demande
        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;


        $statut_demande_achat = $this->getLastStatutDemandeAchat($demandeAchat->id);

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;
        }

        $entete = "DEMANDE D'ACHAT ENREGISTRÉE";

        $value_bouton = null;
        $bouton = null;

        $value_bouton2 = null;
        $bouton2 = null;
        
        $url_complet = $request->url();
        
        $tableau = explode('demande_achats/',$url_complet);

        $url1 = $tableau[0];
        $url2 = $tableau[1];

        $tableau2 = explode('/',$url2);

        $page_view = $tableau2[0];
        
        if ($type_profils_name === "Gestionnaire des achats") {

            if ($libelle != "Soumis pour validation" && $libelle != "Rejeté (Responsable des achats)" && $libelle != "Annulé (Gestionnaire des achats)" && $libelle != "Annulé (Responsable des achats)") {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === "Soumis pour validation" or $libelle === "Rejeté (Responsable des achats)" or $libelle === "Annulé (Gestionnaire des achats)" or $libelle === "Annulé (Responsable des achats)") {

                $entete = "MODIFICATION DE LA DEMANDE D'ACHAT";

                if ($page_view === 'edit') {

                    $value_bouton = "modifier";
                    $bouton = "Modifier";

                    if ($libelle != "Annulé (Gestionnaire des achats)") {
                        $value_bouton2 = "annuler_g_achat";
                        $bouton2 = "Annuler";
                    }

                }elseif ($page_view === 'send') {

                    $entete = "TRANSMISSION DE LA DEMANDE AU RESPONSABLE DES ACHATS";

                    $value_bouton = "tr_r_achat";
                    $bouton = "Transmettre";

                    if ($libelle != "Annulé (Gestionnaire des achats)") {
                        $value_bouton2 = "annuler_g_achat";
                        $bouton2 = "Annuler";
                    }

                }

            }

            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        //vérifier si le Responsable DMP à visé le choix du fournisseur
            $statut = $this->getCategorieDemandeAchat($demandeAchat->id);
        //
        
            
        $credit_budgetaire_structure = $this->getCreditBudgetaireById($demandeAchat->credit_budgetaires_id);

        $affiche_zone_struture = 1;

        $type_piece = "Demande d'achats";

        $piece_jointes = $this->getPieceJointes($demandeAchat->id, $type_piece);

        if ($value_bouton === null) {
            $griser = 1;
        }
        $echantillonnage_cnps = $this->controlEchantionnageCnps($demandeAchat->id);  

        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);

        $familles = $this->getFamille();

        return view('demande_achats.edit',[
            'articles'=>$articles,
            'gestions'=>$gestions,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'info'=>$info,
            'demande_achats'=>$demande_achats,
            'demande_achats_id'=> $demandeAchat->id,
            'commentaire'=>$commentaire,
            'libelle'=>$libelle,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'statut'=>$statut,
            'structures'=>$structures,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'piece_jointes'=>$piece_jointes,
            'bouton'=>$bouton,
            'value_bouton'=>$value_bouton,
            'bouton2'=>$bouton2,
            'value_bouton2'=>$value_bouton2,
            'entete'=>$entete,
            'demandeAchat'=>$demandeAchat,
            'echantillonnage_cnps'=>$echantillonnage_cnps,
            'statut_demande_achats'=>$statut_demande_achats,
            'familles'=>$familles,
            'famille'=>$famille,
            'structure'=>$structure,
            'gestion'=>$gestion,
            'disponible'=>$disponible,
            'disponible_display'=>$disponible_display,
        ]);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DemandeAchat  $demandeAchat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DemandeAchat $demandeAchat)
    {
        if (isset($request->submit)) {
            if ($request->submit === 'annuler_g_achat') {
                $request->validate([
                    'code_structure'=>['required','numeric'],
                    'credit_budgetaires_id'=>['required','numeric'],
                    'nom_structure'=>['required','string'],
                    'commentaire'=>['required','string'],
                    'id'=>['required','numeric'],
                    'ref_fam'=>['required','numeric'],
                    'design_fam'=>['required','string'],
                    'code_gestion'=>['required','string'],
                    'nom_gestion'=>['required','string'],
                    'intitule'=>['required','string'],
                    'ref_articles'=>['required','array'],
                    'design_article'=>['required','array'],
                    'qte'=>['required','array'],
                    'submit'=>['required','string'],
                    'echantillon_flag'=>['nullable','array'] 
                ]);
            }else{
                $request->validate([
                    'code_structure'=>['required','numeric'],
                    'credit_budgetaires_id'=>['required','numeric'],
                    'nom_structure'=>['required','string'],
                    'commentaire'=>['nullable','string'],
                    'id'=>['required','numeric'],
                    'ref_fam'=>['required','numeric'],
                    'design_fam'=>['required','string'],
                    'code_gestion'=>['required','string'],
                    'nom_gestion'=>['required','string'],
                    'intitule'=>['required','string'],
                    'ref_articles'=>['required','array'],
                    'design_article'=>['required','array'],
                    'qte'=>['required','array'],
                    'submit'=>['required','string'],
                    'echantillon_flag'=>['nullable','array'] 
                ]);
            }
        }else{
            $request->validate([
                'code_structure'=>['required','numeric'],
                'credit_budgetaires_id'=>['required','numeric'],
                'nom_structure'=>['required','string'],
                'commentaire'=>['nullable','string'],
                'id'=>['required','numeric'],
                'ref_fam'=>['required','numeric'],
                'design_fam'=>['required','string'],
                'code_gestion'=>['required','string'],
                'nom_gestion'=>['required','string'],
                'intitule'=>['required','string'],
                'ref_articles'=>['required','array'],
                'design_article'=>['required','array'],
                'qte'=>['required','array'],
                'submit'=>['required','string'],
                'echantillon_flag'=>['nullable','array'] 
            ]);
        }

        

        if (Session::has('profils_id')) {

            $etape = "update";

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Gestionnaire des achats'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
        if ($infoUserConnect != null) {
            $ref_depot = $infoUserConnect->ref_depot;
        }

        $demande_control = $this->getDemandeAchat($request->id);

        if ($demande_control!=null) {

            if ($demande_control->ref_depot != $ref_depot) {

                return redirect()->back()->with('error','Vous n\'est pas authorisé à effectuer cette action');

            }

        }else{
            return redirect()->back()->with('error','Demande d\'achat introuvable.');
        }


        
        $message_success = '';
        $message_error = 'Echec de l\'opération';

        $detail_demande_achat = null;

        if (isset($request->submit)) {
            if ($request->submit === "modifier" or $request->submit === "tr_r_achat" or $request->submit === "annuler_g_achat") {
                if(isset($request->ref_articles)){
                    if (count($request->ref_articles) > 0) {
                        foreach ($request->ref_articles as $item => $value) {
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);

                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            if (($request->ref_articles[$item] != null) and ($request->design_article[$item] != null) and ($qte[$item] == null) or ($qte[$item] <= 0)) {
                                return redirect()->back()->with('error', 'Veuillez saisir une quantité valide');
                            }
                        }
                        
                        // demande d'achat
                        $credit_budgetaire = $this->getCreditBudgetaireById($request->credit_budgetaires_id);
                            
                        if ($credit_budgetaire!=null) {
                            $credit_budgetaires_id = $credit_budgetaire->credit_budgetaires_id;
                            $exercice = $credit_budgetaire->exercice;
                        }else{
                            return redirect()->back()->with('error','Crédit budgétaire introuvable');
                        }

                        $demande_achat = $this->getDemandeAchat($request->id);
                        if($demande_achat != null){

                            //$demande_achats_id,$profils_id,$ref_depot,$credit_budgetaires_id,$intitule,$code_gestion,$exercice,$ref_fam,$num_bc=null

                            $explode_num_bc = explode('/',explode('BC', $demande_achat->num_bc)[0])[1];
                            
                            if($explode_num_bc === $request->code_structure){    
                                $num_bc = $demande_achat->num_bc;
                            }
                            
                            if($explode_num_bc != $request->code_structure){
                                $num_bc = $this->getLastNumBc($exercice,$request->code_structure,$demande_achat->id);
                            }

                            $dataSetDemandeAchat = [
                                'profils_id'=>Session::get('profils_id'),
                                'ref_depot'=>$ref_depot,
                                'credit_budgetaires_id'=>$credit_budgetaires_id,
                                'intitule'=>$request->intitule,
                                'code_gestion'=>$request->code_gestion,
                                'exercice'=>$exercice,
                                'num_bc'=>$num_bc,
                                'ref_fam'=>$request->ref_fam,
                            ];

                            $this->controller3->setDemandeAchat($demande_achat->id,$dataSetDemandeAchat);
                        }
                            
                        // détacher fichier joint
                        $type_piece = "Demande d'achats";
                        
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

                                
                                $libelle = "Demande d'achats";
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

        
                                        $libelle = "Demande d'achats";

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

                        //statut demande achat
                        
                        

                        if ($request->submit === "modifier") {
                            $libelle = 'Soumis pour validation';
                            $libelle_df = 'Imputé (Gestionnaire des achats)';
                        }elseif ($request->submit === "tr_r_achat") {
                            $libelle = 'Transmis (Responsable des achats)';            
                            $libelle_df = 'Transmis (Responsable des achats)';            
                        }elseif ($request->submit === "annuler_g_achat") {
                            $libelle = 'Annulé (Gestionnaire des achats)';
                            $libelle_df = 'Annulé (Gestionnaire des achats)';
                        }
                        
                        // statut demande d'achat
                        $commentaire = $request->commentaire;

                        $this->storeTypeStatutDemandeAchat($libelle);

                        $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

                        if ($type_statut_demande_achat != null) {
                            $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                            $this->setLastStatutDemandeAchat($request->id);
                            
                            $dataStatutDemandeAchat = [
                                'demande_achats_id'=>$request->id,
                                'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                                'date_debut'=>date('Y-m-d'),
                                'date_fin'=>date('Y-m-d'),
                                'commentaire'=>trim($commentaire),
                                'profils_id'=>Session::get('profils_id'),
                            ];

                            $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                            $type_operations_libelle = "Demande d'achats" ;

                            $this->storeTypeOperation($type_operations_libelle);

                            $type_operation = $this->getTypeOperation($type_operations_libelle);
                            if ($type_operation!=null) {

                                $type_operations_id = $type_operation->id;

                                $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($request->id,$type_operations_id);

                                if($demande_fond_bon_commande != null){

                                    if(isset($libelle_df)){
                                        $this->storeStatutDemandeFond($libelle_df,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'),$commentaire);
                                    }
                                    

                                }
                            }
        

                            

                        }

                        $donnees = [];

                        foreach ($request->ref_articles as $item => $value) {
                            $donnees[$item] = $request->ref_articles[$item];
                        }

                        

                        $detail_demande_achat_deletes = $this->getDeleteDetailDemandeAchat($request->id,$donnees);

                        foreach ($detail_demande_achat_deletes as $detail_demande_achat_delete) {

                            $detail_demande_achat_delete_id = $detail_demande_achat_delete->id;
                            $detail_demande_achat_delete_ref_articles = $detail_demande_achat_delete->ref_articles;

                            
                            $this->deleteDemandeAchatCascade($request->id,$detail_demande_achat_delete_id,$detail_demande_achat_delete_ref_articles);

                            


                        }

                        
                        // DetailDemandeAchat::where('demande_achats_id',$demande_achats_id)->delete();
                        
                        foreach ($request->ref_articles as $item => $value) {
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);

                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }


                            if (($request->ref_articles[$item] != null) and ($request->design_article[$item] != null) and ($qte[$item] != null) and ($qte[$item] > 0)) {

                                $setDetailDemandeAchat =  1;
                                
                                if (isset($request->echantillon[$item])) {

                                    $echantillon[$item] =  $request->echantillon[$item]->store('echantillonnage','public');                                 
                                    
                                }else{

                                                                       
                                    if (isset($request->echantillon_flag[$item])) {

                                        if ($request->echantillon_flag[$item] == 1) {

                                            $setDetailDemandeAchat =  2;

                                        }elseif ($request->echantillon_flag[$item] == 0){

                                            $setDetailDemandeAchat =  1;
                                            $echantillon[$item] = null;
                                            
                                        } 
                                        
                                    }else{
                                        $echantillon[$item] = null; 
                                    }
                                }

                                 


                                if (isset($request->description_articles_libelle[$item])) {

                                    $this->storeDescriptionArticle($request->description_articles_libelle[$item]);

                                    $description_article = $this->getDescriptionArticle($request->description_articles_libelle[$item]);

                                    if($description_article != null){
                                        $description_articles_id = $description_article->id;
                                    }else{
                                        $description_articles_id = null;
                                    }
                                    
                                }else{
                                    $description_articles_id = null;
                                }

                                

                                $detail_demande_achats = $this->getDetailDemandeAchatByRefArticle($request->id,$request->ref_articles[$item]);

                                //dd($detail_demande_achats,$qte[$item],$setDetailDemandeAchat,$request);

                                if ($detail_demande_achats!=null) {

                                    if ($setDetailDemandeAchat ===  1) {

                                        $detail_demande_achat = $this->setDetailDemandeAchat($detail_demande_achats->id,$request->id,$request->ref_articles[$item],$qte[$item],Session::get('profils_id'),$description_articles_id,$echantillon[$item]);

                                    }elseif ($setDetailDemandeAchat ===  2){

                                        $detail_demande_achat = $this->setDetailDemandeAchat2($detail_demande_achats->id,$request->id,$request->ref_articles[$item],$qte[$item],Session::get('profils_id'),$description_articles_id);

                                    }
                                    

                                }else{

                                    $dataDetail = [
                                        'demande_achats_id' => $request->id,
                                        'ref_articles' => $request->ref_articles[$item],
                                        'qte_demandee' => $qte[$item],
                                        'profils_id'=>Session::get('profils_id'),
                                        'description_articles_id'=>$description_articles_id,
                                        'echantillon'=>$echantillon[$item],
                                        'chargement_bon_de_commande'=>null
                                    ];
                                    $detail_demande_achat = $this->controller3->procedureStoreDetailDemandeAchat($dataDetail);

                                }
                                
                                if ($request->submit === "modifier") {
                                    $message_success = 'Modification de la demande d\'achat effectuée';
                                }elseif ($request->submit === "tr_r_achat") {
                                    $message_success = 'Transmission de la demande d\'achat effectuée';                    
                                }elseif ($request->submit === "annuler_g_achat") {
                                    $libelle = 'Annulation de la demande d\'achat';
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($detail_demande_achat!=null) {

            if (isset($request->submit)) {

                if ($request->submit === "modifier") {
                    // notifier l'émetteur
                        $subject = 'Modification de demande d\'achats';

                    // utilisateur connecté
                        $email = auth()->user()->email;
                        $this->notifDemandeAchat($email,$subject,$request->id);
                    //

                }elseif ($request->submit === "annuler_g_achat") {
                    // notifier l'émetteur
                        $subject = 'Annulation de demande d\'achats';

                    // utilisateur connecté
                        $email = auth()->user()->email;
                        $this->notifDemandeAchat($email,$subject,$request->id);
                    //

                }elseif ($request->submit === "tr_r_achat") {
                    // notifier l'émetteur
                    $subject = 'Transmission de demande d\'achats au Responsable des achats';

                    // utilisateur connecté
                        $email = auth()->user()->email;
                        $this->notifDemandeAchat($email,$subject,$request->id);
                    //


                    // notifier le responsable des achats
                        $this->notifDemandeAchat($email,$subject,$request->id);
                }
            }

            
            
            if (!isset($message_success)) {
                $message_success = "Modification effectuée";
            }
            return redirect('/demande_achats/index')->with('success',$message_success);
        }else{
            return redirect()->back()->with('error',$message_error);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DemandeAchat  $demandeAchat
     * @return \Illuminate\Http\Response
     */
    public function destroy(DemandeAchat $demandeAchat)
    {
        //
    }

    public function cotation($demandeAchat, Request $request)
    {
        
        $verrou = null;

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeAchat);
        } catch (DecryptException $e) {
            //
        }

        $demandeAchat = DemandeAchat::findOrFail($decrypted);

        if (Session::has('profils_id')) {

            $etape = "cotation";

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_achats = $this->getTypeAchatDemandeAchats();

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;


        $statut_demande_achat = $this->getLastStatutDemandeAchat($demandeAchat->id);
        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;
        }     
        
        $credit_budgetaires_select = null;

        $griser = 1;

        $demande_achats = $this->getValiderDemandeAchats($demandeAchat->id);

        $commande = $this->getCommande($demandeAchat->id);       

        $demande_achat_info = $this->getDemandeAchat($demandeAchat->id);

        $libelle_type_achat = null;

        $type_achat = $this->getTypeAchatDemandeAchat($demandeAchat->id);
        
        if ($type_achat!=null) {
            $libelle_type_achat = $type_achat->libelle;
        }

        $periodes = $this->getPeriodes();
        
        //dd($demande_achats_fusionnables);
        $organisations = [];
        if ($demande_achat_info!=null) {

            //$organisations = $this->getOrganisationArticleByRefFamAndRefDepot($demande_achat_info->ref_fam,$demande_achat_info->ref_depot);
            
            $organisations = $this->getOrganisationActifs(); 

            if ($organisations!=null) {
                $nbre_organisations = count($organisations);
            }else{
                $nbre_organisations = 0;
            }

            if (count($organisations) === 0) {

                return redirect()->back()->with('error', 'Veuillez demander à l\'administrateur fonctionnel de rattacher à votre dépôt au moins un fournisseur au compte : "'.$demande_achat_info->design_fam.'"');

            }
            
        }

        $preselections = $this->getPreselectionSoumissionnaires($demandeAchat->id); 

        $all_preselection = $this->getPreselectionSoumissionnaire($demandeAchat->id);          
        
       //vérifier si le Responsable DMP à visé le choix du fournisseur
            $statut = $this->getCategorieDemandeAchat($demandeAchat->id);
       //

        $credit_budgetaire_structure = $this->getCreditBudgetaireById($demandeAchat->credit_budgetaires_id);

        $affiche_zone_struture = 1;

        $type_piece = "Demande d'achats";

        $piece_jointes = $this->getPieceJointes($demandeAchat->id, $type_piece);
        
        $entete = "Demande de cotation";

        $value_bouton = null;
        $bouton = null;

        $value_bouton2 = null;
        $bouton2 = null;

        $url_complet = $request->url();
        
        $tableau = explode('demande_achats/',$url_complet);

        $url2 = $tableau[1];

        $tableau2 = explode('/',$url2);

        $page_view = $tableau2[0];

        if ($page_view === 'send_cotation') {
            $entete = "Transmission la demande de cotation au Responsable DMP";

            $value_bouton = 'transmettre';
            $bouton = 'Transmettre';

            $value_bouton2 = 'annuler_r_achat';
            $bouton2 = 'Annuler';
        }elseif ($page_view === 'cotation') {

            if($type_profils_name === 'Responsable des achats'){
                $entete = "Enregistrement la demande de cotation";


                $value_bouton = 'soumettre';
                $bouton = 'Enregistrer';
    
                $value_bouton2 = 'annuler_r_achat';
                $bouton2 = 'Annuler';
            }elseif($type_profils_name === 'Responsable DMP'){
                
                $entete = "Validation de la demande de cotation";

                $value_bouton = 'valider';
                $bouton = 'Valider';
    
                $value_bouton2 = 'annuler_r_cmp';
                $bouton2 = 'Rejeter';

                $verrou = 1;

            }

            


        }elseif ($page_view === 'cotation_send_frs') {
            $entete = "Transmission la demande de cotation aux fournisseurs présélectionnés";

            $value_bouton = 'transmettre_frs';
            $bouton = 'Transmettre';

            $value_bouton2 = 'annuler_r_achat';
            $bouton2 = 'Annuler';
        }
        
        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);

        $disponible = $this->getCreditBudgetaireDisponibleByDemandeAchatId($demandeAchat->id);

        return view('demande_achats.cotation',[
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'piece_jointes'=>$piece_jointes,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'periodes'=>$periodes,
            'commande'=>$commande,
            'organisations' =>$organisations,
            'nbre_organisations'=>$nbre_organisations,
            'libelle_type_achat'=>$libelle_type_achat,
            'preselections' => $preselections,
            'all_preselection'=>$all_preselection,
            'commentaire'=>$commentaire,
            'libelle'=>$libelle,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'type_achats'=>$type_achats,
            'statut'=>$statut,
            'entete'=>$entete,
            'bouton'=>$bouton,
            'value_bouton'=>$value_bouton,
            'bouton2'=>$bouton2,
            'value_bouton2'=>$value_bouton2,
            'verrou'=>$verrou,
            'statut_demande_achats'=>$statut_demande_achats,
            'disponible'=>$disponible
        ]);
    }

    public function cotation_store(Request $request)
    {
        if(isset($request->submit)){
            if ($request->submit === 'annuler_r_achat' or $request->submit === 'annuler_r_cmp') {
                $request->validate([
                    'commentaire'=>['required','string'],
                    'periode_id'=>['required','string'],
                    'valeur'=>['required','numeric'],
                    'delai'=>['nullable','numeric'],
                    'date_echeance'=>['required','string'],
                    'id'=>['required','numeric'],
                    'libelle_type_achat'=>['nullable','string'],
                    'taux_acompte'=> ['nullable','numeric','min:0','max:100'],
                    'time_echeance' => ['nullable','date_format:H:i'],
                ]);
            }else{
                $request->validate([
                    'commentaire'=>['nullable','string'],
                    'periode_id'=>['required','string'],
                    'valeur'=>['required','numeric'],
                    'delai'=>['nullable','numeric'],
                    'date_echeance'=>['required','string'],
                    'id'=>['required','numeric'],
                    'libelle_type_achat'=>['nullable','string'],
                    'taux_acompte'=> ['nullable','numeric','min:0','max:100'],
                    'time_echeance' => ['nullable','date_format:H:i'],
                ]);
            }
        }
        

        

        $etape = "cotation_store";

        

        if (Session::has('profils_id')) {

            $etape = "cotation";

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];

            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        
        if ($request->submit === 'soumettre' or $request->submit === 'transmettre' or $request->submit === 'annuler_r_achat' or $request->submit === 'transmettre_frs') {
            
            
            // vérifier le type d'achat
                if ($request->libelle_type_achat === "Appel d'offre") {
                    if (!isset($request->designation_org)) {

                        if ($request->submit != 'annuler_r_achat') {
                            return redirect()->back()->with('error', 'Pour un achat de type : (Appel d\'offre) vous devez impérativemment sélectionner le fournisseur');
                        }

                    }

                    if (isset($request->designation_org)) {
                        if (count($request->designation_org) > 0) {
                            foreach ($request->designation_org as $item => $value) {
                                if ($request->designation_org[$item]===null) {

                                    if ($request->submit != 'annuler_r_achat') {
                                        return redirect()->back()->with('error', 'Pour un achat de type : (Appel d\'offre) vous devez impérativemment sélectionner le fournisseur');
                                    }
                                    
                                }
                            }
                        }
                    }

                }
            //

            // vérifier les fournisseurs
                
                    if (isset($request->designation_org)) {
                        if (count($request->designation_org) > 0) {
                            foreach ($request->designation_org as $item => $value) {
                                $organisation = null;
                                if ($request->designation_org[$item]!=null) {
                                    $org = explode(' - ', $request->designation_org[$item]);
                                    $organisation_id = $org[0];
                                    $denomination = $org[1];

                                    // determination de la famille d'articles
                                    

                                    $demande_achat = $this->getDemandeAchat($request->id);

                                    if ($demande_achat!=null) {

                                        $ref_fam = $demande_achat->ref_fam;
                                        $ref_depot = $demande_achat->ref_depot;
                                        $design_fam = $demande_achat->design_fam;
                                        $design_dep = $demande_achat->design_dep;

                                        $this->controller3->setOrganitionsArticles($organisation_id,$ref_fam);

                                        $this->controller3->setOrganitionsDepots($organisation_id,$ref_depot);

                                        $organisation = $this->controller3->getOrganisationByFamilleByDepot($organisation_id,$ref_fam,$ref_depot);                                       

                                        if ($organisation===null) {

                                            return redirect()->back()->with('error', 'Le fournisseur : ('.$denomination.') n\'est pas reconnu comme étant un prestataire de service de la famille : ('.$design_fam.') au dépôt ('.$design_dep.')');

                                        }
                                    }
                                }
                            }
                        }
                    }
                
            //

            // ref_fam et ref_depot

                $demande_achat = $this->getDemandeAchat($request->id);

                if ($demande_achat!=null) {

                    $ref_fam = $demande_achat->ref_fam;
                    $ref_depot = $demande_achat->ref_depot;

                    DemandeAchat::where('id',$request->id)->update([
                        'taux_acompte'=>$request->taux_acompte
                    ]);

                }

            //

            $periode = $this->getPeriode($request->periode_id);
            if ($periode != null) {
                $periodes_id = $periode->id;
            }else{
                return redirect()->back()->with('error','Période introuvable');
            }
            
                
            $block_date = explode("/",$request->date_echeance);
            $d = $block_date[0];
            $m = $block_date[1];
            $Y = $block_date[2];

            $date_echeance = $Y.'-'.$m.'-'.$d;

            if (isset($request->time_echeance)) {
                $date_echeance = $date_echeance.' '.$request->time_echeance;
            }
            
            $dataStoreCommande = [
                'periodes_id'=>$periodes_id,
                'delai'=>$request->delai,
                'date_echeance'=>$date_echeance,
                'demande_achats_id'=>$request->id,
                'profils_id'=>Session::get('profils_id'),
            ];
            $commande = $this->controller3->getCommandeInfo($request->id);
            if ($commande === null) {
                $this->controller3->storeCommande($dataStoreCommande);
            }
            
            if($commande != null){
                $this->controller3->setCommande($dataStoreCommande);
            }                

            
            if ($request->submit === 'soumettre') {
                $libelle = 'Demande de cotation';
                $libelle_df = 'Demande de cotation';
            }elseif ($request->submit === 'annuler_r_achat') {
                $libelle = 'Annulé (Responsable des achats)';
                $libelle_df = 'Annulé (Responsable des achats)';
            }elseif ($request->submit === 'transmettre') {
                $libelle = 'Demande de cotation (Transmis Responsable DMP)';                
                $libelle_df = 'Demande de cotation (Transmis Responsable DMP)';                
            }elseif ($request->submit === 'transmettre_frs') {
                $libelle = 'Transmis pour cotation';
                $libelle_df = 'Transmis pour cotation';
            }

            $commentaire = $request->commentaire;

            $this->storeTypeStatutDemandeAchat($libelle);

            $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

            if ($type_statut_demande_achat != null) {

                $type_statut_demande_achats_id = $type_statut_demande_achat->id;
                
                $dataStatutDemandeAchat = [
                    'demande_achats_id'=>$request->id,
                    'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                    'date_debut'=>date('Y-m-d'),
                    'date_fin'=>date('Y-m-d'),
                    'commentaire'=>trim($commentaire),
                    'profils_id'=>Session::get('profils_id'),
                ];
                $statut_demande_achat = $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                $type_operations_libelle = "Demande d'achats" ;

                $this->storeTypeOperation($type_operations_libelle);

                $type_operation = $this->getTypeOperation($type_operations_libelle);
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                    $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($request->id,$type_operations_id);

                    if($demande_fond_bon_commande != null){

                        if(isset($libelle_df)){
                            $this->storeStatutDemandeFond($libelle_df,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'),$request->commentaire);
                        }                            

                    }
                }

            }
            
            
            $libelle_visa = ['Demande de cotation','Transmis pour cotation'];
           
            $demande_ac  =  $this->getDemandeAchatVisa($request->id,$libelle_visa);

            if ($demande_ac!=null) {

                $intitule = $demande_ac->intitule;
                $design_fam = $demande_ac->design_fam;

            }  


            // mise à jour de la demande d'achats 
                $type_achats_id = null;

                $type_achat = $this->controller3->getTypeAchatByLibelle($request->libelle_type_achat);

                if ($type_achat!=null) {
                    $type_achats_id = $type_achat->id;
                }else{

                    $type_achat = $this->controller3->storeTypeAchat($request->libelle_type_achat);

                    $type_achats_id = $type_achat->id;
                }

                $dataSetDemandeAchat = [
                    'type_achats_id'=>$type_achats_id
                ];
                $this->controller3->setDemandeAchat($request->id,$dataSetDemandeAchat);

            //
            $libelle_critere = 'Fournisseurs Cibles';
            $critere =  $this->controller3->getCritereByLibelle($libelle_critere);
            
            if ($critere === null) {

                $critere = $this->controller3->storeCritere($libelle_critere);

                $criteres_id = $critere->id;

            }else{
                $criteres_id = $critere->id;
            }

            if ($criteres_id != null) {

                $critere_adjudication = $this->controller3->getCritereAdjudicationByCritereByDemandeAchat($criteres_id,$request->id);
                

                if ($critere_adjudication===null) {

                    $critere_adjudication = $this->controller3->storeCritereAdjudication($criteres_id,$request->id);

                    $critere_adjudications_id = $critere_adjudication->id;

                }else{
                    $critere_adjudications_id = $critere_adjudication->id;
                }

            }

            PreselectionSoumissionnaire::where('critere_adjudications_id',$critere_adjudications_id)->delete();
            
            if (isset($request->designation_org)) {

                if (count($request->designation_org) > 0) {

                    foreach ($request->designation_org as $item => $value) {
                            $envoi_mail = 0;

                            if ($request->designation_org[$item]!=null) {
                                
                                $org = explode(' - ',$request->designation_org[$item]);
                                $organisation_id = $org[0];
                                $denomination = $org[1];
                                

                                // vérifier le fournisseur
                                $organisation = $this->controller3->getOrganisationByFamilleByDepot($organisation_id,$ref_fam,$ref_depot);
    
                                if ($organisation!=null) {         
                                    if (isset($intitule) && isset($design_fam)) {
    
                                        if ($critere_adjudications_id!=null) {

                                            $preselection_soumissionnaire = $this->controller3->getPreselectionSoumissionnaireByOrganisation($organisation->organisations_id,$critere_adjudications_id);                                           

                                            if ($preselection_soumissionnaire === null) {

                                                $preselection_soumissionnaire = $this->controller3->storePreselectionSoumissionnaire($organisation->organisations_id,$critere_adjudications_id);

                                            }else{

                                                $this->controller3->setPreselectionSoumissionnaire($organisation->organisations_id,$critere_adjudications_id,$preselection_soumissionnaire->id);

                                            } 
                                        }
                                    }
                                }
                            }
                    }   
                }else{
                    if ($critere_adjudications_id!=null) {
    
                        $all_organisations = $this->getOrganisationByFamilleByDepots($ref_fam,$ref_depot);                    
    
                        foreach ($all_organisations as $all_organisation) {
    
                            $preselection_soumissionnaire = $this->controller3->getPreselectionSoumissionnaireByOrganisation($all_organisation->organisations_id,$critere_adjudications_id);
    
                            if ($preselection_soumissionnaire===null) {
    
                                $preselection_soumissionnaire = $this->controller3->storePreselectionSoumissionnaire($all_organisation->organisations_id,$critere_adjudications_id);
    
    
                            }else{
    
                                $this->controller3->setPreselectionSoumissionnaire($all_organisation->organisations_id,$critere_adjudications_id,$preselection_soumissionnaire->id);
    
                            }
    
                        }
                        
                    }
                }
            }

            if ($statut_demande_achat!=null) {

                // notifier l'émetteur
                $subject = 'Demande de cotation fournisseur';

                if ($request->submit === 'soumettre') {

                    $subject = 'Demande de cotation fournisseur';

                }elseif ($request->submit === 'annuler_r_achat') {

                    $subject = 'Demande de cotation fournisseur annulée';

                    $this->deleteCascade($request->id);

                }elseif ($request->submit === 'transmettre') {

                    $subject = 'Demande de cotation fournisseur transmise au Responsable DMP';             

                }elseif ($request->submit === 'transmettre_frs') {

                    $subject = 'Demande de cotation fournisseur';  

                }

                // utilisateur connecté
                    $email = auth()->user()->email;

                    $this->notifDemandeAchat($email,$subject,$request->id);
                //

                    $type_profils_names = ['Gestionnaire des achats','Responsable DMP'];

                    $this->notifDemandeAchats($subject,$request->id,$type_profils_names);

                if ($request->submit === 'transmettre_frs') {
                    $this->notifDemandeAchatFournisseurs($subject,$request->id);
                }

                return redirect('/demande_achats/index')->with('success',$subject);

            }else{
                return redirect()->back()->with('error','Echec de l\'envoi à cotation');
            }


            
        }elseif ($request->submit === 'valider' ) {

            $libelle = 'Demande de cotation (Validé)';
            $libelle_df = 'Demande de cotation (Validé)';
            
            $commentaire = $request->commentaire;

            $this->storeTypeStatutDemandeAchat($libelle);

            $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

            if ($type_statut_demande_achat != null) {

                $type_statut_demande_achats_id = $type_statut_demande_achat->id;
                
                $dataStatutDemandeAchat = [
                    'demande_achats_id'=>$request->id,
                    'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                    'date_debut'=>date('Y-m-d'),
                    'date_fin'=>date('Y-m-d'),
                    'commentaire'=>trim($commentaire),
                    'profils_id'=>Session::get('profils_id'),
                ];

                $statut_demande_achat = $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                $type_operations_libelle = "Demande d'achats" ;

                $this->storeTypeOperation($type_operations_libelle);

                $type_operation = $this->getTypeOperation($type_operations_libelle);
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                    $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($request->id,$type_operations_id);

                    if($demande_fond_bon_commande != null){

                        if(isset($libelle_df)){
                            $this->storeStatutDemandeFond($libelle_df,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'),$request->commentaire);
                        }                            

                    }
                }

            }


            $subject = 'Demande de cotation validée';             
                

            // utilisateur connecté

                $email = auth()->user()->email;

                $this->notifDemandeAchat($email,$subject,$request->id);
            
            //

                $type_profils_names = ['Gestionnaire des achats','Responsable DMP'];

                $this->notifDemandeAchats($subject,$request->id,$type_profils_names);

                if ($statut_demande_achat != null) {
                    return redirect('/demande_achats/index')->with('success',$subject);
                }else{
                    return redirect()->back()->with('error','Echec de la validation de la cotation');
                }
        }elseif ($request->submit === 'annuler_r_cmp' ) {
            $request->validate([
                'commentaire'=>['required','string'],
            ]);
            $libelle = 'Demande de cotation (Annulé)';
            $libelle_df = 'Demande de cotation (Annulé)';
            
            $commentaire = $request->commentaire;

            $this->storeTypeStatutDemandeAchat($libelle);

            $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

            if ($type_statut_demande_achat != null) {

                $type_statut_demande_achats_id = $type_statut_demande_achat->id;
                
                $dataStatutDemandeAchat = [
                    'demande_achats_id'=>$request->id,
                    'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                    'date_debut'=>date('Y-m-d'),
                    'date_fin'=>date('Y-m-d'),
                    'commentaire'=>trim($commentaire),
                    'profils_id'=>Session::get('profils_id'),
                ];

                $statut_demande_achat = $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                $type_operations_libelle = "Demande d'achats" ;

                $this->storeTypeOperation($type_operations_libelle);

                $type_operation = $this->getTypeOperation($type_operations_libelle);
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                    $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($request->id,$type_operations_id);

                    if($demande_fond_bon_commande != null){

                        if(isset($libelle_df)){
                            $this->storeStatutDemandeFond($libelle_df,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'),$request->commentaire);
                        }                            

                    }
                }

            }

            $subject = 'Demande de cotation annulée';             
                

            // utilisateur connecté
                $email = auth()->user()->email;

                $this->notifDemandeAchat($email,$subject,$request->id);
            //

            $type_profils_names = ['Gestionnaire des achats','Responsable DMP'];

            $this->notifDemandeAchats($subject,$request->id,$type_profils_names);

                if ($statut_demande_achat != null) {
                    return redirect('/demande_achats/index')->with('success',$subject);
                }else{
                    return redirect()->back()->with('error','Echec de l\'annulation de la cotation');
                }
        }

        
        
    }

    public function storeSessionBackground($request){

        $request->session()->put('backgroundImage','container-infographie3');

    }



}
