<?php

namespace App\Http\Controllers;

use App\Models\Famille;
use Illuminate\Http\Request;
use App\Models\StatutFamille;
use Illuminate\Validation\Rule;
use App\Models\TypeStatutFamille;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class FamilleController extends Controller
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
        //
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
            return redirect()->back()->with('error','Accès refusé');
        }

        $familles = Famille::all();
        $liste_familles = DB::table('familles')->orderByDesc('id')->paginate(4);

        $datas = DB::table('familles')->orderByDesc('updated_at')->get();

        return view('familles.create',[
            'familles'=>$familles,
            'liste_familles'=>$liste_familles,
            'datas'=>$datas
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
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
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
            'ref_fam'=>['required','numeric','unique:familles'],
            'design_fam'=>['required','string','unique:familles'],
            'flag_actif'=>['nullable'],
        ]);

        if (isset($request->flag_actif)) {
            $flag_actif = 1;
        }else{
            $flag_actif = 0;
        }
        $data = [
            'ref_fam'=>$request->ref_fam,
            'design_fam'=>$request->design_fam,
            'flag_actif'=>$flag_actif,
        ];

        $familles_id = Famille::create($data)->id;


        if ($familles_id!=null) {

            $commentaire = null;

            $this->statut_famille($flag_actif,$familles_id,$profils_id,$commentaire);


            return redirect()->back()->with('success','Enregistrement reussi');
        }else{
            return redirect()->back()->with('error','Enregistrement echoué');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Famille  $famille
     * @return \Illuminate\Http\Response
     */
    public function show(Famille $famille)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Famille  $famille
     * @return \Illuminate\Http\Response
     */
    public function edit($famille, Request $request)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($famille);
        } catch (DecryptException $e) {
            //
        }

        $famille = Famille::findOrFail($decrypted);

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
            return redirect()->back()->with('error','Accès refusé');
        }

        $familles = DB::table('familles')->where('id',$famille->id)->first();

        

        $liste_familles = Famille::all();

        $datas = DB::table('familles')->orderByDesc('id')->get();


        return view('familles.edit',[
            'familles'=>$familles,
            'liste_familles'=>$liste_familles,
            'datas'=>$datas
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Famille  $famille
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Famille $famille)
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
        if($profil!=null){

            $profils_id = $profil->id;

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $validate = $request->validate([
            'id'=>['required','numeric'],
            'ref_fam'=>['required','numeric',Rule::unique('familles')->ignore($request->id)],
            'design_fam'=>['required','string',Rule::unique('familles')->ignore($request->id)],
            'flag_actif'=>['nullable'],
        ]);

        if (isset($request->flag_actif)) {
            $flag_actif = 1;
        }else{
            $flag_actif = 0;
        }
        $data = [
            'ref_fam'=>$request->ref_fam,
            'design_fam'=>$request->design_fam,
            'flag_actif'=>$flag_actif,
        ];

        $famille = Famille::where('id',$request->id)->update($data);

        if ($famille!=null) {

            $familles_id = Famille::where('id',$request->id)->first()->id;

            $commentaire = null;

            $this->statut_famille($flag_actif,$familles_id,$profils_id,$commentaire);


            return redirect()->back()->with('success','Modification reussie');
        }else{
            return redirect()->back()->with('error','Modification echouée');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Famille  $famille
     * @return \Illuminate\Http\Response
     */
    public function destroy(Famille $famille)
    {
        //
    }

    public function statut_famille($flag_actif,$familles_id,$profils_id,$commentaire){
        if (isset($flag_actif)) {

            
            if ($flag_actif === 1) {

                $libelle = "Activé";

            }elseif ($flag_actif === 0) {

                $libelle = "Désactivé";

            }

            if (isset($libelle)) {

                $type_statut_famille = TypeStatutFamille::where('libelle',$libelle)->first();
                if ($type_statut_famille!=null) {

                    $type_statut_familles_id = $type_statut_famille->id;

                }else{

                    $type_statut_familles_id = TypeStatutFamille::create([
                        'libelle'=>$libelle,
                    ])->id;

                }

                if (isset($type_statut_familles_id)) {

                    $statut_famille = DB::table('statut_familles as sf')
                    ->where('sf.familles_id',$familles_id)
                    ->orderByDesc('sf.id')
                    ->limit(1)
                    ->first();
                    if ($statut_famille!=null) {

                        StatutFamille::where('id',$statut_famille->id)->update([
                            'date_fin'=>date("Y-m-d"),
                        ]);
                    }

                    StatutFamille::create([
                        'familles_id'=>$familles_id,
                        'type_statut_familles_id'=>$type_statut_familles_id,
                        'profils_id'=>$profils_id,
                        'date_debut'=>date("Y-m-d"),
                        'commentaire'=>$commentaire,
                    ]);
                }



            }

            

        }
    }
}

