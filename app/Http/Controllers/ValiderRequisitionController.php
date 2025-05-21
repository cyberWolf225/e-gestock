<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Jobs\SendEmail;
use App\Models\Demande;
use App\Models\Requisition;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use App\Models\DemandeConsolide;
use App\Models\StatutRequisition;
use App\Models\ValiderRequisition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\TypeStatutRequisition;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ValiderRequisitionController extends Controller
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
    public function create($requisition)
    {
               
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisition);
        } catch (DecryptException $e) {
            //
        }

        $requisition = Requisition::findOrFail($decrypted);

        

        if (Session::has('profils_id')) {
            
            $etape = "valider_requisition_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $code_structure = $infoUserConnect->code_structure;
            }

            $type_profils_lists = ['Agent Cnps','Pilote AEE','Gestionnaire des stocks','Responsable des stocks','Responsable N+1','Responsable N+2'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$requisition);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }


        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        $profil = null;
        $demandes_fusionnables = [];
        $entete = "Demande d'article";
        $value_bouton2 = null;
        $bouton2 = null;
        $code_structure = null;
        $value_bouton = null;
        $bouton = null;
        $ref_depot = null;
        $statut_profil = null;
        $libelle_fusionnable = null;
        $libelle_fusionnable2 = null;

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
        if ($infoUserConnect != null) {
            $ref_depot = $infoUserConnect->ref_depot;
        }
        

        $statut_requisition = DB::table('statut_requisitions as sr')
                            ->where('sr.requisitions_id', $requisition->id)
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->join('profils as p','p.id','=','sr.profils_id')
                            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->orderByDesc('id')
                            ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
                            ->limit(1)
                            ->first();
        if ($statut_requisition!=null) {
            $libelle = $statut_requisition->libelle;
            $commentaire = $statut_requisition->commentaire;
            $profil_commentaire = $statut_requisition->name;
            $nom_prenoms_commentaire = $statut_requisition->nom_prenoms;

        }

        
        if ($type_profils_name === "Responsable N+1") {
            
            if ($libelle === "Transmis (Responsable N+1)" or $libelle === "Annulé (Responsable N+2)" or $libelle === "Annulé (Pilote AEE)") {
                $profil = $this->acces_validateur($requisition->id,$libelle);
    
                $value_bouton2 = "annuler_agent_n1";
                $bouton2 = "Annuler";
    
                $value_bouton = "valider_agent_n1";
                $bouton = "Valider";
                
    
                $entete = "Validation de demande d'article";
                
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

        }elseif ($type_profils_name === "Responsable N+2") {
            if ($libelle === "Transmis (Responsable N+2)" or $libelle === "Annulé (Pilote AEE)") {
                $profil = $this->acces_validateur($requisition->id,$libelle);
    
                $entete = "Validation de demande d'article";
                $value_bouton2 = "annuler_agent_n2";
                $bouton2 = "Annuler";

                $value_bouton = "valider_agent_n2";
                $bouton = "Valider";
                
                
            }else{                
                return redirect()->back()->with('error','Accès refusé');
            }
        }elseif ($type_profils_name === 'Pilote AEE') {

            if ($libelle === "Transmis (Pilote AEE)" or $libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)" or $libelle === "Validé (Pilote AEE)" or $libelle === "Partiellement validé (Pilote AEE)") {

                $profil = $this->acces_validateur($requisition->id,$libelle);
                $code_structure = $profil->code_structure;
    
                if ($libelle === "Transmis (Pilote AEE)") {
    
                    $libelle_fusionnable = 'Transmis (Pilote AEE)';
                    $libelle_fusionnable2 = 'Partiellement validé (Pilote AEE)';
    
                    $entete = "Consolidation des demandes d'articles de la structure";
                    $value_bouton = "consolider";
                    $bouton = "Consolider";
    
                }elseif($libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)") {
                    $entete = "Transmission de la demande d'article au Responsable des stocks";
                    $value_bouton = "transfert_respo_stock";
                    $bouton = "Transmettre";
                }elseif ($libelle === "Validé (Pilote AEE)") {

                    $libelle_fusionnable = 'Transmis (Pilote AEE)';
                    $libelle_fusionnable2 = 'Partiellement validé (Pilote AEE)';
        
                    $profil = $this->acces_validateur($requisition->id,$libelle);
                    $code_structure = $profil->code_structure;
        
                    $entete = "Validation de demande d'article";
        
                    
                }elseif ($libelle === "Partiellement validé (Pilote AEE)") {
                    $profil = $this->acces_validateur($requisition->id,$libelle);
                    $code_structure = $profil->code_structure;
        
                    $libelle_fusionnable = 'Transmis (Pilote AEE)';
                    $libelle_fusionnable2 = 'Partiellement validé (Pilote AEE)';
        
                    $entete = "Consolidation des demandes d'articles de la structure";
                    $value_bouton = "consolider";
                    $bouton = "Consolider";
                    
                }
                
                $value_bouton2 = "annuler_pilote";
                $bouton2 = "Annuler";
    
                
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

        }elseif ($type_profils_name === 'Responsable des stocks') {
            
            if ($libelle === "Transmis (Responsable des stocks)" or $libelle === "Validé (Responsable des stocks)" or $libelle === "Partiellement validé (Responsable des stocks)") {
                
                $profil = $this->acces_validateur($requisition->id,$libelle,$ref_depot);
    
                if ($libelle === "Transmis (Responsable des stocks)") {
    
                    $entete = "Validation de demande d'article";
                    $value_bouton = "valider_respo_stock";
                    $bouton = "Valider";
    
                    $statut_profil = 1;
    
                }elseif ($libelle === "Validé (Responsable des stocks)" or $libelle === "Partiellement validé (Responsable des stocks)") {
    
                    $entete = "Transmission de la demande d'article au Gestionnaire des stocks pour livraison";
                    $value_bouton = "transmettre_respo_stock";
                    $bouton = "Transmettre";
    
                }
                
                $value_bouton2 = "annuler_respo_stock";
                $bouton2 = "Annuler";
    
                
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        

        if ($profil === null) {
            return redirect()->back();
        }

        $requisitions = $this->requisition_first($requisition->id);    

        $demandes = $this->requisition_get($requisition->id,$libelle,$code_structure);

        $demandes_fusionnables = $this->requisition_consolidable($requisition->id,$code_structure,$libelle_fusionnable,$libelle_fusionnable2);

        
        return view('valider_requisitions.create',[
            'demandes' => $demandes,
            'requisitions' => $requisitions,
            'demandes_fusionnables'=>$demandes_fusionnables,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'requisition'=>$requisition,
            'entete'=>$entete,
            'value_bouton2'=>$value_bouton2,
            'bouton2'=>$bouton2,
            'value_bouton'=>$value_bouton,
            'bouton'=>$bouton,
            'statut_profil'=>$statut_profil

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

            $etape = "store";
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

            $profil = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$request->requisitions_id);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }
            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $libelle = null;
        $profil = null;
        $statut_requisition_store = null;
        $message = null;
        $code_structure = null;
        $ref_depot = null;
        $demande_non_cochees = [];

        $this->validate_request($request);
        
        $requisitions_id = $request->requisitions_id;

        $depot = $this->depot_requisition($requisitions_id);
        if ($depot!=null) {
            $ref_depot = $depot->ref_depot;
        }

        $statut_requisition = $this->dernier_statut_requisition($requisitions_id);
    
        if ($statut_requisition!=null) {
            $libelle = $statut_requisition->libelle;
        }

        $type_profils_name = null;

        if ($libelle === "Transmis (Responsable N+1)" or $libelle === "Annulé (Responsable N+2)") {
            $profil = $this->acces_validateur($requisitions_id,$libelle);

            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
            
        }elseif ($libelle === "Transmis (Responsable N+2)") {
            $profil = $this->acces_validateur($requisitions_id,$libelle);

            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
            
            
        }elseif ($libelle === "Annulé (Pilote AEE)") {

            $profil = $this->acces_validateur($requisitions_id,$libelle);

            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));            
            
        }elseif ($libelle === "Transmis (Pilote AEE)" or $libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)" or $libelle === "Validé (Pilote AEE)" or $libelle === "Partiellement validé (Pilote AEE)") {
            $profil = $this->acces_validateur($requisitions_id,$libelle);
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
            $code_structure = $profil->code_structure;

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $ref_depot = $infoUserConnect->ref_depot;
            }
            
        }elseif ($libelle === "Transmis (Responsable des stocks)" or $libelle === "Validé (Responsable des stocks)" or $libelle === "Partiellement validé (Responsable des stocks)") {
            $profil = $this->acces_validateur($requisitions_id,$libelle,$ref_depot);
            $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
            
        }


        if ($profil === null) {
            return redirect()->back();
        }

        $profils_id = $this->profil_connect($type_profils_name);

        if ($profils_id === null) {
            return redirect()->back();
        }

        
        if (isset($request->submit)) {
            if ($request->submit === "annuler_agent_n1") {

                $libelle_statut = "Annulé (Responsable N+1)";
                $message = "Demande d'article annulée";
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

            }elseif ($request->submit === "valider_agent_n1") {

                $hierarchie = $this->hierarchie($requisitions_id);

                if ($hierarchie!=null) {
                    $libelle_statut = "Transmis (Responsable N+2)";
                }else{
                    $libelle_statut = "Transmis (Pilote AEE)";
                }

                
                $message = "Demande d'article validée";
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);
            }elseif ($request->submit === "annuler_agent_n2") {

                $libelle_statut = "Annulé (Responsable N+2)";
                $message = "Demande d'article annulée";
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

            }elseif ($request->submit === "valider_agent_n2") {

                
                $libelle_statut = "Transmis (Pilote AEE)";
                
                $message = "Demande d'article validée";
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);


            }elseif ($request->submit === "annuler_pilote") {

                $libelle_statut = "Annulé (Pilote AEE)";
                $message = "Demande d'article annulée";

                //vérifier si la demande n'est déjà pas consolidée
                $error_control = $this->demande_consolide_control($requisitions_id);

                if ($error_control!=null) {
                    return redirect()->back()->with('error',$error_control);
                }
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

            }elseif ($request->submit === "consolider") {
                
                $libelle_statut = "Consolidée (Pilote AEE)";
                $message = "Demande d'article consolidée";

                
                $exercice = date("Y");

                $exercices = $this->getExercice();
                if($exercices != null){
                    $exercice = $exercices->exercice;
                }

                $num_bc = $this->getLastNumBci($exercice, $code_structure,$ref_depot);


                $intitule = "Demande de la structure consolidée";

                $code_gestion = 'G';

                
                
                $error = null;
                if (isset($request->approvalcd)) {
                    if (count($request->approvalcd) > 0) {
                        foreach ($request->approvalcd as $item => $value) {
                            
                            if (isset($request->approvalcd[$item])) {
                                $this->valide_saisie($request, $item);
                                
                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Veuillez rattacher au moins un article à cette demande');
                    }
                }else{
                    return redirect()->back()->with('error','Veuillez rattacher au moins un article à cette demande');
                }

                if ($error!=null) {
                    return redirect()->back()->with('error',$error);
                }

                $requisitions_id_consolide = $this->requisition_consolide($code_structure,$num_bc,$exercice,$intitule,$code_gestion,$profils_id);



                //vérifier si la demande n'est déjà pas consolidée
                $donnee_actuelles = [];

                if (isset($request->approvalcd)) {
                    if (count($request->approvalcd) > 0) {
                        foreach ($request->approvalcd as $item => $value) {
                           if (isset($request->approvalcd[$item])) {
                               $donnee_actuelles[$request->demandes_id[$item]] = $request->demandes_id[$item];
                           }
                        }
                    }
                }

                $this->demande_consolide_annule($requisitions_id,$donnee_actuelles);

                
                if (isset($request->approvalcd)) {
                    if (count($request->approvalcd) > 0) {
                        foreach ($request->approvalcd as $item => $value) {
                           
                            
                           if (isset($request->approvalcd[$item])) {

                               $demandes_id = $request->demandes_id[$item];
                               $demande_consolide = $this->demande_consolide($demandes_id,$requisitions_id_consolide);


                           }
                           

                        }
                    }
                }

                $consolide = null;

                if ($demande_consolide!=null) {
                    $consolide = $this->consolidation($requisitions_id_consolide,$profils_id);
                }
                

                if ($consolide!=null) {

                    $this->demande_consolide_detail($requisitions_id_consolide);

                    $statut_requisition_store = $this->statut_requisition($request,$requisitions_id_consolide,$profils_id,$libelle_statut);


                    $this->statut_requisition_consolide($request,$requisitions_id_consolide,$profils_id);
                }

            }elseif($request->submit === "transfert_respo_stock"){

                $libelle_statut = "Transmis (Responsable des stocks)";
                $message = "Demande d'article transmise au Responsable des stocks";
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

            }elseif ($request->submit === "valider_respo_stock") {
                $this->procedureSetDemande($request);
                $error = $this->storeValiderRequisition($request,$profils_id,$requisitions_id);

                if ($error!=null) {
                    return redirect()->back()->with('error',$error); 
                }

                $table = "valider_requisitions";
                $libelle_statut = $this->statut_valider_requisition($requisitions_id,$table);
                
                $message = "Demande d'article [ ".$libelle_statut." ]";
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

            }elseif($request->submit === "annuler_respo_stock"){
                $libelle_statut = "Annulé (Responsable des stocks)";
                $message = "Demande d'article annulée";
                
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

                try {
                    ValiderRequisition::whereIn('demandes_id', function($query) use($requisitions_id){
                        $query->select(DB::raw('d.id'))
                              ->from('demandes as d')
                              ->where('d.requisitions_id',$requisitions_id)
                              ->whereRaw('demandes_id = d.id');
                    })
                    ->delete();
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }elseif ($request->submit === "transmettre_respo_stock") {
                
                $error = $this->storeValiderRequisition($request,$profils_id,$requisitions_id);

                if ($error!=null) {
                    return redirect()->back()->with('error',$error);
                }

                $libelle_statut = "Soumis pour livraison";
                $message = "Demande d'article soumise pour livraison";
                $statut_requisition_store = $this->statut_requisition($request,$requisitions_id,$profils_id,$libelle_statut);

                

            }
        }

        if ($statut_requisition_store!=null) {

            if (isset($request->submit)) {

                if ($request->submit === "annuler_agent_n1" or $request->submit === "valider_agent_n1" or $request->submit === "annuler_agent_n2" or $request->submit === "valider_agent_n2") {
                    $this->notification_demandeur($requisitions_id,$message);

                    if ($request->submit === "valider_agent_n1" or $request->submit === "annuler_agent_n2" or $request->submit === "valider_agent_n2") {
                        $responsable = 3;
                        $this->notification_hierarchie($requisitions_id,$responsable,$message);
            
                    }
                }elseif ($request->submit === "consolider") {
                    if ($consolide!=null) {
                        $this->notification_demandeur_consolide($requisitions_id_consolide);
                        $responsable = 3;
                        $this->notification_hierarchie_consolide($requisitions_id_consolide,$responsable,$message);
                        $this->notification_pilote($requisitions_id_consolide);
                    }
                }elseif ($request->submit === "annuler_pilote") {
                    $responsable = 3;
                    $this->notification_hierarchie($requisitions_id,$responsable,$message);

                }elseif($request->submit === "transfert_respo_stock"){
                    $this->notification_demandeur_consolide2($requisitions_id,$message);
                    $responsable = 3;
                    $this->notification_hierarchie_consolide2($requisitions_id,$responsable,$message);
                    $this->notification_pilote2($requisitions_id,$message);
                    $this->notification_responsable_stock($requisitions_id,$message);
                }elseif($request->submit === "valider_respo_stock" or $request->submit === "annuler_respo_stock" or $request->submit === "transmettre_respo_stock"){
                    $this->notification_demandeur_consolide2($requisitions_id,$message);
                    $responsable = 3;
                    $this->notification_hierarchie_consolide2($requisitions_id,$responsable,$message);
                    $this->notification_pilote2($requisitions_id,$message);
                    $this->notification_responsable_stock($requisitions_id,$message);
                }

            }
            
            return redirect('/requisitions/index')->with('success',$message);
        }else{
            $message = null;
            if (isset($request->submit)) {
                if ($request->submit === "annuler_agent_n1") {
                    $message = "Echec de l'annulation de la demande d'article";
                }elseif ($request->submit === "valider_agent_n1") {
                    $message = "Echec de la validation de la demande d'article";
                }
            }
            return redirect()->back()->with('error',$message);
        }

        
    }
    

    /**
     * Display the specified resource.
     *
     * @param  \App\ValiderRequisition  $validerRequisition
     * @return \Illuminate\Http\Response
     */
    public function show(ValiderRequisition $validerRequisition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ValiderRequisition  $validerRequisition
     * @return \Illuminate\Http\Response
     */
    public function edit(ValiderRequisition $validerRequisition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ValiderRequisition  $validerRequisition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ValiderRequisition $validerRequisition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ValiderRequisition  $validerRequisition
     * @return \Illuminate\Http\Response
     */
    public function destroy(ValiderRequisition $validerRequisition)
    {
        //
    }

    public function dernier_statut_requisition($requisitions_id){

        $requisition = DB::table('requisitions as r')
            ->join('statut_requisitions as sr','sr.requisitions_id','=','r.id')
            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
            ->join('profils as p','p.id','=','sr.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->select('sr.id','sr.requisitions_id','tsr.libelle','commentaire','name','nom_prenoms')
            ->where('r.id',$requisitions_id)
            ->orderByDesc('sr.id')
            ->limit(1)
            ->first();
        
        return $requisition;
    }

    public function acces_validateur($requisitions_id,$libelle,$ref_depot = null){

        if (Session::has('profils_id')) {
            
        }else{
            return redirect()->back();
        }
        
        $profil = null;

        if ($libelle === "Transmis (Responsable N+1)" or $libelle === "Annulé (Responsable N+2)") {

            $profil = DB::table('requisitions as r')
                ->select('r.id as id','r.num_bc as num_bc','r.updated_at as updated_at','r.intitule as intitule')
                ->whereIn('r.id', function($query) use($libelle,$requisitions_id){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n1',auth()->user()->agents_id)
                        ->where('sr.requisitions_id',$requisitions_id)
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('r.id = sr.requisitions_id')
                        ->whereIn('h.agents_id_n1',function($subquery){
                            $subquery->select(DB::raw('aa.id'))
                                     ->from('agents as aa')
                                     ->join('users as uu','uu.agents_id','=','aa.id')
                                     ->join('profils as pp','pp.users_id','=','uu.id')
                                     ->join('type_profils as tpp','tpp.id','=','pp.type_profils_id')
                                     ->where('tpp.name','Responsable N+1')
                                     ->where('aa.id',auth()->user()->agents_id)
                                     ->where('pp.flag_actif',1)
                                     ->whereRaw('h.agents_id_n1 = aa.id');
                        });
                })
                ->first();
        }elseif ($libelle === "Transmis (Responsable N+2)") {

            $profil = DB::table('requisitions as r')
                ->select('r.id as id','r.num_bc as num_bc','r.updated_at as updated_at','r.intitule as intitule')
                ->whereIn('r.id', function($query) use($libelle,$requisitions_id){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n2',auth()->user()->agents_id)
                        ->where('sr.requisitions_id',$requisitions_id)
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('r.id = sr.requisitions_id')
                        ->whereIn('h.agents_id_n2',function($subquery){
                            $subquery->select(DB::raw('aa.id'))
                                     ->from('agents as aa')
                                     ->join('users as uu','uu.agents_id','=','aa.id')
                                     ->join('profils as pp','pp.users_id','=','uu.id')
                                     ->join('type_profils as tpp','tpp.id','=','pp.type_profils_id')
                                     ->where('tpp.name','Responsable N+2')
                                     ->where('pp.flag_actif',1)
                                     ->where('aa.id',auth()->user()->agents_id)
                                     ->whereRaw('h.agents_id_n2 = aa.id');
                        });
                })
                ->first();
        }elseif ($libelle === "Transmis (Pilote AEE)" or $libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)") {

            $profil = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Pilote AEE')
                ->where('tsas.libelle','Activé')
                ->select('r.id as id','r.num_bc as num_bc','r.updated_at as updated_at','r.intitule as intitule','r.code_structure')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('p.id', Session::get('profils_id'))
                ->where('r.id', $requisitions_id)
                ->first();

            
        }elseif ($libelle === "Transmis (Responsable des stocks)" or $libelle === "Validé (Responsable des stocks)" or $libelle === "Partiellement validé (Responsable des stocks)") {
            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {

                $ref_depot = $infoUserConnect->ref_depot;

                if($ref_depot === 83) {
                    $profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    ->where('tp.name','Responsable des stocks')
                    ->where('tsas.libelle','Activé')
                    ->where('p.flag_actif',1)
                    ->where('a.id',auth()->user()->agents_id)
                    ->where('st.ref_depot',$ref_depot)
                    ->whereIn('st.ref_depot', function($query) use($requisitions_id){
                        $query->select(DB::raw('dp.ref_depot'))
                              ->from('requisitions as r')
                              ->join('structures as st2','st2.code_structure','=','r.code_structure')
                              ->join('demandes as d', 'd.requisitions_id', '=', 'r.id')
                              ->join('magasin_stocks as ms', 'ms.id', '=', 'd.magasin_stocks_id')
                              ->join('magasins as m', 'm.ref_magasin', '=', 'ms.ref_magasin')
                              ->join('depots as dp', 'dp.id', '=', 'm.depots_id')
                              ->whereRaw('dp.ref_depot = st.ref_depot')
                              ->where('r.id',$requisitions_id);
                    })
                    ->first();
                }else{
                    $profil = DB::table('users as u')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                    ->where('tp.name','Responsable des stocks')
                    ->where('tsas.libelle','Activé')
                    ->where('p.flag_actif',1)
                    ->where('a.id',auth()->user()->agents_id)
                    ->where('st.ref_depot',$ref_depot)
                    ->whereIn('st.ref_depot', function($query) use($requisitions_id){
                        $query->select(DB::raw('st2.ref_depot'))
                              ->from('requisitions as r')
                              ->join('structures as st2','st2.code_structure','=','r.code_structure')
                              ->whereRaw('st2.ref_depot = st.ref_depot')
                              ->where('r.id',$requisitions_id);
                    })
                    ->first();
                }
            }
                        
            
        }elseif ($libelle === "Annulé (Pilote AEE)") {

            $profil = DB::table('requisitions as r')
                ->select('r.id as id','r.num_bc as num_bc','r.updated_at as updated_at','r.intitule as intitule')
                ->whereIn('r.id', function($query) use($libelle,$requisitions_id){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n1',auth()->user()->agents_id)
                        ->where('sr.requisitions_id',$requisitions_id)
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('r.id = sr.requisitions_id')
                        ->whereIn('h.agents_id_n1',function($subquery){
                            $subquery->select(DB::raw('aa.id'))
                                     ->from('agents as aa')
                                     ->join('users as uu','uu.agents_id','=','aa.id')
                                     ->join('profils as pp','pp.users_id','=','uu.id')
                                     ->join('type_profils as tpp','tpp.id','=','pp.type_profils_id')
                                     ->where('tpp.name','Responsable N+1')
                                     ->where('pp.flag_actif',1)
                                     ->where('aa.id',auth()->user()->agents_id)
                                     ->whereRaw('h.agents_id_n1 = aa.id');
                        });
                })
                ->orWhereIn('r.id', function($query) use($libelle,$requisitions_id){
                    $query->select(DB::raw('sr.requisitions_id'))
                        ->from('statut_requisitions as sr')
                        ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                        ->join('demandes as d','d.requisitions_id','=','sr.requisitions_id')
                        ->join('profils as p','p.id','=','d.profils_id')
                        ->join('users as u','u.id','=','p.users_id')
                        ->join('agents as a','a.id','=','u.agents_id')
                        ->join('hierarchies as h','h.agents_id','=','a.id')
                        ->where('h.flag_actif',1)
                        ->where('h.agents_id_n2',auth()->user()->agents_id)
                        ->where('sr.requisitions_id',$requisitions_id)
                        ->where('tsr.libelle',$libelle)
                        ->whereRaw('r.id = sr.requisitions_id')
                        ->whereIn('h.agents_id_n2',function($subquery){
                            $subquery->select(DB::raw('aa.id'))
                                     ->from('agents as aa')
                                     ->join('users as uu','uu.agents_id','=','aa.id')
                                     ->join('profils as pp','pp.users_id','=','uu.id')
                                     ->join('type_profils as tpp','tpp.id','=','pp.type_profils_id')
                                     ->where('tpp.name','Responsable N+2')
                                     ->where('pp.flag_actif',1)
                                     ->where('aa.id',auth()->user()->agents_id)
                                     ->whereRaw('h.agents_id_n2 = aa.id');
                        });
                })
                ->first();
        }elseif ($libelle === "Validé (Pilote AEE)" or $libelle === "Partiellement validé (Pilote AEE)") {

            $profil = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('requisitions as r','r.code_structure','=','s.code_structure')
                ->join('structures as st','st.code_structure','=','r.code_structure')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Pilote AEE')
                ->where('tsas.libelle','Activé')
                ->select('r.id as id','r.num_bc as num_bc','r.updated_at as updated_at','r.intitule as intitule','r.code_structure')
                ->where('p.flag_actif',1)
                ->whereRaw('st.code_structure = r.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('p.id', Session::get('profils_id'))
                ->where('r.id', $requisitions_id)
                ->first();
        }
        

        return $profil;

    }

    public function requisition_first($requisitions_id){

        $requisition = DB::table('requisitions as r')
                            ->join('gestions as g','g.code_gestion','=','r.code_gestion')
                            ->join('structures as st','st.code_structure','=','r.code_structure')
                            ->where('r.id',$requisitions_id)
                            ->select('st.*','g.*','r.*')
                            ->first();
        return $requisition;
                
    }

    public function requisition_get($requisitions_id,$libelle,$code_structure){

        $demandes = [];
        if ($libelle === "Transmis (Pilote AEE)" or $libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)") {
            $demandes = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->where('r.id',$requisitions_id)
                ->where('r.code_structure',$code_structure)
                ->select('d.id','a.ref_articles','a.design_article','ms.cmup','d.qte','d.requisitions_id_consolide')
                ->get();
        }elseif ($libelle === "Partiellement validé (Responsable des stocks)" or $libelle === "Validé (Responsable des stocks)"){
                $demandes = DB::table('requisitions as r')
                    ->join('demandes as d','d.requisitions_id','=','r.id')
                    ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                    ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->where('r.id',$requisitions_id)
                    ->select('d.id','a.ref_articles','a.design_article','vr.prixu as cmup','vr.qte','d.requisitions_id_consolide','vr.flag_valide')
                    ->where('vr.flag_valide',1) //donner la possibilité au responsable d'ajouter à la liste de demande validé d'autres demandes de la réquisition précédemment non validée
                    ->get();
            
        }else{
            $demandes = DB::table('requisitions as r')
                ->join('demandes as d','d.requisitions_id','=','r.id')
                ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->where('r.id',$requisitions_id)
                ->select('d.id','a.ref_articles','a.design_article','ms.cmup','d.qte','d.requisitions_id_consolide')
                ->get();
        
        }
        

        return $demandes;
                
    }

    public function requisition_consolidable($requisitions_id,$code_structure,$libelle,$libelle2){

        $donnees = [];

        $demandes = DB::table('requisitions as r')
            ->join('demandes as d','d.requisitions_id','=','r.id')
            ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
            ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            ->whereNotIn('r.id',[$requisitions_id])
            ->whereNull('d.requisitions_id_consolide')
            ->where('r.code_structure',$code_structure)
            ->select('d.id as demandes_id','r.id as requisitions_id')
            ->get();
            
            foreach ($demandes as $demande) {

                $statut_requisition = DB::table('statut_requisitions as sr')
                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                    ->where('sr.requisitions_id',$demande->requisitions_id)
                    ->orderByDesc('sr.id')
                    ->limit(1)
                    ->select('tsr.libelle')
                    ->first();

                    if ($statut_requisition!=null) {
                        if (($statut_requisition->libelle === $libelle) or ($statut_requisition->libelle === $libelle2)) {
                            $donnees[$demande->demandes_id] = $demande->demandes_id;
                        }
                    }

            }

        $demandes_fusionnables = DB::table('requisitions as r')
            ->join('demandes as d','d.requisitions_id','=','r.id')
            ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
            ->join('articles as a','a.ref_articles','=','ms.ref_articles')
            ->where('r.code_structure',$code_structure)
            ->select('d.id','a.ref_articles','a.design_article','ms.cmup','d.qte','ms.cmup')
            ->whereIn('d.id',$donnees)
            ->get();
        return $demandes_fusionnables;
    }

    public function validate_request($request){

        $validate = $request->validate([
            'demandes_id' => ['required','array'],
            'num_bc' => ['required','string'],
            'requisitions_id' => ['required','numeric'],
            'qte_validee' => ['required','array'],
            'ref_articles' => ['required','array'],
            'design_article' => ['required','array'],
            'cmup' => ['required','array'],
            'qte' => ['required','array'],
            'montant' => ['required','array'],
            'commentaire' => ['nullable','string'],
            'approvalcd' => ['nullable','array'],
        ]);

    }

    public function profil_connect($type_profils_name){

        $profils_id = null;

        $profil = DB::table('profils as p')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->where('u.id',auth()->user()->id)
            ->where('p.flag_actif',1)
            ->where('tp.name',$type_profils_name)
            ->select('p.id as profils_id')
            ->first();
        
        if ($profil != null) {
            $profils_id = $profil->profils_id;
        }
        
        return $profils_id;
    }

    public function statut_requisition($request,$requisitions_id,$profils_id,$libelle){
        
        $type_statut_requisition = TypeStatutRequisition::where('libelle', $libelle)->first();
    
        if ($type_statut_requisition===null) {

            $type_statut_requisitions_id = TypeStatutRequisition::create([
                'libelle'=>$libelle
            ])->id;

        }else{

            $type_statut_requisitions_id = $type_statut_requisition->id;

        }



        StatutRequisition::where('requisitions_id',$requisitions_id)
        ->orderByDesc('id')
        ->limit(1)
        ->update([
            'date_fin'=>date('Y-m-d'),
        ]);


        $statut_requisition_store = StatutRequisition::create([
            'profils_id'=>$profils_id,
            'requisitions_id'=>$requisitions_id,
            'type_statut_requisitions_id'=>$type_statut_requisitions_id,
            'date_debut'=>date('Y-m-d'),
            'commentaire'=>$request->commentaire,
        ]);

        return $statut_requisition_store;
            
        

            
    }

    public function statut_requisition_consolide($request,$requisitions_id_consolide,$profils_id){
        
        

        $demandes = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
        ->groupBy('requisitions_id')
        ->select(DB::raw('requisitions_id'))
        ->get();

        foreach ($demandes as $demande) {

            $requisitions_id = $demande->requisitions_id;

            //nombre total d'articles de la demande du demandeur
            $nbre_total = count(Demande::where('requisitions_id',$requisitions_id)->get());

            $nbre_consolide = count(Demande::where('requisitions_id',$requisitions_id)
            ->where('requisitions_id_consolide',$requisitions_id_consolide)
            ->get());

            if ($nbre_total === $nbre_consolide) {
                $libelle = "Validé (Pilote AEE)";
            }elseif ($nbre_total > $nbre_consolide) {
                $libelle = "Partiellement validé (Pilote AEE)";
            }

            if (isset($libelle)) {

                $type_statut_requisition = TypeStatutRequisition::where('libelle', $libelle)->first();
    
                if ($type_statut_requisition===null) {

                    $type_statut_requisitions_id = TypeStatutRequisition::create([
                        'libelle'=>$libelle
                    ])->id;

                }else{

                    $type_statut_requisitions_id = $type_statut_requisition->id;

                }

                StatutRequisition::where('requisitions_id',$requisitions_id)
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'date_fin'=>date('Y-m-d'),
                ]);


                StatutRequisition::create([
                    'profils_id'=>$profils_id,
                    'requisitions_id'=>$requisitions_id,
                    'type_statut_requisitions_id'=>$type_statut_requisitions_id,
                    'date_debut'=>date('Y-m-d'),
                    'commentaire'=>$request->commentaire,
                ]);

            }
                


            
        }

        
            
        

            
    }

    public function notification_demandeur($requisitions_id,$subject){

        $requisition = DB::table('requisitions as r')
            ->join('profils as p','p.id','=','r.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->where('r.id',$requisitions_id)
            ->select('u.email')
            ->first();
        if ($requisition!=null) {

            $details = [
                'email' => $requisition->email,
                'subject' => $subject,
                'requisitions_id' => $requisitions_id,
                'link' => URL::to('/'),
            ];
    
            $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
            dispatch($emailJob);
        }

        
    }

    public function notification_demandeur_consolide($requisitions_id_consolide){

        $demandes = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
        ->groupBy('requisitions_id')
        ->select(DB::raw('requisitions_id'))
        ->get();

        foreach ($demandes as $demande) {
            $requisitions_id = $demande->requisitions_id;

            //nombre total d'articles de la demande du demandeur
            $nbre_total = count(Demande::where('requisitions_id',$requisitions_id)->get());

            $nbre_consolide = count(Demande::where('requisitions_id',$requisitions_id)
            ->where('requisitions_id_consolide',$requisitions_id_consolide)
            ->get());

            if ($nbre_total === $nbre_consolide) {
                $subject = "Demande validé (Pilote AEE)";
            }elseif ($nbre_total > $nbre_consolide) {
                $subject = "Demande partiellement validé (Pilote AEE)";
            }

            if (isset($subject)) {

                $requisition = DB::table('requisitions as r')
                ->join('profils as p','p.id','=','r.profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->where('r.id',$requisitions_id)
                ->select('u.email')
                ->first();
                if ($requisition!=null) {
    
                    $details = [
                        'email' => $requisition->email,
                        'subject' => $subject,
                        'requisitions_id' => $requisitions_id,
                        'link' => URL::to('/'),
                    ];
            
                    $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                    dispatch($emailJob);
                }

            }

            
        }
        

        
    }

    public function notification_hierarchie($requisitions_id,$responsable,$subject){

        if ($responsable === 1) {

            $hierarchie = DB::table('agents as a')
            ->join('hierarchies as h','h.agents_id_n1','=','a.id')
            ->join('users as u','u.agents_id','=','h.agents_id_n1')
            ->where('h.flag_actif',1)
            ->select('u.email')
            ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                $query->select(DB::raw('h2.agents_id_n1'))
                    ->from('requisitions as r')
                    ->join('profils as p','p.id','=','r.profils_id')
                    ->join('users as u2','u2.id','=','p.users_id')
                    ->join('agents as a2','a2.id','=','u2.agents_id')
                    ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                    ->where('h2.flag_actif',1)
                    ->where('r.id',$requisitions_id)
                    ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
            })
            ->first();

            if ($hierarchie!=null) {
                $details = [
                    'email' => $hierarchie->email,
                    'subject' => $subject,
                    'requisitions_id' => $requisitions_id,
                    'link' => URL::to('/'),
                ];
        
                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);
            }

        }elseif ($responsable === 2) {

            $hierarchie = DB::table('agents as a')
            ->join('hierarchies as h','h.agents_id_n2','=','a.id')
            ->join('users as u','u.agents_id','=','h.agents_id_n2')
            ->where('h.flag_actif',1)
            ->select('u.email')
            ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                $query->select(DB::raw('h2.agents_id_n2'))
                    ->from('requisitions as r')
                    ->join('profils as p','p.id','=','r.profils_id')
                    ->join('users as u2','u2.id','=','p.users_id')
                    ->join('agents as a2','a2.id','=','u2.agents_id')
                    ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                    ->where('h2.flag_actif',1)
                    ->where('r.id',$requisitions_id)
                    ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
            })
            ->first();

            if ($hierarchie!=null) {
                $details = [
                    'email' => $hierarchie->email,
                    'subject' => $subject,
                    'requisitions_id' => $requisitions_id,
                    'link' => URL::to('/'),
                ];
        
                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);
            }
            
        }elseif ($responsable === 3) {

            $hierarchie = DB::table('agents as a')
            ->join('hierarchies as h','h.agents_id_n1','=','a.id')
            ->join('users as u','u.agents_id','=','h.agents_id_n1')
            ->where('h.flag_actif',1)
            ->select('u.email')
            ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                $query->select(DB::raw('h2.agents_id_n1'))
                    ->from('requisitions as r')
                    ->join('profils as p','p.id','=','r.profils_id')
                    ->join('users as u2','u2.id','=','p.users_id')
                    ->join('agents as a2','a2.id','=','u2.agents_id')
                    ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                    ->where('h2.flag_actif',1)
                    ->where('r.id',$requisitions_id)
                    ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
            })
            ->first();

            if ($hierarchie!=null) {
                $details = [
                    'email' => $hierarchie->email,
                    'subject' => $subject,
                    'requisitions_id' => $requisitions_id,
                    'link' => URL::to('/'),
                ];
        
                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);
            }


            $hierarchie = DB::table('agents as a')
            ->join('hierarchies as h','h.agents_id_n2','=','a.id')
            ->join('users as u','u.agents_id','=','h.agents_id_n2')
            ->where('h.flag_actif',1)
            ->select('u.email')
            ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                $query->select(DB::raw('h2.agents_id_n2'))
                    ->from('requisitions as r')
                    ->join('profils as p','p.id','=','r.profils_id')
                    ->join('users as u2','u2.id','=','p.users_id')
                    ->join('agents as a2','a2.id','=','u2.agents_id')
                    ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                    ->where('h2.flag_actif',1)
                    ->where('r.id',$requisitions_id)
                    ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
            })
            ->first();

            if ($hierarchie!=null) {
                $details = [
                    'email' => $hierarchie->email,
                    'subject' => $subject,
                    'requisitions_id' => $requisitions_id,
                    'link' => URL::to('/'),
                ];
        
                $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                dispatch($emailJob);
            }

        }

    }

    public function notification_hierarchie_consolide($requisitions_id_consolide,$responsable){


        $demandes = Demande::where('requisitions_id_consolide',$requisitions_id_consolide)
        ->groupBy('requisitions_id')
        ->select(DB::raw('requisitions_id'))
        ->get();

        foreach ($demandes as $demande) {
            $requisitions_id = $demande->requisitions_id;

            //nombre total d'articles de la demande du demandeur
            $nbre_total = count(Demande::where('requisitions_id', $requisitions_id)->get());

            $nbre_consolide = count(Demande::where('requisitions_id', $requisitions_id)
            ->where('requisitions_id_consolide', $requisitions_id_consolide)
            ->get());

            if ($nbre_total === $nbre_consolide) {
                $subject = "Demande validé (Pilote AEE)";
            } elseif ($nbre_total > $nbre_consolide) {
                $subject = "Demande partiellement validé (Pilote AEE)";
            }

            if (isset($subject)) {
                if ($responsable === 1) {

                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n1')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n1'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                            'link' => URL::to('/'),
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
                }elseif ($responsable === 2) {
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n2')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n2'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                            'link' => URL::to('/'),
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
                    
                }elseif ($responsable === 3) {
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n1')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n1', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n1'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n1 = h2.agents_id_n1');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                            'link' => URL::to('/'),
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
        
                    $hierarchie = DB::table('agents as a')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->join('users as u','u.agents_id','=','h.agents_id_n2')
                    ->where('h.flag_actif',1)
                    ->select('u.email')
                    ->whereIn('h.agents_id_n2', function($query) use($requisitions_id){
                        $query->select(DB::raw('h2.agents_id_n2'))
                            ->from('requisitions as r')
                            ->join('profils as p','p.id','=','r.profils_id')
                            ->join('users as u2','u2.id','=','p.users_id')
                            ->join('agents as a2','a2.id','=','u2.agents_id')
                            ->join('hierarchies as h2','h2.agents_id','=','a2.id')
                            ->where('h2.flag_actif',1)
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('h.agents_id_n2 = h2.agents_id_n2');
                    })
                    ->first();
        
                    if ($hierarchie!=null) {
                        $details = [
                            'email' => $hierarchie->email,
                            'subject' => $subject,
                            'requisitions_id' => $requisitions_id,
                            'link' => URL::to('/'),
                        ];
                
                        $emailJob = (new SendEmail($details))->delay(Carbon::now()->addMinutes(0.5));
                        dispatch($emailJob);
                    }
        
                }
            }
        }




        

    }

    public function requisition_consolide($code_structure,$num_bc,$exercice,$intitule,$code_gestion,$profils_id){

        //
        $requisitions_id = null;

        $requisition = DB::table('requisitions as r')
                            ->where('r.code_structure',$code_structure)
                            ->where('r.exercice',$exercice)
                            ->where('r.flag_consolide',1)
                            ->orderByDesc('r.id')
                            ->limit(1)
                            ->first();
        
        if ($requisition!=null) {

            $requisitions = DB::table('requisitions as r')
                            ->join('demandes as d','d.requisitions_id','=','r.id')
                            ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                            ->where('r.code_structure',$code_structure)
                            ->where('r.exercice',$exercice)
                            ->where('r.flag_consolide',1)
                            ->where('vr.flag_valide',1)
                            ->get();
            
            if (count($requisitions) > 0) {

                $requisitions_id = Requisition::create([
                    'code_structure'=>$code_structure,
                    'num_bc'=>$num_bc,
                    'exercice'=>$exercice,
                    'intitule'=>$intitule,
                    'code_gestion'=>$code_gestion,
                    'profils_id'=>$profils_id,
                    'flag_consolide'=>1
                ])->id;

            }else{
                $requisitions_id = $requisition->id;
            }
            

        }else{
            $requisitions_id = Requisition::create([
                'code_structure'=>$code_structure,
                'num_bc'=>$num_bc,
                'exercice'=>$exercice,
                'intitule'=>$intitule,
                'code_gestion'=>$code_gestion,
                'profils_id'=>$profils_id,
                'flag_consolide'=>1
            ])->id;
        }
        

        return $requisitions_id;


    }

    public function controlAcces($type_profils_name,$etape,$users_id,$requisitions_id=null){

        $profil = null;
        if ($etape === "create" or $etape === "store") {

            if ($type_profils_name === 'Agent Cnps') {

                $profil = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Agent Cnps')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('p.id',function($query) use($requisitions_id){
                    $query->select(DB::raw('r.profils_id'))
                            ->from('requisitions as r')
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('r.profils_id = p.id');
                })
                ->first();

            }elseif ($type_profils_name === 'Pilote AEE'){
                $profil = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->where('tp.name', 'Pilote AEE')
                ->limit(1)
                ->select('p.id', 'se.code_section', 's.ref_depot')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.code_structure',function($query) use($requisitions_id){
                    $query->select(DB::raw('r.code_structure'))
                            ->from('requisitions as r')
                            ->where('r.id',$requisitions_id)
                            ->whereRaw('r.code_structure = s.code_structure');
                })
                ->first();
            }elseif ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks'){


                $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
                if ($infoUserConnect != null) {
                    
                    $ref_depot = $infoUserConnect->ref_depot;

                        if($ref_depot === 83) {
                            $profil = DB::table('profils as p')
                            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                            ->join('users as u', 'u.id', '=', 'p.users_id')
                            ->join('agents as a', 'a.id', '=', 'u.agents_id')
                            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                            ->where('p.users_id', auth()->user()->id)
                            ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                            ->limit(1)
                            ->select('p.id', 'se.code_section', 's.ref_depot')
                            ->where('p.flag_actif',1)
                            ->where('p.id',Session::get('profils_id'))
                            ->where('tsase.libelle','Activé')
                            ->whereIn('s.ref_depot',function($query) use($requisitions_id){
                                $query->select(DB::raw('dp.ref_depot'))
                                        ->from('requisitions as r')
                                        ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                        ->join('demandes as d', 'd.requisitions_id', '=', 'r.id')
                                        ->join('magasin_stocks as ms', 'ms.id', '=', 'd.magasin_stocks_id')
                                        ->join('magasins as m', 'm.ref_magasin', '=', 'ms.ref_magasin')
                                        ->join('depots as dp', 'dp.id', '=', 'm.depots_id')
                                        ->where('r.id',$requisitions_id)
                                        ->whereRaw('dp.ref_depot = s.ref_depot');
                            })
                            ->first();
                        }else{
                            $profil = DB::table('profils as p')
                            ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                            ->join('users as u', 'u.id', '=', 'p.users_id')
                            ->join('agents as a', 'a.id', '=', 'u.agents_id')
                            ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                            ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                            ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                            ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                            ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                            ->where('p.users_id', auth()->user()->id)
                            ->whereIn('tp.name', ['Gestionnaire des stocks','Responsable des stocks'])
                            ->limit(1)
                            ->select('p.id', 'se.code_section', 's.ref_depot')
                            ->where('p.flag_actif',1)
                            ->where('p.id',Session::get('profils_id'))
                            ->where('tsase.libelle','Activé')
                            ->whereIn('s.ref_depot',function($query) use($requisitions_id){
                                $query->select(DB::raw('st.ref_depot'))
                                        ->from('requisitions as r')
                                        ->join('structures as st', 'st.code_structure', '=', 'r.code_structure')
                                        ->where('r.id',$requisitions_id)
                                        ->whereRaw('st.ref_depot = s.ref_depot');
                            })
                            ->first();
                        }   
                    }
                
                
            }elseif ($type_profils_name === 'Responsable N+1') {
                
                $profil = DB::table('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                    ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->where('tp.name','Responsable N+1')
                    ->limit(1)
                    ->select('p.id', 'se.code_section', 's.ref_depot')
                    ->where('p.flag_actif',1)
                    ->where('p.id',Session::get('profils_id'))
                    ->where('tsase.libelle','Activé')
                    ->join('hierarchies as h','h.agents_id_n1','=','a.id')
                    ->where('h.flag_actif',1)
                    ->where('h.agents_id_n1',auth()->user()->agents_id)
                    ->whereIn('h.agents_id',function($query) use($requisitions_id){
                        $query->select(DB::raw('aa.id'))
                                ->from('requisitions as r')
                                ->join('profils as pp','pp.id','=','r.profils_id')
                                ->join('users as uu','uu.id','=','pp.users_id')
                                ->join('agents as aa','aa.id','=','uu.agents_id')
                                ->where('r.id',$requisitions_id)
                                ->whereRaw('h.agents_id = aa.id');
                    })
                    ->first();
                

                    
                

            }elseif ($type_profils_name === 'Responsable N+2') {

                $profil = DB::table('profils as p')
                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                    ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                    ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                    ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                    ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                    ->where('p.users_id', auth()->user()->id)
                    ->whereIn('tp.name', ['Responsable N+2'])
                    ->limit(1)
                    ->select('p.id', 'se.code_section', 's.ref_depot')
                    ->where('p.flag_actif',1)
                    ->where('p.id',Session::get('profils_id'))
                    ->where('tsase.libelle','Activé')
                    ->join('hierarchies as h','h.agents_id_n2','=','a.id')
                    ->where('h.flag_actif',1)
                    ->where('h.agents_id_n2',auth()->user()->agents_id)
                    ->whereIn('h.agents_id',function($query) use($requisitions_id){
                        $query->select(DB::raw('aa.id'))
                                ->from('requisitions as r')
                                ->join('profils as pp','pp.id','=','r.profils_id')
                                ->join('users as uu','uu.id','=','pp.users_id')
                                ->join('agents as aa','aa.id','=','uu.agents_id')
                                ->where('r.id',$requisitions_id)
                                ->whereRaw('h.agents_id = aa.id');
                    })
                    ->first();
                

            }
    
        }

        return $profil;
    }

    
}
