<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MieuxDisantDemandeAchatController extends Controller
{
    private $controller1;
    private $controller2;
    private $controller3; 
    private $controller4; 
    private $controllerDemandeCotation; 
    public function __construct(Controller1 $controller1, Controller2 $controller2, Controller3 $controller3, Controller4 $controller4, ControllerDemandeCotation $controllerDemandeCotation){
        $this->controller1 = $controller1;
        $this->controller2 = $controller2;
        $this->controller3 = $controller3;
        $this->controller4 = $controller4;
        $this->controllerDemandeCotation = $controllerDemandeCotation;
    }
    public function store($mieux_disant){
        
        if($mieux_disant === null){
            return redirect()->back()->with('error','Mieux disant introuvable');
        }
        $dataGenerateBc = [
            'mieux_disant'=>$mieux_disant
        ];
        $response = $this->controller4->procedureGenerateBc($dataGenerateBc);

        $reponse_cotation = $response['reponse_cotation'];
        $organisation = $response['organisation'];
        $demande_cotation = $response['demande_cotation'];
        $detail_demande_cotations = $response['detail_demande_cotations'];
        
        $num_bc = $this->getLastNumBc($demande_cotation->exercice,$demande_cotation->code_structure);

        $dataStoreDemandeAchat = [
            'num_bc'=>$num_bc,
            'ref_fam'=>$demande_cotation->ref_fam,
            'ref_depot'=>$demande_cotation->ref_depot,
            'profils_id'=>Session::get('profils_id'),
            'intitule'=>$demande_cotation->intitule,
            'code_gestion'=>$demande_cotation->code_gestion,
            'exercice'=>$demande_cotation->exercice,
            'credit_budgetaires_id'=>$demande_cotation->credit_budgetaires_id
        ];

        $demande_achat = $this->controller3->procedureStoreDemandeAchat($dataStoreDemandeAchat);

        if($demande_achat != null){
            $dataStoreDetail = [
                'detail_demande_cotations'=>$detail_demande_cotations,
                'demande_achats_id'=>$demande_achat->id
            ];
            $this->controller3->procedureStoreDetailMieuxDisantDemandeAchat($dataStoreDetail);
        }
        
        if($demande_achat != null){
            $detail_demande_achats = $this->controller3->getDetailDemandeAchatsByDemandeAchatId($demande_achat->id);
            $this->controller3->procedureStoreValiderDetailDemandeAchat($detail_demande_achats);
        }
        
        if($demande_achat != null){
            $this->controller3->setOrganitionsArticles($mieux_disant->organisations_id,$demande_cotation->ref_fam);

            $this->controller3->setOrganitionsDepots($mieux_disant->organisations_id,$demande_cotation->ref_depot);
        }

        if($demande_achat != null){
            $this->controller3->procedureSetDemandeAchat($demande_achat,$mieux_disant);
        }
        
        if($demande_achat != null){
            $dataProcedureStoreCommande = [
                'demande_achat'=>$demande_achat,
                'demande_cotation'=>$demande_cotation,
            ];
            $this->controller3->procedureStoreCommande($dataProcedureStoreCommande);
        }
        
        if($demande_achat != null){
            $criteres_libelle = 'Fournisseurs Cibles';
            $critere = $this->controller3->procedureStoreCritere($criteres_libelle);
            $critere_adjudication = $this->controller3->procedureStoreCritereAdjudication($critere,$demande_achat);
            $this->controller3->procedureStorePreselectionSoumissionnaire($mieux_disant,$critere_adjudication);
        }
        
        $cotation_fournisseur = null;
        if($demande_achat != null){
            $dataStoreCotationFournisseur = [
                'demande_achat'=>$demande_achat,
                'demande_cotation'=>$demande_cotation,
                'reponse_cotation'=>$reponse_cotation,
                'mieux_disant'=>$mieux_disant,
                'organisation'=>$organisation
            ];
            $cotation_fournisseur = $this->controller3->procedureStoreCotationFournisseur($dataStoreCotationFournisseur);

            $this->controller3->procedureCheckStoreDetailCotationFournisseur($cotation_fournisseur,$dataStoreCotationFournisseur);

            $this->controller3->procedureCheckStoreSelectionAdjudication($cotation_fournisseur,$dataStoreCotationFournisseur);

        }

        if($demande_achat != null){
            $type_piece = "Demande de cotation";
            $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
            foreach ($piece_jointes as $piece_jointe) {
                $piece =  $piece_jointe->piece;
                $name = $piece_jointe->name;
                $libelle = "Demande d'achats";
                $flag_actif = 1;
                $piece_jointes_id = null;

                $dataPiece = [
                    'subject_id'=>$demande_achat->id,
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

        if($demande_achat != null){
            $type_statut_demande_cotations = ['Soumis pour validation','Transmis (Responsable des achats)','Demande de cotation (Transmis Responsable DMP)','Transmis pour cotation','Coté'];
            $type_statut_demande_cotations2 = ['Fournisseur sélectionné','Transmis (Responsable DMP)'];

            $dataStatut = [
                'type_statut_demande_cotations'=>$type_statut_demande_cotations,
                'type_statut_demande_cotations2'=>$type_statut_demande_cotations2,
                'demande_achat'=>$demande_achat,
                'demande_cotation'=>$demande_cotation,
                'reponse_cotation'=>$reponse_cotation,
                'mieux_disant'=>$mieux_disant,
                'organisation'=>$organisation
            ];
            $this->controller3->procedureCheckStoreStatutDemandeAchat($dataStatut);
            $this->controller4->procedureCheck2StoreStatutDemandeAchat($dataStatut);
        }

        if($demande_achat != null){
            $dataParam = [
                'demande_cotation'=>$demande_cotation,
                'reponse_cotation'=>$reponse_cotation,
                'mieux_disant'=>$mieux_disant,
                'operation'=>$demande_achat,
                'detail_demande_cotations'=>$detail_demande_cotations,
                'cotation_fournisseur'=>$cotation_fournisseur
            ];
            $this->controller3->procedureSetDate($dataParam);
        }

        if($demande_achat != null && $mieux_disant != null){
            $this->controller4->procedureStoreMieuxDisantDemandeAchat($mieux_disant,$demande_achat);
        }

        if($demande_achat != null && $demande_cotation != null){
            $this->controller4->procedureStoreDemandeCotationDemandeAchat($demande_cotation,$demande_achat);
        }

        return $demande_achat;
    }
}
