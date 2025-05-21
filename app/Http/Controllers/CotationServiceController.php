<?php

namespace App\Http\Controllers;

use App\Models\Taxe;
use App\Models\Unite;
use App\Models\Periode;
use App\Models\Service;
use App\Models\DemandeFond;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\CotationService;
use Illuminate\Support\Facades\DB;
use App\Models\DetailCotationService;
use App\Models\StatutCotationService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\TypeStatutCotationService;
use Illuminate\Contracts\Encryption\DecryptException;

class CotationServiceController extends Controller
{
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
    public function create($demandeFond)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeFond);
        } catch (DecryptException $e) {
            //
        }

        $demandeFond = DemandeFond::findOrFail($decrypted);

        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $demande_fond = $this->getDemandeFond($demandeFond->id);

        if ($demande_fond===null) {
            return redirect()->back()->with('error','Demande de fonds indisponible');
        }

        $ref_depot = $demande_fond->ref_depot;
        $ref_fam = $demande_fond->ref_fam;
        $design_fam = $demande_fond->design_fam;
        $exercice = $demande_fond->exercice;
        $code_structure = $demande_fond->code_structure;

        $etape = "create";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$demande_fond);

       
        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }

        $periodes = Periode::all();

        $taxes = Taxe::all();        
        
        $organisations = $this->getOrganisationsDFCS($ref_fam,$ref_depot);     
        
        if (count($organisations) === 0) {
            return redirect()->back()->with('error', 'Veuillez demander à l\'administrateur fonctionnel de rattacher à votre dépôt au moins un fournisseur au compte : "'.$design_fam.'"');
        }

        $cotation_service = $this->getCotationService($demandeFond->id);
        

        //générer le N° Bon de commande
        $sequence_id = count(CotationService::join('demande_fonds as df','df.id','=','cotation_services.demande_fonds_id')
        ->join('sections as s','s.code_section','=','df.code_section')
        ->where('df.exercice',$exercice)
        ->where('s.code_structure',$code_structure)
        ->get()) + 1;

        $sequence_id = str_pad($sequence_id, 2, "0", STR_PAD_LEFT);

        $num_bc = date("y").'/'.$code_structure.'BCDF'.$sequence_id;

        $services = $this->getServices($ref_fam);

        $unites = $this->getUnites();

        // dd($demande_fond);
        
        return view('cotation_services.create',[
            'periodes'=>$periodes,
            'taxes'=>$taxes,
            'cotation_service'=>$cotation_service,
            'demande_fond'=>$demande_fond,
            'organisations'=>$organisations,
            'num_bc'=>$num_bc,
            'services'=>$services,
            'unites'=>$unites
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
            
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $demande_fond = $this->getDemandeFond($request->demande_fonds_id);

        if ($demande_fond===null) {
            return redirect()->back()->with('error','Demande de fonds indisponible');
        }

        $exercice = $demande_fond->exercice;
        $code_structure = $demande_fond->code_structure;

        $etape = "store";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$demande_fond);
        
        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }else{
            $profils_id = $profils->id;
        }
        
        $this->validate_request($request);
        
        
        $error = $this->validate_saisie($request);
        
        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_brut = filter_var($request->montant_total_brut, FILTER_SANITIZE_NUMBER_INT);
        
        $info = 'Montant total brut';
        $error = $this->setInt($montant_total_brut,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $remise_generale = filter_var($request->remise_generale, FILTER_SANITIZE_NUMBER_INT);

        $info = 'Remise générale';
        $error = $this->setInt($remise_generale,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_net = filter_var($request->montant_total_net, FILTER_SANITIZE_NUMBER_INT);
            
        $info = 'Montant total net';
        $error = $this->setInt($montant_total_net,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }

        $montant_total_ttc = filter_var($request->montant_total_ttc, FILTER_SANITIZE_NUMBER_INT);
            
        $info = 'Montant total ttc';
        $error = $this->setInt($montant_total_ttc,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }


        $net_a_payer = filter_var($request->net_a_payer, FILTER_SANITIZE_NUMBER_INT);
               
        $info = 'Net à payer';
        $error = $this->setInt($net_a_payer,$info);

        if (isset($error)) {
            return redirect()->back()->with('error',$error);
        }


        $block = explode('/',$request->date_echeance);
        $jour = $block[0];
        $mois = $block[1];
        $annee = $block[2];

        $date_echeance = $annee.'-'.$mois.'-'.$jour;

        $periode = Periode::where('libelle_periode',$request->periodes_id)->first();

        if ($periode!=null) {
            $periodes_id = $periode->id;
        }else{
            return redirect()->back()->with('error','Echéance incorrecte');
        }




        //générer le N° Bon de commande
        $sequence_id = count(CotationService::join('demande_fonds as df','df.id','=','cotation_services.demande_fonds_id')
        ->join('sections as s','s.code_section','=','df.code_section')
        ->where('df.exercice',$exercice)
        ->where('s.code_structure',$code_structure)
        ->get()) + 1;

        $sequence_id = str_pad($sequence_id, 2, "0", STR_PAD_LEFT);

        $num_bc = date("y").'/'.$code_structure.'BCDF'.$sequence_id;

        if (isset($request->acompte)) {
            $taux_acompte = $request->taux_acompte;

            $montant_acompte = filter_var($request->montant_acompte, FILTER_SANITIZE_NUMBER_INT);

            $info = 'Montant acompte';
            $error = $this->setInt($montant_acompte,$info);

            if (isset($error)) {
                return redirect()->back()->with('error',$error);
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

        $organisations_id = $this->getOrganisation($request->entnum);

        if ($organisations_id != null) {
            $data = [
                'num_bc'=>$num_bc,
                'organisations_id'=>$organisations_id,
                'demande_fonds_id'=>$request->demande_fonds_id,
                'montant_total_brut'=>$montant_total_brut,
                'remise_generale'=>$remise_generale,
                'montant_total_net'=>$montant_total_net,
                'tva'=>$request->tva,
                'montant_total_ttc'=>$montant_total_ttc,
                'net_a_payer'=>$net_a_payer,
                'acompte'=>$acompte,
                'taux_acompte'=>$taux_acompte,
                'montant_acompte'=>$montant_acompte,
                'delai'=>$request->delai,
                'periodes_id'=>$periodes_id,
                'date_echeance'=>$date_echeance,
                'date_retrait'=>$request->date_retrait,
                'date_livraison_prevue'=>$request->date_livraison_prevue,
            ];
        }else {
            return redirect()->back()->with('error','N° CNPS incorrect');
        }
        

        $cotation_services_id = CotationService::create($data)->id;

        
        $this->storeDetailCotationService($request,$cotation_services_id);

        

        if ($cotation_services_id!=null) {

            //statut bon de commande

            $libelle = 'Soumis pour validation';

            $this->storeStatutCotationService($libelle,$cotation_services_id,$profils_id);
            
            $demande_fonds_id = Crypt::encryptString($request->demande_fonds_id);
            return redirect('/demande_fonds/edit/'.$demande_fonds_id)->with('success','Bon de commande enregistré');
        }else{
            return redirect()->back()->with('error','Echec de l\'enregistrement');
        }

        


        

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CotationService  $cotationService
     * @return \Illuminate\Http\Response
     */
    public function show(CotationService $cotationService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CotationService  $cotationService
     * @return \Illuminate\Http\Response
     */
    public function edit($demandeFond)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($demandeFond);
        } catch (DecryptException $e) {
            //
        }

        $demandeFond = DemandeFond::findOrFail($decrypted);


        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $demande_fond = $this->getDemandeFond($demandeFond->id);

        if ($demande_fond===null) {
            return redirect()->back()->with('error','Demande de fonds indisponible');
        }

        $ref_depot = $demande_fond->ref_depot;
        $ref_fam = $demande_fond->ref_fam;
        $design_fam = $demande_fond->design_fam;

        $etape = "edit";
        $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$demande_fond);

        if($profils===null){
            return redirect()->back()->with('error','Accès refusé');
        }
        

        $periodes = Periode::all();

        $taxes = Taxe::all();

        $organisations = $this->getOrganisationsDFCS($ref_fam,$ref_depot);     
        
        if (count($organisations) === 0) {
            return redirect()->back()->with('error', 'Veuillez demander à l\'administrateur fonctionnel de rattacher à votre dépôt au moins un fournisseur au compte : "'.$design_fam.'"');
        }

        $cotation_service = $this->getCotationService($demandeFond->id);
        
        $detail_cotation_services = $this->getDetailCotationService($demandeFond->id);

        $statut_cotation_service = $this->getStatutCotationService($demandeFond->id);

        $entete = 'Bon de commande';
        $bouton = null;
        $value_bouton = null;
        $visa = 1;
        if ($statut_cotation_service!=null) {
            if ($type_profils_name === 'Gestionnaire des achats') {
                if ($statut_cotation_service->libelle != 'Soumis pour validation') {
                    return redirect()->back()->with('error','Accès refusé');
                }

                if ($statut_cotation_service->libelle === 'Soumis pour validation') {
                    $entete = 'Modification du bon de commande';
                    $bouton = 'Modifier';
                    $value_bouton = 'modifier';
                    $visa = null;
                }
            }
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        

        $statut_demande_fond = $this->getStatutDemandeFond($demandeFond->id);

        $services = $this->getServices($ref_fam);
        
        $visualiser = 1;
        
        
        if($statut_demande_fond->libelle === 'Imputé (Gestionnaire des achats)' OR $statut_demande_fond->libelle === 'Édité (Gestionnaire des achats)' OR $statut_demande_fond->libelle === 'Validé'){
            $visualiser = null;
            

            if($type_profils_name != 'Gestionnaire des achats'){
                $visualiser = 1;
            }
        }

        
        $unites = $this->getUnites();

        return view('cotation_services.edit',[
            'periodes'=>$periodes,
            'taxes'=>$taxes,
            'cotation_service'=>$cotation_service,
            'demande_fond'=>$demande_fond,
            'organisations'=>$organisations,
            'detail_cotation_services'=>$detail_cotation_services,
            'statut_cotation_service'=>$statut_cotation_service,
            'statut_demande_fond'=>$statut_demande_fond,
            'services'=>$services,
            'entete'=>$entete,
            'bouton'=>$bouton,
            'value_bouton'=>$value_bouton,
            'visa'=>$visa,
            'visualiser'=>$visualiser,
            'unites'=>$unites
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CotationService  $cotationService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CotationService $cotationService)
    {
        if (Session::has('profils_id')) {
            # code...
        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $type_profils_name = $this->getTypeProfilName(Session::get('profils_id'));

        $demande_fond = $this->getDemandeFond($request->demande_fonds_id);

        if ($demande_fond===null) {
            return redirect()->back()->with('error','Demande de fonds indisponible');
        }

        $exercice = $demande_fond->exercice;
        $code_structure = $demande_fond->code_structure;

        
        
        // determination du dépôt de l'agent connecté
        $users_id = auth()->user()->id; // utilisateur connecté

        if (isset($request->submit)) {

            if ($request->submit === 'modifier') {
                
                $etape = "modifier";
                $profils = $this->controlAcces($type_profils_name,$etape,auth()->user()->id,$demande_fond);
                
                if($profils===null){
                    return redirect()->back()->with('error','Accès refusé');
                }else{
                    $profils_id = $profils->id;
                }

                $this->validate_request($request);
        
                $error = $this->validate_saisie($request);
                
                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }
                

                $montant_total_brut = filter_var($request->montant_total_brut, FILTER_SANITIZE_NUMBER_INT);
        
                $info = 'Montant total brut';
                $error = $this->setInt($montant_total_brut,$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }

                $remise_generale = filter_var($request->remise_generale, FILTER_SANITIZE_NUMBER_INT);

                $info = 'Remise générale';
                $error = $this->setInt($remise_generale,$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }

                $montant_total_net = filter_var($request->montant_total_net, FILTER_SANITIZE_NUMBER_INT);
                    
                $info = 'Montant total net';
                $error = $this->setInt($montant_total_net,$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }

                $montant_total_ttc = filter_var($request->montant_total_ttc, FILTER_SANITIZE_NUMBER_INT);
                    
                $info = 'Montant total ttc';
                $error = $this->setInt($montant_total_ttc,$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }


                $net_a_payer = filter_var($request->net_a_payer, FILTER_SANITIZE_NUMBER_INT);
                    
                $info = 'Net à payer';
                $error = $this->setInt($net_a_payer,$info);

                if (isset($error)) {
                    return redirect()->back()->with('error',$error);
                }

                $block = explode('/',$request->date_echeance);
                $jour = $block[0];
                $mois = $block[1];
                $annee = $block[2];

                $date_echeance = $annee.'-'.$mois.'-'.$jour;

                $periode = Periode::where('libelle_periode',$request->periodes_id)->first();

                if ($periode!=null) {
                    $periodes_id = $periode->id;
                }else{
                    return redirect()->back()->with('error','Echéance incorrecte');
                }

                //générer le N° Bon de commande

                if (isset($request->acompte)) {
                    $taux_acompte = $request->taux_acompte;
        
                    $montant_acompte = filter_var($request->montant_acompte, FILTER_SANITIZE_NUMBER_INT);
        
                    $info = 'Montant acompte';
                    $error = $this->setInt($montant_acompte,$info);
        
                    if (isset($error)) {
                        return redirect()->back()->with('error',$error);
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


                $organisations_id = $this->getOrganisation($request->entnum);

                if ($organisations_id != null) {
                    $data = [
                        'num_bc'=>$request->num_bc,
                        'organisations_id'=>$organisations_id,
                        'demande_fonds_id'=>$request->demande_fonds_id,
                        'montant_total_brut'=>$montant_total_brut,
                        'remise_generale'=>$remise_generale,
                        'montant_total_net'=>$montant_total_net,
                        'tva'=>$request->tva,
                        'montant_total_ttc'=>$montant_total_ttc,
                        'net_a_payer'=>$net_a_payer,
                        'acompte'=>$acompte,
                        'taux_acompte'=>$taux_acompte,
                        'montant_acompte'=>$montant_acompte,
                        'delai'=>$request->delai,
                        'periodes_id'=>$periodes_id,
                        'date_echeance'=>$date_echeance,
                        'date_retrait'=>$request->date_retrait,
                        'date_livraison_prevue'=>$request->date_livraison_prevue,
                    ];
                }else {
                    return redirect()->back()->with('error','N° CNPS incorrect');
                }

                
                
                

                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                    CotationService::where('id',$cotation_services_id)->update($data);

                    DetailCotationService::where('cotation_services_id',$cotation_services_id)->delete();


                }

                
                $this->storeDetailCotationService($request,$cotation_services_id);

                

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Soumis pour validation';

                    $this->storeStatutCotationService($libelle,$cotation_services_id,$profils_id);

                    $demande_fonds_id = Crypt::encryptString($request->demande_fonds_id);

                    return redirect('/demande_fonds/edit/'.$demande_fonds_id)->with('success','Modification enregistrée');
                }else{
                    return redirect()->back()->with('error','Echec de la modification');
                }



            }

            if ($request->submit === 'visa_r_achat') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Responsable des achats']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Responsable des achats)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_r_cmp') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Responsable DMP']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Responsable DMP)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_r_cb') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Responsable contrôle budgetaire']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Responsable Contrôle Budgétaire)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_d_cb') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Chef Département DCG']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Chef Département Contrôle Budgétaire)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_r_dcg') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Responsable DCG']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Responsable DCG)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_r_dgaaf') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Directeur Général Adjoint']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Directeur Général Adjoint)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_r_dg') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Directeur Général']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Directeur Général)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'visa_r_dfc') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Responsable DFC']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Visé (Responsable DFC)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande visé');

                }else{
                    return redirect()->back()->with('error','Validation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'annuler_g_achat') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des achats']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                    CotationService::where('id',$cotation_services_id)->update([
                        'flag_actif'=>null
                    ]);
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Annulé (Gestionnaire des achats)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande annulé');

                }else{
                    return redirect()->back()->with('error','Annulation du bon de commande échouée');
                }




                
            }

            if ($request->submit === 'confirmer_r_dfc') {

                $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', $users_id)
                      ->whereIn('type_profils.name', (['Gestionnaire des achats']))
                      ->limit(1)
                      ->select('profils.id','type_profils.name')
                      ->first();
                if($profil===null){
                    return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                }else{
                    $profils_id = $profil->id;
                }

                $validate = $request->validate([
                    'commentaire'=>['required','string'],
                ]);


                $cotation_services_id = null;

                $cotation_service = CotationService::where('num_bc',$request->num_bc)->first();
                if ($cotation_service!=null) {
                    $cotation_services_id = $cotation_service->id;
                    CotationService::where('id',$cotation_services_id)->update([
                        'flag_actif'=>1
                    ]);
                }

                if ($cotation_services_id!=null) {

                    //statut bon de commande
                    $libelle = 'Confirmé (Gestionnaire des achats)';

                    $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                        
                    if ($type_statut_cotation_service===null) {
                        $type_statut_cot_services_id = TypeStatutCotationService::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_cot_services_id = $type_statut_cotation_service->id;
                    }

                    if (isset($type_statut_cot_services_id)) {


                        $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                        ->orderByDesc('id')
                        ->limit(1)
                        ->first();

                        if ($statut_cotation_service!=null) {
                                StatutCotationService::where('id',$statut_cotation_service->id)->update([
                                    'date_fin'=>date('Y-m-d'),
                                ]);
                        }



                        StatutCotationService::create([
                            'profils_id'=>$profils_id,
                            'cotation_services_id'=>$cotation_services_id,
                            'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                            'date_debut'=>date('Y-m-d'),
                            'date_fin'=>date('Y-m-d'),
                            'commentaire'=>$request->commentaire,
                        ]);

                    }

                    return redirect('/demande_fonds/send/'.$request->demande_fonds_id)->with('success','Bon de commande confirmé');

                }else{
                    return redirect()->back()->with('error','Confirmation du bon de commande échouée');
                }




                
            }


        }

        

        


        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CotationService  $cotationService
     * @return \Illuminate\Http\Response
     */
    public function destroy(CotationService $cotationService)
    {
        // 
    }

    public function getTypeProfilName($profils_id){

        $type_profils_name = null;

        $type_profil = DB::table('type_profils as tp')
        ->join('profils as p', 'p.type_profils_id', '=', 'tp.id')
        ->where('p.id',$profils_id)
        ->first();

        if ($type_profil!=null) {
            $type_profils_name = $type_profil->name;
        }

        return $type_profils_name;
    }

    public function getDemandeFond($demande_fonds_id){

        $demande_fond = DB::table('demande_fonds as df')
        ->where('df.id',$demande_fonds_id)
        ->join('familles as f','f.ref_fam','=','df.ref_fam')
        ->join('profils as p','p.id','=','df.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->join('sections as s','s.code_section','=','df.code_section')
        ->join('structures as st','st.code_structure','=','s.code_structure')
        ->select('st.*','s.*','a.*','u.*','p.*','f.*','df.*')
        ->first();

        return $demande_fond;
    }

    public function getOrganisationsDFCS($ref_fam,$ref_depot){
            
        $organisations = DB::table('organisations as o')
        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->join('organisation_articles as oa','oa.organisations_id','=','o.id')
        ->join('organisation_depots as od','od.organisations_id','=','o.id')
        ->where('tso.libelle','Activé')
        ->where('oa.flag_actif',1)
        ->where('od.flag_actif',1)
        ->where('oa.ref_fam',$ref_fam)
        ->where('od.ref_depot',$ref_depot)
        ->select('o.id','o.entnum','o.denomination')
        ->get();
        return $organisations;
    }

    public function getCotationService($demande_fonds_id){

        $cotation_service = CotationService::where('demande_fonds_id',$demande_fonds_id)
        ->join('demande_fonds as df','df.id','=','cotation_services.demande_fonds_id')
        ->join('periodes as p','p.id','=','cotation_services.periodes_id')
        ->join('organisations as o','o.id','=','cotation_services.organisations_id')
        ->select('df.*','p.*','o.*','cotation_services.*')
        ->first();

        return $cotation_service;
    }

    public function getServices($ref_fam){
        $services = DB::table('services as s')
        ->join('detail_cotation_services as dcs','dcs.services_id','=','s.id')
        ->join('cotation_services as cs','cs.id','=','dcs.cotation_services_id')
        ->join('demande_fonds as df','df.id','=','cs.demande_fonds_id')
        ->where('df.ref_fam',$ref_fam)
        ->select('s.libelle')
        ->distinct('s.libelle')
        ->get();
        return $services;
    }

    public function controlAcces($type_profils_name,$etape,$users_id,$request=null){

        $profils = null;
        if ($etape === "create" or $etape === "store") {

            $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', $users_id)
                ->whereIn('tp.name', ['Gestionnaire des achats'])
                ->limit(1)
                ->select('p.id', 'se.code_section')
                ->where('p.flag_actif',1)
                ->where('p.flag_actif',1)
                ->where('u.id',auth()->user()->id)
                ->where('tsase.libelle','Activé')
                ->whereIn('s.ref_depot',function($query) use($request){
                    $query->select(DB::raw('sst.ref_depot'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->join('structures as sst','sst.code_structure','=','ss.code_structure')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('sst.ref_depot = s.ref_depot');
                })
                ->first();
                
        }elseif ($etape === "edit" or $etape === "modifier") {

            if ($type_profils_name === 'Agent Cnps') {
                $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->whereIn('tp.name', ['Agent Cnps'])
                ->limit(1)
                ->select('p.id', 'se.code_section')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.code_structure',function($query) use($request){
                    $query->select(DB::raw('ss.code_structure'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('ss.code_structure = s.code_structure');
                })
                ->whereIn('a.id',function($query) use($request){
                    $query->select(DB::raw('df.agents_id'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('a.id = df.agents_id');
                })
                ->first();

            }else{
                $profils = DB::table('profils as p')
                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                ->join('users as u', 'u.id', '=', 'p.users_id')
                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                ->join('statut_agent_sections as sase', 'sase.agent_sections_id', '=', 'ase.id')
                ->join('type_statut_agent_sections as tsase', 'tsase.id', '=', 'sase.type_statut_agent_sections_id')
                ->join('sections as se', 'se.id', '=', 'ase.sections_id')
                ->join('structures as s', 's.code_structure', '=', 'se.code_structure')
                ->where('p.users_id', auth()->user()->id)
                ->whereIn('tp.name', ['Pilote AEE','Secrétaire DMP','Responsable DMP','Responsable des achats','Gestionnaire des achats','Responsable contrôle budgetaire','Chef Département DCG','Responsable DCG','Secrétaire DCG','Responsable DFC','Directeur Général Adjoint','Directeur Général','Secrétaire DFC','Gestionnaire des achats'])
                ->limit(1)
                ->select('p.id', 'se.code_section')
                ->where('p.flag_actif',1)
                ->where('p.id',Session::get('profils_id'))
                ->where('tsase.libelle','Activé')
                ->whereIn('s.ref_depot',function($query) use($request){
                    $query->select(DB::raw('sst.ref_depot'))
                          ->from('demande_fonds as df')
                          ->join('sections as ss','ss.code_section','=','df.code_section')
                          ->join('structures as sst','sst.code_structure','=','ss.code_structure')
                          ->where('df.num_dem',$request->num_dem)
                          ->whereRaw('sst.ref_depot = s.ref_depot');
                })
                ->first();
            }
            
        }

        return $profils;
    }

    public function validate_request($request){

        if (isset($request->submit)) {
            if ($request->submit === 'modifier') {
                $validate = $request->validate([
                    'demande_fonds_id'=>['required','string'],
                    'num_bc'=>['required','string'],
                    'intitule'=>['required','string'],
                    'periodes_id'=>['required','string'],
                    'valeur'=>['required','string'],
                    'delai'=>['required','string'],
                    'date_echeance'=>['required','string'],
                    'ref_fam'=>['required','string'],
                    'entnum'=>['required','string'],
                    'denomination'=>['required','string'],
                    'libelle_service'=>['required','array'],
                    'qte'=>['required','array'],
                    'prix_unit'=>['required','array'],
                    'remise'=>['nullable','array'],
                    'montant_ht'=>['required','array'],
                    'montant_total_brut'=>['required','string'],
                    'remise_generale'=>['required','string'],
                    'montant_total_net'=>['required','string'],
                    'tva'=>['nullable','string'],
                    'montant_tva'=>['nullable','string'],
                    'montant_total_ttc'=>['required','string'],
                    'net_a_payer'=>['required','string'],
                    'taux_acompte'=>['nullable','string'],
                    'montant_acompte'=>['nullable','string'],
                    'submit'=>['required','string'],
                ]);
            }
        }else {
            $validate = $request->validate([
                'demande_fonds_id'=>['required','string'],
                'num_bc'=>['required','string'],
                'intitule'=>['required','string'],
                'periodes_id'=>['required','string'],
                'valeur'=>['required','string'],
                'delai'=>['required','string'],
                'date_echeance'=>['required','string'],
                'ref_fam'=>['required','string'],
                'entnum'=>['required','string'],
                'denomination'=>['required','string'],
                'libelle_service'=>['required','array'],
                'unite'=>['required','array'],
                'qte'=>['required','array'],
                'prix_unit'=>['required','array'],
                'remise'=>['nullable','array'],
                'montant_ht'=>['required','array'],
                'montant_total_brut'=>['required','string'],
                'remise_generale'=>['required','string'],
                'montant_total_net'=>['required','string'],
                'tva'=>['nullable','string'],
                'montant_tva'=>['nullable','string'],
                'montant_total_ttc'=>['required','string'],
                'net_a_payer'=>['required','string'],
                'taux_acompte'=>['nullable','string'],
                'montant_acompte'=>['nullable','string'],
            ]);
        }
        

        
    }

    public function validate_saisie($request){
        $error = null;
        if(isset($request->libelle_service)){
            if (count($request->libelle_service) > 0) {
                foreach ($request->libelle_service as $item => $value) {
                    if (isset($request->libelle_service[$item]) && isset($request->qte[$item]) && isset($request->prix_unit[$item]) && isset($request->remise[$item]) && isset($request->montant_ht[$item])) {
                        
                        $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $qte[$item] = $qte[$item] * 1;
                        } catch (\Throwable $th) {
                            $error = "Quantité : Une valeur non numérique rencontrée.";
                        }
                        
                        if (gettype($qte[$item])!='integer') {
                            $error = "Quantité : Une valeur non numérique rencontrée.";
                        }

                        if ($qte[$item] <= 0) {
                            $error = "Veuillez saisir une quantité valide";
                        }

                        
                        $prix_unit[$item] = filter_var($request->prix_unit[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $prix_unit[$item] = $prix_unit[$item] * 1;
                        } catch (\Throwable $th) {
                            $error = "Prix unitaire : Une valeur non numérique rencontrée.";
                        }
                        
                        if (gettype($prix_unit[$item])!='integer') {
                            $error = "Prix unitaire : Une valeur non numérique rencontrée.";

                        }

                        if ($prix_unit[$item] <= 0) {
                            $error = "Veuillez saisir un prix unitaire valide";
                        }

                        if (isset($request->montant_ht[$item])) {

                            $remise[$item] = filter_var($request->remise[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                            try {
                                $remise[$item] = $remise[$item] * 1;
                            } catch (\Throwable $th) {
                                $error = "Remise : Une valeur non numérique rencontrée.";
                            }
                        
                            if (gettype($remise[$item])!='integer') {
                                $error = "Remise : Une valeur non numérique rencontrée.";
                            }

                            if ($remise[$item] < 0) {
                                $error = "Veuillez saisir un remise valide";

                            }
                        }


                        $montant_ht[$item] = filter_var($request->montant_ht[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $montant_ht[$item] = $montant_ht[$item] * 1;
                        } catch (\Throwable $th) {
                            $error = "Montant ht : Une valeur non numérique rencontrée.";
                        }
                        
                        if (gettype($montant_ht[$item])!='integer') {
                            $error = "Montant ht : Une valeur non numérique rencontrée.";

                        }

                        



                    }else{
                        $error = "Veuillez saisir l'ensemble des champs pour chaque service";

                    }
                }
            }
        }else{
            $error = "Veuillez saisir au moins une ligne de devis";
        }

        return $error;
    }

    public function setInt($saisie,$info){
        $error = null;

        try {
            $saisie = $saisie * 1;
        } catch (\Throwable $th) {
            $error = $info." : Une valeur non numérique rencontrée.";
        }
        
        if (gettype($saisie)!='integer') {
            $error = $info." : Une valeur non numérique rencontrée.";
        }

        return $error;
    }

    public function storeDetailCotationService($request,$cotation_services_id){
        if(isset($request->libelle_service)){
            if (count($request->libelle_service) > 0) {
                foreach ($request->libelle_service as $item => $value) {
                    if (isset($request->libelle_service[$item]) && isset($request->qte[$item]) && isset($request->prix_unit[$item]) && isset($request->remise[$item]) && isset($request->montant_ht[$item])) {
                        
                        $qte[$item] = filter_var($request->qte[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $qte[$item] = $qte[$item] * 1;
                        } catch (\Throwable $th) {
                            return redirect()->back()->with('error','Quantité : Une valeur non numérique rencontrée.');
                        }
                        
                        if (gettype($qte[$item])!='integer') {
                            return redirect()->back()->with('error','Quantité : Une valeur non numérique rencontrée.');
                        }

                        if ($qte[$item] <= 0) {
                            return redirect()->back()->with('error','Veuillez saisir une quantité valide');
                        }

                        
                        $prix_unit[$item] = filter_var($request->prix_unit[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $prix_unit[$item] = $prix_unit[$item] * 1;
                        } catch (\Throwable $th) {
                            return redirect()->back()->with('error','Prix unitaire : Une valeur non numérique rencontrée.');
                        }
                        
                        if (gettype($prix_unit[$item])!='integer') {
                            return redirect()->back()->with('error','Prix unitaire : Une valeur non numérique rencontrée.');
                        }

                        if ($prix_unit[$item] <= 0) {
                            return redirect()->back()->with('error','Veuillez saisir un prix unitaire valide');
                        }
                        
                        if (isset($request->montant_ht[$item])) {

                            $remise[$item] = filter_var($request->remise[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                            try {
                                $remise[$item] = $remise[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error', 'Remise : Une valeur non numérique rencontrée.');
                            }
                        
                            if (gettype($remise[$item])!='integer') {
                                return redirect()->back()->with('error', 'Remise : Une valeur non numérique rencontrée.');
                            }

                            if ($remise[$item] < 0) {
                                return redirect()->back()->with('error', 'Veuillez saisir un remise valide');
                            }
                        }else{
                            $remise[$item] = 0;
                        }


                        $montant_ht[$item] = filter_var($request->montant_ht[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                        try {
                            $montant_ht[$item] = $montant_ht[$item] * 1;
                        } catch (\Throwable $th) {
                            return redirect()->back()->with('error','Montant ht : Une valeur non numérique rencontrée.');
                        }
                        
                        if (gettype($montant_ht[$item])!='integer') {
                            return redirect()->back()->with('error','Montant ht : Une valeur non numérique rencontrée.');
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

                        $montant_ttc[$item] = $montant_ht[$item]*$taux_tva;
                        $libelle_service[$item] = trim($request->libelle_service[$item]);

                        $service = Service::where('libelle',$libelle_service[$item])->first();
                        if ($service!=null) {
                            $services_id = $service->id;
                        }else{
                            $services_id = Service::create([
                                'libelle'=>$libelle_service[$item]
                            ])->id;
                        }

                        
                        $unite_libelle[$item] = trim($request->unite[$item]);

                        $unite = Unite::where('unite',$unite_libelle[$item])->first();

                        if ($unite!=null) {
                            $code_unite = $unite->code_unite;
                        }else{
                            $code_unite = Unite::create([
                                'unite'=>$unite_libelle[$item]
                            ])->code_unite;
                        }


                        $data1 = [
                            'cotation_services_id'=>$cotation_services_id,
                            'services_id'=>$services_id,
                            'code_unite'=>$code_unite,
                            'qte'=>$qte[$item],
                            'prix_unit'=>$prix_unit[$item],
                            'remise'=>$remise[$item],
                            'montant_ht'=>$montant_ht[$item],
                            'montant_ttc'=>$montant_ttc[$item],
                        ];

                        DetailCotationService::create($data1);





                    }else{
                        return redirect()->back()->with('error','Veuillez saisir l\'ensemble des champs pour chaque service');
                    }
                }
            }
        }
    }

    public function storeStatutCotationService($libelle,$cotation_services_id,$profils_id){

        $type_statut_cotation_service = TypeStatutCotationService::where('libelle', $libelle)->first();
                
            if ($type_statut_cotation_service===null) {
                $type_statut_cot_services_id = TypeStatutCotationService::create([
                    'libelle'=>$libelle
                ])->id;
            } else {
                $type_statut_cot_services_id = $type_statut_cotation_service->id;
            }

            if (isset($type_statut_cot_services_id)) {


                $statut_cotation_service = StatutCotationService::where('cotation_services_id',$cotation_services_id)
                ->orderByDesc('id')
                ->limit(1)
                ->first();

                if ($statut_cotation_service!=null) {
                        StatutCotationService::where('id',$statut_cotation_service->id)->update([
                            'date_fin'=>date('Y-m-d'),
                        ]);
                }



                StatutCotationService::create([
                    'profils_id'=>$profils_id,
                    'cotation_services_id'=>$cotation_services_id,
                    'type_statut_cot_services_id'=>$type_statut_cot_services_id,
                    'date_debut'=>date('Y-m-d'),
                ]);

            }
    }

    public function getDetailCotationService($demande_fonds_id){
        $detail_cotation_services = CotationService::where('demande_fonds_id',$demande_fonds_id)
        ->join('demande_fonds as df','df.id','=','cotation_services.demande_fonds_id')
        ->join('detail_cotation_services as dcs','dcs.cotation_services_id','=','cotation_services.id')
        ->join('unites as u','u.code_unite','=','dcs.code_unite')
        ->join('services as s','s.id','=','dcs.services_id')
        ->get();
        
        return $detail_cotation_services;
    }

    public function getStatutCotationService($demande_fonds_id){

        $statut_cotation_service = DB::table('statut_cotation_services as scs')
        ->join('type_statut_cotation_services as tscs','tscs.id','=','scs.type_statut_cot_services_id')
        ->join('cotation_services as cs','cs.id','=','scs.cotation_services_id')
        ->join('demande_fonds as df','df.id','=','cs.demande_fonds_id')

        ->join('profils as p','p.id','=','scs.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')

        ->where('df.id',$demande_fonds_id)
        ->orderByDesc('scs.id')
        ->limit(1)
        ->select('tscs.libelle','scs.commentaire','tp.name as profil_commentaire','a.nom_prenoms as nom_prenoms_commentaire')
        ->first();

        return $statut_cotation_service;
    }

    public function getStatutDemandeFond($demande_fonds_id){
            
        $statut_demande_fond = DB::table('statut_demande_fonds as sdf')
        ->join('type_statut_demande_fonds as tsdf','tsdf.id','=','sdf.type_statut_demande_fonds_id')
        ->join('profils as p','p.id','=','sdf.profils_id')
        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as a','a.id','=','u.agents_id')
        ->where('sdf.demande_fonds_id',$demande_fonds_id)
        ->orderByDesc('sdf.id')
        ->limit(1)
        ->first();

        return $statut_demande_fond;
    }

    public function getUnites(){
        $unites = Unite::all();
        return $unites;
    }

    public function getOrganisation($entnum){

        $organisations_id = null;

        $organisation = Organisation::where('entnum',$entnum)->first();
        if ($organisation != null) {
            $organisations_id = $organisation->id;
        }

        return $organisations_id;

    }
    
}
