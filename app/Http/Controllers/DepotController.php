<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Depot;
use App\Models\Ville;
use App\Models\Profil;
use App\Models\TypeProfil;
use App\Models\StatutDepot;
use App\Models\StatutProfil;
use Illuminate\Http\Request;
use App\Models\TypeStatutDepot;
use Illuminate\Validation\Rule;
use App\Models\TypeStatutProfil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class DepotController extends Controller
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
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }
        

        

        $depots = DB::table('depots as d')
                        ->join('villes as v','v.code_ville','=','d.code_ville')
                        ->select('d.id','d.ref_depot','d.design_dep','d.updated_at','d.tel_dep','d.adr_dep','v.nom_ville')
                        ->orderByDesc('d.updated_at')
                        ->get();

        return view('depots.index',[
            'depots'=>$depots
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
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }


        
        $depots = Depot::all();
        $agents = Agent::all();
        $liste_villes = Ville::all();

        return view('depots.create',[
            'depots'=>$depots,
            'agents'=>$agents,
            'liste_villes'=>$liste_villes,
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
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();

        if($profil!=null){
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $validate = $request->validate([
            'ref_depot'=>['required','numeric','unique:depots'],
            'design_dep'=>['required','string','unique:depots'],
            'tel_dep'=>['nullable','string'],
            'adr_dep'=>['nullable','string'],
            'ville_dep'=>['nullable','string'],
            'profils_id'=>['nullable','string'],
            'principal'=>['nullable','numeric'],
        ]);


        //Profil

        if($request->profils_id != null){
            $mle_nom_prenoms = explode(' - ',trim($request->profils_id));

        
            if(isset($mle_nom_prenoms[0]))
            {
                $mle = $mle_nom_prenoms[0];
            }else{
                return redirect()->back()->with('error','Veuillez saisir correctement le profil');
            }

            if(isset($mle_nom_prenoms[1]))
            {
                $nom_prenoms = $mle_nom_prenoms[1];
            }else{
                return redirect()->back()->with('error','Veuillez saisir correctement le profil');
            }

            $agent = DB::table('agents')
                          ->join('users','users.agents_id','=','agents.id')
                          ->where('agents.mle',$mle)
                          ->where('agents.nom_prenoms',$nom_prenoms)
                          ->select('users.id as users_id')
                          ->first();

            if ($agent === null) {
                return redirect()->back()->with('error','Cet agent n\'existe pas');
            }else{
                $users_id = $agent->users_id;
            }

            $type_profil = DB::table('type_profils')
                          ->where('type_profils.name','Responsable des stocks')
                          ->first();

            if ($type_profil === null) {

                $new_type_profil = TypeProfil::create([
                    'name'=>'Responsable des stocks'
                ]);

                $type_profils_id = $new_type_profil->id;
            }else{
                $type_profils_id = $type_profil->id;
            }

            $profil = DB::table('agents as a')
                          ->join('users as u','u.agents_id','=','a.id')
                          ->join('profils as p','p.users_id','=','u.id')
                          ->join('type_profils as tp','tp.id','p.type_profils_id')
                          ->where('tp.name','Responsable des stocks')
                          ->select('p.id as profils_id','p.flag_actif')
                          ->where('u.id',$users_id)
                          ->first();

            if ($profil != null) {
                $profils_ids = $profil->profils_id;


                if ($profil->flag_actif!=1) {


                    Profil::where('id',$profils_ids)->update([
                        'flag_actif'=>1
                    ]);
                    
                    $libelle = "Réactivé";
                    $commentaire = null;

                    $this->statut_profil($profils_ids,$libelle,$profils_id,$commentaire);

                }

            }else{
                if(isset($users_id)){
                    if(isset($type_profils_id)){
                        $data = [
                            'users_id'=>$users_id,
                            'type_profils_id'=>$type_profils_id
                        ];
                        $profils_ids = Profil::create($data)->id;

                        $libelle = "Attribué";
                        $commentaire = null;

                        $this->statut_profil($profils_ids,$libelle,$profils_id,$commentaire);

                    }else{
                    return redirect()->back()->with('error','Type profil utilisateur introuvable');
                    }
                }else{
                    return redirect()->back()->with('error','Identifiant utilisateur introuvable');
                }
                
            }
        }

        
        

        //Ville
        $code_ville = null;

        if ($request->ville_dep != null) {
            $ville = DB::table('villes')
                        ->where('villes.nom_ville',trim($request->ville_dep))
                        ->first();
            if ($ville === null) {

                $villes = Ville::create([
                    'nom_ville'=>trim($request->ville_dep),
                    'code_pays'=>1
                ]);

                if ($villes != null) {
                    $code_ville = $villes->id;
                }
                
            }else{
                $code_ville = $ville->code_ville;
            }
        }


        $depots_id = Depot::create([
            'ref_depot'=>$request->ref_depot,
            'design_dep'=>$request->design_dep,
            'tel_dep'=>$request->tel_dep,
            'adr_dep'=>$request->adr_dep,
            'principal'=>$request->principal,
            'code_ville'=>$code_ville,
        ])->id;


        if ($depots_id != null) {


            $libelle = "Création de dépôt";
            $commentaire = null;

            $this->statut_depot($depots_id,$libelle,$profils_id,$commentaire);

            return redirect('/depots/index')->with('success','Enregistrement reussi');
        }else{
            return redirect()->back()->with('error','Enregistrement echoué');

        }
               
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function show(Depot $depot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function edit($depot)
    {

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();

        if($profil!=null){
            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }


        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($depot);
        } catch (DecryptException $e) {
            //
        }

        $depot = Depot::findOrFail($decrypted);

        $depots = DB::table('depots as d')
            ->join('villes as v','v.code_ville','=','d.code_ville')
            ->where('d.id',$depot->id)
            ->select('d.id','v.nom_ville','d.tel_dep','d.adr_dep','d.principal','d.ref_depot','d.design_dep')
            ->first();
        
        $agents = Agent::all();
        $liste_villes = Ville::all();

        return view('depots.edit',[
            'depots'=>$depots,
            'agents'=>$agents,
            'liste_villes'=>$liste_villes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Depot $depot)
    {

        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->first();
        if($profil!=null){
            $profils_id = $profil->id;
        }else{
            return redirect()->back();
        }


        $validate = $request->validate([
            'id'=>['required'],
            'ref_depot'=>['required','numeric',Rule::unique('depots')->ignore($request->id)],
            'design_dep'=>['required','string',Rule::unique('depots')->ignore($request->id)],
            'tel_dep'=>['nullable','string'],
            'adr_dep'=>['nullable','string'],
            'ville_dep'=>['nullable','string'],
            'profils_id'=>['nullable','string'],
            'principal'=>['nullable','numeric'],
        ]);


        //Profil

        if($request->profils_id != null){
            $mle_nom_prenoms = explode(' - ',trim($request->profils_id));

        
            if(isset($mle_nom_prenoms[0]))
            {
                $mle = $mle_nom_prenoms[0];
            }else{
                return redirect()->back()->with('error','Veuillez saisir correctement le profil');
            }

            if(isset($mle_nom_prenoms[1]))
            {
                $nom_prenoms = $mle_nom_prenoms[1];
            }else{
                return redirect()->back()->with('error','Veuillez saisir correctement le profil');
            }

            $agent = DB::table('agents')
                          ->join('users','users.agents_id','=','agents.id')
                          ->where('agents.mle',$mle)
                          ->where('agents.nom_prenoms',$nom_prenoms)
                          ->select('users.id as users_id')
                          ->first();

            if ($agent === null) {
                return redirect()->back()->with('error','Cet agent n\'existe pas');
            }else{
                $users_id = $agent->users_id;
            }

            $type_profil = DB::table('type_profils')
                          ->where('type_profils.name','Responsable des stocks')
                          ->first();

            if ($type_profil === null) {

                $new_type_profil = TypeProfil::create([
                    'name'=>'Responsable des stocks'
                ]);

                $type_profils_id = $new_type_profil->id;
            }else{
                $type_profils_id = $type_profil->id;
            }

            $profil = DB::table('agents as a')
                          ->join('users as u','u.agents_id','=','a.id')
                          ->join('profils as p','p.users_id','=','u.id')
                          ->join('type_profils as tp','tp.id','p.type_profils_id')
                          ->where('tp.name','Responsable des stocks')
                          ->select('p.id as profils_id','p.flag_actif')
                          ->where('u.id',$users_id)
                          ->first();

            if ($profil != null) {
                $profils_ids = $profil->profils_id;


                if ($profil->flag_actif!=1) {


                    Profil::where('id',$profils_ids)->update([
                        'flag_actif'=>1
                    ]);
                    
                    $libelle = "Réactivé";
                    $commentaire = null;

                    $this->statut_profil($profils_ids,$libelle,$profils_id,$commentaire);

                }

            }else{
                if(isset($users_id)){
                    if(isset($type_profils_id)){
                        $data = [
                            'users_id'=>$users_id,
                            'type_profils_id'=>$type_profils_id
                        ];
                        $profils_ids = Profil::create($data)->id;

                        $libelle = "Attribué";
                        $commentaire = null;

                        $this->statut_profil($profils_ids,$libelle,$profils_id,$commentaire);

                    }else{
                    return redirect()->back()->with('error','Type profil utilisateur introuvable');
                    }
                }else{
                    return redirect()->back()->with('error','Identifiant utilisateur introuvable');
                }
                
            }
        }

        
        

        //Ville
        $code_ville = null;

        if ($request->ville_dep != null) {
            $ville = DB::table('villes')
                        ->where('villes.nom_ville',trim($request->ville_dep))
                        ->first();
            if ($ville === null) {

                $villes = Ville::create([
                    'nom_ville'=>trim($request->ville_dep),
                    'code_pays'=>1
                ]);

                if ($villes != null) {
                    $code_ville = $villes->id;
                }
                
            }else{
                $code_ville = $ville->code_ville;
            }
        }


        Depot::where('id',$request->id)->update([
            'ref_depot'=>$request->ref_depot,
            'design_dep'=>$request->design_dep,
            'tel_dep'=>$request->tel_dep,
            'adr_dep'=>$request->adr_dep,
            'principal'=>$request->principal,
            'code_ville'=>$code_ville,
        ]);

        $depots_id = Depot::where('id',$request->id)->first()->id;


        if ($depots_id != null) {


            $libelle = "Modification de dépôt";
            $commentaire = null;

            $this->statut_depot($depots_id,$libelle,$profils_id,$commentaire);

            return redirect('/depots/index')->with('success','Modification reussi');
        }else{
            return redirect()->back()->with('error','Modification echoué');

        }
               
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Depot  $depot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Depot $depot)
    {
        //
    }

    public function statut_depot($depots_id,$libelle,$profils_id,$commentaire){
        $type_statut_depot = TypeStatutDepot::where('libelle',$libelle)->first();
        if ($type_statut_depot!=null) {
            $type_statut_depots_id = $type_statut_depot->id;
        }else{
            $type_statut_depots_id = TypeStatutDepot::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($type_statut_depots_id)) {

            $statut_depot = StatutDepot::where('depots_id',$depots_id)
                                        ->orderByDesc('id')
                                        ->limit(1)
                                        ->first();
            if ($statut_depot!=null) {
                StatutDepot::where('id',$statut_depot->id)
                ->update([
                    'date_fin'=>date('Y-m-d'),
                ]);
            } 

            StatutDepot::create([
                'depots_id'=>$depots_id,
                'type_statut_depots_id'=>$type_statut_depots_id,
                'date_debut'=>date('Y-m-d'),
                'profils_id'=>$profils_id,
                'commentaire'=>$commentaire,
            ]);
            

        }
    }

    public function statut_profil($profils_ids,$libelle,$profils_id,$commentaire){

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

}
