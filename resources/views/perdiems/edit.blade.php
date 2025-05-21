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
<br>
<div class="container">

    @if(isset($agent_fonctions))
        <datalist id="agent_list">
            @foreach($agent_fonctions as $agent_fonction)
                <?php
                    $agen = DB::table('agents as a')
                        ->join('users as u','u.agents_id','=','a.id')
                        ->join('profils as p','p.users_id','=','u.id')
                        ->join('type_profils as tp','tp.id','=','u.id')
                        ->join('profil_fonctions as pf','pf.agents_id','=','a.id')
                        ->join('fonctions as f','f.id','=','pf.fonctions_id')
                        ->where('u.flag_actif',1)
                        ->where('p.flag_actif',1)
                        ->where('pf.flag_actif',1)
                        ->select('pf.id')
                        ->where('a.mle',$agent_fonction->mle)
                        ->where('f.libelle',$agent_fonction->libelle)
                        ->first();
                    if ($agen!=null) {
                        $profil_fonctions_id = $agen->id;

                        ?>
                            <option value="{{ $profil_fonctions_id ?? '' }}->{{ $agent_fonction->mle ?? '' }}->{{ $agent_fonction->nom_prenoms ?? '' }}->{{ $agent_fonction->libelle ?? '' }}">{{ $agent_fonction->nom_prenoms ?? '' }}</option>
                        <?php
                    }
                ?>
                
            @endforeach
        </datalist>
    @endif
    
    @if(isset($familles))
        <datalist id="famille">
            @foreach($familles as $famille_list)
                <option value="{{ $famille_list->ref_fam}}->{{ $famille_list->design_fam }}">{{ $famille_list->design_fam }}</option>
            @endforeach
        </datalist>
    @endif

    @if(isset($gestions))
        <datalist id="gestion">
            @foreach($gestions as $gestion_list)
                <option value="{{ $gestion_list->code_gestion }}->{{ $gestion_list->libelle_gestion }}">{{ $gestion_list->libelle_gestion }}</option>
            @endforeach
        </datalist>
    @endif

    @if(isset($structures))
        <datalist id="structure">
            @foreach($structures as $structure_list)
                <option value="{{ $structure_list->code_structure}}->{{ $structure_list->nom_structure }}">{{ $structure_list->nom_structure }}</option>
            @endforeach
        </datalist>
    @endif

    <datalist id="agent">
        @foreach($agents as $agent)
            <option>{{ $agent->nom_prenoms ?? '' }}</option>
        @endforeach
    </datalist>

    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card">
                <div class="card-header entete-table">{{ mb_strtoupper($entete ?? '') }}
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y",strtotime($perdiem->created_at ?? null)) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $perdiem->exercice ?? date("Y") }}</strong></span>
                    
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

                    <form enctype="multipart/form-data" method="POST" action="{{ route('perdiems.update') }}">
                        @csrf  
                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Compte à imputer @if($griser === null) <sup style="color: red">*</sup> @endif </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input
                                            
                                            @if($griser === 1) 
                                                onfocus="this.blur()" style="background-color: #e9ecef"
                                                @elseif($griser === null) 
                                                onkeyup="editCompte(this)"  list="famille"
                                            @endif

                                            required  autocomplete="off" type="text" name="ref_fam" id="ref_fam" value="{{ old('ref_fam') ?? $famille_perdiem->ref_fam ?? $perdiem->ref_fam ?? '' }}"  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" value="{{ old('design_fam') ?? $famille_perdiem->design_fam ?? $perdiem->design_fam ?? '' }}"  class="form-control griser design_fam">

                                            @error('design_fam')
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
                                            <label class="mt-1 label">Structure @if($griser === null) <sup style="color: red">*</sup> @endif</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none" value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? $perdiem->credit_budgetaires_id ?? '' }}">

                                            <input 
                                            
                                            @if($griser === 1) 
                                                onfocus="this.blur()" style="background-color: #e9ecef"
                                                @elseif($griser === null) 
                                                onkeyup="editStructure(this)" list="structure"
                                            @endif
                                            
                                            required autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure_perdiem->code_structure ?? $perdiem->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ??  $structure_perdiem->nom_structure ??   $perdiem->nom_structure ?? '' }}">
                                        </div>
                                        @error('nom_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Gestion @if($griser === null) <sup style="color: red">*</sup> @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input 
                                            
                                            @if($griser === 1) 
                                                onfocus="this.blur()" style="background-color: #e9ecef"
                                                @elseif($griser === null) 
                                                onkeyup="editGestion(this)" list="gestion"
                                            @endif
                                            
                                            required autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion_perdiem->code_gestion ?? $perdiem->code_gestion ??  '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control @error('libelle_gestion') is-invalid @enderror libelle_gestion" style="background-color: #e9ecef" value="{{ old('libelle_gestion') ?? $gestion_perdiem->libelle_gestion ?? $perdiem->libelle_gestion ?? '' }}">
                                        </div>
                                        @error('libelle_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                @if(isset($edite_signataire))
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">

                                                <label class="label" class="mt-1 ">Signataire <!--<sup style="color: red">*</sup>
                                                    <br/>
                                                    <a title="Ajouter un signataire" onclick="myCreateFunction3()" class="addRow" style="cursor:pointer">
                                                        <i style="font-size:10px">Ajouter un signataire</i>
                                                    </a>
                                                -->
                                                    
                                                </label> 

                                            </div>
                                        </div>
                                        <div class="col-md-9 pr-1">
                                            <div class="form-group d-flex ">
                                                <table id="tableSignataire" width="100%">
                                                    @if(count($signataires) > 0)
                                                        <?php $o = 1; ?>
                                                        @foreach($signataires as $signataire)

                                                        <tr>

                                                            <td style="width: 32%; padding-right:3px;">
                                                                    <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                    style="display: none"
                                                                    required
                                                                    value="{{ $signataire->profil_fonctions_id ?? '' }}"
                                                                    >
                                                                    <!--list="agent_list"-->
                                                                    <input
                                                                    onkeyup="editAgent(this)"

                                                                    onfocus="this.blur()" 
                                                                    style="background-color: #e9ecef"

                                                                     autocomplete="off" type="text" name="mle[]" class="form-control mle"
                                                                    required
                                                                    value="{{ $signataire->mle ?? '' }}"

                                                                    @if(!isset($edite_signataire))
                                                                        disabled
                                                                    @endif
                                                                    >
    
                                                            </td>
    
                                                            <td>
                                                                
                                                                <input onfocus="this.blur()" 
                                                                style="background-color: #e9ecef"
                                                                autocomplete="off" type="text" name="nom_prenoms_signataires[]" class="form-control griser nom_prenoms_signataires" 
                                                                required
                                                                value="{{ $signataire->nom_prenoms ?? '' }}"
                                                                >
    
                                                            </td>
    
                                                            <td style="width: 1px; white-space:nowrap">
                                                                @if(isset($edite_signataire))
                                                                    @if($o === 1)
                                                                    <!--
                                                                    <a title="Retirer ce signataire" onclick="removeRow3(this)" class="remove"><svg style="color: red; font-weight:bold; font-size:15px; cursor:pointer" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a>
                                                                    -->

                                                                    @else
                                                                    <!--
                                                                    <a title="Retirer ce signataire" onclick="removeRow3(this)" class="remove"><svg style="color: red; font-weight:bold; font-size:15px; cursor:pointer" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a>
                                                                    -->
                                                                    @endif
                                                                @endif
                                                                
                                                        
                                                            </td>
    
                                                        </tr>
                                                        <?php $o++; ?>
                                                        @endforeach
                                                        @else
                                                        <tr>

                                                            <td style="width: 32%; padding-right:3px;">
                                                                    <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                    style="display: none"
                                                                    required
                                                                    >
                    
                                                                    <input
                                                                    onkeyup="editAgent(this)"
                                                                    list="agent_list" autocomplete="off" type="text" name="mle[]" class="form-control mle"
                                                                    required
                                                                    >
    
                                                            </td>
    
                                                            <td>
                                                                
                                                                <input onfocus="this.blur()" 
                                                                style="background-color: #e9ecef"
                                                                autocomplete="off" type="text" name="nom_prenoms_signataires[]" class="form-control griser nom_prenoms_signataires" 
                                                                required
                                                                >
    
                                                            </td>
    
                                                            <td style="width: 1px; white-space:nowrap">
                                                                <a title="Ajouter un signataire" onclick="myCreateFunction3()" class="addRow">
                                                                    <svg style="font-weight: bold; font-size:15px; cursor:pointer;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                    </svg>
                                                                </a>
                                                        
                                                            </td>
    
                                                        </tr>
                                                    @endif                                                    
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px; ">
                                    <div class="col-sm-3" style="display:none">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">N° Oracle @if($griser === null) <sup style="color: red">*</sup> @endif </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1" style="display:none">
                                        <div class="form-group d-flex ">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef;color:red; font-weight:bold; margin-top:-7px; display:none" id="perdiems_id" type="text" class="form-control @error('perdiems_id') is-invalid @enderror" name="perdiems_id" value="{{ old('perdiems_id') ?? $perdiem->id ?? '' }}"  autocomplete="off" >

                                            <input onfocus="this.blur()" style="background-color: #e9ecef;color:red; font-weight:bold; margin-top:-7px" id="num_or" type="text" class="form-control @error('num_or') is-invalid @enderror" name="num_or" value="{{ old('num_or') ?? $perdiem->num_or ?? '' }}"  autocomplete="off" >
                                        
                                        
                                        </div>
                                        @error('num_or')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">N° Perdiem</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input onfocus="this.blur()" style="background-color: transparent;color:red; font-weight:bold; margin-top:-px; border:none" id="num_pdm" type="text" class="form-control @error('num_pdm') is-invalid @enderror" name="num_pdm" value="{{ old('num_pdm') ?? $perdiem->num_pdm ?? '' }}"  autocomplete="off" >
                                        
                                        
                                        </div>
                                        @error('num_pdm')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Intitulé @if($griser === null) <sup style="color: red">*</sup> @endif</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-9 pr-1">
                                        <div class="form-group d-flex ">

                                            <textarea 
                                            
                                            @if($griser === 1) 
                                                onfocus="this.blur()" style="background-color: #e9ecef; resize:none"
                                                @elseif($griser === null) 
                                                style="resize:none"
                                            @endif
                                            
                                            required rows="3" id="perdiems_intitule" type="text" class="form-control @error('perdiems_intitule') is-invalid @enderror" name="perdiems_intitule"   >{{ old('perdiems_intitule') ?? $perdiem->libelle ?? '' }}</textarea>
                                        
                                            @error('perdiems_intitule')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-6" style="text-align: right">

                                        <input style="display:none" onfocus="this.blur()" class="form-control" name="solde_avant_op" id="credit" value="{{ $credit_budgetaires_credit ?? '' }}">
                                        
                                        <label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible avant opération : </label>
                                    
                                    </div>
                                    <div class="col-sm-3"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($credit_budgetaires_credit)) {{ strrev(wordwrap(strrev($credit_budgetaires_credit ?? ''), 3, ' ', true)) ?? '' }} @else <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table @if(isset($griser)) table-striped @endif" id="myTable" style="width:100%; border-collapse: collapse; padding: 0; margin: 0;">

                                    <thead>
                                        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                            <th style="text-align: center; vertical-align:middle; white-space:nowrap; width:1px">N°</th>
                                            <th style="text-align: center; vertical-align:middle">NOM ET PRÉNOM(S)</th>
                                            <th style="text-align: center; vertical-align:middle; width:20%">MONTANTS</th>
                                            <th style="text-align: center; vertical-align:middle; white-space:nowrap; width:1px">PIÈCE D'IDENTITÉ</th>
                                            @if(!isset($griser))
                                            <th style="text-align:center; width:1%; color:white">
                                                
                                                <a title="Ajouter un nouveau bénéficiaire" onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                              </svg></a>
                                              
                                            </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($detail_perdiems as $detail_perdiem)

                                        <?php 
                                            $piece = null;
                                            if (isset($detail_perdiem->piece)) {
                                                $piece = $detail_perdiem->piece;
                                            }
                                        ?>
                                            
                                        
                                        <tr>
                                            <td style="text-align: center; vertical-align:middle; white-space:nowrap; width:1px; padding: 0; margin: 0;">{{ $i }}</td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"><input @if(isset($griser)) onfocus="this.blur()" style="background-color: transparent; border:none" @else list="agent" style="border:none" @endif  required class="form-control form-control-sm" name="nom_prenoms[]" autocomplete="off" value="{{ $detail_perdiem->nom_prenoms ?? '' }}">

                                                <input style="display: none" onfocus="this.blur()" required class="form-control form-control-sm" name="detail_perdiems_id[]" autocomplete="off" value="{{ $detail_perdiem->id ?? '' }}">
                                            </td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"><input @if(isset($griser)) onfocus="this.blur()" style="background-color: transparent; border:none; text-align:right;" @else style="text-align:right; border:none" onkeyup="editMontant(this)" @endif required class="form-control form-control-sm montant" name="montant[]" autocomplete="off" onkeypress="validate(event)" value="{{ strrev(wordwrap(strrev($detail_perdiem->montant ?? ''), 3, ' ', true)) }}">
                                                
                                            <input required class="form-control form-control-sm montant_bis" name="montant_bis[]" onkeypress="validate(event)" style="text-align:right; display:none" value="{{ $detail_perdiem->montant ?? '' }}"></td>

                                            <td @if(isset($griser)) style="text-align:center; vertical-align:middle; margin: 0; padding: 0;" @else style="display:flex; margin: 0; padding: 0;"  @endif>
                                            @if(!isset($griser))
                                                <input style="height: 30px;" type="file" name="piece_identite[]">
                                            @endif
                                            
                                            <input style="display: none" type="checkbox" name="piece_identite_flag[]" checked @if(isset($piece)) value="1" @else value="0" @endif >

                                            @if(isset($piece))

                                                <!-- Modal -->

                                                    <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $detail_perdiem->id }}">
                                                            <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                            </svg>
                                                    </a>
                                                    <div class="modal fade" id="exampleModalCenter{{ $detail_perdiem->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="text-align: center">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Pièce d'identité de { <span style="color: red">{{ $detail_perdiem->nom_prenoms ?? '' }}</span> } </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src='{{ asset('storage/'.$piece) }}' style='width:100%;'>
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
                                            @if(!isset($griser))
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"><a title="Retirer cette ligne" onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                              </svg></a></td>
                                            @endif
                                        </tr>
                                        <?php $i++; ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color:#aabcc6">
                                            <td colspan="2" style="text-align: center; vertical-align:middle; padding: 0; margin: 0; font-weight:bold; color:#033d88">TOTAL</td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"><input @if(isset($griser)) style="background-color: transparent; border:none; text-align:right; color:#155724; font-weight:bold" @else style="text-align:right; border:none; color:#155724; font-weight:bold" @endif  onfocus="this.blur()" required class="form-control form-control-sm montant_total" name="montant_total" id="montant_total" value="{{ strrev(wordwrap(strrev($perdiem->montant_total ?? ''), 3, ' ', true)) }}"></td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"></td>
                                            @if(!isset($griser))
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"></td>
                                            @endif
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
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
                                        @if(!isset($griser))
                                        <input type="file" name="piece[]" class="form-control-file" id="piece" > 
                                        @endif  
                                    </td>
                                    <td style="border: none;vertical-align: middle; text-align:right">
                                        @if(!isset($griser))
                                        <a title="Retirer ce fichier" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg></a>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            
                            
                        </table>

                        <table style="width: 100%">
                            <tr>
                                <td colspan="4" style="border-bottom: none">
                                    <span style="font-weight: bold">Dernier commentaire du dossier </span> : écrit par <span style="color: brown; margin-left:3px;"> {{ $statut_perdiem->nom_prenoms ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $statut_perdiem->name ?? '' }} </span>)

                                    <br>
                                    <br>
                                    <table width="100%" cellspacing="0" border="1" style="background-color:#d4edda; border-color:#155724; font-weight:bold">
                                        <tr>
                                            <td>
                                                <svg style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text-fill" viewBox="0 0 16 16">
                                                    <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
                                                  </svg> &nbsp; {{ $statut_perdiem->commentaire ?? '' }}
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
                                    
                                        <div class="col-md-8">

                                            <textarea title="@if(isset($commentateur)) {{ $commentateur ?? '' }} @endif" placeholder="Écrivez votre commentaire ici" rows="1" id="commentaire" type="text" class="form-control @error('commentaire') is-invalid @enderror" name="commentaire" style="width: 100%; margin-left:15px; margin-right:5px; resize:none;">{{ old('commentaire') ?? '' }}</textarea>

                                            @error('commentaire')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        

                                        <div class="col-md-4" style="margin-top:0px">
                                        
                                            @if(isset($value_bouton2))

                                                <button onclick="return confirm({{ $alert_bouton2 ?? '' }}) " style="width:75px;" type="submit" name="submit" value="{{ $value_bouton2 ?? '' }}" class="btn btn-danger">{{ $bouton2 ?? '' }}</button>

                                            @endif

                                            @if(isset($value_bouton))

                                                <button  onclick="return confirm({{ $alert_bouton ?? '' }}) " style="width:80px;" value="{{ $value_bouton ?? '' }}" type="submit" name="submit" class="btn btn-success">
                                                {{ $bouton ?? '' }}
                                                </button>

                                            @endif

                                            @if(isset($value_bouton3))

                                                <button onclick="return confirm({{ $alert_bouton3 ?? '' }}) " style="width:80px;" type="submit" name="submit" value="{{ $value_bouton3 ?? '' }}" class="btn btn-warning">{{ $bouton3 ?? '' }}</button>

                                            @endif

                                        </div>
                                    </div>

                                </td>

                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    editAgent = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('.mle').val();

        if(value != ''){
            const block = value.split('->');
            var profil_fonctions_id = block[0];
            var mle = block[1];
            var nom_prenoms_signataires = block[2];
            
        }else{
            profil_fonctions_id = "";
            mle = "";
            nom_prenoms_signataires = "";
        }

        if (profil_fonctions_id === undefined ) {
            tr.find('.profil_fonctions_id').val('');
        }else{
            tr.find('.profil_fonctions_id').val(profil_fonctions_id);
        }

        if (nom_prenoms_signataires === undefined ) {
            tr.find('.nom_prenoms_signataires').val('');
        }else{
            tr.find('.nom_prenoms_signataires').val(nom_prenoms_signataires);
        }

        if (mle === undefined ) {
            tr.find('.mle').val(value);
        }else{
            tr.find('.mle').val(mle);
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

    editGestion = function(a){
        const saisie=document.getElementById('code_gestion').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var code_gestion = block[0];
            var libelle_gestion = block[1];
            
        }else{
            code_gestion = "";
            libelle_gestion = "";
        }

        document.getElementById('code_gestion').value = code_gestion;
        if (libelle_gestion === undefined) {
            document.getElementById('libelle_gestion').value = "";
        }else{
            document.getElementById('libelle_gestion').value = libelle_gestion;

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
        var montant=tr.find('.montant').val();
        montant = montant.trim();
        montant = montant.replace(' ', '');
        montant = reverseFormatNumber(montant,'fr');

        montant = montant * 1;

        if (montant <= 0) {
            tr.find('.montant').val("");
            tr.find('.montant_bis').val("");
        }else{
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.montant').val(int3.format(montant));
            tr.find('.montant_bis').val(montant);
        } 
        
        total();
    }

    function total(){
        
        var montant_total=0;
        $('.montant_bis').each(function(i,e){
            var montant_bis =$(this).val()-0;
            montant_total +=montant_bis;
        });

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
        $('.montant_total').val(int3.format(montant_total));    
    
    }
    

    editChargeSuivi = function(a){
        const saisie=document.getElementById('mle_charge').value;
        if(saisie != ''){

            const block = saisie.split('->');
            var mle_charge = block[0];
            var nom_prenoms_charge = block[1];
            var agents_id = block[2];
            
        }else{
            mle_charge = "";
            nom_prenoms_charge = "";
            agents_id = "";
            
        }
        
        document.getElementById('mle_charge').value = mle_charge;
        if (nom_prenoms_charge === undefined) {
            document.getElementById('nom_prenoms_charge').value = "";
            document.getElementById('agents_id').value = "";
        }else{
            document.getElementById('nom_prenoms_charge').value = nom_prenoms_charge;
            document.getElementById('agents_id').value = agents_id;
        }
        
    }

    function myCreateFunction() {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length-1;

        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        cell1.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;">'+nbre_rows+'</td>';
        cell2.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input list="agent" required class="form-control form-control-sm" style="border:none" name="nom_prenoms[]" autocomplete="off"></td>';
        cell3.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input required class="form-control form-control-sm montant" name="montant[]" autocomplete="off" onkeypress="validate(event)" style="text-align:right; border:none" onkeyup="editMontant(this)"><input required class="form-control form-control-sm montant_bis" name="montant_bis[]" onkeypress="validate(event)" style="text-align:right; display:none"></td>';
        cell4.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input style="height: 30px;" type="file" name="piece_identite[]"></td>';
        cell5.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><a title="Retirer cette ligne" onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
    

        cell1.style.borderCollapse = "collapse";
        cell1.style.padding = "0";
        cell1.style.margin = "0";
        cell1.style.verticalAlign = "middle";
        cell1.style.textAlign = "center";

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
        cell5.style.textAlign = "center";
    
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

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;

            if(nbre_rows < 4){
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
                total();
            }  
    }

    removeRow3 = function(el) {
        var table = document.getElementById("tableSignataire");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<2){
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Vous ne pouvez pas supprimer la dernière ligne',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });

            }else{
                $(el).parents("tr").remove(); 
            }  
    }

    function myCreateFunction3() {
        var table = document.getElementById("tableSignataire");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        
        if(nbre_rows < 3){
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.innerHTML = '<td style="width: 32%; padding-right:3px;"> <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id" style="display: none"> <input onkeyup="editAgent(this)" list="agent_list" autocomplete="off" type="text" name="mle[]" class="form-control mle"></td>';
            cell2.innerHTML = '<td> <input onfocus="this.blur()" style="font-weight: normal;color:gray;font-size: 10px;background-color: #e9ecef;border-color:transparent;" autocomplete="off" type="text" name="nom_prenoms_signataires[]" class="form-control nom_prenoms_signataires"></td>';
            cell3.innerHTML = '<td style="width: 1px; white-space:nowrap"><a title="Retirer ce signataire" onclick="removeRow3(this)" class="remove"><svg style="color: red; font-weight:bold; font-size:15px; cursor:pointer" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';

            cell1.style.width = "32%";
            cell1.style.paddingRight = "3px";
        }else{

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Vous avez atteint le nombre limite de signataires',
            focusConfirm: false,
            confirmButtonText:
                'Compris'
            });

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
