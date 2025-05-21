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
                    <span style="float: right; margin-right: 20px;">STRUCTURE : <strong>{{ $structure->nom_structure ?? '' }}</strong></span>
                    
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
                                            <input style="background-color: #e9ecef; display:none" onfocus="this.blur()" value="{{ $demande_achat_info->id ?? '' }}" autocomplete="off" type="text" name="id" class="form-control">
                                            
                                            <input style="color:red; font-weight:bold; margin-top:-6px;" onfocus="this.blur()" value="{{ $num_bc ?? '' }}" autocomplete="off" type="text" name="num_bc" class="form-control griser">
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
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $demande_achat_info->ref_fam ?? ''}}" autocomplete="off" type="text" name="ref_fam" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $demande_achat_info->design_fam ?? ''}}" autocomplete="off" type="text" name="design_fam" class="form-control griser">
                                            
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
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $commande->libelle_periode ?? '' }}" autocomplete="off" type="text" name="demande_periode_id" class="form-control">
                                            <input value="{{ $commande->valeur ?? '' }}" style="display: none" onfocus="this.blur()" type="text" id="demande_valeur" name="demande_valeur" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" onkeypress="validate(event)" value="{{ $commande->delai ?? '' }}" autocomplete="off" type="text" id="demande_delai" name="demande_delai" class="form-control">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <div class="form-group d-flex ">
                                            <input @if(isset($commande->date_echeance)) value="{{ date("d/m/Y H:i:s",strtotime($commande->date_echeance)) }}" @endif style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="demande_date_echeance" name="demande_date_echeance" class="form-control">
                                            
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
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $fournisseur->organisations_id ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $fournisseur->entnum ?? '' }}" autocomplete="off" type="text" name="entnum" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $fournisseur->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control griser">
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
                                            <!-- onkeyup="editDevise(this)" <span style="color: red"><sup> *</sup></span> -->
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ old('code_devise') ?? $devise_cotation->code ?? $devise_default->code ?? '' }}"  list="list_devise" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input value="{{ old('libelle_devise') ?? $devise_cotation->libelle ?? $devise_default->libelle ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control griser">
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
                                <th style="width:12%; text-align:center">QTÉ<span style="color: red"><sup> *</sup></span></th>
                                <th style="width:15%; text-align:center">PU HT<span style="color: red"><sup> *</sup></span></th>
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
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $demande_achat->ref_articles ?? '' }}" autocomplete="off" required type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">

                                    {{ $demande_achat->ref_articles ?? '' }}
                                </td>
                                <td style="vertical-align:middle;">
                                    <input value="{{ $demande_achat->design_article ?? '' }}" autocomplete="off" required onfocus="this.blur()" style="background-color: #e9ecef; display:none" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article">

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
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" onkeyup="editMontant(this)" value="{{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" name="qte_demandee[]" class="form-control qte_demandee">

                                    {{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? ''), 3, ' ', true)) }}

                                </td>
                                <td style=" vertical-align:middle; background-color:white">
                                    <input style="text-align:center; border:none" value="{{ strrev(wordwrap(strrev($qte ?? $demande_achat->qte_accordee ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte">
                                </td>
                                <td style=" vertical-align:middle; background-color:white">
                                    <input style="text-align:right; border:none" 
                                    
                                    @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif
                                    
                                    autocomplete="off" required type="text" onkeyup="editMontant(this)" oninput="validateNumber(this);" name="prix_unit[]" class="form-control prix_unit">

                                </td>
                                <td style="width:1px; white-space:nowrap;  vertical-align:middle; background-color:white">
                                    <input
                                    @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif
                                    autocomplete="off" type="text" style="text-align: center; border:none" onkeyup="editMontant(this)" oninput="validateNumber(this);" name="remise[]" class="form-control remise">
                                </td>
                                <td style="vertical-align:middle;">
                                    <input                                     
                                    @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif

                                    autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: transparent; text-align:right; border-color:transparent" type="text" name="montant_ht[]" class="form-control montant_ht">


                                    <input value="{{ $montant_ht ?? '' }}" autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis">

                                    

                                </td>
                                
                                <td style="text-align: left; vertical-align:middle;">

                                    <input style="display: none" type="checkbox" name="echantillon_flag[]" checked @if(isset($echantillon)) value="1" @else value="0" @endif>

                                    <input style="height: 30px;" accept="image/*" @if(!isset($echantillon)) @endif  type="file" name="echantillon[{{ $demande_achat->ref_articles ?? '' }}]" id="echantillon[{{ $demande_achat->ref_articles ?? '' }}]">

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
                                                           
                                            <div class="pr-0" style="text-align:center"><label class="label" class=" mt-1 mr-1">Montant total brut</label><br>  <input 
                                                
                                            @if($cotation_demande_achat!= null) 

                                                @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                
                                            @endif 
                                            
                                            required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut">
                                        
                                            </div>

                                            <div class="pl-1" style="text-align:center"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>  <input oninput ="validateNumber(this);" onkeyup="editTauxRemiseGenerale(this)" autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale" style="width:90px; text-align:center;" 
                                                
                                                @if(isset($taux_remise_generale_partie_decimale) && $taux_remise_generale_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                @else
                                                value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif
                                            >
                                            </div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class="  mt-1 mr-1">Remise</label><br>  <input 
                                                
                                                @if($cotation_demande_achat!= null)

                                                    @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif

                                                @endif 
                                                
                                                onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off"  style="width:110px; text-align:right;" type="text" name="remise_generale" class="form-control remise_generale"></div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total net ht</label><br>  <input 
                                                
                                                @if($cotation_demande_achat!= null) 

                                                    @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                
                                                @endif 
                                                
                                                required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"></div>

                                            
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">TVA (%)</label><br>  <input list="list_taxe_tva" @if($cotation_demande_achat!= null) value="{{ $cotation_demande_achat->tva }}" @endif onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off" style=" width:80px; text-align:center" type="text" name="tva" class="form-control tva"></div>
                                            
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
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input 
                                                
                                                @if($cotation_demande_achat!= null) 

                                                    @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                
                                                @endif  
                                                
                                                autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva"></div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total ttc</label><br>  <input 
                                                
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
            
                                        <div class="pr-0"><label class="label" class=" mt-1 mr-2">Assiette BNC</label><br>  <input @if($cotation_demande_achat!= null) value="{{ $cotation_demande_achat->assiete_bnc }}" @endif  autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="assiette" class="form-control assiette"></div>
            
                                        <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Taux BNC</label><br>  <input @if($cotation_demande_achat!= null) value="{{ $cotation_demande_achat->taux_bnc }}" @endif autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:60px;" oninput="validateNumber(this);" type="text" name="taux_bnc" class="form-control"></div>
            
                                        <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input @if($cotation_demande_achat!= null) value="{{ ($cotation_demande_achat->assiete_bnc * ($cotation_demande_achat->taux_bnc)/100) }}" @endif autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="montant_bnc" class="form-control montant_bnc"></div>
            
                                        <div class="pl-1" style="text-align:center" style="margin-left:0px;"><label class="label" class=" mt-1 mr-2">Net à payer</label><br>  <input 
                                            @if($cotation_demande_achat!= null) 

                                                @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif

                                            @endif 
                                            
                                            required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"> </div>

                                        <div style="padding-left:3px; text-align:center" ><label class="label" class=" mt-1 mr-2">Acompte</label><br>  <input style="vertical-align:middle;" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" @if($cotation_demande_achat!= null) @if($cotation_demande_achat->acompte === 1) checked  @endif  @endif ></div>

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
                                        
                                        id="acompte_taux_div" ><label class="label" class=" mt-1 mr-2">(%)</label><br>  <input disabled maxlength="3" onkeyup="editTauxAcompte(this)" @if($demande_achat_info!= null) value="{{ $cotation_demande_achat->taux_acompte ?? $demande_achat_info->taux_acompte ?? 0 }}" @endif autocomplete="off" oninput="validateNumber(this);" style=" width:70px; text-align:center;" type="text" name="taux_acompte" class="form-control taux_acompte"></div>

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

                                        
                                        id="i_acompte_taux_div" ><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

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
                                        
                                        id="acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input disabled 
                                        
                                        @if($cotation_demande_achat!= null) 

                                        @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif 

                                        @endif 
                                        
                                        autocomplete="off" onkeyup="editAcompte(this)" oninput="validateNumber(this);" style=" width:110px; text-align:right" type="text" name="montant_acompte" class="form-control montant_acompte"></div>

                                        <div class="pl-1" style="text-align:center"
                                        
                                        @if($cotation_demande_achat!= null)
                                        
                                            @if($cotation_demande_achat->acompte === 1) 
                                                style="display:none"
                                            @else
                                            
                                            @endif

                                        @else
                                        
                                        @endif

                                        id="i_acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                        <div style="padding-left: 10px;" ><label class="label" class=" mt-1 mr-2">&nbsp;</label><br></div>
                                    
                                    </div>
                                </td>
                
                            </tr>
                            
                        </tfoot>
                    </table>
                    <table style="width:100%;"> 
                        <tr>
                            <td colspan="4" style="border-bottom: none">
                                <span style="font-weight: bold">Dernier commentaire du dossier </span> : écrit par <span style="color: brown; margin-left:3px;"> {{ $nom_prenoms_commentaire ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $profil_commentaire ?? '' }} </span>)

                                <br>
                                <br>
                                <table width="100%" cellspacing="0" border="1" style="background-color:#d4edda; border-color:#155724; font-weight:bold">
                                    <tr>
                                        <td>
                                            <svg style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text-fill" viewBox="0 0 16 16">
                                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
                                              </svg> &nbsp; {{ $commentaire ?? '' }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border: none; vertical-align:middle; text-align:right">

                                <div class="row justify-content-center">
                                
                                    <div class="col-md-9">

                                        <textarea style="width: 100%; resize:none" class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                        @error('commentaire')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    

                                    <div class="col-md-3" style="margin-top:0px"> 

                                        <button onclick="return confirm('Faut-il confirmer l\'enregistrement de la cotation ?')" style="margin-top: 7px" type="submit" class="btn btn-success">Enregistrer</button>

                                    </div>
                                </div>
                                
                            </td>

                        

                        </tr>
                    </table>
                </div>
                </form>

            </div>



            
        </div>


        
    </div>
</div>
  
<!--    
<script>
    editPeriode = function(a){
        const saisie=document.getElementById('periode_id').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var periode_id = block[1];
            var valeur = block[2];

            var delai = document.getElementById('delai').value;
            if(delai==""){
                delai = 1;
            }


            var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
            
            var date_echeance = moment(d).format('DD/MM/YYYY');
        }else{
            periode_id = "";
            valeur = "";
        }
        
        document.getElementById('periode_id').value = periode_id;
        if (valeur === undefined) {
            document.getElementById('valeur').value = "";
        }else{
            document.getElementById('valeur').value = valeur;
        }

        if (date_echeance === 'Invalid date') {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
        }
        
    }

    editPeriode2 = function(a){
            var periode_id = document.getElementById('periode_id').value;
            var valeur = document.getElementById('valeur').value;
            var delai = document.getElementById('delai').value;
            if (periode_id != '') {
                if (valeur != undefined) {
                    if(delai==""){
                        delai = 1;
                    }


                    var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
                    
                    var date_echeance = moment(d).format('DD/MM/YYYY');
                }
            }
        
        
        
        if (periode_id === undefined) {
            
        }else{
            document.getElementById('periode_id').value = periode_id;
        }

        if (valeur === undefined) {
            
        }else{
            document.getElementById('valeur').value = valeur;
        }

        if (date_echeance === 'Invalid date') {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
        }

        if (date_echeance === undefined) {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
        }
        
    }

    editDevise = function(a){
            var value = document.getElementById('code_devise').value;
            
            if (value != '') {
                const block = value.split('->');
                var code_devise = block[0];
                var libelle_devise = block[1];
                var symbole_devise = block[3];
            }else{
                code_devise = "";
                libelle_devise = "";
                symbole_devise = "";
            }
        
        
        
            document.getElementById('code_devise').value = code_devise;
            document.getElementById('libelle_devise').value = libelle_devise;

        
        
    }

    function doalert(checkboxElem) {
        if (checkboxElem.checked) {
            document.getElementById("acompte_div").style.display = ""; 
            document.getElementById("acompte_taux_div").style.display = "";

            document.getElementById("i_acompte_div").style.display = "none"; 
            document.getElementById("i_acompte_taux_div").style.display = "none";
        } else {
            document.getElementById("acompte_div").style.display = "none"; 
            document.getElementById("acompte_taux_div").style.display = "none";

            document.getElementById("i_acompte_div").style.display = ""; 
            document.getElementById("i_acompte_taux_div").style.display = "";
        }
    } 

    // Empecher la saisie de lettre
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
    // annuler le séparateur de millier

    function reverseFormatNumber(val,locale){
        var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
        var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
        var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
        reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
        return Number.isNaN(reversedVal)?0:reversedVal;
    }

    
    editMontant = function(e){

        var tr=$(e).parents("tr");

        var design_article=tr.find('.design_article').val();               

        var qte_demandee=tr.find('.qte_demandee').val();
        qte_demandee = qte_demandee.trim();
        qte_demandee = qte_demandee.replace(' ','');
        qte_demandee = reverseFormatNumber(qte_demandee,'fr');
        qte_demandee = qte_demandee * 1;

        var qte=tr.find('.qte').val();
        qte = qte.trim();
        qte = qte.replace(' ','');
        qte = reverseFormatNumber(qte,'fr');
        qte = qte * 1;

        if (qte_demandee<qte) {

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'La quantité proposée ne peut être supérieure à la quantité demandée',
            focusConfirm: false,
            confirmButtonText:
                'Compris'
            });

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte').val(int3.format(qte_demandee));

        }else{

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte').val(int3.format(qte));

        }


        var prix_unit=tr.find('.prix_unit').val();
        prix_unit = prix_unit.trim();
        prix_unit = prix_unit.replace(' ','');
        prix_unit = reverseFormatNumber(prix_unit,'fr');

        var remise=tr.find('.remise').val();
        remise = remise.trim();
        remise = remise.replace(' ','');
        remise = reverseFormatNumber(remise,'fr');

        

        
        
        
        var montant_ht=( ( prix_unit * qte ) - ((( prix_unit * qte ) * remise)/100) );

        var ref_articles=tr.find('.ref_articles').val();
        // var echantillon = document.getElementById("echantillon["+ref_articles+"]");

        // if (montant_ht > 0) {
        //     echantillon.style.required = "required";
        //     echantillon.setAttribute("required", ""); 
        //     echantillon.required = true;               
        //     jQuery(echantillon).attr('required', ''); 
        //     $("#echantillon["+ref_articles+"]").attr('required', '');

        // }else if(montant_ht === 0){

        //     echantillon.removeAttribute("required");   
        //     echantillon.required = false;               
        //     jQuery(echantillon).removeAttr('required');
        //     $("#echantillon["+ref_articles+"]").removeAttr('required');

        // }
        

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        tr.find('.prix_unit').val(int3.format(prix_unit));
        tr.find('.remise').val(int3.format(remise));
        tr.find('.montant_ht').val(int3.format(montant_ht));
        tr.find('.montant_ht_bis').val(montant_ht);

        totalCf();
    }

    function totalCf(){
            var montant_total_brut=0;
            $('.montant_ht_bis').each(function(i,e){
                var montant_ht_bis =$(this).val()-0;
                montant_total_brut +=montant_ht_bis;
            });            
            
            var remise_generale=$('.remise_generale').val();
            remise_generale = remise_generale.trim();
            remise_generale = remise_generale.replace(' ','');
            remise_generale = reverseFormatNumber(remise_generale,'fr');

            var montant_total_net = montant_total_brut - remise_generale;

            var tva=$('.tva').val();

            if(tva === null){
                tva = 0;
            }else{
                tva = (tva/100);
            }

            var montant_tva = montant_total_net * tva;
            
            var montant_total_ttc = montant_total_net + montant_tva;

            var net_a_payer = montant_total_ttc;

            var montant_acompte=$('.montant_acompte').val();  
            montant_acompte = montant_acompte.trim();
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = reverseFormatNumber(montant_acompte,'fr');  
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = montant_acompte * 1;

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

              var taux_acompte=$('.taux_acompte').val();
                taux_acompte = taux_acompte.trim();
                taux_acompte = taux_acompte.replace(' ','');
                taux_acompte = reverseFormatNumber(taux_acompte,'fr');
                taux_acompte = taux_acompte * 1;

                if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                    // alert(taux_acompte);
                    if(taux_acompte === null){
                        taux_acompte = 0;
                    }else{
                        taux_acompte = (taux_acompte/100);
                    }

                    var montant_acompte = net_a_payer * taux_acompte;

                    $('.montant_acompte').val(int3.format(montant_acompte)); 
                }            

            
            $('.remise_generale').val(int3.format(remise_generale)); 
            $('.montant_total_brut').val(int3.format(montant_total_brut)); 
            $('.montant_total_net').val(int3.format(montant_total_net)); 
            $('.montant_total_ttc').val(int3.format(montant_total_ttc)); 
            $('.net_a_payer').val(int3.format(net_a_payer));  
            $('.montant_tva').val(int3.format(montant_tva));      
    
    }

    editRemiseGenerale = function(e){
        var tr=$(e).parents("tr");
        
        var remise_generale=tr.find('.remise_generale').val();
        remise_generale = remise_generale.trim();
        remise_generale = remise_generale.replace(' ','');
        remise_generale = reverseFormatNumber(remise_generale,'fr');
        remise_generale = remise_generale.replace(' ','');
        
        var montant_total_brut=tr.find('.montant_total_brut').val();  
        montant_total_brut = montant_total_brut.trim();
        montant_total_brut = montant_total_brut.replace(' ','');
        montant_total_brut = reverseFormatNumber(montant_total_brut,'fr');  
        montant_total_brut = montant_total_brut.replace(' ','');

        var montant_total_net= montant_total_brut - remise_generale;

        var tva=tr.find('.tva').val();
        

        if(tva == ""){
            tva = 0;
        }else{
            tva = (tva/100);
        }

        

        var montant_tva = montant_total_net * tva;

        

        
        var montant_total_ttc = montant_total_net + montant_tva;
        var net_a_payer = montant_total_ttc;



        var montant_acompte=tr.find('.montant_acompte').val();  
        montant_acompte = montant_acompte.trim();
        montant_acompte = montant_acompte.replace(' ','');
        montant_acompte = reverseFormatNumber(montant_acompte,'fr');  
        montant_acompte = montant_acompte.replace(' ','');
        montant_acompte = montant_acompte * 1;

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

            var taux_acompte=$('.taux_acompte').val();
            taux_acompte = taux_acompte.trim();
            taux_acompte = taux_acompte.replace(' ','');
            taux_acompte = reverseFormatNumber(taux_acompte,'fr');
            taux_acompte = taux_acompte * 1;

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                
                if(taux_acompte === null){
                    taux_acompte = 0;
                }else{
                    taux_acompte = (taux_acompte/100);
                }

                var montant_acompte = net_a_payer * taux_acompte;

                $('.montant_acompte').val(int3.format(montant_acompte)); 
            }

        

        

        
            
        tr.find('.montant_total_net').val(int3.format(montant_total_net));
        tr.find('.montant_tva').val(int3.format(montant_tva));
        tr.find('.montant_total_ttc').val(int3.format(montant_total_ttc));
        tr.find('.net_a_payer').val(int3.format(net_a_payer));
        tr.find('.remise_generale').val(int3.format(remise_generale));
    }

    editAcompte = function(e){
        var tr=$(e).parents("tr");

        
        
        var net_a_payer=tr.find('.net_a_payer').val();
        net_a_payer = net_a_payer.trim();
        net_a_payer = net_a_payer.replace(' ','');
        net_a_payer = reverseFormatNumber(net_a_payer,'fr');
        net_a_payer = net_a_payer.replace(' ','');

        if (net_a_payer!='') {

            var taux_acompte=$('.taux_acompte').val();
            taux_acompte = taux_acompte.trim();
            taux_acompte = taux_acompte.replace(' ','');
            taux_acompte = reverseFormatNumber(taux_acompte,'fr');
            taux_acompte = taux_acompte * 1;

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                
                if(taux_acompte === null){
                    taux_acompte = 0;
                }else{
                    taux_acompte = (taux_acompte/100);
                }

                var montant_acompte = net_a_payer * taux_acompte;

                $('.montant_acompte').val(int3.format(montant_acompte)); 
            }

            
                
            


        }else{
            tr.find('.montant_acompte').val('');
        }
        
        
    }

    editTauxAcompte = function(e){
        var tr=$(e).parents("tr");

        
        
        var net_a_payer=tr.find('.net_a_payer').val();
        net_a_payer = net_a_payer.trim();
        net_a_payer = net_a_payer.replace(' ','');
        net_a_payer = reverseFormatNumber(net_a_payer,'fr');
        net_a_payer = net_a_payer.replace(' ','');

        if (net_a_payer!='') {

            var taux_acompte=$('.taux_acompte').val();
            taux_acompte = taux_acompte.trim();
            taux_acompte = taux_acompte.replace(' ','');
            taux_acompte = reverseFormatNumber(taux_acompte,'fr');
            taux_acompte = taux_acompte * 1;

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                
                if(taux_acompte === null){
                    taux_acompte = 0;
                }else{
                    taux_acompte = (taux_acompte/100);
                }

                var montant_acompte = net_a_payer * taux_acompte;

                $('.montant_acompte').val(int3.format(montant_acompte)); 
            }

            


        }else{
            tr.find('.montant_acompte').val('');
        }
        
        
    }
    
