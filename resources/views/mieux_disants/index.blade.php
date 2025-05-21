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

                <div class="card-header entete-table">{{ __('MIEUX DISANTS') }}
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
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">N° CNPS</th>
                    <th style="vertical-align: middle; text-align:center" width="100%">RAISON SOCIALE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">MONTANT <br/>TOTAL BRUT</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">MONTANT <br/>REMISE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">MONTANT <br/>TOTAL NET</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">TVA</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">NET A PAYER</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">MONTANT <br/>ACOMPTE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">DATE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">STATUT</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">                        
                    </th>
                    </tr>
                    </thead>
                    <tbody style="vertical-align: middle">                        
                        <?php $i = 1; ?>

                        @foreach($mieux_disants as $mieux_disant)
                            <?php 
                                $responseProcedureSet = $controller2->procedureSetReponseCotationFormat($mieux_disant);

                                $responseFormat = $controller2->formatTotaux($responseProcedureSet);

                                $demande_cotations_id_encryptString = Crypt::encryptString($demande_cotation->id);
                                $mieux_disants_id_encryptString = Crypt::encryptString($mieux_disant->id);

                                $type_statut_mieux_disants_libelle = null;
                            
                                $statut_mieux_disant = $controller2->getLastStatutMieuxDisant($mieux_disant->id);
                                if($statut_mieux_disant != null){
                                    $type_statut_mieux_disants_libelle = $statut_mieux_disant->libelle;
                                }

                                $type_statut_demande_cotations_libelle = null;
                            
                                $statut_demande_cotation = $controllerDemandeCotation->getLastStatutDemandeCotation($demande_cotation->id);
                                if($statut_demande_cotation != null){
                                    $type_statut_demande_cotations_libelle = $statut_demande_cotation->libelle;
                                }
                                $color = 'orange';
                                if($type_statut_mieux_disants_libelle === "Cotation rejetée"){
                                    $color = 'red';
                                }

                                if($type_statut_mieux_disants_libelle === "Cotation sélectionnée"){
                                    $color = 'green';
                                }

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

                                $title_show = "Visualiser";
                                $href_show = "/mieux_disants/show/".$mieux_disants_id_encryptString;
                                $display_show = "";
                                
                                if($type_statut_demande_cotations_libelle === "Coté" or $type_statut_demande_cotations_libelle === "Cotation en cours d'analyse" or $type_statut_demande_cotations_libelle === "Cotation sélectionnée"){
                                    if($type_profils_name === 'Gestionnaire des achats' or $type_profils_name === 'Responsable des achats' or $type_profils_name === 'Responsable DMP'){
                            
                                        $title_edit = "Modifier";
                                        $href_edit = "/mieux_disants/edit/".$mieux_disants_id_encryptString;
                                        $display_edit = "";
                                        
                                    }
                                }

                            ?>
                            <tr>
                                <td style="text-align: center; vertical-align:middle; width: 1px; white-space: nowrap;">{{ $i }}</td>
                                <td style="text-align: left; vertical-align:middle; font-weight:bold; width: 1px; white-space: nowrap; color: #7d7e8f">{{ $mieux_disant->entnum ?? '' }}</td>
                                <td style="vertical-align:middle">{{ $mieux_disant->denomination ?? '' }}</td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">

                                    {{ $responseFormat['montant_total_brut'] ?? '' }}

                                    @if(isset($responseFormat['montant_total_brut']))
                                        @if($responseFormat['montant_total_brut'] != '')
                                            <b>{{ ' '.$mieux_disant->symbole ?? '' }}</b>
                                        @endif
                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">

                                    {{ $responseFormat['remise_generale'] ?? '' }}

                                    @if(isset($responseFormat['remise_generale']))
                                        @if($responseFormat['remise_generale'] != '')
                                            <b>{{ ' '.$mieux_disant->symbole ?? '' }}</b>
                                        @endif
                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">

                                    {{ $responseFormat['montant_total_net'] ?? '' }}

                                    @if(isset($responseFormat['montant_total_net']))
                                        @if($responseFormat['montant_total_net'] != '')
                                            <b>{{ ' '.$mieux_disant->symbole ?? '' }}</b>
                                        @endif
                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">

                                    {{ $responseFormat['montant_tva'] ?? '' }}

                                    @if(isset($responseFormat['montant_tva']))
                                        @if($responseFormat['montant_tva'] != '')
                                            <b>{{ ' '.$mieux_disant->symbole ?? '' }}</b>
                                        @endif
                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">

                                    {{ $responseFormat['net_a_payer'] ?? '' }}

                                    @if(isset($responseFormat['net_a_payer']))
                                        @if($responseFormat['net_a_payer'] != '')
                                            <b>{{ ' '.$mieux_disant->symbole ?? '' }}</b>
                                        @endif
                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">

                                    {{ $responseFormat['montant_acompte'] ?? '' }}

                                    @if(isset($responseFormat['montant_acompte']))
                                        @if($responseFormat['montant_acompte'] != '')
                                            <b>{{ ' '.$mieux_disant->symbole ?? '' }}</b>
                                        @endif
                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle; text-align:right;">
                                    @if(isset($mieux_disant->created_at))
                                       {{ date("d/m/Y",strtotime($mieux_disant->created_at)) }} 
                                    @endif
                                </td>
                                <td style="width:1px; white-space:nowrap;vertical-align:middle; font-weight:bold; color:{{ $color ?? 'black' }}">
                                    @if($type_statut_mieux_disants_libelle != null)
                                       {{ $type_statut_mieux_disants_libelle ?? '' }} 
                                    @endif

                                    @if($type_statut_mieux_disants_libelle === null)
                                       {{ 'En attente d\'analyse' }} 
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