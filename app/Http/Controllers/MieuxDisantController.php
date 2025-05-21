<?php

namespace App\Http\Controllers;

use App\Models\MieuxDisant;
use Illuminate\Http\Request;
use App\Models\DemandeCotation;
use App\Models\ReponseCotation;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class MieuxDisantController extends Controller
{
    private $controller1;
    private $controller2;
    private $controller3;
    private $controller4;
    private $controllerDemandeCotation;
    private $mieuxDisantDemandeAchatController;
    private $mieuxDisantTravauxController;
    public function __construct(Controller1 $controller1, Controller2 $controller2, Controller3 $controller3, Controller4 $controller4, ControllerDemandeCotation $controllerDemandeCotation, MieuxDisantDemandeAchatController $mieuxDisantDemandeAchatController, MieuxDisantTravauxController $mieuxDisantTravauxController){
        $this->middleware('auth');
        $this->controller1 = $controller1; 
        $this->controller2 = $controller2; 
        $this->controller3 = $controller3; 
        $this->controller4 = $controller4; 
        $this->controllerDemandeCotation = $controllerDemandeCotation; 
        $this->mieuxDisantDemandeAchatController = $mieuxDisantDemandeAchatController; 
        $this->mieuxDisantTravauxController = $mieuxDisantTravauxController; 
    }
    //demandeCotation
    public function index($demandeCotation){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeCotation);
        } catch (DecryptException $e) {
            //
        }

        $demandeCotation = DemandeCotation::findOrFail($decrypted);

        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($demandeCotation->id);

        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }
        
        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }

        $viewRessourceName = 'MieuxDisantIndex';
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

        $mieux_disants = $this->controller2->getMieuxDisantsByDemandeCotationId($demande_cotation->id);

        return view('mieux_disants.index',[
            'controller2'=>$this->controller2,
            'demande_cotation'=>$demande_cotation,
            'mieux_disants'=>$mieux_disants,
            'controllerDemandeCotation'=>$this->controllerDemandeCotation,
            'type_profils_name'=>$responseAccess['type_profils_name']
        ]);
    }
    public function create($reponseCotation){
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
        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }

        $type_statut_reponse_cotation = null;
        $statut_reponse_cotation = $this->controller2->getLastStatutReponseCotation($reponse_cotation->id);
        if($statut_reponse_cotation != null){
            $type_statut_reponse_cotation = $statut_reponse_cotation->libelle;
        }

        $viewRessourceName = 'MieuxDisantCreate';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'demande_cotations_id'=>$demande_cotation->id,
            'TypeStatutReponseCotationLibelle'=>$type_statut_reponse_cotation,
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

            //$detail_demande_cotations = $this->controllerDemandeCotation->getDetailDemandeCotationsByDemandeCotationId($demande_cotation->id,$demande_cotation->libelle);

            $detail_demande_cotations = $this->controller2->getDetailDemandeCotationsNotInMieuxDisantByDemandeCotationId($demande_cotation->id,$demande_cotation->libelle);

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

        $devises_libelle = 'Franc CFA (UEMOA)';
        $devise_default = $this->getDeviseByLibelle($devises_libelle);
        $devises = $this->getDevises();
        $taxes = $this->controller1->getTaxes();
        $fournisseur_demande_cotation = $this->controller1->getFournisseurDemandeCotationById($reponse_cotation->fournisseur_demande_cotations_id);
        if($fournisseur_demande_cotation != null){
            $organisation = $this->controller1->getOrganisationActifById($fournisseur_demande_cotation->organisations_id);
        }
        $statut_demande_cotation_frs = null;
        if($organisation != null){
            $statut_demande_cotation_frs = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,$organisation->profils_id);
        }
        
        $griser_selection = null;
        return view('mieux_disants.create',[
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
            'controller3'=>$this->controller3,
            'controller4'=>$this->controller4,
            'fournisseur_demande_cotation'=>$fournisseur_demande_cotation,
            'griser_selection'=>$griser_selection,
            'statut_demande_cotation_frs'=>$statut_demande_cotation_frs
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
            'fonctionName'=>'storeMieuxDisant',
            'TypeOperationsLibelle'=>$type_operations_libelle,
        ];
        
        $this->controllerDemandeCotation->validateRequest($dataValidateRequest);

        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($request->demande_cotations_id);
        
        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }


        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        
        $viewRessourceName = 'MieuxDisantStore';
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

        $response_totaux = $this->controller1->procedureCalculTotaux($request,$responseDataSet);

        //montant_acompte
        $dataStoreMieuxDisant = [
            'reponse_cotations_id'=>$request->reponse_cotations_id,
            'montant_total_brut'=>$response_totaux['montant_total_brut'],
            'remise_generale'=>$response_totaux['remise_generale'],
            'montant_total_net'=>$response_totaux['montant_total_net'],
            'montant_total_ttc'=>$response_totaux['montant_total_ttc'],
            'assiete_bnc'=>$assiete_bnc,
            'net_a_payer'=>$response_totaux['net_a_payer'],
            'montant_acompte'=>$response_totaux['montant_acompte'],
        ];

        $data = [
            'request'=>$request,
            'dataStoreMieuxDisant'=>$dataStoreMieuxDisant,
            'demande_cotations_id'=>$demande_cotations_id,
            'controller1'=>$this->controller1,
            'controllerDemandeCotation'=>$this->controllerDemandeCotation,
        ];

        if($request->submit === 'rejeter_cotation'){
            $this->controller2->procedureStoreRejetReponseCotation($data);
        }

        if($request->submit === 'select_mieux_disant' or $request->submit === 'generer_bc'){
            
            $mieux_disant = $this->controller2->procedureStoreSelectionMieuxDisant($data);

        }   
        
        $type_statut_demande_cotations_libelle = $this->controller2->procedureCheckStatutDemandeCotation($demande_cotations_id);

        if($type_statut_demande_cotations_libelle === null){
            return redirect()->back()->with('error','Erreur lors de l\'enregistrement');
        }

        if($type_statut_demande_cotations_libelle != null){

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

            if($mieux_disant != null && $request->submit === 'generer_bc' && $type_operations_libelle === "Demande d'achats"){
                $this->mieuxDisantDemandeAchatController->store($mieux_disant);
            }

            if($mieux_disant != null && $request->submit === 'generer_bc' && $type_operations_libelle === "Commande non stockable"){
                $this->mieuxDisantTravauxController->store($mieux_disant);
            }
            
            return redirect('/reponse_cotations/index/'.Crypt::encryptString($demande_cotations_id))->with($statut_operation, $response['subject']);
            
        }

    }
    public function show($mieuxDisant){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($mieuxDisant);
        } catch (DecryptException $e) {
            //
        }

        $mieuxDisant = MieuxDisant::findOrFail($decrypted);

        $mieux_disant = $this->controller2->getMieuxDisantById($mieuxDisant->id);

        if($mieux_disant === null){
            return redirect()->back()->with('error','Mieux disant introuvable');
        }

        $reponse_cotation = $this->controller1->getReponseCotationById($mieuxDisant->reponse_cotations_id);

        if($reponse_cotation === null){
            return redirect()->back()->with('error','Cotation introuvable');
        }
        
        $demande_cotation = $this->controller1->getDemandeCotationByReponseCotationId($reponse_cotation->id);

        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }

        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }

        $type_statut_reponse_cotation = null;
        $statut_reponse_cotation = $this->controller2->getLastStatutReponseCotation($reponse_cotation->id);
        if($statut_reponse_cotation != null){
            $type_statut_reponse_cotation = $statut_reponse_cotation->libelle;
        }

        $type_statut_mieux_disant = null;
        $statut_mieux_disant = $this->controller2->getLastStatutMieuxDisant($mieux_disant->id);
        if($statut_mieux_disant != null){
            $type_statut_mieux_disant = $statut_mieux_disant->libelle;
        }
        
        $viewRessourceName = 'MieuxDisantEdit';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            //'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_mieux_disant,
            'demande_cotations_id'=>$demande_cotation->id,
            'TypeStatutReponseCotationLibelle'=>$type_statut_reponse_cotation,
            'TypeStatutMieuxDisantLibelle'=>$type_statut_mieux_disant,
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

            $detail_demande_cotations = $this->controller2->getDetailDemandeCotationsByMieuxDisantId($mieux_disant->id,$demande_cotation->libelle);


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

        $devises_libelle = 'Franc CFA (UEMOA)';
        $devise_default = $this->getDeviseByLibelle($devises_libelle);
        $devises = $this->getDevises();
        $taxes = $this->controller1->getTaxes();
        $fournisseur_demande_cotation = $this->controller1->getFournisseurDemandeCotationById($reponse_cotation->fournisseur_demande_cotations_id);
        if($fournisseur_demande_cotation != null){
            $organisation = $this->controller1->getOrganisationActifById($fournisseur_demande_cotation->organisations_id);
        }
        
        $griser_selection = null;
        return view('mieux_disants.show',[
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
            'mieux_disant'=>$mieux_disant,
            'controller1'=>$this->controller1,
            'controller2'=>$this->controller2,
            'fournisseur_demande_cotation'=>$fournisseur_demande_cotation,
            'griser_selection'=>$griser_selection,
            'statut_mieux_disant'=>$statut_mieux_disant
        ]);
    }
    public function edit($mieuxDisant){
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($mieuxDisant);
        } catch (DecryptException $e) {
            //
        }

        $mieuxDisant = MieuxDisant::findOrFail($decrypted);   
          
        $mieux_disant = $this->controller2->getMieuxDisantById($mieuxDisant->id);
        
        if($mieux_disant === null){
            return redirect()->back()->with('error','Mieux disant introuvable');
        }

        $reponse_cotation = $this->controller1->getReponseCotationById($mieuxDisant->reponse_cotations_id);

        if($reponse_cotation === null){
            return redirect()->back()->with('error','Cotation introuvable');
        }
        
        $demande_cotation = $this->controller1->getDemandeCotationByReponseCotationId($reponse_cotation->id);

        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }
        /*
        if($mieux_disant != null && $demande_cotation->libelle === "Demande d'achats"){
            $this->mieuxDisantDemandeAchatController->store($mieux_disant);
        }

        if($mieux_disant != null && $demande_cotation->libelle === "Commande non stockable"){
            $this->mieuxDisantTravauxController->store($mieux_disant);
        }

        dd($mieux_disant,'ici');
        */

        $type_statut_demande_cotation = null;
        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }

        $type_statut_reponse_cotation = null;
        $statut_reponse_cotation = $this->controller2->getLastStatutReponseCotation($reponse_cotation->id);
        if($statut_reponse_cotation != null){
            $type_statut_reponse_cotation = $statut_reponse_cotation->libelle;
        }

        $type_statut_mieux_disant = null;
        $statut_mieux_disant = $this->controller2->getLastStatutMieuxDisant($mieux_disant->id);
        if($statut_mieux_disant != null){
            $type_statut_mieux_disant = $statut_mieux_disant->libelle;
        }
        
        $viewRessourceName = 'MieuxDisantEdit';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            //'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation,
            'TypeStatutDemandeCotationLibelle'=>$type_statut_mieux_disant,
            'demande_cotations_id'=>$demande_cotation->id,
            'TypeStatutReponseCotationLibelle'=>$type_statut_reponse_cotation,
            'TypeStatutMieuxDisantLibelle'=>$type_statut_mieux_disant,
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

            $detail_demande_cotations = $this->controller2->getDetailDemandeCotationsByMieuxDisantId($mieux_disant->id,$demande_cotation->libelle);


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

        $devises_libelle = 'Franc CFA (UEMOA)';
        $devise_default = $this->getDeviseByLibelle($devises_libelle);
        $devises = $this->getDevises();
        $taxes = $this->controller1->getTaxes();
        $fournisseur_demande_cotation = $this->controller1->getFournisseurDemandeCotationById($reponse_cotation->fournisseur_demande_cotations_id);
        if($fournisseur_demande_cotation != null){
            $organisation = $this->controller1->getOrganisationActifById($fournisseur_demande_cotation->organisations_id);
        }
        
        $griser_selection = null;
        return view('mieux_disants.edit',[
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
            'mieux_disant'=>$mieux_disant,
            'controller1'=>$this->controller1,
            'controller2'=>$this->controller2,
            'controller3'=>$this->controller3,
            'controller4'=>$this->controller4,
            'fournisseur_demande_cotation'=>$fournisseur_demande_cotation,
            'griser_selection'=>$griser_selection,
            'statut_mieux_disant'=>$statut_mieux_disant
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
            'fonctionName'=>'updateMieuxDisant',
            'TypeOperationsLibelle'=>$type_operations_libelle,
        ];
        
        $this->controllerDemandeCotation->validateRequest($dataValidateRequest);

        $mieux_disant = $this->controller2->getMieuxDisantById($request->mieux_disants_id);
        
        if($mieux_disant === null){
            return redirect()->back()->with('error','Mieux disant introuvable');
        }

        $demande_cotation = $this->controllerDemandeCotation->getDemandeCotationById($request->demande_cotations_id);
        
        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande de cotation introuvable');
        }


        $statut_demande_cotation = $this->controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
        if($statut_demande_cotation != null){
            $type_statut_demande_cotation = $statut_demande_cotation->libelle;
        }
        $type_statut_mieux_disant = null;
        $statut_mieux_disant = $this->controller2->getLastStatutMieuxDisant($mieux_disant->id);
        if($statut_mieux_disant != null){
            $type_statut_mieux_disant = $statut_mieux_disant->libelle;
        }
        
        $viewRessourceName = 'MieuxDisantUpdate';
        $dataProcedureControllerAccess = [
            'viewRessourceName'=>$viewRessourceName,
            //'TypeStatutDemandeCotationLibelle'=>$type_statut_demande_cotation
            'TypeStatutDemandeCotationLibelle'=>$type_statut_mieux_disant
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

        $response_totaux = $this->controller1->procedureCalculTotaux($request,$responseDataSet);

        //montant_acompte
        $dataStoreMieuxDisant = [
            'reponse_cotations_id'=>$request->reponse_cotations_id,
            'montant_total_brut'=>$response_totaux['montant_total_brut'],
            'remise_generale'=>$response_totaux['remise_generale'],
            'montant_total_net'=>$response_totaux['montant_total_net'],
            'montant_total_ttc'=>$response_totaux['montant_total_ttc'],
            'assiete_bnc'=>$assiete_bnc,
            'net_a_payer'=>$response_totaux['net_a_payer'],
            'montant_acompte'=>$response_totaux['montant_acompte'],
        ];

        $data = [
            'request'=>$request,
            'dataStoreMieuxDisant'=>$dataStoreMieuxDisant,
            'demande_cotations_id'=>$demande_cotations_id,
            'controller1'=>$this->controller1,
            'controllerDemandeCotation'=>$this->controllerDemandeCotation,
        ];
        
        $this->controller3->deleteStatutMieuxDisantById($request->mieux_disants_id);
        $this->controller3->deleteDetailMieuxDisantsById($request->mieux_disants_id);
        $this->controller3->deleteMieuxDisantById($request->mieux_disants_id);
        
        if($request->submit === 'modifier_select_mieux_disant' or $request->submit === 'generer_bc'){
            $mieux_disant = $this->controller2->procedureStoreSelectionMieuxDisant($data);
        }
        
        $type_statut_demande_cotations_libelle = $this->controller2->procedureCheckStatutDemandeCotation($demande_cotations_id);

        if($type_statut_demande_cotations_libelle === null){
            return redirect()->back()->with('error','Erreur lors de l\'enregistrement');
        }

        if($type_statut_demande_cotations_libelle != null){

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

            if($mieux_disant != null && $request->submit === 'generer_bc' && $type_operations_libelle === "Demande d'achats"){
                $demande_achat = $this->mieuxDisantDemandeAchatController->store($mieux_disant);
            }

            if($mieux_disant != null && $request->submit === 'generer_bc' && $type_operations_libelle === "Commande non stockable"){
                $this->mieuxDisantTravauxController->store($mieux_disant);
            }
            
            return redirect('/mieux_disants/index/'.Crypt::encryptString($demande_cotations_id))->with($statut_operation, $response['subject']);
            
        }

    }
}