</script> -->
<script>

    editPeriode = function(a){
        const saisie=document.getElementById('periodes_id').value;

        const opts = document.getElementById('list_periode').childNodes;

        
        for (var i = 0; i < opts.length; i++) {

            if (opts[i].value === saisie) {

                if(saisie != ''){
                    const block = saisie.split('->');
                    var periodes_id = block[1];
                    var valeur = block[2];

                    var delai = document.getElementById('delai').value;
                    if(delai==""){
                        delai = 1;
                    }


                    var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
                    
                    var date_echeance = moment(d).format('DD/MM/YYYY');
                }else{
                    periodes_id = "";
                    valeur = "";
                }
        

                if (periodes_id === undefined) {
                    
                }else{
                    document.getElementById('periodes_id').value = periodes_id;
                }

                if (valeur === undefined) {
                    document.getElementById('valeur').value = "";
                    document.getElementById('delai').value = "";
                }else{
                    document.getElementById('valeur').value = valeur;
                    document.getElementById('delai').value = 1;
                }

                if (date_echeance === 'Invalid date') {
                    document.getElementById('date_echeance').value = "";
                }else{
                    document.getElementById('date_echeance').value = date_echeance;
                }
                break;
            }else{
                document.getElementById('valeur').value = "";
                document.getElementById('delai').value = "";
                document.getElementById('date_echeance').value = "";
            }
        }
        
    }

    editPeriode2 = function(a){
            var periodes_id = document.getElementById('periodes_id').value;
            var valeur = document.getElementById('valeur').value;
            var delai = document.getElementById('delai').value;
            if (periodes_id != '') {
                if (valeur != undefined) {
                    if(delai==""){
                        delai = 1;
                    }


                    var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
                    
                    var date_echeance = moment(d).format('DD/MM/YYYY');
                }
            }
        
        
        
        if (periodes_id === undefined) {
            
        }else{
            document.getElementById('periodes_id').value = periodes_id;
        }

        if (valeur === undefined) {
            
        }else{
            document.getElementById('valeur').value = valeur;
        }

        if (date_echeance === 'Invalid date') {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
        }

        if (date_echeance === undefined) {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
        }
        
    }

    function doalert(checkboxElem) {
        if (checkboxElem.checked) {
            document.getElementById("acompte_div").style.display = ""; 
            document.getElementById("acompte_taux_div").style.display = "";

            document.getElementById("i_acompte_div").style.display = "none"; 
            document.getElementById("i_acompte_taux_div").style.display = "none";
        } else {
            document.getElementById("acompte_div").style.display = "none"; 
            document.getElementById("acompte_taux_div").style.display = "none";

            document.getElementById("i_acompte_div").style.display = ""; 
            document.getElementById("i_acompte_taux_div").style.display = "";
        }
    }

    // Empecher la saisie de lettre
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
    
    function validateNumber(elem) {
    	
    	var theEvent = elem || window.event;
    	var validNumber = new RegExp(/^\d*\.?\d*$/); 

        var elem_value = elem.value.trim();
        elem_value = elem_value.replace(' ','');
        elem_value = reverseFormatNumber(elem_value,'fr');

    	if (validNumber.test(elem_value)) {
	    	lastValid = elem_value;
		} else {
			let lastValid = elem_value;
			if (theEvent.type === 'paste') {
				elem.value = lastValid.slice(0, -1);
			}else{
				elem.value = '';
				
			}
		}
    }
    // annuler le séparateur de millier

    function reverseFormatNumber(val,locale){
        var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
        var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
        var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
        reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
        return Number.isNaN(reversedVal)?0:reversedVal;
    }

    editMontant = function(e){

        var tr=$(e).parents("tr");

        var qte=tr.find('.qte').val();
        qte = qte.trim();
        qte = qte.replace(' ','');
        qte = reverseFormatNumber(qte,'fr');

        var prix_unit=tr.find('.prix_unit').val();
        prix_unit = prix_unit.trim();
        prix_unit = prix_unit.replace(' ','');
        prix_unit = reverseFormatNumber(prix_unit,'fr');        

        var remise=tr.find('.remise').val();
        remise = remise.trim();
        remise = remise.replace(' ','');
        remise = reverseFormatNumber(remise,'fr');

        if (remise > 100) {
            alert("Attention !!!  Taux remise invalide : Le taux de la remise ne peut être supérieur à 100%.");
            remise = 0;
        }
        
        var montant_ht=( ( prix_unit * qte ) - ((( prix_unit * qte ) * remise)/100) );

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        montant_ht = montant_ht.toFixed(2);   
        
        prix_unit_display = formatInt3(prix_unit);

        montant_ht_display = formatInt3(montant_ht);

        tr.find('.qte').val(int3.format(qte));
        tr.find('.prix_unit').val(prix_unit_display);
        tr.find('.remise').val(remise);
        tr.find('.montant_ht').val(montant_ht_display);
        tr.find('.montant_ht_bis').val(montant_ht);

        totalCf();
    }    

    function totalCf(){
            var montant_total_brut=0;
            $('.montant_ht_bis').each(function(i,e){
                var montant_ht_bis =$(this).val()-0;
                montant_total_brut +=montant_ht_bis;
            }); 

            montant_total_brut = montant_total_brut.toFixed(2);
            
            
            var remise_generale=$('.remise_generale').val();
            remise_generale = remise_generale.trim();
            remise_generale = remise_generale.replace(' ','');
            remise_generale = reverseFormatNumber(remise_generale,'fr');
            remise_generale = remise_generale.replace(' ','');

            var taux_remise_generale=$('.taux_remise_generale').val();
            taux_remise_generale = taux_remise_generale.trim();
            taux_remise_generale = taux_remise_generale.replace(' ','');
            taux_remise_generale = reverseFormatNumber(taux_remise_generale,'fr');
            taux_remise_generale = taux_remise_generale * 1;

            if ((taux_remise_generale >= 0) && (taux_remise_generale <= 100)) {
                // alert(taux_remise_generale);
                if(taux_remise_generale === null){
                    taux_remise_generale = 0;
                }else{
                    taux_remise_generale = (taux_remise_generale/100);
                }

                var remise_generale = montant_total_brut * taux_remise_generale;
                remise_generale = remise_generale.toFixed(2);

                $('.remise_generale').val(remise_generale); 
            }


            var montant_total_net = montant_total_brut - remise_generale;

            var tva=$('.tva').val();

            if(tva === null){
                tva = 0;
            }else{
                tva = (tva/100);
            }

            var montant_tva = montant_total_net * tva;
            
            var montant_total_ttc = montant_total_net + montant_tva;

            var net_a_payer = montant_total_ttc;

            var montant_acompte=$('.montant_acompte').val();  
            montant_acompte = montant_acompte.trim();
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = reverseFormatNumber(montant_acompte,'fr');  
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = montant_acompte * 1;

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});


            
            var taux_acompte=$('.taux_acompte').val();
            if (taux_acompte != '') {
                taux_acompte = taux_acompte.trim();
                taux_acompte = taux_acompte.replace(' ','');
                taux_acompte = reverseFormatNumber(taux_acompte,'fr');
                taux_acompte = taux_acompte * 1;

                if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                    // alert(taux_acompte);
                    if(taux_acompte === null){
                        taux_acompte = 0;
                    }else{
                        taux_acompte = (taux_acompte/100);
                    }

                    var montant_acompte = net_a_payer * taux_acompte;

                    montant_acompte = montant_acompte.toFixed(2);
                    montant_acompte_display = formatInt3(montant_acompte);

                    $('.montant_acompte').val(montant_acompte_display); 
                }else{
                    $('.montant_acompte').val(0); 
                }
            }else{
                $('.montant_acompte').val(0); 
            }

            
                   
            montant_total_net = montant_total_net.toFixed(2);
            montant_total_ttc = montant_total_ttc.toFixed(2);
            net_a_payer = net_a_payer.toFixed(2);
            montant_tva = montant_tva.toFixed(2);

            remise_generale_display = formatInt3(remise_generale);
            montant_total_brut_display = formatInt3(montant_total_brut);
            montant_total_net_display = formatInt3(montant_total_net);
            montant_total_ttc_display = formatInt3(montant_total_ttc);
            net_a_payer_display = formatInt3(net_a_payer);
            montant_tva_display = formatInt3(montant_tva);

            $('.remise_generale').val(remise_generale_display); 
            $('.montant_total_brut').val(montant_total_brut_display); 
            $('.montant_total_net').val(montant_total_net_display); 
            $('.montant_total_ttc').val(montant_total_ttc_display); 
            $('.net_a_payer').val(net_a_payer_display);  
            $('.montant_tva').val(montant_tva_display);      
    
    }

    editRemiseGenerale = function(e){
        var tr=$(e).parents("tr");
        
        var remise_generale=tr.find('.remise_generale').val();
        remise_generale = remise_generale.trim();
        remise_generale = remise_generale.replace(' ','');
        remise_generale = remise_generale.replace(' ','');
        remise_generale = remise_generale.replace(' ','');
        remise_generale = remise_generale.replace(' ','');

        remise_generale = reverseFormatNumber(remise_generale,'fr');
        
        
        var montant_total_brut=tr.find('.montant_total_brut').val();  
        montant_total_brut = montant_total_brut.trim();
        montant_total_brut = montant_total_brut.replace(' ','');
        montant_total_brut = montant_total_brut.replace(' ','');
        montant_total_brut = montant_total_brut.replace(' ','');
        montant_total_brut = montant_total_brut.replace(' ','');

        montant_total_brut = reverseFormatNumber(montant_total_brut,'fr'); 

        if (montant_total_brut!='') {

            var taux_remise_generale = (remise_generale / montant_total_brut) * 100;

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

            if ((taux_remise_generale >= 0) && (taux_remise_generale <= 100)) {

                taux_remise_generale = taux_remise_generale.toFixed(2);
                $('.taux_remise_generale').val(taux_remise_generale); 

            }else{

                alert("Attention !!!  Taux remise invalide : Le taux remise ne peut être supérieur à 100%.");

                tr.find('.taux_remise_generale').val(0);
                tr.find('.remise_generale').val(0);
                remise_generale = 0;

            }

            


        }else{
            tr.find('.taux_remise_generale').val(0);
            tr.find('.remise_generale').val(0);
            remise_generale = 0;
        }    
        
        var montant_total_net= montant_total_brut - remise_generale;

        var tva=tr.find('.tva').val();

        if(tva == ""){
            tva = 0;
        }else{
            tva = (tva/100);
        }        

        var montant_tva = montant_total_net * tva;
        
        var montant_total_ttc = montant_total_net + montant_tva;
        var net_a_payer = montant_total_ttc;

        var taux_acompte=$('.taux_acompte').val();

        if (taux_acompte != '') {
            taux_acompte = taux_acompte.trim();
            taux_acompte = taux_acompte.replace(' ','');
            taux_acompte = taux_acompte.replace(' ','');
            taux_acompte = reverseFormatNumber(taux_acompte,'fr');  
            taux_acompte = taux_acompte * 1;

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                // alert(taux_acompte);
                if(taux_acompte === null){
                taux_acompte = 0;
                }else{
                    taux_acompte = (taux_acompte/100);
                }

                var montant_acompte = net_a_payer * taux_acompte;

                montant_acompte = montant_acompte.toFixed(2);
                montant_acompte_display = formatInt3(montant_acompte);

                $('.montant_acompte').val(montant_acompte_display); 
            }else{
                alert("Attention !!!  Taux acompte invalide : Le taux d'acompte ne peut être supérieur à 100%.");
                tr.find('.taux_acompte').val(0);
                tr.find('.montant_acompte').val(0);
            }
        }else{
            $('.montant_acompte').val(0); 
        }       
        
        montant_total_net = montant_total_net.toFixed(2);
        montant_tva = montant_tva.toFixed(2);
        montant_total_ttc = montant_total_ttc.toFixed(2);
        net_a_payer = net_a_payer.toFixed(2);

        montant_total_net_display = formatInt3(montant_total_net);
        montant_tva_display = formatInt3(montant_tva);
        montant_total_ttc_display = formatInt3(montant_total_ttc);
        net_a_payer_display = formatInt3(net_a_payer);
        remise_generale_display = formatInt3(remise_generale);

       

        

        tr.find('.montant_total_net').val(montant_total_net_display);
        tr.find('.montant_tva').val(montant_tva_display);
        tr.find('.montant_total_ttc').val(montant_total_ttc_display);
        tr.find('.net_a_payer').val(net_a_payer_display);
        tr.find('.remise_generale').val(remise_generale_display);

    }

    function formatInt3(number) {
        //alert("formatInt3 1 : "+number);

        nombre = number.trim();
        nombre = nombre.replace(' ','');
        nombre = nombre.replace(' ','');
        nombre = nombre.replace(' ','');
        nombre = nombre.replace(' ','');
        nombre = reverseFormatNumber(nombre,'fr');

       // alert("formatInt3 2: "+nombre);

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        if(nombre.toString().split('.')[1] !== undefined){

            let decimalDigits = nombre.toString().split('.')[1];
            let decimalPlaces = decimalDigits.length;
            let decimalDivider = Math.pow(10, decimalPlaces);
            let fractionValue = decimalDigits/decimalDivider;
            let integerValue = nombre - fractionValue;

            

            if (decimalDigits == '00') {
                //alert("formatInt3 3 1: "+integerValue);
                var response = int3.format(integerValue);

                //alert("formatInt3 3: "+response);
            }else{
                //alert("formatInt3 4 1: "+integerValue+".".decimalDigits);
                var response = int3.format(integerValue)+'.'+decimalDigits;
                //alert("formatInt3 4: "+response);
            }

        }else{

            //alert("formatInt3 5: "+nombre);
            var response = int3.format(nombre);
            //alert("formatInt3 6: "+response);

        }    

        //alert("formatInt3 7: "+response);

        return response;

    }

    editAcompte = function(e){
        var tr=$(e).parents("tr");

        
        
        var net_a_payer=tr.find('.net_a_payer').val();
        net_a_payer = net_a_payer.trim();
        net_a_payer = net_a_payer.replace(' ','');
        net_a_payer = reverseFormatNumber(net_a_payer,'fr');
        net_a_payer = net_a_payer.replace(' ','');

        if (net_a_payer!='') {
            var montant_acompte=tr.find('.montant_acompte').val();  
            montant_acompte = montant_acompte.trim();
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = reverseFormatNumber(montant_acompte,'fr');  
            montant_acompte = montant_acompte.replace(' ','');

            var taux_acompte = (montant_acompte / net_a_payer) * 100;
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                tr.find('.taux_acompte').val(taux_acompte.toFixed(2));
                tr.find('.montant_acompte').val(montant_acompte.toFixed(2));
            }else{
                alert("Attention !!! Montant acompte invalide : L'acompte ne peut être supérieur au montant net à payer.");
                tr.find('.taux_acompte').val('');
                tr.find('.montant_acompte').val(0);
            }

            
                
            


        }else{
            tr.find('.taux_acompte').val('');
            tr.find('.montant_acompte').val('');
        }
        
        
    }

    editTauxAcompte = function(e){
        var tr=$(e).parents("tr");        
        var net_a_payer=tr.find('.net_a_payer').val();
        net_a_payer = net_a_payer.trim();
        net_a_payer = net_a_payer.replace(' ','');
        net_a_payer = reverseFormatNumber(net_a_payer,'fr');
        net_a_payer = net_a_payer.replace(' ','');

        if (net_a_payer!='') {
            var taux_acompte=tr.find('.taux_acompte').val();  
            taux_acompte = taux_acompte.trim();
            taux_acompte = taux_acompte.replace(' ','');
            taux_acompte = reverseFormatNumber(taux_acompte,'fr');  
            taux_acompte = taux_acompte.replace(' ','');

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                // alert(taux_acompte);
                if(taux_acompte === null){
                taux_acompte = 0;
                }else{
                    taux_acompte = (taux_acompte/100);
                }

                var montant_acompte = net_a_payer * taux_acompte;

                var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

                montant_acompte = montant_acompte.toFixed(2);
                montant_acompte_display = formatInt3(montant_acompte);

                $('.montant_acompte').val(montant_acompte_display); 
            }else{
                alert("Attention !!!  Taux acompte invalide : Le taux d'acompte ne peut être supérieur à 100%.");
                tr.find('.taux_acompte').val("");
                tr.find('.montant_acompte').val(0);
            }

            


        }else{
            tr.find('.taux_acompte').val('');
            tr.find('.montant_acompte').val('');
        }
        
        
    }

    editTauxRemiseGenerale = function(e){
        var tr=$(e).parents("tr");
        
        var montant_total_brut=tr.find('.montant_total_brut').val();
        montant_total_brut = montant_total_brut.trim();
        montant_total_brut = montant_total_brut.replace(' ','');
        montant_total_brut = reverseFormatNumber(montant_total_brut,'fr');
        montant_total_brut = montant_total_brut.replace(' ','');

        if (montant_total_brut!='') {
            var taux_remise_generale=tr.find('.taux_remise_generale').val();  
            taux_remise_generale = taux_remise_generale.trim();
            taux_remise_generale = taux_remise_generale.replace(' ','');
            taux_remise_generale = reverseFormatNumber(taux_remise_generale,'fr');  
            taux_remise_generale = taux_remise_generale.replace(' ','');

            if ((taux_remise_generale >= 0) && (taux_remise_generale <= 100)) {
                // alert(taux_remise_generale);
                if(taux_remise_generale === null){
                    taux_remise_generale = 0;
                }else{
                    taux_remise_generale = (taux_remise_generale/100);
                }

                var remise_generale = montant_total_brut * taux_remise_generale;

                var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

                remise_generale = remise_generale.toFixed(2);
                $('.remise_generale').val(remise_generale); 

                
            }else{
                alert("Attention !!!  Taux remise invalide : Le taux remise ne peut être supérieur à 100%.");
                tr.find('.taux_remise_generale').val(0);
                tr.find('.remise_generale').val(0);
            }

            


        }else{
            tr.find('.taux_remise_generale').val(0);
            tr.find('.remise_generale').val(0);
        }
        
        totalCf();
        
    }

    function myCreateFunction() {
      var table = document.getElementById("myTable");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length;
        
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            cell1.innerHTML = '<td><input list="list_service" autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service"></td>';
            cell2.innerHTML = '<td><input style="text-align:center" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)"  name="qte[]" class="form-control qte"></td>';
            cell3.innerHTML = '<td><input style="text-align:right" autocomplete="off" required type="text" onkeyup="editMontant(this)" oninput ="validateNumber(this);" name="prix_unit[]" class="form-control prix_unit"></td>';
            cell4.innerHTML = '<td><input autocomplete="off" type="text" style="text-align: center" onkeyup="editMontant(this)" oninput ="validateNumber(this);"  name="remise[]" class="form-control remise"></td>';
            cell5.innerHTML = '<td><input autocomplete="off" required onfocus="this.blur()" oninput ="validateNumber(this);"  style="border-color: transparent;background-color: transparent; text-align:right" type="text" name="montant_ht[]" class="form-control montant_ht"><input autocomplete="off" required onfocus="this.blur()" oninput ="validateNumber(this);"  style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis"></td>';
            cell6.innerHTML = '<td><a onclick="removeRow(this)" href="#" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';


            cell1.style.borderCollapse = "collapse";
            cell1.style.padding = "0";
            cell1.style.margin = "0";

            cell2.style.borderCollapse = "collapse";
            cell2.style.padding = "0";
            cell2.style.margin = "0";

            cell3.style.borderCollapse = "collapse";
            cell3.style.padding = "0";
            cell3.style.margin = "0";

            cell4.style.borderCollapse = "collapse";
            cell4.style.padding = "0";
            cell4.style.margin = "0";

            cell5.style.borderCollapse = "collapse";
            cell5.style.padding = "0";
            cell5.style.margin = "0";

            cell6.style.borderCollapse = "collapse";
            cell6.style.padding = "0";
            cell6.style.margin = "0";
            cell6.style.verticalAlign = "middle";
            cell6.style.textAlign = "center";
            
        
      
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<3){

                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Vous ne pouvez pas supprimer cette ligne',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });

            }else{
                $(el).parents("tr").remove(); 
            }  
    }

    editDevise = function(a){

        var saisie = document.getElementById('code_devise').value;
        const opts = document.getElementById('list_devise').childNodes;

        
        for (var i = 0; i < opts.length; i++) {

            if (opts[i].value === saisie) {
        
                if (saisie != '') {
                    const block = saisie.split('->');
                    var code_devise = block[0];
                    var libelle_devise = block[1];
                    var symbole_devise = block[3];
                }else{
                    code_devise = "";
                    libelle_devise = "";
                    symbole_devise = "";
                }

                document.getElementById('code_devise').value = code_devise;
                document.getElementById('libelle_devise').value = libelle_devise;

                break;

            }else{
                document.getElementById('libelle_devise').value = "";
            }

        }
    
    
    
                
        
    }

    function myCreateFunction2() {
      var table = document.getElementById("tablePiece");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            cell1.innerHTML = '<td><input type="file" name="piece[]" class="form-control-file" id="piece" ></td>';
            cell2.innerHTML = '<td><a title="Retirer ce fichier" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
            cell2.style.textAlign = "right";
        
      
    }

    removeRow2 = function(el) {
        var table = document.getElementById("tablePiece");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;

            if(nbre_rows == 1){
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Vous ne pouvez pas supprimer cette ligne',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });

            }else{
                $(el).parents("tr").remove(); 
            }  
    }

</script>

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
