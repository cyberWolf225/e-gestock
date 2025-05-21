<?php

namespace App\Http\Controllers;

use App\Models\Inventaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class InventaireController extends Controller
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


    

    public function create(Request $request){

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back();
        }
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        //determiner le dépôt
        $design_dep = null;
        $depot = DB::table('agents as a')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('depots as d','d.ref_depot','=','st.ref_depot')
                    ->where('tsase.libelle','Activé')
                    ->where('a.id',auth()->user()->agents_id)
                    ->first(); 
        if ($depot!=null) {
            $design_dep = $depot->design_dep;
        }

        $inventaires = DB::table('inventaires as i')
                    ->select('i.id','i.debut_per','i.fin_per','i.flag_valide','i.flag_integre')
                    ->orderByDesc('i.id')
                    ->whereIn('i.ref_depot', function($query){
                        $query->select(DB::raw('s.ref_depot'))
                              ->from('structures as s')
                              ->join('sections as se','se.code_structure','=','s.code_structure')
                              ->join('agent_sections as ase','ase.sections_id','=','se.id')
                              ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                              ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                              ->where('tsase.libelle','Activé')
                              ->where('ase.agents_id',auth()->user()->agents_id)
                              ->whereRaw('s.ref_depot = i.ref_depot');
                    })
                    ->get();

        $this->storeSessionBackground($request);
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));


        return view('inventaires.create',[
            'design_dep' => $design_dep,
            'inventaires'=>$inventaires,
            'type_profils_name'=>$type_profils_name
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

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        
        $depot = DB::table('agents as a')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('depots as d','d.ref_depot','=','st.ref_depot')
                ->where('tsase.libelle','Activé')
                ->where('a.id',auth()->user()->agents_id)
                ->select('d.ref_depot')
                ->first();
                

        $validate = $request->validate([
            'debut_per' => ['required','date'],
            'fin_per' => ['required','date','after_or_equal:debut_per']
        ]);

        $inventaire = null;

        
        if ($depot!=null) {


            $debut_per = $request->debut_per;
            $fin_per = $request->fin_per;
            $inventaires = DB::table('inventaires as i')
                ->where('i.ref_depot',$depot->ref_depot)
                ->whereBetween('i.debut_per', [$debut_per, $fin_per])
                ->orWhereBetween('i.fin_per', [$debut_per, $fin_per])
                ->orWhere('i.id', function($query) use($debut_per){
                    $query->select(DB::raw('ii.id'))
                          ->from('inventaires as ii')
                          ->where('i.debut_per','<=',$debut_per)
                          ->where('i.fin_per','>=',$debut_per)
                          ->whereRaw('ii.id = i.id');
                })
                ->first();


            if ( $inventaires != null ) {
                return redirect()->back()->with('error','Veuillez initier une période d\'inventaire qui ne chevauche pas avec une autre précédemment initiée.');
            }

            $data = [
                'debut_per'=>$debut_per,
                'fin_per'=>$fin_per,
                'ref_depot'=>$depot->ref_depot,
            ];
    
            $inventaire = Inventaire::create($data);
        }


        

        if ($inventaire!=null) {
            $inventaires_id = Crypt::encryptString($inventaire->id);
            return redirect('/inventaire_articles/create/'.$inventaires_id);
        }else{
            return redirect()->back()->with('error','Initiation de la période echouée');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventaire  $inventaire
     * @return \Illuminate\Http\Response
     */
    public function show(Inventaire $inventaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventaire  $inventaire
     * @return \Illuminate\Http\Response
     */
    public function edit($inventaire)
    {
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($inventaire);
        } catch (DecryptException $e) {
            //
        }

        $inventaire = Inventaire::findOrFail($decrypted);

        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        //determiner le dépôt
        $design_dep = null;
        $depot = DB::table('agents as a')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('depots as d','d.ref_depot','=','st.ref_depot')
                    ->where('tsase.libelle','Activé')
                    ->where('a.id',auth()->user()->agents_id)
                    ->first(); 
        if ($depot!=null) {
            $design_dep = $depot->design_dep;
        }

        $inventaires = DB::table('inventaires as i')
        ->where('i.id',$inventaire->id)
        ->whereNotIn('i.id',function($query){
            $query->select(DB::raw('ia.inventaires_id'))
                  ->from('inventaire_articles as ia')
                  ->whereRaw('i.id = ia.inventaires_id');
        })
        ->first();

        if ($inventaires === null) {
            return redirect()->back()->with('error','Impossible de modifier cette période d\'inventaire');
        }



        $list_inventaires = DB::table('inventaires as i')
                    ->join('structures as s','s.ref_depot','=','i.ref_depot')
                    ->join('sections as se','se.code_structure','=','s.code_structure')
                    ->join('agent_sections as ase','ase.sections_id','=','se.id')
                    ->where('ase.agents_id',auth()->user()->agents_id)
                    ->select('i.id','i.debut_per','i.fin_per','i.flag_valide','i.flag_integre')
                    ->orderByDesc('i.id')
                    ->get();
        
        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));


        return view('inventaires.edit',[
            'design_dep' => $design_dep,
            'inventaires' => $inventaires,
            'list_inventaires'=>$list_inventaires,
            'type_profils_name'=>$type_profils_name
        ]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventaire  $inventaire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventaire $inventaire)
    {
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        $validate = $request->validate([
            'id' => ['required','numeric'],
            'debut_per' => ['required','date'],
            'fin_per' => ['required','date','after_or_equal:debut_per']
        ]);

        $depot = DB::table('agents as a')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('depots as d','d.ref_depot','=','st.ref_depot')
                ->where('tsase.libelle','Activé')
                ->where('a.id',auth()->user()->agents_id)
                ->select('d.ref_depot')
                ->first();

        $inventaire = null;

        if ($depot!=null) {


            $debut_per = $request->debut_per;
            $fin_per = $request->fin_per;
            $id = $request->id;

            $inventaires = null;

            
            $inventaires = DB::table('inventaires as i')
            ->where('ref_depot',$depot->ref_depot)
            ->whereNotIn('i.id',[$id])
            ->whereBetween('debut_per', [$debut_per, $fin_per])
            ->orWhereBetween('fin_per', [$debut_per, $fin_per])
            ->orWhere('id', function($query) use($debut_per, $id){
                $query->select(DB::raw('ii.id'))
                        ->from('inventaires as ii')
                        ->where('i.debut_per','<=',$debut_per)
                        ->where('i.fin_per','>=',$debut_per)
                        ->whereNotIn('ii.id',[$id])
                        ->whereRaw('ii.id = i.id');
            })
            ->first();

            if ( $inventaires != null ) {
                return redirect()->back()->with('error','Veuillez initier une période d\'inventaire qui ne chevauche pas avec une autre précédemment initiée.');
            }

            
            

            $data = [
                'debut_per'=>$debut_per,
                'fin_per'=>$fin_per,
                'ref_depot'=>$depot->ref_depot,
            ];
    
            $inventaire = Inventaire::where('id',$request->id)->update($data);
            $inventaire = Inventaire::where('id',$request->id)->first();
        }


        

        if ($inventaire!=null) {
            $inventaires_id = Crypt::encryptString($inventaire->id);
            return redirect('/inventaire_articles/create/'.$inventaires_id);
        }else{
            return redirect()->back()->with('error','Initiation de la période echouée');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventaire  $inventaire
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventaire $inventaire)
    {
    }

    public function storeSessionBackground($request){
        $request->session()->put('backgroundImage','container-infographie7');
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
