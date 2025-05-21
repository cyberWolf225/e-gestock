<?php

namespace App\Http\Controllers;

use App\Models\Famille;
use Illuminate\Http\Request;
use App\Models\TypeMouvement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EtatController extends Controller
{
    public $controller1;
    public $controller2;
    public $controller4;
    public function __construct(Controller1 $controller1, Controller2 $controller2, Controller4 $controller4)
    {
        $this->middleware('auth');
         $this->controller1 = $controller1;
         $this->controller2 = $controller2;
         $this->controller4 = $controller4;
    }
    public function articles_compte_comptable($famille_crypt=null,$date_debut_crypt=null,$date_fin_crypt=null){
        $famille = null;
        $date_debut = null;
        $date_fin = null;
        $titre = "Etat des articles par compte comptable";
        if($famille_crypt != null){
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }

            $famille = Famille::findOrFail($decrypted);

        }

        if($date_debut_crypt != null){
            try {
                $date_debut = Crypt::decryptString($date_debut_crypt);
            } catch (DecryptException $e) {
                //
            }
        }

        if($date_fin_crypt != null){
            try {
                $date_fin = Crypt::decryptString($date_fin_crypt);
            } catch (DecryptException $e) {
                //
            }
        }

        
        $articles = [];
        if($famille != null){
            $ref_fam = $famille->ref_fam ?? '';
            $design_fam = $famille->design_fam ?? '';
            $articles = $this->getArticleByFamilleId($famille->id);
            $titre = "Etat des articles par compte comptable : ".$ref_fam.' - '.$design_fam;
        }

        
        if($famille != null && $date_debut != null && $date_fin != null){
            
            $ref_fam = $famille->ref_fam ?? '';
            $design_fam = $famille->design_fam ?? '';
            $data = [
                'familles_id'=>$famille->id,
                'date_debut'=>$date_debut,
                'date_fin'=>$date_fin,
            ];
            $articles = $this->controller2->getArticleByFamilleIdByPeriodeT($data);
            $titre = "Etat des articles par compte comptable : ".$ref_fam.' - '.$design_fam.'. Paramétrés dans la période du ' . date('d/m/Y',strtotime($date_debut))  . ' au ' . date('d/m/Y',strtotime($date_fin));
        }

        $familles = $this->getFamilles();

        return view('etats.articles_compte_comptables.articles_compte_comptable',[
            'familles'=>$familles,
            'articles'=>$articles,
            'controller1'=>$this->controller1,
            'titre'=>$titre,
            'famille'=>$famille,
            'famille_crypt'=>$famille_crypt,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
        ]);
    }
    public function crypt_articles_compte_comptable($ref_fam){
        $famille = $this->getFamilleByRefFam($ref_fam);
        if($famille === null){
            return redirect()->back()->with('error','Veuillez saisir un compte comptable valide');
        }

        return redirect('/etats/articles_compte_comptable/'.Crypt::encryptString($famille->id));
    }
    public function store(Request $request){
        
        if(isset($request->famille_crypt)){
            
            if($request->submit === 'imprimer'){  
                $date_debut_crypt = null;
                $date_fin_crypt = null;
                if(isset($request->date_debut) && isset($request->date_fin)){
                    $date_debut_crypt = Crypt::encryptString($request->date_debut);
                    $date_fin_crypt = Crypt::encryptString($request->date_fin);
                }
                
                return redirect('etats/print_articles_compte_comptable/'.$request->famille_crypt.'/'.$date_debut_crypt.'/'.$date_fin_crypt);
            }

            if($request->submit === 'soumettre'){
                
                $validate = $request->validate([
                    'date_debut' => ['required','date'],
                    'date_fin' => ['required','date','after_or_equal:date_debut']
                ]);
                $date_debut_crypt = Crypt::encryptString($request->date_debut);
                $date_fin_crypt = Crypt::encryptString($request->date_fin);

                return redirect('etats/articles_compte_comptable/'.$request->famille_crypt.'/'.$date_debut_crypt.'/'.$date_fin_crypt);
                //
            }
            
        }
    }
    public function print_articles_compte_comptable($famille_crypt=null,$date_debut_crypt=null,$date_fin_crypt=null){
        $famille = null;
        $date_debut = null;
        $date_fin = null;
        $titre = "Etat des articles par compte comptable";
        if($famille_crypt != null){
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }

            $famille = Famille::findOrFail($decrypted);

        }

        if($date_debut_crypt != null){
            try {
                $date_debut = Crypt::decryptString($date_debut_crypt);
            } catch (DecryptException $e) {
                //
            }
        }

        if($date_fin_crypt != null){
            try {
                $date_fin = Crypt::decryptString($date_fin_crypt);
            } catch (DecryptException $e) {
                //
            }
        }

        
        $articles = [];
        if($famille != null){
            $ref_fam = $famille->ref_fam ?? '';
            $design_fam = $famille->design_fam ?? '';
            $articles = $this->getArticleByFamilleId($famille->id);
            $titre = "Etat des articles par compte comptable : ".$ref_fam.' - '.$design_fam;
        }

        
        if($famille != null && $date_debut != null && $date_fin != null){
            
            $ref_fam = $famille->ref_fam ?? '';
            $design_fam = $famille->design_fam ?? '';
            $data = [
                'familles_id'=>$famille->id,
                'date_debut'=>$date_debut,
                'date_fin'=>$date_fin,
            ];
            $articles = $this->controller2->getArticleByFamilleIdByPeriodeT($data);
            $titre = "Etat des articles par compte comptable : ".$ref_fam.' - '.$design_fam.'. Paramétrés dans la période du ' . date('d/m/Y',strtotime($date_debut))  . ' au ' . date('d/m/Y',strtotime($date_fin));
        }

        $familles = $this->getFamilles();

        $pdf = PDF::loadView('prints.etats.articles_compte_comptable',[
            'familles'=>$familles,
            'articles'=>$articles,
            'controller1'=>$this->controller1,
            'titre'=>$titre,
            'famille'=>$famille,
            'famille_crypt'=>$famille_crypt,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
        ]);

        return $pdf->stream($titre.'.pdf');
    }
    public function stock($famille_crypt=null,$depot_crypt=null,$article_crypt=null,$date_stock_crypt=null){

        $titre = "ETAT DU STOCK";
        $magasin_stocks = []; 

        $famille = null;

        if($famille_crypt != null){

            $decrypted_famille = null;

            try {
                $decrypted_famille = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $famille = $this->getFamilleByRefFam($decrypted_famille);

        }

        $depot = null;

        if($depot_crypt != null){

            $decrypted_depot = null;
            
            try {
                $decrypted_depot = Crypt::decryptString($depot_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $depot = $this->controller4->getDepotByRef($decrypted_depot);

        }

        $article = null;

        if($article_crypt != null){

            $decrypted_article = null;
            
            try {
                $decrypted_article = Crypt::decryptString($article_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $article = $this->getArticleByRef($decrypted_article);

        }

        $date_stock = null;

        if($date_stock_crypt != null){

            $date_stock = null;
            
            try {
                $date_stock = Crypt::decryptString($date_stock_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }
        
        if($famille != null){
            
            $magasin_stocks = $this->controller4->getMagasinStocks($depot, $article, $famille, $date_stock);

            if($article != null && $depot != null){
                $titre = 'Etat du stock de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article != null && $depot === null){
                $titre = 'Etat du stock de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }
            
            if($article === null && $depot != null){
                $titre = 'Etat du stock : du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article === null && $depot === null){
                $titre = 'Etat du stock des articles du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }

            if($date_stock != null){
                $titre = $titre . '. Au ' . date('d/m/Y',strtotime($date_stock));
            }
        }

        $depots = $this->getDepots();
        $familles = $this->getFamilles();

        return view('etats.stocks.stock',[
            'magasin_stocks'=>$magasin_stocks,
            'titre'=>$titre,
            'articles'=>$articles,
            'depots'=>$depots,
            'familles'=>$familles,
            'famille'=>$famille,
            'depot'=>$depot,
            'article'=>$article,
            'date_stock'=>$date_stock
        ]);
    }
    public function crypt($famille=null,$depot=null,$article=null,$periode_debut=null,$periode_fin=null){

        $crypt_famille = null;
        if(isset($famille)){
            $crypt_famille = Crypt::encryptString($famille);
        }

        $crypt_depot = null;
        if(isset($depot)){
            $crypt_depot = Crypt::encryptString($depot);
        }

        $crypt_article = null;
        if(isset($article)){
            $crypt_article = Crypt::encryptString($article);
        }

        $crypt_periode_debut = null;
        if(isset($periode_debut)){
            $crypt_periode_debut = Crypt::encryptString($periode_debut);
        }

        $crypt_periode_fin = null;
        if(isset($periode_fin)){
            $crypt_periode_fin = Crypt::encryptString($periode_fin);
        }

        return redirect('etats/stock/'.$crypt_famille.'/'.$crypt_depot.'/'.$crypt_article.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
    }
    public function crypt_post(Request $request){        
        $request->validate([
            'cst'=>['nullable','numeric'],
            'nst'=>['nullable','string'],
            'rf'=>['required','numeric'],
            'df'=>['required','string'],
            'date_stock'=>['nullable','date'],
            'submit'=>['required','string'],
        ]);
        
        $famille=null;
        $depot=null;
        $article=null;
        $date_stock=null;
        

        if(isset($request->rf)){ $famille = $request->rf; }

        if(isset($request->cst)){ $depot = $request->cst; }

        if(isset($request->art)){ $article = $request->art; }

        if(isset($request->date_stock)){ $date_stock = $request->date_stock; }
        
        $crypt_famille = Crypt::encryptString($famille);
        $crypt_depot = Crypt::encryptString($depot);
        $crypt_article = Crypt::encryptString($article);
        $crypt_date_stock = Crypt::encryptString($date_stock);
            
        if(isset($request->submit)){
            if($request->submit === 'soumettre'){
                return redirect('etats/stock/'.$crypt_famille.'/'.$crypt_depot.'/'.$crypt_article.'/'.$crypt_date_stock);
            }elseif($request->submit === 'imprimer'){
                return redirect('prints/etats/stock/'.$crypt_famille.'/'.$crypt_depot.'/'.$crypt_article.'/'.$crypt_date_stock);
            }
        }
        
    }
    public function print_stock($famille_crypt=null,$depot_crypt=null,$article_crypt=null,$date_stock_crypt=null){

        $titre = null;
        $magasin_stocks = []; 

        $famille = null;

        if($famille_crypt != null){

            $decrypted_famille = null;

            try {
                $decrypted_famille = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $famille = $this->getFamilleByRefFam($decrypted_famille);

        }

        $depot = null;

        if($depot_crypt != null){

            $decrypted_depot = null;
            
            try {
                $decrypted_depot = Crypt::decryptString($depot_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $depot = $this->controller4->getDepotByRef($decrypted_depot);

        }

        $article = null;

        if($article_crypt != null){

            $decrypted_article = null;
            
            try {
                $decrypted_article = Crypt::decryptString($article_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $article = $this->getArticleByRef($decrypted_article);

        }

        $date_stock = null;

        if($date_stock_crypt != null){

            $date_stock = null;
            
            try {
                $date_stock = Crypt::decryptString($date_stock_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }
        
        if($famille != null){
            
            $magasin_stocks = $this->controller4->getMagasinStocks($depot, $article, $famille, $date_stock);

            if($article != null && $depot != null){
                $titre = 'Etat du stock de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article != null && $depot === null){
                $titre = 'Etat du stock de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }
            
            if($article === null && $depot != null){
                $titre = 'Etat du stock : du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article === null && $depot === null){
                $titre = 'Etat du stock des articles du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }

            if($date_stock != null){
                $titre = $titre . '. Au ' . date('d/m/Y',strtotime($date_stock));
            }
        }

        
        $depots = $this->getDepots();
        $familles = $this->getFamilles();

        $pdf = PDF::loadView('prints.etats.stock',[
            'magasin_stocks'=>$magasin_stocks,
            'titre'=>$titre,
            'articles'=>$articles,
            'depots'=>$depots,
            'familles'=>$familles,
            'famille'=>$famille,
            'depot'=>$depot,
            'article'=>$article,
            'date_stock'=>$date_stock,
        ]);

        return $pdf->stream($titre.'.pdf');
    }

    public function mouvement($famille_crypt=null,$depot_crypt=null,$type_mouvement_crypt=null,$article_crypt=null,$date_debut_crypt=null,$date_fin_crypt=null){

        $titre = "ETAT DES MOUVEMENTS";
        $magasin_stocks = []; 

        $type_mouvement = null;

        if($type_mouvement_crypt != null){

            $decrypted_type_mouvement = null;

            try {
                $decrypted_type_mouvement = Crypt::decryptString($type_mouvement_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $type_mouvement = TypeMouvement::findOrFail($decrypted_type_mouvement);

        }

        $famille = null;

        if($famille_crypt != null){

            $decrypted_famille = null;

            try {
                $decrypted_famille = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $famille = $this->getFamilleByRefFam($decrypted_famille);

        }

        $depot = null;

        if($depot_crypt != null){

            $decrypted_depot = null;
            
            try {
                $decrypted_depot = Crypt::decryptString($depot_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $depot = $this->controller4->getDepotByRef($decrypted_depot);

        }

        $article = null;

        if($article_crypt != null){

            $decrypted_article = null;
            
            try {
                $decrypted_article = Crypt::decryptString($article_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $article = $this->getArticleByRef($decrypted_article);

        }

        $date_debut = null;

        if($date_debut_crypt != null){

            $date_debut = null;
            
            try {
                $date_debut = Crypt::decryptString($date_debut_crypt);
            } catch (DecryptException $e) {
                //
            }


        }

        $date_fin = null;

        if($date_fin_crypt != null){

            $date_fin = null;
            
            try {
                $date_fin = Crypt::decryptString($date_fin_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }

        $type_mouvements = $this->controller4->getTypeMouvements();
        
        if($famille != null && $type_mouvement != null){
            
            $magasin_stocks = $this->controller4->getMouvements($depot, $article, $famille, $type_mouvement, $date_debut,$date_fin);

            if($article != null && $depot != null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article != null && $depot === null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }
            
            if($article === null && $depot != null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article === null && $depot === null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" des articles du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }

            if($date_debut != null && $date_fin != null){
                $titre = $titre . '. Du ' . date('d/m/Y',strtotime($date_debut)) . ' au ' . date('d/m/Y',strtotime($date_fin));
            }
        }

        $depots = $this->getDepots();
        $familles = $this->getFamilles();
        
        return view('etats.mouvements.mouvement',[
            'magasin_stocks'=>$magasin_stocks,
            'titre'=>$titre,
            'articles'=>$articles,
            'depots'=>$depots,
            'familles'=>$familles,
            'famille'=>$famille,
            'depot'=>$depot,
            'article'=>$article,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
            'type_mouvements'=>$type_mouvements,
            'type_mouvement'=>$type_mouvement
        ]);
    }
    public function crypt_mouvement($famille=null,$depot=null,$type_mouvement=null,$article=null,$periode_debut=null,$periode_fin=null){
        $crypt_famille = null;
        if(isset($famille)){
            $crypt_famille = Crypt::encryptString($famille);
        }

        $crypt_depot = null;
        if(isset($depot)){
            $crypt_depot = Crypt::encryptString($depot);
        }

        $crypt_type_mouvement = null;
        if(isset($type_mouvement)){
            $crypt_type_mouvement = Crypt::encryptString($type_mouvement);
        }

        $crypt_article = null;
        if(isset($article)){
            $crypt_article = Crypt::encryptString($article);
        }

        $crypt_periode_debut = null;
        if(isset($periode_debut)){
            $crypt_periode_debut = Crypt::encryptString($periode_debut);
        }

        $crypt_periode_fin = null;
        if(isset($periode_fin)){
            $crypt_periode_fin = Crypt::encryptString($periode_fin);
        }

        return redirect('etats/mouvement/'.$crypt_famille.'/'.$crypt_depot.'/'.$crypt_type_mouvement.'/'.$crypt_article.'/'.$crypt_periode_debut.'/'.$crypt_periode_fin);
    }
    public function crypt_mouvement_post(Request $request){        
        $request->validate([
            'type_mouvements_id'=>['required','numeric'],
            'cst'=>['nullable','numeric'],
            'nst'=>['nullable','string'],
            'rf'=>['required','numeric'],
            'df'=>['required','string'],
            'date_debut'=>['nullable','date'],
            'date_fin' => ['nullable','date','after_or_equal:date_debut'],
            'submit'=>['required','string'],
        ]);
        
        $famille=null;
        $depot=null;
        $article=null;
        $date_debut=null;
        $date_fin=null;
        $type_mouvement=null;

        if(isset($request->type_mouvements_id)){ $type_mouvement = $request->type_mouvements_id; }
        if(isset($request->rf)){ $famille = $request->rf; }

        if(isset($request->cst)){ $depot = $request->cst; }

        if(isset($request->art)){ $article = $request->art; }

        if(isset($request->date_debut)){ $date_debut = $request->date_debut; }
        if(isset($request->date_fin)){ $date_fin = $request->date_fin; }
        
        $crypt_type_mouvement = Crypt::encryptString($type_mouvement);
        $crypt_famille = Crypt::encryptString($famille);
        $crypt_depot = Crypt::encryptString($depot);
        $crypt_article = Crypt::encryptString($article);
        $crypt_date_debut = Crypt::encryptString($date_debut);
        $crypt_date_fin = Crypt::encryptString($date_fin);
            
        if(isset($request->submit)){
            if($request->submit === 'soumettre'){
                return redirect('etats/mouvement/'.$crypt_famille.'/'.$crypt_depot.'/'.$crypt_type_mouvement.'/'.$crypt_article.'/'.$crypt_date_debut.'/'.$crypt_date_fin);
            }elseif($request->submit === 'imprimer'){
                return redirect('prints/etats/mouvement/'.$crypt_famille.'/'.$crypt_depot.'/'.$crypt_type_mouvement.'/'.$crypt_article.'/'.$crypt_date_debut.'/'.$crypt_date_fin);
            }
        }
        
    }
    public function print_mouvement($famille_crypt=null,$depot_crypt=null,$type_mouvement_crypt=null,$article_crypt=null,$date_debut_crypt=null,$date_fin_crypt=null){

        $titre = "ETAT DES MOUVEMENTS";
        $magasin_stocks = []; 

        $type_mouvement = null;

        if($type_mouvement_crypt != null){

            $decrypted_type_mouvement = null;

            try {
                $decrypted_type_mouvement = Crypt::decryptString($type_mouvement_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $type_mouvement = TypeMouvement::findOrFail($decrypted_type_mouvement);

        }

        $famille = null;

        if($famille_crypt != null){

            $decrypted_famille = null;

            try {
                $decrypted_famille = Crypt::decryptString($famille_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $famille = $this->getFamilleByRefFam($decrypted_famille);

        }

        $depot = null;

        if($depot_crypt != null){

            $decrypted_depot = null;
            
            try {
                $decrypted_depot = Crypt::decryptString($depot_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $depot = $this->controller4->getDepotByRef($decrypted_depot);

        }

        $article = null;

        if($article_crypt != null){

            $decrypted_article = null;
            
            try {
                $decrypted_article = Crypt::decryptString($article_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $article = $this->getArticleByRef($decrypted_article);

        }

        $date_debut = null;

        if($date_debut_crypt != null){

            $date_debut = null;
            
            try {
                $date_debut = Crypt::decryptString($date_debut_crypt);
            } catch (DecryptException $e) {
                //
            }


        }

        $date_fin = null;

        if($date_fin_crypt != null){

            $date_fin = null;
            
            try {
                $date_fin = Crypt::decryptString($date_fin_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }

        $type_mouvements = $this->controller4->getTypeMouvements();
        
        if($famille != null && $type_mouvement != null){
            
            $magasin_stocks = $this->controller4->getMouvements($depot, $article, $famille, $type_mouvement, $date_debut,$date_fin);

            if($article != null && $depot != null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article != null && $depot === null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" de l\'articles : ' . $article->ref_articles . ' - ' . $article->design_article .', du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }
            
            if($article === null && $depot != null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', du dépôt : ' . $depot->ref_depot . ' - ' . $depot->design_dep;
            }

            if($article === null && $depot === null){
                $titre = 'Etat des mouvements : "' . $type_mouvement->libelle . '" des articles du compte comptable : ' . $famille->ref_fam. ' - ' . $famille->design_fam . ', de tous les dépôts';
            }

            if($date_debut != null && $date_fin != null){
                $titre = $titre . '. Du ' . date('d/m/Y',strtotime($date_debut)) . ' au ' . date('d/m/Y',strtotime($date_fin));
            }
        }

        $depots = $this->getDepots();
        $familles = $this->getFamilles();
        
        $pdf = PDF::loadView('prints.etats.mouvement',[
            'magasin_stocks'=>$magasin_stocks,
            'titre'=>$titre,
            'articles'=>$articles,
            'depots'=>$depots,
            'familles'=>$familles,
            'famille'=>$famille,
            'depot'=>$depot,
            'article'=>$article,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
            'type_mouvements'=>$type_mouvements,
            'type_mouvement'=>$type_mouvement
        ]);

        return $pdf->stream($titre.'.pdf');
    }
}
