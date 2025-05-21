<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Immobilisation;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ControllerImmobilisation extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getImmobilisations($libelle,$type_profils_name,$code_structure=null,$libelle2=null){

        $immobilisations = [];
        if (isset($type_profils_name)) {
            if ($type_profils_name === "Agent Cnps") {

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('immobilisations as r')
                            ->join('statut_immobilisations as sr','sr.immobilisations_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            // ->join('profils as p','p.id','=','r.profils_id')
                            // ->where('p.users_id',auth()->user()->id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('immobilisations.id = r.id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();               

            }elseif($type_profils_name === "Responsable N+1"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('immobilisations as r')
                            ->join('statut_immobilisations as sr','sr.immobilisations_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('detail_immobilisations as d','d.immobilisations_id','=','sr.immobilisations_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n1',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('immobilisations.id = r.id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();

            }elseif($type_profils_name === "Responsable N+2"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('r.id'))
                            ->from('immobilisations as r')
                            ->join('statut_immobilisations as sr','sr.immobilisations_id','=','r.id')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('detail_immobilisations as d','d.immobilisations_id','=','sr.immobilisations_id')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->join('hierarchies as h','h.agents_id','=','a.id')
                            ->where('h.flag_actif',1)
                            ->where('h.agents_id_n2',auth()->user()->agents_id)
                            ->where('tsr.libelle',[$libelle])
                            ->whereRaw('immobilisations.id = r.id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();
            }elseif($type_profils_name === "Pilote AEE"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                ->where('immobilisations.code_structure',$code_structure)
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->orWhereIn('immobilisations.id', function($query) use($libelle2){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle2)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();
            }elseif($type_profils_name === "Responsable des stocks"){

                
                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                //->where('immobilisations.code_structure',$code_structure)
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();

               // dd($immobilisations);
            }elseif($type_profils_name === "Gestionnaire des stocks"){

                $immobilisations = Immobilisation::select('immobilisations.id as id','immobilisations.num_bc as num_bc','immobilisations.updated_at as updated_at','immobilisations.intitule as intitule','aa.mle','aa.nom_prenoms')
                ->join('profils as pp','pp.id','=','immobilisations.profils_id')
                ->join('users as uu','uu.id','=','pp.users_id')
                ->join('agents as aa','aa.id','=','uu.agents_id')
                ->orderByDesc('immobilisations.id')
                //->where('immobilisations.code_structure',$code_structure)
                ->whereIn('immobilisations.id', function($query) use($libelle){
                    $query->select(DB::raw('sr.immobilisations_id'))
                          ->from('statut_immobilisations as sr')
                          ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                          ->where('tsr.libelle',$libelle)
                          ->whereRaw('immobilisations.id = sr.immobilisations_id');
                })
                ->whereIn('immobilisations.id', function($query){
                    $query->select(DB::raw('d.immobilisations_id'))
                          ->from('detail_immobilisations as d')
                          ->whereRaw('immobilisations.id = d.immobilisations_id');
                })
                ->get();
            }
        }

        return $immobilisations;

    }
}
