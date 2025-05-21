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
            <div class="card-header entete-table">{{ __('MAGASIN DE STOCKAGE ( CONSOMMATION GROUPÉE )') }}</div>
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
              
              <div class="row justify-content-center">
                <div class="col-md-12">
                  <div class="form-group row">

                    <div class="col-md-2">
                      <label class="label" for="ref_fam">Référence famille</label>
                      <input onfocus="this.blur()" style="background-color: #e9ecef; " id="ref_fam" value="{{ $mouvement->ref_fam ?? '' }}" type="text" class="form-control" autocomplete="ref_fam">
                    </div>

                    <div class="col-md-4">
                      <label class="label" for="design_fam">Désignation famille</label>
                      <input onfocus="this.blur()" style="background-color: #e9ecef;" id="design_fam" value="{{ $mouvement->design_fam ?? '' }}" type="text" class="form-control" autocomplete="design_fam">
                    </div>

                    <div class="col-md-2">
                      <label class="label" for="ref_articles">Référence article</label>
                      <input onfocus="this.blur()" style="background-color: #e9ecef;" id="ref_articles" value="{{ $mouvement->ref_articles ?? '' }}" type="text" class="form-control" autocomplete="ref_articles">
                    </div>

                    <div class="col-md-4">
                      <label class="label" for="design_article">Désignation article</label>
                      <input onfocus="this.blur()" style="background-color: #e9ecef;" id="design_article" value="{{ $mouvement->design_article ?? '' }}" type="text" class="form-control" autocomplete="design_article">
                    </div>

                  </div>
                </div>
              </div>

              Quantité totale en stock : <strong style="color:#033d88; font-size:20px"> {{ strrev(wordwrap(strrev($qte_stock_total ?? ''), 3, ' ', true)) }} </strong>

              <p>&nbsp;</p>
            
            
            <table class="table table-striped bg-white data-table" style="width: 100%">
                <thead>
                  <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">#</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MLE / N° CNPS /<br>CODE STRUCTURE</th>
                    <th style="vertical-align: middle; text-align:center;">NOM & PRENOM(S) / RAISON SOCIALE / NOM STRUCTURE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MOUVEMENT</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">QTÉ</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">PRIX U.</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MONTANT HT</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">TAXE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MONTANT TTC</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">DATE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">&nbsp;</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $i = 1;  ?>
                  @foreach($magasin_stocks as $magasin_stock)
                    <?php 

                      $mle = null;
                      $nom_prenoms = null;
                      
                      $title_show = "";
                      $href_show = "";
                      $display_show = "none";

                      $title_edit = "";
                      $href_edit = "";
                      $display_edit = "none";
                      $svg_edit = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                      <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                      </svg>';

                      if (isset($magasin_stock->libelle)) {

                        if ($magasin_stock->libelle === "Entrée en stock") {

                            $livraison_valider = DB::table('livraison_validers as lv')
                                ->join('livraison_commandes as lc','lc.id','=','lv.livraison_commandes_id')
                                ->join('cotation_fournisseurs as cf','cf.id','=','lc.cotation_fournisseurs_id')
                                ->join('organisations as o','o.id','=','cf.organisations_id')
                                ->where('lv.mouvements_id',$magasin_stock->id)
                                ->first();
                            if ($livraison_valider!=null) {
                                $mle = $livraison_valider->entnum;
                                $nom_prenoms = $livraison_valider->denomination;
                            }else{

                              $inventaire_article = DB::table('mouvements as m')
                              ->join('profils as p','p.id','=','m.profils_id')
                              ->join('users as u','u.id','=','p.users_id')
                              ->join('agents as a','a.id','=','u.agents_id')
                              ->where('m.id',$magasin_stock->id)
                              ->first();

                              if ($inventaire_article!=null) {

                                  $mle = $inventaire_article->mle;
                                  $nom_prenoms = $inventaire_article->nom_prenoms;
                                  if(isset($type_profils_name)){
                                    
                                    if($type_profils_name === "Responsable des stocks"){

                                      $title_edit = "Modifier le mouvement";
                                      $href_edit = "/mouvements/edit/".Crypt::encryptString($magasin_stock->id);
                                      $display_edit = "";

                                    }
                                  }
                                 
                              }

                            }

                        }elseif ($magasin_stock->libelle === "Sortie du stock") {

                            $livraison = DB::table('livraisons as l')
                                ->join('demandes as d','d.id','=','l.demandes_id')
                                ->join('requisitions as r','r.id','=','d.requisitions_id')
                                ->join('profils as p','p.id','=','d.profils_id')
                                ->join('users as u','u.id','=','p.users_id')
                                ->join('agents as a','a.id','=','u.agents_id')
                                ->where('l.mouvements_id',$magasin_stock->id)
                                ->first();
                            if ($livraison!=null) {
                                $mle = $livraison->mle;
                                $nom_prenoms = $livraison->nom_prenoms;


                                $magasin_stocks_id = Crypt::encryptString($magasin_stock->magasin_stocks_id);

                                $title_show = "Voir les details des consommations par agent";
                                $href_show = "../shows/".$magasin_stocks_id;
                                $display_show = "";
                                
                                if(isset($livraison->type_beneficiaire)){
                                  if($livraison->type_beneficiaire === "Structure"){
                                    $display_show = "none";

                                    $structure = DB::table('structures')->where('code_structure',$livraison->code_structure)->first();

                                    if($structure != null){
                                      $mle = $structure->code_structure;
                                      $nom_prenoms = $structure->nom_structure;
                                    }

                                  }
                                }
                                

                            }



                        }elseif ($magasin_stock->libelle === "Intégration d'inventaire") {

                            $inventaire_article = DB::table('inventaire_articles as ia')
                            ->join('profils as p','p.id','=','ia.profils_id')
                            ->join('users as u','u.id','=','p.users_id')
                            ->join('agents as a','a.id','=','u.agents_id')
                            ->where('ia.mouvements_id',$magasin_stock->id)
                            ->first();

                            if ($inventaire_article!=null) {
                                $mle = $inventaire_article->mle;
                                $nom_prenoms = $inventaire_article->nom_prenoms;

                                if($type_profils_name === "Responsable des stocks"){

                                  $title_edit = "Modifier le mouvement";
                                  $href_edit = "/mouvements/edit/".Crypt::encryptString($magasin_stock->id);
                                  $display_edit = "";

                                }
                            }

                        }elseif ($magasin_stock->libelle === "Annulation de BCI") {
                          $inventaire_article = DB::table('mouvements as m')
                          ->join('profils as p','p.id','=','m.profils_id')
                          ->join('users as u','u.id','=','p.users_id')
                          ->join('agents as a','a.id','=','u.agents_id')
                          ->where('m.id',$magasin_stock->id)
                          ->first();

                          if ($inventaire_article!=null) {

                              $mle = $inventaire_article->mle;
                              $nom_prenoms = $inventaire_article->nom_prenoms;
                              
                          }
                        }

                      }

                      $qte = null;

                      if (isset($magasin_stock->qte)) {
                          $qte = strrev(wordwrap(strrev($magasin_stock->qte ?? ''), 3, ' ', true));
                      }

                      $prix_unit = null;

                      if (isset($magasin_stock->prix_unit)) {
                          $prix_unit = strrev(wordwrap(strrev($magasin_stock->prix_unit ?? ''), 3, ' ', true));
                      }

                      $montant_ht = null;

                      if (isset($magasin_stock->montant_ht)) {
                          $montant_ht = strrev(wordwrap(strrev($magasin_stock->montant_ht ?? ''), 3, ' ', true));
                      }

                      $taxe = 0;

                      if (isset($magasin_stock->taxe)) {
                          $taxe = $magasin_stock->taxe;
                      }

                      $montant_ttc = null;

                      if (isset($magasin_stock->montant_ttc)) {
                          $montant_ttc = strrev(wordwrap(strrev($magasin_stock->montant_ttc ?? ''), 3, ' ', true));
                      }
                      $date_mouvement = null;
                      if (isset($magasin_stock->date_mouvement)) {
                        $date_mouvement = date('d/m/Y',strtotime($magasin_stock->date_mouvement));
                      }
                      $color = "black";
                      $svg = null;
                      if($magasin_stock->libelle === "Entrée en stock"){
                        $color = "green";

                        $svg = '<svg style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                        </svg>';
                      }

                      if($magasin_stock->libelle === "Sortie du stock"){
                        $color = "red";

                        $svg = '<svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                        </svg>';
                      }

                      if($magasin_stock->libelle === "Intégration d'inventaire"){
                        $color = "orange";

                        $svg = '<svg style="color:orange" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/>
                        </svg>';
                      }

                      if($magasin_stock->libelle === "Annulation de BCI"){
                        $color = "blue";

                        $svg = '<svg style="color:blue" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                        </svg>';
                      }
                    ?>
                    <tr>
                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">
                        {{ $i ?? '' }} 
                        @if(isset($svg))

                          <?php if(isset($svg)){ echo $svg; }  ?>

                        @endif
                      
                      </td>
                      <td style="vertical-align: middle; text-align:left;"><strong>{{ $mle ?? '' }}</strong></td>
                      <td style="vertical-align: middle; text-align:left;">{{ $nom_prenoms ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap; font-weight:bold; color:{{ $color ?? '' }}">{{ $magasin_stock->libelle ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $qte ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $prix_unit ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $montant_ht ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $taxe ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $montant_ttc ?? '' }}</td>
                      <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $date_mouvement ?? '' }}</td>
                      <td style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">
                                    
                        <a style="display:{{ $display_show }};" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                        </svg></a>   

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