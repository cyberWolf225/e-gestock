<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Taxe;
use App\Jobs\SendEmail;
use App\Models\TypeArticle;
use Illuminate\Http\Request;
use App\Models\ReponseCotation;
use Illuminate\Support\Facades\DB;
use App\Models\DetailReponseCotation;
use Illuminate\Support\Facades\Session;
use App\Models\FournisseurDemandeCotation;

class Controller1 extends Controller
{
    public function getOrganisationActifById($organisations_id){
        return DB::table('organisations as o')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->join('type_statut_organisations','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')
        ->where('p.flag_actif',1)
        ->where('u.flag_actif',1)
        ->where('tp.name','Fournisseur')
        ->select('so.profils_id','o.id','o.entnum','o.denomination','o.sigle')
        ->where('o.id',$organisations_id)
        ->first();
    }

    public function getOrganisationActifByIdWithName($organisations_id,$denomination){
        return DB::table('organisations as o')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->join('type_statut_organisations','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')
        ->where('p.flag_actif',1)
        ->where('u.flag_actif',1)
        ->where('tp.name','Fournisseur')
        ->select('o.id','o.entnum','o.denomination','o.sigle')
        ->where('o.id',$organisations_id)
        ->where('o.denomination',$denomination)
        ->first();
    }

    public function storeFournisseurDemandeCotation($data){
        $fournisseur_demande_cotation = $this->getFournisseurDemandeCotation($data);
        if($fournisseur_demande_cotation === null){
            FournisseurDemandeCotation::create([
                'demande_cotations_id'=>$data['demande_cotations_id'],
                'organisations_id'=>$data['organisations_id'],
            ]);
        }
    }

