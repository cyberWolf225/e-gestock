<?php

namespace App\Http\Controllers;

use App\Models\Famille;
use App\Models\Gestion;
use App\Models\Service;
use App\Models\Structure;
use Illuminate\Http\Request;
use App\Models\DemandeCotation;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class DemandeCotationController extends ControllerDemandeCotation
{
    private $fournisseurDemandeCotationController;
    private $controller1;
    private $controllerDemandeCotation;
    public function __construct(FournisseurDemandeCotationController $fournisseurDemandeCotationController, Controller1 $controller1, ControllerDemandeCotation $controllerDemandeCotation)
    {
        $this->middleware('auth');
        $this->fournisseurDemandeCotationController = $fournisseurDemandeCotationController;
        $this->controller1 = $controller1;
        $this->controllerDemandeCotation = $controllerDemandeCotation;
    }

    public function index(){

        $acces_create = null;

        $viewRessourceName = 'DemandeCotationIndex';
        
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $dataGetTypeStatutDemandeCotationForDisplay = [
            'type_profils_name'=>$responseAccess['type_profils_name']
        ];
        $typeStatutDemandeCotationForDisplay = $this->getTypeStatutDemandeCotationForDisplay($dataGetTypeStatutDemandeCotationForDisplay);

        $dataGetDemandeCotationsByDepot = [
            'typeStatut'=>$typeStatutDemandeCotationForDisplay,
            'ref_depot'=>$responseAccess['ref_depot']
        ];
        $demande_cotations = $this->getDemandeCotationsByDepot($dataGetDemandeCotationsByDepot);

        if($responseAccess['type_profils_name'] === 'Administrateur fonctionnel'){
            $demande_cotations = $this->getDemandeCotations();
        }

        if($responseAccess['type_profils_name'] === 'Fournisseur'){

            $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
            if($organisation != null){
                $demande_cotations = $this->controller1->getDemandeCotationWithFournisseurDemandeCotationsByOrganisationId($organisation->id,$typeStatutDemandeCotationForDisplay);
            }
            
        }

        if($responseAccess['type_profils_name'] === 'Gestionnaire des achats'){
            $acces_create = 1;
        }
        
        return view('demande_cotations.index',[
            'demande_cotations'=>$demande_cotations,
            'type_profils_name'=>$responseAccess['type_profils_name'],
            'acces_create'=>$acces_create,
            'controller1'=>$this->controller1,
            'controllerDemandeCotation'=>$this->controllerDemandeCotation
        ]);
    }

    public function create(){

        $nom_prenoms_commentaire = null;
        $profil_commentaire = null;
        $viewRessourceName = 'DemandeCotationCreate';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $familles = $this->getFamilles();
        $structures = $this->getStructuresByRefDepot($responseAccess['ref_depot']);
        $articles = $this->getArticleActifs();
        $gestions = $this->getGestion();
        $description_articles = $this->getDescriptionArticles();
        $nbre_ligne = 0;
        $credit_budgetaires_credit = null;
        $griser = 1;
        $display_div_save = null;
        $display_div_detail_bcs = null;
        $display_div_detail_bcn = null;
        $exercice = date("Y");
        $getGxercice = $this->getExercice();
        if($getGxercice != null){
            $exercice = $getGxercice->exercice;
        }

        $services = $this->getServices();
        $unites = $this->getUnites();

        return view('demande_cotations.create',[
            'familles'=>$familles,
            'structures'=>$structures,
            'articles'=>$articles,
            'gestions'=>$gestions,
            'description_articles'=>$description_articles,
            'nbre_ligne'=>$nbre_ligne,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit,
            'griser'=>$griser,
            'display_div_save'=>$display_div_save,
            'display_div_detail_bcs'=>$display_div_detail_bcs,
            'display_div_detail_bcn'=>$display_div_detail_bcn,
            'exercice'=>$exercice,
            'services'=>$services,
            'unites'=>$unites,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'profil_commentaire'=>$profil_commentaire
        ]);
    }

    public function crypt($ref_fam,$code_structure,$code_gestion){
        return redirect('demande_cotations/create/'.Crypt::encryptString($ref_fam).'/'.Crypt::encryptString($code_structure).'/'.Crypt::encryptString($code_gestion).'');
    }

    public function create_credit($crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null){

        $viewRessourceName = 'DemandeCotationCreate';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

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

        $famille = Famille::where('ref_fam', $decrypted_ref_fam)->first();
        if($famille === null) {
            return redirect('/demande_cotations/create')->with('error', 'Veuillez saisir un compte valide');
        }

        $structure = Structure::where('code_structure', $decrypted_code_structure)->first();
        if($structure === null) {
            return redirect('/demande_cotations/create')->with('error', 'Veuillez saisir une structure valide');
        }

        $gestion = Gestion::where('code_gestion', $decrypted_code_gestion)->first();
        if($gestion === null) {
            return redirect('/demande_cotations/create')->with('error', 'Veuillez saisir une gestion valide');
        }

        $familles = $this->getFamilles();
        $structures = $this->getStructuresByRefDepot($responseAccess['ref_depot']);
        $articles = $this->getArticleActifs();
        $gestions = $this->getGestion();
        $description_articles = $this->getDescriptionArticles();
        $nbre_ligne = 0;
        $credit_budgetaires_credit = null;
        $griser = null;
        $display_div_save = null;
        $display_div_detail_bcs = null;
        $display_div_detail_bcn = null;
        $exercice = date("Y");
        $getGxercice = $this->getExercice();
        if($getGxercice != null){
            $exercice = $getGxercice->exercice;
        }

        if($famille != null && $structure != null && $exercice != null && $gestion != null){
            try {
                $param = $exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;
                $this->storeCreditBudgetaireByWebService($param,$structure->ref_depot);                 
            } catch (\Throwable $th) {

            }

            $famille_select = $this->getFamilleById($famille->id);
            $structure_select = $this->getStructureByCode($structure->code_structure);
            $gestion_select = $this->getGestionById($gestion->id);
            $nbre_ligne = $this->countArticleByFamilleId($famille->id);
            $articles = $this->getArticleConcerners($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercice);
            $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercice);
            if($disponible != null){
                $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;
            }

        }
        $services = $this->getServices();
        $unites = $this->getUnites();
        

        return view('demande_cotations.create',[
            'familles'=>$familles,
            'structures'=>$structures,
            'articles'=>$articles,
            'gestions'=>$gestions,
            'description_articles'=>$description_articles,
            'nbre_ligne'=>$nbre_ligne,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit,
            'griser'=>$griser,
            'display_div_save'=>$display_div_save,
            'display_div_detail_bcs'=>$display_div_detail_bcs,
            'display_div_detail_bcn'=>$display_div_detail_bcn,
            'exercice'=>$exercice,
            'famille_select'=>$famille_select,
            'structure_select'=>$structure_select,
            'gestion_select'=>$gestion_select,
            'services'=>$services,
            'unites'=>$unites,
            'nom_prenoms_commentaire'=>$responseAccess['nom_prenoms_commentaire'],
            'profil_commentaire'=>$responseAccess['profil_commentaire']
            
        ]);

    }

    public function store(Request $request){

        $type_operations_libelle = null;
        if(isset($request->type_operations_libelle)){
            $type_operations_libelle = $request->type_operations_libelle;
        }

        if($type_operations_libelle === 'BCS'){
            $type_operations_libelle = "Demande d'achats";
        }

        if($type_operations_libelle === 'BCN'){
            $type_operations_libelle = "Commande non stockable";
        }
        $dataValidateRequest = [
            'request'=>$request,
            'fonctionName'=>'store',
            'TypeOperationsLibelle'=>$type_operations_libelle,
        ];

        $this->validateRequest($dataValidateRequest);
        
        $viewRessourceName = 'DemandeCotationStore';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $type_operations_id = null;
        $ref_depot = $responseAccess['ref_depot'];
        $flag_actif = 1;
        $periodes_id = null;
        $delai = null;
        $date_echeance = null;
        $credit_budgetaires_id = null;
        $demande_cotations_id = null;
        
        $exercice = date("Y");
        $getGxercice = $this->getExercice();
        if($getGxercice != null){
            $exercice = $getGxercice->exercice;
        }

        $intitule = $request->intitule;
        $ref_fam = $request->ref_fam;
        $design_fam = $request->design_fam;
        $code_structure = $request->code_structure;
        $nom_structure = $request->nom_structure;
        $code_gestion = $request->code_gestion;
        $libelle_gestion = $request->libelle_gestion;

        if($type_operations_libelle === null){
            return redirect()->back()->with('error','Type operation invalide');
        }
        
        $this->storeTypeOperation($type_operations_libelle);
        $type_operation = $this->getTypeOperation($type_operations_libelle);
        if($type_operation != null){
            $type_operations_id = $type_operation->id;
        }

        $credit_budgetaire = $this->getCreditBudgetaireDisponible($ref_fam,$code_structure,$code_gestion,$exercice);

        if($credit_budgetaire === null){
            return redirect()->back()->with('error','Budget introuvable');
        }
        
        if($credit_budgetaire != null){
            $credit_budgetaires_id = $credit_budgetaire->id;
        }

        $num_dem = $this->getLastNumDemCot($exercice,$code_structure);
        
        $dataStoreDemandeCotation = [
            'num_dem'=>$num_dem,
            'intitule'=>$intitule,
            'type_operations_id'=>$type_operations_id,
            'credit_budgetaires_id'=>$credit_budgetaires_id,
            'ref_depot'=>$ref_depot,
            'periodes_id'=>$periodes_id,
            'delai'=>$delai,
            'date_echeance'=>$date_echeance,
            'flag_actif'=>$flag_actif,
        ];
        
        $demande_cotation = $this->storeDemandeCotation($dataStoreDemandeCotation);

        if($demande_cotation === null){
            return redirect()->back()->with('error','Echec de l\'enregistrement');
        }
        
        if($demande_cotation != null){
            $demande_cotations_id = $demande_cotation->id;
        }

        $detail_demande_cotation = null;
        
        if($type_operations_libelle === "Demande d'achats"){

            if(count($request->ref_articles) > 0){
                foreach ($request->ref_articles as $key => $ref_articles) {

                    $code_unite = null;
                    $description_articles_id = null;

                    $design_article = $request->design_article[$key];

                    $description_articles_libelle = $request->description_articles_libelle[$key];

                    $unites_libelle = $request->unites_libelle_bcs[$key];

                    $qte = filter_var($request->qte_bcs[$key], FILTER_SANITIZE_NUMBER_INT);

                    $qte_demandee = $qte;

                    $qte_accordee = $qte;
                    
                    $flag_valide = 1;

                    if($unites_libelle != null){
                        $this->storeUnite($unites_libelle);
                        $unite = $this->getUnite($unites_libelle);
                        if($unite != null){
                            $code_unite = $unite->code_unite;
                        }
                    }

                    if($description_articles_libelle != null){
                        $this->storeDescriptionArticle($description_articles_libelle);
                        $description_article = $this->getDescriptionArticle($description_articles_libelle);
                        if($description_article != null){
                            $description_articles_id = $description_article->id;
                        }
                    }

                    $dataStoreDetailDemandeCotation = [
                        'demande_cotations_id'=>$demande_cotations_id,
                        'code_unite'=>$code_unite,
                        'qte_demandee'=>$qte_demandee,
                        'qte_accordee'=>$qte_accordee,
                        'flag_valide'=>$flag_valide,
                    ];
                    $detail_demande_cotation = $this->storeDetailDemandeCotation($dataStoreDetailDemandeCotation);

                    if($detail_demande_cotation != null){

                        $dataStoreBcsDetailDemandeCotation = [
                            'detail_demande_cotations_id'=>$detail_demande_cotation->id,
                            'ref_articles'=>$ref_articles,
                            'description_articles_id'=>$description_articles_id,
                        ];

                        $bcs_detail_demande_cotation = $this->storeBcsDetailDemandeCotation($dataStoreBcsDetailDemandeCotation);

                    }

                    if ($detail_demande_cotation != null && isset($request->echantillon_bcs[$key])) {

                        $type_detail_operations_libelle = 'Detail demande de cotation';
                        $echantillon =  $request->echantillon_bcs[$key]->store('echantillonnage','public');

                        $dataSetEchantillon = [
                            'echantillon'=>$echantillon,
                            'detail_operations_id'=>$detail_demande_cotation->id,
                            'type_detail_operations_libelle'=>$type_detail_operations_libelle,
                        ];
                        $this->setEchantillon($dataSetEchantillon);
                    }
                }
            }
        }
        
        if($type_operations_libelle === "Commande non stockable"){
            if(count($request->libelle_service) > 0){
                foreach ($request->libelle_service as $key => $services_libelle){
                    $code_unite = null;
                    $services_id = null;
                    $unites_libelle = $request->unites_libelle_bcn[$key];
                    $qte = filter_var($request->qte_bcn[$key], FILTER_SANITIZE_NUMBER_INT);
                    $qte_demandee = $qte;
                    $qte_accordee = $qte;
                    $flag_valide = 1;

                    if($unites_libelle != null){
                        $this->storeUnite($unites_libelle);
                        $unite = $this->getUnite($unites_libelle);
                        if($unite != null){
                            $code_unite = $unite->code_unite;
                        }
                    }

                    if($services_libelle != null){
                        $this->storeService($services_libelle);
                        $service = $this->getServiceByLibelle($services_libelle);
                        if($service != null){
                            $services_id = $service->id;
                        }
                    }

                    $dataStoreDetailDemandeCotation = [
                        'demande_cotations_id'=>$demande_cotations_id,
                        'code_unite'=>$code_unite,
                        'qte_demandee'=>$qte_demandee,
                        'qte_accordee'=>$qte_accordee,
                        'flag_valide'=>$flag_valide,
                    ];
                    $detail_demande_cotation = $this->storeDetailDemandeCotation($dataStoreDetailDemandeCotation);

                    $bcn_detail_demande_cotation = null;

                    if($detail_demande_cotation != null){

                        $dataStoreBcnDetailDemandeCotation = [
                            'detail_demande_cotations_id'=>$detail_demande_cotation->id,
                            'services_id'=>$services_id,
                        ];

                        $bcn_detail_demande_cotation = $this->storeBcnDetailDemandeCotation($dataStoreBcnDetailDemandeCotation);

                    }

                    if ($detail_demande_cotation != null && isset($request->echantillon_bcn[$key])) {

                        $type_detail_operations_libelle = 'Detail demande de cotation';
                        $echantillon =  $request->echantillon_bcn[$key]->store('echantillonnage','public');

                        $dataSetEchantillon = [
                            'echantillon'=>$echantillon,
                            'detail_operations_id'=>$detail_demande_cotation->id,
                            'type_detail_operations_libelle'=>$type_detail_operations_libelle,
                        ];
                        $this->setEchantillon($dataSetEchantillon);
                    }
                }
            }
        }

        if($detail_demande_cotation != null){

            $type_statut_demande_cotations_libelle = "Soumis pour validation";
            $dataStatut = [
                'type_statut_demande_cotations_libelle'=>$type_statut_demande_cotations_libelle,
                'demande_cotations_id'=>$demande_cotations_id,
                'commentaire'=>$request->commentaire,
            ];
            $this->controller1->procedureStoreTypeStatutDemandeCotation($dataStatut);
            
        }

        if($demande_cotations_id != null && isset($request->piece)){
            if (count($request->piece) > 0) {

                $type_operations_libelle2 = "Demande de cotation";
                $dataProcedurePieceJointes = [
                    'request'=>$request,
                    'type_operations_libelle'=>$type_operations_libelle2,
                    'profils_id'=>Session::get('profils_id'),
                    'subject_id'=>$demande_cotations_id,
                ];
                $this->procedurePieceJointes($dataProcedurePieceJointes);
            }
        }

        if($demande_cotation != null){

            $subject = "Demande de cotation enregistrée";

            $list_profil_a_nofier = ['Gestionnaire des achats'];

            $this->notifDemandeCotations($subject,$demande_cotations_id,$list_profil_a_nofier);

            return redirect('/demande_cotations/index')->with('success', 'Enregistrement effectué');
        }

        if($demande_cotation === null){
            return redirect()->back()->with('error', 'Enregistrement echoué');
        }

    }

    public function show($demandeCotation){

        $disponible = null;
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeCotation);
        } catch (DecryptException $e) {
            //
        }

        $demandeCotation = DemandeCotation::findOrFail($decrypted);

        $demande_cotation = $this->getDemandeCotationById($demandeCotation->id); 
        
        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->getLastStatutDemandeCotation($demandeCotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'DemandeCotationShow';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);
        
        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }
        
        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $familles = $this->getFamilles();
        $structures = $this->getStructuresByRefDepot($responseAccess['ref_depot']);
        $articles = $this->getArticleActifs();
        $gestions = $this->getGestion();
        $description_articles = $this->getDescriptionArticles();
        $nbre_ligne = 0;
        $credit_budgetaires_credit = null;
        $griser = 1;
        $display_div_save = null;
        $display_div_detail_bcs = null;
        $display_div_detail_bcn = null;
        $exercice = date("Y");
        $getGxercice = $this->getExercice();
        if($getGxercice != null){
            $exercice = $getGxercice->exercice;
        }
        
        $detail_demande_cotations = [];

        if($demande_cotation != null){

            if($demande_cotation->libelle === "Demande d'achats"){
                $display_div_detail_bcs = 1;
            }

            if($demande_cotation->libelle === "Commande non stockable"){
                $display_div_detail_bcn = 1;
            }

            $detail_demande_cotations = $this->getDetailDemandeCotationsByDemandeCotationId($demandeCotation->id,$demande_cotation->libelle);
        }

        if($demande_cotation != null){

            $famille_select = $this->getFamilleById($demande_cotation->familles_id);
            $structure_select = $this->getStructureByCode($demande_cotation->code_structure);
            $gestion_select = $this->getGestionById($demande_cotation->gestions_id);
            $nbre_ligne = $this->countArticleByFamilleId($demande_cotation->familles_id);
            $articles = $this->getArticleConcerners($demande_cotation->ref_fam,$demande_cotation->code_structure,$demande_cotation->code_gestion,$demande_cotation->exercice);

            $disponible = $this->getCreditBudgetaireDisponible($demande_cotation->ref_fam, $demande_cotation->code_structure, $demande_cotation->code_gestion, $demande_cotation->exercice);

            if($disponible != null){
                $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;

                $display_div_save = 1;
            }

        }

        $services = $this->getServices();
        $unites = $this->getUnites();

        $type_piece = "Demande de cotation";

        $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
        
        $periode = null;
        if($demande_cotation != null){
            $periode = $this->getPeriodeById($demande_cotation->periodes_id);
        }
        $data = [
            'demande_cotations_id'=>$demande_cotation->id
        ];
        
        $partialSelectFournisseurBlade = null;
        $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotations($data);
        if(count($fournisseur_demande_cotations)>0){ $partialSelectFournisseurBlade = 1; }
        $buttons = [
            'partialSelectFournisseurBlade'=>$partialSelectFournisseurBlade
        ];
        $display_solde_avant_operation = 1;
        $display_pieces_jointes = 1;
        $info_organisation = "FOURNISSEURS DEVANT SOUMETTRE UNE COTATION";

        if($responseAccess['type_profils_name'] === 'Fournisseur'){

            $display_solde_avant_operation = null;
            $display_pieces_jointes = null;
            $fournisseur_demande_cotations = [];
            $info_organisation = "VOUS FIGUREZ PARMI LES FOURNISSEURS DEVANT SOUMETTRE UNE COTATION";

            $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
            if($organisation != null){
                $dataByOrganisationId = [
                    'demande_cotations_id'=>$demande_cotation->id,
                    'organisations_id'=>$organisation->id,
                ];
                $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotationByOrganisationId($dataByOrganisationId);
            }
            
        }
        
        return view('demande_cotations.show',[
            'familles'=>$familles,
            'structures'=>$structures,
            'articles'=>$articles,
            'gestions'=>$gestions,
            'description_articles'=>$description_articles,
            'nbre_ligne'=>$nbre_ligne,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit,
            'griser'=>$griser,
            'display_div_save'=>$display_div_save,
            'display_div_detail_bcs'=>$display_div_detail_bcs,
            'display_div_detail_bcn'=>$display_div_detail_bcn,
            'exercice'=>$exercice,
            'famille_select'=>$famille_select,
            'structure_select'=>$structure_select,
            'gestion_select'=>$gestion_select,
            'services'=>$services,
            'unites'=>$unites,
            'nom_prenoms_commentaire'=>$responseAccess['nom_prenoms_commentaire'],
            'profil_commentaire'=>$responseAccess['profil_commentaire'],
            'statut_demande_cotation'=>$statut_demande_cotation,
            'demande_cotation'=>$demande_cotation,
            'detail_demande_cotations'=>$detail_demande_cotations,
            'piece_jointes'=>$piece_jointes,
            'buttons'=>$buttons,
            'periode'=>$periode,
            'fournisseur_demande_cotations'=>$fournisseur_demande_cotations,
            'type_statut_demande_cotation'=>$type_statut_demande_cotation,
            'display_solde_avant_operation'=>$display_solde_avant_operation,
            'display_pieces_jointes'=>$display_pieces_jointes,
            'info_organisation'=>$info_organisation,
        ]);
    }

    public function edit($demandeCotation, Request $request,$limited=null,$crypted_ref_fam=null ,$crypted_code_structure = null, $crypted_code_gestion = null){

        $famille = null;
        $structure = null;
        $gestion = null;
        $disponible = null;

        if($crypted_ref_fam != null && $crypted_code_structure != null && $crypted_code_gestion != null){

            $famille = Famille::where('ref_fam',$crypted_ref_fam)->first();
            
            if($famille === null){
                return redirect('/demande_cotations/edit/'.$demandeCotation)->with('error', 'Veuillez saisir un compte valide');
            }

            $structure = Structure::where('code_structure',$crypted_code_structure)->first();
            if($structure === null){
                return redirect('/demande_cotations/edit/'.$demandeCotation)->with('error', 'Veuillez saisir une structure valide');
            }

            $gestion = Gestion::where('code_gestion',$crypted_code_gestion)->first();
            if($gestion === null){
                return redirect('/demande_cotations/edit/'.$demandeCotation)->with('error', 'Veuillez saisir une gestion valide');
            }


        }

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeCotation);
        } catch (DecryptException $e) {
            //
        }

        $demandeCotation = DemandeCotation::findOrFail($decrypted);

        $demande_cotation = $this->getDemandeCotationById($demandeCotation->id); 
        
        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->getLastStatutDemandeCotation($demandeCotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        $viewRessourceName = 'DemandeCotationEdit';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'demande_cotations_id'=>$demandeCotation->id
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);
        
        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }
        
        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        /******/
        $familles = $this->getFamilles();
        $structures = $this->getStructuresByRefDepot($responseAccess['ref_depot']);
        $articles = $this->getArticleActifs();
        $gestions = $this->getGestion();
        $description_articles = $this->getDescriptionArticles();
        $nbre_ligne = 0;
        $credit_budgetaires_credit = null;
        $griser = null;
        $display_div_save = null;
        $display_div_detail_bcs = null;
        $display_div_detail_bcn = null;
        $exercice = date("Y");
        $getGxercice = $this->getExercice();
        if($getGxercice != null){
            $exercice = $getGxercice->exercice;
        }
        
        $detail_demande_cotations = [];

        if($demande_cotation != null){

            if($demande_cotation->libelle === "Demande d'achats"){
                $display_div_detail_bcs = 1;
            }

            if($demande_cotation->libelle === "Commande non stockable"){
                $display_div_detail_bcn = 1;
            }

            $detail_demande_cotations = $this->getDetailDemandeCotationsByDemandeCotationId($demandeCotation->id,$demande_cotation->libelle);
        }

        if($famille != null && $structure != null && $exercice != null && $gestion != null){
            try {
                $param = $exercice.'-'.$gestion->code_gestion.'-'.$structure->code_structure.'-'.$famille->ref_fam;
                $this->storeCreditBudgetaireByWebService($param,$structure->ref_depot);                 
            } catch (\Throwable $th) {

            }

            $famille_select = $this->getFamilleById($famille->id);
            $structure_select = $this->getStructureByCode($structure->code_structure);
            $gestion_select = $this->getGestionById($gestion->id);
            $nbre_ligne = $this->countArticleByFamilleId($famille->id);
            $articles = $this->getArticleConcerners($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercice);

            $disponible = $this->getCreditBudgetaireDisponible($famille->ref_fam,$structure->code_structure,$gestion->code_gestion,$exercice);
            if($disponible != null){
                $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;

                $display_div_save = 1;
            }

            if($demande_cotation != null){
                if($demande_cotation->ref_fam != $famille->ref_fam && $demande_cotation->libelle === "Demande d'achats"){
                    $detail_demande_cotations = [];
                }
            }

        }

        if($demande_cotation != null && $crypted_ref_fam === null && $crypted_code_structure === null && $crypted_code_gestion === null){

            $famille_select = $this->getFamilleById($demande_cotation->familles_id);
            $structure_select = $this->getStructureByCode($demande_cotation->code_structure);
            $gestion_select = $this->getGestionById($demande_cotation->gestions_id);
            $nbre_ligne = $this->countArticleByFamilleId($demande_cotation->familles_id);
            $articles = $this->getArticleConcerners($demande_cotation->ref_fam,$demande_cotation->code_structure,$demande_cotation->code_gestion,$demande_cotation->exercice);

            $disponible = $this->getCreditBudgetaireDisponible($demande_cotation->ref_fam, $demande_cotation->code_structure, $demande_cotation->code_gestion, $demande_cotation->exercice);

            if($disponible != null){
                $credit_budgetaires_credit = $disponible->credit - $disponible->consommation_non_interfacee;

                $display_div_save = 1;
            }

        }

        $services = $this->getServices();
        $unites = $this->getUnites();

        $type_piece = "Demande de cotation";
        $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
        $dataSetButton = array_merge($responseAccess, $dataProcedureControllerAccess);
        $buttons = $this->setButton($dataSetButton);
        $organisations = $this->getOrganisationActifs();
        $periodes = $this->getPeriodes();
        $periode = null;
        
        if($demande_cotation != null){
            $periode = $this->getPeriodeById($demande_cotation->periodes_id);
        }
        $data = [
            'demande_cotations_id'=>$demande_cotation->id
        ];
        $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotations($data);
        return view('demande_cotations.edit',[
            'familles'=>$familles,
            'structures'=>$structures,
            'articles'=>$articles,
            'gestions'=>$gestions,
            'description_articles'=>$description_articles,
            'nbre_ligne'=>$nbre_ligne,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit,
            'griser'=>$griser,
            'display_div_save'=>$display_div_save,
            'display_div_detail_bcs'=>$display_div_detail_bcs,
            'display_div_detail_bcn'=>$display_div_detail_bcn,
            'exercice'=>$exercice,
            'famille_select'=>$famille_select,
            'structure_select'=>$structure_select,
            'gestion_select'=>$gestion_select,
            'services'=>$services,
            'unites'=>$unites,
            'nom_prenoms_commentaire'=>$responseAccess['nom_prenoms_commentaire'],
            'profil_commentaire'=>$responseAccess['profil_commentaire'],
            'statut_demande_cotation'=>$statut_demande_cotation,
            'demande_cotation'=>$demande_cotation,
            'detail_demande_cotations'=>$detail_demande_cotations,
            'piece_jointes'=>$piece_jointes,
            'buttons'=>$buttons,
            'organisations'=>$organisations,
            'periodes'=>$periodes,
            'periode'=>$periode,
            'fournisseur_demande_cotations'=>$fournisseur_demande_cotations
        ]);

    }

    public function update(Request $request,DemandeCotation $demandeCotation){
        
        $type_operations_libelle = null;
        if(isset($request->type_operations_libelle)){
            $type_operations_libelle = $request->type_operations_libelle;
        }

        if($type_operations_libelle === 'BCS'){
            $type_operations_libelle = "Demande d'achats";
        }

        if($type_operations_libelle === 'BCN'){
            $type_operations_libelle = "Commande non stockable";
        }
        $dataValidateRequest = [
            'request'=>$request,
            'fonctionName'=>'update',
            'TypeOperationsLibelle'=>$type_operations_libelle,
        ];

        $this->validateRequest($dataValidateRequest);
        
        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->getLastStatutDemandeCotation($request->demande_cotations_id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'DemandeCotationUpdate';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation
        ];
        $responseAccess = $this->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $type_statut_demande_cotations_libelle = $this->getTypeStatutByButtonAction($request->submit);
        
        if($type_statut_demande_cotations_libelle === null){
            return redirect()->back()->with('error','Action non autorisée. Veuillez contacter votre administrateur');
        }
        
        $type_operations_id = null;
        $ref_depot = $responseAccess['ref_depot'];
        $flag_actif = 1;
        $periodes_id = null;
        $delai = null;
        $date_echeance = null;
        $credit_budgetaires_id = null;
        $demande_cotations_id = null;
        $taux_acompte = null;
        if(isset($request->taux_acompte)){
            if($request->taux_acompte >= 0 && $request->taux_acompte <= 100){
                $taux_acompte = $request->taux_acompte;
            }
        }
        
        $exercice = date("Y");
        $getGxercice = $this->getExercice();
        if($getGxercice != null){
            $exercice = $getGxercice->exercice;
        }

        $intitule = $request->intitule;
        $ref_fam = $request->ref_fam;
        $design_fam = $request->design_fam;
        $code_structure = $request->code_structure;
        $nom_structure = $request->nom_structure;
        $code_gestion = $request->code_gestion;
        $libelle_gestion = $request->libelle_gestion;

        if($type_operations_libelle === null){
            return redirect()->back()->with('error','Type operation invalide');
        }
        
        $this->storeTypeOperation($type_operations_libelle);
        $type_operation = $this->getTypeOperation($type_operations_libelle);
        if($type_operation != null){
            $type_operations_id = $type_operation->id;
        }

        $credit_budgetaire = $this->getCreditBudgetaireDisponible($ref_fam,$code_structure,$code_gestion,$exercice);

        if($credit_budgetaire === null){
            return redirect()->back()->with('error','Budget introuvable');
        }
        
        if($credit_budgetaire != null){
            $credit_budgetaires_id = $credit_budgetaire->id;
        }

        $explode_num_dem = explode('/',explode('DC', $request->num_dem)[0])[1];
        if($explode_num_dem === $request->code_structure){
            $num_dem = $request->num_dem;
        }

        if($explode_num_dem != $request->code_structure){
            $num_dem = $this->getLastNumDemCot($exercice,$code_structure,$request->$demande_cotations_id);
        }

        if(isset($request->libelle_periode) && isset($request->valeur) && isset($request->delai) && isset($request->date_echeance)){
            
            $periode = $this->getPeriode($request->libelle_periode);
            if($periode != null){
                $periodes_id = $periode->id;
            }
            $delai = $request->delai;

            $block_date = explode("/",$request->date_echeance);
            $d = $block_date[0];
            $m = $block_date[1];
            $Y = explode(" ",$block_date[2])[0];

            $date_echeance = $Y.'-'.$m.'-'.$d.' 12:00:00';

        }
        
        $dataStoreDemandeCotation = [
            'demande_cotations_id'=>$request->demande_cotations_id,
            'num_dem'=>$num_dem,
            'intitule'=>$intitule,
            'type_operations_id'=>$type_operations_id,
            'credit_budgetaires_id'=>$credit_budgetaires_id,
            'ref_depot'=>$ref_depot,
            'periodes_id'=>$periodes_id,
            'delai'=>$delai,
            'date_echeance'=>$date_echeance,
            'flag_actif'=>$flag_actif,
            'taux_acompte'=>$taux_acompte
        ];

        $demande_cotation = $this->setDemandeCotation($dataStoreDemandeCotation);
        
        if($demande_cotation === null){
            return redirect()->back()->with('error','Echec de l\'enregistrement');
        }
        
        if($demande_cotation != null){

            $demande_cotations_id = $demande_cotation->id;
            $dataSuppression = [
                'request'=>$request,
                'demande_cotations_id'=>$demande_cotations_id,
                'type_operations_libelle'=>$type_operations_libelle,
            ];

            if (isset($request->organisations_id)) {
                if(count($request->organisations_id) > 0){
                    $this->controller1->procedureSuppressionFournisseurDemandeCotation($dataSuppression);
                    $this->fournisseurDemandeCotationController->store($request);
                }
            }
            $this->procedureSuppressionDetailDemandeCotation($dataSuppression);
        }
        $detail_demande_cotation = null;
        $statut_demande_cotation = null;
        
        if($type_operations_libelle === "Demande d'achats"){

            if(count($request->ref_articles) > 0){
                foreach ($request->ref_articles as $key => $ref_articles) {

                    $code_unite = null;
                    $description_articles_id = null;

                    $detail_demande_cotations_id = null;

                    if(isset($request->detail_demande_cotations_id[$key])){
                        $detail_demande_cotations_id = (int) $request->detail_demande_cotations_id[$key];
                    }

                    $design_article = $request->design_article[$key];

                    $description_articles_libelle = $request->description_articles_libelle[$key];

                    $unites_libelle = $request->unites_libelle_bcs[$key];

                    $qte = filter_var($request->qte_bcs[$key], FILTER_SANITIZE_NUMBER_INT);

                    $qte_demandee = $qte;

                    $qte_accordee = $qte;
                    
                    $flag_valide = 1;

                    if($unites_libelle != null){
                        $this->storeUnite($unites_libelle);
                        $unite = $this->getUnite($unites_libelle);
                        if($unite != null){
                            $code_unite = $unite->code_unite;
                        }
                    }

                    if($description_articles_libelle != null){
                        $this->storeDescriptionArticle($description_articles_libelle);
                        $description_article = $this->getDescriptionArticle($description_articles_libelle);
                        if($description_article != null){
                            $description_articles_id = $description_article->id;
                        }
                    }

                    $dataStoreDetailDemandeCotation = [
                        'detail_demande_cotations_id'=>$detail_demande_cotations_id,
                        'demande_cotations_id'=>$demande_cotations_id,
                        'code_unite'=>$code_unite,
                        'qte_demandee'=>$qte_demandee,
                        'qte_accordee'=>$qte_accordee,
                        'flag_valide'=>$flag_valide,
                    ];
                    $detail_demande_cotation = $this->setDetailDemandeCotation($dataStoreDetailDemandeCotation);

                    if($detail_demande_cotation != null){

                        $dataStoreBcsDetailDemandeCotation = [
                            'detail_demande_cotations_id'=>$detail_demande_cotation->id,
                            'ref_articles'=>$ref_articles,
                            'description_articles_id'=>$description_articles_id,
                        ];

                        $bcs_detail_demande_cotation = $this->setBcsDetailDemandeCotation($dataStoreBcsDetailDemandeCotation);

                    }

                    $echantillon = null;
                    if(isset($request->echantillon_bcs[$key])){
                        $echantillon = $request->echantillon_bcs[$key];
                    }

                    $echantillon_flag = null;
                    if(isset($request->echantillon_bcs_flag)){
                        $echantillon_flag = $request->echantillon_bcs_flag;
                    }

                    $dataProcedureSetEchantillon = [
                        'echantillon_flag'=>$echantillon_flag,
                        'detail_demande_cotations_id'=>$detail_demande_cotation->id,
                        'detail_demande_cotation'=>$detail_demande_cotation,
                        'echantillon'=>$echantillon,
                    ];
                    $this->procedureSetEchantillon($dataProcedureSetEchantillon);
                    
                }
            }
        }
        
        if($type_operations_libelle === "Commande non stockable"){
            if(count($request->libelle_service) > 0){
                foreach ($request->libelle_service as $key => $services_libelle){

                    $detail_demande_cotations_id = null;

                    if(isset($request->detail_demande_cotations_id[$key])){
                        $detail_demande_cotations_id = (int) $request->detail_demande_cotations_id[$key];
                    }

                    $code_unite = null;
                    $services_id = null;
                    $unites_libelle = $request->unites_libelle_bcn[$key];
                    $qte = filter_var($request->qte_bcn[$key], FILTER_SANITIZE_NUMBER_INT);
                    $qte_demandee = $qte;
                    $qte_accordee = $qte;
                    $flag_valide = 1;

                    if($unites_libelle != null){
                        $this->storeUnite($unites_libelle);
                        $unite = $this->getUnite($unites_libelle);
                        if($unite != null){
                            $code_unite = $unite->code_unite;
                        }
                    }

                    if($services_libelle != null){
                        $this->storeService($services_libelle);
                        $service = $this->getServiceByLibelle($services_libelle);
                        if($service != null){
                            $services_id = $service->id;
                        }
                    }

                    $dataStoreDetailDemandeCotation = [
                        'detail_demande_cotations_id'=>$detail_demande_cotations_id,
                        'demande_cotations_id'=>$demande_cotations_id,
                        'code_unite'=>$code_unite,
                        'qte_demandee'=>$qte_demandee,
                        'qte_accordee'=>$qte_accordee,
                        'flag_valide'=>$flag_valide,
                    ];

                    
                    $detail_demande_cotation = $this->setDetailDemandeCotation($dataStoreDetailDemandeCotation);

                    $bcn_detail_demande_cotation = null;

                    if($detail_demande_cotation != null){
                        $dataStoreBcnDetailDemandeCotation = [
                            'detail_demande_cotations_id'=>$detail_demande_cotation->id,
                            'services_id'=>$services_id,
                        ];
                        $bcn_detail_demande_cotation = $this->setBcnDetailDemandeCotation($dataStoreBcnDetailDemandeCotation);
                    }
                    $echantillon = null;
                    if(isset($request->echantillon_bcn[$key])){
                        $echantillon = $request->echantillon_bcn[$key];
                    }

                    $echantillon_flag = null;
                    if(isset($request->echantillon_bcn_flag)){
                        $echantillon_flag = $request->echantillon_bcn_flag;
                    }
                    $dataProcedureSetEchantillon = [
                        'echantillon_flag'=>$echantillon_flag,
                        'detail_demande_cotations_id'=>$detail_demande_cotations_id,
                        'detail_demande_cotation'=>$detail_demande_cotation,
                        'echantillon'=>$echantillon,
                    ];
                    $this->procedureSetEchantillon($dataProcedureSetEchantillon);                    
                }
            }
        }

        if($demande_cotations_id != null){
            $type_piece = "Demande de cotation";
                            
            $piece_jointes = $this->getPieceJointes($demande_cotations_id, $type_piece);

            foreach ($piece_jointes as $piece_jointe) {
                
                if (isset($request->piece_jointes_id[$piece_jointe->id])) {

                    $flag_actif = 1;

                    $piece_jointes_id = $piece_jointe->id;

                    if (isset($request->piece_flag_actif[$piece_jointes_id])) {
                        $flag_actif = 1;
                    }else{
                        $flag_actif = 0;
                    }

                    
                    $piece = null;
                    $name = null;

                    $data_piece = [
                        'subject_id'=>$demande_cotations_id,
                        'profils_id'=>Session::get('profils_id'),
                        'type_operations_libelle'=>$type_piece,
                        'piece'=>$piece,
                        'flag_actif'=>$flag_actif,
                        'name'=>$name,
                        'piece_jointes_id'=>$piece_jointes_id,
                    ];
                    $this->storePieceJointe($data_piece);

                }
            }
        }

        if($demande_cotations_id != null && isset($request->piece)){
            if (count($request->piece) > 0) {

                $type_operations_libelle2 = "Demande de cotation";
                $dataProcedurePieceJointes = [
                    'request'=>$request,
                    'type_operations_libelle'=>$type_operations_libelle2,
                    'profils_id'=>Session::get('profils_id'),
                    'subject_id'=>$demande_cotations_id,
                ];
                $this->procedurePieceJointes($dataProcedurePieceJointes);
            }
        }

        if($detail_demande_cotation != null){

            $this->storeTypeStatutDemandeCotation($type_statut_demande_cotations_libelle);

            $type_statut_demande_cotation = $this->getTypeStatutDemandeCotation($type_statut_demande_cotations_libelle);

            if($type_statut_demande_cotation != null){

                $this->setLastStatutDemandeCotation($detail_demande_cotation->demande_cotations_id);
                
                $dataStoreStatutDemandeCotation = [
                    'demande_cotations_id'=>$detail_demande_cotation->demande_cotations_id,
                    'type_statuts_id'=>$type_statut_demande_cotation->id,
                    'profils_id'=>Session::get('profils_id'),
                    'date_debut'=>date('Y-m-d H:i:s'),
                    'date_fin'=>null,
                    'commentaire'=>$request->commentaire
                ];
                $statut_demande_cotation = $this->storeStatutDemandeCotation($dataStoreStatutDemandeCotation);
            }
        }
        
        if($statut_demande_cotation != null){
            
            $statut_operation = 'success';
            $dataGetSubject = [
                'statut'=>$statut_operation,
                'submit'=>$request->submit,
            ];
            $response = $this->getSubject($dataGetSubject);
            if($response['list_profil_a_nofier'] != null){
                $this->notifDemandeCotations($response['subject'],$demande_cotations_id,$response['list_profil_a_nofier']);
            }

            return redirect('/demande_cotations/index')->with($statut_operation, $response['subject']);
        }
        
        if($statut_demande_cotation === null){

            $statut_operation = 'error';
            $dataGetSubject = [
                'statut'=>$statut_operation,
                'submit'=>$request->submit,
            ];
            $response = $this->getSubject($dataGetSubject);

            if($response['list_profil_a_nofier'] != null){
                $this->notifDemandeCotations($response['subject'],$demande_cotations_id,$response['list_profil_a_nofier']);
            }

            return redirect()->back()->with($statut_operation, $response['subject']);
        }

    }
}
