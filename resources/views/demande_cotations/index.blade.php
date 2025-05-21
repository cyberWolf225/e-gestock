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
        <br>
        <div class="row">
            <div class="col-12">

                @if(isset($acces_create))
                    <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
                        <div class="col-lg-12">
                            <a class="btn btn-sm" href="{{ route("demande_cotations.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
                                Créer une demande de cotation
                            </a>
                        </div>
                    </div>
                @endif

                <div class="card-header entete-table">{{ __('LISTE DES DEMANDES DE COTATIONS') }}
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
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">TYPE OPERATION</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">DATE</th>
                    <th style="vertical-align: middle; text-align:center;">STATUT</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">                        
                    </th>
                    </tr>
                    </thead>
                    <tbody style="vertical-align: middle">                        
                        <?php $i = 1; ?>

                        @foreach($demande_cotations as $demande_cotation)
                            
                            <?php 
                            
                                $dataCheckDateEcheance = [
                                    'type_profils_name'=>$type_profils_name,
                                    'date_echeance'=>$demande_cotation->date_echeance,
                                ];
                                $response_echeance = $controller1->checkDateEcheance($dataCheckDateEcheance);
                            
                                $demande_cotations_id_encryptString = Crypt::encryptString($demande_cotation->id);
                                
                                
                                $title_show = "";
                                $href_show = "";
                                $display_show = "none";
                                $svg_show = '<svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>';
                            
                                $title_edit = "";
                                $href_edit = "";
                                $display_edit = "none";
                                $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                </svg>';
                            
                                $title_print = "";
                                $href_print = "";
                                $display_print = "none";
                                $svg_print = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/><path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/></svg>';
                            
                                $title_info = "";
                                $href_info = "";
                                $display_info = "none";
                                $svg_info = '<svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-stars" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/><path d="M2.242 2.194a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.256-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53z"/></svg>';

                                $title_folder = "";
                                $href_folder = "";
                                $display_folder = "none";
                                $svg_folder = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder2-open" viewBox="0 0 16 16">
                                <path d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14V3.5zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5V6zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7H1.633z"/>
                                </svg>';                           
                            
                                $type_statut_demande_cotations_libelle = null;
                            
                                $statut_demande_cotation = $controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
                                if($statut_demande_cotation != null){
                                    $type_statut_demande_cotations_libelle = $statut_demande_cotation->libelle;
                                }

                                $svg_echeance = null;
                                if($demande_cotation->date_echeance != null && $type_profils_name === 'Fournisseur'){
                                    if($type_statut_demande_cotations_libelle === "Transmis pour cotation" or $type_statut_demande_cotations_libelle === "Coté"){
                                        $svg_echeance = '<span title="Date d\'échéance de la demande de cotation : '.date('d/m/Y H:i:s',strtotime($demande_cotation->date_echeance)).'" style="color:orange; font-weight:bold">
                                            <svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg>
                                        </span>';
                                    }
                                }

                                $reponse_cotation = null;
                                $reponse_cotations_id_encryptString = null;
                                if($type_profils_name === 'Fournisseur'){
                                    $fournisseur_demande_cotation = null;
                                    $organisation = $controller1->getOrganisationByUserId(auth()->user()->id);

                                    if($organisation != null){
                                        $datagetFrs = [
                                            'organisations_id'=>$organisation->id,
                                            'demande_cotations_id'=>$demande_cotation->id
                                        ];
                                        $fournisseur_demande_cotation = $controller1->getFournisseurDemandeCotation($datagetFrs);

                                        if($fournisseur_demande_cotation != null){
                                            $reponse_cotation = $controller1->getReponseCotationByFournisseurDemandeCotationId($fournisseur_demande_cotation->id);

                                        }
                                    }

                                }

                                if($reponse_cotation != null){
                                    $reponse_cotations_id_encryptString = Crypt::encryptString($reponse_cotation->id);
                                }

                            
                                $title_show = "Voir les détails";
                                $href_show = "/demande_cotations/show/".$demande_cotations_id_encryptString;
                                $display_show = "";
                            
                                if($type_statut_demande_cotations_libelle === "Soumis pour validation" or $type_statut_demande_cotations_libelle === "Annulé (Gestionnaire des achats)" or $type_statut_demande_cotations_libelle === "Annulé (Responsable des achats)" or $type_statut_demande_cotations_libelle === "Transmis pour cotation"){
                                    if($type_profils_name === 'Gestionnaire des achats'){
                            
                                        $title_edit = "Modifier la demande de cotation";
                                        $href_edit = "/demande_cotations/edit/".$demande_cotations_id_encryptString;
                                        $display_edit = "";
                                        
                                    }
                                }
                            
                                if($type_statut_demande_cotations_libelle === "Transmis (Responsable des achats)" or $type_statut_demande_cotations_libelle === "Rejeté (Responsable DMP)" or $type_statut_demande_cotations_libelle === "Transmis pour cotation"){
                                    if($type_profils_name === 'Responsable des achats'){
                            
                                        $title_edit = "Valider la demande de cotation";
                                        $href_edit = "/demande_cotations/edit/".$demande_cotations_id_encryptString;
                                        $display_edit = "";
                                        
                                    }
                                }
                            
                                if($type_statut_demande_cotations_libelle === "Demande de cotation (Transmis Responsable DMP)" or $type_statut_demande_cotations_libelle === "Transmis pour cotation"){
                                    if($type_profils_name === 'Responsable DMP'){
                            
                                        $title_edit = "Valider la demande de cotation";
                                        $href_edit = "/demande_cotations/edit/".$demande_cotations_id_encryptString;
                                        $display_edit = "";
                                        
                                    }
                                }
                            
                                if($type_statut_demande_cotations_libelle === "Transmis pour cotation"){
                                    if($type_profils_name === 'Fournisseur'){
                                        if($response_echeance === true){
                                            $title_edit = "Repondre à la demande de cotation";
                                            $href_edit = "/reponse_cotations/create/".$demande_cotations_id_encryptString;
                                            $display_edit = "";
                                        }
                                    }
                                }

                                if($type_statut_demande_cotations_libelle === "Coté"){
                                    if($type_profils_name === 'Fournisseur'){
                                        if($response_echeance === true){
                                        
                                            if($reponse_cotation != null && $reponse_cotations_id_encryptString != null){
                                                $title_edit = "Modifier la cotation";
                                                $href_edit = "/reponse_cotations/edit/".$reponse_cotations_id_encryptString;
                                                $display_edit = "";
                                            }

                                            if($reponse_cotation === null){
                                                $title_edit = "Repondre à la demande de cotation";
                                                $href_edit = "/reponse_cotations/create/".$demande_cotations_id_encryptString;
                                                $display_edit = "";

                                                $type_statut_demande_cotations_libelle = "Transmis pour cotation";
                                            }
                                        }
                                    }

                                    if($type_profils_name === 'Gestionnaire des achats' or $type_profils_name === 'Responsable des achats' or $type_profils_name === 'Responsable DMP'){

                                        $title_folder = "Liste des cotations fournisseurs";
                                        $href_folder = "/reponse_cotations/index/".$demande_cotations_id_encryptString;
                                        $display_folder = "";

                                    }
                                }

                                if($type_statut_demande_cotations_libelle === "Cotation en cours d'analyse" or $type_statut_demande_cotations_libelle === "Cotation sélectionnée"){

                                    if($type_profils_name === 'Gestionnaire des achats' or $type_profils_name === 'Responsable des achats' or $type_profils_name === 'Responsable DMP'){

                                    $title_folder = "Liste des cotations fournisseurs";
                                    $href_folder = "/reponse_cotations/index/".$demande_cotations_id_encryptString;
                                    $display_folder = "";

                                    }

                                }

                                if($type_profils_name === 'Fournisseur' && $response_echeance === false){
                                    $type_statut_demande_cotations_libelle = "Échéance dépassée";
                                }

                                
                                if($reponse_cotation != null){
                                    $title_show = "Voir la cotation";
                                    $href_show = "/reponse_cotations/show/".$reponse_cotations_id_encryptString;
                                    $display_show = "";
                                }
                            ?>

                            <tr>
                                <td style="text-align: center; vertical-align:middle; width: 1px; white-space: nowrap;">{{ $i }}</td>
                                <td style="text-align: left; vertical-align:middle; font-weight:bold; width: 1px; white-space: nowrap; color: #7d7e8f">{{ $demande_cotation->num_dem ?? '' }}</td>
                                <td style="vertical-align:middle">{{ $demande_cotation->intitule ?? '' }}</td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle;">
                                    {{ $demande_cotation->libelle ?? '' }}
                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle;">
                                    @if(isset($demande_cotation->created_at))
                                       {{ date("d/m/Y",strtotime($demande_cotation->created_at)) }} 
                                    @endif
                                </td>
                                <td style="text-align: left; vertical-align:middle; font-weight:bold; color:red; font-weight:bold; vertical-align:middle;">{{ $type_statut_demande_cotations_libelle ?? '' }}
                                
                                    @if($svg_echeance != null)
                                        <?php if(isset($svg_echeance)){ echo $svg_echeance; }  ?>
                                    @endif
                                

                                </td>
                                <td style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">

                                    <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm">
                                        <?php if(isset($svg_show)){ echo $svg_show; }  ?>
                                    </a>

                                    <a style="display:{{ $display_folder }}; background-color:#d6c6a0" title="{{ $title_folder }}" href="{{ $href_folder }}" class="btn btn-sm">
                                        <?php if(isset($svg_folder)){ echo $svg_folder; }  ?>
                                    </a>

                                    <a style="display:{{ $display_info }};" title="{{ $title_info }}" href="{{ $href_info }}" class="btn btn-light btn-sm">
                                        <?php if(isset($svg_info)){ echo $svg_info; }  ?>
                                    </a>

                                    <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm">
                                        <?php if(isset($svg_edit)){ echo $svg_edit; }  ?>
                                    </a>

                                    <a target="_blank" style="display:{{ $display_print }};" title="{{ $title_print }}" href="{{ $href_print }}" class="btn btn-secondary btn-sm">
                                        <?php if(isset($svg_print)){ echo $svg_print; }  ?>
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




