<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComiteReception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ComiteReceptionController extends Controller
{
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

        
        $etape = "create";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Administrateur DFC']; 
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        $agents = $this->getListeComiteReception();  
        
        $type_statut_demande_achats_libelles = ['Livraison partielle','Livraison totale'];

        $type_statut_demande_achats_libelles2 = ['Livré'];


        $demande_achats = $this->getStatutDemandeAchatGroupe2($type_statut_demande_achats_libelles,$type_statut_demande_achats_libelles2);
        
        return view('comite_receptions.create',[
            'agents'=>$agents,
            'type_profils_name'=>$type_profils_name,
            'demande_achats'=>$demande_achats
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

        
        $etape = "store";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Administrateur DFC']; 
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }


        $request->validate([
            'mle'=>['required','string'],
            'nom_prenoms'=>['required','string'],
            'nom_structure'=>['required','string'],
            'num_bc'=>['required','string'],
            'intitule'=>['required','string'],
            'design_dep'=>['required','string'],
        ]);

        $comite_receptions_id = null;
        $demande_achats_id = null;
        $agents_id = null;

        $agent = DB::table('agents as a')
        ->join('users as u','u.agents_id','=','a.id')
        ->where('a.mle',$request->mle)
        ->select('u.agents_id','u.id as users_id')
        ->first();
        if ($agent != null) {
            $agents_id = $agent->agents_id;
            $users_id = $agent->users_id;
        }else{
            return redirect()->back()->with('error','Matricule incorrect');
        }

        $type_statut_demande_achats_libelles = ['Livraison partielle','Livraison totale'];

        $type_statut_demande_achats_libelles2 = ['Livraison totale confirmée'];


        $demande_achat = $this->getStatutDemandeAchatGroupe3($type_statut_demande_achats_libelles,$type_statut_demande_achats_libelles2,$request->num_bc);

        if ($demande_achat != null) {
            $demande_achats_id = $demande_achat->id;
        }else{
            return redirect()->back()->with('error','Choix de la demande d\'achat non valide');
        }

        

        if ($agents_id != null && $demande_achats_id != null && $users_id != null) {

            $comite_reception = $this->getComiteReception($agents_id,$demande_achats_id);            

            if ($comite_reception === null) {

                $this->setComiteReception($demande_achats_id,Session::get('profils_id'));

                $flag_actif = 1;

                $comite_reception = $this->storeComiteReception($agents_id,$demande_achats_id,$flag_actif,Session::get('profils_id'));

                $comite_receptions_id = $comite_reception->id;

                if ($comite_receptions_id != null) {
                    $type_profils_libelle = 'Comite Réception';
                    $flag_actif = 1;

                    $profils_id = $this->setProfil($type_profils_libelle,$users_id,$flag_actif);

                    $type_statut_profils_libelle = 'Activé';
                    $date_debut = date("Y-m-d");
                    
                    $this->setStatutProfil($profils_id,$type_statut_profils_libelle,$date_debut,Session::get('profils_id'));


                }
                
            }else{
                return redirect()->back()->with('error','Affectation déjà enregistrée');
            }
        }else{
            return redirect()->back()->with('error','Enregistrement echoué (1)');
        }

        if ($comite_receptions_id != null) {
            return redirect()->back()->with('success','Enregistrement effectué');
        }else{
            return redirect()->back()->with('error','Enregistrement echoué');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ComiteReception  $comiteReception
     * @return \Illuminate\Http\Response
     */
    public function show(ComiteReception $comiteReception)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ComiteReception  $comiteReception
     * @return \Illuminate\Http\Response
     */
    public function edit(ComiteReception $comiteReception)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ComiteReception  $comiteReception
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ComiteReception $comiteReception)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ComiteReception  $comiteReception
     * @return \Illuminate\Http\Response
     */
    public function destroy(ComiteReception $comiteReception)
    {
        //
    }
}
