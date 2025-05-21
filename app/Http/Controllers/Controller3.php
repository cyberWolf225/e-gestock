<?php

namespace App\Http\Controllers;

use App\Models\Critere;
use App\Models\Travaux;
use App\Models\Commande;
use App\Models\TypeAchat;
use App\Models\MieuxDisant;
use App\Models\PieceJointe;
use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use App\Models\DetailTravaux;
use App\Models\TypeOperation;
use App\Models\DetailCotation;
use App\Models\DetailMieuxDisant;
use App\Models\StatutMieuxDisant;
use App\Models\DetailDemandeAchat;
use App\Models\StatutDemandeAchat;
use Illuminate\Support\Facades\DB;
use App\Models\CotationFournisseur;
use App\Models\CritereAdjudication;
use App\Models\ValiderDemandeAchat;
use App\Models\SelectionAdjudication;
use Illuminate\Support\Facades\Session;
use App\Models\PreselectionSoumissionnaire;

class Controller3 extends Controller
{
    public function text(){
        return 'ok';
    }
    public function procedureSetReponseCotationWhenCreateMieuxDisantFormat($reponse_cotation){
        
        $detail_reponse_cotations = $this->getDetailReponseCotationsNotMieuxDisantByReponseCotationId($reponse_cotation->id);
        $montant_total_brut = 0;
        foreach ($detail_reponse_cotations as $detail_reponse_cotation) {
            $montant_total_brut = $montant_total_brut + $detail_reponse_cotation->montant_ttc;
        }
        
        $montant_total_brut_partie_entiere = null;
        $montant_total_brut_partie_decimale = null;

        $taux_remise_generale_partie_entiere = null;
        $taux_remise_generale_partie_decimale = null;
        
        $remise_generale_partie_entiere = null;
        $remise_generale_partie_decimale = null;

        $montant_total_net_partie_entiere = null;
        $montant_total_net_partie_decimale = null;

        $montant_total_ttc_partie_entiere = null;
        $montant_total_ttc_partie_decimale = null;

        $net_a_payer_partie_entiere = null;
        $net_a_payer_partie_decimale = null;

        $montant_acompte_partie_entiere = null;
        $montant_acompte_partie_decimale = null;

        $montant_tva_partie_entiere = null;
        $montant_tva_partie_decimale = null;

        $montant_total_brut = number_format((float)$montant_total_brut, 2, '.', '');

        $block_montant_total_brut = explode(".",$montant_total_brut);
        
        if (isset($block_montant_total_brut[0])) {
            $montant_total_brut_partie_entiere = (int) $block_montant_total_brut[0];
        }

        if (isset($block_montant_total_brut[1])) {
            $montant_total_brut_partie_decimale = (int) $block_montant_total_brut[1];
        }
        
        //$taux_remise_generale = number_format((float)$reponse_cotation->taux_remise_generale, 2, '.', '');
        $taux_remise_generale = $reponse_cotation->taux_remise_generale;

        $block_taux_remise_generale = explode(".",$taux_remise_generale);
        
        if (isset($block_taux_remise_generale[0])) {
            $taux_remise_generale_partie_entiere = (int) $block_taux_remise_generale[0];
        }
        
        if (isset($block_taux_remise_generale[1])) {
            $taux_remise_generale_partie_decimale = (int) $block_taux_remise_generale[1];
        }        
        //$remise_generale = ( $montant_total_brut * $taux_remise_generale ) / 100;
        $remise_generale = ( $montant_total_brut * $reponse_cotation->taux_remise_generale ) / 100;

        
        $remise_generale = number_format((float)$remise_generale, 2, '.', '');

        $block_remise_generale = explode(".",$remise_generale);
        
        if (isset($block_remise_generale[0])) {
            $remise_generale_partie_entiere = (int) $block_remise_generale[0];
        }

        if (isset($block_remise_generale[1])) {
            $remise_generale_partie_decimale = (int) $block_remise_generale[1];
        }

        $montant_total_net = $montant_total_brut - $remise_generale;

        $montant_total_net = number_format((float)$montant_total_net, 2, '.', '');

        $block_montant_total_net = explode(".",$montant_total_net);
        
        if (isset($block_montant_total_net[0])) {
            $montant_total_net_partie_entiere = (int) $block_montant_total_net[0];
        }

        if (isset($block_montant_total_net[1])) {
            $montant_total_net_partie_decimale = (int) $block_montant_total_net[1];
        }

        $montant_total_ttc = $montant_total_net;
        $montant_tva = null;
        
        if(isset($reponse_cotation->tva) && isset($montant_total_net)){
            $dividende_montant_tva = $reponse_cotation->tva * $montant_total_net;

            if($dividende_montant_tva != 0){
                $montant_tva = $dividende_montant_tva / 100;
            }
        }
        if($montant_tva != null){
            $montant_tva = number_format((float)$montant_tva, 2, '.', '');

            $block_montant_tva = explode(".",$montant_tva);           
            
            if (isset($block_montant_tva[0])) {
                $montant_tva_partie_entiere = (int) $block_montant_tva[0];
            }
            
            if (isset($block_montant_tva[1])) {
                $montant_tva_partie_decimale = (int) $block_montant_tva[1];
            }
            $montant_total_ttc = $montant_total_net + $montant_tva;
        }

        $montant_total_ttc = number_format((float)$montant_total_ttc, 2, '.', '');

        $block_montant_total_ttc = explode(".",$montant_total_ttc);
        
        if (isset($block_montant_total_ttc[0])) {
            $montant_total_ttc_partie_entiere = (int) $block_montant_total_ttc[0];
        }

        if (isset($block_montant_total_ttc[1])) {
            $montant_total_ttc_partie_decimale = (int) $block_montant_total_ttc[1];
        }

        $net_a_payer = number_format((float)$montant_total_ttc, 2, '.', '');

        $block_net_a_payer = explode(".",$net_a_payer);
        
        if (isset($block_net_a_payer[0])) {
            $net_a_payer_partie_entiere = (int) $block_net_a_payer[0];
        }
        
        if (isset($block_net_a_payer[1])) {
            $net_a_payer_partie_decimale = (int) $block_net_a_payer[1];
        }

        $montant_acompte = ($reponse_cotation->taux_acompte * $net_a_payer) / 100;

        $montant_acompte = number_format((float)$montant_acompte, 2, '.', '');

        $block_montant_acompte = explode(".",$montant_acompte);
        
        if (isset($block_montant_acompte[0])) {
            $montant_acompte_partie_entiere = (int) $block_montant_acompte[0];
        }

        if (isset($block_montant_acompte[1])) {
            $montant_acompte_partie_decimale = (int) $block_montant_acompte[1];
        }

        if($montant_total_brut_partie_decimale === 0){ 
            $montant_total_brut_partie_decimale = null; 
        }
        if($taux_remise_generale_partie_decimale === 0){

            $taux_remise_generale_partie_decimale = null; 

            if($taux_remise_generale_partie_entiere === 0){ 
                $taux_remise_generale_partie_entiere = null; 
            }
        }
        if($remise_generale_partie_decimale === 0){ 
            $remise_generale_partie_decimale = null; 

            if($remise_generale_partie_entiere === 0){ 
                $remise_generale_partie_entiere = null; 
            }

        }
        if($montant_total_net_partie_decimale === 0){ 
            $montant_total_net_partie_decimale = null;
        }


        if($montant_total_ttc_partie_decimale === 0){ 
            $montant_total_ttc_partie_decimale = null;
        }

        if($net_a_payer_partie_decimale === 0){ 
            $net_a_payer_partie_decimale = null;
        }

        if($montant_acompte_partie_decimale === 0){ 
            $montant_acompte_partie_decimale = null;

            if($montant_acompte_partie_entiere === 0){ 
                $montant_acompte_partie_entiere = null;
            }

        }

        if($montant_tva_partie_decimale === 0){

            $montant_tva_partie_decimale = null; 

            if($montant_tva_partie_entiere === 0){ 
                $montant_tva_partie_entiere = null; 
            }
        }

        $response = [
            'montant_total_brut_partie_entiere'=>$montant_total_brut_partie_entiere,
            'montant_total_brut_partie_decimale'=>$montant_total_brut_partie_decimale,
            'taux_remise_generale_partie_entiere'=>$taux_remise_generale_partie_entiere,
            'taux_remise_generale_partie_decimale'=>$taux_remise_generale_partie_decimale,
            'remise_generale_partie_entiere'=>$remise_generale_partie_entiere,
            'remise_generale_partie_decimale'=>$remise_generale_partie_decimale,
            'montant_total_net_partie_entiere'=>$montant_total_net_partie_entiere,
            'montant_total_net_partie_decimale'=>$montant_total_net_partie_decimale,
            'montant_total_ttc_partie_entiere'=>$montant_total_ttc_partie_entiere,
            'montant_total_ttc_partie_decimale'=>$montant_total_ttc_partie_decimale,
            'net_a_payer_partie_entiere'=>$net_a_payer_partie_entiere,
            'net_a_payer_partie_decimale'=>$net_a_payer_partie_decimale,
            'montant_acompte_partie_entiere'=>$montant_acompte_partie_entiere,
            'montant_acompte_partie_decimale'=>$montant_acompte_partie_decimale,
            'montant_tva_partie_entiere'=>$montant_tva_partie_entiere,
            'montant_tva_partie_decimale'=>$montant_tva_partie_decimale,
        ];
        
        return $response;
    }
    public function getDetailReponseCotationsNotMieuxDisantByReponseCotationId($reponse_cotations_id){
        return DB::table('detail_reponse_cotations as drc')
        ->where('drc.reponse_cotations_id',$reponse_cotations_id)
        ->whereNotIn('drc.detail_demande_cotations_id', function($query){
            $query->select(DB::raw('drc2.detail_demande_cotations_id'))
                ->from('detail_reponse_cotations as drc2')
                ->join('detail_mieux_disants as dmd','dmd.detail_reponse_cotations_id','=','drc2.id')
                ->whereRaw('drc.detail_demande_cotations_id = drc2.detail_demande_cotations_id');
        })
        ->get();
    }
    public function deleteDetailMieuxDisantsById($mieux_disants_id){
        DetailMieuxDisant::where('mieux_disants_id',$mieux_disants_id)->delete();
    }
    public function deleteMieuxDisantById($mieux_disants_id){
        MieuxDisant::where('id',$mieux_disants_id)->delete();
    }

