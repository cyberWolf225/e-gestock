<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Travaux;
use Illuminate\Http\Request;
use App\Models\DetailTravaux;
use App\Models\StatutTravaux;
use App\Models\TypeMouvement;
use App\Models\TypeStatutTravaux;
use App\Models\MieuxDisantTravaux;
use Illuminate\Support\Facades\DB;
use App\Models\DemandeCotationTravaux;
use App\Models\MieuxDisantDemandeAchat;
use Illuminate\Support\Facades\Session;
use App\Models\DemandeCotationDemandeAchat;

class Controller4 extends Controller
{
    public function procedureCheck2StoreStatutDemandeAchat($data){
        $type_statut_demande_cotations = $data['type_statut_demande_cotations2'];
        foreach ($type_statut_demande_cotations as $key => $type_statut_demande_cotation) {
            $this->procedure2StoreStatutDemandeAchat($type_statut_demande_cotation,$data);
        }
    }

    public function procedure2StoreStatutDemandeAchat($libelle,$data){
        $controller3 = new Controller3();
        $demande_achat = $data['demande_achat'];

        $this->storeTypeStatutDemandeAchat($libelle);

        $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

        if ($type_statut_demande_achat != null) {
            //$this->setLastStatutDemandeAchat($demande_achat->id);

            $dataStatutDemandeAchat = [
                'demande_achats_id' => $demande_achat->id,
                'type_statut_demande_achats_id' => $type_statut_demande_achat->id,
                'date_debut' => date('Y-m-d'),
                'date_fin' => date('Y-m-d'),
                'commentaire' => null,
                'profils_id' => Session::get('profils_id'),
            ];

            $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
        }
    }
    public function procedureStoreMieuxDisantDemandeAchat($mieux_disant,$demande_achat){
        $mieux_disant_demande_achat = $this->getMieuxDisantDemandeAchat($mieux_disant,$demande_achat);
        if($mieux_disant_demande_achat === null){
            $this->storeMieuxDisantDemandeAchat($mieux_disant,$demande_achat);
        }

        if($mieux_disant_demande_achat != null){
            $this->procedure2StoreMieuxDisantDemandeAchat($mieux_disant,$demande_achat,$mieux_disant_demande_achat);
        }
    }
    public function procedure2StoreMieuxDisantDemandeAchat($mieux_disant,$demande_achat,$mieux_disant_demande_achat){

        if($mieux_disant_demande_achat->mieux_disants_id != $mieux_disant->id){
            $this->deleteMieuxDisantDemandeAchatByDemandeAchatId($demande_achat->id);
            $this->storeMieuxDisantDemandeAchat($mieux_disant,$demande_achat);
        }
    }
    public function storeMieuxDisantDemandeAchat($mieux_disant,$demande_achat){
        MieuxDisantDemandeAchat::create([
            'mieux_disants_id'=>$mieux_disant->id,
            'demande_achats_id'=>$demande_achat->id,
        ]);
    }
    public function getMieuxDisantDemandeAchat($mieux_disant,$demande_achat){
        return MieuxDisantDemandeAchat::where('mieux_disants_id',$mieux_disant->id)
        ->where('demande_achats_id',$demande_achat->id)
        ->first();
    }
    public function deleteMieuxDisantDemandeAchatByDemandeAchatId($demande_achats_id){
        return MieuxDisantDemandeAchat::where('demande_achats_id',$demande_achats_id)->delete();
    }

    public function procedureStoreDemandeCotationDemandeAchat($demande_cotation,$demande_achat){
        $demande_cotation_demande_achat = $this->getDemandeCotationDemandeAchat($demande_cotation,$demande_achat);
        if($demande_cotation_demande_achat === null){
            $this->storeDemandeCotationDemandeAchat($demande_cotation,$demande_achat);
        }

        if($demande_cotation_demande_achat != null){
            $this->procedure2StoreDemandeCotationDemandeAchat($demande_cotation,$demande_achat,$demande_cotation_demande_achat);
        }
    }
    public function procedure2StoreDemandeCotationDemandeAchat($demande_cotation,$demande_achat,$demande_cotation_demande_achat){

        if($demande_cotation_demande_achat->demande_cotations_id != $demande_cotation->id){
            $this->deleteDemandeCotationDemandeAchatByDemandeAchatId($demande_achat->id);
            $this->storeDemandeCotationDemandeAchat($demande_cotation,$demande_achat);
        }
    }
    public function storeDemandeCotationDemandeAchat($demande_cotation,$demande_achat){
        DemandeCotationDemandeAchat::create([
            'demande_cotations_id'=>$demande_cotation->id,
            'demande_achats_id'=>$demande_achat->id,
        ]);
    }
    public function getDemandeCotationDemandeAchat($demande_cotation,$demande_achat){
        return DemandeCotationDemandeAchat::where('demande_cotations_id',$demande_cotation->id)
        ->where('demande_achats_id',$demande_achat->id)
        ->first();
    }
    public function deleteDemandeCotationDemandeAchatByDemandeAchatId($demande_achats_id){
        return DemandeCotationDemandeAchat::where('demande_achats_id',$demande_achats_id)->delete();
    }

