<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Unite;
use App\Jobs\SendEmail;
use App\Models\Periode;
use App\Models\Service;
use App\Models\PieceJointe;
use Illuminate\Http\Request;
use App\Models\TypeOperation;
use App\Models\DemandeCotation;
use Illuminate\Support\Facades\DB;
use App\Models\DetailDemandeCotation;
use App\Models\DetailReponseCotation;
use App\Models\StatutDemandeCotation;
use Illuminate\Support\Facades\Session;
use App\Models\BcnDetailDemandeCotation;
use App\Models\BcsDetailDemandeCotation;
use App\Models\TypeStatutDemandeCotation;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ControllerDemandeCotation extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function accessView($data){
        $response = false;
        if($data['viewRessourceName'] === 'DemandeCotationIndex' or $data['viewRessourceName'] === 'DemandeCotationShow'){
            $typeProfilsNamesAutorise = "'Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'";
            if(isset(explode($data['typeProfilsName'],$typeProfilsNamesAutorise)[1])){
                $response = true;
            }
        }

        if($data['viewRessourceName'] === 'DemandeCotationCreate' or $data['viewRessourceName'] === 'DemandeCotationStore'){
            $typeProfilsNamesAutorise = "'Gestionnaire des achats'";
            if(isset(explode($data['typeProfilsName'],$typeProfilsNamesAutorise)[1])){
                $response = true;
            }
        }

        //
        if($data['viewRessourceName'] === 'DemandeCotationEdit' or $data['viewRessourceName'] === 'DemandeCotationUpdate' or $data['viewRessourceName'] === 'ReponseCotationIndex' or $data['viewRessourceName'] === 'MieuxDisantCreate' or $data['viewRessourceName'] === 'MieuxDisantIndex' or $data['viewRessourceName'] === 'MieuxDisantStore' or $data['viewRessourceName'] === 'MieuxDisantEdit' or $data['viewRessourceName'] === 'MieuxDisantUpdate'){
            
            $typeProfilsNamesAutorise = "'Gestionnaire des achats','Responsable des achats','Responsable DMP'";
            if(isset(explode($data['typeProfilsName'],$typeProfilsNamesAutorise)[1])){          
                $response = $this->accessViewByStatutDemandeCotation($data);
            }
        }

        if($data['viewRessourceName'] === 'ReponseCotationCreate' or $data['viewRessourceName'] === 'ReponseCotationStore' or $data['viewRessourceName'] === 'ReponseCotationEdit'){
            
            $typeProfilsNamesAutorise = "'Fournisseur'";
            if(isset(explode($data['typeProfilsName'],$typeProfilsNamesAutorise)[1])){         
                $response = $this->accessViewByStatutDemandeCotation($data);
            }
        }

        if($data['viewRessourceName'] === 'ReponseCotationShow'){
            $typeProfilsNamesAutorise = "'Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'";
            if(isset(explode($data['typeProfilsName'],$typeProfilsNamesAutorise)[1])){
                $response = true;
            }
        }
        
        return $response;
    }
    public function accessViewByStatutDemandeCotation($data){
        $response = false;
        $typeStatutDemandeCotationLibellesAutorise = null;
        if($data['typeProfilsName'] === 'Gestionnaire des achats'){
            $typeStatutDemandeCotationLibellesAutorise = "'Soumis pour validation','Annulé (Gestionnaire des achats)','Annulé (Responsable des achats)','Transmis pour cotation','Coté','Cotation rejetée','Cotation en cours d'analyse','Cotation sélectionnée'";
        } 

        if($data['typeProfilsName'] === 'Responsable des achats'){
            $typeStatutDemandeCotationLibellesAutorise = "'Transmis (Responsable des achats)','Rejeté (Responsable DMP)','Transmis pour cotation','Coté','Cotation rejetée','Cotation en cours d'analyse','Cotation sélectionnée'";
        }

        if($data['typeProfilsName'] === 'Responsable DMP'){
            $typeStatutDemandeCotationLibellesAutorise = "'Demande de cotation (Transmis Responsable DMP)','Transmis pour cotation','Coté','Cotation rejetée','Cotation en cours d'analyse','Cotation sélectionnée'";
        }
        
        if($data['typeProfilsName'] === 'Fournisseur'){
            $typeStatutDemandeCotationLibellesAutorise = "'Transmis pour cotation','Coté'";
        }

        if ($data['TypeStatutDemandeCotationLibelle'] != null) {
            
            if(isset(explode($data['TypeStatutDemandeCotationLibelle'],$typeStatutDemandeCotationLibellesAutorise)[1])){
                $response = true;
            }

        }
        
        return $response;
    }
    public function getTypeStatutDemandeCotationForDisplay($data){
        $response = null;
        if($data['type_profils_name'] === 'Gestionnaire des achats'){
            $response = 'Soumis pour validation';
        }

        if($data['type_profils_name'] === 'Responsable des achats'){
            $response = 'Transmis (Responsable des achats)';
        }

        if($data['type_profils_name'] === 'Responsable DMP'){
            $response = 'Demande de cotation (Transmis Responsable DMP)';
        }

        if($data['type_profils_name'] === 'Fournisseur'){
            $response = 'Transmis pour cotation';
        }

        return $response;
    }
    public function getDemandeCotationsByDepot($data){
        $type_statut_demande_cotations_libelle = $data['typeStatut'];

        return DB::table('demande_cotations as dc')
        ->join('type_operations as to','to.id','=','dc.type_operations_id')
        ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
        ->where('dc.ref_depot',$data['ref_depot'])
        ->where('dc.flag_actif',1)
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
    public function getDemandeCotations(){
        return DB::table('demande_cotations as dc')
        ->join('type_operations as to','to.id','=','dc.type_operations_id')
        ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
        ->where('dc.flag_actif',1)
        ->select('cb.*','to.libelle','dc.*')
        ->orderByDesc('dc.updated_at')
        ->get();
    }
    public function getUnites(){ return Unite::all(); }
    public function getServices(){ return Service::all(); }

    public function procedureControllerAccess($data){
        $type_profils_name = null;
        $ref_depot = null;
        $nom_prenoms_commentaire = null;
        $profil_commentaire = null;
        $autorisation_access = false;

        if (Session::has('profils_id')) {

            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));
            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if($infoUserConnect != null){
                $ref_depot = $infoUserConnect->ref_depot;
                $nom_prenoms_commentaire = $infoUserConnect->nom_prenoms;
                $profil_commentaire = $type_profils_name;
            }

            $dataAccessView = [
                'typeProfilsName'=>$type_profils_name,
                'viewRessourceName'=>$data['viewRessourceName'],
                'TypeStatutDemandeCotationLibelle'=>$data['TypeStatutDemandeCotationLibelle'] ?? null
            ];
            
            $autorisation_access = $this->accessView($dataAccessView);
        }

        $response = [
            'type_profils_name'=>$type_profils_name,
            'ref_depot'=>$ref_depot,
            'profils_id'=>Session::get('profils_id'),
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'autorisation_access'=>$autorisation_access,
        ];

        return $response;
    }
    public function getLastNumDemCot($exercice,$code_structure,$demande_cotations_id=null){

        $sequence_id = 1;

        if(isset($demande_cotations_id)){
            $demande_cotation = DemandeCotation::join('credit_budgetaires as cb','cb.id','=','demande_cotations.credit_budgetaires_id')
            ->where('demande_cotations.id', $demande_cotations_id)
            ->select('demande_cotations.num_dem')
            ->limit(1)
            ->first();
    
            if($demande_cotation != null){
    
                $num_dem = $demande_cotation->num_dem;
                
                if(isset(explode('DC', $num_dem)[1])){
                    $sequence_id =  (int) explode('DC', $num_dem)[1];                     
                }
                  
            }
        }else{
            $demande_cotation = DemandeCotation::join('credit_budgetaires as cb','cb.id','=','demande_cotations.credit_budgetaires_id')
            ->where('cb.exercice', $exercice)
            ->orderByDesc('demande_cotations.id')
            ->select('demande_cotations.num_dem')
            ->limit(1)
            ->first();
    
            if($demande_cotation != null){
    
                $num_dem = $demande_cotation->num_dem;
                
                if(isset(explode('DC', $num_dem)[1])){
                    $sequence_id =  (int) explode('DC', $num_dem)[1] + 1;                     
                }
                  
    
            }

        }

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'DC';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);

    }
    public function storeDemandeCotation($data){
        $demande_cotation = DemandeCotation::where('num_dem',$data['num_dem'])->first();
        if($demande_cotation === null){
            return DemandeCotation::create([
                        'num_dem'=>$data['num_dem'],
                        'intitule'=>$data['intitule'],
                        'type_operations_id'=>$data['type_operations_id'],
                        'credit_budgetaires_id'=>$data['credit_budgetaires_id'],
                        'ref_depot'=>$data['ref_depot'],
                        'periodes_id'=>$data['periodes_id'],
                        'delai'=>$data['delai'],
                        'date_echeance'=>$data['date_echeance'],
                        'flag_actif'=>$data['flag_actif']
                    ]);
        }
    }
    public function setDemandeCotation($data){
        DemandeCotation::where('id',$data['demande_cotations_id'])
        ->update([
            'num_dem'=>$data['num_dem'],
            'intitule'=>$data['intitule'],
            'type_operations_id'=>$data['type_operations_id'],
            'credit_budgetaires_id'=>$data['credit_budgetaires_id'],
            'ref_depot'=>$data['ref_depot'],
            'periodes_id'=>$data['periodes_id'],
            'delai'=>$data['delai'],
            'date_echeance'=>$data['date_echeance'],
            'flag_actif'=>$data['flag_actif'],
            'taux_acompte'=>$data['taux_acompte'],
        ]);
        return DemandeCotation::where('id',$data['demande_cotations_id'])->first();
    }
    public function storeUnite($unite){
        $getunite = Unite::where('unite',$unite)->first();
        if($getunite === null) {
            return Unite::create([
                'unite'=>$unite
            ]);
        }
    }
    public function getUnite($unite){
        return Unite::where('unite',$unite)->first();
    }
    public function storeDetailDemandeCotation($data){
        return  DetailDemandeCotation::create([
                    'demande_cotations_id'=>$data['demande_cotations_id'],
                    'code_unite'=>$data['code_unite'],
                    'qte_demandee'=>$data['qte_demandee'],
                    'qte_accordee'=>$data['qte_accordee'],
                    'flag_valide'=>$data['flag_valide']
                ]);
    }

    public function setDetailDemandeCotation($data){
        
        $detail_demande_cotation = null;
        if($data['detail_demande_cotations_id'] === null){
            $detail_demande_cotation = $this->storeDetailDemandeCotation($data);
        }

        if($data['detail_demande_cotations_id'] != null){
            $detail_demande_cotation = $this->updateDetailDemandeCotation($data);
        }

        return $detail_demande_cotation;
    }

    public function updateDetailDemandeCotation($data){
        $detail_demande_cotation = null;

        if($data['detail_demande_cotations_id'] != null){
            DetailDemandeCotation::where('id',$data['detail_demande_cotations_id'])
            ->update([
                'demande_cotations_id'=>$data['demande_cotations_id'],
                'code_unite'=>$data['code_unite'],
                'qte_demandee'=>$data['qte_demandee'],
                'qte_accordee'=>$data['qte_accordee'],
                'flag_valide'=>$data['flag_valide']
            ]);
            $detail_demande_cotation = DetailDemandeCotation::where('id',$data['detail_demande_cotations_id'])->first();
        }

        return $detail_demande_cotation;
    }

    public function storeBcsDetailDemandeCotation($data){
        $bcs_detail_demande_cotation = $this->getBcsDetailDemandeCotationById($data);
        if($bcs_detail_demande_cotation === null){
            return  BcsDetailDemandeCotation::create([
                'detail_demande_cotations_id'=>$data['detail_demande_cotations_id'],
                'ref_articles'=>$data['ref_articles'],
                'description_articles_id'=>$data['description_articles_id']
            ]);
        }
        return $bcs_detail_demande_cotation;
    }

    public function setBcsDetailDemandeCotation($data){
        
        $bcs_detail_demande_cotation = $this->getBcsDetailDemandeCotationById($data);
        if($bcs_detail_demande_cotation != null){
            BcsDetailDemandeCotation::where('id',$bcs_detail_demande_cotation->id)
            ->update([
                'ref_articles'=>$data['ref_articles'],
                'description_articles_id'=>$data['description_articles_id']
            ]);
        }

        if($bcs_detail_demande_cotation === null){
            $bcs_detail_demande_cotation = $this->storeBcsDetailDemandeCotation($data);
        }
        
        return $this->getBcsDetailDemandeCotationById($data);
    }

    public function getBcsDetailDemandeCotationById($data){
        return BcsDetailDemandeCotation::where('detail_demande_cotations_id',$data['detail_demande_cotations_id'])
        ->first();
    }

    public function storeTypeStatutDemandeCotation($libelle){
        $type_statut_demande_cotation = $this->getTypeStatutDemandeCotation($libelle);
        if($type_statut_demande_cotation === null){
            TypeStatutDemandeCotation::create([
                'libelle'=>$libelle
            ]);
        }
    }

    public function getTypeStatutDemandeCotation($libelle){
        return TypeStatutDemandeCotation::where('libelle',$libelle)->first(); 
    }

    public function setLastStatutDemandeCotation($demande_cotations_id){
        $statut_demande_cotation = StatutDemandeCotation::where('demande_cotations_id',$demande_cotations_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();
        if($statut_demande_cotation != null){
            StatutDemandeCotation::where('id',$statut_demande_cotation->id)
            ->update([
                'date_fin'=>date('Y-m-d H:i:s')
            ]);
        }
    }

    public function storeStatutDemandeCotation($data){
        return  StatutDemandeCotation::create([
                    'demande_cotations_id'=>$data['demande_cotations_id'],
                    'type_statuts_id'=>$data['type_statuts_id'],
                    'profils_id'=>$data['profils_id'],
                    'date_debut'=>$data['date_debut'],
                    'date_fin'=>$data['date_fin'],
                    'commentaire'=>$data['commentaire'],
                ]);
    }

    public function storeService($libelle){
        $service = $this->getServiceByLibelle($libelle);
        if($service === null){
            Service::create([
                'libelle'=>$libelle
            ]);
        }
    }

    public function getServiceByLibelle($libelle){
        return Service::where('libelle',$libelle)->first();
    }

    public function storeBcnDetailDemandeCotation($data){
        return BcnDetailDemandeCotation::create([
            'detail_demande_cotations_id'=>$data['detail_demande_cotations_id'],
            'services_id'=>$data['services_id']
        ]);
    }

    public function setBcnDetailDemandeCotation($data){
        $bcn_detail_demande_cotation = $this->getBcnDetailDemandeCotationById($data['detail_demande_cotations_id']);

        if($bcn_detail_demande_cotation != null){
            BcnDetailDemandeCotation::where('id',$bcn_detail_demande_cotation->id)
            ->update([
                'services_id'=>$data['services_id']
            ]);

            $bcn_detail_demande_cotation = $this->getBcnDetailDemandeCotationById($data['detail_demande_cotations_id']);
        }

        if($bcn_detail_demande_cotation === null){
            $bcn_detail_demande_cotation = $this->storeBcnDetailDemandeCotation($data);
        }

        return $bcn_detail_demande_cotation;
    }

    public function getBcnDetailDemandeCotationById($detail_demande_cotations_id){
        return BcnDetailDemandeCotation::where('detail_demande_cotations_id',$detail_demande_cotations_id)
        ->first();
    }

    public function notifDemandeCotations($subject,$demande_cotations_id,$type_profils_names,$organisations_id=null){       
        $profils = DB::table('profils as p')
        ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
        ->join('users as u', 'u.id', '=', 'p.users_id')
        ->join('agents as a', 'a.id', '=', 'u.agents_id')
        ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
        ->join('sections as s', 's.id', '=', 'ase.sections_id')
        ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
        ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
        ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
        ->whereIn('tp.name', $type_profils_names)
        ->select('u.email')
        ->where('p.flag_actif',1)
        ->where('tsase.libelle','Activé')
        ->whereIn('st.ref_depot',function($query) use($demande_cotations_id){
            $query->select(DB::raw('dc.ref_depot'))
                ->from('demande_cotations as dc')
                ->where('dc.id',$demande_cotations_id)
                ->whereRaw('st.ref_depot = dc.ref_depot');
        })
        ->get();

        foreach ($profils as $profil) {
            if($profil->email == 'an.kocola@cnps.ci'){ //condition à supprimer en environement de production
                $this->notifDemandeCotation($profil->email,$subject,$demande_cotations_id);
            }
        }

        $statut_demande_cotation = $this->getLastStatutDemandeCotation($demande_cotations_id);
        if($statut_demande_cotation != null){
            $controller1 = new Controller1();
            $controller1->procedureNotifDemandeCotationFournisseur($statut_demande_cotation,$subject,$demande_cotations_id,$organisations_id);
        }
    }

    public function notifDemandeCotation($email,$subject,$demande_cotations_id){
        // utilisateur connecté
            $details = [
                'email' => $email,
                'subject' => $subject,
                'demande_cotations_id' => $demande_cotations_id,
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }

    public function procedurePieceJointes($data){
        foreach ($data['request']->piece as $item => $value) {

            $data_piece = [
                'piece'=>$data['request']->piece[$item],
                'type_operations_libelle'=>$data['type_operations_libelle'],
                'profils_id'=>$data['profils_id'],
                'subject_id'=>$data['subject_id'],
            ];

            $this->setPieceJointe($data_piece);
        }
    }

    public function setPieceJointe($data){
        if (isset($data['piece'])) {

            $piece =  $data['piece']->store('piece_jointe','public');
            $name = $data['piece']->getClientOriginalName();
            $flag_actif = 1;
            $piece_jointes_id = null;

            $data_piece = [
                'subject_id'=>$data['subject_id'],
                'profils_id'=>$data['profils_id'],
                'type_operations_libelle'=>$data['type_operations_libelle'],
                'piece'=>$piece,
                'flag_actif'=>$flag_actif,
                'name'=>$name,
                'piece_jointes_id'=>$piece_jointes_id,
            ];
            $this->storePieceJointe($data_piece);
            
        }
    }

    public function storePieceJointe($data){
        $type_operation = TypeOperation::where('libelle',$data['type_operations_libelle'])->first();
        if ($type_operation!=null) {
            $type_operations_id = $type_operation->id;
        }else{
            $type_operations_id = TypeOperation::create([
                'libelle'=>$data['type_operations_libelle']
            ])->id;
        }

        if (isset($data['piece_jointes_id'])) {
            PieceJointe::where('id',$data['piece_jointes_id'])->update([
                'profils_id'=>$data['profils_id'],
                'flag_actif'=>$data['flag_actif']
            ]);
        }else{
            PieceJointe::create([
                'type_operations_id'=>$type_operations_id,
                'profils_id'=>$data['profils_id'],
                'subject_id'=>$data['subject_id'],
                'name'=>$data['name'],
                'piece'=>$data['piece'],
                'flag_actif'=>$data['flag_actif']    
            ]);
        }
    }

    public function setEchantillon($data){
        
        if($data['type_detail_operations_libelle'] === 'Detail demande de cotation' && isset($data['detail_operations_id'])){
            DetailDemandeCotation::where('id',$data['detail_operations_id'])
            ->update([
                'echantillon'=>$data['echantillon']
            ]);
        }
        
        if($data['type_detail_operations_libelle'] === 'Detail cotation' && isset($data['detail_reponse_cotations_id'])){
            
            DetailReponseCotation::where('id',$data['detail_reponse_cotations_id'])
            ->update([
                'echantillon'=>$data['echantillon']
            ]);
        }
    }

    public function getLastStatutDemandeCotation($demande_cotations_id,$profils_id=null,$type_statut_demande_cotations_libelle=null){

        $statut_demande_cotation = DB::table('statut_demande_cotations as sda')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','sda.type_statuts_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_cotations_id',$demande_cotations_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();

        if($profils_id != null){
            $statut_demande_cotation = DB::table('statut_demande_cotations as sda')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','sda.type_statuts_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_cotations_id',$demande_cotations_id)
            ->where('sda.profils_id',$profils_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
        }

        if($type_statut_demande_cotations_libelle != null){
            $statut_demande_cotation = DB::table('statut_demande_cotations as sda')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','sda.type_statuts_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_cotations_id',$demande_cotations_id)
            ->where('tsda.libelle',$type_statut_demande_cotations_libelle)
            ->orderByDesc('sda.id')
            ->select('u.*','tp.*','p.*','a.*','tsda.*','sda.*')
            ->limit(1)
            ->first();
        }

        if($type_statut_demande_cotations_libelle != null && $profils_id != null){
            $statut_demande_cotation = DB::table('statut_demande_cotations as sda')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','sda.type_statuts_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_cotations_id',$demande_cotations_id)
            ->where('tsda.libelle',$type_statut_demande_cotations_libelle)
            ->where('sda.profils_id',$profils_id)
            ->orderByDesc('sda.id')
            ->select('u.*','tp.*','p.*','a.*','tsda.*','sda.*')
            ->limit(1)
            ->first();
        }
        

        return $statut_demande_cotation;
        
    }

    public function getDemandeCotationById($demande_cotations_id){
        return DB::table('demande_cotations as dc')
        ->join('type_operations as to','to.id','=','dc.type_operations_id')
        ->join('credit_budgetaires as cb','cb.id','=','dc.credit_budgetaires_id')
        ->join('structures as s','s.code_structure','=','cb.code_structure')
        ->join('gestions as g','g.code_gestion','=','cb.code_gestion')
        ->join('familles as f','f.ref_fam','=','cb.ref_fam')
        ->where('dc.flag_actif',1)
        ->where('dc.id',$demande_cotations_id)
        ->select('f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','dc.*')
        ->first();
    }

    public function getDetailDemandeCotationsByDemandeCotationId($demande_cotations_id,$type_operations_libelle){
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
                ->where('dc.flag_actif',1)
                ->where('ddc.flag_valide',1)
                ->where('dc.id',$demande_cotations_id)
                ->select('f.id as familles_id','f.*','g.id as gestions_id','g.*','s.*','cb.*','to.libelle','se.libelle as services_libelle','bddc.*','ddc.*','dc.*')
                ->get(); 
        }

        return $detail_demande_cotations;
    }
    
    public function setButton($data){
        
        $titre = null;
        $value_button = null;
        $value_button2 = null;
        $value_button3 = null;
        $confirm = null;
        $confirm2 = null;
        $confirm3 = null;
        $class_button = null;
        $class_button2 = null;
        $class_button3 = null;
        $button = null;
        $button2 = null;
        $button3 = null;
        $partialSelectFournisseurBlade = null;
        
        if($data['TypeStatutDemandeCotationLibelle'] === "Soumis pour validation" or $data['TypeStatutDemandeCotationLibelle'] === "Annulé (Gestionnaire des achats)" or $data['TypeStatutDemandeCotationLibelle'] === "Annulé (Responsable des achats)" or $data['TypeStatutDemandeCotationLibelle'] === "Transmis pour cotation"){

            if($data['type_profils_name'] === "Gestionnaire des achats"){

                $titre = "MODIFICATION DE DEMANDE DE COTATION";
                $value_button = 'modifier_g_achat';
                $value_button3 = 'transferer_g_achat';

                $confirm = 'Faut-il enregistrer les modifications ?';
                $confirm3 = 'Faut-il transférer la demande de cotation au Responsable des achats ?';

                $class_button = 'btn btn-success';
                $class_button3 = 'btn btn-warning';

                $button = 'Modifier';
                $button3 = 'Transférer';

                if($data['TypeStatutDemandeCotationLibelle'] != "Annulé (Gestionnaire des achats)"){
                    $value_button2 = 'annuler_g_achat';
                    $confirm2 = 'Faut-il annuler la demande de cotation ?';
                    $class_button2 = 'btn btn-danger';
                    $button2 = 'Annuler';
                }

                $controller1 = new Controller1();
                $partialSelectFournisseurBlade = $controller1->getAccessFournisseurDemandeCotations($data);
                
            }
        }

        if($data['TypeStatutDemandeCotationLibelle'] === "Transmis (Responsable des achats)" or $data['TypeStatutDemandeCotationLibelle'] === "Rejeté (Responsable DMP)" or $data['TypeStatutDemandeCotationLibelle'] === "Transmis pour cotation"){

            if($data['type_profils_name'] === "Responsable des achats"){

                $titre = "PRÉSÉLECTION DES FOURNISSEURS DEVANT SOUMETTRE UNE COTATION";
                $partialSelectFournisseurBlade = 1;
                $value_button = 'modifier_r_achat';
                $value_button3 = 'transferer_r_achat';

                $confirm = 'Faut-il enregistrer les modifications ?';
                $confirm3 = 'Faut-il transférer la demande de cotation au Responsable DMP ?';

                $class_button = 'btn btn-success';
                $class_button3 = 'btn btn-warning';

                $button = 'Modifier';
                $button3 = 'Transférer';

                $value_button2 = 'annuler_r_achat';
                $confirm2 = 'Faut-il annuler la demande de cotation ?';
                $class_button2 = 'btn btn-danger';
                $button2 = 'Annuler';

            }
        }

        if($data['TypeStatutDemandeCotationLibelle'] === "Demande de cotation (Transmis Responsable DMP)" or $data['TypeStatutDemandeCotationLibelle'] === "Transmis pour cotation"){

            if($data['type_profils_name'] === "Responsable DMP"){

                $titre = "VALIDATION DE LA DEMANDE DE COTATION";
                $partialSelectFournisseurBlade = 1;
                $value_button = 'modifier_r_cmp';
                $confirm = 'Faut-il enregistrer les modifications ?';
                $class_button = 'btn btn-success';
                $button = 'Modifier';

                $value_button2 = 'annuler_r_cmp';
                $confirm2 = 'Faut-il annuler la demande de cotation ?';
                $class_button2 = 'btn btn-danger';
                $button2 = 'Annuler';

                if($data['TypeStatutDemandeCotationLibelle'] != "Transmis pour cotation"){
                    $value_button3 = 'transferer_r_cmp';
                    $confirm3 = 'Faut-il transférer la demande de cotation aux présélectionnés ?';$class_button3 = 'btn btn-warning';
                    $button3 = 'Transférer';
                }
            }
        }

        if($data['TypeStatutDemandeCotationLibelle'] === "Transmis pour cotation" or $data['TypeStatutDemandeCotationLibelle'] === "Coté"){

            if($data['type_profils_name'] === "Fournisseur"){

                if($data['viewRessourceName'] === 'ReponseCotationCreate'){
                    $titre = "REPONSE A LA DEMANDE DE COTATION";
                    $value_button = 'enregistrer_frs';
                    $confirm = 'Faut-il enregistrer la cotation ?';
                    $class_button = 'btn btn-success';
                    $button = 'Enregistrer';
                }

                if($data['viewRessourceName'] === 'ReponseCotationEdit'){
                    $titre = "MODIFICATION LA COTATION";
                    $value_button = 'modifier_frs';
                    $confirm = 'Faut-il modifier la cotation ?';
                    $class_button = 'btn btn-warning';
                    $button = 'Modifier';
                }
                
            }
        }

        if($data['TypeStatutDemandeCotationLibelle'] === "Coté" or $data['TypeStatutDemandeCotationLibelle'] === "Cotation en cours d'analyse"){

            if($data['type_profils_name'] === "Gestionnaire des achats" or $data['type_profils_name'] === "Responsable des achats" or $data['type_profils_name'] === "Responsable DMP"){

                if($data['viewRessourceName'] === 'MieuxDisantCreate'){
                    $titre = "SÉLECTION DU MIEUX DISANT";
                    $value_button = 'select_mieux_disant';
                    $confirm = 'Faut-il enregistrer ?';
                    $class_button = 'btn btn-success';
                    $button = 'Enregistrer';

                    $value_button2 = 'rejeter_cotation';
                    $confirm2 = 'Faut-il rejeter la cotation ?';
                    $class_button2 = 'btn btn-danger';
                    $button2 = 'Rejeter';                    

                    if($data['type_profils_name'] === "Responsable des achats" or $data['type_profils_name'] === "Responsable DMP"){
                        
                        $value_button3 = 'generer_bc';
                        $confirm3 = 'Faut-il générer le bon de commande ?';
                        $class_button3 = 'btn btn-warning';
                        $button3 = 'Générer le bon commande';
                    }
                }

                if($data['viewRessourceName'] === 'MieuxDisantEdit'){
                    $titre = "MODIFICATION DE LA SÉLECTION DU MIEUX DISANT";
                    $value_button = 'modifier_select_mieux_disant';
                    $confirm = 'Faut-il modifier ?';
                    $class_button = 'btn btn-success';
                    $button = 'Modifier';

                    $value_button2 = 'rejeter_cotation_select';
                    $confirm2 = 'Faut-il rejeter la cotation ?';
                    $class_button2 = 'btn btn-danger';
                    $button2 = 'Rejeter';                    

                    if($data['type_profils_name'] === "Responsable des achats" or $data['type_profils_name'] === "Responsable DMP"){
                        
                        $value_button3 = 'generer_bc';
                        $confirm3 = 'Faut-il générer le bon de commande ?';
                        $class_button3 = 'btn btn-warning';
                        $button3 = 'Générer le bon commande';
                    }
                }

            }
        }

        $buttons = [
            'value_button'=>$value_button,
            'value_button2'=>$value_button2,
            'value_button3'=>$value_button3,
            'confirm'=>$confirm,
            'confirm2'=>$confirm2,
            'confirm3'=>$confirm3,
            'class_button'=>$class_button,
            'class_button2'=>$class_button2,
            'class_button3'=>$class_button3,
            'button'=>$button,
            'button2'=>$button2,
            'button3'=>$button3,
            'titre'=>$titre,
            'partialSelectFournisseurBlade'=>$partialSelectFournisseurBlade
        ];
        return $buttons;
    }
    public function validateRequest($data){
        
        $data['request']->validate([
            'type_operations_libelle'=>['required','string','max:3'],
            'intitule'=>['required','string'],
            'commentaire'=>['nullable','string'],
        ]);

        if($data['request']->submit === 'annuler_g_achat' or $data['request']->submit === 'annuler_r_achat' or $data['request']->submit === 'annuler_r_cmp' or $data['request']->submit === 'rejeter_cotation' or $data['request']->submit === 'rejeter_cotation_select'){
            $data['request']->validate([
                'commentaire'=>['required','string'],
            ]);
        }

        if($data['fonctionName'] === 'store' or $data['fonctionName'] === 'update'){
            $data['request']->validate([
                'nbre_ligne'=>['nullable','numeric'],
                'ref_fam'=>['required','numeric'],
                'design_fam'=>['required','string'],
                'code_structure'=>['required','numeric'],
                'nom_structure'=>['required','string'],
                'code_gestion'=>['required','string','max:1'],
                'libelle_gestion'=>['required','string'],
            ]);

            if($data['fonctionName'] === 'update'){
                $data['request']->validate([
                    'demande_cotations_id'=>['required','numeric'],
                    'num_dem'=>['required','string'],
                    'submit'=>['required','string'],
                ]);

                if($data['request']->submit === 'modifier_r_achat' or $data['request']->submit === 'transferer_r_achat'){
                    $data['request']->validate([
                        'libelle_periode'=>['required','string'],
                        'valeur'=>['required','string'],
                        'delai'=>['required','numeric'],
                        'date_echeance'=>['required','string'],
                        'taux_acompte'=>['nullable','numeric','min:0','max:100'],
                    ]);
                    $fournisseur_requis = null;
                    if(isset($data['request']->organisations_id)){
                        foreach($data['request']->organisations_id as $key => $value){
                            if(isset($data['request']->organisations_id[$key]) && isset($data['request']->denomination[$key])){
                                $fournisseur_requis = 1;
                            }
                        }
                    }
                    if($fournisseur_requis === null){
                        $data['request']->validate([
                            'fournisseur'=>['required','string'],
                        ]);
                    }
                }
            }

            if($data['TypeOperationsLibelle'] === "Demande d'achats"){
                $data['request']->validate([
                    'ref_articles'=>['required','array'],
                    'design_article'=>['required','array'],
                    'description_articles_libelle'=>['required','array'],
                    'unites_libelle_bcs'=>['required','array'],
                    'qte_bcs'=>['required','array'],
                    'echantillon_bcs'=>['nullable','array'],
                ]);
            }

            if($data['TypeOperationsLibelle'] === "Commande non stockable"){
                $data['request']->validate([
                    'libelle_service'=>['required','array'],
                    'unites_libelle_bcn'=>['required','array'],
                    'qte_bcn'=>['required','array'],
                    'echantillon_bcn'=>['nullable','array'],
                ]);
            }
        }

        if($data['fonctionName'] === 'storeReponseCotation' or $data['fonctionName'] === 'updateReponseCotation' or $data['fonctionName'] === 'storeMieuxDisant' or $data['fonctionName'] === 'updateMieuxDisant'){
            
            if($data['fonctionName'] === 'updateReponseCotation'){
                $data['request']->validate([
                    'reponse_cotations_id'=>['required','numeric'],
                ]);
            }

            if($data['fonctionName'] === 'storeMieuxDisant' or $data['fonctionName'] === 'updateMieuxDisant'){
                $data['request']->validate([
                    'detail_reponse_cotations_id'=>['required','array'],
                ]);

                if($data['fonctionName'] === 'updateMieuxDisant'){
                    $data['request']->validate([
                        'mieux_disants_id'=>['required','numeric'],
                    ]);
                }
            }

            $data['request']->validate([
                'demande_cotations_id'=>['required','numeric'],
                'num_dem'=>['required','string'],
                'organisations_id'=>['required','numeric'],
                'entnum'=>['nullable','numeric'],
                'denomination'=>['required','string'],
                'libelle_periode'=>['required','string'],
                'valeur'=>['required','numeric'],
                'delai'=>['required','numeric'],
                'date_echeance'=>['required','string'],
                'code_devise'=>['required','string'],
                'libelle_devise'=>['required','string'],
                'detail_demande_cotations_id'=>['required','array'],
                'unites_libelle'=>['nullable','array'],
                'qte_cde'=>['required','array'],
                'qte'=>['required','array'],

                'prix_unit'=>['required','array'],
                'remise'=>['nullable','array'],
                'montant_ht'=>['required','array'],
                'montant_ht_bis'=>['required','array'],

                'montant_total_brut'=>['required','string'],
                'taux_remise_generale'=>['nullable','string'],
                'remise_generale'=>['nullable','string'],
                'montant_total_net'=>['required','string'],
                'tva'=>['nullable','string'],
                'montant_tva'=>['nullable','string'],
                'montant_total_ttc'=>['required','string'],
                'assiette' => ['nullable','string'],
                'taux_bnc' => ['nullable','string'],
                'montant_bnc' => ['nullable','string'],
                'net_a_payer'=>['required','string'],
                'acompte'=>['nullable','string'],
                'commentaire'=>['nullable','string'],
                'submit'=>['required','string'],

                'echantillon'=>['nullable','array'],

            ]);

            if($data['TypeOperationsLibelle'] === "Demande d'achats"){
                $data['request']->validate([
                    'ref_articles'=>['required','array'],
                    'design_article'=>['required','array'],
                    'description_articles_libelle'=>['nullable','array'],
                ]);
            }

            if($data['TypeOperationsLibelle'] === "Commande non stockable"){
                $data['request']->validate([
                    'libelle_service'=>['required','array'],
                ]);
            }
        }
    }
    public function getSubject($data){
        $response = null;
        if($data['statut'] === 'success'){
            $response = $this->getSubjectSuccess($data);
        }

        if($data['statut'] === 'error'){
            $response = $this->getSubjectError($data);
        }

        return $response;
    }

    public function getSubjectSuccess($data){
        $subject = null;
        $list_profil_a_nofier = null;
        
        if($data['submit'] === 'annuler_g_achat'){
            $subject = "Demande de cotation annulée";
            $list_profil_a_nofier = ['Gestionnaire des achats'];
        }
        if($data['submit'] === 'modifier_g_achat'){
            $subject = "Demande de cotation modifiée";
            $list_profil_a_nofier = ['Gestionnaire des achats'];
        }
        if($data['submit'] === 'transferer_g_achat'){
            $subject = "Demande de cotation transférée au Responsable des achats";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats'];
        }

        if($data['submit'] === 'annuler_r_achat'){
            $subject = "Demande de cotation annulée";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats'];
        }
        if($data['submit'] === 'modifier_r_achat'){
            $subject = "Demande de cotation modifiée par le Responsable des achats";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats'];
        }
        if($data['submit'] === 'transferer_r_achat'){
            $subject = "Demande de cotation transférée au Responsable DMP";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];
        }

        if($data['submit'] === 'annuler_r_cmp'){
            $subject = "Demande de cotation annulée";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];
        }
        if($data['submit'] === 'modifier_r_cmp'){
            $subject = "Demande de cotation modifiée par le Responsable DMP";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP'];
        }
        if($data['submit'] === 'transferer_r_cmp'){
            $subject = "Demande de cotation transférée aux fournisseurs présélectionnés";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        if($data['submit'] === 'enregistrer_frs'){
            $subject = "Cotation enregistrée";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        if($data['submit'] === 'modifier_frs'){
            $subject = "Cotation modifiée";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        if($data['submit'] === 'rejeter_cotation'){
            $subject = "Cotation rejetée";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        if($data['submit'] === 'select_mieux_disant'){
            $subject = "Cotation en cours d'analyse";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        if($data['submit'] === 'generer_bc'){
            $subject = "Cotation sélectionnée";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }
        
        if($data['submit'] === 'rejeter_cotation_select'){
            $subject = "Mieux disant rejeté";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        if($data['submit'] === 'modifier_select_mieux_disant'){
            $subject = "Cotation en cours d'analyse";
            $list_profil_a_nofier = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Fournisseur'];
        }

        $response = [
            'subject'=>$subject,
            'list_profil_a_nofier'=>$list_profil_a_nofier,
        ];

        return $response;
    }

    public function getSubjectError($data){
        $subject = null;
        $list_profil_a_nofier = null;

        if($data['submit'] === 'annuler_g_achat'){
            $subject = "Echec de l'annulation de la demande de cotation";
            $list_profil_a_nofier = ['Gestionnaire des achats'];
        }
        if($data['submit'] === 'modifier_g_achat'){
            $subject = "Echec de la modification de la demande de cotation";
            $list_profil_a_nofier = ['Gestionnaire des achats'];
        }
        if($data['submit'] === 'transferer_g_achat'){
            $subject = "Echec du transfert de la demande de cotation au Responsable des achats";
            $list_profil_a_nofier = ['Gestionnaire des achats'];
        }

        if($data['submit'] === 'annuler_r_achat'){
            $subject = "Echec de l'annulation de la demande de cotation";
            $list_profil_a_nofier = ['Responsable des achats'];
        }
        if($data['submit'] === 'modifier_r_achat'){
            $subject = "Echec de la modification de la demande de cotation par le Responsable des achats";
            $list_profil_a_nofier = ['Responsable des achats'];
        }
        if($data['submit'] === 'transferer_r_achat'){
            $subject = "Echec du transfert de la demande de cotation au Responsable DMP";
            $list_profil_a_nofier = ['Responsable des achats'];
        }

        if($data['submit'] === 'annuler_r_cmp'){
            $subject = "Echec de l'annulation de la demande de cotation";
            $list_profil_a_nofier = ['Responsable DMP'];
        }
        if($data['submit'] === 'modifier_r_cmp'){
            $subject = "Echec de la modification de la demande de cotation par le Responsable DMP";
            $list_profil_a_nofier = ['Responsable DMP'];
        }
        if($data['submit'] === 'transferer_r_cmp'){
            $subject = "Echec du transfert de la demande de cotation aux fournisseurs présélectionnés";
            $list_profil_a_nofier = ['Responsable DMP'];
        }

        $response = [
            'subject'=>$subject,
            'list_profil_a_nofier'=>$list_profil_a_nofier,
        ];

        return $response;
    }

    public function procedureSuppressionDetailDemandeCotation($data){
        $detail_demande_cotations_id_array_new = [];
        $detail_demande_cotations_id_array_old = [];
        if(isset($data['request']->detail_demande_cotations_id)){
            if(count($data['request']->detail_demande_cotations_id) > 0){
                foreach ($data['request']->detail_demande_cotations_id as $key => $value) {
                    $detail_demande_cotations_id_array_new[] = (int) $value;
                }
            }
        }

        $detail_demande_cotations = $this->getDetailDemandeCotationsByDemandeCotationId($data['demande_cotations_id'],$data['type_operations_libelle']);
        foreach ($detail_demande_cotations as $detail_demande_cotation) {
            $detail_demande_cotations_id_array_old[] = $detail_demande_cotation->detail_demande_cotations_id;
        }

        $detail_demande_cotations_id_array_old_a_supprimer = array_merge(array_diff($detail_demande_cotations_id_array_new, $detail_demande_cotations_id_array_old), array_diff($detail_demande_cotations_id_array_old, $detail_demande_cotations_id_array_new));

        if(count($detail_demande_cotations_id_array_old_a_supprimer) > 0){
            foreach ($detail_demande_cotations_id_array_old_a_supprimer as $key => $detail_demande_cotations_id) {
                $this->procedureDeleteDetailDemandeCotationById($detail_demande_cotations_id);
            }
        }        
    }

    public function procedureDeleteDetailDemandeCotationById($detail_demande_cotations_id){
        $suppression_definitive = 0;
        $detail_reponse_cotation = $this->getDetailReponseCotationByDetailDemandeCotation($detail_demande_cotations_id);

        if($detail_reponse_cotation === null){
            $suppression_definitive = 1;
        }

        if($suppression_definitive === 0){
            $flag_actif = 0;
            $flag_valide = 0;
            $datasetFlagActif = [
                'flag_actif'=>$flag_actif,
                'flag_valide'=>$flag_valide,
                'detail_demande_cotations_id'=>$detail_demande_cotations_id,
            ];
            $this->setFlagActifDetailDemandeCotationById($datasetFlagActif);
        }

        if($suppression_definitive === 1){
            $this->deleteDetailDemandeCotationById($detail_demande_cotations_id);
        }
    }

    public function getDetailReponseCotationByDetailDemandeCotation($detail_demande_cotations_id){
        return DB::table('detail_reponse_cotations as drc')
        ->where('drc.detail_demande_cotations_id',$detail_demande_cotations_id)
        ->first();
    }

    public function setFlagActifDetailDemandeCotationById($data){
        DetailDemandeCotation::where('id',$data['detail_demande_cotations_id'])
        ->update([
            'flag_actif'=>$data['flag_actif'],
            'flag_valide'=>$data['flag_valide'],
        ]);
    }

    public function deleteDetailDemandeCotationById($detail_demande_cotations_id){

        BcnDetailDemandeCotation::where('detail_demande_cotations_id',$detail_demande_cotations_id)->delete();

        BcsDetailDemandeCotation::where('detail_demande_cotations_id',$detail_demande_cotations_id)->delete();

        DetailDemandeCotation::where('id',$detail_demande_cotations_id)->delete();

    }

    public function getTypeStatutByButtonAction($submit){
        $response = null;
        if($submit === 'annuler_g_achat'){
            $response = "Annulé (Gestionnaire des achats)";
        }

        if($submit === 'modifier_g_achat'){
            $response = "Soumis pour validation";
        }

        if($submit === 'transferer_g_achat'){
            $response = "Transmis (Responsable des achats)";
        }

        if($submit === 'annuler_r_achat'){
            $response = "Annulé (Responsable des achats)";
        }

        if($submit === 'modifier_r_achat'){
            $response = "Transmis (Responsable des achats)";
        }

        if($submit === 'transferer_r_achat'){
            $response = "Demande de cotation (Transmis Responsable DMP)";
        }

        if($submit === 'annuler_r_cmp'){
            $response = "Rejeté (Responsable DMP)";
        }

        if($submit === 'modifier_r_cmp'){
            $response = "Demande de cotation (Transmis Responsable DMP)";
        }

        if($submit === 'transferer_r_cmp'){
            $response = "Transmis pour cotation";
        }
        return $response;
    }

    public function procedureSetEchantillon($data){
        $set_echantillon = null;
        $find_echantillon_flag = null;
        if(isset($data['echantillon_flag'])){
            foreach ($data['echantillon_flag'] as $item => $value) {
                if($data['detail_demande_cotations_id'] === (int) $value){
                    $find_echantillon_flag = 1;
                }
            }
        }
        if($find_echantillon_flag === null){
            $echantillon = null;
            $set_echantillon = 1;
        }
        $type_detail_operations_libelle = 'Detail demande de cotation';
        if ($data['detail_demande_cotation'] != null && isset($data['echantillon'])) {
            $echantillon =  $data['echantillon']->store('echantillonnage','public');
            $set_echantillon = 1;
        }

        if($set_echantillon === 1 && $data['detail_demande_cotation'] != null){
            $dataSetEchantillon = [
                'echantillon'=>$echantillon,
                'detail_operations_id'=>$data['detail_demande_cotation']->id,
                'type_detail_operations_libelle'=>$type_detail_operations_libelle,
            ];
            $this->setEchantillon($dataSetEchantillon);
        }
    }
    function getPeriodeById($periodes_id){
        return Periode::where('id',$periodes_id)->first();
    }

}

