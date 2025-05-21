<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Profil;
use App\Models\Structure;
use App\Models\TypeProfil;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class AgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique'];

        $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        
        if($profil===null){
            return redirect()->back();
        }

        $agents = $this->getAllAgent();
        

        return view('agents.index',[
            'agents'=>$agents
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back();
        }

        $griser = 1;

        $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique'];

        $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        if($profil===null){
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }

        //sections
        $sections = $this->getAllSection();

        //Structure
        $structures = Structure::all();

        //Type de profils
        $type_profils = TypeProfil::all();


        return view('agents.create',[
            'sections' => $sections,
            'structures' => $structures,
            'type_profils' => $type_profils,
            'griser'=>$griser
        ]);
    }


    public function creates($structure)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($structure);
        } catch (DecryptException $e) {
            //
        }

        $structure = Structure::findOrFail($decrypted);
        
        $griser = null;
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back();
        }

        $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique'];

        $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        if($profil===null){
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }

        //sections
        $sections = $this->getSectionById($structure->code_structure);

        //Structure
        $structures = Structure::all();

        //Type de profils
        $type_profils = TypeProfil::all();


        return view('agents.create',[
            'sections' => $sections,
            'structures' => $structures,
            'type_profils' => $type_profils,
            'griser'=>$griser,
            'select_structure'=>$structure
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back();
        }

        $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique'];

        $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        if($profil!=null){
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }

        
        
        $request->validate([
            'mle'=>['required','numeric','unique:agents'],
            'nom_prenoms'=>['required','string','max:255'],
            'email'=>['required','email','unique:users'],
            'nom_section'=>['required','array'],
            'code_structure'=>['required','numeric'],
            'structure'=>['required','string','max:255'],
            'type_profil'=>['required','array']
        ]);


        if (isset($request->nom_section)) {
            if (count($request->nom_section) > 0) {
                foreach ($request->nom_section as $key => $value) {

                    $section = $this->getSectionByIdByName($request->nom_section[$key],$request->code_structure);
                    
        
                    if ($section===null) {
                        return redirect()->back()->with('error','Section invalide');
                    }
                }
                
            }
        }


        //insert agent
        $agent = $this->storeAgent($request->mle,$request->nom_prenoms);

        // Mot de passe aleatoire

        $password = $this->generatePassword();
        

        $username = 'm'.$request->mle;
        
        $user = $this->storeUser($agent->id,$username,$request->email,$password);

        //profil
        if( count($request->type_profil) > 0) {

            foreach ($request->type_profil as $item => $value) {
                $type_profils_id = null;

                $type_profil = $this->getTypeProfilByName($request->type_profil[$item]);

                if ($type_profil!=null) {
                    $type_profils_id = $type_profil->id;
                }else{
                    $this->storeTypeProfilByName($request->type_profil[$item]);

                    $type_profil = $this->getTypeProfilByName($request->type_profil[$item]);

                    if ($type_profil!=null) {
                        $type_profils_id = $type_profil->id;
                    }
                }
                

                $profil = $this->storeProfil($user->id,$type_profils_id);

            }
        }

        //section

        if (isset($request->nom_section)) {
            if (count($request->nom_section) > 0) {
                foreach ($request->nom_section as $key => $value) {

                    try {

                        $section = $this->getSectionByIdByName($request->nom_section[$key],$request->code_structure);

                        if ($section != null) {
                            $sections_id = $section->id;
                        }
            
                        if ($sections_id!=null) {

                            $agent_section = $this->storeAgentSection($agent->id,$sections_id,date("Y"));    
                            $libelle = 'Activé';  

                            $type_statut_agent_section = $this->getTypeStatutAgentSectionByName($libelle);
                            
                            if ($type_statut_agent_section!=null) {

                                $type_statut_agent_sections_id = $type_statut_agent_section->id;

                            }else{

                                $this->storeTypeStatutAgentSection($libelle);

                                $type_statut_agent_section = $this->getTypeStatutAgentSectionByName($libelle);
                            
                                if ($type_statut_agent_section!=null) {

                                    $type_statut_agent_sections_id = $type_statut_agent_section->id;

                                }
                            }

                            $agent_sections_id = $agent_section->id;

                            $this->storeStatutAgentSection($agent_sections_id,$type_statut_agent_sections_id,$profils_id);

                        }else{
                            return redirect()->back()->with('error','Echec inscrit. Section introuvable');
                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                    }


                }
                
            }
        }
        
        
        
        $nom_prenoms = explode(' ',$agent->nom_prenoms) ;
        $prenoms='';

        for ($i=1; $i < 4; $i++) { 
            if (isset($nom_prenoms[$i])) {
                $prenoms .= $nom_prenoms[$i].' '; 
            }
        }
        $prenoms = trim($prenoms);
        
        

        $param_acces_login = $user->email;
        $param_acces_password = $password;
        

        if ($agent!=null AND $user!=null AND $profil!=null AND $agent_section!=null) {

                $agents_id = $agent->id;
                // notifier l'émetteur
                $subject = 'Création de compte utilisateur';

                // utilisateur connecté
                    $email = $user->email;
                    $this->notif_user_connect($email,$subject,$agents_id,$param_acces_login,$param_acces_password);
                //

                // notifier l'utilisateur cree 
                    $this->notif_user_create($subject,$agents_id,$param_acces_login,$param_acces_password);
                //

            return redirect()->back()->with('success','Agent inscrit');
        }else{
            return redirect()->back()->with('error','Echec inscrit');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function show(Agent $agent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function edit($agent)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($agent);
        } catch (DecryptException $e) {
            //
        }

        $agent = Agent::findOrFail($decrypted);

        $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique'];

        $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        if($profil!=null){
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }

        //Structure
        $structures = Structure::all();

        //Type de profils
        $type_profils = TypeProfil::all();
        
        $code_structure = null;
        
        $agents = $this->getAgentById($agent->id);
            
        if ($agents!=null) {
            $code_structure = $agents->code_structure;
        }
        //sections
        $sections = $this->getSectionById($code_structure);

        $profils = $this->getProfilByAgentId($agent->id);        

        $agent_sections = $this->getAgentSectionByAgentId($agent->id);

        return view('agents.edit',[
            'sections' => $sections,
            'structures' => $structures,
            'type_profils' => $type_profils,
            'profils'=>$profils,
            'agents'=>$agents,
            'agent_sections'=>$agent_sections
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agent $agent)
    {           
        $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique'];
             
        $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

        if($profil!=null){
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }
        if($profil!=null){
            $profils_ids = $profil->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }

        
        $validate = $request->validate([
            'mle'=>['required','numeric',Rule::unique('agents')->ignore($request->id)],
            'email'=>['required','email',Rule::unique('users')->ignore($request->users_id)],
            'nom_prenoms'=>['required','string','max:255'],
            'email_old'=>['required','email'],
            'nom_section'=>['required','array'],
            'code_structure'=>['required','numeric'],
            'structure'=>['required','string','max:255'],
            'type_profil'=>['required','array']
        ]);

        if (isset($request->nom_section)) {
            if (count($request->nom_section) > 0) {
                foreach ($request->nom_section as $key => $value) {
                    $section = $this->getSectionByIdByName($request->nom_section[$key],$request->code_structure);
        
                    if ($section===null) {
                        return redirect()->back()->with('error','Section invalide');
                    }
                }
                
            }
        }

        
        //insert agent
        $agents =  Agent::where('id',$request->id)->first();
        if ($agents!=null) {
            if (($request->mle != $agents->mle) or ($request->nom_prenoms != $agents->nom_prenoms)) {
                Agent::where('id',$request->id)->update([
                    'mle'=>$request->mle,
                    'nom_prenoms'=>$request->nom_prenoms,
                ]);
            }
        }

        

        $agent = Agent::where('id',$request->id)->first();

        

        // Mot de passe aleatoire
        $password = $this->generatePassword();        

        if ($request->email_old != $request->email) {

            $this->setUser($request->id,$request->email,$password);            
            
        }
        
        $user = $this->getUserByAgentId($request->id);
        
        //profil
        if( count($request->type_profil) > 0) {

            $donnees = [];
            foreach ($request->type_profil as $item => $value) {
                $donnees[$item] =  $request->type_profil[$item];
            }

            

            //désactivé tous les profils de cet utilissateur

            $profils_offs = Profil::where('profils.users_id',$user->id)
                  ->join('type_profils as tp','tp.id','=','profils.type_profils_id')
                  ->where('profils.flag_actif',1)
                  ->whereNotIn('tp.name',$donnees)
                  ->select('profils.id as profils_id','tp.name as type_profils_name','profils.users_id as users_id')
                  ->get();

            foreach ($profils_offs as $profils_off) {

                $type_statut_profils_libelle = "Désactivé";
                $date_debut = date("Y-m-d");
                // $profils_id = $profils_off->profils_id;
                $type_profils_name = $profils_off->type_profils_name;
                $users_id = $profils_off->users_id;
                $flag_actif = 0;

                $profils_id = $this->setProfil($type_profils_name,$users_id,$flag_actif);
                $this->setStatutProfil($profils_id,$type_statut_profils_libelle,$date_debut,$profils_ids);

            }

                  

            foreach ($request->type_profil as $item => $value) {
                
                $type_statut_profils_libelle = "Activé";
                $date_debut = date("Y-m-d");
                $users_id = $user->id;
                $flag_actif = 1;

                $profils_id = $this->setProfil($request->type_profil[$item],$users_id,$flag_actif);
                $this->setStatutProfil($profils_id,$type_statut_profils_libelle,$date_debut,$profils_ids);

            }

        }


        //section

        if (isset($request->nom_section)) {
            if (count($request->nom_section) > 0) {
                foreach ($request->nom_section as $key => $value) {

                    try {
                    
                        $section = $this->getSectionByIdByName($request->nom_section[$key],$request->code_structure);

                        if ($section != null) {
                            $sections_id = $section->id;


                        

                            $agent_sections = $this->getAgentSectionByAgentIdSectionId($request->id,$sections_id);                           


                
                            if ($agent_sections === null) {
                
                                $agent_section = $this->storeAgentSection($agent->id,$sections_id,date("Y"));
                
                                $agent_sections_id = $agent_section->id;
                                
                            }else{
                                $agent_sections_id = $agent_sections->id;
                            }
                            
                
                
                            
                
                            //désactiver
                            $libelle = 'Désactivé';
                            $this->statut_agent_section_desactive($agent->id,$profils_id,$libelle,$request->code_structure);
                
                            // activer
                            $libelle = 'Activé';
                
                            $statut_agent_sections = $this->statut_agent_section_active($agent_sections_id,$profils_id,$libelle);
                            
                            
                
                        }else{
                            return redirect()->back()->with('error','Section invalide');
                        }

                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                }
                
            }
        }

        $nom_prenoms = explode(' ',$agent->nom_prenoms) ;
        $prenoms='';

        for ($i=1; $i < 4; $i++) { 
            if (isset($nom_prenoms[$i])) {
                $prenoms .= $nom_prenoms[$i].' '; 
            }
        }
        $prenoms = trim($prenoms);
        
        

        $param_acces_login = $user->email;
        $param_acces_password = $password;
        

        if ($statut_agent_sections!=null) {

                $agents_id = $request->id;
                // notifier l'émetteur
                $subject = 'Modification de compte utilisateur';

                // utilisateur connecté
                    $email = auth()->user()->email;
                    $this->notif_user_connect($email,$subject,$agents_id,$param_acces_login,$param_acces_password);
                //

                // notifier l'utilisateur cree 
                    $this->notif_user_create($subject,$agents_id,$param_acces_login,$param_acces_password);
                //

            return redirect()->back()->with('success','Agent inscrit');
        }else{
            return redirect()->back()->with('error','Echec inscrit');
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent $agent)
    {
        //
    }

    
}
