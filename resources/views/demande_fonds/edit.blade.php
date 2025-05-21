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
    <datalist id="agent_list">
        @foreach($agents as $agent)
            <?php
                $agen = DB::table('agents as a')
                    ->join('users as u','u.agents_id','=','a.id')
                    ->join('profils as p','p.users_id','=','u.id')
                    ->join('type_profils as tp','tp.id','=','u.id')
                    ->join('profil_fonctions as pf','pf.agents_id','=','u.agents_id')
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
                        <option value="{{ $profil_fonctions_id ?? '' }}->{{ 'M'.$agent->mle ?? '' }}->{{ $agent->nom_prenoms ?? '' }}->{{ $agent->libelle ?? '' }}">{{ $agent->nom_prenoms ?? '' }}</option>
                    <?php
                }
            ?>
            
        @endforeach
    </datalist>

    <datalist id="famille">
        @foreach($familles as $famille_list)
            <option value="{{ $famille_list->ref_fam}}->{{ $famille_list->design_fam }}">{{ $famille_list->design_fam }}</option>
        @endforeach
    </datalist>

    <datalist id="list_num_bc">
        @foreach($travauxes as $travauxe_list)
            <option>{{ $travauxe_list->num_bc ?? '' }}</option>
        @endforeach

        @foreach($demande_achats as $demande_achat_list)
            <option>{{ $demande_achat_list->num_bc ?? '' }}</option>
        @endforeach
    </datalist>

    <datalist id="beneficiaire">
        @foreach($charge_suivis as $charge_suivi)

            <?php $mle_datalist = null;  $explodes = explode('frs',$charge_suivi->mle); 
            if(isset($explodes[1])){
                $mle_datalist = $explodes[1];
            }else{
                $mle_datalist = $charge_suivi->mle;
            } ?>
            <option value="{{ $mle_datalist }}->{{ $charge_suivi->nom_prenoms }}->{{ $charge_suivi->agents_id }}">{{ $charge_suivi->nom_prenoms }}</option>
            <?php ?>
        @endforeach
    </datalist>

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

    @if(isset($sections))
        <datalist id="section">
            @foreach($sections as $section_list)
                <option value="{{ $section_list->code_section}}->{{ $section_list->nom_section }}">{{ $section_list->nom_section }}</option>
            @endforeach
        </datalist>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            

          <?php
                //entete
                if (Request::is('demande_fonds/send/'. $demande_fond->id ?? 0 )) {

                    $entete = 'DEMANDE DE FONDS';
                    $value_bouton = null;
                    $bouton = null;

                    if (isset($libelle)) {
                        if ($libelle === 'Imputé (Gestionnaire des achats)' or $libelle === 'Édité (Gestionnaire des achats)') {
                            if ($type_profils_name === 'Gestionnaire des achats') {
                                
                                $entete = 'TRANSMISSION DE LA DEMANDE DE FONDS AU RESPONSABLE DES ACHATS';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_g_achat';
                                $griser = 1;
                            }
                        }elseif ($libelle === 'Transmis (Responsable des achats)' or $libelle === 'Visé (Responsable des achats)') {

                            if (isset($profil_responsable_achat)) {
                                $entete = 'TRANSMISSION DE LA DEMANDE DE FONDS AU Responsable DMP';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_r_achat';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Retransmis (Responsable DMP)' or $libelle === 'Signé (Responsable DMP)') {

                            if (isset($profil_responsable_cmp)) {
                                $entete = 'TRANSMISSION DU DOSSIER À LA DIRECTION EN CHARGE DU CONTRÔLE DE GESTION';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_dcg';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Transmis (Responsable Contrôle Budgétaire)' or $libelle === 'Visé (Responsable Contrôle Budgétaire)') {

                            if (isset($profil_responsable_cb)) {
                                $entete = 'TRANSMISSION DU DOSSIER AU CHEF DU DÉPARTEMENT CONTRÔLE DE GESTION';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_cb';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Transmis (Chef Département Contrôle Budgétaire)' or $libelle === 'Visé (Chef Département Contrôle Budgétaire)') {

                            if (isset($profil_departement_dcg)) {
                                $entete = 'TRANSMISSION DU DOSSIER AU DIRECTEUR DE LA DCG';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_r_dcg';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Transmis (Responsable DCG)' or $libelle === 'Signé (Responsable DCG)') {

                            if (isset($profil_responsable_dcg)) {
                                if (isset($demande_fond->montant)) {
                                    if ($demande_fond->montant <= 250000) {
                                        $entete = 'TRANSMISSION DU DOSSIER À LA DFC';
                                    }else{
                                        $entete = 'TRANSMISSION DU DOSSIER AU DGAAF';
                                    }

                                    $bouton = 'Transmettre';
                                    $value_bouton = 'transmettre_r_dfc_dgaaf';
                                    $griser = 1; 
                                }
                                
                                
                            }
                        
                        }elseif ($libelle === 'Transmis (Directeur Général Adjoint)' or $libelle === 'Visé (Directeur Général Adjoint)') {

                            if (isset($profil_responsable_dgaaf)) {
                                $entete = 'TRANSMISSION DU DOSSIER À LA DIRECTION GÉNÉRALE';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_d_dgaaf';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Transmis (Directeur Général)' or $libelle === 'Visé (Directeur Général)') {

                            if (isset($profil_responsable_dg)) {
                                $entete = 'TRANSMISSION DU DOSSIER À LA DFC';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_d_dg';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Transmis (Responsable DFC)' or $libelle === 'Visé (Responsable DFC)') {

                            if (isset($profil_responsable_dfc)) {
                                $entete = 'TRANSMISSION DU DOSSIER AUX COMPTABLES ET À LA CMP';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_d_dfc';
                                $griser = 1; 
                            }
                        }elseif ($libelle === 'Transmis (Gestionnaire des achats)' or $libelle === 'Confirmé (Gestionnaire des achats)' or $libelle === 'Annulé (Gestionnaire des achats)') {

                            if (isset($profil_gestionnaire_achat)) {
                                $entete = 'TRANSMISSION DU DOSSIER AU RESPONSABLES DES ACHATS';
                                $bouton = 'Transmettre';
                                $value_bouton = 'transmettre_r_achat2';
                                $griser = 1; 
                            }
                        }

                    }

                } 
          ?>
            <div class="card">
                <div class="card-header entete-table">{{ mb_strtoupper($entete ?? '') }} 
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
                    <form enctype="multipart/form-data" method="POST" action="{{ route('demande_fonds.update') }}">
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
                                            <label class="mt-1 label">Compte à imputer @if(!isset($griser))<sup style="color: red">*</sup>@endif</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input style="display:none" onfocus="this.blur()" id="credit" type="text" class="form-control @error('credit') is-invalid @enderror" name="credit" value="{{ old('credit') ?? $credit_budgetaires_credit ?? $demande_fond->solde_avant_op ?? '' }}"  autocomplete="off">

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
                                            <label class="mt-1 label">Structure @if(!isset($griser))<span style="color: red"><sup> *</sup></span>@endif</label> 
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
                                            <label class="label">Gestion @if(!isset($griser))<span style="color: red"><sup> *</sup></span>@endif </label> 
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
                                            <label class="mt-1 label">Section @if($disponible != null && !isset($griser)) <span style="color: red"><sup> *</sup></span> @endif </label> 
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
                                            <label class="mt-1 label">Bénéficiaire @if($disponible != null && !isset($griser)) <span style="color: red"><sup> *</sup></span> @endif</label> 
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
                                
                                @if(isset($edit_signataire))
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">

                                                <label class="label" class="mt-1 ">Signataire 
                                                    <!--<sup style="color: red">*</sup>-->
                                                </label> 

                                            </div>
                                        </div>
                                        <div class="col-md-9 pr-1">
                                            <div class="form-group d-flex ">
                                                <table id="tableSignataire" width="100%">
                                                    <?php $o = 1; ?>
                                                    @if(count($signataires) > 0)
                                                        @foreach($signataires as $signataire)

                                                        <tr>

                                                            <td style="width: 32%; padding-right:2px;">
                                                                    <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                    style="display:none"
                                                                    required
                                                                    value="{{ $signataire->profil_fonctions_id ?? '' }}"

                                                                    @if(isset($type_profils_name))
                                                                    @if($type_profils_name != 'Gestionnaire des achats')
                                                                        disabled
                                                                    @endif
                                                                    @endif
                                                                    >
                                                                        <!-- onkeyup="editAgent(this)"
                                                                        list="agent_list" -->
                                                                    <input

                                                                    onfocus="this.blur()" 
                                                                
                                                                    style="background-color:#e9ecef;"

                                                                    autocomplete="off" type="text" name="mle_signataire[]" class="form-control mle_signataire"
                                                                    required
                                                                    value="{{ $signataire->mle ?? '' }}"

                                                                    @if(!isset($edit_signataire))
                                                                        disabled
                                                                    @endif

                                                                    @if(isset($type_profils_name))
                                                                    @if($type_profils_name != 'Gestionnaire des achats')
                                                                        disabled
                                                                    @endif
                                                                    @endif

                                                                    >
    
                                                            </td>
    
                                                            <td>
                                                                
                                                                <input onfocus="this.blur()" 
                                                                
                                                                style="background-color:#e9ecef;" 
                                                                autocomplete="off" type="text" name="nom_prenoms_signataire[]" class="form-control nom_prenoms_signataire" 
                                                                required
                                                                value="{{ $signataire->nom_prenoms ?? '' }}"

                                                                @if(isset($type_profils_name))
                                                                    @if($type_profils_name != 'Gestionnaire des achats')
                                                                        disabled
                                                                    @endif
                                                                    @endif
                                                                >
    
                                                            </td>
    
                                                            <td style="width: 1px; white-space:nowrap">
                                                                @if(isset($type_profils_name))
                                                                    @if($type_profils_name === 'Gestionnaire des achats')
                                                                    
                                                                @if(isset($edit_signataire))
                                                                    @if($o === 1)
                                                                    <!--
                                                                    <a title="Ajouter un signataire" onclick="myCreateFunction()" href="#" class="addRow">
                                                                        <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                        </svg>
                                                                    </a> -->
                                                                    @else
                                                                    <!--
                                                                    <a title="Retirer ce signataire" onclick="removeRow3(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a>-->

                                                                    @endif
                                                                @endif
                                                                @endif
                                                                @endif
                                                                
                                                        
                                                            </td>
    
                                                        </tr>
                                                        <?php $o++; ?>
                                                        @endforeach
                                                    @else
                                                        <tr>

                                                            <td style="width: 32%; padding-right:2px;">
                                                                    <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                    style="display:none"
                                                                    required
                                                                    >
                    
                                                                    <input
                                                                    onkeyup="editAgent(this)"
                                                                    list="agent_list" autocomplete="off" type="text" name="mle_signataire[]" class="form-control mle_signataire"
                                                                    required
                                                                    >
    
                                                            </td>
    
                                                            <td>
                                                                
                                                                <input onfocus="this.blur()" 
                                                                
                                                                style="background-color:#e9ecef;" 
                                                                autocomplete="off" type="text" name="nom_prenoms_signataire[]" class="form-control nom_prenoms_signataire" 
                                                                required
                                                                >
    
                                                            </td>
    
                                                            <td style="width: 1px; white-space:nowrap">
                                                                <!--
                                                                <a title="Ajouter un signataire" onclick="myCreateFunction()" href="#" class="addRow">
                                                                    <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                    </svg>
                                                                </a>
                                                                -->
                                                        
                                                            </td>
    
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-6" style="text-align: right">

                                        <input style="display:none" onfocus="this.blur()" class="form-control" name="credit" id="credit" value="{{ $credit_budgetaires_credit ?? '' }}">
                                        
                                        <label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible avant opération : </label>
                                    
                                    </div>
                                    <div class="col-sm-3"><label class="label" @if(isset($credit_budgetaires_credit)) @if($credit_budgetaires_credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($credit_budgetaires_credit)) {{ strrev(wordwrap(strrev($credit_budgetaires_credit ?? ''), 3, ' ', true)) ?? '' }} @else <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Chargé du suivi @if($disponible != null && !isset($griser)) <span style="color: red"><sup> *</sup></span> @endif </label> 
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
                                        <label class="label" for="intitule">Objet demande @if(!isset($griser)) <sup style="color: red">*</sup> @endif</label>
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
                                        <label class="label" for="montant">Montant @if(!isset($griser)) <sup style="color: red">*</sup> @endif</label>
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

                                    <div class="col-md-1" style="text-align: right">
                                        <label class="label"></label>
                                    </div>
                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="observation">Observation</label>
                                    </div>
                                    <div class="col-md-5">
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

                            @if(isset($value_bouton) or isset($demande_fond->moyen_paiements_id)) 
                                @if($value_bouton === 'edition_g_achat' or $value_bouton === 'transmettre_g_achat' or isset($demande_fond->moyen_paiements_id)) 
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <div class="col-md-2" style="text-align: right">
                                                <label class="label" for="ref_fam">Moyen de paiement @if(!isset($griser)) <sup style="color: red">*</sup> @endif</label>
                                            </div>
                                            <div class="col-md-3">
                                                
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
                                            <div id="label_bc" class="col-md-2" style="text-align: right; 
                                                @if(!isset($num_bc))
                                                    @if(old('moyen_paiment') === 'Chèque') 
                                                        @else
                                                        display:none
                                                    @endif 
                                                @endif


                                            ">
                                                <label class="label" for="ref_fam">N° Bon cde. @if(!isset($griser)) <sup style="color: red">*</sup> @endif</label>
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

                
                                        </div>  
                                    </div>
                                @endif
                            @endif
                            
                            <div class="col-md-12">
                                <div class="form-group row">
                                    @if(isset($affiche_info))
                                        @if($affiche_info === 1)

                                            <svg style="color:orange" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-octagon" viewBox="0 0 16 16">
                                            <path d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353L4.54.146zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1H5.1z"/>
                                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                            </svg>
                                            <span style="color:dimgray; font-weight:bold"><i> &nbsp;&nbsp;{{ $information_sur_bon_de_commande_rattache ?? '' }}</i></span>
                                            

                                        @endif
                                    @endif
                                </div>

                                <div class="form-group row">
                                    <hr>
                                </div>
                            </div>
                        </div>
                        <hr/>
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

                                    @if(isset($edition_charge_suivi)) 
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

                                            @if(isset($edition_charge_suivi))
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
                                    
                                        <div class="col-md-8">

                                            <textarea title="@if(isset($commentateur)) {{ $commentateur ?? '' }} @endif" placeholder="Écrivez votre commentaire ici" rows="1" id="commentaire" type="text" class="form-control @error('commentaire') is-invalid @enderror" name="commentaire" style="width: 100%; margin-left:15px; margin-right:5px; resize:none; 
                                            
                                            @if(isset($active_commentaire)) 
                                                @if($active_commentaire === 0)
                                                background-color: #e9ecef; 
                                                @endif 
                                            @endif 
                                            
                                            @if(isset($verou_commentaire)) 
                                            @if(!isset($edition_pilote)) 
                                            background-color: #e9ecef; 
                                            @endif
                                            @endif
                                            " 

                                            @if(isset($active_commentaire)) 
                                                @if($active_commentaire === 0)
                                                onfocus="this.blur()" 
                                                @endif 
                                            @endif

                                            @if(isset($verou_commentaire)) 
                                            @if(!isset($edition_pilote)) 
                                            disabled 
                                            @endif  
                                            @endif 

                                            >{{ old('commentaire') ?? '' }}</textarea>

                                            @error('commentaire')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        

                                        <div class="col-md-4" style="margin-top:0px">
                                        
                                            @if(isset($value_bouton))
                                    
                                                <button onclick="return confirm('Faut-il enregistrer ?')" style="width:80px" type="submit" class="btn btn-success" name="submit" value="{{ $value_bouton ?? '' }}">
                                                    {{ $bouton ?? '' }}
                                                </button>

                                            @endif
                                            @if(isset($value_bouton2))
                                                
                                                <button onclick="return confirm('Faut-il enregistrer ?')" type="submit" class="btn btn-danger" name="submit" style="margin-left: 10px; width:80px" value="{{ $value_bouton2 ?? '' }}">
                                                    {{ $bouton2 ?? '' }}
                                                </button>

                                            @endif
                                            @if(isset($value_bouton3))
                                                
                                                <button onclick="return confirm('Faut-il enregistrer ?')" type="submit" class="btn btn-warning" name="submit" style="margin-left: 10px; width:80px" value="{{ $value_bouton3 ?? '' }}">
                                                    {{ $bouton3 ?? '' }}
                                                </button>

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

    editSection = function(a){

        const saisie=document.getElementById('code_section').value;


        if(saisie != ''){

            const block = saisie.split('->');
            var code_section = block[0];
            var nom_section = block[1];
            
        }else{
            code_section = "";
            nom_section = "";
        }

        document.getElementById('code_section').value = code_section;

        if (nom_section === undefined) {

            document.getElementById('nom_section').value = "";

        }else{

            document.getElementById('nom_section').value = nom_section;            

        }

    }

    editMontant = function(e){

        var montant = document.getElementById('montant').value;
        montant = montant.trim();
        montant = montant.replace(' ','');
        montant = reverseFormatNumber(montant,'fr');
        montant = montant.replace(' ','');
        montant = montant * 1;

        var solde_avant_operation = document.getElementById('credit').value;
        solde_avant_operation = reverseFormatNumber(solde_avant_operation,'fr');

        if (solde_avant_operation != '') {
            if (solde_avant_operation > 0) {
                if (solde_avant_operation < montant) {
                    Swal.fire({
                    title: '<strong>e-GESTOCK</strong>',
                    icon: 'error',
                    html: 'Le montant de l\'opération ne peut être supérieur au solde disponible avant l\'opération',
                    focusConfirm: false,
                    confirmButtonText:
                        'Compris'
                    });

                    montant = 0;
                }
            }else{
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Solde avant opération insuffisant',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });

                montant = 0;
            }
        }else{
            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Solde avant opération insuffisant',
            focusConfirm: false,
            confirmButtonText:
                'Compris'
            });

            montant = 0;
        }



        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        document.getElementById("montant").value = int3.format(montant);

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

    var currentValue = 0;
    function handleClick(myRadio) {
        currentValue = myRadio.value;

        if(currentValue === "Chèque" || currentValue === "Espèce"){

            if(currentValue === "Chèque"){

                var label_bc = document.getElementById("label_bc");
                label_bc.style.display = "";

                var input_bc = document.getElementById("input_bc");
                input_bc.style.display = "flex";

                var button_bc = document.getElementById("button_bc");
                button_bc.style.display = "";



            }else if(currentValue === "Espèce"){

                var label_bc = document.getElementById("label_bc");
                label_bc.style.display = "none";

                var input_bc = document.getElementById("input_bc");
                input_bc.style.display = "none";

                var button_bc = document.getElementById("button_bc");
                button_bc.style.display = "none";

            }


            }else{

                var label_bc = document.getElementById("label_bc");
                label_bc.style.display = "none";

                var input_bc = document.getElementById("input_bc");
                input_bc.style.display = "none";

                var button_bc = document.getElementById("button_bc");
                button_bc.style.display = "none";

            }

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

    editBeneficiaire = function(a){

        const saisie=document.getElementById('mle').value;
        if(saisie != ''){

            const block = saisie.split('->');
            var mle = block[0];
            var nom_prenoms = block[1];
            var agents_id_beneficiaire = block[2];
            
        }else{

            mle = "";
            nom_prenoms = "";
            agents_id_beneficiaire = "";
            
        }

        document.getElementById('mle').value = mle;

        if (nom_prenoms === undefined) {

            document.getElementById('nom_prenoms').value = "";

        }else{

            document.getElementById('nom_prenoms').value = nom_prenoms;
            document.getElementById('agents_id_beneficiaire').value = agents_id_beneficiaire;

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

    editAgent = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('.mle_signataire').val();

        if(value != ''){
            const block = value.split('->');
            var profil_fonctions_id = block[0];
            var mle_signataire = block[1];
            var nom_prenoms_signataire = block[2];
            
        }else{
            profil_fonctions_id = "";
            mle_signataire = "";
            nom_prenoms_signataire = "";
        }
        
        if (profil_fonctions_id === undefined ) {
            tr.find('.profil_fonctions_id').val('');
        }else{
            tr.find('.profil_fonctions_id').val(profil_fonctions_id);
        }

        if (nom_prenoms_signataire === undefined ) {
            tr.find('.nom_prenoms_signataire').val('');
        }else{
            tr.find('.nom_prenoms_signataire').val(nom_prenoms_signataire);
        }

        if (mle_signataire === undefined ) {
            tr.find('.mle_signataire').val(value);
        }else{
            tr.find('.mle_signataire').val(mle_signataire);
        }
        
    }

    function myCreateFunction() {
        var table = document.getElementById("tableSignataire");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        cell1.innerHTML = '<td style="width: 32%; padding-right:3px;"> <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id" style="display:none"> <input onkeyup="editAgent(this)" list="agent_list" autocomplete="off" type="text" name="mle_signataire[]" class="form-control mle_signataire"></td>';
        cell2.innerHTML = '<td> <input onfocus="this.blur()" style="background-color:#e9ecef;" autocomplete="off" type="text" name="nom_prenoms_signataire[]" class="form-control nom_prenoms_signataire"></td>';
        cell3.innerHTML = '<td style="width: 1px; white-space:nowrap"><a title="Retirer ce signataire" onclick="removeRow3(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
      
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
