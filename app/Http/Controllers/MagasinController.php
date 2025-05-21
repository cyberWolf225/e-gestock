<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Depot;
use App\Models\Ville;
use App\Models\Magasin;
use Illuminate\Http\Request;
use App\Models\StatutMagasin;
use Illuminate\Validation\Rule;
use App\Models\TypeStatutMagasin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class MagasinController extends Controller
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
    public function index( Request $request)
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
              
        
        $magasins = DB::table('magasins as m')
                        ->join('depots as d', 'd.id', '=', 'm.depots_id')
                        ->join('villes as v','v.code_ville','=','d.code_ville')
                        ->select('m.id','d.ref_depot','design_dep', 'm.ref_magasin','m.design_magasin','m.updated_at','d.tel_dep','d.adr_dep','v.nom_ville')
                        ->orderByDesc('m.updated_at')
                        ->get();
        
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        return view('magasins.index',[
            'magasins'=>$magasins,
            'type_profils_name'=>$type_profils_name,
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
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $ref_magasin_new = count(Magasin::whereNotNull('ref_magasin')->get()) + 1;
        
        $depots = Depot::all();
        $agents = Agent::all();
        $liste_villes = Ville::all();

        $magasins = Magasin::all();

        return view('magasins.create',[
            'depots'=>$depots,
            'agents'=>$agents,
            'liste_villes'=>$liste_villes,
            'magasins'=>$magasins,
            'ref_magasin_new'=>$ref_magasin_new,
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
            return redirect()->back();
        }

        $validate = $request->validate([
            'ref_magasin'=>['required','string','unique:magasins'],
            'design_magasin'=>['required','string',],
            'ref_depot'=>['required','string',],
            'design_dep'=>['required','string',],
        ]);

        $depot = DB::table('depots')
                      ->where('ref_depot',$request->ref_depot)
                      ->first();
        if ($depot === null) {
                return redirect()->back()->with('error','Depôt introuvable');
        }else{
            $depots_id = $depot->id;
        }

        $magasins_id = Magasin::create([
            'ref_magasin' =>$request->ref_magasin,
            'design_magasin' =>$request->design_magasin,
            'depots_id' =>$depots_id,
        ])->id;

        if ($magasins_id != null) {

            $libelle = "Enregistré";
            $commentaire = null;

            $this->statut_magasin($magasins_id,$libelle,$profils_id,$commentaire);

            return redirect('/magasins/index')->with('success','Enregistrement réussi');
        }else{
            return redirect()->back()->with('error','Enregistrement echoué');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Magasin  $magasin
     * @return \Illuminate\Http\Response
     */
    public function show(Magasin $magasin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Magasin  $magasin
     * @return \Illuminate\Http\Response
     */
    public function edit($magasin)
    {
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($magasin);
        } catch (DecryptException $e) {
            //
        }

        $magasin = Magasin::findOrFail($decrypted);


        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id', Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back();
        }


        
        $depots = Depot::all();
        $agents = Agent::all();
        $liste_villes = Ville::all();

        $magasins = Magasin::all();

        $search_magasin = DB::table('magasins as m')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->join('villes as v','v.code_ville','=','d.code_ville')
                    ->select('v.nom_ville','d.tel_dep','d.adr_dep','d.principal','d.ref_depot','d.design_dep','m.id','m.ref_magasin','m.design_magasin')
                    ->where('m.id',$magasin->id)
                    ->first();

        return view('magasins.edit',[
            'depots'=>$depots,
            'agents'=>$agents,
            'liste_villes'=>$liste_villes,
            'magasins'=>$magasins,
            'search_magasin'=>$search_magasin
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Magasin  $magasin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Magasin $magasin)
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
            'ref_magasin'=>['required','numeric',Rule::unique('magasins')->ignore($request->id)],
            'design_magasin'=>['required','string',Rule::unique('magasins')->ignore($request->id)],
            'ref_depot'=>['required','string',],
            'design_dep'=>['required','string',],
        ]);

        $depot = DB::table('depots')
                      ->where('ref_depot',$request->ref_depot)
                      ->first();
        if ($depot === null) {
                return redirect()->back()->with('error','Depôt introuvable');
        }else{
            $depots_id = $depot->id;
        }

        Magasin::where('id',$request->id)->update([
            'ref_magasin' =>$request->ref_magasin,
            'design_magasin' =>$request->design_magasin,
            'depots_id' =>$depots_id,
        ]);

        $magasins_id = Magasin::where('id',$request->id)->first()->id;

        if ($magasins_id != null) {

            $libelle = "Modifié";
            $commentaire = null;

            $this->statut_magasin($magasins_id,$libelle,$profils_id,$commentaire);

            return redirect('/magasins/index')->with('success','Modification réussi');
        }else{
            return redirect()->back()->with('error','Modification echoué');
        }

    }
    /*
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $validate = $request->validate([
            'ref_magasin'=>['required','string',Rule::unique('magasins')->ignore($request->id)],
            'design_magasin'=>['required','string',],
            'ref_depot'=>['required','string',],
            'design_dep'=>['nullable','string',],
        ]);

        $depot = DB::table('depots')
                      ->where('ref_depot',$request->ref_depot)
                      ->first();
        if ($depot === null) {
                return redirect()->back()->with('error','Depôt introuvable');
        }else{
            $depots_id = $depot->id;
        }

        $magasins = Magasin::where('id',$request->id)->update([
            'ref_magasin' =>$request->ref_magasin,
            'design_magasin' =>$request->design_magasin,
            'depots_id' =>$depots_id,
        ]);

        if ($magasins != null) {
            return redirect('magasins/create')->with('success','Modification réussie');
        }else{
            return redirect('magasins/create')->with('error','Modification echouée');
        }
    }
    */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Magasin  $magasin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Magasin $magasin)
    {
        //
    }

    public function statut_magasin($magasins_id,$libelle,$profils_id,$commentaire){

        $type_statut_magasin = TypeStatutMagasin::where('libelle',$libelle)->first();
        if ($type_statut_magasin!=null) {
            $type_statut_magasins_id = $type_statut_magasin->id;
        }else{
            $type_statut_magasins_id = TypeStatutMagasin::create([
                'libelle'=>$libelle
            ])->id;
        }

        if (isset($type_statut_magasins_id)) {

            StatutMagasin::where('magasins_id',$magasins_id)
                            ->orderByDesc('id')
                            ->limit(1)
                            ->update([
                                'date_fin'=>date('Y-m-d'),
                            ]);
             

            StatutMagasin::create([
                'magasins_id'=>$magasins_id,
                'type_statut_magasins_id'=>$type_statut_magasins_id,
                'date_debut'=>date('Y-m-d'),
                'profils_id'=>$profils_id,
                'commentaire'=>$commentaire,
            ]);
            

        }
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

}
