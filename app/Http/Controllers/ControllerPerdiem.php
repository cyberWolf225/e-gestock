<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\SendEmail;
use App\Models\Perdiem;
use App\Models\Travaux;
use App\Models\Commande;
use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use App\Models\DetailPerdiem;
use App\Models\StatutPerdiem;
use App\Models\SignatairePerdiem;
use App\Models\TypeStatutPerdiem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\StatutSignatairePerdiem;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ControllerPerdiem extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function procedureStoreSignatairePerdiemParValidateur($data){
        $info_user_connect = $this->getInfoUserConnect($data['profils_id'],$data['users_id']);
        if ($info_user_connect != null) {
            $profil_fonction = $this->getProfilFonctionByAgentId($info_user_connect->agents_id);
            if($profil_fonction != null){

                $type_statut_signataires_libelle = "Activé";
                $data2 = [
                    'profil_fonctions_id'=>$profil_fonction->id,
                    'perdiems_id'=>$data['perdiems_id'],
                    'profils_id'=>$data['profils_id'],
                    'type_statut_signataires_libelle'=>$type_statut_signataires_libelle,
                ];

                $this->storeSignatairePerdiem2($data2);
            }
        }
    }
    function notifPerdiems($subject,$perdiems_id,$type_profils_names){                    
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
        ->whereIn('st.ref_depot',function($query) use($perdiems_id){
            $query->select(DB::raw('stt.ref_depot'))
                ->from('perdiems as p')
                ->join('structures as stt', 'stt.code_structure', '=', 'p.code_structure')
                ->where('p.id',$perdiems_id)
                ->whereRaw('st.ref_depot = stt.ref_depot');
        })
        ->get();
        foreach ($profils as $profil) {
            $this->notifPerdiem($profil->email,$subject,$perdiems_id);
        }
    }
    function notifPerdiem($email,$subject,$perdiems_id){
        $details = [
            'email' => $email,
            'subject' => $subject,
            'perdiems_id' => $perdiems_id,
            'link' => URL::to('/'),
        ];
        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
        dispatch($emailJob);
    }
    function procedureComptabilisationEcriture($data_all){
        $controllerTravaux = new ControllerTravaux();
        $data = null;

        $user = $this->getInfoUserByProfilId($data_all['profils_id']);
        if($user != null){
            
            if($data_all['type_operations_libelle'] === 'Perdiems'){
                $perdiem = $this->getPerdiem($data_all['operations_id']);
                if($perdiem != null){

                    $reference_piece = $perdiem->num_pdm;
                    $compte = $perdiem->ref_fam;
                    $exercice = $perdiem->exercice;
                    $code_gestion = $perdiem->code_gestion;
                    $montant = $perdiem->montant_total * $data_all['signe'];
                    $date_transaction = date('Y-m-d H:i:s');
                    $mle = $user->mle;
                    $code_structure = $perdiem->code_structure;
                    $code_section = $perdiem->code_structure."01";
        
                    $data = [
                        'type_piece'=>$data_all['type_piece'],
                        'reference_piece'=>$reference_piece,
                        'compte'=>$compte,
                        'montant'=>$montant,
                        'date_transaction'=>$date_transaction,
                        'mle'=>$mle,
                        'code_structure'=>$code_structure,
                        'code_section'=>$code_section,
                        'ref_depot'=>$user->ref_depot,
                        'acompte'=>$data_all['flag_acompte'],
                        'exercice'=>$exercice,
                        'code_gestion'=>$code_gestion,
                    ];                
                }
            }

            if($data_all['type_operations_libelle'] === 'Commande non stockable'){

                $travaux = $controllerTravaux->getTravauxById($data_all['operations_id']);
                if($travaux != null){

                    $reference_piece = $travaux->num_bc;
                    $compte = $travaux->ref_fam;
                    $exercice = $travaux->exercice;
                    $code_gestion = $travaux->code_gestion;

                    $montant = ($travaux->net_a_payer * $travaux->taux_de_change) * $data_all['signe'];
    
                    if($travaux->montant_acompte != null){
                        $montant = (($travaux->net_a_payer * $travaux->taux_de_change) - ($travaux->montant_acompte * $travaux->taux_de_change)) * $data_all['signe'];
                    }

                    if($data_all['flag_acompte'] === 1){
                        $montant = ($travaux->montant_acompte * $travaux->taux_de_change) * $data_all['signe'];
                    }

                    $date_transaction = date('Y-m-d H:i:s');
                    $mle = $user->mle;
                    $code_structure = $travaux->code_structure;
                    $code_section = $travaux->code_structure."01";
        
                    $data = [
                        'type_piece'=>$data_all['type_piece'],
                        'reference_piece'=>$reference_piece,
                        'compte'=>$compte,
                        'montant'=>$montant,
                        'date_transaction'=>$date_transaction,
                        'mle'=>$mle,
                        'code_structure'=>$code_structure,
                        'code_section'=>$code_section,
                        'ref_depot'=>$user->ref_depot,
                        'acompte'=>$data_all['flag_acompte'],
                        'exercice'=>$exercice,
                        'code_gestion'=>$code_gestion,
                    ];
                }

            }

            if($data_all['type_operations_libelle'] === "Demande d'achats"){

                $demandeAchat = $this->getDetailCotationFournisseur($data_all['operations_id'],$data_all['cotation_fournisseurs_id']);

                if($demandeAchat != null){

                    $reference_piece = $demandeAchat->num_bc;
                    $compte = $demandeAchat->ref_fam;
                    $exercice = $demandeAchat->exercice;
                    $code_gestion = $demandeAchat->code_gestion;

                    $montant = ($demandeAchat->net_a_payer * $demandeAchat->taux_de_change) * $data_all['signe'];
    
                    if($demandeAchat->montant_acompte != null){
                        $montant = (($demandeAchat->net_a_payer * $demandeAchat->taux_de_change) - ($demandeAchat->montant_acompte * $demandeAchat->taux_de_change)) * $data_all['signe'];
                    }

                    if($data_all['flag_acompte'] === 1){
                        $montant = ($demandeAchat->montant_acompte * $demandeAchat->taux_de_change) * $data_all['signe'];
                    }

                    $date_transaction = date('Y-m-d H:i:s');
                    $mle = $user->mle;
                    $code_structure = $demandeAchat->code_structure;
                    $code_section = $demandeAchat->code_structure."01";
        
                    $data = [
                        'type_piece'=>$data_all['type_piece'],
                        'reference_piece'=>$reference_piece,
                        'compte'=>$compte,
                        'montant'=>$montant,
                        'date_transaction'=>$date_transaction,
                        'mle'=>$mle,
                        'code_structure'=>$code_structure,
                        'code_section'=>$code_section,
                        'ref_depot'=>$demandeAchat->ref_depot,
                        'acompte'=>$data_all['flag_acompte'],
                        'exercice'=>$exercice,
                        'code_gestion'=>$code_gestion,
                    ];
                }

            }
            
        }

        return $data;
    }
    public function storeSignatairePerdiem2($data){
        $flag_actif = 0;
        if($data['type_statut_signataires_libelle'] === 'Activé'){
            $flag_actif = 1;
        }
        
        $type_statut_sign_id = $this->getTypeStatutSignataire($data['type_statut_signataires_libelle']);

        if (isset($data['profil_fonctions_id'])) {

            
            $signataire = SignatairePerdiem::where('profil_fonctions_id',$data['profil_fonctions_id'])
            ->where('perdiems_id',$data['perdiems_id'])
            ->where('flag_actif',$flag_actif)
            ->first();
            if ($signataire === null) {

                $signataire_perdiems_id = SignatairePerdiem::create([
                    'profil_fonctions_id'=>$data['profil_fonctions_id'],
                    'perdiems_id'=>$data['perdiems_id'],
                ])->id;

                StatutSignatairePerdiem::create([
                    'signataire_perdiems_id'=>$signataire_perdiems_id,
                    'type_statut_sign_id'=>$type_statut_sign_id,
                    'profils_id'=>$data['profils_id'],
                    'date_debut'=>date('Y-m-d'),
                ]);
            }           

        }
    }
    public function verificationSignataire($signataires,$profil_fonctions){
        $array_signataire = [];
        foreach ($signataires as $signataire) {
            $array_signataire[] = $signataire->profil_fonctions_id.'';
        }
        
        $result1=array_diff($array_signataire,$profil_fonctions);
        $result2=array_diff($profil_fonctions,$array_signataire);


        return count(array_merge($result1, $result2));
    }    
    function getPerdiems($type_profils_name,$code_structure,$libelle){

        $perdiems = [];

        if($type_profils_name === "Gestionnaire des achats" or $type_profils_name === "Responsable des achats" or $type_profils_name === "Responsable DMP" or $type_profils_name === "Responsable contrôle budgetaire" or $type_profils_name === "Chef Département DCG" or $type_profils_name === "Responsable DCG" or $type_profils_name === "Directeur Général Adjoint" or $type_profils_name === "Responsable DFC" or $type_profils_name === "Directeur Général"){
            $perdiems = DB::table('perdiems as p')
            ->join('familles as f', 'f.ref_fam', '=', 'p.ref_fam')
            ->join('structures as s', 's.code_structure', '=', 'p.code_structure')
            ->select('f.ref_fam', 'f.design_fam', 'p.num_or', 'p.libelle', 'p.id', 'p.montant_total', 'p.solde_avant_op','p.code_gestion','p.code_structure','p.exercice','s.nom_structure','p.num_pdm')
            ->orderByDesc('p.id')
            ->whereIn('p.id', function ($query) use ($libelle) {
                $query->select(DB::raw('sp.perdiems_id'))
                    ->from('statut_perdiems as sp')
                    ->join('type_statut_perdiems as tsp', 'tsp.id', '=', 'sp.type_statut_perdiems_id')
                    ->whereIn('tsp.libelle', [$libelle])
                    ->whereRaw('p.id = sp.perdiems_id');
            })
            ->whereIn('s.ref_depot', function ($query) {
                $query->select(DB::raw('sst.ref_depot'))
                    ->from('agent_sections as ase')
                    ->join('sections as ss', 'ss.id', '=', 'ase.sections_id')
                    ->join('structures as sst', 'sst.code_structure', '=', 'ss.code_structure')
                    ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                    ->where('tsas.libelle', 'Activé')
                    ->where('ase.agents_id', auth()->user()->agents_id)
                    ->whereRaw('s.ref_depot = sst.ref_depot');
            })
            ->get();
        }
        
        return $perdiems;
    }
    public function storePerdiem($num_pdm,$libelle,$num_or,$code_gestion,$exercice,$ref_fam,$code_structure,$solde_avant_op,$credit_budgetaires_id,$montant_total){
        $perdiem = Perdiem::create([
            'num_pdm'=>$num_pdm,
            'libelle'=>$libelle,
            'num_or'=>$num_or,
            'code_gestion'=>$code_gestion,
            'exercice'=>$exercice,
            'ref_fam'=>$ref_fam,
            'code_structure'=>$code_structure,
            'solde_avant_op'=>$solde_avant_op,
            'credit_budgetaires_id'=>$credit_budgetaires_id,
            'montant_total'=>$montant_total,
        ]);

        return $perdiem;
    }
    public function setPerdiem($num_pdm,$perdiems_id,$libelle,$num_or,$code_gestion,$exercice,$ref_fam,$code_structure,$solde_avant_op,$credit_budgetaires_id,$montant_total){
        return Perdiem::where('id',$perdiems_id)
        ->update([
            'libelle'=>$libelle,
            'num_pdm'=>$num_pdm,
            'code_gestion'=>$code_gestion,
            // 'exercice'=>$exercice,
            'ref_fam'=>$ref_fam,
            'code_structure'=>$code_structure,
            'solde_avant_op'=>$solde_avant_op,
            'credit_budgetaires_id'=>$credit_budgetaires_id,
            'montant_total'=>$montant_total,
        ]);
    }
    public function storeDetailPerdiem($perdiems_id,$nom_prenoms,$montant,$piece,$piece_name){
        $detail_perdiem = DetailPerdiem::create([
            'nom_prenoms'=>$nom_prenoms,
            'montant'=>$montant,
            'piece'=>$piece,
            'piece_name'=>$piece_name,
            'perdiems_id'=>$perdiems_id,
        ]);

        return $detail_perdiem;
    }
    public function getTypeStatutPerdiem($libelle){
        $type_statut_perdiem = TypeStatutPerdiem::where('libelle',$libelle)->first();

        return $type_statut_perdiem;
    }
    public function storeTypeStatutPerdiem($libelle){
        $type_statut_perdiem = TypeStatutPerdiem::where('libelle',$libelle)->first();
        if ($type_statut_perdiem === null) {
            TypeStatutPerdiem::create([
                'libelle'=>$libelle
            ]);
        }
    }
    public function storeStatutPerdiem($perdiems_id,$type_statut_perdiems_id,$date_debut,$date_fin,$profils_id,$commentaire){
        StatutPerdiem::create([
            'perdiems_id'=>$perdiems_id,
            'type_statut_perdiems_id'=>$type_statut_perdiems_id,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
            'profils_id'=>$profils_id,
            'commentaire'=>$commentaire,
        ]);
    }
    public function getDetailPerdiems($perdiems_id){
        $detail_perdiems = DB::table('perdiems as p')
                        ->join('detail_perdiems as dp','dp.perdiems_id','=','p.id')
                        ->where('p.id',$perdiems_id)
                        ->select('p.*','dp.*')
                        ->get();

        return $detail_perdiems;
    }
    public function getPerdiem($perdiems_id){
        return DB::table('perdiems as p')
        ->join('familles as f','f.ref_fam','=','p.ref_fam')
        ->join('gestions as g','g.code_gestion','=','p.code_gestion')
        ->join('structures as s','s.code_structure','=','p.code_structure')
        ->join('credit_budgetaires as cb','cb.id','=','p.credit_budgetaires_id')
        ->select('g.*','cb.*','s.*','f.*','p.*')
        ->where('p.id',$perdiems_id)
        ->first();
    }
    public function deleteDetailPerdiemByArrayNotIn($perdiems_id,$detail_perdiems_id_array){
        DB::table('detail_perdiems')->whereNotIn('id',$detail_perdiems_id_array)
        ->where('perdiems_id',$perdiems_id)
        ->delete();
    }
    public function setDetailPerdiem($detail_perdiems_id,$perdiems_id,$nom_prenoms,$montant,$piece,$piece_name){

        $detail_perdiem = DetailPerdiem::where('id',$detail_perdiems_id)
        ->where('perdiems_id',$perdiems_id)
        ->update([
            'nom_prenoms'=>$nom_prenoms,
            'montant'=>$montant,
            'piece'=>$piece,
            'piece_name'=>$piece_name
        ]);

        return $detail_perdiem;

    }
    public function getAgentFonctionsSignatairePerdiem($perdiems_id){
            
        $signataires = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
        ->join('signataire_perdiems as sp','sp.profil_fonctions_id','=','pf.id')
        ->join('fonctions as f','f.id','=','pf.fonctions_id')
        ->where('sp.flag_actif',1)
        ->where('u.flag_actif',1)
        ->where('pf.flag_actif',1)
        ->where('sp.perdiems_id',$perdiems_id)
        ->select(DB::raw('a.mle'),DB::raw('a.nom_prenoms'),DB::raw('f.libelle'),DB::raw('sp.profil_fonctions_id'))
        ->groupBy('f.libelle','a.mle','a.nom_prenoms','sp.profil_fonctions_id')
        ->get();

        return $signataires;
    }
    public function storeSignatairePerdiem($request,$perdiems_id,$profils_id){
        
        $donnees = [];       
        
        if (isset($request->profil_fonctions_id)) {
    
            if (count($request->profil_fonctions_id) > 0) {
    
                foreach ($request->profil_fonctions_id as $item => $value) {
    
                    $donnees[$item] =  $request->profil_fonctions_id[$item];
    
                }
    
            }
    
        }
    
        $signataire_perdiem = DB::table('signataire_perdiems as sp')
        ->where('sp.flag_actif',1)
        ->where('sp.perdiems_id',$perdiems_id)
        ->first();
        if ($signataire_perdiem!=null) {
    
            $signataire_perdiem_listes = DB::table('signataire_perdiems as sp')
            ->where('sp.flag_actif',1)
            ->where('sp.perdiems_id',$perdiems_id)
            ->whereNotIn('sp.profil_fonctions_id',$donnees)
            ->get();
            foreach ($signataire_perdiem_listes as $signataire_perdiem_liste) {
    
                $libelle = 'Désactivé';
                $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);
    
                SignatairePerdiem::where('id',$signataire_perdiem_liste->id)
                ->update([
                    'profil_fonctions_id'=>$signataire_perdiem_liste->profil_fonctions_id,
                    'perdiems_id'=>$perdiems_id,
                    'flag_actif'=>0,
                ]);
    
                StatutSignatairePerdiem::create([
                    'signataire_perdiems_id'=>$signataire_perdiem_liste->id,
                    'type_statut_sign_id'=>$type_statut_sign_id,
                    'profils_id'=>$profils_id,
                    'date_debut'=>date('Y-m-d'),
                ]);
    
            }
    
            $libelle = 'Activé';
            $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);
    
            if (isset($request->profil_fonctions_id)) {
                if (count($request->profil_fonctions_id) > 0) {
    
                    foreach ($request->profil_fonctions_id as $item => $value) {
    
                        $signataire = SignatairePerdiem::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('perdiems_id',$perdiems_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {
    
                            $signataire_perdiems_id = SignatairePerdiem::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'perdiems_id'=>$perdiems_id,
                            ])->id;
    
                            StatutSignatairePerdiem::create([
                                'signataire_perdiems_id'=>$signataire_perdiems_id,
                                'type_statut_sign_id'=>$type_statut_sign_id,
                                'profils_id'=>$profils_id,
                                'date_debut'=>date('Y-m-d'),
                            ]);
                        }
    
                        
    
                        
                    }
    
                }
            }
    
        }else{
    
            $libelle = 'Activé';
            $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);
    
            if (isset($request->profil_fonctions_id)) {
                if (count($request->profil_fonctions_id) > 0) {
    
                    foreach ($request->profil_fonctions_id as $item => $value) {
    
                        $signataire = SignatairePerdiem::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('perdiems_id',$perdiems_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {
    
                            $signataire_perdiems_id = SignatairePerdiem::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'perdiems_id'=>$perdiems_id,
                            ])->id;
    
                            StatutSignatairePerdiem::create([
                                'signataire_perdiems_id'=>$signataire_perdiems_id,
                                'type_statut_sign_id'=>$type_statut_sign_id,
                                'profils_id'=>$profils_id,
                                'date_debut'=>date('Y-m-d'),
                            ]);
                        }
                    }
    
                }
            }
    
        }
    }
    public function setDetailPerdiem2($detail_perdiems_id,$perdiems_id,$nom_prenoms,$montant){
        
        $detail_perdiem = DetailPerdiem::where('id',$detail_perdiems_id)
        ->where('perdiems_id',$perdiems_id)
        ->update([
            'nom_prenoms'=>$nom_prenoms,
            'montant'=>$montant
        ]);

        return $detail_perdiem;

    }
    public function getLastStatutPerdiem($perdiems_id){

        $statut_perdiem_last = DB::table('statut_perdiems as sp')
            ->join('type_statut_perdiems as tsp','tsp.id','=','sp.type_statut_perdiems_id')
            ->join('profils as p','p.id','=','sp.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sp.perdiems_id',$perdiems_id)
            ->orderByDesc('sp.id')
            ->limit(1)
            ->first();

        return $statut_perdiem_last;
        
    }
    public function setLastStatutPerdiem($perdiems_id){
        $statut_perdiem = StatutPerdiem::where('perdiems_id',$perdiems_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_perdiem != null) {
            StatutPerdiem::where('id',$statut_perdiem->id)->update([
                'date_fin'=>date('Y-m-d'),
            ]);
        }
    }
    public function getLastNumPdm($exercice,$code_structure,$perdiems_id=null){

        $sequence_id = 1;

        if(isset($perdiems_id)){
            $perdiem = Perdiem::join('credit_budgetaires as cb','cb.id','=','perdiems.credit_budgetaires_id')
            ->where('perdiems.id', $perdiems_id)
            ->select('perdiems.num_pdm')
            ->limit(1)
            ->first();
    
            if($perdiem != null){
    
                $num_pdm = $perdiem->num_pdm;
                
                if(isset(explode('PDM', $num_pdm)[1])){
                    $sequence_id =  (int) explode('PDM', $num_pdm)[1];                     
                }
                  
            }
        }else{
            $perdiem = Perdiem::join('credit_budgetaires as cb','cb.id','=','perdiems.credit_budgetaires_id')
            ->where('perdiems.exercice', $exercice)
            ->orderByDesc('perdiems.id')
            ->select('perdiems.num_pdm')
            ->limit(1)
            ->first();
    
            if($perdiem != null){
    
                $num_pdm = $perdiem->num_pdm;
                
                if(isset(explode('PDM', $num_pdm)[1])){
                    $sequence_id =  (int) explode('PDM', $num_pdm)[1] + 1;                     
                }
                  
    
            }

        }

        

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'PDM';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);


    }
    public function publicPath($data){
        $path = 'storage/documents/' . $data['type_operations_libelle'] . '/' . $data['reference'] . '.' . $data['extension'];
        return str_replace("/","\\",public_path($path));
    }
    public function setSoldeAvantOperation($data){

        if(isset($data['type_operations_libelle']) && isset($data['operations_id']) && isset($data['flag_engagement'])){

            $solde_avant_op = (int) $data['solde_avant_op'];

            if($data['type_operations_libelle'] === 'Perdiems'){
                Perdiem::where('id',$data['operations_id'])
                ->update([
                    'solde_avant_op'=>$solde_avant_op,
                    'flag_engagement'=>$data['flag_engagement'],
                ]);
            }

            if($data['type_operations_libelle'] === 'Commande non stockable'){
                Travaux::where('id',$data['operations_id'])
                ->update([
                    'solde_avant_op'=>$solde_avant_op,
                    'flag_engagement'=>$data['flag_engagement'],
                ]);
            }

            if($data['type_operations_libelle'] === "Demande d'achats"){

                $solde_avant_op = $data['solde_avant_op'];

                Commande::where('demande_achats_id',$data['operations_id'])
                ->update([
                    'solde_avant_op'=>$solde_avant_op,
                    'solde_apres_op'=>null,
                    
                ]);

                DemandeAchat::where('id',$data['operations_id'])
                ->update([
                    'flag_engagement'=>$data['flag_engagement'],
                ]);
            }

        }

    }


}
