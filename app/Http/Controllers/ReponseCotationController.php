<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeCotation;
use App\Models\ReponseCotation;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ReponseCotationController extends Controller
{
    private $fournisseurDemandeCotationController;
    private $controller1;
    private $controller2;
    private $controllerDemandeCotation;
    public function __construct(FournisseurDemandeCotationController $fournisseurDemandeCotationController, Controller1 $controller1, Controller2 $controller2, ControllerDemandeCotation $controllerDemandeCotation)
    {
        $this->middleware('auth');
        $this->fournisseurDemandeCotationController = $fournisseurDemandeCotationController;
        $this->controller1 = $controller1;
        $this->controller2 = $controller2;
        $this->controllerDemandeCotation = $controllerDemandeCotation;
    }
    public function index($demandeCotation){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeCotation);
        } catch (DecryptException $e) {
            //
        }

        $demandeCotation = DemandeCotation::findOrFail($decrypted);
        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($demandeCotation->id); 
        $reponse_cotations = $this->controller2->getReponseCotationsByDemandeCotationId($demande_cotation->id);

        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demandeCotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'ReponseCotationIndex';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'demande_cotations_id'=>$demandeCotation->id
        ];
        $responseAccess = $this->controllerDemandeCotation->procedureControllerAccess($dataProcedureControllerAccess);
        
        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }
        
        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }
        
        return view('reponse_cotations.index',[
            'controller2'=>$this->controller2,
            'demande_cotation'=>$demande_cotation,
            'reponse_cotations'=>$reponse_cotations,
            'controllerDemandeCotation'=>$this->controllerDemandeCotation,
            'type_profils_name'=>$responseAccess['type_profils_name']
        ]);
    }
    public function create($demandeCotation){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeCotation);
        } catch (DecryptException $e) {
            //
        }

        $demandeCotation = DemandeCotation::findOrFail($decrypted);
        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($demandeCotation->id); 

        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demandeCotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'ReponseCotationCreate';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'demande_cotations_id'=>$demandeCotation->id
        ];
        $responseAccess = $this->controllerDemandeCotation->procedureControllerAccess($dataProcedureControllerAccess);
        
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

            $detail_demande_cotations = $this->controllerDemandeCotation->getDetailDemandeCotationsByDemandeCotationId($demandeCotation->id,$demande_cotation->libelle);


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

        $services = $this->controllerDemandeCotation->getServices();
        $unites = $this->controllerDemandeCotation->getUnites();

        $type_piece = "Demande de cotation";
        $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
        $dataSetButton = array_merge($responseAccess, $dataProcedureControllerAccess);
        $buttons = $this->controllerDemandeCotation->setButton($dataSetButton);
        $organisations = $this->getOrganisationActifs();
        $periodes = $this->getPeriodes();
        $periode = null;
        
        if($demande_cotation != null){
            $periode = $this->controllerDemandeCotation->getPeriodeById($demande_cotation->periodes_id);
        }
        $data = [
            'demande_cotations_id'=>$demande_cotation->id
        ];
        $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotations($data);
        $griser_demande = 1;
        $griser_offre = 1;
        $display_solde_avant_operation = 1;
        $display_pieces_jointes = 1;
        $organisation = null;
        $vue_fournisseur = null;
        if($responseAccess['type_profils_name'] === 'Fournisseur'){
            
            $dataCheckEcheance = [
                'date_echeance'=>$demande_cotation->date_echeance,
                'type_profils_name'=>$responseAccess['type_profils_name'],
            ];
            $response_echeance = $this->controller1->checkDateEcheance($dataCheckEcheance);
            if($response_echeance === false){
                return redirect()->back()->with('error','Echéance de cotation dépassée');
            }

            $display_solde_avant_operation = null;
            $display_pieces_jointes = null;
            $griser_offre = null;
            $vue_fournisseur = 1;

            $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
            if($organisation != null){
                $dataByOrganisationId = [
                    'demande_cotations_id'=>$demande_cotation->id,
                    'organisations_id'=>$organisation->id,
                ];
                $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotationByOrganisationId($dataByOrganisationId);
            }
            
        }
        $devises_libelle = 'Franc CFA (UEMOA)';
        $devise_default = $this->getDeviseByLibelle($devises_libelle);
        $devises = $this->getDevises();
        $taxes = $this->controller1->getTaxes();
        
        $type_statut_demande_cotations_libelle = "Transmis pour cotation";
        $statut_demande_cotation_demande = $this->controller2->getLastStatutDemandeCotationByLibelle($demande_cotation->id,$type_statut_demande_cotations_libelle);
        

        return view('reponse_cotations.create',[
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
            'fournisseur_demande_cotations'=>$fournisseur_demande_cotations,
            'griser_demande'=>$griser_demande,
            'griser_offre'=>$griser_offre,
            'display_solde_avant_operation'=>$display_solde_avant_operation,
            'display_pieces_jointes'=>$display_pieces_jointes,
            'devise_default'=>$devise_default,
            'devises'=>$devises,
            'organisation'=>$organisation,
            'vue_fournisseur'=>$vue_fournisseur,
            'taxes'=>$taxes,
            'statut_demande_cotation_demande'=>$statut_demande_cotation_demande,
            'type_profils_name'=>$responseAccess['type_profils_name']
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
            'fonctionName'=>'storeReponseCotation',
            'TypeOperationsLibelle'=>$type_operations_libelle,
        ];
        
        $this->controllerDemandeCotation->validateRequest($dataValidateRequest);        

        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($request->demande_cotations_id);
        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $fournisseur_demande_cotations = [];
        $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
        if($organisation != null){
            $dataByOrganisationId = [
                'demande_cotations_id'=>$demande_cotation->id,
                'organisations_id'=>$organisation->id,
            ];
            $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotationByOrganisationId($dataByOrganisationId);
        }

        if(count($fournisseur_demande_cotations) === 0){
            return redirect()->back()->with('error','Vous n\'êtes pas autorisé à soumettre une cotation');
        }

        $fournisseur_demande_cotations_id = null;
        foreach ($fournisseur_demande_cotations as $fournisseur_demande_cotation) {
            $fournisseur_demande_cotations_id = $fournisseur_demande_cotation->fournisseur_demande_cotations_id;
        }

        if($fournisseur_demande_cotations_id === null){
            return redirect()->back()->with('error','Vous n\'êtes pas autorisé à soumettre une cotation');
        }

        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'ReponseCotationStore';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation
        ];
        $responseAccess = $this->controllerDemandeCotation->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $demande_cotations_id = $request->demande_cotations_id;
        $num_dem = $request->num_dem;
        $organisations_id = $request->organisations_id;
        $entnum = $request->entnum;
        $denomination = $request->denomination;
        $libelle_periode = $request->libelle_periode;
        $valeur = $request->valeur;
        $delai = $request->delai;
        $date_echeance = $request->date_echeance;
        $code_devise = $request->code_devise;
        $libelle_devise = $request->libelle_devise;
        $montant_total_brut = $request->montant_total_brut;
        $taux_remise_generale = $request->taux_remise_generale;
        $remise_generale = $request->remise_generale;
        $montant_total_net = $request->montant_total_net;
        $tva = $request->tva;
        $montant_tva = $request->montant_tva;
        $montant_total_ttc = $request->montant_total_ttc;
        $assiete_bnc  = $request->assiette;
        $taux_bnc  = $request->taux_bnc;
        $montant_bnc  = $request->montant_bnc;
        $net_a_payer = $request->net_a_payer;
        
        $commentaire = $request->commentaire;
        $submit = $request->submit;

        $devises_id = $this->getDevise($code_devise,$libelle_devise);
        if($devises_id === null) {
            return redirect()->back()->with('error','Veuillez saisir une devise valide');
        }

        $taux_de_change = $this->thmx_currency_convert($code_devise);
        $acompte = 0;
        $taux_acompte = 0;
        if(isset($request->acompte)){
            $taux_acompte = $demande_cotation->taux_acompte;
            $acompte = 1;
        }
        $dataSet = [
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$tva,
            'montant_tva'=>$montant_tva,
            'montant_total_ttc'=>$montant_total_ttc,
            'net_a_payer'=>$net_a_payer,
            'taux_acompte'=>$taux_acompte,
        ];
        $responseDataSet = $this->controller1->procedureSetDecimal($dataSet);
        
        $dataCheckEcheance = [
            'date_echeance'=>$demande_cotation->date_echeance,
            'type_profils_name'=>$responseAccess['type_profils_name'],
        ];
        $response_echeance = $this->controller1->checkDateEcheance($dataCheckEcheance);
        if($response_echeance === false){
            return redirect()->back()->with('error','Echéance de cotation dépassée');
        }
        
        $datacheckQte = [
            'qte_cde'=>$request->qte_cde,
            'qte'=>$request->qte,
        ];
        $response_qte = $this->controller1->checkQte($datacheckQte);
        if($response_qte === false){
            return redirect()->back()->with('error','La quatité de la cotation ne peut être supérieure à la quantité commandée');
        }
        
        $response_totaux = $this->controller1->procedureCalculTotaux($request,$responseDataSet);

        $dataStoreReponseCotation = [
            'fournisseur_demande_cotations_id'=>$fournisseur_demande_cotations_id,
            'devises_id'=>$devises_id,
            'montant_total_brut'=>$response_totaux['montant_total_brut'],
            'taux_remise_generale'=>$response_totaux['taux_remise_generale'],
            'remise_generale'=>$response_totaux['remise_generale'],
            'montant_total_net'=>$response_totaux['montant_total_net'],
            'tva'=>$response_totaux['tva'],
            'montant_total_ttc'=>$response_totaux['montant_total_ttc'],
            'assiete_bnc'=>$assiete_bnc,
            'taux_bnc'=>$taux_bnc,
            'net_a_payer'=>$response_totaux['net_a_payer'],
            'acompte'=>$acompte,
            'taux_acompte'=>$response_totaux['taux_acompte'],
            'montant_acompte'=>$response_totaux['montant_acompte'],
            'taux_de_change'=>$taux_de_change,
        ];

        $reponse_cotation = $this->controller1->procedureStoreReponseCotation($dataStoreReponseCotation);

        if($reponse_cotation === null){
            return redirect()->back()->with('error','Erreur lors de l\'enregistrement');
        }
        $detail_reponse_cotations = null;
        if($reponse_cotation != null){

            $dataStoreDetail = [
                'request'=>$request,
                'reponse_cotation'=>$reponse_cotation,
            ];

            $this->controller1->procedureStoreDetailReponseCotationCheck($dataStoreDetail);

            $detail_reponse_cotations = $this->controller1->getDetailReponseCotationsByReponseCotationId($reponse_cotation->id);

            if($reponse_cotation != null && isset($request->piece)){
                if (count($request->piece) > 0) {
    
                    $type_operations_libelle2 = "Cotation";
                    $dataProcedurePieceJointes = [
                        'request'=>$request,
                        'type_operations_libelle'=>$type_operations_libelle2,
                        'profils_id'=>Session::get('profils_id'),
                        'subject_id'=>$reponse_cotation->id,
                    ];
                    $this->controllerDemandeCotation->procedurePieceJointes($dataProcedurePieceJointes);
                }
            }

            $type_statut_demande_cotations_libelle = "Coté";
            $dataStatut = [
                'type_statut_demande_cotations_libelle'=>$type_statut_demande_cotations_libelle,
                'demande_cotations_id'=>$demande_cotations_id,
                'commentaire'=>$request->commentaire,
            ];
            $this->controller1->procedureStoreTypeStatutDemandeCotation($dataStatut);


            $statut_operation = 'success';
            $dataGetSubject = [
                'statut'=>$statut_operation,
                'submit'=>$request->submit,
            ];
            $response = $this->controllerDemandeCotation->getSubject($dataGetSubject);
            if($response['list_profil_a_nofier'] != null){
                $this->controllerDemandeCotation->notifDemandeCotations($response['subject'],$demande_cotations_id,$response['list_profil_a_nofier']);
            }

            return redirect('/demande_cotations/index')->with($statut_operation, $response['subject']);

        }
    }
    public function edit($reponseCotation){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($reponseCotation);
        } catch (DecryptException $e) {
            //
        }

        $reponseCotation = ReponseCotation::findOrFail($decrypted);

        $reponse_cotation = $this->controller1->getReponseCotationById($reponseCotation->id);

        if($reponse_cotation === null){
            return redirect()->back()->with('error','Cotation introuvable');
        }
        
        $demande_cotation = $this->controller1->getDemandeCotationByReponseCotationId($reponseCotation->id);

        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,Session::get('profils_id'));
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'ReponseCotationEdit';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'demande_cotations_id'=>$demande_cotation->id
        ];

        $responseAccess = $this->controllerDemandeCotation->procedureControllerAccess($dataProcedureControllerAccess);

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

            $detail_demande_cotations = $this->controllerDemandeCotation->getDetailDemandeCotationsByDemandeCotationId($demande_cotation->id,$demande_cotation->libelle);


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

        $services = $this->controllerDemandeCotation->getServices();
        $unites = $this->controllerDemandeCotation->getUnites();

        $type_piece = "Demande de cotation";
        $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
        $dataSetButton = array_merge($responseAccess, $dataProcedureControllerAccess);
        $buttons = $this->controllerDemandeCotation->setButton($dataSetButton);
        $organisations = $this->getOrganisationActifs();
        $periodes = $this->getPeriodes();
        $periode = null;
        
        if($demande_cotation != null){
            $periode = $this->controllerDemandeCotation->getPeriodeById($demande_cotation->periodes_id);
        }
        $data = [
            'demande_cotations_id'=>$demande_cotation->id
        ];
        $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotations($data);
        $griser_demande = 1;
        $griser_offre = 1;
        $display_solde_avant_operation = 1;
        $display_pieces_jointes = 1;
        $organisation = null;
        $vue_fournisseur = null;
        if($responseAccess['type_profils_name'] === 'Fournisseur'){
            
            $dataCheckEcheance = [
                'date_echeance'=>$demande_cotation->date_echeance,
                'type_profils_name'=>$responseAccess['type_profils_name'],
            ];
            $response_echeance = $this->controller1->checkDateEcheance($dataCheckEcheance);
            if($response_echeance === false){
                return redirect()->back()->with('error','Echéance de cotation dépassée');
            }

            $display_solde_avant_operation = null;
            $display_pieces_jointes = null;
            $griser_offre = null;
            $vue_fournisseur = 1;

            $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
            if($organisation != null){
                $dataByOrganisationId = [
                    'demande_cotations_id'=>$demande_cotation->id,
                    'organisations_id'=>$organisation->id,
                ];
                $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotationByOrganisationId($dataByOrganisationId);
            }
            
        }
        $devises_libelle = 'Franc CFA (UEMOA)';
        $devise_default = $this->getDeviseByLibelle($devises_libelle);
        $devises = $this->getDevises();
        $taxes = $this->controller1->getTaxes();

        return view('reponse_cotations.edit',[
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
            'fournisseur_demande_cotations'=>$fournisseur_demande_cotations,
            'griser_demande'=>$griser_demande,
            'griser_offre'=>$griser_offre,
            'display_solde_avant_operation'=>$display_solde_avant_operation,
            'display_pieces_jointes'=>$display_pieces_jointes,
            'devise_default'=>$devise_default,
            'devises'=>$devises,
            'organisation'=>$organisation,
            'vue_fournisseur'=>$vue_fournisseur,
            'taxes'=>$taxes,
            'reponse_cotation'=>$reponse_cotation,
            'controller1'=>$this->controller1,
            'controller2'=>$this->controller2
        ]);

    }
    public function show($reponseCotation){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($reponseCotation);
        } catch (DecryptException $e) {
            //
        }

        $reponseCotation = ReponseCotation::findOrFail($decrypted);

        $reponse_cotation = $this->controller1->getReponseCotationById($reponseCotation->id);

        if($reponse_cotation === null){
            return redirect()->back()->with('error','Cotation introuvable');
        }
        
        $demande_cotation = $this->controller1->getDemandeCotationByReponseCotationId($reponseCotation->id);

        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $type_statut_demande_cotation = null;

        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,Session::get('profils_id'));
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        
        $viewRessourceName = 'ReponseCotationShow';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'demande_cotations_id'=>$demande_cotation->id
        ];

        $responseAccess = $this->controllerDemandeCotation->procedureControllerAccess($dataProcedureControllerAccess);
        
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

            $detail_demande_cotations = $this->controllerDemandeCotation->getDetailDemandeCotationsByDemandeCotationId($demande_cotation->id,$demande_cotation->libelle);


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

        $services = $this->controllerDemandeCotation->getServices();
        $unites = $this->controllerDemandeCotation->getUnites();

        $type_piece = "Demande de cotation";
        $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
        $dataSetButton = array_merge($responseAccess, $dataProcedureControllerAccess);
        $buttons = $this->controllerDemandeCotation->setButton($dataSetButton);
        $organisations = $this->getOrganisationActifs();
        $periodes = $this->getPeriodes();
        $periode = null;
        
        if($demande_cotation != null){
            $periode = $this->controllerDemandeCotation->getPeriodeById($demande_cotation->periodes_id);
        }
        $data = [
            'demande_cotations_id'=>$demande_cotation->id
        ];
        $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotations($data);
        $griser_demande = 1;
        $griser_offre = 1;
        $display_solde_avant_operation = 1;
        $display_pieces_jointes = 1;
        $organisation = null;
        $vue_fournisseur = null;
        if($responseAccess['type_profils_name'] === 'Fournisseur'){
            
            $dataCheckEcheance = [
                'date_echeance'=>$demande_cotation->date_echeance,
                'type_profils_name'=>$responseAccess['type_profils_name'],
            ];
            $response_echeance = $this->controller1->checkDateEcheance($dataCheckEcheance);
            if($response_echeance === false){
                return redirect()->back()->with('error','Echéance de cotation dépassée');
            }

            $display_solde_avant_operation = null;
            $display_pieces_jointes = null;
            $griser_offre = 1;
            $vue_fournisseur = 1;

            $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
            if($organisation != null){
                $dataByOrganisationId = [
                    'demande_cotations_id'=>$demande_cotation->id,
                    'organisations_id'=>$organisation->id,
                ];
                $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotationByOrganisationId($dataByOrganisationId);
            }
            
        }
        $fournisseur_demande_cotation = null;
        if($responseAccess['type_profils_name'] != 'Fournisseur'){
            $fournisseur_demande_cotation = $this->controller1->getFournisseurDemandeCotationById($reponse_cotation->fournisseur_demande_cotations_id);
        }
        $devises_libelle = 'Franc CFA (UEMOA)';
        $devise_default = $this->getDeviseByLibelle($devises_libelle);
        $devises = $this->getDevises();
        $taxes = $this->controller1->getTaxes();

        return view('reponse_cotations.show',[
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
            'fournisseur_demande_cotations'=>$fournisseur_demande_cotations,
            'griser_demande'=>$griser_demande,
            'griser_offre'=>$griser_offre,
            'display_solde_avant_operation'=>$display_solde_avant_operation,
            'display_pieces_jointes'=>$display_pieces_jointes,
            'devise_default'=>$devise_default,
            'devises'=>$devises,
            'organisation'=>$organisation,
            'vue_fournisseur'=>$vue_fournisseur,
            'taxes'=>$taxes,
            'reponse_cotation'=>$reponse_cotation,
            'controller1'=>$this->controller1,
            'controller2'=>$this->controller2,
            'fournisseur_demande_cotation'=>$fournisseur_demande_cotation
        ]);

    }
    public function update(Request $request){
        
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
            'fonctionName'=>'updateReponseCotation',
            'TypeOperationsLibelle'=>$type_operations_libelle,
        ];
        
        $this->controllerDemandeCotation->validateRequest($dataValidateRequest);        

        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($request->demande_cotations_id);
        
        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $fournisseur_demande_cotations = [];
        $organisation  = $this->controller1->getOrganisationByUserId(auth()->user()->id);
        if($organisation != null){
            $dataByOrganisationId = [
                'demande_cotations_id'=>$demande_cotation->id,
                'organisations_id'=>$organisation->id,
            ];
            $fournisseur_demande_cotations = $this->controller1->getFournisseurDemandeCotationByOrganisationId($dataByOrganisationId);
        }

        if(count($fournisseur_demande_cotations) === 0){
            return redirect()->back()->with('error','Vous n\'êtes pas autorisé à soumettre une cotation');
        }

        $fournisseur_demande_cotations_id = null;
        foreach ($fournisseur_demande_cotations as $fournisseur_demande_cotation) {
            $fournisseur_demande_cotations_id = $fournisseur_demande_cotation->fournisseur_demande_cotations_id;
        }

        if($fournisseur_demande_cotations_id === null){
            return redirect()->back()->with('error','Vous n\'êtes pas autorisé à soumettre une cotation');
        }

        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'ReponseCotationStore';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation
        ];
        $responseAccess = $this->controllerDemandeCotation->procedureControllerAccess($dataProcedureControllerAccess);

        if($responseAccess['profils_id'] === null){
            return redirect()->back()->with('error','Profil introuvable');
        }

        if($responseAccess['autorisation_access'] === false){
            return redirect()->back()->with('error','Accès non autorisé');
        }

        $demande_cotations_id = $request->demande_cotations_id;
        $num_dem = $request->num_dem;
        $organisations_id = $request->organisations_id;
        $entnum = $request->entnum;
        $denomination = $request->denomination;
        $libelle_periode = $request->libelle_periode;
        $valeur = $request->valeur;
        $delai = $request->delai;
        $date_echeance = $request->date_echeance;
        $code_devise = $request->code_devise;
        $libelle_devise = $request->libelle_devise;
        $montant_total_brut = $request->montant_total_brut;
        $taux_remise_generale = $request->taux_remise_generale;
        $remise_generale = $request->remise_generale;
        $montant_total_net = $request->montant_total_net;
        $tva = $request->tva;
        $montant_tva = $request->montant_tva;
        $montant_total_ttc = $request->montant_total_ttc;
        $assiete_bnc  = $request->assiette;
        $taux_bnc  = $request->taux_bnc;
        $montant_bnc  = $request->montant_bnc;
        $net_a_payer = $request->net_a_payer;
        
        $commentaire = $request->commentaire;
        $submit = $request->submit;

        $devises_id = $this->getDevise($code_devise,$libelle_devise);
        if($devises_id === null) {
            return redirect()->back()->with('error','Veuillez saisir une devise valide');
        }

        $taux_de_change = $this->thmx_currency_convert($code_devise);
        $acompte = 0;
        $taux_acompte = 0;
        if(isset($request->acompte)){
            $taux_acompte = $demande_cotation->taux_acompte;
            $acompte = 1;
        }
        $dataSet = [
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$tva,
            'montant_tva'=>$montant_tva,
            'montant_total_ttc'=>$montant_total_ttc,
            'net_a_payer'=>$net_a_payer,
            'taux_acompte'=>$taux_acompte,
        ];
        $responseDataSet = $this->controller1->procedureSetDecimal($dataSet);
        
        $dataCheckEcheance = [
            'date_echeance'=>$demande_cotation->date_echeance,
            'type_profils_name'=>$responseAccess['type_profils_name'],
        ];
        $response_echeance = $this->controller1->checkDateEcheance($dataCheckEcheance);
        if($response_echeance === false){
            return redirect()->back()->with('error','Echéance de cotation dépassée');
        }
        
        $datacheckQte = [
            'qte_cde'=>$request->qte_cde,
            'qte'=>$request->qte,
        ];
        $response_qte = $this->controller1->checkQte($datacheckQte);
        if($response_qte === false){
            return redirect()->back()->with('error','La quatité de la cotation ne peut être supérieure à la quantité commandée');
        }
        
        $response_totaux = $this->controller1->procedureCalculTotaux($request,$responseDataSet);

        $dataStoreReponseCotation = [
            'fournisseur_demande_cotations_id'=>$fournisseur_demande_cotations_id,
            'devises_id'=>$devises_id,
            'montant_total_brut'=>$response_totaux['montant_total_brut'],
            'taux_remise_generale'=>$response_totaux['taux_remise_generale'],
            'remise_generale'=>$response_totaux['remise_generale'],
            'montant_total_net'=>$response_totaux['montant_total_net'],
            'tva'=>$response_totaux['tva'],
            'montant_total_ttc'=>$response_totaux['montant_total_ttc'],
            'assiete_bnc'=>$assiete_bnc,
            'taux_bnc'=>$taux_bnc,
            'net_a_payer'=>$response_totaux['net_a_payer'],
            'acompte'=>$acompte,
            'taux_acompte'=>$response_totaux['taux_acompte'],
            'montant_acompte'=>$response_totaux['montant_acompte'],
            'taux_de_change'=>$taux_de_change,
        ];
        
        $reponse_cotation = $this->controller1->procedureStoreReponseCotation($dataStoreReponseCotation);
        
        if($reponse_cotation === null){
            return redirect()->back()->with('error','Erreur lors de l\'enregistrement');
        }
        $detail_reponse_cotations = null;
        if($reponse_cotation != null){

            $dataStoreDetail = [
                'request'=>$request,
                'reponse_cotation'=>$reponse_cotation,
            ];

            $this->controller1->procedureStoreDetailReponseCotationCheck($dataStoreDetail);
        
            $detail_reponse_cotations = $this->controller1->getDetailReponseCotationsByReponseCotationId($reponse_cotation->id);

            if($reponse_cotation != null && isset($request->piece)){
                if (count($request->piece) > 0) {
    
                    $type_operations_libelle2 = "Cotation";
                    $dataProcedurePieceJointes = [
                        'request'=>$request,
                        'type_operations_libelle'=>$type_operations_libelle2,
                        'profils_id'=>Session::get('profils_id'),
                        'subject_id'=>$reponse_cotation->id,
                    ];
                    $this->controllerDemandeCotation->procedurePieceJointes($dataProcedurePieceJointes);
                }
            }

            $type_statut_demande_cotations_libelle = "Coté";
            $dataStatut = [
                'type_statut_demande_cotations_libelle'=>$type_statut_demande_cotations_libelle,
                'demande_cotations_id'=>$demande_cotations_id,
                'commentaire'=>$request->commentaire,
            ];
            $this->controller1->procedureStoreTypeStatutDemandeCotation($dataStatut);


            $statut_operation = 'success';
            $dataGetSubject = [
                'statut'=>$statut_operation,
                'submit'=>$request->submit,
            ];
            $response = $this->controllerDemandeCotation->getSubject($dataGetSubject);
            if($response['list_profil_a_nofier'] != null){
                $this->controllerDemandeCotation->notifDemandeCotations($response['subject'],$demande_cotations_id,$response['list_profil_a_nofier'],$request->organisations_id);
            }
            $this->controller2->setDemandeCotationWithUpdatedById($demande_cotations_id);
            return redirect('/demande_cotations/index')->with($statut_operation, $response['subject']);

        }
    }
}