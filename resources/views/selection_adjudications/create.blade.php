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
</head>
<div class="container" style="color:black">

    <?php
        $text = null;

        $borderColor = null; $color = null; $backgroundColor = "#e9ecef";

        if (isset($disponible)) {

            $credit = $disponible;

        }else{
            $credit = 0;
        }

        if(isset($demande_achat_info->net_a_payer)){

            $taux_de_change = 1;

            if (isset($demande_achat_info->taux_de_change)) {
                $taux_de_change = $demande_achat_info->taux_de_change;
            }


            $net_a_payer = $demande_achat_info->net_a_payer * $taux_de_change;


            if ($credit >= $net_a_payer) {

                $color = "#155724";
                $backgroundColor = "#d4edda"; 
                $borderColor = "#d4edda"; 
                $text = "Coût de l'opération : ".strrev(wordwrap(strrev((int)$net_a_payer ?? 'aucun budget'), 3, ' ', true)).". Fonds suffisant";

            }else{

                $color = "#721c24";
                $backgroundColor = "#f8d7da"; 
                $borderColor = "#f8d7da"; 
                $text = "Coût de l'opération : ".strrev(wordwrap(strrev((int)$net_a_payer ?? 'aucun budget'), 3, ' ', true)).". Fonds insuffisant";
            }

            if (isset($disponible)) {
                if ($disponible >= $net_a_payer) {

                    $color = "#155724";
                    $backgroundColor = "#d4edda"; 
                    $borderColor = "#d4edda"; 
                    $text = "Coût de l'opération : ".strrev(wordwrap(strrev((int)$net_a_payer ?? 'aucun budget'), 3, ' ', true)).". Fonds suffisant";
                    
                }else{

                    $color = "#721c24";
                    $backgroundColor = "#f8d7da"; 
                    $borderColor = "#f8d7da"; 
                    $text = "Coût de l'opération : ".strrev(wordwrap(strrev((int)$net_a_payer ?? 'aucun budget'), 3, ' ', true)).". Fonds insuffisant";
                    
                }
            }
        }

                
            
    ?>

    @if(isset($type_profils_name))
        @if($type_profils_name != 'Fournisseur')
            @include('partials.workflow')
            @else
            <br>
        @endif
    @else
    <br>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <?php 
                    $entete = "COTATION FOURNISSEUR";
                    if (isset($type_profils_name)) {
                        if ($type_profils_name === "Responsable des achats") {

                            if (isset($libelle)) {

                                if ($libelle === "Coté" or $libelle === "Fournisseur sélectionné" or $libelle === "Rejeté (Responsable DMP)") {
                                    $entete = "CHOIX DU FOURNISSEUR";
                                }

                            }
                        }
                    }
                   
                ?>
                <div class="card-header entete-table">{{ $entete }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($demande_achat_info->created_at ?? '')) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $demande_achat_info->exercice ?? '' }}</strong></span>
                    
                    
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
                    <form method="POST" action="{{ route('selection_adjudications.store') }}">
                        @csrf
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
                                                            
                                                            $num_bc = str_replace('BC','',str_replace('BCS','',$demande_achat_info->num_bc)) ;

                                                            ?> 
                                                        @endif 
                                                    @endif

                                                @endif  
                                            </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="color:red; font-weight:bold; margin-top:-7px;" autocomplete="off" type="text" name="intitule" class="form-control griser" value="{{ $num_bc ?? '' }}">
                                            <input onfocus="this.blur()" style="display: none" name="cotation_fournisseurs_id" value="{{ $cotation_fournisseurs_id ?? '' }}"/>
                                            
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
                                            <textarea onfocus="this.blur()" style="background-color: #e9ecef; resize:none" autocomplete="off" type="text" name="intitule" class="form-control">{{ $demande_achat_info->intitule ?? '' }}</textarea>
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
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="ref_fam" class="form-control" value="{{ $demande_achat_info->ref_fam ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="intitule" class="form-control griser" @if(isset($demande_achat_info->ref_fam) && isset($demande_achat_info->design_fam))
                                            value="{{ $demande_achat_info->design_fam ?? '' }}"
                                            @endif >
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Structure <?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editStructure(this)" list="structure" <?php } ?>  autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $credit_budgetaire_structure->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $credit_budgetaire_structure->nom_structure ?? '' }}">
                                        </div>
                                        @error('nom_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-6" style="text-align: left"><label class="label" @if(isset($disponible)) @if($disponible > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible avant opération : </label></div>

                                    <div class="col-sm-3"><label class="label" @if(isset($disponible)) @if($disponible > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($disponible)) {{ strrev(wordwrap(strrev($disponible ?? 'aucun budget'), 3, ' ', true)) ?? '' }} @else <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    @if(isset($text))
                                        <div class="col-sm-9">
                                            <label class="label" style="background-color: {{ $backgroundColor ?? '' }} ; color: {{ $color ?? '' }} ; border-color: {{ $borderColor ?? '' }} ; ">
                                                {{ $text ?? '' }}
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                 
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Gestion </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="code_gestion" class="form-control" value="{{ $demande_achat_info->code_gestion ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_gestion" class="form-control griser" value="{{ $demande_achat_info->libelle_gestion ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Échéance </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_periode" class="form-control" value="{{ $commande->libelle_periode ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="delai" class="form-control" value="{{ $commande->delai ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="date_echeance" class="form-control" @if(isset($commande->date_echeance))
                                            value="{{  date("d/m/Y H:i:s", strtotime($commande->date_echeance)) }}"
                                            @endif >
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
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $demande_achat_info->organisations_id ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $demande_achat_info->entnum ?? '' }}" autocomplete="off" type="text" name="entnum" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $demande_achat_info->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Devise</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ old('code_devise') ?? $demande_achat_info->code_devise ?? '' }}" onkeyup="editDevise(this)" list="list_devise" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input value="{{ old('libelle_devise') ?? $demande_achat_info->libelle_devise ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        

                        <div class="panel panel-footer">
                            <table class="table table-bordered table-striped" id="myTable" style="width:100%">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:1px; white-space:nowrap; text-align:center">RÉF.</th>
                                        <th style="width:60%">DÉSIGNATION ARTICLE</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center">QTÉ DEMANDÉE</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center">QTÉ COTATION</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center">PU HT</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center">REMISE (%)</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center">MONTANT HT</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demande_achats as $demande_achat)
                                    <?php

                                        $echantillon_cnps = null;
                                        if (isset($demande_achat->echantillon_cnps)) {
                                            $echantillon_cnps = $demande_achat->echantillon_cnps;
                                        }
                                        $echantillon = null;

                                        $echantillon = $demande_achat->echantillon;
                                        $qte_accordee = null;

                                        $detail_demande_achats = DB::select("SELECT * FROM detail_demande_achats WHERE demande_achats_id = '".$demande_achat->demande_achats_id."' AND ref_articles = '".$demande_achat->ref_articles."' ");
                                        foreach ($detail_demande_achats as $detail_demande_achat) {
                                            $qte_accordee = $detail_demande_achat->qte_accordee;
                                        }


                                        $prix_unit = number_format((float)$demande_achat->prix_unit, 2, '.', '');

                                        $block_prix_unit = explode(".",$prix_unit);
                                        
                                        $prix_unit_partie_entiere = null;
                                        
                                        if (isset($block_prix_unit[0])) {
                                            $prix_unit_partie_entiere = $block_prix_unit[0];
                                        }

                                        $prix_unit_partie_decimale = null;
                                        if (isset($block_prix_unit[1])) {
                                            $prix_unit_partie_decimale = $block_prix_unit[1];
                                        }

                                        // $remise = $demande_achat->remise;

                                        $remise = number_format((float)$demande_achat->remise, 2, '.', '');

                                        $block_remise = explode(".",$remise);
                                        
                                        $remise_partie_entiere = null;
                                        
                                        if (isset($block_remise[0])) {
                                            $remise_partie_entiere = $block_remise[0];
                                        }

                                        $remise_partie_decimale = null;
                                        if (isset($block_remise[1])) {
                                            $remise_partie_decimale = $block_remise[1];
                                        }

                                        // $montant_ht = $demande_achat->montant_ht;

                                        $montant_ht = number_format((float)$demande_achat->montant_ht, 2, '.', '');

                                        $block_montant_ht = explode(".",$montant_ht);
                                        
                                        $montant_ht_partie_entiere = null;
                                        
                                        if (isset($block_montant_ht[0])) {
                                            $montant_ht_partie_entiere = $block_montant_ht[0];
                                        }

                                        $montant_ht_partie_decimale = null;
                                        if (isset($block_montant_ht[1])) {
                                            $montant_ht_partie_decimale = $block_montant_ht[1];
                                        }

                                        $description_articles_libelle = null;

                                            $description_article = DB::table('detail_demande_achats as dda')
                                            ->join('description_articles as da','da.id','=','dda.description_articles_id')
                                            ->select('da.libelle')
                                            ->where('dda.demande_achats_id',$demande_achat->demande_achats_id)
                                            ->where('dda.ref_articles',$demande_achat->ref_articles)
                                            ->first();

                                            if ($description_article != null) {
                                                $description_articles_libelle = $description_article->libelle;
                                            }
                                    ?>
                                    <tr>
                                        <td style="width:1px; white-space:nowrap;text-align: left; vertical-align:middle; font-weight:bold; color: #7d7e8f">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $demande_achat->ref_articles ?? '' }}" autocomplete="off" required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">

                                            {{ $demande_achat->ref_articles ?? '' }}
                                        </td>
                                        <td style="text-align: left; vertical-align:middle">
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
                                        <td style="width:1px; white-space:nowrap;text-align: center; vertical-align:middle">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" value="{{ strrev(wordwrap(strrev($qte_accordee ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" onkeyup="editMontant(this)" name="qte_accordee[]" class="form-control qte_accordee">

                                            {{ strrev(wordwrap(strrev($qte_accordee ?? ''), 3, ' ', true)) }}
                                        </td>
                                        <td style="width:1px; white-space:nowrap;text-align: center; vertical-align:middle">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" value="{{ strrev(wordwrap(strrev($demande_achat->qte ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" onkeyup="editMontant(this)" name="qte[]" class="form-control qte">

                                            {{ strrev(wordwrap(strrev($demande_achat->qte ?? ''), 3, ' ', true)) }}
                                        </td>
                                        <td style="width:1px; white-space:nowrap;text-align: right; vertical-align:middle">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; display:none" value="{{ strrev(wordwrap(strrev($demande_achat->prix_unit ?? ''), 3, ' ', true)) }}" autocomplete="off" required type="text" onkeyup="editMontant(this)" name="prix_unit[]" class="form-control prix_unit">

                                            @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}
                                                @else
                                                {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif

                                        </td>
                                        <td style="width:1px; white-space:nowrap;text-align: center; vertical-align:middle">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" value="{{ $demande_achat->remise }}" autocomplete="off" type="text" onkeyup="editMontant(this)" name="remise[]" class="form-control remise">

                                            @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}
                                                @else
                                                {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif

                                        </td>
                                        <td style="width:1px; white-space:nowrap;text-align: right; vertical-align:middle">
                                            <input value="{{ strrev(wordwrap(strrev($demande_achat->montant_ht ?? ''), 3, ' ', true)) }}" autocomplete="off" required onfocus="this.blur()" style="background-color: #e9ecef;text-align:right; display:none" type="text" name="montant_ht[]" class="form-control montant_ht">

                                            @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}
                                                @else
                                                {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align:middle;">

                                            @if(isset($echantillon_cnps))
                                                <!-- Modal -->

                                                    <a title="Échantillon CNPS : {{ $demande_achat->design_articles ?? '' }}" href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->ref_articles }}">
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

                                            @if(isset($echantillon))

                                                <!-- Modal -->

                                                    <a title="Échantillon Fournisseur : {{ $demande_achat->design_articles ?? '' }}" href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->id }}">
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
                        
                                    $montant_total_brut = number_format((float)$demande_achat->montant_total_brut, 2, '.', '');

                                    $block_montant_total_brut = explode(".",$montant_total_brut);
                                    
                                    $montant_total_brut_partie_entiere = null;
                                    
                                    if (isset($block_montant_total_brut[0])) {
                                        $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
                                    }

                                    $montant_total_brut_partie_decimale = null;
                                    if (isset($block_montant_total_brut[1])) {
                                        $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
                                    }


                                    $taux_remise_generale = number_format((float)$demande_achat->taux_remise_generale, 2, '.', '');

                                    $block_taux_remise_generale = explode(".",$taux_remise_generale);
                                    
                                    
                                    
                                    if (isset($block_taux_remise_generale[0])) {
                                        $taux_remise_generale_partie_entiere = $block_taux_remise_generale[0];
                                    }

                                    
                                    if (isset($block_taux_remise_generale[1])) {
                                        $taux_remise_generale_partie_decimale = $block_taux_remise_generale[1];
                                    }
                                    
                                    $remise_generale = number_format((float)$demande_achat->remise_generale, 2, '.', '');

                                    $block_remise_generale = explode(".",$remise_generale);
                                    
                                    $remise_generale_partie_entiere = null;
                                    
                                    if (isset($block_remise_generale[0])) {
                                        $remise_generale_partie_entiere = $block_remise_generale[0];
                                    }

                                    $remise_generale_partie_decimale = null;
                                    if (isset($block_remise_generale[1])) {
                                        $remise_generale_partie_decimale = $block_remise_generale[1];
                                    }

                                    $montant_total_net = number_format((float)$demande_achat->montant_total_net, 2, '.', '');

                                    $block_montant_total_net = explode(".",$montant_total_net);
                                    
                                    $montant_total_net_partie_entiere = null;
                                    
                                    if (isset($block_montant_total_net[0])) {
                                        $montant_total_net_partie_entiere = $block_montant_total_net[0];
                                    }

                                    $montant_total_net_partie_decimale = null;
                                    if (isset($block_montant_total_net[1])) {
                                        $montant_total_net_partie_decimale = $block_montant_total_net[1];
                                    }
                                    

                                    $montant_total_ttc = number_format((float)$demande_achat->montant_total_ttc, 2, '.', '');

                                    $block_montant_total_ttc = explode(".",$montant_total_ttc);
                                    
                                    $montant_total_ttc_partie_entiere = null;
                                    
                                    if (isset($block_montant_total_ttc[0])) {
                                        $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
                                    }

                                    $montant_total_ttc_partie_decimale = null;
                                    if (isset($block_montant_total_ttc[1])) {
                                        $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
                                    }


                                    $net_a_payer = number_format((float)$demande_achat->net_a_payer, 2, '.', '');

                                    $block_net_a_payer = explode(".",$net_a_payer);
                                    
                                    $net_a_payer_partie_entiere = null;
                                    
                                    if (isset($block_net_a_payer[0])) {
                                        $net_a_payer_partie_entiere = $block_net_a_payer[0];
                                    }

                                    $net_a_payer_partie_decimale = null;
                                    if (isset($block_net_a_payer[1])) {
                                        $net_a_payer_partie_decimale = $block_net_a_payer[1];
                                    }

                                    $montant_acompte = number_format((float)$demande_achat->montant_acompte, 2, '.', '');

                                    $block_montant_acompte = explode(".",$montant_acompte);
                                    
                                    $montant_acompte_partie_entiere = null;
                                    
                                    if (isset($block_montant_acompte[0])) {
                                        $montant_acompte_partie_entiere = $block_montant_acompte[0];
                                    }

                                    $montant_acompte_partie_decimale = null;
                                    if (isset($block_montant_acompte[1])) {
                                        $montant_acompte_partie_decimale = $block_montant_acompte[1];
                                    }

                                ?>

                                <tfoot>
                                    <tr>
                                        <td colspan="7" style="border: none">
                                            <div class="row d-flex pl-3">
                                                <div class="pr-0"><label class="label" class=" mt-1 mr-2">Montant total brut</label><br>  <input
                                                    
                                                    @if($demande_achat!= null) 

                                                        @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                    @endif

                                                    required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;;text-align:right" type="text" name="montant_total_brut" class="form-control montant_total_brut"></div>

                                                    <div class="pl-1" style="text-align:center"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>  <input oninput ="validateNumber(this);" onkeyup="editTauxRemiseGenerale(this)" autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale" style="width:90px; text-align:center; background-color: #e9ecef;" onfocus="this.blur()"
                                                
                                                        @if(isset($taux_remise_generale_partie_decimale) && $taux_remise_generale_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                    >
                                                    </div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Remise</label><br>  <input onfocus="this.blur()"

                                                    @if($demande_achat!= null) 

                                                        @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                    @endif
                                                    
                                                    onkeyup="editRemiseGenerale(this)" 
                                                    autocomplete="off"  style="width:110px; background-color: #e9ecef;text-align:right" type="text" name="remise_generale" class="form-control remise_generale"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Montant total net ht</label><br>  <input 
                                                    
                                                    @if($demande_achat!= null) 

                                                        @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                    @endif
                                                    
                                                    required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;;text-align:right" type="text" name="montant_total_net" class="form-control montant_total_net"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">TVA (%)</label><br>  <input onfocus="this.blur()" value="{{ $demande_achat->tva }}" onkeyup="editRemiseGenerale(this)" autocomplete="off" style=" width:80px; background-color: #e9ecef;text-align:center" type="text" name="tva" class="form-control tva"></div>

                                                <?php 
                                                $montant_tva = 0;
                                                if (isset($demande_achat->tva)) {

                                                    $montant_tva = round(number_format((float)(($demande_achat->montant_total_net * ($demande_achat->tva)/100)), 2, '.', ''));

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


                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input
                                                    
                                                    @if($demande_achat!= null) 

                                                        @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                    @endif

                                                    autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;;text-align:right" type="text" name="montant_tva" class="form-control montant_tva"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Montant total ttc</label><br>  <input
                                                    
                                                    @if($demande_achat!= null) 

                                                        @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                    @endif
                                                    
                                                    required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;;text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"></div>
                                            </div>
                                            <div class="row d-flex pl-3">
                                                <div class="pl-0"><label class="label" class=" mt-1 mr-2">Assiette BNC</label><br>  <input value="{{ $demande_achat->assiete_bnc }}" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="assiette" class="form-control assiette"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Taux BNC</label><br>  <input onfocus="this.blur()" value="{{ $demande_achat->taux_bnc }}" autocomplete="off" style=" width:60px; background-color: #e9ecef" type="text" name="taux_bnc" class="form-control"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input value="{{ ($demande_achat->assiete_bnc * ($demande_achat->taux_bnc)/100) }}" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;" type="text" name="montant_bnc" class="form-control montant_bnc"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Net à payer</label><br>  <input

                                                    @if($demande_achat!= null) 

                                                        @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                    @endif
                                                    
                                                    required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"></div>


                                                <div style="padding-left:3px; text-align:center" ><label class="label" class=" mt-1 mr-2">Acompte</label><br>  <input disabled style="" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" @if($demande_achat!= null) @if($demande_achat->acompte === 1) checked  @endif  @endif ></div>

                                                <div  
                                                
                                                @if($demande_achat!= null)
                                                
                                                    @if($demande_achat->acompte === 1) 
                                                        style="padding-left: 10px; text-align: center" 
                                                    @else
                                                        style="padding-left: 10px; text-align: center; display:none"
                                                    @endif

                                                @else
                                                    style="padding-left: 10px; text-align: center; display:none"
                                                @endif
                                                
                                                id="acompte_taux_div" ><label class="label" class=" mt-1 mr-2">(%)</label><br>  <input onkeyup="editRemiseGenerale(this)" @if(isset($demande_achat->taux_acompte)) value="{{ number_format($demande_achat->taux_acompte ?? '',2)  }}" @endif autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style=" width:70px; text-align:center; background-color: #e9ecef;" type="text" name="taux_acompte" class="form-control taux_acompte"></div>

                                                <div 
                                                
                                                
                                                @if($demande_achat!= null)
                                                
                                                    @if($demande_achat->acompte === 1) 
                                                        style="padding-left: 10px; text-align: center; display:none" 
                                                    @else
                                                        style="padding-left: 10px; text-align: center"
                                                    @endif

                                                @else
                                                    style="padding-left: 10px; text-align: center"
                                                @endif

                                                
                                                id="i_acompte_taux_div" ><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                                <div class="pl-1" 
                                                
                                                @if($demande_achat!= null)
                                                
                                                    @if($demande_achat->acompte === 1) 
                                                    
                                                    @else
                                                    style="display:none"
                                                    @endif

                                                @else
                                                style="display:none"
                                                @endif
                                                
                                                id="acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input 
                                                
                                                @if($demande_achat!= null) 

                                                    @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                        

                                                @endif
                                                
                                                autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style=" width:110px; text-align:right; background-color: #e9ecef;" type="text" name="montant_acompte" class="form-control montant_acompte"></div>

                                                <div class="pl-1"
                                                
                                                @if($demande_achat!= null)
                                                
                                                    @if($demande_achat->acompte === 1) 
                                                        style="display:none"
                                                    @else
                                                    
                                                    @endif

                                                @else
                                                
                                                @endif

                                                id="i_acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control"></div>
                                            
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
                                                @if($entete === "CHOIX DU FOURNISSEUR")
                                                    <textarea style="width: 100%; resize:none" class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                                    @error('commentaire')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                @endif
                                            </div>
                                            

                                            <div class="col-md-3" style="margin-top:0px"> 

                                                @if($entete === "CHOIX DU FOURNISSEUR")
                                                    <button onclick="return confirm('Êtes-vous sûr de choisir ce fournisseur pour cette demande d\'achat ?')" type="submit" class="btn btn-success">
                                                    Choisir ce fournisseur
                                                    </button>
                                                @endif

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
</div>
<script>

        editMontant = function(e){
            var tr=$(e).parents("tr");
            var prix_unit=tr.find('.prix_unit').val();
            var qte=tr.find('.qte').val();
            var remise=tr.find('.remise').val();
            var taxe=tr.find('.taxe').val();

            if(taxe === null){
                taxe = 1;
            }else{
                taxe = 1 + (taxe/100);
            }



            var montant_ht=(prix_unit*qte);
            var remise = (montant_ht * remise)/100;
            montant_ht = montant_ht - remise;
            var montant_ttc=(montant_ht*taxe);

            tr.find('.montant_ht').val(montant_ht);
            tr.find('.montant_ttc').val(montant_ttc);
            totalSelection();
        }

    function totalSelection(){
        var montant_total_brut=0;
        $('.montant_ttc').each(function(i,e){
            var montant_ttc =$(this).val()-0;
            montant_total_brut +=montant_ttc;
        });
        $('.montant_total_brut').val(montant_total_brut); 
        var remise_generale=$('.remise_generale').val();
        var montant_total_net = montant_total_brut - remise_generale;
        var montant_total_ttc = montant_total_net;
        var net_a_payer = montant_total_ttc;
        $('.montant_total_net').val(montant_total_net); 
        $('.montant_total_ttc').val(montant_total_ttc);  
        $('.net_a_payer').val(net_a_payer);   
        //$('.montant_total_brut').html(montant_total_brut);  
    }

        editRemiseGenerale = function(e){
            var tr=$(e).parents("tr");
            var remise_generale=tr.find('.remise_generale').val();
            var montant_total_brut=tr.find('.montant_total_brut').val();
            

            var montant_total_net= montant_total_brut - remise_generale;

            var tva=tr.find('.tva').val();
            if(tva === null){
                tva = 0;
            }else{
                tva = (tva/100);
            }
            var montant_tva = montant_total_net * tva;
            var montant_total_ttc = montant_total_net - montant_tva;
            var net_a_payer = montant_total_ttc;
            tr.find('.montant_total_net').val(montant_total_net);
            tr.find('.montant_tva').val(montant_tva);
            tr.find('.montant_total_ttc').val(montant_total_ttc);
            tr.find('.net_a_payer').val(net_a_payer);
            //totalSelection();
        }

        editDesign = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('.ref_articles').val();
            if(value != ''){
                const block = value.split('->');
                var ref_articles = block[0];
                var design_article = block[1];
                var taxe = block[2];
                
            }else{
                ref_articles = "";
                design_article = "";
                taxe = "";
            }
            
            tr.find('.ref_articles').val(ref_articles);
            tr.find('.design_article').val(design_article);
            tr.find('.taxe').val(taxe);
            //tr.find('.qte_demandee').val(0);
            //tr.find('.montant').val(0);
        }


    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<4){
                alert('Dernière ligne non supprimée');
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
