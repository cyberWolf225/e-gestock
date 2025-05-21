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

    .td-center-white-space{
        text-align: center;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }

    .td-center{
        text-align: center;
        vertical-align: middle;
    }

    .td-center-bold{
        text-align: center;
        vertical-align: middle;
        font-weight: bold
    }

    .td-left{
        text-align: left;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }
  </style>

@endsection

@section('content')
<br>
<div class="container"> 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('INVENTAIRE RÉALISÉ') }} 
                    <span style="float: right; margin-right: 20px;">DÉPÔT : <strong>{{ $design_dep ?? '' }}</strong></span>
                </div> 

                <div class="card-body">
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
                      <div class="row">
                          <div class="col-md-3">
                              <div class="form-group pt-2 pl-4">
                                DÉBUT DE PÉRIODE : <strong>{{ date("d/m/Y", strtotime($debut_per ?? ''))   }}</strong>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group pt-2 pl-4">
                                FIN DE PÉRIODE : <strong>{{ date("d/m/Y", strtotime($fin_per ?? ''))   }}</strong>
                              </div>
                          </div>
                      </div>

                    <br><br>
                    <table id="example1" class="table table-striped table-bordered bg-white">
                        <thead>
                          <tr id="">
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">RÉF. ARTICLE</th>
                            <th style="vertical-align: middle; text-align:center ;width:80%;">DÉSIGNATION</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">QTÉ THÉO.</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">QTÉ PHYS.</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">ECART</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">CMUP</th>
                            <th style="vertical-align: middle; text-align:center ; width:1px; white-space:nowrap">JUSTIFICATIF</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">DATE</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">STATUT</th>
                            <th style="vertical-align: middle; text-align:center ;width:1px; white-space:nowrap">INTÉGRÉ</th>
                            <th style="vertical-align: middle; text-align:center ;width:10%;">FAMILLE</th>
                            <th style="vertical-align: middle; text-align:center ;width:5%;">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>  
                            @foreach($inventaires as $inventaire)

                                <?php
                                    $inventaires_id = Crypt::encryptString($inventaire->id);

                                    $updated_at = null;

                                    if (isset($inventaire->updated_at)) {
                                        $updated_at = date('d/m/Y',strtotime($inventaire->updated_at));
                                    }

                                    $flag_valide = null;
                                    if ($inventaire->flag_valide == 1) {
                                        $flag_valide = 'Validé';
                                    }else{
                                        $flag_valide = 'Non validé';
                                    }

                                    $flag_integre = null;
                                    if ($inventaire->flag_integre == 1) {
                                        $flag_integre = 'Oui';
                                    }else{
                                        $flag_integre = 'Non';
                                    }

                                    $cmup_inventaire = null;
                                    if (isset($inventaire->cmup_inventaire)) {
                                        $cmup_inventaire = $inventaire->cmup_inventaire;
                                    }


                                    $title_edit = "";
                                    $href_edit = "";
                                    $display_edit = "none";

                                    $title_transfert = "";
                                    $href_transfert = "";
                                    $display_transfert = "none";
                                        

                                    if ($type_profils_name === 'Gestionnaire des stocks') {
                                        
                                        
                                        $title_transfert = "";
                                        $href_transfert = "";
                                        $display_transfert = "none";

                                        if ($inventaire->flag_integre != 1) {

                                            $title_edit = "Modifier l'inventaire";
                                            $href_edit = "/inventaire_articles/edit/".$inventaires_id;
                                            $display_edit = "";

                                        }

                                        


                                    }

                                    if ($type_profils_name === 'Responsable des stocks') {

                                        if ($inventaire->flag_integre != 1) {

                                            $title_edit = "Modifier l'inventaire";
                                            $href_edit = "/inventaire_articles/edit/".$inventaires_id;
                                            $display_edit = "";

                                        }

                                    }
                                ?>
                                <tr>
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $inventaire->ref_articles ?? '' }}</td>   
                                    <td style="vertical-align: middle">{{ $inventaire->design_article ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $inventaire->qte_theo ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $inventaire->qte_phys ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $inventaire->ecart ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">@if(isset($cmup_inventaire)) {{ strrev(wordwrap(strrev($cmup_inventaire ?? ''), 3, ' ', true)) }} @endif </td>   
                                    <td style="vertical-align: middle; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $inventaire->justificatif ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $updated_at ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $flag_valide ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">{{ $flag_integre ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">{{ $inventaire->design_fam ?? '' }}</td>   
                                    <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                        <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                        </svg></a>
                                            <a style="display:{{ $display_transfert }};" title="{{ $title_transfert }}" href="{{ $href_transfert }}" class="btn btn-success btn-sm"><svg style="color:black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                        </svg></a>    
                                    </td>    
                                </tr> 

                            @endforeach
                                                   
                        </tbody>
                        {{-- <tfoot>
                            <tr>
                                <td colspan="3">
                                    Début de période : <strong>{{ date("d/m/Y", strtotime($debut_per ?? ''))   }}</strong>
                                </td>
                                <td colspan="3">
                                    Fin de période : <strong>{{ date("d/m/Y", strtotime($fin_per ?? ''))   }}</strong>
                                </td>
                                <td colspan="9">
                                    Famille d'article : <strong>{{ $inventaire->design_fam ?? '' }}</strong>
                                </td>
                            </tr>
                        </tfoot> --}}
                      </table>


                </div>
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
