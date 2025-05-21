@extends('layouts.admin')

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
    @include('partials.workflow')

    <datalist id="famille">
        @foreach($familles as $famille_list)
            <option value="{{ $famille_list->ref_fam}}->{{ $famille_list->design_fam }}">{{ $famille_list->design_fam }}</option>
        @endforeach
    </datalist>

    <datalist id="credit">
        @foreach($credit_budgetaires as $credit_budgetaire_list)
            <option value="{{ $credit_budgetaire_list->id}}->{{ $credit_budgetaire_list->ref_fam}}->{{ $credit_budgetaire_list->design_fam }}">{{ $credit_budgetaire_list->design_fam }}</option>
        @endforeach
    </datalist>

    @if(isset($structures))
        <datalist id="structure">
            @foreach($structures as $structure_list)
                <option value="{{ $structure_list->code_structure}}->{{ $structure_list->nom_structure }}">{{ $structure_list->nom_structure }}</option>
            @endforeach
        </datalist>
    @endif

    <datalist id="list_article">            
        @foreach($articles as $article_list)
            <option value="{{ $article_list->ref_articles }}->{{ $article_list->design_article }}">{{ $article_list->design_article }}</option>
        @endforeach  
    </datalist>
    <datalist id="gestion">
        @foreach($gestions as $gestion_list)
            <option value="{{ $gestion_list->code_gestion }}->{{ $gestion_list->libelle_gestion }}">{{ $gestion_list->libelle_gestion }}</option>
        @endforeach
    </datalist>
    
    <div class="row"> 
        <div class="col-md-12">         
            <div class="card">
                <div class="card-header entete-table">{{ $entete ?? '' }} 
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date('d/m/Y',strtotime($info->created_at ?? date('d/m/Y')))  }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $demandeAchat->exercice ?? '' }}</strong></span>
                    
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


                    <form enctype="multipart/form-data" method="POST" action="{{ route('demande_achats.update') }}">
                        @csrf
                        
                            
                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label mt-1 font-weight-bold">
                                                @if(isset($statut))

                                                    @if($statut === "bon_commande")
                                                        N° Bon Cde.

                                                        @if($credit_budgetaires_select!=null)
                                                            <?php
                                                            $num_bc = $credit_budgetaires_select->num_bc;
                                                            ?>
                                                        @endif 

                                                    @elseif($statut === "demande_cotation")
                                                        N° Demande
                                                        @if($credit_budgetaires_select!=null)
                                                            <?php
                                                            $num_bc = str_replace('BC','',$credit_budgetaires_select->num_bc);
                                                            ?>
                                                        @endif 
                                                    @endif

                                                @endif
                                            </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="margin-top:-3px; color:red; font-weight:bold" autocomplete="off" type="text" id="num_bc" 
                                            
                                            @if ($credit_budgetaires_select!=null)  
                                                
                                                value="{{ old('num_bc') ?? $num_bc ?? '' }}" 
                                            
                                            @endif 

                                            class="form-control griser @error('num_bc') is-invalid @enderror num_bc" >
                                        </div>
                                        @error('num_bc')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label mt-1 font-weight-bold">Compte budg. </label> 
                                        </div>
                                        
                                    </div> 
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onfocus="this.blur()" style="display:none " autocomplete="off" type="text" name="id" id="id"  value="{{ old('id') ?? $demande_achats_id }}"  class="form-control @error('id') is-invalid @enderror id" >

                                            <input onkeyup="editCompte(this)"  list="famille" required autocomplete="off" type="text" name="ref_fam" id="ref_fam" value="{{ old('ref_fam') ?? $famille->ref_fam ?? $credit_budgetaires_select->ref_fam ?? '' }}"   class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >

                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam"  value="{{ old('design_fam') ?? $famille->design_fam ??$credit_budgetaires_select->design_fam }}"  class="form-control griser design_fam">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label mt-1 font-weight-bold">Structure <?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none" 
                                            @if(isset($disponible_display))
                                                value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? '' }}"
                                            @else
                                                value="{{ old('credit_budgetaires_id') ??  $credit_budgetaire_structure->credit_budgetaires_id ?? '' }}"
                                            @endif
                                            
                                            
                                            >

                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editStructure(this)" list="structure" <?php } ?>  autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure->code_structure ?? $credit_budgetaire_structure->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $structure->nom_structure ?? $credit_budgetaire_structure->nom_structure ?? '' }}">
                                        </div>
                                        @error('nom_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label mt-1 font-weight-bold">Gestion <?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editGestion(this)" list="gestion" <?php } ?>  autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion->code_gestion ?? $info->code_gestion ?? '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_gestion" id="nom_gestion" class="form-control griser @error('nom_gestion') is-invalid @enderror nom_gestion" value="{{ old('nom_gestion') ?? $gestion->libelle_gestion ?? $info->libelle_gestion ?? '' }}">
                                        </div>
                                        @error('nom_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label mt-1 font-weight-bold">Intitulé <span style="color: red"><sup> *</sup></span> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            
                                            <textarea required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') ?? $info->intitule ?? '' }}</textarea>
                                        </div>
                                        @error('intitule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3" style="text-align: right"><label class="label" @if(isset($disponible->credit)) @if($disponible->credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible : </label></div>
                                    <div class="col-sm-3"><label class="label" @if(isset($disponible->credit)) @if($disponible->credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($disponible->credit)) {{ strrev(wordwrap(strrev($disponible->credit ?? 'aucun budget'), 3, ' ', true)) ?? '' }} @else <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>

                            </div>
                        </div>
                                
                        

                        <div class="panel panel-footer">
                            <table class="table table-bordered table-striped" id="myTable" style="width:100%">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;"> 
                                        <th style="width:15%; text-align:center">RÉF. ARTICLE<?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></th>
                                        <th style="width:45%; text-align:center">DÉSIGNATION ARTICLE</span></th>
                                        <th style="width:29%; text-align:center">DESCRIPTION ARTICLE</span></th>
                                        <th style="width:10%; text-align:center">QUANTITÉ<?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></th>
                                        <th style="width:1%; text-align:center">ÉCHANTILLON</th>
                                        <th style="text-align:center; width:1%"><?php if ($griser==null) { ?><a onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                          </svg></a><?php } ?></th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $echantillon_flag_value = 0; ?>
                                    
                                    @foreach($demande_achats as $demande_achat)
                                        <?php 
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
                                        <tr>
                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                <input required <?php if ($griser!=null) { ?> style="background-color: transparent; border:none; text-align:left" onfocus="this.blur()" <?php }else{ ?> onkeyup="editDesign(this)" list="list_article" style="background-color: ; border:; text-align:left"  <?php } ?> value="{{ $demande_achat->ref_articles ?? '' }}" autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">
                                            </td>
                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                
                                                <textarea rows="1" required autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article">{{ $demande_achat->design_article ?? '' }}</textarea>

                                            </td>
                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                
                                                <textarea rows="1" autocomplete="off" style="border-color:transparent; resize:none" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle">{{ $description_articles_libelle ?? '' }}</textarea>
                                                
                                            </td>
                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                
                                                <textarea rows="1" maxlength="12" onkeyup="editQte(this)" onkeypress="validate(event)" <?php if ($griser==null) { ?> style="text-align:center; resize:none" <?php } ?> required <?php if ($griser!=null) { ?> style="background-color: transparent; text-align:center; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text"  name="qte[]" class="form-control qte">{{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? $demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}</textarea>

                                            </td>
                                            <td style="text-align: left; vertical-align:middle;  border-collapse: collapse; padding: 0; margin: 0; @if(isset($echantillon_cnps)) display:flex; @endif">
                                                <input style="height: 30px;" accept="image/*" type="file" name="echantillon[]">

                                                
                                                    <input style=" @if(isset($echantillon_cnps)) display: ; @else display:none; @endif " type="checkbox" name="echantillon_flag[]" checked @if(isset($echantillon_cnps)) value="1" @else value="0" @endif >
                                                

                                                @if(isset($echantillon_cnps))
                                                

                                                    <!-- Modal -->

                                                        <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->ref_articles }}">
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
                                            </td>
                                            <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                                @if(!isset($griser))
                                                    <a onclick="removeRow(this)" href="#" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                    </svg></a>
                                                @endif
                                                
                                            </td>
                                        </tr>
                                        <?php $echantillon_flag_value++; ?>
                                    @endforeach
                                    <tr style="display: none">
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
                                    </tr>
                                    
                                </tbody>
                                
                            </table> 
                            <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                                <tr>
                                    <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">
                                        @if(!isset($griser)) 
                                            <a title="Rattacher une nouvelle pièce" onclick="myCreateFunction2()" href="#">
                                                <svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg>
                                            </a> 
                                        @endif 
                                    </td>
                                </tr>
                                @if(count($piece_jointes)>0)
                                    @foreach($piece_jointes as $piece_jointe)
                                        <tr>
                                            <td>
                                                @if(!isset($griser))
                                                    <input type="file" name="piece[]" class="form-control-file" id="piece" >
                                                @endif
                                                 
                                            </td>
                                            <td style="border: none;vertical-align: middle; text-align:right">
                                                <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>  
                                                <a @if(isset($griser)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 ) }}">
                                                    <svg style="color: blue; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                      </svg>
                                                </a>
                                                <input @if(isset($griser)) disabled @endif style="display: none" onfocus="this.blur()" class="form-control" name="piece_jointes_id[{{ $piece_jointe->id ?? 0 }}]" value="{{ $piece_jointe->id ?? 0 }}">
                                                
                                                <input @if(isset($griser)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                                @if(isset($piece_jointe->flag_actif))
                                                    @if($piece_jointe->flag_actif == 1)
                                                        checked
                                                    @endif
                                                @endif >

                                                @if(!isset($griser)) 

                                                    <a  title="Retirer ce fichier" onclick="removeRow2(this)" href="#">
                                                        <svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                        </svg>
                                                    </a>

                                                @endif
                                                

                                            </td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                @else
                                    <tr>
                                        
                                        <td>
                                            <input type="file" name="piece[]" class="form-control-file" id="piece" >   
                                        </td>
                                        <td style="border: none;vertical-align: middle; text-align:right">
                                            <a title="Retirer ce fichier" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                </svg></a>
                                        </td>
                                    </tr>
                                @endif
                                
                            </table>
                            <table style="width: 100%">
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

                                        <td colspan="4" style="border: none; vertical-align:middle; text-align:right">
                                            <div class="row justify-content-center">
                                            
                                                <div class="col-md-9">

                                                    <textarea @if(isset($griser)) disabled style="width: 100%; resize:none" @else style="width: 100%; resize:none" @endif class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                                    @error('commentaire')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                

                                                <div class="col-md-3" style="margin-top:40px">
                                                
                                                    @if(isset($value_bouton2))

                                                        <button onclick="return confirm_delete()" style="margin-top: -60px; width:75px;" type="submit" name="submit" value="{{ $value_bouton2 ?? '' }}" class="btn btn-danger">{{ $bouton2 ?? '' }}</button>

                                                    @endif

                                                    @if(isset($value_bouton))

                                                        <button  onclick="return confirm_update()" style="margin-top: -60px; width:80px;" value="{{ $value_bouton ?? '' }}" type="submit" name="submit" class="btn btn-success">
                                                        {{ $bouton ?? '' }}
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
    
    function confirm_update() {
        return window.confirm('Êtes-vous sûr d\'enregistrer les modifications ?');
    }

    function confirm_delete() {
        return window.confirm('Êtes-vous sûr d\'annuler cette demande d\'achat ?');
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
    
    editQte = function(e){
        var tr=$(e).parents("tr");
        var qte=tr.find('.qte').val();
        qte = qte.trim();
        qte = reverseFormatNumber(qte,'fr');

        qte = qte * 1;

        if (qte <= 0) {
            
            tr.find('.qte').val("");

        }else{
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte').val(int3.format(qte));
        }

        
        
    }

    editDesign = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('.ref_articles').val();
        if(value != ''){
            const block = value.split('->');
            var ref_articles = block[0];
            var design_article = block[1];
            
        }else{
            ref_articles = "";
            design_article = "";
        }
        
        tr.find('.ref_articles').val(ref_articles);
        tr.find('.design_article').val(design_article);
    }

    editGestion = function(a){
        const saisie=document.getElementById('code_gestion').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var code_gestion = block[0];
            var nom_gestion = block[1];
            
        }else{
            code_gestion = "";
            nom_gestion = "";
        }
        
        document.getElementById('code_gestion').value = code_gestion;
        if (nom_gestion === undefined) {
            document.getElementById('nom_gestion').value = "";
        }else{
            document.getElementById('nom_gestion').value = nom_gestion;

            code_structure = document.getElementById('code_structure').value;
            ref_fam = document.getElementById('ref_fam').value;

                if (ref_fam != '') {
                    if (code_gestion != '') {
                        if (code_structure != '') {

                            
                            var url = window.location.href.split('/limited')[0];

                            document.location.replace(url+'/limited/'+ref_fam+'/'+code_structure+'/'+code_gestion);
                        }
                    }
                }


        }
        
    }

    editStructure = function(a){

        const saisie=document.getElementById('code_structure').value;


        if(saisie != ''){

            const block = saisie.split('->');
            var code_structure = block[0];
            var nom_structure = block[1];
            
        }else{
            code_structure = "";
            nom_structure = "";
        }

        document.getElementById('code_structure').value = code_structure;

        if (nom_structure === undefined) {

            document.getElementById('nom_structure').value = "";

        }else{

            document.getElementById('nom_structure').value = nom_structure;

            ref_fam = document.getElementById('ref_fam').value;
            code_gestion = document.getElementById('code_gestion').value;
            

            if (ref_fam != '') {
                if (code_gestion != '') {
                    if (code_structure != '') {

                        var url = window.location.href.split('/limited')[0];

                        document.location.replace(url+'/limited/'+ref_fam+'/'+code_structure+'/'+code_gestion);

                    }
                }
            }

            

        }

    }
    
    editCompte = function(a){
        const saisie = document.getElementById('ref_fam').value;
        const opts = document.getElementById('famille').childNodes;

        
        for (var i = 0; i < opts.length; i++) {
        if (opts[i].value === saisie) {
            
            if(saisie != ''){
            const block = saisie.split('->');
            
            var ref_fam = block[0];
            var design_fam = block[1];
            
            }else{
                design_fam = "";
                ref_fam = "";
            }

            document.getElementById('ref_fam').value = ref_fam;
            if (design_fam === undefined) {
                document.getElementById('design_fam').value = "";
            }else{
                
                document.getElementById('design_fam').value = design_fam;

                code_structure = document.getElementById('code_structure').value;
                code_gestion = document.getElementById('code_gestion').value;

            

                if (ref_fam != '') {
                    if (code_gestion != '') {
                        if (code_structure != '') {

                            var url = window.location.href.split('/limited')[0];
                            
                            document.location.replace(url+'/limited/'+ref_fam+'/'+code_structure+'/'+code_gestion);
                            
                        }
                    }
                }

            }
            
            break;
        }else{
            document.getElementById('design_fam').value = "";
        }
        }
    }
    
    function myCreateFunction() {
      var table = document.getElementById("myTable");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;
        if(nbre_rows<11){
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            cell1.innerHTML = '<td><input required <?php if ($griser!=null) { ?> style="background-color: transparent; border: none; text-align:left" onfocus="this.blur()" <?php }else{ ?> onkeyup="editDesign(this)" list="list_article" style="background-color: ; border: ; text-align:left" <?php } ?> autocomplete="off" type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles"></td>';
            cell2.innerHTML = '<td><textarea rows="1" required autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article"></textarea></td>';
            cell3.innerHTML = '<td><textarea rows="1" autocomplete="off" style="border-color:transparent; resize:none" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle"></textarea></td>';
            cell4.innerHTML = '<td><textarea rows="1" maxlength="12" onkeyup="editQte(this)" onkeypress="validate(event)" <?php if ($griser==null) { ?> style="text-align:center ; resize:none" <?php } ?> required <?php if ($griser!=null) { ?> style="background-color: transparent; text-align:center; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text" name="qte[]" class="form-control qte"></textarea></td>';
            cell5.innerHTML = '<td><input style="height: 30px;" accept="image/*" type="file" name="echantillon[]"></td>';
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
            cell5.style.verticalAlign = "middle";
            cell5.style.textAlign = "left";

            cell6.style.borderCollapse = "collapse";
            cell6.style.padding = "0";
            cell6.style.margin = "0";
            cell6.style.verticalAlign = "middle";
            cell6.style.textAlign = "center";
            
        }
      
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<4){
                // alert('Dernière ligne non supprimée');
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'info',
                html:
                    'Vous ne pouvez pas supprimer la dernière ligne',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });
            }else{
                $(el).parents("tr").remove(); 
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
    // style="border: none;vertical-align: middle; text-align:right"
    removeRow2 = function(el) {
        var table = document.getElementById("tablePiece");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;

            if(nbre_rows == 1){
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Vous ne pouvez pas supprimer la dernière ligne de dépot d\article',
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
