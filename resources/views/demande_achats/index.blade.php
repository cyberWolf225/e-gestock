@extends('layouts.admin')

@section('styles_datatable')
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('../plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('../dist/css/adminlte.min.css') }}">

  <link rel="shortcut icon" href="{{ asset('../dist/img/logo.png') }}">

  <style>
    .fond{
        background: url('../../dist/img/sante2.jpg') no-repeat;
        background-size: 100% 100%;
        font-size: 11px;
    }
  </style>

@endsection
 
@section('content')
    <div class="container" style="color:black">
        @if(isset($acces_create))
        <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-sm" href="{{ route("demande_cotations.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
                    Créer une demande d'achat
                </a>
            </div>
        </div>
        @else
        <br>
        @endif
        <div class="row">

            <div class="col-12">
                 

                <div class="card-header entete-table">{{ __('LISTE DES BONS DE COMMANDES FOURNISSEURS') }}
                </div>
                <div class="card-body bg-white">
                    @if(Session::has('success'))
                    <div class="alert alert-success" style="background-color: #d4edda; color:#155724">
                        {{ Session::get('success') }}
                    </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger" style="background-color: #f8d7da; color:#721c24">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    <table id="example1" class="table table-striped  bg-white" style="width: 100%">
                    <thead>
                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">#</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">N° DEMANDE</th>
                    <th style="vertical-align: middle; text-align:center" width="100%">INTITULE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">DATE</th>
                    <th style="vertical-align: middle; text-align:center;">STATUT</th>

                    @if(isset($demande_achat_edite)) 
                        @if($demande_achat_edite != null)
                        <th style="vertical-align: middle; text-align:center; width: 15%; ">FOURNISSEUR</th>
                        <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">CONTACTS</th>
                        @endif
                    @endif


                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">                        
                    </th>
                    </tr>
                    </thead>
                    <tbody style="vertical-align: middle">

                        <?php 
                        
                            $i = 1;
                            

                        ?>

                        

                        @foreach($demande_achats as $demande_achat)



                            <?php 
                                $libelle_statut = null;
                                $statut_demande_achat = null;
                                $demande_control = null;
                                $num_bc = null;
                                $verificateur_cotation = null;
                                $profils_fournisseur = null;
                                $cotation_fournisseurs_id = null;
                                $fournisseur = null;
                                $correspondant = null;

                                $demande_achats_id = Crypt::encryptString($demande_achat->id);

                                


                                $title_info = "";
                                $href_info = "";
                                $display_info = "none";

                                $title_liste_livraison = "";
                                $href_liste_livraison = "";
                                $display_liste_livraison = "none";

                                $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                </svg>';

                                $selection_adjudication = DB::table('selection_adjudications as sa')
                                ->join('cotation_fournisseurs as cf','cf.id','=','sa.cotation_fournisseurs_id')
                                ->where('cf.demande_achats_id',$demande_achat->id)
                                ->first();
                                if ($selection_adjudication!=null) {

                                    $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);

                                    if ($type_profils_name != "Fournisseur") {

                                        $title_info = "Voir les cotations fournisseurs";
                                        $href_info = "/selection_adjudications/liste/".$demande_achats_id;
                                        $display_info = "";
                                        
                                        
                                    }

                                    


                                    $statut_demande_achat = DB::table('statut_demande_achats as sda')
                                    ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                    ->where('sda.demande_achats_id',$demande_achat->id)
                                    ->where('tsda.libelle','Édité')
                                    ->limit(1)
                                    ->first();
                                    if ($statut_demande_achat!=null) {

                                        $organisation = DB::table('demande_achats as da')
                                        ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
                                        ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
                                        ->join('organisations as o','o.id','=','cf.organisations_id')
                                        ->join('statut_organisations as so','so.organisations_id','=','o.id')
                                        ->join('profils as p','p.id','=','so.profils_id')
                                        ->join('users as u','u.id','=','p.users_id')
                                        ->join('agents as a','a.id','=','u.agents_id')
                                        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                        ->where('tp.name','Fournisseur')
                                        ->where('da.id',$demande_achat->id)
                                        ->orderByDesc('sa.id')
                                        ->limit(1)
                                        ->select('denomination','entnum','nom_prenoms','contacts')
                                        ->first();

                                        if ($organisation!=null) {
                                            $fournisseur = $organisation->denomination.' ('.$organisation->entnum.')';

                                            $correspondant = $organisation->contacts;
                                        }
                                    }
                                    

                                }


                                $demande_control = DB::table('demande_achats as da')
                                    ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
                                    ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
                                    ->where('da.id',$demande_achat->id)
                                    ->whereIn('da.id',function($query){
                                    $query->select(DB::raw('da2.id'))
                                    ->from('demande_achats as da2')
                                    ->join('statut_demande_achats as sda','sda.demande_achats_id','=','da2.id')
                                    ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                    ->whereIn('tsda.libelle',['Visé (Responsable DMP)','Validé'])
                                    ->whereRaw('da.id = da2.id');
                                    })
                                    ->first();
                                    if ($demande_control!=null) {
                                        $num_bc = $demande_achat->num_bc;
                                    }else{
                                        $num_bc = str_replace('BC','',str_replace('BCS','',$demande_achat->num_bc)) ;
                                    }

                                    
                                    $title_show = "Voir les details de la demande d'achat";
                                    $href_show = "show/".$demande_achats_id;
                                    $display_show = ""; 

                                    $title_print = "";
                                    $href_print = "";
                                    $display_print = "none";

                                    $title_edit = "";
                                    $href_edit = "";
                                    $display_edit = "none";

                                    $title_transfert = "";
                                    $href_transfert = "";
                                    $display_transfert = "none";

                                    

                                    $statut_demande_achat = DB::table('statut_demande_achats as sda')
                                    ->join('type_statut_demande_achats as tsda', 'tsda.id', '=', 'sda.type_statut_demande_achats_id')
                                    ->where('sda.demande_achats_id',$demande_achat->id)
                                    ->orderByDesc('sda.id')
                                    ->limit(1)
                                    ->select('tsda.libelle') 
                                    ->first();

                                    if ($statut_demande_achat!=null) {
                                        $libelle_statut = $statut_demande_achat->libelle;
                                    }

                                    if ($libelle_statut === "Fournisseur sélectionné"){
                                        if ($type_profils_name === "Responsable des achats") {
                                            $display_info = "none";
                                        }
                                    }


                                    if ($libelle_statut === "Soumis pour validation" or $libelle_statut === "Rejeté (Responsable des achats)" or $libelle_statut === "Annulé (Gestionnaire des achats)" or $libelle_statut === "Annulé (Responsable des achats)") {

                                        if ($type_profils_name === "Gestionnaire des achats") {

                                            $title_edit = "Modifier la demande d'achat";

                                            $href_edit = "edit/".$demande_achats_id;
                                            $display_edit = "";
                            
                                            $title_transfert = "Transmettre la demande d'achat au Responsable des achats";
                                            $href_transfert = "send/".$demande_achats_id;
                                            $display_transfert = "";

                                        } 

                                    }elseif($libelle_statut === "Transmis (Responsable des achats)"){

                                        if ($type_profils_name === "Responsable des achats") {

                                            $title_edit = "Valider la demande d'achat";
                                            $href_edit = "/valider_demande_achats/create/".$demande_achats_id;
                                            $display_edit = "";

                                        }

                                    }elseif ($libelle_statut === "Validé (Responsable des achats)" or $libelle_statut === "Partiellement validé (Responsable des achats)") {

                                        if ($type_profils_name === "Responsable des achats") {

                                            $title_edit = "Valider la demande d'achat";
                                            $title_transfert = "Enregistrer la demande cotation";

                                            $href_edit = "/valider_demande_achats/create/".$demande_achats_id;
                                            $href_transfert = "cotation/".$demande_achats_id;

                                            $display_edit = "";
                                            $display_transfert = "";

                                        }

                                    }elseif ($libelle_statut === "Demande de cotation" or $libelle_statut === 'Demande de cotation (Annulé)') {

                                        if ($type_profils_name === "Responsable des achats") {

                                            $title_edit = "Modifier la demande de cotation";
                                            $title_transfert = "Transmettre la demande de cotation au Responsable DMP";

                                            $href_edit = "cotation/".$demande_achats_id;
                                            $href_transfert = "send_cotation/".$demande_achats_id;

                                            $display_edit = "";
                                            $display_transfert = "";

                                        }

                                    }elseif ($libelle_statut === 'Demande de cotation (Transmis Responsable DMP)') {

                                        if ($type_profils_name === "Responsable DMP") {

                                            $title_edit = "Valider la demande de cotation";

                                            $href_edit = "cotation/".$demande_achats_id;

                                            $display_edit = "";

                                        }

                                    }elseif ($libelle_statut === 'Demande de cotation (Validé)') {
                                        if ($type_profils_name === "Responsable des achats") {

                                            $title_transfert = "Soumettre la demande cotation aux fourmisseurs présélectionnés";

                                            $href_transfert = "cotation_send_frs/".$demande_achats_id;

                                            $display_transfert = "";

                                        }
                                    }
                                    elseif ($libelle_statut === "Transmis pour cotation") {

                                        if ($type_profils_name === "Responsable des achats") {

                                            $title_edit = "Valider la demande d'achat";
                                            $title_transfert = "Modifier la demande cotation transmise aux fourmisseurs";

                                            $href_edit = "/valider_demande_achats/create/".$demande_achats_id;
                                            $href_transfert = "cotation_send_frs/".$demande_achats_id;

                                            $display_edit = "";
                                            $display_transfert = "";

                                        }elseif ($type_profils_name === "Fournisseur") {
                                            
                                            $title_edit = "Faire la cotation de cette demande d'achat";
                                            $href_edit = "/cotation_fournisseurs/create/".$demande_achats_id;
                                            $display_edit = ""; 

                                        }

                                    }elseif( $libelle_statut === "Coté"){

                                        if ($type_profils_name === "Fournisseur") {
                                            
                                            $title_edit = "Faire la cotation de cette demande d'achat";
                                            $href_edit = "/cotation_fournisseurs/create/".$demande_achats_id;
                                            $display_edit = "";

                                            $profils_fournisseur = DB::table('type_profils as tp')
                                            ->join('profils as p','p.type_profils_id','=','tp.id')
                                            ->where('p.users_id',auth()->user()->id)
                                            ->whereIn('tp.name',['Fournisseur'])
                                            ->where('p.id',Session::get('profils_id'))
                                            ->first();

                                            if ($profils_fournisseur!=null) {
                                                $verificateur_cotation = DB::table('demande_achats')->orderByDesc('demande_achats.id')
                                                ->where('demande_achats.id',$demande_achat->id)
                                                ->whereIn('demande_achats.id', function($query) use($demande_achat){
                                                    $query->select(DB::raw('sda.demande_achats_id'))
                                                        ->from('statut_demande_achats as sda')
                                                        ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                                        ->join('demande_achats as da','da.id','=','sda.demande_achats_id')
                                                        ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
                                                        ->join('criteres as c','c.id','=','ca.criteres_id')
                                                        ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
                                                        ->join('organisations as o','o.id','=','ps.organisations_id')
                                                        ->join('statut_organisations as so','so.organisations_id','=','o.id')
                                                        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                                                        ->join('profils as p','p.id','=','so.profils_id')
                                                        ->join('users as u','u.id','=','p.users_id')
                                                        ->where('u.id',auth()->user()->id)
                                                        ->where('tso.libelle','Activé')
                                                        ->where('c.libelle','Fournisseurs Cibles')
                                                        ->where('tsda.libelle',['Coté'])
                                                        ->whereRaw('sda.profils_id = so.profils_id')
                                                        ->whereRaw('demande_achats.id = sda.demande_achats_id')
                                                        ->where('da.id',$demande_achat->id);
                                                })->first();

                                                if ($verificateur_cotation===null) {
                                                    $libelle_statut = "Transmis pour cotation";
                                                }
                                            }



                                        }elseif ($type_profils_name === "Responsable des achats") {

                                            $title_edit = "Sélectionner le fournisseur";
                                            $href_edit = "/selection_adjudications/liste/".$demande_achats_id;
                                            $display_edit = "";

                                        }

                                    }elseif ($libelle_statut === "Fournisseur sélectionné" or $libelle_statut==='Rejeté (Responsable DMP)') {
                                        
                                        if ($type_profils_name === "Responsable des achats") {

                                            if (isset($cotation_fournisseurs_id)) {

                                                $title_transfert = "Transférer la cotation du mieux disant au Responsable DMP pour validation";
                                                $href_transfert = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                                $display_transfert = "";

                                            }
                                            
                                            $title_edit = "Sélectionner le fournisseur";
                                            $href_edit = "/selection_adjudications/liste/".$demande_achats_id;
                                            $display_edit = "";
                                            

                                        } 

                                    }elseif ($libelle_statut === "Transmis (Responsable DMP)" or $libelle_statut==='Rejeté (Responsable Contrôle Budgétaire)' or $libelle_statut==='Rejeté (Responsable DFC)') {

                                        if ($type_profils_name === "Responsable DMP") {
                                            if (isset($cotation_fournisseurs_id)) {

                                                $title_edit = "Viser le bon de commande";

                                                $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;

                                                $display_edit = "";
                                            }
                                        }
                                        

                                    }elseif($libelle_statut==='Visé (Responsable DMP)'){

                                        if ($type_profils_name === "Responsable DMP") {
                                            if (isset($cotation_fournisseurs_id)) {

                                                $title_edit = "Modifier votre visa du bon de commande";

                                                $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;

                                                $display_edit = "";

                                                $title_transfert = "Transférer le dossier au Responsable Contrôle Budgétaire";

                                                $href_transfert = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;

                                                $display_transfert = "";

                                            }
                                        }

                                    }elseif($libelle_statut==='Transmis (Responsable Contrôle Budgétaire)' or $libelle_statut==='Rejeté (Chef Département DCG)'){

                                        if ($type_profils_name === "Responsable contrôle budgetaire") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Transmis (Chef Département DCG)' or $libelle_statut==='Rejeté (Responsable DCG)') {

                                        if ($type_profils_name === "Chef Département DCG") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Transmis (Responsable DCG)' or $libelle_statut==='Rejeté (Directeur Général Adjoint)') {

                                        if ($type_profils_name === "Responsable DCG") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Transmis (Directeur Général Adjoint)' or $libelle_statut==='Rejeté (Directeur Général)') {

                                        if ($type_profils_name === "Directeur Général Adjoint") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Transmis (Directeur Général)') {

                                        if ($type_profils_name === "Directeur Général") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Transmis (Responsable DFC)') {

                                        if ($type_profils_name === "Responsable DFC") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Validé' or $libelle_statut==='Annulé (Fournisseur)') {

                                        if ($type_profils_name === "Responsable des achats" or $type_profils_name === "Gestionnaire des achats") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Édité') {

                                        if ($type_profils_name === "Fournisseur") {

                                            $selection_adjudication = DB::table('organisations')
                                            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                            ->join('cotation_fournisseurs','cotation_fournisseurs.organisations_id','=','organisations.id')
                                            ->join('demande_achats as da','da.id','=','cotation_fournisseurs.demande_achats_id')
                                            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('commandes as c','c.demande_achats_id','=','da.id')
                                            ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                            ->where('type_statut_organisations.libelle','Activé')
                                            ->where('cotation_fournisseurs.demande_achats_id',$demande_achat->id)
                                            ->where('criteres.libelle','Fournisseurs Cibles')
                                            ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                            ->select('statut_organisations.profils_id','selection_adjudications.cotation_fournisseurs_id')
                                            ->orderByDesc('selection_adjudications.id')
                                            ->limit(1)
                                            ->first();

                                            if ($selection_adjudication!=null) {

                                                $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);

                                                $title_edit = "Retirer du bon de commande";
                                                $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                                $display_edit = "";
                                            }

                                        }elseif ($type_profils_name === "Responsable des achats" or $type_profils_name === "Gestionnaire des achats") {
                                            $title_edit = "Viser le bon de commande";
                                            $href_edit = "/cotation_fournisseurs/create_bc/".$cotation_fournisseurs_id;
                                            $display_edit = "";
                                        }

                                    }elseif ($libelle_statut==='Retiré (Frs.)') {

                                        if ($type_profils_name === "Fournisseur") {

                                            $selection_adjudication = DB::table('organisations')
                                            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                            ->join('cotation_fournisseurs','cotation_fournisseurs.organisations_id','=','organisations.id')
                                            ->join('demande_achats as da','da.id','=','cotation_fournisseurs.demande_achats_id')
                                            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('commandes as c','c.demande_achats_id','=','da.id')
                                            ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                            ->where('type_statut_organisations.libelle','Activé')
                                            ->where('cotation_fournisseurs.demande_achats_id',$demande_achat->id)
                                            ->where('criteres.libelle','Fournisseurs Cibles')
                                            ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                            ->select('statut_organisations.profils_id','selection_adjudications.cotation_fournisseurs_id')
                                            ->orderByDesc('selection_adjudications.id')
                                            ->limit(1)
                                            ->first();



                                            if ($selection_adjudication!=null) {


                                                $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);

                                                $title_edit = "Livrer la commande";
                                                $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                                $display_edit = "";

                                                $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                                                </svg>';
                                            }

                                        }
                                    }elseif ($libelle_statut==='Livraison partielle') {

                                        if ($type_profils_name === "Fournisseur") {

                                            $selection_adjudication = DB::table('organisations')
                                            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                            ->join('cotation_fournisseurs','cotation_fournisseurs.organisations_id','=','organisations.id')
                                            ->join('demande_achats as da','da.id','=','cotation_fournisseurs.demande_achats_id')
                                            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('commandes as c','c.demande_achats_id','=','da.id')
                                            ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                            ->where('type_statut_organisations.libelle','Activé')
                                            ->where('cotation_fournisseurs.demande_achats_id',$demande_achat->id)
                                            ->where('criteres.libelle','Fournisseurs Cibles')
                                            ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                            ->select('statut_organisations.profils_id','selection_adjudications.cotation_fournisseurs_id')
                                            ->orderByDesc('selection_adjudications.id')
                                            ->limit(1)
                                            ->first();



                                            if ($selection_adjudication!=null) {

                                                // $cotation_fournisseurs_id = $selection_adjudication->cotation_fournisseurs_id;

                                                $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);

                                                $title_edit = "Livrer la commande";
                                                $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                                $display_edit = "";

                                                

                                                $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                                                </svg>';

                                            }

                                        }elseif($type_profils_name === "Comite Réception"){

                                            $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);
                                            
                                            $title_edit = "Confirmer la livraison de la commande";
                                            $title_transfert = "";
                                            $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                            //$display_edit = "";  
                                            $display_edit = "none";  
                                            
                                            $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                                            <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg>';

                                        }
                                    }elseif ($libelle_statut==='Livraison totale') {

                                        if($type_profils_name === "Comite Réception"){
                                            
                                            $title_edit = "Confirmer la livraison de la commande";
                                            $title_transfert = "";
                                            $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                            //$display_edit = "";   
                                            $display_edit = "none";   
                                            
                                            $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                                            <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg>';

                                        }
                                    }elseif ($libelle_statut==='Livraison annulée (Comite Réception)') {

                                        // if($type_profils_name === "Comite Réception"){

                                        //     // $cotation_fournisseurs_id = Crypt::encryptString($cotation_fournisseurs_id);
                                            
                                        //     $title_edit = "Confirmer la livraison de la commande";
                                        //     $title_transfert = "";
                                        //     $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                        //     $display_edit = "";             

                                        // }else
                                        
                                        if ($type_profils_name === "Fournisseur") {

                                            $selection_adjudication = DB::table('organisations')
                                            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                            ->join('cotation_fournisseurs','cotation_fournisseurs.organisations_id','=','organisations.id')
                                            ->join('demande_achats as da','da.id','=','cotation_fournisseurs.demande_achats_id')
                                            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('commandes as c','c.demande_achats_id','=','da.id')
                                            ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                            ->where('type_statut_organisations.libelle','Activé')
                                            ->where('cotation_fournisseurs.demande_achats_id',$demande_achat->id)
                                            ->where('criteres.libelle','Fournisseurs Cibles')
                                            ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                            ->select('statut_organisations.profils_id','selection_adjudications.cotation_fournisseurs_id')
                                            ->orderByDesc('selection_adjudications.id')
                                            ->limit(1)
                                            ->first();



                                            if ($selection_adjudication!=null) {

                                                $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);

                                                $title_edit = "Livrer la commande";
                                                $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                                $display_edit = "";
                                            }

                                        }
                                    }elseif ($libelle_statut==='Livraison partielle confirmée') {

                                        // if($type_profils_name === "Comite Réception"){
                                            
                                        //     $title_edit = "Confirmer la livraison de la commande";
                                        //     $title_transfert = "";
                                        //     $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                        //     $display_edit = "";             

                                        // }else
                                        
                                        if ($type_profils_name === "Fournisseur") {

                                            $selection_adjudication = DB::table('organisations')
                                            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                            ->join('cotation_fournisseurs','cotation_fournisseurs.organisations_id','=','organisations.id')
                                            ->join('demande_achats as da','da.id','=','cotation_fournisseurs.demande_achats_id')
                                            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('commandes as c','c.demande_achats_id','=','da.id')
                                            ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                            ->where('type_statut_organisations.libelle','Activé')
                                            ->where('cotation_fournisseurs.demande_achats_id',$demande_achat->id)
                                            ->where('criteres.libelle','Fournisseurs Cibles')
                                            ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                            ->select('statut_organisations.profils_id','selection_adjudications.cotation_fournisseurs_id')
                                            ->orderByDesc('selection_adjudications.id')
                                            ->limit(1)
                                            ->first();



                                            if ($selection_adjudication!=null) {

                                                $cotation_fournisseurs_id = Crypt::encryptString($selection_adjudication->cotation_fournisseurs_id);

                                                $title_edit = "Livrer la commande";
                                                $href_edit = "/livraison_commandes/create/".$cotation_fournisseurs_id;
                                                $display_edit = "";
                                            }

                                        }
                                    }

                                    //affichage du print
                                        $statut_demande_achat_edit = DB::table('statut_demande_achats as sda')
                                            ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                            ->where('tsda.libelle','Validé') // Édité Retiré (Frs.)
                                            ->orderByDesc('sda.id')
                                            ->limit(1)
                                            ->select('sda.id')
                                            ->where('sda.demande_achats_id',$demande_achat->id)
                                            ->first();
                                        if ($statut_demande_achat_edit!=null) {
                                            $statut_demande_achat_edit_id = $statut_demande_achat_edit->id;


                                            $statut_demande_achat_annule = DB::table('statut_demande_achats as sda')
                                                ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                                ->whereIn('tsda.libelle',['Annulé (Responsable des achats)','Annulé (Fournisseur)'])
                                                ->orderByDesc('sda.id')
                                                ->limit(1)
                                                ->select('sda.id')
                                                ->where('sda.demande_achats_id',$demande_achat->id)
                                                ->first();
                                                if ($statut_demande_achat_annule!=null) {
                                                    $statut_demande_achat_annule_id = $statut_demande_achat_annule->id;
                                                }else{
                                                    $statut_demande_achat_annule_id = 0;
                                                }

                                                if ($statut_demande_achat_edit_id < $statut_demande_achat_annule_id) {
                                                    $statut_demande_achat_edit_id = null;
                                                }

                                        }else{
                                            $statut_demande_achat_edit_id = null;
                                        }

                                        if ($statut_demande_achat_edit_id !=null) {

                                            $title_print = "Imprimer le bon de commande";

                                            $path = 'storage/documents/bcs/'.$demande_achat->num_bc.'.pdf';

                                            $public_path = str_replace("/","\\",public_path($path));

                                            $type_operations_libelle = Crypt::encryptString('bcs');

                                            if(file_exists($public_path)){

                                                $href_print = '/documents/'.$demande_achats_id.'/'.$type_operations_libelle;
                                                $display_print = "";
                                                
                                            }else{
                                                $href_print = "/print/da/".$demande_achats_id;
                                                $display_print = "none";
                                            }

                                        }

                                        if(isset($selection_adjudication)){
                                            if(isset($selection_adjudication->cotation_fournisseurs_id)){
                                                

                                                $livraison_commande = DB::table('livraison_commandes')
                                                ->where('cotation_fournisseurs_id',$selection_adjudication->cotation_fournisseurs_id)
                                                ->first();

                                                if($livraison_commande != null){

                                                    $title_liste_livraison = "Livraison effectuée";
                                                    $href_liste_livraison = "/livraison_commandes/index/".$cotation_fournisseurs_id;
                                                    $display_liste_livraison = "";

                                                    $svg_liste_livraison = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                                                    <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9V5.5z"/>
                                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                                    </svg>';

                                                }
                                                

                                            }
                                        }

                                        
                                    //
                                    
                                //

                                // BC Validé
                                $statut_demande_achat_valide = DB::table('statut_demande_achats as sda')
                                    ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                    ->where('sda.demande_achats_id',$demande_achat->id)
                                    ->where('tsda.libelle','Validé')
                                    ->limit(1)
                                    ->first();

                                if($statut_demande_achat_valide != null){
                                    if ($type_profils_name === "Fournisseur") {

                                        $cotation_valide = DB::table('organisations')
                                        ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                        ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                        ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                        ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                        ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                        ->join('cotation_fournisseurs','cotation_fournisseurs.organisations_id','=','organisations.id')
                                        ->join('demande_achats as da','da.id','=','cotation_fournisseurs.demande_achats_id')
                                        ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                        ->join('commandes as c','c.demande_achats_id','=','da.id')
                                        ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                        ->where('type_statut_organisations.libelle','Activé')
                                        ->where('cotation_fournisseurs.demande_achats_id',$statut_demande_achat_valide->demande_achats_id)
                                        ->where('criteres.libelle','Fournisseurs Cibles')
                                        ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                        ->select('statut_organisations.profils_id','sa.cotation_fournisseurs_id')
                                        ->orderByDesc('sa.id')
                                        ->limit(1)
                                        ->first();

                                        if ($cotation_valide === null) {

                                            $title_edit = "";
                                            $href_edit = "";
                                            $display_edit = "none";

                                            $cotation_frs = DB::table('organisations')
                                            ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                            ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                            ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                            ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                            ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                            ->join('cotation_fournisseurs as cf','cf.organisations_id','=','organisations.id')
                                            ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
                                            ->join('commandes as c','c.demande_achats_id','=','da.id')
                                            ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                            ->where('type_statut_organisations.libelle','Activé')
                                            ->where('cf.demande_achats_id',$statut_demande_achat_valide->demande_achats_id)
                                            ->where('criteres.libelle','Fournisseurs Cibles')
                                            ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                            ->select('statut_organisations.profils_id','cf.id as cotation_fournisseurs_id')
                                            ->limit(1)
                                            ->first();
                                            if($cotation_frs != null){

                                                $title_show = "Voir votre cotation";
                                                $href_show = "/cotation_fournisseurs/show/".Crypt::encryptString($cotation_frs->cotation_fournisseurs_id);
                                                $display_show = ""; 
                                                
                                            }
                                            

                                            $title_liste_livraison = "";
                                            $href_liste_livraison = "";
                                            $display_liste_livraison = "none";

                                            $title_print = "";
                                            $href_print = "";
                                            $display_print = "none";

                                            $title_transfert = "";
                                            $href_transfert = "";
                                            $display_transfert = "none";

                                            $libelle_statut = "Cotation non sélectionnée";
                                        }

                                    }
                                }

                                if ($type_profils_name === "Fournisseur"){
                                    $cotation_frs2 = DB::table('organisations')
                                        ->join('statut_organisations','statut_organisations.organisations_id','=','organisations.id')
                                        ->join('type_statut_organisations','type_statut_organisations.id','=','statut_organisations.type_statut_organisations_id')
                                        ->join('preselection_soumissionnaires','preselection_soumissionnaires.organisations_id','=','organisations.id')
                                        ->join('critere_adjudications','critere_adjudications.id','=','preselection_soumissionnaires.critere_adjudications_id')
                                        ->join('criteres','criteres.id','=','critere_adjudications.criteres_id')
                                        ->join('cotation_fournisseurs as cf','cf.organisations_id','=','organisations.id')
                                        ->join('demande_achats as da','da.id','=','cf.demande_achats_id')
                                        ->join('commandes as c','c.demande_achats_id','=','da.id')
                                        ->join('familles as f','f.ref_fam','=','da.ref_fam')
                                        ->where('type_statut_organisations.libelle','Activé')
                                        ->where('cf.demande_achats_id',$demande_achat->id)
                                        ->where('criteres.libelle','Fournisseurs Cibles')
                                        ->where('statut_organisations.profils_id',Session::get('profils_id'))
                                        ->select('statut_organisations.profils_id','cf.id as cotation_fournisseurs_id')
                                        ->limit(1)
                                        ->first();
                                        if($cotation_frs2 != null){

                                            $title_show = "Voir votre cotation";
                                            $href_show = "/cotation_fournisseurs/show/".Crypt::encryptString($cotation_frs2->cotation_fournisseurs_id);
                                            $display_show = ""; 
                                            
                                        }else{
                                            $title_show = "";
                                            $href_show = "";
                                            $display_show = "none"; 
                                        }
                                }
                            ?>



                            <tr>
                                <td style="text-align: center; vertical-align:middle; width: 1px; white-space: nowrap;">{{ $i }}</td>
                                <td style="text-align: left; vertical-align:middle; font-weight:bold; width: 1px; white-space: nowrap; color: #7d7e8f">{{ $num_bc ?? '' }}</td>
                                <td style="vertical-align:middle">{{ $demande_achat->intitule ?? '' }}</td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle;">
                                    @if(isset($demande_achat->created_at))
                                       {{ date("d/m/Y",strtotime($demande_achat->created_at)) }} 
                                    @endif
                                </td>
                                <td style="text-align: left; vertical-align:middle; font-weight:bold; color:red; font-weight:bold; vertical-align:middle;">{{ $libelle_statut ?? '' }}</td>
                                @if(isset($demande_achat_edite))
                                    <td style="width: 15%; vertical-align:middle;">
                                        @if($libelle_statut != "Cotation non sélectionnée")
                                            {{ $fournisseur ?? '' }}
                                            
                                        @endif
                                    </td>
                                    <td style="width: 15%; vertical-align:middle;">
                                        @if($libelle_statut != "Cotation non sélectionnée")
                                            {{ $correspondant ?? '' }}
                                            
                                        @endif
                                    </td>
                                @endif
                                <td style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">




                                    <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm">
                                        <svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                    </a>

                                    <a style="display:{{ $display_info }};" title="{{ $title_info }}" href="{{ $href_info }}" class="btn btn-light btn-sm">

                                        <svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-stars" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                                        <path d="M2.242 2.194a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.256-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53z"/>
                                        </svg>

                                    </a>

                                    <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm">
                                            
                                        <?php if(isset($svg_edit)){ echo $svg_edit; }  ?>
                                        
                                    </a>

                                    <a style="display:{{ $display_liste_livraison }};" title="{{ $title_liste_livraison }}" href="{{ $href_liste_livraison }}" class="btn btn-success btn-sm">
                                            
                                        <?php if(isset($svg_liste_livraison)){ echo $svg_liste_livraison; }  ?>
                                        
                                    </a>

                                    <a style="display:{{ $display_transfert }};" title="{{ $title_transfert }}" href="{{ $href_transfert }}" class="btn btn-success btn-sm">
                                        <svg style="color:black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg>
                                    </a>

                                    <a target="_blank" style="display:{{ $display_print }};" title="{{ $title_print }}" href="{{ $href_print }}" class="btn btn-secondary btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                                            <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                            <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                          </svg>
                                    </a>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('javascripts_datatable') 
    <!-- jQuery -->
    <script src="{{ asset('../plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('../plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('../plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('../dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('../dist/js/demo.js') }}"></script>
    <!-- Page specific script -->
    <script>
    $(function () {
        $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });
    </script>
@endsection




