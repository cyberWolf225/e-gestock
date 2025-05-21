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
                <div class="card-header entete-table">{{ __('APERCU DE LA COTATION') }} 
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y",strtotime($demande_cotation->created_at ?? date("Y-m-d"))) }} </strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $demande_cotation->exercice ?? $exercice ?? '' }}</strong></span>
                    
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
                    
                    <form enctype="multipart/form-data" method="POST" action="{{ route('reponse_cotations.update') }}">
                        @csrf
                            
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

                                            <input onfocus="this.blur()" name="demande_cotations_id" id="demande_cotations_id" style="display:none" value="{{ $demande_cotation->id ?? '' }}">

                                            <input onfocus="this.blur()" name="reponse_cotations_id" id="reponse_cotations_id" style="display:none" value="{{ $reponse_cotation->id ?? '' }}">

                                            <input required autocomplete="off" type="text" name="type_operations_libelle" id="type_operations_libelle" class=" @error('type_operations_libelle') is-invalid @enderror type_operations_libelle"

                                            @if($demande_cotation->libelle === "Demande d'achats") value="BCS" @endif 

                                            @if($demande_cotation->libelle === "Commande non stockable") value="BCN" @endif

                                            onfocus="this.blur()" style="display:none">

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
                                @if($vue_fournisseur === 1)
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">
                                                <label class="mt-1 label">Intitulé @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif </label> 
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group d-flex ">
                                                <textarea required 
                                                
                                                @if($griser_demande != null)  style="background-color: #e9ecef; resize:none" onfocus="this.blur()" @endif 

                                                @if($griser_demande === null)  style="resize:none" @endif
                                                
                                                autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') ?? $demande_cotation->intitule ?? '' }}</textarea>
                                            </div>
                                            @error('intitule')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                                @if($vue_fournisseur === null)
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">
                                                <label class="mt-1 label">Compte budg. @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif</label> 
                                            </div>
                                        </div>
                                        <div class="col-md-3 pr-1">
                                            <div class="form-group d-flex ">
                                                <input onfocus="this.blur()" style="display:none" onfocus="this.blur()" autocomplete="off" type="text" name="nbre_ligne" id="nbre_ligne" value="{{ old('nbre_ligne') ?? $nbre_ligne }}" class="form-control" >

                                                <input required autocomplete="off" type="text" name="ref_fam" id="ref_fam" class="form-control @error('ref_fam') is-invalid @enderror ref_fam" value="{{ old('ref_fam') ?? $famille_select->ref_fam ?? '' }}"
                                                
                                                @if($griser_demande != null)  style="background-color: #e9ecef;" onfocus="this.blur()" @endif 

                                                @if($griser_demande === null)  onkeyup="editCompte(this)" list="famille" @endif

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
                                                <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" class="form-control griser design_fam" value="{{ old('design_fam') ?? $famille_select->design_fam ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">
                                                <label class="mt-1 label">Structure @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif</label> 
                                            </div>
                                        </div>
                                        <div class="col-md-3 pr-1">
                                            <div class="form-group d-flex ">

                                                <input required autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure_select->code_structure ?? '' }}"
                                                
                                                @if($griser_demande != null)  style="background-color: #e9ecef;" onfocus="this.blur()" @endif 

                                                @if($griser_demande === null)  onkeyup="editStructure(this)" list="structure" @endif

                                                >
                                            </div>
                                            @error('code_structure')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 pl-0">
                                            <div class="form-group d-flex ">
                                                <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $structure_select->nom_structure ?? '' }}">
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

                                                <input required autocomplete="off" type="radio" name="type_operations_libelle_bcs" id="type_operations_libelle_bcs" class=" @error('type_operations_libelle_bcs') is-invalid @enderror type_operations_libelle_bcs" value="BCS" 
                                                
                                                @if($demande_cotation->libelle === "Demande d'achats") checked @endif 
                                                
                                                @if($griser_demande != null) disabled @endif 

                                                @if($griser_demande === null)  onclick="handleClick(this);"
                                                @endif
                                                >

                                                <label  style="cursor: pointer" class="mt-1 ml-5 mr-3 label" for="type_operations_libelle_bcn">Commande non stockable</label>

                                                <input required autocomplete="off" type="radio" name="type_operations_libelle_bcn" id="type_operations_libelle_bcn" class="@error('type_operations_libelle_bcn') is-invalid @enderror type_operations_libelle_bcn" value="BCN"
                                                
                                                @if($demande_cotation->libelle === "Commande non stockable") checked @endif
                                                
                                                @if($griser_demande != null) disabled @endif 

                                                @if($griser_demande === null)  onclick="handleClick(this);"
                                                @endif

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
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($vue_fournisseur === null)
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">
                                                <label class="mt-1 label">Gestion @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif</label> 
                                            </div>
                                        </div>
                                        <div class="col-md-3 pr-1">
                                            <div class="form-group d-flex ">
                                                <input required autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion_select->code_gestion ?? '' }}"
                                                
                                                @if($griser_demande != null)  style="background-color: #e9ecef;" onfocus="this.blur()" @endif 

                                                @if($griser_demande === null)  onkeyup="editGestion(this)" list="gestion" @endif

                                                >
                                            </div>
                                            @error('code_gestion')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 pl-0">
                                            <div class="form-group d-flex ">
                                                <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control griser @error('libelle_gestion') is-invalid @enderror libelle_gestion" value="{{ old('libelle_gestion') ?? $gestion_select->libelle_gestion ?? '' }}">
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
                                                <label class="mt-1 label">Intitulé @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif </label> 
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group d-flex ">
                                                <textarea required 
                                                
                                                @if($griser_demande != null)  style="background-color: #e9ecef; resize:none" onfocus="this.blur()" @endif 

                                                @if($griser_demande === null)  style="resize:none" @endif
                                                
                                                autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') ?? $demande_cotation->intitule ?? '' }}</textarea>
                                            </div>
                                            @error('intitule')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Fournisseur </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $organisation->id ?? $fournisseur_demande_cotation->organisations_id ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $organisation->entnum ?? $fournisseur_demande_cotation->entnum ?? '' }}" autocomplete="off" type="text" name="entnum" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $organisation->denomination ?? $fournisseur_demande_cotation->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                    
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Code échéance @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group">
                                            <input value="{{ old('libelle_periode') ?? $periode->libelle_periode ?? '' }}" autocomplete="off" type="text" id="libelle_periode" name="libelle_periode" class="form-control @error('libelle_periode') is-invalid @enderror libelle_periode" 
                                            
                                            @if($griser_demande === 1) 
                                                style="background-color: #e9ecef;" onfocus="this.blur()" 
                                            @endif

                                            @if($griser_demande === null)
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
                                            
                                            @if($griser_demande === 1) 
                                                style="background-color: #e9ecef; text-align:center" onfocus="this.blur()" 
                                            @endif

                                            @if($griser_demande === null)
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
                                            <label class="label" class="mt-1">Devise @if($griser_offre === null)<span style="color: red"><sup> *</sup></span> @endif</label> 
                                        </div>
                                    </div>
                                    <!-- onfocus="this.blur()" style="background-color: #e9ecef" -->
                                    
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input value="{{ old('code_devise') ?? $reponse_cotation->code ?? $devise_default->code ?? '' }}" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control" 

                                            @if($griser_offre != null)  style="background-color: #e9ecef;" onfocus="this.blur()" @endif 

                                            @if($griser_offre === null)  onkeyup="editDevise(this)" list="list_devise" @endif

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input value="{{ old('libelle_devise') ?? $reponse_cotation->libelle ?? $devise_default->libelle ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                        @if($credit_budgetaires_credit != null && $credit_budgetaires_credit > 0)
                            <div class="panel panel-footer">
                                @if(isset($demande_cotation->libelle))
                                    @if($demande_cotation->libelle === "Demande d'achats")
                                        
                                    <div id="div_detail_bcs" style="@if($display_div_detail_bcs === null) display:none @endif">
                                        @include('reponse_cotations.partial_show_bcs')
                                    </div>
                                        
                                    @endif
                                @endif
                                

                                @if(isset($demande_cotation->libelle))
                                    @if($demande_cotation->libelle === "Commande non stockable")
                                        
                                    <div id="div_detail_bcn" style="@if($display_div_detail_bcn === null) display:none @endif">
                                        @include('reponse_cotations.partial_show_bcn')
                                    </div>
                                        
                                    @endif
                                @endif
                                

                                @include('reponse_cotations.partial_show_footer')
                                
                                @if($buttons['partialSelectFournisseurBlade'] === 1)

                                    <br/>
                                    <span style="color:red; font-weight:bold">
                                        PRÉSÉLECTIONNEZ LES FOURNISSEURS DEVANT SOUMETTRE UNE COTATION
                                    </span>
                                    @if($griser_demande === null)
                                        <br/>
                                        <span>
                                            <a style="cursor: pointer; color:cadetblue; font-style:italic; font-weight:bold" onclick="myCreateFunctionOrg()" class="addRow">
                                                (Ajouter un nouveau fournisseur)
                                            </a>
                                        </span>
                                    @endif
                                    <br/><br/>

                                    @include('demande_cotations.partial_select_fournisseur')
                                @endif
                                
                                <div id="div_save" style="@if($display_div_save === null) display:none @endif">
                                    @if(isset($display_pieces_jointes))
                                    @if($display_pieces_jointes === 1)
                                        <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                    
                                            <tr>
                                                <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">
                                                    @if(!isset($griser_demande)) 
                                                        <a title="Rattacher une nouvelle pièce" onclick="myCreateFunction2()" >
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
                                                            @if(!isset($griser_demande))
                                                                <input type="file" name="piece[]" class="form-control-file" id="piece" >
                                                            @endif
                                                            
                                                        </td>
                                                        <td style="border: none;vertical-align: middle; text-align:right">
                                                            <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>  
                                                            <a @if(isset($griser_demande)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 ) }}">
                                                                <svg style="color: blue; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                </svg>
                                                            </a>
                                                            <input @if(isset($griser_demande)) disabled @endif style="display:none" onfocus="this.blur()" class="form-control" name="piece_jointes_id[{{ $piece_jointe->id ?? 0 }}]" value="{{ $piece_jointe->id ?? 0 }}">
                                                            
                                                            <input @if(isset($griser_demande)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                                            @if(isset($piece_jointe->flag_actif))
                                                                @if($piece_jointe->flag_actif == 1)
                                                                    checked
                                                                @endif
                                                            @endif >
            
                                                            @if(!isset($griser_demande)) 
            
                                                                <a  title="Retirer ce fichier" onclick="removeRow2(this)" >
                                                                    <svg style="color: red; cursor:pointer; display:none" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
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
                                                        <a title="Retirer ce fichier" onclick="removeRow2(this)" ><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg></a>
                                                    </td>
                                                </tr>
                                            @endif
                                            
                                        </table>
                                        <br/>
                                    @endif
                                    @endif
                                </div>                                
                            </div>
                        @endif
                    </form>
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