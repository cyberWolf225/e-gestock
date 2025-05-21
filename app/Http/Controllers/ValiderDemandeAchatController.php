<?php

namespace App\Http\Controllers;

use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use App\Models\ValiderDemandeAchat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class ValiderDemandeAchatController extends Controller
{
    private $controller3;
    public function __contruct(Controller3 $controller3) {
        $this->middleware('auth');
        $this->controller3 = $controller3;
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
    public function create($demandeAchat) 
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeAchat);
        } catch (DecryptException $e) {
            //
        }

        $demandeAchat = DemandeAchat::findOrFail($decrypted);
        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        

        
        $etape = "create";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Responsable des achats'];
        
        $profil = $this->validerDemandeAchatControlAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        
            // dernier statut de la demande
            $libelle = null;
            $commentaire = null;
            $profil_commentaire = null;
            $nom_prenoms_commentaire = null;

            $statut_demande_achat = $this->getLastStatutDemandeAchat($demandeAchat->id);

            if ($statut_demande_achat!=null) {

                $libelle = $statut_demande_achat->libelle;
                $commentaire = $statut_demande_achat->commentaire;
                $profil_commentaire = $statut_demande_achat->name;
                $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;

            }

         
        $demande_achats = $this->getDemandeAchats($demandeAchat->id); 

         if (count($demande_achats) == 0) {
             return redirect()->back();
         }

        $gestions = $this->getGestion();

        $credit_budgetaires = $this->getFamille();
        
        $articles = $this->getArticle();
        
        $credit_budgetaires_select = null;
        $griser = 1;

        $demande_achat_info = $this->getDemandeAchat($demandeAchat->id);

        //vérifier si le Responsable DMP à visé le choix du fournisseur
            $statut = $this->getCategorieDemandeAchat($demandeAchat->id);
        //

        $credit_budgetaire_structure = $this->getCreditBudgetaireById($demandeAchat->credit_budgetaires_id);    

        $affiche_zone_struture = 1;

        $type_piece = "Demande d'achats";

        $piece_jointes = $this->getPieceJointes($demandeAchat->id, $type_piece);

        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);

        $check_default = count($this->getValiderDemandeAchatVerif($demandeAchat->id));

        $demande_achat_info = $this->getDemandeAchat($demandeAchat->id);

        if($demandeAchat != null){
            try {
                $param = $demande_achat_info->exercice.'-'.$demande_achat_info->code_gestion.'-'.$demande_achat_info->code_structure.'-'.$demande_achat_info->ref_fam;
    
                $this->storeCreditBudgetaireByWebService($param, $demande_achat_info->ref_depot);
            } catch (\Throwable $th) {

            }
        }

        $disponible = $this->getCreditBudgetaireDisponibleByDemandeAchatId($demandeAchat->id);

        return view('valider_demande_achats.create',[
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'piece_jointes'=>$piece_jointes,
            'articles'=>$articles,
            'gestions'=>$gestions,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'commentaire'=>$commentaire,
            'libelle'=>$libelle,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'statut'=>$statut,
            'statut_demande_achats'=>$statut_demande_achats,
            'check_default'=>$check_default,
            'disponible'=>$disponible
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
        if (isset($request->submit)) {
            if ($request->submit === 'annuler_r_achat') {

                $request->validate([
                    'commentaire'=>['required','string'],
                    'ref_fam'=>['required','numeric'],
                    'design_fam'=>['required','string'],
                    'code_gestion'=>['required','string'],
                    'nom_gestion'=>['required','string'],
                    'intitule'=>['required','string'],
                    'id'=>['required','numeric'],
                    'ref_articles' => ['required','array'],
                    'design_article' => ['required','array'],
                    'detail_demande_achat_id' => ['required','array'],
                    'qte' => ['required','array'],
                    'qte_validee' => ['required','array'],
                    'submit'=>['required','string'],
                ]);

            }else{
                $request->validate([
                    'commentaire'=>['nullable','string'],
                    'ref_fam'=>['required','numeric'],
                    'design_fam'=>['required','string'],
                    'code_gestion'=>['required','string'],
                    'nom_gestion'=>['required','string'],
                    'intitule'=>['required','string'],
                    'id'=>['required','numeric'],
                    'ref_articles' => ['required','array'],
                    'design_article' => ['required','array'],
                    'detail_demande_achat_id' => ['required','array'],
                    'qte' => ['required','array'],
                    'qte_validee' => ['required','array'],
                    'submit'=>['required','string'],
                ]);
            }
        }else{
            $request->validate([
                'commentaire'=>['nullable','string'],
                'ref_fam'=>['required','numeric'],
                'design_fam'=>['required','string'],
                'code_gestion'=>['required','string'],
                'nom_gestion'=>['required','string'],
                'intitule'=>['required','string'],
                'id'=>['required','numeric'],
                'ref_articles' => ['required','array'],
                'design_article' => ['required','array'],
                'detail_demande_achat_id' => ['required','array'],
                'qte' => ['required','array'],
                'qte_validee' => ['required','array'],
                'submit'=>['required','string'],
            ]);
        }

        if(isset($request->ref_articles)){
            if (count($request->ref_articles) > 0) {


                $t = 0;
                $control_check = 0;
                $r = 0;
    
                foreach ($request->ref_articles as $item0 => $value0) {
    
                    if (isset($request->approvalcd[$request->detail_demande_achat_id[$item0]])) {
                        $control_check = 1;
                    }
    
                }
    
                if ($control_check === 0 && $request->submit != "annuler_r_achat") {
                    $request->validate([
                        'commentaire'=>['required','string']
                    ]);
                }
            }
        }

        
        

        
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        

        
        $etape = "store";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Responsable des achats'];
        
        $profil = $this->validerDemandeAchatControlAcces($etape,$type_profils_lists,$type_profils_name,$request);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        

        if (isset($request->ref_articles)) {

            if (count($request->ref_articles) > 0) {
                foreach ($request->ref_articles as $item => $value) {
                    if (isset($request->approvalcd[$request->detail_demande_achat_id[$item]])) {
                        
                        if ($request->qte_validee[$item]!=null) {
                            
                            $qte_validee[$item] = filter_var($request->qte_validee[$item], FILTER_SANITIZE_NUMBER_INT);

                            try {
                                $qte_validee[$item] = $qte_validee[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte_validee[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                        }

                    }
                    
                }
            }

            $valider_demande_achat = null;
            
            if (count($request->ref_articles) > 0) {
                

                $t = 0;
                $r = 0;
                
                
                foreach ($request->ref_articles as $item => $value) {

                    
                    if ( ($request->detail_demande_achat_id[$item] != null) AND ($request->qte_validee[$item] != null) AND ($request->ref_articles[$item] != null) AND ($request->design_article[$item] != null) AND ($request->qte[$item] != null) AND ($request->qte[$item] >= 0) ) {

                        $qte_validee[$item] = filter_var($request->qte_validee[$item], FILTER_SANITIZE_NUMBER_INT);

                        try {
                            $qte_validee[$item] = $qte_validee[$item] * 1;
                        } catch (\Throwable $th) {
                            return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                        }
                        
                        if (gettype($qte_validee[$item])!='integer') {
                            return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                        }

                    
                        if (isset($request->approvalcd[$request->detail_demande_achat_id[$item]])) {
                            $t = 1;
                            $flag_valide = true;

                            if ($qte_validee[$item] === 0) {
                                $flag_valide = false;
                            }
                        }else{
                            $flag_valide = false;
                            $r++;
                        }

                        if (isset($request->submit)) {

                            if ($request->submit === "annuler_r_achat") {
                                $flag_valide = false;
                            }
                        }               
                        $this->setDetailDemandeAchat3($request->detail_demande_achat_id[$item],$qte_validee[$item],$flag_valide);
                        
                        $this->deleteValiderDemandeAchat($request->detail_demande_achat_id[$item]);
                        
                        $dataStoreValiderDemandeAchat = [
                            'detail_demande_achats_id'=>$request->detail_demande_achat_id[$item],
                            'profils_id'=>Session::get('profils_id'),
                            'qte_validee'=>$qte_validee[$item],
                            'flag_valide'=>$flag_valide,
                        ];
                        $valider_demande_achat = $this->controller3->storeValiderDemandeAchat($dataStoreValiderDemandeAchat);  
                    }
                }


                // Type statut demande d'achat
                

                if (isset($request->submit)) {

                    if ($request->submit === "annuler_r_achat") {

                        $libelle = 'Rejeté (Responsable des achats)';
                        $libelle_df = 'Annulé (Responsable des achats)';

                    }elseif ($request->submit === "valider_r_achat") {
                        
                        if ($t==1) {
                    
                            /*if ($r!=0) {
                                $libelle = 'Partiellement validé (Responsable des achats)';
                            }else{
                                $libelle = 'Validé (Responsable des achats)';
                            }*/

                            $libelle = 'Validé (Responsable des achats)';

                            $libelle_df = 'Visé (Responsable des achats)';
                            
                        }else{
                            return redirect()->back()->with('error','Veuillez sélectionner au moins un article');
                            //$libelle = 'Rejeté (Responsable des achats)';
                        }

                    }

                }
                
                $this->storeTypeStatutDemandeAchat($libelle);

                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

                if ($type_statut_demande_achat != null) {

                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                    $this->setLastStatutDemandeAchat($request->id);

                    $dataStatutDemandeAchat = [
                        'demande_achats_id'=>$request->id,
                        'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                        'date_debut'=>date('Y-m-d'),
                        'date_fin'=>date('Y-m-d'),
                        'commentaire'=>trim($request->commentaire),
                        'profils_id'=>Session::get('profils_id'),
                    ];
                    $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                    $type_operations_libelle = "Demande d'achats" ;

                    $this->storeTypeOperation($type_operations_libelle);

                    $type_operation = $this->getTypeOperation($type_operations_libelle);
                    if ($type_operation!=null) {

                        $type_operations_id = $type_operation->id;

                        $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($request->id,$type_operations_id);

                        if($demande_fond_bon_commande != null){

                            if(isset($libelle_df)){
                                $this->storeStatutDemandeFond($libelle_df,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'),$request->commentaire);
                            }                            

                        }
                    }

                }

            }

        }
        if ($valider_demande_achat!=null) { 

            // notifier l'émetteur
            $subject = 'Validation de demande d\'achats';

            // utilisateur connecté
            $email = auth()->user()->email;
            $this->notifDemandeAchat($email,$subject,$request->id);
        //


            // notifier le gestionnaire des achats
                $profils = ['Gestionnaire des achats'];
                $this->notifDemandeAchat2($subject,$request->id,$profils);


            
            return redirect('/demande_achats/index')->with('success','Demande d\'achat traitée');
        }else{
            return redirect()->back()->with('error','Traitement de la demande d\'achat echoué');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ValiderDemandeAchat  $validerDemandeAchat
     * @return \Illuminate\Http\Response
     */
    public function show(ValiderDemandeAchat $validerDemandeAchat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ValiderDemandeAchat  $validerDemandeAchat
     * @return \Illuminate\Http\Response
     */
    public function edit(ValiderDemandeAchat $validerDemandeAchat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ValiderDemandeAchat  $validerDemandeAchat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ValiderDemandeAchat $validerDemandeAchat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ValiderDemandeAchat  $validerDemandeAchat
     * @return \Illuminate\Http\Response
     */
    public function destroy(ValiderDemandeAchat $validerDemandeAchat)
    {
        //
    }
}
