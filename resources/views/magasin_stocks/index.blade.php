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
<div class="container" style="color:black;">
<br>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header entete-table">{{ __(strtoupper('Magasin de stockage')) }}</div>
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
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">#</th>
                    <th style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap">RÉF. ARTICLE</th>
                    <th style="vertical-align: middle; text-align:left">DÉSIGNATION ARTICLE</th>
                    <th style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap">RÉF. FAMILLE</th>
                    <th style="vertical-align: middle; text-align:left">DÉSIGNATION FAMILLE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">QTÉ EN STOCK</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">CMUP</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">DATE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">&nbsp;</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $i = 1; ?>
                  @foreach($magasin_stocks as $magasin_stock)
                    <?php 
                      $updated_at = null;
                      if (isset($magasin_stock->updated_at)) {
                          $updated_at = date('d/m/Y',strtotime($magasin_stock->updated_at));
                      }

                      $magasin_stocks_id = Crypt::encryptString($magasin_stock->id);

                      $title_show = "Voir les details des mouvements de cet articles";
                      $href_show = "show/".$magasin_stocks_id;
                      $display_show = "";
                    ?>
                    <tr style="color: #7d7e8f">
                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>

                      <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap"><strong style="color: #7d7e8f">{{ $magasin_stock->ref_articles ?? '' }}</strong></td>

                      <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap">{{ $magasin_stock->design_article ?? '' }}</td>

                      <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap"><strong style="color: #7d7e8f">{{ $magasin_stock->ref_fam ?? '' }}</strong></td>

                      <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap">{{ $magasin_stock->design_fam ?? '' }}</td>

                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ strrev(wordwrap(strrev($magasin_stock->qte ?? ''), 3, ' ', true)) }}</td>
                      <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ strrev(wordwrap(strrev($magasin_stock->cmup ?? ''), 3, ' ', true)) }}</td>
                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $updated_at ?? '' }}</td>
                      <td style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">
                                    
                        <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        </svg></a>   
                        
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