    public function procedureGenerateBc($data){
        $mieux_disant = $data['mieux_disant'];
        $controller1 = new Controller1();
        $controller2 = new Controller2();
        $controllerDemandeCotation = new ControllerDemandeCotation();
        
        $reponse_cotation = null;
        $organisation = null;

        if($mieux_disant != null){
            $reponse_cotation = $controller1->getReponseCotationById($mieux_disant->reponse_cotations_id);  
            $organisation = $controller1->getOrganisationActifById($mieux_disant->organisations_id);
        }

        if($reponse_cotation === null){
            return redirect()->back()->with('error','Cotation introuvable');
        }

        $demande_cotation = null;

        if($reponse_cotation != null){
            $demande_cotation = $controllerDemandeCotation->getDemandeCotationById($reponse_cotation->demande_cotations_id);
        }

        if($demande_cotation === null){
            return redirect()->back()->with('error','Demande cotation introuvable');
        }
        
        $detail_demande_cotations = $controller2->getDetailDemandeCotationsByMieuxDisantId($mieux_disant->id,$demande_cotation->libelle);

        return [
            'reponse_cotation'=>$reponse_cotation,
            'organisation'=>$organisation,
            'demande_cotation'=>$demande_cotation,
            'detail_demande_cotations'=>$detail_demande_cotations,
        ];
    }
    public function procedureStoreTravaux($data){
        $controllerTravaux = new ControllerTravaux();
        $travauxe = $controllerTravaux->getTravauxByNumBc($data['num_bc']);
        if($travauxe === null){
            $this->storeTravaux($data);
        }

        if($travauxe != null){
            $this->setTravaux($data,$travauxe->id);
        }
    }