    public function deleteStatutMieuxDisantById($mieux_disants_id){
        StatutMieuxDisant::where('mieux_disants_id',$mieux_disants_id)->delete();
    }

    public function procedureStoreDemandeAchat($data){

        $demande_achat = $this->getDemandeAchatByNumBc($data['num_bc']);
        if($demande_achat === null){
            $demande_achat = $this->storeDemandeAchat($data);
        }
        return $demande_achat;
    }
    public function storeDemandeAchat($data){
        return DemandeAchat::create($data);
    }

    public function getDemandeAchatByNumBc($num_bc){
        return DemandeAchat::where('num_bc',$num_bc)->first();
    }

    public function procedureStoreDetailDemandeAchat($data){

        $detail_demande_achat = $this->getDetailDemandeAchatByDemandeAchatIdAndArticleRef($data['demande_achats_id'],$data['ref_articles']);
        
        if ($detail_demande_achat === null) {

            if($data['chargement_bon_de_commande'] != null){
                $dataStore = [
                    'demande_achats_id' => $data['demande_achats_id'],
                    'ref_articles' => $data['ref_articles'],
                    'qte_demandee' => $data['qte_demandee'],
                    'qte_accordee' => $data['qte_accordee'],
                    'flag_valide' => 1,
                    'profils_id'=>$data['profils_id'],
                    'description_articles_id'=>$data['description_articles_id'],
                    'echantillon'=>$data['echantillon']
                ];
            }

            if($data['chargement_bon_de_commande'] === null){
                $dataStore = [
                    'demande_achats_id' => $data['demande_achats_id'],
                    'ref_articles' => $data['ref_articles'],
                    'qte_demandee' => $data['qte_demandee'],
                    'profils_id'=>$data['profils_id'],
                    'description_articles_id'=>$data['description_articles_id'],
                    'echantillon'=>$data['echantillon']
                ];
            }
            
            $detail_demande_achat = $this->storeDetailDemandeAchat($dataStore);
        }

        return $detail_demande_achat;
    }

