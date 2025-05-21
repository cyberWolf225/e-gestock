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
<div class="container">
    <br>
    <div class="row justify-content-center">
      <div class="col-md-12">

        <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
            <div class="col-lg-12">
                <a title="Enregistrer un nouveau fournisseur" class="btn btn-sm" href="{{ route("organisations.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
                    Enregistrer un fournisseur
                </a>
            </div>
        </div>

          <div class="card">
              <div class="card-header entete-table">{{ mb_strtoupper('Liste des fournisseurs') }}</div>
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
              <table id="example1" class="table table-striped bg-white" style="width: 100%">
                  <thead>
                  <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">#</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">N° CNPS</th>
                    <th style="vertical-align: middle; text-align: center;">RAISON SOCIALE</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">CONTACTS</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">SIGLE</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">STATUT</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">DATE</th>
                    <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">&nbsp;</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $i = 1; ?>
                  @foreach($organisations as $organisation)
                    <?php 

                      $organisations_id = Crypt::encryptString($organisation->id);

                      $updated_at = null;
                      if (isset($organisation->updated_at)) {
                          $updated_at = date('d/m/Y',strtotime($organisation->updated_at));
                      }


                      $title_show = "Voir les détails de l'organisation";
                      $href_show = "show/".$organisations_id;
                      $display_show = "none";

                      $title_edit = "Modifier les données de l'organisation";
                      $href_edit = "edit/".$organisations_id;
                      $display_edit = "";


                    ?>
                    <tr>
                      <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                      <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $organisation->entnum ?? '' }}</td>
                      <td style="vertical-align: middle; text-align: left;">{{ $organisation->denomination ?? '' }}</td>
                      <td style="vertical-align: middle; text-align: left; width:1px; white-space:nowrap">{{ $organisation->contacts ?? '' }} ( {{ $organisation->email ?? '' }} )</td>
                      <td style="vertical-align: middle; text-align: left; width:1px; white-space:nowrap">{{ $organisation->nom_prenoms ?? '' }}</td>
                      <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $organisation->libelle ?? '' }}</td>
                      <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $updated_at ?? '' }}</td>
                      <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">
                        <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm"><svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        </svg></a>
                        <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg></a>
                      </td>
                    </tr>
                    <?php $i++; ?>
                  @endforeach
                </tbody>

              </table>
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