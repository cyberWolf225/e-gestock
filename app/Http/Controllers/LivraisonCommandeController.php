<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use Illuminate\Http\Request;
use App\Models\DetailCotation;
use App\Models\LivraisonCommande;
use App\Models\CotationFournisseur;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class LivraisonCommandeController extends Controller
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
    public function index($cotationFournisseur)
    {
        $controller3 = new Controller3();

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($cotationFournisseur);
        } catch (DecryptException $e) {
            //
        }

        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }


        $livraison_commandes = [];
        $demande_achat_info = null;
        $cotationFournisseur = CotationFournisseur::findOrFail($decrypted);
        if($cotationFournisseur != null){
            $livraison_commandes = $this->getLivraisonCommandes($cotationFournisseur->id);
           
            $demande_achat_info = $this->getDetailCotationFournisseur($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);
        }

        return view('livraison_commandes.index',[
            'livraison_commandes'=>$livraison_commandes,
            'type_profils_name'=>$type_profils_name,
            'demande_achat_info'=>$demande_achat_info
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($cotationFournisseur)
    {   
        $controller3 = new Controller3();

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($cotationFournisseur);
        } catch (DecryptException $e) {
            //
        }

        $cotationFournisseur = CotationFournisseur::findOrFail($decrypted);


        $type_profils_name = null;

        if (Session::has('profils_id')) {

            $demandeAchat = null;
            if ($cotationFournisseur != null) {
                $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
            }

            $etape = "livraison_commande_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $type_profils_lists = ['Fournisseur','Comite Réception'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        // dernier statut de la demande
        $libelle = '';
        $mle_signataire = null;
        $nom_prenoms_signataire = null;

        $statut_demande_achat = $this->getLastStatutDemandeAchat($cotationFournisseur->demande_achats_id);

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
        }

        //Signataire
        $type_statut_demande_achats_libelles = ['Visé (Directeur Général)'];

        $agent_signataire = $this->getStatutDemandeAchatGroupe($cotationFournisseur->demande_achats_id,$type_statut_demande_achats_libelles);
        if ($agent_signataire!=null) {
            $mle_signataire = $agent_signataire->mle;
            $nom_prenoms_signataire = $agent_signataire->nom_prenoms;
        }

        
        $profil_fournisseur = null;
        $profil_comite_reception = null;

        if ($type_profils_name === 'Comite Réception') {
            $profil_comite_reception = 'Comite Réception';
        }
        
        $type_profils_names = [$type_profils_name];

        if ($type_profils_name === "Fournisseur") {

            $profil_fournisseur = $this->getSelectionAdjudicationGroupe($cotationFournisseur->id,$type_profils_names);


            if ($profil_fournisseur != null) {
                if ($libelle!='Bon de commande retiré' and $libelle!='Livraison Partielle') {
                    redirect()->back()->with('Impossible de livrer cette demande');
                }
            }

            if($profil_fournisseur!=null){

            $demande_achats = $this->getDetailCotationFournisseursSelectionnees($profil_fournisseur->demande_achats_id,$profil_fournisseur->cotation_fournisseurs_id);           

        }else{
            return redirect()->back()->with('error','Aucune commande trouvée');
        }   

        }

        

        $credit_budgetaires_select = null;
        $griser = 1;

        
          

        if (isset($type_profils_name)) {
            if ($type_profils_name==='Comite Réception') {
                
                $demande_achats = $this->getLivraisons($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);

            }
        }


        $demande_achat_info = $this->getDetailCotationFournisseur($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);
        
        

        // dernier statut de la demande
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        if ($statut_demande_achat!=null) {
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;
        }       
        
        


        $signataires = $this->getAgentFonctionsSignataireDemandeAchat($cotationFournisseur->demande_achats_id);

        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);
        
        
        return view('livraison_commandes.create',[
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'cotation_fournisseurs_id' => $cotationFournisseur->id,
            // 'livraison_commande' => $livraison_commande,
            'profil_fournisseur' => $profil_fournisseur,
            'profil_comite_reception' => $profil_comite_reception,
            'libelle' => $libelle,
            'profils_name'=>$type_profils_name,
            'type_profils_name'=>$type_profils_name,
            'nom_prenoms_signataire'=>$nom_prenoms_signataire,
            'mle_signataire'=>$mle_signataire,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'signataires'=>$signataires,
            'statut_demande_achats'=>$statut_demande_achats
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
        $controller3 = new Controller3();
        $request->validate([
            
            'num_bc'=>['required','string'],
            'montant_acompte'=>['nullable','string'],
            'taux_acompte'=>['nullable','numeric'],
            'ref_fam'=>['required','numeric'],
            'libelle_gestion'=>['required','string'],
            'code_gestion'=>['required','string'],
            'denomination'=>['required','string'],
            'detail_cotations_id'=>['required','array'],
            'net_a_payer'=>['required','string'],
            'organisations_id'=>['required','numeric'],
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
            'qte_cotation'=>['required','array'],
            'qte'=>['required','array'],
            'design_article'=>['required','array'],
            'ref_articles'=>['required','array'],
            'cotation_fournisseurs_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'submit'=>['required','string'],
        ]);

        $montant_total_brut = filter_var($request->montant_total_brut,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total brut';
        $error_number = $this->setDecimal($montant_total_brut,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $montant_total_brut = $montant_total_brut * 1;
        }

        if(isset($request->taux_remise_generale)){
            $taux_remise_generale = $request->taux_remise_generale;
        }else{
            $taux_remise_generale = null;
        }

        $remise_generale = filter_var($request->remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Remise générale';
        $error_number = $this->setDecimal($remise_generale,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $remise_generale = $remise_generale * 1;
        }


        if(isset($request->montant_tva)){
            $montant_tva = filter_var($request->montant_tva,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

            $info = 'Montant TVA';
            $error_number = $this->setDecimal($montant_tva,$info);
            if(isset($error_number)) {
                return redirect()->back()->with('error',$error_number);
            }else{
                $montant_tva = $montant_tva * 1;
            }
        }else{
            $montant_tva = null;
        }
        

        $montant_total_net = filter_var($request->montant_total_net,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total net';
        $error_number = $this->setDecimal($montant_total_net,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $montant_total_net = $montant_total_net * 1;
        }

        if(isset($request->tva)){
            $tva = $request->tva;
        }else{
            $tva = null;
        }

        if(isset($request->assiete_bnc)){
            $assiete_bnc = $request->assiete_bnc;
        }else{
            $assiete_bnc = null;
        }
        
        if(isset($request->taux_bnc)){
            $taux_bnc = $request->taux_bnc;
        }else{
            $taux_bnc = null;
        }

        

        $montant_total_ttc = filter_var($request->montant_total_ttc,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total ttc';
        $error_number = $this->setDecimal($montant_total_ttc,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $montant_total_ttc = $montant_total_ttc * 1;
        }

        $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total ttc';
        $error_number = $this->setDecimal($net_a_payer,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $net_a_payer = $net_a_payer * 1;
        }

        $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
        ->first();

        if ($cotationFournisseur!=null) {
            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = null;

        if (Session::has('profils_id')) {

            $demandeAchat = null;
            if ($cotationFournisseur != null) {
                $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
            }

            $etape = "livraison_commande_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $type_profils_lists = ['Fournisseur','Comite Réception'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        if (isset($request->submit)) {

            if ($request->submit === 'livrer') {

                

                // controle des quantités
                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {
                        foreach ($request->detail_cotations_id as $item => $value) {

                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            
                            $detail_cotation = $controller3->getDetailCotationById($request->detail_cotations_id[$item]);
                            
                            $detail_cotation = DetailCotation::where('id',$request->detail_cotations_id[$item])->first();
                            

                            if ($detail_cotation != null) {
                                $qte_cotation[$item] = $detail_cotation->qte;

                                if ($qte_cotation[$item] < $qte[$item]) {
                                    return redirect()->back()->with('error','La quantité livrée ne peut être supérieure à la quantité commandée.');
                                }
                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }
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

                $detail_livraison = null;
                $sequence = null;
                
                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {

                        if($demandeAchat != null){

                            $num_bl = $this->getLastNumBl($demandeAchat->exercice,$demandeAchat->code_structure);

                            if(isset($num_bl)){

                                $livraison_commande = $this->storeLivraisonCommande($request->cotation_fournisseurs_id,Session::get('profils_id'),$num_bl,$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$cotationFournisseur->taux_de_change);

                                $livraison_commandes_id = $livraison_commande->id;
                            }                                

                        }

                        if(isset($livraison_commandes_id)){

                            $detail_livrai = $this->getLastDetailLivraison($livraison_commandes_id);                        

                            if ($detail_livrai != null) {
                                $sequence = $detail_livrai->sequence + 1;
                            }else{
                                $sequence = 1;
                            }
    
                            foreach ($request->detail_cotations_id as $item => $value) {
                                
    
                                $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
    
    
                                try {
                                    $qte[$item] = $qte[$item] * 1;
                                } catch (\Throwable $th) {
                                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                }
                                
                                if (gettype($qte[$item])!='integer') {
                                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                }
    
                                $detail_cotations_id = $request->detail_cotations_id[$item];
                                $remise = $request->remise[$item]; 
    
                                $detail_livraison =  $this->storeDetailLivraison($detail_cotations_id,$qte[$item],$taux_tva,$remise,$livraison_commandes_id,$detail_livraison,$request->submit,$sequence);
                                
                            }

                        }
                        
                    }else{

                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');

                    }
                }




                if ($detail_livraison!=null) {

                    $detail_cotationss = $this->getSumDetailCotationByCotationFournisseur($request->cotation_fournisseurs_id);
                    

                    if ($detail_cotationss!=null) {
                        $qte_total_commandee = $detail_cotationss->qte_total_commandee;
                    }else{
                        $qte_total_commandee = 0;
                    }

                    $detail_livraisonss = $this->getSumDetailLivraisonByCotationFournisseur($request->cotation_fournisseurs_id);                    

                    if ($detail_livraisonss!=null) {
                        $qte_total_livree = $detail_livraisonss->qte_total_livree;
                    }else{
                        $qte_total_livree = 0;
                    }

                    if ($qte_total_livree != 0) {

                        if ($qte_total_commandee > $qte_total_livree) {
                            $libelle = "Livraison partielle";
                            $libelle_livraison = "Livraison partielle";
                        }elseif ($qte_total_commandee === $qte_total_livree) {
                            $libelle = "Livraison totale";
                            $libelle_livraison = "Livraison totale";
                        }

                    }

                    
                    
                    if (isset($libelle)) {
                        // statut livraison
                
                            //determiner la demande d'achats
                                $demande_achats_id = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first()->demande_achats_id;
                            // fin determiner la demande d'achats

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

                                $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                            }

                            if(isset($libelle_livraison) && isset($livraison_commandes_id)){

                                $this->storeTypeStatutDemandeAchat($libelle_livraison);

                                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_livraison);

                                if ($type_statut_demande_achat != null) {
                                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                                    $this->setLastStatutLivraisonCommande($livraison_commandes_id);

                                    $this->storeStatutLivraisonCommande($livraison_commandes_id, Session::get('profils_id'), $type_statut_demande_achats_id,$request->commentaire);
                                }  

                            }
                            
            
                        //fin statut livraison


                    $subject = $libelle;


                    $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                    //

                    // notifier l'émetteur

                    // utilisateur connecté

                    $this->notifDemandeAchat(auth()->user()->email,$subject,$demande_achats_id);
                

                    $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);
                    

                    $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);
                        //

                    // bon de livraison
                        /*
                        if (isset($livraison_commandes_id)) {
                            
                            if (isset($request->bon_livraison)) {

                                $bon_livraison =  $request->bon_livraison->store('bon_livraison','public');

                                $name = $request->bon_livraison->getClientOriginalName();

                                $flag_actif = 1;

                                $this->storebonLivraison($livraison_commandes_id,Session::get('profils_id'),$bon_livraison,$flag_actif,$name,$sequence);
                                

                                $libelle_piece = "Demande d'achats";
                                $piece_jointes_id = null;

                                $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
                                ->first();

                                if ($cotationFournisseur!=null) {
                                    $dataPiece = [
                                        'subject_id'=>$cotationFournisseur->demande_achats_id,
                                        'profils_id'=>Session::get('profils_id'),
                                        'libelle'=>$libelle_piece,
                                        'piece'=>$bon_livraison,
                                        'flag_actif'=>$flag_actif,
                                        'name'=>$name,
                                        'piece_jointes_id'=>$piece_jointes_id,
                                    ];

                                    $controller3->procedureStorePieceJointe($dataPiece);
                                }                                
                            }
                                    
                        }
                        */


                        if (isset($livraison_commandes_id)) {
                            
                            if (isset($request->bon_livraison)) {

                                $bon_livraison =  $request->bon_livraison->store('bon_livraison','public');

                                $name = $request->bon_livraison->getClientOriginalName();

                                $flag_actif = 1;

                                $this->storebonLivraison($livraison_commandes_id,Session::get('profils_id'),$bon_livraison,$flag_actif,$name,$sequence);
                                

                                $libelle_piece = "Demande d'achats";
                                $piece_jointes_id = null;

                                $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
                                ->first();

                                if ($cotationFournisseur!=null) {
                                    $dataPiece = [
                                        'subject_id'=>$cotationFournisseur->demande_achats_id,
                                        'profils_id'=>Session::get('profils_id'),
                                        'libelle'=>$libelle_piece,
                                        'piece'=>$bon_livraison,
                                        'flag_actif'=>$flag_actif,
                                        'name'=>$name,
                                        'piece_jointes_id'=>$piece_jointes_id,
                                    ];

                                    $controller3->procedureStorePieceJointe($dataPiece);
                                }
                            }      
                        }

                    //


                        return redirect('demande_achats/index')->with('success',$libelle.' effectuée');
                    }else {
                        return redirect()->back()->with('error','Aucune livraison effectuée');
                    }                   
                }else{
                   return redirect()->back()->with('error','Livraison echouée');
                }




            }elseif ($request->submit === 'valider') {

               $request->validate([
            
                    'detail_livraisons_id'=>['required','array'],
                    'pv_reception'=>['required'],

                ]);



                // controle des quantités
                if (isset($request->detail_livraisons_id)) {
                    if (count($request->detail_livraisons_id)>0) {
                        foreach ($request->detail_livraisons_id as $item => $value) {
                            
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            if ($qte[$item] < 0) {
                                return redirect()->back()->with('error','La quantité validée ne peut être négative.');
                            }

                            $detail_livraison = $this->getDetailLivraisonById($request->detail_livraisons_id[$item]);                            

                            if ($detail_livraison!=null) {
                                $qte_livree[$item] = $detail_livraison->qte;

                                if ($qte_livree[$item] < $qte[$item]) {
                                    return redirect()->back()->with('error','La quantité validée ne peut être supérieure à la quantité livrée.');
                                }
                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }

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

                $livraison_valider = null;

                if (isset($request->detail_livraisons_id)) {
                    if (count($request->detail_livraisons_id)>0) {
                        
                        foreach ($request->detail_livraisons_id as $item => $value) {
                            if (isset($request->ref_articles[$item])) {
                                if (!empty($request->ref_articles[$item])) {
                                        $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                                        try {
                                            $qte[$item] = $qte[$item] * 1;
                                        } catch (\Throwable $th) {
                                            return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                        }
                                
                                        if (gettype($qte[$item])!='integer') {
                                            return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                        }

                                        if ($qte[$item] < 0) {
                                            return redirect()->back()->with('error', 'La quantité validée ne peut être négative.');
                                        }

                                        if (isset($detail_cotations_id)) { unset($detail_cotations_id); }

                                        if (isset($detail_livraisons_id)) { unset($detail_livraisons_id); }

                                        if (isset($remise)) { unset($remise); }

                                        if (isset($prix_unit)) { unset($prix_unit); }

                                        if (isset($montant_ht)) { unset($montant_ht); }

                                        if (isset($montant_ttc)) { unset($montant_ttc); }


                                        $detail_cotations_id = $request->detail_cotations_id[$item];
                                        $detail_livraisons_id = $request->detail_livraisons_id[$item];
                                        $remise = $request->remise[$item];

                                        $livraison_commandes = $this->getLivraisonCommandeByCotationFournisseur($request->cotation_fournisseurs_id);


                                        if ($livraison_commandes!=null) {
                                            
                                            $livraison_commandes_id = $livraison_commandes->id;
                                            $sequence = null;

                                            $this->storeDetailLivraison($detail_cotations_id,$qte[$item],$taux_tva,$remise,$livraison_commandes_id,null,$request->submit,$sequence);

                                        }


                                        

                                        
                                        $livraison_valider = $this->storeLivraisionValider($detail_cotations_id,$taux_tva,$qte[$item],$detail_livraisons_id,$remise,Session::get('profils_id'));

                                        if ($livraison_valider!=null) {
                                            
                                            $prix_unit = $livraison_valider->prix_unit;
                                            $montant_ht = $livraison_valider->montant_ht;
                                            $montant_ttc = $livraison_valider->montant_ttc;

                                            //recupération de la quantité coté

                                            //Entrée en stock

                                                //Type de movement
                                                $libelle_mvt = 'Entrée en stock';

                                                $type_mouvements_id = $this->getTypeMouvement($libelle_mvt);


                                                //determiner la demande d'achats
                                                try {

                                                    $demande_achats_id = CotationFournisseur::where('id', $request->cotation_fournisseurs_id)
                                                    ->first()
                                                    ->demande_achats_id;

                                                } catch (\Throwable $th) {
                                                    //throw $th;
                                                }
                                                    
                                                // fin determiner la demande d'achats

                                                //determiner le magasin
                                                    $ref_magasin = $this->getMagasin($demande_achats_id);
                                                //fin magasin


                                                // determiner l'articles
                                                    $ref_articles = null;
                                                    $date2 = null;

                                                    $livraison_valid = $this->getLivraisonValider($detail_livraisons_id);
                                                    

                                                    if ($livraison_valid!=null) {

                                                        $ref_articles = $livraison_valid->ref_articles;
                                                        $livraison_commandes_id = $livraison_valid->livraison_commandes_id;
                                                        $date2 = date("Y-m-d", strtotime($livraison_valid->created_at)) ;
                                                        $date2 = new DateTime($date2);
                                                        $date_clone2 = $date2;
                                                        
                                                    }        

                                                // fin determiner l'article

                                                // determiner le magasin_stocks
                                                    $magasin_stocks_id = null;

                                                    if ($ref_magasin!=null && $ref_articles) {

                                                        $magasin_stocks_id = $this->getMagasinStock($ref_magasin,$ref_articles);

                                                    }
                                                //fin determiner le magasin_stocks

                                                // enregistrer le mouvement d'article

                                                    if (isset($mouvements_id)) {

                                                        unset($mouvements_id);

                                                    }

                                                    try {
                                                        $taux_de_change = CotationFournisseur::where('id', $request->cotation_fournisseurs_id)
                                                        ->first()
                                                        ->taux_de_change;
                                                    } catch (\Throwable $th) {
                                                        //throw $th;
                                                    }

                                                    $mouvements_id = $this->storeMouvement($type_mouvements_id,$magasin_stocks_id,Session::get('profils_id'),$qte[$item],$prix_unit,$montant_ht,$request->tva,$montant_ttc,$taux_de_change);

                                                    if (isset($mouvements_id)) {
                                                        
                                                        if ($livraison_valider!=null) {
                                                            
                                                            $this->setLivraisonValider($livraison_valider->id,$mouvements_id);
                                                            
                                                        }
    
    
                                                        // récupération du montant du stock actuel
                                                            $montant_apres_stock = $this->getMontantMagasinStock($magasin_stocks_id);
                                                        //
    
                                                        // récupération de la quantité du stock actuel
                                                            $mouvements_qte = $this->getQteMagasinStock($magasin_stocks_id);
                                                            
    
                                                            // Quantité après eventuel stockage
                                                            if ($mouvements_qte!=null) {
                                                                $qte_apres_stockage = $mouvements_qte->qte_stock;
                                                                $qte_stock = $qte_apres_stockage;
                                                            } else {
                                                                $qte_apres_stockage = 1;
                                                                $qte_stock = 0;
                                                            }
                                                        //
    
                                                        //
                                                            if (isset($qte_apres_stockage) && isset($montant_apres_stock)) {
                                                                $cmup = $montant_apres_stock / $qte_apres_stockage;
                                                                $montant_stock = $cmup * $qte_stock;
                                                            }

                                                            $this->setMagasinStock($magasin_stocks_id,$cmup,$qte_stock,$montant_stock);
                                                            
                                                        //
                                                        
    
                                                        // stock_alert, stock_securite, stock_mini
    
                                                            //delai de livraison de cet article
                                                            //determiner la derniere livraison de cet article
                                                            $date1 = null;

                                                            $livraison_valid = $this->getLivraisonValider2($detail_livraisons_id,$ref_articles,$livraison_commandes_id);
                                                            
                                                        
                                                            if ($livraison_valid!=null) {
                                                                $date1 = date("Y-m-d", strtotime($livraison_valid->created_at)) ;
    
                                                                $date1 = new DateTime($date1);
                                                            }
    
                                                            if ($date2!=null && $date1!=null) {

                                                                // $date1 = new DateTime($date1);
                                                                // $date2 = new DateTime($date2);
                                                                $diff = $date2->diff($date1)->format("%a");
    
    
                                                                $delai_livraison = number_format((($diff/30)/3), 2) ;
                                                            } else {
                                                                $delai_livraison = 1;
                                                            }
    
                                                            //
    
    
                                                            // determiner la consommation moyenne du trimestre ecoulé
    
                                                            $date0 = $date_clone2->sub(new DateInterval('P3M'))->format('Y-m-d'); // P1D means a period of 1 day
    
                                                            $date3 = $date_clone2->add(new DateInterval('P3M'))->format('Y-m-d');
                                                            
                                                            $mouvement = $this->getMouvement($magasin_stocks_id,$date3);                  
    
                                                            if ($mouvement!=null) {
                                                                $moy_requisition = $mouvement->qte_sortie;
                                                            } else {
                                                                $moy_requisition = 0;
                                                            }
                                                    
    
                                                            //
    
                                                            // determiner le retard de livraison
    
                                                            //date de livraison prévue

                                                            $date_livraison_prevue = null;

                                                            $commande = $this->getCommande($demande_achats_id);

                                                            if ($commande != null) {
                                                                $date_livraison_prevue = $commande->date_livraison_prevue;
                                                            }
    
                                                            $date_livraison_prevue = new DateTime($date_livraison_prevue);
    
                                                            $diff_retard = $date2->diff($date_livraison_prevue)->format("%a");
    
                                                            $retard_livraison = number_format((($diff_retard/30)/3), 2);
    
    
                                                            //
    
                                                            //
    
                                                            $stock_mini = $delai_livraison * $moy_requisition;
                                                            $stock_securite = $retard_livraison * $moy_requisition;
                                                            $stock_alert = $stock_securite + $stock_mini;
    
                                                            $this->setMagasinStock2($magasin_stocks_id,$stock_mini,$stock_securite,$stock_alert);

                                                           
                                                        //
                                                    }
                                                    

                                                //

                                                
                                            //fin entrée en stock

                                        }

                                    //Fin entrée en stock
                                }
                            }
                        }


                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }

                }


                if ($livraison_valider!=null) {

                    $detail_cotationss = $this->getSumDetailCotationByCotationFournisseur($request->cotation_fournisseurs_id);

                    if ($detail_cotationss!=null) {
                        $qte_total_commandee = $detail_cotationss->qte_total_commandee;
                    }else{
                        $qte_total_commandee = 0;
                    }

                    $detail_livraisonss = $this->getSumDetailLivraisonByCotationFournisseur($request->cotation_fournisseurs_id);

                    if ($detail_livraisonss!=null) {
                        $qte_total_livree = $detail_livraisonss->qte_total_livree;
                    }else{
                        $qte_total_livree = 0;
                    }

                    $livraison_validerss = $this->getLivraisonValiderByCotationFournisseur($request->cotation_fournisseurs_id);
                    

                    if ($livraison_validerss!=null) {
                        $qte_total_livree_confirme = $livraison_validerss->qte_total_livree_confirme;
                    }else{
                        $qte_total_livree_confirme = 0;
                    }



                    if ($qte_total_livree_confirme != 0) {

                        if ($qte_total_commandee > $qte_total_livree_confirme) {
                            $libelle = "Livraison partielle confirmée";
                            $libelle_livraison = "Livraison partielle confirmée";
                        }elseif ($qte_total_commandee === $qte_total_livree_confirme) {
                            $libelle = "Livré";
                            $libelle_livraison = "Livré";
                        }

                    }else{
                        $libelle = "Livraison annulée (Comite Réception)";
                        $libelle_livraison = "Livraison annulée (Comite Réception)";
                    }        
        
                    if (isset($libelle)) {
                        // statut livraison
                
                            //determiner la demande d'achats
                                $demande_achats_id = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first()->demande_achats_id;
                            // fin determiner la demande d'achats

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

                                $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                            }

                            if(isset($libelle_livraison) && isset($livraison_commandes_id)){

                                $this->storeTypeStatutDemandeAchat($libelle_livraison);

                                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_livraison);

                                if ($type_statut_demande_achat != null) {
                                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                                    $this->setLastStatutLivraisonCommande($livraison_commandes_id);

                                    $this->storeStatutLivraisonCommande($livraison_commandes_id, Session::get('profils_id'), $type_statut_demande_achats_id,$request->commentaire);
                                }  

                            }
            
                        //fin statut livraison


                        $subject = $libelle;

                        //

                        $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                        //

                        // notifier l'émetteur

                        // utilisateur connecté

                        $this->notifDemandeAchat(auth()->user()->email,$subject,$demande_achats_id);
                

                        $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);
                        

                        $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);
                        //

                        //PV de réception

                        if (isset($request->pv_reception)) {
            
                            $pv_reception =  $request->pv_reception->store('pv_reception','public');

                            $name = $request->pv_reception->getClientOriginalName(); 

                            $libelle_piece = "Demande d'achats";
                            $flag_actif = 1;
                            $piece_jointes_id = null;

                            if(isset($livraison_commandes_id)){

                                $libelle_piece_livraison = "PV de réception";

                                $this->storePieceJointeLivraison($livraison_commandes_id,Session::get('profils_id'),$libelle_piece_livraison,$pv_reception,$flag_actif,$name,null);

                            }
                            

                            $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
                            ->first();

                            if ($cotationFournisseur!=null) {
                                $dataPiece = [
                                    'subject_id'=>$cotationFournisseur->demande_achats_id,
                                    'profils_id'=>Session::get('profils_id'),
                                    'libelle'=>$libelle_piece,
                                    'piece'=>$pv_reception,
                                    'flag_actif'=>$flag_actif,
                                    'name'=>$name,
                                    'piece_jointes_id'=>$piece_jointes_id,
                                ];

                                $controller3->procedureStorePieceJointe($dataPiece);
                            }

                            
                        }


                        return redirect('demande_achats/index')->with('success',$libelle);

                    }else {
                        return redirect()->back()->with('error','Aucune livraison effectuée');
                    }

                }else{
                    return redirect()->back()->with('error','Validation de la livraison echouée');
                }







            }elseif ($request->submit === 'annuler_livraison_achat') {

                $request->validate([
            
                    'detail_livraisons_id'=>['required','array'],
                    'commentaire'=>['required','string'],

                ]);

                

                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {
                        foreach ($request->detail_cotations_id as $item => $value) {

                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            $detail_cotation = $controller3->getDetailCotationById($request->detail_cotations_id[$item]);

                            if ($detail_cotation!=null) {

                                $qte_cotation[$item] = $detail_cotation->qte;

                                if ($qte_cotation[$item] < $qte[$item]) {
                                    return redirect()->back()->with('error','La quantité livrée ne peut être supérieure à la quantité commandée.');
                                }

                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }
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




                $detail_livraison = null;

                
                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {

                        $livraison_commandes = $this->getLivraisonCommandeByCotationFournisseur($request->cotation_fournisseurs_id);

                        if ($livraison_commandes!=null) {
                            
                            $livraison_commandes_id = $livraison_commandes->id;

                            $this->setLivraisonCommande($livraison_commandes_id,$request->cotation_fournisseurs_id,Session::get('profils_id'),$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$cotationFournisseur->taux_de_change);      

                        }else {

                            if($demandeAchat != null) {

                                $num_bl = $this->getLastNumBl($demandeAchat->exercice, $demandeAchat->code_structure);

                                if(isset($num_bl)) {

                                    $livraison_commande = $this->storeLivraisonCommande($request->cotation_fournisseurs_id,Session::get('profils_id'),$num_bl,$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$cotationFournisseur->taux_de_change);  

                                    $livraison_commandes_id = $livraison_commande->id;

                                }
                            }

                                

                            

                        }

                        foreach ($request->detail_cotations_id as $item => $value) {
                            

                            $qte[$item] = 0;
                            // $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            $detail_cotations_id = $request->detail_cotations_id[$item];
                            $remise = $request->remise[$item];

                            $sequence = null;
                            $detail_livraison =  $this->storeDetailLivraison($detail_cotations_id,$qte[$item],$taux_tva,$remise,$livraison_commandes_id,$detail_livraison,$request->submit,$sequence);
                            
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }
                }


                if ($detail_livraison!=null) {
                    
                    $libelle = "Livraison annulée (Comite Réception)";
                    $libelle_livraison = "Livraison annulée (Comite Réception)";
                    if (isset($libelle)) {
                        // statut livraison
                
                            //determiner la demande d'achats
                                $demande_achats_id = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first()->demande_achats_id;
                            // fin determiner la demande d'achats
                
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

                                $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                            }

                            if(isset($libelle_livraison) && isset($livraison_commandes_id)){

                                $this->storeTypeStatutDemandeAchat($libelle_livraison);

                                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_livraison);

                                if ($type_statut_demande_achat != null) {
                                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                                    $this->setLastStatutLivraisonCommande($livraison_commandes_id);

                                    $this->storeStatutLivraisonCommande($livraison_commandes_id, Session::get('profils_id'), $type_statut_demande_achats_id,$request->commentaire);
                                }  

                            }

                            // if(isset($livraison_commandes_id)){
                            //     try {
                            //         BonLivraison::where('livraison_commandes_id',$livraison_commandes_id)->delete();
                            //     } catch (\Throwable $th) {
                            //         //throw $th;
                            //     }
                                
                            // }
            
                        //fin statut livraison


                            $subject = $libelle;

                            $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                                //

                                // notifier l'émetteur

                                // utilisateur connecté
                                $this->notifDemandeAchat(auth()->user()->email,$subject,$demande_achats_id);
                

                                $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);
                                

                                $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);

                            

                        //

                        return redirect('demande_achats/index')->with('success','Annulation enregistrée');
                    }else {
                        return redirect()->back()->with('error','Aucune annulation effectuée');
                    }                   
                }else{
                   return redirect()->back()->with('error','Annulation echouée');
                }




            }

        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function show($livraisonCommande)
    {      
        $controller3 = new Controller3();  
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($livraisonCommande);
        } catch (DecryptException $e) {
            //
        }

        $livraisonCommande = LivraisonCommande::findOrFail($decrypted);
        $cotationFournisseur = CotationFournisseur::where('id',$livraisonCommande->cotation_fournisseurs_id)->first();

        $type_profils_name = null;

        if (Session::has('profils_id')) {

            $demandeAchat = null;
            if ($cotationFournisseur != null) {
                $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
            }

            $etape = "livraison_commande_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $type_profils_lists = ['Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Fournisseur','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception','Pilote AEE','Agent Cnps'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        // dernier statut de la demande
        $libelle = '';
        $libelle_livraison = '';
        $mle_signataire = null;
        $nom_prenoms_signataire = null;

        $statut_livraison_commande = $this->getLastStatutLivraisonCommande($livraisonCommande->id);
        
        if ($statut_livraison_commande!=null) {
            $libelle_livraison = $statut_livraison_commande->libelle;
        }

        $statut_demande_achat = $this->getLastStatutDemandeAchat($cotationFournisseur->demande_achats_id);

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
        }

        //Signataire
        $type_statut_demande_achats_libelles = ['Visé (Directeur Général)'];

        $agent_signataire = $this->getStatutDemandeAchatGroupe($cotationFournisseur->demande_achats_id,$type_statut_demande_achats_libelles);
        if ($agent_signataire!=null) {
            $mle_signataire = $agent_signataire->mle;
            $nom_prenoms_signataire = $agent_signataire->nom_prenoms;
        }

        
        $profil_fournisseur = null;
        $profil_comite_reception = null;

        $credit_budgetaires_select = null;
        $griser = 1;
        $griser_qte = 1;
        $detail_livraisons = $this->getDetailLivraisonCommandesById($livraisonCommande->id);
        $livraison_commandes_info = $this->getLivraisonCommandeById($livraisonCommande->id);

        // dernier statut de la demande
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        $commentaire_livraison = null;
        $profil_commentaire_livraison = null;
        $nom_prenoms_commentaire_livraison = null;

        if ($statut_demande_achat!=null) {
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;
        } 
        
        if ($statut_livraison_commande!=null) {
            $commentaire_livraison = $statut_livraison_commande->commentaire;
            $profil_commentaire_livraison = $statut_livraison_commande->name;
            $nom_prenoms_commentaire_livraison = $statut_livraison_commande->nom_prenoms;
        }

        $signataires = $this->getAgentFonctionsSignataireDemandeAchat($cotationFournisseur->demande_achats_id);

        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);
        
        return view('livraison_commandes.show',[
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'griser_qte'=>$griser_qte,
            'detail_livraisons'=>$detail_livraisons,
            'livraison_commandes_info'=>$livraison_commandes_info,
            'cotation_fournisseurs_id' => $cotationFournisseur->id,
            // 'livraison_commande' => $livraison_commande,
            'profil_fournisseur' => $profil_fournisseur,
            'profil_comite_reception' => $profil_comite_reception,
            'libelle' => $libelle,
            'libelle_livraison' => $libelle_livraison,
            'profils_name'=>$type_profils_name,
            'type_profils_name'=>$type_profils_name,
            'nom_prenoms_signataire'=>$nom_prenoms_signataire,
            'mle_signataire'=>$mle_signataire,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'commentaire_livraison'=>$commentaire_livraison,
            'profil_commentaire_livraison'=>$profil_commentaire_livraison,
            'nom_prenoms_commentaire_livraison'=>$nom_prenoms_commentaire_livraison,
            'signataires'=>$signataires,
            'statut_demande_achats'=>$statut_demande_achats
        ]);
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function edit($livraisonCommande)
    {  
        $controller3 = new Controller3();      
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($livraisonCommande);
        } catch (DecryptException $e) {
            //
        }

        $livraisonCommande = LivraisonCommande::findOrFail($decrypted);

        $cotationFournisseur = CotationFournisseur::where('id',$livraisonCommande->cotation_fournisseurs_id)->first();

        $type_profils_name = null;

        if (Session::has('profils_id')) {

            $demandeAchat = null;
            if ($cotationFournisseur != null) {
                $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
            }

            $etape = "livraison_commande_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $type_profils_lists = ['Fournisseur','Comite Réception'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        // dernier statut de la demande
        $libelle = '';
        $libelle_livraison = '';
        $mle_signataire = null;
        $nom_prenoms_signataire = null;

        $statut_livraison_commande = $this->getLastStatutLivraisonCommande($livraisonCommande->id);
        
        if ($statut_livraison_commande!=null) {
            $libelle_livraison = $statut_livraison_commande->libelle;
        }

        $statut_demande_achat = $this->getLastStatutDemandeAchat($cotationFournisseur->demande_achats_id);

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
        }

        //Signataire
        $type_statut_demande_achats_libelles = ['Visé (Directeur Général)'];

        $agent_signataire = $this->getStatutDemandeAchatGroupe($cotationFournisseur->demande_achats_id,$type_statut_demande_achats_libelles);
        if ($agent_signataire!=null) {
            $mle_signataire = $agent_signataire->mle;
            $nom_prenoms_signataire = $agent_signataire->nom_prenoms;
        }

        
        $profil_fournisseur = null;
        $profil_comite_reception = null;

        $credit_budgetaires_select = null;
        $griser = null;
        $griser_qte = null;

        if ($type_profils_name === 'Comite Réception') {
            $profil_comite_reception = 'Comite Réception';
            $griser_qte = 1;

        }elseif ($type_profils_name === 'Fournisseur') {
            $profil_fournisseur = 1;
        }         

        


        if($livraisonCommande->validation === 1){
            $griser = 1;
            $griser_qte = 1;
        }
        

        $detail_livraisons = $this->getDetailLivraisonCommandesById($livraisonCommande->id);


        $livraison_commandes_info = $this->getLivraisonCommandeById($livraisonCommande->id);
        
        

        // dernier statut de la demande
        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        $commentaire_livraison = null;
        $profil_commentaire_livraison = null;
        $nom_prenoms_commentaire_livraison = null;

        if ($statut_demande_achat!=null) {
            $commentaire = $statut_demande_achat->commentaire;
            $profil_commentaire = $statut_demande_achat->name;
            $nom_prenoms_commentaire = $statut_demande_achat->nom_prenoms;
        } 
        
        if ($statut_livraison_commande!=null) {
            $commentaire_livraison = $statut_livraison_commande->commentaire;
            $profil_commentaire_livraison = $statut_livraison_commande->name;
            $nom_prenoms_commentaire_livraison = $statut_livraison_commande->nom_prenoms;
        }


        $signataires = $this->getAgentFonctionsSignataireDemandeAchat($cotationFournisseur->demande_achats_id);

        $statut_demande_achats = $this->getStatutDemandeAchats($demandeAchat->id);
        
        return view('livraison_commandes.edit',[
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'griser_qte'=>$griser_qte,
            'detail_livraisons'=>$detail_livraisons,
            'livraison_commandes_info'=>$livraison_commandes_info,
            'cotation_fournisseurs_id' => $cotationFournisseur->id,
            // 'livraison_commande' => $livraison_commande,
            'profil_fournisseur' => $profil_fournisseur,
            'profil_comite_reception' => $profil_comite_reception,
            'libelle' => $libelle,
            'libelle_livraison' => $libelle_livraison,
            'profils_name'=>$type_profils_name,
            'type_profils_name'=>$type_profils_name,
            'nom_prenoms_signataire'=>$nom_prenoms_signataire,
            'mle_signataire'=>$mle_signataire,
            'commentaire'=>$commentaire,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'commentaire_livraison'=>$commentaire_livraison,
            'profil_commentaire_livraison'=>$profil_commentaire_livraison,
            'nom_prenoms_commentaire_livraison'=>$nom_prenoms_commentaire_livraison,
            'signataires'=>$signataires,
            'statut_demande_achats'=>$statut_demande_achats
        ]);
       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CotationFournisseur  $cotationFournisseur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $controller3 = new Controller3();
       
        $request->validate([
            
            'num_bc'=>['required','string'],
            'num_bl'=>['required','string'],
            'livraison_commandes_id'=>['required','numeric'],
            'detail_livraisons_id'=>['required','array'],
            'montant_acompte'=>['nullable','string'],
            'taux_acompte'=>['nullable','numeric'],
            'ref_fam'=>['required','numeric'],
            'libelle_gestion'=>['required','string'],
            'code_gestion'=>['required','string'],
            'denomination'=>['required','string'],
            'detail_cotations_id'=>['required','array'],
            
            'net_a_payer'=>['required','string'],
            'organisations_id'=>['required','numeric'],
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
            'qte_cotation'=>['required','array'],
            'qte'=>['required','array'],
            'design_article'=>['required','array'],
            'ref_articles'=>['required','array'],
            'cotation_fournisseurs_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'submit'=>['required','string'],

            
        ]);
        
        $montant_total_brut = filter_var($request->montant_total_brut,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total brut';
        $error_number = $this->setDecimal($montant_total_brut,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $montant_total_brut = $montant_total_brut * 1;
        }

        if(isset($request->taux_remise_generale)){
            $taux_remise_generale = $request->taux_remise_generale;
        }else{
            $taux_remise_generale = null;
        }

        $remise_generale = filter_var($request->remise_generale,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Remise générale';
        $error_number = $this->setDecimal($remise_generale,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $remise_generale = $remise_generale * 1;
        }


        if(isset($request->montant_tva)){
            $montant_tva = filter_var($request->montant_tva,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

            $info = 'Montant TVA';
            $error_number = $this->setDecimal($montant_tva,$info);
            if(isset($error_number)) {
                return redirect()->back()->with('error',$error_number);
            }else{
                $montant_tva = $montant_tva * 1;
            }
        }else{
            $montant_tva = null;
        }
        

        $montant_total_net = filter_var($request->montant_total_net,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total net';
        $error_number = $this->setDecimal($montant_total_net,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $montant_total_net = $montant_total_net * 1;
        }

        if(isset($request->tva)){
            $tva = $request->tva;
        }else{
            $tva = null;
        }

        if(isset($request->assiete_bnc)){
            $assiete_bnc = $request->assiete_bnc;
        }else{
            $assiete_bnc = null;
        }
        
        if(isset($request->taux_bnc)){
            $taux_bnc = $request->taux_bnc;
        }else{
            $taux_bnc = null;
        }

        

        $montant_total_ttc = filter_var($request->montant_total_ttc,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total ttc';
        $error_number = $this->setDecimal($montant_total_ttc,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $montant_total_ttc = $montant_total_ttc * 1;
        }

        $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);

        $info = 'Montant total ttc';
        $error_number = $this->setDecimal($net_a_payer,$info);
        if(isset($error_number)) {
            return redirect()->back()->with('error',$error_number);
        }else{
            $net_a_payer = $net_a_payer * 1;
        }

        $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
        ->first();

        if ($cotationFournisseur!=null) {
            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = null;

        if (Session::has('profils_id')) {

            $demandeAchat = null;
            if ($cotationFournisseur != null) {
                $demandeAchat = $this->getDemandeAchat($cotationFournisseur->demande_achats_id);
            }

            $etape = "livraison_commande_create";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }

            $type_profils_lists = ['Fournisseur','Comite Réception'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        if (isset($request->submit)) {

            if ($request->submit === 'livrer') {

                

                // controle des quantités
                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {
                        foreach ($request->detail_cotations_id as $item => $value) {

                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            $detail_cotation = $controller3->getDetailCotationById($request->detail_cotations_id[$item]);

                            if ($detail_cotation!=null) {
                                $qte_cotation[$item] = $detail_cotation->qte;

                                if ($qte_cotation[$item] < $qte[$item]) {
                                    return redirect()->back()->with('error','La quantité livrée ne peut être supérieure à la quantité commandée.');
                                }
                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }
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




                $detail_livraison = null;
                $sequence = null;

                
                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {

                        
                        $livraison_commandes = $this->getLivraisonCommandeById($request->livraison_commandes_id);

                        if ($livraison_commandes!=null) {
                            
                            $livraison_commandes_id = $livraison_commandes->livraison_commandes_id;

                            if($demandeAchat != null){

                                if($livraison_commandes->validation != 1){

                                    $num_bl = $this->getLastNumBl($demandeAchat->exercice,$demandeAchat->code_structure,$livraison_commandes_id);

                                    $this->setLivraisonCommande($livraison_commandes_id,$request->cotation_fournisseurs_id,Session::get('profils_id'),$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$cotationFournisseur->taux_de_change); 

                                }

                            }                                                       
                            
                        }

                        if(isset($livraison_commandes_id)){

                            $detail_livrai = $this->getLastDetailLivraison($livraison_commandes_id);                        

                            if ($detail_livrai != null) {
                                $sequence = $detail_livrai->sequence + 1;
                            }else{
                                $sequence = 1;
                            }
    
                            foreach ($request->detail_cotations_id as $item => $value) {
                                
    
                                $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
    
    
                                try {
                                    $qte[$item] = $qte[$item] * 1;
                                } catch (\Throwable $th) {
                                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                }
                                
                                if (gettype($qte[$item])!='integer') {
                                    return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                }
    
                                $detail_cotations_id = $request->detail_cotations_id[$item];
                                $remise = $request->remise[$item]; 
    
                                $detail_livraison = $this->storeDetailLivraison($detail_cotations_id,$qte[$item],$taux_tva,$remise,$livraison_commandes_id,$detail_livraison,$request->submit,$sequence,$request->detail_livraisons_id[$item]);
                                
                            }

                        }
                        
                    }else{

                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');

                    }
                }




                if ($detail_livraison!=null) {

                    $detail_cotationss = $this->getSumDetailCotationByCotationFournisseur($request->cotation_fournisseurs_id);
                    

                    if ($detail_cotationss!=null) {
                        $qte_total_commandee = $detail_cotationss->qte_total_commandee;
                    }else{
                        $qte_total_commandee = 0;
                    }

                    $detail_livraisonss = $this->getSumDetailLivraisonByCotationFournisseur($request->cotation_fournisseurs_id);                    

                    if ($detail_livraisonss!=null) {
                        $qte_total_livree = $detail_livraisonss->qte_total_livree;
                    }else{
                        $qte_total_livree = 0;
                    }

                    if ($qte_total_livree != 0) {

                        if ($qte_total_commandee > $qte_total_livree) {
                            $libelle = "Livraison partielle";
                            $libelle_livraison = "Livraison partielle";
                        }elseif ($qte_total_commandee === $qte_total_livree) {
                            $libelle = "Livraison totale";
                            $libelle_livraison = "Livraison totale";
                        }

                    }

                    
                    
                    if (isset($libelle)) {
                        // statut livraison
                
                            //determiner la demande d'achats
                                $demande_achats_id = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first()->demande_achats_id;
                            // fin determiner la demande d'achats

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

                                $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                            }

                            if(isset($libelle_livraison) && isset($livraison_commandes_id)){

                                $this->storeTypeStatutDemandeAchat($libelle_livraison);

                                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_livraison);

                                if ($type_statut_demande_achat != null) {
                                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                                    $this->setLastStatutLivraisonCommande($livraison_commandes_id);

                                    $this->storeStatutLivraisonCommande($livraison_commandes_id, Session::get('profils_id'), $type_statut_demande_achats_id,$request->commentaire);
                                }  

                            }
            
                        //fin statut livraison


                        $subject = $libelle;


                        $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                        //

                        // notifier l'émetteur

                        // utilisateur connecté

                        $this->notifDemandeAchat(auth()->user()->email,$subject,$demande_achats_id);
                    

                        $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);
                        

                        $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);
                            //

                        // bon de livraison
                    
                        if (isset($livraison_commandes_id)) {
                            
                            if (isset($request->bon_livraison)) {

                                $bon_livraison =  $request->bon_livraison->store('bon_livraison','public');

                                $name = $request->bon_livraison->getClientOriginalName();

                                $flag_actif = 1;

                                $this->storebonLivraison($livraison_commandes_id,Session::get('profils_id'),$bon_livraison,$flag_actif,$name,$sequence);
                                

                                $libelle_piece = "Demande d'achats";
                                $piece_jointes_id = null;

                                $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
                                ->first();

                                if ($cotationFournisseur!=null) {
                                    $dataPiece = [
                                        'subject_id'=>$cotationFournisseur->demande_achats_id,
                                        'profils_id'=>Session::get('profils_id'),
                                        'libelle'=>$libelle_piece,
                                        'piece'=>$bon_livraison,
                                        'flag_actif'=>$flag_actif,
                                        'name'=>$name,
                                        'piece_jointes_id'=>$piece_jointes_id,
                                    ];

                                    $controller3->procedureStorePieceJointe($dataPiece);
                                }
                            }      
                        }

                    


                        return redirect('demande_achats/index')->with('success',$libelle.' effectuée');
                    }else {
                        return redirect()->back()->with('error','Aucune livraison effectuée');
                    }                   
                }else{
                   return redirect()->back()->with('error','Livraison echouée');
                }




            }elseif ($request->submit === 'valider') {

               $request->validate([
            
                    'detail_livraisons_id'=>['required','array'],
                    'pv_reception'=>['required'],

                ]);



                // controle des quantités
                if (isset($request->detail_livraisons_id)) {
                    if (count($request->detail_livraisons_id)>0) {
                        foreach ($request->detail_livraisons_id as $item => $value) {
                            
                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            if ($qte[$item] < 0) {
                                return redirect()->back()->with('error','La quantité validée ne peut être négative.');
                            }

                            $detail_livraison = $this->getDetailLivraisonById($request->detail_livraisons_id[$item]);                            

                            if ($detail_livraison!=null) {
                                $qte_livree[$item] = $detail_livraison->qte;

                                if ($qte_livree[$item] < $qte[$item]) {
                                    return redirect()->back()->with('error','La quantité validée ne peut être supérieure à la quantité livrée.');
                                }
                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }

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

                $livraison_valider = null;

                if (isset($request->detail_livraisons_id)) {
                    if (count($request->detail_livraisons_id)>0) {
                        
                        foreach ($request->detail_livraisons_id as $item => $value) {
                            if (isset($request->ref_articles[$item])) {
                                if (!empty($request->ref_articles[$item])) {
                                        $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                                        try {
                                            $qte[$item] = $qte[$item] * 1;
                                        } catch (\Throwable $th) {
                                            return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                        }
                                
                                        if (gettype($qte[$item])!='integer') {
                                            return redirect()->back()->with('error', 'Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                                        }

                                        if ($qte[$item] < 0) {
                                            return redirect()->back()->with('error', 'La quantité validée ne peut être négative.');
                                        }

                                        if (isset($detail_cotations_id)) { unset($detail_cotations_id); }

                                        if (isset($detail_livraisons_id)) { unset($detail_livraisons_id); }

                                        if (isset($remise)) { unset($remise); }

                                        if (isset($prix_unit)) { unset($prix_unit); }

                                        if (isset($montant_ht)) { unset($montant_ht); }

                                        if (isset($montant_ttc)) { unset($montant_ttc); }


                                        $detail_cotations_id = $request->detail_cotations_id[$item];
                                        $detail_livraisons_id = $request->detail_livraisons_id[$item];
                                        $remise = $request->remise[$item];

                                        $livraison_commandes = $this->getLivraisonCommandeById($request->livraison_commandes_id);


                                        if ($livraison_commandes!=null) {
                                            
                                            $livraison_commandes_id = $livraison_commandes->livraison_commandes_id;
                                            $sequence = null;

                                            $this->storeDetailLivraison($detail_cotations_id,$qte[$item],$taux_tva,$remise,$livraison_commandes_id,null,$request->submit,$sequence,$detail_livraisons_id);

                                        }


                                        

                                        
                                        $livraison_valider = $this->storeLivraisionValider($detail_cotations_id,$taux_tva,$qte[$item],$detail_livraisons_id,$remise,Session::get('profils_id'));

                                        if ($livraison_valider!=null) {
                                            
                                            $prix_unit = $livraison_valider->prix_unit;
                                            $montant_ht = $livraison_valider->montant_ht;
                                            $montant_ttc = $livraison_valider->montant_ttc;

                                            //recupération de la quantité coté

                                            //Entrée en stock

                                                //Type de movement
                                                $libelle_mvt = 'Entrée en stock';

                                                $type_mouvements_id = $this->getTypeMouvement($libelle_mvt);


                                                //determiner la demande d'achats
                                                try {

                                                    $demande_achats_id = CotationFournisseur::where('id', $request->cotation_fournisseurs_id)
                                                    ->first()
                                                    ->demande_achats_id;

                                                } catch (\Throwable $th) {
                                                    //throw $th;
                                                }
                                                    
                                                // fin determiner la demande d'achats

                                                //determiner le magasin
                                                    $ref_magasin = $this->getMagasin($demande_achats_id);
                                                //fin magasin


                                                // determiner l'articles
                                                    $ref_articles = null;
                                                    $date2 = null;

                                                    $livraison_valid = $this->getLivraisonValider($detail_livraisons_id);
                                                    

                                                    if ($livraison_valid!=null) {

                                                        $ref_articles = $livraison_valid->ref_articles;
                                                        $livraison_commandes_id = $livraison_valid->livraison_commandes_id;
                                                        $date2 = date("Y-m-d", strtotime($livraison_valid->created_at)) ;
                                                        $date2 = new DateTime($date2);
                                                        $date_clone2 = $date2;
                                                        
                                                    }        

                                                // fin determiner l'article

                                                // determiner le magasin_stocks
                                                    $magasin_stocks_id = null;

                                                    if ($ref_magasin!=null && $ref_articles) {

                                                        $magasin_stocks_id = $this->getMagasinStock($ref_magasin,$ref_articles);

                                                    }
                                                //fin determiner le magasin_stocks

                                                // enregistrer le mouvement d'article

                                                    if (isset($mouvements_id)) {

                                                        unset($mouvements_id);

                                                    }

                                                    try {
                                                        $taux_de_change = CotationFournisseur::where('id', $request->cotation_fournisseurs_id)
                                                        ->first()
                                                        ->taux_de_change;
                                                    } catch (\Throwable $th) {
                                                        //throw $th;
                                                    }

                                                    $mouvements_id = $this->storeMouvement($type_mouvements_id,$magasin_stocks_id,Session::get('profils_id'),$qte[$item],$prix_unit,$montant_ht,$request->tva,$montant_ttc,$taux_de_change);

                                                    if (isset($mouvements_id)) {
                                                        
                                                        if ($livraison_valider!=null) {
                                                            
                                                            $this->setLivraisonValider($livraison_valider->id,$mouvements_id);
                                                            
                                                        }
    
    
                                                        // récupération du montant du stock actuel
                                                            $montant_apres_stock = $this->getMontantMagasinStock($magasin_stocks_id);
                                                        //
    
                                                        // récupération de la quantité du stock actuel
                                                            $mouvements_qte = $this->getQteMagasinStock($magasin_stocks_id);
                                                            
    
                                                            // Quantité après eventuel stockage
                                                            if ($mouvements_qte!=null) {
                                                                $qte_apres_stockage = $mouvements_qte->qte_stock;
                                                                $qte_stock = $qte_apres_stockage;
                                                            } else {
                                                                $qte_apres_stockage = 1;
                                                                $qte_stock = 0;
                                                            }
                                                        //
    
                                                        //
                                                            if (isset($qte_apres_stockage) && isset($montant_apres_stock)) {
                                                                $cmup = $montant_apres_stock / $qte_apres_stockage;
                                                                $montant_stock = $cmup * $qte_stock;
                                                            }

                                                            $this->setMagasinStock($magasin_stocks_id,$cmup,$qte_stock,$montant_stock);
                                                            
                                                        //
                                                        
    
                                                        // stock_alert, stock_securite, stock_mini
    
                                                            //delai de livraison de cet article
                                                            //determiner la derniere livraison de cet article
                                                            $date1 = null;

                                                            $livraison_valid = $this->getLivraisonValider2($detail_livraisons_id,$ref_articles,$livraison_commandes_id);
                                                            
                                                        
                                                            if ($livraison_valid!=null) {
                                                                $date1 = date("Y-m-d", strtotime($livraison_valid->created_at)) ;
    
                                                                $date1 = new DateTime($date1);
                                                            }
    
                                                            if ($date2!=null && $date1!=null) {

                                                                // $date1 = new DateTime($date1);
                                                                // $date2 = new DateTime($date2);
                                                                $diff = $date2->diff($date1)->format("%a");
    
    
                                                                $delai_livraison = number_format((($diff/30)/3), 2) ;
                                                            } else {
                                                                $delai_livraison = 1;
                                                            }
    
                                                            //
    
    
                                                            // determiner la consommation moyenne du trimestre ecoulé
    
                                                            $date0 = $date_clone2->sub(new DateInterval('P3M'))->format('Y-m-d'); // P1D means a period of 1 day
    
                                                            $date3 = $date_clone2->add(new DateInterval('P3M'))->format('Y-m-d');
                                                            
                                                            $mouvement = $this->getMouvement($magasin_stocks_id,$date3);                  
    
                                                            if ($mouvement!=null) {
                                                                $moy_requisition = $mouvement->qte_sortie;
                                                            } else {
                                                                $moy_requisition = 0;
                                                            }
                                                    
    
                                                            //
    
                                                            // determiner le retard de livraison
    
                                                            //date de livraison prévue

                                                            $date_livraison_prevue = null;

                                                            $commande = $this->getCommande($demande_achats_id);

                                                            if ($commande != null) {
                                                                $date_livraison_prevue = $commande->date_livraison_prevue;
                                                            }
    
                                                            $date_livraison_prevue = new DateTime($date_livraison_prevue);
    
                                                            $diff_retard = $date2->diff($date_livraison_prevue)->format("%a");
    
                                                            $retard_livraison = number_format((($diff_retard/30)/3), 2);
    
    
                                                            //
    
                                                            //
    
                                                            $stock_mini = $delai_livraison * $moy_requisition;
                                                            $stock_securite = $retard_livraison * $moy_requisition;
                                                            $stock_alert = $stock_securite + $stock_mini;
    
                                                            $this->setMagasinStock2($magasin_stocks_id,$stock_mini,$stock_securite,$stock_alert);

                                                           
                                                        //
                                                    }
                                                    

                                                //

                                                
                                            //fin entrée en stock

                                        }

                                    //Fin entrée en stock
                                }
                            }
                        }


                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }

                }

                if ($livraison_valider!=null) {

                    $validation = 1;
                    $this->setLivraisonCommandeValidation($request->livraison_commandes_id,$validation);

                    $detail_cotationss = $this->getSumDetailCotationByCotationFournisseur($request->cotation_fournisseurs_id);

                    if ($detail_cotationss!=null) {
                        $qte_total_commandee = $detail_cotationss->qte_total_commandee;
                    }else{
                        $qte_total_commandee = 0;
                    }

                    $detail_livraisonss = $this->getSumDetailLivraisonByCotationFournisseur($request->cotation_fournisseurs_id);

                    if ($detail_livraisonss!=null) {
                        $qte_total_livree = $detail_livraisonss->qte_total_livree;
                    }else{
                        $qte_total_livree = 0;
                    }

                    $livraison_validerss = $this->getLivraisonValiderByCotationFournisseur($request->cotation_fournisseurs_id);
                    

                    if ($livraison_validerss!=null) {
                        $qte_total_livree_confirme = $livraison_validerss->qte_total_livree_confirme;
                    }else{
                        $qte_total_livree_confirme = 0;
                    }



                    if ($qte_total_livree_confirme != 0) {

                        if ($qte_total_commandee > $qte_total_livree_confirme) {
                            $libelle = "Livraison partielle confirmée";
                            $libelle_livraison = "Livraison partielle confirmée";
                        }elseif ($qte_total_commandee === $qte_total_livree_confirme) {
                            $libelle = "Livré";
                            $libelle_livraison = "Livré";
                        }

                    }else{
                        //$libelle = "Livraison annulée (Comite Réception)";
                        //$libelle_livraison = "Livraison annulée (Comite Réception)";
                    }        
        
                    //dd($qte_total_livree_confirme,$qte_total_commandee,$qte_total_livree_confirme);
                    if (isset($libelle)) {
                        // statut livraison
                
                            //determiner la demande d'achats
                                $demande_achats_id = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first()->demande_achats_id;
                            // fin determiner la demande d'achats

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

                                $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                            }

                            if(isset($libelle_livraison) && isset($livraison_commandes_id)){

                                $this->storeTypeStatutDemandeAchat($libelle_livraison);

                                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_livraison);

                                if ($type_statut_demande_achat != null) {
                                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                                    $this->setLastStatutLivraisonCommande($livraison_commandes_id);

                                    $this->storeStatutLivraisonCommande($livraison_commandes_id, Session::get('profils_id'), $type_statut_demande_achats_id,$request->commentaire);
                                }  

                            }
            
                        //fin statut livraison


                        $subject = $libelle;

                        //

                        $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                        //

                        // notifier l'émetteur

                        // utilisateur connecté

                        $this->notifDemandeAchat(auth()->user()->email,$subject,$demande_achats_id);
                

                        $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);
                        

                        $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);
                        //

                        //PV de réception

                        if (isset($request->pv_reception)) {
            
                            $pv_reception =  $request->pv_reception->store('pv_reception','public');

                            $name = $request->pv_reception->getClientOriginalName(); 

                            $libelle_piece = "Demande d'achats";
                            $flag_actif = 1;
                            $piece_jointes_id = null;

                            if(isset($livraison_commandes_id)){

                                $libelle_piece_livraison = "PV de réception";

                                $this->storePieceJointeLivraison($livraison_commandes_id,Session::get('profils_id'),$libelle_piece_livraison,$pv_reception,$flag_actif,$name,null);
                                
                            }

                            $cotationFournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)
                            ->first();

                            if ($cotationFournisseur!=null) {
                                $dataPiece = [
                                    'subject_id'=>$cotationFournisseur->demande_achats_id,
                                    'profils_id'=>Session::get('profils_id'),
                                    'libelle'=>$libelle_piece,
                                    'piece'=>$pv_reception,
                                    'flag_actif'=>$flag_actif,
                                    'name'=>$name,
                                    'piece_jointes_id'=>$piece_jointes_id,
                                ];

                                $controller3->procedureStorePieceJointe($dataPiece);
                            }
                        }

                        //Comptabilisation
                        if($demandeAchat != null){
                            $user = $this->getInfoUserByProfilId(Session::get('profils_id'));

                            if($user != null){
                                
                                
                                $reference_piece = $demandeAchat->num_bc;
                                $compte = (int) $demandeAchat->ref_fam;

                                $taux_de_change = 1;

                                try {
                                    $taux_de_change = CotationFournisseur::where('id', $request->cotation_fournisseurs_id)
                                    ->first()
                                    ->taux_de_change;
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }

                                $net_a_payer = filter_var($request->net_a_payer,FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
                                

                                $net_a_payers = $net_a_payer * $taux_de_change;

                                $montant_comptabilisation = $net_a_payers;
                                $date_transaction = date('Y-m-d H:i:s');
                                $mle = $user->mle;
                                $code_structure = $demandeAchat->code_structure;
                                $code_section = $demandeAchat->code_structure."01";
    
                                

                                if($compte >= 600000 && $compte < 700000){

                                    $type_piece = "ACH_STOCK";

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
                                        'acompte'=>null,
                                        'exercice'=>$demandeAchat->exercice,
                                        'code_gestion'=>$demandeAchat->code_gestion,
                                    ];

                                    $this->storeComptabilisationEcriture($data);

                                    $type_piece_ent_stock = "ENT_STOCK";
                                    $data = [
                                        'type_piece'=>$type_piece_ent_stock,
                                        'reference_piece'=>$reference_piece,
                                        'compte'=>$compte,
                                        'montant'=>$montant_comptabilisation,
                                        'date_transaction'=>$date_transaction,
                                        'mle'=>$mle,
                                        'code_structure'=>$code_structure,
                                        'code_section'=>$code_section,
                                        'ref_depot'=>$user->ref_depot,
                                        'acompte'=>null,
                                        'exercice'=>$demandeAchat->exercice,
                                        'code_gestion'=>$demandeAchat->code_gestion,
                                    ];

                                    $this->storeComptabilisationEcriture($data);

                                }
                                

                                $type_piece_desengagement = "ENG";

                                $montant_comptabilisation_signe = -1 * $montant_comptabilisation;

                                $data = [
                                    'type_piece'=>$type_piece_desengagement,
                                    'reference_piece'=>$reference_piece,
                                    'compte'=>$compte,
                                    'montant'=>$montant_comptabilisation_signe,
                                    'date_transaction'=>$date_transaction,
                                    'mle'=>$mle,
                                    'code_structure'=>$code_structure,
                                    'code_section'=>$code_section,
                                    'ref_depot'=>$user->ref_depot,
                                    'acompte'=>null,
                                    'exercice'=>$demandeAchat->exercice,
                                    'code_gestion'=>$demandeAchat->code_gestion,
                                ];
                                
                                $this->storeComptabilisationEcriture($data);
                            }
                        }
                        
                        return redirect('demande_achats/index')->with('success',$libelle);

                    }else {
                        return redirect()->back()->with('error','Aucune livraison effectuée');
                    }

                }else{
                    return redirect()->back()->with('error','Validation de la livraison echouée');
                }

            }elseif ($request->submit === 'annuler_livraison_achat') {

                $request->validate([
            
                    'detail_livraisons_id'=>['required','array'],
                    'commentaire'=>['required','string'],

                ]);

                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {
                        foreach ($request->detail_cotations_id as $item => $value) {

                            $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            $detail_cotation = $controller3->getDetailCotationById($request->detail_cotations_id[$item]);

                            if ($detail_cotation!=null) {
                                $qte_cotation[$item] = $detail_cotation->qte;

                                if ($qte_cotation[$item] < $qte[$item]) {
                                    return redirect()->back()->with('error','La quantité livrée ne peut être supérieure à la quantité commandée.');
                                }
                            }
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }
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




                $detail_livraison = null;

                
                if (isset($request->detail_cotations_id)) {
                    if (count($request->detail_cotations_id)>0) {

                        $livraison_commandes = $this->getLivraisonCommandeById($request->livraison_commandes_id);

                        if ($livraison_commandes!=null) {
                            
                            $livraison_commandes_id = $livraison_commandes->livraison_commandes_id;

                            if($demandeAchat != null) {

                                    $validation_livraison = 0;
                                    $this->setLivraisonCommande($livraison_commandes_id,$request->cotation_fournisseurs_id,Session::get('profils_id'),$montant_total_brut,$taux_remise_generale,$remise_generale,$montant_total_net,$tva,$montant_total_ttc,$assiete_bnc,$taux_bnc,$net_a_payer,$cotationFournisseur->taux_de_change,$validation_livraison);


                                
                            }
                                  

                        }

                        foreach ($request->detail_cotations_id as $item => $value) {

                            //$qte[$item] = 0;
                            // $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);


                            try {
                                $qte[$item] = $qte[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            
                            if (gettype($qte[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            $detail_cotations_id = $request->detail_cotations_id[$item];
                            $remise = $request->remise[$item];

                            $sequence = null;
                            $detail_livraison =  $this->storeDetailLivraison($detail_cotations_id,$qte[$item],$taux_tva,$remise,$livraison_commandes_id,$detail_livraison,$request->submit,$sequence,$request->detail_livraisons_id[$item]);
                            
                        }
                    }else{
                        return redirect()->back()->with('error','Aucune ligne d\'articles trouvé');
                    }
                }


                if ($detail_livraison!=null) {
                    
                    $libelle = "Livraison annulée (Comite Réception)";
                    $libelle_livraison = "Livraison annulée (Comite Réception)";
                    if (isset($libelle)) {
                        // statut livraison
                
                            //determiner la demande d'achats
                                $demande_achats_id = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first()->demande_achats_id;
                            // fin determiner la demande d'achats
                
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

                                $controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);
                            }

                            if(isset($libelle_livraison) && isset($livraison_commandes_id)){

                                $this->storeTypeStatutDemandeAchat($libelle_livraison);

                                $type_statut_demande_achat = $this->getTypeStatutDemandeAchat($libelle_livraison);

                                if ($type_statut_demande_achat != null) {
                                    $type_statut_demande_achats_id = $type_statut_demande_achat->id;

                                    $this->setLastStatutLivraisonCommande($livraison_commandes_id);

                                    $this->storeStatutLivraisonCommande($livraison_commandes_id, Session::get('profils_id'), $type_statut_demande_achats_id,$request->commentaire);
                                }  

                            }
                            // if(isset($livraison_commandes_id)){
                            //     try {
                            //         BonLivraison::where('livraison_commandes_id',$livraison_commandes_id)->delete();
                            //     } catch (\Throwable $th) {
                            //         //throw $th;
                            //     }
                                
                            // }
            
                        //fin statut livraison


                            $subject = $libelle;

                            $type_profils_names = ['Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Responsable DFC'];
                                //

                                // notifier l'émetteur

                                // utilisateur connecté
                                $this->notifDemandeAchat(auth()->user()->email,$subject,$demande_achats_id);
                

                                $this->notifDemandeAchats($subject,$demande_achats_id,$type_profils_names);
                                

                                $this->notifDemandeAchatFournisseur($subject,$demande_achats_id,$request->cotation_fournisseurs_id);

                            

                        //

                        return redirect('demande_achats/index')->with('success','Annulation enregistrée');
                    }else {
                        return redirect()->back()->with('error','Aucune annulation effectuée');
                    }                   
                }else{
                   return redirect()->back()->with('error','Annulation echouée');
                }




            }

        }



        

        


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