    public function storeDetailDemandeAchat($data){
        return DetailDemandeAchat::create($data);
    }

    public function getDetailDemandeAchatByDemandeAchatIdAndArticleRef($demande_achats_id,$ref_articles){
        return DetailDemandeAchat::where('demande_achats_id',$demande_achats_id)
        ->where('ref_articles',$ref_articles)
        ->first();
    }

    public function procedureStoreDetailMieuxDisantDemandeAchat($data){
        
        $detail_demande_cotations = $data['detail_demande_cotations'];
        $detail_demande_achat = null;

        foreach ($detail_demande_cotations as $detail_demande_cotation) {

            $dataStore = [
                'demande_achats_id' => $data['demande_achats_id'],
                'ref_articles' => $detail_demande_cotation->ref_articles,
                'qte_demandee' => $detail_demande_cotation->qte_demandee,
                'qte_accordee' => $detail_demande_cotation->qte_accordee,
                'flag_valide' => 1,
                'profils_id'=>Session::get('profils_id'),
                'description_articles_id'=>$detail_demande_cotation->description_articles_id,
                'echantillon'=>$detail_demande_cotation->echantillon,
                'chargement_bon_de_commande'=>1
            ];
            $detail_demande_achat = $this->procedureStoreDetailDemandeAchat($dataStore);  
        }
        return $detail_demande_achat;
    }
    public function procedureStorePieceJointe($data){
        
        $type_operations_id = null;
        $this->storeTypeOperation($data['libelle']);
        $type_operation = $this->getTypeOperation($data['libelle']);
        if ($type_operation!=null) {
            $type_operations_id = $type_operation->id;
        }

        if ($data['piece_jointes_id'] != null) {
            $this->setPieceJointe($data);
        }
        
        if ($data['piece_jointes_id'] === null && $type_operations_id != null){
            $dataStore = [
                'type_operations_id'=>$type_operations_id,
                'profils_id'=>$data['profils_id'],
                'subject_id'=>$data['subject_id'],
                'name'=>$data['name'],
                'piece'=>$data['piece'],
                'flag_actif'=>$data['flag_actif'],
            ];
            $this->storePieceJointe($dataStore);
        }
        
    }

    public function storePieceJointe($data){
        PieceJointe::create([
            'type_operations_id'=>$data['type_operations_id'],
            'profils_id'=>$data['profils_id'],
            'subject_id'=>$data['subject_id'],
            'name'=>$data['name'],
            'piece'=>$data['piece'],
            'flag_actif'=>$data['flag_actif'],

        ]);
    }

