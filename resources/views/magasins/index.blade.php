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

    <div class="row justify-content-center">
      <div class="col-md-12">
          <div class="card">
              <div class="card-header entete-table">{{ __('Liste des magasins') }}</div>
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
              <table id="example1" class="table table-striped table-bordered bg-white data-table" style="width: 100%">
                    <thead>
                        <tr>
                        <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">#</th>
                        <th style="vertical-align: middle; text-align: center;">Magasin</th>
                        <th style="vertical-align: middle; text-align: center;">Depôt</th>
                        <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">Téléphone</th>
                        <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">Ville</th>
                        <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">Responsable</th>
                        <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">Date</th>
                        <th title="Enregistrer un nouveau magasin" style="cursor: pointer; color:blue; vertical-align: middle; text-align: center; width:1px; white-space:nowrap" onclick="document.location='create'" style="text-align: center" scope="col"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        @foreach($magasins as $magasin)
                            <?php

                            $magasins_id = Crypt::encryptString($magasin->id);
                            
                            $responsable = null;

                            if (isset($magasin->ref_depot)) {

                                $responsable_depot = DB::table('users as u')
                                    ->join('profils as p','p.users_id','=','u.id')
                                    ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                    ->join('agents as a','a.id','=','u.agents_id')
                                    ->join('agent_sections as ase','ase.agents_id','=','a.id')
                                    ->join('sections as s','s.id','=','ase.sections_id')
                                    ->join('requisitions as r','r.code_structure','=','s.code_structure')
                                    ->join('structures as st','st.code_structure','=','r.code_structure')
                                    ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                                    ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                                    ->where('tp.name','Responsable des stocks')
                                    ->where('tsas.libelle','Activé')
                                    ->where('p.flag_actif',1)
                                    ->whereRaw('st.code_structure = r.code_structure')
                                    ->where('st.ref_depot', $magasin->ref_depot)
                                    ->limit(1)
                                    ->first();
                                if ($responsable_depot!=null) {
                                    $responsable = $responsable_depot->nom_prenoms;
                                }

                            }

                            $updated_at = null;

                            if (isset($magasin->updated_at)) {
                                $updated_at = date("d/m/Y",strtotime($magasin->updated_at));
                            }

                            $title_edit = "Modifier les données du magasin";
                            $href_edit = "edit/".$magasins_id;
                            $display_edit = "";

                            
                            
                            ?>
                            <tr>
                                <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: left"> <strong style="color: red">{{ $magasin->ref_magasin ?? '' }}</strong> - {{ $magasin->design_magasin ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: left"><strong style="color: red">{{ $magasin->ref_depot ?? '' }}</strong> - {{ $magasin->design_dep ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $magasin->tel_dep ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: left; width:1px; white-space:nowrap">{{ $magasin->nom_ville ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: left; width:1px; white-space:nowrap">{{ $responsable ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">{{ $updated_at ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">
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
