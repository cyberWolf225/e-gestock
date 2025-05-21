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
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header entete-table">{{ __('MAGASIN DE STOCKAGE (CONSOMMATION DÉTAILLÉE) ') }}</div>
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
                  <div class="form-group row mb-5">

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
            
            <table class="table table-striped bg-white data-table" style="width: 100%">
                <thead>
                  <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">#</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MLE / N° CNPS</th>
                    <th style="vertical-align: middle; text-align:center;">NOM(S) / RAISON SOCIALE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MOUVEMENT</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">QTÉ</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">PRIX U.</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MONTANT HT</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">TAXE</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">MONTANT TTC</th>
                    <th style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">DATE</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $i = 1; ?>
                  @foreach($magasin_stocks as $magasin_stock)
                    <?php 
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

                                if(isset($livraison->type_beneficiaire)){
                                  if($livraison->type_beneficiaire === "Structure"){

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
                      $created_at = null;
                      if (isset($magasin_stock->created_at)) {
                        $created_at = date('d/m/Y',strtotime($magasin_stock->created_at));
                      }
                    ?>

                    @if($magasin_stock->libelle != "Sortie du stock")  
                    
                      <tr>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap"><strong>{{ $mle ?? '' }}</strong></td>
                        <td style="vertical-align: middle; text-align:left;">{{ $nom_prenoms ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $magasin_stock->libelle ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $qte ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $prix_unit ?? '' }}</td>
                        <td style="vertical-align: middle; text-align :right; width:1px; white-space:nowrap">{{ $montant_ht ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $taxe ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $montant_ttc ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $created_at ?? '' }}</td>
                      </tr>

                      <?php $i++; ?>

                      @elseif($magasin_stock->libelle === "Sortie du stock")

                      
                      <?php
                        $mle = null;
                        $nom_prenoms = null;
                        $consommations = DB::table('requisitions as r')
                          ->join('demandes as d','d.requisitions_id','=','r.id')

                          ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                          ->join('distributions as di','di.demande_consolides_id','=','dc.id')

                          ->join('consommations as c','c.distributions_id','=','di.id')
                          ->join('profils as p','p.id','=','d.profils_id')
                          ->join('users as u','u.id','=','p.users_id')
                          ->join('agents as a','a.id','=','u.agents_id')
                          ->select('a.mle','a.nom_prenoms','c.qte','c.prixu','c.montant','c.created_at')
                          ->whereRaw('d.magasin_stocks_id = dc.magasin_stocks_id')
                          ->where('d.magasin_stocks_id',$magasin_stock->magasin_stocks_id)
                          ->where('dc.magasin_stocks_id',$magasin_stock->magasin_stocks_id)
                          ->whereIn('d.requisitions_id_consolide', function($query) use($magasin_stock){
                              $query->select(DB::raw('dd.requisitions_id'))
                                    ->from('livraisons as l')
                                    ->join('demandes as dd','dd.id','=','l.demandes_id')
                                    ->where('l.mouvements_id',$magasin_stock->id)
                                    ->whereRaw('d.requisitions_id_consolide = dd.requisitions_id');
                          })
                          ->get();
                          
                          foreach ($consommations as $consommation) {

                            $mle = $consommation->mle;
                            $nom_prenoms = $consommation->nom_prenoms;

                            
                            

                            $qte = null;

                            if (isset($consommation->qte)) {
                                $qte = strrev(wordwrap(strrev($consommation->qte ?? ''), 3, ' ', true));
                            }

                            $prix_unit = null;

                            if (isset($consommation->prixu)) {
                                $prix_unit = strrev(wordwrap(strrev($consommation->prixu ?? ''), 3, ' ', true));
                            }

                            $montant_ht = null;

                            if (isset($consommation->montant)) {
                                $montant_ht = strrev(wordwrap(strrev($consommation->montant ?? ''), 3, ' ', true));
                            }

                            $taxe = 0;
                            

                            $montant_ttc = $montant_ht;

                            
                            $created_at = null;
                            if (isset($consommation->created_at)) {
                              $created_at = date('d/m/Y',strtotime($consommation->created_at));
                            }

                            
                            ?>
                            <tr>
                              <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:left;"><strong style="color: red">{{ $mle ?? '' }}</strong> - {{ $nom_prenoms ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $magasin_stock->libelle ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $qte ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $prix_unit ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $montant_ht ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $taxe ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ $montant_ttc ?? '' }}</td>
                              <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $created_at ?? '' }}</td>
                            </tr>
                            <?php

                            $i++;
                          }
                      ?>

                    @endif

                    
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