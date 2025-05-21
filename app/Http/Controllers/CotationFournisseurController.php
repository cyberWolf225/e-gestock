<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Taxe;
use App\Models\Devise;
use App\Models\Critere;
use App\Models\Commande;
use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use App\Models\CotationFournisseur;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use AmrShawky\LaravelCurrency\Facade\Currency;
use Illuminate\Contracts\Encryption\DecryptException;

class CotationFournisseurController extends Controller
{
    private $controller3;
    public function __construct(Controller3 $controller3)
    {
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

        
        $etape = "cotation_fournisseur_create";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Fournisseur']; 
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        $taxes = Taxe::whereIn('ref_taxe',['67','68','69'])->get();

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        $devise_cotation = null;

        $type_statut_demande_achats_libelles = ['Transmis pour cotation'];

        $statut_demande_achat = $this->getStatutDemandeAchatGroupe($demandeAchat->id,$type_statut_demande_achats_libelles);

        if ($statut_demande_achat!=null) {

            $libelle = $statut_demande_achat->libelle;
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;

        }
        
        $fournisseur = $this->getOrganisationUserConnect(Session::get('profils_id'),$demandeAchat->id);

        $gestions = $this->getGestion();        

        $credit_budgetaires = $this->getFamilleDemandeAchat($demandeAchat->ref_fam);
        
        $credit_budgetaires_select = null;
        $griser = 1;

        $demande_achats = $this->getDemandeAchatValiders($demandeAchat->id); 

        $demande_achat_info = $this->getDemandeAchat($demandeAchat->id);

        $cotation_demande_achat = null;

        if ($fournisseur!=null) {
            
            // COTATION DEJA EFFECTUEE
            $organisations_id = $fournisseur->organisations_id;

            $cotation_fournisseurs = $this->controller3->getCotationFournisseurs($organisations_id,$demandeAchat->id);            

            if ($cotation_fournisseurs!=null) {
                
                $cotation_fournisseurs_id = $cotation_fournisseurs->cotation_fournisseurs_id;

                $cotation_demande_achat = $this->getCotationFournisseur($demandeAchat->id,$cotation_fournisseurs_id);

                if ($cotation_demande_achat!=null) {

                    if (isset($cotation_demande_achat->devises_id)) {

                        $devise_cotation = Devise::where('id',$cotation_demande_achat->devises_id)->first();

                    }
                    
                    $statut_demande_achat = $this->getStatutDemandeAchatFournisseur($cotation_demande_achat->cotation_fournisseurs_id,$cotation_demande_achat->id);
                    
                    if ($statut_demande_achat!=null) {

                        $libelle = $statut_demande_achat->libelle;
                        $commentaire = $statut_demande_achat->commentaire;
                        $profil_commentaire = $statut_demande_achat->name;
                        $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;

                    }
                }
            }
        }
        //dd($demande_achat_info);

        $commande = $this->getCommande($demandeAchat->id);

        $periodes = $this->getPeriodes();

        $statut = $this->getCategorieDemandeAchat($demandeAchat->id);

        // $devises = Devise::all();

        $devises = [];

        $devise_default = Devise::where('libelle','Franc CFA (UEMOA)')->first();

        $credit_budgetaire_structure = $this->getCreditBudgetaireById($demandeAchat->credit_budgetaires_id);

        $type_piece = "Demande d'achats";
        $piece_jointes = $this->getPieceJointes($demandeAchat->id,$type_piece);

        $error = $this->getDelaiCommande($demandeAchat->id,$type_profils_name);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        return view('cotation_fournisseurs.create',[
            'gestions'=>$gestions,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'fournisseur'=>$fournisseur,
            'commande'=>$commande,
            'periodes'=>$periodes,
            'cotation_demande_achat'=>$cotation_demande_achat,
            'taxes'=>$taxes,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'statut'=>$statut,
            'devises'=>$devises,
            'devise_default'=>$devise_default,
            'devise_cotation'=>$devise_cotation,
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'piece_jointes'=>$piece_jointes
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
        
        $request->validate([
            'id'=>['required','numeric'],
            'montant_total_brut'=>['required','string'],
            'montant_total_net'=>['required','string'],
            'remise_generale'=>['nullable','string'],
            'tva'=>['nullable','numeric'],
            'taux_bnc'=>['nullable','numeric'],
            'montant_bnc'=>['nullable','numeric'],
            'montant_total_ttc'=>['required','string'],
            'net_a_payer'=>['required','string'],
            'ref_articles'=>['required','array'],
            'design_article'=>['required','array'],
            'qte_demandee'=>['required','array'],
            'qte'=>['required','array'],
            'prix_unit'=>['required','array'],
            'remise'=>['required','array'],
            'montant_ht'=>['required','array'],
            'taux_acompte'=>['nullable','string'],
            'montant_acompte'=>['nullable','string'],
            'echantillon'=>['nullable','array'],
            'commentaire'=>['nullable','string'],
            'code_devise'=>['required','string'],
            'libelle_devise'=>['required','string'],
            'echantillon_flag'=>['nullable','array']
        ]);

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        

        
        $etape = "cotation_fournisseur_store";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Fournisseur'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$request);

        if ($profil != null) {
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $devises_id = $this->getDevise($request->code_devise,$request->libelle_devise);

        $taux_de_change = $this->thmx_currency_convert($request->code_devise);

        if ($devises_id === null) {
            return redirect()->back()->with('error','Veuillez saisir une devise valide');
        }

        
        if (isset($request->tva)) {

            if($request->tva === null){
                $taux_tva = 1;
            }else{
                $taux_tva = 1 + ($request->tva/100);
            }
        }else{
            $taux_tva = 1;
        }
        

        if (count($request->ref_articles) > 0) {
            
            foreach ($request->ref_articles as $item => $value) { 

                $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                try {
                    $qte[$item] = $qte[$item] * 1;
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                }
                
                if (gettype($qte[$item])!='integer') {
                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                }


                $prix_unit[$item] = filter_var($request->prix_unit[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                    
                $info = 'Prix unitaire';
                $error = $this->setDecimal($prix_unit[$item],$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }else{
                    $prix_unit[$item] = $prix_unit[$item] * 1;
                }

                $montant_ht[$item] = filter_var($request->montant_ht[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                $info = 'Montant ht';
                $error = $this->setDecimal($montant_ht[$item],$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }else{
                    $montant_ht[$item] = $montant_ht[$item] * 1;
                }

                $montant_ttc[$item] = $montant_ht[$item]*$taux_tva;

                if (($request->ref_articles[$item] != null) and ($request->design_article[$item] != null) and ($qte[$item] != null) and ($prix_unit[$item] != null) and ($montant_ht[$item] != null) and ($montant_ttc[$item] != null) and ($qte[$item] > 0) and ($prix_unit[$item] > 0) and ($montant_ttc[$item] > 0)) {
                    // controle des quantité 

                    $detail_demande_achat = $this->getDetailDemandeAchatByRefArticle($request->id,$request->ref_articles[$item]);

                    if ($detail_demande_achat!=null) {

                        $qte_accordee = $detail_demande_achat->qte_accordee;
                        if ($qte[$item] > $qte_accordee) {

                            return redirect()->back()->with('error','La quantité soumise ne peut être supérieure à celle exposée à cotation');

                        }

                    }

                }
            }

            $montant_total_brut = filter_var($request->montant_total_brut,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
        
            $info = 'Montant total brut';
            $error = $this->setDecimal($montant_total_brut,$info);

            if (isset($error)) {
                return redirect()->back()->with('error',$error);
            }

            

            if (isset($request->remise_generale)) {

                $remise_generale = filter_var($request->remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                $info = 'Remise générale';
                $error = $this->setDecimal($remise_generale,$info);

                if (isset($error)) {
                    $remise_generale = null;
                }

            }else{
                $remise_generale = NULL;
            }

            if (isset($request->taux_remise_generale)) {

                $taux_remise_generale = filter_var($request->taux_remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                $info = 'Taux remise générale';
                $error = $this->setDecimal($taux_remise_generale,$info);

                if (isset($error)) {
                    $taux_remise_generale = null;
                }

            }else{
                $taux_remise_generale = NULL;
            }

            $montant_total_net = filter_var($request->montant_total_net,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            
            $info = 'Montant total net';
            $error = $this->setDecimal($montant_total_net,$info);

            if (isset($error)) {
                return redirect()->back()->with('error',$error);
            }

            $montant_total_ttc = filter_var($request->montant_total_ttc,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
            
            $info = 'Montant total ttc';
            $error = $this->setDecimal($montant_total_ttc,$info);

            if (isset($error)) {
                return redirect()->back()->with('error',$error);
            }

            $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
               
            $info = 'Net à payer';
            $error = $this->setDecimal($net_a_payer,$info);

            if (isset($error)) {
                return redirect()->back()->with('error',$error);
            }

            

            if (isset($request->acompte)) {
                
                $taux_acompte = 0;

                $demande_achat = DemandeAchat::where('id',$request->id)
                ->first();
                if ($demande_achat!=null) {
                    
                    $taux_acompte = $demande_achat->taux_acompte;

                    if ($taux_acompte === null) {
                        $taux_acompte = 0;
                    }

                }

                if ($taux_acompte != 0) {
                    $montant_acompte = $net_a_payer * ($taux_acompte / 100) ;
                }else{
                    $montant_acompte = $net_a_payer * $taux_acompte;
                }
                

                if ($montant_acompte!=0) {
                    $acompte = true;
                }else{
                    $acompte = false;
                }
            }else{
                $montant_acompte = null;
                $acompte = false;
                $taux_acompte = null;
            }

            $dataStoreCotationFournisseur = [
                'organisations_id'=>$request->organisations_id,
                'demande_achats_id'=>$request->id,
                'acompte'=>$acompte,
                'montant_total_brut'=>$montant_total_brut,
                'taux_remise_generale'=>$taux_remise_generale,
                'remise_generale'=>$remise_generale,
                'montant_total_net'=>$montant_total_net,
                'tva'=>$request->tva,
                'montant_total_ttc'=>$montant_total_ttc,
                'assiete_bnc'=>$request->assiete_bnc,
                'taux_bnc'=>$request->taux_bnc,
                'net_a_payer'=>$net_a_payer,
                'taux_de_change'=>$taux_de_change,
                'taux_acompte'=>$taux_acompte,
                'montant_acompte'=>$montant_acompte,
                'devises_id'=>$devises_id,
            ];
            
            $cotation_fournisseur = $this->controller3->getCotationFournisseurs($request->organisations_id,$request->id);

            if ($cotation_fournisseur === null) {
                $cotation_fournisseur = $this->controller3->storeCotationFournisseur($dataStoreCotationFournisseur);

                $cotation_fournisseur_id = $cotation_fournisseur->id;
            }
            
            if ($cotation_fournisseur != null){
                $cotation_fournisseur_id = $cotation_fournisseur->id;

                $this->controller3->setCotationFournisseur($request->organisations_id,$request->id,$dataStoreCotationFournisseur);  
            }

            if (isset($cotation_fournisseur_id)) {

                $libelle = "Coté";
                $libelle_df = "Coté";

                $this->storeTypeStatutDemandeAchat($libelle);

                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);
            
                if ($type_statut_demande_achat != null) {

                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                }

                $this->setLastStatutDemandeAchat($request->id);
                $dataStatutDemandeAchat = [
                    'demande_achats_id'=>$request->id,
                    'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                    'date_debut'=>date('Y-m-d'),
                    'date_fin'=>date('Y-m-d'),
                    'commentaire'=>trim($request->commentaire),
                    'profils_id'=>$profils_id,
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

            // critère adjudication
                // récupérer la date de soumission à cotation

                $type_statut_demande_achats_libelles = ['Transmis pour cotation'];

                $statut_demande_achat = $this->getStatutDemandeAchatGroupe($request->id,$type_statut_demande_achats_libelles);

                if ($statut_demande_achat!=null) {
                    $created_at = $statut_demande_achat->created_at;

                    $select_cotation_fournisseur = $this->controller3->getCotationFournisseurs($request->organisations_id,$request->id);
                    
                    if ($select_cotation_fournisseur!=null) {
                        
                        $created_at_cotation = $select_cotation_fournisseur->updated_at;
                        $cotation_fournisseurs_id = $select_cotation_fournisseur->id;

                        
                        $date1 = new DateTime($created_at_cotation);

                        $date2 =  new DateTime($created_at);

                        $interval = $date1->diff($date2);

                        $valeur = ($interval->format('%h'));

                        $criteres_labelle = 'Diligence';

                        $critere = $this->controller3->getCritereByLibelle($criteres_labelle);

                        if ($critere!=null) {

                            $criteres_id = $critere->id;

                        }else{

                            $critere_mesure = 'Heure(s)';

                            $critere = $this->controller3->storeCritere($criteres_labelle,$critere_mesure);

                            $criteres_id = $critere->id;
                            
                        }

                        if ($criteres_id!=null) {

                            $critere_adjudication = $this->getCritereAdjudication($criteres_id,$request->id);

                            if ($critere_adjudication === null) {

                                $critere_adjudication = $this->controller3->storeCritereAdjudication($criteres_id,$request->id);                                

                                $critere_adjudications_id = $critere_adjudication->id;

                            }else{

                                $critere_adjudications_id = $critere_adjudication->id;

                            }

                        }
                        
                        $data_abjudication = [
                            'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
                            'critere_adjudications_id'=>$critere_adjudications_id,
                            'valeur'=>$valeur,
                        ];

                        $detail_adjudication = $this->getDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id);                        

                        if ($detail_adjudication===null) {

                            $this->storeDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$valeur);
                            
                        }else{
                            $this->setDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$valeur);
                        }

                        if (isset($criteres_id)) {
                            unset($criteres_id);
                        }

                        if (isset($critere_adjudications_id)) {
                            unset($critere_adjudications_id);
                        }
                    }

                }

            
                
            
            $valeur_qte = 0;
            foreach ($request->ref_articles as $item => $value) {


                $montant_ht[$item] = filter_var($request->montant_ht[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                $info = 'Montant ht';
                $error = $this->setDecimal($montant_ht[$item],$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }else{
                    $montant_ht[$item] = $montant_ht[$item] * 1;
                }

                if ($montant_ht[$item] <= 0 or $montant_ht[$item] === '') {

                    $this->deleteDetailCotation($cotation_fournisseur_id,$request->ref_articles[$item]);

                }


            }
            

            foreach ($request->ref_articles as $item => $value) {

                $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                try {
                    $qte[$item] = $qte[$item] * 1;
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                }
                
                if (gettype($qte[$item])!='integer') {
                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                }

                

                $prix_unit[$item] = filter_var($request->prix_unit[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                    
                $info = 'Prix unitaire';
                $error = $this->setDecimal($prix_unit[$item],$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }else{
                    $prix_unit[$item] = $prix_unit[$item] * 1;
                }

                $montant_ht[$item] = filter_var($request->montant_ht[$item],FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                $info = 'Montant ht';
                $error = $this->setDecimal($montant_ht[$item],$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }else{
                    $montant_ht[$item] = $montant_ht[$item] * 1;
                }

                $montant_ttc[$item] = $montant_ht[$item]*$taux_tva;
                


                if ( ($request->ref_articles[$item] != null) AND ($request->design_article[$item] != null) AND ($qte[$item] != null) AND ($prix_unit[$item] != null) AND ($montant_ht[$item] != null) AND ($montant_ttc[$item] != null) AND ($qte[$item] > 0) AND ($prix_unit[$item] > 0) AND ($montant_ttc[$item] > 0) ) {

                    // controle des quantité 

                    // controle des quantité 

                    $detail_demande_achat = $this->getDetailDemandeAchatByRefArticle($request->id,$request->ref_articles[$item]);

                    if ($detail_demande_achat!=null) {
                        $qte_accordee = $detail_demande_achat->qte_accordee;
                        if ($qte[$item] == $qte_accordee) {
                            $valeur_qte++;
                        }
                        
                    }

                    $setDetailDemandeAchat =  1;

                    if (isset($request->echantillon[$request->ref_articles[$item]])) {

                        $echantillon[$item] =  $request->echantillon[$request->ref_articles[$item]]->store('echantillonnage','public');
                        
                        
                    }else{

                        if (isset($request->echantillon_flag[$item])) {

                            if ($request->echantillon_flag[$item] == 1) {

                                $setDetailDemandeAchat =  2;

                            }elseif ($request->echantillon_flag[$item] == 0){

                                $setDetailDemandeAchat =  1;
                                $echantillon[$item] = null;
                                
                            } 
                            
                        }else{
                            $echantillon[$item] = null;
                        }

                        $echantillon[$item] = null;
                    }

                    $detail_cotations = $this->controller3->getDetailCotation($cotation_fournisseur_id,$request->ref_articles[$item]);                    
                    
                    $dataStoreDetailCotation = [
                        'cotation_fournisseurs_id'=>$cotation_fournisseur_id,
                        'ref_articles'=>$request->ref_articles[$item],
                        'qte'=>$qte[$item],
                        'prix_unit'=>$prix_unit[$item],
                        'remise'=>$request->remise[$item],
                        'montant_ht'=>$montant_ht[$item],
                        'montant_ttc'=>$montant_ttc[$item],
                        'echantillon'=>$echantillon[$item],
                    ]; 

                    if ($detail_cotations === null) {

                        $detail_cotation = $this->controller3->storeDetailCotation($dataStoreDetailCotation);

                    }
                    
                    if ($detail_cotations != null){
                        
                        if ($setDetailDemandeAchat == 2){
                            $dataStoreDetailCotation = [
                                'cotation_fournisseurs_id'=>$cotation_fournisseur_id,
                                'ref_articles'=>$request->ref_articles[$item],
                                'qte'=>$qte[$item],
                                'prix_unit'=>$prix_unit[$item],
                                'remise'=>$request->remise[$item],
                                'montant_ht'=>$montant_ht[$item],
                                'montant_ttc'=>$montant_ttc[$item],
                            ];
                        }
                        $this->controller3->setDetailCotation($detail_cotations->id,$dataStoreDetailCotation);

                        $detail_cotation = $this->controller3->getDetailCotationById($detail_cotations->id);                        

                    }


                }
            }

            // CRITERE RESPECT DES QUANTITE

                $criteres_labelle = 'Respect des quantités';

                $critere = $this->controller3->getCritereByLibelle($criteres_labelle);

                if ($critere!=null) {

                    $criteres_id = $critere->id;

                }else{

                    $critere_mesure = 'Point(s)';

                    $critere = $this->controller3->storeCritere($criteres_labelle,$critere_mesure);

                    $criteres_id = $critere->id;
                    
                }

                if ($criteres_id!=null) {

                    $critere_adjudication = $this->getCritereAdjudication($criteres_id,$request->id);

                    if ($critere_adjudication===null) {

                        $critere_adjudication = $this->controller3->storeCritereAdjudication($criteres_id,$request->id);                                

                        $critere_adjudications_id = $critere_adjudication->id;

                    }else{
                        $critere_adjudications_id = $critere_adjudication->id;
                    }

                }

                $data_abjudication = [
                    'cotation_fournisseurs_id'=>$cotation_fournisseurs_id,
                    'critere_adjudications_id'=>$critere_adjudications_id,
                    'valeur'=>$valeur_qte,
                ];

                $detail_adjudication = $this->getDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id);

                if ($detail_adjudication===null) {

                    $this->storeDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$valeur_qte);

                }else{

                    $this->setDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$valeur_qte);

                }

                if (isset($criteres_id)) {
                    unset($criteres_id);
                }

                if (isset($critere_adjudications_id)) {
                    unset($critere_adjudications_id);
                }


            // CRITERE COUT

            

                $criteres_labelle = 'Numéraire';

                $critere = $this->controller3->getCritereByLibelle($criteres_labelle);

                if ($critere!=null) {

                    $criteres_id = $critere->id;

                }else{

                    $critere_mesure = 'FCFA';

                    $critere = $this->controller3->storeCritere($criteres_labelle,$critere_mesure);

                    $criteres_id = $critere->id;
                    
                }

                if ($criteres_id!=null) {

                    $critere_adjudication = $this->getCritereAdjudication($criteres_id,$request->id);

                    if ($critere_adjudication===null) {

                        $critere_adjudication = $this->controller3->storeCritereAdjudication($criteres_id,$request->id);                                

                        $critere_adjudications_id = $critere_adjudication->id;

                    }else{
                        $critere_adjudications_id = $critere_adjudication->id;
                    }

                }

                $detail_adjudication = $this->getDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id);

                if ($detail_adjudication===null) {

                    $this->storeDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$request->net_a_payer);

                }else{

                    $this->setDetailAdjudication($cotation_fournisseurs_id,$critere_adjudications_id,$request->net_a_payer);
                }

                if (isset($criteres_id)) {
                    unset($criteres_id);
                }

                if (isset($critere_adjudications_id)) {
                    unset($critere_adjudications_id);
                }


            if ($detail_cotation!=null) {
                // notifier l'émetteur
                $subject = 'Cotation fournisseur';

                // utilisateur connecté
                    $email = auth()->user()->email;
                    $this->notifDemandeAchat($email,$subject,$request->id);
                //

                // notifier le gestionnaire des achats
                $profils = ['Gestionnaire des achats','Responsable des achats'];
                $this->notifDemandeAchat2($subject,$request->id,$profils);

                // notifier le gestionnaire des achats

                // notifier responsable des achats
        
                
                return redirect('demande_achats/index')->with('success','Cotation enregistrée');
            }else{
                return redirect()->back()->with('error','Echec de l\'enregistrement de la cotation');
            }

        }

        //dd($profils_id,$request);
    }

    public function create_bc($cotationFournisseur)
    {
        
        $flag_engagement = 0;
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($cotationFournisseur);
        } catch (DecryptException $e) {
            //
        }

        $cotationFournisseur = CotationFournisseur::findOrFail($decrypted);

        $demandeAchat = null;
        if ($cotationFournisseur != null) {
            $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
        }

        $etape = "cotation_fournisseur_create_bc";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }

        $type_profils_lists = ['Administrateur fonctionnel','Responsable des achats','Gestionnaire des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Directeur Général','Signataire'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        // dernier statut de la demande
        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        $statut_demande_achat = $this->getLastStatutDemandeAchat($cotationFournisseur->demande_achats_id);

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;
        }

        $type_statut_demande_achats_libelles = ['Validé'];

        $signataire = $this->getStatutDemandeAchatGroupe($cotationFournisseur->demande_achats_id,$type_statut_demande_achats_libelles);

        $agents = $this->getAgentFonctions();    
        
        $signataires = $this->getAgentFonctionsSignataireDemandeAchat($cotationFournisseur->demande_achats_id);

        $profil_fournisseur = null;
        $retrait_bc = null;

        $type_profils_name_frs = ['Fournisseur'];
        $retrait_bc = $this->getSelectionAdjudicationGroupe($cotationFournisseur->id,$type_profils_name_frs);
        
        if ($retrait_bc!=null) {
            $profil_fournisseur = $retrait_bc->name;
        }

        $entete = "Demande d'achats";
        $value_button2 = null;
        $button2 = null;
        $value_button = null;
        $button = null;
        $edit_signataire = null;

        if ($type_profils_name === 'Responsable des achats') {

            if ($libelle != 'Fournisseur sélectionné' && $libelle != 'Annulé (Responsable des achats)'  && $libelle != 'Rejeté (Responsable DMP)' && $libelle != 'Validé' && $libelle != 'Annulé (Fournisseur)') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Fournisseur sélectionné' or $libelle === 'Annulé (Responsable des achats)'  or $libelle === 'Rejeté (Responsable DMP)' or $libelle === 'Validé' or $libelle === 'Annulé (Fournisseur)'){

                $entete = "Transfert du dossier au Responsable DMP";

                $edit_signataire = 1;

                if ($libelle != 'Annulé (Responsable des achats)'){
                    $value_button2 = "annuler_r_achat";
                    $button2 = "Annuler";
                }

                if ($libelle === 'Validé'){

                    $entete = "Editer le bon de commande";

                    $value_button = "editer";
                    $button = "Éditer";

                }else{

                    $value_button = "transfert_r_cmp";
                    $button = "Transférer";

                }
                

                
            }

        }elseif ($type_profils_name === 'Gestionnaire des achats') {

            if ($libelle != 'Validé' && $libelle != 'Annulé (Fournisseur)' && $libelle != 'Édité') {

                return redirect()->back()->with('error','Accès refusé');

            }
            
            if ($libelle === 'Validé' or $libelle === 'Annulé (Fournisseur)' or $libelle === 'Édité'){

                $entete = "Editer le bon de commande";

                $edit_signataire = 1;

                if ($libelle != 'Annulé (Gestionnaire des achats)'){
                    $value_button2 = "annuler_g_achat";
                    $button2 = "Annuler";
                }

                if ($libelle === 'Validé' or $libelle === 'Édité'){

                    $value_button = "editer";
                    $button = "Éditer";

                    $value_button2 = null;
                    $button2 = null;

                    $value_button2 = "annuler_g_achat";
                    $button2 = "Annuler";

                }
                  
            }

        }elseif ($type_profils_name === 'Responsable DMP') {

            if ($libelle != 'Transmis (Responsable DMP)' && $libelle != 'Rejeté (Responsable Contrôle Budgétaire)' && $libelle != 'Rejeté (Responsable DFC)') {
            
                return redirect()->back()->with('error','Accès refusé');

            }

            if ($libelle === 'Transmis (Responsable DMP)' or $libelle === 'Rejeté (Responsable Contrôle Budgétaire)' or $libelle === 'Rejeté (Responsable DFC)') {
            
                $entete = "Validation du dossier";
                $value_button2 = "invalider_cmp";
                $button2 = "Rejeter";

                $value_button = "valider_cmp";
                $button = "Viser";

            }

        }elseif ($type_profils_name === 'Responsable contrôle budgetaire') {

            if ($libelle != 'Transmis (Responsable Contrôle Budgétaire)' && $libelle != 'Rejeté (Chef Département DCG)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable Contrôle Budgétaire)' or $libelle === 'Rejeté (Chef Département DCG)') {
                
                $entete = "CONTRÔLE BUDGÉTAIRE DE LA DEMANDE D'ACHATS";
                $value_button2 = "invalider_dcg";
                $button2 = "Rejeter";

                $value_button = "valider_dcg";
                $button = "Viser";
            }

        }elseif ($type_profils_name === 'Chef Département DCG') {

            if ($libelle != 'Transmis (Chef Département DCG)' && $libelle != 'Rejeté (Responsable DCG)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Chef Département DCG)' or $libelle === 'Rejeté (Responsable DCG)') {
                
                $entete = "Contrôle budgétaire de la demande d'achats";
                $value_button2 = "invalider_d_dcg";
                $button2 = "Rejeter";

                $value_button = "valider_d_dcg";
                $button = "Viser";
            }
            
        }elseif ($type_profils_name === 'Responsable DCG') {

            if ($libelle != 'Transmis (Responsable DCG)' && $libelle != 'Rejeté (Directeur Général Adjoint)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable DCG)' or $libelle === 'Rejeté (Directeur Général Adjoint)') {
                $entete = "Visa de la demande d'achats";

                $value_button2 = "invalider_r_dcg";
                $button2 = "Rejeter";

                $value_button = "valider_r_dcg";
                $button = "Viser";
            }
            
        }elseif ($type_profils_name === 'Directeur Général Adjoint') {

            if ($libelle != 'Transmis (Directeur Général Adjoint)' && $libelle != 'Rejeté (Directeur Général)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Directeur Général Adjoint)' or $libelle === 'Rejeté (Directeur Général)') {

                $entete = "Validation du dossier";
                $value_button2 = "invalider_r_dgaaf";
                $button2 = "Rejeter";

                $value_button = "valider_r_dgaaf";
                $button = "Valider";
            }
            
        }elseif ($type_profils_name === 'Directeur Général') {

            if ($libelle != 'Transmis (Directeur Général)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Directeur Général)') {
                $entete = "Validation du dossier";

                $value_button2 = "invalider_r_dg";
                $button2 = "Rejeter";

                $value_button = "valider_r_dg";
                $button = "Viser";
            }
            
        }elseif ($type_profils_name === 'Responsable DFC') {

            if ($libelle != 'Transmis (Responsable DFC)') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Transmis (Responsable DFC)') {
                $entete = "Validation du dossier";
                $value_button2 = "invalider_r_dfc";
                $button2 = "Rejeter";

                $value_button = "valider_r_dfc";
                $button = "Viser";
            }
            
        }elseif ($type_profils_name === 'Fournisseur') {

            if ($libelle != 'Édité') {
                return redirect()->back()->with('error','Accès refusé');
            }

            if ($libelle === 'Édité') {
                $entete = "Retrait du bon de commande";

                $value_button2 = "annuler_fournisseur";
                $button2 = "Annuler";

                $value_button = "retirer";
                $button = "Retirer";
            }
            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        

                        
                        
        $credit_budgetaires_select = null;
        $griser = 1;

        $demande_achats = $this->getDetailCotationFournisseursSelectionnees($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);

        $demande_achat_info = $this->getDetailCotationFournisseur($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);

        $statut = $this->getCategorieDemandeAchat($cotationFournisseur->demande_achats_id);
        
        $credit_budgetaire_structure = null;
        if ($demande_achat_info!=null) {

            $credit_budgetaire_structure = $this->getCreditBudgetaireById($demande_achat_info->credit_budgetaires_id);

        }

        $affiche_zone_struture = 1;
        
        $type_piece = "Demande d'achats";

        $piece_jointes = $this->getPieceJointes($cotationFournisseur->demande_achats_id, $type_piece);

        $statut_demande_achats = $this->getStatutDemandeAchats($cotationFournisseur->demande_achats_id);
        

        if($demandeAchat != null){
            $flag_engagement = $demandeAchat->flag_engagement;

            if($flag_engagement === 0){
                try {
                    $param = $demandeAchat->exercice.'-'.$demandeAchat->code_gestion.'-'.$demandeAchat->code_structure.'-'.$demandeAchat->ref_fam;
        
                    $this->storeCreditBudgetaireByWebService($param, $demandeAchat->ref_depot);
                } catch (\Throwable $th) {
                }
            }
            
        }

        $credit_budgetaires_credit = null;

        $select_disponible = $this->getCreditBudgetaireDisponibleByDemandeAchatId($cotationFournisseur->demande_achats_id);
        if($select_disponible != null){
            $credit_budgetaires_credit = $select_disponible->credit - $select_disponible->consommation_non_interfacee;
        }

        $commande = $this->getCommande($cotationFournisseur->demande_achats_id); 

        if($commande != null){
            if($commande->solde_avant_op != null){
                if($flag_engagement === 1){
                    $credit_budgetaires_credit = $commande->solde_avant_op;
                }
                
            }
        }

        $signataires = $this->getSignataires($cotationFournisseur->demande_achats_id);
        
        return view('cotation_fournisseurs.create_bc',[
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'cotation_fournisseurs_id'=>$cotationFournisseur->id,
            'profil_fournisseur'=>$profil_fournisseur,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'signataire'=>$signataire,
            'statut'=>$statut,
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'piece_jointes'=>$piece_jointes,
            'agents'=>$agents,
            'signataires'=>$signataires,
            'edit_signataire'=>$edit_signataire,
            'entete'=>$entete,
            'value_button2'=>$value_button2,
            'button2'=>$button2,
            'value_button'=>$value_button,
            'button'=>$button,
            'type_profils_name'=>$type_profils_name,
            'statut_demande_achats'=>$statut_demande_achats,
            'credit_budgetaires_credit'=>$credit_budgetaires_credit
        ]);

    }

    public function store_bc(Request $request){
        
        $controllerPerdiem = new ControllerPerdiem();
        $signatureController = new SignatureController();
        $type_operations_libelle = "Demande d'achats" ;
        $flag_engagement = 0;
        $edit_signataire_demande_de_fonds = null;
        $edit_signataire_bcs = null;
        $ordre_de_signature = null;
        $ordre_de_signature_demande_de_fonds = null;
        
        $request->validate([
            'net_a_payer'=>['required','string'],
            'montant_bnc'=>['nullable','numeric'],
            'taux_bnc'=>['nullable','numeric'],
            'assiette'=>['nullable','string'],
            'montant_total_ttc'=>['required','string'],
            'montant_tva'=>['nullable','string'],
            'tva'=>['nullable','numeric'],
            'montant_total_net'=>['required','string'],
            'remise_generale'=>['nullable','string'],
            'montant_total_brut'=>['required','string'],
            'montant_ht'=>['required','array'],
            'remise'=>['required','array'],
            'prix_unit'=>['required','array'],
            'qte_accordee'=>['required','array'],
            'qte'=>['required','array'],
            'design_article'=>['required','array'],
            'ref_articles'=>['required','array'],
            'ref_fam'=>['required','numeric'],
            'libelle_gestion'=>['required','string'],
            'code_gestion'=>['required','string'],
            'denomination'=>['required','string'],
            'organisations_id'=>['required','numeric'],
            'cotation_fournisseurs_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'submit'=>['required','string'],
            'commentaire'=>['nullable','string'],
        ]);

        $type_profils_name = null;

        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }

        if($type_profils_name != 'Fournisseur'){
            $request->validate([
                'solde_avant_op'=>['required','string'],
            ]);
        }
        
        $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first();

        $demandeAchat = null;
        $signataires = [];

        if ($cotationFournisseur != null) {

            $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
            $signataires = $this->getSignataires($cotationFournisseur->demande_achats_id);
        }

        $etape = "cotation_fournisseur_store_bc";

       

        $type_profils_lists = ['Responsable des achats','Gestionnaire des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC','Directeur Général','Signataire'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        $montant_acompte = null;

        $taux_de_change = 1;

        $demande_achat = $this->getDetailCotationFournisseur($demandeAchat->id,$request->cotation_fournisseurs_id);

        if ($demande_achat!=null) {

            $flag_engagement = $demande_achat->flag_engagement;

            $demande_achats_id = $demande_achat->id;

            $taux_de_change = $demande_achat->taux_de_change;

            if($demande_achat->acompte == 1){
                $montant_acompte = $demande_achat->montant_acompte * $taux_de_change;
            }

            $statut_demande_achat = $this->getLastStatutDemandeAchat($demande_achats_id);

            if ($statut_demande_achat!=null) {

                $libelle = $statut_demande_achat->libelle; 

                $this->getWorkflowDemandeAchat($request->submit,$libelle);
                
            }

        }else{

            $this->getWorkflowDemandeAchat2($request->submit);
            
        }

        $type_profils_names = [];
        $set_engagement = null;
        $engagement_depense_annulation = null;
        $engagement_depense = null;

        if(isset($request->solde_avant_op)){
            $solde_avant_op = filter_var($request->solde_avant_op, FILTER_SANITIZE_NUMBER_INT);
        }
        

        if (isset($demande_achats_id)) {

            $net_a_payers = 0;

            if(isset($request->net_a_payer) && isset($taux_de_change)){

                $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
               
                $info = 'Net à payer';
                $error = $this->setDecimal($net_a_payer,$info);
    
                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }
    
                $net_a_payers = $net_a_payer * $taux_de_change;
            }

            // STATUT DE LA DEMANDE

            if ($request->submit === 'transfert_r_cmp') {

                $libelle = "Transmis (Responsable DMP)";
                $libelle_df = "Transmis (Responsable DMP)";
                $subject = "Dossier transmis au Responsable DMP";
                $type_profils_names = ['Gestionnaire des achats','Responsable DMP'];

            }elseif ($request->submit === 'annuler_r_achat') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Annulé (Responsable des achats)";
                $libelle_df = "Annulé (Responsable des achats)";
                $subject = "Dossier annulé par le Responsable des achats";
                $type_profils_names = ['Gestionnaire des achats'];

            }elseif ($request->submit === 'valider_cmp') {

                $libelle = "Visé (Responsable DMP)";
                $libelle_send = "Transmis (Responsable Contrôle Budgétaire)";
                $libelle_df = "Transmis (Responsable Contrôle Budgétaire)";
                $subject = "Validation du bon de commande (Responsable DMP)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable contrôle budgetaire','Responsable DCG'];

                if($flag_engagement === 0){
                    $engagement_depense = 1;
                    $set_engagement = 1;
                }
                $edit_signataire_demande_de_fonds = 1;

            }elseif ($request->submit === 'invalider_cmp') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Rejeté (Responsable DMP)";
                $libelle_df = "Annulé (Responsable DMP)";
                $subject = "Invalidation du bon de commande (Responsable DMP)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats'];

                
                if($flag_engagement === 1){
                    $engagement_depense_annulation = 1;
                    $set_engagement = 1;
                }

            }elseif ($request->submit === 'transfert_dcg') {

                $libelle = "Transmis (Responsable Contrôle Budgétaire)";
                $libelle_df = "Transmis (Responsable Contrôle Budgétaire)";
                $subject = "Dossier transmis au Responsable Contrôle Budgétaire";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable contrôle budgetaire','Responsable DCG'];
                
            }elseif ($request->submit === 'valider_dcg') {

                $libelle = "Visé (Responsable Contrôle Budgétaire)";
                $libelle_send = "Transmis (Chef Département DCG)";
                $libelle_df = "Transmis (Chef Département Contrôle Budgétaire)";
                $subject = "Validation du bon de commande (Responsable Contrôle Budgétaire)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Chef Département DCG'];

            }elseif ($request->submit === 'invalider_dcg') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Rejeté (Responsable Contrôle Budgétaire)";
                $libelle_df = "Annulé (Responsable contrôle budgetaire)";
                $subject = "Invalidation du bon de commande (Responsable Contrôle Budgétaire)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable DCG'];

                

            }elseif ($request->submit === 'transfert_d_dcg') {

                $libelle = "Transmis (Chef Département DCG)";
                $libelle_df = "Transmis (Chef Département Contrôle Budgétaire)";

                $subject = "Dossier transmis au Chef Département DCG";
                
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Chef Département DCG'];
                


            }elseif ($request->submit === 'valider_d_dcg') {
                
                $libelle = "Visé (Chef Département DCG)";
                $subject = "Validation du bon de commande (Chef Département DCG)";
                $libelle_send = "Transmis (Responsable DCG)";
                $libelle_df = "Transmis (Responsable DCG)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Responsable DCG'];
                

            }elseif ($request->submit === 'invalider_d_dcg') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);
                $libelle = "Rejeté (Chef Département DCG)";
                $libelle_df = "Annulé (Chef Département Contrôle Budgétaire)";
                $subject = "Invalidation du bon de commande (Chef Département DCG)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Responsable DCG'];                    

            }elseif ($request->submit === 'transfert_r_dcg') {

                $libelle = "Transmis (Responsable DCG)";
                $libelle_df = "Transmis (Responsable DCG)";
                $subject = "Dossier transmis au Responsable DCG";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Responsable DCG'];
                
            }elseif ($request->submit === 'valider_r_dcg') {


                $libelle = "Visé (Responsable DCG)";
                $subject = "Validation du bon de commande (Responsable DCG)";
                
                $libelle_send = "Transmis (Directeur Général Adjoint)";
                $libelle_df = "Transmis (Directeur Général Adjoint)";

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Directeur Général Adjoint'];
                

            }elseif ($request->submit === 'invalider_r_dcg') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Rejeté (Responsable DCG)";
                $libelle_df = "Annulé (Responsable DCG)";
                $subject = "Invalidation du bon de commande (Responsable DCG)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG'];

                

            }elseif ($request->submit === 'transfert_r_dgaaf') {
                
                $libelle = "Transmis (Directeur Général Adjoint)";
                $libelle_df = "Transmis (Directeur Général Adjoint)";

                $subject = "Dossier transmis au Directeur Général Adjoint";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Directeur Général Adjoint'];

            }elseif ($request->submit === 'valider_r_dgaaf') {

                $libelle = "Visé (Directeur Général Adjoint)";
                $subject = "Validation du bon de commande (Directeur Général Adjoint)";

                if ($net_a_payers > 5000000) {

                    $libelle_send = "Transmis (Directeur Général)";
                    $libelle_df = "Transmis (Directeur Général)";

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général'];

                }else{

                    $libelle_send = "Validé";
                    $libelle_df = "Validé";

                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];

                    $edit_signataire_demande_de_fonds = 1;
                    $edit_signataire_bcs = 1;
                    $ordre_de_signature = 1;
                    $ordre_de_signature_demande_de_fonds = 1;
                }
                

            }elseif ($request->submit === 'invalider_r_dgaaf') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Rejeté (Directeur Général Adjoint)";
                $libelle_df = "Annulé (Directeur Général Adjoint)";
                $subject = "Invalidation du bon de commande (Directeur Général Adjoint)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG'];

            }elseif ($request->submit === 'transfert_r_dg') {

                $libelle = "Transmis (Directeur Général)";
                $libelle_df = "Transmis (Directeur Général)";

                $subject = "Dossier transmis au Directeur Général";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général'];

                

            }elseif ($request->submit === 'valider_r_dg') {
                
                $libelle = "Visé (Directeur Général)";
                $subject = "Validation du bon de commande (Directeur Général)";

                $libelle_send = "Validé";
                $libelle_df = "Validé";

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];

