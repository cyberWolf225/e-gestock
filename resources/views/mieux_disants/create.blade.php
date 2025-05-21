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
    @if(isset($familles))
        <datalist id="famille">
            @foreach($familles as $famille_data)
                <option value="{{ $famille_data->ref_fam}}->{{ $famille_data->design_fam }}">{{ $famille_data->design_fam }}</option>
            @endforeach
        </datalist>
    @endif
    @if(isset($structures))
        <datalist id="structure">
            @foreach($structures as $structure_data)
                <option value="{{ $structure_data->code_structure}}->{{ $structure_data->nom_structure }}">{{ $structure_data->nom_structure }}</option>
            @endforeach
        </datalist>
    @endif
    @if(isset($gestions))
        <datalist id="gestion">
            @foreach($gestions as $gestion_data)
                <option value="{{ $gestion_data->code_gestion }}->{{ $gestion_data->libelle_gestion }}">{{ $gestion_data->libelle_gestion }}</option>
            @endforeach
        </datalist> 
    @endif  
    @if(isset($articles))
        <datalist id="list_article">            
            @foreach($articles as $article_data)
                <option value="{{ $article_data->ref_articles }}->{{ $article_data->design_article }}">{{ $article_data->design_article }}</option>
            @endforeach  
        </datalist>
    @endif
    @if(isset($description_articles))
        <datalist id="description_articles">
            @foreach($description_articles as $description_article_data)
                <option>{{ $description_article_data->libelle ?? '' }}</option>
            @endforeach
        </datalist>
    @endif
    @if(isset($services))
        <datalist id="list_service">
            @foreach($services as $service_data)
                <option>{{ $service_data->libelle }}</option>
            @endforeach
        </datalist>
    @endif
    @if(isset($unites))
        <datalist id="list_unite">
            @foreach($unites as $unite_data)
                <option>{{ $unite_data->unite }}</option>
            @endforeach
        </datalist>
    @endif
    @if(isset($organisations))
        <datalist id="list_organisation">
            @foreach($organisations as $organisation_list)
                <option value="{{ $organisation_list->id ?? ''}}->{{ $organisation_list->entnum ?? '' }}->{{ $organisation_list->denomination ?? '' }}->{{ $organisation_list->sigle ?? '' }}">{{ $organisation_list->denomination ?? '' }}</option>
            @endforeach
        </datalist>
    @endif
    @if(isset($periodes))
        <datalist id="list_periode">            
            @foreach($periodes as $periode_list)
                <option value="{{ $periode_list->id }}->{{ $periode_list->libelle_periode }}->{{ $periode_list->valeur }}">{{ $periode_list->libelle_periode }}</option>
            @endforeach  
        </datalist>
    @endif
    <datalist id="list_taux_acompte">

        @for($i = 0; $i < 101; $i++)
            <option value="{{ $i }}">{{ $i.'%' }}</option>
        @endfor
        
    </datalist>
    @if(isset($devises))
        <datalist id="list_devise">       
            @foreach($devises as $devise)
                <option value="{{ $devise->code }}->{{ $devise->libelle }}->{{ $devise->symbole }}">{{ $devise->libelle }}</option>
            @endforeach  
        </datalist>
    @endif
    @if(isset($taxes))
        <datalist id="list_taxe_tva">            
            @foreach($taxes as $taxe)
                <option value="{{ $taxe->taux }}">{{ $taxe->nom_taxe }}->{{ $taxe->taux }}</option>
            @endforeach  
        </datalist>
    @endif
    <div class="row"> 
        <div class="col-md-12">

            <div class="card">
                <div class="card-header entete-table">{{ __( $buttons['titre'] ?? 'SÉLECTION DU MIEUX DISANT') }} 
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
                    
                    <form enctype="multipart/form-data" method="POST" action="{{ route('mieux_disants.store') }}">
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
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $fournisseur_demande_cotation->organisations_id ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $fournisseur_demande_cotation->entnum ?? '' }}" autocomplete="off" type="text" name="entnum" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $fournisseur_demande_cotation->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control">
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
                                        @include('mieux_disants.partial_create_bcs')
                                    </div>
                                        
                                    @endif
                                @endif
                                

                                @if(isset($demande_cotation->libelle))
                                    @if($demande_cotation->libelle === "Commande non stockable")
                                        
                                    <div id="div_detail_bcn" style="@if($display_div_detail_bcn === null) display:none @endif">
                                        @include('mieux_disants.partial_create_bcn')
                                    </div>
                                        
                                    @endif
                                @endif
                                

                                @include('mieux_disants.partial_create_footer')
                                
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
                                    <br/>
                                    <table style="width: 100%">
                                        <tr>
                                            <td colspan="4" style="border-bottom: none">
                                                <span style="font-weight: bold">Dernier commentaire écrit par </span> <span style="color: brown; margin-left:3px;"> {{ $statut_demande_cotation_frs->nom_prenoms ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $statut_demande_cotation_frs->name ?? '' }} </span>)

                                                <br>
                                                <br>
                                                <table width="100%" cellspacing="0" border="1" style="background-color:#d4edda; border-color:#155724; font-weight:bold">
                                                    <tr>
                                                        <td>
                                                            <svg style="color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-text-fill" viewBox="0 0 16 16">
                                                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4.414a1 1 0 0 0-.707.293L.854 15.146A.5.5 0 0 1 0 14.793V2zm3.5 1a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 2.5a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5z"/>
                                                            </svg> &nbsp; {{ $statut_demande_cotation_frs->commentaire ?? '' }}
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
                                                
                                                    <div class="col-md-7">

                                                        <textarea class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                                        @error('commentaire')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    

                                                    <div class="col-md-5" style="margin-top:px">
                                                

                                                        @if(isset($buttons['value_button2']))
                                                            <button  onclick="return confirm('{{ $buttons['confirm2'] ?? '' }}')" style="margin-left:10px; width:100px;" type="submit" name="submit" value="{{ $buttons['value_button2'] }}" class="{{ $buttons['class_button2'] ?? '' }}">{{ $buttons['button2'] }}</button>
                                                        @endif

                                                        @if(isset($buttons['value_button']))
                                                            <button  onclick="return confirm('{{ $buttons['confirm'] ?? '' }}')" style="margin-left:10px; width:100px;" type="submit" name="submit" value="{{ $buttons['value_button'] }}" class="{{ $buttons['class_button'] ?? '' }}">{{ $buttons['button'] }}</button>
                                                        @endif

                                                        @if(isset($buttons['value_button3']))
                                                            <button  onclick="return confirm('{{ $buttons['confirm3'] ?? '' }}')" style="margin-left:10px; width:100px;" type="submit" name="submit" value="{{ $buttons['value_button3'] }}" class="{{ $buttons['class_button3'] ?? '' }}">{{ $buttons['button3'] }}</button>
                                                        @endif

                                                    </div>
                                                </div>

                                            </td>

                                        </tr>
                                    </table>
                                </div>                                
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    // //Empecher la saisie de lettre
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

    function reverseFormatNumber(val,locale){
        var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
        var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
        var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
        reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
        return Number.isNaN(reversedVal)?0:reversedVal;
    }
    
    editQteBcs = function(e){
        var tr=$(e).parents("tr");
        var qte_bcs=tr.find('.qte_bcs').val();
        qte_bcs = qte_bcs.trim();
        qte_bcs = reverseFormatNumber(qte_bcs,'fr');

        qte_bcs = qte_bcs * 1;

        if (qte_bcs <= 0) {
            tr.find('.qte_bcs').val("");
        }else{
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte_bcs').val(int3.format(qte_bcs));
        }
        
    }

    editQteBcn = function(e){
        var tr=$(e).parents("tr");
        var qte_bcn=tr.find('.qte_bcn').val();
        qte_bcn = qte_bcn.trim();
        qte_bcn = reverseFormatNumber(qte_bcn,'fr');

        qte_bcn = qte_bcn * 1;

        if (qte_bcn <= 0) {
            tr.find('.qte_bcn').val("");
        }else{
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte_bcn').val(int3.format(qte_bcn));
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
      var nbre_ligne = document.getElementById("nbre_ligne").value;
      nbre_ligne = nbre_ligne * 1;
        
            var row = table.insertRow(nbre_rows);
        if (nbre_rows <= nbre_ligne) {

            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);

            cell1.innerHTML = '<td><input required <?php if ($griser_demande!=null) { ?> style="background-color: transparent; border: none;" onfocus="this.blur()" <?php }else{ ?> style="background-color: ; border:  ;" onkeyup="editDesign(this)" list="list_article" <?php } ?> autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles"></td>';
            cell2.innerHTML = '<td><textarea rows="1" required autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article"></textarea></td>';
            cell3.innerHTML = '<td><textarea style="border-color:transparent; resize:none" rows="1" list="description_articles" autocomplete="off" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle"></textarea></td>';
            cell4.innerHTML = '<td><input style="border-color:transparent;" list="list_unite" autocomplete="off" type="text" id="unites_libelle_bcs" name="unites_libelle_bcs[]" class="form-control unites_libelle_bcs"/></td>';
            cell5.innerHTML = '<td><textarea rows="1" maxlength="12" onkeyup="editQteBcs(this)" onkeypress="validate(event)" <?php if ($griser_demande==null) { ?> style="text-align:center; border-color:transparent; resize:none" <?php } ?> required <?php if ($griser_demande!=null) { ?> style="background-color: #e9ecef; text-align:center; border-color:transparent; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text" id="qte_bcs" name="qte_bcs[]" class="form-control qte_bcs"></textarea></td>';
            cell6.innerHTML = '<td><input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcs[]"></td>';
            cell7.innerHTML = '<td><a onclick="removeRow(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';

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
            cell6.style.textAlign = "left";

            cell7.style.borderCollapse = "collapse";
            cell7.style.padding = "0";
            cell7.style.margin = "0";
            cell7.style.verticalAlign = "middle";
            cell7.style.textAlign = "center";

        }
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<3){
                
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'info',
                html:
                    'Vous ne pouvez pas supprimer la dernière ligne',
                showCloseButton: true, 
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });
            }else{
                totalCf();
                $(el).parents("tr").remove(); 
                totalCf();
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
            cell2.innerHTML = '<td><a title="Retirer ce fichier" onclick="removeRow2(this)" ><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
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

    var currentValue = 0;
    function handleClick(myRadio) {
        currentValue = myRadio.value;

        var div_detail_bcs = document.getElementById("div_detail_bcs");
        var div_detail_bcn = document.getElementById("div_detail_bcn");
        var div_save = document.getElementById("div_save");
        var input_ref_articles = document.getElementById("ref_articles");
        var input_design_article = document.getElementById("design_article");
        var input_qte_bcs = document.getElementById("qte_bcs");
        var input_qte_bcn = document.getElementById("qte_bcn");
        var input_libelle_service = document.getElementById("libelle_service");
                
        if(currentValue === "BCS"){       
            div_detail_bcs.style.display = "";
            div_detail_bcn.style.display = "none";
            div_save.style.display = "";

            if (input_libelle_service.hasAttribute("required")) {
                input_libelle_service.removeAttribute("required");
            }

            if (input_qte_bcn.hasAttribute("required")) {
                input_qte_bcn.removeAttribute("required");
            }

            input_ref_articles.setAttribute("required","");
            input_design_article.setAttribute("required","");
            input_qte_bcs.setAttribute("required","");
        }

        if(currentValue === "BCN"){
            div_detail_bcs.style.display = "none";
            div_detail_bcn.style.display = "";
            div_save.style.display = ""; 
            
            if (input_ref_articles.hasAttribute("required")) {
                input_ref_articles.removeAttribute("required");
            }

            if (input_design_article.hasAttribute("required")) {
                input_design_article.removeAttribute("required");
            }

            if (input_qte_bcs.hasAttribute("required")) {
                input_qte_bcs.removeAttribute("required");
            }

            input_libelle_service.setAttribute("required","");
            input_qte_bcn.setAttribute("required","");
        }

    }

    function myCreateFunctionBcn() {
        var table = document.getElementById("myTableBcn");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);
        cell1.innerHTML = '<td><input list="list_service" autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service"></td>';
        cell2.innerHTML = '<td><input list="list_unite" autocomplete="off" type="text"  id="unites_libelle_bcn" name="unites_libelle_bcn[]" class="form-control unites_libelle_bcn"></td>';
        cell3.innerHTML = '<td><input style="text-align:center" autocomplete="off" required type="text" onkeyup="editQteBcn(this)" onkeypress="validate(event)" id="qte_bcn" name="qte_bcn[]" class="form-control qte_bcn"></td>';
        cell4.innerHTML = '<td><input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcn[]"></td>';
        cell5.innerHTML = '<td><a onclick="removeRowBcn(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';


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
        cell4.style.verticalAlign = "middle";
        cell4.style.textAlign = "left";

        cell5.style.borderCollapse = "collapse";
        cell5.style.padding = "0";
        cell5.style.margin = "0";
        cell5.style.verticalAlign = "middle";
        cell5.style.textAlign = "center";      
    }

    removeRowBcn = function(el) {
        var table = document.getElementById("myTableBcn");
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
                totalCf();
                $(el).parents("tr").remove(); 
                totalCf();
            }  
    }

    function myCreateFunctionOrg() {
        var table = document.getElementById("myTableOrg");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);
        cell1.innerHTML = '<td><input style="text-align:center" autocomplete="off" required type="text" id="organisations_id" name="organisations_id[]" class="form-control organisations_id" list="list_organisation" onkeyup="editOrganisation(this)" </td>';
        cell2.innerHTML = '<td><input autocomplete="off" type="text"  id="entnum" name="entnum[]" class="form-control entnum" style="background-color:transparent; border:none" onfocus="this.blur()"></td>';
        cell3.innerHTML = '<td><input autocomplete="off" required type="text" id="denomination" name="denomination[]" class="form-control denomination" style="background-color:transparent; border:none;" onfocus="this.blur()"></td>';
        cell4.innerHTML = '<td><input autocomplete="off" type="text" id="sigle" name="sigle[]" class="form-control sigle" style="background-color:transparent; border:none;" onfocus="this.blur()"></td>';
        cell5.innerHTML = '<td><a style="cursor:pointer;" onclick="removeRowOrg(this)"  class="remove" title="Retirer ce fournisseur"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';


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
        cell4.style.verticalAlign = "middle";

        cell5.style.borderCollapse = "collapse";
        cell5.style.padding = "0";
        cell5.style.margin = "0";
        cell5.style.verticalAlign = "middle";
        cell5.style.textAlign = "center";      
    }
    
    editOrganisation = function(a){
        var tr=$(a).parents("tr");
        const saisie=tr.find('.organisations_id').val();
        const opts = document.getElementById('list_organisation').childNodes;

        for (var i = 0; i < opts.length; i++) {
            if (opts[i].value === saisie) {

                if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var organisations_id = block[0];
                    var entnum = block[1];
                    var denomination = block[2];
                    var sigle = block[3];

                    
                }else{
                    var organisations_id = "";
                    var entnum = "";
                    var denomination = "";
                    var sigle = "";
                }

                tr.find('.organisations_id').val(organisations_id);
                if (organisations_id === undefined) {
                    tr.find('.organisations_id').val(saisie);
                }else{
                    tr.find('.entnum').val(entnum);
                    tr.find('.denomination').val(denomination);
                    tr.find('.sigle').val(sigle);
                }
                break;
            }else{
                tr.find('.entnum').val("");
                tr.find('.denomination').val("");
                tr.find('.sigle').val("");
            }
        }
    }

    removeRowOrg = function(el) {
        var table = document.getElementById("myTableOrg");
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

    editPeriode = function(a){
        const saisie=document.getElementById('libelle_periode').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var libelle_periode = block[1];
            var valeur = block[2];

            var delai = document.getElementById('delai').value;
            if(delai==""){
                delai = 1;
            }


            var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
            
            var date_echeance = moment(d).format('DD/MM/YYYY');
        }else{
            libelle_periode = "";
            valeur = "";
        }
        
        if (libelle_periode === undefined) {
            document.getElementById('libelle_periode').value = "";
        }else{
            document.getElementById('libelle_periode').value = libelle_periode;
        }

        if (valeur === undefined) {
            document.getElementById('valeur').value = "";
        }else{
            document.getElementById('valeur').value = valeur;
        }

        if (date_echeance === 'Invalid date') {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
            document.getElementById('delai').value = delai;
        }
        
    }  
    
    editPeriode2 = function(a){
            var libelle_periode = document.getElementById('libelle_periode').value;
            var valeur = document.getElementById('valeur').value;
            var delai = document.getElementById('delai').value;
            if (libelle_periode != '') {
                if (valeur != undefined) {
                    if(delai==""){
                        delai = 1;
                    }


                    var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
                    
                    var date_echeance = moment(d).format('DD/MM/YYYY');
                }
            }
        
        
        
        if (libelle_periode === undefined) {
            
        }else{
            document.getElementById('libelle_periode').value = libelle_periode;
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
        const saisie=document.getElementById('code_devise').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var code_devise = block[0];
            var libelle_devise = block[1];
            
        }else{
            code_devise = "";
            libelle_devise = "";
        }
        
        document.getElementById('code_devise').value = code_devise;
        if (libelle_devise === undefined) {
            document.getElementById('libelle_devise').value = "";
        }else{
            document.getElementById('libelle_devise').value = libelle_devise;
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

    function formatInt3(number) {
        nombre = number.trim();
        nombre = nombre.replace(' ','');
        nombre = nombre.replace(' ','');
        nombre = nombre.replace(' ','');
        nombre = nombre.replace(' ','');
        nombre = reverseFormatNumber(nombre,'fr');
        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        if(nombre.toString().split('.')[1] !== undefined){
            let decimalDigits = nombre.toString().split('.')[1];
            let decimalPlaces = decimalDigits.length;
            let decimalDivider = Math.pow(10, decimalPlaces);
            let fractionValue = decimalDigits/decimalDivider;
            let integerValue = nombre - fractionValue;
            if (decimalDigits == '00') {
                var response = int3.format(integerValue);
            }else{
                var response = int3.format(integerValue)+'.'+decimalDigits;
            }
        }else{
            var response = int3.format(nombre);
        }
        return response;
    }

    editMontant = function(e){
        var tr=$(e).parents("tr");
        var qte=tr.find('.qte').val();
        qte = qte.trim();
        qte = qte.replace(' ','');
        qte = reverseFormatNumber(qte,'fr');
        qte = qte * 1;

        var qte_cde=tr.find('.qte_cde').val();
        qte_cde = qte_cde.trim();
        qte_cde = qte_cde.replace(' ','');
        qte_cde = reverseFormatNumber(qte_cde,'fr');
        qte_cde = qte_cde * 1;
        
        if(qte_cde < qte){
            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'La quatité de la cotation ne peut être supérieure à la quantité commandée',
            focusConfirm: false,
            confirmButtonText:
                'Compris'
            });
            qte = qte_cde;
        }

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