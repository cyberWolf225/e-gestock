@extends('layouts.admin')

@section('autres_scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
@endsection


@section('styles')
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
  
    <link rel="shortcut icon" href="{{ asset('dist/img/logo.png') }}">

    <style>

      .fond{ 
          background: url('dist/img/contrat.gif') no-repeat;
          background-size: 100% 100%;
          font-size: 11px;
      }
  
    </style>
@endsection

@section('content')

<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>

</head>
<div class="container" style="color:black">
    <br>
    <datalist id="list_periode">            
        @foreach($periodes as $periode)
            <option value="{{ $periode->id }}->{{ $periode->libelle_periode }}->{{ $periode->valeur }}">{{ $periode->libelle_periode }}</option>
        @endforeach  
    </datalist>

    <datalist id="list_devise">            
        @foreach($devises as $devise)
            <option value="{{ $devise->code }}->{{ $devise->libelle }}->{{ $devise->symbole }}">{{ $devise->libelle }}</option>
        @endforeach  
    </datalist>



    <datalist id="list_taxe_tva">            
        @foreach($taxes as $taxe)
            <option value="{{ $taxe->taux }}">{{ $taxe->nom_taxe }}->{{ $taxe->taux }}</option>
        @endforeach  
    </datalist>

    <div class="row">
        <div class="col-md-12">
            
            <div class="card">
                <div class="card-header entete-table">{{ __('COTATION FOURNISSEUR') }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($demande_achat_info->created_at ?? '')) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $demande_achat_info->exercice ?? '' }}</strong></span>
                    
                </div>
                
                <form method="POST" action="{{ route('cotation_fournisseurs.store') }}" enctype="multipart/form-data">
                @csrf
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
                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">
                                                @if(isset($statut))

                                                    @if($statut === "bon_commande")
                                                        N° Bon Cde.

                                                        @if($demande_achat_info!=null)
                                                            <?php
                                                            $num_bc = $demande_achat_info->num_bc;
                                                            ?>
                                                        @endif 

                                                    @elseif($statut === "demande_cotation")
                                                        N° Demande
                                                        @if($demande_achat_info!=null)
                                                            <?php
                                                            $num_bc = str_replace('BC','',$demande_achat_info->num_bc);
                                                            ?>
                                                        @endif 
                                                    @endif

                                                @endif    
                                            </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled style="background-color: #e9ecef; display:none" onfocus="this.blur()" value="{{ $demande_achat_info->id ?? '' }}" autocomplete="off" type="text" name="id" class="form-control">
                                            
                                            <input disabled style="color:red; font-weight:bold; margin-top:-6px;" onfocus="this.blur()" value="{{ $num_bc ?? '' }}" autocomplete="off" type="text" name="num_bc" class="form-control griser">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Compte budg. </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $demande_achat_info->ref_fam ?? ''}}" autocomplete="off" type="text" name="ref_fam" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $demande_achat_info->design_fam ?? ''}}" autocomplete="off" type="text" name="design_fam" class="form-control griser">
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea style="background-color: #e9ecef; resize:none" onfocus="this.blur()" autocomplete="off" type="text" name="intitule" class="form-control">{{ $demande_achat_info->intitule ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Échéance </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $commande->libelle_periode ?? '' }}" autocomplete="off" type="text" name="demande_periode_id" class="form-control">
                                            <input disabled value="{{ $commande->valeur ?? '' }}" style="display: none" onfocus="this.blur()" type="text" id="demande_valeur" name="demande_valeur" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled style="background-color: #e9ecef" onfocus="this.blur()" onkeypress="validate(event)" value="{{ $commande->delai ?? '' }}" autocomplete="off" type="text" id="demande_delai" name="demande_delai" class="form-control">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled @if(isset($commande->date_echeance)) value="{{ date("d/m/Y H:i:s",strtotime($commande->date_echeance)) }}" @endif style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="demande_date_echeance" name="demande_date_echeance" class="form-control">
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Fournisseur </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $cotation_demande_achat->organisations_id ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control">

                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $cotation_demande_achat->entnum ?? '' }}" autocomplete="off" type="text" name="entnum" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $cotation_demande_achat->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Devise </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <!-- onkeyup="editDevise(this)"  -->
                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef" value="{{ old('code_devise') ?? $devise_cotation->code ?? $devise_default->code ?? '' }}"  list="list_devise" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled value="{{ old('libelle_devise') ?? $devise_cotation->libelle ?? $devise_default->libelle ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="panel panel-footer">
                    <table class="table table-bordered table-striped" id="myTable" style="width:100%; ">
                        <thead>
                            <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                <th style="width:1px; white-space:nowrap; text-align:center">RÉF.</th>
                                <th style="width:40%">DÉSIGNATION ARTICLE</th>
                                <th style="width:1px; white-space:nowrap; text-align:center">QTÉ DEMANDÉE</th>
                                <th style="width:12%; text-align:center">QTÉ</th>
                                <th style="width:15%; text-align:center">PU HT</th>
                                <th style="width:1px; white-space:nowrap; text-align:center">REMISE (%)</th>
                                <th style="width:15%; text-align:center">MONTANT HT</th>
                                <th style="width:1%; text-align:center">ÉCHANTILLON</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demande_achats as $demande_achat)
                            <?php 
                                $qte = null;
                                $prix_unit = null;
                                $remise = null;
                                $montant_ht = null;
                                $montant_ttc = null;
                                $echantillon = null;

                                $echantillon_cnps = null;
                                if (isset($demande_achat->echantillon_cnps)) {
                                    $echantillon_cnps = $demande_achat->echantillon_cnps;
                                }

                                $description_articles_libelle = null;

                                $description_article = DB::table('detail_demande_achats as dda')
                                ->join('description_articles as da','da.id','=','dda.description_articles_id')
                                ->select('da.libelle')
                                ->where('dda.demande_achats_id',$demande_achat->demande_achats_id)
                                ->where('dda.id',$demande_achat->id)
                                ->first();

                                if ($description_article != null) {
                                    $description_articles_libelle = $description_article->libelle;
                                }
                            ?>
                            
                            @if($cotation_demande_achat!=null)
                                <?php 

                                $cotation_demande_achats_detail = DB::table('demande_achats as da')
                                ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
                                ->join('detail_demande_achats as dda','dda.demande_achats_id','=','da.id')
                                ->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')
                                ->where('da.id',$demande_achat->demande_achats_id)
                                ->where('cf.id',$cotation_demande_achat->cotation_fournisseurs_id)
                                ->whereRaw('dc.ref_articles = dda.ref_articles')
                                ->where('dc.ref_articles',$demande_achat->ref_articles)
                                ->limit(1)
                                ->first();

                                if ($cotation_demande_achats_detail!=null) {
                                    
                                    $qte = $cotation_demande_achats_detail->qte;
                                    // $prix_unit = $cotation_demande_achats_detail->prix_unit; 

                                    $prix_unit = number_format((float)$cotation_demande_achats_detail->prix_unit, 2, '.', '');

                                    $block_prix_unit = explode(".",$prix_unit);
                                    
                                    $prix_unit_partie_entiere = null;
                                    
                                    if (isset($block_prix_unit[0])) {
                                        $prix_unit_partie_entiere = $block_prix_unit[0];
                                    }

                                    $prix_unit_partie_decimale = null;
                                    if (isset($block_prix_unit[1])) {
                                        $prix_unit_partie_decimale = $block_prix_unit[1];
                                    }

                                    // $remise = $cotation_demande_achats_detail->remise;

                                    $remise = number_format((float)$cotation_demande_achats_detail->remise, 2, '.', '');

                                    $block_remise = explode(".",$remise);
                                    
                                    $remise_partie_entiere = null;
                                    
                                    if (isset($block_remise[0])) {
                                        $remise_partie_entiere = $block_remise[0];
                                    }

                                    $remise_partie_decimale = null;
                                    if (isset($block_remise[1])) {
                                        $remise_partie_decimale = $block_remise[1];
                                    }

                                    // $montant_ht = $cotation_demande_achats_detail->montant_ht;

                                    $montant_ht = number_format((float)$cotation_demande_achats_detail->montant_ht, 2, '.', '');

                                    $block_montant_ht = explode(".",$montant_ht);
                                    
                                    $montant_ht_partie_entiere = null;
                                    
                                    if (isset($block_montant_ht[0])) {
                                        $montant_ht_partie_entiere = $block_montant_ht[0];
                                    }

                                    $montant_ht_partie_decimale = null;
                                    if (isset($block_montant_ht[1])) {
                                        $montant_ht_partie_decimale = $block_montant_ht[1];
                                    }

                                    $montant_ttc = $cotation_demande_achats_detail->montant_ttc;
                                    $echantillon = $cotation_demande_achats_detail->echantillon;

                                    
                                }
                                ?>
                                
                            @endif
                            
                            <tr style=" color: #7d7e8f">
                                <td style="width:1px; white-space:nowrap;text-align:left; vertical-align:middle; font-weight:bold; ; color: #7d7e8f">
                                    <input disabled onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $demande_achat->ref_articles ?? '' }}" autocomplete="off" required type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">

                                    {{ $demande_achat->ref_articles ?? '' }}
                                </td>
                                <td style="vertical-align:middle;">
                                    <input disabled value="{{ $demande_achat->design_article ?? '' }}" autocomplete="off" required onfocus="this.blur()" style="background-color: #e9ecef; display:none" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article">

                                    {{ $demande_achat->design_article ?? '' }}

                                    @if(isset($description_articles_libelle))
                                        <br/>
                                        <br/>
                                        <span style="color:red; font-weight:bold" >{{ 'Description de l\'article' }}</span>
                                        <br/>
                                        {{ $description_articles_libelle ?? '' }}
                                        
                                    @endif

                                </td>
                                <td style="width:1px; white-space:nowrap;text-align:center; vertical-align:middle; ">
                                    <input disabled onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" onkeyup="editMontant(this)" value="{{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" name="qte_demandee[]" class="form-control qte_demandee">

                                    {{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? ''), 3, ' ', true)) }}

                                </td>
                                <td style=" vertical-align:middle; text-align:center;">
                                    {{ strrev(wordwrap(strrev($qte ?? $demande_achat->qte_accordee ?? ''), 3, ' ', true)) }}
                                </td>
                                <td style=" vertical-align:middle; text-align:right;">

                                    @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                                    {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}
                                    @else
                                    {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}
                                    @endif

                                </td>
                                <td style="width:1px; white-space:nowrap;  vertical-align:middle; text-align:center;">

                                    @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                                    {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}
                                    @else
                                    {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}
                                    @endif

                                </td>
                                <td style="vertical-align:middle; text-align:right;">

                                    @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)
                                    {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}
                                    @else
                                    {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}
                                    @endif                                    

                                </td>
                                
                                <td style="text-align: left; vertical-align:middle;">

                                   

                                    @if(isset($echantillon_cnps))
                                        <!-- Modal -->

                                            <a title="Echantillon CNPS : {{ $demande_achat->design_article ?? '' }}" href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->ref_articles }}">
                                                <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                </svg>
                                            </a>
                                            <div class="modal fade" id="exampleModalCenter{{ $demande_achat->ref_articles }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="text-align: center">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Échantillon CNPS { <span style="color: orange">{{ $demande_achat->design_article ?? '' }}</span> } </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img src='{{ asset('storage/'.$echantillon_cnps) }}' style='width:100%;'>
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        <!-- Modal -->
                                        
                                    @endif

                                    &nbsp;&nbsp;&nbsp;

                                    @if(isset($echantillon))

                                        <!-- Modal -->

                                            <a title="Échantillon Fournisseur : {{ $demande_achat->design_article ?? '' }}" href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->id }}">
                                                    <svg style="cursor: pointer; color:blue; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                    </svg>
                                            </a>
                                            <div class="modal fade" id="exampleModalCenter{{ $demande_achat->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="text-align: center">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Échantillon Fournisseur { <span style="color: red">{{ $demande_achat->design_article ?? '' }}</span> } </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <img src='{{ asset('storage/'.$echantillon) }}' style='width:100%;'>
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        <!-- Modal -->
                                        
                                    @endif
                                    
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>

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
                            $montant_acompte_partie_entiere = null;
                            $montant_acompte_partie_decimale = null;
                            
                            if (isset($cotation_demande_achat)) {
                                
                                
                                $montant_total_brut = number_format((float)$cotation_demande_achat->montant_total_brut, 2, '.', '');

                                $block_montant_total_brut = explode(".",$montant_total_brut);
                                
                                
                                
                                if (isset($block_montant_total_brut[0])) {
                                    $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
                                }

                                
                                if (isset($block_montant_total_brut[1])) {
                                    $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
                                }

                                $taux_remise_generale = number_format((float)$cotation_demande_achat->taux_remise_generale, 2, '.', '');

                                $block_taux_remise_generale = explode(".",$taux_remise_generale);
                                
                                
                                
                                if (isset($block_taux_remise_generale[0])) {
                                    $taux_remise_generale_partie_entiere = $block_taux_remise_generale[0];
                                }

                                
                                if (isset($block_taux_remise_generale[1])) {
                                    $taux_remise_generale_partie_decimale = $block_taux_remise_generale[1];
                                }


                                $remise_generale = number_format((float)$cotation_demande_achat->remise_generale, 2, '.', '');

                                $block_remise_generale = explode(".",$remise_generale);
                                
                                
                                
                                if (isset($block_remise_generale[0])) {
                                    $remise_generale_partie_entiere = $block_remise_generale[0];
                                }

                                
                                if (isset($block_remise_generale[1])) {
                                    $remise_generale_partie_decimale = $block_remise_generale[1];
                                }

                                $montant_total_net = number_format((float)$cotation_demande_achat->montant_total_net, 2, '.', '');

                                $block_montant_total_net = explode(".",$montant_total_net);
                                
                                
                                
                                if (isset($block_montant_total_net[0])) {
                                    $montant_total_net_partie_entiere = $block_montant_total_net[0];
                                }

                                
                                if (isset($block_montant_total_net[1])) {
                                    $montant_total_net_partie_decimale = $block_montant_total_net[1];
                                }
                                

                                $montant_total_ttc = number_format((float)$cotation_demande_achat->montant_total_ttc, 2, '.', '');

                                $block_montant_total_ttc = explode(".",$montant_total_ttc);
                                
                               
                                
                                if (isset($block_montant_total_ttc[0])) {
                                    $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
                                }

                                
                                if (isset($block_montant_total_ttc[1])) {
                                    $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
                                }


                                $net_a_payer = number_format((float)$cotation_demande_achat->net_a_payer, 2, '.', '');

                                $block_net_a_payer = explode(".",$net_a_payer);
                                
                                
                                
                                if (isset($block_net_a_payer[0])) {
                                    $net_a_payer_partie_entiere = $block_net_a_payer[0];
                                }
                                
                                if (isset($block_net_a_payer[1])) {
                                    $net_a_payer_partie_decimale = $block_net_a_payer[1];
                                }

                                $montant_acompte = number_format((float)$cotation_demande_achat->montant_acompte, 2, '.', '');

                                $block_montant_acompte = explode(".",$montant_acompte);
                                
                                
                                
                                if (isset($block_montant_acompte[0])) {
                                    $montant_acompte_partie_entiere = $block_montant_acompte[0];
                                }

                                
                                if (isset($block_montant_acompte[1])) {
                                    $montant_acompte_partie_decimale = $block_montant_acompte[1];
                                }
                            }
                        ?>
                        <tfoot>
                            <tr>
                                <td colspan="8" style="border: none">
                                    <div class="row d-flex pl-3">
                                                           
                                            <div class="pr-0" style="text-align:center"><label class="label" class=" mt-1 mr-1">Montant total brut</label><br>  <input disabled 
                                                
                                            @if($cotation_demande_achat!= null) 

                                                @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                
                                            @endif 
                                            
                                            required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut">
                                        
                                            </div>

                                            <div class="pl-1" style="text-align:center"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>  <input disabled oninput ="validateNumber(this);" onkeyup="editTauxRemiseGenerale(this)" autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale" style="width:90px; text-align:center;" 
                                                
                                                @if(isset($taux_remise_generale_partie_decimale) && $taux_remise_generale_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                @else
                                                value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif
                                            >
                                            </div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class="  mt-1 mr-1">Remise</label><br>  <input disabled 
                                                
                                                @if($cotation_demande_achat!= null)

                                                    @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif

                                                @endif 
                                                
                                                onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off"  style="width:110px; text-align:right;" type="text" name="remise_generale" class="form-control remise_generale"></div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total net ht</label><br>  <input disabled 
                                                
                                                @if($cotation_demande_achat!= null) 

                                                    @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                
                                                @endif 
                                                
                                                required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"></div>

                                            
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">TVA (%)</label><br>  <input disabled list="list_taxe_tva" @if($cotation_demande_achat!= null) value="{{ $cotation_demande_achat->tva }}" @endif onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off" style=" width:80px; text-align:center" type="text" name="tva" class="form-control tva"></div>
                                            
                                            <?php 
                                            if(isset($cotation_demande_achat->montant_total_net)){
                                            

                                                    $montant_tva = number_format((float)(($cotation_demande_achat->montant_total_net * ($cotation_demande_achat->tva)/100)), 2, '.', '');

                                                    $montant_tva = number_format((float)$montant_tva, 2, '.', '');

                                                    $block_montant_tva = explode(".",$montant_tva);
                                                    
                                                    $montant_tva_partie_entiere = null;
                                                    
                                                    if (isset($block_montant_tva[0])) {
                                                        $montant_tva_partie_entiere = $block_montant_tva[0];
                                                    }

                                                    $montant_tva_partie_decimale = null;
                                                    if (isset($block_montant_tva[1])) {
                                                        $montant_tva_partie_decimale = $block_montant_tva[1];
                                                    }

                                            }
                                            ?>
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input disabled 
                                                
                                                @if($cotation_demande_achat!= null) 

                                                    @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                
                                                @endif  
                                                
                                                autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva"></div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total ttc</label><br>  <input disabled 
                                                
                                                @if($cotation_demande_achat!= null) 

                                                    @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                @endif
                                                
                                                required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"></div>
                                    </div>
                                    <div class="row d-flex pl-3">
            
                                        <div class="pr-0"><label class="label" class=" mt-1 mr-2">Assiette BNC</label><br>  <input disabled @if($cotation_demande_achat!= null) value="{{ $cotation_demande_achat->assiete_bnc }}" @endif  autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="assiette" class="form-control assiette"></div>
            
                                        <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Taux BNC</label><br>  <input disabled @if($cotation_demande_achat!= null) value="{{ $cotation_demande_achat->taux_bnc }}" @endif autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:60px;" oninput="validateNumber(this);" type="text" name="taux_bnc" class="form-control"></div>
            
                                        <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input disabled @if($cotation_demande_achat!= null) value="{{ ($cotation_demande_achat->assiete_bnc * ($cotation_demande_achat->taux_bnc)/100) }}" @endif autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="montant_bnc" class="form-control montant_bnc"></div>
            
                                        <div class="pl-1" style="text-align:center" style="margin-left:0px;"><label class="label" class=" mt-1 mr-2">Net à payer</label><br>  <input disabled 
                                            @if($cotation_demande_achat!= null) 

                                                @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif

                                            @endif 
                                            
                                            required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"> </div>

                                        <div style="padding-left:3px; text-align:center" ><label class="label" class=" mt-1 mr-2">Acompte</label><br>  <input disabled style="vertical-align:middle;" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" @if($cotation_demande_achat!= null) @if($cotation_demande_achat->acompte === 1) checked  @endif  @endif ></div>

                                        <div  
                                        
                                        @if($cotation_demande_achat!= null)
                                        
                                            @if($cotation_demande_achat->acompte === 1) 
                                                style="padding-left: 10px; text-align: center" 
                                            @else
                                                style="padding-left: 10px; text-align: center; display:none"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center; display:none"
                                        @endif
                                        
                                        id="acompte_taux_div" ><label class="label" class=" mt-1 mr-2">(%)</label><br>  <input disabled disabled maxlength="3" onkeyup="editTauxAcompte(this)" @if($demande_achat_info!= null) value="{{ $cotation_demande_achat->taux_acompte ?? $demande_achat_info->taux_acompte ?? 0 }}" @endif autocomplete="off" oninput="validateNumber(this);" style=" width:70px; text-align:center;" type="text" name="taux_acompte" class="form-control taux_acompte"></div>

                                        <div 
                                        
                                        
                                        @if($cotation_demande_achat!= null)
                                        
                                            @if($cotation_demande_achat->acompte === 1) 
                                                style="padding-left: 10px; text-align: center; display:none" 
                                            @else
                                                style="padding-left: 10px; text-align: center"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center"
                                        @endif

                                        
                                        id="i_acompte_taux_div" ><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input disabled onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                        <div class="pl-1" 
                                        
                                        @if($cotation_demande_achat!= null)
                                            
                                            @if($cotation_demande_achat->acompte === 1) 
                                                style="text-align:center;"
                                            @else
                                            style="text-align:center; display:none"
                                            @endif

                                        @else
                                        style="text-align:center; display:none"
                                        @endif 
                                        
                                        id="acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input disabled disabled 
                                        
                                        @if($cotation_demande_achat!= null) 

                                        @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif 

                                        @endif 
                                        
                                        autocomplete="off" onkeyup="editAcompte(this)" oninput="validateNumber(this);" style=" width:110px; text-align:right" type="text" name="montant_acompte" class="form-control montant_acompte"></div>

                        
                                    
                                    </div>
                                </td>
                
                            </tr>
                            
                        </tfoot>
                    </table>
                </div>
                </form>

            </div>



            
        </div>


        
    </div>
</div>

@endsection

@section('javascripts')
    <!-- jQuery -->
  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- ChartJS -->
  <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
  <!-- Sparkline -->
  <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
  <!-- JQVMap -->
  <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
  <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
  <!-- jQuery Knob Chart -->
  <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
  <!-- daterangepicker -->
  <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
  <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
  <!-- Summernote -->
  <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
  <!-- overlayScrollbars -->
  <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('dist/js/adminlte.js') }}"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="{{ asset('dist/js/demo.js') }}"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="{{ asset('dist/js/pages/dashboard.js') }}"></script>
@endsection
