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
<div class="container">
<br>

    <datalist id="famille">
        @foreach($familles as $famille_list)
            <option value="{{ $famille_list->ref_fam}}->{{ $famille_list->design_fam }}->{{ $famille_list->id }}->{{ Crypt::encryptString($famille_list->id)}}">{{ $famille_list->design_fam }}</option>
        @endforeach
    </datalist>

    @if(isset($structures))
        <datalist id="structure">
            @foreach($structures as $structure_list)
                <option value="{{ $structure_list->code_structure}}->{{ $structure_list->nom_structure }}">{{ $structure_list->nom_structure }}</option>
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

    <datalist id="gestion">
        @foreach($gestions as $gestion_list)
            <option value="{{ $gestion_list->code_gestion }}->{{ $gestion_list->libelle_gestion }}">{{ $gestion_list->libelle_gestion }}</option>
        @endforeach
    </datalist> 

    <datalist id="agent_list">
        @foreach($agents as $agent)
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
                    ->where('a.mle',$agent->mle)
                    ->where('f.libelle',$agent->libelle)
                    ->first();
                if ($agen!=null) {
                    $profil_fonctions_id = $agen->id;

                    ?>
                        <option value="{{ $profil_fonctions_id ?? '' }}->{{ $agent->mle ?? '' }}->{{ $agent->nom_prenoms ?? '' }}->{{ $agent->libelle ?? '' }}">{{ $agent->nom_prenoms ?? '' }}</option>
                    <?php
                }
            ?>
            
        @endforeach
    </datalist> 

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ $entete ?? '' }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime( $travauxe->created_at ?? date("Y-m-d"))) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $travauxe->exercice ?? '' }}</strong></span>
                    
                </div>

                <form enctype="multipart/form-data" method="POST" action="{{ route('travaux.update') }}">
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
                                            <label class="label mt-1 font-weight-bold">
                                                N° Bon Cde.
                                            </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="margin-top:-3px; color:red; font-weight:bold" autocomplete="off" type="text" id="num_bc" name="num_bc" value="{{ old('num_bc') ?? $travauxe->num_bc ?? '' }}" class="form-control griser @error('num_bc') is-invalid @enderror num_bc" >
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
                                            <label class="label">Compte budg. <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?> </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input type="text" value="{{ old('travauxes_id') ?? $travauxe->travauxes_id ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; display:none" required name="travauxes_id">

                                            <input style="display: none" onfocus="this.blur()" autocomplete="off" type="text" name="nbre_ligne" id="nbre_ligne" value="{{ old('nbre_ligne') ?? $nbre_ligne }}" class="form-control" >

                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" required autocomplete="off" type="text" name="id" id="id" value="{{ old('id') ?? $compte_budgetaires_select->id ?? '' }}"  class="form-control @error('id') is-invalid @enderror id" >

                                            {{-- list="famille" onfocus="this.blur()" style="background-color: #e9ecef" --}}
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editCompte(this)"  list="famille" <?php } ?>  autocomplete="off" type="text" name="ref_fam" id="ref_fam" value="{{ old('ref_fam') ?? $compte_budgetaires_select->ref_fam ?? '' }}"  class="form-control @error('ref_fam') is-invalid @enderror ref_fam"  >
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" value="{{ old('design_fam') ?? $compte_budgetaires_select->design_fam ?? '' }}" class="form-control design_fam">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Structure <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none"
                                            
                                            @if(isset($disponible_display))
                                                value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? '' }}"
                                            @else
                                                value="{{ old('credit_budgetaires_id') ??  $travauxe->credit_budgetaires_id ?? '' }}"
                                            @endif

                                            >

                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editStructure(this)" list="structure" <?php } ?>  autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure->code_structure ?? $travauxe->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control @error('nom_structure') is-invalid @enderror nom_structure" style="background-color: #e9ecef" value="{{ old('nom_structure') ?? $structure->nom_structure ?? $travauxe->nom_structure ?? '' }}">
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
                                            <label class="label">Gestion <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editGestion(this)" list="gestion" <?php } ?> autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion->code_gestion ??  $travauxe->code_gestion ?? '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control @error('libelle_gestion') is-invalid @enderror libelle_gestion" style="background-color: #e9ecef" value="{{ old('libelle_gestion') ?? $gestion->libelle_gestion ?? $travauxe->libelle_gestion ?? '' }}">
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
                                            <label class="label">Intitulé <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') ?? $travauxe->intitule ?? '' }}</textarea>
                                        </div>
                                        @error('intitule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                
                                @if(isset($edit_signataire))
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">

                                                <label class="label" class="mt-1 ">Signataire <!--<sup style="color: red">*</sup>
                                                    
                                                    <br/>
                                                    
                                                    <a title="Ajouter un signataire" onclick="myCreateFunction2()" class="addRow" style="cursor:pointer">
                                                        <i style="font-size:10px">Ajouter un signataire</i> -->
                                                    </a>
                                                    
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
                                                                    <input onfocus="this.blur()" autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                    style="display:none"
                                                                    required
                                                                    value="{{ $signataire->profil_fonctions_id ?? '' }}"
                                                                    >
                                                                    <!--list="agent_list" onkeyup="editAgent(this)"-->
                                                                    <input
                                                                    
                                                                    onfocus="this.blur()" 
                                                                
                                                                    style="background-color: #e9ecef;"
                                                                    
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
                                                                
                                                                style="background-color: #e9ecef;" 
                                                                autocomplete="off" type="text" name="nom_prenoms[]" class="form-control nom_prenoms" 
                                                                required
                                                                value="{{ $signataire->nom_prenoms ?? '' }}"
                                                                >
    
                                                            </td>
    
                                                            <td style="width: 1px; white-space:nowrap">
                                                                @if(isset($edit_signataire))
                                                                    @if($o === 1)
                                                                    <!--
                                                                        <a title="Ajouter un signataire" onclick="myCreateFunction2()" href="#" class="addRow">
                                                                        <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                        </svg>
                                                                        </a>
                                                                    -->

                                                                    <!--
                                                                    <a title="Retirer ce signataire" onclick="removeRow2(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a>
                                                                    -->
                                                                    @else
                                                                    <!--
                                                                    <a title="Retirer ce signataire" onclick="removeRow2(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a>
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
                                                                
                                                                style="background-color: #e9ecef;" 
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

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-6" style="text-align: right"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible avant opération : </label></div>
                                    <div class="col-sm-3">
                                        
                                        <label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >
                                        
                                            
                                        @if(isset($credit_budgetaires_credit)) {{ 
                                            
                                            strrev(wordwrap(strrev(  $credit_budgetaires_credit ?? 'aucun budget'), 3, ' ', true)) ?? '' }} 
                                            
                                        @else 
                                            
                                        <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>
                                
                            </div>
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Code échéance <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editPeriode(this)" list="list_periode" <?php } ?>  autocomplete="off" type="text" id="periodes_id" name="periodes_id" class="form-control" 
                                            value="{{ old('periodes_id') ?? $travauxe->libelle_periode ?? '' }}"
                                            
                                            >

                                            <input onfocus="this.blur()" type="text" id="valeur" name="valeur" class="form-control" style="display: none" value="{{ old('valeur') ?? $travauxe->valeur ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input  onkeypress="validate(event)"  required onkeyup="editPeriode2(this)" autocomplete="off" type="text" id="delai" name="delai" class="form-control" 

                                            value="{{ old('delai') ?? $travauxe->delai ?? '' }}"
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif

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
                                            <label class="label">Fournisseur <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required autocomplete="off" type="text" name="organisations_id" id="organisations_id" class="form-control" style="background-color: #e9ecef; display:none" onfocus="this.blur()" value="{{ $travauxe->organisations_id ?? '' }}">

                                            <input <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editFr(this)" list="list_organisation" <?php } ?> autocomplete="off" type="text" name="entnum" id="entnum" class="form-control" value="{{ $travauxe->entnum ?? '' }}"
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif

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
                                            <label class="label" class="mt-1">Devise <?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editDevise(this)" list="list_devise" <?php } ?> autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control" 
                                            value="{{ $travauxe->code ?? '' }}"
                                            
                                            @if (isset($griser))  
                                                style="background-color: #e9ecef;" onfocus="this.blur()"
                                            @endif

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
                                
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Date livraison </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror" @if(isset($travauxe->date_livraison_prevue))
                                            value="{{  $travauxe->date_livraison_prevue }}"
                                            @endif 
                                            
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

                                @if(isset($edit_signataire))
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Date retrait </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_retrait" class="form-control @error('date_retrait') is-invalid @enderror" @if(isset($travauxe->date_retrait))
                                            value="{{  $travauxe->date_retrait }}"
                                            @endif >
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
                                <th style="width:50%; text-align:center">DÉSIGNATION</th>
                                <th style="width:10%; text-align:center">QTÉ<?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></th>
                                <th style="width:13%; text-align:center">PU HT<?php if ($griser===null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></th>
                                <th style="width:10%; text-align:center">REMISE (%)</th>
                                <th style="width:15%; text-align:center">MONTANT HT ht</th>

                                

                                @if(!isset($griser))
                                <th style="text-align:center; width:1%">
                                    
                                        
                                    
                                        <a onclick="myCreateFunction()" href="#" class="addRow">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                            </svg>
                                        </a>
                                    
                                
                                </th>
                                @endif
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
                                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service" 
                                        
                                        @if(isset($griser))
                                            style="border-color: transparent;background-color: transparent;" onfocus="this.blur()"
                                            @else
                                            list="list_service"
                                        @endif

                                        value="{{ $detail_travauxe->libelle ?? '' }}"
                                        >
                                    </td>
                                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)"  name="qte[]" class="form-control qte" 
                                        
                                        @if(isset($griser))
                                            style="border-color: transparent;background-color: transparent;; text-align:center" onfocus="this.blur()"
                                            @else
                                            style="text-align:center"
                                        @endif

                                        value="{{ strrev(wordwrap(strrev($detail_travauxe->qte ?? ''),3,' ',true)) }}" 

                                        >
                                    </td>
                                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required type="text" onkeyup="editMontant(this)" oninput ="validateNumber(this);"  name="prix_unit[]" class="form-control prix_unit" 
                                        
                                        @if(isset($griser))
                                            style="border-color: transparent;background-color: transparent;; text-align:right" onfocus="this.blur()"
                                            @else
                                            style="text-align:right"
                                        @endif

                                        @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif

                                        >

                                    </td>
                                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" type="text" onkeyup="editMontant(this)" oninput ="validateNumber(this);"  name="remise[]" class="form-control remise" 
                                        
                                        @if(isset($griser))
                                            style="border-color: transparent;background-color: transparent;; text-align:center" onfocus="this.blur()"
                                            @else
                                            style="text-align:center"
                                        @endif

                                        @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif
                                        
                                        >
                                    </td>
                                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                        <input autocomplete="off" required onfocus="this.blur()" oninput ="validateNumber(this);"  style="border-color: transparent;background-color: transparent; text-align:right" type="text" name="montant_ht[]" class="form-control montant_ht"

                                        @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)

                                        value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}"

                                        @else

                                        value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}"

                                        @endif

                                        >

                                        <input autocomplete="off" required onfocus="this.blur()" oninput ="validateNumber(this);"  style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis" 
                                        
                                        value="{{ $detail_travauxe->montant_ht ?? '' }}"
                                        >

                                    </td>
                                    @if(!isset($griser))
                                    <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                        
                                            
                                        
                                        <a onclick="removeRow(this)" href="#" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg></a>
                                        
                                        
                                    </td>
                                    @endif
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
                                                       
                                        <div class="pr-0"><label class="label" class="font-weight-bold mt-1 mr-1">Montant total brut</label><br>  <input oninput ="validateNumber(this);" required autocomplete="off"   onfocus="this.blur()"  style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut" 
                                        

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
                                                
                                                @if(isset($griser))
                                                    style="background-color: #e9ecef; width:110px; text-align:right;" onfocus="this.blur()"
                                                    @else
                                                    style="width:110px; text-align:right;"
                                                @endif
                                                
                                                >
                                        </div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold  mt-1 mr-1">Montant remise</label><br>  <input oninput ="validateNumber(this);"   onkeyup="editRemiseGenerale(this)"  autocomplete="off" type="text" name="remise_generale" class="form-control remise_generale" 

                                            @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                            value="{{ strrev(wordwrap(strrev(old('remise_generale') ?? $remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev(old('remise_generale') ?? $remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                            
                                            @if(isset($griser))
                                                style="background-color: #e9ecef; width:110px; text-align:right;" onfocus="this.blur()"
                                                @else
                                                style="width:110px; text-align:right;"
                                            @endif
                                            
                                            >
                                        </div>

                                        
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total net ht</label><br>  <input oninput ="validateNumber(this);" required   autocomplete="off" onfocus="this.blur()"  style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net" 
                                        

                                        @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_net') ?? $montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_net') ?? $montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif

                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">TVA (%)</label><br>  <input oninput ="validateNumber(this);" list="list_taxe_tva"   onkeyup="editRemiseGenerale(this)"  autocomplete="off" type="text" name="tva" class="form-control tva" 

                                        @if(isset($tva_partie_decimale) && $tva_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('tva') ?? $tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('tva') ?? $tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif
                                            
                                            @if(isset($griser))
                                                style="background-color: #e9ecef; width:80px; text-align:center" onfocus="this.blur()"
                                                @else
                                                style="width:80px; text-align:center"
                                            @endif
                                            
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input oninput ="validateNumber(this);" autocomplete="off" onfocus="this.blur()  " style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva" 
                                            

                                        @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('montant_tva') ?? $montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('montant_tva') ?? $montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif
                                            
                                            ></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total ttc</label><br>  <input oninput ="validateNumber(this);" required autocomplete="off"   onfocus="this.blur()"  style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc" 

                                        @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_ttc') ?? $montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('montant_total_ttc') ?? $montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif
                                            
                                            ></div>

                                        <div class="pl-1" style="margin-left:0px;"><label class="label" class="font-weight-bold mt-1 mr-2">Net à payer</label><br>  <input oninput ="validateNumber(this);" required   autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer" 
                                            

                                        @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                        value="{{ strrev(wordwrap(strrev(old('net_a_payer') ?? $net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                        @else
                                        value="{{ strrev(wordwrap(strrev(old('net_a_payer') ?? $net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                        @endif

                                            > </div>

                                </div>
                                <div class="row d-flex pl-3">

                                    <div class="pl-1" style="padding-left:3px; text-align:center" ><label class="label" class="font-weight-bold mt-1 mr-2">Acompte</label><br>  <input type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" 
                                        
                                        @if(isset($griser))
                                            disabled
                                        @endif

                                        @if(isset($travauxe->acompte))
                                            @if($travauxe->acompte === 1)
                                                checked
                                            @endif
                                        @endif
                                        >
                                    </div>

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

                                    
                                    
                                    id="acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">(%)</label><br>  <input oninput ="validateNumber(this);"  onkeyup="editTauxAcompte(this)" autocomplete="off" type="text" name="taux_acompte" class="form-control taux_acompte" 

                                    @if(isset($taux_acompte_partie_decimale) && $taux_acompte_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev(old('taux_acompte') ?? $taux_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                    @else
                                    value="{{ strrev(wordwrap(strrev(old('taux_acompte') ?? $taux_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif

                                    @if(isset($griser))
                                        style="background-color: #e9ecef;  width:70px; text-align:center;" onfocus="this.blur()"
                                        @else
                                        style=" width:70px; text-align:center;"
                                    @endif

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

                                    
                                    id="i_acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input oninput ="validateNumber(this);"   onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

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

                                    
                                    id="acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onkeyup="editAcompte(this)" onkeypress="validate(event)"  type="text" name="montant_acompte" class="form-control montant_acompte" 
                                    

                                    @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                    value="{{ strrev(wordwrap(strrev(old('montant_acompte') ?? $montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                    @else
                                    value="{{ strrev(wordwrap(strrev(old('montant_acompte') ?? $montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                    @endif

                                    @if(isset($griser))
                                        style="background-color: #e9ecef; width:110px; text-align:right" onfocus="this.blur()"
                                        @else
                                        style="width:110px; text-align:right"
                                    @endif

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

                                    <div style="padding-left: 10px;" ><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>
                                        
                                    </div>
                                
                                </div>
                            </td>
            
                        </tr>
                    </table>

                    <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                        <tr>
                            <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">
                                @if(!isset($griser)) 
                                    <a title="Joindre une nouvelle pièce" onclick="myCreateFunction3()" href="#">
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
                                        <a @if(isset($griser)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 )   }}">
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

                                            <a  title="Retirer ce fichier" onclick="removeRow3(this)" href="#">
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
                                    <a title="Retirer ce fichier" onclick="removeRow3(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
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

                                        <textarea @if(isset($griser_button)) disabled style="width: 100%; resize:none" @else style="width: 100%; resize:none" @endif class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                        @error('commentaire')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    

                                    <div class="col-md-3" style="margin-top:40px">
                                    
                                        @if(!isset($griser_button))

                                            @if(isset($value_bouton2))
                                                <button onclick="return confirm('Faut-il annuler ?')" type="submit" style="width: 80px; margin-top: -60px;" name="submit" value="{{ $value_bouton2 ?? '' }}" class="btn btn-danger">{{ $bouton2 }}</button>
                                            @endif

                                            @if(isset($value_bouton))
                                                <button onclick="return confirm('Faut-il enregistrer ?')" type="submit"  style="width: 80px; margin-top: -60px;" name="submit" value="{{ $value_bouton ?? '' }}" class="btn btn-success">{{ $bouton }}</button>
                                            @endif

                                            @if(isset($value_bouton3))
                                                <button onclick="return confirm('Faut-il réception de la commande ?')" type="submit" style="width: 80px; margin-top: -60px;" name="submit" value="{{ $value_bouton3 ?? '' }}" class="btn btn-warning">{{ $bouton3 }}</button>
                                            @endif

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

<script>

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

    function myCreateFunction2() {
        var table = document.getElementById("tableSignataire");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        if (nbre_rows < 2) {        
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            cell1.innerHTML = '<td style="width: 32%; padding-right:3px;"> <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id" style="display: none"> <input onkeyup="editAgent(this)" list="agent_list" autocomplete="off" type="text" name="mle[]" class="form-control mle"></td>';
            cell2.innerHTML = '<td> <input onfocus="this.blur()" style="background-color: #e9ecef;" autocomplete="off" type="text" name="nom_prenoms[]" class="form-control nom_prenoms"></td>';
            cell3.innerHTML = '<td style="width: 1px; white-space:nowrap"><a title="Retirer ce signataire" onclick="removeRow2(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';

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

    removeRow2 = function(el) {
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

    editAgent = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('.mle').val();

        if(value != ''){
            const block = value.split('->');
            var profil_fonctions_id = block[0];
            var mle = block[1];
            var nom_prenoms = block[2];
            
        }else{
            profil_fonctions_id = "";
            mle = "";
            nom_prenoms = "";
        }

        if (profil_fonctions_id === undefined ) {
            tr.find('.profil_fonctions_id').val('');
        }else{
            tr.find('.profil_fonctions_id').val(profil_fonctions_id);
        }

        if (nom_prenoms === undefined ) {
            tr.find('.nom_prenoms').val('');
        }else{
            tr.find('.nom_prenoms').val(nom_prenoms);
        }

        if (mle === undefined ) {
            tr.find('.mle').val(value);
        }else{
            tr.find('.mle').val(mle);
        }
    }

    function myCreateFunction3() {
      var table = document.getElementById("tablePiece");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            cell1.innerHTML = '<td><input type="file" name="piece[]" class="form-control-file" id="piece" ></td>';
            cell2.innerHTML = '<td><a title="Retirer ce fichier" onclick="removeRow3(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
            cell2.style.textAlign = "right";
        
      
    }
    
    removeRow3 = function(el) {
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
