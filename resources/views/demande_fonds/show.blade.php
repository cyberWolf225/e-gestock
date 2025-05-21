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
<div class="container">
    <br>

    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header entete-table">{{ mb_strtoupper('Aperçu de la demande de fonds') }} 
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y",strtotime($demande_fond->created_at)) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $demande_fond->exercice ?? '' }}</strong></span>                    
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
                                        <div class="form-group d-flex">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" id="demande_fonds_id" type="text" class="form-control @error('demande_fonds_id') is-invalid @enderror" name="demande_fonds_id" value="{{ old('demande_fonds_id') ?? $demande_fond->id ?? '' }}"  autocomplete="off" >

                                            <input onfocus="this.blur()" style="font-weight:bold;color: red; margin-top:-1px ;" id="num_dem" type="text" class="form-control griser @error('num_dem') is-invalid @enderror" name="num_dem" value="{{ old('num_dem') ?? $demande_fond->num_dem ?? '' }}"  autocomplete="off" >
                                            
                                            @error('num_dem')
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
                                            <label class="mt-1 label">Compte à imputer @if(!isset($griser))@endif</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input style="display:none" onfocus="this.blur()" id="credit" type="text" class="form-control @error('credit') is-invalid @enderror" name="credit" value="{{ old('credit') ?? $disponible->credit ?? $demande_fond->solde_avant_op ?? '' }}"  autocomplete="off">

                                            <input style="display:none" required onfocus="this.blur()" id="credit_budgetaires_id" type="text" class="form-control @error('credit_budgetaires_id') is-invalid @enderror" name="credit_budgetaires_id"  autocomplete="off" 
                                            
                                            @if(isset($disponible_display))
                                                value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? '' }}"
                                            @else
                                                value="{{ old('credit_budgetaires_id') ??  $demande_fond->credit_budgetaires_id ?? '' }}"
                                            @endif

                                            >


                                            <input 
                                                @if(isset($griser))
                                                    onfocus="this.blur()"
                                                    
                                                    style="background-color: #e9ecef;"
                                                    @else
                                                    list="famille" onkeyup="editCompte(this)"
                                                @endif
                                              id="ref_fam" type="text" class="form-control @error('ref_fam') is-invalid @enderror" name="ref_fam" value="{{ old('ref_fam') ?? $famille->ref_fam ?? $demande_fond->ref_fam ?? '' }}"  autocomplete="off" >
                                            
                                            @error('ref_fam')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef;; " id="design_fam" type="text" class="form-control @error('design_fam') is-invalid @enderror" name="design_fam" value="{{ old('design_fam') ?? $famille->design_fam ?? $demande_fond->design_fam ?? '' }}"  autocomplete="off" >
                                        
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
                                            <label class="mt-1 label">Structure @if(!isset($griser))@endif</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none" value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? '' }}">                                        

                                            <input
                                                @if(isset($griser))
                                                    onfocus="this.blur()"
                                                    
                                                    style="background-color: #e9ecef;"
                                                    @else
                                                    list="structure" onkeyup="editStructure(this)"
                                                @endif
                                             id="code_structure" type="text" class="form-control @error('code_structure') is-invalid @enderror" name="code_structure" value="{{ old('code_structure') ??  $structure->code_structure ?? $demande_fond->code_structure ?? '' }}"  autocomplete="off" >
                                            
                                            @error('code_section')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; " id="nom_structure" type="text" class="form-control @error('nom_structure') is-invalid @enderror" name="nom_structure" value="{{ old('nom_structure') ??  $structure->nom_structure ?? $demande_fond->nom_structure ?? '' }}"  autocomplete="off" >
                                        
                                            @error('nom_section')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-12">
                                        <div class="form-group" style="text-align: right">
                                            <input class="form-control" style="visibility: hidden; background-color:#e9ecef">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Gestion @if(!isset($griser))@endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion->code_gestion ?? $demande_fond->code_gestion ?? '' }}" 
                                            
                                            @if(isset($griser))
                                                @if(!isset($edition_pilote))
                                                onfocus="this.blur()"
                                                
                                                style="background-color: #e9ecef"
                                                @else
                                                list="gestion" onkeyup="editGestion(this)" 
                                                @endif
                                            @endif

                                            >

                                            @error('code_gestion')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control @error('libelle_gestion') is-invalid @enderror libelle_gestion" style="background-color: #e9ecef" value="{{ old('libelle_gestion') ?? $gestion->libelle_gestion ?? $demande_fond->libelle_gestion ?? '' }}">

                                            @error('libelle_gestion')
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
                                            <label class="mt-1 label">Section @if($disponible != null && !isset($griser))  @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input
                                            
                                                @if(isset($griser))
                                                    onfocus="this.blur()"
                                                    
                                                    style="background-color: #e9ecef;"
                                                    @else

                                                    @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef" @else onkeyup="editSection(this)" list="section" @endif
                                                    @if(isset($sections))

                                                        @if(count($sections) === 0)
                                                            onfocus="this.blur()" style="background-color: #e9ecef"
                                                        @endif
                                                        
                                                    @endif

                                                @endif

                                            
                                            required  autocomplete="off" type="text" name="code_section" id="code_section" class="form-control @error('code_section') is-invalid @enderror code_section" value="{{ old('code_section') ?? $demande_fond->code_section ?? '' }}">
                                        </div>
                                        @error('code_section')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_section" id="nom_section" class="form-control griser @error('nom_section') is-invalid @enderror nom_section"

                                            @if(isset($sections))

                                                @if(count($sections) === 0)

                                                    value="{{ 'Aucune section trouvée. Veuillez contacter votre administrateur' }}"

                                                @else

                                                    value="{{ old('nom_section') ?? $demande_fond->nom_section ?? '' }}"

                                                @endif

                                                @else
                                                value="{{ old('nom_section') ?? $demande_fond->nom_section ?? '' }}"
                                            @endif
                                            >
                                        </div>
                                        @error('nom_section')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex">
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Bénéficiaire @if($disponible != null && !isset($griser))  @endif</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <?php 
                                            
                                                $mle_bene = null; 
                                                if(isset($demande_fond->mle)){

                                                    $agents_id_beneficiaire = null;

                                                    $agent_beneficiaire = DB::table('agents as a')
                                                    ->where('a.mle',$demande_fond->mle)
                                                    ->first();

                                                    if($agent_beneficiaire != null){
                                                        $agents_id_beneficiaire = $agent_beneficiaire->id;
                                                    }
                                                    
                                                    $explodes = explode('frs',$demande_fond->mle); 

                                                    if(isset($explodes[1])){
                                                        $mle_bene = $explodes[1];
                                                    }else{
                                                        $mle_bene = $demande_fond->mle;
                                                    }
                                                    
                                                } 
                                            ?>
                                            <input style="display:none" onfocus="this.blur()" required id="agents_id_beneficiaire" type="text" class="form-control @error('agents_id_beneficiaire') is-invalid @enderror" name="agents_id_beneficiaire" value="{{ old('agents_id_beneficiaire') ?? $agents_id_beneficiaire ?? '' }}"  autocomplete="off" >

                                            <input id="mle" type="text" class="form-control @error('mle') is-invalid @enderror" name="mle" value="{{ old('mle') ?? $mle_bene ?? '' }}"  autocomplete="off" 
                                            
                                            
                                                @if(isset($griser))
                                                    @if(!isset($edition_pilote))
                                                    onfocus="this.blur()"
                                                    
                                                    style="background-color: #e9ecef"
                                                    @else
                                                    list="beneficiaire" onkeyup="editBeneficiaire(this)"
                                                    @endif
                                                @endif
                                                >
                                            
                                            @error('mle')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; ; " id="nom_prenoms" type="text" class="form-control @error('nom_prenoms') is-invalid @enderror" name="nom_prenoms" value="{{ old('nom_prenoms') ?? $demande_fond->nom_prenoms ?? '' }}"  autocomplete="off" >
                                        
                                            @error('nom_prenoms')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @if(isset($signataires))
                                    <?php $nbr_signataire = count($signataires); $compteur_signateur = 1;  ?>
                                    @foreach($signataires as $signataire)
                                        <?php $classement = null; if($compteur_signateur === 1){ $classement = 'er';}else{ $classement = 'eme';} ?>
                                        <div class="row d-flex" style="margin-top: -10px;">
                                            <div class="col-sm-3">
                                                <div class="form-group" style="text-align: right">
                                                    <label class="label" class="mt-1 "><?php if($nbr_signataire > 1){ if(isset($classement)){echo $compteur_signateur . '<sup>' . $classement . '</sup>'; } } ?> Signataire</label> 
                                                </div>
                                            </div>
                                            <div class="col-md-3 pr-1">
                                                <div class="form-group d-flex ">
                                                    <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="mle_signataire_dg" class="form-control griser" value="{{ 'M'.$signataire->mle ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 pl-0">
                                                <div class="form-group d-flex ">
                                                    <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_prenoms_signataire_dg" class="form-control griser" 
                                                    value="{{ $signataire->nom_prenoms ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <?php $compteur_signateur++; ?>
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Chargé du suivi @if($disponible != null && !isset($griser))  @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <?php 
                                            
                                                $mle_suivi = null; 
                                                if(isset($charge_suivi_save->mle)){
                                                    
                                                    $explodes = explode('frs',$charge_suivi_save->mle); 

                                                    if(isset($explodes[1])){
                                                        $mle_suivi = $explodes[1];
                                                    }else{
                                                        $mle_suivi = $charge_suivi_save->mle;
                                                    }
                                                    
                                                } 
                                            ?>
                                            
                                            <input style="display:none" onfocus="this.blur()" required id="agents_id" type="text" class="form-control @error('agents_id') is-invalid @enderror" name="agents_id" value="{{ old('agents_id') ?? $charge_suivi_save->id ?? '' }}"  autocomplete="off" >

                                            <input required id="mle_charge" type="text" class="form-control @error('mle_charge') is-invalid @enderror" name="mle_charge" value="{{ old('mle_charge') ?? $mle_suivi ?? '' }}"  autocomplete="off" 
                                            
                                                @if(isset($griser))
                                                    @if(!isset($edition_pilote))
                                                    onfocus="this.blur()"
                                                    
                                                    style="background-color: #e9ecef"
                                                    @else
                                                    list="beneficiaire" onkeyup="editChargeSuivi(this)"
                                                    @endif
                                                @endif

                                            >
                                            
                                            @error('mle_charge')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef; " id="nom_prenoms_charge" type="text" class="form-control @error('nom_prenoms_charge') is-invalid @enderror" name="nom_prenoms_charge" value="{{ old('nom_prenoms_charge') ?? $charge_suivi_save->nom_prenoms ?? '' }}"  autocomplete="off" >
                                        
                                            @error('nom_prenoms_charge')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="intitule">Objet demande @if(!isset($griser))  @endif</label>
                                    </div>
                                    <div class="col-md-10">
                                        <textarea 
                                            @if(isset($griser))
                                                onfocus="this.blur()"
                                                
                                                style="background-color: #e9ecef; resize:none"
                                                @else
                                                style="resize:none"
                                            @endif
                                        rows="2" id="intitule" type="text" class="form-control @error('intitule') is-invalid @enderror" name="intitule">{{ old('intitule') ?? $demande_fond->intitule ?? '' }}</textarea>
                                        @error('intitule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
        
                                </div>  
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="intitule">Solde avant opération</label>
                                    </div>
                                    <div class="col-md-2">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; " id="solde_avant_op" type="text" class="form-control @error('solde_avant_op') is-invalid @enderror" name="solde_avant_op" value="{{ strrev(wordwrap(strrev($demande_fond->solde_avant_op ?? ''),3,' ',true)) }}"  autocomplete="off" >
                                    </div>
                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="observation">Observation</label>
                                    </div>
                                    <div class="col-md-6">
                                        <textarea 
                                        
                                            @if(isset($griser))
                                                onfocus="this.blur()"
                                                style="background-color: #e9ecef; resize:none"
                                                @else
                                                style="resize:none"
                                            @endif
                                        
                                        rows="2" id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation">{{ old('observation') ?? $demande_fond->observation ?? '' }}</textarea>
                                        @error('observation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
        
                                </div>  
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="montant">Montant @if(!isset($griser))  @endif</label>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <input 
                                        
                                            @if(isset($griser))
                                                onfocus="this.blur()"
                                                
                                                style="background-color: #e9ecef; text-align: right; "
                                            @endif
                                        
                                        onkeypress="validate(event)" onkeyup="editMontant(this)" id="montant" type="text" class="form-control @error('montant') is-invalid @enderror" name="montant" value="{{ strrev(wordwrap(strrev(old('montant') ?? $demande_fond->montant ?? ''),3,' ',true)) }}"  autocomplete="off" style="text-align: right; ">
                                        
                                        @error('montant')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    @if(isset($value_bouton) or isset($demande_fond->moyen_paiements_id)) 
                                        @if($value_bouton === 'edition_g_achat' or $value_bouton === 'transmettre_g_achat' or isset($demande_fond->moyen_paiements_id)) 
                                            <div class="col-md-2" style="text-align: right">
                                                <label class="label" for="ref_fam">Moyen de paiement @if(!isset($griser))  @endif</label>
                                            </div>
                                            <div class="col-md-2">
                                                
                                                <?php 
                                                    $moyen_paiments = DB::table('moyen_paiements')
                                                    ->get();
                                                    $index = 1;
                                                ?>
                                                @foreach($moyen_paiments as $moyen_paiment )
                                                
                                                    <label class="label" 
                                                    
                                                    style="font-weight: normal; cursor:pointer; @if($index !=1) margin-left: 20px; @endif " 
                                                    for="{{ 'moyen_paiment'.$moyen_paiment->id ?? '' }}">

                                                    {{ $moyen_paiment->libelle ?? '' }}

                                                    </label>

                                                    <input style="margin-left: 10px;" onclick="handleClick(this);" id="{{ 'moyen_paiment'.$moyen_paiment->id ?? '' }}" type="radio" name="moyen_paiment" value="{{ $moyen_paiment->libelle ?? '' }}" class="@error('moyen_paiment') is-invalid @enderror" 
                                                    
                                                    {{ ( $moyen_paiment->libelle === $libelle_moyen_paiement) ? 'checked' : '' }}

                                                    @if(!isset($demande_fond->moyen_paiements_id))
                                                        @if(isset($moyen_paiment->libelle))
                                                            @if($moyen_paiment->libelle === 'Chèque')
                                                                @if(isset($num_bc))
                                                                    checked
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                    
                                                    

                                                    @if(isset($griser))
                                                        @if($griser === 1)
                                                            disabled
                                                        @endif
                                                    @endif
                                                    >
                                                    <?php $index++; ?>
                                                @endforeach
                                            
                                                
                                                @error('moyen_paiment')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div id="label_bc" class="col-md-1" style="text-align: right; 
                                                @if(!isset($num_bc))
                                                    @if(old('moyen_paiment') === 'Chèque') 
                                                        @else
                                                        display:none
                                                    @endif 
                                                @endif


                                            ">
                                                <label class="label" for="ref_fam">N° Bon cde. @if(!isset($griser))  @endif</label>
                                            </div>
                                            <div id="input_bc" class="col-md-3" style="text-align: right; display:flex; 
                                                @if(!isset($num_bc))
                                                    @if(old('moyen_paiment') === 'Chèque') 
                                                        @else
                                                        display:none
                                                    @endif  
                                                @endif
                                                ">
                                                <input 
                                                

                                                @if(isset($griser))
                                                    onfocus="this.blur()"
                                                    
                                                    style="background-color: #e9ecef; @if(!isset($num_bc)) @if(isset($nbre_bc)) @if($nbre_bc === 0) background-color:#f4c1c1; @endif @endif @endif 

                                                    @if(isset($type_profils_name))
                                                        @if($type_profils_name === 'Gestionnaire des achats')
                                                    width:57% ;
                                                    @else
                                                    width:63% ;
                                                    @endif
                                                    @else
                                                    width:63% ;
                                                    @endif
                                                    
                                                    @if(isset($num_bc)) 
                                                        color: red; 
                                                        font-weight:bold;
                                                        @else 
                                                        color: grey; 
                                                    @endif"
                                                    @else
                                                    list="list_num_bc"
                                                    style="

                                                        @if(!isset($num_bc)) @if(isset($nbre_bc)) @if($nbre_bc === 0) background-color:#f4c1c1; @endif @endif @endif 

                                                        @if(isset($type_profils_name))
                                                            @if($type_profils_name === 'Gestionnaire des achats')
                                                        width:57% ;
                                                        @else
                                                        width:63% ;
                                                        @endif
                                                        @else
                                                        width:63% ;
                                                        @endif
                                                        
                                                        @if(isset($num_bc)) 
                                                            color: red; 
                                                            font-weight:bold;
                                                            @else 
                                                            color: grey; 
                                                        @endif
                                                        
                                                        
                                                        "
                                                @endif

                                                @if(!isset($num_bc)) @if(isset($nbre_bc)) @if($nbre_bc === 0) onfocus="this.blur()" title="Veuillez créer une demande d'achat ou un bon de commande non stockable avant d'effectuer cette opération " @endif @endif @endif  
                                                
                                                    id="num_bc" type="text" class="form-control @error('num_bc') is-invalid @enderror" name="num_bc" value="{{ $num_bc ?? $num_bc_desactiver }}"  autocomplete="off" >

                                                @if(isset($num_bc_desactiver))
                                                    <input type="checkbox" name="flag_actif" title="Activer le bon de commande rattaché" style="margin-left: 10px; margin-top:5px;">
                                                @endif
                                                
                                                

                                                @if(isset($num_bc) && isset($route))
                                                
                                                    <a id="button_bc"

                                                    @if(isset($num_bc) && isset($route))
                                                        onclick="document.location='/{{ $route ?? '' }}'"
                                                    @endif
                                                    style="border-color:transparent; margin-left: 10px; background-color:transparent; cursor:pointer; margin-top:5px;                                                   
                                                    ">
                                                    
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                                                            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                                                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/>
                                                            <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                @error('num_bc')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        @endif
                                    @endif
        
                                </div>  
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <hr>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
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
                                             
                                            <input @if(isset($griser)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                            @if(isset($piece_jointe->flag_actif))
                                                @if($piece_jointe->flag_actif == 1)
                                                    checked
                                                @endif
                                            @endif >
                                            

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
                        </table>
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
