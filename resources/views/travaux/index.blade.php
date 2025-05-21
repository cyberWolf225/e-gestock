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
        
        <div class="row">

            <div class="col-12">
                 
                @if(isset($acces_create))
                    <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
                        <div class="col-lg-12">
                        <a class="btn btn-sm" href="{{ route("demande_cotations.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
                        Créer un bon de commande non stockable
                        </a>

                        </div>
                    </div>
                    
                @endif

                <div class="card-header entete-table">{{ __('LISTE DE BON DE COMMANDE NON STOCKABLE') }}
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

                    <table id="example1" class="table table-striped bg-white" style="width: 100%"> 
                    <thead>
                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">#</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">N° BON <br>DE COMMANDE</th>
                    <th style="vertical-align: middle; text-align:center" width="100%">INTITULÉ</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">DATE</th>
                    <th style="vertical-align: middle; text-align:center;">STATUT</th>

                    <th style="vertical-align: middle; text-align:center;">FOURNISSEUR</th>


                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">
                    &nbsp;                        
                    </th>
                    </tr>
                    </thead>
                    <tbody style="vertical-align: middle">

                    
                        <?php 
                        
                        $i = 1;
                        

                    ?>

                    

                    @foreach($travauxes as $travauxe)



                        <?php 
                            $libelle_statut = null;
                            $statut_travauxe = null;
                            $demande_control = null;
                            $num_bc = null;
                            $verificateur_cotation = null;
                            $profils_fournisseur = null;
                            $cotation_fournisseurs_id = null;
                            $fournisseur = null;
                            $correspondant = null;

                            $travauxes_id = Crypt::encryptString($travauxe->id);

                            


                            $title_info = "";
                            $href_info = "";
                            $display_info = "none";

                            $num_bc = $travauxe->num_bc;


                               

                                
                                $title_show = "Voir les details de la demande";
                                $href_show = "show/".$travauxes_id;
                                $display_show = "";

                                $title_print = "";
                                $href_print = "";
                                $display_print = "none";

                                $title_edit = "";
                                $href_edit = "";
                                $display_edit = "none";

                                $title_transfert = "";
                                $href_transfert = "";
                                $display_transfert = "none";

                                

                                $statut_travauxe = DB::table('statut_travauxes as sda')
                                ->join('type_statut_travauxes as tsda', 'tsda.id', '=', 'sda.type_statut_travauxes_id')
                                ->where('sda.travauxes_id',$travauxe->id)
                                ->orderByDesc('sda.id')
                                ->limit(1)
                                ->select('tsda.libelle') 
                                ->first();

                                if ($statut_travauxe!=null) {
                                    $libelle_statut = $statut_travauxe->libelle;
                                }

                                


                                if ($libelle_statut === "Soumis pour validation" or $libelle_statut === "Rejeté (Responsable des achats)" or $libelle_statut === "Annulé (Gestionnaire des achats)" or $libelle_statut === "Annulé (Responsable des achats)") {

                                    if ($type_profils_name === "Gestionnaire des achats") {

                                        $title_edit = "Modifier la demande";

                                        

                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                        
                                        $title_transfert = "Transmettre la demande au Responsable des achats";
                                        $href_transfert = "send/".$travauxes_id;
                                        $display_transfert = "";

                                    }

                                }elseif($libelle_statut === "Transmis (Responsable des achats)" or $libelle_statut==='Rejeté (Responsable DMP)'){

                                    
                                    if ($type_profils_name === "Responsable des achats") {

                                        $title_edit = "Valider la demande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";

                                    }

                                }
                                elseif ($libelle_statut === "Transmis (Responsable DMP)" or $libelle_statut==='Rejeté (Responsable Contrôle Budgétaire)') {

                                    if ($type_profils_name === "Responsable DMP") {

                                            $title_edit = "Viser le bon de commande";

                                            $href_edit = "edit/".$travauxes_id;

                                            $display_edit = "";
                                    }
                                    

                                }elseif($libelle_statut==='Transmis (Responsable Contrôle Budgétaire)' or $libelle_statut==='Rejeté (Chef Département DCG)'){

                                    if ($type_profils_name === "Responsable contrôle budgetaire") {
                                        $title_edit = "Viser le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                }elseif ($libelle_statut==='Transmis (Chef Département DCG)' or $libelle_statut==='Rejeté (Responsable DCG)') {

                                    if ($type_profils_name === "Chef Département DCG") {
                                        $title_edit = "Viser le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                }elseif ($libelle_statut==='Transmis (Responsable DCG)' or $libelle_statut==='Rejeté (Directeur Général Adjoint)') {

                                    if ($type_profils_name === "Responsable DCG") {
                                        $title_edit = "Viser le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                }elseif ($libelle_statut==='Transmis (Directeur Général Adjoint)' or $libelle_statut==='Rejeté (Directeur Général)') {

                                    if ($type_profils_name === "Directeur Général Adjoint") {
                                        $title_edit = "Viser le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                }elseif ($libelle_statut==='Transmis (Directeur Général)') {

                                    if ($type_profils_name === "Directeur Général") {
                                        $title_edit = "Viser le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                }elseif ($libelle_statut==='Transmis (Responsable DFC)') {

                                    if ($type_profils_name === "Responsable DFC") {
                                        $title_edit = "Viser le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                }elseif ($libelle_statut==='Validé' or $libelle_statut==='Édité' or $libelle_statut==='Retiré (Frs.)') {

                                    if ($type_profils_name === "Gestionnaire des achats") {
                                        $title_edit = "Édité le bon de commande";
                                        $href_edit = "edit/".$travauxes_id;
                                        $display_edit = "";
                                    }

                                } 

                                //affichage du print
                                    $statut_travauxe_edit = DB::table('statut_travauxes as sda')
                                        ->join('type_statut_travauxes as tsda','tsda.id','=','sda.type_statut_travauxes_id')
                                        ->where('tsda.libelle','Validé') //Édité
                                        ->orderByDesc('sda.id')
                                        ->limit(1)
                                        ->select('sda.id')
                                        ->where('sda.travauxes_id',$travauxe->id)
                                        ->first();
                                    if ($statut_travauxe_edit!=null) {
                                        $statut_travauxe_edit_id = $statut_travauxe_edit->id;


                                        $statut_travauxe_annule = DB::table('statut_travauxes as sda')
                                            ->join('type_statut_travauxes as tsda','tsda.id','=','sda.type_statut_travauxes_id')
                                            ->whereIn('tsda.libelle',['Annulé (Responsable des achats)','Annulé (Fournisseur)'])
                                            ->orderByDesc('sda.id')
                                            ->limit(1)
                                            ->select('sda.id')
                                            ->where('sda.travauxes_id',$travauxe->id)
                                            ->first();
                                            if ($statut_travauxe_annule!=null) {
                                                $statut_travauxe_annule_id = $statut_travauxe_annule->id;
                                            }else{
                                                $statut_travauxe_annule_id = 0;
                                            }

                                            if ($statut_travauxe_edit_id < $statut_travauxe_annule_id) {
                                                $statut_travauxe_edit_id = null;
                                            }

                                    }else{
                                        $statut_travauxe_edit_id = null;
                                    }

                                    if ($statut_travauxe_edit_id !=null) {

                                        $title_print = "Imprimer le bon de commande";

                                        $href_print = "/print/dt/".$travauxes_id;

                                        $path = 'storage/documents/bcn/'.$travauxe->num_bc.'.pdf';

                                        $public_path = str_replace("/","\\",public_path($path));

                                        $type_operations_libelle = Crypt::encryptString('bcn');

                                        if(file_exists($public_path)){

                                            $href_print = '/documents/'.$travauxes_id.'/'.$type_operations_libelle;
                                            $display_print = "";

                                        }else{
                                            $href_print = "/print/dt/".$travauxes_id;
                                            $display_print = "none";
                                        }  
                                    }
                                //
                                
                            //



                            //fournisseur

                            $travaux = DB::table('travauxes as t')
                            ->join('structures as s', 's.code_structure', '=', 't.code_structure')
                            ->join('gestions as g', 'g.code_gestion', '=', 't.code_gestion')
                            ->join('organisations as o', 'o.id', '=', 't.organisations_id')
                            ->join('devises as d', 'd.id', '=', 't.devises_id')
                            ->join('periodes as p', 'p.id', '=', 't.periodes_id')
                            ->join('familles as f', 'f.ref_fam', '=', 't.ref_fam')
                            ->select('t.id as travauxes_id', 't.num_bc', 't.intitule', 't.ref_fam', 't.code_structure', 't.ref_depot', 't.code_gestion', 't.exercice', 't.montant_total_brut', 't.remise_generale', 't.montant_total_net', 't.tva', 't.montant_total_ttc', 't.net_a_payer', 't.acompte', 't.taux_acompte', 't.montant_acompte', 't.delai', 't.date_echeance', 's.nom_structure', 'f.design_fam', 't.credit_budgetaires_id', 'p.libelle_periode', 'o.id as organisations_id', 'o.denomination', 'd.code', 'd.libelle as devises_libelle', 't.created_at', 'p.valeur', 't.date_livraison_prevue', 't.date_retrait', 'o.entnum')
                            ->where('t.id', $travauxe->id)
                            ->first();

                            $organisation = null;

                            if ($travaux != null) {
                                $organisation = $travaux->entnum .' - '. $travaux->denomination;
                            }
                            
                        ?>



                        <tr>
                            <td style="text-align: center; vertical-align:middle; width: 1px; white-space: nowrap;">{{ $i }}</td>
                            <td style="text-align: left; vertical-align:middle; font-weight:bold; width: 1px; white-space: nowrap; color: #7d7e8f">{{ $num_bc ?? '' }}</td>
                            <td style="vertical-align:middle">{{ mb_strtoupper($travauxe->intitule ?? '') }}</td>
                            <td style="width: 1px; white-space: nowrap;vertical-align:middle;">
                                @if(isset($travauxe->updated_at))
                                   {{ date("d/m/Y",strtotime($travauxe->updated_at)) }} 
                                @endif
                            </td>
                            <td style="text-align: left; vertical-align:middle; font-weight:bold; color:red; font-weight:bold; vertical-align:middle;">{{ $libelle_statut ?? '' }}</td>
                            
                            <td style="vertical-align:middle;">{{ $organisation ?? '' }}</td>
                                
                            
                            <td style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">




                                <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm">
                                    <svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                                </a>

                                <a style="display:{{ $display_info }};" title="{{ $title_info }}" href="{{ $href_info }}" class="btn btn-light btn-sm">

                                    <svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list-stars" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                                    <path d="M2.242 2.194a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.256-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53zm0 4a.27.27 0 0 1 .516 0l.162.53c.035.115.14.194.258.194h.551c.259 0 .37.333.164.493l-.468.363a.277.277 0 0 0-.094.3l.173.569c.078.255-.213.462-.423.3l-.417-.324a.267.267 0 0 0-.328 0l-.417.323c-.21.163-.5-.043-.423-.299l.173-.57a.277.277 0 0 0-.094-.299l-.468-.363c-.206-.16-.095-.493.164-.493h.55a.271.271 0 0 0 .259-.194l.162-.53z"/>
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




