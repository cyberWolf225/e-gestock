<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ExerciceController extends Controller
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
        $acces_create = null;
        $type_profils_name = null;
        
        if (Session::has('profils_id')) {
            $etape = "index";
            
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if ($type_profil != null) {
                $type_profils_name = $type_profil->name;
            }

            $type_profils_lists = ['Administrateur fonctionnel'];

            $profil = $this->controllerAcces($etape, $type_profils_lists, $type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error', 'Accès refusé');
            }
        }

        if($type_profils_name === 'Administrateur fonctionnel'){
            $acces_create = 1;
        }

        $exercices = $this->getExercicesAll();

        return view('exercices.index', [
            'exercices' => $exercices,
            'acces_create' => $acces_create
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'exercice'=>['required','numeric','min:4','unique:exercices']
        ]);


        $statut_exercice = null;
        
        $store_exercice_response = $this->storeExercice($request->exercice);

        if($store_exercice_response != null){

            $this->setStatutExerciceAll();
            $libelle = 'Ouverture';
            $date_debut = date('Y-m-d');

            $statut_exercice = $this->storeStatutExercice($libelle, $store_exercice_response->exercice, Session::get('profils_id'),$date_debut);

        }
        

        if($statut_exercice != null){
            return redirect()->back()->with('success', 'Enregistrement effectué');
        }else{
            return redirect()->back()->with('error', 'Enregistrement echoué');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exercice  $exercice
     * @return \Illuminate\Http\Response
     */
    public function show(Exercice $exercice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Exercice  $exercice
     * @return \Illuminate\Http\Response
     */
    public function edit($exercice,$type_statut_exercices_libelle_hash)
    {
        $decrypted = null;
        $statut_exercice = null;

        $type_statut_exercices_libelle = null;
        try {
            $decrypted = Crypt::decryptString($exercice);
            $type_statut_exercices_libelle = Crypt::decryptString($type_statut_exercices_libelle_hash);
        } catch (DecryptException $e) {
            //
        }
        $exercice = Exercice::findOrFail($decrypted);

        if($exercice != null && $type_statut_exercices_libelle != null){

            $date_fin = date('Y-m-d');
            if($type_statut_exercices_libelle === 'Ouverture'){
                $this->setStatutExerciceAll();
                $date_fin = null;
            }

            $date_debut = date('Y-m-d');
            

            $statut_exercice = $this->storeStatutExercice($type_statut_exercices_libelle, $exercice->exercice, Session::get('profils_id'),$date_debut,$date_fin);

        }
        
        if($statut_exercice != null){
            return redirect()->back()->with('success', 'Modification effectuée');
        }else{
            return redirect()->back()->with('error', 'Modification echouée');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exercice  $exercice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exercice $exercice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Exercice  $exercice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exercice $exercice)
    {
        //
    }
}
