<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profil;
use App\Models\TypeProfil;
use App\Models\StatutProfil;
use Illuminate\Http\Request;
use App\Models\ProfilFonction;
use App\Models\TypeStatutProfil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request) 
    {

        if (Session::has('profils_id')) {
            
        }else{
            $this->storeSessionData($request,auth()->user()->id);
        } 

        

        $user = User::where('users.id',auth()->user()->id)->where('annuaire',1)->first();
        if ($user != null) {
            User::where('users.id',auth()->user()->id)
            ->join('agent_sections as a','a.agents_id','=','users.agents_id')
            ->update([
                'password'=>null,
            ]);
        }

        

        $agents_id = auth()->user()->agents_id;

        $first_conn = DB::table('users')
                          ->where('reset',1)
                          ->where('agents_id',$agents_id)->first();

        if ($first_conn != null) {
            return redirect('/password/reset');
        }


        $agents = DB::table('agents')
                     ->join('users','users.agents_id', '=', 'agents.id')
                     ->where('agents.id',$agents_id)
                     ->get();
        
        $agent_fournisseur = DB::table('agents')
                            ->join('users','users.agents_id', '=', 'agents.id')
                            ->join('profils','profils.users_id', '=', 'users.id')
                            ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                            ->join('statut_organisations as so','so.profils_id', '=', 'profils.id')
                            ->join('organisations as o','o.id', '=', 'so.organisations_id')
                            ->where('agents.id',$agents_id)
                            ->where('type_profils.name','Fournisseur')
                            ->first();
        
        $agent_profils = DB::table('agents')
                            ->join('users','users.agents_id', '=', 'agents.id')
                            ->join('profils','profils.users_id', '=', 'users.id')
                            ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                            ->where('agents.id',$agents_id)
                            ->get();
        //ensemble des réquisition d'agent
        $requisitions = DB::table('requisitions')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->where('agents.id',$agents_id)
                            ->get();
        
                            
        //ensemble des demandes d'agent
        $demandes = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                            ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                            ->join('profils','profils.id','=','demandes.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->join('unites','unites.code_unite', '=', 'articles.code_unite')
                            ->where('agents.id',$agents_id)
                            ->select('unites.unite','requisitions.*','demandes.*','demandes.id as demandes_id','profils.*','users.*','agents.*','magasin_stocks.*','articles.*')
                            ->paginate(4);
        
        
        
        $valider_requisitions = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->where('agents.id',$agents_id)
                            ->get();
                            
        
        

        $valider_requisitions_true = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->where('agents.id',$agents_id)
                            ->where('valider_requisitions.flag_valide',1)
                            ->get();

        $valider_requisitions_livree = DB::table('requisitions')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
                            ->join('livraisons','livraisons.demandes_id','=','demandes.id')
                            ->join('profils','profils.id','=','requisitions.profils_id')
                            ->join('users','users.id', '=', 'profils.users_id')
                            ->join('agents','agents.id', '=', 'users.agents_id')
                            ->where('agents.id',$agents_id)
                            ->where('livraisons.statut',1)
                            ->get();
                    
        $section = DB::table('sections')
                        ->join('agent_sections','agent_sections.sections_id', '=', 'sections.id')
                        ->join('structures','structures.code_structure', '=', 'sections.code_structure')
                        ->where('agent_sections.agents_id',$agents_id)
                        ->where('exercice',date("Y"))
                        ->first();

        try {
            $profils_id = $this->getSessionData($request);
        } catch (\Throwable $th) {
            $th->getMessage(); 
        }        
        
        return view('home',[
            'agents' => $agents,
            'agent_profils' => $agent_profils,
            'requisitions' => $requisitions,
            'demandes' => $demandes,
            'valider_requisitions' => $valider_requisitions,
            'valider_requisitions_true' => $valider_requisitions_true,
            'valider_requisitions_livree' => $valider_requisitions_livree,
            'section' => $section,
            'agent_fournisseur' => $agent_fournisseur
        ]);
    }

    public function getSessionData(Request $request){

        if ($request->session()->has('profils_id')) {
            $profils_id = $request->session()->get('profils_id');
        }else{
            $profils_id = null;
        }
        
        return $profils_id;
    }

    public function setProfil($type_profils_name,$users_id,$flag_actif){
        
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

    public function setStatutProfil($profils_id,$type_statut_profils_libelle,$date_debut,$profils_ids){
        
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

    public function setProfilHierarchie($attribut,$agents_id,$users_id,$type_profil_name,$type_statut_profils_libelle,$date_debut,$flag_actif){
            
        $hierarchie_n1 = DB::table('hierarchies as h')
        ->where($attribut,$agents_id)
        ->where('flag_actif',1)
        ->first();
        if ($hierarchie_n1!=null) {

            $profils_id = $this->setProfil($type_profil_name,$users_id,$flag_actif);
            
            $profils_ids = $profils_id;

            if ($profils_id!=null) {
                $this->setStatutProfil($profils_id,$type_statut_profils_libelle,$date_debut,$profils_ids);
            }

        }
        
    }

    public function storeSessionData(Request $request,$users_id){

        $type_profil = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->join('users as u','u.id','=','p.users_id')
        ->where('u.id',$users_id)
        ->where('p.flag_actif',1)
        ->where('tp.name','Agent Cnps')
        ->select('p.id as profils_id','u.agents_id')
        ->first();
        if ($type_profil!=null) {

            $profils_id = $type_profil->profils_id;
            $agents_id = $type_profil->agents_id;

            

        }else{
            $type_profil_autre = DB::table('type_profils as tp')
            ->join('profils as p','p.type_profils_id','=','tp.id')
            ->join('users as u','u.id','=','p.users_id')
            ->where('u.id',$users_id)
            ->where('p.flag_actif',1)
            ->select('p.id as profils_id','u.agents_id')
            ->first();
            if ($type_profil_autre!=null) {
                $profils_id = $type_profil_autre->profils_id;
                $agents_id = $type_profil_autre->agents_id;
            }
        }

        $this->setSessionFonction($agents_id,$request);


        $request->session()->put('profils_id',$profils_id);
    } 

    public function setSessionFonction($agents_id,$request){
        
        if (isset($agents_id)) {
            $profil_fonction = ProfilFonction::where('agents_id',$agents_id)
            ->where('flag_actif',1)
            ->first();
            if ($profil_fonction!=null) {
                $fonctions_id = $profil_fonction->fonctions_id;

                
                $request->session()->put('fonctions_id',$fonctions_id);
            }
        }
    }
}