    public function setPieceJointe($data){
        PieceJointe::where('id',$data['piece_jointes_id'])
        ->update([
            'profils_id'=>$data['profils_id'],
            'flag_actif'=>$data['flag_actif'],
        ]);
    }
    public function procedureStoreStatutDemandeAchat($data,$statut_demande_cotation){
        $this->storeTypeStatutDemandeAchat($data['libelle']);
        $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($data['libelle']);

        if ($type_statut_demande_achat != null) {
            $type_statut_demande_achats_id = $type_statut_demande_achat->id;

            //$this->setLastStatutDemandeAchat($data['demande_achats_id']);

            $dataStatutDemandeAchat = [
                'demande_achats_id'=>$data['demande_achats_id'],
                'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                'date_debut'=>date('Y-m-d'),
                'date_fin'=>date('Y-m-d'),
                'commentaire'=>trim($data['commentaire']),
                'profils_id'=>Session::get('profils_id'),
            ];

            $statut_demande_achat = $this->storeStatutDemandeAchat($dataStatutDemandeAchat);
            
            $this->procedureSetStatutDemandeAchat($statut_demande_cotation,$statut_demande_achat);
        }
    }

    public function procedureCheckStoreStatutDemandeAchat($data){
        $controllerDemandeCotation = new ControllerDemandeCotation();
        $type_statut_demande_cotations = $data['type_statut_demande_cotations'];
        $demande_achat = $data['demande_achat'];
        $demande_cotation = $data['demande_cotation'];
        $organisation = $data['organisation'];
        foreach ($type_statut_demande_cotations as $key => $type_statut_demande_cotations_libelle) {
            
            if($type_statut_demande_cotations_libelle != 'Coté'){
                $statut_demande_cotation = $controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,null,$type_statut_demande_cotations_libelle);
            }
            if($type_statut_demande_cotations_libelle === 'Coté'){
                $statut_demande_cotation = $controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,$organisation->profils_id,$type_statut_demande_cotations_libelle);
            }
            $this->procedureCheckStoreStatutDemandeAchat2($statut_demande_cotation,$demande_achat);
            
        }
    }

    public function procedureCheckStoreStatutDemandeAchat2($statut_demande_cotation,$demande_achat){
        if($statut_demande_cotation != null){
            $dataStatut = [
                'libelle'=>$statut_demande_cotation->libelle,
                'demande_achats_id'=>$demande_achat->id,
                'commentaire'=>$statut_demande_cotation->commentaire
            ];
            $this->procedureStoreStatutDemandeAchat($dataStatut,$statut_demande_cotation);
        }
    }

    public function storeStatutDemandeAchat($data){
        return StatutDemandeAchat::create($data);
    }

    public function procedureSetStatutDemandeAchat($statut_demande_cotation,$statut_demande_achat){
        $dataSet = [
            'date_debut'=>$statut_demande_cotation->date_debut,
            'date_fin'=>$statut_demande_cotation->date_fin,
            'profils_id'=>$statut_demande_cotation->profils_id,
            'created_at'=>$statut_demande_cotation->created_at,
            'updated_at'=>$statut_demande_cotation->updated_at,
        ];
        $this->setStatutDemandeAchat($dataSet,$statut_demande_achat);
    }

    public function setStatutDemandeAchat($data,$statut_demande_achat){
        return StatutDemandeAchat::where('id',$statut_demande_achat->id)
        ->update($data);
    }
    public function setDate($data,$dataSet){
        if($data['type_operations_libelle'] === "Demande d'achats"){
            DemandeAchat::where('id',$data['operations_id'])
            ->update($dataSet);

            DetailDemandeAchat::where('demande_achats_id',$data['operations_id'])
            ->update($dataSet);

            ValiderDemandeAchat::join('detail_demande_achats as dda','dda.id','=','valider_demande_achats.detail_demande_achats_id')
            ->where('dda.demande_achats_id',$data['operations_id'])
            ->update([
                'valider_demande_achats.created_at'=>$dataSet['created_at'],
                'valider_demande_achats.updated_at'=>$dataSet['updated_at'],
            ]);
        }
        
        if($data['type_operations_libelle'] === "Cotation fournisseur"){
            CotationFournisseur::where('id',$data['cotation_fournisseurs_id'])
            ->update($dataSet);

            DetailCotation::where('cotation_fournisseurs_id',$data['cotation_fournisseurs_id'])
            ->update($dataSet);

            SelectionAdjudication::where('cotation_fournisseurs_id',$data['cotation_fournisseurs_id'])
            ->update($dataSet);
        }

        if($data['type_operations_libelle'] === "Sélection adjudication"){
            SelectionAdjudication::where('cotation_fournisseurs_id',$data['cotation_fournisseurs_id'])
            ->update($dataSet);
        }

        if($data['type_operations_libelle'] === "Commande non stockable"){
            Travaux::where('id',$data['operations_id'])
            ->update($dataSet);

            DetailTravaux::where('travauxes_id',$data['operations_id'])
            ->update($dataSet);
        }
    }
    public function procedureSetDate($data){

        $demande_cotation = $data['demande_cotation'];
        $reponse_cotation = $data['reponse_cotation'];
        $mieux_disant = $data['mieux_disant'];
        $operation = $data['operation'];
        $cotation_fournisseur = $data['cotation_fournisseur'];

        if($demande_cotation != null){
            $dataParam = [
                'type_operations_libelle'=>$demande_cotation->libelle,
                'operations_id'=>$operation->id,
            ];
            $created_at = $demande_cotation->created_at;
            $updated_at = $demande_cotation->updated_at;

            if($demande_cotation->libelle === "Commande non stockable"){
                $created_at = $mieux_disant->created_at;
                $updated_at = $mieux_disant->updated_at;
            }

            $dataSetDate = [
                'created_at'=>$created_at,
                'updated_at'=>$updated_at,
            ];
            $this->setDate($dataParam,$dataSetDate);
        }

        if($reponse_cotation != null && $cotation_fournisseur != null){
            $dataParam = [
                'type_operations_libelle'=>'Cotation fournisseur',
                'operations_id'=>$operation->id,
                'cotation_fournisseurs_id'=>$cotation_fournisseur->id
            ];

            $dataSetDate = [
                'created_at'=>$reponse_cotation->created_at,
                'updated_at'=>$reponse_cotation->updated_at,
            ];
            $this->setDate($dataParam,$dataSetDate);
        }

        if($mieux_disant != null && $cotation_fournisseur != null){
            $dataParam = [
                'type_operations_libelle'=>'Sélection adjudication',
                'operations_id'=>$operation->id,
                'cotation_fournisseurs_id'=>$cotation_fournisseur->id
            ];

            $dataSetDate = [
                'created_at'=>$mieux_disant->created_at,
                'updated_at'=>$mieux_disant->updated_at,
            ];
            $this->setDate($dataParam,$dataSetDate);
        }
    }
    public function storeValiderDemandeAchat($data){
        
        $valider_demande_achat = $this->getValiderDemandeAchatByDetailDemandeAchatId($data['detail_demande_achats_id']);

        if ($valider_demande_achat === null) {
            $valider_demande_achat = ValiderDemandeAchat::create($data);
        }

        return $valider_demande_achat;
    }
    public function getValiderDemandeAchatByDetailDemandeAchatId($detail_demande_achat_id){
        return DB::table('valider_demande_achats')
        ->where('detail_demande_achats_id',$detail_demande_achat_id)
        ->first();
    }
    public function getDetailDemandeAchatsByDemandeAchatId($demande_achats_id){
        return DetailDemandeAchat::where('demande_achats_id',$demande_achats_id)->get();
    }
    public function procedureStoreValiderDetailDemandeAchat($detail_demande_achats){
        foreach ($detail_demande_achats as $detail_demande_achat) {
            $dataStoreValiderDemandeAchat = [
                'detail_demande_achats_id'=>$detail_demande_achat->id,
                'profils_id'=>Session::get('profils_id'),
                'qte_validee'=>$detail_demande_achat->qte_accordee,
                'flag_valide'=>true,
            ];
            $this->storeValiderDemandeAchat($dataStoreValiderDemandeAchat);  
        }
    }
    public function setOrganitionsArticles($organisations_id,$ref_fam){

        

        $organisation_article = $this->getOrganisationArticles($organisations_id,$ref_fam);

        $setOrgArticle = null;

        if ($organisation_article != null) {

            if(isset($organisation_article->flag_actif)){
                if($organisation_article->flag_actif != 1){
                    $setOrgArticle = 1;
                }
            }else{
                $setOrgArticle = 1;
            }            

        }else{
            $setOrgArticle = 1;
        }

        

        if(isset($setOrgArticle)){

                if ($ref_fam!=null) {

                    $organisation_article = $this->getOrganisationArticleByOrganisationByRefFam($organisations_id,$ref_fam);
        
                    if ($organisation_article!=null) {
        
                        if ($organisation_article->flag_actif==1) {
                            
                        }else{
        
                            $this->setOrganisationArticle($organisation_article->id,$organisations_id,$ref_fam);
        
                            $libelle = "Activé";
        
                            $organisation_articles_id = $organisation_article->id;
        
                        }
                    }else{
        
                        $libelle = "Créé";
        
                        $organisation_article = $this->storeOrganisationArticle($organisations_id,$ref_fam);
        
                        $organisation_articles_id = $organisation_article->id;
                    }
        
                    if (isset($organisation_articles_id)) {
        
                        
                        $commentaire = null;
                        
                        $type_statut_organisation_article = $this->getTypeStatutOrganisationArticle($libelle);
        
                        if ($type_statut_organisation_article!=null) {
        
                            $type_statut_org_articles_id = $type_statut_organisation_article->id;
                
                        }else{
                            $type_statut_org_article = $this->storeTypeStatutOrganisationArticle($libelle);
        
                            $type_statut_org_articles_id = $type_statut_org_article->id;
                        }
        
                        if (isset($type_statut_org_articles_id)) {
        
                            $this->setLastStatutOrganisationArticle($organisation_articles_id);
        
                            $this->storeStatutOrganisationArticle($organisation_articles_id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);
        
                        }
        
                    }
        
                }


            
        }

    }

    public function setOrganitionsDepots($organisations_id,$ref_depot){

        $organisation_depot = $this->getOrganisationDepots($organisations_id,$ref_depot);

        $setOrgDepot = null;

        if ($organisation_depot != null) {

            if(isset($organisation_depot->flag_actif)){
                if($organisation_depot->flag_actif != 1){
                    $setOrgDepot = 1;
                }
            }else{
                $setOrgDepot = 1;
            }

            
        }else{
            $setOrgDepot = 1;
        }

        if(isset($setOrgDepot)){

                if ($ref_depot!=null) {

                    $organisation_depot = $this->getOrganisationDepotRefDepot($organisations_id,$ref_depot);
        
                    if ($organisation_depot!=null) {
        
                        if ($organisation_depot->flag_actif==1) {
                            
                        }else{
        
                            $this->setOrganisationDepot($organisation_depot->id,$organisations_id,$ref_depot);
        
                            $libelle = "Activé";
        
                            $organisation_depots_id = $organisation_depot->id;
        
                        }
                    }else{
        
                        $libelle = "Créé";
        
                        $organisation_depot = $this->storeOrganisationDepot($organisations_id,$ref_depot);
        
                        $organisation_depots_id = $organisation_depot->id;
                    }
        
                    if (isset($organisation_depots_id)) {
        
                        
                        $commentaire = null;
                        
                        $type_statut_organisation_depot = $this->getTypeStatutOrganisationDepot($libelle);
        
                        if ($type_statut_organisation_depot!=null) {
        
                            $type_statut_org_depots_id = $type_statut_organisation_depot->id;
                
                        }else{
                            $type_statut_org_depot = $this->storeTypeStatutOrganisationDepot($libelle);
        
                            $type_statut_org_depots_id = $type_statut_org_depot->id;
                        }
        
                        if (isset($type_statut_org_depots_id)) {
        
                            $this->setLastStatutOrganisationDepot($organisation_depots_id);
        
                            $this->storeStatutOrganisationDepot($organisation_depots_id,$type_statut_org_depots_id,Session::get('profils_id'),$commentaire);
        
                        }
        
                    }
        
                }


            
        }

    }
    public function setDemandeAchat($demande_achats_id,$data){
        DemandeAchat::where('id',$demande_achats_id)->update($data); 
    }
    public function getCommandeInfo($demande_achats_id){
        return Commande::where('demande_achats_id',$demande_achats_id)->first();
    }

    public function storeCommande($data){
        return Commande::create($data);
    }

    public function setCommande($data){
        Commande::where('demande_achats_id',$data['demande_achats_id'])->update($data);
    }

    public function procedureStoreCommande($data){
        $demande_achat = $data['demande_achat'];
        $demande_cotation = $data['demande_cotation'];
        $dataStoreCommande = [
            'periodes_id'=>$demande_cotation->periodes_id,
            'delai'=>$demande_cotation->delai,
            'date_echeance'=>$demande_cotation->date_echeance,
            'demande_achats_id'=>$demande_achat->id,
            'profils_id'=>Session::get('profils_id'),
        ];
        $commande = $this->getCommandeInfo($demande_achat->id);
        if ($commande === null) {
            $this->storeCommande($dataStoreCommande);
        }
        
        if($commande != null){
            $this->setCommande($dataStoreCommande);
        }
    }
    public function getTypeAchatByLibelle($libelle){
        return TypeAchat::where('libelle',$libelle)->first();        
    }

    public function storeTypeAchat($libelle_type_achat){
        return TypeAchat::create([
            'libelle'=>$libelle_type_achat
        ]);
    }
    public function procedureSetDemandeAchat($demande_achat,$mieux_disant){
        $type_achats_id = null;
        $type_achats_libelle = 'Achat direct';
        $type_achat = $this->getTypeAchatByLibelle($type_achats_libelle);

        if ($type_achat != null) {
            $type_achats_id = $type_achat->id;
        }

        if ($type_achat === null){
            $type_achat = $this->storeTypeAchat($type_achats_libelle);
            $type_achats_id = $type_achat->id;
        }

        $dataSetDemandeAchat = [
            'taux_acompte'=>$mieux_disant->taux_acompte,
            'type_achats_id'=>$type_achats_id
        ];
        $this->setDemandeAchat($demande_achat->id,$dataSetDemandeAchat);
    }
    public function getCritereByLibelle($criteres_libelle){
        return Critere::where('libelle',$criteres_libelle)->first();
    }

    public function storeCritere($criteres_libelle,$critere_mesure=null){
        return Critere::create([
            'libelle' => $criteres_libelle,
            'mesure' => $critere_mesure
        ]);
    }
    public function procedureStoreCritere($criteres_libelle){
        $critere =  $this->getCritereByLibelle($criteres_libelle);
        if ($critere === null) {
            $critere = $this->storeCritere($criteres_libelle);
        }
        return $critere;
    }

    public function procedureStoreCritereAdjudication($critere,$demande_achat){
        $critere_adjudication = $this->getCritereAdjudicationByCritereByDemandeAchat($critere->id,$demande_achat->id);
    
        if ($critere_adjudication===null) {
            $critere_adjudication = $this->storeCritereAdjudication($critere->id,$demande_achat->id);
        }
        return $critere_adjudication;
    }
    public function getCritereAdjudicationByCritereByDemandeAchat($criteres_id,$demande_achats_id){
        return CritereAdjudication::where('criteres_id',$criteres_id)
        ->where('demande_achats_id',$demande_achats_id)
        ->first();        
    }
    public function storeCritereAdjudication($criteres_id,$demande_achats_id){
        return CritereAdjudication::create([
            'criteres_id' => $criteres_id,
            'demande_achats_id' => $demande_achats_id
        ]);
    }
    public function getOrganisationByFamilleByDepot($organisation_id,$ref_fam,$ref_depot){
        return DB::table('organisation_articles as oa')
        ->join('organisations as o', 'o.id', '=', 'oa.organisations_id')
        ->join('statut_organisations as so', 'so.organisations_id', '=', 'o.id')
        ->join('type_statut_organisations as tso', 'tso.id', '=', 'so.type_statut_organisations_id')
        ->join('organisation_depots as od', 'od.organisations_id', '=', 'o.id')
        ->where('tso.libelle', 'Activé')
        ->where('oa.flag_actif', 1)
        ->where('ref_fam', $ref_fam)
        ->where('od.ref_depot', $ref_depot)
        ->where('o.id', $organisation_id)
        ->where('tso.libelle','Activé')
        ->select('o.id', 'o.denomination','oa.organisations_id')
        ->first();
    }
    public function getPreselectionSoumissionnaireByOrganisation($organisations_id,$critere_adjudications_id){                                       
        return PreselectionSoumissionnaire::where('organisations_id',$organisations_id)
        ->where('critere_adjudications_id',$critere_adjudications_id)
        ->first();
    }
    public function storePreselectionSoumissionnaire($organisations_id,$critere_adjudications_id){
        return PreselectionSoumissionnaire::create([
            'organisations_id' => $organisations_id,
            'critere_adjudications_id' => $critere_adjudications_id
        ]);
    }
    public function setPreselectionSoumissionnaire($organisations_id,$critere_adjudications_id,$preselection_soumissionnaires_id){

        PreselectionSoumissionnaire::where('id',$preselection_soumissionnaires_id)->update([
            'organisations_id' => $organisations_id,
            'critere_adjudications_id' => $critere_adjudications_id
        ]);

    }
    public function procedureStorePreselectionSoumissionnaire($mieux_disant,$critere_adjudication){
        $preselection_soumissionnaire = $this->getPreselectionSoumissionnaireByOrganisation($mieux_disant->organisations_id,$critere_adjudication->id);                                           

        if ($preselection_soumissionnaire === null) {
            $preselection_soumissionnaire = $this->storePreselectionSoumissionnaire($mieux_disant->organisations_id,$critere_adjudication->id);
        }
        
        if ($preselection_soumissionnaire != null){
            $this->setPreselectionSoumissionnaire($mieux_disant->organisations_id,$critere_adjudication->id,$preselection_soumissionnaire->id);
        }
    }
    public function getCotationFournisseurs($organisations_id,$demande_achats_id){   
        return DB::table('cotation_fournisseurs')
        ->where('organisations_id',$organisations_id)
        ->where('demande_achats_id',$demande_achats_id)
        ->select('id as cotation_fournisseurs_id','cotation_fournisseurs.*')
        ->first();
    }
    public function storeCotationFournisseur($data){
        return CotationFournisseur::create($data);
    }
    public function setCotationFournisseur($organisations_id,$demande_achats_id,$data){
        CotationFournisseur::where('demande_achats_id',$demande_achats_id)
        ->where('organisations_id',$organisations_id)
        ->update($data);
    }
    public function procedureStoreCotationFournisseur($data){
        $mieux_disant = $data['mieux_disant'];
        $demande_achat = $data['demande_achat'];
        $dataStoreCotationFournisseur = [
            'organisations_id'=>$mieux_disant->organisations_id,
            'demande_achats_id'=>$demande_achat->id,
            'acompte'=>$mieux_disant->acompte,
            'montant_total_brut'=>$mieux_disant->montant_total_brut,
            'taux_remise_generale'=>$mieux_disant->taux_remise_generale,
            'remise_generale'=>$mieux_disant->remise_generale,
            'montant_total_net'=>$mieux_disant->montant_total_net,
            'tva'=>$mieux_disant->tva,
            'montant_total_ttc'=>$mieux_disant->montant_total_ttc,
            'assiete_bnc'=>$mieux_disant->assiete_bnc,
            'taux_bnc'=>$mieux_disant->taux_bnc,
            'net_a_payer'=>$mieux_disant->net_a_payer,
            'taux_de_change'=>$mieux_disant->taux_de_change,
            'taux_acompte'=>$mieux_disant->taux_acompte,
            'montant_acompte'=>$mieux_disant->montant_acompte,
            'devises_id'=>$mieux_disant->devises_id,
        ];
        
        $cotation_fournisseur = $this->getCotationFournisseurs($mieux_disant->organisations_id,$demande_achat->id);

        if ($cotation_fournisseur === null) {
            $cotation_fournisseur = $this->storeCotationFournisseur($dataStoreCotationFournisseur);
        }
        
        if ($cotation_fournisseur != null){
            $this->setCotationFournisseur($mieux_disant->organisations_id,$demande_achat->id,$dataStoreCotationFournisseur);  
        }
        return $cotation_fournisseur;
    }
    public function getDetailCotation($cotation_fournisseur_id,$ref_articles){
        return DetailCotation::where('cotation_fournisseurs_id',$cotation_fournisseur_id)->where('ref_articles',$ref_articles)->first();
    }
    public function storeDetailCotation($data){                
        return DetailCotation::create($data);
    }
    public function setDetailCotation($detail_cotations_id,$data){
        DetailCotation::where('id',$detail_cotations_id)->update($data);
    }
    public function getDetailCotationById($detail_cotations_id){
        return DetailCotation::where('id',$detail_cotations_id)->first();
    }

    public function procedureCheckStoreDetailCotationFournisseur($cotation_fournisseur,$data){
        if($cotation_fournisseur != null && count($data) > 0){
            $controller2 = new Controller2();
            $mieux_disant = $data['mieux_disant'];
            $demande_cotation = $data['demande_cotation'];

            $detail_mieux_disants = $controller2->getDetailDemandeCotationsByMieuxDisantId($mieux_disant->id,$demande_cotation->libelle);

            $this->procedureCheck2StoreDetailCotationFournisseur($cotation_fournisseur,$detail_mieux_disants);
        }
    }
    public function procedureCheck2StoreDetailCotationFournisseur($cotation_fournisseur,$detail_mieux_disants){
        foreach ($detail_mieux_disants as $detail_mieux_disant) {
            $this->procedureStoreDetailCotationFournisseur($detail_mieux_disant,$cotation_fournisseur);
        }
    }
    public function procedureStoreDetailCotationFournisseur($detail_mieux_disant,$cotation_fournisseur){
        $detail_cotations = $this->getDetailCotation($cotation_fournisseur->id,$detail_mieux_disant->ref_articles);                    
                    
        $dataStoreDetailCotation = [
            'cotation_fournisseurs_id'=>$cotation_fournisseur->id,
            'ref_articles'=>$detail_mieux_disant->ref_articles,
            'qte'=>$detail_mieux_disant->detail_reponse_cotations_qte,
            'prix_unit'=>$detail_mieux_disant->detail_reponse_cotations_prix_unit,
            'remise'=>$detail_mieux_disant->detail_reponse_cotations_remise,
            'montant_ht'=>$detail_mieux_disant->detail_reponse_cotations_montant_ht,
            'montant_ttc'=>$detail_mieux_disant->detail_reponse_cotations_montant_ttc,
            'echantillon'=>$detail_mieux_disant->detail_reponse_cotations_echantillon,
        ]; 

        if ($detail_cotations === null) {
            $this->storeDetailCotation($dataStoreDetailCotation);
        }
        
        if ($detail_cotations != null){
            $this->setDetailCotation($detail_cotations->id,$dataStoreDetailCotation);
        }
    }
    public function getSelectionAdjudicationsByDemandeAchats($demande_achats_id){ 
        return SelectionAdjudication::join('cotation_fournisseurs as cf','cf.id','=','selection_adjudications.cotation_fournisseurs_id')
        ->where('cf.demande_achats_id',$demande_achats_id)
        ->select('selection_adjudications.id')
        ->get();
    }
    public function storeSelectionAdjudication($cotation_fournisseurs_id,$profils_id){
        return SelectionAdjudication::create([
            'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
            'profils_id'=>$profils_id,
        ]);
    }
    public function procedureCheckStoreSelectionAdjudication($cotation_fournisseur,$data){
        if($cotation_fournisseur != null && count($data) > 0){
            $this->storeSelectionAdjudication($cotation_fournisseur->id,Session::get('profils_id'));
        }
    }

    public function procedureDeleteSelectionAdjudication($data){
        $selection_adjudications = $this->getSelectionAdjudicationsByDemandeAchats($data['demande_achat']->id);            
        foreach ($selection_adjudications as $selection_adjudication) {
            $this->deleteSelectionAdjudication($selection_adjudication->id); 
        }
    }

    public function deleteSelectionAdjudication($selection_adjudications_id){
        SelectionAdjudication::where('id',$selection_adjudications_id)->delete();
    }
    
}
