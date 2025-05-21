<?php

namespace App\Http\Controllers;

use App\Models\Taxe;
use App\Models\Unite;
use App\Models\Article;
use App\Models\Famille;
use App\Models\TypeArticle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ArticleController extends Controller
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
        /*
            $familles = Famille::orderBy('ref_fam')->get();
            foreach ($familles as $famille) {

                $i = 1;

                $articles = Article::orderBy('design_article')
                ->where('ref_fam',$famille->ref_fam)
                ->get();
                foreach ($articles as $article) {
                    $articles_id = $article->id;
                    $ref_articles = $article->ref_fam.''.$i;

                    Article::where('id',$articles_id)->update([
                        'ref_articles'=>$ref_articles
                    ]);


                    $i++;
                }

            }
        */
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        } 

        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }

        $acces_create = null;
        if ($type_profils_name === 'Administrateur fonctionnel' or $type_profils_name === 'Responsable des achats' or $type_profils_name === 'Gestionnaire des achats' or $type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks') {
            $acces_create = 1;
        }

        $familles = Famille::all();

        $articles = DB::table('articles as a')
                        ->join('type_articles as ta','ta.id','=','a.type_articles_id')
                        ->join('familles as f','f.ref_fam','=','a.ref_fam')
                        ->select('a.id','a.ref_articles','a.design_article','f.ref_fam','f.design_fam','a.flag_actif','a.updated_at','a.code_unite','ta.design_type')
                        ->orderByDesc('a.updated_at')
                        ->get();
        

        return view('articles.index',[
            'familles'=>$familles,
            'articles'=>$articles,
            'acces_create'=>$acces_create
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        $articles = DB::table('articles')
                        ->join('familles','familles.ref_fam','=','articles.ref_fam')
                        ->join('unites','unites.code_unite','=','articles.code_unite')
                        ->get();

        $articls = DB::table('articles')
                        ->join('familles','familles.ref_fam','=','articles.ref_fam')
                        ->join('unites','unites.code_unite','=','articles.code_unite')
                        ->get();
        
        $familles = Famille::all();
        $unites = Unite::all(); 
        $type_articles = TypeArticle::all();

        return view('articles.create',[
            'articles'=>$articles,
            'familles'=>$familles,
            'unites'=>$unites,
            'type_articles'=>$type_articles,
            'articls'=>$articls,
        ]);
    }

    public function creates($famille)
    {
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        // $decrypted = null;
        // try {
        //     $decrypted = Crypt::decryptString($famille);
        // } catch (DecryptException $e) {
        //     //
        // }

        // $famille = Famille::findOrFail($decrypted);

        $famille = Famille::findOrFail($famille);

        $articles = DB::table('articles')
                        ->join('familles','familles.ref_fam','=','articles.ref_fam')
                        ->join('unites','unites.code_unite','=','articles.code_unite')
                        ->get();

        $famille_article = DB::table('familles as f')
                        ->where('f.id',$famille->id)
                        ->first();
 

        $ref_articles = $this->generateRefArticle($famille->ref_fam);            

        
        
        $articls = DB::table('articles')
                        ->join('familles','familles.ref_fam','=','articles.ref_fam')
                        ->join('unites','unites.code_unite','=','articles.code_unite')
                        ->get();
        
        $familles = Famille::all();
        $unites = Unite::all(); 
        $type_articles = TypeArticle::all();

        //dd($famille_article);

        return view('articles.create',[
            'articles'=>$articles,
            'familles'=>$familles,
            'unites'=>$unites,
            'type_articles'=>$type_articles,
            'articls'=>$articls,
            'famille_article'=>$famille_article,
            'ref_articles'=>$ref_articles
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
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        $validate = $request->validate([
            'ref_articles'=>['required','string','unique:articles'],
            'design_article'=>['required','string','unique:articles'],
            'design_type'=>['required','string'],
            'design_fam'=>['required','string'],
            'unite'=>['required','string'],
        ]);

        
        


        $ref_fam = Famille::where('design_fam',$request->design_fam)->first()->ref_fam;
        
        $code_unite = Unite::where('unite',$request->unite)->first()->code_unite;

        if ($request->design_type!=null) {
            $type_articles = TypeArticle::where('design_type',$request->design_type)->first();
            //dd($type_articles);
            if ($type_articles===null) {
                $type_article = TypeArticle::create([
                    'design_type'=>$request->design_type,
                ]);

                $type_articles_id = TypeArticle::where('design_type',$type_article->design_type)->first()->id;
            }else{
               $type_articles_id = $type_articles->id; 
            }
        }else{
            $type_articles_id = null;
        }

        $data = [
            'ref_articles'=>$request->ref_articles,
            'design_article'=>$request->design_article,
            'type_articles_id'=>$type_articles_id,
            'ref_fam'=>$ref_fam,
            'code_unite'=>$code_unite,
        ];

        $article = Article::create($data);

        if ($article!=null) {
            return redirect('articles/index')->with('success','Enregistrement reussi');
        }else{
            return redirect()->back()->with('error','Enregistrement echoué');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit($article)
    {
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->where('profils.id',Session::get('profils_id'))
                      ->first();
        if($profil===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($article);
        } catch (DecryptException $e) {
            //
        }

        $articles = Article::findOrFail($decrypted);

        $article = DB::table('articles')
        ->join('familles','familles.ref_fam','=','articles.ref_fam')
        ->join('unites','unites.code_unite','=','articles.code_unite')
        ->select('articles.*','familles.ref_fam','familles.design_fam','unites.code_unite','unites.unite')
        ->where('articles.id',$articles->id)
        ->first();


        $familles = Famille::all();
        $unites = Unite::all(); 
        $taxes = Taxe::all();
        $type_articles = TypeArticle::all();

        return view('articles.edit',[
        'article'=>$article,
        'familles'=>$familles,
        'unites'=>$unites,
        'taxes'=>$taxes,
        'type_articles'=>$type_articles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $validate = $request->validate([
            'ref_articles'=>['required','string',Rule::unique('articles')->ignore($request->id)],
            'design_article'=>['required','string',Rule::unique('articles')->ignore($request->id)],
            'design_type'=>['required','string'],
            'design_fam'=>['required','string'],
            'unite'=>['required','string'],
        ]);

        /*$block_taxe = explode(' - ',$request->taxe);
        $nom_taxe = $block_taxe[0];
        $taxe = $block_taxe[1];*/

        //$ref_taxe = Taxe::where('nom_taxe',$nom_taxe)->first()->ref_taxe;
        $ref_taxe = null;

        $ref_fam = Famille::where('design_fam',$request->design_fam)->first()->ref_fam;
        
        $code_unite = Unite::where('unite',$request->unite)->first()->code_unite;

        
        if ($request->design_type!=null) {

            $type_articles = TypeArticle::where('design_type',$request->design_type)->first();
            //dd($type_articles);

            
            if ($type_articles===null) {


                $type_article = TypeArticle::create([
                    'design_type'=>$request->design_type,
                ]);


                $type_articles_id = TypeArticle::where('design_type',$type_article->design_type)->first()->id;

            }else{
               $type_articles_id = $type_articles->id; 
            }
        }else{
            $type_articles_id = null;
        }

        

        $ref_articles = $request->ref_articles;


        if($request->old_ref_fam != $ref_fam){
            $ref_articles = $this->generateRefArticle($ref_fam);
        }

        if(isset($request->flag_actif)){
            $flag_actif = 1;
        }else{
            $flag_actif = 0;
        }

        $data = [
            'ref_articles'=>$ref_articles,
            'design_article'=>$request->design_article,
            'type_articles_id'=>$type_articles_id,
            'ref_fam'=>$ref_fam,
            'code_unite'=>$code_unite,
            'ref_taxe'=>$ref_taxe,
            'flag_actif'=>$flag_actif,
        ];

        $article = Article::where('id',$request->id)->update($data);

        if ($article!=null) {
            return redirect('articles/index')->with('success','Modification reussie');
        }else{
            return redirect('articles/create')->with('error','Modification echouée');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $user_id = auth()->user()->id; // utilisateur connecté
        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', (['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Gestionnaire des stocks','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->first();
        if($profil===null){
            return redirect()->back();
        }

        $article = Article::where('id',$article->id)->delete();
        if ($article!=null) {
            return redirect()->back()->with('success','Suppression reussie');
        }else{
            return redirect()->back()->with('success','Suppression echoué');
        }
    }
}
