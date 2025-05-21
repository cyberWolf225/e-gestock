<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\SendEmail;
use App\Models\Service;
use App\Models\Travaux;
use App\Models\PieceJointe;
use Illuminate\Http\Request;
use App\Models\DetailTravaux;
use App\Models\StatutTravaux;
use App\Models\TypeOperation;
use App\Models\SignataireTravaux;
use App\Models\TypeStatutTravaux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\TypeStatutSignataire;
use App\Models\StatutSignataireTravaux;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ControllerTravaux extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function storeSessionBackground($request){
        $request->session()->put('backgroundImage','container-infographie3');
    }
    public function getTypeProfilName($profils_id){
        $type_profils_name = null;
        $type_profil = DB::table('type_profils as tp')
        ->join('profils as p', 'p.type_profils_id', '=', 'tp.id')
        ->where('p.id',$profils_id)
        ->first();

        if ($type_profil!=null) {
            $type_profils_name = $type_profil->name;
        }

        return $type_profils_name;
    }
    public function controlAcces($type_profils_name,$etape,$users_id,$request=null){
        $profils = null;
        if ($etape === "index") {
            $profils = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();

        }elseif ($etape === "create" or $etape === "store") {

            $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Gestionnaire des achats')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('tsase.libelle','Activé')
                ->where('p.id',Session::get('profils_id'))
                ->where('u.flag_actif',1)
                ->first();
                
        }elseif ($etape === "edit" or $etape === "update") {

            $profils = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->where('tp.name', $type_profils_name)
            ->limit(1)
            ->select('p.id', 'se.code_section', 's.ref_depot')
            ->where('p.flag_actif',1)
            ->where('p.id',Session::get('profils_id'))
            ->where('tsase.libelle','Activé')
            ->whereIn('s.ref_depot',function($query) use($request){
                $query->select(DB::raw('da.ref_depot'))
                        ->from('travauxes as da')
                        ->where('da.id',$request)
                        ->whereRaw('da.ref_depot = s.ref_depot');
            })
            ->first();
            
            
        }elseif ($etape === "show") {

            $profils = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', ['Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception','Administrateur fonctionnel','Pilote AEE','Agent Cnps'])
            ->limit(1)
            ->select('p.id', 'se.code_section','s.ref_depot')
            ->where('p.flag_actif',1)
            ->where('p.id',Session::get('profils_id'))
            ->where('tsase.libelle','Activé')
            ->whereIn('s.ref_depot',function($query) use($request){
                $query->select(DB::raw('da.ref_depot'))
                        ->from('travauxes as da')
                        ->where('da.id',$request)
                        ->whereRaw('da.ref_depot = s.ref_depot');
            })
            ->first();
            
            
        }elseif ($etape === "cotation" or $etape === "cotation_store") {

            $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->whereIn('tp.name', ['Responsable des achats','Responsable DMP'])
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.ref_depot',function($query) use($request){
                    $query->select(DB::raw('da.ref_depot'))
                            ->from('travauxes as da')
                            ->where('da.id',$request)
                            ->whereRaw('da.ref_depot = s.ref_depot');
                })
                ->first();
            
            
        }
        return $profils;
    }
    public function structure_connectee(){
        $code_structure = null;

        $structure = DB::table('sections as s')
            ->join('agent_sections as ase','ase.sections_id','=','s.id')
            ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
            ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
            ->join('structures as st','st.code_structure','=','s.code_structure')
            ->where('ase.agents_id',auth()->user()->agents_id)
            ->where('tsas.libelle','Activé')
            ->first();
        
        if ($structure!=null) {
            $code_structure = $structure->code_structure;
        }

        return $code_structure;

    }
    public function getOrganisationsTravaux($ref_fam,$ref_depot){
            
        $organisations = DB::table('organisations as o')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->join('organisation_articles as oa','oa.organisations_id','=','o.id')
        ->join('organisation_depots as od','od.organisations_id','=','o.id')
        ->where('tso.libelle','Activé')
        ->where('oa.flag_actif',1)
        ->where('od.flag_actif',1)
        ->where('oa.ref_fam',$ref_fam)
        ->where('od.ref_depot',$ref_depot)
        ->select('o.id','o.denomination')
        ->get();
        return $organisations;
    }
    public function storeDetailTravaux($request,$travauxes_id){
        if(isset($request->libelle_service)){
            if (count($request->libelle_service) > 0) {
                foreach ($request->libelle_service as $item => $value) {
                    if (isset($request->libelle_service[$item]) && isset($request->qte[$item]) && isset($request->prix_unit[$item]) && isset($request->montant_ht[$item])) {
                        
                        $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $qte[$item] = $qte[$item] * 1;
                        } catch (\Throwable $th) {
                            return redirect()->back()->with('error','Quantité : Une valeur non numérique rencontrée.');
                        }
                        
                        if (gettype($qte[$item])!='integer') {
                            return redirect()->back()->with('error','Quantité : Une valeur non numérique rencontrée.');
                        }

                        if ($qte[$item] <= 0) {
                            return redirect()->back()->with('error','Veuillez saisir une quantité valide');
                        }

                        

                        $prix_unit[$item] = filter_var($request->prix_unit[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                    
                        $info = 'Prix unitaire';
                        $error = $this->setDecimal($prix_unit[$item],$info);

                        if (isset($error)) {
                            return redirect()->back()->with('error',$error);
                        }else{
                            $prix_unit[$item] = $prix_unit[$item] * 1;
                        }
                        

                        if ($prix_unit[$item] <= 0) {
                            return redirect()->back()->with('error','Veuillez saisir un prix unitaire valide');
                        }
                        
                        if (isset($request->remise[$item])) {


                            $remise[$item] = filter_var($request->remise[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                            $info = 'Remise';
                            $error = $this->setDecimal($remise[$item],$info);

                            if (isset($error)) {
                                return redirect()->back()->with('error',$error);
                            }else{
                                $remise[$item] = $remise[$item] * 1;
                            }

                            if ($remise[$item] < 0) {
                                return redirect()->back()->with('error', 'Veuillez saisir un remise valide');
                            }

                        }else{
                            $remise[$item] = 0;
                        }

                        $montant_ht[$item] = filter_var($request->montant_ht[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                        $info = 'Montant ht';
                        $error = $this->setDecimal($montant_ht[$item],$info);

                        if (isset($error)) {
                            return redirect()->back()->with('error',$error);
                        }else{
                            $montant_ht[$item] = $montant_ht[$item] * 1;
                        }

                        if (isset($request->tva)) {
                            if($request->tva === null){
                                $taux_tva = 1;
                            }else{
                                $taux_tva = 1 + ($request->tva/100);
                            }
                        }else{
                            $taux_tva = 1;
                        }

                        $montant_ttc[$item] = $montant_ht[$item]*$taux_tva;
                        $libelle_service[$item] = trim($request->libelle_service[$item]);

                        $service = Service::where('libelle',$libelle_service[$item])->first();
                        if ($service!=null) {
                            $services_id = $service->id;
                        }else{
                            $services_id = Service::create([
                                'libelle'=>$libelle_service[$item]
                            ])->id;
                        }

                        $data1 = [
                            'travauxes_id'=>$travauxes_id,
                            'services_id'=>$services_id,
                            'qte'=>$qte[$item],
                            'prix_unit'=>$prix_unit[$item],
                            'remise'=>$remise[$item],
                            'montant_ht'=>$montant_ht[$item],
                            'montant_ttc'=>$montant_ttc[$item],
                        ];

                        DetailTravaux::create($data1);
                    }else{
                        return redirect()->back()->with('error','Veuillez saisir l\'ensemble des champs pour chaque service');
                    }
                }
            }
        }
    }
    public function validate_saisie($request){
        $error = null;
        if(isset($request->libelle_service)){
            if (count($request->libelle_service) > 0) {
                foreach ($request->libelle_service as $item => $value) {
                    if (isset($request->libelle_service[$item]) && isset($request->qte[$item]) && isset($request->prix_unit[$item]) && isset($request->montant_ht[$item])) {
                        
                        $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $qte[$item] = $qte[$item] * 1;
                        } catch (\Throwable $th) {
                            $error = "Quantité : Une valeur non numérique rencontrée.";
                        }
                        
                        if (gettype($qte[$item])!='integer') {
                            $error = "Quantité : Une valeur non numérique rencontrée.";
                        }

                        if ($qte[$item] <= 0) {
                            $error = "Veuillez saisir une quantité valide";
                        }

                        

                        $prix_unit[$item] = filter_var($request->prix_unit[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                        $info = 'Prix unitaire';
                        $error = $this->setDecimal($prix_unit[$item],$info);

                        if (isset($error)) {
                            return redirect()->back()->with('error',$error);
                        }else{
                            $prix_unit[$item] = $prix_unit[$item] * 1;
                        }
                        

                        if ($prix_unit[$item] <= 0) {
                            $error = "Veuillez saisir un prix unitaire valide";
                        }


                        if(isset($request->remise[$item])){

                            $remise[$item] = filter_var($request->remise[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                            $info = 'Remise';
                            $error = $this->setDecimal($remise[$item],$info);

                            if (isset($error)) {
                                return redirect()->back()->with('error',$error);
                            }else{
                                $remise[$item] = $remise[$item] * 1;
                            }
                    

                            if ($remise[$item] < 0) {
                                $error = "Veuillez saisir un remise valide";

                            }
                        }

                            
                        

                        $montant_ht[$item] = filter_var($request->montant_ht[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                        $info = 'Montant ht';
                        $error = $this->setDecimal($montant_ht[$item],$info);

                        if (isset($error)) {
                            return redirect()->back()->with('error',$error);
                        }else{
                            $montant_ht[$item] = $montant_ht[$item] * 1;
                        }

                    }else{
                        $error = "Veuillez saisir l'ensemble des champs pour chaque service";

                    }
                }
            }
        }else{
            $error = "Veuillez saisir au moins une ligne de devis";
        }

        return $error;
    }
    public function setInt($saisie,$info){
        $error = null;

        try {
            $saisie = $saisie * 1;
        } catch (\Throwable $th) {
            $error = $info." : Une valeur non numérique rencontrée.";
        }
        
        if (gettype($saisie)!='integer') {
            $error = $info." : Une valeur non numérique rencontrée.";
        }

        return $error;
    }
    
    public function data($libelle,$type_profils_name,$libelle2=null){

        
        $travauxes = [];
        if (isset($type_profils_name)) {
            

                $travauxes = Travaux::select('travauxes.id as id', 'travauxes.num_bc as num_bc', 'travauxes.updated_at as updated_at', 'travauxes.intitule as intitule')
                ->orderByDesc('travauxes.updated_at')
                ->whereIn('travauxes.id', function ($query) use($libelle) {
                    $query->select(DB::raw('sda.travauxes_id'))
                            ->from('statut_travauxes as sda')
                            ->join('type_statut_travauxes as tsda', 'tsda.id', '=', 'sda.type_statut_travauxes_id')
                            ->where('tsda.libelle', [$libelle])
                            ->whereRaw('travauxes.id = sda.travauxes_id');
                })
                ->whereIn('travauxes.ref_depot',function($query) use($type_profils_name){
                    $query->select(DB::raw('st.ref_depot'))
                    ->from('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u', 'u.id', '=', 'p.users_id')
                    ->join('agents as a', 'a.id', '=', 'u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('sections as s', 's.id', '=', 'ase.sections_id')
                    ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->where('tp.name', $type_profils_name)
                    ->where('p.flag_actif',1)
                    ->where('tsase.libelle','Activé')
                    ->where('u.id',auth()->user()->id)
                    ->whereRaw('travauxes.ref_depot = st.ref_depot');
                })
                ->get();            
        }

        

        return $travauxes;

    } 

    public function sendMailUsers($subject,$travauxes_id,$type_profils_names){
                    
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
        ->whereIn('st.ref_depot',function($query) use($travauxes_id){
            $query->select(DB::raw('da.ref_depot'))
                ->from('travauxes as da')
                ->where('da.id',$travauxes_id)
                ->whereRaw('st.ref_depot = da.ref_depot');
        })
        ->get();

        foreach ($profils as $profil) {

            $details = [
                'email' => $profil->email,
                'subject' => $subject,
                'travauxes_id' => $travauxes_id,
                'link' => URL::to('/'),
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);

        }
    }    

    public function getTypeStatutSignataire($libelle){

        $type_statut_sign_id = null;
        $type_statut_signataire = TypeStatutSignataire::where('libelle',$libelle)
        ->first();
        if ($type_statut_signataire!=null) {
            $type_statut_sign_id = $type_statut_signataire->id;
        }else{
            $type_statut_sign_id = TypeStatutSignataire::create([
                'libelle'=>$libelle
            ])->id;
        }

        return $type_statut_sign_id;
        

    }

    public function piece_jointe($travauxes_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id){

        $type_operation = TypeOperation::where('libelle',$libelle)->first();
        if ($type_operation!=null) {
            $type_operations_id = $type_operation->id;
        }else{
            $type_operations_id = TypeOperation::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($piece_jointes_id)) {
            PieceJointe::where('id',$piece_jointes_id)->update([
                'profils_id'=>$profils_id,
                'flag_actif'=>$flag_actif
            ]);
        }else{
            PieceJointe::create([
                'type_operations_id'=>$type_operations_id,
                'profils_id'=>$profils_id,
                'subject_id'=>$travauxes_id,
                'name'=>$name,
                'piece'=>$piece,
                'flag_actif'=>$flag_actif
    
            ]);
        }
        
    }

    public function getPieceJointesTravaux($travauxes_id){

        $piece_jointes = DB::table('piece_jointes as pj')
        ->join('type_operations as to','to.id','=','pj.type_operations_id')
        ->where('to.libelle','Commande non stockable')
        ->where('pj.subject_id',$travauxes_id)
        ->select('pj.id','pj.piece','pj.name','pj.flag_actif')
        ->where('flag_actif',1)
        ->get();

        return $piece_jointes;
    }

    public function getTravauxById($travauxes_id){
        return DB::table('travauxes as t')
        ->join('structures as s', 's.code_structure', '=', 't.code_structure')
        ->join('gestions as g', 'g.code_gestion', '=', 't.code_gestion')
        ->join('organisations as o', 'o.id', '=', 't.organisations_id')
        ->join('devises as d', 'd.id', '=', 't.devises_id')
        ->join('periodes as p', 'p.id', '=', 't.periodes_id')
        ->join('familles as f', 'f.ref_fam', '=', 't.ref_fam')
        ->select('t.id as travauxes_id', 't.num_bc', 't.intitule', 't.ref_fam', 't.code_structure', 't.ref_depot', 't.code_gestion', 't.exercice', 't.montant_total_brut', 't.taux_remise_generale','t.remise_generale', 't.montant_total_net', 't.tva', 't.montant_total_ttc', 't.net_a_payer', 't.acompte', 't.taux_acompte', 't.montant_acompte', 't.delai', 't.date_echeance', 's.nom_structure', 'f.design_fam', 't.credit_budgetaires_id', 'p.libelle_periode', 'o.id as organisations_id', 'o.denomination', 'o.entnum', 'd.code', 'd.libelle as devises_libelle', 't.created_at', 'p.valeur', 't.date_livraison_prevue', 't.date_retrait','g.libelle_gestion','t.solde_avant_op','t.taux_de_change','t.flag_engagement')
        ->where('t.id', $travauxes_id)
        ->first(); 
    }

    public function getTravauxCreditBudgetaireById($travauxes_id){
        return DB::table('travauxes as t')
        ->join('credit_budgetaires as cb', 'cb.id', '=', 't.credit_budgetaires_id')
        ->where('t.id', $travauxes_id)
        ->first(); 
    }
    

    public function getDetailTravauxById($travauxes_id){
        return DB::table('travauxes as t')
        ->join('detail_travauxes as dt', 'dt.travauxes_id', '=', 't.id')
        ->join('services as s', 's.id', '=', 'dt.services_id')
        ->select('s.libelle', 'dt.qte', 'dt.prix_unit', 'dt.remise', 'dt.montant_ht', 'dt.montant_ttc')
        ->where('t.id', $travauxes_id)
        ->where('dt.flag_valide', 1)
        ->get();
    }

    public function getTravauxByNumBc($num_bc){
        return Travaux::where('num_bc',$num_bc)->first();
    }

    public function storeStatutTravaux($libelle,$travauxes_id,$profils_id,$commentaire){

        $type_statut_travauxe = TypeStatutTravaux::where('libelle', $libelle)->first();
                
            if ($type_statut_travauxe===null) {
                $type_statut_travauxes_id = TypeStatutTravaux::create([
                    'libelle'=>$libelle
                ])->id;
            } else {
                $type_statut_travauxes_id = $type_statut_travauxe->id;
            }

            if (isset($type_statut_travauxes_id)) {


                $statut_travauxe = StatutTravaux::where('travauxes_id',$travauxes_id)
                ->orderByDesc('id')
                ->limit(1)
                ->first();

                if ($statut_travauxe!=null) {
                        StatutTravaux::where('id',$statut_travauxe->id)->update([
                            'date_fin'=>date('Y-m-d'),
                        ]);
                }



                StatutTravaux::create([
                    'profils_id'=>$profils_id,
                    'travauxes_id'=>$travauxes_id,
                    'type_statut_travauxes_id'=>$type_statut_travauxes_id,
                    'commentaire'=>$commentaire,
                    'date_debut'=>date('Y-m-d'),
                ]);

            }
    }

    public function getRouteBcTravaux($type_statut_libelle,$type_profils_name,$operations_id_crypt){

        $route = "travaux/show/".$operations_id_crypt;

        if ($type_statut_libelle === "Soumis pour validation" or $type_statut_libelle === "Rejeté (Responsable des achats)" or $type_statut_libelle === "Annulé (Gestionnaire des achats)" or $type_statut_libelle === "Annulé (Responsable des achats)") {
    
            if ($type_profils_name === "Gestionnaire des achats") {
    
            //$route = "travaux/edit/".$operations_id_crypt;
    
            $route = "travaux/edit/".$operations_id_crypt;
    
            }
    
        }elseif($type_statut_libelle === "Transmis (Responsable des achats)" or $type_statut_libelle==='Rejeté (Responsable DMP)'){
    
    
        if ($type_profils_name === "Responsable des achats") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }
        elseif ($type_statut_libelle === "Transmis (Responsable DMP)" or $type_statut_libelle==='Rejeté (Responsable Contrôle Budgétaire)') {
    
        if ($type_profils_name === "Responsable DMP") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
    
        }elseif($type_statut_libelle==='Transmis (Responsable Contrôle Budgétaire)' or $type_statut_libelle==='Rejeté (Chef Département DCG)'){
    
        if ($type_profils_name === "Responsable contrôle budgetaire") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Chef Département DCG)' or $type_statut_libelle==='Rejeté (Responsable DCG)') {
    
        if ($type_profils_name === "Chef Département DCG") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Responsable DCG)' or $type_statut_libelle==='Rejeté (Directeur Général Adjoint)') {
    
        if ($type_profils_name === "Responsable DCG") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Directeur Général Adjoint)' or $type_statut_libelle==='Rejeté (Directeur Général)') {
    
        if ($type_profils_name === "Directeur Général Adjoint") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Directeur Général)') {
    
        if ($type_profils_name === "Directeur Général") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Responsable DFC)') {
    
            if ($type_profils_name === "Responsable DFC") {
    
            $route = "travaux/edit/".$operations_id_crypt;
    
            }
    
        }elseif ($type_statut_libelle==='Validé' or $type_statut_libelle==='Édité' or $type_statut_libelle==='Retiré (Frs.)') {
    
        if ($type_profils_name === "Gestionnaire des achats") {
    
        $route = "travaux/edit/".$operations_id_crypt;
    
        }
    
        }else{
        $route = "travaux/show/".$operations_id_crypt;
        }    
        return $route;
    }

    public function getLastStatutTravaux($travauxes_id){
        return DB::table('statut_travauxes as sda')
        ->join('type_statut_travauxes as tsda','tsda.id','=','sda.type_statut_travauxes_id')
        ->join('profils as p','p.id','=','sda.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('sda.travauxes_id',$travauxes_id)
        ->orderByDesc('sda.id')
        ->limit(1)
        ->first();
    }

    function getSignatairesTravaux($travauxes_id){       
        return DB::table('travauxes as t')
        ->join('signataire_travauxes as st','st.travauxes_id','=','t.id')
        ->join('profil_fonctions as pf','pf.id','=','st.profil_fonctions_id')
        ->join('agents as a','a.id','=','pf.agents_id')
        ->where('st.travauxes_id',$travauxes_id)
        ->where('st.flag_actif',1)
        ->get(); 
    }

    public function storeSignataireTravaux($request,$travauxes_id,$profils_id){
        
        $donnees = [];
        if (isset($request->profil_fonctions_id)) {
            if (count($request->profil_fonctions_id) > 0) {

                foreach ($request->profil_fonctions_id as $item => $value) {
                    $donnees[$item] =  $request->profil_fonctions_id[$item];
                }

            }
        }


        $signataire_travaux = DB::table('signataire_travauxes as sda')
        ->where('sda.flag_actif',1)
        ->where('sda.travauxes_id',$travauxes_id)
        ->first();

        
        if ($signataire_travaux!=null) {

            $signataire_travauxes = DB::table('signataire_travauxes as sda')
            ->where('sda.flag_actif',1)
            ->where('sda.travauxes_id',$travauxes_id)
            ->whereNotIn('sda.profil_fonctions_id',$donnees)
            ->get();           

            foreach ($signataire_travauxes as $signataire_travauxe) {

                $libelle = 'Désactivé';
                $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);

                SignataireTravaux::where('id',$signataire_travauxe->id)
                ->update([
                    'profil_fonctions_id'=>$signataire_travauxe->profil_fonctions_id,
                    'travauxes_id'=>$travauxes_id,
                    'flag_actif'=>0,
                ]);

                StatutSignataireTravaux::create([
                    'signataire_travauxes_id'=>$signataire_travauxe->id,
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

                        $signataire = SignataireTravaux::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('travauxes_id',$travauxes_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {

                            $signataire_travauxes_id = SignataireTravaux::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'travauxes_id'=>$travauxes_id,
                            ])->id;

                            StatutSignataireTravaux::create([
                                'signataire_travauxes_id'=>$signataire_travauxes_id,
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

                        $signataire = SignataireTravaux::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('travauxes_id',$travauxes_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {

                            $signataire_travauxes_id = SignataireTravaux::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'travauxes_id'=>$travauxes_id,
                            ])->id;

                            StatutSignataireTravaux::create([
                                'signataire_travauxes_id'=>$signataire_travauxes_id,
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

    public function storeSignataireTravaux2($profil_fonctions_id,$travauxes_id,$profils_id){

        $libelle = 'Activé';
        $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);

        if (isset($profil_fonctions_id)) {

            $signataire = SignataireTravaux::where('profil_fonctions_id',$profil_fonctions_id)
            ->where('travauxes_id',$travauxes_id)
            ->where('flag_actif',1)
            ->first();
            if ($signataire===null) {

                $signataire_travauxes_id = SignataireTravaux::create([
                    'profil_fonctions_id'=>$profil_fonctions_id,
                    'travauxes_id'=>$travauxes_id,
                ])->id;

                StatutSignataireTravaux::create([
                    'signataire_travauxes_id'=>$signataire_travauxes_id,
                    'type_statut_sign_id'=>$type_statut_sign_id,
                    'profils_id'=>$profils_id,
                    'date_debut'=>date('Y-m-d'),
                ]);
            }
        }
    }

    public function getLastNumBcn($exercice,$code_structure,$travauxes_id=null){

        $sequence_id = 1;

        if(isset($travauxes_id)){
            $travaux = Travaux::join('credit_budgetaires as cb','cb.id','=','travauxes.credit_budgetaires_id')
            ->where('travauxes.id', $travauxes_id)
            ->select('travauxes.num_bc')
            ->limit(1)
            ->first();
    
            if($travaux != null){
    
                $num_bc = $travaux->num_bc;
                
                if(isset(explode('BCN', $num_bc)[1])){
                    $sequence_id =  (int) explode('BCN', $num_bc)[1];                     
                }
                  
            }
        }else{
            $travaux = Travaux::join('credit_budgetaires as cb','cb.id','=','travauxes.credit_budgetaires_id')
            ->where('travauxes.exercice', $exercice)
            ->orderByDesc('travauxes.id')
            ->select('travauxes.num_bc')
            ->limit(1)
            ->first();
    
            if($travaux != null){
    
                $num_bc = $travaux->num_bc;
                
                if(isset(explode('BCN', $num_bc)[1])){
                    $sequence_id =  (int) explode('BCN', $num_bc)[1] + 1;                     
                }
                  
    
            }

        }

        

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'BCN';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);


    }

    public function procedureEngagementEtEcritureComptable($data){
        
        $data_solde_avant_operation = [
            'type_operations_libelle'=>$data['type_operations_libelle'],
            'operations_id'=>$data['operations_id'],
            'solde_avant_op'=>$data['solde_avant_op'],
            'flag_engagement'=>$data['flag_engagement']
        ];
        $data['this']->setSoldeAvantOperation($data_solde_avant_operation);

        $data['code_gestion'];
        $data_all = [
            'type_operations_libelle'=>$data['type_operations_libelle'],
            'profils_id'=>Session::get('profils_id'),
            'operations_id'=>$data['operations_id'],
            'type_piece'=>$data['type_piece'],
            'signe'=>$data['signe'],
            'flag_acompte'=>0
        ];

        $data_comptabilisation = $data['this']->procedureComptabilisationEcriture($data_all);

        if($data_comptabilisation != null){
            $this->storeComptabilisationEcriture($data_comptabilisation);
        }

        if($data['montant_acompte'] != null){
            
            $data_all = [
                'type_operations_libelle'=>$data['type_operations_libelle'],
                'profils_id'=>Session::get('profils_id'),
                'operations_id'=>$data['operations_id'],
                'type_piece'=>$data['type_piece'],
                'signe'=>$data['signe'],
                'flag_acompte'=>1
            ];

            $data_comptabilisation = $data['this']->procedureComptabilisationEcriture($data_all);

            if($data_comptabilisation != null){
                $this->storeComptabilisationEcriture($data_comptabilisation);
            }
        }

        $data_param = [
            'type_piece'=>$data['type_piece'],
            'compte'=>$data['compte'],
            'exercice'=>$data['exercice'],
            'code_structure'=>$data['code_structure'],
            'code_gestion'=>$data['code_gestion'],
        ];
        $operation_non_interfacee = $this->getOperationEncoursNonComptabilisee($data_param); 

        $this->setCreditBudgetaireConsoNonComptabiliseByArray($operation_non_interfacee);
    }
}
