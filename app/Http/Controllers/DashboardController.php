<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Models\SubDashboard;
use Illuminate\Http\Request;
use App\Models\SubSubDashboard;
use Illuminate\Validation\Rule;
use App\Models\SubSubSubDashboard;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

        $dashboards = DB::table('dashboards')->paginate(5);
        //dd($dashboards);
        return view('dashboard.index',[
            'dashboards'=>$dashboards,
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

        $position = DAshboard::all()->count() + 1;
        return view('dashboard.create',[
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
            'status' => ['required','string'],
            'link' => ['required','string'],
            'position' => ['required','integer','unique:dashboards'],
        ]);

        $dashboard = Dashboard::create([
            'name' => $request->name,
            'status' => $request->status,
            'link' => $request->link,
            'position' => $request->position
        ]);
        if ($dashboard!=null) {
            return redirect()->back()->with('success','Elément du menu ajouté');
        }else{
            return redirect()->back()->with('error','Echec de l\'ajout de l\'élément au menu');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function show(Dashboard $dashboard)
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

        return view('dashboard.show',[
            'dashboard' => $dashboard,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function edit(Dashboard $dashboard)
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

        $dashboard_id = $dashboard->id;

        $dashboards = DB::table('dashboards')
                          ->where('id',$dashboard_id)
                          ->first();
        
        return view('dashboard.edit',[
            'dashboards'=>$dashboards,
        ]);

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dashboard $dashboard)
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
            'name' => ['required',Rule::unique('dashboards')->ignore($request->id),],
            'status' => ['required','string',],
            'link' => ['required','string',],
            'position' => ['required',Rule::unique('dashboards')->ignore($request->id),],
        ]);

    
        $dashboard = Dashboard::where('id',$request->id)
                                ->update([
                                    'name' => $request->name,
                                    'status' => $request->status,
                                    'link' => $request->link,
                                    'position' => $request->position
                                ]);

        if ($dashboard!=null) {
            return redirect()->back()->with('success','Elément du menu modifié');
        }else{
            return redirect()->back()->with('error','Echec de la modification de l\'élément au menu');
        }

        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dashboard $dashboard)
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
        
        
        $subdashs = SubDashboard::where('dashboards_id',$dashboard->id)->get();
        foreach ($subdashs as $subdash) {
            SubDashboard::where('id',$subdash->id)->delete();

            $subsubdashs = SubSubDashboard::where('sub_dashboards_id',$subdash->id)->get();
            foreach ($subsubdashs as $subsubdash) {
                SubSubDashboard::where('id',$subsubdash->id)->delete();

                $subsubsubdashs = SubSubSubDashboard::where('sub_sub_dashboards_id',$subsubdash->id)->get();
                foreach ($subsubsubdashs as $subsubsubdash) {
                    SubSubSubDashboard::where('id',$subsubsubdash->id)->delete();
                }

            }


        }
        $dashboard = Dashboard::where('id',$dashboard->id)->delete();



        if ($dashboard!=null) {
            return redirect()->back()->with('success','Elément du menu supprimé');
        }else{
            return redirect()->back()->with('error','Echec de la suppression de l\'élément au menu');
        }
    }
}
