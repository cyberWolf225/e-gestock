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
<br/>
<div class="container"> 
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('MODIFICATION DE LA PÉRIODE D\'INVENTAIRE') }} 
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

                    @if($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks')
                        <form method="POST" action="{{ route('inventaires.update') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group pt-2 pl-4">
                                        Début de période
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ml-0">
                                        
                                        <input autocomplete="off" type="number" name="id" class="form-control @error('id') is-invalid @enderror" value="{{ old('id') ?? $inventaires->id ?? '' }}"  autocomplete="id" style="display: none">

                                        <input autocomplete="off" type="date" name="debut_per" class="form-control @error('debut_per') is-invalid @enderror" value="{{ old('debut_per') ?? $inventaires->debut_per ?? '' }}"  autocomplete="debut_per">
                                    </div>
                                    @error('debut_per')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group pt-2 pl-4">
                                        <label>Fin de période</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <input autocomplete="off" type="date" name="fin_per" class="form-control @error('fin_per') is-invalid @enderror" value="{{ old('fin_per') ?? $inventaires->fin_per ?? '' }}"  autocomplete="fin_per">
                                    </div>
                                    @error('fin_per')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Modifier la période</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <br><br>
                    @endif
                    
                    <table id="example1" class="table table-striped table-bordered bg-white">
                        <thead>
                          <tr>
                            <th class="td-center-white-space" style="vertical-align: middle; width:1%">#</th>
                            <th class="td-center-white-space" style="vertical-align: middle">DÉBUT PÉRIODE</th>
                            <th class="td-center-white-space" style="vertical-align: middle">FIN PÉRIODE</th>
                            <th class="td-center-white-space" style="vertical-align: middle; text-align:center;">STATUT</th>
                            <th class="td-center-white-space" style="vertical-align: middle; text-align:center;">INTÉGRÉ</th>
                            <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">&nbsp;</th>
                          </tr>
                        </thead>
                        <tbody>  
                            <?php $i = 1; ?>
                            @foreach($list_inventaires as $list_inventaire)
                            <tr>
                                <?php 

                                    $inventaires_id = Crypt::encryptString($list_inventaire->id);
                                    
                                    $debut_per = null;

                                    if (isset($list_inventaire->debut_per)) {
                                        $debut_per = date('d/m/Y',strtotime($list_inventaire->debut_per));
                                    }

                                    $fin_per = null;

                                    if (isset($list_inventaire->fin_per)) {
                                        $fin_per = date('d/m/Y',strtotime($list_inventaire->fin_per));
                                    }
                                    $flag_valide = null;
                                    if ($list_inventaire->flag_valide == 1) {
                                        $flag_valide = 'Validé';
                                    }else{
                                        $flag_valide = 'Non validé';
                                    }

                                    $flag_integre = null;
                                    if ($list_inventaire->flag_integre == 1) {
                                        $flag_integre = 'Oui';
                                    }else{
                                        $flag_integre = 'Non';
                                    }

                                    $title_show = "Voir les details de l'inventaire";
                                    $title_edit = "";
                                    $title_transfert = "";
                                    $title_create = "";

                                    $href_show = "show/".$inventaires_id;
                                    $href_edit = "";
                                    $href_transfert = "";
                                    $href_create = "";

                                    $display_show = "";
                                    $display_edit = "none";
                                    $display_transfert = "none";
                                    $display_create = "none";

                                    $inventaire = DB::table('inventaires as i')
                                    ->where('i.id',$list_inventaire->id)
                                    ->whereNotIn('i.id',function($query){
                                        $query->select(DB::raw('ia.inventaires_id'))
                                            ->from('inventaire_articles as ia')
                                            ->whereRaw('i.id = ia.inventaires_id');
                                    })
                                    ->first();
                    

                                    if ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks') {
                                        
                                        $title_show = "Voir les details de l'inventaire";
                                        
                                        $title_transfert = "";
                                        $title_create = "Faire l'inventaire";

                                        $display_edit = "";
                                        $display_create = "";

                                        $href_transfert = "";
                                        $href_create = "/inventaire_articles/create/".$inventaires_id;

                                        $display_transfert = "none";

                                        $href_show = "show/".$inventaires_id;

                                        if ($inventaire === null) {

                                            $title_edit = "Modifier l'inventaire";
                                            $href_edit = "edit/".$inventaires_id;
                                            $display_edit = "none";

                                        }


                                    }

                                ?>
                                <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $i ?? '' }}</th>
                                <th style="vertical-align: middle; text-align:center; width:15%;">{{ $debut_per ?? '' }}</th>
                                <th style="vertical-align: middle; text-align:center; width:15%;">{{ $fin_per ?? '' }}</th>
                                <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $flag_valide ?? '' }}</th>
                                <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $flag_integre ?? '' }}</th>
                                <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">
                                    <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm"><svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg></a>
                                        <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg></a>
                                        <a style="display:{{ $display_transfert }};" title="{{ $title_transfert }}" href="{{ $href_transfert }}" class="btn btn-success btn-sm"><svg style="color:black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg></a>
                                        <a style="display:{{ $display_create }};" title="{{ $title_create }}" href="{{ $href_create }}" class="btn btn-secondary btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                                        <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                                        <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/>
                                        <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/>
                                    </svg></a>
                                </th>
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
