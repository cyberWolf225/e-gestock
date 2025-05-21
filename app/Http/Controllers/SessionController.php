<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use Illuminate\Http\Request;
use App\Models\ProfilFonction;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function getSessionData(Request $request){

        if ($request->session()->has('profils_id')) {
            echo $request->session()->get('profils_id');
        }else{
            echo 'No data in the session';
        }
        
    }
    public function getSessionFonction(Request $request){

        if ($request->session()->has('fonctions_id')) {
            echo $request->session()->get('fonctions_id');
        }else{
            echo 'No data in the session';
        }
        
    }

    public function storeSessionData(Request $request, Profil $profil){
       

        $profil = DB::table('profils as p')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->where('p.users_id',auth()->user()->id)
        ->where('p.flag_actif',1)
        ->where('u.flag_actif',1)
        ->where('p.id',$profil->id)
        ->select('p.id as profils_id','u.agents_id')
        ->first();

        if ($profil!=null) {
            $request->session()->put('profils_id',$profil->profils_id);
            $this->setSessionFonction($profil->agents_id,$request);
            return redirect('/home');
        }else{
            return redirect()->back()->with('error','Echec du changement de profil');
        }
        
    } 

    public function deleteSessionData(Request $request){
        $request->session()->forget('profils_id');
        $this->deleteSessionFonction($request);
    }

    public function deleteSessionFonction(Request $request){
        $request->session()->forget('fonctions_id');
    }

    public function setSessionFonction($agents_id,$request){

        if (isset($agents_id)) {
            $profil_fonction = ProfilFonction::where('agents_id',$agents_id)
            ->where('flag_actif',1)
            ->first();
            if ($profil_fonction!=null) {
                $fonctions_id = $profil_fonction->fonctions_id;

                $request->session()->put('fonctions_id',$fonctions_id);
            }
        }

    }

    
}
