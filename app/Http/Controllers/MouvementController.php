<?php

namespace App\Http\Controllers;

use App\Models\Mouvement;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class MouvementController extends ControllerMouvement
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
    public function index(MagasinStock $magasinStock)
    {
        $mouvements = Mouvement::where('mouvements.magasin_stocks_id',$magasinStock->id)
                      ->join('type_mouvements as tm','tm.id','=','mouvements.type_mouvements_id')
                      ->join('profils as p','p.id','=','mouvements.profils_id')
                      ->join('users as u','u.id','=','p.users_id')
                      ->join('agents as a','a.id','=','u.agents_id')
                      ->select('p.*','u.*','a.*','tm.*','mouvements.*')
                      ->paginate(10);
        return view('mouvements.index',[
            'mouvements'=>$mouvements,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $autorisation_acces = "Accès refusé";
        $type_profil = null;

        if (Session::has('profils_id')){
            
            $autorisation_acces = null;

            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        }

        if($type_profil != null){

            $type_profils_name_autorises = "Gestionnaire des stocks,Responsable des stocks";

            $autorisation_acces = $this->AutorisationAcces($type_profil->name,$type_profils_name_autorises);

        }
        
        if($autorisation_acces === "Accès refusé"){
            return redirect()->back()->with('error','Accès refusé');
        }

        $articles = $this->getArticleActifs();

        return view('mouvements.create',[
            'articles'=>$articles
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
        $this->validateRequest($request);

        $qte = (int) filter_var($request->qte, FILTER_SANITIZE_NUMBER_INT);
        $prixu = (int) filter_var($request->prixu, FILTER_SANITIZE_NUMBER_INT);

        if($qte <= 0){
            return redirect()->back()->with('error','Veuillez saisir une quantité valide');
        }

        if($prixu <= 0){
            return redirect()->back()->with('error','Veuillez saisir un prix unitaire valide');
        }

        $article = $this->getArticleByRef($request->ref_articles);

        if($article === null){
            return redirect()->back()->with('error','Veuillez saisir un article valide');
        }

        $utilisateur_connecte = $this->getInfoUserByProfilId(Session::get('profils_id'));

        if($utilisateur_connecte === null){
            return redirect()->back()->with('error','Utilisateur non identifié');
        }

        $magasin = $this->getMagasinByIdDepot($utilisateur_connecte->ref_depot);
        
        $magasin_stocks_id = $this->getMagasinStock($magasin->ref_magasin,$article->ref_articles,$qte);

        $type_mouvements_libelle = "Entrée en stock";

        $type_mouvements_id = $this->storeTypeMouvement($type_mouvements_libelle);

        if($type_mouvements_id != null && $magasin_stocks_id != null){

            $montant_ht = $prixu * $qte;
            $taux_de_change = 1;
            $tva = null;
            $montant_ttc = $montant_ht;

            $mouvements_id = $this->storeMouvement($type_mouvements_id,$magasin_stocks_id,Session::get('profils_id'),$qte,$prixu,$montant_ht,$tva,$montant_ttc,$taux_de_change);

            if($mouvements_id != null){
                $data = ['date_mouvement'=>$request->date_entree];
                $this->setMouvement($mouvements_id,$data);
            }

            $this->procedureSetMagasinStock($magasin_stocks_id);

            $this->setAuditMouvement($type_mouvements_libelle,$mouvements_id,Session::get('profils_id'));

            return  redirect('/magasin_stocks/show/'.Crypt::encryptString($magasin_stocks_id))->with('success','Entrée en stock effectuée');
        }
        return redirect()->back()->with('error','Echec de l\'entrée en stock');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Mouvement  $mouvement
     * @return \Illuminate\Http\Response
     */
    public function show(Mouvement $mouvement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mouvement  $mouvement
     * @return \Illuminate\Http\Response
     */
    public function edit($mouvement)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($mouvement);
        } catch (DecryptException $e) {
            //
        }

        $mouvement_decryter = Mouvement::findOrFail($decrypted);

        $mouvement = $this->getMouvementById($mouvement_decryter->id);

        $autorisation_acces = "Accès refusé";
        $type_profil = null;

        if (Session::has('profils_id')){
            
            $autorisation_acces = null;

            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        }

        if($type_profil != null){

            $type_profils_name_autorises = "Gestionnaire des stocks,Responsable des stocks";

            $autorisation_acces = $this->AutorisationAcces($type_profil->name,$type_profils_name_autorises);

        }
        
        if($autorisation_acces === "Accès refusé"){
            return redirect()->back()->with('error','Accès refusé');
        }

        $articles = $this->getArticleActifs();
        $type_mouvements_libelle = null;
        if($mouvement != null){
            $type_mouvements_libelle = $mouvement->type_mouvements_libelle;
        }

        $entete = null;
        if($type_mouvements_libelle === "Intégration d'inventaire"){
            $entete = "Modifier l'inventaire intégré";
        }

        if($type_mouvements_libelle === "Entrée en stock"){
            $entete = "Modifier l'entrée en stock";
        }

        if($type_mouvements_libelle === "Sortie du stock"){
            $entete = "Modifier la sortie du stock";
        }
        
        //dd($articles,$mouvement);
        return view('mouvements.edit',[
            'articles'=>$articles,
            'mouvement'=>$mouvement,
            'entete'=>$entete
        ]);

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mouvement  $mouvement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validateRequestUpdate($request);
        $qte = (int) filter_var($request->qte, FILTER_SANITIZE_NUMBER_INT);
        $prixu = (int) filter_var($request->prixu, FILTER_SANITIZE_NUMBER_INT);

        if($qte <= 0){
            return redirect()->back()->with('error','Veuillez saisir une quantité valide');
        }

        if($prixu <= 0){
            return redirect()->back()->with('error','Veuillez saisir un prix unitaire valide');
        }

        $article = $this->getArticleByRef($request->ref_articles);

        if($article === null){
            return redirect()->back()->with('error','Veuillez saisir un article valide');
        }

        $utilisateur_connecte = $this->getInfoUserByProfilId(Session::get('profils_id'));

        if($utilisateur_connecte === null){
            return redirect()->back()->with('error','Utilisateur non identifié');
        }

        $magasin = $this->getMagasinByIdDepot($utilisateur_connecte->ref_depot);
        
        $magasin_stocks_id = $this->getMagasinStock($magasin->ref_magasin,$article->ref_articles,$qte);

        $mouvement = $this->getMouvementById($request->mouvements_id);
        if($mouvement != null){

            $montant_ht = $prixu * $qte;
            $tva = null;
            $montant_ttc = $montant_ht;

            $data = [
                'qte'=>$qte,
                'prix_unit'=>$prixu,
                'montant_ht'=>$montant_ht,
                'taxe'=>$tva,
                'montant_ttc'=>$montant_ttc,
                'date_mouvement'=>$request->date_entree,
            ];

            $this->setMouvement($request->mouvements_id,$data);

            $this->procedureSetMagasinStock($magasin_stocks_id);

            $type_statut_mouvement_libelle = null;

            if($mouvement->type_mouvements_libelle === "Intégration d'inventaire"){
                $type_statut_mouvement_libelle = "Modification de l'inventaire intégré";

                $this->setInventaireArticleByMouvement($mouvement->id,$qte,$prixu);
            }
    
            if($mouvement->type_mouvements_libelle === "Entrée en stock"){
                $type_statut_mouvement_libelle = "Modification de l'entrée en stock";
            }
    
            if($mouvement->type_mouvements_libelle === "Sortie du stock"){
                $type_statut_mouvement_libelle = "Modification de la sortie du stock";
            }

            if($type_statut_mouvement_libelle != null){
                $this->setAuditMouvement($type_statut_mouvement_libelle,$request->mouvements_id,Session::get('profils_id'));
            }

            return redirect('/magasin_stocks/show/'.Crypt::encryptString($magasin_stocks_id))->with('success','Modification de l\'entrée en stock effectuée');
        }
        return redirect()->back()->with('error','Echec de la modification de l\'entrée en stock');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mouvement  $mouvement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mouvement $mouvement)
    {
        //
    }
}
