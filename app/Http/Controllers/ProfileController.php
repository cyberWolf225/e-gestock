<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profil;
use App\Models\TypeProfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function show(User $user){
       // 
    }

    public function index(){
        //
    }



    

    public function create(){
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Administrateur technique']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

              $agents = DB::table('agents')
                        ->join('users', 'agents.id', '=', 'users.agents_id')
                        ->select('agents.*', 'users.email') 
                        ->get();
           


        $type_profils = DB::table('type_profils')
                        ->get();


        return view('profiles.create',[
            'agents' => $agents,
            'type_profils' => $type_profils,
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string'],
            'mle' => ['required', 'integer'],
        ]);
    }

    public function store(Request $request){

       
        $profil_create = null;
        foreach ($request->name as $item => $value) {
            

            $type_profils = DB::table('type_profils')
                        ->where('name',$request->name[$item])
                        ->get()
                        ->first();
            if ($type_profils != null) {
                $type_profils_id = $type_profils->id;
            }else{
                $new_type_profile = TypeProfil::create([
                    'name' => $request->name[$item],
                ]);

                $type_profils = DB::table('type_profils')
                            ->where('name',$request->name[$item])
                            ->get()
                            ->first();
                if ($type_profils != null) {
                    $type_profils_id = $type_profils->id;
                }
            }
            

            if (isset($type_profils_id)) {

                $user = DB::table('users')
                            ->join('agents', 'agents.id', '=', 'users.agents_id')
                            ->where('agents.mle',$request->mle[$item])
                            ->select('users.id as id')
                            ->get()
                            ->first(); 
                if ($user != null) {
                    $users_id = $user->id;
                }else{
                    return redirect()->back()->with('error','Agent introuvable');
                }

                

                if (isset($users_id)) {
                    $controle = Profil::where('users_id',$users_id)->where('type_profils_id',$type_profils_id)->first();
                    if ($controle===null) {
                        $profil_create = Profil::create([
                            'users_id' => $users_id,
                            'type_profils_id' => $type_profils_id,
                        ]);
                    }
                    

                }
                
            }

        }

        if ($profil_create!=null) {
            return redirect()->back()->with('success','Enregistrement de profil réussi');
        }else{
            return redirect()->back()->with('error','Aucun enregistrement effectué. Vérifiez si l\'utilisateur n\'a déjà pas ce profil');
        }
        
                
        
        
    }
}