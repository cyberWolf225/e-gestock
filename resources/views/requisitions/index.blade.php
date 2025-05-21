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
 
    <div class="container">
        
    
        <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
            <div class="col-lg-12">
                @if(isset($acces_create))
                <a class="btn btn-sm" href="{{ route("requisitions.create") }}" style="color: #033d88; background-color:orange; font-weight:bold; margin-left:-8px;">
                Créer une demande d'article
                </a>
                @endif

                @if(count($requisitions_all) > 100)
                    <a onclick="return confirm('Attention ceci peut durer plusieurs minutes')" class="btn btn-sm ml-5" href="{{ route("requisitions.index_all") }}" style="color: #033d88; background-color:orange; font-weight:bold; margin-left:-8px;">
                    Afficher toutes les demandes d'article
                    </a>
                @endif
            </div>
        </div>
        
    
    <br>
        <div class="row">
            <div class="col-12">

                <div class="card-header entete-table">{{ mb_strtoupper($titre ?? '') }}
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
                        <!-- table-bordered -->
                    <table id="example1" class="table table-striped  bg-white data-table" style="width: 100%" >
                    <thead>
                        <!-- color: #7d7e8f; -->
                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">#</th>
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">N° DEMANDE</th>
                      <th style="vertical-align: middle; text-align:center" width="90%">INTITULÉ</th>
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">DATE</th>
                      <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">STATUT</th>
                      @if(isset($affiche_demandeur))
                          <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">DEMANDEUR / PILOTE AEE</th>
                      @endif
                      


                      <th style="vertical-align: middle; text-align:center" width="20%">
                          
                      </th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; ?>
                        @foreach($requisitions as $requisition)
                        
                            <?php 
                            //statut de la demande 

                            $requisitions_id = Crypt::encryptString($requisition->id);

                            $statut = null;

                            $title_show = "Voir les details de la réquisition";
                            $title_edit = "";
                            $title_transfert = "";

                            $href_show = "show/".$requisitions_id;
                            $href_edit = "";
                            $href_transfert = "";

                            $display_show = "";
                            $display_edit = "none";
                            $display_transfert = "none";

                            $title_recycle = "";
                            $href_recycle = "";
                            $display_recycle = "none";

                            $statut_requisition = DB::table('statut_requisitions as sr')
                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                            ->where('sr.requisitions_id',$requisition->id)
                            ->orderByDesc('sr.id')
                            ->limit(1)
                            ->first();
                            if ($statut_requisition!=null) {
                                $statut = $statut_requisition->libelle;
                            }

                            if ($statut === "Soumis pour validation" or $statut === "Annulé (Agent Cnps)" or $statut === "Annulé (Responsable N+1)") {

                                if ($type_profils_name === 'Agent Cnps') {
                                    
                                    $hierarchie = DB::table('users as u')
                                        ->join('hierarchies as h','h.agents_id','=','u.agents_id')
                                        ->where('u.id',auth()->user()->id)
                                        ->where('h.flag_actif',1)
                                        ->first();

                                        if ($hierarchie!=null) {
                                            $title_transfert = "Transmettre la demande au Responsable directe";
                                        }else{
                                            $title_transfert = "Transmettre la réquisition au pilote AEE";
                                        }
                                        
                                        $href_transfert = "send/".$requisitions_id;
                                        $display_transfert = "";
                                        
                                        $title_edit = "Modifier la réquisition";
                                        $href_edit = "edit/".$requisitions_id;
                                        $display_edit = "";
                                    

                                }

                            }elseif($statut === "Transmis (Responsable N+1)" or $statut === "Annulé (Responsable N+2)") {
                    

                                if ($type_profils_name === 'Responsable N+1') {
                                    
                                    $title_edit = "Valider la demande d'articles";
                                    $href_edit = "/valider_requisitions/create/".$requisitions_id;
                                    $display_edit = "";

                                }

                            }elseif($statut === "Transmis (Responsable N+2)") {
                    

                                if ($type_profils_name === 'Responsable N+2') {
                                    
                                    $title_edit = "Valider la demande d'articles";
                                    $href_edit = "/valider_requisitions/create/".$requisitions_id;
                                    $display_edit = "";

                                }

                            }elseif($statut === "Transmis (Pilote AEE)") {
                    

                                if ($type_profils_name === 'Pilote AEE') {
                                    
                                    $title_edit = "Valider la demande d'articles";
                                    $href_edit = "/valider_requisitions/create/".$requisitions_id;
                                    $display_edit = "";

                                }

                            }elseif($statut === "Annulé (Pilote AEE)") {
                    

                                if ($type_profils_name === 'Responsable N+1' or $type_profils_name === 'Responsable N+2') {
                                    
                                    $title_edit = "Valider la demande d'articles";
                                    $href_edit = "/valider_requisitions/create/".$requisitions_id;
                                    $display_edit = "";

                                }elseif($type_profils_name === 'Agent Cnps'){
                                    if ($sans_hierarchie === null) {
                                        
                                        $title_transfert = "Transmettre la réquisition au pilote AEE";
                                        $href_transfert = "send/".$requisitions_id;
                                        $display_transfert = "";
                                        
                                        $title_edit = "Modifier la réquisition";
                                        $href_edit = "edit/".$requisitions_id;
                                        $display_edit = "";
                                    }
                                }


                            }elseif ($statut === "Consolidée (Pilote AEE)" or $statut === "Partiellement validé (Pilote AEE)" or $statut === "Validé (Pilote AEE)"  or $statut === "Annulé (Responsable des stocks)") {

                                

                                if ($type_profils_name === 'Pilote AEE') {

                                    $libelle = null;
                                    if ($statut === "Partiellement validé (Pilote AEE)" or $statut === "Validé (Pilote AEE)") {
                                        $demande = DB::table('demandes')
                                        ->where('requisitions_id',$requisition->id)
                                        ->whereNotNull('requisitions_id_consolide')
                                        ->first();

                                        if ($demande!=null) {
                                            $requisitions_id_consolide = $demande->requisitions_id_consolide;

                                            $statut_requisition_controll = DB::table('statut_requisitions as sr')
                                            ->where('sr.requisitions_id', $demande->requisitions_id_consolide)
                                            ->join('type_statut_requisitions as tsr','tsr.id','=','sr.type_statut_requisitions_id')
                                            ->orderByDesc('sr.id')
                                            ->select('tsr.libelle')
                                            ->limit(1)
                                            ->first();

                                            if ($statut_requisition_controll != null) {
                                                $libelle = $statut_requisition_controll->libelle;
                                            }
                                        }

                                    }


                                    $title_edit = "Consolider la demande";
                                    

                                    $href_edit = "/valider_requisitions/edit/".$requisitions_id;
                                    
                                    if ($libelle === 'Consolidée (Pilote AEE)' or $libelle === 'Annulé (Responsable des stocks)') {
                                        $display_edit = "";
                                    }else{
                                        $display_edit = "none";
                                    }
                                    
                                    

                                    if ($statut === "Consolidée (Pilote AEE)") {

                                        $title_transfert = "Transmettre la demande au Responsable des stocks";
                                        $href_transfert = "/valider_requisitions/send/".$requisitions_id;
                                        $display_transfert = "";


                                        $display_edit = "none";
                                        $title_edit = ""; 
                                        $href_edit = "";
                                    }


                                }

                            }elseif ($statut === "Transmis (Responsable des stocks)") {
                    

                                if ($type_profils_name === 'Responsable des stocks') {
                                    
                                    $title_edit = "Valider la demande";
                                    $href_edit = "/valider_requisitions/create/".$requisitions_id;
                                    $display_edit = "";


                                }

                            }elseif ($statut === "Validé (Responsable des stocks)" or $statut === "Partiellement validé (Responsable des stocks)") {
                    

                                if ($type_profils_name === 'Responsable des stocks') {
                                    
                                    
                                    $title_transfert = "Soumettre la demande pour livraison";
                                    $href_transfert = "/valider_requisitions/create/".$requisitions_id;
                                    $display_transfert = "";

                                    
                                    


                                }

                            }elseif ($statut === "Soumis pour livraison") {
                    

                                if ($type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {
                                    
                                    $title_edit = "livrer la demande";

                                    $href_edit = "/livraisons/create/".$requisitions_id;

                                    $display_edit = "";


                                }

                            }elseif($statut === "Livraison partielle"){
                                if ($type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {
                                    
                                    $title_edit = "livrer le reste de la demande";

                                    $href_edit = "/livraisons/create/".$requisitions_id;

                                    $display_edit = "";


                                    if($type_profils_name === 'Responsable des stocks'){
                                        $title_recycle = "Annuler la livraison";
                                        $href_recycle = "/annulation/livraison_requisition/".$requisitions_id;
                                        $display_recycle = "";
                                    }



                                }elseif($type_profils_name === 'Pilote AEE'){
                                    

                                    // vérifier si cette réquisition appartient au pilote AEE
                                    $requisit = DB::table('requisitions as r')
                                    ->where('r.profils_id',Session::get('profils_id'))
                                    ->where('r.id',$requisition->id)
                                    ->first();
                                    
                                    if ($requisit!=null) {

                                        $title_edit = "Confirmer la réception de l'article";
                                        $href_edit = "/livraisons/confirme/".$requisitions_id;
                                        $display_edit = "";

                                    }
                                }elseif($type_profils_name === 'Agent Cnps'){
                                    

                                    // vérifier si cette réquisition appartient au pilote AEE
                                    $requisit = DB::table('requisitions as r')
                                    ->where('r.profils_id',Session::get('profils_id'))
                                    ->where('r.id',$requisition->id)
                                    ->first();
                                    
                                    if ($requisit!=null) {

                                        $title_edit = "Confirmer la réception de l'article";
                                        $href_edit = "/livraisons/reception/".$requisitions_id;
                                        $display_edit = "";
                                        
                                    }
                                }
                            }elseif ($statut === 'Livraison partielle [ Confirmé ]') {
                                
                                if ($type_profils_name === 'Responsable des stocks' or $type_profils_name === 'Gestionnaire des stocks') {
                                    
                                    $title_edit = "livrer le reste de la demande";

                                    $href_edit = "/livraisons/create/".$requisitions_id;

                                    $display_edit = "";


                                }elseif($type_profils_name === 'Pilote AEE'){
                                    $title_edit = "Confirmer la réception de l'article";
                                    $href_edit = "/livraisons/confirme/".$requisitions_id;
                                    $display_edit = "";

                        
                                    $title_transfert = "Distribution des articles aux bénéficiaires";
                                    $href_transfert = "/livraisons/distribution/".$requisitions_id;
                                    $display_transfert = "";


                    
                                }

                            }elseif ($statut === 'Livraison totale') {
                                
                                if($type_profils_name === 'Pilote AEE'){

                                    // vérifier si cette réquisition appartient au pilote AEE
                                    $requisit = DB::table('requisitions as r')
                                    ->where('r.profils_id',Session::get('profils_id'))
                                    ->where('r.id',$requisition->id)
                                    ->first();
                                    
                                    if ($requisit!=null) {

                                        $title_edit = "Confirmer la réception de l'article";
                                        $href_edit = "/livraisons/confirme/".$requisitions_id;
                                        $display_edit = "";

                                    }

                                }elseif($type_profils_name === 'Agent Cnps'){
                                    

                                    // vérifier si cette réquisition appartient au pilote AEE
                                    $requisit = DB::table('requisitions as r')
                                    ->where('r.profils_id',Session::get('profils_id'))
                                    ->where('r.id',$requisition->id)
                                    ->first();
                                    
                                    if ($requisit!=null) {

                                        $title_edit = "Confirmer la réception de l'article";
                                        $href_edit = "/livraisons/reception/".$requisitions_id;
                                        $display_edit = "";
                                        
                                    }
                                }elseif($type_profils_name === 'Responsable des stocks'){
                                    $title_recycle = "Annuler la livraison";
                                    $href_recycle = "/annulation/livraison_requisition/".$requisitions_id;
                                    $display_recycle = "";
                                }

                            }elseif ($statut === 'Livraison totale [ Confirmé ]') {
                                
                                if($type_profils_name === 'Pilote AEE'){
                        
                                    $title_transfert = "Distribution des articles aux bénéficiaires";
                                    $href_transfert = "/livraisons/distribution/".$requisitions_id;
                                    $display_transfert = "";
                    
                                }

                            }elseif ($statut === 'Réception partielle') {
                                
                                if($type_profils_name === 'Pilote AEE'){

                                    

                                }elseif($type_profils_name === 'Agent Cnps'){
                                    

                                    // vérifier si cette réquisition appartient au pilote AEE
                                    $requisit = DB::table('requisitions as r')
                                    ->where('r.profils_id',Session::get('profils_id'))
                                    ->where('r.id',$requisition->id)
                                    ->first();
                                    
                                    if ($requisit!=null) {

                                        $title_edit = "Confirmer la réception de l'article";
                                        $href_edit = "/livraisons/reception/".$requisitions_id;
                                        $display_edit = "";
                                        
                                    }
                                }

                            }



                            
                            
                            ?>
                            <tr style="color: #7d7e8f; text-shadow: 2px 5px 5px #aabcc6;">
                                <td class="td-center">{{ $i ?? '' }}</td>
                                <td class="td-left" style="font-weight: bold">{{ $requisition->num_bc ?? '' }}</td>
                                <td class="td-left">{{ mb_strtoupper($requisition->intitule ?? '') }}</td>
                                <td class="td-center">
                                    @if(isset($requisition->updated_at))
                                        {{ date('d/m/Y',strtotime($requisition->updated_at)) }}
                                    @endif
                                </td>
                                <td class="td-center" style="color: red; text-align:left" >{{ $statut ?? '' }}</td>
                                @if(isset($affiche_demandeur))
                                    <td style="text-align:left">{{ $requisition->mle ?? '' }} - {{ $requisition->nom_prenoms ?? '' }}</td>
                                @endif
                                <td class="td-center">


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

                                    <a style="display:{{ $display_recycle }};" title="{{ $title_recycle }}" href="{{ $href_recycle }}" class="btn btn-success btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2z"/>
                                        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466"/>
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
