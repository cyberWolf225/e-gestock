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
                <option value="{{ $structure->code_structure}}->{{ $structure->nom_structure }}">{{ $structure->nom_structure }}</option>
            @endforeach
        </datalist>
    @endif

    <datalist id="gestion">
        @foreach($gestions as $gestion)
            <option value="{{ $gestion->code_gestion }}->{{ $gestion->libelle_gestion }}">{{ $gestion->libelle_gestion }}</option>
        @endforeach
    </datalist> 

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
            <option value="{{ $organisation->id }}->{{ $organisation->entnum }}->{{ $organisation->denomination }}">{{ $organisation->denomination }}</option>
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
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime(date("Y-m-d"))) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $exercices->exercice ?? '' }}</strong></span>
                    
                </div>
                
                <form  enctype="multipart/form-data" method="POST" action="{{ route('travaux.store') }}">
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
                                            <label class="label">Compte budg. </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input style="display: none" onfocus="this.blur()" autocomplete="off" type="text" name="nbre_ligne" id="nbre_ligne" value="{{ old('nbre_ligne') ?? $nbre_ligne }}" class="form-control" >

                                            <input style="display: none" required autocomplete="off" type="text" name="id" id="id" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ old('id') ?? $credit_budgetaires_select->id ?? '' }}" <?php } ?>  class="form-control @error('id') is-invalid @enderror id" >

                                            <input required onkeyup="editCompte(this)"  list="famille" autocomplete="off" type="text" name="ref_fam" id="ref_fam" <?php if ($credit_budgetaires_select != null) { ?> value="{{ old('ref_fam') ?? $credit_budgetaires_select->ref_fam ?? '' }}" <?php } ?>  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ old('design_fam') ??$credit_budgetaires_select->design_fam ?? '' }}" <?php } ?> class="form-control design_fam">
                                        </div>
                                    </div>
                                </div>                                

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Structure <span style="color: red"><sup> *</sup></span> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none" value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? '' }}">

                                            <input required onkeyup="editStructure(this)" list="structure" autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure_default->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control @error('nom_structure') is-invalid @enderror nom_structure" style="background-color: #e9ecef" value="{{ old('nom_structure') ?? $structure_default->nom_structure ?? '' }}">
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
                                            <label class="label">Gestion <span style="color: red"><sup> *</sup></span> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required  onkeyup="editGestion(this)" list="gestion" autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion_default->code_gestion ?? '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control @error('libelle_gestion') is-invalid @enderror libelle_gestion" style="background-color: #e9ecef" value="{{ old('libelle_gestion') ?? $gestion_default->libelle_gestion ?? '' }}">
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
                                            <label class="label">Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php } ?> autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') }}</textarea>
                                        </div>
                                        @error('intitule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3" style="text-align: right"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible : </label></div>
                                    <div class="col-sm-3"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($credit_budgetaires_credit)) {{ strrev(wordwrap(strrev($credit_budgetaires_credit ?? 'aucun budget'), 3, ' ', true)) ?? '' }} @else Oups! pas de budget disponible @endif</label></div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Code échéance<span style="color: red"><sup> *</sup></span> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onkeyup="editPeriode(this)" list="list_periode" autocomplete="off" type="text" id="periodes_id" name="periodes_id" class="form-control" 
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif
                                            >

                                            <input onfocus="this.blur()" type="text" id="valeur" name="valeur" class="form-control" style="display: none">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input  onkeypress="validate(event)" required onkeyup="editPeriode2(this)" autocomplete="off" type="text" id="delai" name="delai" class="form-control" 
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="date_echeance" name="date_echeance" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Fournisseur </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required autocomplete="off" type="text" name="organisations_id" id="organisations_id" class="form-control" style="background-color: #e9ecef; display:none" onfocus="this.blur()">

                                            <input onkeyup="editFr(this)" list="list_organisation" autocomplete="off" type="text" name="entnum" id="entnum" class="form-control" 
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="denomination" id="denomination" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Devise <span style="color: red"><sup> *</sup></span></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onkeyup="editDevise(this)" list="list_devise" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control" 
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control" style="background-color: #e9ecef">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Date livraison</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror" 
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()" disabled
                                            @endif

                                            >
                                            @error('date_livraison_prevue')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                </div>

                <div class="panel panel-footer">
                    <table class="table table-bordered table-striped" id="myTable" width="100%">
                        <thead>
                            <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                <th style="width:50%; text-align:left">DÉSIGNATION</th>
                                <th style="width:10%; text-align:center">QTÉ<span style="color: red"><sup> *</sup></span></th>
                                <th style="width:13%; text-align:center">PU HT<span style="color: red"><sup> *</sup></span></th>
                                <th style="width:10%; text-align:center">REMISE (%)</th>
                                <th style="width:15%; text-align:center">MONTANT HT</th>
                                <th style="text-align:center; width:1%">
                                    @if(!isset($griser))
                                        
                                    
                                        <a onclick="myCreateFunction()" href="#" class="addRow">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                            </svg>
                                        </a>
                                    @endif
                                
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input list="list_service" autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service" 
                                    
                                    @if(isset($griser))
                                        style="background-color: #e9ecef" onfocus="this.blur()"
                                    @endif
                                    >
                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte" 
                                    
                                    @if(isset($griser))
                                        style="background-color: #e9ecef; text-align:center" onfocus="this.blur()"
                                        @else
                                        style="text-align:center"
                                    @endif

                                    >
                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input autocomplete="off" required type="text" onkeyup="editMontant(this)" oninput="validateNumber(this);" name="prix_unit[]" class="form-control prix_unit" 
                                    
                                    @if(isset($griser))
                                        style="background-color: #e9ecef; text-align:right" onfocus="this.blur()"
                                        @else
                                        style="text-align:right"
                                    @endif
                                    
                                    >

                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input autocomplete="off" type="text" onkeyup="editMontant(this)" oninput="validateNumber(this);" name="remise[]" class="form-control remise" 
                                    
                                    @if(isset($griser))
                                        style="background-color: #e9ecef; text-align:center" onfocus="this.blur()"
                                        @else
                                        style="text-align:center"
                                    @endif
                                    
                                    >
                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="border-color: transparent;background-color: transparent; text-align:right" type="text" name="montant_ht[]" class="form-control montant_ht">

                                    <input autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis">

                                </td>
                                <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                    @if(!isset($griser))
                                        
                                    
                                    <a onclick="removeRow(this)" href="#" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg></a>
                                    
                                    @endif
                                </td>
                            </tr>
                            
                        </tbody>
                        
                    </table>
                    <table width="100%" style="margin-bottom:10px;">
                        <tr>
                            <td colspan="6" style="border: none">
                                <div class="row d-flex pl-3">
                                                       
                                        <div class="pr-0"><label class="label" class="font-weight-bold mt-1 mr-1">Montant total brut</label><br>  <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut">
                                    
                                        </div>

                                        <div class="pl-1"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>  <input oninput ="validateNumber(this);" onkeyup="editTauxRemiseGenerale(this)" autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale">
                                        </div>

            
                                        <div class="pl-1"><label class="label" class="font-weight-bold  mt-1 mr-1">Montant remise</label><br>  <input onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off" type="text" name="remise_generale" class="form-control remise_generale" 
                                            
                                            @if(isset($griser))
                                                style="background-color: #e9ecef; width:110px; text-align:right;" onfocus="this.blur()"
                                                @else
                                                style="width:110px; text-align:right;"
                                            @endif
                                            
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total net ht</label><br>  <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">TVA (%)</label><br>  <input list="list_taxe_tva" onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off" type="text" name="tva" class="form-control tva" 
                                            
                                            @if(isset($griser))
                                                style="background-color: #e9ecef; width:80px; text-align:center" onfocus="this.blur()"
                                                @else
                                                style="width:80px; text-align:center"
                                            @endif
                                            
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total ttc</label><br>  <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"></div>

                                        <div class="pl-1" style="margin-left:0px;"><label class="label" class="font-weight-bold mt-1 mr-2">Net à payer</label><br>  <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"> </div>

                                </div>
                                <div class="row d-flex pl-3">

                                    <div class="pl-1" style="padding-left:3px; text-align:center" ><label class="label" class="font-weight-bold mt-1 mr-2">Acompte</label><br>  <input style="" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" 
                                        
                                        @if(isset($griser))
                                            disabled
                                        @endif
                                        ></div>

                                    <div 
                                    
                                    
                                        {{-- @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                                style="padding-left: 10px; text-align: center" 
                                            @else
                                                style="padding-left: 10px; text-align: center; display:none"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center; display:none"
                                        @endif --}}

                                        style="padding-left: 10px; text-align: center; display:none"
                                    
                                    
                                    id="acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">(%)</label><br>  <input onkeyup="editTauxAcompte(this)" autocomplete="off" oninput="validateNumber(this);" style=" width:70px; text-align:center;" type="text" name="taux_acompte" class="form-control taux_acompte"></div>

                                    <div 
                                    
                                        {{-- @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                                style="padding-left: 10px; text-align: center; display:none" 
                                            @else
                                                style="padding-left: 10px; text-align: center"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center"
                                        @endif --}}

                                        style="padding-left: 10px; text-align: center"
                                    
                                    id="i_acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                    <div class="pl-1" 
                                    
                                    
                                        {{-- @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                            
                                            @else
                                            style="display:none"
                                            @endif

                                        @else
                                        style="display:none"
                                        @endif --}}

                                        style="display:none"

                                    
                                    id="acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onkeyup="editAcompte(this)" oninput="validateNumber(this);" style=" width:110px; text-align:right" type="text" name="montant_acompte" class="form-control montant_acompte"></div>

                                    <div class="pl-1" 
                                    
                                    
                                        {{-- @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                                style="display:none"
                                            @else
                                            
                                            @endif

                                        @else
                                        
                                        @endif --}}

                                    
                                    id="i_acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control"></div>
                                
                                </div>
                            </td>
            
                        </tr>
                    </table>

                    <table style="width:100%" id="tablePiece">
                                
                        <tr>
                            <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">

                                @if(!isset($griser))
                                <a title="Joindre une nouvelle pièce" onclick="myCreateFunction2()" href="#"><svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg></a> 
                                @endif
                            </td>
                        </tr>

                        <tr>
                            
                            <td>
                                @if(!isset($griser))
                                
                                    <input type="file" name="piece[]" class="form-control-file" id="piece" >  
                                
                                @endif
                            </td>
                            <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;">
                                @if(!isset($griser))
                                    <a title="Retirer ce fichier" onclick="removeRow2(this)" href="#">
                                        <svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </a>
                                @endif

                            </td>
                        </tr>
                            
                        

                        <tr>
                            <td colspan="2" style="vertical-align: middle; text-align:right;"> 
                                @if(!isset($griser))

                                    <br/> <br/>

                                    <button onclick="return confirm('Faut-il enregistrer ?')" type="submit" class="btn btn-success">Enregistrer</button>
                                    &nbsp;
                                    &nbsp;
                                    &nbsp;

                                    <br/> <br/>
                                @endif
                            </td>
                        </tr>

                    </table>
                    
                </div>
                </form>

            </div>



            
        </div>


        
    </div>
