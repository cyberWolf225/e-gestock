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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>


</head>
<br>
<div class="container">

    <datalist id="credit">
        @foreach($credit_budgetaires as $credit_budgetaire)
            <option value="{{ $credit_budgetaire->ref_fam}}->{{ $credit_budgetaire->design_fam }}->{{ $credit_budgetaire->id}}->{{ Crypt::encryptString($credit_budgetaire->id)}}">{{ $credit_budgetaire->design_fam }}</option>
        @endforeach
    </datalist>

    <datalist id="famille">
        @foreach($familles as $famille)
            <option value="{{ $famille->ref_fam}}->{{ $famille->design_fam }}->{{ $famille->id }}->{{ Crypt::encryptString($famille->id)}}">{{ $famille->design_fam }}</option>
        @endforeach
    </datalist>

    @if(isset($structures))
        <datalist id="structure">
            @foreach($structures as $structure)
                <option value="{{ $structure->code_structure}}->{{ $structure->nom_structure }}->{{ $structure->credit_budgetaires_id }}">{{ $structure->nom_structure }}</option>
            @endforeach
        </datalist>
    @endif

    <datalist id="list_periode">            
        @foreach($periodes as $periode)
            <option value="{{ $periode->id }}->{{ $periode->libelle_periode }}->{{ $periode->valeur }}">{{ $periode->libelle_periode }}</option>
        @endforeach  
    </datalist>

    <datalist id="list_taxe_tva">            
        @foreach($taxes as $taxe)
            <option value="{{ $taxe->taux }}">{{ $taxe->nom_taxe }}->{{ $taxe->taux }}</option>
        @endforeach  
    </datalist>

    <datalist id="list_organisation">
        @foreach($organisations as $organisation)
            <option value="{{ $organisation->id }}->{{ $organisation->denomination }}">{{ $organisation->denomination }}</option>
        @endforeach
    </datalist>

    <datalist id="list_service">
        @foreach($services as $service)
            <option>{{ $service->libelle }}</option>
        @endforeach
    </datalist>

    <datalist id="list_devise">            
        @foreach($devises as $devise)
            <option value="{{ $devise->code }}->{{ $devise->libelle }}->{{ $devise->symbole }}">{{ $devise->libelle }}</option>
        @endforeach  
    </datalist>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('BON DE COMMANDE NON STOCKABLE') }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime( $travauxe->created_at ?? date("Y-m-d"))) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $travauxe->exercice ?? '' }}</strong></span>
                    
                </div>

                <form method="POST">
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
                                            <label class="label" class="mt-1 "> 
                                                N° Bon Cde.
                                            </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="margin-top : -5px; color:red; font-weight:bold" autocomplete="off" type="text" name="num_bc" class="form-control griser" value="{{ $travauxe->num_bc ?? '' }}">
                                            
                                            
                                        </div>
                                    </div>
                                    @if(isset($href_print))
                                        <div class="col-md-3">
                                            
                                                <div class="row d-flex" style="margin-top: 0px;">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="text-align: right">
                                                            <a target="_blank" style="display:{{ $display_print }};" title="{{ $title_print }}" href="{{ $href_print }}" class="btn btn-secondary btn-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                                                                    <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                                                    <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                                                    </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                        </div>
                                    @endif
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" >Compte budg. </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input type="text" value="{{ $travauxe->travauxes_id ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; display:none" required name="travauxes_id">
                                            <input style="display: none" onfocus="this.blur()" autocomplete="off" type="text" name="nbre_ligne" id="nbre_ligne" value="{{ old('nbre_ligne') ?? $nbre_ligne }}" class="form-control" >

                                            <input style="display: none; background-color: #e9ecef;" required autocomplete="off" type="text" name="id" id="id" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ old('id') ?? $credit_budgetaires_select->id }}" <?php } ?>  class="form-control @error('id') is-invalid @enderror id" 
                                            
                                            onfocus="this.blur()" >

                                            {{-- list="famille" --}}
                                            <input required onkeyup="editCompte(this)"   autocomplete="off" type="text" name="ref_fam" id="ref_fam" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ old('ref_fam') ?? $credit_budgetaires_select->ref_fam }}" <?php } ?>  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" onfocus="this.blur()" style="background-color: #e9ecef" >
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ $credit_budgetaires_select->design_fam }}" <?php } ?> class="form-control design_fam">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" >Structure <?php if ($griser==null) { ?> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none" value="{{ old('credit_budgetaires_id') ?? $travauxe->credit_budgetaires_id ?? '' }}">

                                            <input required style="background-color: #e9ecef" onfocus="this.blur()" autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $travauxe->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control @error('nom_structure') is-invalid @enderror nom_structure" style="background-color: #e9ecef" value="{{ old('nom_structure') ?? $travauxe->nom_structure ?? '' }}">
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
                                            <label class="label" >Gestion <?php if ($griser==null) { ?> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required style="background-color: #e9ecef" onfocus="this.blur()" autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $travauxe->code_gestion ??  $gestion_default->code_gestion ?? '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="nom_gestion" id="nom_gestion" class="form-control @error('nom_gestion') is-invalid @enderror nom_gestion" style="background-color: #e9ecef" value="{{ old('nom_gestion') ?? $travauxe->nom_gestion ?? $gestion_default->libelle_gestion ?? '' }}">
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
                                            <label class="label" >Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea required  style="background-color: #e9ecef; resize:none" onfocus="this.blur()"  autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ $travauxe->intitule ?? '' }}</textarea>
                                        </div>
                                        @error('intitule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                @if(isset($signataires))
                                @if(count($signataires) > 0)
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">

                                            <label class="label" class="mt-1 ">Signataire</label> 

                                        </div>
                                    </div>
                                    <div class="col-md-9 pr-1">
                                        <div class="form-group d-flex ">
                                            <table id="tableSignataire" width="100%">
                                                @if(count($signataires) > 0)
                                                    @foreach($signataires as $signataire)

                                                    <tr>

                                                        <td style="width: 32%; padding-right:3px;">
                                                                <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                style="display: none"
                                                                required
                                                                value="{{ $signataire->profil_fonctions_id ?? '' }}"
                                                                >
                
                                                                <input
                                                                onkeyup="editAgent(this)"
                                                               autocomplete="off" type="text" name="mle[]" class="form-control mle"
                                                                required
                                                                value="{{ $signataire->mle ?? '' }}"

                                                                @if(!isset($edit_signataire))
                                                                    disabled
                                                                @endif
                                                                >

                                                        </td>

                                                        <td>
                                                            
                                                            <input onfocus="this.blur()" 
                                                            
                                                            style="background-color:#e9ecef;" 
                                                            autocomplete="off" type="text" name="nom_prenoms[]" class="form-control nom_prenoms" 
                                                            required
                                                            value="{{ $signataire->nom_prenoms ?? '' }}"
                                                            >

                                                        </td>

                                                        <td style="width: 1px; white-space:nowrap">
                                                            @if(isset($edit_signataire))
                                                                <a title="Ajouter un signataire" onclick="myCreateFunction2()" href="#" class="addRow">
                                                                    <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                    </svg>
                                                                </a>
                                                            @endif
                                                            
                                                    
                                                        </td>

                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>

                                                        <td style="width: 32%; padding-right:3px;">
                                                                <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                style="display: none"
                                                                required
                                                                >
                
                                                                <input
                                                                onkeyup="editAgent(this)" autocomplete="off" type="text" name="mle[]" class="form-control mle"
                                                                required
                                                                >

                                                        </td>

                                                        <td>
                                                            
                                                            <input onfocus="this.blur()" 
                                                            
                                                            style="background-color: transparent; border-color:transparent; 
                                                            font-weight:bold" 
                                                            autocomplete="off" type="text" name="nom_prenoms[]" class="form-control nom_prenoms" 
                                                            required
                                                            >

                                                        </td>

                                                        <td style="width: 1px; white-space:nowrap">
                                                            <a title="Ajouter un signataire" onclick="myCreateFunction2()" href="#" class="addRow">
                                                                <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
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
                                @endif
                                
                            </div>
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" >Code échéance </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onkeyup="editPeriode(this)" autocomplete="off" type="text" id="periodes_id" name="periodes_id" class="form-control" 
                                            value="{{ old('periodes_id') ?? $travauxe->libelle_periode ?? '' }}"
                                            
                                            style="background-color: #e9ecef;" onfocus="this.blur()"
                                            
                                            >

                                            <input onfocus="this.blur()" type="text" id="valeur" name="valeur" class="form-control" style="display: none" value="{{ old('valeur') ?? $travauxe->valeur ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input  onkeypress="validate(event)" required onkeyup="editPeriode2(this)" autocomplete="off" type="text" id="delai" name="delai" class="form-control" 

                                            value="{{ old('delai') ?? $travauxe->delai ?? '' }}"
                                            
                                            style="background-color: #e9ecef;" onfocus="this.blur()"
                                            

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="date_echeance" name="date_echeance" class="form-control" 
                                            value="{{ date('d/m/Y', strtotime($travauxe->date_echeance ?? '')) }}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" >Fournisseur </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onkeyup="editFr(this)" autocomplete="off" type="text" name="organisations_id" id="organisations_id" class="form-control" 
                                            value="{{ $travauxe->entnum ?? '' }}"

                                            style="background-color: #e9ecef;" onfocus="this.blur()"
                                            

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="denomination" id="denomination" class="form-control" 
                                            value="{{ $travauxe->denomination ?? '' }}"
                                            >
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
                                            <input onkeyup="editDevise(this)" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control" 
                                            value="{{ $travauxe->code ?? '' }}"
                                            
                                            style="background-color: #e9ecef;" onfocus="this.blur()"
                                            

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control" style="background-color: #e9ecef" 
                                            value="{{ $travauxe->devises_libelle ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                
                                @if(isset($travauxe->date_livraison_prevue))
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Date livraison</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror" @if(isset($travauxe->date_livraison_prevue))
                                            value="{{  $travauxe->date_livraison_prevue }}"
                                            @endif 
                                            
                                            style="background-color: #e9ecef;" onfocus="this.blur()" disabled
                                            
                                            >
                                            @error('date_livraison_prevue')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                </div>
                                @endif 

                                @if(isset($travauxe->date_retrait))
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Date retrait </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_retrait" class="form-control @error('date_retrait') is-invalid @enderror" 
                                            value="{{  $travauxe->date_retrait }}"
                                            style="background-color: #e9ecef;" onfocus="this.blur()" disabled >
                                            @error('date_retrait')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                </div>
                                @endif 
                                
                            </div>
                        </div>
                </div>

                <div class="panel panel-footer">
                    <table class="table table-bordered table-striped" id="myTable" width="100%">
                        <thead>
                            <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                <th style="width:50%; text-align:left">DÉSIGNATION</th>
                                <th style="width:10%; text-align:center">QTÉ</th>
                                <th style="width:13%; text-align:center">PU HT</th>
                                <th style="width:10%; text-align:center">REMISE (%)</th>
                                <th style="width:15%; text-align:center">MONTANT HT</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($detail_travauxes as $detail_travauxe)

                                <?php 
                                    $prix_unit_partie_entiere = null;
                                    $prix_unit_partie_decimale = null;

                                    $remise_partie_entiere = null;
                                    $remise_partie_decimale = null;

                                    $montant_ht_partie_entiere = null;
                                    $montant_ht_partie_decimale = null;
                                    
                                    if (isset($detail_travauxe)) {

                                        $montant_ht = number_format((float)$detail_travauxe->montant_ht, 2, '.', '');

                                        $block_montant_ht = explode(".",$montant_ht);
                                        
                                        
                                        
                                        if (isset($block_montant_ht[0])) {
                                            $montant_ht_partie_entiere = $block_montant_ht[0];
                                        }

                                        
                                        if (isset($block_montant_ht[1])) {
                                            $montant_ht_partie_decimale = $block_montant_ht[1];
                                        }

                                        $remise = number_format((float)$detail_travauxe->remise, 2, '.', '');

                                        $block_remise = explode(".",$remise);
                                        
                                        
                                        
                                        if (isset($block_remise[0])) {
                                            $remise_partie_entiere = $block_remise[0];
                                        }

                                        
                                        if (isset($block_remise[1])) {
                                            $remise_partie_decimale = $block_remise[1];
                                        }


                                        $prix_unit = number_format((float)$detail_travauxe->prix_unit, 2, '.', '');

                                        $block_prix_unit = explode(".",$prix_unit);
                                        
                                        
                                        
                                        if (isset($block_prix_unit[0])) {
                                            $prix_unit_partie_entiere = $block_prix_unit[0];
                                        }

                                        
                                        if (isset($block_prix_unit[1])) {
                                            $prix_unit_partie_decimale = $block_prix_unit[1];
                                        }
                                    }
                                    
                                ?>
                                
                            
                                <tr>
                                    <td style="border-collapse: collapse; padding: 10px; vertical-align:middle">
                                        {{ $detail_travauxe->libelle ?? '' }}
                                    </td>
                                    <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte" 
                                        
                                        style="background-color: #e9ecef; text-align:center; border-color: transparent;background-color: transparent;" onfocus="this.blur()" value="{{ strrev(wordwrap(strrev($detail_travauxe->qte ?? ''),3,' ',true)) }}">
                                    </td>
                                    <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="prix_unit[]" class="form-control prix_unit" 
                                        
                                        style="background-color: #e9ecef; text-align:right; border-color: transparent;background-color: transparent;" onfocus="this.blur()"                                       

                                        @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif 

                                        >

                                    </td>
                                    <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="remise[]" class="form-control remise" 
                                        
                                        style="background-color: #e9ecef; text-align:center; border-color: transparent;background-color: transparent;" onfocus="this.blur()"

                                        @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif
                                        
                                        >
                                    </td>
                                    <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required onfocus="this.blur()" onkeypress="validate(event)" style="border-color: transparent;background-color: transparent; text-align:right" type="text" name="montant_ht[]" class="form-control montant_ht"

                                        @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif

                                        >

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>

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

                        $tva_partie_entiere = null;
                        $tva_partie_decimale = null;

                        $montant_tva_partie_entiere = null;
                        $montant_tva_partie_decimale = null;

                        $taux_acompte_partie_entiere = null;
                        $taux_acompte_partie_decimale = null;
                        
                        if (isset($travauxe)) {

                            $taux_acompte = number_format((float)$travauxe->taux_acompte, 2, '.', '');

                            $block_taux_acompte = explode(".",$taux_acompte);
                            
                            
                            
                            if (isset($block_taux_acompte[0])) {
                                $taux_acompte_partie_entiere = $block_taux_acompte[0];
                            }

                            
                            if (isset($block_taux_acompte[1])) {
                                $taux_acompte_partie_decimale = $block_taux_acompte[1];
                            }

                            $tva = number_format((float)$travauxe->tva, 2, '.', '');

                            $block_tva = explode(".",$tva);
                            
                            
                            
                            if (isset($block_tva[0])) {
                                $tva_partie_entiere = $block_tva[0];
                            }

                            
                            if (isset($block_tva[1])) {
                                $tva_partie_decimale = $block_tva[1];
                            }

                            $montant_tva = 0;

                            if(isset($travauxe->montant_total_net) && isset($travauxe->tva)){
                                $montant_tva = number_format((float)(($travauxe->montant_total_net * ($travauxe->tva)/100)), 2, '.', '');
                            }

                            $montant_tva = number_format((float)$montant_tva, 2, '.', '');

                            $block_montant_tva = explode(".",$montant_tva);

                            
                            if (isset($block_montant_tva[0])) {
                                $montant_tva_partie_entiere = $block_montant_tva[0];
                            }

                            
                            if (isset($block_montant_tva[1])) {
                                $montant_tva_partie_decimale = $block_montant_tva[1];
                            }

                            $taux_acompte = number_format((float)$travauxe->taux_acompte, 2, '.', '');

                            $block_taux_acompte = explode(".",$taux_acompte);
                            
                            
                            
                            if (isset($block_taux_acompte[0])) {
                                $taux_acompte_partie_entiere = $block_taux_acompte[0];
                            }

                            
                            if (isset($block_taux_acompte[1])) {
                                $taux_acompte_partie_decimale = $block_taux_acompte[1];
                            }
                            
                            
                            $montant_total_brut = number_format((float)$travauxe->montant_total_brut, 2, '.', '');

                            $block_montant_total_brut = explode(".",$montant_total_brut);
                            
                            
                            
                            if (isset($block_montant_total_brut[0])) {
                                $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
                            }

                            
                            if (isset($block_montant_total_brut[1])) {
                                $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
                            }

                            $taux_remise_generale = number_format((float)$travauxe->taux_remise_generale, 2, '.', '');

                            $block_taux_remise_generale = explode(".",$taux_remise_generale);
                            
                            
                            
                            if (isset($block_taux_remise_generale[0])) {
                                $taux_remise_generale_partie_entiere = $block_taux_remise_generale[0];
                            }

                            
                            if (isset($block_taux_remise_generale[1])) {
                                $taux_remise_generale_partie_decimale = $block_taux_remise_generale[1];
                            }


                            $remise_generale = number_format((float)$travauxe->remise_generale, 2, '.', '');

                            $block_remise_generale = explode(".",$remise_generale);
                            
                            
                            
                            if (isset($block_remise_generale[0])) {
                                $remise_generale_partie_entiere = $block_remise_generale[0];
                            }

                            
                            if (isset($block_remise_generale[1])) {
                                $remise_generale_partie_decimale = $block_remise_generale[1];
                            }

                            $montant_total_net = number_format((float)$travauxe->montant_total_net, 2, '.', '');

                            $block_montant_total_net = explode(".",$montant_total_net);
                            
                            
                            
                            if (isset($block_montant_total_net[0])) {
                                $montant_total_net_partie_entiere = $block_montant_total_net[0];
                            }

                            
                            if (isset($block_montant_total_net[1])) {
                                $montant_total_net_partie_decimale = $block_montant_total_net[1];
                            }
                            

                            $montant_total_ttc = number_format((float)$travauxe->montant_total_ttc, 2, '.', '');

                            $block_montant_total_ttc = explode(".",$montant_total_ttc);
                            
                            
                            
                            if (isset($block_montant_total_ttc[0])) {
                                $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
                            }

                            
                            if (isset($block_montant_total_ttc[1])) {
                                $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
                            }


                            $net_a_payer = number_format((float)$travauxe->net_a_payer, 2, '.', '');

                            $block_net_a_payer = explode(".",$net_a_payer);
                            
                            
                            
                            if (isset($block_net_a_payer[0])) {
                                $net_a_payer_partie_entiere = $block_net_a_payer[0];
                            }
                            
                            if (isset($block_net_a_payer[1])) {
                                $net_a_payer_partie_decimale = $block_net_a_payer[1];
                            }

                            $montant_acompte = number_format((float)$travauxe->montant_acompte, 2, '.', '');

                            $block_montant_acompte = explode(".",$montant_acompte);
                            
                            
                            
                            if (isset($block_montant_acompte[0])) {
                                $montant_acompte_partie_entiere = $block_montant_acompte[0];
                            }

                            
                            if (isset($block_montant_acompte[1])) {
                                $montant_acompte_partie_decimale = $block_montant_acompte[1];
                            }
                        }
                    ?>

                    <table width="100%" style="margin-bottom:10px;">
                        <tr>
                            <td colspan="6" style="border: none">
                                <div class="row d-flex pl-3">
                                                       
                                        <div class="pr-0"><label class="label" class="font-weight-bold mt-1 mr-1">Montant total brut</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut" 

                                        @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev(old('montant_total_brut') ?? $montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev(old('montant_total_brut') ?? $montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif

                                            >
                                    
                                        </div>

                                        <div class="pl-1"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>  <input oninput ="validateNumber(this);" onkeyup="editTauxRemiseGenerale(this)"    autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale" 

                                            @if(isset($taux_remise_generale_partie_decimale) && $taux_remise_generale_partie_decimale != 0)
                                            value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                                
                                            style="background-color: #e9ecef; width:110px; text-align:right;" onfocus="this.blur()"
                                                
                                                >
                                        </div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold  mt-1 mr-1">Remise</label><br>  <input onkeyup="editRemiseGenerale(this)" onkeypress="validate(event)" autocomplete="off" type="text" name="remise_generale" class="form-control remise_generale" 

                                            @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                            value="{{ strrev(wordwrap(strrev(old('remise_generale') ?? $remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev(old('remise_generale') ?? $remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                            
                                        style="background-color: #e9ecef; width:110px; text-align:right;" onfocus="this.blur()"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total net ht</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"

                                        @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_net') ?? $montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_net') ?? $montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">TVA (%)</label><br>  <input onkeyup="editRemiseGenerale(this)" onkeypress="validate(event)" autocomplete="off" type="text" name="tva" class="form-control tva" 

                                            @if(isset($tva_partie_decimale) && $tva_partie_decimale != 0)
                                            value="{{ strrev(wordwrap(strrev(old('tva') ?? $tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev(old('tva') ?? $tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                            
                                            style="background-color: #e9ecef; width:80px; text-align:center" onfocus="this.blur()"
                                            
                                            
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva" 
                                            

                                        @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('montant_tva') ?? $montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('montant_tva') ?? $montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif
                                            
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total ttc</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"

                                        @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_ttc') ?? $montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_ttc') ?? $montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif
                                            
                                            ></div>

                                        <div class="pl-1" style="margin-left:0px;"><label class="label" class="font-weight-bold mt-1 mr-2">Net à payer</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer" 
                                            

                                        @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('net_a_payer') ?? $net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('net_a_payer') ?? $net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif

                                            > </div>

                                </div>
                                <div class="row d-flex pl-3">

                                    <div class="pl-1" style="padding-left:3px; text-align:center" ><label class="label" class="font-weight-bold mt-1 mr-2">Acompte</label><br>  <input style="" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" disabled></div>

                                    <div 
                                    
                                    
                                        @if(isset($travauxe->acompte))
                                            
                                            @if($travauxe->acompte === 1) 
                                                style="padding-left: 10px; text-align: center" 
                                            @else
                                                style="padding-left: 10px; text-align: center; display:none"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center; display:none"
                                        @endif

                                    
                                    
                                    id="acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">(%)</label><br>  <input maxlength="3" onkeyup="editTauxAcompte(this)" autocomplete="off" onkeypress="validate(event)" style="background-color: #e9ecef; width:70px; text-align:center;" type="text" name="taux_acompte" class="form-control taux_acompte" 

                                    @if(isset($taux_acompte_partie_decimale) && $taux_acompte_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev(old('taux_acompte') ?? $taux_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                    @else
                                    value="{{ strrev(wordwrap(strrev(old('taux_acompte') ?? $taux_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif
                                    
                                    onfocus="this.blur()"

                                    ></div>

                                    <div 
                                    
                                        @if(isset($travauxe->acompte))
                                                
                                            @if($travauxe->acompte === 1) 
                                                style="padding-left: 10px; text-align: center; display:none" 
                                            @else
                                                style="padding-left: 10px; text-align: center"

                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center"

                                        @endif

                                    
                                    id="i_acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                    <div class="pl-1" 
                                    
                                    
                                    @if(isset($travauxe->acompte))
                                            
                                        @if($travauxe->acompte === 1) 
                                            style="padding-left: 10px;" 
                                        @else
                                            style="padding-left: 10px; display:none"
                                        @endif

                                    @else
                                        style="padding-left: 10px; display:none"
                                    @endif

                                    
                                    id="acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onkeyup="editAcompte(this)" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_acompte" class="form-control montant_acompte" 
                                    
                                    @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev(old('montant_acompte') ?? $montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                    @else
                                    value="{{ strrev(wordwrap(strrev(old('montant_acompte') ?? $montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif

                                    onfocus="this.blur()"

                                    ></div>

                                    <div class="pl-1" 
                                    
                                    
                                    @if(isset($travauxe->acompte))
                                                
                                        @if($travauxe->acompte === 1) 
                                            style="padding-left: 10px; text-align: center; display:none" 
                                        @else
                                            style="padding-left: 10px; text-align: center"

                                        @endif

                                    @else
                                        style="padding-left: 10px; text-align: center"

                                    @endif

                                    
                                    id="i_acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control"></div>
                                
                                </div>
                            </td>
            
                        </tr>
                    </table>
                    <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                        <tr>
                            <td style="border: none;vertical-align: middle; text-align:right"  colspan="2">
                                
                            </td>
                        </tr>
                        @if(count($piece_jointes)>0)
                            @foreach($piece_jointes as $piece_jointe)
                                <tr>
                                    <td>
                                        
                                         
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
                                        
                                        

                                        
                                        

                                    </td>
                                </tr>
                            @endforeach
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                        @endif
                        
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
