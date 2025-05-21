<?php

namespace App\Http\Controllers;

use DateTime;
use App\Critere;
use App\Facture;
use App\Periode;
use App\Commande;
use App\DetailFacture;
use App\DetailCotation;
use App\DetailAdjudication;
use App\DetailDemandeAchat;
use App\StatutDemandeAchat;
use App\CotationFournisseur;
use App\CritereAdjudication;
use Illuminate\Http\Request;
use App\SelectionAdjudication;
use App\TypeStatutDemandeAchat;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(SelectionAdjudication $selectionAdjudication)
    {
        // determination de l'identifiant de la demande
        $ref_fam = 0;
        $demande_achat = DB::table('selection_adjudications as sa')
        ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
        ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
        ->where('sa.id',$selectionAdjudication->id)
        ->first();
        
        if ($demande_achat!=null) {
            $ref_fam = $demande_achat->ref_fam;
        }

        $selection_adjudication = DB::table('selection_adjudications as sa')
                    ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
                    ->join('organisations as o','o.id','=','cf.organisations_id')
                    ->join('statut_organisations as so','so.organisations_id','=','o.id')
                    ->join('profils as p','p.id','=','so.profils_id')
                    ->join('users as u','u.id','=','p.users_id')
                    ->where('u.id',auth()->user()->id)
                    ->where('sa.id',$selectionAdjudication->id)
                    ->first();
        if ($selection_adjudication!=null) {
            $demande_achats_id = $selection_adjudication->demande_achats_id;
        }else{
            return redirect()->back()->with('error','Vous n\êtes pas autorisé à établir une facture définitive');
        }
        
        $fournisseur = DB::table('organisations')
                  ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                  ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                  ->join('profils','profils.id','=','statut_organisations.profils_id')
                  ->join('users','users.id','=','profils.users_id')
                  ->join('agents','agents.id','=','users.agents_id')
                  ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                  ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                  ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                  ->where('type_statut_organisations.libelle','ACTIVER')
                  ->where('critere_adjudications.demande_achats_id',$demande_achats_id)
                  ->where('users.id',auth()->user()->id)
                  ->where('criteres.libelle','Fournisseurs Cibles')
                  ->select('organisations.id as organisations_id','organisations.denomination')
                  ->first();

        if ($fournisseur===null) {
            
            

            $agents_id = auth()->user()->agents_id;
            $agent_fournisseur = DB::table('agents')
                                ->join('users','users.agents_id', '=', 'agents.id')
                                ->join('profils','profils.users_id', '=', 'users.id')
                                ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                                ->join('statut_organisations as so','so.profils_id', '=', 'profils.id')
                                ->join('organisations as o','o.id', '=', 'so.organisations_id')
                                ->where('agents.id',$agents_id)
                                ->where('type_profils.name','Fournisseur')
                                ->select('o.id','type_profils.name')
                                ->first();
            
                                
    
            if ($agent_fournisseur!=null) {
                if ($agent_fournisseur->name == "Fournisseur") {
                    $fournisseur = DB::table('preselection_soumissionnaires as ps')
                      ->join('critere_adjudications as ca','ca.id','=','ps.critere_adjudications_id')
                      ->join('criteres as c','c.id','=','ca.criteres_id')
                      ->join('demande_achats as da','da.id','=','ca.demande_achats_id')
                      ->join('familles as f','f.ref_fam','=','da.ref_fam')
                      ->join('profils as p','p.id','=','da.profils_id')
                      ->join('users as u','u.id','=','p.users_id')
                      ->join('agents as a','a.id','=','u.agents_id')
                      ->join('agent_sections as ase','ase.agents_id','=','a.id')
                      ->join('sections as s','s.id','=','ase.sections_id')
                      ->join('structures as st','st.code_structure','=','s.code_structure')
                      ->join('depots as d','d.ref_depot','=','st.ref_depot')
                      ->join('organisation_depots as od','od.ref_depot','=','d.ref_depot')
                      ->join('organisations as o','o.id','=','od.organisations_id')
                      ->join('organisation_articles as oa','oa.ref_fam','=','f.ref_fam')
                      ->join('organisation_articles as oat','oat.organisations_id','=','o.id')
                      ->join('statut_organisations as so','so.organisations_id','=','o.id')
                      ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                      ->select('o.id as organisations_id','o.denomination')
                      ->where('ps.organisations_id',NULL)
                      ->where('ca.demande_achats_id',$demande_achats_id)
                      ->where('o.id',$agent_fournisseur->id)
                      ->where('tso.libelle','ACTIVER')
                      ->first();

                      if ($fournisseur===null) {
                          return redirect()->back();
                      }
                      
                }
            }



        }
        
        $agents_id = auth()->user()->agents_id; 


        $gestions = DB::table('gestions')
                        ->get();

        $section = DB::table('sections')
                        ->join('agent_sections','agent_sections.sections_id','=','sections.id')
                        ->join('structures','structures.code_structure','=','sections.code_structure')
                        ->where('agent_sections.agents_id',$agents_id)
                        ->first();

        $credit_budgetaires = DB::table('familles')->get();

                        
                        
        
        $credit_budgetaires_select = null;

        $credit_budgetaires_select = DB::table('familles as f')
                        ->join('demande_achats as da','da.ref_fam','=','f.ref_fam')
                        ->where('da.ref_fam',$ref_fam)
                        ->first();
        $griser = 1;


        $demande_achats = DB::table('demande_achats')
        ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
        ->join('detail_cotations','detail_cotations.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
        ->join('articles','articles.ref_articles','=','detail_cotations.ref_articles')
        ->join('taxes','taxes.ref_taxe','=','articles.ref_taxe')
        ->join('familles','familles.ref_fam','=','articles.ref_fam')
        ->join('profils','profils.id','=','demande_achats.profils_id')
        ->join('users','users.id','=','profils.users_id')
        ->join('agent_sections','agent_sections.agents_id','=','users.agents_id')
        ->join('sections','sections.id','=','agent_sections.sections_id')
        ->join('structures','structures.code_structure','=','sections.code_structure')
        ->where('demande_achats.id',$demande_achats_id)
        ->where('detail_cotations.cotation_fournisseurs_id',$selectionAdjudication->cotation_fournisseurs_id)
        ->select('demande_achats.id as demande_achats_id','articles.ref_articles','articles.design_article','detail_cotations.qte','detail_cotations.prix_unit','detail_cotations.remise','detail_cotations.montant_ht','detail_cotations.montant_ttc','detail_cotations.id')
        ->get();

        

        $demande_achat_info = DB::table('demande_achats')
        ->join('detail_demande_achats','detail_demande_achats.demande_achats_id','=','demande_achats.id')
        ->join('gestions','gestions.code_gestion','=','demande_achats.code_gestion')
        ->join('articles','articles.ref_articles','=','detail_demande_achats.ref_articles')
        ->join('familles','familles.ref_fam','=','demande_achats.ref_fam')
        ->join('profils','profils.id','=','demande_achats.profils_id')
        ->join('users','users.id','=','profils.users_id')
        ->join('agent_sections','agent_sections.agents_id','=','users.agents_id')
        ->join('sections','sections.id','=','agent_sections.sections_id')
        ->join('structures','structures.code_structure','=','sections.code_structure')
        ->select('structures.nom_structure','demande_achats.created_at','demande_achats.intitule','demande_achats.num_bc','demande_achats.id','gestions.code_gestion','gestions.libelle_gestion','familles.ref_fam','familles.design_fam','demande_achats.exercice','demande_achats.ref_fam')
        ->where('demande_achats.id',$demande_achats_id)
        ->where('detail_demande_achats.flag_valide',1)
        ->first();

        $cotation_demande_achat = null;

        if ($fournisseur!=null) {
            
            
            // COTATION DEJA EFFECTUEE
            $organisations_id = $fournisseur->organisations_id;
            $cotation_fournisseurs = CotationFournisseur::where('organisations_id',$organisations_id)->where('demande_achats_id',$demande_achats_id)->first();
            

            if ($cotation_fournisseurs!=null) {
                
                $cotation_fournisseurs_id = $cotation_fournisseurs->id;

                $cotation_demande_achat = DB::table('demande_achats')
                ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
                ->join('detail_cotations','detail_cotations.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                ->join('gestions','gestions.code_gestion','=','demande_achats.code_gestion')
                ->join('articles','articles.ref_articles','=','detail_cotations.ref_articles')
                ->join('familles','familles.ref_fam','=','articles.ref_fam')
                ->join('profils','profils.id','=','demande_achats.profils_id')
                ->join('users','users.id','=','profils.users_id')
                ->join('periodes','periodes.id','=','cotation_fournisseurs.periodes_id')
                ->select('demande_achats.created_at','demande_achats.intitule','demande_achats.num_bc','demande_achats.id','gestions.code_gestion','gestions.libelle_gestion','familles.ref_fam','familles.design_fam','demande_achats.exercice','cotation_fournisseurs.organisations_id','cotation_fournisseurs.delai','cotation_fournisseurs.date_echeance','periodes.valeur','periodes.libelle_periode','cotation_fournisseurs.acompte','cotation_fournisseurs.montant_total_brut','cotation_fournisseurs.remise_generale','cotation_fournisseurs.montant_total_net','cotation_fournisseurs.tva','cotation_fournisseurs.montant_total_ttc','cotation_fournisseurs.assiete_bnc','cotation_fournisseurs.taux_bnc','cotation_fournisseurs.net_a_payer','cotation_fournisseurs.taux_acompte','cotation_fournisseurs.montant_acompte','cotation_fournisseurs.id as cotation_fournisseurs_id')
                ->where('cotation_fournisseurs.id',$cotation_fournisseurs_id)
                ->where('demande_achats.id',$demande_achats_id)
                ->first();


            }


            
        }
        


        //dd($demande_achat_info);

        $commande = DB::table('commandes')
        ->join('periodes','periodes.id','=','commandes.periodes_id')
        ->where('commandes.demande_achats_id',$demande_achats_id)
        ->first();

        $commande = DB::table('commandes')
        ->join('periodes','periodes.id','=','commandes.periodes_id')
        ->where('commandes.demande_achats_id',$demande_achats_id)
        ->first();

        $periodes = Periode::all();

        return view('factures.create',[
            'gestions'=>$gestions,
            'section'=>$section,
            'credit_budgetaires'=>$credit_budgetaires,
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'fournisseur'=>$fournisseur,
            'commande'=>$commande,
            'periodes'=>$periodes,
            'cotation_demande_achat'=>$cotation_demande_achat,
            'selection_adjudications_id'=>$selectionAdjudication->id
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
        
        $validate = $request->validate([
            'selection_adjudications_id'=>['required','numeric'],
            'demande_achats_id'=>['required','numeric'],
            'montant_total_brut'=>['required','string'],
            'montant_total_net'=>['required','string'],
            'remise_generale'=>['nullable','string'],
            'tva'=>['nullable','numeric'],
            'taux_bnc'=>['nullable','numeric'],
            'montant_bnc'=>['nullable','numeric'],
            'montant_total_ttc'=>['required','string'],
            'net_a_payer'=>['required','string'],
            'periode_id'=>['required','string'],
            'valeur'=>['required','numeric'],
            'delai'=>['nullable','numeric'],
            'date_echeance'=>['required','string'],
            'ref_articles'=>['required','array'],
            'design_article'=>['required','array'],
            'qte_demandee'=>['required','array'],
            'qte'=>['required','array'],
            'prix_unit'=>['required','array'],
            'remise'=>['required','array'],
            'montant_ht'=>['required','array'],
            'ref_articles'=>['required','array'],
            'taux_acompte'=>['nullable','string'],
            'montant_acompte'=>['nullable','string'],
        ]);


        


        
        

            $periodes_id = Periode::where('libelle_periode',$request->periode_id)->first()->id;
            
            $block_date = explode("/",$request->date_echeance);
            $d = $block_date[0];
            $m = $block_date[1];
            $Y = $block_date[2];

            $date_echeance = $Y.'-'.$m.'-'.$d;

        
        
        $user_id = auth()->user()->id; // utilisateur connecté


        $profils = DB::table('organisations')
                  ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                  ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                  ->join('profils','profils.id','=','statut_organisations.profils_id')
                  ->join('users','users.id','=','profils.users_id')
                  ->join('agents','agents.id','=','users.agents_id')
                  ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                  ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                  ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                  ->where('type_statut_organisations.libelle','ACTIVER')
                  ->where('critere_adjudications.demande_achats_id',$request->demande_achats_id)
                  ->where('users.id',auth()->user()->id)
                  ->where('criteres.libelle','Fournisseurs Cibles')
                  ->select('profils.id')
                  ->first();
        if ($profils!=null) {
            $profils_id = $profils->id;
        }else {




            $agents_id = auth()->user()->agents_id;
            $agent_fournisseur = DB::table('agents')
                                ->join('users','users.agents_id', '=', 'agents.id')
                                ->join('profils','profils.users_id', '=', 'users.id')
                                ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                                ->join('statut_organisations as so','so.profils_id', '=', 'profils.id')
                                ->join('organisations as o','o.id', '=', 'so.organisations_id')
                                ->where('agents.id',$agents_id)
                                ->where('type_profils.name','Fournisseur')
                                ->select('o.id','type_profils.name')
                                ->first();
    
            if ($agent_fournisseur!=null) {

                $profils_id = $agent_fournisseur->id;

                if ($agent_fournisseur->name == "Fournisseur") {
                    $fournisseur = DB::table('preselection_soumissionnaires as ps')
                      ->join('critere_adjudications as ca','ca.id','=','ps.critere_adjudications_id')
                      ->join('criteres as c','c.id','=','ca.criteres_id')
                      ->join('demande_achats as da','da.id','=','ca.demande_achats_id')
                      ->join('familles as f','f.ref_fam','=','da.ref_fam')
                      ->join('profils as p','p.id','=','da.profils_id')
                      ->join('users as u','u.id','=','p.users_id')
                      ->join('agents as a','a.id','=','u.agents_id')
                      ->join('agent_sections as ase','ase.agents_id','=','a.id')
                      ->join('sections as s','s.id','=','ase.sections_id')
                      ->join('structures as st','st.code_structure','=','s.code_structure')
                      ->join('depots as d','d.ref_depot','=','st.ref_depot')
                      ->join('organisation_depots as od','od.ref_depot','=','d.ref_depot')
                      ->join('organisations as o','o.id','=','od.organisations_id')
                      ->join('organisation_articles as oa','oa.ref_fam','=','f.ref_fam')
                      ->join('organisation_articles as oat','oat.organisations_id','=','o.id')
                      ->join('statut_organisations as so','so.organisations_id','=','o.id')
                      ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                      ->select('o.id as organisations_id','o.denomination')
                      ->where('ps.organisations_id',NULL)
                      ->where('ca.demande_achats_id',$request->demande_achats_id)
                      ->where('o.id',$agent_fournisseur->id)
                      ->where('tso.libelle','ACTIVER')
                      ->first();

                      if ($fournisseur===null) {
                        return redirect()->back()->with('error','Vous n\'avez pas le profile requis pour cette opération');
                      }
                      
                }
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
        

        if (count($request->ref_articles) > 0) {

            if (isset($request->acompte)) {
                $acompte = true;
            }else{
                $acompte = false;
            }

            
            foreach ($request->ref_articles as $item => $value) {

                $qte[$item] = str_replace(' ','',$request->qte[$item]);
                $prix_unit[$item] = str_replace(' ','',$request->prix_unit[$item]);
                $montant_ht[$item] = str_replace(' ','',$request->montant_ht[$item]);
                $montant_ttc[$item] = $montant_ht[$item]*$taux_tva;
                



                if (($request->ref_articles[$item] != null) and ($request->design_article[$item] != null) and ($qte[$item] != null) and ($prix_unit[$item] != null) and ($montant_ht[$item] != null) and ($montant_ttc[$item] != null) and ($qte[$item] > 0) and ($prix_unit[$item] > 0) and ($montant_ttc[$item] > 0)) {
                    // controle des quantité 
                    $detail_demande_achat = DetailDemandeAchat::where('demande_achats_id',$request->demande_achats_id)->where('ref_articles',$request->ref_articles[$item])->first();
                    if ($detail_demande_achat!=null) {
                        $qte_accordee = $detail_demande_achat->qte_accordee;
                        if ($qte[$item] > $qte_accordee) {
                            return redirect()->back()->with('error','La quantité soumise ne peut être supérieure à celle demandée');
                        }
                    }

                    
                }
            }

            

            $montant_total_brut = str_replace(' ','',$request->montant_total_brut);
            if (isset($request->remise_generale)) {
                $remise_generale = str_replace(' ','',$request->remise_generale);
            }else{
                $remise_generale = NULL;
            }
            
            $montant_total_ttc = str_replace(' ','',$request->montant_total_ttc);
            $net_a_payer = str_replace(' ','',$request->net_a_payer);
            $montant_total_net = str_replace(' ','',$request->montant_total_net);
            $montant_acompte = str_replace(' ','',$request->montant_acompte);

            if ($montant_acompte!=0) {
                $acompte = true;
            }else{
                $acompte = false;
            }

            $data_facture = [
                'selection_adjudications_id'=>$request->selection_adjudications_id,
                'organisations_id'=>$request->organisations_id,
                'demande_achats_id'=>$request->demande_achats_id,
                'acompte'=>$acompte,
                'montant_total_brut'=>$montant_total_brut,
                'remise_generale'=>$remise_generale,
                'montant_total_net'=>$montant_total_net,
                'tva'=>$request->tva,
                'montant_total_ttc'=>$montant_total_ttc,
                'assiete_bnc'=>$request->assiete_bnc,
                'taux_bnc'=>$request->taux_bnc,
                'net_a_payer'=>$net_a_payer,
                'periodes_id'=>$periodes_id,
                'delai'=>$request->delai,
                'date_echeance'=>$date_echeance, 
                'taux_acompte'=>$request->taux_acompte,
                'montant_acompte'=>$montant_acompte,
            ];

            $cotation_fournisseur = Facture::where('demande_achats_id',$request->demande_achats_id)->where('organisations_id',$request->organisations_id)->first();

            if ($cotation_fournisseur===null) {
                $factures_id = Facture::create($data_facture)->id;

            }else{
                $factures_id = $cotation_fournisseur->id;

                Facture::where('demande_achats_id',$request->demande_achats_id)->where('organisations_id',$request->organisations_id)->update($data_facture);
            }

            


            // critère adjudication
                // récupérer la date de soumission à cotation
                
            $valeur_qte = 0;
            foreach ($request->ref_articles as $item => $value) {

                $qte[$item] = str_replace(' ','',$request->qte[$item]);
                $prix_unit[$item] = str_replace(' ','',$request->prix_unit[$item]);
                $montant_ht[$item] = str_replace(' ','',$request->montant_ht[$item]);
                $montant_ttc[$item] = $montant_ht[$item]*$taux_tva;
                


                if ( ($request->ref_articles[$item] != null) AND ($request->design_article[$item] != null) AND ($qte[$item] != null) AND ($prix_unit[$item] != null) AND ($montant_ht[$item] != null) AND ($montant_ttc[$item] != null) AND ($qte[$item] > 0) AND ($prix_unit[$item] > 0) AND ($montant_ttc[$item] > 0) ) {

                    // controle des quantité 

                    // controle des quantité 
                    $detail_demande_achat = DetailDemandeAchat::where('demande_achats_id',$request->demande_achats_id)->where('ref_articles',$request->ref_articles[$item])->first();
                    if ($detail_demande_achat!=null) {
                        $qte_accordee = $detail_demande_achat->qte_accordee;
                        if ($qte[$item] == $qte_accordee) {
                            $valeur_qte++;
                        }
                        
                    }

                    $data = [
                        'factures_id'=>$factures_id,
                        'ref_articles'=>$request->ref_articles[$item],
                        'qte'=>$qte[$item],
                        'prix_unit'=>$prix_unit[$item],
                        'remise'=>$request->remise[$item],
                        'montant_ht'=>$montant_ht[$item],
                        'montant_ttc'=>$montant_ttc[$item],
                    ];
                    $detail_facture = DetailFacture::where('factures_id',$factures_id)->where('ref_articles',$request->ref_articles[$item])->first();
                    if ($detail_facture===null) {
                        $detail_facture = DetailFacture::create($data);
                    }else{
                        $detail_facture = DetailFacture::where('factures_id',$factures_id)->where('ref_articles',$request->ref_articles[$item])->update($data);
                    }
                }
            }

            // CRITERE RESPECT DES QUANTITE



            if ($detail_facture!=null) {

                if (isset($request->demande_achats_id)) {
                    // STATUT DE LA DEMANDE
                $libelle = "Facture définitive";
    
                $type_statut_demande_achat = TypeStatutDemandeAchat::where('libelle',$libelle)->first();
                    
                    if ($type_statut_demande_achat===null) {
                        $type_statut_demande_achats_id = TypeStatutDemandeAchat::create([
                            'libelle'=>$libelle
                        ])->id;
                    }else{
                        $type_statut_demande_achats_id = $type_statut_demande_achat->id;
                    }

                    $statut_demande_achats = StatutDemandeAchat::where('date_fin',NULL)->where('demande_achats_id',$request->demande_achats_id)->get();
                    foreach ($statut_demande_achats as $statut_demande_achat) {
                        StatutDemandeAchat::where('id',$statut_demande_achat->id)->update([
                            'date_fin'=>date('Y-m-d'),
                        ]);
                    }

                    StatutDemandeAchat::create([
                        'demande_achats_id'=>$request->demande_achats_id,
                        'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                        'date_debut'=>date('Y-m-d'),
                        'profils_id'=>$profils_id,
                    ]);
    
    
                // FIN STATUT DEMANDE
                }



                
                return redirect()->back()->with('success','Facture définitive enregistrée');
            }else{
                return redirect()->back()->with('error','Echec de l\'enregistrement de la facture définitive');
            }

        }

        //dd($profils_id,$request);
    }


    public function create_bc(Facture $facture)
    {

        // dernier statut de la demande
        $libelle = '';

        $statut_demande_achat = DB::table('statut_demande_achats as sda')
        ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
        ->where('sda.demande_achats_id',$facture->demande_achats_id)
        ->orderByDesc('sda.id')
        ->limit(1)
        ->first();
        if ($statut_demande_achat!=null) {
            $libelle = $statut_demande_achat->libelle;
        }
        // determiner l'exercice de la demande
        $exercice = 0;
        $demande_achat_exercice = DB::table('factures as f')
        ->join('demande_achats as da','da.id','=','f.demande_achats_id')
        ->first();
        if ($demande_achat_exercice!=null) {
            $exercice = $demande_achat_exercice->exercice;
        }
        $agents_id = auth()->user()->agents_id; 

        $profils_name = null;
        $retrait_bc = null;

        $retrait_bc = DB::table('selection_adjudications as sa')
        ->join('factures as f','f.selection_adjudications_id','=','sa.id')
        ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
        ->join('organisations as o','o.id','=','cf.organisations_id')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->where('u.id',auth()->user()->id)
        ->where('f.id',$facture->id)
        ->where('tp.name','Fournisseur')
        ->select('tp.name')
        ->first();
        if ($retrait_bc!=null) {
            $profils_name = $retrait_bc->name;
        }

        $profils_name_four = null;
        $profil_four = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->where('p.users_id',auth()->user()->id)
        ->where('tp.name','Responsable des achats')
        ->first();

        if ($profil_four!=null) {
            $factures = DB::table('selection_adjudications as sa')
            ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
            ->join('factures as f','f.selection_adjudications_id','=','sa.id')
            ->where('f.id',$facture->id)
            ->select('f.id')
            ->first();
            if ($factures!=null) {
                $profils_name_four = $profil_four->name;
            }
        }

        $profils_respo_dcg = null;

        $profil_responsable_dcg = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->where('p.users_id',auth()->user()->id)
        ->where('tp.name','Responsable contrôle budgetaire')
        ->first();

        if ($profil_responsable_dcg!=null) {
            $factures = DB::table('selection_adjudications as sa')
            ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
            ->join('factures as f','f.selection_adjudications_id','=','sa.id')
            ->where('f.id',$facture->id)
            ->select('f.id')
            ->first();
            if ($factures!=null) {
                $profils_respo_dcg = $profil_responsable_dcg->name;
            }
        }

        $profils_signataire = null;

        $profil_signataire = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->where('p.users_id',auth()->user()->id)
        ->where('tp.name','Signataire')
        ->first();

        if ($profil_signataire!=null) {
            $factures = DB::table('selection_adjudications as sa')
            ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
            ->join('factures as f','f.selection_adjudications_id','=','sa.id')
            ->where('f.id',$facture->id)
            ->select('f.id')
            ->first();
            if ($factures!=null) {
                $profils_signataire = $profil_signataire->name;
            }
        }


        $profils_respo_cmp = null;
        $profil_cmp = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->where('p.users_id',auth()->user()->id)
        ->where('tp.name','Responsable DMP')
        ->first();

        if ($profil_cmp!=null) {
            $factures = DB::table('selection_adjudications as sa')
            ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
            ->join('factures as f','f.selection_adjudications_id','=','sa.id')
            ->where('f.id',$facture->id)
            ->select('f.id')
            ->first();
            if ($factures!=null) {
                $profils_respo_cmp = $profil_cmp->name;
            }
        }

                        
                        
        $credit_budgetaires_select = null;
        $griser = 1;

        $demande_achats = DB::table('demande_achats')
        ->join('factures','factures.demande_achats_id','=','demande_achats.id')
        ->join('detail_factures','detail_factures.factures_id','=','factures.id')
        ->join('articles','articles.ref_articles','=','detail_factures.ref_articles')
        ->join('taxes','taxes.ref_taxe','=','articles.ref_taxe')
        ->join('familles','familles.ref_fam','=','articles.ref_fam')
        ->join('profils','profils.id','=','demande_achats.profils_id')
        ->join('users','users.id','=','profils.users_id')
        ->join('agent_sections','agent_sections.agents_id','=','users.agents_id')
        ->join('sections','sections.id','=','agent_sections.sections_id')
        ->join('structures','structures.code_structure','=','sections.code_structure')
        ->where('demande_achats.id',$facture->demande_achats_id)
        ->where('detail_factures.factures_id',$facture->id)
        ->where('agent_sections.exercice',$exercice)
        ->get();
        

        #dd($demande_achats);

        $demande_achat_info = DB::table('demande_achats')
                ->join('detail_demande_achats','detail_demande_achats.demande_achats_id','=','demande_achats.id')
                ->join('gestions','gestions.code_gestion','=','demande_achats.code_gestion')
                ->join('articles','articles.ref_articles','=','detail_demande_achats.ref_articles')
                ->join('familles','familles.ref_fam','=','articles.ref_fam')
                ->join('profils','profils.id','=','demande_achats.profils_id')
                ->join('users','users.id','=','profils.users_id')
                ->join('agents','agents.id','=','users.agents_id')
                ->join('agent_sections','agent_sections.agents_id','=','agents.id')
                ->join('sections','sections.id','=','agent_sections.sections_id')
                ->join('structures','structures.code_structure','=','sections.code_structure')
                ->join('factures','factures.demande_achats_id','=','demande_achats.id')
                ->join('organisations','organisations.id','=','factures.organisations_id')
                ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                ->join('periodes','periodes.id','=','factures.periodes_id')
                ->join('detail_factures','detail_factures.factures_id','=','factures.id')
                ->join('commandes','commandes.demande_achats_id','=','demande_achats.id')
                ->select('structures.nom_structure','demande_achats.created_at','demande_achats.intitule','demande_achats.num_bc','demande_achats.id','gestions.code_gestion','gestions.libelle_gestion','familles.ref_fam','familles.design_fam','demande_achats.exercice','organisations.id as organisations_id','organisations.denomination','periodes.id as periodes_id','periodes.libelle_periode','factures.delai','factures.date_echeance','commandes.date_livraison_prevue')
                ->where('factures.id',$facture->id)
                ->where('demande_achats.id',$facture->demande_achats_id)
                ->where('agent_sections.exercice',$exercice)
                ->where('detail_demande_achats.flag_valide',1)
                ->where('type_statut_organisations.libelle','ACTIVER')
                ->first();


        //dd($demande_achat_info);

        return view('factures.create_bc',[
            'credit_budgetaires_select'=>$credit_budgetaires_select,
            'griser'=>$griser,
            'demande_achats'=>$demande_achats,
            'demande_achat_info'=>$demande_achat_info,
            'factures_id'=>$facture->id,
            'profils_name'=>$profils_name,
            'profils_name_four'=>$profils_name_four,
            'libelle'=>$libelle,
            'profils_respo_cmp'=>$profils_respo_cmp,
            'profils_respo_dcg'=>$profils_respo_dcg,
            'profils_signataire'=>$profils_signataire,
        ]);
    
    }

    public function store_bc(Request $request){

        $user_id = auth()->user()->id;
        $profils = DB::table('profils')
                      ->join('type_profils', 'type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $user_id)
                      ->whereIn('type_profils.name', ['Fournisseur','Responsable des achats','Responsable DMP','Responsable contrôle budgetaire','Signataire'])
                      ->limit(1)
                      ->select('profils.id')
                      ->get();
        foreach ($profils as $profil) {
            $profils_id = $profil->id;
        }

        if (!isset($profils_id)) {
            return redirect()->back()->with('error', 'Vous n\'avez pas le profile requis pour cette opération');
        }


        $validate = $request->validate([
            
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
            'factures_id'=>['required','numeric'],
            'intitule'=>['required','string'],
            'submit'=>['required','string'],
            
        ]);


        


        $demande_achat = DB::table('factures as f')
        ->join('selection_adjudications as sa','sa.id','=','f.selection_adjudications_id')
        ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
        ->where('f.id',$request->factures_id)
        ->first();

        if ($demande_achat!=null) {
            $demande_achats_id = $demande_achat->demande_achats_id;

            $statut_demande_achat = DB::table('statut_demande_achats as sda')
            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
            ->where('sda.demande_achats_id',$demande_achats_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
            if ($statut_demande_achat!=null) {
                $libelle = $statut_demande_achat->libelle;

                if ($request->submit == 'transfert_dcg') {
                    if ($libelle!='Facture définitive') {
                        return redirect()->back()->with('error','Impossible de transférer la facture au Responsable DCG');   
                    }
                }elseif ($request->submit == 'valider_dcg') {
                    if ($libelle!='Transféré au Responsable DCG') {
                        return redirect()->back()->with('error','Impossible de valider la disponibilité de fonds');                         
                    }
                }elseif ($request->submit == 'invalider_dcg') {
                    if ($libelle!='Transféré au Responsable DCG') {
                        return redirect()->back()->with('error','Impossible d\'invalider la disponibilité de fonds');
                    }
                }elseif ($request->submit == 'valider_signataire') {
                    if ($libelle!='Facture validée (DCG)') {
                        return redirect()->back()->with('error','Impossible de signer le bon de commande');                         
                    }
                }elseif ($request->submit == 'invalider_signataire') {
                    if ($libelle!='Facture validée (DCG)') {
                        return redirect()->back()->with('error','Impossible de signer le bon de commande');                         
                    }
                }elseif ($request->submit == 'valider_cmp') {
                    if ($libelle!='Transféré au Responsable DMP') {
                        return redirect()->back()->with('error','Impossible de valider cette facture');   
                    }
                }elseif ($request->submit == 'invalider_cmp') {
                    if ($libelle!='Transféré au Responsable DMP') {
                        return redirect()->back()->with('error','Impossible d\'invalider cette facture');   
                    }
                }elseif ($request->submit == 'editer') {
                    if ($libelle!='Bon de commande signé') {
                        return redirect()->back()->with('error','Edition de bon de commande impossible');   
                    }
                }elseif ($request->submit == 'retirer') {
                    if ($libelle!='Bon de commande édité') {
                        return redirect()->back()->with('error','Retrait de bon de commande impossible');   
                    }
                }
                
            }

        }else{
            if ($request->submit == 'transfert_dcg') {
                return redirect()->back()->with('error', 'Impossible de transférer la facture au Responsable DCG');
            }elseif ($request->submit == 'valider_dcg') {
                return redirect()->back()->with('error', 'Impossible de valider disponibilité de fonds');
            }elseif ($request->submit == 'invalider_dcg') {
                return redirect()->back()->with('error', 'Impossible d\'invalider disponibilité de fonds');
            }elseif ($request->submit == 'valider_signataire') {
                return redirect()->back()->with('error', 'Impossible de signer le bon de commande');
            }elseif ($request->submit == 'invalider_signataire') {
                return redirect()->back()->with('error', 'Impossible de signer le bon de commande');
            }elseif ($request->submit == 'valider_cmp') {
                return redirect()->back()->with('error', 'Impossible de valider cette facture');
            }elseif ($request->submit == 'invalider_cmp') {
                return redirect()->back()->with('error', 'Impossible d\'invalider cette facture');
            }elseif ($request->submit == 'editer') {
                return redirect()->back()->with('error', 'Edition de bon de commande impossible');
            }elseif ($request->submit == 'retirer') {
                return redirect()->back()->with('error', 'Retrait de bon de commande impossible');
            }
        }

        if (isset($demande_achats_id)) {
            // STATUT DE LA DEMANDE
            if ($request->submit == 'transfert_dcg') {
                $libelle = "Transféré au Responsable DCG";
            }elseif ($request->submit == 'valider_dcg') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "facture validée (DCG)";
            }elseif ($request->submit == 'invalider_dcg') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "facture invalidée (DCG)";
            }elseif ($request->submit == 'valider_signataire') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Bon de commande signé";
            }elseif ($request->submit == 'invalider_signataire') {

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);

                $libelle = "Bon de commande rejeté (Signataire)";
            }elseif ($request->submit == 'valider_cmp') {
                $libelle = "facture validée (CMP)";
            }elseif ($request->submit == 'invalider_cmp') {
                $libelle = "Facture invalidée (CMP)";
            }elseif ($request->submit == 'editer') {
                $libelle = "Bon de commande édité";
            }elseif ($request->submit == 'retirer') {
                $libelle = "Bon de commande retiré";

                $validate = $request->validate([
                    'date_livraison_prevue'=>['required','date'],
                ]);

            }

        $type_statut_demande_achat = TypeStatutDemandeAchat::where('libelle',$libelle)->first();
            
            if ($type_statut_demande_achat===null) {
                $type_statut_demande_achats_id = TypeStatutDemandeAchat::create([
                    'libelle'=>$libelle
                ])->id;
            }else{
                $type_statut_demande_achats_id = $type_statut_demande_achat->id;
            }

            $statut_demande_achat = StatutDemandeAchat::where('date_fin',NULL)->where('demande_achats_id',$demande_achats_id)->first();

            if ($statut_demande_achat!=null) {

                    StatutDemandeAchat::where('id',$statut_demande_achat->id)->update([
                        'date_fin'=>date('Y-m-d'),
                    ]);
                

            }
                
                StatutDemandeAchat::create([
                    'demande_achats_id'=>$demande_achats_id,
                    'type_statut_demande_achats_id'=>$type_statut_demande_achats_id,
                    'date_debut'=>date('Y-m-d'),
                    'profils_id'=>$profils_id,
                    'commentaire'=>trim($request->commentaire),
                ]);

                if (isset($request->date_livraison_prevue)) {
                    Commande::where('demande_achats_id',$demande_achats_id)->update([
                        'date_livraison_prevue'=>$request->date_livraison_prevue
                    ]);
                }
            


        // FIN STATUT DEMANDE

            return redirect('demande_achats/index')->with('success',$libelle);
        }else{
            if ($request->submit == 'transfert_dcg') {
                $libelle = "Echec du transfert de la facture au Responsable DCG";
            }elseif ($request->submit == 'valider_cmp') {
                $libelle = "Echec de la validation de la facture";
            }elseif ($request->submit == 'valider_dcg') {
                $libelle = "Echec de la validation de la facture";
            }elseif ($request->submit == 'invalider_dcg') {
                $libelle = "Echec de l\'invalidation de la facture";
            }elseif ($request->submit == 'valider_signataire') {
                $libelle = "Echec de la signature du bon de commande";
            }elseif ($request->submit == 'invalider_signataire') {
                $libelle = "Echec de la signature du bon de commande";
            }elseif ($request->submit == 'invalider_cmp') {
                $libelle = "Echec de l'invalidation de la facture";
            }elseif ($request->submit == 'editer') {
                $libelle = "Echec de l'édition du bon de commande";
            }elseif ($request->submit == 'retirer') {
                $libelle = "Echec du retrait du bon de commande";
            }
            return redirect()->back()->with('error',$libelle);
        }





    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Facture  $facture
     * @return \Illuminate\Http\Response
     */
    public function show(Facture $facture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Facture  $facture
     * @return \Illuminate\Http\Response
     */
    public function edit(Facture $facture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Facture  $facture
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facture $facture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Facture  $facture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facture $facture)
    {
        //
    }
}