    public function storeTravaux($data){
        return Travaux::create($data);
    }
    public function setTravaux($data,$travauxes_id){
        return Travaux::where('id',$travauxes_id)->update($data);
    }
    public function procedureStoreDetailMieuxDisantTravaux($data){
        $detail_demande_cotations = $data['detail_demande_cotations'];
        $detail_travauxe = null;

        foreach ($detail_demande_cotations as $detail_demande_cotation) {

            $dataStore = [
                'travauxes_id' => $data['travauxes_id'],
                'services_id'=>$detail_demande_cotation->services_id,
                'qte'=>$detail_demande_cotation->detail_reponse_cotations_qte,
                'prix_unit'=>$detail_demande_cotation->detail_reponse_cotations_prix_unit,
                'remise'=>$detail_demande_cotation->detail_reponse_cotations_remise,
                'montant_ht'=>$detail_demande_cotation->detail_reponse_cotations_montant_ht,
                'montant_ttc'=>$detail_demande_cotation->detail_reponse_cotations_montant_ttc,
            ];
            $detail_travauxe = $this->storeDetailTravaux($dataStore);  
        }
        return $detail_travauxe;
    }
    public function storeDetailTravaux($data){
        return DetailTravaux::create($data);
    }
    public function procedureCheckStoreStatutTravaux($data){
        $controllerDemandeCotation = new ControllerDemandeCotation();
        $type_statut_demande_cotations = $data['type_statut_demande_cotations'];
        $travauxe = $data['travauxe'];
        $demande_cotation = $data['demande_cotation'];
        $organisation = $data['organisation'];
        foreach ($type_statut_demande_cotations as $key => $type_statut_demande_cotations_libelle) {
            
            if($type_statut_demande_cotations_libelle != 'Coté'){
                $statut_demande_cotation = $controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,null,$type_statut_demande_cotations_libelle);
            }
            if($type_statut_demande_cotations_libelle === 'Coté'){
                $statut_demande_cotation = $controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id,$organisation->profils_id,$type_statut_demande_cotations_libelle);
            }
            $this->procedureCheckStoreStatutTravaux2($statut_demande_cotation,$travauxe);
            
        }
    }
    public function procedureCheckStoreStatutTravaux2($statut_demande_cotation,$travauxe){
        if($statut_demande_cotation != null){
            $dataStatut = [
                'libelle'=>$statut_demande_cotation->libelle,
                'travauxes_id'=>$travauxe->id,
                'commentaire'=>$statut_demande_cotation->commentaire
            ];
            $this->procedureStoreStatutTravaux($dataStatut,$statut_demande_cotation);
        }
    }
    public function procedureStoreStatutTravaux($data,$statut_demande_cotation){
        
        $this->storeTypeStatutTravaux($data['libelle']);
        $type_statut_travauxe = $this->getTypeStatutTravaux($data['libelle']);

        if ($type_statut_travauxe != null) {
            $type_statut_travauxes_id = $type_statut_travauxe->id;

            //$this->setLastStatutTravaux($data['travauxes_id']);

            $dataStatutTravaux = [
                'travauxes_id'=>$data['travauxes_id'],
                'type_statut_travauxes_id'=>$type_statut_travauxes_id,
                'date_debut'=>date('Y-m-d'),
                'date_fin'=>date('Y-m-d'),
                'commentaire'=>trim($data['commentaire']),
                'profils_id'=>Session::get('profils_id'),
            ];

            $statut_travauxe = $this->storeStatutTravaux($dataStatutTravaux);
            
            $this->procedureSetStatutTravaux($statut_demande_cotation,$statut_travauxe);
        }
    }
    public function storeStatutTravaux($data){
        return StatutTravaux::create($data);
    }
    public function procedureSetStatutTravaux($statut_demande_cotation,$statut_travauxe){
        $dataSet = [
            'date_debut'=>$statut_demande_cotation->date_debut,
            'date_fin'=>$statut_demande_cotation->date_fin,
            'profils_id'=>$statut_demande_cotation->profils_id,
            'created_at'=>$statut_demande_cotation->created_at,
            'updated_at'=>$statut_demande_cotation->updated_at,
        ];
        $this->setStatutTravaux($dataSet,$statut_travauxe);
    }
    public function setStatutTravaux($data,$statut_travauxe){
        return StatutTravaux::where('id',$statut_travauxe->id)
        ->update($data);
    }
    public function storeTypeStatutTravaux($libelle){
        $type_statut_travauxe = TypeStatutTravaux::where('libelle',$libelle)->first();
                        
        if ($type_statut_travauxe===null) {
            TypeStatutTravaux::create([
                'libelle'=>$libelle
            ]);
        }
    }
    public function getTypeStatutTravaux($libelle){
        return TypeStatutTravaux::where('libelle',$libelle)->first();
    }
    public function procedureCheck2StoreStatutTravaux($data){
        $type_statut_demande_cotations = $data['type_statut_demande_cotations2'];
        foreach ($type_statut_demande_cotations as $key => $type_statut_demande_cotation) {
            $this->procedure2StoreStatutTravaux($type_statut_demande_cotation,$data);
        }
    }
    public function procedure2StoreStatutTravaux($libelle,$data){
        $controller4 = new Controller4();
        $travauxe = $data['travauxe'];

        $this->storeTypeStatutTravaux($libelle);

        $type_statut_travauxe = $this->getTypeStatutTravaux($libelle);

        if ($type_statut_travauxe != null) {
            //$this->setLastStatutTravaux($travauxe->id);

            $dataStatutTravaux = [
                'travauxes_id' => $travauxe->id,
                'type_statut_travauxes_id' => $type_statut_travauxe->id,
                'date_debut' => date('Y-m-d'),
                'date_fin' => date('Y-m-d'),
                'commentaire' => null,
                'profils_id' => Session::get('profils_id'),
            ];

            $controller4->storeStatutTravaux($dataStatutTravaux);
        }
    }
    public function procedureStoreMieuxDisantTravaux($mieux_disant,$travauxe){
        $mieux_disant_travauxe = $this->getMieuxDisantTravaux($mieux_disant,$travauxe);
        if($mieux_disant_travauxe === null){
            $this->storeMieuxDisantTravaux($mieux_disant,$travauxe);
        }

        if($mieux_disant_travauxe != null){
            $this->procedure2StoreMieuxDisantTravaux($mieux_disant,$travauxe,$mieux_disant_travauxe);
        }
    }
    public function getMieuxDisantTravaux($mieux_disant,$travauxe){
        return MieuxDisantTravaux::where('mieux_disants_id',$mieux_disant->id)
        ->where('travauxes_id',$travauxe->id)
        ->first();
    }

    public function storeMieuxDisantTravaux($mieux_disant,$travauxe){
        MieuxDisantTravaux::create([
            'mieux_disants_id'=>$mieux_disant->id,
            'travauxes_id'=>$travauxe->id,
        ]);
    }
    public function procedure2StoreMieuxDisantTravaux($mieux_disant,$travauxe,$mieux_disant_travauxe){

        if($mieux_disant_travauxe->mieux_disants_id != $mieux_disant->id){
            $this->deleteMieuxDisantTravauxByTravauxId($travauxe->id);
            $this->storeMieuxDisantTravaux($mieux_disant,$travauxe);
        }
    }
    public function deleteMieuxDisantTravauxByTravauxId($travauxes_id){
        return MieuxDisantTravaux::where('travauxes_id',$travauxes_id)->delete();
    }
    public function procedureStoreDemandeCotationTravaux($demande_cotation,$travauxe){
        $demande_cotation_travauxe = $this->getDemandeCotationTravaux($demande_cotation,$travauxe);
        if($demande_cotation_travauxe === null){
            $this->storeDemandeCotationTravaux($demande_cotation,$travauxe);
        }

        if($demande_cotation_travauxe != null){
            $this->procedure2StoreDemandeCotationTravaux($demande_cotation,$travauxe,$demande_cotation_travauxe);
        }
    }
    public function getDemandeCotationTravaux($demande_cotation,$travauxe){
        return DemandeCotationTravaux::where('demande_cotations_id',$demande_cotation->id)
        ->where('travauxes_id',$travauxe->id)
        ->first();
    }
    public function storeDemandeCotationTravaux($demande_cotation,$travauxe){
        DemandeCotationTravaux::create([
            'demande_cotations_id'=>$demande_cotation->id,
            'travauxes_id'=>$travauxe->id,
        ]);
    }
    public function procedure2StoreDemandeCotationTravaux($demande_cotation,$travauxe,$demande_cotation_travauxe){

        if($demande_cotation_travauxe->demande_cotations_id != $demande_cotation->id){
            $this->deleteDemandeCotationTravauxByTravauxId($travauxe->id);
            $this->storeDemandeCotationTravaux($demande_cotation,$travauxe);
        }
    }
    public function deleteDemandeCotationTravauxByTravauxId($travauxes_id){
        return DemandeCotationTravaux::where('travauxes_id',$travauxes_id)->delete();
    }

    public function getAllDetailReponseCotationByDetailDemandeCotationId($detail_demande_cotations_id,$detail_reponse_cotations_id){
        return DB::table('reponse_cotations as rc')
        ->join('detail_reponse_cotations as drc','drc.reponse_cotations_id','=','rc.id')
        ->join('fournisseur_demande_cotations as fdc','fdc.id','=','rc.fournisseur_demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')
        ->where('drc.id','!=',$detail_reponse_cotations_id)
        ->select('o.entnum','o.denomination','drc.*')
        ->whereIn('drc.detail_demande_cotations_id', function($query) use($detail_demande_cotations_id){
            $query->select(DB::raw('drc2.detail_demande_cotations_id'))
                ->from('detail_reponse_cotations as drc2')
                ->where('drc2.detail_demande_cotations_id',$detail_demande_cotations_id)
                ->whereRaw('drc.detail_demande_cotations_id = drc2.detail_demande_cotations_id');
        })
        ->get();
    }
    public function getMagasinStocks($depot = null, $article = null, $famille = null, $date_stock = null){
        $ref_depot = null;
        $articles_id = null;
        $familles_id = null;
        
        if($depot != null){ $ref_depot = $depot->ref_depot; }
        if($article != null){ $articles_id = $article->id; }
        if($famille != null){ $familles_id = $famille->id; }

        $magasin_stocks = [];
        /*
        ->where(DB::raw('DATE(a.created_at)'),'<=',$date_fin)
            ->where(DB::raw('DATE(a.created_at)'),'>=',$date_debut)
        */

        if ($ref_depot != null && $articles_id != null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('d.ref_depot',$ref_depot)
                ->where('a.id',$articles_id)
                ->where('f.id',$familles_id)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('d.ref_depot',$ref_depot)
                    ->where('a.id',$articles_id)
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        if ($ref_depot != null && $articles_id != null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('d.ref_depot',$ref_depot)
                ->where('a.id',$articles_id)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('d.ref_depot',$ref_depot)
                    ->where('a.id',$articles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }        

        if ($ref_depot != null && $articles_id === null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('d.ref_depot',$ref_depot)
                ->where('f.id',$familles_id)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('d.ref_depot',$ref_depot)
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        if ($ref_depot != null && $articles_id === null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('d.ref_depot',$ref_depot)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('d.ref_depot',$ref_depot)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        if ($ref_depot === null && $articles_id != null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('a.id',$articles_id)
                ->where('f.id',$familles_id)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('a.id',$articles_id)
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        if ($ref_depot === null && $articles_id != null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('a.id',$articles_id)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('a.id',$articles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        if ($ref_depot === null && $articles_id === null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->where('f.id',$familles_id)
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        if ($ref_depot === null && $articles_id === null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                ->get();

                if($date_stock != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam',DB::raw('SUM(mo.qte) as qte'),DB::raw('AVG(mo.prix_unit) as cmup'))
                    ->groupBy('a.ref_articles','a.design_article','f.ref_fam','f.design_fam')
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_stock)
                    ->get();
                }
        }

        return $magasin_stocks;
    }

    public function getDepotByRef($ref_depot){
        return Depot::where('ref_depot',$ref_depot)->first();
    }
    public function getTypeMouvements(){
        return TypeMouvement::all();
    }

    public function getMouvements($depot = null, $article = null, $famille = null, $type_mouvement = null, $date_debut = null, $date_fin = null){
        $ref_depot = null;
        $articles_id = null;
        $familles_id = null;
        $type_mouvements_id = null;
        
        if($depot != null){ $ref_depot = $depot->ref_depot; }
        if($article != null){ $articles_id = $article->id; }
        if($famille != null){ $familles_id = $famille->id; }
        if($type_mouvement != null){ $type_mouvements_id = $type_mouvement->id; }
    
        $magasin_stocks = [];
    
        if ($type_mouvement != null && $ref_depot != null && $articles_id != null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                ->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('d.ref_depot',$ref_depot)
                ->where('a.id',$articles_id)
                ->where('f.id',$familles_id)
                ->where('tmo.id',$type_mouvements_id)
                ->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')
                    ->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('d.ref_depot',$ref_depot)
                    ->where('a.id',$articles_id)
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        if ($type_mouvement != null && $ref_depot != null && $articles_id != null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('d.ref_depot',$ref_depot)
                ->where('a.id',$articles_id)
                ->where('tmo.id',$type_mouvements_id)
                ->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('d.ref_depot',$ref_depot)
                    ->where('a.id',$articles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }        
    
        if ($type_mouvement != null && $ref_depot != null && $articles_id === null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('d.ref_depot',$ref_depot)
                ->where('f.id',$familles_id)
                ->where('tmo.id',$type_mouvements_id)
                ->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('d.ref_depot',$ref_depot)
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        if ($type_mouvement != null && $ref_depot != null && $articles_id === null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('d.ref_depot',$ref_depot)
                ->where('tmo.id',$type_mouvements_id)->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('d.ref_depot',$ref_depot)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        if ($type_mouvement != null && $ref_depot === null && $articles_id != null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('a.id',$articles_id)
                ->where('f.id',$familles_id)
                ->where('tmo.id',$type_mouvements_id)->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('a.id',$articles_id)
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        if ($type_mouvement != null && $ref_depot === null && $articles_id != null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('a.id',$articles_id)
                ->where('tmo.id',$type_mouvements_id)->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('a.id',$articles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        if ($type_mouvement != null && $ref_depot === null && $articles_id === null && $familles_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('f.id',$familles_id)
                ->where('tmo.id',$type_mouvements_id)->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where('f.id',$familles_id)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        if ($type_mouvement != null && $ref_depot === null && $articles_id === null && $familles_id === null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
                ->where('tmo.id',$type_mouvements_id)->get();
    
                if($date_debut != null && $date_fin != null){
                    $magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('mouvements as mo','mo.magasin_stocks_id','=','ms.id')->join('type_mouvements as tmo','tmo.id','=','mo.type_mouvements_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','mo.qte','mo.prix_unit','mo.date_mouvement')
    
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'<=',$date_fin)
                    ->where(DB::raw('DATE(mo.date_mouvement)'),'>=',$date_debut)
                    ->where('tmo.id',$type_mouvements_id)
                    ->get();
                }
        }
    
        return $magasin_stocks;
    }

}