                $edit_signataire_demande_de_fonds = 1;
                $edit_signataire_bcs = 1;
                $ordre_de_signature = 1;
                $ordre_de_signature_demande_de_fonds = 1;
                

            }elseif ($request->submit === 'invalider_r_dg') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Rejeté (Directeur Général)";
                $libelle_df = "Annulé (Directeur Général)";

                $subject = "Invalidation du bon de commande (Directeur Général)";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];

                

            }elseif ($request->submit === 'transfert_r_achat') {

                $libelle = "Validé";
                $libelle_df = "Transmis (Responsable DFC)";
                $subject = "Dossier validé";
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint'];
                

            }elseif ($request->submit === 'editer') {
                
                $validate = $request->validate([
                    'profil_fonctions_id'=>['required','array'],
                    'mle'=>['required','array'],
                    'nom_prenoms'=>['required','array'],
                ]);

                $libelle = "Édité";
                $libelle_df = "Édité";
                /*
                $this->storeTypeOperation($type_operations_libelle);
                $type_operation = $this->getTypeOperation($type_operations_libelle);
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                    $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($demande_achats_id,$type_operations_id);

                    if($demande_fond_bon_commande != null){                        

                        if(isset($libelle_df)){

                            if (isset($request->mle)) {

                                if (count($request->mle) > 0) {

                                    $profil_fonction_array = null;
                                    if(isset($request->profil_fonctions_id)){
                                        if(count($request->profil_fonctions_id) > 0) {
                                            $profil_fonction_array = $request->profil_fonctions_id;

                                            if($profil_fonction_array != null) {
                                                $type_operations_libelle_explode = "Demande de fonds";

                                                $this->setSignataire($type_operations_libelle_explode,$demande_fond_bon_commande->demande_fonds_id,$profil_fonction_array);
                                            }
                                        }
                                    }
                                        

                                    foreach ($request->mle as $item => $value) {

                                            $profil_fonction = $this->getProfilFonctionByMle($value);

                                            if($profil_fonction != null){

                                                $this->storeSignataireDemandeFond2($profil_fonction->id,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'));

                                            }
                                        

                                    }

                                }

                            }

                        }
                        

                    }
                }*/
                
                /*
                $controllerPerdiem = new ControllerPerdiem();
                $signature = $controllerPerdiem->verificationSignataire($signataires,$request->profil_fonctions_id);
                if($signature != 0){
                    $this->storeSignataireDemandeAchat($request,$demande_achats_id,Session::get('profils_id'));
                }*/

                $subject = "Bon de commande édité";
                
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];

                $notifier_fournisseur = 1;

                /*

                $data_path = [
                    'type_operations_libelle'=>'bcs',
                    'reference'=>$demande_achat->num_bc,
                    'extension'=>'pdf'
                ];
                
                $public_path = $controllerPerdiem->publicPath($data_path);
                
                if(@file_exists($public_path) === false){
                    $signature = 1;
                }

                if($signature != 0){

                    $printController = new PrintController();
                    $printController->printDemandeAchat(Crypt::encryptString($demande_achats_id));

                }*/
                

            }elseif ($request->submit === 'annuler_g_achat') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                

                $libelle = "Annulé (Gestionnaire des achats)";
                $libelle_df = "Annulé (Gestionnaire des achats)";

                if($demandeAchat != null){
                    $user = $this->getInfoUserByProfilId(Session::get('profils_id'));

                    if($user != null){

                        $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

                        $net_a_payers = $net_a_payer * $taux_de_change;
                        $net_a_payers_signe = -1 * $net_a_payers ;

                        if($montant_acompte != null){
                            $montant_comptabilisation = $net_a_payers - $montant_acompte;

                            $net_a_payers_signe = -1 * $montant_comptabilisation;
                        }

                        $type_piece = "ENG";
                        $reference_piece = $demandeAchat->num_bc;
                        $compte = $demandeAchat->ref_fam;
                        $montant_comptabilisation = $net_a_payers_signe;
                        $date_transaction = date('Y-m-d H:i:s');
                        $mle = $user->mle;
                        $code_structure = $demandeAchat->code_structure;
                        $code_section = $demandeAchat->code_structure."01";

                        $flag_acompte = 0;

                        $data = [
                            'type_piece'=>$type_piece,
                            'reference_piece'=>$reference_piece,
                            'compte'=>$compte,
                            'montant'=>$montant_comptabilisation,
                            'date_transaction'=>$date_transaction,
                            'mle'=>$mle,
                            'code_structure'=>$code_structure,
                            'code_section'=>$code_section,
                            'ref_depot'=>$user->ref_depot,
                            'acompte'=>$flag_acompte,
                            'exercice'=>$demande_achat->exercice,
                            'code_gestion'=>$demande_achat->code_gestion,
                        ];

                        $this->storeComptabilisationEcriture($data);

                        if($montant_acompte != null){

                            $montant_comptabilisation = -1 * $montant_acompte;
                            $flag_acompte = 1;

                            $data_acompte = [
                                'type_piece'=>$type_piece,
                                'reference_piece'=>$reference_piece,
                                'compte'=>$compte,
                                'montant'=>$montant_comptabilisation,
                                'date_transaction'=>$date_transaction,
                                'mle'=>$mle,
                                'code_structure'=>$code_structure,
                                'code_section'=>$code_section,
                                'ref_depot'=>$user->ref_depot,
                                'acompte'=>$flag_acompte,
                                'exercice'=>$demande_achat->exercice,
                                'code_gestion'=>$demande_achat->code_gestion,
                            ];

                            $this->storeComptabilisationEcriture($data_acompte);
                            
                        }
                    }
                }

                $subject = "Dossier annulé par le Gestionnaire des achats";
                $type_profils_names = ['Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];

            }elseif ($request->submit === 'retirer') {
                $libelle = "Retiré (Frs.)";
                $libelle_df = "Retiré (Frs.)";

                $validate = $request->validate([
                    'date_livraison_prevue'=>['required','date'],
                ]);

                $subject = "Bon de commande retiré";

                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];

                

            }elseif ($request->submit === 'annuler_fournisseur') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Annulé (Fournisseur)";
                $libelle_df = "Annulé (Fournisseur)";

                $subject = "Annulation du bon de commande (Fournisseur)";
                
                $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];

                

            }

            $info_user_connect =  $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($info_user_connect != null) {

                $profil_fonction = $this->getProfilFonctionByAgentId($info_user_connect->agents_id);

                if($profil_fonction != null){
                    $profil_fonctions_id = $profil_fonction->id;
                }
            }

            if($edit_signataire_demande_de_fonds === 1){

                $this->storeTypeOperation($type_operations_libelle);

                $type_operation = $this->getTypeOperation($type_operations_libelle);
                if ($type_operation!=null) {

                    $type_operations_id = $type_operation->id;

                    $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($demande_achats_id,$type_operations_id);

                    if($demande_fond_bon_commande != null){                        
                        $demande_fonds_id = $demande_fond_bon_commande->demande_fonds_id;

                    }
                }
                
            }

            if($edit_signataire_demande_de_fonds === 1 && isset($libelle_df) && isset($profil_fonctions_id) && isset($demande_fonds_id)){
                $this->storeSignataireDemandeFond2($profil_fonctions_id,$demande_fonds_id,Session::get('profils_id'));
            }

            if($edit_signataire_bcs === 1 && isset($profil_fonctions_id)){
                $this->storeSignataireDemandeAchat2($profil_fonctions_id, $demande_achats_id, Session::get('profils_id'));
            }

            if($ordre_de_signature === 1){

                $data_ordre_signature = [
                    'type_operations_libelle'=>$type_operations_libelle,
                    'operations_id'=>$demande_achats_id,
                    'reference'=>$demandeAchat->num_bc,
                    'extension'=>'pdf',
                    'signature'=>1
                ];

                $signatureController->OrdreDeSignature($data_ordre_signature);

            }

            if($ordre_de_signature_demande_de_fonds === 1 && isset($demande_fonds_id)){

                $demande_fond = $this->getDemandeFond($demande_fonds_id);
                if($demande_fond != null){
                    $type_operations_libelle_demande_fonds = "Demande de fonds";
                    $data_ordre_signature = [
                        'type_operations_libelle'=>$type_operations_libelle_demande_fonds,
                        'operations_id'=>$demande_fond->id,
                        'reference'=>$demande_fond->num_dem,
                        'extension'=>'pdf',
                        'signature'=>1
                    ];
    
                    $signatureController->OrdreDeSignature($data_ordre_signature);
                }
                

            }

            $this->storeTypeStatutDemandeAchat($libelle);

            $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle);

            if ($type_statut_demande_achat != null) {
                
                $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                $this->setLastStatutDemandeAchat($demande_achats_id);
                
                $dataStatutDemandeAchat = [
                    'demande_achats_id'=>$demande_achats_id,
                    'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                    'date_debut'=>date('Y-m-d'),
                    'date_fin'=>date('Y-m-d'),
                    'commentaire'=>trim($request->commentaire),
                    'profils_id'=>Session::get('profils_id'),
                ];

                $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
            }

            if (isset($libelle_send)) {

                $this->storeTypeStatutDemandeAchat($libelle_send);

                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_send);

                if ($type_statut_demande_achat != null) {
                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                    $this->setLastStatutDemandeAchat($demande_achats_id);
                    
                    $dataStatutDemandeAchat = [
                        'demande_achats_id'=>$demande_achats_id,
                        'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                        'date_debut'=>date('Y-m-d'),
                        'date_fin'=>date('Y-m-d'),
                        'commentaire'=>trim($request->commentaire),
                        'profils_id'=>Session::get('profils_id'),
                    ];

                    $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                }
            }

            $this->storeTypeOperation($type_operations_libelle);

            $type_operation = $this->getTypeOperation($type_operations_libelle);
            if ($type_operation!=null) {

                $type_operations_id = $type_operation->id;

                $demande_fond_bon_commande = $this->getDemandeFondBonCommandeByBc($demande_achats_id,$type_operations_id);

                if($demande_fond_bon_commande != null){

                    if(isset($libelle_df)){
                        $this->storeStatutDemandeFond($libelle_df,$demande_fond_bon_commande->demande_fonds_id,Session::get('profils_id'),$request->commentaire);
                    }                            

                }
            }
            

            if (isset($request->date_livraison_prevue)) {

                Commande::where('demande_achats_id',$demande_achats_id)->update([
                    'date_livraison_prevue'=>$request->date_livraison_prevue
                ]);

            }

            if ($set_engagement != null) {

                if($engagement_depense != null){
                    $flag_engagement = 1;
                    $signe = 1;
                }

                if($engagement_depense_annulation != null){
                    $flag_engagement = 0;
                    $signe = -1;
                    $solde_avant_op = null;
                }

                $data_solde_avant_operation = [
                    'type_operations_libelle'=>$type_operations_libelle,
                    'operations_id'=>$demande_achats_id,
                    'solde_avant_op'=>$solde_avant_op,
                    'flag_engagement'=>$flag_engagement
                ];

                $controllerPerdiem->setSoldeAvantOperation($data_solde_avant_operation);

                $type_piece = "ENG";
                
                $data_all = [
                    'type_operations_libelle'=>$type_operations_libelle,
                    'profils_id'=>Session::get('profils_id'),
                    'operations_id'=>$demande_achats_id,
                    'type_piece'=>$type_piece,
                    'signe'=>$signe,
                    'flag_acompte'=>0,
                    'cotation_fournisseurs_id'=>$request->cotation_fournisseurs_id
                ];

                $data_comptabilisation = $controllerPerdiem->procedureComptabilisationEcriture($data_all);

                if($data_comptabilisation != null){
                    $this->storeComptabilisationEcriture($data_comptabilisation);
                }

                if($montant_acompte != null){
                    
                    $data_all = [
                        'type_operations_libelle'=>$type_operations_libelle,
                        'profils_id'=>Session::get('profils_id'),
                        'operations_id'=>$demande_achats_id,
                        'type_piece'=>$type_piece,
                        'signe'=>$signe,
                        'flag_acompte'=>1,
                        'cotation_fournisseurs_id'=>$request->cotation_fournisseurs_id
                    ];

                    $data_comptabilisation = $controllerPerdiem->procedureComptabilisationEcriture($data_all);

                    if($data_comptabilisation != null){
                        $this->storeComptabilisationEcriture($data_comptabilisation);
                    }
                }

                $data_param = [
                    'type_piece'=>$type_piece,
                    'compte'=>$demandeAchat->ref_fam,
                    'exercice'=>$demandeAchat->exercice,
                    'code_structure'=>$demandeAchat->code_structure,
                    'code_gestion'=>$demandeAchat->code_gestion,
                ];

                $operation_non_interfacee = $this->getOperationEncoursNonComptabilisee($data_param); 

                $this->setCreditBudgetaireConsoNonComptabiliseByArray($operation_non_interfacee);

            }

            // FIN STATUT DEMANDE
            
            // notification mail
                $email = auth()->user()->email;

                $this->notifDemandeAchat($email,$subject,$demande_achats_id);

                $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);

            

                if (isset($notifier_fournisseur)) {
                    // notifier le fournisseur

                        $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);

                    //
                }
            //

            return redirect('demande_achats/index')->with('success',$subject);

        }else{

            $libelle = $this->getWorkflowDemandeAchatError($request->submit);
            
            return redirect()->back()->with('error',$libelle);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function show($cotationFournisseur)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($cotationFournisseur);
        } catch (DecryptException $e) {
            //
        }

        $cotationFournisseur = CotationFournisseur::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }        

        
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        

        $taxes = Taxe::whereIn('ref_taxe',['67','68','69'])->get();

        $libelle = null;
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;
        $devise_cotation = null;

        $type_statut_demande_achats_libelles = ['Transmis pour cotation'];

        $statut_demande_achat = $this->getStatutDemandeAchatGroupe($cotationFournisseur->demande_achats_id,$type_statut_demande_achats_libelles);

        if ($statut_demande_achat!=null) {

            $libelle = $statut_demande_achat->libelle;
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;

        }
        
        $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
        if($demandeAchat === null){
            return redirect()->back()->with('error', 'Demande introuvable');
        }
        $gestions = $this->getGestion();        

        $credit_budgetaires = $this->getFamilleDemandeAchat($demandeAchat->ref_fam);
        
        $credit_budgetaires_select = null;
        $griser = 1;

        $demande_achats = $this->getDemandeAchatValiders($demandeAchat->id); 

        $demande_achat_info = $this->getDemandeAchat($demandeAchat->id);

        $cotation_demande_achat = null;
        $cotation_fournisseurs_id = $cotationFournisseur->id;

        $cotation_demande_achat = $this->getCotationFournisseur($demandeAchat->id,$cotation_fournisseurs_id);

        if ($cotation_demande_achat!=null) {

            if (isset($cotation_demande_achat->devises_id)) {

                $devise_cotation = Devise::where('id',$cotation_demande_achat->devises_id)->first();

            }
            
            $statut_demande_achat = $this->getStatutDemandeAchatFournisseur($cotation_demande_achat->cotation_fournisseurs_id,$cotation_demande_achat->id);
            
            if ($statut_demande_achat!=null) {

                $libelle = $statut_demande_achat->libelle;
                $commentaire = $statut_demande_achat->commentaire;
                $profil_commentaire = $statut_demande_achat->name;
                $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;

            }
        }
        

        //dd($demande_achat_info);

        $commande = $this->getCommande($demandeAchat->id);

        $periodes = $this->getPeriodes();

        $statut = $this->getCategorieDemandeAchat($demandeAchat->id);

        // $devises = Devise::all();

        $devises = [];

        $devise_default = Devise::where('libelle','Franc CFA (UEMOA)')->first();

        $credit_budgetaire_structure = $this->getCreditBudgetaireById($demandeAchat->credit_budgetaires_id);

        $type_piece = "Demande d'achats";
        $piece_jointes = $this->getPieceJointes($demandeAchat->id,$type_piece);

        

        return view('cotation_fournisseurs.show',[
            'gestions'=>$gestions,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'commande'=>$commande,
            'periodes'=>$periodes,
            'cotation_demande_achat'=>$cotation_demande_achat,
            'taxes'=>$taxes,
            'libelle'=>$libelle,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'statut'=>$statut,
            'devises'=>$devises,
            'devise_default'=>$devise_default,
            'devise_cotation'=>$devise_cotation,
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'piece_jointes'=>$piece_jointes
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function edit(CotationFournisseur $cotationFournisseur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CotationFournisseur $cotationFournisseur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function destroy(CotationFournisseur $cotationFournisseur)
    {
        //
    }

    
}
