<?php

namespace App\Http\Controllers;

use App\Models\Retour;
use App\Models\Livraison;
use Illuminate\Http\Request;
use App\Models\ValiderRetour;
use App\Models\LivraisonRetour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ValiderRetourController extends Controller
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
                      ->whereIn('type_profils.name', (['Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

                $valider_retours = DB::table('requisitions')
                ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                ->join('valider_requisitions','valider_requisitions.demandes_id','=','demandes.id')
                ->join('livraisons','livraisons.demandes_id','=','demandes.id')
                ->join('retours','retours.livraisons_id','=','livraisons.id')
                ->join('valider_retours','valider_retours.retours_id','=','retours.id')
                ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                ->join('profils','profils.id','=','requisitions.profils_id')
                ->join('users','users.id', '=', 'profils.users_id')
                ->join('agents','agents.id', '=', 'users.agents_id')
                ->where('valider_retours.flag_valide',1)
                ->select('num_dem','num_bc','requisitions.exercice','intitule','requisitions.code_gestion','mle','nom_prenoms','demandes.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte_demandee','magasin_stocks.cmup','valider_requisitions.profils_id','valider_retours.qte_validee','valider_requisitions.flag_valide','valider_requisitions.id as valider_requisitions_id','livraisons.qte','livraisons.profils_id as profils_id_livreur','livraisons.id as livraisons_id','qte_retour','retours.id as retours_id','valider_retours.profils_id as profils_id_validateur_retour','valider_retours.id as valider_retours_id')
                ->paginate(5);

        

        return view('valider_retours.index',[
        'valider_retours' => $valider_retours,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Retour $retour)
    {

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Pilote AEE','Validateur de commandes']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $livraisons_id = $retour->livraisons_id;
        $demandes_id = Livraison::where('id',$livraisons_id)->first()->demandes_id;

        

        $requisitions = DB::table('requisitions')
                            ->join('gestions','gestions.code_gestion','=','requisitions.code_gestion')
                            ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                            ->where('demandes.id',$demandes_id)
                            ->first();
                            //dd($requisitions);
        $retours = [];
        $strucs = DB::select("SELECT st.code_structure FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st WHERE a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND u.id = '".$user_id."'   ");

        foreach ($strucs as $struc) {
            if ($struc!=null) {
                $retours = DB::table('requisitions')
                ->join('demandes','demandes.requisitions_id','=','requisitions.id')
                ->join('magasin_stocks','magasin_stocks.id','=','demandes.magasin_stocks_id')
                ->join('articles','articles.ref_articles','=','magasin_stocks.ref_articles')
                ->join('profils','profils.id','=','requisitions.profils_id')
                ->join('users','users.id', '=', 'profils.users_id')
                ->join('agents','agents.id', '=', 'users.agents_id')
                ->join('valider_requisitions','valider_requisitions.demandes_id', '=', 'demandes.id')
                ->join('livraisons','livraisons.demandes_id', '=', 'demandes.id')
                ->join('retours','retours.livraisons_id', '=', 'livraisons.id')
                ->join('agent_sections as ase','ase.agents_id','=','agents.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->where('demandes.id',$demandes_id)
                ->where('valider_requisitions.flag_valide',1) // réquisition validée
                ->where('livraisons.statut',1) // réquisition livrée
                ->where('st.code_structure',$struc->code_structure)
                ->select('retours.id','articles.ref_articles','articles.design_article','magasin_stocks.cmup','demandes.qte_demandee','magasin_stocks.cmup','livraisons.qte','retours.qte_retour')
                ->get();

                
            }
        }
        
        if (count($retours) == 0) {
            return redirect()->back();
        }

        return view('valider_retours.create',[
            'retours' => $retours,
            'requisitions' => $requisitions,

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

        $profils = DB::table('agents') 
                        ->join('users','users.agents_id','=','agents.id')
                        ->join('profils','profils.users_id','=','users.id')
                        ->join('type_profils','type_profils.id','=','profils.type_profils_id')
                        ->where('users.id',$user_id)
                        ->whereIn('type_profils.name', (['Pilote AEE','Validateur de commandes']))
                        ->select('profils.id')
                        ->first();
        if ($profils != null) {
            $profils_id = $profils->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }
        $validate = $request->validate([
            'ref_articles' => ['required','array'],
            'retours_id' => ['required','array'],
            'cmup' => ['required','array'],
            'qte_retour' => ['required','array'],
            'qte_validee' => ['required','array'],
            'montant' => ['required','array'],
        ]);



            if (count($request->retours_id) > 0) {
                foreach ($request->retours_id as $item => $value) {

                    if (isset($request->approvalcd[$request->retours_id[$item]])) {

                        $qte_validee[$item] = str_replace(' ','',$request->qte_validee[$item]);

                        if ($qte_validee[$item] === null) {
                            $qte_validee[$item] = 0;
                        }
                        $qte_retour[$item] = Retour::where('id', $request->retours_id[$item])->first()->qte_retour;
                        if ($qte_retour[$item] < $qte_validee[$item]) {
                            return redirect()->back()->with('error', 'La quantité validée ne peut être supérieure à la quantité retournée');
                        }

                        if ($qte_validee[$item] <= 0) {
                            return redirect()->back()->with('error', 'La quantité validée ne peut être nulle ou négative');
                        }
                    
                    }

                }
            }
            
            if (count($request->retours_id) > 0) {
                foreach ($request->retours_id as $item => $value) {
                    if (isset($request->approvalcd[$request->retours_id[$item]])) {
                        $flag_valide = true;
                    }else{
                        $flag_valide = false;
                    }

                        $qte_validee[$item] = str_replace(' ','',$request->qte_validee[$item]);
                        $cmup[$item] = str_replace(' ','',$request->cmup[$item]);
                        $montant[$item] = str_replace(' ','',$request->montant[$item]);

                        
                    
                        $data = [
                            'retours_id' => $request->retours_id[$item],
                            'qte_validee' => $qte_validee[$item],
                            'prixu_retour_valide' => $cmup[$item],
                            'montant_retour_valide' => $montant[$item],
                            'profils_id' => $profils_id,
                            'flag_valide' => $flag_valide,
                        ];

                        $retour_verif = Retour::where('id', $request->retours_id[$item])->first();
                        if ($retour_verif!=null) {
                            $montant_retour[$item] = $cmup[$item] * $retour_verif->qte_retour;
                        }else{
                            $montant_retour[$item] = 0;
                        }

                        $data_retour = [
                            'prixu_retour' => $cmup[$item],
                            'montant_retour' => $montant_retour[$item],
                        ];


                        $valider_retour = ValiderRetour::where('retours_id', $request->retours_id[$item])->first();

                        if ($valider_retour === null) {
                            $valider_retours = ValiderRetour::create($data);
                            
                            Retour::where('id', $request->retours_id[$item])->update($data_retour);
                            
                            $subject = "Validation du retour de la réquisition";
                            $message = 'Retour enregistré avec succes';
                            if (!isset($requisitions_id)) {
                                $requisition = ValiderRetour::where('valider_retours.id',$valider_retours->id)
                                ->join('retours as r','r.id','=','valider_retours.retours_id')
                                ->join('livraisons as l','l.id','=','r.livraisons_id')
                                ->join('demandes as d','d.id','=','l.demandes_id')
                                ->first();
                                if ($requisition!=null) {
                                    $requisitions_id = $requisition->requisitions_id;
                                }
                            }
                        }else{
                                $valider_retours = ValiderRetour::where('retours_id', $request->retours_id[$item])->update($data);
                                
                                Retour::where('id', $request->retours_id[$item])->update($data_retour);

                                $subject = "Modification de la validation du retour de la réquisition";
                                $message = 'Retour mis à jour avec succes';
                                $valider_retours = ValiderRetour::where('retours_id', $request->retours_id[$item])->first();

                                if (!isset($requisitions_id)) {
                                    $requisition = ValiderRetour::where('valider_retours.id',$valider_retours->id)
                                    ->join('retours as r','r.id','=','valider_retours.retours_id')
                                    ->join('livraisons as l','l.id','=','r.livraisons_id')
                                    ->join('demandes as d','d.id','=','l.demandes_id')
                                    ->first();
                                    if ($requisition!=null) {
                                        $requisitions_id = $requisition->requisitions_id;
                                    }
                                }

                        }
                    
                    
                }


                if ($valider_retours != null) {
                    if (isset($requisitions_id)) {
                    
                        $email = auth()->user()->email;
                        
        
                        $datas = [
                            'requisitions_id'=>$requisitions_id,
                            'subject'=>$subject,
                            'valider_retour' => 1
                        ];
        
                        Mail::to($email)->send(new \App\Mail\SendMailCreateRequisition($datas));

                        // determinier le mail du demandeur
                    $strucs_demandeurs = DB::select("SELECT * FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st, profils as p, type_profils as tp, requisitions as r WHERE u.id = p.users_id AND p.type_profils_id = tp.id AND a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND r.profils_id = p.id AND tp.name like 'Agent Cnps' AND r.id = '".$requisitions_id."' ");

                    foreach ($strucs_demandeurs as $strucs_demandeur) {
                        if ($strucs_demandeur->email!=null) {
                            Mail::to($strucs_demandeur->email)->send(new \App\Mail\SendMailCreateRequisition($datas));
                        }
                    }


                    // determinier le mail du responsable des stocks
                    $strucs = DB::select("SELECT * FROM users as u, agents as a, agent_sections as ase, sections as s, structures as st, profils as p, type_profils as tp WHERE u.id = p.users_id AND p.type_profils_id = tp.id AND a.id = u.agents_id AND ase.agents_id = a.id AND s.id = ase.sections_id AND st.code_structure = s.code_structure AND tp.name like 'Responsable des stocks' AND st.ref_depot IN (SELECT rd.ref_depot FROM users as u, profils as p, responsable_depots as rd, type_profils as tp WHERE u.id = p.users_id AND p.id = rd.profils_id AND tp.id = p.type_profils_id AND tp.name like 'Responsable des stocks' )");

                    foreach ($strucs as $struc) {
                        if ($struc->email!=null) {
                            Mail::to($struc->email)->send(new \App\Mail\SendMailCreateRequisition($datas));
                        }
                    }
        
                    }
                    return redirect()->back()->with('success',$message);
                }else{
                    return redirect()->back()->with('error','Echec de la validation du retour d\'articles');
                }
                
            }

            
        


        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ValiderRetour  $validerRetour
     * @return \Illuminate\Http\Response
     */
    public function show(ValiderRetour $validerRetour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ValiderRetour  $validerRetour
     * @return \Illuminate\Http\Response
     */
    public function edit(ValiderRetour $validerRetour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ValiderRetour  $validerRetour
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ValiderRetour $validerRetour)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ValiderRetour  $validerRetour
     * @return \Illuminate\Http\Response
     */
    public function destroy(ValiderRetour $validerRetour)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Responsable des stocks','Pilote AEE','Validateur de commandes']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }
        
        $livraison_retour = LivraisonRetour::where('retours_id',$validerRetour->retours_id)->first();
        if($livraison_retour === null){
            $delete_valider_retour = ValiderRetour::where('id',$validerRetour->id)->delete();
        }else{
            return redirect()->back()->with('error','Suppression echouée. Retour déjà livré');
        }

        

        if($delete_valider_retour!=null){
            return redirect()->back()->with('success','Suppression reussie');
        }else{
            return redirect()->back()->with('error','Suppression echouée');
        }
    }
}
