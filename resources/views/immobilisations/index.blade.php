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

    @if(isset($acces_create))
        <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
            <div class="col-lg-12">
                <a title="Enregistrer une nouvelle immobilisation" class="btn btn-success" href="/immobilisations/create">
                    Ajouter
                </a>
            </div>
        </div>
        @else
        <br>
    @endif

    <div class="container">
        
        <div class="row">
            <div class="col-12">

                <div class="card-header entete-table">{{ __('LISTE DES IMMOBILISATIONS') }}
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

                    <table id="example1" class="table table-striped table-bordered bg-white data-table" style="width: 100%">
                    <thead>
                    <tr style="color: #7d7e8f">
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">#</th>
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">N° DEMANDE</th>
                      <th style="vertical-align: middle; text-align:left" width="60%">INTITULE</th>
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">DATE</th>
                      <th style="vertical-align: middle; text-align:left; width: 1%; white-space: nowrap;">STATUT</th>
                      <th style="vertical-align: middle; text-align:left" width="30%">PILOTE AEE</th>

                      <th style="vertical-align: middle; text-align:center" width="20%">
                      </th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; ?>
                        @foreach($immobilisations as $immobilisation)
                        
                            <?php 
                                //statut de la demande 

                                $immobilisations_id = Crypt::encryptString($immobilisation->id);

                                $statut = null;

                                $title_show = "Voir les details de la demande d'équipements";
                                $title_edit = "";
                                $title_transfert = "";

                                $href_show = "show/".$immobilisations_id;
                                $href_edit = "";
                                $href_transfert = "";

                                $display_show = "";
                                $display_edit = "none";
                                $display_transfert = "none";

                                $statut_immobilisation = DB::table('statut_immobilisations as sr')
                                    ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                                    ->where('sr.immobilisations_id',$immobilisation->id)
                                    ->orderByDesc('sr.id')
                                    ->limit(1)
                                    ->first();
                                    if ($statut_immobilisation!=null) {
                                        $statut = $statut_immobilisation->libelle;

                                        if (isset($type_profils_name)) {
                                            if ($type_profils_name === 'Gestionnaire des stocks' or $type_profils_name === 'Responsable des stocks') {

                                                $detail_immobilisation = DB::table('detail_immobilisations as di')
                                                ->where('di.immobilisations_id',$immobilisation->id)
                                                ->select(DB::raw('SUM(di.qte) as qte_totale_demande'),DB::raw('SUM(di.qte_sortie) as qte_totale_sortie'))
                                                ->groupBy('di.immobilisations_id')
                                                ->first();

                                                if ($detail_immobilisation != null) {
                                                    if($detail_immobilisation->qte_totale_demande > $detail_immobilisation->qte_totale_sortie){

                                                        if ($detail_immobilisation->qte_totale_sortie != 0) {
                                                            $statut = "Sortie partielle (Stock)";
                                                        }
                                                        

                                                    }
                                                }

                                            }elseif ($type_profils_name === 'Gestionnaire entretien' or $type_profils_name === 'Pilote AEE') {
                                                $detail_immobilisation = DB::table('detail_immobilisations as di')
                                                ->where('di.immobilisations_id',$immobilisation->id)
                                                ->select(DB::raw('SUM(di.qte_sortie) as qte_totale_sortie'))
                                                ->groupBy('di.immobilisations_id')
                                                ->first();

                                                if ($detail_immobilisation != null) {

                                                    $qte_affectee = count(DB::table('affectations as a')
                                                    ->join('detail_immobilisations as di','di.id','=','a.detail_immobilisations_id')
                                                    ->where('di.immobilisations_id',$immobilisation->id)
                                                    ->get());

                                                    if($detail_immobilisation->qte_totale_sortie > $qte_affectee){

                                                        if ($qte_affectee != 0) {

                                                            if ($type_profils_name === 'Pilote AEE'){

                                                                if ($statut != 'Réception invalidée (Pilote AEE)') {
                                                                    $statut = "Mise à disposition (Partiel)";
                                                                }

                                                            }else{
                                                                $statut = "Mise à disposition (Partiel)";
                                                            }
                                                            
                                                            
                                                        }

                                                        

                                                    }
                                                }
                                            }
                                        }
                                    }

                                if ($statut === "Soumis pour validation" or $statut === "Annulé (Pilote AEE)" or $statut === "Invalidé (Responsable section entretien)") {

                                    if ($type_profils_name === 'Pilote AEE') {
                                            
                                            $title_edit = "Modifier / Transmettre la demande d'équipements au Responsable de la section entretien";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Transmis (Responsable section entretien)") {

                                    if ($type_profils_name === 'Responsable section entretien') {
                                            
                                            $title_edit = "Analyser l'expression du besoin";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Transmis (Gestionnaire entretien)" or $statut === "Enquête de vérification" or $statut === "Enquête de vérification (Invalidé)") {

                                    if ($type_profils_name === 'Gestionnaire entretien') {
                                            
                                            $title_edit = "Rédiger l'enquête de vérification du besoin";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Enquête de vérification (Transmis)" or $statut === "Enquête de vérification (Validé)" or $statut === "Invalidé (Responsable des stocks)") {

                                    if ($type_profils_name === 'Responsable section entretien') {
                                            
                                            $title_edit = "Analyser l'enquête de vérification du besoin";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Transmis (Responsable des stocks)" or $statut === "Invalidé (Gestionnaire des stocks)") {

                                    if ($type_profils_name === 'Responsable des stocks') {
                                            
                                            $title_edit = "Analyser l'expression du besoin";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Transmis (Gestionnaire des stocks)" or $statut === "Demande d'accord" or $statut === "Demande d'accord (Invalidé)") {

                                    if ($type_profils_name === 'Gestionnaire des stocks') {
                                            
                                            $title_edit = "Élaborer la demande d'accord pour attribution";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Demande d'accord (Transmis)" or $statut === "Demande d'accord (Validé)") {

                                    if ($type_profils_name === 'Responsable des stocks') {
                                            
                                            $title_edit = "Analyser la demande d'accord pour attribution";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Transmis (Responsable DMP)") {

                                    if ($type_profils_name === 'Responsable DMP') {
                                            
                                            $title_edit = "Analyser la demande d'accord pour attribution";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Accord pour attribution" or $statut === "Sortie partielle (Stock)" or $statut === "Réception invalidée (Logistique)") {

                                    if ($type_profils_name === 'Gestionnaire des stocks') {
                                            
                                            $title_edit = "Sortir des équipements du stock";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }elseif ($type_profils_name === 'Responsable Logistique') {
                                            
                                        if ($statut === "Sortie partielle (Stock)") {

                                            $title_edit = "Valider la réception d'équipements";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        }
                                        

                                    }

                                }elseif ($statut === "Sortie partielle (Stock)" or $statut === "Sortie totale (Stock)") {

                                    if ($type_profils_name === 'Responsable Logistique' or $type_profils_name === 'Responsable section entretien') {
                                            
                                            $title_edit = "Valider la réception d'équipements";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Réception équipement (Logistique)" or $statut === "Mise à disposition (Partiel)") {

                                    if ($type_profils_name === 'Gestionnaire entretien') {
                                            
                                            $title_edit = "Préparer et mettre à disposition d’équipements";
                                            $href_edit = "edit/".$immobilisations_id;
                                            $display_edit = "";
                                        

                                    }elseif ($type_profils_name === 'Pilote AEE') {
                                            
                                        $title_edit = "Réception de l'équipement";
                                        $href_edit = "edit/".$immobilisations_id;
                                        $display_edit = "";
                                        

                                    }

                                }elseif ($statut === "Mise à disposition") {

                                    if ($type_profils_name === 'Pilote AEE') {
                                            
                                        $title_edit = "Réception de l'équipement";
                                        $href_edit = "edit/".$immobilisations_id;
                                        $display_edit = "";

                                    }

                                }elseif ($statut === "Réception invalidée (Pilote AEE)") {

                                    if ($type_profils_name === 'Gestionnaire entretien') {
                                            
                                        $title_edit = "Préparer et mettre à disposition d’équipements";
                                        $href_edit = "edit/".$immobilisations_id;
                                        $display_edit = "";

                                    }

                                }



                            
                            
                            ?>

                            <tr>
                                <td style="vertical-align: middle" class="td-center">{{ $i ?? '' }}</td>
                                <td style="vertical-align: middle; color: #7d7e8f; text-align:left" class="td-center-bold">{{ $immobilisation->num_bc ?? '' }}</td>
                                <td style="vertical-align: middle" class="td-left">{{ $immobilisation->intitule ?? '' }}</td>
                                <td style="vertical-align: middle" class="td-center">
                                    @if(isset($immobilisation->updated_at))
                                        {{ date('d/m/Y',strtotime($immobilisation->updated_at)) }}
                                    @endif
                                </td>
                                <td class="td-center" style="color: red; vertical-align: middle; font-weight: bold; text-align:left">{{ $statut ?? '' }}</td>
                                @if(isset($affiche_demandeur))
                                    <td>{{ $immobilisation->mle ?? '' }} - {{ $immobilisation->nom_prenoms ?? '' }}</td>
                                @endif
                                <td style="vertical-align: middle" class="td-center">


                                    <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm">
                                        <svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                    </a>
                                    <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                        </svg>
                                    </a>
                                    <a style="display:{{ $display_transfert }};" title="{{ $title_transfert }}" href="{{ $href_transfert }}" class="btn btn-success btn-sm">
                                        <svg style="color:black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
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
