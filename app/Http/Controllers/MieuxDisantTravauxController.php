<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MieuxDisantTravauxController extends Controller
{
    private $controller1;
    private $controller2;
    private $controller3; 
    private $controller4; 
    private $controllerTravaux; 
    private $controllerDemandeCotation; 
    public function __construct(Controller1 $controller1, Controller2 $controller2, Controller3 $controller3, Controller4 $controller4, ControllerDemandeCotation $controllerDemandeCotation, ControllerTravaux $controllerTravaux){
        $this->controller1 = $controller1;
        $this->controller2 = $controller2;
        $this->controller3 = $controller3;
        $this->controller4 = $controller4;
        $this->controllerTravaux = $controllerTravaux;
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

        $num_bc = $this->controllerTravaux->getLastNumBcn($demande_cotation->exercice,$demande_cotation->code_structure); 

        $dataStoreTravaux = [
            'num_bc'=>$num_bc,
            'intitule'=>$demande_cotation->intitule,
            'exercice'=>$demande_cotation->exercice,
            'organisations_id'=>$organisation->id,
            'credit_budgetaires_id'=>$demande_cotation->credit_budgetaires_id,
            'devises_id'=>$mieux_disant->devises_id,
            'code_structure'=>$demande_cotation->code_structure,
            'code_gestion'=>$demande_cotation->code_gestion,
            'ref_depot'=>$demande_cotation->ref_depot,
            'montant_total_brut'=>$mieux_disant->montant_total_brut,
            'taux_remise_generale'=>$mieux_disant->taux_remise_generale,
            'remise_generale'=>$mieux_disant->remise_generale,
            'montant_total_net'=>$mieux_disant->montant_total_net,
            'tva'=>$mieux_disant->tva,
            'montant_total_ttc'=>$mieux_disant->montant_total_ttc,
            'net_a_payer'=>$mieux_disant->net_a_payer,
            'acompte'=>$mieux_disant->acompte,
            'taux_acompte'=>$mieux_disant->taux_acompte,
            'montant_acompte'=>$mieux_disant->montant_acompte,
            'delai'=>$demande_cotation->delai,
            'periodes_id'=>$demande_cotation->periodes_id,
            'date_echeance'=>$demande_cotation->date_echeance,
            'ref_fam'=>$demande_cotation->ref_fam,
            'date_livraison_prevue'=>null,
            'date_retrait'=>null,
            'taux_de_change'=>$mieux_disant->taux_de_change,
        ];
        
        $this->controller4->procedureStoreTravaux($dataStoreTravaux);
        $travauxe = $this->controllerTravaux->getTravauxByNumBc($num_bc);
        if($travauxe != null){
            $dataStoreDetail = [
                'detail_demande_cotations'=>$detail_demande_cotations,
                'travauxes_id'=>$travauxe->id
            ];
            $this->controller4->procedureStoreDetailMieuxDisantTravaux($dataStoreDetail);
        }

        if($travauxe != null){
            $type_piece = "Demande de cotation";
            $piece_jointes = $this->getPieceJointes($demande_cotation->id, $type_piece);
            foreach ($piece_jointes as $piece_jointe) {
                $piece =  $piece_jointe->piece;
                $name = $piece_jointe->name;
                $libelle = "Commande non stockable";
                $flag_actif = 1;
                $piece_jointes_id = null;

                $dataPiece = [
                    'subject_id'=>$travauxe->id,
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

        if($travauxe != null){
            $type_statut_demande_cotations = ['Soumis pour validation','Transmis (Responsable des achats)','Demande de cotation (Transmis Responsable DMP)','Transmis pour cotation','Coté'];
            $type_statut_demande_cotations2 = ['Fournisseur sélectionné','Transmis (Responsable DMP)'];

            $dataStatut = [
                'type_statut_demande_cotations'=>$type_statut_demande_cotations,
                'type_statut_demande_cotations2'=>$type_statut_demande_cotations2,
                'travauxe'=>$travauxe,
                'demande_cotation'=>$demande_cotation,
                'reponse_cotation'=>$reponse_cotation,
                'mieux_disant'=>$mieux_disant,
                'organisation'=>$organisation
            ];
            $this->controller4->procedureCheckStoreStatutTravaux($dataStatut);
            $this->controller4->procedureCheck2StoreStatutTravaux($dataStatut);
        }
        
        if($travauxe != null){
            $dataParam = [
                'demande_cotation'=>$demande_cotation,
                'reponse_cotation'=>$reponse_cotation,
                'mieux_disant'=>$mieux_disant,
                'operation'=>$travauxe,
                'detail_demande_cotations'=>$detail_demande_cotations,
                'cotation_fournisseur'=>null
            ];
            $this->controller3->procedureSetDate($dataParam);
        }

        if($travauxe != null && $mieux_disant != null){
            $this->controller4->procedureStoreMieuxDisantTravaux($mieux_disant,$travauxe);
        }

        if($travauxe != null && $demande_cotation != null){
            $this->controller4->procedureStoreDemandeCotationTravaux($demande_cotation,$travauxe);
        }
    }
}
