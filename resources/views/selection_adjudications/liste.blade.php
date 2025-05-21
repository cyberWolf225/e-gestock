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
          <div class="card bg-white">
            <div class="card-header entete-table">{{ __('LISTE DES DEMANDES D\'ACHAT') }} 
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
            <table id="example1" class="table table-striped">
                <thead>
                  <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap" >#</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap;" >N° CNPS</th>
                    <th style="vertical-align: middle; text-align:center;" >RAISON SOCIALE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap" >NET À PAYER</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap" >ACOMPTE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap" >ÉVALUATION</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap" colspan="1" >&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=0; ?>
                  @foreach($cotation_fournisseurs as $cotation_fournisseur)
                  <?php
                  $cotation_fournisseurs_id = Crypt::encryptString($cotation_fournisseur->id);

                  $color = "red";
                  
                  $selection_adjudication = DB::table('selection_adjudications as sa')
                  ->where('cotation_fournisseurs_id',$cotation_fournisseur->id)->get();

                  if (count($selection_adjudication)==1) {
                    $color = "green";
                  }
                  
                  $i++;
                    $detail_adjudications = DB::select("SELECT * FROM detail_adjudications da, critere_adjudications ca WHERE ca.id = da.critere_adjudications_id AND da.cotation_fournisseurs_id = '".$cotation_fournisseur->id."' ");

                      $net_a_payer = number_format((float)$cotation_fournisseur->net_a_payer, 2, '.', '');

                      $block_net_a_payer = explode(".",$net_a_payer);
                      
                      $net_a_payer_partie_entiere = null;
                      
                      if (isset($block_net_a_payer[0])) {
                          $net_a_payer_partie_entiere = $block_net_a_payer[0];
                      }

                      $net_a_payer_partie_decimale = null;
                      if (isset($block_net_a_payer[1])) {
                          $net_a_payer_partie_decimale = $block_net_a_payer[1];
                      }

                      

                      $montant_acompte = number_format((float)$cotation_fournisseur->montant_acompte, 2, '.', '');

                      $block_montant_acompte = explode(".",$montant_acompte);
                      
                      $montant_acompte_partie_entiere = null;
                      
                      if (isset($block_montant_acompte[0])) {
                          $montant_acompte_partie_entiere = $block_montant_acompte[0];
                      }

                      $montant_acompte_partie_decimale = null;
                      if (isset($block_montant_acompte[1])) {
                          $montant_acompte_partie_decimale = $block_montant_acompte[1];
                      }
                  
                  ?>
                    <tr style="cursor: pointer">
                      <td style="vertical-align: middle;  width:1px; white-space:nowrap; text-align:center; width:1%"> <svg xmlns="http://www.w3.org/2000/svg" style="color: @if($color) {{ $color }} @endif " width="40" height="40" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                        <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                      </svg></td>
                      <td style="vertical-align: middle;  width:1px; white-space:nowrap; text-align:center">{{ $cotation_fournisseur->entnum ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:left">{{ $cotation_fournisseur->denomination ?? '' }}</td>
                      <td style="vertical-align: middle;  width:1px; white-space:nowrap; text-align:right">                      
                        @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                        {{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}
                            @else
                            {{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}
                        @endif {{ $cotation_fournisseur->symbole ?? $cotation_fournisseur->code ?? '' }}</td>
                      <td style="vertical-align: middle;  width:1px; white-space:nowrap; text-align:right">
                        @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                        {{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}
                            @else
                            {{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}
                        @endif
                        {{ $cotation_fournisseur->symbole ?? $cotation_fournisseur->code ?? '' }}</td>
                      <td style="vertical-align: middle;  width:1px; white-space:nowrap;">
                        @foreach($detail_adjudications as $detail_adjudication)
                            <?php 
                              $criteres = DB::select("SELECT * FROM criteres WHERE id ='".$detail_adjudication->criteres_id."' LIMIT 1 ");

                              foreach ($criteres as $critere) {
                                $libelle = $critere->libelle;
                                $mesure = $critere->mesure;

                                if ($mesure === "FCFA") {
                                  $mesure = $cotation_fournisseur->symbole ?? $cotation_fournisseur->code ?? ''; 
                                }
                              }

                            ?>
                            <u>
                              {{ $libelle }}</u> : <strong style="color: red">{{ $detail_adjudication->valeur }}</strong> {{ $mesure }} <br/>
                        @endforeach
                      </td>

                      <td title="Voir les details" style="vertical-align: middle; text-align:center; width:1%" onclick="document.location='/../selection_adjudications/create/{{ $cotation_fournisseurs_id }}'" style="text-align: center">
                        <a class="btn btn-info btn-sm">
                          <svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                          </svg>
                        </a>
                        
                    </td>
                    </tr>
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