    public function setFournisseurDemandeCotation($data){
        $fournisseur_demande_cotation = $this->getFournisseurDemandeCotationById($data['fournisseur_demande_cotations_id']);
        if($fournisseur_demande_cotation != null){
            FournisseurDemandeCotation::where('id',$fournisseur_demande_cotation->id)
            ->update([
                'demande_cotations_id'=>$data['demande_cotations_id'],
                'organisations_id'=>$data['organisations_id'],
            ]);
        }
    }
    public function getFournisseurDemandeCotation($data){
        return FournisseurDemandeCotation::where('demande_cotations_id',$data['demande_cotations_id'])
        ->where('organisations_id',$data['organisations_id'])
        ->first();
    }
    public function getFournisseurDemandeCotationById($fournisseur_demande_cotations_id){
        return DB::table('fournisseur_demande_cotations as fdc')
        ->join('organisations as o','o.id','=','fdc.organisations_id')
        ->where('fdc.id',$fournisseur_demande_cotations_id)
        ->select('o.*','fdc.*')
        ->first();
    }
    public function getFournisseurDemandeCotations($data){
        $fournisseur_demande_cotations =  DB::table('fournisseur_demande_cotations as fdc')
        ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')

        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')

        ->where('fdc.demande_cotations_id',$data['demande_cotations_id'])
        ->where('fdc.flag_actif',1)
        ->select('u.email','o.*','dc.*','fdc.*','fdc.id as fournisseur_demande_cotations_id')
        ->orderBy('fdc.id')
        ->get();

        if(isset($data['organisations_id'])){
            if($data['organisations_id'] != null){
                $fournisseur_demande_cotations =  DB::table('fournisseur_demande_cotations as fdc')
                ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
                ->join('organisations as o','o.id','=','fdc.organisations_id')
        
                ->join('statut_organisations as so','so.organisations_id','=','o.id')
                ->join('profils as p','p.id','=','so.profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                ->where('tso.libelle','Activé')
        
                ->where('fdc.demande_cotations_id',$data['demande_cotations_id'])
                ->where('o.id',$data['organisations_id'])
                ->where('fdc.flag_actif',1)
                ->select('u.email','o.*','dc.*','fdc.*','fdc.id as fournisseur_demande_cotations_id')
                ->orderBy('fdc.id')
                ->get();
            }
        }
        return $fournisseur_demande_cotations;
    }

    public function getFournisseurDemandeCotationConnect($data){
        return DB::table('fournisseur_demande_cotations as fdc')
        ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')

        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')

        ->where('fdc.demande_cotations_id',$data['demande_cotations_id'])
        ->where('fdc.flag_actif',1)
        ->where('u.id',auth()->user()->id)
        ->select('u.email','o.*','dc.*','fdc.*','fdc.id as fournisseur_demande_cotations_id')
        ->orderBy('fdc.id')
        ->first();
    }

    public function procedureDeleteFournisseurDemandeCotation($data){
        $reponse_cotation = null;
        $fournisseur_demande_cotation = null;

        if(isset($data['demande_cotations_id']) && isset($data['organisations_id'])){
            $fournisseur_demande_cotation = $this->getFournisseurDemandeCotation($data);
        }
        
        if(isset($data['fournisseur_demande_cotations_id'])){
            $fournisseur_demande_cotation = $this->getFournisseurDemandeCotationById($data['fournisseur_demande_cotations_id']);
        }
        
        if($fournisseur_demande_cotation != null){
            $reponse_cotation = $this->getReponseCotationByFournisseurDemandeCotationId($fournisseur_demande_cotation->id);
        }
        
        if($reponse_cotation != null){
            $this->setFlagActifFournisseurDemandeCotation($reponse_cotation->fournisseur_demande_cotations_id);
        }

        if($reponse_cotation === null && $fournisseur_demande_cotation != null){
            $this->deleteFournisseurDemandeCotation($fournisseur_demande_cotation->id);
        }
    }

    public function setFlagActifFournisseurDemandeCotation($fournisseur_demande_cotations_id){
        FournisseurDemandeCotation::where('id',$fournisseur_demande_cotations_id)
        ->update([
            'flag_actif'=>0
        ]);
    }

    public function deleteFournisseurDemandeCotation($fournisseur_demande_cotations_id){
        FournisseurDemandeCotation::where('id',$fournisseur_demande_cotations_id)->delete();
    }

    public function getReponseCotationByFournisseurDemandeCotationId($fournisseur_demande_cotations_id){
        return DB::table('reponse_cotations as rc')
        ->where('rc.fournisseur_demande_cotations_id',$fournisseur_demande_cotations_id)
        ->first();
    }
    public function procedurestoreFournisseurDemandeCotation($data){
        if($data['organisation_by_id'] != null && $data['organisation_by_name'] != null){

            if(($data['organisation_by_id']->id === $data['organisation_by_name']->id) && ($data['organisation_by_id']->entnum === $data['organisation_by_name']->entnum) && ($data['organisation_by_id']->denomination === $data['organisation_by_name']->denomination) && ($data['organisation_by_id']->sigle === $data['organisation_by_name']->sigle)){

                $data = [
                    'demande_cotations_id'=>$data['demande_cotations_id'],
                    'organisations_id'=>$data['organisation_by_id']->id,
                    'fournisseur_demande_cotations_id'=>$data['fournisseur_demande_cotations_id']
                ];
                if($data['fournisseur_demande_cotations_id'] != null){
                    $this->setFournisseurDemandeCotation($data);
                }

                if($data['fournisseur_demande_cotations_id'] === null){
                    $this->storeFournisseurDemandeCotation($data);
                }

                

            }                    
        }
    }
    public function controllProcedurestoreFournisseurDemandeCotation($data){
        if(isset($data['organisations_id']) && isset($data['denomination'])){

            $organisation_by_id = $this->getOrganisationActifById($data['organisations_id']);

            $organisation_by_name = $this->getOrganisationActifByIdWithName($data['organisations_id'],$data['denomination']);

            $data = [
                'organisation_by_id'=>$organisation_by_id,
                'organisation_by_name'=>$organisation_by_name,
                'demande_cotations_id'=>$data['demande_cotations_id'],
                'fournisseur_demande_cotations_id'=>$data['fournisseur_demande_cotations_id'],
            ];
            $this->procedurestoreFournisseurDemandeCotation($data);
        }
    }

    public function procedureSuppressionFournisseurDemandeCotation($data){

        $fournisseur_demande_cotations_id_array_new = [];
        $fournisseur_demande_cotations_id_array_old = [];
        if(isset($data['request']->fournisseur_demande_cotations_id)){
            if(count($data['request']->fournisseur_demande_cotations_id) > 0){
                foreach ($data['request']->fournisseur_demande_cotations_id as $key => $value) {
                    $fournisseur_demande_cotations_id_array_new[] = (int) $value;
                }
            }
        }
        $fournisseur_demande_cotations = $this->getFournisseurDemandeCotations($data);
        foreach ($fournisseur_demande_cotations as $fournisseur_demande_cotation) {
            $fournisseur_demande_cotations_id_array_old[] = $fournisseur_demande_cotation->fournisseur_demande_cotations_id;
        }

        $fournisseur_demande_cotations_id_array_old_a_supprimer = array_merge(array_diff($fournisseur_demande_cotations_id_array_new, $fournisseur_demande_cotations_id_array_old), array_diff($fournisseur_demande_cotations_id_array_old, $fournisseur_demande_cotations_id_array_new));

        if(count($fournisseur_demande_cotations_id_array_old_a_supprimer) > 0){
            foreach ($fournisseur_demande_cotations_id_array_old_a_supprimer as $key => $fournisseur_demande_cotations_id) {
                $dataProcedure = [
                    'fournisseur_demande_cotations_id'=>$fournisseur_demande_cotations_id
                ];
                $this->procedureDeleteFournisseurDemandeCotation($dataProcedure);
            }
        }
    }

    public function getAccessFournisseurDemandeCotations($data){
        $partialSelectFournisseurBlade = null; 
        if(isset($data['demande_cotations_id'])){
            
            $fournisseur_demande_cotations = $this->getFournisseurDemandeCotations($data);
            if(count($fournisseur_demande_cotations) > 0){ 
                $partialSelectFournisseurBlade = 1; 
            }
        }
        return $partialSelectFournisseurBlade;
    }
    public function procedureNotifDemandeCotationFournisseur($statut_demande_cotation,$subject,$demande_cotations_id,$organisations_id=null){

        $data = [
            'demande_cotations_id'=>$demande_cotations_id,
            'organisations_id'=>$organisations_id
        ];

        if($statut_demande_cotation->libelle === 'Transmis pour cotation'){
            $fournisseur_demande_cotations = $this->getFournisseurDemandeCotations($data);
            foreach ($fournisseur_demande_cotations as $fournisseur_demande_cotation) {
                $this->notifDemandeCotationFournisseur($fournisseur_demande_cotation->email,$subject,$demande_cotations_id);
            }
        }

        if($statut_demande_cotation->libelle === 'Coté'){
            $fournisseur_demande_cotation = $this->getFournisseurDemandeCotationConnect($data);
            if($fournisseur_demande_cotation != null){
                $this->notifDemandeCotationFournisseur($fournisseur_demande_cotation->email,$subject,$demande_cotations_id);
            }
        }

        if($statut_demande_cotation->libelle === 'rejeter_cotation' or $statut_demande_cotation->libelle === 'select_mieux_disant' or $statut_demande_cotation->libelle === 'generer_bc'){
           
            $fournisseur_demande_cotations = $this->getFournisseurDemandeCotations($data);
            foreach ($fournisseur_demande_cotations as $fournisseur_demande_cotation) {
                $this->notifDemandeCotationFournisseur($fournisseur_demande_cotation->email,$subject,$demande_cotations_id);
            }
        }

    }

    public function notifDemandeCotationFournisseur($email,$subject,$demande_cotations_id){
        $details = [
            'email' => $email,
            'subject' => $subject,
            'demande_cotations_id' => $demande_cotations_id,
        ];

        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
        dispatch($emailJob);
    }

    public function getOrganisationByUserId($users_id){
        return DB::table('organisations as o')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->join('type_statut_organisations','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')
        ->where('p.flag_actif',1)
        ->where('u.flag_actif',1)
        ->where('u.id',$users_id)
        ->where('tp.name','Fournisseur')
        ->select('o.id','o.entnum','o.denomination','o.sigle')
        ->first();
    }

    public function getDemandeCotationWithFournisseurDemandeCotationsByOrganisationId($organisations_id,$type_statut_demande_cotations_libelle){
        return DB::table('demande_cotations as dc')
        ->join('type_operations as to','to.id','=','dc.type_operations_id')
        ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
        ->join('fournisseur_demande_cotations as fdc','fdc.demande_cotations_id','=','dc.id')
        ->where('dc.flag_actif',1)
        ->where('fdc.flag_actif',1)
        ->where('fdc.organisations_id',$organisations_id)
        ->select('cb.*','to.libelle','dc.*')
        ->orderByDesc('dc.updated_at')
        ->whereIn('dc.id',function($query) use($type_statut_demande_cotations_libelle){
            $query->select(DB::raw('sdc.demande_cotations_id'))
                    ->from('statut_demande_cotations as sdc')
                    ->join('type_statut_demande_cotations as tsdc','tsdc.id','=','sdc.type_statuts_id')
                    ->where('tsdc.libelle',$type_statut_demande_cotations_libelle)
                    ->whereRaw('dc.id = sdc.demande_cotations_id');
        })
        ->get();
    }

    public function getFournisseurDemandeCotationByOrganisationId($data){
        return DB::table('fournisseur_demande_cotations as fdc')
        ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')

        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')

        ->where('fdc.demande_cotations_id',$data['demande_cotations_id'])
        ->where('fdc.organisations_id',$data['organisations_id'])
        ->where('fdc.flag_actif',1)
        ->select('u.email','o.*','dc.*','fdc.*','fdc.id as fournisseur_demande_cotations_id')
        ->orderBy('fdc.id')
        ->get();
    }

    public function getTaxes(){
        return Taxe::whereIn('ref_taxe',['67','68','69'])->get();;
    }

    public function procedureSetDecimal($data){
        
        $montant_total_brut = 0;
        if($data['montant_total_brut'] != null){
            $montant_total_brut = filter_var($data['montant_total_brut'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $taux_remise_generale = 0;
        if($data['taux_remise_generale'] != null){
            $taux_remise_generale = filter_var($data['taux_remise_generale'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION)  * 1;
        } 

        $remise_generale = 0;
        if($data['remise_generale'] != null){
            $remise_generale = filter_var($data['remise_generale'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $montant_total_net = 0;
        if($data['montant_total_net'] != null){
            $montant_total_net = filter_var($data['montant_total_net'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $tva = 0;
        if($data['tva'] != null){
            $tva = filter_var($data['tva'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $montant_tva = 0;
        if($data['montant_tva'] != null){
            $montant_tva = filter_var($data['montant_tva'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $montant_total_ttc = 0;
        if($data['montant_total_ttc'] != null){
            $montant_total_ttc = filter_var($data['montant_total_ttc'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $net_a_payer = 0;
        if($data['net_a_payer'] != null){
            $net_a_payer = filter_var($data['net_a_payer'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $net_a_payer = 0;
        if($data['net_a_payer'] != null){
            $net_a_payer = filter_var($data['net_a_payer'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $taux_acompte = 0;
        if($data['taux_acompte'] != null){
            $taux_acompte = filter_var($data['taux_acompte'],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $dataSet = [
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$tva,
            'montant_tva'=>$montant_tva,
            'net_a_payer'=>$montant_total_ttc,
            'net_a_payer'=>$net_a_payer,
            'taux_acompte'=>$taux_acompte,
        ];
        return $dataSet;
    }

    public function checkDateEcheance($data){
        $response = true;

        if($data['type_profils_name'] === 'Fournisseur'){
            if($data['date_echeance'] < date('Y-m-d H:i:s')){
                $response = false;
            }
        }
        return $response;
    }

    public function checkQte($data){
        $response = true;
        $array_qte_cde = $data['qte_cde'];  
        $array_qte = $data['qte'];  
        
        foreach($array_qte_cde as $key => $value){
            $qte_cde = (int) filter_var($array_qte_cde[$key],FILTER_SANITIZE_NUMBER_INT);
            $qte = (int) filter_var($array_qte[$key],FILTER_SANITIZE_NUMBER_INT);
            
            if($qte > $qte_cde){
                $response = false;
            }
        }
        return $response;
    }

    public function procedureCalculTotaux($request,$responseDataSet){
        $response = [
            'montant_total_brut'=>null,
            'taux_remise_generale'=>null,
            'remise_generale'=>null,
            'montant_total_net'=>null,
            'tva'=>null,
            'montant_tva'=>null,
            'montant_total_ttc'=>null,
            'net_a_payer'=>null,
            'taux_acompte'=>null,
            'montant_acompte'=>null
        ];
        if(isset($request->qte)){
            $response = $this->calculTotaux($request,$responseDataSet);
        }
        return $response;
    }

    public function calculTotaux($request,$responseDataSet){
        $montant_total_brut = 0;
        $montant_total_net = null;
        $montant_tva = null;
        $montant_total_ttc = null;
        $net_a_payer = null;
        $taux_acompte = $responseDataSet['taux_acompte'];
        $montant_acompte = null;

        foreach ($request->qte as $key => $qte) {

            $data = $this->setAndFormatDetailDemandeCotation($request,$key);

            $montant_total_brut = $montant_total_brut + $data['montant_ht'];
        }

        $remise_generale = null;
        if($responseDataSet['remise_generale'] != null){
            $remise_generale = $responseDataSet['remise_generale'];
        }

        $taux_remise_generale = null;
        if($remise_generale != null && $remise_generale != 0 && $montant_total_brut != 0){
            $taux_remise_generale = ($remise_generale/$montant_total_brut) * 100;
        }

        $montant_total_net = $montant_total_brut - $remise_generale;

        $tva = null;
        if($responseDataSet['tva'] != null){
            $tva = $responseDataSet['tva'];
        }
        $tva_dividende = 0;
        if($tva != null){
            $tva_dividende = $montant_total_net * $tva;
        }
        
        if($tva_dividende != 0){
            $montant_tva = $tva_dividende / 100;
        }
        if($tva_dividende === 0){
            $montant_tva = null;
        }

        $montant_total_ttc = $montant_total_net + $montant_tva;
        $net_a_payer = $montant_total_ttc;

        $montant_acompte_dividende = $net_a_payer * $taux_acompte;
        if($montant_acompte_dividende != 0){
            $montant_acompte = $montant_acompte_dividende / 100;
        }
        if($montant_acompte_dividende === 0){
            $montant_acompte = null;
        }


        $response = [
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$tva,
            'montant_tva'=>$montant_tva,
            'montant_total_ttc'=>$montant_total_ttc,
            'net_a_payer'=>$net_a_payer,
            'taux_acompte'=>$taux_acompte,
            'montant_acompte'=>$montant_acompte
        ];
        
        return $response;
    }

    public function procedureStoreReponseCotation($data){
        
        $reponse_cotation = $this->getReponseCotationByFournisseurDemandeCotationId($data['fournisseur_demande_cotations_id']);
        
        if($reponse_cotation === null){
            $reponse_cotation = $this->storeReponseCotation($data);
        }

        if($reponse_cotation != null){
            $this->setReponseCotation($data,$reponse_cotation->id);
            $reponse_cotation = $this->getReponseCotationById($reponse_cotation->id);
        }
        return $reponse_cotation;
    }

    public function storeReponseCotation($data){
        return ReponseCotation::create($data);
    }

    public function setReponseCotation($data,$reponse_cotations_id){
        ReponseCotation::where('id',$reponse_cotations_id)->update($data);
    }

    public function getReponseCotationById($reponse_cotations_id){
        return DB::table('reponse_cotations as rc')
        ->join('fournisseur_demande_cotations as fdc','fdc.id','=','rc.fournisseur_demande_cotations_id')
        ->join('devises as d','d.id','=','rc.devises_id')
        ->where('rc.id',$reponse_cotations_id)
        ->select('d.*','fdc.*','rc.*')
        ->first();
    }
    public function procedureStoreDetailReponseCotationCheck($data){
        if(isset($data['request']->qte)){
            if(count($data['request']->qte) > 0){
                $this->procedureStoreDetailReponseCotation($data);
            }
        }
    }

    public function procedureStoreDetailReponseCotation($data){
        $request = $data['request'];
        $reponse_cotation = $data['reponse_cotation'];
        foreach ($request->qte as $key => $qte) {
            $responseData = $this->setAndFormatDetailDemandeCotation($request,$key);
            
            $dataCheck = [
                'request'=>$request,
                'responseData'=>$responseData,
                'reponse_cotation'=>$reponse_cotation,
                'key'=>$key,
            ];
            $this->storeDetailReponseCotationCheck($dataCheck);
        }
    }
    public function setAndFormatDetailDemandeCotation($request,$key){
        
        $detail_demande_cotations_id = null;
        if($request->detail_demande_cotations_id[$key] != null){
            $detail_demande_cotations_id = filter_var($request->detail_demande_cotations_id[$key],FILTER_SANITIZE_NUMBER_INT);
        }

        $qte = 0;
        if($request->qte[$key] != null){
            $qte = filter_var($request->qte[$key],FILTER_SANITIZE_NUMBER_INT) * 1;
        }

        $prix_unit = 0;
        if($request->prix_unit[$key] != null){
            $prix_unit = filter_var($request->prix_unit[$key],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $remise = 0;
        if($request->remise[$key] != null){
            $remise = filter_var($request->remise[$key],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) * 1;
        }

        $montant_brut = $qte * $prix_unit;

        $montant_remise_dividende = (int) $montant_brut * $remise;
        
        if($montant_remise_dividende != 0){
            $montant_remise = $montant_remise_dividende / 100;
        }
        if($montant_remise_dividende === 0){
            $montant_remise = 0;
        }
       
        $montant_ht = $montant_brut - $montant_remise;

        $data = [
            'qte'=>$qte,
            'prix_unit'=>$prix_unit,
            'remise'=>$remise,
            'montant_brut'=>$montant_brut,
            'montant_remise'=>$montant_remise,
            'montant_ht'=>$montant_ht,
            'detail_demande_cotations_id'=>$detail_demande_cotations_id
        ];

        return $data;
    }
    public function storeDetailReponseCotationCheck($data){

        $key = $data['key'];
        $request = $data['request'];
        $reponse_cotation = $data['reponse_cotation'];
        $detail_demande_cotations_id = $data['responseData']['detail_demande_cotations_id'];
        $qte = $data['responseData']['qte'];
        $prix_unit = $data['responseData']['prix_unit'];
        $remise = $data['responseData']['remise'];
        $montant_ht = $data['responseData']['montant_ht'];

        $dataParam = [
            'reponse_cotations_id'=>$reponse_cotation->id,
            'detail_demande_cotations_id'=>$detail_demande_cotations_id,
        ];

        $dataStore = [
            'reponse_cotations_id'=>$reponse_cotation->id,
            'detail_demande_cotations_id'=>$detail_demande_cotations_id,
            'qte'=>$qte,
            'prix_unit'=>$prix_unit,
            'remise'=>$remise,
            'montant_ht'=>$montant_ht,
            'montant_ttc'=>$montant_ht,
        ];

        
        $detail_reponse_cotation = $this->getDetailReponseCotationByForeignId($dataParam);

        if($detail_reponse_cotation != null){
            $this->setDetailReponseCotation($dataStore,$detail_reponse_cotation->id);
        }

        if($detail_reponse_cotation === null){    
            $detail_reponse_cotation = $this->storeDetailReponseCotation($dataStore);
        }
        
        if($detail_reponse_cotation != null){
            $dataSetEchantillon = [
                'request'=>$request,
                'detail_reponse_cotation'=>$detail_reponse_cotation,
                'key'=>$key,
            ];
            $this->procedureSetEchantillonDetailReponseCotation($dataSetEchantillon);
        }
    }

    public function getDetailReponseCotationByForeignId($data){
        return DB::table('detail_reponse_cotations as drc')
        ->where('drc.reponse_cotations_id',$data['reponse_cotations_id'])
        ->where('drc.detail_demande_cotations_id',$data['detail_demande_cotations_id'])
        ->first();
    }

    public function storeDetailReponseCotation($data){
        return DetailReponseCotation::create($data);
    }
    public function setDetailReponseCotation($data,$detail_reponse_cotations_id){
        DetailReponseCotation::where('id',$detail_reponse_cotations_id)
        ->update($data);
    }

    public function getDetailReponseCotationsByReponseCotationId($reponse_cotations_id){
        return DB::table('detail_reponse_cotations as drc')
        ->where('drc.reponse_cotations_id',$reponse_cotations_id)
        ->get();
    }

    public function procedureSetEchantillonDetailReponseCotation($data){
        
        $request = $data['request'];             
        $key = $data['key'];    
        $detail_reponse_cotation = $data['detail_reponse_cotation'];
        $controllerDemandeCotation = new ControllerDemandeCotation();
        $controller2 = new Controller2();
        
        if($request->submit === 'enregistrer_frs'){
            if ($detail_reponse_cotation != null && isset($request->echantillon[$key])) {

                $type_detail_operations_libelle = 'Detail cotation';
                $echantillon =  $request->echantillon[$key]->store('echantillonnage','public');
    
                $dataSetEchantillon = [
                    'echantillon'=>$echantillon,
                    'detail_reponse_cotations_id'=>$detail_reponse_cotation->id,
                    'type_detail_operations_libelle'=>$type_detail_operations_libelle,
                ];
                $controllerDemandeCotation->setEchantillon($dataSetEchantillon);
            }
        }

        if($request->submit === 'modifier_frs'){
            $echantillon = null;
            if(isset($request->echantillon[$key])){
                $echantillon = $request->echantillon[$key];
            }

            $echantillon_flag = null;
            if(isset($request->echantillon_flag)){
                $echantillon_flag = $request->echantillon_flag;
            }

            $dataProcedureSetEchantillon = [
                'echantillon_flag'=>$echantillon_flag,
                'detail_reponse_cotations_id'=>$detail_reponse_cotation->id,
                'detail_reponse_cotation'=>$detail_reponse_cotation,
                'echantillon'=>$echantillon,
            ];
            $controller2->procedureSetEchantillonReponse($dataProcedureSetEchantillon);
        }

    }

    public function procedureStoreTypeStatutDemandeCotation($data){
        $controllerDemandeCotation = new ControllerDemandeCotation();
        $type_statut_demande_cotations_libelle = $data['type_statut_demande_cotations_libelle'];
        $demande_cotations_id = $data['demande_cotations_id'];
        $commentaire = $data['commentaire'];
        $controllerDemandeCotation->storeTypeStatutDemandeCotation($type_statut_demande_cotations_libelle);

        $type_statut_demande_cotation = $controllerDemandeCotation->getTypeStatutDemandeCotation($type_statut_demande_cotations_libelle);

        if($type_statut_demande_cotation != null){

            $controllerDemandeCotation->setLastStatutDemandeCotation($demande_cotations_id);
            
            $dataStoreStatutDemandeCotation = [
                'demande_cotations_id'=>$demande_cotations_id,
                'type_statuts_id'=>$type_statut_demande_cotation->id,
                'profils_id'=>Session::get('profils_id'),
                'date_debut'=>date('Y-m-d H:i:s'),
                'date_fin'=>null,
                'commentaire'=>$commentaire
            ];
            $controllerDemandeCotation->storeStatutDemandeCotation($dataStoreStatutDemandeCotation);
        }
    }

    public function getDemandeCotationByReponseCotationId($reponse_cotations_id){
        return DB::table('demande_cotations as dc')
        ->join('type_operations as to','to.id','=','dc.type_operations_id')
        ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
        ->join('structures as s','s.code_structure','=','cb.code_structure')
        ->join('gestions as g','g.code_gestion','=','cb.code_gestion')
        ->join('familles as f','f.ref_fam','=','cb.ref_fam')
        ->where('dc.flag_actif',1)

        ->join('fournisseur_demande_cotations as fdc','fdc.demande_cotations_id','=','dc.id')
        ->join('reponse_cotations as rc','rc.fournisseur_demande_cotations_id','=','fdc.id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')

        ->where('fdc.flag_actif',1)
        ->where('rc.id',$reponse_cotations_id)
        ->select('o.*','fdc.*','rc.*','f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','dc.*')
        ->first();
    }
    public function getDetailReponseCotationByDetailDemandeCotationIdAndReponseCotationId($detail_demande_cotations_id,$reponse_cotations_id){
        return DB::table('detail_reponse_cotations as drc')
        ->where('drc.detail_demande_cotations_id',$detail_demande_cotations_id)
        ->where('drc.reponse_cotations_id',$reponse_cotations_id)
        ->first();
    }

    public function getDetailReponseCotationFormat($data){

        $prix_unit = number_format((float)$data['prix_unit'], 2, '.', '');

        $block_prix_unit = explode(".",$prix_unit);
        
        $prix_unit_partie_entiere = null;
        
        if (isset($block_prix_unit[0])) {
            $prix_unit_partie_entiere = (int) $block_prix_unit[0];
        }

        $prix_unit_partie_decimale = null;
        if (isset($block_prix_unit[1])) {
            $prix_unit_partie_decimale = (int) $block_prix_unit[1];
        }


        $remise = number_format((float)$data['remise'], 2, '.', '');

        $block_remise = explode(".",$remise);
        
        $remise_partie_entiere = null;
        
        if (isset($block_remise[0])) {
            $remise_partie_entiere = (int) $block_remise[0];
        }

        $remise_partie_decimale = null;
        if (isset($block_remise[1])) {
            $remise_partie_decimale = (int) $block_remise[1];
        }


        $montant_ht = number_format((float)$data['montant_ht'], 2, '.', '');

        $block_montant_ht = explode(".",$montant_ht);
        
        $montant_ht_partie_entiere = null;
        
        if (isset($block_montant_ht[0])) {
            $montant_ht_partie_entiere = (int) $block_montant_ht[0];
        }

        $montant_ht_partie_decimale = null;
        if (isset($block_montant_ht[1])) {
            $montant_ht_partie_decimale = (int) $block_montant_ht[1];
        }


        $montant_ttc = number_format((float)$data['montant_ttc'], 2, '.', '');

        $block_montant_ttc = explode(".",$montant_ttc);
        
        $montant_ttc_partie_entiere = null;
        
        if (isset($block_montant_ttc[0])) {
            $montant_ttc_partie_entiere = (int) $block_montant_ttc[0];
        }

        $montant_ttc_partie_decimale = null;
        if (isset($block_montant_ttc[1])) {
            $montant_ttc_partie_decimale = (int) $block_montant_ttc[1];
        }

        if($prix_unit_partie_decimale === 0){ $prix_unit_partie_decimale = null; }
        if($montant_ht_partie_decimale === 0){ $montant_ht_partie_decimale = null; }
        if($montant_ttc_partie_decimale === 0){ $montant_ttc_partie_decimale = null; }
        if($remise_partie_decimale === 0){ 
            $remise_partie_decimale = null; 
            if($remise_partie_entiere === 0){ $remise_partie_entiere = null; }
        }

        $data_response = [
            'prix_unit_partie_entiere'=>$prix_unit_partie_entiere,
            'prix_unit_partie_decimale'=>$prix_unit_partie_decimale,
            'montant_ht_partie_entiere'=>$montant_ht_partie_entiere,
            'montant_ht_partie_decimale'=>$montant_ht_partie_decimale,
            'montant_ttc_partie_entiere'=>$montant_ttc_partie_entiere,
            'montant_ttc_partie_decimale'=>$montant_ttc_partie_decimale,
            'remise_partie_entiere'=>$remise_partie_entiere,
            'remise_partie_decimale'=>$remise_partie_decimale,
        ];

        return $data_response;
    }

    public function procedureSetDetailReponseCotationFormat($detail_demande_cotations_id,$reponse_cotations_id,$mieux_disants_id=null){

        $controller2 = new Controller2();
        if($mieux_disants_id === null){
            $detail_reponse_cotation = $this->getDetailReponseCotationByDetailDemandeCotationIdAndReponseCotationId($detail_demande_cotations_id,$reponse_cotations_id);
        }

        if($mieux_disants_id != null){
            $detail_reponse_cotation = $controller2->getDetailMieuxDisantByDetailDemandeCotationIdAndMieuxDisantId($detail_demande_cotations_id,$mieux_disants_id);
        }
        
        if($detail_reponse_cotation != null){}
            $response = $controller2->procedureSetDetailReponseCotationFormat2($detail_reponse_cotation);
        
        
        //dd($response,$detail_reponse_cotation,$detail_demande_cotations_id,$reponse_cotations_id,$mieux_disants_id);
        
        return $response;
    }

    public function getTypeArticleById($type_article_id){
        return TypeArticle::where('id',$type_article_id)->first();
    }
}
