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
    <div class="row"> 
        <div class="col-md-12">
            
            <div class="card">
                <div class="card-header entete-table">{{ mb_strtoupper('Validation de la demande d\'achat') }} 
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y",strtotime($demande_achat_info->created_at ?? '')) }}</strong></span>
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
                    <form method="POST" action="{{ route('valider_demande_achats.store') }}"> 
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
                                            <input onfocus="this.blur()" style="color:red; font-weight:bold" autocomplete="off" type="text" id="num_bc" 
                                            @if ($demande_achat_info!=null)
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
                                            <input value="{{ $demande_achat_info->ref_fam ?? '' }}" required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php } ?> onkeyup="editCompte(this)" list="credit" autocomplete="off" type="text" name="ref_fam" id="ref_fam" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ old('ref_fam') ?? $credit_budgetaires_select->ref_fam }}" <?php } ?>  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >
                                            <input onfocus="this.blur()" style="display: none" name="id" value="{{ $demande_achat_info->id ?? '' }}"/>
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" value="{{ $demande_achat_info->design_fam ?? '' }}"  class="form-control griser design_fam">
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
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label mt-1 font-weight-bold">Gestion </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input  required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editGestion(this)" list="gestion" <?php } ?>  autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ $demande_achat_info->code_gestion ?? '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_gestion" id="nom_gestion" class="form-control griser @error('nom_gestion') is-invalid @enderror nom_gestion" value="{{ $demande_achat_info->libelle_gestion ?? '' }}">
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
                                            <label class="label mt-1 font-weight-bold">Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php } ?> autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ $demande_achat_info->intitule ?? '' }}</textarea>
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
                            <table class="table table-bordered table-striped" id="myTable" style="width:100%; border-collapse: collapse;">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:1%; white-space:nowrap; vertical-align:middle; text-align:center">RÉF.</th>
                                        <th style="width:59%; vertical-align:middle; text-align:center">DÉSIGNATION ARTICLE</span></th>
                                        <th style="width:10%; vertical-align:middle; text-align:center">QTÉ</th>
                                        <th style="width:10%; vertical-align:middle; text-align:center">QTÉ ACCORDÉE<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:1%; text-align:center">ÉCHANTILLON</th>
                                        <th style="text-align:center; width:1%; vertical-align:middle" title="Veuillez cocher les lignes de demandes d'articles que vous souhaitez valider"><svg title="Veuillez cocher les lignes de demandes que vous souhaitez valider" style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
                                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                                            <path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
                                          </svg></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demande_achats as $demande_achat)
                                        
                                    <?php 
                                        $check = null;
                                        if ($demande_achat->flag_valide == 1) {
                                            $check = 'checked';
                                        }
                                        
                                        if (isset($check_default)) {
                                            if ($check_default === 0) {
                                                $check = 'checked';
                                            }
                                        }

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
                                    <tr style="border-collapse: collapse;">
                                        <td style="text-align:left; vertical-align:middle; font-weight:bold; color: #7d7e8f">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; text-align:center; display:none" onfocus="this.blur()" <?php }else{ ?> onkeyup="editDesign(this)" list="list_article" <?php } ?> autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" value="{{ $demande_achat->ref_articles ?? '' }}">

                                            <input style="display: none;" onfocus="this.blur()" name="detail_demande_achat_id[]" value="{{ $demande_achat->id ?? '' }}"/>

                                            {{ $demande_achat->ref_articles ?? '' }}
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <input required autocomplete="off" required onfocus="this.blur()" style="background-color: #cae4ff; display:none" type="text" id="design_article" name="design_article[]" class="form-control design_article" value="{{ $demande_achat->design_article ?? '' }}">

                                            {{ $demande_achat->design_article ?? '' }}

                                            @if(isset($description_articles_libelle))
                                                <br/>
                                                <br/>
                                                <span style="color:red; font-weight:bold" >{{ 'Description de l\'article' }}</span>
                                                <br/>
                                                {{ $description_articles_libelle ?? '' }}
                                                
                                            @endif
                                        </td>
                                        <td style="vertical-align: middle; text-align: center; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; vertical-align: middle; text-align: center; display:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text"  name="qte[]" class="form-control qte" value="{{ strrev(wordwrap(strrev($demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}">

                                            {{ strrev(wordwrap(strrev($demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}
                                        </td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0; vertical-align: middle; background-color:white">

                                            <input maxlength="12" onkeyup="editQte(this)" onkeypress="validate(event)" style="vertical-align: middle; text-align: center; border-color:transparent; border:none" required autocomplete="off" required type="text"  name="qte_validee[]" class="form-control qte_validee" value="{{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? $demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}">

                                        </td>
                                        <td style="text-align: center; vertical-align:middle;">
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
                                            <input {{ $check ?? '' }} type="checkbox" id="approvalcd_{{ $demande_achat->id }}" name="approvalcd[{{ $demande_achat->id }}]" class="approvalcd_{{ $demande_achat->id }}" />
                                        </td>
                                    </tr>

                                    @endforeach
                                </tbody>
                                
                            </table>
                            <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                                <tr>
                                    <td style="border: none;vertical-align: middle; text-align:right"  colspan="2">
                                        @if(!isset($griser)) 
                                            <a title="Rattacher un nouveau dépôt" onclick="myCreateFunction2()" href="#">
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
                                                    <input type="file" name="piece[]" class="form-control-file" id="piece" accept="application/pdf">
                                                @endif
                                                 
                                            </td>
                                            <td style="border: none;vertical-align: middle; text-align:right">
                                                <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>  
                                                <a @if(isset($griser)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ $piece_jointe->id ?? 0 }}">
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

                                    <td colspan="5" style="border: none;vertical-align: middle; text-align:right">

                                        <div class="row justify-content-center">
                                        
                                            <div class="col-md-9">
                                                <textarea class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2" style="width: 100%; resize:none">{{ old('commentaire') ?? '' }}</textarea> 

                                                @error('commentaire')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3" style="margin-top:40px">
                                                <button style="margin-top: -60px; width:75px;" type="submit" name="submit" value="annuler_r_achat" class="btn btn-danger">Annuler</button>

                                                <button style="margin-top: -60px; width:75px;" value="valider_r_achat" type="submit" name="submit" class="btn btn-success">Valider</button>
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
            var qte_validee=tr.find('.qte_validee').val();
            qte_validee = qte_validee.trim();
            qte_validee = qte_validee.replace(' ','');
            qte_validee = reverseFormatNumber(qte_validee,'fr')
            qte_validee = qte_validee.replace(' ','');
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte_validee').val(int3.format(qte_validee));
            
        }


    function myCreateFunction2() {
      var table = document.getElementById("tablePiece");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            cell1.innerHTML = '<td><input type="file" name="piece[]" class="form-control-file" id="piece" accept="application/pdf"></td>';
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


