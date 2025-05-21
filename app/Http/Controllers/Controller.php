<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Agent;
use App\Models\Depot;
use App\Models\Devise;
use App\Models\Option;
use App\Models\Profil;
use App\Jobs\SendEmail;
use App\Models\Article;
use App\Models\Critere;
use App\Models\Demande;
use App\Models\Famille;
use App\Models\Magasin;
use App\Models\Perdiem;
use App\Models\Periode;
use App\Models\Section;
use App\Models\Travaux;
use App\Models\Commande;
use App\Models\Exercice;
use App\Models\Mouvement;
use App\Models\Structure;
use App\Models\TypeAchat;
use App\Models\Inventaire;
use App\Models\TypeProfil;
use App\Models\TypeStatut;
use App\Models\Affectation;
use App\Models\DemandeFond;
use App\Models\Departement;
use App\Models\PieceJointe;
use App\Models\Requisition;
use App\Models\AgentSection;
use App\Models\BonLivraison;
use App\Models\DemandeAchat;
use App\Models\MagasinStock;
use App\Models\Organisation;
use App\Models\StatutProfil;
use App\Models\DateOperation;
use App\Models\DetailPerdiem;
use App\Models\StatutPerdiem;
use App\Models\StatutTravaux;
use App\Models\TypeMouvement;
use App\Models\TypeOperation;
use App\Models\DetailCotation;
use App\Models\Immobilisation;
use App\Models\ProfilFonction;
use App\Models\StatutExercice;
use App\Models\ComiteReception;
use App\Models\DetailLivraison;
use App\Models\TypeAffectation;
use App\Models\CreditBudgetaire;
use App\Models\DemandeConsolide;
use App\Models\LivraisonValider;
use App\Models\OptionEquipement;
use App\Models\StatutEquipement;
use App\Models\StatutInventaire;
use App\Models\TypeOrganisation;
use App\Models\TypeStatutProfil;
use App\Models\ConsommationAchat;
use App\Models\InventaireArticle;
use App\Models\LivraisonCommande;
use App\Models\OrganisationDepot;
use App\Models\SignatairePerdiem;
use App\Models\SignataireTravaux;
use App\Models\StatutDemandeFond;
use App\Models\StatutRequisition;
use App\Models\TypeStatutPerdiem;
use App\Models\TypeStatutTravaux;
use App\Models\DescriptionArticle;
use App\Models\DetailAdjudication;
use App\Models\DetailDemandeAchat;
use App\Models\StatutAgentSection;
use App\Models\StatutDemandeAchat;
use App\Models\StatutOrganisation;
use App\Models\TypeStatutExercice;
use App\Models\ValiderRequisition;
use Illuminate\Support\Facades\DB;
use App\Models\CotationFournisseur;
use App\Models\CritereAdjudication;
use App\Models\OrganisationArticle;
use App\Models\ValiderDemandeAchat;
use Illuminate\Support\Facades\URL;
use App\Models\DetailImmobilisation;
use App\Models\PieceJointeLivraison;
use App\Models\StatutImmobilisation;
use App\Models\TypeStatutEquipement;
use App\Models\TypeStatutInventaire;
use App\Models\TypeStatutSignataire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\AchatFournitureStocke;
use App\Models\EquipementImmobiliser;
use App\Models\SelectionAdjudication;
use App\Models\SignataireDemandeFond;
use App\Models\TypeStatutDemandeFond;
use App\Models\TypeStatutRequisition;
use App\Models\DemandeFondBonCommande;
use App\Models\SignataireDemandeAchat;
use App\Models\StatutCreditBudgetaire;
use App\Models\TypeStatutAgentSection;
use App\Models\TypeStatutDemandeAchat;
use App\Models\TypeStatutOrganisation;
use App\Models\StatutLivraisonCommande;
use App\Models\StatutOrganisationDepot;
use App\Models\StatutSignatairePerdiem;
use App\Models\StatutSignataireTravaux;
use Illuminate\Support\Facades\Session;
use App\Models\ComptabilisationEcriture;
use App\Models\ConsommationDistribution;
use App\Models\StatutOrganisationArticle;
use App\Models\TypeStatutCreditBudgetaire;
use App\Models\PreselectionSoumissionnaire;
use App\Models\StatutSignataireDemandeFond;
use App\Models\TypeStatutOrganisationDepot;
use App\Models\StatutSignataireDemandeAchat;
use App\Models\TypeStatutOrganisationArticle;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function controlleurUser($profils_id,$type_profils_lists){

        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', $type_profils_lists)
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',$profils_id)
                      ->first();

        return $profil;

    }

    function controlleurUserDemandeAchat($profils_id,$type_profils_lists,$request=null){

        if($request != null){
            $profil = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', $type_profils_lists)
            ->limit(1)
            ->select('p.id', 'se.code_section')
            ->where('p.flag_actif',1)
            ->where('p.id',$profils_id)
            ->where('tsase.libelle','Activé')
            ->whereIn('s.ref_depot',function($query) use($request){
                $query->select(DB::raw('da.ref_depot'))
                        ->from('demande_achats as da')
                        ->where('da.id',$request->id)
                        ->whereRaw('da.ref_depot = s.ref_depot');
            })
            ->first();
        }else{
            $profil = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', $type_profils_lists)
            ->limit(1)
            ->select('p.id', 'se.code_section')
            ->where('p.flag_actif',1)
            ->where('p.id',$profils_id)
            ->where('tsase.libelle','Activé')
            ->first();
        }
        

            return $profil;
    }

    function controlleurUserImmobilisation($profils_id,$type_profils_lists,$type_profils_name,$request=null){

        $profil = null;

        if ($type_profils_name === 'Pilote AEE') {

            $profil = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', $type_profils_lists)
            ->limit(1)
            ->select('p.id', 'se.code_section')
            ->where('p.flag_actif',1)
            ->where('p.id',$profils_id)
            ->where('tsase.libelle','Activé')
            ->whereIn('s.code_structure',function($query) use($request){
                $query->select(DB::raw('i.code_structure'))
                        ->from('immobilisations as i')
                        ->where('i.id',$request->id)
                        ->whereRaw('i.code_structure = s.code_structure');
            })
            ->first();

        }else{

            $profil = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', $type_profils_lists)
            ->limit(1)
            ->select('p.id', 'se.code_section')
            ->where('p.flag_actif',1)
            ->where('p.id',$profils_id)
            ->where('tsase.libelle','Activé')
            ->whereIn('s.ref_depot',function($query) use($request){
                $query->select(DB::raw('st.ref_depot'))
                        ->from('immobilisations as i')
                        ->join('structures as st', 'st.code_structure', '=', 'i.code_structure')
                        ->where('i.id',$request->id)
                        ->whereRaw('st.ref_depot = s.ref_depot');
            })
            ->first();

        }
        

            return $profil;
    }

    function controlleurUserDemandeAchatAdmin($profils_id,$type_profils_lists,$request=null){
        
        $profil = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', $type_profils_lists)
            ->limit(1)
            ->select('p.id', 'se.code_section')
            ->where('p.flag_actif',1)
            ->where('p.id',$profils_id)
            ->where('tsase.libelle','Activé')
            ->first();

            return $profil;
    }

    function controlleurUserDemandeAchatFournisseur($profils_id,$type_profils_lists,$request=null){
        
        $profil = DB::table('profils as p')
            ->join('statut_organisations as so', 'so.profils_id', '=', 'p.id')
            ->join('organisations as o', 'o.id', '=', 'so.organisations_id')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->where('p.users_id', auth()->user()->id)
            ->whereIn('tp.name', $type_profils_lists)
            ->limit(1)
            ->select('p.id')
            ->where('p.flag_actif',1)
            ->where('p.id',$profils_id)
            ->whereIn('o.id',function($query) use($request){
                $query->select(DB::raw('ps.organisations_id'))
                        ->from('demande_achats as da')
                        ->join('critere_adjudications as ca', 'ca.demande_achats_id', '=', 'da.id')
                        ->join('preselection_soumissionnaires as ps', 'ps.critere_adjudications_id', '=', 'ca.id')
                        ->where('da.id',$request->id)
                        ->whereRaw('o.id = ps.organisations_id');
            })
            ->first();

            return $profil;
    }

    function controlleurUserDemandeAchatFournisseur2($profils_id,$type_profils_lists,$request){

        $profil = DB::table('organisations as o')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->join('preselection_soumissionnaires as ps','ps.organisations_id','=','o.id')
            ->join('critere_adjudications as ca','ca.id','=','ps.critere_adjudications_id')
            ->join('criteres as c','c.id','=','ca.criteres_id')
            ->where('tso.libelle','Activé')
            ->where('ca.demande_achats_id',$request->id)
            ->where('u.id',auth()->user()->id)
            ->where('c.libelle','Fournisseurs Cibles')
            ->select('o.id as organisations_id','o.denomination','p.id','o.entnum')
            ->where('p.id',$profils_id)
            ->first();

        return $profil;
    }

    function controlleurUserDemandeAchatFournisseur3($profils_id,$type_profils_lists,$request){

        $profil = DB::table('organisations as o')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            
            ->join('preselection_soumissionnaires as ps','ps.organisations_id','=','o.id')
            ->join('critere_adjudications as ca','ca.id','=','ps.critere_adjudications_id')
            ->join('cotation_fournisseurs as cf','cf.organisations_id','=','o.id')
            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
            ->join('criteres as c','c.id','=','ca.criteres_id')
            ->whereRaw('ca.demande_achats_id = cf.demande_achats_id')
            ->where('tso.libelle','Activé')
            ->where('ca.demande_achats_id',$request->id)
            ->where('u.id',auth()->user()->id)
            ->where('c.libelle','Fournisseurs Cibles')
            ->select('o.id as organisations_id','o.denomination','p.id','o.entnum')
            ->where('p.id',$profils_id)
            ->first();

        return $profil;
    }

    function getOrganisationUserConnect($profils_id,$demande_achats_id){

        $profil = DB::table('organisations as o')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->join('preselection_soumissionnaires as ps','ps.organisations_id','=','o.id')
            ->join('critere_adjudications as ca','ca.id','=','ps.critere_adjudications_id')
            ->join('criteres as c','c.id','=','ca.criteres_id')
            ->where('tso.libelle','Activé')
            ->where('ca.demande_achats_id',$demande_achats_id)
            ->where('u.id',auth()->user()->id)
            ->where('c.libelle','Fournisseurs Cibles')
            ->select('o.id as organisations_id','o.denomination','p.id','o.entnum')
            ->where('p.id',$profils_id)
            ->first();

        return $profil;
    }

    function getAllAgent(){
        $agents = DB::table('agents as a')
                ->join('users as u','u.agents_id','=','a.id')
                ->select('a.id','a.mle','a.nom_prenoms','u.email','u.flag_actif','u.id as users_id','a.updated_at')
                ->where('u.flag_actif',1)
                ->whereIn('a.id',function($query){
                    $query->select(DB::raw('ase.agents_id'))
                          ->from('agent_sections as ase')
                          ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                          ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                          ->join('sections as s','s.id','=','ase.sections_id')
                          ->join('structures as st','st.code_structure','=','s.code_structure')
                          ->where('tsase.libelle','Activé')
                          ->whereRaw('a.id = ase.agents_id');
                })
                ->get();
        return $agents;
    }

    function getAllSection(){

        $sections = DB::table('sections')
                    ->join('structures','structures.code_structure','=','sections.code_structure')
                    ->get();
        return $sections;

    }

    function getSectionById($code_structure){
        $sections = DB::table('sections')
                    ->join('structures','structures.code_structure','=','sections.code_structure')
                    ->where('structures.code_structure',$code_structure)
                    ->get();
        return $sections;
    }

    function getSectionByIdByName($sections_name,$code_structure){

        $section = Section::where('nom_section',$sections_name)
                          ->where('code_structure',$code_structure)
                          ->first();
        return $section;
        
    }

    function storeAgent($mle,$nom_prenoms){
        $agent = Agent::create([
            'mle'=>$mle,
            'nom_prenoms'=>$nom_prenoms,
        ]);

        return $agent;
    }

    function setAgent($mle,$nom_prenoms,$agents_id){
        $agent = Agent::where('id', $agents_id)
                        ->update([
                            'mle'=>$mle,
                            'nom_prenoms'=>$nom_prenoms,
                        ]);

        return $agent;
    }

    function generatePassword(){

        $password='';

        $characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        

        for($i=0;$i<8;$i++)
        {
            $password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
        }

        return $password;
    }

    function storeUser($agents_id,$username,$email,$password){
        $user = User::create([
            'agents_id'=>$agents_id,
            'username'=>$username,
            'email'=>$email,
            'flag_actif'=>true,
            'reset'=>true,
            'password'=>Hash::make($password),
        ]);

        return $user;
    }

    

    function getTypeProfilByName($name){
        $type_profil = DB::table('type_profils as tp')
                ->where('name',$name)
                ->first();
        return $type_profil;
    }

    function storeTypeProfilByName($name){
        TypeProfil::create([
            'name'=>$name
        ]);
    }

    function storeAgentSection($agents_id,$sections_id,$date){

        $agent_section = AgentSection::create([
            'agents_id'=>$agents_id,
            'sections_id'=>$sections_id,
            'exercice'=>$date,

        ]);

        return $agent_section;
    }

    function getTypeStatutAgentSectionByName($libelle){
        $type_statut_agent_section = TypeStatutAgentSection::where('libelle',$libelle)->first();
        return $type_statut_agent_section;
    }

    function storeTypeStatutAgentSection($libelle){
        TypeStatutAgentSection::create([
            'libelle'=>$libelle
        ])->id;
    }

    function storeStatutAgentSection($agent_sections_id,$type_statut_agent_sections_id,$profils_id){
        StatutAgentSection::create([
            'agent_sections_id'=>$agent_sections_id,
            'type_statut_agent_sections_id'=>$type_statut_agent_sections_id,
            'date_debut'=>date("Y-m-d"),
            'date_fin'=>date("Y-m-d"),
            'profils_id'=>$profils_id,
        ]);
    }

    function notif_user_connect($email,$subject,$agents_id,$param_acces_login,$param_acces_password){
        // utilisateur connecté
            $details = [
                'email' => $email,
                'subject' => $subject,
                'agents_id' => $agents_id,
                'param_acces_login'=>$param_acces_login,
                'param_acces_password'=>$param_acces_password,
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }

    function notifDemandeAchat($email,$subject,$demande_achats_id){
        // utilisateur connecté
            $details = [
                'email' => $email,
                'subject' => $subject,
                'demande_achats_id' => $demande_achats_id,
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }

    function notifRequisition($email,$subject,$requisitions_id){
        // utilisateur connecté
            $details = [
                'email' => $email,
                'subject' => $subject,
                'requisitions_id' => $requisitions_id,
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }

    function notifImmobilisation($email,$subject,$immobilisations_id){
        // utilisateur connecté
            $details = [
                'email' => $email,
                'subject' => $subject,
                'immobilisations_id' => $immobilisations_id,
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }

    function notifImmobilisations($subject,$immobilisations_id,$type_profils_names){
        // Gestionnaire des achats
                    
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
            ->whereIn('st.ref_depot',function($query) use($immobilisations_id){
                $query->select(DB::raw('stt.ref_depot'))
                    ->from('immobilisations as i')
                    ->join('structures as stt', 'stt.code_structure', '=', 'i.code_structure')
                    ->where('i.id',$immobilisations_id)
                    ->whereRaw('st.ref_depot = stt.ref_depot');
            })
            ->get();

            foreach ($profils as $profil) {

                $this->notifImmobilisation($profil->email,$subject,$immobilisations_id);

            }
        //
    }

    function notifPiloteImmobilisations($subject,$immobilisations_id){
        // Gestionnaire des achats
                    
            $profils = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('sections as s', 's.id', '=', 'ase.sections_id')
            ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->whereIn('tp.name', ['Pilote AEE'])
            ->select('u.email')
            ->where('p.flag_actif',1)
            ->where('tsase.libelle','Activé')
            ->whereIn('st.code_structure',function($query) use($immobilisations_id){
                $query->select(DB::raw('i.code_structure'))
                    ->from('immobilisations as i')
                    ->where('i.id',$immobilisations_id)
                    ->whereRaw('st.code_structure = i.code_structure');
            })
            ->get();

            foreach ($profils as $profil) {

                $this->notifImmobilisation($profil->email,$subject,$immobilisations_id);

            }
        //
    }

    function notifDemandeAchats($subject,$demande_achats_id,$type_profils_names){
        // Gestionnaire des achats
                    
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
            ->whereIn('st.ref_depot',function($query) use($demande_achats_id){
                $query->select(DB::raw('da.ref_depot'))
                    ->from('demande_achats as da')
                    ->where('da.id',$demande_achats_id)
                    ->whereRaw('st.ref_depot = da.ref_depot');
            })
            ->get();

            foreach ($profils as $profil) {

                $this->notifDemandeAchat($profil->email,$subject,$demande_achats_id);

            }
        //
    }

    function notifDemandeAchatFournisseurs($subject,$demande_achats_id){
        
        $fournisseurs = DB::table('demande_achats as da')
            ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
            ->join('criteres as c','c.id','=','ca.criteres_id')
            ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
            ->join('organisations as o','o.id','=','ps.organisations_id')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->where('da.id',$demande_achats_id)
            ->where('tso.libelle','Activé')
            ->where('c.libelle','Fournisseurs Cibles')
            ->select('u.email')
            ->get();

        foreach ($fournisseurs as $fournisseur) {

            $this->notifDemandeAchat($fournisseur->email,$subject,$demande_achats_id);

        }
    }

    function notifDemandeAchatFournisseur($subject,$demande_achats_id,$cotation_fournisseurs_id){
        
        $fournisseurs = DB::table('demande_achats as da')
            ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
            ->join('criteres as c','c.id','=','ca.criteres_id')
            ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
            ->join('organisations as o','o.id','=','ps.organisations_id')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('cotation_fournisseurs as cf','cf.organisations_id','=','o.id')
            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
            ->where('da.id',$demande_achats_id)
            ->where('cf.id',$cotation_fournisseurs_id)
            ->where('tso.libelle','Activé')
            ->where('c.libelle','Fournisseurs Cibles')
            ->select('u.email')
            ->get();

        foreach ($fournisseurs as $fournisseur) {

            $this->notifDemandeAchat($fournisseur->email,$subject,$demande_achats_id);

        }
    }

    function notif_user_create($subject,$agents_id,$param_acces_login,$param_acces_password){
        // utilisateur connecté
            $details = [
                'email' => $param_acces_login,
                'subject' => $subject,
                'agents_id' => $agents_id,
                'param_acces_login'=>$param_acces_login,
                'param_acces_password'=>$param_acces_password,
            ];

            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        //
    }
    
    function getAgentById($agents_id){
        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('agent_sections as ase','ase.agents_id','=','a.id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
        ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
        ->select('a.mle','st.nom_structure','a.nom_prenoms','u.flag_actif','u.reset','a.id','u.email','s.code_section','s.nom_section','st.code_structure','st.nom_structure','u.id as users_id')
        ->where('ase.agents_id',$agents_id)
        ->orderByDesc('sase.id')
        ->limit(1)
        ->where('tsase.libelle','Activé')
        ->first();

        return $agents;
    }

    function getAgent($agents_id){
        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('agent_sections as ase','ase.agents_id','=','a.id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
        ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
        ->select('a.mle','st.nom_structure','a.nom_prenoms','u.flag_actif','u.reset','a.id','u.email','s.code_section','s.nom_section','st.code_structure','st.nom_structure','u.id as users_id')
        ->where('ase.agents_id',$agents_id)
        ->orderByDesc('sase.id')
        ->limit(1)
        ->where('tsase.libelle','Activé')
        ->first();

        return $agents;
    }

    function getProfilByAgentId($agents_id){

        $profils = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->select('tp.name','p.flag_actif')
        ->where('a.id',$agents_id)
        ->where('p.flag_actif',1)
        ->get();

        return $profils;
    }

    function getProfilFirstByAgentId($agents_id){

        $profil = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->select('p.id as profils_id')
        ->where('a.id',$agents_id)
        ->where('p.flag_actif',1)
        ->orderByDesc('p.id')
        ->first();

        return $profil;
    }

    function getAgentSectionByAgentId($agents_id){
        $agent_sections = DB::table('agents as a')
            ->join('users as u','u.agents_id','=','a.id')
            ->join('agent_sections as ase','ase.agents_id','=','a.id')
            ->join('sections as s','s.id','=','ase.sections_id')
            ->join('structures as st','st.code_structure','=','s.code_structure')
            ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
            ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
            ->select('s.code_section','s.nom_section','s.code_structure')
            ->where('ase.agents_id',$agents_id)
            ->orderByDesc('sase.id')
            ->where('tsase.libelle','Activé')
            ->get();
            
        return $agent_sections;
    }

    function setUser($agents_id,$email,$password,$username=null){

        User::where('agents_id',$agents_id)->update([
            'email'=>$email,
            'username'=>$username,
            'flag_actif'=>true,
        ]);

    }

    function getUserByAgentId($agents_id){
        $user = User::where('agents_id',$agents_id)->first();

        return $user;
    }

    function getAgentSectionByAgentIdSectionId($agents_id,$sections_id){
        $agent_sections = AgentSection::where('agents_id',$agents_id)
        ->where('sections_id',$sections_id)
        ->first();

        return $agent_sections;
    }

    function setProfil($type_profils_name,$users_id,$flag_actif){
        
        $profils_id = null;

        $type_profil = TypeProfil::where('name',$type_profils_name)
            ->first();
        if ($type_profil!=null) {

            $type_profils_id = $type_profil->id;

        }else{

            $type_profils_id = TypeProfil::create([
                'name'=>$type_profils_name
            ])->id;

        }

        if (isset($type_profils_id)) {
            
            $profil = Profil::where('users_id',$users_id)
            ->where('type_profils_id',$type_profils_id)
            ->first();
            if ($profil!=null) {

                $profils_id = $profil->id;

                if ($profil->flag_actif != $flag_actif) {
                    Profil::where('id',$profils_id)->update([
                        'flag_actif'=>$flag_actif,
                    ]);
                }

            }else{
                $profils_id = Profil::create([
                                'users_id'=>$users_id,
                                'flag_actif'=>$flag_actif,
                                'type_profils_id'=>$type_profils_id,
                            ])->id;
            }
            
        }

        return $profils_id;
    }

    function setStatutProfil($profils_id,$type_statut_profils_libelle,$date_debut,$profils_ids){
        
        $type_statut_profil = TypeStatutProfil::where('libelle',$type_statut_profils_libelle)
            ->first();
        if ($type_statut_profil!=null) {

            $type_statut_profils_id = $type_statut_profil->id;

        }else{

            $type_statut_profils_id = TypeStatutProfil::create([
                'libelle'=>$type_statut_profils_libelle
            ])->id;

        }

        if (isset($type_statut_profils_id)) {

            $statut_profil = StatutProfil::where('profils_id',$profils_id)
            ->where('type_statut_profils_id',$type_statut_profils_id)
            ->first();
            if ($statut_profil===null) {
                StatutProfil::create([
                    'profils_id'=>$profils_id,
                    'profils_ids'=>$profils_ids,
                    'date_debut'=>$date_debut,
                    'date_fin'=>$date_debut,
                    'type_statut_profils_id'=>$type_statut_profils_id,
                ]);
            }

            
        }
    }

    function statut_agent_section_desactive($agents_id,$profils_id,$libelle,$code_structure){

        $type_statut_agent_section = TypeStatutAgentSection::where('libelle',$libelle)
                ->first();
                if ($type_statut_agent_section!=null) {

                    $type_statut_agent_sections_id = $type_statut_agent_section->id;

                }else{

                    $type_statut_agent_sections_id = TypeStatutAgentSection::create([
                        'libelle'=>$libelle
                    ])->id;

                }

                
                StatutAgentSection::where('ase.agents_id',$agents_id)
                ->join('agent_sections as ase','ase.id','=','agent_sections_id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','type_statut_agent_sections_id')
                ->where('tsas.libelle','Activé')
                ->whereNotIn('st.code_structure',[$code_structure])
                ->update([
                    'type_statut_agent_sections_id'=>$type_statut_agent_sections_id,
                    'date_fin'=>date("Y-m-d"),
                    'profils_id'=>$profils_id,
                ]);
    }

    function statut_agent_section_active($agent_sections_id,$profils_id,$libelle){

        $type_statut_agent_section = TypeStatutAgentSection::where('libelle',$libelle)
                ->first();
                if ($type_statut_agent_section!=null) {

                    $type_statut_agent_sections_id = $type_statut_agent_section->id;

                }else{

                    $type_statut_agent_sections_id = TypeStatutAgentSection::create([
                        'libelle'=>$libelle
                    ])->id;

                }

                $statut_agent_section = StatutAgentSection::where('agent_sections_id',$agent_sections_id)
                ->where('type_statut_agent_sections_id',$type_statut_agent_sections_id)
                ->first();
                if ($statut_agent_section===null) {
                    StatutAgentSection::create([
                        'agent_sections_id'=>$agent_sections_id,
                        'type_statut_agent_sections_id'=>$type_statut_agent_sections_id,
                        'date_debut'=>date("Y-m-d"),
                        'profils_id'=>$profils_id,
                    ]);
                }

        return 'ok';        

    }

    public function getTypeProfilByProfilId($profils_id){

        return DB::table('type_profils as tp')
        ->join('profils as p', 'p.type_profils_id', '=', 'tp.id')
        ->where('p.id',$profils_id)
        ->first();
    }

    function controllerAcces($etape,$type_profils_lists,$type_profils_name,$request=null){

        $profil = null;
        if ($etape === "index") {

            $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        }elseif ($etape === "create" or $etape === "store") {
            
            $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);
                
        }elseif ($etape === "show") {

            if ($type_profils_name === 'Administrateur fonctionnel') {

                $profil = $this->controlleurUserDemandeAchatAdmin(Session::get('profils_id'),$type_profils_lists);

            }elseif ($type_profils_name === 'Fournisseur') {

                $profil = $this->controlleurUserDemandeAchatFournisseur(Session::get('profils_id'),$type_profils_lists,$request);

            }else{

                $profil = $this->controlleurUserDemandeAchat(Session::get('profils_id'),$type_profils_lists,$request);

            }
            
        }elseif ($etape === "edit" or $etape === "update") {

            $profil = $this->controlleurUserDemandeAchat(Session::get('profils_id'),$type_profils_lists,$request);            
            
        }elseif ($etape === "cotation" or $etape === "cotation_store") {

            $profil = $this->controlleurUserDemandeAchat(Session::get('profils_id'),$type_profils_lists,$request);           
            
        }elseif ($etape === "cotation_fournisseur_create" or $etape === "cotation_fournisseur_store") {

            $profil = $this->controlleurUserDemandeAchatFournisseur2(Session::get('profils_id'),$type_profils_lists,$request);

        }elseif ($etape === "cotation_fournisseur_create_bc" or $etape === "cotation_fournisseur_store_bc" or $etape === "livraison_commande_create" or $etape === "livraison_commande_store") {

            if ($type_profils_name === 'Fournisseur') {

                $profil = $this->controlleurUserDemandeAchatFournisseur3(Session::get('profils_id'),$type_profils_lists,$request);

            }else{

                $profil = $this->controlleurUserDemandeAchat(Session::get('profils_id'),$type_profils_lists,$request);

            }

            

        }elseif ($etape === "selection_adjudication_liste" or $etape ===  "selection_adjudication_create" or $etape ===  "selection_adjudication_store"){

            $profil = $this->controlleurUserDemandeAchat(Session::get('profils_id'),$type_profils_lists,$request);

        }elseif ($etape === "immobilisation_create" or $etape === "immobilisation_update") {

            $profil = $this->controlleurUserImmobilisation(Session::get('profils_id'),$type_profils_lists,$type_profils_name,$request);

        }elseif ($etape === "valider_requisition_create") {
            if ($type_profils_name === 'Agent Cnps') {

                $profil = DB::table('profils as p')
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
                ->whereIn('p.id',function($query) use($request){
                    $query->select(DB::raw('r.profils_id'))
                            ->from('requisitions as r')
                            ->where('r.id',$request->id)
                            ->whereRaw('r.profils_id = p.id');
                })
                ->first();
                
            }elseif ($type_profils_name === 'Pilote AEE'){
                $profil = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Pilote AEE')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.code_structure',function($query) use($request){
                    $query->select(DB::raw('r.code_structure'))
                            ->from('requisitions as r')
                            ->where('r.id',$request->id)
                            ->whereRaw('r.code_structure = s.code_structure');
                })
                ->first();
            }elseif ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks'){
                

                $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
                if ($infoUserConnect != null) {
                    
                    $ref_depot = $infoUserConnect->ref_depot;

                    if($ref_depot === 83){

                        $profil = DB::table('profils as p')
                            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                            ->join('users as u', 'u.id', '=', 'p.users_id')
                            ->join('agents as a', 'a.id', '=', 'u.agents_id')
                            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                            ->where('p.users_id', auth()->user()->id)
                            ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                            ->limit(1)
                            ->select('p.id', 'se.code_section', 's.ref_depot')
                            ->where('p.flag_actif',1)
                            ->where('p.id',Session::get('profils_id'))
                            ->where('tsase.libelle','Activé')
                            ->whereIn('s.ref_depot',function($query) use($request){
                                $query->select(DB::raw('dp.ref_depot'))
                                        ->from('requisitions as r')
                                        ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                        ->join('demandes as d', 'd.requisitions_id', '=', 'r.id')
                                        ->join('magasin_stocks as ms', 'ms.id', '=', 'd.magasin_stocks_id')
                                        ->join('magasins as m', 'm.ref_magasin', '=', 'ms.ref_magasin')
                                        ->join('depots as dp', 'dp.id', '=', 'm.depots_id')
                                        ->where('r.id',$request->id)
                                        ->whereRaw('dp.ref_depot = s.ref_depot');
                            })
                            ->first();

                    }else{
                        $profil = DB::table('profils as p')
                        ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                        ->join('users as u', 'u.id', '=', 'p.users_id')
                        ->join('agents as a', 'a.id', '=', 'u.agents_id')
                        ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                        ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                        ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                        ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                        ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                        ->where('p.users_id', auth()->user()->id)
                        ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                        ->limit(1)
                        ->select('p.id', 'se.code_section', 's.ref_depot')
                        ->where('p.flag_actif',1)
                        ->where('p.id',Session::get('profils_id'))
                        ->where('tsase.libelle','Activé')
                        ->whereIn('s.ref_depot',function($query) use($request){
                            $query->select(DB::raw('st.ref_depot'))
                                    ->from('requisitions as r')
                                    ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                    ->where('r.id',$request->id)
                                    ->whereRaw('st.ref_depot = s.ref_depot');
                        })
                        ->first();
                    }
                }
            }elseif ($type_profils_name === 'Responsable N+1') {
                
                $profil = DB::table('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                    ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->where('tp.name','Responsable N+1')
                    ->limit(1)
                    ->select('p.id', 'se.code_section', 's.ref_depot')
                    ->where('p.flag_actif',1)
                    ->where('p.id',Session::get('profils_id'))
                    ->where('tsase.libelle','Activé')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->where('h.flag_actif',1)
                    ->where('h.agents_id_n1',auth()->user()->agents_id)
                    ->whereIn('h.agents_id',function($query) use($request){
                        $query->select(DB::raw('aa.id'))
                                ->from('requisitions as r')
                                ->join('profils as pp','pp.id','=','r.profils_id')
                                ->join('users as uu','uu.id','=','pp.users_id')
                                ->join('agents as aa','aa.id','=','uu.agents_id')
                                ->where('r.id',$request->id)
                                ->whereRaw('h.agents_id = aa.id');
                    })
                    ->first();
                

                    
                

            }elseif ($type_profils_name === 'Responsable N+2') {

                $profil = DB::table('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                    ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->whereIn('tp.name', ['Responsable N+2'])
                    ->limit(1)
                    ->select('p.id', 'se.code_section', 's.ref_depot')
                    ->where('p.flag_actif',1)
                    ->where('p.id',Session::get('profils_id'))
                    ->where('tsase.libelle','Activé')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->where('h.flag_actif',1)
                    ->where('h.agents_id_n2',auth()->user()->agents_id)
                    ->whereIn('h.agents_id',function($query) use($request){
                        $query->select(DB::raw('aa.id'))
                                ->from('requisitions as r')
                                ->join('profils as pp','pp.id','=','r.profils_id')
                                ->join('users as uu','uu.id','=','pp.users_id')
                                ->join('agents as aa','aa.id','=','uu.agents_id')
                                ->where('r.id',$request->id)
                                ->whereRaw('h.agents_id = aa.id');
                    })
                    ->first();
                

            }
        }

        return $profil;
    }

    
    function controlAcces($type_profils_name,$etape,$users_id,$demande_achats_id=null){

        $profils = null;
        if ($etape === "create" or $etape === "store") {

            
                
        }elseif ($etape === "create_bc" or $etape === "store_bc") {

            if ($type_profils_name === 'Fournisseur') {
                $profils = DB::table('organisations')
                ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                ->join('profils','profils.id','=','statut_organisations.profils_id')
                ->join('users','users.id','=','profils.users_id')
                ->join('agents','agents.id','=','users.agents_id')
                ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                ->where('type_statut_organisations.libelle','Activé')
                ->where('critere_adjudications.demande_achats_id',$demande_achats_id)
                ->where('users.id',auth()->user()->id)
                ->where('criteres.libelle','Fournisseurs Cibles')
                ->select('profils.id')
                ->where('profils.id',Session::get('profils_id'))
                ->first();
            }else{
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
                ->whereIn('tp.name', ['Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Directeur Général','Signataire'])
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.ref_depot',function($query) use($demande_achats_id){
                    $query->select(DB::raw('da.ref_depot'))
                            ->from('demande_achats as da')
                            ->where('da.id',$demande_achats_id)
                            ->whereRaw('da.ref_depot = s.ref_depot');
                })
                ->first();
            }
            
        }

        return $profils;
    }

    function getGestion(){
        $gestions = DB::table('gestions')->get();
        return $gestions;
    }

    public function getExercice(){

        $exercices = DB::table('exercices as e')
        ->join('statut_exercices as se','se.exercice','=','e.exercice')
        ->join('type_statut_exercices as tse','tse.id','=','se.type_statut_exercices_id')
        ->where('tse.libelle','Ouverture')
        ->whereNull('se.date_fin')
        ->orderBy('se.exercice')
        ->limit(1)
        ->first();

        return $exercices;

    }

    function getSectionByAgentId($agents_id){
            
        $section = DB::table('sections as s')
            ->join('agent_sections as ase','ase.sections_id','=','s.id')
            ->join('structures as st','st.code_structure','=','s.code_structure')
            ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
            ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
            ->where('tsas.libelle','Activé')
            ->where('ase.agents_id',$agents_id)
            ->orderByDesc('sas.id')
            ->limit(1)
            ->first();

        return $section;
    }

    function getFamille(){
        $familles = DB::table('familles')->get();
        
        return $familles;
    }

    function getCreditBudgetaireByRefDepotByExercice($ref_depot,$exercice){
            
        $credit_budgetaires = DB::table('familles as f')
            ->join('credit_budgetaires as cb','cb.ref_fam','=','f.ref_fam')
            ->join('articles as a','a.ref_fam','=','cb.ref_fam')
            ->select('f.id','f.ref_fam','f.design_fam')
            ->distinct('f.id','f.ref_fam','f.design_fam')
            ->where('cb.ref_depot',$ref_depot)
            ->where('cb.exercice',$exercice)
            ->get();

        return $credit_budgetaires;
    }

    function getArticleConcerners($ref_fam,$code_structure,$code_gestion,$exercice){
            
        $articles = DB::table('familles as f')
            ->join('credit_budgetaires as cb','cb.ref_fam','=','f.ref_fam')
            ->join('articles as a','a.ref_fam','=','cb.ref_fam')
            ->select('a.ref_articles','a.design_article')
            ->distinct('a.ref_articles','a.design_article')
            ->where('cb.ref_fam',$ref_fam)
            ->where('cb.code_structure',$code_structure)
            ->where('cb.code_gestion',$code_gestion)
            ->where('cb.exercice',$exercice)
            ->where('a.flag_actif',1) 
            ->whereRaw('cb.credit > 0')
            ->get();

        return $articles;
    }

    function getCreditBudgetaireDisponible($ref_fam,$code_structure,$code_gestion,$exercice){
            
        $type_piece = "ENG";
        $data_param = [
            'type_piece'=>$type_piece,
            'compte'=>$ref_fam,
            'exercice'=>$exercice,
            'code_structure'=>$code_structure,
            'code_gestion'=>$code_gestion,
        ];
        $operation_non_interfacee = $this->getOperationEncoursNonComptabilisee($data_param); 

        $this->setCreditBudgetaireConsoNonComptabiliseByArray($operation_non_interfacee);

        $credit_budgetaire = DB::table('credit_budgetaires')
            ->select('id','credit','consommation_non_interfacee')
            ->where('ref_fam',$ref_fam)
            ->where('code_structure',$code_structure)
            ->where('code_gestion',$code_gestion)
            ->where('exercice',$exercice)
            ->first();

        return $credit_budgetaire;
    }

    function getCreditBudgetaireDisponibleByDemandeAchatId($demande_achats_id){
            
        $credit_budgetaire = DB::table('credit_budgetaires as cb')
        ->join('demande_achats as da','da.credit_budgetaires_id','=','cb.id')
        ->select('cb.id','cb.credit','cb.consommation_non_interfacee','cb.ref_fam','cb.exercice','cb.code_structure','cb.code_gestion')
        ->where('da.id',$demande_achats_id)
        ->first();

        if($credit_budgetaire != null){
            $type_piece = "ENG";
            $data_param = [
                'type_piece'=>$type_piece,
                'compte'=>$credit_budgetaire->ref_fam,
                'exercice'=>$credit_budgetaire->exercice,
                'code_structure'=>$credit_budgetaire->code_structure,
                'code_gestion'=>$credit_budgetaire->code_gestion,
            ];
            $operation_non_interfacee = $this->getOperationEncoursNonComptabilisee($data_param); 
    
            $this->setCreditBudgetaireConsoNonComptabiliseByArray($operation_non_interfacee);
        }
           

        return DB::table('credit_budgetaires as cb')
        ->join('demande_achats as da','da.credit_budgetaires_id','=','cb.id')
        ->select('cb.id','cb.credit','cb.consommation_non_interfacee')
        ->where('da.id',$demande_achats_id)
        ->first();
    }

    function getArticle(){
        return DB::table('articles')
            ->join('familles','familles.ref_fam', '=', 'articles.ref_fam')
            ->select('articles.*', 'familles.*')
            ->get();
    }

    function getArticleActifs(){
        return DB::table('articles as a')
            ->join('familles as f','f.ref_fam', '=', 'a.ref_fam')
            ->select('a.*', 'f.*')
            ->where('a.flag_actif',1)
            ->get();
    }

    function getInfoUserConnect($profils_id,$users_id){

        $profil = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->join('depots as d', 'd.ref_depot', '=', 's.ref_depot')
                ->where('p.users_id', $users_id)
                ->select('p.id', 'se.code_section', 's.ref_depot', 's.code_structure', 's.nom_structure','d.id as depots_id','a.mle','a.id as agents_id','a.nom_prenoms')
                ->where('p.flag_actif',1)
                ->where('tsase.libelle','Activé')
                ->where('p.id',$profils_id)
                ->where('u.flag_actif',1)
                ->orderByDesc('sase.id')
                ->limit(1)
                ->first();

        return $profil;
    }

    public function getInfoUserByProfilId($profils_id){

        return DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->join('depots as d', 'd.ref_depot', '=', 's.ref_depot')
                ->select('p.id', 'se.code_section', 's.ref_depot', 's.code_structure', 's.nom_structure','d.id as depots_id','a.mle','a.id as agents_id')
                ->where('p.flag_actif',1)
                ->where('tsase.libelle','Activé')
                ->where('p.id',$profils_id)
                ->where('u.flag_actif',1)
                ->orderByDesc('sase.id')
                ->limit(1)
                ->first();
    }

    function getAgentByMle($mle,$type_profils_name){

        $profil = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->where('a.mle', $mle)
                ->select('p.id as profils_id','a.id as agents_id')
                ->where('p.flag_actif',1)
                ->where('tp.name',$type_profils_name)
                ->limit(1)
                ->first();

        return $profil;
    }

    function getInfoUserByMle($mle){

        $profil = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->join('depots as d', 'd.ref_depot', '=', 's.ref_depot')
                ->where('a.mle', $mle)
                ->select('p.id', 'se.code_section', 's.ref_depot', 's.code_structure', 's.nom_structure','d.id as depots_id')
                ->where('p.flag_actif',1)
                ->where('tsase.libelle','Activé')
                ->where('u.flag_actif',1)
                ->orderByDesc('sase.id')
                ->limit(1)
                ->first();

        return $profil;
    }

    function getAgentsByStructure($code_structure){

        $agents = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
            ->select('a.mle', 'a.nom_prenoms')
            ->where('p.flag_actif',1)
            ->where('tsase.libelle','Activé')
            ->where('s.code_structure',$code_structure)
            ->where('u.flag_actif',1)
            ->distinct('a.mle', 'a.nom_prenoms')
            ->get();

        return $agents; 

    }

    function storeCreditBudgetaireByWebService($param,$ref_depot,$code_structure = null){
          
        
        $response = Http::get('http://srvevaldrh.cnps.ci:8080/webservice/public/api/credit_budgetaire_b/'.$param);

        $status = $response->status();

        if($status === 200){
            $results = $response->json();
        
            if($results['status'] === 200){
    
                if($results['resultat'] != null){
                    $code_structure = (int) $results['resultat']['code_structure'];
                    $exercice = (int) $results['resultat']['exercice'];
                    $ref_fam = (int) $results['resultat']['ref_fam'];
                    $code_gestion = $results['resultat']['code_gestion'];
                    
                    $credit_initial = (int) $results['resultat']['credit_initial'];
                    $consommation = (int) $results['resultat']['consommation'];
                    $credit = (int) $results['resultat']['credit'];
        
                    $donnes = [
                        'ref_depot'=>$ref_depot,
                        'code_structure'=>$code_structure,
                        'ref_fam'=>$ref_fam,
                        'code_gestion'=>$code_gestion,
                        'exercice'=>$exercice,
                        'credit_initiale'=>$credit_initial,
                        'consommation'=>$consommation,
                        'credit'=>$credit
                    ];
        
                    $credit_budgetaire = DB::table('credit_budgetaires')
                    ->where('code_structure',$code_structure)
                    ->where('ref_fam',$ref_fam)
                    ->where('exercice',$exercice)
                    ->where('code_gestion',$code_gestion)
                    ->first();
        
                    if($credit_budgetaire != null){
                        CreditBudgetaire::where('id',$credit_budgetaire->id)
                        ->update($donnes);
                    }else{
                        CreditBudgetaire::create($donnes);
                    }
                }
                //dd($status,$results['resultat']);
                
                
            }
        }
        
        
    }

    function getGestionDefault(){
        return DB::table('gestions')->where('code_gestion','G')->first();
    }

    function getGestionById($gestions_id){
        return DB::table('gestions')->where('id',$gestions_id)->first();
    }

    function getFamilleById($familles_id){

        $credit_budgetaires_select = DB::table('familles')
                        ->where('id',$familles_id)
                        ->first();
        return $credit_budgetaires_select;

    }

    public function getArticleByFamilleId($familles_id){
        return DB::table('articles as a')
            ->join('familles as f','f.ref_fam', '=', 'a.ref_fam')
            ->where('f.id',$familles_id)
            ->select('a.created_at as articles_created_at','a.updated_at as articles_updated_at','a.*', 'f.*')
            ->get();
    }

    function getArticleByFamilleRef($ref_fam){

        $articles = DB::table('articles as a')
            ->join('familles as f','f.ref_fam', '=', 'a.ref_fam')
            ->where('f.ref_fam',$ref_fam)
            ->select('a.*', 'f.*')
            ->get();

        return $articles;
        
    }

    function countArticleByFamilleId($familles_id){

        $nbre_ligne = count( DB::table('articles')
            ->join('familles','familles.ref_fam', '=', 'articles.ref_fam')
            ->where('familles.id',$familles_id)
            ->get());

        return $nbre_ligne;
        
    }

    function getStructureCreditBudgetaireByRefDepotByRefFamilleByExercice($ref_depot,$ref_fam,$exercice){

        $structures = DB::table('structures as s')
            ->join('credit_budgetaires as cb','cb.code_structure','=','s.code_structure')
            ->where('s.ref_depot',$ref_depot)
            ->where('cb.ref_fam',$ref_fam)
            ->where('cb.exercice',$exercice)
            ->whereRaw('cb.credit > 0')
            ->select('s.code_structure','s.nom_structure','cb.id as credit_budgetaires_id')
            ->get();

        return $structures;

    }

    function getStructuresByRefDepot($ref_depot,$code_structure=null,$type_profils_name=null){

        $structures = [];
        if($type_profils_name != null){
            if($type_profils_name === "Responsable des stocks" OR $type_profils_name === "Gestionnaire des stocks"){
                    
                $structures = DB::table('structures')
                ->select('code_structure','nom_structure')
                ->get();
                

            }else{
                if($code_structure != null){
                    $structures = DB::table('structures')
                    ->where('ref_depot',$ref_depot)
                    ->where('code_structure',$code_structure)
                    ->select('code_structure','nom_structure')
                    ->get();
                }else{
                    
                    $structures = DB::table('structures')
                    ->where('ref_depot',$ref_depot)
                    ->select('code_structure','nom_structure')
                    ->get();
                }
            }
            
        }else{
            if($code_structure != null){
                $structures = DB::table('structures')
                ->where('ref_depot',$ref_depot)
                ->where('code_structure',$code_structure)
                ->select('code_structure','nom_structure')
                ->get();
            }else{
                
                $structures = DB::table('structures')
                ->where('ref_depot',$ref_depot)
                ->select('code_structure','nom_structure')
                ->get();
            }
        }
        
        

        return $structures;

    }

    function getDepartements(){

        $departement = Departement::all();

        return $departement;

    }

    function getCreditBudgetaireById($credit_budgetaires_id){

        $credit_budgetaire = DB::table('structures as s')
            ->join('credit_budgetaires as cb','cb.code_structure','=','s.code_structure')
            ->where('cb.id',$credit_budgetaires_id)
            ->select('cb.id as credit_budgetaires_id','s.*','cb.*')
            ->first();
        return $credit_budgetaire;

    }

    function generateNumBc($exercice,$code_structure,$type_bc,$sequence_id){

        $y = substr($exercice, -2);
        $num_bc = $y.'/'.$code_structure.$type_bc.$sequence_id;

        return $num_bc;

    }

    function generateImmobilisation($exercice,$code_structure,$type_bc,$sequence_id){

        $y = substr($exercice, -2);
        $num_immo = $y.'/'.$code_structure.$type_bc.$sequence_id;

        return $num_immo;

    }

    function getEquipementImmobiliserByOption($valeur_option,$options_libelle){
        $equipement_immobiliser = DB::table('equipement_immobilisers as ei')
        ->join('option_equipements as oe','oe.ref_equipement','=','ei.ref_equipement')
        ->join('options as o','o.id','=','oe.options_id')
        ->where('o.libelle',$options_libelle)
        ->where('oe.valeur_option',$valeur_option)
        ->first();

        return $equipement_immobiliser;
    }

    function getEquipementImmobiliser($ref_equipement){
        $equipement_immobiliser = DB::table('equipement_immobilisers as ei')
        ->where('ei.ref_equipement',$ref_equipement)
        ->first();

        return $equipement_immobiliser;
    }

    function storeEquipementImmobiliser($ref_equipement,$magasin_stocks_id,$exercice){

        $equipement =  EquipementImmobiliser::where('ref_equipement',$ref_equipement)->first();

        if ($equipement === null) {

            $equipement = EquipementImmobiliser::create([
                'ref_equipement'=>$ref_equipement,
                'magasin_stocks_id'=>$magasin_stocks_id,
                'exercice'=>$exercice,
            ]);

        }

        return $equipement;

    }

    function storeOption($options_libelle){
        $option = Option::where('libelle',$options_libelle)->first();
        if ($option === null) {
            Option::create([
                'libelle'=>$options_libelle
            ]);
        }
        
    }
    function setAffectation($ref_equipement,$options_id,$valeur_option){

        $affectations = DB::table('affectations as a')
        ->join('option_equipements as oe','oe.ref_equipement','=','a.ref_equipement')
        ->where('oe.valeur_option',$valeur_option)
        ->where('oe.options_id',$options_id)
        ->orderByDesc('a.id')
        ->get();

        if($ref_equipement == 'ref_equipement'){
            dd($ref_equipement,$affectations);
        }

        foreach ($affectations as $affectation) {

            if ($ref_equipement != $affectation->ref_equipement) {

                Affectation::where('id',$affectation->id)
                ->update([
                    'flag_actif'=>2,
                ]);

            }
            
        }

    }

    function getDevises(){ return Devise::all(); }

    function getCotationFournisseursDevises(){
        $cotation_fournisseur = DB::table('cotation_fournisseurs as cf')
        ->join('devises as d','d.id','=','cf.devises_id')
        ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
        ->join('organisations as o','o.id','=','cf.organisations_id')
        ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
        ->select('d.libelle as devises_libelle','da.num_bc','o.entnum','o.denomination','cf.id')
        ->get();

        return $cotation_fournisseur;
    }

    function setCotationFournisseurDevise($cotation_fournisseurs_id,$data_cotation_fournisseurs){
        CotationFournisseur::where('id',$cotation_fournisseurs_id)->update($data_cotation_fournisseurs);
    }
    function setDetailCotationDevise($detail_cotations_id,$cotation_fournisseurs_id,$data_detail_cotations){
        DetailCotation::where('id',$detail_cotations_id)
        ->where('cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->update($data_detail_cotations);
    }

    function getCotationFournisseursDevise($cotation_fournisseurs_id){
        $cotation_fournisseur = DB::table('cotation_fournisseurs as cf')
        ->join('devises as d','d.id','=','cf.devises_id')
        ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
        ->join('organisations as o','o.id','=','cf.organisations_id')
        ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
        ->select('d.libelle as devises_libelle','da.num_bc','o.entnum','o.denomination','cf.id','cf.*')
        ->where('cf.id',$cotation_fournisseurs_id)
        ->first();

        return $cotation_fournisseur;
    }

    function getDetailCotationDevise($cotation_fournisseurs_id){

        $detail_cotations = DetailCotation::where('cotation_fournisseurs_id',$cotation_fournisseurs_id)
         ->get();

         return $detail_cotations;
     }
     
    function storeOptionEquipement($ref_equipement,$options_id,$valeur_option){

        $this->setAffectation($ref_equipement,$options_id,$valeur_option);

        $option_equipement = OptionEquipement::where('valeur_option',$valeur_option)
        ->where('options_id',$options_id)
        ->where('ref_equipement',$ref_equipement)
        ->first();

        if ($option_equipement != null) {
            OptionEquipement::where('id',$option_equipement->id)
            ->update([
                'valeur_option'=>$valeur_option,
            ]);
        }else{

            OptionEquipement::create([
                'ref_equipement'=>$ref_equipement,
                'options_id'=>$options_id,
                'valeur_option'=>$valeur_option,
            ]);
        }
        
    }

    function deleteOptionEquipement($ref_equipement,$options_id){
        OptionEquipement::where('ref_equipement',$ref_equipement)
        ->where('options_id',$options_id)
        ->delete();
    }

    function storeAffection($ref_equipement,$index,$detail_immobilisations_id,$type_affectations_id,$date_debut,$date_fin=null,$flag_actif){

        $affectation = Affectation::where('detail_immobilisations_id',$detail_immobilisations_id)
        ->where('index',$index)
        ->first();

        if ($affectation != null) {

            Affectation::where('id',$affectation->id)
            ->update([
                'ref_equipement'=>$ref_equipement,
                'index'=>$index,
                'detail_immobilisations_id'=>$detail_immobilisations_id,
                'type_affectations_id'=>$type_affectations_id,
                'flag_actif'=>$flag_actif
            ]);

            $affectation = Affectation::where('id',$affectation->id)
            ->where('index',$index)
            ->first();

        }else{

            $affectation = Affectation::create([
                'ref_equipement'=>$ref_equipement,
                'index'=>$index,
                'detail_immobilisations_id'=>$detail_immobilisations_id,
                'type_affectations_id'=>$type_affectations_id,
                'date_debut'=>$date_debut,
                'flag_actif'=>$flag_actif
            ]);

        }

        return $affectation;
    }

    function getOption($options_libelle){

        $option = Option::where('libelle',$options_libelle)->first();

        return $option;
        
    }

    function storeDateOperation($date){

        $date_operation = DateOperation::where('date',$date)->first();

        if ($date_operation === null) {
            $date_operation = DateOperation::create([
                'date'=>$date
            ]);
        }
        
        return $date_operation;
        
    }

    function storePieceJointeLivraison($subject_id,$profils_id,$libelle,$piece,$flag_actif,$name,$piece_jointes_id){

        $type_operation = TypeOperation::where('libelle',$libelle)->first();
        if ($type_operation!=null) {
            $type_operations_id = $type_operation->id;
        }else{
            $type_operations_id = TypeOperation::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($piece_jointes_id)) {
            PieceJointeLivraison::where('id',$piece_jointes_id)->update([
                'profils_id'=>$profils_id,
                'flag_actif'=>$flag_actif
            ]);
        }else{
            PieceJointeLivraison::create([
                'type_operations_id'=>$type_operations_id,
                'profils_id'=>$profils_id,
                'subject_id'=>$subject_id,
                'name'=>$name,
                'piece'=>$piece,
                'flag_actif'=>$flag_actif
    
            ]);
        }
        
    }

    function storeTypeStatutDemandeAchat($libelle){
        $type_statut_demande_achat = TypeStatutDemandeAchat::where('libelle',$libelle)->first();
                        
        if ($type_statut_demande_achat===null) {
            TypeStatutDemandeAchat::create([
                'libelle'=>$libelle
            ]);
        }
    }
    
    function getTypeStatutDemandeAchat($libelle){
        $type_statut_demande_achat = TypeStatutDemandeAchat::where('libelle',$libelle)->first();
        return $type_statut_demande_achat;
    }

    function storeTypeStatutEquipement($libelle){
        $type_statut_equipement = TypeStatutEquipement::where('libelle',$libelle)->first();
                        
        if ($type_statut_equipement===null) {
            TypeStatutEquipement::create([
                'libelle'=>$libelle
            ]);
        }
    }

    function getTypeStatutEquipement($libelle){
        $type_statut_equipement = TypeStatutEquipement::where('libelle',$libelle)->first();
        return $type_statut_equipement;
    }

    function setLastStatutEquipement($ref_equipement){
        $statut_equipement = StatutEquipement::where('ref_equipement',$ref_equipement)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_equipement != null) {
            StatutEquipement::where('id',$statut_equipement->id)->update([
                'date_fin'=>date('Y-m-d'),
            ]);
        }
    }

    function storeStatutEquipement($ref_equipement,$profils_id,$type_statut_equipements_id,$commentaire=null){

        $statut_equipement = StatutEquipement::create([
            'ref_equipement'=>$ref_equipement,
            'type_statut_equipements_id'=>$type_statut_equipements_id,
            'date_debut'=>date('Y-m-d'),
            'commentaire'=>trim($commentaire),
            'profils_id'=>$profils_id,
        ]);

        return $statut_equipement;
    }

    function storeTypeStatutRequisition($libelle){
        $type_statut_requisition = TypeStatutRequisition::where('libelle',$libelle)->first();
                        
        if ($type_statut_requisition===null) {
            TypeStatutRequisition::create([
                'libelle'=>$libelle
            ]);
        }
    }

    function getTypeStatutRequisition($libelle){
        return TypeStatutRequisition::where('libelle',$libelle)->first();
         
    }

    function setLastStatutDemandeAchat($demande_achats_id){
        $statut_demande_achat = StatutDemandeAchat::where('demande_achats_id',$demande_achats_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_demande_achat!=null) {
                StatutDemandeAchat::where('id',$statut_demande_achat->id)->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
        }
    }

    function setLastStatutLivraisonCommande($livraison_commandes_id){
        $statut_livraison_commande = StatutLivraisonCommande::where('livraison_commandes_id',$livraison_commandes_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_livraison_commande!=null) {
                StatutLivraisonCommande::where('id',$statut_livraison_commande->id)->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
        }
    }

    function setLastStatutRequisition($requisitions_id){
        $statut_requisition = StatutRequisition::where('requisitions_id',$requisitions_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_requisition!=null) {
                StatutRequisition::where('id',$statut_requisition->id)->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
        }
    }

    function storeStatutLivraisonCommande($livraison_commandes_id,$profils_id,$type_statut_demande_achats_id,$commentaire=null){

        return StatutLivraisonCommande::create([
            'livraison_commandes_id'=>$livraison_commandes_id,
            'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
            'date_debut'=>date('Y-m-d'),
            'date_fin'=>date('Y-m-d'),
            'commentaire'=>trim($commentaire),
            'profils_id'=>$profils_id,
        ]);
    }

    function storeStatutRequisition($requisitions_id,$profils_id,$type_statut_requisitions_id,$commentaire=null){

        $statut_requisition = StatutRequisition::create([
            'requisitions_id'=>$requisitions_id,
            'type_statut_requisitions_id'=>$type_statut_requisitions_id,
            'date_debut'=>date('Y-m-d'),
            'date_fin'=>date('Y-m-d'),
            'commentaire'=>trim($commentaire),
            'profils_id'=>$profils_id,
        ]);

        return $statut_requisition;
    }

    function setLastStatutImmobilisation($immobilisations_id){
        $statut_immobilisation = StatutImmobilisation::where('immobilisations_id',$immobilisations_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_immobilisation!=null) {
                StatutImmobilisation::where('id',$statut_immobilisation->id)->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
        }
    }

    function storeStatutImmobilisation($immobilisations_id,$profils_id,$type_statut_requisitions_id,$commentaire=null){

        $statut_immobilisation = StatutImmobilisation::create([
            'immobilisations_id'=>$immobilisations_id,
            'type_statut_requisitions_id'=>$type_statut_requisitions_id,
            'date_debut'=>date('Y-m-d'),
            'date_fin'=>date('Y-m-d'),
            'commentaire'=>trim($commentaire),
            'profils_id'=>$profils_id,
        ]);

        return $statut_immobilisation;
    }

    function getCountDetailImmobilisation($immobilisations_id){

        $qte_total = 0;

        $detail_immobilisation = DB::table('detail_immobilisations')
        ->select(DB::raw('SUM(qte) as qte_total'))
        ->where('immobilisations_id', $immobilisations_id)
        ->groupBy('immobilisations_id')
        ->first();

        if ($detail_immobilisation != null) {

            $qte_total = $detail_immobilisation->qte_total;

        }

        return $qte_total;

    }

    function getCountAffectationByImmobilisationId($immobilisations_id){

        $qte_total_affecte = count( DB::table('affectations as a')
        ->whereIn('a.detail_immobilisations_id', function ($query) use($immobilisations_id) {
            $query->select(DB::raw('di.id'))
                    ->from('detail_immobilisations as di')
                    ->where('di.immobilisations_id', $immobilisations_id)
                    ->whereRaw('a.detail_immobilisations_id = di.id');
        })
        ->get() );

        return $qte_total_affecte;

    }

    function setDetailDemandeAchat($detail_demande_achats_id,$demande_achats_id,$ref_articles,$qte,$profils_id,$description_articles_id,$echantillon){
    
        $detail_demande_achat = DetailDemandeAchat::where('id',$detail_demande_achats_id)
        ->update([
            'demande_achats_id' => $demande_achats_id,
            'ref_articles' => $ref_articles,
            'qte_demandee' => $qte,
            'profils_id'=>$profils_id,
            'description_articles_id'=>$description_articles_id,
            'echantillon'=>$echantillon
        ]);

        return $detail_demande_achat;
    }

    function setDetailDemandeAchat2($detail_demande_achats_id,$demande_achats_id,$ref_articles,$qte,$profils_id,$description_articles_id){
    
        $detail_demande_achat = DetailDemandeAchat::where('id',$detail_demande_achats_id)
        ->update([
            'demande_achats_id' => $demande_achats_id,
            'ref_articles' => $ref_articles,
            'qte_demandee' => $qte,
            'profils_id'=>$profils_id,
            'description_articles_id'=>$description_articles_id
        ]);

        return $detail_demande_achat;
    }

    function demandeAchatData($libelle,$type_profils_name,$libelle2=null){

        
        $demande_achats = [];
        if (isset($type_profils_name)) {
            if ($type_profils_name === "Administrateur fonctionnel"){
                $demande_achats = DemandeAchat::select('demande_achats.id as id', 'demande_achats.num_bc as num_bc', 'demande_achats.updated_at as updated_at','demande_achats.created_at as created_at', 'demande_achats.intitule as intitule')
                ->orderByDesc('demande_achats.updated_at')
                ->whereIn('demande_achats.id', function ($query) use($libelle) {
                    $query->select(DB::raw('sda.demande_achats_id'))
                            ->from('statut_demande_achats as sda')
                            ->join('type_statut_demande_achats as tsda', 'tsda.id', '=', 'sda.type_statut_demande_achats_id')
                            ->where('tsda.libelle', [$libelle])
                            ->whereRaw('demande_achats.id = sda.demande_achats_id');
                })
                ->get();
            }elseif ($type_profils_name === "Fournisseur") {


                $demande_achats = DemandeAchat::select('demande_achats.id as id','demande_achats.num_bc as num_bc','demande_achats.updated_at as updated_at','demande_achats.created_at as created_at','demande_achats.intitule as intitule')->orderByDesc('demande_achats.updated_at')
                ->whereIn('demande_achats.id', function($query) use($libelle){
                    $query->select(DB::raw('sda.demande_achats_id'))
                          ->from('statut_demande_achats as sda')
                          ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                          ->join('demande_achats as da','da.id','=','sda.demande_achats_id')
                          ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
                          ->join('criteres as c','c.id','=','ca.criteres_id')
                          ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
                          ->join('organisations as o','o.id','=','ps.organisations_id')
                          ->join('statut_organisations as so','so.organisations_id','=','o.id')
                          ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                          ->join('profils as p','p.id','=','so.profils_id')
                          ->join('users as u','u.id','=','p.users_id')
                          ->where('u.id',auth()->user()->id)
                          ->where('tso.libelle','Activé')
                          ->where('c.libelle','Fournisseurs Cibles')
                          ->where('tsda.libelle',[$libelle])
                          ->whereRaw('demande_achats.id = sda.demande_achats_id');
                })->get();

            }elseif ($type_profils_name === "Comite Réception") {

                $demande_achats = DemandeAchat::select('demande_achats.id as id', 'demande_achats.num_bc as num_bc', 'demande_achats.updated_at as updated_at','demande_achats.created_at as created_at', 'demande_achats.intitule as intitule')
                ->orderByDesc('demande_achats.updated_at')
                ->whereIn('demande_achats.id', function ($query) use($libelle,$libelle2) {
                    $query->select(DB::raw('sda.demande_achats_id'))
                            ->from('statut_demande_achats as sda')
                            ->join('type_statut_demande_achats as tsda', 'tsda.id', '=', 'sda.type_statut_demande_achats_id')
                            ->whereIn('tsda.libelle', [$libelle,$libelle2])
                            ->whereRaw('demande_achats.id = sda.demande_achats_id');
                })
                ->whereIn('demande_achats.ref_depot',function($query) use($type_profils_name){
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
                    ->whereRaw('demande_achats.ref_depot = st.ref_depot');
                })
                ->whereIn('demande_achats.id', function ($query){
                    $query->select(DB::raw('cr.demande_achats_id'))
                          ->from('comite_receptions as cr')
                          ->join('users as u','u.agents_id','=','cr.agents_id')
                          ->whereRaw('demande_achats.id = cr.demande_achats_id')
                          ->where('cr.flag_actif',1)
                          ->where('u.flag_actif',1)
                          ->where('u.id',auth()->user()->id);
                })
                ->get();

            }else{

                $demande_achats = DemandeAchat::select('demande_achats.id as id', 'demande_achats.num_bc as num_bc', 'demande_achats.updated_at as updated_at','demande_achats.created_at as created_at', 'demande_achats.intitule as intitule')
                ->orderByDesc('demande_achats.updated_at')
                ->whereIn('demande_achats.id', function ($query) use($libelle) {
                    $query->select(DB::raw('sda.demande_achats_id'))
                            ->from('statut_demande_achats as sda')
                            ->join('type_statut_demande_achats as tsda', 'tsda.id', '=', 'sda.type_statut_demande_achats_id')
                            ->where('tsda.libelle', [$libelle])
                            ->whereRaw('demande_achats.id = sda.demande_achats_id');
                })
                ->whereIn('demande_achats.ref_depot',function($query) use($type_profils_name){
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
                    ->whereRaw('demande_achats.ref_depot = st.ref_depot');
                })
                ->get();

            }
        }

        

        return $demande_achats;

    }

    function getStatutDemandeAchatGroupe($demande_achats_id,$type_statut_demande_achats_libelles){

        $statut_demande_achat = DB::table('statut_demande_achats as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')

            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')

            ->where('sda.demande_achats_id',$demande_achats_id)
            ->whereIn('tsda.libelle',$type_statut_demande_achats_libelles)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->select('tp.*','p.*','u.*','a.*','tsda.*','sda.*')
            ->first();

        return $statut_demande_achat;
        
    }

    function getStatutDemandeAchatGroupe2($type_statut_demande_achats_libelles,$type_statut_demande_achats_libelles2){

        $statut_demande_achat = DB::table('demande_achats as da')
            ->join('depots as d','d.ref_depot','=','da.ref_depot')
            ->whereIn('da.id', function($query) use($type_statut_demande_achats_libelles){
                $query->select(DB::raw('sda.demande_achats_id'))
                    ->from('statut_demande_achats as sda')
                    ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                    ->whereIn('tsda.libelle',$type_statut_demande_achats_libelles)
                    ->whereRaw('da.id = sda.demande_achats_id');
            })
            ->whereNotIn('da.id', function($query) use($type_statut_demande_achats_libelles2){
                $query->select(DB::raw('sda.demande_achats_id'))
                    ->from('statut_demande_achats as sda')
                    ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                    ->whereIn('tsda.libelle',$type_statut_demande_achats_libelles2)
                    ->whereRaw('da.id = sda.demande_achats_id');
            })
            ->get();

        return $statut_demande_achat;
        
    }

    function getStatutDemandeAchatGroupe3($type_statut_demande_achats_libelles,$type_statut_demande_achats_libelles2,$num_bc){
        $demande_achat = DB::table('demande_achats as da')
        ->join('depots as d','d.ref_depot','=','da.ref_depot')
        ->whereIn('da.id', function($query) use($type_statut_demande_achats_libelles){
            $query->select(DB::raw('sda.demande_achats_id'))
                  ->from('statut_demande_achats as sda')
                  ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                  ->whereIn('tsda.libelle',$type_statut_demande_achats_libelles)
                  ->whereRaw('da.id = sda.demande_achats_id');
        })
        ->whereNotIn('da.id', function($query) use($type_statut_demande_achats_libelles2){
            $query->select(DB::raw('sda.demande_achats_id'))
                  ->from('statut_demande_achats as sda')
                  ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                  ->whereIn('tsda.libelle',$type_statut_demande_achats_libelles2)
                  ->whereRaw('da.id = sda.demande_achats_id');
        })
        ->where('da.num_bc',$num_bc)
        ->select('da.id')
        ->first();

        return $demande_achat;
    }

    function getComiteReception($agents_id,$demande_achats_id){

        $comite_reception = ComiteReception::where('agents_id',$agents_id)
        ->where('demande_achats_id',$demande_achats_id)
        ->where('flag_actif',1)
        ->first();

        return $comite_reception;
        
    }

    function setComiteReception($demande_achats_id,$profils_id){

        $comite_reception = ComiteReception::where('demande_achats_id',$demande_achats_id)
        ->where('flag_actif',1)
        ->update([
            'flag_actif'=>0,
            'retrait_profils_id'=>$profils_id,
        ]);

        return $comite_reception;

    }

    function storeComiteReception($agents_id,$demande_achats_id,$flag_actif,$profils_id){
                    
        $comite_reception = ComiteReception::create([
            'agents_id'=>$agents_id,
            'demande_achats_id'=>$demande_achats_id,
            'flag_actif'=>$flag_actif,
            'profils_id'=>$profils_id,
        ]);

        return $comite_reception;
    }

    function getLastStatutDemandeAchat($demande_achats_id,$profils_id=null){

        if($profils_id != null){
            $statut_demande_achat_last = DB::table('statut_demande_achats as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_achats_id',$demande_achats_id)
            ->where('sda.profils_id',$profils_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
        }else{
            $statut_demande_achat_last = DB::table('statut_demande_achats as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_achats_id',$demande_achats_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
        }
        

        return $statut_demande_achat_last;
        
    }
    //type_statut_demande_achat
    function getLastStatutLivraisonCommande($livraison_commandes_id,$profils_id=null){

        if($profils_id != null){
            $statut_livraison_commande_last = DB::table('statut_livraison_commandes as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.livraison_commandes_id',$livraison_commandes_id)
            ->where('sda.profils_id',$profils_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
        }else{
            $statut_livraison_commande_last = DB::table('statut_livraison_commandes as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.livraison_commandes_id',$livraison_commandes_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
        }
        

        return $statut_livraison_commande_last;
        
    }

    function getStatutDemandeAchats($demande_achats_id){

        $statut_demande_achats = DB::table('statut_demande_achats as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_achats_id',$demande_achats_id)
            ->select('tsda.libelle','sda.demande_achats_id','sda.id as statut_demande_achats_id')
            ->orderBy('sda.created_at')
            ->get();

        return $statut_demande_achats;
        
    }

    function getDemandeAchats($demande_achats_id,$ref_fam=null){
        $demande_achats = [];
        if($ref_fam != null){
            $demande_achats = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->select('da.id as demande_achats_id','a.ref_articles','a.design_article','dda.qte_demandee','dda.qte_accordee','dda.flag_valide','dda.id','dda.qte_accordee','dda.echantillon as echantillon_cnps')
            ->where('dda.demande_achats_id',$demande_achats_id)
            ->where('a.ref_fam',$ref_fam)
            ->get();
        }else{
            $demande_achats = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->select('da.id as demande_achats_id','a.ref_articles','a.design_article','dda.qte_demandee','dda.qte_accordee','dda.flag_valide','dda.id','dda.qte_accordee','dda.echantillon as echantillon_cnps')
            ->where('dda.demande_achats_id',$demande_achats_id)
            ->get();
        }
        

        return $demande_achats;

    }

    function getValiderDemandeAchats($demande_achats_id){
            
        $demande_achats = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->select('da.id as demande_achats_id','a.ref_articles','a.design_article','dda.qte_demandee','dda.qte_accordee','dda.flag_valide','dda.id','dda.qte_accordee','dda.echantillon as echantillon_cnps')
            ->where('dda.demande_achats_id',$demande_achats_id)
            ->where('dda.flag_valide',1)
            ->get();

        return $demande_achats;

    }

    function getDemandeAchatValiders($demande_achats_id){

        $demande_achats = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('valider_demande_achats as vda','vda.detail_demande_achats_id','=','dda.id')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles','familles.ref_fam','=','a.ref_fam')
            ->select('da.id as demande_achats_id','a.ref_articles','a.design_article','dda.qte_demandee','dda.qte_accordee','dda.flag_valide','dda.id','dda.qte_accordee','dda.echantillon as echantillon_cnps')
            ->where('dda.demande_achats_id',$demande_achats_id)
            ->where('vda.flag_valide',1)
            ->get();

        return $demande_achats;

    }

    public function getDemandeAchat($demande_achats_id){
            
        $demande_achat_info = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('gestions as g','g.code_gestion','=','da.code_gestion')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
            ->join('structures as st','st.code_structure','=','cb.code_structure')
            ->join('profils as p','p.id','=','da.profils_id')
            ->join('type_profils as tp','tp.id','p.type_profils_id')
            ->join('depots as d', 'd.ref_depot', '=', 'st.ref_depot')
            ->select('st.code_structure','st.ref_depot', 'd.design_dep','da.taux_acompte','tp.name','st.nom_structure','da.created_at','da.intitule','da.id','g.code_gestion','g.libelle_gestion','f.design_fam','da.exercice','da.ref_fam','da.num_bc','da.*')
            ->where('da.id',$demande_achats_id)
            ->first();
        
        return $demande_achat_info;

    }

    function getLastCotationFournisseur($demande_achats_id){
        $cotation_fournisseur = DB::table('demande_achats')
            ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
            ->orderByDesc('selection_adjudications.id')
            ->limit(1)
            ->where('demande_achats.id',$demande_achats_id)
            ->first();

        return $cotation_fournisseur;
    }

    function getDetailCotationFournisseursSelectionnees($demande_achats_id,$cotation_fournisseurs_id){
                
        $demande_achats = DB::table('demande_achats as da')

            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id') //nouvelle ligne

            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
            
            ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')
            ->join('articles as a','a.ref_articles','=','dc.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')

            ->whereRaw('dda.ref_articles = dc.ref_articles')//nouvelle ligne

            ->where('da.id',$demande_achats_id)
            ->where('dc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
            ->select('dda.echantillon as echantillon_cnps','da.*','cf.*','sa.*','dc.id as detail_cotations_id','dc.*','a.*','f.*','da.*')
            ->get();

            

        return $demande_achats;
    }

    function getLivraisons($demande_achats_id,$cotation_fournisseurs_id){
        $demande_achats = DB::table('demande_achats as da')

                    ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id') //nouvelle ligne

                    ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
                    ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')
                    ->join('detail_livraisons as dl','dl.detail_cotations_id','=','dc.id')
                    ->join('articles as a','a.ref_articles','=','dc.ref_articles')
                    ->join('familles as f','f.ref_fam','=','a.ref_fam')
                    ->where('da.id',$demande_achats_id)
                    ->where('dc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
                    ->select('cf.*','dc.*','dl.*','a.*','dc.id as detail_cotations_id','dl.id as detail_livraisons_id','dda.echantillon as echantillon_cnps','dc.id as detail_cotations_id','f.*','da.*')
                    ->whereRaw('dl.qte > 0')

                    ->whereRaw('dda.ref_articles = dc.ref_articles')//nouvelle ligne

                    ->get();
        return $demande_achats;
    }

    function getDetailLivraisonCommandesById($livraison_commandes_id){
        $demande_achats = DB::table('demande_achats as da')

            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id') //nouvelle ligne

            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')
            
            ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')

            ->join('detail_cotations as dc','dc.id','=','dl.detail_cotations_id')

            ->join('articles as a','a.ref_articles','=','dc.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')

            ->where('lc.id',$livraison_commandes_id)
            ->select('cf.*','dc.*','dc.qte as qte_cmd','a.*','dl.id as detail_livraisons_id','dda.echantillon as echantillon_cnps','dc.id as detail_cotations_id','f.*','da.*','lc.*','dl.*','dl.prix_unit','dl.remise','dl.montant_ht','dl.montant_ttc')
            ->whereRaw('dl.qte > 0')

            ->whereRaw('dda.ref_articles = dc.ref_articles')//nouvelle ligne

            ->get();
        return $demande_achats;
    }

    function getDetailCotationFournisseurs($demande_achats_id,$cotation_fournisseurs_id){
                
        $demande_achats = DB::table('demande_achats as da')

            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id') //nouvelle ligne

            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')
            ->join('articles as a','a.ref_articles','=','dc.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')

            ->whereRaw('dda.ref_articles = dc.ref_articles')//nouvelle ligne

            ->where('da.id',$demande_achats_id)
            ->where('dc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
            ->select('dda.echantillon as echantillon_cnps','da.*','cf.*','dc.id as detail_cotations_id','dc.*','a.*','f.*','da.*')
            ->get();

        return $demande_achats;
    }

    function getDetailCotationFournisseur($demande_achats_id,$cotation_fournisseurs_id){
                
        return DB::table('credit_budgetaires as cb')
            ->join('demande_achats as da','da.credit_budgetaires_id','=','cb.id')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('gestions as g','g.code_gestion','=','da.code_gestion')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('organisations as o','o.id','=','cf.organisations_id')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('commandes as c','c.demande_achats_id','=','da.id')
            ->join('periodes as p','p.id','=','c.periodes_id')
            ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')
            ->join('devises as d','d.id','=','cf.devises_id')
            ->select('cf.net_a_payer','cf.taux_de_change','o.entnum','da.created_at','da.intitule','da.num_bc','da.id','g.code_gestion','g.libelle_gestion','f.ref_fam','f.design_fam','da.exercice','o.id as organisations_id','o.denomination','o.contacts','p.id as periodes_id','p.libelle_periode','c.delai','c.date_echeance','c.date_livraison_prevue','da.credit_budgetaires_id','d.code as code_devise','d.libelle as libelle_devise','d.symbole','cf.acompte','cf.taux_acompte','cf.montant_acompte','da.flag_engagement','cb.code_structure','da.ref_depot')
            ->where('cf.id',$cotation_fournisseurs_id)
            ->where('da.id',$demande_achats_id)
            ->where('dda.flag_valide',1)
            ->where('tso.libelle','Activé')
            ->first();
    }

    function getLivraisonCommandeById($livraison_commandes_id){
                
        $demande_achat_info = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('gestions as g','g.code_gestion','=','da.code_gestion')
            ->join('articles as a','a.ref_articles','=','dda.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')
            ->join('organisations as o','o.id','=','cf.organisations_id')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('commandes as c','c.demande_achats_id','=','da.id')
            ->join('periodes as p','p.id','=','c.periodes_id')
            ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')

            ->join('devises as d','d.id','=','cf.devises_id')
            
            ->select('lc.net_a_payer','lc.taux_de_change'
            
            ,'lc.num_bl','lc.montant_total_brut','lc.taux_remise_generale','lc.remise_generale','lc.montant_total_net','lc.tva','lc.montant_total_ttc','lc.assiete_bnc','lc.taux_bnc','lc.net_a_payer','lc.taux_de_change','o.entnum','da.intitule','da.num_bc','da.id','g.code_gestion','g.libelle_gestion','f.ref_fam','f.design_fam','da.exercice','o.id as organisations_id','o.denomination','p.id as periodes_id','p.libelle_periode','c.delai','c.date_echeance','c.date_livraison_prevue','da.credit_budgetaires_id','d.code as code_devise','d.libelle as libelle_devise','da.taux_acompte','cf.acompte','cf.montant_acompte','lc.created_at','lc.id as livraison_commandes_id','lc.validation')
            ->where('lc.id',$livraison_commandes_id)
            ->where('dda.flag_valide',1)
            ->where('tso.libelle','Activé')
            ->first();

        return $demande_achat_info;
    }

    function getDemandeAchatVisa($demande_achats_id,$libelle_visa){
                
        $signataire = DB::table('demande_achats as da')
        ->join('familles as f','f.ref_fam','=','da.ref_fam')
        ->join('statut_demande_achats as sda','sda.demande_achats_id','=','da.id')
        ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
        ->join('profils as p','p.id','=','sda.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('sda.demande_achats_id',$demande_achats_id)
        ->where('tsda.libelle',$libelle_visa)
        ->orderByDesc('sda.id')
        ->limit(1)
        ->first(); 

        return $signataire;
    }

    function getSignataires($demande_achats_id){
                
        $signataires = DB::table('demande_achats as da')
        ->join('signataire_demande_achats as sda','sda.demande_achats_id','=','da.id')
        ->join('profil_fonctions as pf','pf.id','=','sda.profil_fonctions_id')
        ->join('agents as a','a.id','=','pf.agents_id')
        ->where('sda.demande_achats_id',$demande_achats_id)
        ->where('sda.flag_actif',1)
        ->get(); 

        return $signataires;
    }

    function getDetailLivraisons($cotation_fournisseurs_id){
        $livraison_commandes = DB::table('livraison_commandes as lc')
        ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')
        ->where('lc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->get();

        return $livraison_commandes;
    }

    function getPieceJointes($demande_achats_id,$type_piece){

        $piece_jointes = DB::table('piece_jointes as pj')
        ->join('type_operations as to','to.id','=','pj.type_operations_id')
        ->where('to.libelle',$type_piece)
        ->where('pj.subject_id',$demande_achats_id)
        ->select('pj.id','pj.piece','pj.name','pj.flag_actif')
        ->where('flag_actif',1)
        ->get(); 

        return $piece_jointes;
    }



    function controlEchantionnageCnps($demande_achats_id){

        $echantillonnage_cnps = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->where('da.id',$demande_achats_id)
            ->whereNotNull('dda.echantillon')
            ->first();

        return $echantillonnage_cnps;
        
    }

    function getCategorieDemandeAchat($demande_achats_id){

        $demande_achat = DB::table('demande_achats as da')
            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
            ->where('da.id',$demande_achats_id)
            ->whereIn('da.id',function($query){
                $query->select(DB::raw('da2.id'))
                        ->from('demande_achats as da2')
                        ->join('statut_demande_achats as sda','sda.demande_achats_id','=','da2.id')
                        ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                        ->whereIn('tsda.libelle',['Visé (Responsable DMP)','Validé'])
                        ->whereRaw('da.id = da2.id');
            })
            ->first();
            
        if ($demande_achat!=null) {
            return 'bon_commande';
        }else{
            return 'demande_cotation';
        }

    }

    function getDeleteDetailDemandeAchat($demande_achats_id,$donnees){

        $detail_demande_achat_deletes = DetailDemandeAchat::where('demande_achats_id',$demande_achats_id)
            ->whereNotIn('ref_articles',$donnees)
            ->select('id','ref_articles')
            ->get();

        return $detail_demande_achat_deletes;
        
    }

    function deleteDemandeAchatCascade($demande_achats_id,$detail_demande_achat_delete_id,$detail_demande_achat_delete_ref_articles){
        
        $valider_demande_achat = ValiderDemandeAchat::where('detail_demande_achats_id', $detail_demande_achat_delete_id)
        ->first();

        if ($valider_demande_achat!=null) {

            $cotation_fournisseur = CotationFournisseur::where('demande_achats_id', $demande_achats_id)
            ->first();
            if ($cotation_fournisseur!=null) {

                $detail_cotation = DetailCotation::where('cotation_fournisseurs_id', $cotation_fournisseur->id)

                ->where('ref_articles', $detail_demande_achat_delete_ref_articles)
                ->first();
                if ($detail_cotation!=null) {

                } else {
                    $delete = 1;
                }
            } else {
                $delete = 1;
            }
        }else{
            DetailDemandeAchat::where('id', $detail_demande_achat_delete_id)->delete();
        }

        if (isset($delete)) {
            $valider_demande_achat = ValiderDemandeAchat::where('detail_demande_achats_id', $detail_demande_achat_delete_id)
            ->delete();

            DetailDemandeAchat::where('id', $detail_demande_achat_delete_id)->delete();

            unset($delete);
        }

    }

    function getDetailDemandeAchatByRefArticle($demande_achats_id,$ref_articles){
        return DetailDemandeAchat::where('demande_achats_id',$demande_achats_id)
        ->where('ref_articles',$ref_articles)
        ->first();        
    }

    function validerDemandeAchatControlAcces($etape,$type_profils_lists,$type_profils_name,$request=null){

        $profil = null;

        if ($etape === "create" or $etape === "store") {

            $profil = $this->controlleurUserDemandeAchat(Session::get('profils_id'),$type_profils_lists,$request);
                
        }

        return $profil;
    }

    function setDetailDemandeAchat3($detail_demande_achat_id,$qte_validee,$flag_valide){

        if($flag_valide === false){
            DetailDemandeAchat::where('id',$detail_demande_achat_id)->update([
                'qte_accordee' => null,
                'flag_valide'=>$flag_valide,
            ]);
        }else{
            DetailDemandeAchat::where('id',$detail_demande_achat_id)->update([
                'qte_accordee' => $qte_validee,
                'flag_valide'=>$flag_valide,
            ]);
        }
        

    }

    function deleteValiderDemandeAchat($detail_demande_achat_id){
        ValiderDemandeAchat::where('detail_demande_achats_id',$detail_demande_achat_id)->delete();
    }

    function notifDemandeAchat2($subject,$demande_achats_id,$profils){
        // Gestionnaire des achats
                    
            $profil_gestionnaire_achats = DB::table('profils as p')
            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as a', 'a.id', '=', 'u.agents_id')
            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
            ->join('sections as s', 's.id', '=', 'ase.sections_id')
            ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
            ->whereIn('tp.name', $profils)
            ->select('u.email')
            ->where('p.flag_actif',1)
            ->where('tsase.libelle','Activé')
            ->whereIn('st.ref_depot',function($query) use($demande_achats_id){
                $query->select(DB::raw('da.ref_depot'))
                    ->from('demande_achats as da')
                    ->where('da.id',$demande_achats_id)
                    ->whereRaw('st.ref_depot = da.ref_depot');
            })
            ->get();

            foreach ($profil_gestionnaire_achats as $profil_gestionnaire_achat) {

                $this->notifDemandeAchat($profil_gestionnaire_achat->email,$subject,$demande_achats_id);

            }
        //
    }

    function getTypeAchats(){
        $type_achats = TypeAchat::all();

        return $type_achats;
    }

    function getCommande($demande_achats_id){
            
        $commande = DB::table('commandes')
        ->join('periodes','periodes.id','=','commandes.periodes_id')
        ->where('commandes.demande_achats_id',$demande_achats_id)
        ->first();

        return $commande;
    }

    function getTypeAchatDemandeAchat($demande_achats_id){

        $type_achat = DB::table('type_achats as ta')
        ->join('demande_achats as da','da.type_achats_id','=','ta.id')
        ->where('da.id',$demande_achats_id)
        ->first();

        return $type_achat;
        
    }

    function getTypeAchatDemandeAchats(){
        return DB::table('type_achats')->get();
    }

    function getPeriodes(){
        return Periode::all();
    }

    function getOrganisationArticleByRefFamAndRefDepot($ref_fam,$ref_depot){

        $organisations = OrganisationArticle::where('ref_fam',$ref_fam)
        ->join('organisations','organisations.id','=','organisation_articles.organisations_id')
        ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
        ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
        ->join('organisation_depots as od','od.organisations_id','=','organisations.id')
        ->where('type_statut_organisations.libelle','Activé')
        ->where('organisation_articles.flag_actif',1)
        ->where('od.ref_depot',$ref_depot)
        ->select('organisations.id','organisations.denomination')
        ->get();

        return $organisations;
    }

    function getPreselectionSoumissionnaires($demande_achats_id){
        $preselections = DB::table('organisations')
            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
            ->join('profils','profils.id','=','statut_organisations.profils_id')
            ->join('users','users.id','=','profils.users_id')
            ->join('agents','agents.id','=','users.agents_id')
            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
            ->where('type_statut_organisations.libelle','Activé')
            ->where('critere_adjudications.demande_achats_id',$demande_achats_id)
            ->where('criteres.libelle','Fournisseurs Cibles')
            ->get();

        return $preselections;
    }

    function getPreselectionSoumissionnaire($demande_achats_id){

        $preselections = DB::table('preselection_soumissionnaires')
            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
            ->where('critere_adjudications.demande_achats_id',$demande_achats_id)
            ->where('criteres.libelle','Fournisseurs Cibles')
            ->where('preselection_soumissionnaires.organisations_id',null)
            ->first();

        return $preselections;

    }

    function getOrganisations(){

        $organisations = DB::table('organisations as o')
            ->join('statut_organisations as so','so.organisations_id','=','o.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->select('o.id','o.entnum','o.denomination','o.contacts','o.updated_at','tso.libelle','a.mle','a.nom_prenoms','u.email')
            ->orderByDesc('o.updated_at')
            ->get();

        return $organisations;
    }



    function getDepots(){
        $depots = Depot::all();

        return $depots;
    }

    function getTypeProfilFournisseur(){

        $fournisseur = TypeProfil::join('profils as p','p.type_profils_id','=','type_profils.id')
        ->where('type_profils.name','Fournisseur')
        ->get();

        return $fournisseur;
    }

    function getFournisseursEntnumNull(){

        $organisation = Organisation::whereNull('entnum')->get();

        return $organisation;
    }

    function getFamilleByDesignFam($design_fam){
                            
        $famille = Famille::where('design_fam',$design_fam)->first();

        return $famille;
    }

    function getDepotDesignDep($design_dep){
        return Depot::where('design_dep',$design_dep)
        ->first();
    }

    public function getMagasinByIdDepot($ref_depot){
        return DB::table('magasins as m')
        ->join('depots as d','d.id','=','m.depots_id')
        ->where('d.ref_depot',$ref_depot)
        ->select('m.ref_magasin')
        ->first();
    }

    function storeProfil($users_id,$type_profils_id){
                    
        $profil = Profil::create([
            'users_id'=>$users_id,
            'type_profils_id'=>$type_profils_id,
        ]);

        return $profil;
    }

    function getProfil($users_id,$type_profils_id){

        $profil = Profil::where('users_id',$users_id)
                        ->where('type_profils_id',$type_profils_id)
                        ->first();

        return $profil;
    }

    function getTypeOrganisation($type_profils_name_fournisseur){
            
        $type_organisation = TypeOrganisation::where('libelle',$type_profils_name_fournisseur)
        ->first();

        return $type_organisation;
    }

    function storeTypeOrganisation($type_profils_name_fournisseur){

        $type_organisation = TypeOrganisation::create([
            'libelle' => $type_profils_name_fournisseur
        ]);

        return $type_organisation;
    }

    function storeOrganisation($entnum=null,$denomination,$nom_prenoms,$type_organisations_id,$contacts,$adresse,$num_contribuable){
            
        $organisation = Organisation::create([
            'entnum' => $entnum,
            'denomination' => $denomination,
            'sigle' => $nom_prenoms,
            'type_organisations_id' => $type_organisations_id,
            'contacts' => $contacts,
            'adresse' => $adresse,
            'num_contribuable' => $num_contribuable,
        ]);

        return $organisation;
    }

    function setOrganisation($entnum,$denomination,$nom_prenoms,$type_organisations_id,$contacts,$adresse,$num_contribuable,$organisations_id){

        Organisation::where('id',$organisations_id)->update([
            'entnum' => $entnum,
            'denomination' => $denomination,
            'sigle' => $nom_prenoms,
            'type_organisations_id' => $type_organisations_id,
            'contacts' => $contacts,
            'adresse' => $adresse,
            'num_contribuable' => $num_contribuable,
        ]);

    }

    function getOrganisationArticleByOrganisationByRefFam($organisations_id,$ref_fam){
                        
        $organisation_article = OrganisationArticle::where('organisations_id',$organisations_id)
        ->where('ref_fam',$ref_fam)
        ->first();

        return $organisation_article;
    }

    function setOrganisationArticle($organisation_articles_id,$organisations_id,$ref_fam){
                        
        OrganisationArticle::where('id',$organisation_articles_id)
                            ->update([
                                'organisations_id' => $organisations_id,
                                'ref_fam' => $ref_fam,
                                'flag_actif' => 1
                            ]);

    }

    function storeOrganisationArticle($organisations_id,$ref_fam){
                        
        $organisation_article = OrganisationArticle::create([
                                'organisations_id' => $organisations_id,
                                'ref_fam' => $ref_fam,
                                'flag_actif' => 1
                            ]);

        return $organisation_article;

    }

    function getTypeStatutOrganisationArticle($libelle){

        $type_statut_organisation_article = TypeStatutOrganisationArticle::where('libelle',$libelle)->first();

        return $type_statut_organisation_article;
    }

    function storeTypeStatutOrganisationArticle($libelle){
        
        $type_statut_org_article = TypeStatutOrganisationArticle::create([
            'libelle'=>$libelle
        ]);

        return $type_statut_org_article;
    }

    function setLastStatutOrganisationArticle($organisation_articles_id){
        $statut_organisation_article = StatutOrganisationArticle::where('organisation_articles_id',$organisation_articles_id)
                                        ->orderByDesc('id')
                                        ->limit(1)
                                        ->first();
                                        
            if ($statut_organisation_article!=null) {
                StatutOrganisationArticle::where('id',$statut_organisation_article->id)
                ->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
            }
    }

    function storeStatutOrganisationArticle($organisation_articles_id,$type_statut_org_articles_id,$profils_id,$commentaire){

        StatutOrganisationArticle::create([
            'organisation_articles_id'=>$organisation_articles_id,
            'type_statut_org_articles_id'=>$type_statut_org_articles_id,
            'date_debut'=>date('Y-m-d'),
            'profils_id'=>$profils_id,
            'commentaire'=>$commentaire,
        ]);

    }

    function getOrganisationDepotRefDepot($organisations_id,$ref_depot){
                        
        $organisation_depot = OrganisationDepot::where('organisations_id', $organisations_id)
        ->where('ref_depot', $ref_depot)
        ->first();

        return $organisation_depot;
    }

    function setOrganisationDepot($organisation_depots_id,$organisations_id,$ref_depot){

        OrganisationDepot::where('id', $organisation_depots_id)->update([
            'organisations_id'=>$organisations_id,
            'ref_depot'=>$ref_depot,
            'date_debut' => date("Y-m-d"),
            'flag_actif' => 1
        ]);
        
    }

    function storeOrganisationDepot($organisations_id,$ref_depot){

        $organisation_depot = OrganisationDepot::create([
            'organisations_id'=>$organisations_id,
            'ref_depot'=>$ref_depot,
            'date_debut' => date("Y-m-d"),
            'flag_actif' => 1
        ]);

        return $organisation_depot;
    }

    function getTypeStatutOrganisationDepot($libelle){

        $type_statut_organisation_depot = TypeStatutOrganisationDepot::where('libelle',$libelle)->first();

        return $type_statut_organisation_depot;
    }

    function storeTypeStatutOrganisationDepot($libelle){
        
        $type_statut_org_depot = TypeStatutOrganisationDepot::create([
            'libelle'=>$libelle
        ]);

        return $type_statut_org_depot;
    }

    function setLastStatutOrganisationDepot($organisation_depots_id){
        $statut_organisation_depot = StatutOrganisationDepot::where('organisation_depots_id',$organisation_depots_id)
                                        ->orderByDesc('id')
                                        ->limit(1)
                                        ->first();
                                        
            if ($statut_organisation_depot!=null) {
                StatutOrganisationDepot::where('id',$statut_organisation_depot->id)
                ->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
            }
    }

    function storeStatutOrganisationDepot($organisation_depots_id,$type_statut_org_depots_id,$profils_id,$commentaire){

        StatutOrganisationDepot::create([
            'organisation_depots_id'=>$organisation_depots_id,
            'type_statut_org_depots_id'=>$type_statut_org_depots_id,
            'date_debut'=>date('Y-m-d'),
            'profils_id'=>$profils_id,
            'commentaire'=>$commentaire,
        ]);

    }

    function statut_profil($profils_ids,$libelle,$profils_id,$commentaire){

        $type_statut_profil = TypeStatutProfil::where('libelle',$libelle)->first();
        if ($type_statut_profil!=null) {
            $type_statut_profils_id = $type_statut_profil->id;
        }else{
            $type_statut_profils_id = TypeStatutProfil::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($type_statut_profils_id)) {

            $statut_profil = StatutProfil::where('profils_ids',$profils_ids)
                                        ->orderByDesc('id')
                                        ->limit(1)
                                        ->first();
            if ($statut_profil!=null) {
                StatutProfil::where('id',$statut_profil->id)
                ->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
            } 

            StatutProfil::create([
                'profils_id'=>$profils_id,
                'type_statut_profils_id'=>$type_statut_profils_id,
                'date_debut'=>date('Y-m-d'),
                'profils_ids'=>$profils_ids,
                'commentaire'=>$commentaire,
            ]);
            

        }
    }

    function statut_organisation($organisations_id,$libelle,$profils_id,$profils_ids){

        $type_statut_organisation = TypeStatutOrganisation::where('libelle',$libelle)->first();
        if ($type_statut_organisation!=null) {
            $type_statut_organisations_id = $type_statut_organisation->id;
        }else{
            $type_statut_organisations_id = TypeStatutOrganisation::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($type_statut_organisations_id)) {

            $statut_organisation = StatutOrganisation::where('organisations_id',$organisations_id)
                ->orderByDesc('id')
                ->limit(1)
                ->first();
            if ($statut_organisation!=null) {

                if ($statut_organisation->profils_id != $profils_id) {

                    $type_statut_organisation = TypeStatutOrganisation::where('libelle','Désactivé')->first();
                    if ($type_statut_organisation!=null) {
                        $type_statut_organisations_id2 = $type_statut_organisation->id;
                    }else{
                        $type_statut_organisations_id2 = TypeStatutOrganisation::create([
                            'libelle'=>'Désactivé'
                        ])->id;
                    }

                    StatutOrganisation::where('id',$statut_organisation->id)
                    ->update([
                        'date_fin'=>date('Y-m-d'),
                        'type_statut_organisations_id'=>$type_statut_organisations_id2
                    ]);

                }

                
            } 

            $statut_org = StatutOrganisation::where('organisations_id',$organisations_id)
                    ->where('type_statut_organisations_id',$type_statut_organisations_id)
                    ->where('profils_id',$profils_id)
                    ->first();
            if ($statut_org!=null) {
                StatutOrganisation::where('id',$statut_org->id)
                ->update([
                    'profils_ids'=>$profils_ids,
                ]);
            }else{
                StatutOrganisation::create([
                    'organisations_id'=>$organisations_id,
                    'type_statut_organisations_id'=>$type_statut_organisations_id,
                    'date_debut'=>date('Y-m-d'),
                    'profils_id'=>$profils_id,
                    'profils_ids'=>$profils_ids,
                ]);
            }
            
            

        }
    }

    function getOrganisationArticle($organisations_id){

        $organisation_articles = OrganisationArticle::where('organisations_id',$organisations_id)
        ->join('familles as f','f.ref_fam','=','organisation_articles.ref_fam')
        ->where('organisation_articles.flag_actif',1)
        ->get();

        return $organisation_articles;
        
    }

    function getOrganisationDepot($organisations_id){
            
        $organisation_depots = OrganisationDepot::where('organisations_id',$organisations_id)
        ->join('depots as d','d.ref_depot','=','organisation_depots.ref_depot')
        ->where('organisation_depots.flag_actif',1)
        ->get();

        return $organisation_depots;
    }

    function getOrganisation2($organisations_id){

        $organisations = Organisation::where('organisations.id',$organisations_id)
            ->join('statut_organisations as so','so.organisations_id','=','organisations.id')
            ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
            ->join('profils as p','p.id','=','so.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->select('so.profils_id','so.organisations_id','nom_prenoms','mle','email','tso.libelle','organisations.denomination','organisations.entnum','u.agents_id','contacts','organisations.adresse','organisations.num_contribuable')
            ->first();

        return $organisations;
    }

    function getAgent2($mle){

        $agent = Agent::where('mle',$mle)->first();

        return $agent;

    }

    function getAgent3($mle){

        $agent = Agent::join('users as u','u.agents_id','=','agents.id')
        ->where('agents.mle',$mle)
        ->select('agents.id as agents_id','agents.mle as mle','u.id as users_id')
        ->first();

        return $agent;

    }

    function getOrganisationArticles($organisations_id,$ref_fam=null){

        if($ref_fam != null){
            $organisation_articles = OrganisationArticle::where('organisations_id',$organisations_id)
            ->where('ref_fam',$ref_fam)
            ->get();
        }else{
            $organisation_articles = OrganisationArticle::where('organisations_id',$organisations_id)->get();
        }
       

       return $organisation_articles;

    }

    public function getOrganisationDepots($organisations_id,$ref_depot=null){

        if($ref_depot != null){
            $organisation_depots = OrganisationDepot::where('organisations_id',$organisations_id)
            ->where('ref_depot',$ref_depot)
            ->get();
        }else{
            $organisation_depots = OrganisationDepot::where('organisations_id',$organisations_id)->get();
        }
        
 
        return $organisation_depots;
 
    }

    function getOrganisationByFamilleByDepots($ref_fam,$ref_depot){
                        
        $all_organisations = DB::table('organisation_articles as oa')
        ->join('organisations as o', 'o.id', '=', 'oa.organisations_id')
        ->join('statut_organisations as so', 'so.organisations_id', '=', 'o.id')
        ->join('type_statut_organisations as tso', 'tso.id', '=', 'so.type_statut_organisations_id')
        ->join('organisation_depots as od', 'od.organisations_id', '=', 'o.id')
        ->where('tso.libelle', 'Activé')
        ->where('oa.flag_actif', 1)
        ->where('ref_fam', $ref_fam)
        ->where('od.ref_depot', $ref_depot)
        ->where('tso.libelle','Activé')
        ->get();

        return $all_organisations;
    }

    function getPeriode($libelle_periode){
        return Periode::where('libelle_periode',$libelle_periode)->first();
    }

    function deleteCascade($demande_achats_id){

        try {
            Commande::where('demande_achats_id',$demande_achats_id)->delete();
        } catch (\Throwable $th) {
            return redirect()->back()->with('error',$th->getMessage());
        }

        
        $critere_adjudication = CritereAdjudication::where('demande_achats_id',$demande_achats_id)->first();

        if ($critere_adjudication != null) {
            $critere_adjudications_id = $critere_adjudication->id;
            try {

                

                PreselectionSoumissionnaire::where('critere_adjudications_id',$critere_adjudications_id)->delete();

                CritereAdjudication::where('demande_achats_id',$demande_achats_id)->delete();

                ValiderDemandeAchat::join('detail_demande_achats as dda','dda.id','=','valider_demande_achats.detail_demande_achats_id')
                ->where('dda.demande_achats_id',$demande_achats_id)
                ->delete();

            } catch (\Throwable $th) {
                return redirect()->back()->with('error',$th->getMessage());
            }
            
        }

        
    }

    function getFamilleDemandeAchat($ref_fam){

        $credit_budgetaires =  DB::table('familles as f')
            ->join('demande_achats as da','da.ref_fam','=','f.ref_fam')
            ->where('da.ref_fam',$ref_fam)
            ->get();

        return $credit_budgetaires;

    }

    function getCotationFournisseur($demande_achats_id,$cotation_fournisseurs_id){

        $cotation_demande_achat = DB::table('demande_achats as da')
                ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
                ->join('gestions as g','g.code_gestion','=','da.code_gestion')
                ->join('articles as a','a.ref_articles','=','dda.ref_articles')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->join('profils as p','p.id','=','da.profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
                ->join('organisations as o','o.id','=','cf.organisations_id')
                ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')
                ->select('da.created_at','da.intitule','da.id','g.code_gestion','g.libelle_gestion','f.ref_fam','f.design_fam','da.exercice','cf.organisations_id','cf.taux_remise_generale','cf.delai','cf.date_echeance','cf.acompte','cf.montant_total_brut','cf.remise_generale','cf.montant_total_net','cf.tva','cf.montant_total_ttc','cf.assiete_bnc','cf.taux_bnc','cf.net_a_payer','cf.taux_acompte','cf.montant_acompte','cf.id as cotation_fournisseurs_id','da.num_bc','cf.devises_id','o.id as organisations_id','o.entnum','o.denomination')
                ->where('cf.id',$cotation_fournisseurs_id)
                ->where('da.id',$demande_achats_id)
                ->where('dda.flag_valide',1)
                ->first();

                return $cotation_demande_achat;
    }

    function getStatutDemandeAchatFournisseur($cotation_fournisseurs_id,$demande_achats_id){

        $statut_demande_achat = DB::table('statut_demande_achats as sda')
        ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
        ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','sda.demande_achats_id')
        ->join('organisations as o','o.id','=','cf.organisations_id')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')

        ->join('profils as p','p.id','=','sda.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')

        ->whereRaw('so.profils_id = sda.profils_id')
        ->where('cf.id',$cotation_fournisseurs_id)
        ->where('sda.demande_achats_id',$demande_achats_id)
        ->orderByDesc('sda.id')
        ->limit(1)
        ->first();

        return $statut_demande_achat;
    }

    function getDelaiCommande($demande_achats_id,$type_profils_name){

        $error = null;

        if ($type_profils_name === 'Fournisseur') {

            $date_actuelle = date('Y-m-d H:i:s');

            $commande = Commande::where('demande_achats_id',$demande_achats_id)->first();

            if ($commande!=null) {
                $date_echeance = $commande->date_echeance;

                if ($date_actuelle > $date_echeance) {
                    $error = 'Le délai de réponse est dépassé';
                }
            }

        }

        return $error;

    }

    function getDevise($code_devise,$libelle_devise){
        //controle
        $devises_id = null;

        $devise = Devise::where('code',$code_devise)->where('libelle',$libelle_devise)->first();

        if ($devise != null) {
            $devises_id = $devise->id;
        }

        return $devises_id;
    }

    public function getDeviseByLibelle($devises_libelle){
        return Devise::where('libelle',$devises_libelle)->first();
    }

    function getCritereAdjudication($criteres_id,$demande_achats_id){

        $critere_adjudication = CritereAdjudication::where('criteres_id',$criteres_id)
        ->where('demande_achats_id',$demande_achats_id)
        ->first();

        return $critere_adjudication;
    }

    function getDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id){
                            
        $detail_adjudication = DetailAdjudication::where('cotation_fournisseurs_id',$cotation_fournisseurs_id)->where('critere_adjudications_id',$critere_adjudications_id)->first();

        return $detail_adjudication;
    }

    function storeDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$valeur){
                                
        $detail_adjudication = DetailAdjudication::create([
            'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
            'critere_adjudications_id'=>$critere_adjudications_id,
            'valeur'=>$valeur,
        ]); 

        return $detail_adjudication; 
    }

    function setDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$valeur){
                                
        $detail_adjudication = DetailAdjudication::where('cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->where('critere_adjudications_id',$critere_adjudications_id)
        ->update([
            'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
            'critere_adjudications_id'=>$critere_adjudications_id,
            'valeur'=>$valeur,
        ]); 

        return $detail_adjudication; 
    }

    function deleteDetailCotation($cotation_fournisseur_id,$ref_articles){

        DetailCotation::where('cotation_fournisseurs_id',$cotation_fournisseur_id)->where('ref_articles',$ref_articles)->delete();

    }

    function getSelectionAdjudications(){
            
        $selection_adjudications = DB::table('cotation_fournisseurs')
            ->join('organisations','organisations.id','=','cotation_fournisseurs.organisations_id')
            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
            ->select('selection_adjudications.id as selection_adjudications_id','organisations.*','cotation_fournisseurs.*')
            ->get();

        return $selection_adjudications;

    }

    function getCotationFournisseur2($demande_achats_id){
            
        $cotation_fournisseurs = DB::table('cotation_fournisseurs as cf')
            ->join('organisations as o','o.id','=','cf.organisations_id')
            ->join('preselection_soumissionnaires as ps','ps.organisations_id','=','o.id')
            ->join('critere_adjudications as ca','ca.id','=','ps.critere_adjudications_id')
            ->join('criteres as c','c.id','=','ca.criteres_id')
            ->join('devises as d','d.id','=','cf.devises_id')
            ->select('d.*','o.*','cf.*')
            ->where('cf.demande_achats_id',$demande_achats_id)
            ->where('c.libelle','Fournisseurs Cibles')
            ->distinct('o.id')
            ->get();

        return $cotation_fournisseurs;
    }
    function getAgentFonctions(){
            
        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
        ->join('fonctions as f','f.id','=','pf.fonctions_id')
        ->where('u.flag_actif',1)
        ->where('pf.flag_actif',1)
        ->select(DB::raw('a.mle'),DB::raw('a.nom_prenoms'),DB::raw('f.libelle'))
        ->groupBy('f.libelle','a.mle','a.nom_prenoms')
        ->get();

        return $agents;
    }

    function getAgentFonctionsSignataireDemandeAchat($demande_achats_id){
            
        $signataires = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
        ->join('signataire_demande_achats as sda','sda.profil_fonctions_id','=','pf.id')
        ->join('fonctions as f','f.id','=','pf.fonctions_id')
        ->where('sda.flag_actif',1)
        ->where('u.flag_actif',1)
        ->where('pf.flag_actif',1)
        ->where('sda.demande_achats_id',$demande_achats_id)
        ->select(DB::raw('a.mle'),DB::raw('a.nom_prenoms'),DB::raw('f.libelle'),DB::raw('sda.profil_fonctions_id'))
        ->groupBy('f.libelle','a.mle','a.nom_prenoms','sda.profil_fonctions_id')
        ->get();

        return $signataires;
    }

    function getSelectionAdjudicationGroupe($cotation_fournisseurs_id,$type_profils_name_frs){

        $retrait_bc = DB::table('selection_adjudications as sa')
        ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
        ->join('organisations as o','o.id','=','cf.organisations_id')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->where('u.id',auth()->user()->id)
        ->where('cf.id',$cotation_fournisseurs_id)
        ->whereIn('tp.name',$type_profils_name_frs)
        ->select('tp.name','sa.*','cf.*','o.*')
        ->first();

        return $retrait_bc;

    }

    function budgetDisponible($demande_achats_id){

        $commande = DB::table('commandes')
                        ->where('demande_achats_id',$demande_achats_id)
                        ->whereNotNull('solde_avant_op')
                        ->whereNotNull('solde_apres_op')
                        ->first();

        if($commande != null){
            return $commande;
        }else{

            $commande = DB::table('commandes as c')
                        ->join('demande_achats as da','da.id','=','c.demande_achats_id')
                        ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
                        ->where('c.demande_achats_id',$demande_achats_id)
                        ->select('cb.credit as solde_avant_op')
                        ->first();
            return $commande;
        }
        

    }

    function getWorkflowDemandeAchat($submit,$libelle){

        if ($submit === 'transfert_r_cmp') {

            if ($libelle!='Fournisseur sélectionné' && $libelle!='Annulé (Responsable des achats)' && $libelle!='Rejeté (Responsable DMP)') {

                return redirect()->back()->with('error','Impossible de transférer le dossier au Responsable DMP');   

            }

        }elseif ($submit === 'annuler_r_achat') {

            if ($libelle!='Fournisseur sélectionné' && $libelle!='Validé' && $libelle!='Rejeté (Responsable DMP)') {

                return redirect()->back()->with('error','Impossible d\'annuler le dossier au Responsable DMP');   

            }

        }elseif ($submit === 'valider_cmp') {

            if ($libelle!='Transmis (Responsable DMP)' && $libelle!='Rejeté (Responsable Contrôle Budgétaire)' && $libelle!='Rejeté (Responsable DFC)') {
                
                return redirect()->back()->with('error','Impossible de valider cette demande');   

            }

        }elseif ($submit === 'invalider_cmp') {

            if ($libelle!='Transmis (Responsable DMP)' && $libelle!='Rejeté (Responsable Contrôle Budgétaire)') {

                return redirect()->back()->with('error','Impossible d\'invalider cette demande');   
                
            }
        }elseif ($submit === 'transfert_dcg') {
            if ($libelle!='Visé (Responsable DMP)' && $libelle!='Rejeté (Responsable Contrôle Budgétaire)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier au Responsable DCG');   
            }
        }elseif ($submit === 'valider_dcg') {
            if ($libelle!='Transmis (Responsable Contrôle Budgétaire)' && $libelle!='Rejeté (Chef Département DCG)') {
                return redirect()->back()->with('error','Impossible de valider le dossier');                         
            }
        }elseif ($submit === 'invalider_dcg') {
            if ($libelle!='Transmis (Responsable Contrôle Budgétaire)' && $libelle!='Rejeté (Chef Département DCG)') {
                return redirect()->back()->with('error','Impossible d\'invalider le dossier');
            }
        }elseif ($submit === 'transfert_d_dcg') {
            if ($libelle!='Visé (Responsable Contrôle Budgétaire)' && $libelle!='Rejeté (Chef Département DCG)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier au Chef de Département DCG');   
            }
        }elseif ($submit === 'valider_d_dcg') {
            if ($libelle!='Transmis (Chef Département DCG)' && $libelle!='Rejeté (Responsable DCG)') {
                return redirect()->back()->with('error','Impossible de valider le dossier');                         
            }
        }elseif ($submit === 'invalider_d_dcg') {
            if ($libelle!='Transmis (Chef Département DCG)' && $libelle!='Rejeté (Responsable DCG)') {
                return redirect()->back()->with('error','Impossible d\'invalider le dossier');
            }
        }elseif ($submit === 'transfert_r_dcg') {
            if ($libelle!='Visé (Chef Département DCG)' && $libelle!='Rejeté (Responsable DCG)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier au Directeur de la DCG');   
            }
        }elseif ($submit === 'valider_r_dcg') {
            if ($libelle!='Transmis (Responsable DCG)' && $libelle!='Rejeté (Directeur Général Adjoint)') {
                return redirect()->back()->with('error','Impossible de valider le dossier');                         
            }
        }elseif ($submit === 'invalider_r_dcg') {
            if ($libelle!='Transmis (Responsable DCG)' && $libelle!='Rejeté (Directeur Général Adjoint)') {
                return redirect()->back()->with('error','Impossible d\'invalider le dossier');
            }
        }elseif ($submit === 'transfert_r_dgaaf') {
            if ($libelle!='Visé (Responsable DCG)'  && $libelle!='Rejeté (Directeur Général Adjoint)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier au DGAAF');   
            }
        }elseif ($submit === 'valider_r_dgaaf') {
            if ($libelle!='Transmis (Directeur Général Adjoint)' && $libelle!='Rejeté (Directeur Général)') {
                return redirect()->back()->with('error','Impossible de valider le dossier');                         
            }
        }elseif ($submit === 'invalider_r_dgaaf') {
            if ($libelle!='Transmis (Directeur Général Adjoint)' && $libelle!='Rejeté (Directeur Général)') {
                return redirect()->back()->with('error','Impossible d\'invalider le dossier');
            }
        }elseif ($submit === 'transfert_r_dg') {
            if ($libelle!='Visé (Directeur Général Adjoint)' && $libelle!='Rejeté (Directeur Général)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier au DG');   
            }
        }elseif ($submit === 'valider_r_dg') {
            if ($libelle!='Transmis (Directeur Général)') {
                return redirect()->back()->with('error','Impossible de valider le dossier');                         
            }
        }elseif ($submit === 'invalider_r_dg') {
            if ($libelle!='Transmis (Directeur Général)') {
                return redirect()->back()->with('error','Impossible d\'invalider le dossier');
            }
        }elseif ($submit === 'transfert_r_dfc') {
            if ($libelle!='Visé (Responsable DCG)' && $libelle!='Visé (Directeur Général)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier à la DFC');   
            }
        }elseif ($submit === 'valider_r_dfc') {
            if ($libelle!='Transmis (Responsable DFC)') {
                return redirect()->back()->with('error','Impossible de valider le dossier');                         
            }
        }elseif ($submit === 'invalider_r_dfc') {
            if ($libelle!='Transmis (Responsable DFC)') {
                return redirect()->back()->with('error','Impossible d\'invalider le dossier');
            }
        }elseif ($submit === 'transfert_r_achat') {
            if ($libelle!='Visé (Responsable DFC)') {
                return redirect()->back()->with('error','Impossible de transférer le dossier au Responsable des achats');   
            }
        }elseif ($submit === 'editer') {
            if ($libelle!='Validé' && $libelle!='Annulé (Fournisseur)' && $libelle!='Édité') {
                return redirect()->back()->with('error','Edition de bon de commande impossible');   
            }
        }elseif ($submit === 'annuler_g_achat') {

            if ($libelle!='Validé') {

                return redirect()->back()->with('error','Impossible d\'annuler le dossier par le Gestionnaire des achats');   

            }

        }elseif ($submit === 'retirer') {
            if ($libelle!='Édité') {
                return redirect()->back()->with('error','Retrait de bon de commande impossible');   
            }
        }elseif ($submit === 'annuler_fournisseur') {
            if ($libelle!='Édité') {
                return redirect()->back()->with('error','Impossible d\'annuler le bon de commande');   
            }
        }
    }

    function getWorkflowDemandeAchat2($submit){

        if ($submit === 'transfert_r_cmp') {

            return redirect()->back()->with('error', 'Impossible de transférer le dossier au Responsable DMP');

        }elseif ($submit === 'annuler_r_achat') {

            return redirect()->back()->with('error','Impossible d\'annuler le dossier au Responsable DMP');

        }elseif ($submit === 'valider_cmp') {

            return redirect()->back()->with('error', 'Impossible de valider cette demande');

        }elseif ($submit === 'invalider_cmp') {

            return redirect()->back()->with('error', 'Impossible d\'invalider cette demande');

        }elseif ($submit === 'transfert_dcg') {

            return redirect()->back()->with('error', 'Impossible de transférer le dossier au Responsable DCG');

        }elseif ($submit === 'valider_dcg') {

            return redirect()->back()->with('error', 'Impossible de valider le dossier');

        }elseif ($submit === 'invalider_dcg') {
            return redirect()->back()->with('error', 'Impossible d\'invalider le dossier');
        }elseif ($submit === 'transfert_d_dcg') {
            return redirect()->back()->with('error', 'Impossible de transférer le dossier au Chef de Département DCG');
        }elseif ($submit === 'valider_d_dcg') {
            return redirect()->back()->with('error', 'Impossible de valider le dossier');
        }elseif ($submit === 'invalider_d_dcg') {
            return redirect()->back()->with('error', 'Impossible d\'invalider le dossier');
        }elseif ($submit === 'transfert_r_dcg') {
            return redirect()->back()->with('error','Impossible de transférer le dossier au Directeur de la DCG');  
        }elseif ($submit === 'valider_r_dcg') {
            return redirect()->back()->with('error','Impossible de valider le dossier');
        }elseif ($submit === 'invalider_r_dcg') {
            return redirect()->back()->with('error','Impossible d\'invalider le dossier');
        }elseif ($submit === 'transfert_r_dgaaf') {
            return redirect()->back()->with('error','Impossible de transférer le dossier au DGAAF');
        }elseif ($submit === 'valider_dgaaf') {
            return redirect()->back()->with('error','Impossible de valider le dossier');
        }elseif ($submit === 'invalider_dgaaf') {
            return redirect()->back()->with('error','Impossible d\'invalider le dossier');
        }elseif ($submit === 'transfert_r_dg') {
            return redirect()->back()->with('error','Impossible de transférer le dossier au DG');
        }elseif ($submit === 'valider_dg') {
            return redirect()->back()->with('error','Impossible de valider le dossier');
        }elseif ($submit === 'invalider_dg') {
            return redirect()->back()->with('error','Impossible d\'invalider le dossier');
        }elseif ($submit === 'transfert_r_dfc') {
            return redirect()->back()->with('error','Impossible de transférer le dossier à la DFC');
        }elseif ($submit === 'valider_dfc') {
            return redirect()->back()->with('error','Impossible de valider le dossier');
        }elseif ($submit === 'invalider_dfc') {
            return redirect()->back()->with('error','Impossible d\'invalider le dossier');
        }elseif ($submit === 'transfert_r_achat') {
            return redirect()->back()->with('error','Impossible de transférer le dossier au Responsable des achats');
        }elseif ($submit === 'editer') {
            return redirect()->back()->with('error', 'Edition de bon de commande impossible');
        }elseif ($submit === 'annuler_g_achat') {

            return redirect()->back()->with('error','Impossible d\'annuler le dossier par le Gestionnaire des achats');

        }elseif ($submit === 'retirer') {
            return redirect()->back()->with('error', 'Retrait de bon de commande impossible');
        }elseif ($submit === 'annuler_fournisseur') {
            return redirect()->back()->with('error','Impossible d\'annuler le bon de commande');
        }
    }

    public function storeSignataireDemandeAchat($request,$demande_achats_id,$profils_id){
        
        $donnees = [];       
        
        if (isset($request->profil_fonctions_id)) {

            if (count($request->profil_fonctions_id) > 0) {

                foreach ($request->profil_fonctions_id as $item => $value) {

                    $donnees[$item] =  $request->profil_fonctions_id[$item];

                }

            }

        }

        $signataire_demande_achat = DB::table('signataire_demande_achats as sda')
        ->where('sda.flag_actif',1)
        ->where('sda.demande_achats_id',$demande_achats_id)
        ->first();
        if ($signataire_demande_achat!=null) {

            $signataire_demande_achat_listes = DB::table('signataire_demande_achats as sda')
            ->where('sda.flag_actif',1)
            ->where('sda.demande_achats_id',$demande_achats_id)
            ->whereNotIn('sda.profil_fonctions_id',$donnees)
            ->get();
            foreach ($signataire_demande_achat_listes as $signataire_demande_achat_liste) {

                $libelle = 'Désactivé';
                $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);

                SignataireDemandeAchat::where('id',$signataire_demande_achat_liste->id)
                ->update([
                    'profil_fonctions_id'=>$signataire_demande_achat_liste->profil_fonctions_id,
                    'demande_achats_id'=>$demande_achats_id,
                    'flag_actif'=>0,
                ]);

                StatutSignataireDemandeAchat::create([
                    'signataire_achats_id'=>$signataire_demande_achat_liste->id,
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

                        $signataire = SignataireDemandeAchat::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('demande_achats_id',$demande_achats_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {

                            $signataire_achats_id = SignataireDemandeAchat::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'demande_achats_id'=>$demande_achats_id,
                            ])->id;

                            StatutSignataireDemandeAchat::create([
                                'signataire_achats_id'=>$signataire_achats_id,
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

                        $signataire = SignataireDemandeAchat::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('demande_achats_id',$demande_achats_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {

                            $signataire_achats_id = SignataireDemandeAchat::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'demande_achats_id'=>$demande_achats_id,
                            ])->id;

                            StatutSignataireDemandeAchat::create([
                                'signataire_achats_id'=>$signataire_achats_id,
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

    public function storeSignataireDemandeAchat2($profil_fonctions_id,$demande_achats_id,$profils_id){

        $libelle = 'Activé';
        $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);

        if (isset($profil_fonctions_id)) {

            $signataire = SignataireDemandeAchat::where('profil_fonctions_id',$profil_fonctions_id)
            ->where('demande_achats_id',$demande_achats_id)
            ->where('flag_actif',1)
            ->first();
            if ($signataire === null) {

                $signataire_achats_id = SignataireDemandeAchat::create([
                    'profil_fonctions_id'=>$profil_fonctions_id,
                    'demande_achats_id'=>$demande_achats_id,
                ])->id;

                StatutSignataireDemandeAchat::create([
                    'signataire_achats_id'=>$signataire_achats_id,
                    'type_statut_sign_id'=>$type_statut_sign_id,
                    'profils_id'=>$profils_id,
                    'date_debut'=>date('Y-m-d'),
                ]);
            }           

        }
    }
    public function setSignataire($type_operations_libelle,$operations_id,$profil_fonction_array){

        if($type_operations_libelle === "Demande d'achats"){
            SignataireDemandeAchat::whereNotIn('profil_fonctions_id',$profil_fonction_array)
            ->where('demande_achats_id',$operations_id)
            ->where('flag_actif',1)
            ->update([
                'flag_actif'=>0
            ]);
        }elseif($type_operations_libelle === "Demande de fonds"){

            SignataireDemandeFond::whereNotIn('profil_fonctions_id',$profil_fonction_array)
                ->where('demande_fonds_id',$operations_id)
                ->where('flag_actif',1)
                ->update([
                    'flag_actif'=>0
                ]);
        }elseif($type_operations_libelle === "Commande non stockable"){
            SignataireTravaux::whereNotIn('profil_fonctions_id',$profil_fonction_array)
                ->where('travauxes_id',$operations_id)
                ->where('flag_actif',1)
                ->update([
                    'flag_actif'=>0
                ]);
        }
        
    }

    public function getEngagement($demande_achats_id){
        
        $possibilite_engagement = null;
        $commande = Commande::where('demande_achats_id',$demande_achats_id)
        ->whereNotNull('solde_avant_op')
        ->first();
        if ($commande!=null) {
            $possibilite_engagement = 'non';
        }else{
            $possibilite_engagement = 'oui';
        }

        return $possibilite_engagement;
    }

    function getWorkflowDemandeAchatError($submit){

        $libelle = null;

        if ($submit === 'transfert_r_cmp') {
            $libelle = "Echec du transfert du dossier au Responsable DMP";
        }elseif ($submit === 'annuler_r_achat') {
            $libelle = "Echec de l\'annulation du dossier";
        }elseif ($submit === 'transfert_dcg') {
            $libelle = "Echec du transfert du dossier au Responsable DCG";
        }elseif ($submit === 'valider_cmp') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'valider_dcg') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'invalider_dcg') {
            $libelle = "Echec de l\'invalidation du dossier";
        }elseif ($submit === 'transfert_d_dcg') {
            $libelle = "Echec du transfert du dossier au Chef Département DCG";
        }elseif ($submit === 'valider_d_dcg') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'invalider_d_dcg') {
            $libelle = "Echec de l\'invalidation du dossier";
        }elseif ($submit === 'transfert_r_dcg') {
            $libelle = "Echec du transfert du dossier au Responsable DCG";
        }elseif ($submit === 'valider_r_dcg') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'invalider_r_dcg') {
            $libelle = "Echec de l\'invalidation du dossier";
        }elseif ($submit === 'transfert_r_dgaaf') {
            $libelle = "Echec du transfert du dossier au Directeur Général Adjoint";
        }elseif ($submit === 'valider_r_dgaaf') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'invalider_r_dgaaf') {
            $libelle = "Echec de l\'invalidation du dossier";
        }elseif ($submit === 'transfert_r_dfc') {
            $libelle = "Echec du transfert du dossier au Responsable DFC";
        }elseif ($submit === 'valider_r_dfc') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'invalider_r_dfc') {
            $libelle = "Echec de l\'invalidation du dossier";
        }elseif ($submit === 'transfert_r_dg') {
            $libelle = "Echec du transfert du dossier au Directeur Général";
        }elseif ($submit === 'valider_r_dg') {
            $libelle = "Echec de la validation du dossier";
        }elseif ($submit === 'invalider_r_dg') {
            $libelle = "Echec de l\'invalidation du dossier";
        }elseif ($submit === 'invalider_cmp') {
            $libelle = "Echec de l'invalidation du dossier";
        }elseif ($submit === 'editer') {
            $libelle = "Echec de l'édition du bon de commande";
        }elseif ($submit === 'retirer') {
            $libelle = "Echec du retrait du bon de commande";
        }elseif ($submit === 'annuler_fournisseur') {
            $libelle = "Echec de l\'annulation du dossier";
        }

        return $libelle;
    }

    function getTypeStatutSignataire($libelle){

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

    function getLivraisonCommandeByCotationFournisseur($cotation_fournisseurs_id){
        $livraison_commandes = LivraisonCommande::where('cotation_fournisseurs_id',$cotation_fournisseurs_id)->first();

        return $livraison_commandes;
    }

    function setLivraisonCommande($livraison_commandes_id,$cotation_fournisseurs_id,$profils_id,$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$taux_de_change,$validation=null){
        
        LivraisonCommande::where('id',$livraison_commandes_id)
        ->update([
            'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
            'profils_id'=>$profils_id,
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$tva,
            'montant_total_ttc'=>$montant_total_ttc,
            'assiete_bnc'=>$assiete_bnc,
            'taux_bnc'=>$taux_bnc,
            'net_a_payer'=>$net_a_payer,
            'taux_de_change'=>$taux_de_change,
            'validation'=>$validation
        ]);

    }

    public function setLivraisonCommandeValidation($livraison_commandes_id,$validation){
        LivraisonCommande::where('id',$livraison_commandes_id)
        ->update([
            'validation'=>$validation
        ]);
    }

    function storeLivraisonCommande($cotation_fournisseurs_id,$profils_id,$num_bl,$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$taux_de_change){
        
        
        $livraison_commande = LivraisonCommande::create([
            'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
            'profils_id'=>$profils_id,
            'num_bl'=>$num_bl,
            'montant_total_brut'=>$montant_total_brut,
            'taux_remise_generale'=>$taux_remise_generale,
            'remise_generale'=>$remise_generale,
            'montant_total_net'=>$montant_total_net,
            'tva'=>$tva,
            'montant_total_ttc'=>$montant_total_ttc,
            'assiete_bnc'=>$assiete_bnc,
            'taux_bnc'=>$taux_bnc,
            'net_a_payer'=>$net_a_payer,
            'taux_de_change'=>$taux_de_change,
        ]);

        return $livraison_commande;

    }

    function getLastDetailLivraison($livraison_commandes_id){
                            
        $detail_livrai = DetailLivraison::where('livraison_commandes_id',$livraison_commandes_id)
        ->orderByDesc('livraison_commandes_id')
        ->limit(1)
        ->first();

        return $detail_livrai;
    }

    public function storeDetailLivraison($detail_cotations_id,$qte,$taux_tva,$remise,$livraison_commandes_id,$detail_livraison,$submit,$sequence,$detail_livraisons_id=null){
        

        
        $detail_cotation = DetailCotation::where('id',$detail_cotations_id)->first();

        if ($detail_cotation!=null) {
            $prix_unit = $detail_cotation->prix_unit;
            $remise = $detail_cotation->remise;
        }else{
            $prix_unit = 0;
            $remise = 0;
        }


        $montant_ht = ( ( $prix_unit * $qte ) - ((( $prix_unit * $qte ) * $remise )/100) );

        $montant_ttc = $montant_ht*$taux_tva;
        
        $detail_cotation = DetailCotation::where('id',$detail_cotations_id)->first();

        if ($detail_cotation!=null) {
            $qte_cotation = $detail_cotation->qte;                
                
                if (isset($submit)) {
                    if ($submit === "livrer") {

                        $qte_en_cours_de_livraison = 0;

                        $qte_livraison_commande = DB::table('livraison_commandes as lc')
                        ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')
                        ->where('dl.detail_cotations_id',$detail_cotation->id)
                        ->where('lc.cotation_fournisseurs_id',$detail_cotation->cotation_fournisseurs_id)
                        ->select(DB::raw('SUM(dl.qte_frs) as qte_en_cours_de_livraison'))
                        ->first();

                        if($qte_livraison_commande != null){
                            $qte_en_cours_de_livraison = $qte_livraison_commande->qte_en_cours_de_livraison;
                        }
                        //if ($qte_cotation >= $qte) {
                        if ( $qte_cotation >= $qte_en_cours_de_livraison ) {

                            $data = [
                                'livraison_commandes_id'=>$livraison_commandes_id,
                                'detail_cotations_id'=>$detail_cotations_id,
                                'qte'=>$qte,
                                'qte_frs'=>$qte,
                                'prix_unit'=>$prix_unit,
                                'remise'=>$remise,
                                'montant_ht'=>$montant_ht,
                                'montant_ttc'=>$montant_ttc,
                                'sequence'=>$sequence
                            ];

                            if(isset($detail_livraisons_id)){
                                DetailLivraison::where('id',$detail_livraisons_id)
                                ->update($data);

                                $detail_livraison = DetailLivraison::where('id',$detail_livraisons_id)->first();
                            }else{
                                $detail_livraison = DetailLivraison::create($data);
                            }
                            if($qte === 0){
                                DetailLivraison::where('id',$detail_livraisons_id)->delete();
                            }
                            
                        }
                    }elseif($submit === "annuler_livraison_achat" or $submit === "valider"){

                        if ($qte_cotation >= $qte) {
                            $search = DetailLivraison::where('livraison_commandes_id', $livraison_commandes_id)
                            ->where('detail_cotations_id', $detail_cotations_id)
                            ->whereNotIn('detail_livraisons.id', function ($query) {
                                $query->select(DB::raw('lv.detail_livraisons_id'))
                                    ->from('livraison_validers as lv')
                                    ->whereRaw('lv.detail_livraisons_id = detail_livraisons.id');
                            })
                            ->whereNotIn('qte',[0])
                            ->first();
                            if ($search!=null) {
                                $data = [
                                    'livraison_commandes_id'=>$livraison_commandes_id,
                                    'detail_cotations_id'=>$detail_cotations_id,
                                    'qte'=>$qte,
                                    'prix_unit'=>$prix_unit,
                                    'remise'=>$remise,
                                    'montant_ht'=>$montant_ht,
                                    'montant_ttc'=>$montant_ttc,
                                ];

                                DetailLivraison::where('id', $search->id)->update($data);

                                $detail_livraison = DetailLivraison::where('id', $search->id)->first();
                            }
                        }

                        
                    }
                }
                
            
        }

            return $detail_livraison;
        
        
    }

    function getSumDetailCotationByCotationFournisseur($cotation_fournisseurs_id){
                        
        $detail_cotationss = DB::table('detail_cotations')
        ->select(DB::raw('sum(qte) as qte_total_commandee' ))
        ->where('cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->first();

        return $detail_cotationss;
    }

    function getSumDetailLivraisonByCotationFournisseur($cotation_fournisseurs_id){
                        
        $detail_livraisonss = DB::table('detail_livraisons as dl')
        ->select(DB::raw('sum(dl.qte) as qte_total_livree' ))
        ->join('detail_cotations as dc','dc.id','=','dl.detail_cotations_id')
        ->where('dc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->first();

        return $detail_livraisonss;
    }

    public function storebonLivraison($livraison_commandes_id,$profils_id,$piece,$flag_actif,$name,$sequence){

        $data = [
            'profils_id'=>$profils_id,
            'livraison_commandes_id'=>$livraison_commandes_id,
            'name'=>$name,
            'piece'=>$piece,
            'flag_actif'=>$flag_actif,
        ];
        $bon_livraison = BonLivraison::where('livraison_commandes_id',$livraison_commandes_id)
        ->first();

        if($bon_livraison != null){
            BonLivraison::where('id',$bon_livraison->id)
            ->update($data);
        }else{
            BonLivraison::create($data);
        }

        
        
    }

    function getDetailLivraisonById($detail_livraisons_id){

        $detail_livraison = DetailLivraison::where('id',$detail_livraisons_id)->first();

        return $detail_livraison;
    }

    public function storeLivraisionValider($detail_cotations_id,$taux_tva,$qte,$detail_livraisons_id,$remise,$profils_id){
        
        $livraison_valider = null;
        $detail_cotation = DetailCotation::where('id', $detail_cotations_id)->first();

        if ($detail_cotation!=null) {
            $prix_unit = $detail_cotation->prix_unit;
            $remise = $detail_cotation->remise;
        } else {
            $remise = 0;
            $prix_unit = 0;
        }




        $montant_ht = $prix_unit * $qte;

        $montant_ht = (($prix_unit * $qte) - ((($prix_unit * $qte) * $remise)/100));


        $montant_ttc = $montant_ht*$taux_tva;


        $detail_livraison = DetailLivraison::where('id', $detail_livraisons_id)->first();


        if ($detail_livraison!=null) {
            $qte_livree = $detail_livraison->qte;

            if ($qte_livree >= $qte) {
                $data = [
                        'livraison_commandes_id'=>$detail_livraison->livraison_commandes_id,
                        'profils_id'=>$profils_id,
                        'detail_livraisons_id'=>$detail_livraisons_id,
                        'qte'=>$qte,
                        'prix_unit'=>$prix_unit,
                        'remise'=>$remise,
                        'montant_ht'=>$montant_ht,
                        'montant_ttc'=>$montant_ttc,
                    ];
        
            
                $livraison_valider = LivraisonValider::create($data);
            }
        }

        return $livraison_valider;
    }

    public function getTypeMouvement($libelle){
        
        $type_mouvement = TypeMouvement::where('libelle', $libelle)->first();

        if ($type_mouvement!=null) {
            $type_mouvements_id = $type_mouvement->id;
        } else {
            $type_mouvements_id = TypeMouvement::create([
                'libelle'=>$libelle
            ])->id;
        }

        return $type_mouvements_id;
    }

    public function getMagasin($demande_achats_id){

        $magasin = DB::table('demande_achats as da')
            ->join('depots as d', 'd.ref_depot', '=', 'da.ref_depot')
            ->join('magasins as m', 'm.depots_id', '=', 'd.id')
            ->where('da.id', $demande_achats_id)
            ->first();
            if ($magasin!=null) {
                $ref_magasin = $magasin->ref_magasin;
            }else{
                $ref_magasin = null;
            }
        return $ref_magasin;

    }

    public function getLivraisonValider($detail_livraisons_id){

        $livraison_valid = LivraisonValider::join('detail_livraisons as dl', 'dl.id', '=', 'livraison_validers.detail_livraisons_id')
        ->join('detail_cotations as dc', 'dc.id', '=', 'dl.detail_cotations_id')
        ->where('detail_livraisons_id', $detail_livraisons_id)
        ->select('dc.*', 'dl.*', 'livraison_validers.*')
        ->first();

        return $livraison_valid;
    }

    public function getMagasinStock($ref_magasin,$ref_articles,$qte=null){

        if($qte === null){
            $qte = 0;
            $cmup = 1;
        }

        $magasin_stock = MagasinStock::where('ref_articles', $ref_articles)
        ->where('ref_magasin', $ref_magasin)
        ->first();

        if ($magasin_stock!=null) {
            $magasin_stocks_id = $magasin_stock->id;
        } else {

            if($qte === 0){
                $magasin_stocks_id = MagasinStock::create([
                    'ref_articles' => $ref_articles,
                    'ref_magasin' => $ref_magasin,
                    'qte' => $qte,
                    'cmup' => $cmup
                ])->id;
            }else{
                $magasin_stocks_id = MagasinStock::create([
                    'ref_articles' => $ref_articles,
                    'ref_magasin' => $ref_magasin,
                    'qte' => $qte
                ])->id;
            }                
        }
           
        return $magasin_stocks_id;
    }

    public function getMagasinStockIntegrationEnMasse($ref_magasin,$ref_articles,$qte=null,$cmup=null){

        if($qte === null){
            $qte = 0;
        }

        if($cmup === null){
            $cmup = 1;
        }

        $montant = $qte * $cmup;

        $magasin_stock = MagasinStock::where('ref_articles', $ref_articles)
            ->where('ref_magasin', $ref_magasin)
            ->first();

            if ($magasin_stock!=null) {
                $magasin_stocks_id = $magasin_stock->id;
            } else {
                

                $magasin_stocks_id = MagasinStock::create([
                    'ref_articles' => $ref_articles,
                    'ref_magasin' => $ref_magasin,
                    'qte' => $qte,
                    'cmup' => $cmup,
                    'montant' => $montant,
                ])->id;
                
                
            }
           
        return $magasin_stocks_id;
    }

    public function storeMouvement($type_mouvements_id,$magasin_stocks_id,$profils_id,$qte,$prix_unit,$montant_ht,$tva,$montant_ttc,$taux_de_change){

        $mouvements_id = null;

        if ($magasin_stocks_id!=null) {

            $prix_unit = $prix_unit * $taux_de_change;
            $prix_unit = (int)$prix_unit;

            $montant_ht = $montant_ht * $taux_de_change;
            $montant_ht = (int)$montant_ht;

            $montant_ttc = $montant_ttc * $taux_de_change;
            $montant_ttc = (int)$montant_ttc;

            $data_mvt = [
                'type_mouvements_id' => $type_mouvements_id,
                'magasin_stocks_id' => $magasin_stocks_id,
                'profils_id' => $profils_id,
                'qte' => $qte,
                'prix_unit' => $prix_unit,
                'montant_ht' => $montant_ht,
                'taxe' => $tva,
                'montant_ttc' => $montant_ttc,
            ];

            $mouvements_id = Mouvement::create($data_mvt)->id;
        }

        return $mouvements_id;
    }

    function setLivraisonValider($livraison_validers_id,$mouvements_id){
        LivraisonValider::where('id',$livraison_validers_id)->update([
            'mouvements_id' => $mouvements_id,
        ]);
    }

    public function getMontantMagasinStock($magasin_stocks_id){
        $montant_apres_stock = 0;
        $mouvements_montant = DB::table('mouvements')
        ->select(DB::raw('SUM(montant_ttc) as montant_stock'))
        ->where('magasin_stocks_id', $magasin_stocks_id)
        ->groupBy('magasin_stocks_id')
        ->first();

        if ($mouvements_montant!=null) {
            $montant_apres_stock = $mouvements_montant->montant_stock;
        }

        return $montant_apres_stock;
    }

    public function getQteMagasinStock($magasin_stocks_id){

        return DB::table('mouvements')
            ->select(DB::raw('SUM(qte) as qte_stock'))
            ->where('magasin_stocks_id', $magasin_stocks_id)
            ->groupBy('magasin_stocks_id')
            ->first();

    }

    public function setMagasinStock($magasin_stocks_id,$cmup,$qte_stock,$montant_stock){
        MagasinStock::where('id', $magasin_stocks_id)->update([
            'cmup'=>$cmup,
            'qte'=>$qte_stock,
            'montant'=>$montant_stock
        ]);
    }

    function setMagasinStock2($magasin_stocks_id,$stock_mini,$stock_securite,$stock_alert){

        MagasinStock::where('id', $magasin_stocks_id)->update([
            'stock_mini'=>$stock_mini,
            'stock_securite'=>$stock_securite,
            'stock_alert'=>$stock_alert
        ]);

    }

    function setMagasinStock3($magasin_stocks_id){

        $mouvement = DB::table('mouvements')
        ->select(
            DB::raw('SUM(montant_ttc) as montant_stock'),
            DB::raw('SUM(qte) as qte_stock')
        )
        ->where('magasin_stocks_id', $magasin_stocks_id)
        ->groupBy('magasin_stocks_id')
        ->first();

        if ($mouvement!=null) {

            $qte_stock = $mouvement->qte_stock;

            $magasin_stock = $this->getMagasinStockId($magasin_stocks_id);

            if($magasin_stock != null){

                $montant_stock = $magasin_stock->cmup * $qte_stock;

                MagasinStock::where('id', $magasin_stocks_id)->update([
                    'qte' => $qte_stock,
                    'montant' => $montant_stock,
                ]);
            }
            

            
        }
    }

    function getMagasinStockId($magasin_stocks_id){

        $magasin_stock = MagasinStock::where('id',$magasin_stocks_id)->first();

        return $magasin_stock;
    }

    function getLivraisonValider2($detail_livraisons_id,$ref_articles,$livraison_commandes_id){
                                                                
        $livraison_valid = LivraisonValider::join('detail_livraisons as dl', 'dl.id', '=', 'livraison_validers.detail_livraisons_id')
        ->join('detail_cotations as dc', 'dc.id', '=', 'dl.detail_cotations_id')
        ->where('detail_livraisons_id', $detail_livraisons_id)
        ->where('dc.ref_articles', $ref_articles)
        ->whereNotIn('livraison_validers.livraison_commandes_id', [$livraison_commandes_id])
        ->orderByDesc('livraison_validers.created_at')
        ->limit(1)
        ->select('livraison_validers.created_at')
        ->first();

        return $livraison_valid;
    }

    function getMouvement($magasin_stocks_id,$date3){

        $mouvement = DB::table('mouvements as m')
        ->join('type_mouvements as tm', 'tm.id', '=', 'm.type_mouvements_id')
        ->select(DB::raw('SUM(qte) as qte_sortie'))
        ->where('m.magasin_stocks_id', $magasin_stocks_id)
        ->where('tm.libelle', 'Sortie du stock')
        ->whereBetween(DB::raw('DATE(m.created_at)'), [$date3, $date3])
        ->groupBy('m.magasin_stocks_id')
        ->first();

        return $mouvement;

    }

    function getLivraisonValiderByCotationFournisseur($cotation_fournisseurs_id){
                        
        $livraison_validerss = DB::table('livraison_validers as lv')
        ->select(DB::raw('sum(lv.qte) as qte_total_livree_confirme' ))
        ->join('detail_livraisons as dl','dl.id','=','lv.detail_livraisons_id')
        ->join('detail_cotations as dc','dc.id','=','dl.detail_cotations_id')
        ->where('dc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->first();

        return $livraison_validerss;
    }

    function getListeComiteReception(){
        $agents = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->join('agent_sections as ase','ase.agents_id','=','a.id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
        ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
        ->where('tsase.libelle','Activé')
        ->select('a.mle','st.nom_structure','a.nom_prenoms')
        ->distinct('a.mle','st.nom_structure','a.nom_prenoms')
        ->get();

        return $agents;
    }

    function getMagasinStocksByDepot($depots_id = null){

        if ($depots_id != null) {
            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('ms.id','a.ref_articles','a.design_article','f.ref_fam','f.design_fam','ms.qte','ms.cmup','ms.updated_at','m.ref_magasin')
                ->orderByDesc('ms.updated_at')
                ->where('d.id',$depots_id)
                ->get();
        }
        
        if ($depots_id === null){

            $magasin_stocks = DB::table('magasin_stocks as ms')
                ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('depots as d','d.id','=','m.depots_id')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->select('ms.id','a.ref_articles','a.design_article','f.ref_fam','f.design_fam','ms.qte','ms.cmup','ms.updated_at','m.ref_magasin')
                ->orderByDesc('ms.updated_at')
                ->get();

        }

        return $magasin_stocks;
    }

    function getMagasinStockById($magasin_stocks_id){

        return DB::table('magasin_stocks as ms')
            ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
            ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            ->join('depots as d','d.id','=','m.depots_id')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->select('a.ref_articles','a.design_article','f.ref_fam','f.design_fam','d.ref_depot','d.design_dep','m.ref_magasin')
            ->where('ms.id',$magasin_stocks_id)
            ->first();
    }

    function getgetMagasinStocksById($magasin_stocks_id){
            
        $magasin_stocks = DB::table('magasin_stocks as ms')
        ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->join('depots as d','d.id','=','m.depots_id')
        ->join('familles as f','f.ref_fam','=','a.ref_fam')
        ->join('mouvements as mv','mv.magasin_stocks_id','=','ms.id')
        ->join('type_mouvements as tm','tm.id','=','mv.type_mouvements_id')
        ->select('mv.profils_id','mv.id','tm.libelle','mv.qte','mv.prix_unit','mv.montant_ht','mv.taxe','mv.montant_ttc','mv.created_at','mv.date_mouvement','ms.id as magasin_stocks_id')
        ->orderByDesc('mv.date_mouvement')
        ->where('ms.id',$magasin_stocks_id)
        ->get();

        return $magasin_stocks;
    }








































    function RequisitionData($libelle,$type_profils_name,$code_structure=null,$libelle2=null){

        $requisitions = [];

        if (isset($type_profils_name)) {
            if ($type_profils_name === "Agent Cnps") {

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->where('p.users_id',auth()->user()->id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();               

            }elseif($type_profils_name === "Responsable N+1"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n1',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

            }elseif($type_profils_name === "Responsable N+2"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n2',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }elseif($type_profils_name === "Pilote AEE"){

                    
                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->orWhereIn('requisitions.id', function($query) use($libelle2){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->where('tsr.libelle',$libelle2)
                        ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                        ->from('demandes as d')
                        ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
                

            }elseif($type_profils_name === "Responsable des stocks"){

                
                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

               // dd($requisitions);
            }elseif($type_profils_name === "Gestionnaire des stocks"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }elseif($type_profils_name === "Administrateur fonctionnel"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->whereIn('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

            }
        }

        return $requisitions;

    }

    function RequisitionDataCentDernier($libelle,$type_profils_name,$code_structure=null,$libelle2=null){

        $requisitions = [];

        if (isset($type_profils_name)) {
            if ($type_profils_name === "Agent Cnps") {

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->where('p.users_id',auth()->user()->id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();               

            }elseif($type_profils_name === "Responsable N+1"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n1',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

            }elseif($type_profils_name === "Responsable N+2"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('requisitions as r')
                            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n2',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('requisitions.id = r.id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }elseif($type_profils_name === "Pilote AEE"){

                    
                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                ->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->orWhereIn('requisitions.id', function($query) use($libelle2){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->where('tsr.libelle',$libelle2)
                        ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                        ->from('demandes as d')
                        ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
                

            }elseif($type_profils_name === "Responsable des stocks"){

                
                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

               // dd($requisitions);
            }elseif($type_profils_name === "Gestionnaire des stocks"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();
            }elseif($type_profils_name === "Administrateur fonctionnel"){

                $requisitions = Requisition::select('requisitions.id as id','requisitions.num_bc as num_bc','requisitions.updated_at as updated_at','requisitions.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','requisitions.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('requisitions.updated_at')
                ->limit(100)
                //->where('requisitions.code_structure',$code_structure)
                ->whereIn('requisitions.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.requisitions_id'))
                          ->from('statut_requisitions as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->whereIn('tsr.libelle',$libelle)
                          ->whereRaw('requisitions.id = sr.requisitions_id');
                })
                ->whereIn('requisitions.id', function($query){
                    $query->select(DB::raw('d.requisitions_id'))
                          ->from('demandes as d')
                          ->whereRaw('requisitions.id = d.requisitions_id');
                })
                ->get();

            }
        }

        return $requisitions;

    }

    function controleSaisieRequisition($etape,$request){
        $error = null;

        if (isset($etape)) {
            if ($etape === "store" or $etape === "update") {

                
                if (isset($request->ref_articles)) {
                    if (count($request->ref_articles) > 0) {
                        foreach ($request->ref_articles as $item => $value) {
        
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
        
                            if (gettype($qte[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
                            


                            $montant[$item] = filter_var($request->montant[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                            $info = 'Montant';
                            $error = $this->setDecimal($montant[$item],$info);

                            if (isset($error)) {
                                return redirect()->back()->with('error',$error);
                            }else{
                                $montant[$item] = $montant[$item] * 1;
                            }
        
        
                            $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $cmup[$item] = $cmup[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
        
                            if (gettype($cmup[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
                            
                            if ($request->ref_articles[$item]!=null && $request->design_article[$item]!=null && $qte[$item]!=null) {
        
                                if ($qte[$item] <= 0) {
                                    $error = "La quantité demande ne peut être nulle ou négative";
                                }
        
                                /*if ($montant[$item] <= 0) {
                                    $error = "Le montant demande ne peut être nulle ou négative";
                                }*/
                                
                            }else{
                                $error = "Veuillez remplir tous les champs";
                            }
        
                        }
                    }
                }

                return $error;
                
            }
        }
        
    }

    function storeRequisition($code_structure,$num_bc,$exercice,$intitule,$code_gestion,$profils_id,$type_beneficiaire=null){
            
        $requisition = Requisition::create([
            'code_structure' => $code_structure,
            'num_bc' => $num_bc,
            'exercice' => $exercice,
            'intitule' => $intitule,
            'code_gestion' => $code_gestion,
            'profils_id' => $profils_id,
            'type_beneficiaire' => $type_beneficiaire,
        ]);

        return $requisition;
    }

    function storeDetailDemande($etape,$request,$requisitions_id,$profils_id){
        $error = null;

        if (isset($etape)) {
            if ($etape === "store" or $etape === "update") {
                if (count($request->ref_articles) > 0) {

                    if ($etape === "update") {
                        Demande::where('requisitions_id',$requisitions_id)->delete();
                    }

                    foreach ($request->ref_articles as $item => $value) {
        
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {

                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
        
                            if (gettype($qte[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
                            
        

                            $montant[$item] = filter_var($request->montant[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                            $info = 'Montant';
                            $error = $this->setDecimal($montant[$item],$info);

                            if (isset($error)) {
                                return redirect()->back()->with('error',$error);
                            }else{
                                $montant[$item] = $montant[$item] * 1;
                            }
                            
        
        
                            $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $cmup[$item] = $cmup[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
        
                            if (gettype($cmup[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
                        
                        if (!isset($error)) {

                            $demande = $this->storeDemande($requisitions_id,$request->magasin_stocks_id[$item],$qte[$item],$cmup[$item],$montant[$item],$profils_id);
                            
                            
                            
                            
                        }
                        
                        
                                       
        
                    }
                    
                }
            }
        }

        return $error;
        
    }

    function storeDemande($requisitions_id,$magasin_stocks_id,$qte,$cmup,$montant,$profils_id){

        $demande = Demande::create([
            'requisitions_id' => $requisitions_id,
            'magasin_stocks_id' => $magasin_stocks_id,
            'qte' => $qte, 
            'prixu' => $cmup, 
            'montant' => $montant, 
            'profils_id' => $profils_id, 
        ]);

        return $demande;

    }


















    













    //Immobilisation

    public function getImmobilisations($libelle,$type_profils_name,$code_structure=null,$libelle2=null){

        $immobilisations = [];
        if (isset($type_profils_name)) {
            if ($type_profils_name === "Agent Cnps") {

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('immobilisations as r')
                            ->join('statut_immobilisations as sr','sr.immobilisations_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            // ->join('profils as p','p.id','=','r.profils_id')
                            // ->where('p.users_id',auth()->user()->id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('immobilisations.id = r.id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();               

            }elseif($type_profils_name === "Responsable N+1"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('immobilisations as r')
                            ->join('statut_immobilisations as sr','sr.immobilisations_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('detail_immobilisations as d','d.immobilisations_id','=','sr.immobilisations_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n1',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('immobilisations.id = r.id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();

            }elseif($type_profils_name === "Responsable N+2"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('immobilisations as r')
                            ->join('statut_immobilisations as sr','sr.immobilisations_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('detail_immobilisations as d','d.immobilisations_id','=','sr.immobilisations_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n2',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('immobilisations.id = r.id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();
            }elseif($type_profils_name === "Pilote AEE"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.updated_at')
                ->where('immobilisations.code_structure',$code_structure)
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->orWhereIn('immobilisations.id', function($query) use($libelle2){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle2)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();
            }elseif($type_profils_name === "Administrateur fonctionnel" or $type_profils_name === "Responsable des stocks" or $type_profils_name === "Gestionnaire des stocks" or $type_profils_name === "Responsable section entretien" or $type_profils_name === "Responsable Logistique" or $type_profils_name === "Gestionnaire entretien" or $type_profils_name === "Responsable DMP"){

                
                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.updated_at')
                //->where('immobilisations.code_structure',$code_structure)
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();

               // dd($immobilisations);
            }
        }

        return $immobilisations;

    }

    function getArticleMagasinStock(){

        $articles = DB::table('articles as a')
            ->join('magasin_stocks as ms','ms.ref_articles','=','a.ref_articles')
            ->select('a.ref_articles','a.design_article', 'ms.cmup', 'ms.id', 'ms.qte')
            //->whereRaw('ms.qte > 0')
            ->where('a.flag_actif',1)
            ->orderBy('a.design_article')
            ->get();

        return $articles;
    }

    function storeImmobilisation($code_structure,$num_bc,$exercice,$intitule,$code_gestion,$profils_id){

        $immobilisation = Immobilisation::create([
            'code_structure' => $code_structure,
            'num_bc' => $num_bc,
            'exercice' => $exercice,
            'intitule' => $intitule,
            'code_gestion' => $code_gestion,
            'profils_id' => $profils_id,
        ]);

        return $immobilisation;
    }

    
    function setImmobilisation($immobilisations_id,$intitule,$code_gestion,$profils_id){
            
        $immobilisation = Immobilisation::where('id',$immobilisations_id)
        ->update([
            'intitule' => $intitule,
            'code_gestion' => $code_gestion,
            'profils_id' => $profils_id,
        ]);

        return $immobilisation;
    }

    function setImmobilisation2($immobilisations_id){
            
        $immobilisation = Immobilisation::where('id',$immobilisations_id)
        ->update([
            'id' => $immobilisations_id,
        ]);

        return $immobilisation;
    }

    function setFlagValideImmobilisation($immobilisations_id,$flag_valide,$type_flag){

        $immobilisation = null;

        if ($type_flag === 'flag_valide') {
            $data = [
                'flag_valide' => $flag_valide,
            ];
        }elseif ($type_flag === 'flag_valide_stock') {
            $data = [
                'flag_valide_stock' => $flag_valide,
            ];
        }elseif ($type_flag === 'flag_valide_r_cmp') {
            $data = [
                'flag_valide_r_cmp' => $flag_valide,
            ];
        }elseif ($type_flag === 'flag_valide_r_l') {
            $data = [
                'flag_valide_r_l' => $flag_valide,
            ];
        }
        
        if (isset($data)) {
            $immobilisation = Immobilisation::where('id',$immobilisations_id)
            ->update($data);
        }
       

        return $immobilisation;
    }


    function storeDetailImmobilisation($etape,$request,$immobilisations_id){
        $error = null;

        if (isset($etape)) {

            if ($etape === "store" or $etape === "immobilisation_update") {

                if (count($request->ref_articles) > 0) {

                    if ($etape === "immobilisation_update") {

                        if (isset($request->detail_immobilisations_id)) {

                            $array_detail_immobilisations_ids = [];
                
                            foreach ($request->detail_immobilisations_id as $key => $value) {
                                $array_detail_immobilisations_ids[] =  $value;
                            }
                
                            $this->deleteDetailImmobilisationNoAll($request->id,$array_detail_immobilisations_ids);
                
                        }

                    }

                    foreach ($request->ref_articles as $item => $value) {
        
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {

                                $qte[$item] = $qte[$item] * 1;

                            } catch (\Throwable $th) {

                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
                            }
        
                            if (gettype($qte[$item])!='integer') {

                                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";

                            }        

                            $montant[$item] = filter_var($request->montant[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                            $info = 'Montant';
                            $error = $this->setDecimal($montant[$item],$info);

                            if (isset($error)) {
                                return redirect()->back()->with('error',$error);
                            }else{
                                $montant[$item] = $montant[$item] * 1;
                            }        
        
                            $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $cmup[$item] = $cmup[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
        
                            if (gettype($cmup[$item])!='integer') {
                                $error = "Une valeur non numérique rencontrée. Veuillez saisir un cmup entier numérique";
                            }
                        
                        if (!isset($error)) {

                            $beneficiaire[$item] = $request->beneficiaire[$item];
                            $type_beneficiaire[$item] = $request->type_beneficiaire[$item];

                            $setDetailDemandeAchat =  1;
                                
                            if (isset($request->echantillon[$item])) {

                                $echantillon[$item] =  $request->echantillon[$item]->store('echantillonnage','public');
                                
                                
                            }else{
                                
                                if (isset($request->echantillon_flag[$item])) {

                                    if ($request->echantillon_flag[$item] == 1) {

                                        $setDetailDemandeAchat =  2;

                                    }elseif ($request->echantillon_flag[$item] == 0){

                                        $setDetailDemandeAchat =  1;
                                        $echantillon[$item] = null;
                                        
                                    } 
                                    
                                }else{
                                    $echantillon[$item] = null;
                                }
                            }

                            if ($setDetailDemandeAchat === 1) {

                                $data = [
                                    'immobilisations_id' => $immobilisations_id,
                                    'magasin_stocks_id' => $request->magasin_stocks_id[$item],
                                    'qte' => $qte[$item], 
                                    'prixu' => $cmup[$item], 
                                    'montant' => $montant[$item], 
                                    'beneficiaire' => $beneficiaire[$item], 
                                    'type_beneficiaire' =>$type_beneficiaire[$item],
                                    'echantillon' => $echantillon[$item]
                                ];

                                # code...
                            }elseif ($setDetailDemandeAchat === 2) {

                                $data = [
                                    'immobilisations_id' => $immobilisations_id,
                                    'magasin_stocks_id' => $request->magasin_stocks_id[$item],
                                    'qte' => $qte[$item], 
                                    'prixu' => $cmup[$item], 
                                    'montant' => $montant[$item], 
                                    'beneficiaire' => $beneficiaire[$item], 
                                    'type_beneficiaire' =>$type_beneficiaire[$item],
                                ];
                                # code...
                            }

                            

                            if ($etape === "store") {
    
                                DetailImmobilisation::create($data);

                            }elseif ($etape === "immobilisation_update") {

                                if (isset($request->detail_immobilisations_id[$item])) {

                                    DetailImmobilisation::where('id',$request->detail_immobilisations_id[$item])
                                    ->update($data);

                                }else{

                                    DetailImmobilisation::create($data);

                                }

                                
                                
                            }

                            
                            
                            
                        }
                        
                        
                                       
        
                    }
                    
                }
            }
        }

        return $error;
        
    }

    function getImmobilisation($immobilisations_id){

        $immobilisation = DB::table('immobilisations as i')
        ->join('structures as s','s.code_structure','=','i.code_structure')
        ->join('gestions as g','g.code_gestion','=','i.code_gestion')
        ->join('profils as p','p.id','=','i.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('i.id',$immobilisations_id)
        ->select('p.*','u.*','a.*','g.*','s.*','i.*')
        ->first();

        return $immobilisation;
    }

    function getDetailImmobilisations($immobilisations_id){
            
        $immobilisations = DB::table('immobilisations as i')
            ->join('detail_immobilisations as di','di.immobilisations_id','=','i.id')
            ->join('magasin_stocks as ms','ms.id','=','di.magasin_stocks_id')
            ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            ->join('familles as f','f.ref_fam','=','a.ref_fam')
            ->where('di.immobilisations_id',$immobilisations_id)
            ->orderBy('di.id')
            ->select('f.*','a.*','ms.qte as qte_stock','di.*','di.id as detail_immobilisations_id','di.observations','di.qte_sortie','di.qte','i.*')
            ->get();

        return $immobilisations;
    }

    function deleteDetailImmobilisationNoAll($immobilisations_id,$array_detail_immobilisations_ids){

        DetailImmobilisation::where('immobilisations_id',$immobilisations_id)
                            ->whereNotIn('id',$array_detail_immobilisations_ids)
                            ->delete();

    }

    function getLastStatutImmobilisation($immobilisations_id){

        $statut_immobilisation_last = DB::table('statut_immobilisations as si')
            ->join('type_statut_requisitions as tsr','tsr.id','=','si.type_statut_requisitions_id')
            ->join('profils as p','p.id','=','si.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('si.immobilisations_id',$immobilisations_id)
            ->orderByDesc('si.id')
            ->limit(1)
            ->first();

        return $statut_immobilisation_last;
        
    }

    function setDetailImmobilisationObservation($immobilisations_id,$detail_immobilisations_id,$observations){

        $detail_immobilisation = DetailImmobilisation::where('id',$detail_immobilisations_id)
        ->where('immobilisations_id',$immobilisations_id)
        ->update([
            'observations'=>$observations
        ]);

        return $detail_immobilisation;

    }

    function setDetailImmobilisationQteSortie($immobilisations_id,$detail_immobilisations_id,$qte_sortie){

        $detail_immobilisation = DetailImmobilisation::where('id',$detail_immobilisations_id)
        ->where('immobilisations_id',$immobilisations_id)
        ->first();

        if($detail_immobilisation != null){

            $qte_sortie = $detail_immobilisation->qte_sortie + $qte_sortie;

            $detail_immobilisation = DetailImmobilisation::where('id',$detail_immobilisations_id)
            ->where('immobilisations_id',$immobilisations_id)
            ->update([
                'qte_sortie'=>$qte_sortie
            ]);

        }

    }

    function setDetailImmobilisationMouvement($immobilisations_id,$detail_immobilisations_id,$mouvements_id){

        $detail_immobilisation = DetailImmobilisation::where('id',$detail_immobilisations_id)
        ->where('immobilisations_id',$immobilisations_id)
        ->update([
            'mouvements_id'=>$mouvements_id
        ]);

        return $detail_immobilisation;

    }

    function getDetailImmobilisationTotauxMouvementNotNull($immobilisations_id){
        $detail_immobilisation = DB::table('detail_immobilisations as di')
        ->where('di.immobilisations_id',$immobilisations_id)
        ->select(DB::raw('SUM(di.qte) as qte_totale_demande'),DB::raw('SUM(di.qte_sortie) as qte_totale_sortie'))
        ->groupBy('di.immobilisations_id')
        ->whereNotNull('di.mouvements_id')
        ->first();

        return $detail_immobilisation;
    }

    function getDetailImmobilisationTotaux($immobilisations_id){
        $detail_immobilisation = DB::table('detail_immobilisations as di')
        ->where('di.immobilisations_id',$immobilisations_id)
        ->select(DB::raw('SUM(di.qte) as qte_totale_demande'),DB::raw('SUM(di.qte_sortie) as qte_totale_sortie'))
        ->groupBy('di.immobilisations_id')
        ->first();

        return $detail_immobilisation;
    }

    function storeTypeAffectation($type_affectations_libelle){

        $type_affectation = TypeAffectation::where('libelle',$type_affectations_libelle)->first();

        if ($type_affectation === null) {
            $type_affectation = TypeAffectation::create([
                'libelle'=>$type_affectations_libelle
            ]);
        }

    }

    function getTypeAffectation($type_affectations_libelle){

        $type_affectation = TypeAffectation::where('libelle',$type_affectations_libelle)->first();

        return $type_affectation;
    }

    function getCreditBudgetaire($code_structure,$ref_fam,$exercice,$ref_depot,$credit){
        $response = null;

        $donnes = [
            'ref_depot'=>$ref_depot,
            'code_structure'=>$code_structure,
            'ref_fam'=>$ref_fam,
            'exercice'=>$exercice,
            'credit_initiale'=>$credit,
            'consommation'=>0,
            'credit'=>$credit,
        ];

        $credit_budgetaire = DB::table('credit_budgetaires as cb')
        ->where('cb.code_structure',$code_structure)
        ->where('cb.ref_fam',$ref_fam)
        ->where('cb.exercice',$exercice)
        ->first();

        if($credit_budgetaire != null){
            $response = CreditBudgetaire::where('id',$credit_budgetaire->id)->update($donnes);
        }else{
            $response = CreditBudgetaire::create($donnes);
        }

        return $response;

    }

    function getStructures(){
        return DB::table('structures')->orderBy('nom_structure')->get();
    }

    function getExercicesAll(){

        return Exercice::orderByDesc('exercice')->get();

    }

    function getCreditBudgetaires(){

        $credit_budgetaires = DB::table('credit_budgetaires as cb')
        ->join('depots as d','d.ref_depot','=','cb.ref_depot')
        ->join('structures as s','s.code_structure','=','cb.code_structure')
        ->join('familles as f','f.ref_fam','=','cb.ref_fam')
        ->join('exercices as e','e.exercice','=','cb.exercice')
        ->join('statut_exercices as se','se.exercice','=','e.exercice')
        ->join('type_statut_exercices as tse','tse.id','=','se.type_statut_exercices_id')
        ->where('tse.libelle','Ouverture')
        ->whereNull('se.date_fin')
        ->select('cb.credit','d.ref_depot','d.design_dep','s.code_structure','s.nom_structure','f.ref_fam','f.design_fam','cb.exercice','cb.id as credit_budgetaires_id')
        ->get();

        return $credit_budgetaires;
    }

    function getCreditBudgetairesByStructure($code_structure){
        $credit_budgetaires = DB::table('credit_budgetaires as cb')
        ->join('depots as d','d.ref_depot','=','cb.ref_depot')
        ->join('structures as s','s.code_structure','=','cb.code_structure')
        ->join('familles as f','f.ref_fam','=','cb.ref_fam')
        ->join('exercices as e','e.exercice','=','cb.exercice')
        ->join('statut_exercices as se','se.exercice','=','e.exercice')
        ->join('type_statut_exercices as tse','tse.id','=','se.type_statut_exercices_id')
        ->where('tse.libelle','Ouverture')
        ->whereNull('se.date_fin')
        ->select('cb.credit','d.ref_depot','d.design_dep','s.code_structure','s.nom_structure','f.ref_fam','f.design_fam','cb.exercice','cb.id as credit_budgetaires_id')
        ->where('s.code_structure',$code_structure)
        ->get();

        return $credit_budgetaires;
    }

    /*function getEntrepriseSecu($param){
        $response = Http::get('http://srvevaldrh.cnps.ci:8080/webservice/public/api/entreprises/'.$param);

        $datas = [];

        $status = $response->status();
        if ($status === 200) {
            $datas = $response->json();
        }

        return $datas;
    }*/

    function getEntrepriseSecu($param){
        $response = Http::get('http://srvevaldrh.cnps.ci:8080/webservice/public/api/entreprises/'.$param);

        $datas = [];

        $status = $response->status();

        if($status === 200){
            $results = $response->json();
        
            if($results['status'] === 200){
    
                $entnum = (int) $results['resultat']['entnum'];
                $entcptcont = $results['resultat']['entcptcont'];
                $entraisoc = $results['resultat']['entraisoc'];
                $entsigle = $results['resultat']['entsigle'];
                $ent_email_1 = $results['resultat']['ent_email_1'];
                $entadresphy = $results['resultat']['entadresphy'];
                $entteleph = $results['resultat']['entteleph'];
                $tel_portable = $results['resultat']['tel_portable'];
    
                $datas = [
                    'entcptcont'=>$entcptcont,
                    'entnum'=>$entnum,
                    'entraisoc'=>$entraisoc,
                    'entsigle'=>$entsigle,
                    'ent_email_1'=>$ent_email_1,
                    'entadresphye'=>$entadresphy,
                    'entteleph'=>$entteleph,
                    'tel_portable'=>$tel_portable
                ];
                
            }
        }

        return $datas;
    }

    public function getStructureByCode($code_structure){
        return Structure::where('code_structure',$code_structure)->first();
    }

    public function getSectionByCodeSection($code_section){
        return DB::table('sections as s')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->where('s.code_section',$code_section)
                    ->first();
    }

    public function getStructureByName($nom_structure){
        $structure = Structure::where('nom_structure',$nom_structure)->first();

        return $structure;
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
    public function acces_validate($users_id){
        $profil = DB::table('profils')
            ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
            ->where('profils.users_id', $users_id)
            ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks']))
            ->limit(1)
            ->select('profils.id')
            ->where('profils.flag_actif',1)
            ->where('profils.id',Session::get('profils_id'))
            ->first();

        return $profil;
    }

    public function storeInventaireArticle($magasin_stocks_id,$inventaires_id,$qte_theo,$qte_phys,$ecart,$justificatif,$flag_valide,$flag_integre,$profils_id,$cmup_inventaire=null){
        
        $montant_inventaire = null;
        if($cmup_inventaire != null){
            $montant_inventaire = $cmup_inventaire * $qte_phys;
        }
        
        $data = [
            'magasin_stocks_id'=>$magasin_stocks_id,
            'inventaires_id'=>$inventaires_id,
            'qte_theo'=>$qte_theo,
            'qte_phys'=>$qte_phys,
            'ecart'=>$ecart,
            'justificatif'=>$justificatif,
            'flag_valide'=>$flag_valide,
            'flag_integre'=>$flag_integre,
            'profils_id'=>$profils_id,
            'cmup_inventaire'=>$cmup_inventaire,
            'montant_inventaire'=>$montant_inventaire,
        ];

        $inventaire_article_v = InventaireArticle::where('inventaires_id',$inventaires_id)
        ->where('magasin_stocks_id',$magasin_stocks_id)
        ->first();

        if ($inventaire_article_v === null) {


            $inventaire_articles_id = InventaireArticle::create($data)->id;

        }else{

            if ($inventaire_article_v->flag_integre != 1) {

                $inventaire_articles_id = $inventaire_article_v->id;

                InventaireArticle::where('inventaires_id',$inventaires_id)->where('magasin_stocks_id',$magasin_stocks_id)
                ->update($data);

            }
            

        }

        return $inventaire_articles_id;

    }

    public function setInventaire($inventaire_articles_id){

        $validation = DB::table('inventaire_articles as ia')
        ->join('inventaires as i','i.id','=','ia.inventaires_id')
        ->where('ia.flag_valide', 1)
        ->whereIn('ia.id', function($query) use($inventaire_articles_id){
            $query->select(DB::raw('ia2.id'))
                    ->from('inventaire_articles as ia2')
                    ->join('inventaires as i','i.id','=','ia2.inventaires_id')
                    ->where('ia2.id',$inventaire_articles_id)
                    ->whereRaw('i.id = ia2.inventaires_id');
        })
        ->select('i.id')
        ->first();

        if ($validation!=null) {
            Inventaire::where('id',$validation->id)->update([
                'flag_valide'=>1
            ]);
        }



        $integration = DB::table('inventaire_articles as ia')
                    ->join('inventaires as i','i.id','=','ia.inventaires_id')
                    ->where('ia.flag_integre', 1)
                    ->whereIn('ia.id', function($query) use($inventaire_articles_id){
                        $query->select(DB::raw('ia2.id'))
                                ->from('inventaire_articles as ia2')
                                ->join('inventaires as i','i.id','=','ia2.inventaires_id')
                                ->where('ia2.id',$inventaire_articles_id)
                                ->whereRaw('i.id = ia2.inventaires_id');
                    })
                    ->select('i.id')
                    ->first();

        if ($integration!=null) {
            Inventaire::where('id',$integration->id)->update([
                'flag_integre'=>1
            ]);
        }


    }

    public function storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire){

        $statut_inventaires = null;

        $type_statut_inventaire = TypeStatutInventaire::where('libelle', $libelle)->first();
        
        if ($type_statut_inventaire===null) {
            $type_statut_inventaires_id = TypeStatutInventaire::create([
                'libelle'=>$libelle
            ])->id;
        }else{
            $type_statut_inventaires_id = $type_statut_inventaire->id;
        }

        if (isset($type_statut_inventaires_id)) {


            $statut_inventaire = StatutInventaire::where('inventaire_articles_id',$inventaire_articles_id)
            ->orderByDesc('id')
            ->limit(1)
            ->first();

            if ($statut_inventaire!=null) {
                StatutInventaire::where('id',$statut_inventaire->id)->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
            }



            $statut_inventaires = StatutInventaire::create([
                'profils_id'=>$profils_id,
                'inventaire_articles_id'=>$inventaire_articles_id,
                'type_statut_inventaires_id'=>$type_statut_inventaires_id,
                'date_debut'=>date('Y-m-d'),
                'date_fin'=>date('Y-m-d'),
                'commentaire'=>$commentaire,
            ]);

        }

        return $statut_inventaires;

    }

    public function storeTypeMouvement($libelle_mvt){
        $type_mouvement = TypeMouvement::where('libelle', $libelle_mvt)->first();

        if ($type_mouvement!=null) {
            $type_mouvements_id = $type_mouvement->id;
        } else {
            $type_mouvements_id = TypeMouvement::create([
            'libelle'=>$libelle_mvt
            ])->id;
        }

        return $type_mouvements_id;
    }

    public function storeMagasinStock($type_mouvements_id,$magasin_stocks_id,$ecart,$qte_phys,$profils_id,$inventaire_articles_id){

        $magasin_stock = MagasinStock::where('id',$magasin_stocks_id)
        ->first();

        if ($magasin_stock!=null) {
            $prix_unit = $magasin_stock->cmup;
            $montant_ht = $prix_unit * $ecart;
            $montant_ttc = $montant_ht;
            $ref_articles =  $magasin_stock->ref_articles;
            $montant = $prix_unit * $qte_phys;
        }


        if ($type_mouvements_id!=null) {

            $data_mvt = [
                'type_mouvements_id' => $type_mouvements_id,
                'magasin_stocks_id' => $magasin_stocks_id,
                'profils_id' => $profils_id,
                'qte'=>$ecart,
                'prix_unit' => $prix_unit,
                'montant_ht' => $montant_ht,
                'montant_ttc' => $montant_ttc,
            ];

            $mouvements_id = Mouvement::create($data_mvt)->id;

            MagasinStock::where('id', $magasin_stocks_id)->update([
                'qte' => $qte_phys,
                'montant' => $montant,
            ]);

            InventaireArticle::where('id',$inventaire_articles_id)->update([
                'mouvements_id'=>$mouvements_id
            ]);


        }
    }

    public function storeCreditBudgetaire($ref_depot,$code_structure,$ref_fam,$exercice,$credit_initiale){
        $donnes = [
            'ref_depot'=>$ref_depot,
            'code_structure'=>$code_structure,
            'ref_fam'=>$ref_fam,
            'exercice'=>$exercice,
            'credit_initiale'=>$credit_initiale,
            'consommation'=>0,
            'credit'=>$credit_initiale,
        ];

        $credit_budgetaire = DB::table('credit_budgetaires as cb')
        ->where('cb.code_structure',$code_structure)
        ->where('cb.ref_fam',$ref_fam)
        ->where('cb.exercice',$exercice)
        ->first();

        if($credit_budgetaire === null){
            CreditBudgetaire::create($donnes);
        }
    }

    public function getCreditBudgetaireInsert($ref_depot,$code_structure,$ref_fam,$exercice){

        return DB::table('credit_budgetaires as cb')
        ->where('cb.code_structure',$code_structure)
        ->where('cb.ref_fam',$ref_fam)
        ->where('cb.exercice',$exercice)
        ->where('cb.ref_depot',$ref_depot)
        ->first();

    }

    public function getProfilFonctionByAgentId($agents_id){
        return ProfilFonction::where('agents_id',$agents_id)->where('flag_actif',1)->first();
    }

    public function getProfilFonctionByMle($mle){
        return ProfilFonction::join('agents as a','a.id','=','profil_fonctions.agents_id')
        ->where('a.mle',$mle)
        //->where('profil_fonctions.flag_actif',1)
        ->select('profil_fonctions.id')
        ->first();
    }

    public function getTypeStatutExercice($libelle){
        return TypeStatutExercice::where('libelle', $libelle)->first();
    }

    public function storeTypeStatutExercice($libelle){
        $type_statut_exercice = TypeStatutExercice::where('libelle', $libelle)->first();
        if($type_statut_exercice === null){
            TypeStatutExercice::create(['libelle' => $libelle]);
        }
    }

    public function setStatutExerciceAll(){
        $statut_exercices = StatutExercice::join('type_statut_exercices as tse', 'tse.id', '=', 'statut_exercices.type_statut_exercices_id')
        ->where('tse.libelle', 'Ouverture')
        ->select('statut_exercices.id','statut_exercices.exercice','statut_exercices.date_debut')
        ->get();

        foreach($statut_exercices as $statut_exercice){
            StatutExercice::where('id', $statut_exercice->id )
            ->update([
                'date_fin'=>date('Y-m-d'),
            ]);
            $libelle = 'Fermeture';
            $date_debut = $statut_exercice->date_debut;
            $date_fin = date('Y-m-d');

            $this->storeStatutExercice($libelle, $statut_exercice->exercice, Session::get('profils_id'),$date_debut,$date_fin);
        }        
    }

    public function getExerciceByExercice($exercice){
        return Exercice::where('exercice',$exercice)->first();
    }

    public function storeExercice($exercice)
    {
        $exercice_response = null;

        $get_exercice = $this->getExerciceByExercice($exercice);

        if ($get_exercice === null) {
            $exercice_response = Exercice::create([
                                    'exercice' => $exercice
                                ]);
        }
        return $exercice_response;
    }

    public function storeStatutExercice($libelle,$exercice,$profils_id,$date_debut,$date_fin=null){
        
        $statut_exercice = null;

        $this->storeTypeStatutExercice($libelle);
        $type_statut_exercice = $this->getTypeStatutExercice($libelle);

        if($type_statut_exercice != null){

            StatutExercice::where('exercice',$exercice)
            ->whereNull('date_fin')
            ->update([
                'date_fin'=>date('Y-m-d')
            ]);

            $statut_exercice = StatutExercice::create([
                'date_debut'=>$date_debut,
                'date_fin'=>$date_fin,
                'profils_id'=>$profils_id,
                'type_statut_exercices_id'=>$type_statut_exercice->id,
                'exercice'=>$exercice,
            ]);
            
            
        }

        return $statut_exercice;
        
    }

    public function getLastNumBc($exercice,$code_structure,$demande_achats_id=null){

        $sequence_id = 1;

        if(isset($demande_achats_id)){
            $demande_achat = DemandeAchat::join('credit_budgetaires as cb','cb.id','=','demande_achats.credit_budgetaires_id')
            ->where('demande_achats.id', $demande_achats_id)
            ->select('demande_achats.num_bc')
            ->limit(1)
            ->first();
    
            if($demande_achat != null){
    
                $num_bc = $demande_achat->num_bc;
                
                if(isset(explode('BCS', $num_bc)[1])){
                    $sequence_id =  (int) explode('BCS', $num_bc)[1]; 
                }elseif(isset(explode('BC', $num_bc)[1])){
                    $sequence_id =  (int) explode('BC', $num_bc)[1];
                }
    
            }
        }else{
            $demande_achat = DemandeAchat::join('credit_budgetaires as cb','cb.id','=','demande_achats.credit_budgetaires_id')
            ->where('demande_achats.exercice', $exercice)
            ->orderByDesc('demande_achats.id')
            ->select('demande_achats.num_bc')
            ->limit(1)
            ->first();
    
            if($demande_achat != null){
    
                $num_bc = $demande_achat->num_bc;
                
                if(isset(explode('BCS', $num_bc)[1])){
                    $sequence_id =  (int) explode('BCS', $num_bc)[1] + 1; 
                }elseif(isset(explode('BC', $num_bc)[1])){
                    $sequence_id =  (int) explode('BC', $num_bc)[1] + 1;
                }
    
            }
        }

        

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'BC';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);


    }

    public function getLastNumBl($exercice,$code_structure,$livraison_commandes_id=null){

        $sequence_id = 1;

        if(isset($livraison_commandes_id)){

            $demande_achat = DB::table('demande_achats as da')
            ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')
            ->where('lc.id', $livraison_commandes_id)
            ->select('lc.num_bl')
            ->first(); 

            if($demande_achat != null){

                $num_bl = $demande_achat->num_bl;
                
                if(isset(explode('BL', $num_bl)[1])){
                    $sequence_id =  (int) explode('BL', $num_bl)[1];
                }
    
            }

        }else{
           $demande_achat = DB::table('demande_achats as da')
            ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')
            ->where('da.exercice', $exercice)
            ->orderByDesc('lc.id')
            ->select('lc.num_bl')
            ->limit(1)
            ->first(); 

            if($demande_achat != null){

                $num_bl = $demande_achat->num_bl;
                
                if(isset(explode('BL', $num_bl)[1])){
                    $sequence_id =  (int) explode('BL', $num_bl)[1] + 1;
                }
    
            }
        }
        

        

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'BL';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);

    }

    public function getLastNumDem($exercice,$code_structure,$demande_fonds_id=null){

        $sequence_id = 1;

        if(isset($demande_fonds_id)){
            $demande_fond = DemandeFond::join('credit_budgetaires as cb','cb.id','=','demande_fonds.credit_budgetaires_id')
            ->where('demande_fonds.id', $demande_fonds_id)
            ->select('demande_fonds.num_dem')
            ->limit(1)
            ->first();
    
            if($demande_fond != null){
    
                $num_dem = $demande_fond->num_dem;
                
                if(isset(explode('DF', $num_dem)[1])){
                    $sequence_id =  (int) explode('DF', $num_dem)[1];                     
                }
                  
            }
        }else{
            $demande_fond = DemandeFond::join('credit_budgetaires as cb','cb.id','=','demande_fonds.credit_budgetaires_id')
            ->where('demande_fonds.exercice', $exercice)
            ->orderByDesc('demande_fonds.id')
            ->select('demande_fonds.num_dem')
            ->limit(1)
            ->first();
    
            if($demande_fond != null){
    
                $num_dem = $demande_fond->num_dem;
                
                if(isset(explode('DF', $num_dem)[1])){
                    $sequence_id =  (int) explode('DF', $num_dem)[1] + 1;                     
                }
                  
    
            }

        }

        

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'DF';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);


    }

    public function getLastNumBci($exercice,$code_structure,$ref_depot){

        $sequence_id = 1; 

        $requisition = Requisition::join('structures as s','s.code_structure','=','requisitions.code_structure')
        ->where('s.ref_depot', $ref_depot)
        ->where('requisitions.exercice', $exercice)
        ->orderByDesc('requisitions.id')
        ->select('requisitions.num_bc')
        ->limit(1)
        ->first();

        if($requisition != null){

            $num_bc = $requisition->num_bc;
            
            if(isset(explode('BCI', $num_bc)[1])){
                $sequence_id =  (int) explode('BCI', $num_bc)[1] + 1; 
            }
        }

        $sequence_id = str_pad($sequence_id, 4, "0", STR_PAD_LEFT);

        $type_bc = 'BCI';

        return $this->generateNumBc($exercice,$code_structure,$type_bc,$sequence_id);


    }

    public function generateRefArticle($ref_fam){

        $sequence_id = 1;

        $article = Article::where('ref_fam', $ref_fam)
        ->orderByDesc('ref_articles')
        ->select('ref_articles')
        ->limit(1)
        ->first();

        if($article != null){

            $ref_articles = $article->ref_articles;
            
            if(isset(explode($ref_fam, $ref_articles)[1])){

                $sequence_id =  (int) explode($ref_fam, $ref_articles)[1] + 1;

            }

        }

        $sequence_id = str_pad($sequence_id, 2, "0", STR_PAD_LEFT);
        
        $new_ref_articles = $ref_fam.''.$sequence_id;

        return $new_ref_articles;

    }

    function getValiderDemandeAchatVerif($demande_achats_id){

        $demande_achats = DB::table('demande_achats as da')
            ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
            ->join('valider_demande_achats as vda','vda.detail_demande_achats_id','=','dda.id')
            ->where('dda.demande_achats_id',$demande_achats_id)
            ->get();

        return $demande_achats;

    }

    public function getOrganisationActifs(){
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
        ->get();
    }

    public function getDescriptionArticles(){
        return DescriptionArticle::get();
    }

    public function storeDescriptionArticle($libelle){
        $description_article = DescriptionArticle::where('libelle', $libelle)->first();
        if($description_article === null){
            DescriptionArticle::create(['libelle' => $libelle]);
        }
    }

    public function getDescriptionArticle($libelle){
        return DescriptionArticle::where('libelle', $libelle)->first();
    }

    public function getDetailDemandeAchatDescription($demande_achats_id){
        return DetailDemandeAchat::where('demande_achats_id', $demande_achats_id)
            ->whereNotNull('description_articles_id')
            ->get();
    }

    public function thmx_currency_convert($device){
        try{
            $url = 'https://api.exchangerate-api.com/v4/latest/'.$device;
            $json = file_get_contents($url);
            $exp = json_decode($json);

            $convert = $exp->rates->XOF;

            return $convert;
        }catch (Exception $e) {

        }

        
    } 

    public function getStatutOrganisation($organisations_id){
        return StatutOrganisation::where('organisations_id',$organisations_id)->first();
    }

    public function valider_requisition($profils_id,$demandes_id,$prixu,$montant,$qte,$flag_valide){

        $valider_requisition = null;
        $data = [
                    'profils_id'=>$profils_id,
                    'demandes_id'=>$demandes_id,
                    'prixu'=>$prixu,
                    'montant'=>$montant,
                    'qte'=>$qte,
                    'flag_valide'=>$flag_valide,
                ]; 

                

        $valider_requisitions = ValiderRequisition::where('demandes_id',$demandes_id)
        ->first();
        if ($valider_requisitions!=null) {
            $valider_requisition = ValiderRequisition::where('id',$valider_requisitions->id)
            ->update($data);
        }else{
            $valider_requisition = ValiderRequisition::create($data);
        }

        return $valider_requisition;

    }


    public function setDetailDemandesByIdRequisition($requisitions_id,$date_demande){

        $flag_valide = 1;

        $demandes = Demande::where('requisitions_id',$requisitions_id)->get();
        foreach($demandes as $demande){
            $this->valider_requisition($demande->profils_id,$demande->id,$demande->prixu,$demande->montant,$demande->qte,$flag_valide);

            ValiderRequisition::where('demandes_id',$demande->id)
            ->update([
                'created_at'=>$date_demande,
                'updated_at'=>$date_demande,
            ]);
        }
        

    }

    public function storeValiderRequisition($request,$profils_id,$requisitions_id){
        $error = null;
        if (isset($request->approvalcd)) {

            foreach ($request->approvalcd as $item => $value) {

                if (isset($request->approvalcd[$item])) {

                    if ($request->qte_validee[$item] > 0) {
                        
                        $error = $this->valide_saisie($request,$item);

                    }
                    $break = 1;
                }
                
                if (isset($break)) {
                    break;
                }
            }

        }else{
            $error = "Veuillez sélectionner au moins un article";
        }

        if (isset($request->approvalcd)) {
            foreach ($request->approvalcd as $item => $value) {

                if (isset($request->approvalcd[$item])) {

                    $qte_validee_format = filter_var($request->qte_validee[$item], FILTER_SANITIZE_NUMBER_INT);

                    $cmup_format = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);

                    $montant[$item] = $qte_validee_format * $cmup_format;
                    
                    $flag_valide = 1;

                    $valider_requisition = $this->valider_requisition($profils_id,$request->demandes_id[$item],$cmup_format,$montant[$item],$qte_validee_format,$flag_valide);

                }
                
            }
        }

        $tableau_demande = [];

        if (isset($request->approvalcd)) {
            foreach ($request->approvalcd as $item => $value) {
                    if (isset($request->approvalcd[$item])) {

                        if ($request->qte_validee[$item] > 0) {
                            
                            $tableau_demande[$item] = $request->demandes_id[$item];

                        }

                    }
                
            }
        }

        if (count($tableau_demande) > 0) {
            
            //liste des demandes non cochées
            $table = "valider_requisitions";
            $demande_non_cochees = $this->tableau_demande_non_coche($requisitions_id,$table,$tableau_demande);

            foreach ($demande_non_cochees as $demande_non_cochee) {
                
                $demandes_id_not = $demande_non_cochee->demandes_id;

                foreach ($request->demandes_id as $item => $value) {
                    
                    if ($request->demandes_id[$item] == $demandes_id_not ) {

                        
                            
                        $qte_validee_format = filter_var($request->qte_validee[$item], FILTER_SANITIZE_NUMBER_INT);

                        $cmup_format = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                        
                        $montant[$item] = $qte_validee_format * $cmup_format;
                        

                        $flag_valide = 0;

                        $valider_requisition = $this->valider_requisition($profils_id,$request->demandes_id[$item],$cmup_format,$montant[$item],$qte_validee_format,$flag_valide);

                        

                    }
                }

            }


        

        }

        return $error;
    }

    public function procedureSetDemande($request){
        if(isset($request->demandes_id) && isset($request->qte_reelle)){
            foreach($request->demandes_id as $key => $demandes_id){
                $qte = $request->qte_reelle[$key];
                $prixu = $request->cmup[$key];
                $montant = $prixu * $qte;
                Demande::where('id',$demandes_id)->update([
                    'qte'=>$qte,
                    'prixu'=>$prixu,
                    'montant'=>$montant,
                ]);
            }
        }
    }
    public function storeValiderRequisition2($profils_id,$demandes_id,$qte_validee,$cmup){        

        $qte_validee_format = filter_var($qte_validee, FILTER_SANITIZE_NUMBER_INT);

        $cmup_format = filter_var($cmup, FILTER_SANITIZE_NUMBER_INT);

        $montant = $qte_validee_format * $cmup_format;
        
        $flag_valide = 1;
    
        $this->valider_requisition($profils_id,$demandes_id,$cmup_format,$montant,$qte_validee_format,$flag_valide);

    }

    public function getArticlesNotInMagasinStock($ref_magasin,$ref_fam){

        $articles = DB::table('articles as a')
            ->select('a.ref_articles')
            ->where('a.ref_fam',$ref_fam)
            ->whereNotIn('a.ref_articles', function($query) use($ref_magasin){
                $query->select(DB::raw('ms.ref_articles'))
                        ->from('magasin_stocks as ms')
                        ->where('ms.ref_magasin',$ref_magasin)
                        ->whereRaw('ms.ref_articles = a.ref_articles');
            })
            ->get();

        return $articles;
    }

    public function storeDepartement($nom_departement){
        $departement = Departement::where('nom_departement',$nom_departement)->first();
                        
        if ($departement===null) {
            Departement::create([
                'nom_departement'=>$nom_departement
            ]);
        }
    }

    public function getDepartement($nom_departement){
        return Departement::where('nom_departement',$nom_departement)->first();
    }

    public function setRequisitionDepartement($requisitions_id,$departements_id){
        Requisition::where('id',$requisitions_id)
        ->update([
            'departements_id'=>$departements_id
        ]);
    }

    public function valide_saisie($request,$item){
        $error = null;

        $qte_validee[$item] = filter_var($request->qte_validee[$item], FILTER_SANITIZE_NUMBER_INT);

        if(isset($request->qte_reelle[$item])){
            $qte_reelle[$item] = filter_var($request->qte_reelle[$item], FILTER_SANITIZE_NUMBER_INT);
        }
        
                    
        try {
            $qte_validee[$item] = $qte_validee[$item] * 1;
        } catch (\Throwable $th) {
            $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
        }

        if (gettype($qte_validee[$item])!='integer') {

            $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
        }

        if(isset($qte_reelle[$item])){
            try {
                $qte_reelle[$item] = $qte_reelle[$item] * 1;
            } catch (\Throwable $th) {
                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
            }
    
            if (gettype($qte_reelle[$item])!='integer') {
    
                $error = "Une valeur non numérique rencontrée. Veuillez saisir des quantités d'article entier numérique";
            }
        }
        


        if ($qte_validee[$item] === null) {
            $qte_validee[$item] = 0;
        }

        if(isset($qte_reelle[$item])){
            $dem = Demande::where('id', $request->demandes_id[$item])->first();
            if($dem != null){
    
                Demande::where('id', $dem->id)
                ->update([
                    'qte'=>$qte_reelle[$item],
                    'montant'=>$qte_reelle[$item]*$dem->prixu,
                ]);
                
            }
        }
        
        
        $qte[$item] = Demande::where('id', $request->demandes_id[$item])->first()->qte;
        if ($qte[$item] < $qte_validee[$item]) {
            $error = "La quantité validée ne peut être supérieure à la quantité demandée";
        }

        return $error;
    }

    public function demande_consolide_control($requisitions_id){
        $error = null;

        $requisition = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->where('r.id',$requisitions_id)
                ->whereNotNull('d.requisitions_id_consolide')
                ->first();    
        $this->breakConsolidation($requisitions_id);    

        if ($requisition!=null) {
            $requisitions_id_consolide = $requisition->requisitions_id_consolide;

            $requisitions = DB::table('requisitions as r')
            ->join('demandes as d','d.requisitions_id','=','r.id')
            ->where('r.id',$requisitions_id)
            ->whereNotNull('d.requisitions_id_consolide')
            ->select('d.id as demandes_id','r.id as requisitions_id')
            ->get();
            foreach ($requisitions as $requisition) {

                $demande_consolid = DB::table('demande_consolides as dc')
                ->where('dc.demandes_id',$requisition->demandes_id)
                ->where('dc.requisitions_id',$requisition->requisitions_id)
                ->whereNotIn('dc.demandes_ids',function($query){
                    $query->select(DB::raw('vr.demandes_id'))
                            ->from('valider_requisitions as vr')
                            ->where('vr.flag_valide',1)
                            ->whereRaw('dc.demandes_ids = vr.demandes_id');
                })
                ->select('dc.id','dc.demandes_id','dc.demandes_ids','dc.prixu')
                ->first();

                if ($demande_consolid!=null) {
                    //Debut de l'annulation
                    Demande::where('id',$demande_consolid->demandes_id)
                            ->update([
                                'requisitions_id_consolide'=>null,
                            ]);
                    DemandeConsolide::where('id',$demande_consolid->id)->delete();

                    // mettre à jour les quantités consolidées
                    $this->consolidation($requisitions_id_consolide,Session::get('profils_id'));

                }else{
                    $error = "Impossible d'annuler cette demande";
                }
            }

        }
        return $error;
    }

    public function breakConsolidation($requisitions_id){

        $statut_requisition = $this->getLastStatutRequisition($requisitions_id);

        if($statut_requisition != null){

            if($statut_requisition->libelle === "Consolidée (Pilote AEE)" or $statut_requisition->libelle === "Annulé (Responsable des stocks)"){

                $requisitions_distincts = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->where('d.requisitions_id_consolide',$requisitions_id)
                ->select('d.requisitions_id')
                ->distinct('d.requisitions_id')
                ->get();

                $commentaire = 'Annulation de la consolidation des demandes de la structure';

                $libelle = "Transmis (Pilote AEE)";
                $this->storeTypeStatutRequisition($libelle);
                
                $type_statut_requisition = $this->getTypeStatutRequisition($libelle);

                if($type_statut_requisition != null){
                    foreach ($requisitions_distincts as $requisition) {
                        $this->storeStatutRequisition($requisition->requisitions_id, Session::get('profils_id'), $type_statut_requisition->id, $commentaire);
                    }
                }

                    $requisitions = DB::table('requisitions as r')
                    ->join('demandes as d','d.requisitions_id','=','r.id')
                    ->where('d.requisitions_id_consolide',$requisitions_id)
                    ->select('d.id as demandes_id','d.requisitions_id as requisitions_id','d.requisitions_id_consolide')
                    ->get();                

                    foreach ($requisitions as $requisition) {

                        $demande_consolid = DB::table('demande_consolides as dc')
                        ->where('dc.demandes_id',$requisition->demandes_id)
                        ->where('dc.requisitions_id',$requisition->requisitions_id)
                        ->whereNotIn('dc.demandes_ids',function($query){
                            $query->select(DB::raw('vr.demandes_id'))
                                    ->from('valider_requisitions as vr')
                                    ->where('vr.flag_valide',1)
                                    ->whereRaw('dc.demandes_ids = vr.demandes_id');
                        })
                        ->select('dc.id','dc.demandes_id','dc.demandes_ids','dc.prixu')
                        ->first();

                        if ($demande_consolid!=null) {
                            //Debut de l'annulation
                            Demande::where('id',$demande_consolid->demandes_id)
                                    ->update([
                                        'requisitions_id_consolide'=>null,
                                    ]);
                            DemandeConsolide::where('id',$demande_consolid->id)->delete();

                            // mettre à jour les quantités consolidées
                            $this->consolidation($requisition->requisitions_id_consolide,Session::get('profils_id'));

                        }
                    }

            }

        }
    }

    public function getLastStatutRequisition($requisitions_id){
        return DB::table('statut_requisitions as sr')
            ->where('sr.requisitions_id', $requisitions_id)
            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
            ->join('profils as p','p.id','=','sr.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->orderByDesc('id')
            ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
            ->limit(1)
            ->first();
    }

    public function demande_consolide($demandes_id,$requisitions_id_consolide){

        $demande = Demande::where('id',$demandes_id)->update([
            'requisitions_id_consolide'=>$requisitions_id_consolide
        ]);

        return $demande;
    }

    public function consolidation($requisitions_id_consolide,$profils_id){
        $demandes_ids = null;
        $donnees = [];

        $consolides = DB::table('demandes as d')
                    ->where('d.requisitions_id_consolide',$requisitions_id_consolide)
                    ->groupBy('d.magasin_stocks_id')
                    ->select(DB::raw('SUM(d.qte) as qte'),DB::raw('d.magasin_stocks_id'))
                    ->get();

        foreach ($consolides as $consolide) {
            
            $magasin_stock = MagasinStock::where('id',$consolide->magasin_stocks_id)->first();
            if ($magasin_stock!=null) {
                $prixu = $magasin_stock->cmup;
            }else{
                $prixu = 0;
            }
            
            $montant = $consolide->qte * $prixu;

            $demande = Demande::where('requisitions_id',$requisitions_id_consolide)
            ->where('magasin_stocks_id',$consolide->magasin_stocks_id)
            ->first();
            if ($demande!=null) {

                $demandes_ids = $demande->id;
                $donnees[$demandes_ids] = $demandes_ids;

                Demande::where('id',$demandes_ids)
                        ->update([
                            'qte'=>$consolide->qte,
                            'prixu'=>$prixu,
                            'montant'=>$montant,
                            'profils_id'=>$profils_id,
                        ]);

            }else{

                $demandes_ids = Demande::create([
                    'qte'=>$consolide->qte,
                    'prixu'=>$prixu,
                    'montant'=>$montant,
                    'requisitions_id'=>$requisitions_id_consolide,
                    'magasin_stocks_id'=>$consolide->magasin_stocks_id,
                    'profils_id'=>$profils_id,
                ])->id;

                $donnees[$demandes_ids] = $demandes_ids;

            }
            
        }

        Demande::whereNotIn('id',$donnees)
        ->where('requisitions_id',$requisitions_id_consolide)
        ->delete();

        return $demandes_ids;
    }

    public function demande_consolide_detail($requisitions_id_consolide){
        $demandes = Demande::where('requisitions_id',$requisitions_id_consolide)
        ->get();
        foreach ($demandes as $demande) {

            $demandes_ids = $demande->id;
            $magasin_stocks_id = $demande->magasin_stocks_id;

            $demande_consolides = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
            ->where('magasin_stocks_id',$magasin_stocks_id)
            ->get();

            foreach ($demande_consolides as $demande_consolide) {
                $demandes_id = $demande_consolide->id;
                $qte = $demande_consolide->qte;
                $prixu = $demande_consolide->prixu;
                $montant = $demande_consolide->montant;
                $requisitions_id = $demande_consolide->requisitions_id;
                $profils_id = $demande_consolide->profils_id;
                



                $demande_consolide_first = DemandeConsolide::where('demandes_id',$demandes_id)
                ->where('demandes_ids',$demandes_ids)
                ->first();
                if ($demande_consolide_first!=null) {
                    DemandeConsolide::where('id',$demande_consolide_first->id)
                    ->update([
                        'qte'=>$qte,
                        'prixu'=>$prixu,
                        'montant'=>$montant,
                        'requisitions_id'=>$requisitions_id,
                        'magasin_stocks_id'=>$magasin_stocks_id,
                        'profils_id'=>$profils_id,
                    ]);
                }else{
                    DemandeConsolide::create([
                        'demandes_id'=>$demandes_id,
                        'demandes_ids'=>$demandes_ids,
                        'qte'=>$qte,
                        'prixu'=>$prixu,
                        'montant'=>$montant,
                        'requisitions_id'=>$requisitions_id,
                        'magasin_stocks_id'=>$magasin_stocks_id,
                        'profils_id'=>$profils_id,
                    ]);
                }



            }
        }
    }

    public function notification_pilote($requisitions_id_consolide){

        $demandes = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
        ->groupBy('requisitions_id')
        ->select(DB::raw('requisitions_id'))
        ->get();

        foreach ($demandes as $demande) {
            $requisitions_id = $demande->requisitions_id;

            //nombre total d'articles de la demande du demandeur
            $nbre_total = count(Demande::where('requisitions_id', $requisitions_id)->get());

            $nbre_consolide = count(Demande::where('requisitions_id', $requisitions_id)
            ->where('requisitions_id_consolide', $requisitions_id_consolide)
            ->get());

            if ($nbre_total === $nbre_consolide) {
                $subject = "Demande validé (Pilote AEE)";
            } elseif ($nbre_total > $nbre_consolide) {
                $subject = "Demande partiellement validé (Pilote AEE)";
            }

            if (isset($subject)) {
               $pilotes = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Pilote AEE')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('r.id', $requisitions_id)
                ->get();
                foreach ($pilotes as $pilote) {
    
                    $details = [
                        'email' => $pilote->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }
            }
        }
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

    public function pilote_structure_connectee($requisitions_id){

        $pilote_aee = DB::table('users as u')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->join('agent_sections as ase','ase.agents_id','=','a.id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('requisitions as r','r.code_structure','=','s.code_structure')
        ->join('structures as st','st.code_structure','=','r.code_structure')
        ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
        ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
        ->where('tp.name','Pilote AEE')
        ->where('tsas.libelle','Activé')
        ->where('p.flag_actif',1)
        ->whereRaw('st.code_structure = r.code_structure')
        // ->where('p.users_id', auth()->user()->id)
        ->where('r.id', $requisitions_id)
        ->get();
        

        return $pilote_aee;

    }

    public function notification_responsable_stock($requisitions_id,$subject){

        $responsable_stock = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Responsable des stocks')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('r.id', $requisitions_id)
                ->get();
                foreach ($responsable_stock as $pilote) {
    
                    $details = [
                        'email' => $pilote->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }
            
                return $responsable_stock;
        
    }

    public function notification_demandeur_consolide2($requisitions_id_consolide,$subject){

        $demandes = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
        ->groupBy('requisitions_id')
        ->select(DB::raw('requisitions_id'))
        ->get();

        foreach ($demandes as $demande) {
            $requisitions_id = $demande->requisitions_id;

            //nombre total d'articles de la demande du demandeur

            if (isset($subject)) {

                $requisition = DB::table('requisitions as r')
                ->join('profils as p','p.id','=','r.profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->where('r.id',$requisitions_id)
                ->select('u.email')
                ->first();
                if ($requisition!=null) {
    
                    $details = [
                        'email' => $requisition->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }

            }

            
        }
        

        
    }

    public function notification_pilote2($requisitions_id,$subject){

               $pilotes = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Pilote AEE')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('r.id', $requisitions_id)
                ->get();
                foreach ($pilotes as $pilote) {
    
                    $details = [
                        'email' => $pilote->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }
            
        
    }

    public function notification_hierarchie_consolide2($requisitions_id,$responsable,$subject){

                if ($responsable === 1) {

                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n1')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n1'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
                }elseif ($responsable === 2) {
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n2')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n2'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
                    
                }elseif ($responsable === 3) {
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n1')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n1'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n2')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n2'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
                }

    }

    public function depot_requisition($requisitions_id){

        $depot = DB::table('requisitions as r')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->where('r.id', $requisitions_id)
                ->first();
            
        return $depot;
        
    }
    
    public function tableau_demande_non_coche($requisitions_id,$table,$tableau_demande){

        $demandes = [];
        if (isset($table)) {
            if ($table === "valider_requisitions") {

                $demandes = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->where('r.id',$requisitions_id)
                ->whereNotIn('d.id',$tableau_demande)
                ->select('d.id as demandes_id')
                ->get();

            }
        }

        return $demandes;
    }

    public function statut_valider_requisition($requisitions_id,$table){

        $libelle = null;

        if (isset($table)) {
            if ($table === "valider_requisitions") {

                $nbre_total = count(
                    Demande::where('requisitions_id',$requisitions_id)->get()
                );

                $demande = Demande::where('requisitions_id',$requisitions_id)
                ->select(DB::raw('SUM(qte) as qte'))
                ->groupBy('requisitions_id')
                ->first();
                if ($demande!=null) {
                    $nbre_qte_total = $demande->qte;
                }

                $valider_requisition = DB::table('demandes as d')
                ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                ->where('vr.flag_valide',1)
                ->select(DB::raw('SUM(vr.qte) as qte'))
                ->where('d.requisitions_id',$requisitions_id)->get();
                if ($valider_requisition!=null) {
                    $nbre_qte_valide = $demande->qte;
                }

                $nbre_valide = count(
                    DB::table('demandes as d')
                    ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                    ->where('vr.flag_valide',1)
                    ->where('d.requisitions_id',$requisitions_id)->get()
                );

                if ($nbre_total === $nbre_valide) {
                    $libelle = "Validé (Responsable des stocks)";

                    if ($nbre_qte_total === $nbre_qte_valide) {
                        $libelle = "Validé (Responsable des stocks)";
                    }elseif ($nbre_qte_total > $nbre_qte_valide) {
                        $libelle = "Partiellement validé (Responsable des stocks)";
                    }


                }elseif ($nbre_total > $nbre_valide) {
                    $libelle = "Partiellement validé (Responsable des stocks)";
                }
            }
        }

        return $libelle;

    }

    public function hierarchie($requisitions_id){

        $hierarchie = DB::table('requisitions as r')
            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
            ->join('profils as p','p.id','=','r.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->join('hierarchies as h','h.agents_id','=','a.id')
            ->where('h.flag_actif',1)
            ->where('p.flag_actif',1)
            ->whereNotNull('h.agents_id_n2')
            ->where('sr.requisitions_id',$requisitions_id)
            ->first();

        return $hierarchie;
    }

    public function demande_consolide_annule($requisitions_id,$donnee_actuelles){
        $donnees = [];

        if (count($donnee_actuelles) > 0) {
            
            //demande de cette requisition précedemment consolidée
            

            $demandes = Demande::where('requisitions_id',$requisitions_id)
            ->whereNotNull('requisitions_id_consolide')
            ->get();

            foreach ($demandes as $demande) {
                

                $demand = DB::table('demandes as d')
                ->where('d.requisitions_id',$requisitions_id)
                ->where('d.id',$demande->id)
                ->whereNotIn('d.id',function($query) use($donnee_actuelles,$requisitions_id){
                    $query->select(DB::raw('dd.id'))
                          ->from('demandes as dd')
                          ->whereIn('dd.id',$donnee_actuelles)
                          ->where('dd.requisitions_id',$requisitions_id)
                          ->whereRaw('d.id = dd.id');
                })
                ->first();

                if ($demand != null ) {
                    $donnees[$demand->id] = $demand->id;
                }
            }

        }


        $requisitions = DB::table('requisitions as r')
        ->join('demandes as d','d.requisitions_id','=','r.id')
        ->whereIn('d.id',$donnees)
        ->select('d.id as demandes_id','r.id as requisitions_id','d.requisitions_id_consolide')
        ->get();
        foreach ($requisitions as $requisition) {

            $requisitions_id_consolide = $requisition->requisitions_id_consolide;

            $demande_consolid = DB::table('demande_consolides as dc')
            ->where('dc.demandes_id',$requisition->demandes_id)
            ->where('dc.requisitions_id',$requisition->requisitions_id)
            ->whereNotIn('dc.demandes_ids',function($query){
                $query->select(DB::raw('vr.demandes_id'))
                        ->from('valider_requisitions as vr')
                        ->where('vr.flag_valide',1)
                        ->whereRaw('dc.demandes_ids = vr.demandes_id');
            })
            ->select('dc.id','dc.demandes_id')
            ->first();

            if ($demande_consolid!=null) {
                //Debut de l'annulations
                Demande::where('id',$demande_consolid->demandes_id)
                        ->update([
                            'requisitions_id_consolide'=>null,
                        ]);
                DemandeConsolide::where('id',$demande_consolid->id)->delete();

                // mettre à jour les quantités consolidées
                $this->consolidation($requisitions_id_consolide,Session::get('profils_id'));

            }
        }

                
    }

    public function demande($requisitions_id,$type_profils_name){

        $demandes = [];

        if ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks') {
            $demandes = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('valider_requisitions as vr', 'vr.demandes_id', '=', 'd.id')
                ->join('profils as p', 'p.id', '=', 'd.profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as ag', 'ag.id', '=', 'u.agents_id')
                ->where('r.id', $requisitions_id)
                ->where('vr.flag_valide', 1) // réquisition validée
                ->select('d.id', 'a.ref_articles', 'a.design_article', 'ms.cmup', 'd.qte as qte_demandee', 'ms.cmup', 'vr.qte as qte_validee', 'ms.qte', 'ag.mle', 'ag.nom_prenoms')
                ->whereIn('vr.id', function ($query) {
                    $query->select(DB::raw('vrr.id'))
                            ->from('demandes as dd')
                            ->join('valider_requisitions as vrr', 'vrr.demandes_id', '=', 'dd.id')
                            ->join('profils as pp', 'pp.id', '=', 'vrr.profils_id')
                            ->join('type_profils as tpp', 'tpp.id', '=', 'pp.type_profils_id')
                            ->join('statut_requisitions as sr', 'sr.requisitions_id', '=', 'dd.requisitions_id')
                            ->join('type_statut_requisitions as tsr', 'tsr.id', '=', 'sr.type_statut_requisitions_id')
                            ->where('vrr.flag_valide', 1)
                            ->whereIn('tsr.libelle', ['Validé (Responsable des stocks)','Partiellement validé (Responsable des stocks)'])
                            ->whereIn('tpp.name', ['Responsable des stocks'])
                            ->whereRaw('dd.id = d.id')
                            ->whereRaw('vrr.id = vr.id');
                })
                ->get();
    

        }elseif ($type_profils_name === "Pilote AEE") {
            $demandes = DB::table('requisitions as r')
            ->join('demandes as d','d.requisitions_id','=','r.id')
            ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
            ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            ->join('structures as st','st.code_structure','=','r.code_structure')
            ->join('livraisons as l', 'l.demandes_id', '=', 'd.id')
            ->join('profils as p', 'p.id', '=', 'r.profils_id')
            ->join('users as u', 'u.id', '=', 'p.users_id')
            ->join('agents as ag', 'ag.id', '=', 'u.agents_id')
            ->where('r.id', $requisitions_id)
            ->where('l.statut', 1)
            ->where('u.id', auth()->user()->id)
            ->whereRaw('l.qte != l.qte_recue')
            ->select('d.id', 'a.ref_articles', 'a.design_article', 'ms.cmup', 'd.qte as qte_demandee', 'ms.cmup', 'l.qte as qte_validee', 'ms.qte', 'ag.mle', 'ag.nom_prenoms', 'l.id as livraisons_id', 'l.qte_recue','l.created_at')
            ->whereIn('l.id', function ($query) {
                $query->select(DB::raw('ll.id'))
                        ->from('demandes as dd')
                        ->join('livraisons as ll', 'll.demandes_id', '=', 'dd.id')
                        ->where('ll.statut', 1)
                        ->whereRaw('dd.id = d.id')
                        ->whereRaw('ll.id = l.id');
            })
            ->get();
        }

        return $demandes;
        

    }

    public function setDecimal($saisie,$info){
        $error = null;

        if($saisie === ""){
            $error = $info." : Une valeur non numérique rencontrée.";
        }

        return $error;
    }

    public function getBeneficiaires($type_profils_name,$code_structure=null){

        $beneficiaires = null;

        if ($type_profils_name === "Pilote AEE") {

            /*$beneficiaires = Agent::where('tp.name','Pilote AEE')
            ->join('users as u','u.agents_id','=','agents.id')
            ->join('profils as p','p.users_id','=','u.id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->where('u.agents_id',auth()->user()->agents_id)
            ->limit(1)
            ->where('p.flag_actif',1)
            ->where('u.flag_actif',1)
            ->where('p.id',Session::get('profils_id'))
            ->get();*/

            $beneficiaires = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->join('depots as d', 'd.ref_depot', '=', 's.ref_depot')
                ->distinct('a.mle', 'a.nom_prenoms','a.id as agents_id')
                ->select('a.mle', 'a.nom_prenoms','a.id as agents_id')
                ->where('p.flag_actif',1)
                ->where('tsase.libelle','Activé')
                ->where('u.flag_actif',1)
                ->where('s.code_structure',$code_structure)
                ->orderByDesc('a.nom_prenoms')
                ->get();

        }elseif ($type_profils_name === "Gestionnaire des achats") {
            /*$beneficiaires = DB::table('demande_fonds as df')
            ->join('profils as p','p.id','=','df.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            // ->where('tp.name','Pilote AEE')
            ->limit(1)
            ->where('df.id',$demande_fonds_id)
            ->get();*/

            $beneficiaires = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->join('depots as d', 'd.ref_depot', '=', 's.ref_depot')
                ->distinct('a.mle', 'a.nom_prenoms','a.id as agents_id')
                ->select('a.mle', 'a.nom_prenoms','a.id as agents_id')
                ->where('p.flag_actif',1)
                ->where('tsase.libelle','Activé')
                ->where('u.flag_actif',1)
                ->orderByDesc('a.nom_prenoms')
                ->get();
        }

        return $beneficiaires;
        
    }

    public function getChargeSuivi(){

        $charge_suivis = Agent::join('users as u','u.agents_id','=','agents.id')
        ->where('u.flag_actif',1)
        ->select('agents.id as agents_id','agents.mle','agents.nom_prenoms')
        ->distinct('agents.id','agents.mle')
        ->orderBy('agents.nom_prenoms')
        ->get();

        /*
        $charge_suivis = Agent::join('users as u','u.agents_id','=','agents.id')
        ->join('agent_sections as ase','ase.agents_id','=','u.agents_id')
        ->join('sections as s','s.id','=','ase.sections_id')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->join('profils as p','p.users_id','=','u.id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->where('p.flag_actif',1)
        ->where('u.flag_actif',1)
        ->select('agents.id as agents_id','agents.mle','agents.nom_prenoms')
        ->distinct('agents.id','agents.mle')
        ->orderBy('agents.nom_prenoms')
        ->whereIn('st.code_structure',function($query) use($code_structure){
              $query->select(DB::raw('stt.code_structure'))
                    ->from('agents as aa')
                    ->join('users as uu','uu.agents_id','=','aa.id')
                    ->join('agent_sections as asee','asee.agents_id','=','uu.agents_id')
                    ->join('sections as ss','ss.id','=','asee.sections_id')
                    ->join('structures as stt','stt.code_structure','=','ss.code_structure')
                    ->join('profils as pp','pp.users_id','=','uu.id')
                    ->join('type_profils as tpp','tpp.id','=','pp.type_profils_id')
                    ->where('pp.flag_actif',1)
                    ->where('uu.flag_actif',1)
                    ->where('pp.id',Session::get('profils_id'))
                    ->where('tpp.name','Pilote AEE')
                    ->where('aa.id',auth()->user()->agents_id)
                    ->where('stt.code_structure',$code_structure)
                    ->whereRaw('stt.code_structure = st.code_structure');
        })
        ->get();
        */

        return $charge_suivis;

        

    }

    public function setDemandeFond($demande_fonds_id,$code_section,$profils_id,$solde_avant_op,$agents_id,$credit_budgetaires_id,$ref_fam,$exercice,$intitule,$montant,$observation,$code_gestion,$num_dem=null){

        DemandeFond::where('id',$demande_fonds_id)
        ->update([
            'code_section' => $code_section,
            'profils_id' => $profils_id,
            'solde_avant_op' => $solde_avant_op,
            'agents_id' => $agents_id,
            'credit_budgetaires_id' => $credit_budgetaires_id,
            'ref_fam' => $ref_fam,
            'exercice' => $exercice,
            'intitule' => $intitule,
            'montant' => $montant,
            'observation' => $observation,
            'code_gestion' => $code_gestion,
            'num_dem' => $num_dem,
        ]);
        
        return $demande_fonds_id;

    }

    public function getDemandeFond($demande_fonds_id){

        $demande_fond = DB::table('demande_fonds as df')
        ->where('df.id',$demande_fonds_id)
        ->join('credit_budgetaires as cb','cb.id','=','df.credit_budgetaires_id')
        ->join('familles as f','f.ref_fam','=','df.ref_fam')
        ->join('gestions as g','g.code_gestion','=','df.code_gestion')
        ->join('profils as p','p.id','=','df.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->join('sections as s','s.code_section','=','df.code_section')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->select('cb.*','g.code_gestion','g.libelle_gestion','st.*','s.*','a.*','u.*','p.*','f.*','df.*')
        ->first();

        return $demande_fond;
    }

    public function getCreditBudgetaireById2($credit_budgetaires_id){ 

        $credit_budgetaire = DB::table('credit_budgetaires as cb')
        ->join('familles as f','f.ref_fam','=','cb.ref_fam')
        ->where('cb.id',$credit_budgetaires_id)
        ->select('f.ref_fam','cb.exercice','cb.id as credit_budgetaires_id','cb.credit','cb.*')
        ->first();

        return $credit_budgetaire;
    }

    public function getDemandeAchatByexercice($exercice){
    
        return DB::table('demande_achats as dac')
        ->whereNotIn('dac.id',function($query) {
            $query->select(DB::raw('da.id'))
                    ->from('demande_fond_bon_commandes as dfbc')
                    ->join('demande_achats as da','da.id','=','dfbc.operations_id')
                    ->join('type_operations as to','to.id','=','dfbc.type_operations_id')
                    ->where('dfbc.flag_actif',1)
                    ->whereRaw('dfbc.operations_id = dac.id');
        })
        ->where('dac.exercice',$exercice)
        ->get();
                
    }

    public function storeDemandeFondBonCommande($demande_fonds_id,$operations_id,$type_operations_libelle,$flag_actif){

        $this->storeTypeOperation($type_operations_libelle);

        $type_operation = $this->getTypeOperation($type_operations_libelle);
        if ($type_operation!=null) {
            $type_operations_id = $type_operation->id;

            $data = [
                'demande_fonds_id'=>$demande_fonds_id,
                'type_operations_id'=>$type_operations_id,
                'operations_id'=>$operations_id,
                'flag_actif'=>$flag_actif,
            ];

            $demande_fond_bon_commande = $this->getDemandeFondBonCommande($demande_fonds_id,$operations_id,$type_operations_id);

            if($demande_fond_bon_commande != null){

                DemandeFondBonCommande::where('id',$demande_fond_bon_commande->id)
                ->update($data);

            }else{
                DemandeFondBonCommande::create($data);
            }

        }

    }

    public function getDemandeFondBonCommande($demande_fonds_id,$operations_id,$type_operations_id){

        return DemandeFondBonCommande::where('demande_fonds_id',$demande_fonds_id)
        ->where('operations_id',$operations_id)
        ->where('type_operations_id',$type_operations_id)
        ->first();

    }

    public function getDemandeFondBonCommandeById($demande_fonds_id){

        $liaison = null;

        $demande_fond_bon_commande = DB::table('demande_fond_bon_commandes as dfbc')
        ->join('type_operations as to','to.id','=','dfbc.type_operations_id')
        ->where('dfbc.demande_fonds_id',$demande_fonds_id)
        ->where('dfbc.flag_actif',1)
        ->select('to.libelle','to.id as type_operations_id')
        ->first();
        if($demande_fond_bon_commande != null){
            

            if($demande_fond_bon_commande->libelle === "Demande d'achats"){

                $liaison = DB::table('demande_fond_bon_commandes as dfbc')
                ->join('demande_achats as da','da.id','=','dfbc.operations_id')
                ->join('type_operations as to','to.id','=','dfbc.type_operations_id')
                ->where('dfbc.demande_fonds_id',$demande_fonds_id)
                ->where('dfbc.type_operations_id',$demande_fond_bon_commande->type_operations_id)
                ->where('dfbc.flag_actif',1)
                ->select('da.num_bc','to.libelle','dfbc.operations_id','dfbc.demande_fonds_id')
                ->first();

            }elseif($demande_fond_bon_commande->libelle === "Commande non stockable"){

                $liaison = DB::table('demande_fond_bon_commandes as dfbc')
                ->join('travauxes as t','t.id','=','dfbc.operations_id')
                ->join('type_operations as to','to.id','=','dfbc.type_operations_id')
                ->where('dfbc.demande_fonds_id',$demande_fonds_id)
                ->where('dfbc.type_operations_id',$demande_fond_bon_commande->type_operations_id)
                ->where('dfbc.flag_actif',1)
                ->select('t.num_bc','to.libelle','dfbc.operations_id','dfbc.demande_fonds_id')
                ->first();

            }

        }

        return $liaison;

    }

    public function storeTypeOperation($libelle){
        $type_operation = $this->getTypeOperation($libelle);
        if ($type_operation === null) {
        
            TypeOperation::create([
                'libelle'=>$libelle
            ]);

        }
    }

    public function getTypeOperation($libelle){
        return TypeOperation::where('libelle',$libelle)->first();
    }

    public function storeStatutBC($libelle,$profils_id,$num_bc,$commentaire){
        $controllerTravaux = new ControllerTravaux();
        if(isset($num_bc)){
            if(isset(explode('BCN',$num_bc)[1])){

                $travauxe = $controllerTravaux->getTravauxByNumBc($num_bc);
                if($travauxe != null){
                    $controllerTravaux->storeStatutTravaux($libelle,$travauxe->id,$profils_id,$commentaire);
                } 

            }elseif(isset(explode('BC',$num_bc)[1])){ 

                $demande_achat = $this->getDemandeAchatByNumBc($num_bc);
                if($demande_achat != null){
                    $this->storeTypeStatutDemandeAchat($libelle);
                    $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

                    if($type_statut_demande_achat != null){
                        $dataStatutDemandeAchat = [
                            'demande_achats_id'=>$demande_achat->id,
                            'type_statut_demande_achats_id'=>$type_statut_demande_achat->id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>trim($commentaire),
                            'profils_id'=>$profils_id,
                        ];

                        $this->storeStatutDemandeAchat($dataStatutDemandeAchat);
                    }

                    
                }

            }
        }

    }
    public function getDemandeFondBonCommandeByBc($operations_id,$type_operations_id){
        return DemandeFondBonCommande::where('operations_id',$operations_id)
        ->where('type_operations_id',$type_operations_id)
        ->where('flag_actif',1)
        ->first();
    }

    public function storeStatutDemandeFond($libelle,$demande_fonds_id,$profils_id,$commentaire){

        $type_statut_demande_fond = TypeStatutDemandeFond::where('libelle',$libelle)->first();

        if ($type_statut_demande_fond===null) {

        $type_statut_demande_fonds_id = TypeStatutDemandeFond::create([
                                        'libelle'=>$libelle
                                        ])->id;
        }else{

        $type_statut_demande_fonds_id = $type_statut_demande_fond->id;

        }

        if (isset($type_statut_demande_fonds_id)) {
            
            $statut_demande_fond = StatutDemandeFond::where('demande_fonds_id',$demande_fonds_id)
            ->orderByDesc('id')
            ->limit(1)
            ->first();

            if ($statut_demande_fond!=null) {
                StatutDemandeFond::where('id',$statut_demande_fond->id)->update([
                'date_fin'=>date('Y-m-d'),
                ]);
            }


            StatutDemandeFond::create([
                'demande_fonds_id'=>$demande_fonds_id,
                'type_statut_demande_fonds_id'=>$type_statut_demande_fonds_id,
                'date_debut'=>date('Y-m-d'),
                'date_fin'=>date('Y-m-d'),
                'profils_id'=>$profils_id,
                'commentaire'=>$commentaire,
            ]);

        }

    }

    public function getRouteBc($type_operations_libelle,$type_statut_libelle,$type_profils_name,$operations_id_crypt,$cotation_fournisseurs_id=null){
        $controllerTravaux = new ControllerTravaux();
        $route = null;
        if($type_operations_libelle === "Commande non stockable"){

            $route = $controllerTravaux->getRouteBcTravaux($type_statut_libelle,$type_profils_name,$operations_id_crypt);

        }elseif($type_operations_libelle === "Demande d'achats"){
            $route = $this->getRouteBcDemandeAchat($type_statut_libelle,$type_profils_name,$operations_id_crypt,$cotation_fournisseurs_id);
        }

        return $route;

    }

    public function getRouteBcDemandeAchat($type_statut_libelle,$type_profils_name,$operations_id_crypt,$cotation_fournisseurs_id=null){

        $route = "demande_achats/show/".$operations_id_crypt;

        if ($type_statut_libelle === "Soumis pour validation" or $type_statut_libelle === "Rejeté (Responsable des achats)" or $type_statut_libelle === "Annulé (Gestionnaire des achats)" or $type_statut_libelle === "Annulé (Responsable des achats)") {
    
        if ($type_profils_name === "Gestionnaire des achats") {
    
        $route = "demande_achats/send/".$operations_id_crypt;
    
        } 
    
        }elseif($type_statut_libelle === "Transmis (Responsable des achats)"){
    
        if ($type_profils_name === "Responsable des achats") {
    
        $route = "valider_demande_achats/create/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle === "Validé (Responsable des achats)" or $type_statut_libelle === "Partiellement validé (Responsable des achats)") {
    
        if ($type_profils_name === "Responsable des achats") {
    
        //$route = "valider_demande_achats/create/".$operations_id_crypt;
        $route = "demande_achats/cotation/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle === "Demande de cotation" or $type_statut_libelle === 'Demande de cotation (Annulé)') {
    
        if ($type_profils_name === "Responsable des achats") {
    
        //$route = "demande_achats/cotation/".$operations_id_crypt;
        $route = "demande_achats/send_cotation/".$operations_id_crypt;        
    
        }
    
        }elseif ($type_statut_libelle === 'Demande de cotation (Transmis Responsable DMP)') {
    
        if ($type_profils_name === "Responsable DMP") {
    
        $route = "demande_achats/cotation/".$operations_id_crypt;        
    
        }
    
        }elseif ($type_statut_libelle === 'Demande de cotation (Validé)') {
        if ($type_profils_name === "Responsable des achats") {
    
        $route = "demande_achats/cotation_send_frs/".$operations_id_crypt;
    
        }
        }
        elseif ($type_statut_libelle === "Transmis pour cotation") {
    
        if ($type_profils_name === "Responsable des achats") {
    
        //$route = "valider_demande_achats/create/".$operations_id_crypt;
        $route = "demande_achats/cotation_send_frs/".$operations_id_crypt;
    
    
        }elseif ($type_profils_name === "Fournisseur") {
    
        $route = "cotation_fournisseurs/create/".$operations_id_crypt;
    
        }
    
        }elseif( $type_statut_libelle === "Coté"){
    
        if ($type_profils_name === "Fournisseur") {
    
        $route = "cotation_fournisseurs/create/".$operations_id_crypt;
    
        }elseif ($type_profils_name === "Responsable des achats") {
    
        $route = "selection_adjudications/liste/".$operations_id_crypt;
    
        }
    
        }elseif ($type_statut_libelle === "Fournisseur sélectionné" or $type_statut_libelle==='Rejeté (Responsable DMP)') {
    
        if ($type_profils_name === "Responsable des achats") {
    
        if (isset($cotation_fournisseurs_id)) {
    
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
    
        }else{
    
        $route = "selection_adjudications/liste/".$operations_id_crypt;
    
        }
    
    
    
        }
    
        }elseif ($type_statut_libelle === "Transmis (Responsable DMP)" or $type_statut_libelle==='Rejeté (Responsable Contrôle Budgétaire)' or $type_statut_libelle==='Rejeté (Responsable DFC)') {
    
        if ($type_profils_name === "Responsable DMP") {
        if (isset($cotation_fournisseurs_id)) {
    
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
    
    
        }else{
        $route = "selection_adjudications/liste/".$operations_id_crypt;
        }
        }
    
    
        }elseif($type_statut_libelle==='Visé (Responsable DMP)'){
    
        if ($type_profils_name === "Responsable DMP") {
        if (isset($cotation_fournisseurs_id)) {
    
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
    
    
        }
        }
    
        }elseif($type_statut_libelle==='Transmis (Responsable Contrôle Budgétaire)' or $type_statut_libelle==='Rejeté (Chef Département DCG)'){
    
        if ($type_profils_name === "Responsable contrôle budgetaire") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Chef Département DCG)' or $type_statut_libelle==='Rejeté (Responsable DCG)') {
    
        if ($type_profils_name === "Chef Département DCG") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Responsable DCG)' or $type_statut_libelle==='Rejeté (Directeur Général Adjoint)') {
    
        if ($type_profils_name === "Responsable DCG") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Directeur Général Adjoint)' or $type_statut_libelle==='Rejeté (Directeur Général)') {
    
        if ($type_profils_name === "Directeur Général Adjoint") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Directeur Général)') {
    
        if ($type_profils_name === "Directeur Général") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Transmis (Responsable DFC)') {
    
        if ($type_profils_name === "Responsable DFC") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Validé' or $type_statut_libelle==='Annulé (Fournisseur)') {
    
        if ($type_profils_name === "Responsable des achats" or $type_profils_name === "Gestionnaire des achats") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
    
        }elseif ($type_statut_libelle==='Édité') {
    
        if ($type_profils_name === "Fournisseur") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
        }
    
        }
        }elseif ($type_statut_libelle==='Retiré (Frs.)') {
    
        if ($type_profils_name === "Fournisseur") {
    
        
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "livraison_commandes/create/".$cotation_fournisseurs_id;
        }
    
        }
        }elseif ($type_statut_libelle==='Livraison partielle') {
    
        if ($type_profils_name === "Fournisseur") {
    
        
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "livraison_commandes/create/".$cotation_fournisseurs_id;
        }
    
    
    
        }elseif($type_profils_name === "Comite Réception"){
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "livraison_commandes/create/".$cotation_fournisseurs_id;
        }
    
    
        }
        }elseif ($type_statut_libelle==='Livraison totale') {
    
        if($type_profils_name === "Comite Réception"){
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "livraison_commandes/create/".$cotation_fournisseurs_id;
        }
    
    
        }
        }elseif ($type_statut_libelle==='Livraison annulée (Comite Réception)') {
    
        if ($type_profils_name === "Fournisseur") {
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "livraison_commandes/create/".$cotation_fournisseurs_id;
        }
    
        }
        }elseif ($type_statut_libelle==='Livraison partielle confirmée') {
    
        
    
        if ($type_profils_name === "Fournisseur") {
    
        
    
        if (isset($cotation_fournisseurs_id)) {
        $route = "livraison_commandes/create/".$cotation_fournisseurs_id;
        }
    
        }
        }else{
        $route = "demande_achats/show/".$operations_id_crypt;
        }
    
        return $route;
    }

    public function getLastStatutDemandeFond($demande_fonds_id){
            
        $statut_demande_fond = DB::table('statut_demande_fonds as sdf')
        ->join('type_statut_demande_fonds as tsdf','tsdf.id','=','sdf.type_statut_demande_fonds_id')
        ->join('profils as p','p.id','=','sdf.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('sdf.demande_fonds_id',$demande_fonds_id)
        ->orderByDesc('sdf.id')
        ->limit(1)
        ->first();

        return $statut_demande_fond;
    }
    public function storeSignataireDemandeFond($request,$demande_fonds_id,$profils_id){

        
        $donnees = [];
        if (isset($request->profil_fonctions_id)) {
            if (count($request->profil_fonctions_id) > 0) {
    
                foreach ($request->profil_fonctions_id as $item => $value) {
                    $donnees[$item] =  $request->profil_fonctions_id[$item];
                }
    
            }
        }
    
        
        $signataire_demande_fond = DB::table('signataire_demande_fonds as sdf')
        ->where('sdf.flag_actif',1)
        ->where('sdf.demande_fonds_id',$demande_fonds_id)
        ->first();

        if ($signataire_demande_fond!=null) {
    
            $liste_signataire_demande_fonds = DB::table('signataire_demande_fonds as sdf')
            ->where('sdf.flag_actif',1)
            ->where('sdf.demande_fonds_id',$demande_fonds_id)
            ->whereNotIn('sdf.profil_fonctions_id',$donnees)
            ->get();

            foreach ($liste_signataire_demande_fonds as $liste_signataire_demande_fond) {

                $libelle = 'Désactivé';
                $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);

                SignataireDemandeFond::where('id',$liste_signataire_demande_fond->id)
                ->update([
                    'profil_fonctions_id'=>$liste_signataire_demande_fond->profil_fonctions_id,
                    'demande_fonds_id'=>$demande_fonds_id,
                    'flag_actif'=>0,
                ]);

                StatutSignataireDemandeFond::create([
                    'signataire_fonds_id'=>$liste_signataire_demande_fond->id,
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
    
                        $signataire = SignataireDemandeFond::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('demande_fonds_id',$demande_fonds_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {
    
                            $signataire_fonds_id = SignataireDemandeFond::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'demande_fonds_id'=>$demande_fonds_id,
                            ])->id;
    
                            StatutSignataireDemandeFond::create([
                                'signataire_fonds_id'=>$signataire_fonds_id,
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
    
                        $signataire = SignataireDemandeFond::where('profil_fonctions_id',$request->profil_fonctions_id[$item])
                        ->where('demande_fonds_id',$demande_fonds_id)
                        ->where('flag_actif',1)
                        ->first();
                        if ($signataire===null) {
    
                            $signataire_fonds_id = SignataireDemandeFond::create([
                                'profil_fonctions_id'=>$request->profil_fonctions_id[$item],
                                'demande_fonds_id'=>$demande_fonds_id,
                            ])->id;
    
                            StatutSignataireDemandeFond::create([
                                'signataire_fonds_id'=>$signataire_fonds_id,
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
    public function storeSignataireDemandeFond2($profil_fonctions_id,$demande_fonds_id,$profils_id){

        $libelle = 'Activé';
        $type_statut_sign_id = $this->getTypeStatutSignataire($libelle);

        if (isset($profil_fonctions_id)) {

            $signataire = SignataireDemandeFond::where('profil_fonctions_id',$profil_fonctions_id)
            ->where('demande_fonds_id',$demande_fonds_id)
            ->where('flag_actif',1)
            ->first();
            if ($signataire === null) {

                $signataire_fonds_id = SignataireDemandeFond::create([
                    'profil_fonctions_id'=>$profil_fonctions_id,
                    'demande_fonds_id'=>$demande_fonds_id,
                ])->id;

                StatutSignataireDemandeFond::create([
                    'signataire_fonds_id'=>$signataire_fonds_id,
                    'type_statut_sign_id'=>$type_statut_sign_id,
                    'profils_id'=>$profils_id,
                    'date_debut'=>date('Y-m-d'),
                ]);
            }
            
        }

    }

    public function getSignataireDemandeFond($demande_fonds_id){
        $signataire_demande_fonds = DB::table('signataire_demande_fonds as sdf')
        ->join('profil_fonctions as pf','pf.id','=','sdf.profil_fonctions_id')
        ->join('agents as a','a.id','=','pf.agents_id')
        ->where('sdf.flag_actif',1)
        ->where('sdf.demande_fonds_id',$demande_fonds_id)
        ->get();
        return $signataire_demande_fonds;
    }

    public function getConsomations($ref_fam,$code_structure,$periode_debut,$periode_fin,$ref_articles=null){
        if($ref_articles != null){
            return DB::select("SELECT ms.ref_articles, a.design_article, SUM(l.qte) qte, AVG(l.prixu) cmup, SUM(l.montant) montant, r.code_structure, s.nom_structure, r.departements_id, (SELECT dp.nom_departement FROM departements dp WHERE dp.id = r.departements_id) nom_departement FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s WHERE l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$code_structure."' AND a.ref_fam = '".$ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' AND ms.ref_articles = '".$ref_articles."' GROUP BY r.departements_id,r.code_structure,s.nom_structure,ms.ref_articles,a.design_article ORDER BY r.departements_id, a.design_article ASC");
        }else{
            return DB::select("SELECT ms.ref_articles, a.design_article, SUM(l.qte) qte, AVG(l.prixu) cmup, SUM(l.montant) montant, r.code_structure, s.nom_structure, r.departements_id, (SELECT dp.nom_departement FROM departements dp WHERE dp.id = r.departements_id) nom_departement FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s WHERE l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$code_structure."' AND a.ref_fam = '".$ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' GROUP BY r.departements_id,r.code_structure,s.nom_structure,ms.ref_articles,a.design_article ORDER BY r.departements_id, a.design_article ASC");
        }
        
    }

    public function getAllArticles(){
        return Article::get();
    }

    public function getFamilles(){
        return Famille::get();
    }

    public function getFamilleByRefFam($ref_fam){
        return DB::table('familles')->where('ref_fam',$ref_fam)->first();
    }

    public function getArticleByRef($ref_articles){
        return DB::table('articles')->where('ref_articles',$ref_articles)->first();
    }

    public function deliver_response($status,$status_message,$data=null){
		header("HTTP/1.1 $status $status_message");

		$response['status'] = $status;
		$response['status_message'] = $status_message;
		$response['resultat'] = $data;

		$json_response = json_encode($response);

		return $json_response;

	}

    public function getComptabilisationEcritures($code_structure,$date_transaction){
        $comptabilisation_ecritures = DB::table('comptabilisation_ecritures as ce')
        ->whereIn('ce.ref_depot',function($query) use($code_structure){
            $query->select(DB::raw('s.ref_depot'))
                ->from('structures as s')
                ->where('s.code_structure',$code_structure)
                ->whereRaw('ce.ref_depot = s.ref_depot');
        })
        ->where(DB::raw("(DATE_FORMAT(ce.date_transaction,'%Y-%m-%d'))"),$date_transaction)
        ->get();
        $reponse = [];

        foreach($comptabilisation_ecritures as $comptabilisation_ecriture){

            $reponse[] = array('type_piece' => $comptabilisation_ecriture->type_piece,'reference_piece' => $comptabilisation_ecriture->reference_piece, 'compte' => $comptabilisation_ecriture->compte, 'montant' => (int)round($comptabilisation_ecriture->montant), 'date_transaction' => date("d/m/Y",strtotime($comptabilisation_ecriture->date_transaction)), 'mle' => $comptabilisation_ecriture->mle, 'code_structure' => $comptabilisation_ecriture->code_structure, 'code_section' => $comptabilisation_ecriture->code_section, 'acompte' => $comptabilisation_ecriture->acompte, 'ref_depot' => $comptabilisation_ecriture->ref_depot);
               
        }

        return $reponse;
    }

    public function storeTypeStatut($libelle){
        $type_statut = TypeStatut::where('libelle',$libelle)->first();
        if($type_statut === null){
            TypeStatut::create([
                'libelle'=>$libelle
            ]);
        }
    }

    public function getTypeStatut($libelle){
        return TypeStatut::where('libelle',$libelle)->first();
    }
    public function storeComptabilisationEcriture($data){
        $store_compt = null;
        
        if($data['type_piece'] === "ENT_STOCK" or $data['type_piece'] === "DE_STOCK"  or $data['type_piece'] === "ENT_STOCK_RECU" or $data['type_piece'] === "DE_STOCK_CONSO"){
    
            $compte_int = (int) $data['compte'];
    
            if($compte_int >= 600000 &&  $compte_int < 700000){
                $store_compt = 1;
            }
        }else{
            $store_compt = 1;
        }
        
        
        if($store_compt === 1){
            if($data['acompte'] === null){
                ComptabilisationEcriture::create([
                    'type_piece'=>$data['type_piece'],
                    'reference_piece'=>$data['reference_piece'],
                    'compte'=>$data['compte'],
                    'montant'=>$data['montant'],
                    'date_transaction'=>$data['date_transaction'],
                    'mle'=>$data['mle'],
                    'code_structure'=>$data['code_structure'],
                    'exercice'=>$data['exercice'],
                    'code_gestion'=>$data['code_gestion'],
                    'code_section'=>$data['code_section'],
                    'ref_depot'=>$data['ref_depot'],
                    'acompte'=>0
                ]);
            }else{
                ComptabilisationEcriture::create([
                    'type_piece'=>$data['type_piece'],
                    'reference_piece'=>$data['reference_piece'],
                    'compte'=>$data['compte'],
                    'montant'=>$data['montant'],
                    'date_transaction'=>$data['date_transaction'],
                    'mle'=>$data['mle'],
                    'code_structure'=>$data['code_structure'],
                    'exercice'=>$data['exercice'],
                    'code_gestion'=>$data['code_gestion'],
                    'ref_depot'=>$data['ref_depot'],
                    'code_section'=>$data['code_section'],
                    'acompte'=>$data['acompte'],
                ]);
            }
        }
    }
    public function getLivraisonCommandes($cotation_fournisseurs_id){
        return DB::table('livraison_commandes as lc')
        ->join('cotation_fournisseurs as cf','cf.id','=','lc.cotation_fournisseurs_id')
        ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
        ->join('organisations as o','o.id','=','cf.organisations_id')
        ->where('lc.cotation_fournisseurs_id',$cotation_fournisseurs_id)
        ->select('o.denomination','o.contacts','da.intitule','da.num_bc','lc.*')
        ->get();
    }

    public function getDetailLivraisonDisponible($ref_magasin,$ref_articles){
        return DB::table('magasins as m')
            ->join('depots as de','de.id','=','m.depots_id')
            ->join('demande_achats as da','da.ref_depot','=','de.ref_depot')
            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
            ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')
            ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','lc.cotation_fournisseurs_id')
            ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')
            ->where('m.ref_magasin',$ref_magasin)
            ->where('dc.ref_articles',$ref_articles)
            ->whereRaw('dl.detail_cotations_id = dc.id')
            ->whereRaw('dc.cotation_fournisseurs_id = cf.id')
            ->whereRaw('dl.qte > 0')
            ->whereRaw('dl.qte > dl.qte_consommee')
            ->orderBy('dl.id')
            ->select('da.num_bc','lc.num_bl','da.id as demande_achats_id','dl.qte','dl.prix_unit','cf.taux_de_change','dl.remise','dl.qte_consommee','dc.ref_articles','cf.id as cotation_fournisseurs_id','m.ref_magasin','dl.id as detail_livraisons_id')
            ->get();
    }

    function setDetailLivraison($detail_livraisons_id,$qte_consommee){
        DetailLivraison::where('id',$detail_livraisons_id)->update([
            'qte_consommee'=>$qte_consommee
        ]);
    }

    function setConsommationAchat($consommation_achats_id,$qte_distribuee){
        ConsommationAchat::where('id',$consommation_achats_id)->update([
            'qte_distribuee'=>$qte_distribuee
        ]);
    }

    function storeConsommationAchat($detail_livraisons_id,$livraisons_id,$qte){
        ConsommationAchat::create([
            'livraisons_id'=>$livraisons_id,
            'detail_livraisons_id'=>$detail_livraisons_id,
            'qte'=>$qte,
        ]);
    }


    public function getLivraisonByd($livraisons_id){
        return DB::table('livraisons as l')
                ->join('demandes as d','d.id','=','l.demandes_id')
                ->join('requisitions as r','r.id','=','d.requisitions_id')
                ->where('l.id',$livraisons_id)
                ->select('r.code_structure')
                ->first();
    }

    public function getConsommationAchats($livraisons_id){

        return DB::table('consommation_achats as ca')
                ->join('livraisons as l','l.id','=','ca.livraisons_id')
                ->join('detail_livraisons as dl','dl.id','=','ca.detail_livraisons_id')
                ->join('livraison_commandes as lc','lc.id','=','dl.livraison_commandes_id')
                ->join('cotation_fournisseurs as cf','cf.id','=','lc.cotation_fournisseurs_id')
                ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
                ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
                ->where('ca.livraisons_id',$livraisons_id)
                ->select('cb.exercice','cb.code_gestion','da.num_bc','ca.livraisons_id','da.ref_fam','ca.qte','dl.prix_unit','dl.remise','cf.tva','cf.taux_de_change','l.qte_recue')
                ->get();
    }

    public function getConsommationDistributionsDisponibles($livraisons_id){
        return DB::table('consommation_achats as ca')
                ->join('livraisons as l','l.id','=','ca.livraisons_id')
                ->join('detail_livraisons as dl','dl.id','=','ca.detail_livraisons_id')
                ->join('livraison_commandes as lc','lc.id','=','dl.livraison_commandes_id')
                ->join('cotation_fournisseurs as cf','cf.id','=','lc.cotation_fournisseurs_id')
                ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
                ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
                ->where('ca.livraisons_id',$livraisons_id)
                ->whereRaw('ca.qte > 0')
                ->whereRaw('ca.qte > ca.qte_distribuee')
                ->select('cb.exercice','cb.code_gestion','da.num_bc','ca.livraisons_id','da.ref_fam','ca.qte','ca.qte_distribuee','dl.prix_unit','dl.remise','cf.tva','cf.taux_de_change','l.qte_recue','ca.id as consommation_achats_id')
                ->get();
    }

    public function getDemande($demandes_id){
        return Demande::where('id',$demandes_id)->first();
    }

    public function comptabilisationDistribution($livraisons_id,$type_piece){
        $user = $this->getInfoUserByProfilId(Session::get('profils_id'));

        if($user != null){
            $consommation_achats = $this->getConsommationAchats($livraisons_id);

            foreach($consommation_achats as $consommation_achat){

                $livraison_info = $this->getLivraisonByd($consommation_achat->livraisons_id);

                if($livraison_info != null){

                    $reference_piece = $consommation_achat->num_bc;
                    $compte = $consommation_achat->ref_fam;
                    $taux_de_change = $consommation_achat->taux_de_change;
                    $prix_unit = $consommation_achat->prix_unit;
                    $qte = $consommation_achat->qte;
                    $remise = $consommation_achat->remise;
                    $tva = $consommation_achat->tva;
                    $exercice = $consommation_achat->exercice;
                    $code_gestion = $consommation_achat->code_gestion;

                    $montant_remise = 0;

                    if(isset($remise)){
                        $montant_remise = ((($qte * $prix_unit * $taux_de_change) * $remise) / 100);
                    }
                    
                    $montant_ht = ($qte * $prix_unit * $taux_de_change) - $montant_remise;

                    
                    $montant_tva = 0;
                    if(isset($tva)){
                        $montant_tva = (($montant_ht * $tva) / 100);
                    }
                    
                    $montant_ttc = $montant_ht + $montant_tva;

                    $montant_comptabilisation = $montant_ttc;
                    $date_transaction = date('Y-m-d H:i:s');
                    $mle = $user->mle;
                    $code_structure = $livraison_info->code_structure;
                    $code_section = $livraison_info->code_structure."01";
                    $acompte = 0;

                    $data = [
                        'type_piece'=>$type_piece,
                        'reference_piece'=>$reference_piece,
                        'compte'=>$compte,
                        'montant'=>$montant_comptabilisation,
                        'date_transaction'=>$date_transaction,
                        'mle'=>$mle,
                        'code_structure'=>$code_structure,
                        'code_section'=>$code_section,
                        'ref_depot'=>$user->ref_depot,
                        'acompte'=>$acompte,
                        'exercice'=>$exercice,
                        'code_gestion'=>$code_gestion,
                    ];

                    $this->storeComptabilisationEcriture($data);

                }

            }            
        }
    }

    public function comptabilisationDistributionConsommation($consommations_id,$type_piece){
        $user = $this->getInfoUserByProfilId(Session::get('profils_id'));

        if($user != null){
            $consommation_distributions = $this->getConsommationDistributions($consommations_id);

            foreach($consommation_distributions as $consommation_distribution){

                $livraison_info = $this->getLivraisonByd($consommation_distribution->livraisons_id);

                if($livraison_info != null){

                    //$type_piece = "ENT_STOCK_RECU";
                    $reference_piece = $consommation_distribution->num_bc;
                    $compte = $consommation_distribution->ref_fam;
                    $taux_de_change = $consommation_distribution->taux_de_change;
                    $prix_unit = $consommation_distribution->prix_unit;
                    
                    $qte = $consommation_distribution->qte;
                    
                    
                    $remise = $consommation_distribution->remise;
                    $tva = $consommation_distribution->tva;
                    $exercice = $consommation_distribution->exercice;
                    $code_gestion = $consommation_distribution->code_gestion;

                    $montant_remise = 0;

                    if(isset($remise)){
                        $montant_remise = ((($qte * $prix_unit * $taux_de_change) * $remise) / 100);
                    }
                    
                    $montant_ht = ($qte * $prix_unit * $taux_de_change) - $montant_remise;

                    
                    $montant_tva = 0;
                    if(isset($tva)){
                        $montant_tva = (($montant_ht * $tva) / 100);
                    }
                    
                    $montant_ttc = $montant_ht + $montant_tva;

                    $montant_comptabilisation = (int)$montant_ttc;
                    $date_transaction = date('Y-m-d H:i:s');
                    $mle = $user->mle;
                    $code_structure = $livraison_info->code_structure;
                    $code_section = $livraison_info->code_structure."01";
                    $acompte = 0;

                    $data = [
                        'type_piece'=>$type_piece,
                        'reference_piece'=>$reference_piece,
                        'compte'=>$compte,
                        'montant'=>$montant_comptabilisation,
                        'date_transaction'=>$date_transaction,
                        'mle'=>$mle,
                        'code_structure'=>$code_structure,
                        'code_section'=>$code_section,
                        'ref_depot'=>$user->ref_depot,
                        'acompte'=>$acompte,
                        'exercice'=>$exercice,
                        'code_gestion'=>$code_gestion,
                    ];

                    $this->storeComptabilisationEcriture($data);

                }

            }            
        }
    }

    public function storeConsommationDistribution($consommations_id,$livraisons_id,$qte){
        $consommation_achats = $this->getConsommationDistributionsDisponibles($livraisons_id);
        //dd($consommations_id,$livraisons_id,$qte,$consommation_achats);
        $u = 1;
        
        foreach($consommation_achats as $consommation_achat){

            $qte_achatee = $consommation_achat->qte;
            $qte_distribuee = $consommation_achat->qte_distribuee;

            $qte_en_stock = $qte_achatee - $qte_distribuee;
            $qte_impute = null;
            $break = null;
            //$ref_articles[$u] = $consommation_achat->ref_articles;
            
            $qte_reste_a_impute[$u] = null;

            /*if(isset($qte_reste_a_impute[$u-1])){
                if($ref_articles[$u-1] != $ref_articles[$u]){
                    $qte_reste_a_impute[$u-1] = null;
                }
            }*/

            if(isset($qte_reste_a_impute[$u-1])){

                if($qte_en_stock >= $qte_reste_a_impute[$u-1]){

                    $qte_distribuee = $qte_distribuee + $qte_reste_a_impute[$u-1];
                    $qte_impute = $qte_reste_a_impute[$u-1];

                    $break = 1;

                }else{

                    $qte_distribuee = $qte_distribuee + $qte_en_stock;

                    $qte_impute = $qte_en_stock;

                    $qte_reste_a_impute[$u] = $qte_reste_a_impute[$u-1] - $qte_impute;

                }

            }else{

                if($qte_en_stock >= $qte){

                    $qte_distribuee = $qte_distribuee + $qte;
                    $qte_impute = $qte;

                    $break = 1;

                }else{

                    $qte_distribuee = $qte_distribuee + $qte_en_stock;
                    $qte_impute = $qte_en_stock;

                    $qte_reste_a_impute[$u] = $qte - $qte_impute;

                }

            }
            

            if(isset($consommation_achat->consommation_achats_id) && isset($consommations_id) && isset($qte_impute)){

                ConsommationDistribution::create([
                    'consommation_achats_id'=>$consommation_achat->consommation_achats_id,
                    'consommations_id'=>$consommations_id,
                    'qte'=>$qte_impute,
                ]);

            }
            
            if(isset($consommation_achat->consommation_achats_id) && isset($qte_distribuee)){
                $this->setConsommationAchat($consommation_achat->consommation_achats_id,$qte_distribuee);
            }                                


            $u++;
            if($break === 1){
                break;
            }
        }
    }

    public function getConsommationDistributions($consommations_id){
        return DB::table('consommation_distributions as cd')
                ->join('consommations as c','c.id','=','cd.consommations_id')
                ->join('consommation_achats as ca','ca.id','=','cd.consommation_achats_id')
                ->join('livraisons as l','l.id','=','ca.livraisons_id')
                ->join('detail_livraisons as dl','dl.id','=','ca.detail_livraisons_id')
                ->join('livraison_commandes as lc','lc.id','=','dl.livraison_commandes_id')
                ->join('cotation_fournisseurs as cf','cf.id','=','lc.cotation_fournisseurs_id')
                ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
                ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
                ->select('cb.exercice','cb.code_gestion','da.num_bc','ca.livraisons_id','da.ref_fam','cd.qte','dl.prix_unit','dl.remise','cf.tva','cf.taux_de_change','cd.id as consommation_distributions_id','cd.id as consommation_distributions_id','c.id as consommations_id')
                ->where('cd.id',$consommations_id)
                ->get();
    }

    public function AutorisationAcces($type_profils_name_connecte,$type_profils_name_autorises){

        $autorisation_acces = "Accès refusé";

        if(isset(explode($type_profils_name_connecte,$type_profils_name_autorises)[1])){
            $autorisation_acces = null;
        }
        return $autorisation_acces;
    }

    function correctionInventaireArticle($magasin_stocks_id){
        $libelle = "Intégration d'inventaire";
        $mouvements = DB::table('mouvements as m')
        ->join('type_mouvements as tm','tm.id','=','m.type_mouvements_id')
        ->where('m.magasin_stocks_id',$magasin_stocks_id)
        ->where('tm.libelle',$libelle)
        ->select('m.id as mouvements_id','m.*')
        ->whereNotIn('m.id',function($query) {
            $query->select(DB::raw('ia.mouvements_id'))
                    ->from('inventaire_articles as ia')
                    ->whereRaw('m.id = ia.mouvements_id');
        })
        ->get();
        foreach($mouvements as $mouvement){
            Mouvement::where('id',$mouvement->mouvements_id)->delete();
            $this->procedureSetMagasinStock($mouvement->magasin_stocks_id);
        }
        
    }

    function procedureSetMagasinStock($magasin_stocks_id){

        $montant_ttc_stock_total = (int) $this->getMontantMagasinStock($magasin_stocks_id);

        $qte_stock_total = 0;

        $mouvement = $this->getQteMagasinStock($magasin_stocks_id);

        if($mouvement != null){
            $qte_stock_total = (int) $mouvement->qte_stock;
        }
        $cmup = 0;
        if($qte_stock_total > 0){
            $cmup = $montant_ttc_stock_total / $qte_stock_total;
        }
        
        if($cmup >= 0){
            $this->setMagasinStock($magasin_stocks_id,$cmup,$qte_stock_total,$montant_ttc_stock_total);
        }

    }

    function getOperationEncoursNonComptabilisee($data){
        $data_return = null;
        $comptabilisation_ecriture =  DB::table('comptabilisation_ecritures')
            ->where('type_piece',$data['type_piece'])
            ->where('compte',$data['compte'])
            ->where('exercice',$data['exercice'])
            ->where('code_structure',$data['code_structure'])
            ->where('code_gestion',$data['code_gestion'])
            ->where('flag_comptabilisation',0)
            ->select('type_piece','compte','exercice','code_structure','code_gestion','flag_comptabilisation',DB::raw('SUM(montant) as consommation_non_interfacee'))
            ->groupBy('type_piece','compte','exercice','code_structure','code_gestion','flag_comptabilisation')
            ->first();
        if($comptabilisation_ecriture != null){
            $data_return = [
                'type_piece'=>$comptabilisation_ecriture->type_piece,
                'ref_fam'=>$comptabilisation_ecriture->compte,
                'exercice'=>$comptabilisation_ecriture->exercice,
                'code_structure'=>$comptabilisation_ecriture->code_structure,
                'code_gestion'=>$comptabilisation_ecriture->code_gestion,
                'flag_comptabilisation'=>$comptabilisation_ecriture->flag_comptabilisation,
                'consommation_non_interfacee'=>$comptabilisation_ecriture->consommation_non_interfacee,
            ];
        }
        return $data_return;
    }
    function setCreditBudgetaireConsoNonComptabiliseByArray($data){
        if($data != []){
            CreditBudgetaire::where('ref_fam',$data['ref_fam'])
            ->where('exercice',$data['exercice'])
            ->where('code_structure',$data['code_structure'])
            ->where('code_gestion',$data['code_gestion'])
            ->update([ 
                'consommation_non_interfacee'=>$data['consommation_non_interfacee']
                ]
            );
        }
    }

    public function getTravauxByexercice($exercice){
        return DB::table('travauxes as tr')
        ->whereNotIn('tr.id',function($query) {
            $query->select(DB::raw('t.id'))
                    ->from('demande_fond_bon_commandes as dfbc')
                    ->join('travauxes as t','t.id','=','dfbc.operations_id')
                    ->join('type_operations as to','to.id','=','dfbc.type_operations_id')
                    ->where('dfbc.flag_actif',1)
                    ->whereRaw('dfbc.operations_id = tr.id');
        })
        ->where('tr.exercice',$exercice)
        ->get();
        
    }
}
