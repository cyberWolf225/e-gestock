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

                <div class="card-header entete-table">{{ __('LIVRAISON DE COMMANDES') }}
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
                    <div class="row">
                        <div class="col-md-6">
                            <table width="100%">
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">N° BON DE COMMANDE</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px; font-weight:bold; color:red;">{{ $demande_achat_info->num_bc ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">INTITULE</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px">{{ $demande_achat_info->intitule ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">DEVISE</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px">
                                        {{ $demande_achat_info->libelle_devise ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">DATE</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px">
                                        
                                        @if(isset($demande_achat_info->created_at))
                                            
                                        {{ date('d/m/Y',strtotime($demande_achat_info->created_at ?? '')) }}
                                            
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table width="100%">
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">N° CNPS</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px; font-weight:bold; color:blue;">{{ $demande_achat_info->entnum ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">RAISON SOCIALE</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px">{{ $demande_achat_info->denomination ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:1px; white-space:nowrap; padding:5px; font-weight:bold; color:#7d7e8f">CONTACTS</td>
                                    <td style="width:1px; white-space:nowrap; padding:5px">:</td>
                                    <td style="padding:5px">{{ $demande_achat_info->contacts ?? '' }}</td>
                                </tr>
                                
                            </table>
                        </div>
                    </div>
                    <br/>

                    <table id="example1" class="table table-striped bg-white" style="width: 100%">
                    <thead>
                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">#</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">N° BON <br/>DE LIVRAISON</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">MONTANT <br/>TOTAL BRUT</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">REMISE <br/>GENERALE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">MONTANT <br/>TOTAL NET</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">TVA</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">MONTANT <br/>TOTAL TTC</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">DATE</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">STATUT</th>
                    <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;">                        
                    </th>
                    </tr>
                    </thead>
                    <tbody style="vertical-align: middle">

                        <?php $i = 1; ?>

                        

                        @foreach($livraison_commandes as $livraison_commande)
                            <?php

                                $montant_total_brut_partie_entiere = null;
                                $montant_total_brut_partie_decimale = null;
                                $taux_remise_generale_partie_entiere = null;
                                $taux_remise_generale_partie_decimale = null;
                                $remise_generale_partie_entiere = null;
                                $remise_generale_partie_decimale = null;
                                $montant_total_net_partie_entiere = null;
                                $montant_total_net_partie_decimale = null;
                                $montant_total_ttc_partie_entiere = null;
                                $net_a_payer_partie_entiere = null;
                                $montant_total_ttc_partie_decimale = null;
                                $net_a_payer_partie_decimale = null;
                                $montant_tva_partie_entiere = null;
                                $montant_tva_partie_decimale = null;
                                
                                if (isset($livraison_commande)) {
                                    
                                    
                                    $montant_total_brut = number_format((float)$livraison_commande->montant_total_brut, 2, '.', '');

                                    $block_montant_total_brut = explode(".",$montant_total_brut);
                                    
                                    
                                    
                                    if (isset($block_montant_total_brut[0])) {
                                        $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
                                    }

                                    
                                    if (isset($block_montant_total_brut[1])) {
                                        $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
                                    }

                                    $taux_remise_generale = number_format((float)$livraison_commande->taux_remise_generale, 2, '.', '');

                                    $block_taux_remise_generale = explode(".",$taux_remise_generale);
                                    
                                    
                                    
                                    if (isset($block_taux_remise_generale[0])) {
                                        $taux_remise_generale_partie_entiere = $block_taux_remise_generale[0];
                                    }

                                    
                                    if (isset($block_taux_remise_generale[1])) {
                                        $taux_remise_generale_partie_decimale = $block_taux_remise_generale[1];
                                    }


                                    $remise_generale = number_format((float)$livraison_commande->remise_generale, 2, '.', '');

                                    $block_remise_generale = explode(".",$remise_generale);
                                    
                                    
                                    
                                    if (isset($block_remise_generale[0])) {
                                        $remise_generale_partie_entiere = $block_remise_generale[0];
                                    }

                                    
                                    if (isset($block_remise_generale[1])) {
                                        $remise_generale_partie_decimale = $block_remise_generale[1];
                                    }

                                    $montant_total_net = number_format((float)$livraison_commande->montant_total_net, 2, '.', '');

                                    $block_montant_total_net = explode(".",$montant_total_net);
                                    
                                    
                                    
                                    if (isset($block_montant_total_net[0])) {
                                        $montant_total_net_partie_entiere = $block_montant_total_net[0];
                                    }

                                    
                                    if (isset($block_montant_total_net[1])) {
                                        $montant_total_net_partie_decimale = $block_montant_total_net[1];
                                    }

                                    if(isset($livraison_commande->tva)){

                                        $montant_tva = number_format((float)(($livraison_commande->montant_total_net * ($livraison_commande->tva)/100)), 2, '.', '');

                                        $montant_tva = number_format((float)$montant_tva, 2, '.', '');

                                        $block_montant_tva = explode(".",$montant_tva);                                        

                                        if (isset($block_montant_tva[0])) {
                                            $montant_tva_partie_entiere = $block_montant_tva[0];
                                        }

                                        
                                        if (isset($block_montant_tva[1])) {
                                            $montant_tva_partie_decimale = $block_montant_tva[1];
                                        }

                                    }else{
                                        $montant_tva_partie_entiere = 0;
                                    }
                                    

                                    $montant_total_ttc = number_format((float)$livraison_commande->montant_total_ttc, 2, '.', '');

                                    $block_montant_total_ttc = explode(".",$montant_total_ttc);
                                    
                                
                                    
                                    if (isset($block_montant_total_ttc[0])) {
                                        $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
                                    }

                                    
                                    if (isset($block_montant_total_ttc[1])) {
                                        $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
                                    }


                                    $net_a_payer = number_format((float)$livraison_commande->net_a_payer, 2, '.', '');

                                    $block_net_a_payer = explode(".",$net_a_payer);
                                    
                                    
                                    
                                    if (isset($block_net_a_payer[0])) {
                                        $net_a_payer_partie_entiere = $block_net_a_payer[0];
                                    }
                                    
                                    if (isset($block_net_a_payer[1])) {
                                        $net_a_payer_partie_decimale = $block_net_a_payer[1];
                                    }

                                    
                                }

                            
                                $livraison_commandes_id_crypt = Crypt::encryptString($livraison_commande->id); 

                                $title_show = "Voir les détails";
                                $href_show = "/livraison_commandes/show/".$livraison_commandes_id_crypt;
                                $display_show = "";

                                $svg_show = '<svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                </svg>';

                                $title_edit = "";
                                $href_edit = "";
                                $display_edit = "none";                                

                                $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                                </svg>';

                                if(isset($type_profils_name)){
                                    if($type_profils_name === 'Fournisseur'){

                                        if(isset($livraison_commande->validation)){
                                            if($livraison_commande->validation  === 0){
                                                $title_edit = "Modifier la livraison";
                                                $href_edit = "/livraison_commandes/edit/".$livraison_commandes_id_crypt;
                                                $display_edit = "";
                                            }
                                        }else{
                                            $title_edit = "Modifier la livraison";
                                            $href_edit = "/livraison_commandes/edit/".$livraison_commandes_id_crypt;
                                            $display_edit = "";
                                        }

                                    }elseif($type_profils_name === 'Comite Réception'){

                                        if(isset($livraison_commande->validation)){

                                            if($livraison_commande->validation  === null){
                                                $title_edit = "Valider la livraison";
                                                $href_edit = "/livraison_commandes/edit/".$livraison_commandes_id_crypt;
                                                $display_edit = "";
                                            }

                                        }else{
                                            $title_edit = "Valider la livraison";
                                            $href_edit = "/livraison_commandes/edit/".$livraison_commandes_id_crypt;
                                            $display_edit = "";
                                        }
                                    }
                                }


                            ?>

                            <tr>
                                <td style="text-align: center; vertical-align:middle; width: 1px; white-space: nowrap;">{{ $i }}</td>
                                <td style="text-align: left; vertical-align:middle; font-weight:bold; width: 1px; white-space: nowrap; color: #7d7e8f">{{ $livraison_commande->num_bl ?? '' }}</td>
                                <td style="text-align:right;  vertical-align:middle; width: 1px; white-space: nowrap;">

                                    @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)

                                        {{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}

                                        

                                    @else

                                        {{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}

                                        

                                    @endif

                                </td>
                                <td style="text-align:right;  vertical-align:middle; width: 1px; white-space: nowrap;">

                                    @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)

                                        {{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}

                                        

                                    @else

                                        {{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}

                                        

                                    @endif

                                </td>

                                <td style="text-align:right;  vertical-align:middle; width: 1px; white-space: nowrap;">

                                    @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)

                                        {{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}

                                        

                                    @else

                                        {{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}

                                        

                                    @endif

                                </td>
                                <td style="text-align:right;  vertical-align:middle; width: 1px; white-space: nowrap;">

                                    @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)

                                        {{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}

                                        
                                        

                                    @else

                                        {{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}

                                        
                                        
                                        

                                    @endif

                                </td>
                                <td style="text-align:right;  vertical-align:middle; width: 1px; white-space: nowrap;">

                                    @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)

                                        {{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}

                                        

                                    @else

                                        {{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}

                                        

                                    @endif

                                </td>
                                <td style="width: 1px; white-space: nowrap;vertical-align:middle;">
                                    @if(isset($livraison_commande->created_at))
                                       {{ date("d/m/Y",strtotime($livraison_commande->created_at)) }} 
                                    @endif
                                </td>
                                <td style="white-space:nowrap; width:1px; vertical-align:middle; text-align:center; font-weight:bold">
                                    @if(isset($livraison_commande->validation))
                                        @if($livraison_commande->validation === 1)
                                            <span style="color:green">Validé</span>
                                            @elseif($livraison_commande->validation === 0)
                                            <span style="color:red">Invalidé</span>
                                        @endif

                                    @else
                                        <span style="color:orange">En attente <br/>de validation</span>

                                    @endif
                                </td>
                                <td style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">

                                    <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm">
                                        
                                        <?php if(isset($svg_show)){ echo $svg_show; }  ?>
                                    </a>

                                    <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm">
                                            
                                        <?php if(isset($svg_edit)){ echo $svg_edit; }  ?>
                                        
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




