<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\Famille;
use App\Models\Mouvement;
use App\Models\Inventaire;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use App\Models\TypeMouvement;
use App\Models\StatutInventaire;
use App\Models\InventaireArticle;
use Illuminate\Support\Facades\DB;
use App\Models\TypeStatutInventaire;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class InventaireArticleController extends Controller
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

    public function index($inventaire)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($inventaire);
        } catch (DecryptException $e) {
            //
        }

        $inventaire = Inventaire::findOrFail($decrypted);

        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        //determiner le dépôt
        $design_dep = null;
        $depot = DB::table('agents as a')
                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                    ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                    ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                    ->join('sections as s','s.id','=','ase.sections_id')
                    ->join('structures as st','st.code_structure','=','s.code_structure')
                    ->join('depots as d','d.ref_depot','=','st.ref_depot')
                    ->where('tsase.libelle','Activé')
                    ->where('a.id',auth()->user()->agents_id)
                    ->first(); 
        if ($depot!=null) {
            $design_dep = $depot->design_dep;
        }

        $inventaires = DB::table('inventaires as i')
            ->join('inventaire_articles as ia','ia.inventaires_id','=','i.id')
            ->join('magasin_stocks as ms','ms.id','=','ia.magasin_stocks_id')
            ->join('articles as ar','ar.ref_articles','=','ms.ref_articles')
            ->join('familles as f','f.ref_fam','=','ar.ref_fam')

            ->join('profils as p','p.id','=','ia.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            
            
            ->select('ia.id','i.id as inventaires_id','ar.ref_articles','ar.design_article','ia.qte_theo','ia.qte_phys','ia.ecart','ia.justificatif','ia.flag_valide','ia.flag_integre','a.mle','a.nom_prenoms','ia.updated_at','f.design_fam','ia.cmup_inventaire')
            ->orderByDesc('i.id')
            ->where('i.id',$inventaire->id)
            ->whereIn('i.ref_depot',function($query){
                $query->select(DB::raw('st.ref_depot'))
                        ->from('structures as st')
                        ->join('sections as s','s.code_structure','=','st.code_structure')
                        ->join('agent_sections as ase','ase.sections_id','=','s.id')
                        ->join('agents as a','a.id','=','ase.agents_id')
                        ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                        ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                        ->where('tsase.libelle','Activé')
                        ->orderByDesc('sase.id')
                        ->where('ase.agents_id',auth()->user()->agents_id)
                        ->whereRaw('i.ref_depot = st.ref_depot');
            })
            ->get();

            


         $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));
        return view('inventaire_articles.index',[
            'design_dep' => $design_dep,
            'debut_per' => $inventaire->debut_per,
            'fin_per' => $inventaire->fin_per,
            'inventaires'=>$inventaires,
            'type_profils_name'=>$type_profils_name
        ]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($inventaire)
    {
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($inventaire);
        } catch (DecryptException $e) {
            //
        }

        $inventaire = Inventaire::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $profil_responsable_stock = DB::table('profils')
            ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
            ->where('profils.users_id', $user_id)
            ->whereIn('type_profils.name', (['Responsable des stocks']))
            ->limit(1)
            ->select('profils.id')
            ->where('profils.id',Session::get('profils_id'))
            ->first();

        $familles = Famille::all();


        $magasin_stocks = [];

        $datalist_magasin_stocks = [];

        $depot = Depot::where('ase.agents_id',auth()->user()->agents_id)
        ->join('structures as s','s.ref_depot','=','depots.ref_depot')
        ->join('sections as se','se.code_structure','=','s.code_structure')
        ->join('agent_sections as ase','ase.sections_id','=','se.id')
        ->select('depots.ref_depot','design_dep')
        ->first();

                
        $nbre_ligne_inventaire = 0;

        return view('inventaire_articles.create',[
        'magasin_stocks' => $magasin_stocks,
        'datalist_magasin_stocks' => $datalist_magasin_stocks,
        'debut_per' => $inventaire->debut_per,
        'fin_per' => $inventaire->fin_per,
        'id' => $inventaire->id,
        'familles'=>$familles,
        'depot'=>$depot,
        'profil_responsable_stock'=>$profil_responsable_stock,
        'nbre_ligne_inventaire'=>$nbre_ligne_inventaire,

        ]);
    }

    public function create_inventaire_famille(Request $request)
    {
        
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back();
        }
        
        $id = $request->inventaires_id;
        $famille = Famille::where('design_fam',$request->design_fam)->first();
        if ($famille!=null) {
            $ref_fam = $famille->ref_fam;
        }
        $design_fams = $request->design_fam;
        $debut_per = Inventaire::where('id',$id)->first()->debut_per;
        $fin_per = Inventaire::where('id',$id)->first()->fin_per;

        //dd($id,$ref_fam,$debut_per,$fin_per);

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $profil_responsable_stock = DB::table('profils')
            ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
            ->where('profils.users_id', $user_id)
            ->whereIn('type_profils.name', (['Responsable des stocks']))
            ->limit(1)
            ->select('profils.id')
            ->where('profils.id',Session::get('profils_id'))
            ->first();
        

        $familles = Famille::all();


        $magasin_stocks = DB::table('inventaires as i')
        ->join('inventaire_articles as ia','ia.inventaires_id','=','i.id')
        ->join('magasin_stocks as ms','ms.id','=','ia.magasin_stocks_id')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
        ->join('depots as d','d.id','=','m.depots_id')
        ->where('a.ref_fam',$ref_fam)
        ->where('i.id',$id)
        ->select('a.*','m.*','ms.*')
        ->whereIn('d.ref_depot', function($query){
            $query->select(DB::raw('s.ref_depot'))
                  ->from('structures as s')
                  ->join('sections as se','se.code_structure','=','s.code_structure')
                  ->join('agent_sections as ase','ase.sections_id','=','se.id')
                  ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                  ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                  ->where('tsase.libelle','Activé')
                  ->where('ase.agents_id',auth()->user()->agents_id)
                  ->whereRaw('s.ref_depot = d.ref_depot');
        })
        ->get();

        $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
        if ($infoUserConnect != null) {
            $ref_depot = $infoUserConnect->ref_depot;

            $magasin = $this->getMagasinByIdDepot($ref_depot);

            if ($magasin != null) {

                $articles = $this->getArticlesNotInMagasinStock($magasin->ref_magasin,$ref_fam);

                foreach ($articles as $article) {

                    $this->getMagasinStock($magasin->ref_magasin, $article->ref_articles);

                }

            }   



        }

        $datalist_magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->where('a.ref_fam',$ref_fam)
                    ->select('a.*','m.*','ms.*')
                    ->whereIn('d.ref_depot', function($query){
                        $query->select(DB::raw('s.ref_depot'))
                              ->from('structures as s')
                              ->join('sections as se','se.code_structure','=','s.code_structure')
                              ->join('agent_sections as ase','ase.sections_id','=','se.id')
                              ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                              ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                              ->where('tsase.libelle','Activé')
                              ->where('ase.agents_id',auth()->user()->agents_id)
                              ->whereRaw('s.ref_depot = d.ref_depot');
                    })
                    ->get();

                    
                    //dd('ici',$datalist_magasin_stocks);

        $nbre_ligne_inventaire = count(DB::table('magasin_stocks as ms')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->where('a.ref_fam',$ref_fam)
                    ->select('a.*','m.*','ms.*')
                    ->whereIn('d.ref_depot', function($query){
                        $query->select(DB::raw('s.ref_depot'))
                              ->from('structures as s')
                              ->join('sections as se','se.code_structure','=','s.code_structure')
                              ->join('agent_sections as ase','ase.sections_id','=','se.id')
                              ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                              ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                              ->where('tsase.libelle','Activé')
                              ->where('ase.agents_id',auth()->user()->agents_id)
                              ->whereRaw('s.ref_depot = d.ref_depot');
                    })
                    ->get());
                    

                $depot = Depot::select('depots.ref_depot','design_dep')
                ->whereIn('depots.ref_depot', function($query){
                    $query->select(DB::raw('s.ref_depot'))
                          ->from('structures as s')
                          ->join('sections as se','se.code_structure','=','s.code_structure')
                          ->join('agent_sections as ase','ase.sections_id','=','se.id')
                          ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                          ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                          ->where('tsase.libelle','Activé')
                          ->where('ase.agents_id',auth()->user()->agents_id)
                          ->whereRaw('s.ref_depot = depots.ref_depot');
                })
                ->first();
                
        return view('inventaire_articles.create',[
        'magasin_stocks' => $magasin_stocks,
        'datalist_magasin_stocks' => $datalist_magasin_stocks,
        'debut_per' => $debut_per,
        'fin_per' => $fin_per,
        'id' => $id,
        'familles'=>$familles,
        'design_fams',$design_fams,
        'depot'=>$depot,
        'nbre_ligne_inventaire'=>$nbre_ligne_inventaire,
        'profil_responsable_stock'=>$profil_responsable_stock
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
            # code...
        }else{
            return redirect()->back();
        }

        $profil = $this->acces_validate(auth()->user()->id);
        
        if($profil != null){
            $profils_id = $profil->id;
        }else{
            redirect()->back()->with('error','Vous n\'avez pas le profil requis pour cette action');
        }        
        
        $validate = $request->validate([
            'ref_articles'=>['required','array'],
            'magasin_stocks_id'=>['required','array'],
            'id'=>['required','array'],
            'design_article'=>['required','array'],
            'qte_theo'=>['required','array'],
            'qte_phys'=>['required','array'],
            'ecart'=>['required','array'],
            'justificatif'=>['nullable','array'],
            'commentaire'=>['nullable','string'],
        ]);

        $ref_fam = null;
        if (isset($request->submit)) {
            if ($request->submit === 'edit') {
                $donnees = [];
                $inventaires_id = null;

                if (count($request->id) > 0) {
                    foreach ($request->id as $item => $value) {

                        if (($request->magasin_stocks_id[$item] != null) &&($request->id[$item] != null) &&($request->qte_theo[$item] != null) &&($request->qte_phys[$item] != null) && ($request->ecart[$item] != null)) {

                            $donnees[$item] = $request->magasin_stocks_id[$item];
                            $inventaires_id = $request->id[$item];

                            try {
                                if ($ref_fam === null) {

                                    $ref_fam = DB::table('magasin_stocks as ms')
                                    ->join('articles as a', 'a.ref_articles', '=', 'ms.ref_articles')
                                    ->where('ms.id',$request->magasin_stocks_id[$item])
                                    ->select('a.ref_fam')
                                    ->first()->ref_fam;

                                }
                                
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                            

                        }

                    }
                }

                if ($ref_fam != null) {
                    if ($inventaires_id!=null) {
                        $inventaires_desactives = InventaireArticle::whereNotIn('magasin_stocks_id', $donnees)
                    ->where('inventaires_id', $inventaires_id)
                    ->where('flag_integre', 0)
                    ->whereIn('inventaire_articles.magasin_stocks_id', function ($query) use($ref_fam) {
                        $query->select(DB::raw('ms.id'))
                              ->from('magasin_stocks as ms')
                              ->join('articles as a', 'a.ref_articles', '=', 'ms.ref_articles')
                              ->where('a.ref_fam', $ref_fam)
                              ->whereRaw('inventaire_articles.magasin_stocks_id = ms.id');
                    })
                    ->get();
                        foreach ($inventaires_desactives as $inventaires_desactive) {
                            StatutInventaire::where('inventaire_articles_id', $inventaires_desactive->id)->delete();
                            InventaireArticle::where('id', $inventaires_desactive->id)->delete();
                        }
                    }
                }
            }
        }
        

        $statut_inventaires = null;

        if (count($request->id) > 0) {
            
            foreach ($request->id as $item => $value) {                

                $magasin_stocks_id = null;
                $inventaires_id = null;
                $qte_theo = null;
                $qte_phys = null;
                $ecart = null;
                $justificatif = null;
                $inventaire_articles_id = null;

                if (($request->magasin_stocks_id[$item] != null) &&($request->id[$item] != null) &&($request->qte_theo[$item] != null) &&($request->qte_phys[$item] != null) && ($request->ecart[$item] != null)) {

                    if (isset($request->flag_valide[$item])) {
                        $flag_valide = true;
                    }else{
                        $flag_valide = false;
                    }

                    if (isset($request->flag_integre[$item])) {
                        $flag_integre = true;
                        $flag_valide = true;
                    }else{
                        $flag_integre = false;
                    }
                    
                    
                    $magasin_stocks_id = $request->magasin_stocks_id[$item];
                    $inventaires_id = $request->id[$item];
                    $qte_theo = $request->qte_theo[$item];
                    $qte_phys = $request->qte_phys[$item];
                    $ecart = $request->ecart[$item];
                    $justificatif = $request->justificatif[$item];

                    $inventaire_articles_id = $this->storeInventaireArticle($magasin_stocks_id,$inventaires_id,$qte_theo,$qte_phys,$ecart,$justificatif,$flag_valide,$flag_integre,$profils_id);

                    

                    if (isset($inventaire_articles_id)) {

                        $this->setInventaire($inventaire_articles_id); 
                        
                        
                        //statut inventaire (Inventorié)
                        $libelle = 'Inventorié';

                        $commentaire = $request->commentaire;

                        $statut_inventaires = $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);
                        
                        

                        

                        if ($flag_integre === true) {

                            if(isset($request->integration_definitive)){

                            }else{
                                InventaireArticle::where('inventaires_id',$inventaires_id)->update([
                                    'flag_integre' => 0,
                                ]);
                            }

                            if ($ecart!=0) {
                                //Type de movement

                                $libelle_mvt = 'Intégration d\'inventaire';
                                $type_mouvements_id = $this->storeTypeMouvement($libelle_mvt);
                                
                                $this->storeMagasinStock($type_mouvements_id,$magasin_stocks_id,$ecart,$qte_phys,$profils_id,$inventaire_articles_id);
                                

                            }

                            //statut inventaire (Validé)
                            $libelle = 'Validé';

                            $commentaire = $request->commentaire;

                            $statut_inventaires = $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);

                            //statut inventaire (Intégré)
                            $libelle = 'Intégré';

                            $commentaire = $request->commentaire;

                            $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);


                        }elseif ($flag_valide === true) {
                            //statut inventaire (Validé)
                            $libelle = 'Validé';

                            $commentaire = $request->commentaire;

                            $statut_inventaires = $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);
                        }


                    }
                                        
                    
                }

                
            }

            
        }

        if ($statut_inventaires != null) {
            return redirect()->back()->with('success','Enregistrement reussi');
        }else{
            return redirect()->back()->with('error','Enregistrement echoué');
        }


    }

    

    /**
     * Display the specified resource.
     *
     * @param  \App\InventaireArticle  $inventaireArticle
     * @return \Illuminate\Http\Response
     */
    public function show(InventaireArticle $inventaireArticle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InventaireArticle  $inventaireArticle
     * @return \Illuminate\Http\Response
     */
    public function edit($inventaireArticle)
    {     
       
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($inventaireArticle);
        } catch (DecryptException $e) {
            //
        }

        $inventaireArticle = InventaireArticle::findOrFail($decrypted);

        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $id = $inventaireArticle->inventaires_id;

        $ref_fam = null;

        $familless = DB::table('inventaires as i')
        ->join('inventaire_articles as ia','ia.inventaires_id','=','i.id')
        ->join('magasin_stocks as ms','ms.id','=','ia.magasin_stocks_id')
        ->join('articles as ar','ar.ref_articles','=','ms.ref_articles')
        ->join('familles as f','f.ref_fam','=','ar.ref_fam')
        ->where('ia.id',$inventaireArticle->id)
        ->first();

        if ($familless!=null) {

            $ref_fam = $familless->ref_fam;

        }


        
        $debut_per = Inventaire::where('id',$id)->first()->debut_per;
        $fin_per = Inventaire::where('id',$id)->first()->fin_per;

        //dd($id,$ref_fam,$debut_per,$fin_per);

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des stocks','Responsable des stocks','Responsable des achats','Administrateur fonctionnel','Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $profil_responsable_stock = DB::table('profils')
            ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
            ->where('profils.users_id', $user_id)
            ->whereIn('type_profils.name', (['Responsable des stocks']))
            ->limit(1)
            ->select('profils.id')
            ->where('profils.id',Session::get('profils_id'))
            ->first();
        

        $familles = Famille::all();


        $magasin_stocks = DB::table('inventaires as i')
        ->join('inventaire_articles as ia','ia.inventaires_id','=','i.id')
        ->join('magasin_stocks as ms','ms.id','=','ia.magasin_stocks_id')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
        ->join('depots as d','d.id','=','m.depots_id')
        ->where('a.ref_fam',$ref_fam)
        ->where('ia.id',$inventaireArticle->id)
        ->select('a.*','m.*','ms.*')
        ->whereIn('d.ref_depot', function($query){
            $query->select(DB::raw('s.ref_depot'))
                  ->from('structures as s')
                  ->join('sections as se','se.code_structure','=','s.code_structure')
                  ->join('agent_sections as ase','ase.sections_id','=','se.id')
                  ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                  ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                  ->where('tsase.libelle','Activé')
                  ->where('ase.agents_id',auth()->user()->agents_id)
                  ->whereRaw('s.ref_depot = d.ref_depot');
        })
        ->get();

        $magasin_stock2s = DB::table('inventaires as i')
        ->join('inventaire_articles as ia','ia.inventaires_id','=','i.id')
        ->join('magasin_stocks as ms','ms.id','=','ia.magasin_stocks_id')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
        ->join('depots as d','d.id','=','m.depots_id')
        ->where('a.ref_fam',$ref_fam)
        ->where('i.id',$id)
        ->whereNotIn('ia.id',[$inventaireArticle->id])
        ->select('a.*','m.*','ms.*')
        ->whereIn('d.ref_depot', function($query){
            $query->select(DB::raw('s.ref_depot'))
                  ->from('structures as s')
                  ->join('sections as se','se.code_structure','=','s.code_structure')
                  ->join('agent_sections as ase','ase.sections_id','=','se.id')
                  ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                  ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                  ->where('tsase.libelle','Activé')
                  ->where('ase.agents_id',auth()->user()->agents_id)
                  ->whereRaw('s.ref_depot = d.ref_depot');
        })
        ->get();

        


        /*$datalist_magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->where('a.ref_fam',$ref_fam)
                    ->select('a.*','m.*','ms.*')
                    ->whereIn('d.ref_depot', function($query){
                        $query->select(DB::raw('s.ref_depot'))
                              ->from('structures as s')
                              ->join('sections as se','se.code_structure','=','s.code_structure')
                              ->join('agent_sections as ase','ase.sections_id','=','se.id')
                              ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                              ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                              ->where('tsase.libelle','Activé')
                              ->where('ase.agents_id',auth()->user()->agents_id)
                              ->whereRaw('s.ref_depot = d.ref_depot');
                    })
                    ->get();*/

        $datalist_magasin_stocks = DB::table('magasin_stocks as ms')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->where('a.ref_fam',$ref_fam)
                    ->select('a.*','m.*','ms.*')
                    ->whereIn('d.ref_depot', function($query){
                        $query->select(DB::raw('s.ref_depot'))
                              ->from('structures as s')
                              ->join('sections as se','se.code_structure','=','s.code_structure')
                              ->join('agent_sections as ase','ase.sections_id','=','se.id')
                              ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                              ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                              ->where('tsase.libelle','Activé')
                              ->where('ase.agents_id',auth()->user()->agents_id)
                              ->whereRaw('s.ref_depot = d.ref_depot');
                    })
                    ->get();

        //dd($datalist_magasin_stocks,$magasin_stocks);

        $nbre_ligne_inventaire = count(DB::table('magasin_stocks as ms')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('depots as d','d.id','=','m.depots_id')
                    ->where('a.ref_fam',$ref_fam)
                    ->select('a.*','m.*','ms.*')
                    ->whereIn('d.ref_depot', function($query){
                        $query->select(DB::raw('s.ref_depot'))
                              ->from('structures as s')
                              ->join('sections as se','se.code_structure','=','s.code_structure')
                              ->join('agent_sections as ase','ase.sections_id','=','se.id')
                              ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                              ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                              ->where('tsase.libelle','Activé')
                              ->where('ase.agents_id',auth()->user()->agents_id)
                              ->whereRaw('s.ref_depot = d.ref_depot');
                    })
                    ->get());

                $depot = Depot::select('depots.ref_depot','design_dep')
                ->whereIn('depots.ref_depot', function($query){
                    $query->select(DB::raw('s.ref_depot'))
                          ->from('structures as s')
                          ->join('sections as se','se.code_structure','=','s.code_structure')
                          ->join('agent_sections as ase','ase.sections_id','=','se.id')
                          ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                          ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                          ->where('tsase.libelle','Activé')
                          ->where('ase.agents_id',auth()->user()->agents_id)
                          ->whereRaw('s.ref_depot = depots.ref_depot');
                })
                ->first();
                
        return view('inventaire_articles.edit',[ 
        'magasin_stocks' => $magasin_stocks,
        'magasin_stock2s' => $magasin_stock2s,
        'datalist_magasin_stocks' => $datalist_magasin_stocks,
        'debut_per' => $debut_per,
        'fin_per' => $fin_per,
        'id' => $id,
        'familles'=>$familles,
        'familless'=>$familless,
        'depot'=>$depot,
        'nbre_ligne_inventaire'=>$nbre_ligne_inventaire,
        'profil_responsable_stock'=>$profil_responsable_stock
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InventaireArticle  $inventaireArticle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventaireArticle $inventaireArticle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InventaireArticle  $inventaireArticle
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventaireArticle $inventaireArticle)
    {

    }

    
}
