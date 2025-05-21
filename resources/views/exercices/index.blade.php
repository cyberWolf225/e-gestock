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
          <div class="card">
              <div class="card-header entete-table">{{ __('LISTE DES EXERCICES') }}</div>
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
                @if(isset($acces_create))
                    <form method="POST" action="{{ route('exercices.store') }}" >
                        @csrf
                    <table width="100%">
                        <tr>
                            <td style="width: 1px; white-space:nowrap">Exercice</td>
                            <td width="20%">
                                <div>
                                <input placeholder="Veuillez saisir un nouveau exercice" onkeypress="validate(event)" required maxlength="4" minlength="4" autocomplete="off" type="text" value="{{ old('exercice')  }}" name="exercice" class="form-control form-control-sm @error('exercice') is-invalid @enderror">
                                    @error('exercice')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-success btn-sm">Enregistrer</button>
                            </td>
                        </tr>
                    </table>
                    </form>
                    <br>
                @endif
               <table id="example1" class="table table-striped table-bordered bg-white" style="width: 100%">
                  <thead>
                          <tr>
                            <th style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">#</th>
                            <th style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">Exercice</th>
                            <th style="text-align: center; vertical-align:middle">Intervenant</th>
                            <th style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">Statut</th>
                            <th style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">Date début</th>
                            <th style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">Date fin</th>
                            
                            <th style="text-align: center" style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap"></th>
                          </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            @foreach($exercices as $exercice)

                                <?php
                                    
                                $statut_exercice = DB::table('exercices as e')
                                ->join('statut_exercices as se','se.exercice','=','e.exercice')
                                ->join('type_statut_exercices as tse','tse.id','=','se.type_statut_exercices_id')
                                ->join('profils as p','p.id','=','se.profils_id')
                                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                ->join('users as u','u.id','=','p.users_id')
                                ->join('agents as a','a.id','=','u.agents_id')
                                ->select('e.id','tse.libelle','se.exercice','se.id as statut_exercices_id','a.nom_prenoms','se.date_debut','se.date_fin')
                                ->orderByDesc('se.id')
                                ->where('se.exercice',$exercice->exercice)
                                ->limit(1)
                                ->first();

                                $date_debut = null;
                                $date_fin = null;
                                $nom_prenoms = null;
                                $exercice_libelle = $exercice->exercice;
                                $libelle = 'Fermeture';

                                if ($statut_exercice != null) {

                                    $exercice_libelle = $statut_exercice->exercice;
                                    $nom_prenoms = $statut_exercice->nom_prenoms;
                                    $libelle = $statut_exercice->libelle;

                                    if (isset($statut_exercice->date_debut)) {
                                        $date_debut = date("d/m/Y",strtotime($statut_exercice->date_debut));
                                    }

                                    

                                    if (isset($statut_exercice->date_fin)) {
                                        $date_fin = date("d/m/Y",strtotime($statut_exercice->date_fin));
                                    }
                                }
                                
                                $exercices_id = Crypt::encryptString($exercice->id);

                                $title_edit = "Modifier l'exercice";
                                $href_edit = "edit/".$exercices_id;
                                $display_edit = "";

                                if($libelle === 'Fermeture'){ $libelle = 'Fermé'; }
                                if($libelle === 'Ouverture'){ $libelle = 'Ouvert'; }
                                
                            
                            ?>
                            <tr>
                                <td style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                                <td style="text-align: center; vertical-align:middle">{{ $exercice_libelle ?? '' }}</td>
                                <td style="text-align: left; vertical-align:middle; width:1px; white-space:nowrap">
                                    {{ $nom_prenoms ?? '' }}
                                </td>
                                <td style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">
                                    {{ $libelle ?? '' }}                             
                                </td>
                                <td style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">
                                    {{ $date_debut ?? '' }}
                                </td>
                                <td style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">{{ $date_fin ?? '' }}</td>
                                <td style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">
                                    @if($libelle === 'Ouvert')
                                        <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit."/".Crypt::encryptString('Fermeture') }}" class="btn btn-danger btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-lock2-fill" viewBox="0 0 16 16">
                                            <path d="M7 7a1 1 0 0 1 2 0v1H7V7z"/>
                                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM10 7v1.076c.54.166 1 .597 1 1.224v2.4c0 .816-.781 1.3-1.5 1.3h-3c-.719 0-1.5-.484-1.5-1.3V9.3c0-.627.46-1.058 1-1.224V7a2 2 0 1 1 4 0z"/>
                                               
                                            </svg>
                                            Clôturer l'exercice 
                                        </a>
                                    @elseif($libelle === 'Fermé')
                                        <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit."/".Crypt::encryptString('Ouverture') }}" class="btn btn-success btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-play-fill" viewBox="0 0 16 16">
                                        <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM6 6.883a.5.5 0 0 1 .757-.429l3.528 2.117a.5.5 0 0 1 0 .858l-3.528 2.117a.5.5 0 0 1-.757-.43V6.884z"/>
                                        </svg>Ouvrir l'exercice</a>
                                    @endif
                                        
                                </td>
                              </tr>
                              <?php $i++; ?>
                            @endforeach
                        </tbody>
        
                </table>
                </div>
            </div>
        </div>

        <script>
            function validate(evt) {
                var theEvent = evt || window.event;
        
                // Handle paste
                if (theEvent.type === 'paste') {
                    key = event.clipboardData.getData('text/plain');
                } else {
                // Handle key press
                    var key = theEvent.keyCode || theEvent.which;
                    key = String.fromCharCode(key);
                }
                var regex = /[0-9]|\./;
                if( !regex.test(key) ) {
                    theEvent.returnValue = false;
                    if(theEvent.preventDefault) theEvent.preventDefault();
                }
            }
        </script>

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
