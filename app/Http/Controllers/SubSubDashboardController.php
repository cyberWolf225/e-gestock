<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\SubDashboard;
use Illuminate\Http\Request;
use App\Models\SubSubDashboard;
use Illuminate\Validation\Rule;
use App\Models\SubSubSubDashboard;
use Illuminate\Support\Facades\DB;

class SubSubDashboardController extends Controller
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
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }
        
        $sub_sub_dashboards = DB::table('sub_sub_dashboards')
                                ->join('sub_dashboards','sub_dashboards.id','=','sub_sub_dashboards.sub_dashboards_id')
                                ->join('dashboards','dashboards.id','=','sub_dashboards.dashboards_id')
                                ->select('sub_sub_dashboards.id','sub_sub_dashboards.name','sub_sub_dashboards.link','sub_sub_dashboards.position','sub_sub_dashboards.status','sub_dashboards.name as sub_dashboards_name','dashboards.name as dashboards_name')
                                ->paginate(5);
        //dd($sub_sub_dashboards);
        return view('subsubdashboard.index',[
            'sub_sub_dashboards'=>$sub_sub_dashboards,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $sub_dashboards = SubDashboard::all();
        $position = SubSubDAshboard::all()->count() + 1;
        return view('subsubdashboard.create',[
            'sub_dashboards' => $sub_dashboards,
            'position' => $position,
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
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }
        
        $validate = $request->validate([
            'name' => ['required','string'],
            'sub_dashboard_name' => ['required','string'],
            'status' => ['required','string'],
            'link' => ['required','string'],
            'position' => ['required','integer'], 
        ]);

        
        
        $sub_dashboards_id = SubDashboard::where('name',$request->sub_dashboard_name)->first()->id;
        
        $controle = DB::table('sub_sub_dashboards')
                        ->where('sub_dashboards_id',$sub_dashboards_id)
                        ->where('position',$request->position)
                        ->first();
        
        if ($controle === null) {

            $dashboard = SubSubDashboard::create([
                'name' => $request->name,
                'status' => $request->status,
                'link' => $request->link,
                'sub_dashboards_id' => $sub_dashboards_id,
                'position' => $request->position
            ]);

            if ($dashboard!=null) {
                return redirect()->back()->with('success','Elément du menu ajouté');
            }else{
                return redirect()->back()->with('error','Echec de l\'ajout de l\'élément au menu');
            }

        }else{
            return redirect()->back()->with('error','Echec de l\'ajout de l\'élément au menu. Veuillez vérifier la position de l\'élément');
        }
        

        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubSubDashboard  $subSubDashboard
     * @return \Illuminate\Http\Response
     */
    public function show(SubSubDashboard $subSubDashboard)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $subdashboard = SubDashboard::where('id',$subSubDashboard->sub_dashboards_id)->first();

        $dashboard = Dashboard::where('id',$subdashboard->dashboards_id)->first();
        
        return view('subsubdashboard.show',[
            'dashboard' => $dashboard,
            'subdashboard' => $subdashboard,
            'subsubdashboard' => $subSubDashboard,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubSubDashboard  $subSubDashboard
     * @return \Illuminate\Http\Response
     */
    public function edit(SubSubDashboard $subSubDashboard)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $sub_sub_dashboards = DB::table('sub_sub_dashboards')
                          ->join('sub_dashboards','sub_dashboards.id','=','sub_sub_dashboards.sub_dashboards_id')
                          ->select('sub_sub_dashboards.id','sub_sub_dashboards.name','sub_sub_dashboards.link','sub_sub_dashboards.position','sub_sub_dashboards.status','sub_dashboards.name as sub_dashboards_name')
                          ->where('sub_sub_dashboards.id',$subSubDashboard->id)
                          ->first();

        $sub_dashboards = SubDashboard::all();
        return view('subsubdashboard.edit',[
            'sub_dashboards'=>$sub_dashboards,
            'sub_sub_dashboards' => $sub_sub_dashboards,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubSubDashboard  $subSubDashboard
     * @return \Illuminate\Http\Response
     */
    
    public function update(Request $request, SubSubDashboard $subSubDashboard)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }
        
        
        $validate = $request->validate([
            'name' => ['required',Rule::unique('sub_sub_dashboards')->ignore($request->id),],
            'sub_dashboard_name' => ['required','string'],
            'status' => ['required','string',],
            'link' => ['required','string'],
        ]);

        $sub_dashboards_id = SubDashboard::where('name',$request->sub_dashboard_name)->first()->id;

        

        $controle = DB::table('sub_sub_dashboards')
                        ->where('sub_dashboards_id',$sub_dashboards_id)
                        ->where('id','!=',$request->id)
                        ->where('position',$request->position)
                        ->first();

        if ($controle === null) {

            $subsubdashboard = SubSubDashboard::where('id',$request->id)
                                ->update([
                                    'name' => $request->name,
                                    'status' => $request->status,
                                    'link' => $request->link,
                                    'sub_dashboards_id' => $sub_dashboards_id,
                                    'position' => $request->position
                                ]);

        if ($subsubdashboard!=null) {
            return redirect()->back()->with('success','Elément du sous-menu --niveau 2 modifié');
        }else{
            return redirect()->back()->with('error','Echec de la modification de l\'élément du sous-menu --niveau 2');
        }

        }else{
            return redirect()->back()->with('error','Echec de la modification de l\'élément du sous-menu --niveau 2. Veuillez vérifier la position de l\'élément');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubSubDashboard  $subSubDashboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubSubDashboard $subSubDashboard)
    {    
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur technique','Administrateur fonctionnel']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $subsubsubdashs = SubSubSubDashboard::where('sub_sub_dashboards_id',$subSubDashboard->id)->get();
        foreach ($subsubsubdashs as $subsubsubdash) {
            SubSubSubDashboard::where('id',$subsubsubdash->id)->delete();
        }
        $subsubdashboard = SubSubDashboard::where('id',$subSubDashboard->id)->delete();

        if ($subsubdashboard!=null) {
            return redirect()->back()->with('success','Elément du sous-menu --niveau 2 supprimé');
        }else{
            return redirect()->back()->with('error','Echec de la suppression de l\'élément au sous-menu --niveau 2');
        }
    }
}
