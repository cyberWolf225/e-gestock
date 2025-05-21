<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Agent;
use App\Models\Perdiem;
use App\Models\Travaux;
use App\Models\Fonction;
use App\Models\DemandeFond;
use App\Models\DemandeAchat;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\ProfilFonction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use App\Models\StatutProfilFonction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\TypeStatutProfilFonction;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Encryption\DecryptException;

class PrintController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function getFonctionAuthenticate($libelle){

        $fonction = Fonction::where('libelle',$libelle)->first();

        if ($fonction!=null) {

            $fonctions_id = $fonction->id;

        }else{

            $fonctions_id = Fonction::create([
                'libelle'=>$libelle,
            ])->id;

        }

        return $fonctions_id;

    }

    public function getTypeStatutProfilFonction($libelle){

        $type_statut_profil_fonction = TypeStatutProfilFonction::where('libelle',$libelle)
        ->first();

        if ($type_statut_profil_fonction!=null) {

            $type_statut_profil_fonctions_id = $type_statut_profil_fonction->id;

        }else{

            $type_statut_profil_fonctions_id = TypeStatutProfilFonction::create([
                'libelle'=>$libelle,
            ])->id;

        }

        return $type_statut_profil_fonctions_id;
    }

    public function setStatutProfilFonction($profil_fonctions_id,$type_statut_profil_fonctions_id,$profils_id,$date_debut,$date_fin,$commentaire){

        $data = [
            'profil_fonctions_id'=>$profil_fonctions_id,
            'type_statut_profil_fonctions_id'=>$type_statut_profil_fonctions_id,
            'profils_id'=>$profils_id,
            'date_debut'=>$date_debut,
            'date_fin'=>$date_fin,
            'commentaire'=>$commentaire,
        ];
        $statut_profil_fonction = DB::table('statut_profil_fonctions as spf')
        ->join('type_statut_profil_fonctions as tspf','tspf.id','=','spf.type_statut_profil_fonctions_id')
        ->where('profil_fonctions_id',$profil_fonctions_id)
        ->orderByDesc('spf.id')
        ->limit(1)
        ->select('tspf.libelle')
        ->first();

        if ($statut_profil_fonction!=null) {
            $libelle = $statut_profil_fonction->libelle;

            if ($libelle!='Activé') {
                StatutProfilFonction::create($data);
            }
        }else{
            StatutProfilFonction::create($data);
        }
    }

    public function setProfilFonctionAuthenticate($agents_id,$fonctions_id,$profils_id){

        $profil_fonction = ProfilFonction::where('agents_id',$agents_id)
            ->where('fonctions_id',$fonctions_id)
            ->first();
        if ($profil_fonction!=null) {

            $profil_fonctions_id = $profil_fonction->id;
            if ($profil_fonction->flag_actif != 1) {

                ProfilFonction::where('id',$profil_fonction->id)
                ->update([
                    'flag_actif'=>1
                ]);

            }

        }else{
            $profil_fonctions_id = ProfilFonction::create([
                'agents_id'=>$agents_id,
                'fonctions_id'=>$fonctions_id,
                'flag_actif'=>1
            ])->id;
        }

        if(isset($profil_fonctions_id)){

            $libelle = "Activé";
            $type_statut_profil_fonctions_id = $this->getTypeStatutProfilFonction($libelle);

            if (isset($type_statut_profil_fonctions_id)) {

                $date_debut = date("Y-m-d");
                $date_fin = null;
                $commentaire = "Créé à la connexion";

                $this->setStatutProfilFonction($profil_fonctions_id,$type_statut_profil_fonctions_id,$profils_id,$date_debut,$date_fin,$commentaire);

            }


        }
    }

    public function getAgentAuthenticate($mle){
        $agents_id = null;
        $agent = Agent::where('mle',$mle)->first();
        if ($agent!=null) {
            $agents_id = $agent->id;
        }

        return $agents_id;

    }

    public function printDemandeAchat($demandeAchat){
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeAchat);
        } catch (DecryptException $e) {
            //
        }

        $demandeAchat = DemandeAchat::findOrFail($decrypted);

        $demande_achat = DB::table('demande_achats as da')
            ->join('gestions as g','g.code_gestion','=','da.code_gestion')
            ->join('credit_budgetaires as cb','cb.id','=','da.credit_budgetaires_id')
            ->join('structures as s','s.code_structure','=','cb.code_structure')
            ->where('da.id',$demandeAchat->id)
            ->select('da.exercice','da.code_gestion','s.code_structure','da.ref_fam','da.intitule','da.created_at','da.num_bc','g.libelle_gestion','s.nom_structure')
            ->first();
        
        $cotation_fournisseur = DB::table('cotation_fournisseurs as cf')
            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
            ->join('organisations as o','o.id','=','cf.organisations_id')
            ->join('devises as d','d.id','=','cf.devises_id')
            ->join('commandes as c','c.demande_achats_id','=','cf.demande_achats_id')
            ->where('cf.demande_achats_id',$demandeAchat->id)
            ->select('o.denomination','o.entnum','o.contacts','d.code','d.libelle','d.symbole','o.num_contribuable','o.adresse','c.date_livraison_prevue','cf.montant_total_brut','cf.remise_generale','cf.montant_total_net','cf.tva','cf.montant_total_ttc','cf.assiete_bnc','cf.taux_bnc','cf.net_a_payer','cf.acompte','cf.taux_acompte','cf.montant_acompte')
            ->orderByDesc('sa.id')
            ->limit(1)
            ->first();

        
        $detail_cotations = DB::table('detail_cotations as dc')
            ->join('articles as a','a.ref_articles','=','dc.ref_articles')
            ->join('cotation_fournisseurs as cf','cf.id','=','dc.cotation_fournisseurs_id')
            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
            ->join('organisations as o','o.id','=','cf.organisations_id')
            ->join('devises as d','d.id','=','cf.devises_id')
            ->join('commandes as c','c.demande_achats_id','=','cf.demande_achats_id')
            ->where('cf.demande_achats_id',$demandeAchat->id)
            ->select('dc.ref_articles','dc.qte','dc.prix_unit','dc.remise','dc.montant_ht','dc.montant_ttc','a.design_article','a.code_unite')
            ->get();
        
        $statut_demande_achat = DB::table('statut_demande_achats as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->where('tsda.libelle','Retiré (Frs.)')
            ->where('sda.demande_achats_id',$demandeAchat->id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->select('sda.created_at as date_retrait')
            ->first();

            
        $signataires = DB::table('signataire_demande_achats as sda')
            ->join('profil_fonctions as pf','pf.id','=','sda.profil_fonctions_id')
            ->join('agents as a','a.id','=','pf.agents_id')
            ->join('fonctions as f','f.id','=','pf.fonctions_id')
            ->where('sda.demande_achats_id',$demandeAchat->id)
            ->where('sda.flag_actif',1)
            ->select('a.mle','a.nom_prenoms','a.nom_signataire','f.libelle as libelle_fonction','f.description as description_fonction','a.genres_id')
            ->get();            

            $pdf = PDF::loadView('prints.demande_achats.create', [
                        'demande_achat'=>$demande_achat,
                        'cotation_fournisseur'=>$cotation_fournisseur,
                        'detail_cotations'=>$detail_cotations,
                        'statut_demande_achat'=>$statut_demande_achat,
                        'signataires'=>$signataires
                    ]);

            //return $pdf->stream($demande_achat->num_bc.'.pdf');
            //S'assurer que tous les signataires ont leur signature enregistrée

            $signatureController = new SignatureController();
            $urlPostDocument = $signatureController->urlPostDocument();
            $urlGetDocument = $signatureController->urlGetDocument();
            $apiKey = $signatureController->apiKey();
            $url_to = $signatureController->url_to();
            $headers = $signatureController->headers();
            
            $autorisation_de_signature = 1;
            $liste_signataire_sans_signature = null;

            foreach ($signataires as $signataire) {

                $copie = ' - Copie';
                $extention_img = '.png';
                
                $path_img = 'storage/emargements/m' . $signataire->mle . $copie . $extention_img;

                $public_path_img = str_replace("/","\\",public_path($path_img));

                $public_path_img_statut = @file_exists($public_path_img);

                if($public_path_img_statut === false){
                    $autorisation_de_signature = null;
                    $liste_signataire_sans_signature = $liste_signataire_sans_signature . ' - ' . $signataire->nom_prenoms;
                }
            }

            $orientation = "Portrait_bon_de_commande";
            $data = [
                'reference'=>$demande_achat->num_bc,
                'type_operations_libelle'=>'bcs',
                'pdf'=>$pdf,
                'signataires'=>$signataires,
                'orientation'=>$orientation,
                'url_to'=>$url_to,
                'urlPostDocument'=>$urlPostDocument,
                'urlGetDocument'=>$urlGetDocument,
                'apiKey'=>$apiKey,
                'headers'=>$headers
            ];

            if($autorisation_de_signature === 1){
                $signatureController->proceduresignDocuments($data);
            }

            if($autorisation_de_signature === null){
                return redirect()->back()->with('error',"Echec de la signature du document. Veuillez contacter l'administrateur technique. Liste de signataires dont les signataires sont introuvables : " . $liste_signataire_sans_signature);
            }
            
       
    }

    public function printTravaux($travaux){
        
        
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($travaux);
        } catch (DecryptException $e) {
            //
        }

        $travaux = Travaux::findOrFail($decrypted);

        //
        $travauxe = DB::table('travauxes as t')
            ->join('structures as s', 's.code_structure', '=', 't.code_structure')
            ->join('gestions as g', 'g.code_gestion', '=', 't.code_gestion')
            ->join('organisations as o', 'o.id', '=', 't.organisations_id')
            ->join('devises as d', 'd.id', '=', 't.devises_id')
            ->join('periodes as p', 'p.id', '=', 't.periodes_id')
            ->join('familles as f', 'f.ref_fam', '=', 't.ref_fam')
            ->select('t.id as travauxes_id', 't.num_bc', 't.intitule', 't.ref_fam', 't.code_structure', 't.ref_depot', 't.code_gestion', 't.exercice', 't.montant_total_brut', 't.remise_generale', 't.montant_total_net', 't.tva', 't.montant_total_ttc', 't.net_a_payer', 't.acompte', 't.taux_acompte', 't.montant_acompte', 't.delai', 't.date_echeance', 's.nom_structure', 'f.design_fam', 't.credit_budgetaires_id', 'p.libelle_periode', 'o.id as organisations_id', 'o.denomination', 'd.code', 'd.libelle as devises_libelle', 't.created_at', 'p.valeur', 't.date_livraison_prevue', 't.date_retrait','o.entnum','o.contacts','o.adresse','d.libelle as devises_libelle')
            ->where('t.id', $travaux->id)
            ->first();
        
        $detail_travauxes = DB::table('travauxes as t')
            ->join('detail_travauxes as dt', 'dt.travauxes_id', '=', 't.id')
            ->join('services as s', 's.id', '=', 'dt.services_id')
            ->select('s.libelle', 'dt.qte', 'dt.prix_unit', 'dt.remise', 'dt.montant_ht', 'dt.montant_ttc')
            ->where('t.id', $travaux->id)
            ->where('dt.flag_valide', 1)
            ->get();

        $signataires = DB::table('signataire_travauxes as sda')
            ->join('profil_fonctions as pf','pf.id','=','sda.profil_fonctions_id')
            ->join('agents as a','a.id','=','pf.agents_id')
            ->join('fonctions as f','f.id','=','pf.fonctions_id')
            ->where('sda.travauxes_id',$travaux->id)
            ->where('sda.flag_actif',1)
            ->select('a.mle','a.nom_prenoms','a.nom_signataire','f.libelle as libelle_fonction','f.description as description_fonction','a.genres_id')
            ->get();

            
            

        //instantiate and use the dompdf class

        try {            
            $pdf = PDF::loadView('prints.travaux.create',[
                'travauxe'=>$travauxe,
                'signataires'=>$signataires,
                'detail_travauxes'=>$detail_travauxes
            ]);

            //S'assurer que tous les signataires ont leur signature enregistrée

            $signatureController = new SignatureController();
            $urlPostDocument = $signatureController->urlPostDocument();
            $urlGetDocument = $signatureController->urlGetDocument();
            $apiKey = $signatureController->apiKey();
            $url_to = $signatureController->url_to();
            $headers = $signatureController->headers();
            
            $autorisation_de_signature = 1;
            $liste_signataire_sans_signature = null;

            foreach ($signataires as $signataire) {

                $copie = ' - Copie';
                $extention_img = '.png';
                
                $path_img = 'storage/emargements/m' . $signataire->mle . $copie . $extention_img;

                $public_path_img = str_replace("/","\\",public_path($path_img));

                $public_path_img_statut = @file_exists($public_path_img);

                if($public_path_img_statut === false){
                    $autorisation_de_signature = null;
                    $liste_signataire_sans_signature = $liste_signataire_sans_signature . ' - ' . $signataire->nom_prenoms;
                }
            }

            $orientation = "Portrait_bon_de_commande";
            $data = [
                'reference'=>$travauxe->num_bc,
                'type_operations_libelle'=>'bcn',
                'pdf'=>$pdf,
                'signataires'=>$signataires,
                'orientation'=>$orientation,
                'url_to'=>$url_to,
                'urlPostDocument'=>$urlPostDocument,
                'urlGetDocument'=>$urlGetDocument,
                'apiKey'=>$apiKey,
                'headers'=>$headers
            ];

            if($autorisation_de_signature === 1){
                $signatureController->proceduresignDocuments($data);
            }

            if($autorisation_de_signature === null){
                return redirect()->back()->with('error',"Echec de la signature du document. Veuillez contacter l'administrateur technique. Liste de signataires dont les signataires sont introuvables : " . $liste_signataire_sans_signature);
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
        
       
    }

    public function printDemandeFond($demandeFond){
        $signatureController = new SignatureController();
        $urlPostDocument = $signatureController->urlPostDocument();
        $urlGetDocument = $signatureController->urlGetDocument();
        $apiKey = $signatureController->apiKey();
        $url_to = $signatureController->url_to();
        $headers = $signatureController->headers();

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeFond);
        } catch (DecryptException $e) {
            //
        }

        $demandeFond = DemandeFond::findOrFail($decrypted);

        $demande_fond = DB::table('demande_fonds as df')
            ->join('credit_budgetaires as cb','cb.id','=','df.credit_budgetaires_id')
            ->join('structures as s','s.code_structure','=','cb.code_structure')
            ->join('familles as f', 'f.ref_fam', '=', 'df.ref_fam')
            ->join('moyen_paiements as mp', 'mp.id', '=', 'df.moyen_paiements_id')
            ->join('gestions as g', 'g.code_gestion', '=', 'df.code_gestion')
            ->where('df.id',$demandeFond->id)
            ->select('df.exercice','s.code_structure','df.ref_fam','df.intitule','df.created_at','df.num_dem','s.nom_structure','df.solde_avant_op','mp.libelle as moyen_paiements_libelle','df.code_gestion','g.libelle_gestion','df.profils_id as profils_id_beneficiaire','df.agents_id','df.observation','df.montant')
            ->first();

        $beneficiaire = null;
        $charge_suivi = null;
        $fournisseur = null;
        $organisation = null;

        if ($demande_fond != null) {
            if (isset($demande_fond->profils_id_beneficiaire)) {
                $beneficiaire = DB::table('profils as p')
                    ->join('users as u','u.id','=','p.users_id')
                    ->join('agents as a','a.id','=','u.agents_id')
                    ->where('p.id',$demande_fond->profils_id_beneficiaire)
                    ->select('a.mle','a.nom_prenoms')
                    ->first();

                    if ($beneficiaire != null) {
                        $bloks = explode('frs',$beneficiaire->mle);
                        if (isset($bloks[1])) {
                            $fournisseur = 1;

                            $entnum = str_replace('frs','',$beneficiaire->mle);
                            $organisation = Organisation::where('entnum',$entnum)->first();

                        }
                    }
            }

            if (isset($demande_fond->agents_id)) {
                $charge_suivi = DB::table('agents as a')
                    ->where('a.id',$demande_fond->agents_id)
                    ->select('a.mle','a.nom_prenoms')
                    ->first();
            }
        }

        $cotation_service = DB::table('cotation_services as cs')
            ->join('organisations as o', 'o.id', '=', 'cs.organisations_id')
            ->join('periodes as p', 'p.id', '=', 'cs.periodes_id')
            ->where('cs.demande_fonds_id',$demandeFond->id)
            ->select('o.*','p.*','cs.*','cs.id as cotation_services_id')
            ->first();
        $detail_demande_fonds = [];
        if ($cotation_service != null) {

            $detail_demande_fonds = DB::table('cotation_services as cs')
            ->join('detail_cotation_services as dcs', 'dcs.cotation_services_id', '=', 'cs.id')
            ->join('services as s', 's.id', '=', 'dcs.services_id')
            ->select('s.libelle', 'dcs.qte', 'dcs.prix_unit', 'dcs.remise', 'dcs.montant_ht', 'dcs.montant_ttc','dcs.*')
            ->where('cs.id', $cotation_service->cotation_services_id)
            ->get();

        }

        $signataires = DB::table('signataire_demande_fonds as sda')
            ->join('profil_fonctions as pf','pf.id','=','sda.profil_fonctions_id')
            ->join('agents as a','a.id','=','pf.agents_id')
            ->join('fonctions as f','f.id','=','pf.fonctions_id')
            ->where('sda.demande_fonds_id',$demandeFond->id)
            ->where('sda.flag_actif',1)
            ->select('a.mle','a.nom_prenoms','a.nom_signataire','f.libelle as libelle_fonction','f.description as description_fonction','a.genres_id')
            ->get();

        if($beneficiaire != null){
            $mle = $beneficiaire->mle;
            $nom_prenoms = $beneficiaire->nom_prenoms;
        }

        if($organisation != null){
            $mle = $organisation->entnum;
        }

        try {            
            $pdf = PDF::loadView('prints.demande_fonds.create',[
                'demande_fond'=>$demande_fond,
                'signataires'=>$signataires,
                'cotation_service'=>$cotation_service,
                'detail_demande_fonds'=>$detail_demande_fonds,
                'beneficiaire'=>$beneficiaire,
                'charge_suivi'=>$charge_suivi,
                'fournisseur'=>$fournisseur,
                'organisation'=>$organisation,
                'mle'=>$mle,
                'nom_prenoms'=>$nom_prenoms,
            ]);
            //return $pdf->download($demandeFond->num_bc.'.pdf');
            //return $pdf->stream($demande_fond->num_dem.'.pdf');

            //S'assurer que tous les signataires ont leur signature enregistrée
            
            $autorisation_de_signature = 1;
            $liste_signataire_sans_signature = null;

            foreach ($signataires as $signataire) {

                $copie = ' - Copie';
                $extention_img = '.png';
                
                $path_img = 'storage/emargements/m' . $signataire->mle . $copie . $extention_img;

                $public_path_img = str_replace("/","\\",public_path($path_img));

                $public_path_img_statut = @file_exists($public_path_img);

                if($public_path_img_statut === false){
                    $autorisation_de_signature = null;
                    $liste_signataire_sans_signature = $liste_signataire_sans_signature . ' - ' . $signataire->nom_prenoms;
                }
            }

            $orientation = "Portrait_bon_de_commande";
            $data = [
                'reference'=>$demande_fond->num_dem,
                'type_operations_libelle'=>'demande_fonds',
                'pdf'=>$pdf,
                'signataires'=>$signataires,
                'orientation'=>$orientation,
                'url_to'=>$url_to,
                'urlPostDocument'=>$urlPostDocument,
                'urlGetDocument'=>$urlGetDocument,
                'apiKey'=>$apiKey,
                'headers'=>$headers
            ];

            if($autorisation_de_signature === 1){
                $signatureController->proceduresignDocuments($data);
            }

            if($autorisation_de_signature === null){
                return redirect()->back()->with('error',"Echec de la signature du document. Veuillez contacter l'administrateur technique. Liste de signataires dont les signataires sont introuvables : " . $liste_signataire_sans_signature);
            }

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function print_consommation($famille_crypt=null,$structure_crypt=null,$article_crypt=null,$periode_debut_crypt=null,$periode_fin_crypt=null){

        $titre = null;
        $section = null;
        $consommations = []; 

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

        $structure = null;

        if($structure_crypt != null){

            $decrypted_structure = null;
            
            try {
                $decrypted_structure = Crypt::decryptString($structure_crypt);
            } catch (DecryptException $e) {
                //
            }
    
            $structure = $this->getStructureByCode($decrypted_structure);

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

        $periode_debut = null;

        if($periode_debut_crypt != null){

            $periode_debut = null;
            
            try {
                $periode_debut = Crypt::decryptString($periode_debut_crypt);
            } catch (DecryptException $e) {
                //
            }


        }

        $periode_fin = null;

        if($periode_fin_crypt != null){

            $periode_fin = null;
            
            try {
                $periode_fin = Crypt::decryptString($periode_fin_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }
        $titre = 'consommation';
        if($famille != null && $structure != null && $periode_debut != null && $periode_fin != null){
            if($article != null){
                $consommations = $this->getConsomations($famille->ref_fam,$structure->code_structure,$periode_debut,$periode_fin,$article->ref_articles);

                $titre = $titre.'_'.$famille->ref_fam.'_'.$famille->ref_articles.'_'.$structure->code_structure.'_du_'.$periode_debut.'_au_'.$periode_fin;
            }else{
                $consommations = $this->getConsomations($famille->ref_fam,$structure->code_structure,$periode_debut,$periode_fin);
                $titre = $titre.'_'.$famille->ref_fam.'_'.$structure->code_structure.'_du_'.$periode_debut.'_au_'.$periode_fin;
            }
            $code_section = $structure->code_structure.'01';
            $section = $this->getSectionByCodeSection($code_section);
        }
        //dd($famille,$structure,$article,$periode_debut,$periode_fin,$articles);

        $structures = $this->getStructures();
        $familles = $this->getFamilles();

        $pdf = PDF::loadView('prints.requisitions.consommation',[
            'consommations'=>$consommations,
            'titre'=>$titre,
            'articles'=>$articles,
            'structures'=>$structures,
            'familles'=>$familles,
            'famille'=>$famille,
            'structure'=>$structure,
            'article'=>$article,
            'periode_debut'=>$periode_debut,
            'periode_fin'=>$periode_fin,
            'section_structure'=>$section
        ]);

        return $pdf->stream($titre.'.pdf');
    }

    public function print_recap_consommation($famille_crypt=null,$periode_debut_crypt=null,$periode_fin_crypt=null){

        $array_famille_crypts = [];
        if($famille_crypt != null){
            for ($i=0; $i < 5; $i++) { 
                if(isset(explode('@fy@',$famille_crypt)[$i])){
                    $array_famille_crypts[$i] = explode('@fy@',$famille_crypt)[$i];
                }
            }
        }

        $familles_consernees = [];
        if(count($array_famille_crypts) > 0){
            foreach ($array_famille_crypts as $key => $value) {
                try {
                    $familles_consernees[$key] = Crypt::decryptString($array_famille_crypts[$key]);
                } catch (DecryptException $e) {
                    //
                }
            }
        }
        

        $titre = "recap_consommation";

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



        $periode_debut = null;

        if($periode_debut_crypt != null){

            $periode_debut = null;
            
            try {
                $periode_debut = Crypt::decryptString($periode_debut_crypt);
            } catch (DecryptException $e) {
                //
            }


        }

        $periode_fin = null;

        if($periode_fin_crypt != null){

            $periode_fin = null;
            
            try {
                $periode_fin = Crypt::decryptString($periode_fin_crypt);
            } catch (DecryptException $e) {
                //
            }


        }
        
        $articles = [];
        if($famille != null){
            $articles = $this->getArticleByFamilleRef($famille->ref_fam);
        }

        
        $titre = $titre.'_du_'.$periode_debut.'_au_'.$periode_fin;
        
        $structures = $this->getStructures();
        $familles = $this->getFamilles();

        $pdf = PDF::loadView('prints.requisitions.recap_consommation',[
            'titre'=>$titre,
            'articles'=>$articles,
            'structures'=>$structures,
            'familles'=>$familles,
            'famille'=>$famille,
            'periode_debut'=>$periode_debut,
            'periode_fin'=>$periode_fin,
            'familles_consernees'=>$familles_consernees,
        ]);

        return $pdf->stream($titre.'.pdf');
    }

    public function printPerdiem($perdiem_param){
        
        $signatureController = new SignatureController();
        $urlPostDocument = $signatureController->urlPostDocument();
        $urlGetDocument = $signatureController->urlGetDocument();
        $apiKey = $signatureController->apiKey();
        $url_to = $signatureController->url_to();
        $headers = $signatureController->headers();
       
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($perdiem_param);
        } catch (DecryptException $e) {
            //
        }
        
        $perdiems = Perdiem::findOrFail($decrypted);

        //
        $perdiem = DB::table('perdiems as p')
            ->join('structures as s', 's.code_structure', '=', 'p.code_structure')
            ->join('gestions as g', 'g.code_gestion', '=', 'p.code_gestion')
            ->join('familles as f', 'f.ref_fam', '=', 'p.ref_fam')
            ->select('p.id as perdiems_id','p.libelle', 'p.num_pdm', 'p.code_gestion', 'p.exercice', 'p.ref_fam', 'p.code_structure','p.solde_avant_op', 'p.montant_total', 's.nom_structure', 'f.design_fam', 'p.created_at')
            ->where('p.id', $perdiems->id)
            ->first();

        $detail_perdiems = DB::table('perdiems as p')
            ->join('detail_perdiems as dp', 'dp.perdiems_id', '=', 'p.id')
            ->select('dp.nom_prenoms', 'dp.montant')
            ->where('p.id', $perdiems->id)
            ->get();

        $signataires = DB::table('signataire_perdiems as sda')
            ->join('profil_fonctions as pf','pf.id','=','sda.profil_fonctions_id')
            ->join('agents as a','a.id','=','pf.agents_id')
            ->join('fonctions as f','f.id','=','pf.fonctions_id')
            ->where('sda.perdiems_id',$perdiems->id)
            ->where('sda.flag_actif',1)
            ->select('a.nom_prenoms','a.nom_signataire','f.libelle as libelle_fonction','f.description as description_fonction','a.genres_id','a.mle')
            ->get();

        try {            
            $pdf = PDF::loadView('prints.perdiems.create',[
                'perdiem'=>$perdiem,
                'signataires'=>$signataires,
                'detail_perdiems'=>$detail_perdiems
            ])->setPaper('a4', 'landscape');

            //S'assurer que tous les signataires ont leur signature enregistrée
            
            $autorisation_de_signature = 1;
            $liste_signataire_sans_signature = null;
            foreach ($signataires as $signataire) {
                $copie = ' - Copie';
                $extention_img = '.png';
                $path_img = 'storage/emargements/m' . $signataire->mle . $copie . $extention_img;
                $public_path_img = str_replace("/","\\",public_path($path_img));

                $public_path_img_statut = @file_exists($public_path_img);

                if($public_path_img_statut === false){
                    $autorisation_de_signature = null;
                    $liste_signataire_sans_signature = $liste_signataire_sans_signature . ' - ' . $signataire->nom_prenoms;
                }
            }

            $orientation = "Paysage";
            $data = [
                'reference'=>$perdiems->num_pdm,
                'type_operations_libelle'=>'perdiems',
                'pdf'=>$pdf,
                'signataires'=>$signataires,
                'orientation'=>$orientation,
                'url_to'=>$url_to,
                'urlPostDocument'=>$urlPostDocument,
                'urlGetDocument'=>$urlGetDocument,
                'apiKey'=>$apiKey,
                'headers'=>$headers
            ];

            if($autorisation_de_signature === 1){
                $signatureController->proceduresignDocuments($data);
            }

            if($autorisation_de_signature === null){
                return redirect()->back()->with('error',"Echec de la signature du document. Veuillez contacter l'administrateur technique. Liste de signataires dont les signataires sont introuvables : " . $liste_signataire_sans_signature);
            }
            
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

}
