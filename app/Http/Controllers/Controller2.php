<?php

namespace App\Http\Controllers;

use App\Models\MieuxDisant;
use Illuminate\Http\Request;
use App\Models\DemandeCotation;
use App\Models\DetailMieuxDisant;
use App\Models\StatutMieuxDisant;
use App\Models\MieuxDisantTravaux;
use Illuminate\Support\Facades\DB;
use App\Models\StatutReponseCotation;
use App\Models\MieuxDisantDemandeAchat;
use Illuminate\Support\Facades\Session;

class Controller2 extends Controller
{
    public function procedureSetReponseCotationFormat($reponse_cotation){
        
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


        $montant_total_brut = number_format((float)$reponse_cotation->montant_total_brut, 2, '.', '');

        $block_montant_total_brut = explode(".",$montant_total_brut);
        
        if (isset($block_montant_total_brut[0])) {
            $montant_total_brut_partie_entiere = (int) $block_montant_total_brut[0];
        }

        if (isset($block_montant_total_brut[1])) {
            $montant_total_brut_partie_decimale = (int) $block_montant_total_brut[1];
        }


        $taux_remise_generale = number_format((float)$reponse_cotation->taux_remise_generale, 2, '.', '');

        $block_taux_remise_generale = explode(".",$taux_remise_generale);
        
        if (isset($block_taux_remise_generale[0])) {
            $taux_remise_generale_partie_entiere = (int) $block_taux_remise_generale[0];
        }
        
        if (isset($block_taux_remise_generale[1])) {
            $taux_remise_generale_partie_decimale = (int) $block_taux_remise_generale[1];
        }


        $remise_generale = number_format((float)$reponse_cotation->remise_generale, 2, '.', '');

        $block_remise_generale = explode(".",$remise_generale);
        
        if (isset($block_remise_generale[0])) {
            $remise_generale_partie_entiere = (int) $block_remise_generale[0];
        }

        if (isset($block_remise_generale[1])) {
            $remise_generale_partie_decimale = (int) $block_remise_generale[1];
        }


        $montant_total_net = number_format((float)$reponse_cotation->montant_total_net, 2, '.', '');

        $block_montant_total_net = explode(".",$montant_total_net);
        
        if (isset($block_montant_total_net[0])) {
            $montant_total_net_partie_entiere = (int) $block_montant_total_net[0];
        }

        if (isset($block_montant_total_net[1])) {
            $montant_total_net_partie_decimale = (int) $block_montant_total_net[1];
        }


        $montant_total_ttc = number_format((float)$reponse_cotation->montant_total_ttc, 2, '.', '');

        $block_montant_total_ttc = explode(".",$montant_total_ttc);
        
        if (isset($block_montant_total_ttc[0])) {
            $montant_total_ttc_partie_entiere = (int) $block_montant_total_ttc[0];
        }

        if (isset($block_montant_total_ttc[1])) {
            $montant_total_ttc_partie_decimale = (int) $block_montant_total_ttc[1];
        }


        $net_a_payer = number_format((float)$reponse_cotation->net_a_payer, 2, '.', '');

        $block_net_a_payer = explode(".",$net_a_payer);
        
        if (isset($block_net_a_payer[0])) {
            $net_a_payer_partie_entiere = (int) $block_net_a_payer[0];
        }
        
        if (isset($block_net_a_payer[1])) {
            $net_a_payer_partie_decimale = (int) $block_net_a_payer[1];
        }
        
        $montant_acompte = $reponse_cotation->montant_acompte;
        if($reponse_cotation->montant_acompte === null && $reponse_cotation->taux_acompte != null){
            $montant_acompte = ( $reponse_cotation->net_a_payer * $reponse_cotation->taux_acompte ) / 100;
        }
        $montant_acompte = number_format((float)$montant_acompte, 2, '.', '');

        $block_montant_acompte = explode(".",$montant_acompte);
        
        if (isset($block_montant_acompte[0])) {
            $montant_acompte_partie_entiere = (int) $block_montant_acompte[0];
        }

        if (isset($block_montant_acompte[1])) {
            $montant_acompte_partie_decimale = (int) $block_montant_acompte[1];
        }

        $montant_tva = null;
        
        if(isset($reponse_cotation->tva) && isset($reponse_cotation->montant_total_net)){
            $dividende_montant_tva = $reponse_cotation->tva * $reponse_cotation->montant_total_net;

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

    public function procedureSetEchantillonReponse($data){

        $controllerDemandeCotation = new ControllerDemandeCotation();
        $set_echantillon = null;
        $find_echantillon_flag = null;
        if(isset($data['echantillon_flag'])){
            foreach ($data['echantillon_flag'] as $item => $value) {
                if($data['detail_reponse_cotations_id'] === (int) $value){
                    $find_echantillon_flag = 1;
                }
            }
        }
        if($find_echantillon_flag === null){
            $echantillon = null;
            $set_echantillon = 1;
        }
        $type_detail_operations_libelle = 'Detail cotation';
        if ($data['detail_reponse_cotation'] != null && isset($data['echantillon'])) {
            $echantillon =  $data['echantillon']->store('echantillonnage','public');
            $set_echantillon = 1;
        }

        
        if($set_echantillon === 1 && $data['detail_reponse_cotation'] != null){
            $dataSetEchantillon = [
                'echantillon'=>$echantillon,
                'detail_reponse_cotations_id'=>$data['detail_reponse_cotation']->id,
                'type_detail_operations_libelle'=>$type_detail_operations_libelle,
            ];
            
            $controllerDemandeCotation->setEchantillon($dataSetEchantillon);
        }
    }
    public function setDemandeCotationWithUpdatedById($demande_cotations_id){
        DemandeCotation::where('id',$demande_cotations_id)
        ->update([
            'updated_at'=>date('Y-m-d H:i:s'),
        ]);
    }

    public function getLastStatutDemandeCotationByLibelle($demande_cotations_id,$type_statut_demande_cotations_libelle){
        return DB::table('statut_demande_cotations as sda')
        ->join('type_statut_demande_cotations as tsda','tsda.id','=','sda.type_statuts_id')
        ->join('profils as p','p.id','=','sda.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('sda.demande_cotations_id',$demande_cotations_id)
        ->where('tsda.libelle',$type_statut_demande_cotations_libelle)
        ->orderByDesc('sda.id')
        ->limit(1)
        ->first();
    }

    public function getReponseCotationsByDemandeCotationId($demande_cotations_id){
        return DB::table('reponse_cotations as rc')
        ->join('fournisseur_demande_cotations as fdc','fdc.id','=','rc.fournisseur_demande_cotations_id')
        ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')
        ->join('devises as d','d.id','=','rc.devises_id')
        ->where('dc.id',$demande_cotations_id)
        ->select('d.*','o.*','fdc.*','dc.*','rc.*')
        ->get();
        
    }

    public function formatTotaux($data){

        $montant_total_brut = strrev(wordwrap(strrev($data['montant_total_brut_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['montant_total_brut_partie_decimale'])){
            $montant_total_brut = $montant_total_brut . '.' . strrev(wordwrap(strrev($data['montant_total_brut_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $taux_remise_generale = strrev(wordwrap(strrev($data['taux_remise_generale_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['taux_remise_generale_partie_decimale'])){
            $taux_remise_generale = $taux_remise_generale . '.' . strrev(wordwrap(strrev($data['taux_remise_generale_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $remise_generale = strrev(wordwrap(strrev($data['remise_generale_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['remise_generale_partie_decimale'])){
            $remise_generale = $remise_generale . '.' . strrev(wordwrap(strrev($data['remise_generale_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $montant_total_net = strrev(wordwrap(strrev($data['montant_total_net_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['montant_total_net_partie_decimale'])){
            $montant_total_net = $montant_total_net . '.' . strrev(wordwrap(strrev($data['montant_total_net_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $montant_total_ttc = strrev(wordwrap(strrev($data['montant_total_ttc_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['montant_total_ttc_partie_decimale'])){
            $montant_total_ttc = $montant_total_ttc . '.' . strrev(wordwrap(strrev($data['montant_total_ttc_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $net_a_payer = strrev(wordwrap(strrev($data['net_a_payer_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['net_a_payer_partie_decimale'])){
            $net_a_payer = $net_a_payer . '.' . strrev(wordwrap(strrev($data['net_a_payer_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $montant_acompte = strrev(wordwrap(strrev($data['montant_acompte_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['montant_acompte_partie_decimale'])){
            $montant_acompte = $montant_acompte . '.' . strrev(wordwrap(strrev($data['montant_acompte_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $montant_tva = strrev(wordwrap(strrev($data['montant_tva_partie_entiere'] ?? ''), 3, ' ', true));

        if(isset($data['montant_tva_partie_decimale'])){
            $montant_tva = $montant_tva . '.' . strrev(wordwrap(strrev($data['montant_tva_partie_decimale'] ?? ''), 3, ' ', true));
        }

        $reponse = [
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'montant_total_ttc'=>$montant_total_ttc,
            'net_a_payer'=>$net_a_payer,
            'montant_acompte'=>$montant_acompte,
            'montant_tva'=>$montant_tva,
        ];
        return $reponse;
    }

    public function procedureStoreMieuxDisant($data){
        
        $mieux_disant = $this->getMieuxDisantByReponseCotationId($data['reponse_cotations_id']);
        
        if($mieux_disant === null){
            $mieux_disant = $this->storeMieuxDisant($data);
        }

        if($mieux_disant != null){
            $this->setMieuxDisant($data,$mieux_disant->id);
            $mieux_disant = $this->getMieuxDisantById($mieux_disant->id);
        }
        return $mieux_disant;
    }

    public function storeMieuxDisant($data){
        return MieuxDisant::create($data);
    }

    public function setMieuxDisant($data,$mieux_disants_id){
        MieuxDisant::where('id',$mieux_disants_id)->update($data);
    }

    public function getMieuxDisantByReponseCotationId($reponse_cotations_id){
        return DB::table('mieux_disants as md')
        ->join('reponse_cotations as rc','rc.id','=','md.reponse_cotations_id')
        ->join('fournisseur_demande_cotations as fdc','fdc.id','=','rc.fournisseur_demande_cotations_id')
        ->join('devises as d','d.id','=','rc.devises_id')
        ->where('md.reponse_cotations_id',$reponse_cotations_id)
        ->select('d.*','fdc.*','rc.*','md.*')
        ->first();
    }

    public function getMieuxDisantById($mieux_disants_id){
        return DB::table('mieux_disants as md')
        ->join('reponse_cotations as rc','rc.id','=','md.reponse_cotations_id')
        ->join('fournisseur_demande_cotations as fdc','fdc.id','=','rc.fournisseur_demande_cotations_id')
        ->join('devises as d','d.id','=','rc.devises_id')
        ->where('md.id',$mieux_disants_id)
        ->select('d.*','fdc.*','rc.*','md.*')
        ->first();
    }

    public function procedureStoreDetailMieuxDisantCheck($dataStoreDetail){
        $detail_mieux_disant = null;
        $request = $dataStoreDetail['request'];
        foreach ($request->detail_reponse_cotations_id as $key => $detail_reponse_cotations_id) {
            
            $detail_demande_cotations_id = $request->detail_demande_cotations_id[$key];

            $detail_mieux_disant_old = $this->getDetailMieuxDisantOldByDetailDemandeCotationId($detail_demande_cotations_id,$detail_reponse_cotations_id);
            $data = [
                'request'=>$request,
                'mieux_disant'=>$dataStoreDetail['mieux_disant'],
                'detail_mieux_disant_old'=>$detail_mieux_disant_old,
                'detail_reponse_cotations_id'=>$detail_reponse_cotations_id,
            ];

            $detail_mieux_disant = $this->procedureStoreDetailMieuxDisantCheck2($data);
        }

        return $detail_mieux_disant;
    }
    public function getDetailMieuxDisantOldByDetailDemandeCotationId($detail_demande_cotations_id,$detail_reponse_cotations_id){
        return DB::table('detail_mieux_disants as dmd')
        ->join('detail_reponse_cotations as drc','drc.id','=','dmd.detail_reponse_cotations_id')
        ->join('detail_demande_cotations as ddc','ddc.id','=','drc.detail_demande_cotations_id')
        ->where('ddc.id',$detail_demande_cotations_id)
        ->where('drc.id','!=',$detail_reponse_cotations_id)
        ->select('ddc.*','drc.*','dmd.*')
        ->first();
    }

    public function procedureStoreDetailMieuxDisantCheck2($data){
        $detail_mieux_disant_old = $data['detail_mieux_disant_old'];
        $detail_mieux_disant = null; 

        $dataProcedure = [
            'detail_mieux_disant_old' => $detail_mieux_disant_old,
            'request' => $data['request'],
            'mieux_disant' => $data['mieux_disant'],
            'detail_reponse_cotations_id'=>$data['detail_reponse_cotations_id'],
        ];
        if($detail_mieux_disant_old != null){
            $response = $this->procedureDeleteDetailMieuxDisantCheck($dataProcedure);
            $detail_mieux_disant = $this->procedureDeleteDetailMieuxDisant($dataProcedure,$response);
        }

        if($detail_mieux_disant_old === null){
            $detail_mieux_disant = $this->storeDetailMieuxDisant($dataProcedure);
        }

        return $detail_mieux_disant;
    }

    public function storeDetailMieuxDisant($data){
        $dataStore = [
                        'mieux_disants_id'=>$data['mieux_disant']->id, 
                        'detail_reponse_cotations_id'=>$data['detail_reponse_cotations_id'], 
                    ];
        return DetailMieuxDisant::create($dataStore);
    }
    public function procedureDeleteDetailMieuxDisantCheck($data){
        $response = true;
        if($data['request']->type_operations_libelle === 'BCN'){
            $response = $this->checkMieuxDisantTravauxByMieuxDisantId($data['detail_mieux_disant_old']->mieux_disants_id);
        }
        if($data['request']->type_operations_libelle === 'BCS'){
            $response = $this->checkMieuxDisantDemandeAchatByMieuxDisantId($data['detail_mieux_disant_old']->mieux_disants_id);
        }
        return $response;
    }

    public function checkMieuxDisantTravauxByMieuxDisantId($mieux_disants_id){
        $response = true;
        $controllerTravaux = new ControllerTravaux();
        $mieux_disants_travauxe = MieuxDisantTravaux::where('mieux_disants_id',$mieux_disants_id)->first();
        if($mieux_disants_travauxe != null){
            $statut_travauxe = $controllerTravaux->getLastStatutTravaux($mieux_disants_travauxe->travauxes_id);
            $response = $this->authorizeDeleteDetailMieuxDisant($statut_travauxe);
        }
        return $response;
    }

    public function checkMieuxDisantDemandeAchatByMieuxDisantId($mieux_disants_id){
        $response = true;
        
        $mieux_disants_demande_achat = MieuxDisantDemandeAchat::where('mieux_disants_id',$mieux_disants_id)->first();
        if($mieux_disants_demande_achat != null){
            $statut_demande_achat = $this->getLastStatutDemandeAchat($mieux_disants_demande_achat->demande_achats_id);
            $response = $this->authorizeDeleteDetailMieuxDisant($statut_demande_achat);
        }
        return $response;
    }
    
    public function authorizeDeleteDetailMieuxDisant($statut){
        $response = false;
        if($statut != null){
            if($statut->libelle === 'Transmis (Responsable DMP)' or $statut->libelle === 'Rejeté (Responsable DMP)'){
                $response = true;
            }
        }
        return $response;
    }

    public function procedureDeleteDetailMieuxDisant($dataProcedure,$response){
        $detail_mieux_disant = null;
        if($response === true){
            $this->deleteDetailMieuxDisant($dataProcedure);
            $detail_mieux_disant = $this->storeDetailMieuxDisant($dataProcedure);
        }
        return $detail_mieux_disant;
    }

    public function deleteDetailMieuxDisant($dataProcedure){
        DetailMieuxDisant::where('id',$dataProcedure['detail_mieux_disant_old']->id)->delete();
        $this->procedureDeleteMieuxDisantSansDetail($dataProcedure['detail_mieux_disant_old']->mieux_disants_id);
    }
    public function procedureDeleteMieuxDisantSansDetail($mieux_disants_id){
        $detail_mieux_disants = $this->getDetailMieuxDisantByMieuxDisantId($mieux_disants_id);
        if(count($detail_mieux_disants) < 1){
            $this->deleteMieuxDisant($mieux_disants_id);
        }
    }

    public function getDetailMieuxDisantByMieuxDisantId($mieux_disants_id){
        return DB::table('mieux_disants as md')
        ->join('detail_mieux_disants as dmd','dmd.mieux_disants_id','=','md.id')
        ->where('md.id',$mieux_disants_id)
        ->select('md.*','dmd.*')
        ->get();
    }

    public function deleteMieuxDisant($mieux_disants_id){
        MieuxDisant::where('id',$mieux_disants_id)->delete();
    }

    public function procedureCheckStatutDemandeCotation($demande_cotations_id){
        
        $detail_demande_cotations = $this->getDetailDemandeCotationsByDemandeCotationId($demande_cotations_id);

        $dataDetailDemandeCotations = [];
        foreach ($detail_demande_cotations as $detail_demande_cotation) {
            $dataDetailDemandeCotations[] = $detail_demande_cotation->id;
        }


        $detail_demande_cotations_mieux_disants = $this->getDetailDemandeCotationsWithMieuxDisantByDemandeCotationId($demande_cotations_id);

        $dataDetailDemandeCotationMieuxDisants = [];
        foreach ($detail_demande_cotations_mieux_disants as $detail_demande_cotations_mieux_disant) {
            $dataDetailDemandeCotationMieuxDisants[] = $detail_demande_cotations_mieux_disant->id;
        }

        $type_statut_demande_cotations_libelle = "Cotation en cours d'analyse";
        if(count($dataDetailDemandeCotations) === count($dataDetailDemandeCotationMieuxDisants)){
            $type_statut_demande_cotations_libelle = "Cotation sélectionnée";
        }

        return $type_statut_demande_cotations_libelle;
    }

    public function procedureStoreSelectionMieuxDisant($data){
        
        $request = $data['request'];
        $dataStoreMieuxDisant = $data['dataStoreMieuxDisant'];
        
        $mieux_disant = $this->procedureStoreMieuxDisant($dataStoreMieuxDisant);
        
        if($mieux_disant === null){
            return redirect()->back()->with('error','Erreur lors de l\'enregistrement');
        }
        
        $detail_mieux_disant = null;
        
        if($mieux_disant != null){
            $dataStoreDetail = [
                'request'=>$request,
                'mieux_disant'=>$mieux_disant,
            ];

            $detail_mieux_disant = $this->procedureStoreDetailMieuxDisantCheck($dataStoreDetail);

            $this->procedureCheckStoreStatutMieuxDisant($request,$mieux_disant);            
        }

        if($detail_mieux_disant === null){
            return redirect()->back()->with('error','Erreur lors de l\'enregistrement');
        }
        return $mieux_disant;
    }

    public function procedureStoreRejetReponseCotation($data){
        
        $reponse_cotations_id = $data['request']->reponse_cotations_id;
        $type_statut_demande_cotations_libelle = "Cotation rejetée";
        $dataStatut = [
            'type_statut_demande_cotations_libelle'=>$type_statut_demande_cotations_libelle,
            'reponse_cotations_id'=>$reponse_cotations_id,
            'commentaire'=>$data['request']->commentaire,
        ];
        $this->procedureStoreTypeStatutReponseCotation($dataStatut);

    }

    public function procedureStoreTypeStatutReponseCotation($data){
        
        $controllerDemandeCotation = new ControllerDemandeCotation();

        $controllerDemandeCotation->storeTypeStatutDemandeCotation($data['type_statut_demande_cotations_libelle']);

        $type_statut_demande_cotation = $controllerDemandeCotation->getTypeStatutDemandeCotation($data['type_statut_demande_cotations_libelle']);

        if($type_statut_demande_cotation != null){

            $this->setLastStatutReponseCotation($data['reponse_cotations_id']);
            
            $dataStoreStatutReponseCotation = [
                'reponse_cotations_id'=>$data['reponse_cotations_id'],
                'type_statuts_id'=>$type_statut_demande_cotation->id,
                'profils_id'=>Session::get('profils_id'),
                'date_debut'=>date('Y-m-d H:i:s'),
                'date_fin'=>null,
                'commentaire'=>$data['commentaire']
            ];
            $this->storeStatutReponseCotation($dataStoreStatutReponseCotation);
        }
    }

    public function setLastStatutReponseCotation($reponse_cotations_id){
        $statut_reponse_cotation = StatutReponseCotation::where('reponse_cotations_id',$reponse_cotations_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();
        if($statut_reponse_cotation != null){
            StatutReponseCotation::where('id',$statut_reponse_cotation->id)
            ->update([
                'date_fin'=>date('Y-m-d H:i:s')
            ]);
        }
    }

    public function storeStatutReponseCotation($data){
        return StatutReponseCotation::create($data);
    }

    public function getDetailDemandeCotationsByDemandeCotationId($demande_cotations_id){

        return  DB::table('demande_cotations as dc')
        ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','dc.id')
        ->where('dc.id',$demande_cotations_id)
        ->select('dc.*','ddc.*')
        ->get();

    }

    public function getDetailDemandeCotationsWithMieuxDisantByDemandeCotationId($demande_cotations_id){

        return  DB::table('demande_cotations as dc')
        ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','dc.id')
        ->join('detail_reponse_cotations as drc','drc.detail_demande_cotations_id','=','ddc.id')
        ->join('detail_mieux_disants as dmd','dmd.detail_reponse_cotations_id','=','drc.id')
        ->where('dc.id',$demande_cotations_id)
        ->select('dmd.*','dc.*','ddc.*')
        ->get();

    }

    public function getLastStatutReponseCotation($reponse_cotations_id,$profils_id=null){

        $statut_reponse_cotation = DB::table('statut_reponse_cotations as src')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','src.type_statuts_id')
            ->join('profils as p','p.id','=','src.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('src.reponse_cotations_id',$reponse_cotations_id)
            ->orderByDesc('src.id')
            ->limit(1)
            ->first();

        if($profils_id != null){
            $statut_reponse_cotation = DB::table('statut_reponse_cotations as src')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','src.type_statuts_id')
            ->join('profils as p','p.id','=','src.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('src.reponse_cotations_id',$reponse_cotations_id)
            ->where('src.profils_id',$profils_id)
            ->orderByDesc('src.id')
            ->limit(1)
            ->first();
        }
        

        return $statut_reponse_cotation;
        
    }

    public function procedureStoreStatutMieuxDisant($data){
        
        $controllerDemandeCotation = new ControllerDemandeCotation();    
        
        $controllerDemandeCotation->storeTypeStatutDemandeCotation($data['type_statut_demande_cotations_libelle']);

        $type_statut_mieux_disant = $controllerDemandeCotation->getTypeStatutDemandeCotation($data['type_statut_demande_cotations_libelle']);

        if($type_statut_mieux_disant != null){

            $this->setLastStatutMieuxDisant($data['mieux_disants_id']);
            
            $dataStoreStatutDemandeCotation = [
                'mieux_disants_id'=>$data['mieux_disants_id'],
                'type_statuts_id'=>$type_statut_mieux_disant->id,
                'profils_id'=>Session::get('profils_id'),
                'date_debut'=>date('Y-m-d H:i:s'),
                'date_fin'=>null,
                'commentaire'=>$data['commentaire']
            ];
            $this->storeStatutMieuxDisant($dataStoreStatutDemandeCotation);
        }

    }

    public function storeStatutMieuxDisant($data){
        return StatutMieuxDisant::create($data);
    }

    public function setLastStatutMieuxDisant($mieux_disants_id){
        $statut_mieux_disant = StatutMieuxDisant::where('mieux_disants_id',$mieux_disants_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();
        if($statut_mieux_disant != null){
            StatutMieuxDisant::where('id',$statut_mieux_disant->id)
            ->update([
                'date_fin'=>date('Y-m-d H:i:s')
            ]);
        }
    }

    public function procedureCheckStoreStatutMieuxDisant($request,$mieux_disant){
        $type_statut_mieux_disants_libelle = null;
        if($request->submit === 'select_mieux_disant' or $request->submit === 'modifier_select_mieux_disant'){
            $type_statut_mieux_disants_libelle = "Cotation en cours d'analyse";
        }

        if($request->submit === 'generer_bc'){
            $type_statut_mieux_disants_libelle = "Cotation sélectionnée";
        }
        
        if($type_statut_mieux_disants_libelle != null){

            $dataStatut = [
                'type_statut_demande_cotations_libelle'=>$type_statut_mieux_disants_libelle,
                'mieux_disants_id'=>$mieux_disant->id,
                'commentaire'=>$request->commentaire,
            ];
            $this->procedureStoreStatutMieuxDisant($dataStatut);

            $dataStatutDemandeCotation = [
                'type_statut_demande_cotations_libelle'=>$type_statut_mieux_disants_libelle,
                'reponse_cotations_id'=>$request->reponse_cotations_id,
                'commentaire'=>$request->commentaire,
            ];
            $this->procedureStoreTypeStatutReponseCotation($dataStatutDemandeCotation);
        }
    }

    public function getMieuxDisantsByDemandeCotationId($demande_cotations_id){
        return DB::table('mieux_disants as md')
        ->join('reponse_cotations as rc','rc.id','=','md.reponse_cotations_id')
        ->join('fournisseur_demande_cotations as fdc','fdc.id','=','rc.fournisseur_demande_cotations_id')
        ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')
        ->join('devises as d','d.id','=','rc.devises_id')
        ->where('dc.id',$demande_cotations_id)
        ->select('d.*','o.*','fdc.*','dc.*','rc.*','md.*')
        ->get();
    }

    public function getLastStatutMieuxDisant($mieux_disants_id,$profils_id=null){

        $statut_mieux_disant = DB::table('statut_mieux_disants as smd')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','smd.type_statuts_id')
            ->join('profils as p','p.id','=','smd.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('smd.mieux_disants_id',$mieux_disants_id)
            ->orderByDesc('smd.id')
            ->limit(1)
            ->first();

        if($profils_id != null){
            $statut_mieux_disant = DB::table('statut_mieux_disants as smd')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','smd.type_statuts_id')
            ->join('profils as p','p.id','=','smd.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('smd.mieux_disants_id',$mieux_disants_id)
            ->where('smd.profils_id',$profils_id)
            ->orderByDesc('smd.id')
            ->limit(1)
            ->first();
        }
        

        return $statut_mieux_disant;
        
    }

    public function getArticleByFamilleIdByPeriodeT($data){
        $date_debut = $data['date_debut'];
        $date_fin = $data['date_fin'];
        return DB::table('articles as a')
            ->join('familles as f','f.ref_fam', '=', 'a.ref_fam')
            ->where('f.id',$data['familles_id'])
            ->select(DB::raw('DATE(a.created_at)'),'a.created_at as articles_created_at','a.updated_at as articles_updated_at','a.*', 'f.*')
            ->where(DB::raw('DATE(a.created_at)'),'<=',$date_fin)
            ->where(DB::raw('DATE(a.created_at)'),'>=',$date_debut)
            ->get();
    }

    public function getDetailMieuxDisantByDetailDemandeCotationIdAndMieuxDisantId($detail_demande_cotations_id,$mieux_disants_id){
        return DB::table('detail_mieux_disants as dmd')
        ->join('detail_reponse_cotations as drc','drc.id','=','dmd.detail_reponse_cotations_id')
        ->where('drc.detail_demande_cotations_id',$detail_demande_cotations_id)
        ->where('dmd.mieux_disants_id',$mieux_disants_id)
        ->select('dmd.id as detail_mieux_disants_id','drc.*')
        ->first();
    }

    public function procedureSetDetailReponseCotationFormat2($detail_reponse_cotation){
        $controller1 = new Controller1();

        $detail_mieux_disants_id = null;
        $detail_reponse_cotations_id = null;
        $qte = 0;
        $prix_unit_partie_entiere = null;
        $prix_unit_partie_decimale = null;

        $remise_partie_entiere = null;
        $remise_partie_decimale = null;
        
        $montant_ht_partie_entiere = null;
        $montant_ht_partie_decimale = null;
        $montant_ttc_partie_entiere = null;
        $montant_ttc_partie_decimale = null;
        $echantillon = null;

        $montant_total_brut = 0;

        if($detail_reponse_cotation != null){
            $dataGet = [
                'prix_unit'=>$detail_reponse_cotation->prix_unit,
                'montant_ht'=>$detail_reponse_cotation->montant_ht,
                'montant_ttc'=>$detail_reponse_cotation->montant_ttc,
                'remise'=>$detail_reponse_cotation->remise,
            ];
            $reponse_data = $controller1->getDetailReponseCotationFormat($dataGet);
            
            $echantillon = $detail_reponse_cotation->echantillon;
            $qte = $detail_reponse_cotation->qte;

            $detail_reponse_cotations_id = null;
            if(isset($detail_reponse_cotation->id)){
                $detail_reponse_cotations_id = $detail_reponse_cotation->id;
            }

            $detail_mieux_disants_id = null;
            if(isset($detail_reponse_cotation->detail_mieux_disants_id)){
                $detail_mieux_disants_id = $detail_reponse_cotation->detail_mieux_disants_id;
            }

            $montant_total_brut = $montant_total_brut + $detail_reponse_cotation->montant_ttc;
            
            if(isset($reponse_data['prix_unit_partie_entiere'])){ 
                $prix_unit_partie_entiere = $reponse_data['prix_unit_partie_entiere']; 
            }

            if(isset($reponse_data['prix_unit_partie_decimale'])){ 
                $prix_unit_partie_decimale = $reponse_data['prix_unit_partie_decimale']; 
            }

            if(isset($reponse_data['remise_partie_entiere'])){ 
                $remise_partie_entiere = $reponse_data['remise_partie_entiere']; 
            }

            if(isset($reponse_data['remise_partie_decimale'])){ 
                $remise_partie_decimale = $reponse_data['remise_partie_decimale']; 
            }

            if(isset($reponse_data['montant_ht_partie_entiere'])){ 
                $montant_ht_partie_entiere = $reponse_data['montant_ht_partie_entiere']; 
            }

            if(isset($reponse_data['montant_ht_partie_decimale'])){ 
                $montant_ht_partie_decimale = $reponse_data['montant_ht_partie_decimale']; 
            }

            if(isset($reponse_data['montant_ttc_partie_entiere'])){ 
                $montant_ttc_partie_entiere = $reponse_data['montant_ttc_partie_entiere']; 
            }

            if(isset($reponse_data['montant_ttc_partie_decimale'])){ 
                $montant_ttc_partie_decimale = $reponse_data['montant_ttc_partie_decimale']; 
            }
        }

        $response = [
            'qte'=>$qte,
            'echantillon'=>$echantillon,
            'prix_unit_partie_entiere'=>$prix_unit_partie_entiere,
            'prix_unit_partie_decimale'=>$prix_unit_partie_decimale,
            'montant_ht_partie_entiere'=>$montant_ht_partie_entiere,
            'montant_ht_partie_decimale'=>$montant_ht_partie_decimale,
            'montant_ttc_partie_entiere'=>$montant_ttc_partie_entiere,
            'montant_ttc_partie_decimale'=>$montant_ttc_partie_decimale,
            'remise_partie_entiere'=>$remise_partie_entiere,
            'remise_partie_decimale'=>$remise_partie_decimale,
            'detail_reponse_cotations_id'=>$detail_reponse_cotations_id,
            'montant_total_brut'=>$montant_total_brut,
            'detail_mieux_disants_id'=>$detail_mieux_disants_id
        ];

        return $response;
    }

    public function getDetailDemandeCotationsByMieuxDisantId($mieux_disants_id,$type_operations_libelle){
        $detail_demande_cotations = [];

        if($type_operations_libelle === "Demande d'achats"){
            $detail_demande_cotations =  DB::table('demande_cotations as dc')
                ->join('type_operations as to','to.id','=','dc.type_operations_id')
                ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
                ->join('structures as s','s.code_structure','=','cb.code_structure')
                ->join('gestions as g','g.code_gestion','=','cb.code_gestion')
                ->join('familles as f','f.ref_fam','=','cb.ref_fam')
                ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','dc.id')
                ->join('bcs_detail_demande_cotations as bddc','bddc.detail_demande_cotations_id','=','ddc.id')
                ->join('articles as a','a.ref_articles','=','bddc.ref_articles')

                ->join('detail_reponse_cotations as drc','drc.detail_demande_cotations_id','=','ddc.id')
                ->join('detail_mieux_disants as dmd','dmd.detail_reponse_cotations_id','=','drc.id')
                ->where('dmd.mieux_disants_id',$mieux_disants_id)

                ->where('dc.flag_actif',1)
                ->where('ddc.flag_valide',1)
                ->select('f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','a.*','drc.reponse_cotations_id','drc.qte as detail_reponse_cotations_qte','drc.prix_unit as detail_reponse_cotations_prix_unit','drc.remise as detail_reponse_cotations_remise','drc.montant_ht as detail_reponse_cotations_montant_ht','drc.montant_ttc as detail_reponse_cotations_montant_ttc','drc.echantillon as detail_reponse_cotations_echantillon','bddc.*','ddc.*','dc.*')
                ->get(); 
        }

        if($type_operations_libelle === "Commande non stockable"){
            $detail_demande_cotations =  DB::table('demande_cotations as dc')
                ->join('type_operations as to','to.id','=','dc.type_operations_id')
                ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
                ->join('structures as s','s.code_structure','=','cb.code_structure')
                ->join('gestions as g','g.code_gestion','=','cb.code_gestion')
                ->join('familles as f','f.ref_fam','=','cb.ref_fam')
                ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','dc.id')
                ->join('bcn_detail_demande_cotations as bddc','bddc.detail_demande_cotations_id','=','ddc.id')
                ->join('services as se','se.id','=','bddc.services_id')

                ->join('detail_reponse_cotations as drc','drc.detail_demande_cotations_id','=','ddc.id')
                ->join('detail_mieux_disants as dmd','dmd.detail_reponse_cotations_id','=','drc.id')
                ->where('dmd.mieux_disants_id',$mieux_disants_id)

                ->where('dc.flag_actif',1)
                ->where('ddc.flag_valide',1)
                ->select('f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','se.libelle as services_libelle','drc.reponse_cotations_id','drc.qte as detail_reponse_cotations_qte','drc.prix_unit as detail_reponse_cotations_prix_unit','drc.remise as detail_reponse_cotations_remise','drc.montant_ht as detail_reponse_cotations_montant_ht','drc.montant_ttc as detail_reponse_cotations_montant_ttc','drc.echantillon as detail_reponse_cotations_echantillon','bddc.*','ddc.*','dc.*')
                ->get(); 
        }

        return $detail_demande_cotations;
    }

    public function getDetailDemandeCotationsNotInMieuxDisantByDemandeCotationId($demande_cotations_id,$type_operations_libelle){
        $detail_demande_cotations = [];

        if($type_operations_libelle === "Demande d'achats"){
            $detail_demande_cotations =  DB::table('demande_cotations as dc')
                ->join('type_operations as to','to.id','=','dc.type_operations_id')
                ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
                ->join('structures as s','s.code_structure','=','cb.code_structure')
                ->join('gestions as g','g.code_gestion','=','cb.code_gestion')
                ->join('familles as f','f.ref_fam','=','cb.ref_fam')
                ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','dc.id')
                ->join('bcs_detail_demande_cotations as bddc','bddc.detail_demande_cotations_id','=','ddc.id')
                ->join('articles as a','a.ref_articles','=','bddc.ref_articles')

                ->whereNotIn('ddc.id', function($query){
                    $query->select(DB::raw('drc.detail_demande_cotations_id'))
                        ->from('detail_reponse_cotations as drc')
                        ->join('detail_mieux_disants as dmd','dmd.detail_reponse_cotations_id','=','drc.id')
                        ->whereRaw('ddc.id = drc.detail_demande_cotations_id');
                })

                ->where('dc.flag_actif',1)
                ->where('ddc.flag_valide',1)
                ->where('dc.id',$demande_cotations_id)
                ->select('f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','a.*','bddc.*','ddc.*','dc.*')
                ->get(); 
        }

        if($type_operations_libelle === "Commande non stockable"){
            $detail_demande_cotations =  DB::table('demande_cotations as dc')
                ->join('type_operations as to','to.id','=','dc.type_operations_id')
                ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
                ->join('structures as s','s.code_structure','=','cb.code_structure')
                ->join('gestions as g','g.code_gestion','=','cb.code_gestion')
                ->join('familles as f','f.ref_fam','=','cb.ref_fam')
                ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','dc.id')
                ->join('bcn_detail_demande_cotations as bddc','bddc.detail_demande_cotations_id','=','ddc.id')
                ->join('services as se','se.id','=','bddc.services_id')

                ->whereNotIn('ddc.id', function($query){
                    $query->select(DB::raw('drc.detail_demande_cotations_id'))
                        ->from('detail_reponse_cotations as drc')
                        ->join('detail_mieux_disants as dmd','dmd.detail_reponse_cotations_id','=','drc.id')
                        ->whereRaw('ddc.id = drc.detail_demande_cotations_id');
                })

                ->where('dc.flag_actif',1)
                ->where('ddc.flag_valide',1)
                ->where('dc.id',$demande_cotations_id)
                ->select('f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','se.libelle as services_libelle','bddc.*','ddc.*','dc.*')
                ->get(); 
        }

        return $detail_demande_cotations;
    }
}
