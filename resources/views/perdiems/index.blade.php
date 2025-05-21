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
  <br/>
  <div class="container">
    
    @if(isset($acces_create))
          <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
          <div class="col-lg-12">
          

          <a class="btn btn-sm" href="{{ route("perdiems.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
            Créer une demande de perdiem
        </a>

          </div>
          </div>
        @endif
      <div class="card">

        

        <div class="card-header entete-table">{{ __(strtoupper('Liste des perdiems')) }}
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-12">



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
                
                <table id="example1" class="table table-striped bg-white" style="width: 100%">
                  <thead>
                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                      
                      <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">#</th>
                      <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">N° PERDIEM</th>
                      
                      <th style="vertical-align: middle; text-align: center">INTITULÉ</th>
                      <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">EXERCICE</th>
                      <th style="vertical-align: middle ;text-align: center; width:1px; white-space:nowrap text-align:center">MONTANT TOTAL</th>
                      <th style="vertical-align: middle; text-align: center; width:20%;">COMPTE</th>
                      <th style="vertical-align: middle; text-align: center;">STATUT</th>
                      <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;"> 
                      </th>
                    </tr>
                  </thead>
                  <tbody>

                    <?php $i = 1; ?>
                    @foreach($perdiems as $perdiem) 

                        <?php

                        $perdiems_id = Crypt::encryptString($perdiem->id);
                        

                        $libelle_moyen_paiement = null;
                        $terminer = null;

                        $title_show = "Voir les details";
                        $title_edit = "";
                        $title_transfert = "";
                        $title_create = "";
                        

                        $href_show = "show/".$perdiems_id;
                        $href_edit = "";
                        $href_transfert = "";
                        $href_create = "";

                        $display_show = "";
                        $display_edit = "none";
                        $display_transfert = "none";
                        $display_create = "none";

                        $title_print = "Imprimer la demande de perdiems";
                        $href_print = "";
                        $display_print = "none";

                          $libelle = null;

                          $statut_perdiem = DB::table('statut_perdiems as sp')
                          ->join('type_statut_perdiems as tsp','tsp.id','=','sp.type_statut_perdiems_id')
                          ->where('sp.perdiems_id',$perdiem->id)
                          ->orderByDesc('sp.id')
                          ->limit(1)
                          ->first();

                          if ($statut_perdiem!=null) {
                              $libelle = $statut_perdiem->libelle;
                          }


                          if ($libelle === 'Soumis pour validation' or $libelle === 'Annulé (Gestionnaire des achats)' or $libelle === 'Annulé (Responsable des achats)') {

                            if ($type_profils_name === 'Gestionnaire des achats') {

                              $title_edit = "Modifier ou Transmettre la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "Transmettre la demande de perdiem au Responsable des achats";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }
                            
                          }elseif ($libelle === 'Transmis (Responsable des achats)' or $libelle === 'Annulé (Responsable DMP)') {

                            if ($type_profils_name === 'Responsable des achats') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Transmis (Responsable DMP)' or $libelle === 'Annulé (Responsable contrôle budgetaire)') {

                            if ($type_profils_name === 'Responsable DMP') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Transmis (Responsable contrôle budgetaire)' or $libelle === 'Annulé (Chef Département DCG)') {

                            if ($type_profils_name === 'Responsable contrôle budgetaire') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Transmis (Chef Département DCG)' or $libelle === 'Annulé (Responsable DCG)') {

                            if ($type_profils_name === 'Chef Département DCG') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Transmis (Responsable DCG)' or $libelle === 'Annulé (Directeur Général Adjoint)') {

                            if ($type_profils_name === 'Responsable DCG') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Transmis (Directeur Général Adjoint)') {

                            if ($type_profils_name === 'Directeur Général Adjoint') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Validé' or $libelle === 'Édité') {

                            if ($type_profils_name === 'Gestionnaire des achats') {

                              $title_edit = "Éditer la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                            }

                          }elseif ($libelle === 'Transmis (Responsable DFC)') {

                            if ($type_profils_name === 'Responsable DFC') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }elseif ($libelle === 'Accord pour paiement') {

                            if ($type_profils_name === 'Responsable Caisse') {

                              $title_edit = "Valider la demande de perdiem";
                              $href_edit = "edit/".$perdiems_id;
                              $display_edit = "";

                              $title_transfert = "";
                              $href_transfert = "send/".$perdiems_id;
                              $display_transfert = "none";

                            }

                          }
                           

                          $statut_perdiem_edit = DB::table('statut_perdiems as sdp')
                          ->join('type_statut_perdiems as tsp','tsp.id','=','sdp.type_statut_perdiems_id')
                          ->where('tsp.libelle','Validé') //Édité Retiré (Frs.)
                          ->orderByDesc('sdp.id')
                          ->limit(1)
                          ->select('sdp.id')
                          ->where('sdp.perdiems_id',$perdiem->id)
                          ->first();

                          if($statut_perdiem_edit != null){

                            $title_print = "Imprimer la demande";

                            $path = 'storage/documents/perdiems/'.$perdiem->num_pdm.'.pdf';

                            $public_path = str_replace("/","\\",public_path($path));

                            $type_operations_libelle = Crypt::encryptString('Perdiems');

                            if(file_exists($public_path)){
                              $href_print = '/documents/'.$perdiems_id.'/'.$type_operations_libelle;
                              $display_print = "";
                            }else{
                              $href_print = "/print/dp/".$perdiems_id;
                              $display_print = "none";
                            }                              
                            

                          }

                        ?>
                      <tr>
                        <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap; font-weight:bold; color:#033d88">{{ $perdiem->num_pdm ?? '' }}</td>
                        
                        <td style="vertical-align: middle; text-align:left; ">{{ mb_strtoupper($perdiem->libelle ?? '') }}</td>

                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $perdiem->exercice ?? '' }}</td>
                        
                        <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ strrev(wordwrap(strrev($perdiem->montant_total ?? ''), 3, ' ', true)) }}</td>
                        <td style="vertical-align: middle; text-align:left; width:20%">{{ $perdiem->ref_fam ?? '' }} - {{ mb_strtoupper($perdiem->design_fam ?? '') }}</td>
                        <td style="vertical-align: middle; text-align:left; color:red; font-weight:bold">{{ $libelle ?? '' }}</td>
                        
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">

                          <a style="display:{{ $display_show }}" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm"><svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                          </svg></a>

                          <a style="display:{{ $display_edit }}" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                          <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                          </svg></a>

                          <a style="display:{{ $display_transfert }}" title="{{ $title_transfert }}" href="{{ $href_transfert }}" class="btn btn-success btn-sm"><svg style="color:black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                          </svg></a>

                          <a style="display:{{ $display_create }}" title="{{ $title_create }}" href="{{ $href_create }}" class="btn btn-secondary btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                          <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                          <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/>
                          <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/>
                          </svg></a>

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