<?php

namespace App\Http\Controllers;

use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use App\Models\CotationFournisseur;
use App\Models\SelectionAdjudication;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class SelectionAdjudicationController extends Controller
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
        $selection_adjudications = $this->getSelectionAdjudications();        

        return view('selection_adjudications.index',[ 
            'selection_adjudications'=>$selection_adjudications
        ]);

    }

    public function liste($demandeAchat)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeAchat);
        } catch (DecryptException $e) {
            //
        }

        $demandeAchat = DemandeAchat::findOrFail($decrypted);

        $etape = "selection_adjudication_liste";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }

        $cotation_fournisseurs = $this->getCotationFournisseur2($demandeAchat->id);

        return view('selection_adjudications.liste',[
            'cotation_fournisseurs'=>$cotation_fournisseurs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($cotationFournisseur)
    {

        $type_profils_name = null;

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

        $etape = "selection_adjudication_create";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Administrateur fonctionnel','Gestionnaire des achats','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Directeur Général Adjoint','Directeur Général','Responsable DFC','Comite Réception'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        
        $credit_budgetaires_select = null;
        $griser = 1;
        $affiche_zone_struture = 1;

        
        $demande_achats = $this->getDetailCotationFournisseurs($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);        

           

        // determiner l'année de la demande    
        
        $demande_achat_info = $this->getDetailCotationFournisseur($cotationFournisseur->demande_achats_id,$cotationFournisseur->id);

        $credit_budgetaire_structure = null;

        if ($demande_achat_info != null) {

            $credit_budgetaire_structure = $this->getCreditBudgetaireById($demande_achat_info->credit_budgetaires_id);

        }

        if($credit_budgetaire_structure === null){

            if(isset($demande_achat_info->num_bc)){
                $code_struc = explode('BC',explode('/',$demande_achat_info->num_bc)[1])[0];
                $credit_budgetaire_structure = $this->getStructureByCode($code_struc);
            }
            
        }
        //vérifier si le Responsable DMP à visé le choix du fournisseur

            $statut = $this->getCategorieDemandeAchat($cotationFournisseur->demande_achats_id);

        //

        $commentaire = null;
        $profil_commentaire = null;
        $nom_prenoms_commentaire = null;

        $statut_organisation = $this->getStatutOrganisation($cotationFournisseur->organisations_id);
        
        if($statut_organisation != null){
            
            $statut_demande_achat_specifique = $this->getLastStatutDemandeAchat($cotationFournisseur->demande_achats_id,$statut_organisation->profils_id);

            if ($statut_demande_achat_specifique!=null) {

                $commentaire = $statut_demande_achat_specifique->commentaire;
                $profil_commentaire = $statut_demande_achat_specifique->name;
                $nom_prenoms_commentaire = $statut_demande_achat_specifique->nom_prenoms;  
                
                
            }

        }
       

        $statut_demande_achat = $this->getLastStatutDemandeAchat($cotationFournisseur->demande_achats_id);

        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;            
        }
        
        if($demandeAchat != null){
            try {
                $param = $demandeAchat->exercice.'-'.$demandeAchat->code_gestion.'-'.$demandeAchat->code_structure.'-'.$demandeAchat->ref_fam;
    
                $this->storeCreditBudgetaireByWebService($param, $demandeAchat->ref_depot);
            } catch (\Throwable $th) {
            }
        }

        $disponible = null;

        $select_disponible = $this->getCreditBudgetaireDisponibleByDemandeAchatId($cotationFournisseur->demande_achats_id);
        if($select_disponible != null){
            $disponible = $select_disponible->credit;
        }

        $commande = $this->getCommande($cotationFournisseur->demande_achats_id); 

        if($commande != null){
            if($commande->solde_avant_op != null){
                $disponible = $commande->solde_avant_op;
            }
        }

        $statut_demande_achats = $this->getStatutDemandeAchats($cotationFournisseur->demande_achats_id);

        //dd($commande,$disponible);

        return view('selection_adjudications.create',[
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'cotation_fournisseurs_id'=>$cotationFournisseur->id,
            'statut'=>$statut,
            'commentaire'=>$commentaire,
            'libelle'=>$libelle,
            'profil_commentaire'=>$profil_commentaire,
            'nom_prenoms_commentaire'=>$nom_prenoms_commentaire,
            'commande'=>$commande,
            'affiche_zone_struture'=>$affiche_zone_struture,
            'credit_budgetaire_structure'=>$credit_budgetaire_structure,
            'type_profils_name'=>$type_profils_name,
            'statut_demande_achats'=>$statut_demande_achats,
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
            'delai'=>['nullable','numeric'],
            'date_echeance'=>['required','string'],
            'ref_articles'=>['required','array'],
            'ref_fam'=>['required','numeric'],
            'libelle_gestion'=>['required','string'],
            'code_gestion'=>['required','string'],
            'denomination'=>['required','string'],
            'organisations_id'=>['required','numeric'],
            'libelle_periode'=>['required','string'],
            'cotation_fournisseurs_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'commentaire'=>['nullable','string'],
        ]);

        $cotation_fournisseur = CotationFournisseur::where('id',$request->cotation_fournisseurs_id)->first();

        if ($cotation_fournisseur!=null) {
            $demande_achats_id = $cotation_fournisseur->demande_achats_id;
        }

        $demandeAchat = $this->getDemandeAchat($demande_achats_id);
        

        $etape = "selection_adjudication_store";
        $type_profils_name = null;
        $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

        if($type_profil != null){
            $type_profils_name = $type_profil->name;
        }
        $type_profils_lists = ['Responsable des achats'];
        
        $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name,$demandeAchat);

        if ($profil === null) {
            return redirect()->back()->with('error','Accès refusé');
        }
        // 

        $selection_adjudication = $this->controller3->getSelectionAdjudicationsByDemandeAchats($demande_achats_id);            
        
        foreach ($selection_adjudication as $selection_adjudicatio) {

            $this->controller3->deleteSelectionAdjudication($selection_adjudicatio->id);
            
        }
        
        $selection_adjudications = $this->controller3->storeSelectionAdjudication($request->cotation_fournisseurs_id,Session::get('profils_id'));        

        if ($selection_adjudications!=null) {
            
            if (isset($demande_achats_id)) {

                // STATUT DE LA DEMANDE
                    $libelle = "Fournisseur sélectionné";
                    $libelle_df = "Fournisseur sélectionné";
                    $commentaire = $request->commentaire;

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
                            'commentaire'=>trim($commentaire),
                            'profils_id'=>Session::get('profils_id'),
                        ];

                        $this->controller3->storeStatutDemandeAchat($dataStatutDemandeAchat);

                        $type_operations_libelle = "Demande d'achats" ;

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


                    }



                //

                // notifier l'émetteur
                $subject = 'Sélection du fournisseur';

                // utilisateur connecté
                    $email = auth()->user()->email;
                    $this->notifDemandeAchat($email,$subject,$request->id);
                //

                // notifier le gestionnaire des achats
                $profils = ['Gestionnaire des achats'];
                $this->notifDemandeAchat2($subject,$request->id,$profils);


            }

            return redirect('/demande_achats/index')->with('success','Fournisseur sélectionné');
        }else{
            return redirect()->back()->with('error','Echec de la sélection de la cotation');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SelectionAdjudication  $selectionAdjudication
     * @return \Illuminate\Http\Response
     */
    public function show(SelectionAdjudication $selectionAdjudication)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SelectionAdjudication  $selectionAdjudication
     * @return \Illuminate\Http\Response
     */
    public function edit(SelectionAdjudication $selectionAdjudication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SelectionAdjudication  $selectionAdjudication
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SelectionAdjudication $selectionAdjudication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SelectionAdjudication  $selectionAdjudication
     * @return \Illuminate\Http\Response
     */
    public function destroy(SelectionAdjudication $selectionAdjudication)
    {
        //
    }
}
 