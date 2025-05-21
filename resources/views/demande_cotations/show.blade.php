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
    <br>
    <div class="row"> 
        <div class="col-md-12">

            <div class="card">
                <div class="card-header entete-table">{{ __('DEMANDE DE COTATION') }} 
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y") }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $exercice ?? '' }}</strong></span>
                    
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
                            
                    <div class="row d-flex">
                        <div class="col-md-6">
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label mt-1 font-weight-bold">
                                            N° Demande
                                        </label> 
                                    </div>
                                    
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input disabled name="demande_cotations_id" id="demande_cotations_id" style="display:none" value="{{ $demande_cotation->id ?? '' }}">
                                        <input onfocus="this.blur()" style="margin-top:-3px; color:red; font-weight:bold" autocomplete="off" type="text" id="num_dem" name="num_dem" value="{{ old('num_dem') ?? $demande_cotation->num_dem ?? '' }}" 

                                        class="form-control griser @error('num_dem') is-invalid @enderror num_dem" >
                                    </div>
                                    @error('num_dem')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="mt-1 label">Compte budg.</label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input disabled onfocus="this.blur()" style="display:none" onfocus="this.blur()" autocomplete="off" type="text" name="nbre_ligne" id="nbre_ligne" value="{{ old('nbre_ligne') ?? $nbre_ligne }}" class="form-control" >

                                        <input disabled autocomplete="off" type="text" name="ref_fam" id="ref_fam" class="form-control @error('ref_fam') is-invalid @enderror ref_fam" value="{{ old('ref_fam') ?? $famille_select->ref_fam ?? '' }}"
                                        >
                                    </div>
                                    @error('ref_fam')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input disabled required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" class="form-control griser design_fam" value="{{ old('design_fam') ?? $famille_select->design_fam ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="mt-1 label">Structure </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">

                                        <input disabled required onkeyup="editStructure(this)" list="structure" autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure_select->code_structure ?? '' }}">
                                    </div>
                                    @error('code_structure')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input disabled required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $structure_select->nom_structure ?? '' }}">
                                    </div>
                                    @error('nom_structure')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @if(isset($credit_budgetaires_credit))
                            @if($credit_budgetaires_credit > 0)
                            <div class="row d-flex" style="margin-top: 0px;">
                                <div class="col-md-12 pr-1">
                                    <div class="form-group d-flex ">
                                        <label style="cursor: pointer" class="mt-1 mr-3 label" for="type_operations_libelle_bcs">Commande stockable</label>

                                        <input disabled onclick="handleClick(this);" required autocomplete="off" type="radio" name="type_operations_libelle" id="type_operations_libelle_bcs" class=" @error('type_operations_libelle') is-invalid @enderror type_operations_libelle" value="BCS" 
                                        
                                        @if($demande_cotation->libelle === "Demande d'achats") checked @endif 
                                        
                                        >

                                        <label  style="cursor: pointer" class="mt-1 ml-5 mr-3 label" for="type_operations_libelle_bcn">Commande non stockable</label>

                                        <input disabled onclick="handleClick(this);" required autocomplete="off" type="radio" name="type_operations_libelle" id="type_operations_libelle_bcn" class="@error('type_operations_libelle') is-invalid @enderror type_operations_libelle" value="BCN"
                                        
                                        @if($demande_cotation->libelle === "Commande non stockable") checked @endif
                                        
                                        >
                                    </div>
                                    @error('type_operations_libelle')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            @endif
                        </div>
                        <div class="col-md-6">

                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="mt-1 label">Gestion </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input disabled required onkeyup="editGestion(this)" list="gestion" autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion_select->code_gestion ?? '' }}">
                                    </div>
                                    @error('code_gestion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input disabled required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control griser @error('libelle_gestion') is-invalid @enderror libelle_gestion" value="{{ old('libelle_gestion') ?? $gestion_select->libelle_gestion ?? '' }}">
                                    </div>
                                    @error('libelle_gestion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="mt-1 label">Intitulé @if($griser === null) @endif </label> 
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group d-flex ">
                                        <textarea required 
                                        
                                        @if($griser != null)  style="background-color: #e9ecef; resize:none" onfocus="this.blur()" @endif 

                                        @if($griser === null)  style="resize:none" @endif
                                        
                                        autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') ?? $demande_cotation->intitule ?? '' }}</textarea>
                                    </div>
                                    @error('intitule')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            @if($buttons['partialSelectFournisseurBlade'] === 1)
                                    
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Code échéance</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group">
                                            <input value="{{ old('libelle_periode') ?? $periode->libelle_periode ?? '' }}" autocomplete="off" type="text" id="libelle_periode" name="libelle_periode" class="form-control @error('libelle_periode') is-invalid @enderror libelle_periode" 
                                            
                                            @if($griser === 1) 
                                                style="background-color: #e9ecef;" onfocus="this.blur()" 
                                            @endif

                                            @if($griser === null)
                                                list="list_periode"
                                                onkeyup="editPeriode(this)"
                                            @endif
                                            >

                                            <input value="{{ old('valeur') ?? $periode->valeur ?? '' }}" style="display: none" onfocus="this.blur()" type="text" id="valeur" name="valeur" class="form-control"
                                            >

                                            @error('libelle_periode')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        <div class="form-group">
                                            <input value="{{ old('delai') ?? $demande_cotation->delai ?? '' }}" autocomplete="off" type="text" id="delai" name="delai" class="form-control @error('delai') is-invalid @enderror delai" 
                                            
                                            @if($griser === 1) 
                                                style="background-color: #e9ecef; text-align:center" onfocus="this.blur()" 
                                            @endif

                                            @if($griser === null)
                                                style="text-align:center"
                                                onkeypress="validate(event)" 
                                                onkeyup="editPeriode2(this)"
                                            @endif
                                            >

                                            @error('delai')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="date_echeance" name="date_echeance" class="form-control @error('date_echeance') is-invalid @enderror date_echeance" 
                                            @if(isset($demande_cotation->date_echeance))
                                            value="{{ old('date_echeance') ?? date('d/m/Y H:i:s',strtotime($demande_cotation->date_echeance)) }}"
                                            @else
                                            value="{{ old('date_echeance') ?? '' }}"
                                            @endif
                                            >

                                            @error('date_echeance')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Taux acompte (%)</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <input maxlength="3" onkeypress="validate(event)" class="form-control @error('taux_acompte') is-invalid @enderror" name="taux_acompte" id="taux_acompte" value="{{ old('taux_acompte') ?? $demande_cotation->taux_acompte ?? '' }}" 
                                        
                                        
                                        @if($griser === 1) 
                                            style="background-color: #e9ecef; text-align: center" onfocus="this.blur()"
                                        @endif

                                        @if($griser === null)
                                            list="list_taux_acompte"
                                            style="text-align: center"
                                        @endif
                                        >
                                        @error('taux_acompte')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                    @if(isset($display_solde_avant_operation))
                                        @if($display_solde_avant_operation === 1)
                                        <div class="col-sm-3" style="text-align: right"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible : </label></div>
                                        <div class="col-sm-3"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($credit_budgetaires_credit)) {{ strrev(wordwrap(strrev($credit_budgetaires_credit ?? 'aucun budget'), 3, ' ', true)) ?? '' }} @else Oups! pas de budget disponible @endif</label></div>
                                        @endif
                                    @endif
                                    
                                </div>

                            @endif
                            @if($buttons['partialSelectFournisseurBlade'] === null)
                                @if(isset($display_solde_avant_operation))
                                    @if($display_solde_avant_operation === 1)
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-5" style="text-align: right"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde avant opération : </label></div>
                                        <div class="col-sm-3"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($credit_budgetaires_credit)) {{ strrev(wordwrap(strrev($credit_budgetaires_credit ?? 'aucun budget'), 3, ' ', true)) ?? '' }} @else Oups! pas de budget disponible @endif</label></div>
                                    </div>
                                    @endif
                                @endif
                            @endif
                            
                        </div>
                    </div>
                        
                    @if($credit_budgetaires_credit != null && $credit_budgetaires_credit > 0)
                        <div class="panel panel-footer">
                            <div id="div_detail_bcs" style="@if($display_div_detail_bcs === null) display:none @endif">
                                @include('demande_cotations.partial_edit_bcs')
                            </div>
                            <div id="div_detail_bcn" style="@if($display_div_detail_bcn === null) display:none @endif">
                                @include('demande_cotations.partial_edit_bcn')
                            </div>

                            @if($buttons['partialSelectFournisseurBlade'] === 1)

                                <br/>
                                <span style="color:red; font-weight:bold">
                                    {{ $info_organisation ?? '' }}
                                </span>
                                <br/><br/>

                                @include('demande_cotations.partial_select_fournisseur')
                            @endif
                            
                            <div id="div_save" style="@if($display_div_save === null) display:none @endif">
                                @if(isset($display_pieces_jointes))
                                    @if($display_pieces_jointes === 1)
                                    <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                        @if(count($piece_jointes)>0)
                                            @foreach($piece_jointes as $piece_jointe)
                                                <tr>
                                                    <td style="border: none;vertical-align: middle; text-align:right">
                                                        <a style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 ) }}">
                                                            <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach                                        
                                        @endif
                                        
                                    </table>
                                    @endif
                                @endif
                                
                                @if(isset($statut_demande_cotation->commentaire))
                                    <br/>
                                    <table style="width: 100%">
                                        <tr>
                                            <td colspan="4" style="border-bottom: none">
                                                <span style="font-weight: bold">Dernier commentaire </span> <span style="color: brown; margin-left:3px;"> {{ $nom_prenoms_commentaire ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $profil_commentaire ?? '' }} </span>)

                                                <br>
                                                <br>
                                                <table width="100%" cellspacing="0" border="1" style="background-color:#d4edda; border-color:#155724; font-weight:bold">
                                                    <tr>
                                                        <td>
                                                            <svg style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text-fill" viewBox="0 0 16 16">
                                                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
                                                            </svg> &nbsp; {{ $statut_demande_cotation->commentaire ?? '' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                                <br/>
                                @if(isset($type_statut_demande_cotation))
                                    <span title="Statut du dossier" style="color:red; font-weight:bold">
                                        <svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                        </svg>
                                        &nbsp;
                                        {{ $type_statut_demande_cotation ?? '' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
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