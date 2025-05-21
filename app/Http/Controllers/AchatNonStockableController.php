<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AchatNonStockable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AchatNonStockableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){

        $this->storeSessionBackground($request);
        
        $achat_non_stockables = [];
        $acces_create = null;
        if (Session::has('profils_id')) {

            $etape = "index";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $libelle = null;
            if ($type_profils_name === 'Gestionnaire des achats') {
                $libelle = 'Soumis pour validation';
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
                $acces_create = 1;
            }elseif ($type_profils_name === "Responsable des achats") {
                $libelle = "Transmis (Responsable des achats)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Fournisseur") {
                $libelle = "Transmis pour cotation";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable DMP") {
                $libelle = "Demande de cotation (Transmis Responsable DMP)";
                // $libelle = "Transmis (Responsable DMP)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable contrôle budgetaire") {
                $libelle = "Transmis (Responsable Contrôle Budgétaire)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Chef Département DCG") {
                $libelle = "Transmis (Chef Département DCG)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Responsable DCG") {
                $libelle = "Transmis (Responsable DCG)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Directeur Général Adjoint") {
                $libelle = "Transmis (Directeur Général Adjoint)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Directeur Général") {
                $libelle = "Transmis (Directeur Général)";
                $achat_non_stockables = $this->data($libelle,$type_profils_name);
            }elseif ($type_profils_name === "Comite Réception") {
                
                $libelle = "Livraison partielle";
                $libelle2 = "Livraison totale";
                $achat_non_stockables = $this->data($libelle,$type_profils_name,$libelle2);
                // dd('ici',$achat_non_stockables);
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $achat_non_stockable_edite = DB::table('achat_non_stockables as da')
                ->join('statut_achat_non_stockables as sda','sda.achat_non_stockables_id','=','da.id')
                ->join('type_statut_achat_non_stockables as tsda','tsda.id','=','sda.type_statut_achat_non_stockables_id')
                ->where('tsda.libelle','Édité')
                ->limit(1)
                ->first();

            return view('achat_non_stockables.index',[
                'achat_non_stockable_edite'=>$achat_non_stockable_edite,
                'achat_non_stockables'=>$achat_non_stockables,
                'type_profils_name'=>$type_profils_name,
                'acces_create'=>$acces_create,
            ]);

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        


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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AchatNonStockable  $achatNonStockable
     * @return \Illuminate\Http\Response
     */
    public function show(AchatNonStockable $achatNonStockable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AchatNonStockable  $achatNonStockable
     * @return \Illuminate\Http\Response
     */
    public function edit(AchatNonStockable $achatNonStockable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AchatNonStockable  $achatNonStockable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AchatNonStockable $achatNonStockable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AchatNonStockable  $achatNonStockable
     * @return \Illuminate\Http\Response
     */
    public function destroy(AchatNonStockable $achatNonStockable)
    {
        //
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
                      ->whereIn('type_profils.name', (['Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception']))
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
            ->where('tp.name', 'Gestionnaire des achats')
            ->limit(1)
            ->select('p.id', 'se.code_section', 's.ref_depot')
            ->where('p.flag_actif',1)
            ->where('p.id',Session::get('profils_id'))
            ->where('tsase.libelle','Activé')
            ->whereIn('s.ref_depot',function($query) use($request){
                $query->select(DB::raw('da.ref_depot'))
                        ->from('achat_non_stockables as da')
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
            ->whereIn('tp.name', ['Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception'])
            ->limit(1)
            ->select('p.id', 'se.code_section')
            ->where('p.flag_actif',1)
            ->where('p.id',Session::get('profils_id'))
            ->where('tsase.libelle','Activé')
            ->whereIn('s.ref_depot',function($query) use($request){
                $query->select(DB::raw('da.ref_depot'))
                        ->from('achat_non_stockables as da')
                        ->where('da.id',$request->id)
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
                            ->from('achat_non_stockables as da')
                            ->where('da.id',$request)
                            ->whereRaw('da.ref_depot = s.ref_depot');
                })
                ->first();
            
            
        }

        return $profils;
    }

    public function data($libelle,$type_profils_name,$libelle2=null){

        
        $achat_non_stockables = [];
        if (isset($type_profils_name)) {
            if ($type_profils_name === "Fournisseur") {

                $achat_non_stockables = AchatNonStockable::select('achat_non_stockables.id as id','achat_non_stockables.num_bc as num_bc','achat_non_stockables.updated_at as updated_at','achat_non_stockables.intitule as intitule')->orderByDesc('achat_non_stockables.updated_at')
                ->whereIn('achat_non_stockables.id', function($query) use($libelle){
                    $query->select(DB::raw('sda.achat_non_stockables_id'))
                          ->from('statut_achat_non_stockables as sda')
                          ->join('type_statut_achat_non_stockables as tsda','tsda.id','=','sda.type_statut_achat_non_stockables_id')
                          ->join('achat_non_stockables as da','da.id','=','sda.achat_non_stockables_id')
                          ->join('critere_adjudications as ca','ca.achat_non_stockables_id','=','da.id')
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
                          ->whereRaw('achat_non_stockables.id = sda.achat_non_stockables_id');
                })->get();

            }elseif ($type_profils_name === "Comite Réception") {

                $achat_non_stockables = AchatNonStockable::select('achat_non_stockables.id as id', 'achat_non_stockables.num_bc as num_bc', 'achat_non_stockables.updated_at as updated_at', 'achat_non_stockables.intitule as intitule')
                ->orderByDesc('achat_non_stockables.updated_at')
                ->whereIn('achat_non_stockables.id', function ($query) use($libelle,$libelle2) {
                    $query->select(DB::raw('sda.achat_non_stockables_id'))
                            ->from('statut_achat_non_stockables as sda')
                            ->join('type_statut_achat_non_stockables as tsda', 'tsda.id', '=', 'sda.type_statut_achat_non_stockables_id')
                            ->whereIn('tsda.libelle', [$libelle,$libelle2])
                            ->whereRaw('achat_non_stockables.id = sda.achat_non_stockables_id');
                })
                ->whereIn('achat_non_stockables.ref_depot',function($query) use($type_profils_name){
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
                    ->whereRaw('achat_non_stockables.ref_depot = st.ref_depot');
                })
                ->whereIn('achat_non_stockables.id', function ($query){
                    $query->select(DB::raw('cr.achat_non_stockables_id'))
                          ->from('comite_receptions as cr')
                          ->join('users as u','u.agents_id','=','cr.agents_id')
                          ->whereRaw('achat_non_stockables.id = cr.achat_non_stockables_id')
                          ->where('cr.flag_actif',1)
                          ->where('u.flag_actif',1)
                          ->where('u.id',auth()->user()->id);
                })
                ->get();

            }else{

                $achat_non_stockables = AchatNonStockable::select('achat_non_stockables.id as id', 'achat_non_stockables.num_bc as num_bc', 'achat_non_stockables.updated_at as updated_at', 'achat_non_stockables.intitule as intitule')
                ->orderByDesc('achat_non_stockables.updated_at')
                ->whereIn('achat_non_stockables.id', function ($query) use($libelle) {
                    $query->select(DB::raw('sda.achat_non_stockables_id'))
                            ->from('statut_achat_non_stockables as sda')
                            ->join('type_statut_achat_non_stockables as tsda', 'tsda.id', '=', 'sda.type_statut_achat_non_stockables_id')
                            ->where('tsda.libelle', [$libelle])
                            ->whereRaw('achat_non_stockables.id = sda.achat_non_stockables_id');
                })
                ->whereIn('achat_non_stockables.ref_depot',function($query) use($type_profils_name){
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
                    ->whereRaw('achat_non_stockables.ref_depot = st.ref_depot');
                })
                ->get();

                

            }
        }

        

        return $achat_non_stockables;

    }

    public function storeSessionBackground($request){
        $request->session()->put('backgroundImage','container-infographie3');
    }

}
