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

    .td-center{
        text-align: center;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }

    .td{
        text-align: center;
        vertical-align:middle
    }

    .td-center-bold{
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .td-left{
        text-align: left;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }
  </style>

@endsection

@section('content')

<div class="container" style="color:black">
      <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
          <div class="col-lg-12">
              <a class="btn btn-sm" href="{{ route("agents.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
                  Créer un agent
              </a>
          </div>
      </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-style">
                <div class="card-header entete-table">{{ __('Liste des utilisateurs') }} 
                    <span style="float: right; margin-right: 20px;">Date : <strong>{{ date("d/m/Y") }}</strong></span>
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
                    <table id="example1" class="table table-striped bg-white data-table" style="width: 100%">
                      <thead>
                        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                          
                          <th class="td-center">#</th>
                          <th class="td-center">MLE</th>
                          <th class="td">NOM & PRENOM(S)</th>
                          <th class="td-center">E-MAIL</th>
                          <th class="td-center">STRUCTURE</th>
                          <th class="td-center">COMPTE</th>
                          <th class="td">PROFILS</th>
                          <th class="td-center">DATE</th>
                          <th class="td-center"></th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php $i = 1; ?>
                          @foreach($agents as $agent)
                              <?php
                                $compte = null;
                                $updated_at = null;

                                if (isset($agent->flag_actif)) {
                                    if ($agent->flag_actif === 1) {
                                        $compte = "Activé";
                                    }else {
                                        $compte = "Désactivé";
                                    }
                                }

                                if (isset($agent->updated_at)) {
                                    $updated_at = date("d/m/Y",strtotime($agent->updated_at));
                                }

                                $agents_id = Crypt::encryptString($agent->id);

                                $title_edit = "Modifier les données de l'utilisateur";
                                $href_edit = "edit/".$agents_id;
                                $display_edit = "";

                                
                                $nom_structure = null;

                                if (isset($agent->id)) {
                                   $structure = DB::table('agent_sections as ase')
                                        ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                                        ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                                        ->join('sections as s','s.id','=','ase.sections_id')
                                        ->join('structures as st','st.code_structure','=','s.code_structure')
                                        ->where('tsase.libelle','Activé')
                                        ->where('ase.agents_id',$agent->id)
                                        ->first();
                                        if ($structure != null) {
                                            $nom_structure = $structure->nom_structure;
                                        }
                                }
                              ?>

                                <tr>
                                    <td style="vertical-align: middle" class="td-center">{{ $i ?? '' }}</td>
                                    <td style="vertical-align: middle" class="td-center">{{ $agent->mle ?? '' }}</td>
                                    <td style="vertical-align: middle">{{ $agent->nom_prenoms ?? '' }}</td>
                                    <td class="td-center" style="vertical-align: middle; text-align:left">{{ $agent->email ?? '' }}</td>
                                    <td style="vertical-align: middle">{{ $nom_structure ?? '' }}</td>
                                    <td style="vertical-align: middle">{{ $compte ?? '' }}</td>
                                    <td style="vertical-align: middle">
                                        <?php
                                        $profils = DB::table('users as u')
                                        ->join('profils as p','p.users_id','=','u.id')
                                        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                        ->where('p.flag_actif',1)
                                        ->where('u.id',$agent->users_id)
                                        ->get();
                                        foreach ($profils as $profil) {
                                            echo '<svg style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                                            <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                                            </svg>'.$profil->name.'<br/>';
                                        }
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle" class="td-center">
                                        {{ $updated_at ?? '' }}
                                    </td>
                                    <td style="vertical-align: middle" class="td-center">
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