</div>

<script>

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
                        document.location.replace('/travaux/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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
                                document.location.replace('/travaux/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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
                            document.location.replace('/travaux/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
                        }
                    }
                }


        }
        
    }

    editFr = function(a){
        const saisie = document.getElementById('entnum').value;
        const opts = document.getElementById('list_organisation').childNodes;

        
        for (var i = 0; i < opts.length; i++) {
        if (opts[i].value === saisie) {
            
            if(saisie != ''){
            const block = saisie.split('->');
            
            var organisations_id = block[0];
            var entnum = block[1];
            var denomination = block[2];
            
            }else{
                var organisations_id = "";
                var entnum = "";
                var denomination = "";
            }

            document.getElementById('entnum').value = entnum;
            if (denomination === undefined) {
                document.getElementById('denomination').value = "";
                document.getElementById('organisations_id').value = "";
            }else{
                
                document.getElementById('denomination').value = denomination;
                document.getElementById('organisations_id').value = organisations_id;

            }
            
            break;
        }else{
            document.getElementById('denomination').value = "";
            document.getElementById('organisations_id').value = "";
        }
        }
    }

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

        total();
    }    

    function total(){
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

            if (montant_acompte === 0) {
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

                    $('.montant_acompte').val(montant_acompte); 
                }

            }else{
                var taux_acompte = (montant_acompte / net_a_payer) * 100;
                    
                $('.taux_acompte').val(taux_acompte);
                $('.montant_acompte').val(montant_acompte);
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

        var montant_acompte=tr.find('.montant_acompte').val();  

        if (montant_acompte != '') {
            montant_acompte = montant_acompte.trim();
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = montant_acompte.replace(' ','');
            montant_acompte = reverseFormatNumber(montant_acompte,'fr');  
            montant_acompte = montant_acompte * 1;

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

            if (montant_acompte == 0) {

                var taux_acompte=$('.taux_acompte').val();

                if (taux_acompte != '') {
                    taux_acompte = taux_acompte.trim();
                    taux_acompte = taux_acompte.replace(' ','');
                    taux_acompte = taux_acompte.replace(' ','');
                    taux_acompte = reverseFormatNumber(taux_acompte,'fr');  

                    if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                        // alert(taux_acompte);
                        if(taux_acompte === null){
                        taux_acompte = 0;
                        }else{
                            taux_acompte = (taux_acompte/100);
                        }

                        var montant_acompte = net_a_payer * taux_acompte;

                        $('.montant_acompte').val(montant_acompte.toFixed(2)); 
                    }else{
                        alert("Attention !!!  Taux acompte invalide : Le taux d'acompte ne peut être supérieur à 100%.");
                        tr.find('.taux_acompte').val(0);
                        tr.find('.montant_acompte').val(0);
                    }
                }
                

            }else{
                if (taux_acompte != '') {
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
                

            }
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
        
        total();
        
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
