<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\SubDashboard;
use Illuminate\Http\Request;
use App\Models\SubSubDashboard;
use Illuminate\Validation\Rule;
use App\Models\SubSubSubDashboard;
use Illuminate\Support\Facades\DB;

class SubDashboardController extends Controller
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

        $sub_dashboards = DB::table('sub_dashboards')
                                ->join('dashboards','dashboards.id','=','sub_dashboards.dashboards_id')
                                ->select('sub_dashboards.id','sub_dashboards.name','sub_dashboards.link','sub_dashboards.position','sub_dashboards.status','dashboards.name as dashboards_name')
                                ->paginate(5);
        //dd($sub_dashboards);
        return view('subdashboard.index',[
            'sub_dashboards'=>$sub_dashboards,
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

        $dashboards = Dashboard::all();
        return view('subdashboard.create',[
            'dashboards' => $dashboards,
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
            'dashboard_name' => ['required','string'],
            'status' => ['required','string'],
            'link' => ['required','string'],
            'position' => ['required','integer'],
        ]);
        
        $dashboards_id = Dashboard::where('name',$request->dashboard_name)->first()->id;

        $controle = DB::table('sub_dashboards')
                        ->where('dashboards_id',$dashboards_id)
                        ->where('position',$request->position)
                        ->first();

        if ($controle === null) {

            $subdashboard = SubDashboard::create([
                'name' => $request->name,
                'status' => $request->status,
                'link' => $request->link,
                'dashboards_id' => $dashboards_id,
                'position' => $request->position
            ]);
            if ($subdashboard!=null) {
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
     * @param  \App\SubDashboard  $subDashboard
     * @return \Illuminate\Http\Response
     */
    public function show(SubDashboard $subDashboard)
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

        
        $dashboard = Dashboard::where('id',$subDashboard->dashboards_id)->first();
        
        return view('subdashboard.show',[
            'dashboard' => $dashboard,
            'subdashboard' => $subDashboard,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubDashboard  $subDashboard
     * @return \Illuminate\Http\Response
     */
    public function edit(SubDashboard $subDashboard)
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

        $sub_dashboards = DB::table('sub_dashboards')
                          ->join('dashboards','dashboards.id','=','sub_dashboards.dashboards_id')
                          ->select('sub_dashboards.id','sub_dashboards.name','sub_dashboards.link','sub_dashboards.position','sub_dashboards.status','dashboards.name as dashboards_name')
                          ->where('sub_dashboards.id',$subDashboard->id)
                          ->first();

        $dashboards = Dashboard::all();
        return view('subdashboard.edit',[
            'sub_dashboards'=>$sub_dashboards,
            'dashboards' => $dashboards,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubDashboard  $subDashboard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubDashboard $subDashboard)
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
            'name' => ['required',Rule::unique('sub_dashboards')->ignore($request->id),],
            'dashboard_name' => ['required','string'],
            'status' => ['required','string',],
            'link' => ['required','string',],
            'position' => ['required','numeric'],
        ]);

        $dashboards_id = Dashboard::where('name',$request->dashboard_name)->first()->id;

        $controle = DB::table('sub_dashboards')
                        ->where('dashboards_id',$dashboards_id)
                        ->where('id','!=',$request->id)
                        ->where('position',$request->position)
                        ->first();

        if ($controle === null) {

            $subdashboard = SubDashboard::where('id',$request->id)
                                ->update([
                                    'name' => $request->name,
                                    'status' => $request->status,
                                    'link' => $request->link,
                                    'dashboards_id' => $dashboards_id,
                                    'position' => $request->position
                                ]);

        if ($subdashboard!=null) {
            return redirect()->back()->with('success','Elément du sous-menu modifié');
        }else{
            return redirect()->back()->with('error','Echec de la modification de l\'élément du sous-menu');
        }

        }else{
            return redirect()->back()->with('error','Echec de la modification de l\'élément du sous-menu. Veuillez vérifier la position de l\'élément');
        }

    
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubDashboard  $subDashboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubDashboard $subDashboard)
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
        
        

            $subsubdashs = SubSubDashboard::where('sub_dashboards_id',$subDashboard->id)->get();
            foreach ($subsubdashs as $subsubdash) {
                SubSubDashboard::where('id',$subsubdash->id)->delete();

                $subsubsubdashs = SubSubSubDashboard::where('sub_sub_dashboards_id',$subsubdash->id)->get();
                foreach ($subsubsubdashs as $subsubsubdash) {
                    SubSubSubDashboard::where('id',$subsubsubdash->id)->delete();
                }

            }


        
        $subdashboard = SubDashboard::where('id',$subDashboard->id)->delete();



        if ($subdashboard!=null) {
            return redirect()->back()->with('success','Elément du sous-menu supprimé');
        }else{
            return redirect()->back()->with('error','Echec de la suppression de l\'élément au sous-menu');
        }
    }
}
