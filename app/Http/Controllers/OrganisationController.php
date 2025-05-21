<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Depot;
use App\Models\Famille;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\OrganisationDepot;
use App\Models\OrganisationArticle;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class OrganisationController extends Controller
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
        $organisations = $this->getOrganisations();        
        
        return view('organisations.index',[
            'organisations'=>$organisations
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $depots = $this->getDepots();       

        $familles = $this->getFamille();

        $fournisseur = $this->getTypeProfilFournisseur();

        $num_fournisseur = count($fournisseur) + 1;

        $nbre_fournisseur_non_immatricule = count($this->getFournisseursEntnumNull());

        $mle_non_immatricule = $nbre_fournisseur_non_immatricule + 1;

        $mle_non_immatricule = 'frsx'.str_pad($mle_non_immatricule, 2, "0", STR_PAD_LEFT);

        $organisation = null;

        return view('organisations.create',[
            'depots' => $depots,
            'num_fournisseur' => $num_fournisseur,
            'familles' => $familles,
            'organisation'=>$organisation,
            'mle_non_immatricule'=>$mle_non_immatricule
        ]);
    }

    public function creates($entnum)
    {
        $organisation = null;
        if(isset($entnum)){
            $blok = explode('81045',$entnum);

            if (isset($blok[1])) {

                $blok2 = explode('y65300',$blok[1]);

                if (isset($blok2[0])) {
                    $entnum = $blok2[0];
                }

            }

            $organisation = $this->getEntrepriseSecu($entnum);
        }
        
        $depots = $this->getDepots();       

        $familles = $this->getFamille();

        $fournisseur = $this->getTypeProfilFournisseur();

        $num_fournisseur = count($fournisseur) + 1;

        $nbre_fournisseur_non_immatricule = count($this->getFournisseursEntnumNull());

        $mle_non_immatricule = $nbre_fournisseur_non_immatricule + 1;

        $mle_non_immatricule = 'frsx'.str_pad($mle_non_immatricule, 2, "0", STR_PAD_LEFT);

        return view('organisations.create',[
            'depots' => $depots,
            'num_fournisseur' => $num_fournisseur,
            'familles' => $familles,
            'organisation'=>$organisation,
            'mle_non_immatricule'=>$mle_non_immatricule
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

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Administrateur fonctionnel'];

            $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

            if($profil!=null){

            }else{
                return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        

        $request->validate([
            'mle' => 'required|string|unique:agents',
            'nom_prenoms' => 'required|string',
            'email' => 'required|email|unique:users',
            'entnum' => 'nullable|numeric|unique:organisations',
            'denomination' => 'required|string',
            'depot' => 'required|array',
            'fam_rattache' => 'required|array',
            'adresse' => 'nullable|string',
            'num_contribuable' => 'nullable|string',
        ]);

        if (isset($request->entnum)) {
            $username = 'n'.$request->entnum;
        }else{
            $nbre_fournisseur_non_immatricule = count($this->getFournisseursEntnumNull());

            $mle_non_immatricule = $nbre_fournisseur_non_immatricule + 1;
    
            $mle_non_immatricule = 'frsx'.str_pad($mle_non_immatricule, 2, "0", STR_PAD_LEFT);

            $username = 'n'.explode('frs',$mle_non_immatricule)[1];
        }
        
        $u = 0;
        
        if (isset($request->all_famille)) {

            $u=1;

        }else{

            if (isset($request->fam_rattache)) {
                foreach ($request->fam_rattache as $item => $value) {
                    if ($request->fam_rattache[$item] !=null) {

                        $famille = $this->getFamilleByDesignFam($request->fam_rattache[$item]);
                        
                        if ($famille!=null) {
                            $u=1;

                            break;
                        }

                    }
                }
                
            }

        }

        if ($u === 0) {
            // return redirect()->back()->with('error','Veuillez rattacher le fournisseur à au moins une famille d\'articles');
        }


        $d = 0;
        
        if (isset($request->all_depot)) {

            $d=1;

        }else{

            if (isset($request->depot)) {
                foreach ($request->depot as $item => $value) {
                    if ($request->depot[$item] !=null) {

                        $depot = $this->getDepotDesignDep($request->depot[$item]);
                        
                        if ($depot!=null) {
                            $d=1;
                            break;
                        }

                    }
                }
                
            }

        }

        if ($d === 0) {
            // return redirect()->back()->with('error','Veuillez rattacher le fournisseur à au moins un dépôt d\'articles');
        }

        //insert agent
        $agent = $this->storeAgent($request->mle,$request->nom_prenoms);

        $password = $username;

        $user = $this->storeUser($agent->id,$username,$request->email,$password);
        $type_profils_name_fournisseur = 'Fournisseur';
        //profil

        $type_profils_id = null;

        $type_profil = $this->getTypeProfilByName($type_profils_name_fournisseur);

        if ($type_profil!=null) {
            $type_profils_id = $type_profil->id;
        }else{
            $this->storeTypeProfilByName($type_profils_name_fournisseur);

            $type_profil = $this->getTypeProfilByName($type_profils_name_fournisseur);

            if ($type_profil!=null) {
                $type_profils_id = $type_profil->id;
            }
        }

        if(isset($type_profils_id)) {

            $profil = $this->storeProfil($user->id,$type_profils_id);

            $prof = $this->getProfil($user->id,$type_profils_id);

            if ($prof != null) {
                $profils_id = $prof->id;
            }            
        }

        $type_organisation = $this->getTypeOrganisation($type_profils_name_fournisseur);       

        if ($type_organisation!=null) {
            $type_organisations_id = $type_organisation->id;
        }else{

            $type_organisation =  $this->storeTypeOrganisation($type_profils_name_fournisseur);

            $type_organisations_id = $type_organisation->id;
        }
        
        if (isset($request->entnum)) {
            $organisation =  $this->storeOrganisation($request->entnum,$request->denomination,$request->nom_prenoms,$type_organisations_id,$request->contacts,$request->adresse,$request->num_contribuable);
        }else{
            $organisation =  $this->storeOrganisation(null,$request->denomination,$request->nom_prenoms,$type_organisations_id,$request->contacts,$request->adresse,$request->num_contribuable);
        }
        

        $organisations_id = $organisation->id;               
        
        if (isset($organisations_id)) {

            if (isset($request->all_famille)) {

                $familles = $this->getFamille();

                foreach ($familles as $famille) {

                    $organisation_article = $this->getOrganisationArticleByOrganisationByRefFam($organisations_id,$famille->ref_fam);                    

                    if ($organisation_article != null) {

                        if ($organisation_article->flag_actif==1) {
                            
                        }else{
                            $this->setOrganisationArticle($organisation_article->id,$organisations_id,$famille->ref_fam);

                            $libelle = "Activé";

                            $organisation_articles_id = $organisation_article->id;
                        }
                    }else{

                        $libelle = "Crée";

                        $organisation_article = $this->storeOrganisationArticle($organisations_id,$famille->ref_fam);

                        $organisation_articles_id = $organisation_article->id;
                    }

                    if (isset($organisation_articles_id)) {

                                    
                        $commentaire = null;

                        $type_statut_organisation_article = $this->getTypeStatutOrganisationArticle($libelle);

                        if ($type_statut_organisation_article!=null) {

                            $type_statut_org_articles_id = $type_statut_organisation_article->id;
                
                        }else{
                            $type_statut_org_article = $this->storeTypeStatutOrganisationArticle($libelle);

                            $type_statut_org_articles_id = $type_statut_org_article->id;
                        }

                        if (isset($type_statut_org_articles_id)) {

                            $this->setLastStatutOrganisationArticle($organisation_articles_id);

                            $this->storeStatutOrganisationArticle($organisation_articles_id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);

                        }

                    }

                }
            }else{
                
                    foreach ($request->fam_rattache as $item => $value) {

                        if ($request->fam_rattache[$item] !=null) {
                            $famille = $this->getFamilleByDesignFam($request->fam_rattache[$item]);
                            if ($famille!=null) {
                                $ref_fam = $famille->ref_fam;
    
                                if ($ref_fam!=null) {

                                    $organisation_article = $this->getOrganisationArticleByOrganisationByRefFam($organisations_id,$famille->ref_fam);

                                    if ($organisation_article!=null) {
    
                                        if ($organisation_article->flag_actif==1) {
                                            
                                        }else{

                                            $this->setOrganisationArticle($organisation_article->id,$organisations_id,$famille->ref_fam);

                                            $libelle = "Activé";

                                            $organisation_articles_id = $organisation_article->id;
    
                                        }
                                    }else{
    
                                        $libelle = "Créé";
    
                                        $organisation_article = $this->storeOrganisationArticle($organisations_id,$famille->ref_fam);

                                        $organisation_articles_id = $organisation_article->id;
                                    }
    
                                    if (isset($organisation_articles_id)) {
    
                                        
                                        $commentaire = null;
                                        
                                        $type_statut_organisation_article = $this->getTypeStatutOrganisationArticle($libelle);

                                        if ($type_statut_organisation_article!=null) {

                                            $type_statut_org_articles_id = $type_statut_organisation_article->id;
                                
                                        }else{
                                            $type_statut_org_article = $this->storeTypeStatutOrganisationArticle($libelle);

                                            $type_statut_org_articles_id = $type_statut_org_article->id;
                                        }

                                        if (isset($type_statut_org_articles_id)) {

                                            $this->setLastStatutOrganisationArticle($organisation_articles_id);

                                            $this->storeStatutOrganisationArticle($organisation_articles_id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);

                                        }
    
                                    }
    
                                }
                                
    
                            }
                        }

                    }
                
                
            }
        }

        //depot
        if (isset($organisations_id)) {
            if (isset($request->all_depot)) {

                $depots = $this->getDepots();

                foreach ($depots as $depot) {

                    $organisation_depot = $this->getOrganisationDepotRefDepot($organisations_id,$depot->ref_depot);                    

                    if ($organisation_depot!=null) {

                        if ($organisation_depot->flag_actif==1) {

                        }else{

                            $this->setOrganisationDepot($organisation_depot->id,$organisations_id,$depot->ref_depot);

                            $libelle = "Activé";

                            $organisation_depots_id = $organisation_depot->id;

                        }
                    } else {
                        $libelle = "Crée";

                        $organisation_depot = $this->storeOrganisationDepot($organisations_id,$depot->ref_depot);

                        $organisation_depots_id = $organisation_depot->id;
                    }

                    if (isset($organisation_depots_id)) {

                        $commentaire = null;

                        $type_statut_organisation_depot = $this->getTypeStatutOrganisationDepot($libelle);

                        if ($type_statut_organisation_depot!=null) {

                            $type_statut_org_depots_id = $type_statut_organisation_depot->id;
                
                        }else{
                            $type_statut_org_depot = $this->storeTypeStatutOrganisationDepot($libelle);

                            $type_statut_org_depots_id = $type_statut_org_depot->id;
                        }

                        if (isset($type_statut_org_depots_id)) {

                            $this->setLastStatutOrganisationDepot($organisation_depots_id);

                            $this->storeStatutOrganisationDepot($organisation_depots_id,$type_statut_org_depots_id,Session::get('profils_id'),$commentaire);

                        }

                    }
                }

            }else{
                if (isset($request->depot)) {
                    if (count($request->depot) > 0) {

                        foreach ($request->depot as $item => $value) {

                            $depot_saisie = $this->getDepotDesignDep($request->depot[$item]);

                            if ($depot_saisie!=null) {

                                $organisation_depot = $this->getOrganisationDepotRefDepot($organisations_id,$depot->ref_depot);
            
                                if ($organisation_depot!=null) {
                                    
                                    if ($organisation_depot->flag_actif==1) {
            
                                    } else {
                                        $this->setOrganisationDepot($organisation_depot->id,$organisations_id,$depot_saisie->ref_depot);

                                        $libelle = "Activé";

                                        $organisation_depots_id = $organisation_depot->id;
                                    }
                                } else {
                                    $libelle = "Crée";
            
                                    $organisation_depot = $this->storeOrganisationDepot($organisations_id,$depot->ref_depot);

                                    $organisation_depots_id = $organisation_depot->id;

                                }

                                if (isset($organisation_depots_id)) {
                                    $libelle = "Créé";
                                    $commentaire = null;
                                    
                                    $type_statut_organisation_depot = $this->getTypeStatutOrganisationDepot($libelle);

                                    if ($type_statut_organisation_depot!=null) {

                                        $type_statut_org_depots_id = $type_statut_organisation_depot->id;
                            
                                    }else{
                                        $type_statut_org_depot = $this->storeTypeStatutOrganisationDepot($libelle);

                                        $type_statut_org_depots_id = $type_statut_org_depot->id;
                                    }

                                    if (isset($type_statut_org_depots_id)) {

                                        $this->setLastStatutOrganisationDepot($organisation_depots_id);

                                        $this->storeStatutOrganisationDepot($organisation_depots_id,$type_statut_org_depots_id,Session::get('profils_id'),$commentaire);

                                    }

                                }

                            }
                            

                        }


                    }# code...
                }
            }
        }

        $nom_prenoms = explode(' ',$request->nom_prenoms) ;
        $prenoms='';

        for ($i=1; $i < 4; $i++) { 
            if (isset($nom_prenoms[$i])) {
                $prenoms .= $nom_prenoms[$i].' '; 
            }
        }
        $prenoms = trim($prenoms);
        
        
        

        if ($organisations_id!=null) {

            if (isset($profils_id)) {

                $libelle = "Attribué";
                $commentaire = null;
                $this->statut_profil(Session::get('profils_id'),$libelle,$profils_id,$commentaire);

                if (isset($request->statut)) {
                    $libelle = "Activé";
                }else{
                    $libelle = "Désactivé";
                }
    
                $this->statut_organisation($organisations_id,$libelle,$profils_id,Session::get('profils_id'));
            }

            return redirect()->back()->with('success','Enregistrement effectué');
        }else{
            return redirect()->back()->with('error','Echec inscrit');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function show(Organisation $organisation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function edit($organisation)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($organisation);
        } catch (DecryptException $e) {
            //
        }

        $organisation = Organisation::findOrFail($decrypted);
        
        //Structure
        $depots = $this->getDepots();

        $organisation_articles = $this->getOrganisationArticle($organisation->id);       
        
        $organisation_depots = $this->getOrganisationDepot($organisation->id);  

        

        

        $nbre_fam_rattache = count($organisation_articles);
        
        $nbre_fam = count(Famille::all());
        if ($nbre_fam_rattache == $nbre_fam) {
            $all_famille = 1;
        }else{
            $all_famille = null;
        }

        $nbre_depot_rattache = count($organisation_depots);
        $nbre_depot = count(Depot::all());
        if ($nbre_depot_rattache == $nbre_depot) {
            $all_depot = 1;
        }else{
            $all_depot = null;
        }
        //determiner le nombre de fournisseurs
        $fournisseur = $this->getTypeProfilFournisseur();
        

        $organisations = $this->getOrganisation2($organisation->id);

        

        $familles = Famille::all();

        $num_fournisseur = count($fournisseur) + 1;
        
        return view('organisations.edit',[
            'depots' => $depots,
            'num_fournisseur' => $num_fournisseur,
            'organisations' => $organisations,
            'organisation_articles' => $organisation_articles,
            'all_famille' => $all_famille,
            'familles' => $familles,
            'organisation_depots'=>$organisation_depots,
            'all_depot'=>$all_depot
        ]);
    }

    public function edits($organisation,$entnum=null)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($organisation);
        } catch (DecryptException $e) {
            //
        }

        $organisation = Organisation::findOrFail($decrypted);


        //organisation secu

        $organisation_secu = null;
        if(isset($entnum)){
            $blok = explode('81045',$entnum);

            if (isset($blok[1])) {

                $blok2 = explode('y65300',$blok[1]);

                if (isset($blok2[0])) {
                    $entnum = $blok2[0];
                }

            }

            $organisation_secu = $this->getEntrepriseSecu($entnum);
        }

        
        //Structure
        $depots = $this->getDepots();

        $organisation_articles = $this->getOrganisationArticle($organisation->id);       
        
        $organisation_depots = $this->getOrganisationDepot($organisation->id);  

        

        

        $nbre_fam_rattache = count($organisation_articles);
        
        $nbre_fam = count(Famille::all());
        if ($nbre_fam_rattache == $nbre_fam) {
            $all_famille = 1;
        }else{
            $all_famille = null;
        }

        $nbre_depot_rattache = count($organisation_depots);
        $nbre_depot = count(Depot::all());
        if ($nbre_depot_rattache == $nbre_depot) {
            $all_depot = 1;
        }else{
            $all_depot = null;
        }
        //determiner le nombre de fournisseurs
        $fournisseur = $this->getTypeProfilFournisseur();

        $organisations = $this->getOrganisation2($organisation->id);

        

        $familles = Famille::all();

        $num_fournisseur = count($fournisseur) + 1;
        
        return view('organisations.edit',[
            'depots' => $depots,
            'num_fournisseur' => $num_fournisseur,
            'organisations' => $organisations,
            'organisation_articles' => $organisation_articles,
            'all_famille' => $all_famille,
            'familles' => $familles,
            'organisation_depots'=>$organisation_depots,
            'all_depot'=>$all_depot,
            'organisation_secu'=>$organisation_secu
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organisation $organisation)
    {
        if (Session::has('profils_id')) {

            $etape = "store";

            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }else{
                return redirect()->back()->with('error','Accès refusé');
            }

            $type_profils_lists = ['Administrateur fonctionnel'];

            $profil = $this->controlleurUser(Session::get('profils_id'),$type_profils_lists);

            if($profil!=null){

            }else{
                return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $user_fournisseur = User::where('agents_id', $request->agents_id)->first();
        if ($user_fournisseur!=null) {
            $user_fournisseur_id = $user_fournisseur->id;
        } else {
            $user_fournisseur_id = 0;
        }

        $request->validate([
            'agents_id' => 'required|numeric',
            'depot' => 'required|array',
            'fam_rattache' => 'required|array',
            'entnum' => ['nullable','numeric',Rule::unique('organisations')->ignore($request->organisations_id)],
            'mle' => ['required','string',Rule::unique('agents')->ignore($request->agents_id)],
            'organisations_id' => ['required','numeric'],
            'nom_prenoms' => ['required','string'],
            'email' => ['required','email',Rule::unique('users')->ignore($user_fournisseur_id)],
            'denomination' => ['required','string'],
            'num_contribuable' => ['nullable','string'],
            'adresse' => ['nullable','string'],
        ]);

        if (isset($request->entnum)) {
            $username = 'n'.$request->entnum;
        }else{
            $username = 'n'.explode('frs',$request->mle)[1];
        }
        
        $u = 0;
        
        if (isset($request->all_famille)) {

            $u=1;

        }else{

            if (isset($request->fam_rattache)) {
                foreach ($request->fam_rattache as $item => $value) {
                    if ($request->fam_rattache[$item] !=null) {

                        $famille = $this->getFamilleByDesignFam($request->fam_rattache[$item]);
                        
                        if ($famille!=null) {
                            $u=1;

                            break;
                        }

                    }
                }
                
            }

        }

        if ($u === 0) {
            // return redirect()->back()->with('error','Veuillez rattacher le fournisseur à au moins une famille d\'articles');
        }


        $d = 0;
        
        if (isset($request->all_depot)) {

            $d=1;

        }else{

            if (isset($request->depot)) {
                foreach ($request->depot as $item => $value) {
                    if ($request->depot[$item] !=null) {

                        $depot = $this->getDepotDesignDep($request->depot[$item]);
                        
                        if ($depot!=null) {
                            $d=1;
                            break;
                        }

                    }
                }
                
            }

        }

        if ($d === 0) {
            // return redirect()->back()->with('error','Veuillez rattacher le fournisseur à au moins un dépôt d\'articles');
        }

        //insert agent
        $this->setAgent($request->mle,$request->nom_prenoms,$request->agents_id);

        $agent = $this->getAgent2($request->mle);

        if ($agent === null) {
            return redirect()->back()->with('error','Agent introuvable');
        }

        $password = $username;

        $this->setUser($agent->id,$request->email,$password,$username);

        $user = $this->getUserByAgentId($agent->id);

        if ($user === null) {
            return redirect()->back()->with('error','Agent introuvable');
        }

        $type_profils_name_fournisseur = 'Fournisseur';
        //profil

        $type_profils_id = null;

        $type_profil = $this->getTypeProfilByName($type_profils_name_fournisseur);

        if ($type_profil!=null) {
            $type_profils_id = $type_profil->id;
        }else{
            $this->storeTypeProfilByName($type_profils_name_fournisseur);

            $type_profil = $this->getTypeProfilByName($type_profils_name_fournisseur);

            if ($type_profil!=null) {
                $type_profils_id = $type_profil->id;
            }
        }

        if(isset($type_profils_id)) {

            $flag_actif = 1;
            
            $this->setProfil($type_profils_name_fournisseur,$user->id,$flag_actif);

            $prof = $this->getProfil($user->id,$type_profils_id);

            if ($prof != null) {
                $profils_id = $prof->id;
            }    

        }

        $type_organisation = $this->getTypeOrganisation($type_profils_name_fournisseur);       

        if ($type_organisation != null) {

            $type_organisations_id = $type_organisation->id;

        }else{

            $type_organisation =  $this->storeTypeOrganisation($type_profils_name_fournisseur);

            $type_organisations_id = $type_organisation->id;

        }
        
        if (isset($request->entnum)) {
            $this->setOrganisation($request->entnum,$request->denomination,$request->nom_prenoms,$type_organisations_id,$request->contacts,$request->adresse,$request->num_contribuable,$request->organisations_id);
        }else{
            $this->setOrganisation(null,$request->denomination,$request->nom_prenoms,$type_organisations_id,$request->contacts,$request->adresse,$request->num_contribuable,$request->organisations_id);
        }
        

        $organisations_id = $request->organisations_id;               
        
        if (isset($organisations_id)) {

            $organisation_articles = $this->getOrganisationArticles($organisations_id);

            foreach ($organisation_articles as $organisation_articl) {

                $libelle = "Désactivé";
                $commentaire = null;

                OrganisationArticle::where('id',$organisation_articl->id)->update([
                    'flag_actif'=>0
                ]);

                $type_statut_organisation_article = $this->getTypeStatutOrganisationArticle($libelle);

                if ($type_statut_organisation_article!=null) {

                    $type_statut_org_articles_id = $type_statut_organisation_article->id;

                }else{
                    $type_statut_org_article = $this->storeTypeStatutOrganisationArticle($libelle);

                    $type_statut_org_articles_id = $type_statut_org_article->id;
                }

                if (isset($type_statut_org_articles_id)) {

                    $this->setLastStatutOrganisationArticle($organisation_articl->id);

                    $this->storeStatutOrganisationArticle($organisation_articl->id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);

                }
            }

            if (isset($request->all_famille)) {

                $familles = $this->getFamille();

                foreach ($familles as $famille) {

                    $organisation_article = $this->getOrganisationArticleByOrganisationByRefFam($organisations_id,$famille->ref_fam);                    

                    if ($organisation_article != null) {

                        if ($organisation_article->flag_actif==1) {
                            
                        }else{

                            $this->setOrganisationArticle($organisation_article->id,$organisations_id,$famille->ref_fam);

                            $libelle = "Activé";

                            $organisation_articles_id = $organisation_article->id;

                        }
                    }else{

                        $libelle = "Crée";

                        $organisation_article = $this->storeOrganisationArticle($organisations_id,$famille->ref_fam);

                        $organisation_articles_id = $organisation_article->id;
                    }

                    if (isset($organisation_articles_id)) {

                                    
                        $commentaire = null;

                        $type_statut_organisation_article = $this->getTypeStatutOrganisationArticle($libelle);

                        if ($type_statut_organisation_article!=null) {

                            $type_statut_org_articles_id = $type_statut_organisation_article->id;
                
                        }else{
                            $type_statut_org_article = $this->storeTypeStatutOrganisationArticle($libelle);

                            $type_statut_org_articles_id = $type_statut_org_article->id;
                        }

                        if (isset($type_statut_org_articles_id)) {

                            $this->setLastStatutOrganisationArticle($organisation_articles_id);

                            $this->storeStatutOrganisationArticle($organisation_articles_id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);

                        }

                    }

                }
            }else{
                
                    foreach ($request->fam_rattache as $item => $value) {

                        if ($request->fam_rattache[$item] !=null) {
                            $famille = $this->getFamilleByDesignFam($request->fam_rattache[$item]);
                            if ($famille!=null) {
                                $ref_fam = $famille->ref_fam;
    
                                if ($ref_fam!=null) {

                                    $organisation_article = $this->getOrganisationArticleByOrganisationByRefFam($organisations_id,$famille->ref_fam);

                                    if ($organisation_article!=null) {
    
                                        if ($organisation_article->flag_actif==1) {
                                            
                                        }else{

                                            $this->setOrganisationArticle($organisation_article->id,$organisations_id,$famille->ref_fam);

                                            $libelle = "Activé";

                                            $organisation_articles_id = $organisation_article->id;
    
                                        }
                                    }else{
    
                                        $libelle = "Créé";
    
                                        $organisation_article = $this->storeOrganisationArticle($organisations_id,$famille->ref_fam);

                                        $organisation_articles_id = $organisation_article->id;
                                    }
    
                                    if (isset($organisation_articles_id)) {
    
                                        
                                        $commentaire = null;
                                        
                                        $type_statut_organisation_article = $this->getTypeStatutOrganisationArticle($libelle);

                                        if ($type_statut_organisation_article!=null) {

                                            $type_statut_org_articles_id = $type_statut_organisation_article->id;
                                
                                        }else{
                                            $type_statut_org_article = $this->storeTypeStatutOrganisationArticle($libelle);

                                            $type_statut_org_articles_id = $type_statut_org_article->id;
                                        }

                                        if (isset($type_statut_org_articles_id)) {

                                            $this->setLastStatutOrganisationArticle($organisation_articles_id);

                                            $this->storeStatutOrganisationArticle($organisation_articles_id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);

                                        }
    
                                    }
    
                                }
                                
    
                            }
                        }

                    }
                
                
            }
        }

        //depot
        if (isset($organisations_id)) {

            $organisation_depots = $this->getOrganisationDepots($organisations_id);

            foreach ($organisation_depots as $organisation_depo) {
                
                $libelle = "Désactivé";
                $commentaire = null;

                OrganisationDepot::where('id',$organisation_depo->id)->update([
                    'flag_actif'=>0
                ]);

                $type_statut_organisation_depot = $this->getTypeStatutOrganisationDepot($libelle);

                if ($type_statut_organisation_depot!=null) {

                    $type_statut_org_articles_id = $type_statut_organisation_depot->id;

                }else{
                    $type_statut_org_article = $this->storeTypeStatutOrganisationDepot($libelle);

                    $type_statut_org_articles_id = $type_statut_org_article->id;
                }

                if (isset($type_statut_org_articles_id)) {

                    $this->setLastStatutOrganisationDepot($organisation_depo->id);

                    $this->storeStatutOrganisationDepot($organisation_depo->id,$type_statut_org_articles_id,Session::get('profils_id'),$commentaire);

                }
            }

            if (isset($request->all_depot)) {

                $depots = $this->getDepots();

                foreach ($depots as $depot) {

                    $organisation_depot = $this->getOrganisationDepotRefDepot($organisations_id,$depot->ref_depot);                    

                    if ($organisation_depot!=null) {

                        if ($organisation_depot->flag_actif==1) {

                        }else{

                            $this->setOrganisationDepot($organisation_depot->id,$organisations_id,$depot->ref_depot);

                            $libelle = "Activé";

                            $organisation_depots_id = $organisation_depot->id;

                        }
                    } else {
                        $libelle = "Crée";

                        $organisation_depot = $this->storeOrganisationDepot($organisations_id,$depot->ref_depot);

                        $organisation_depots_id = $organisation_depot->id;
                    }

                    if (isset($organisation_depots_id)) {

                        $commentaire = null;

                        $type_statut_organisation_depot = $this->getTypeStatutOrganisationDepot($libelle);

                        if ($type_statut_organisation_depot!=null) {

                            $type_statut_org_depots_id = $type_statut_organisation_depot->id;
                
                        }else{
                            $type_statut_org_depot = $this->storeTypeStatutOrganisationDepot($libelle);

                            $type_statut_org_depots_id = $type_statut_org_depot->id;
                        }

                        if (isset($type_statut_org_depots_id)) {

                            $this->setLastStatutOrganisationDepot($organisation_depots_id);

                            $this->storeStatutOrganisationDepot($organisation_depots_id,$type_statut_org_depots_id,Session::get('profils_id'),$commentaire);

                        }

                    }
                }

            }else{
                if (isset($request->depot)) {
                    if (count($request->depot) > 0) {

                        foreach ($request->depot as $item => $value) {

                            $depot_saisie = $this->getDepotDesignDep($request->depot[$item]);

                            if ($depot_saisie!=null) {

                                $organisation_depot = $this->getOrganisationDepotRefDepot($organisations_id,$depot->ref_depot);
            
                                if ($organisation_depot!=null) {
                                    
                                    if ($organisation_depot->flag_actif==1) {
            
                                    } else {
                                        $this->setOrganisationDepot($organisation_depot->id,$organisations_id,$depot_saisie->ref_depot);

                                        $libelle = "Activé";

                                        $organisation_depots_id = $organisation_depot->id;
                                    }
                                } else {
                                    $libelle = "Crée";
            
                                    $organisation_depot = $this->storeOrganisationDepot($organisations_id,$depot->ref_depot);

                                    $organisation_depots_id = $organisation_depot->id;

                                }

                                if (isset($organisation_depots_id)) {
                                    $libelle = "Créé";
                                    $commentaire = null;
                                    
                                    $type_statut_organisation_depot = $this->getTypeStatutOrganisationDepot($libelle);

                                    if ($type_statut_organisation_depot!=null) {

                                        $type_statut_org_depots_id = $type_statut_organisation_depot->id;
                            
                                    }else{
                                        $type_statut_org_depot = $this->storeTypeStatutOrganisationDepot($libelle);

                                        $type_statut_org_depots_id = $type_statut_org_depot->id;
                                    }

                                    if (isset($type_statut_org_depots_id)) {

                                        $this->setLastStatutOrganisationDepot($organisation_depots_id);

                                        $this->storeStatutOrganisationDepot($organisation_depots_id,$type_statut_org_depots_id,Session::get('profils_id'),$commentaire);

                                    }

                                }

                            }
                            

                        }


                    }# code...
                }
            }
        }

        $nom_prenoms = explode(' ',$request->nom_prenoms) ;
        $prenoms='';

        for ($i=1; $i < 4; $i++) { 
            if (isset($nom_prenoms[$i])) {
                $prenoms .= $nom_prenoms[$i].' '; 
            }
        }
        $prenoms = trim($prenoms);
        
        
        

        if ($organisations_id!=null) {

            if (isset($profils_id)) {

                $libelle = "Attribué";
                $commentaire = null;
                $this->statut_profil(Session::get('profils_id'),$libelle,$profils_id,$commentaire);

                if (isset($request->statut)) {
                    $libelle = "Activé";
                }else{
                    $libelle = "Désactivé";
                }
    
                $this->statut_organisation($organisations_id,$libelle,$profils_id,Session::get('profils_id'));
            }

            return redirect()->back()->with('success','Modification effectuée');
        }else{
            return redirect()->back()->with('error','Echec inscrit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation)
    {
        //
    }
}
