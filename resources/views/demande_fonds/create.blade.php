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

    <datalist id="famille">
        @foreach($familles as $famille_list)
            <option value="{{ $famille_list->ref_fam}}->{{ $famille_list->design_fam }}">{{ $famille_list->design_fam }}</option>
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
            
            <div class="card">
                <div class="card-header entete-table">{{ __(strtoupper('Enregistrement de demande de fonds')) }}
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y") }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $exercices->exercice ?? '' }}</strong></span>
                    
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

                    

                    <form enctype="multipart/form-data" method="POST" action="{{ route('demande_fonds.store') }}">
                        @csrf  

                        <div class="row d-flex">
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Compte à imputer <sup style="color: red">*</sup></label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input style="display: none" required autocomplete="off" type="text" name="id" id="id" value="{{ old('id') ?? $credit_budgetaires_select->id ?? ''}}"   class="form-control @error('id') is-invalid @enderror id" >

                                            <input required onkeyup="editCompte(this)"  list="famille" autocomplete="off" type="text" name="ref_fam" id="ref_fam" value="{{ old('ref_fam') ?? $credit_budgetaires_select->ref_fam ?? '' }}"  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" value="{{ old('design_fam') ?? $credit_budgetaires_select->design_fam ?? '' }}"  class="form-control griser design_fam">

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
                                            <label class="mt-1 label">Structure <span style="color: red"><sup> *</sup></span></label> 
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
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $structure_default->nom_structure ?? '' }}">
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
                                            <label class="mt-1 label">Section @if($disponible != null) <span style="color: red"><sup> *</sup></span> @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef" @else onkeyup="editSection(this)" list="section" @endif
                                            @if(isset($sections))

                                                @if(count($sections) === 0)
                                                    onfocus="this.blur()" style="background-color: #e9ecef"
                                                @endif
                                                
                                            @endif
                                            required  autocomplete="off" type="text" name="code_section" id="code_section" class="form-control @error('code_section') is-invalid @enderror code_section" value="{{ old('code_section') ?? '' }}">
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

                                                    value="{{ old('nom_section') ?? '' }}"

                                                @endif

                                                @else
                                                value="{{ old('nom_section') ?? '' }}"
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
                                            <label class="mt-1 label">Bénéficiaire @if($disponible != null) <span style="color: red"><sup> *</sup></span> @endif</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input style="display: none" onfocus="this.blur()" required id="agents_id_beneficiaire" type="text" class="form-control @error('agents_id_beneficiaire') is-invalid @enderror" name="agents_id_beneficiaire" value="{{ old('agents_id_beneficiaire') }}"  autocomplete="off" >

                                            <input required @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef" @else list="beneficiaire" onkeyup="editBeneficiaire(this)" @endif  id="mle" type="text" class="form-control @error('mle') is-invalid @enderror" name="mle" value="{{ old('mle') ?? '' }}"  autocomplete="off" >
                                        
                                            @error('mle')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; " id="nom_prenoms" type="text" class="form-control @error('nom_prenoms') is-invalid @enderror" name="nom_prenoms" value="{{ old('nom_prenoms') ?? '' }}"  autocomplete="off" >
                                        
                                            @error('nom_prenoms')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-6" style="text-align: right">

                                        <input style="display: none" onfocus="this.blur()" class="form-control" name="credit" id="credit" value="{{ $disponible->credit ?? '' }}">
                                        
                                        <label class="label" @if(isset($disponible->credit)) @if($disponible->credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible avant opération : </label>
                                    
                                    </div>
                                    <div class="col-sm-3"><label class="label" @if(isset($disponible->credit)) @if($disponible->credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($disponible->credit)) {{ strrev(wordwrap(strrev($disponible->credit ?? ''), 3, ' ', true)) ?? '' }} @else <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Chargé du suivi @if($disponible != null) <span style="color: red"><sup> *</sup></span> @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input style="display: none" onfocus="this.blur()" required id="agents_id" type="text" class="form-control @error('agents_id') is-invalid @enderror" name="agents_id" value="{{ old('agents_id') }}"  autocomplete="off" >

                                            <input required @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef" @else list="beneficiaire" onkeyup="editChargeSuivi(this)" @endif  id="mle_charge" type="text" class="form-control @error('mle_charge') is-invalid @enderror" name="mle_charge" value="{{ old('mle_charge') }}"  autocomplete="off" >
                                            
                                            @error('mle_charge')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef; " id="nom_prenoms_charge" type="text" class="form-control @error('nom_prenoms_charge') is-invalid @enderror" name="nom_prenoms_charge" value="{{ old('nom_prenoms_charge') }}"  autocomplete="off" >
                                        
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

                        <hr>
                        <div class="row d-flex">
                            <div class="col-md-12">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-md-2">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Objet demande @if($disponible != null) <span style="color: red"><sup> *</sup></span> @endif</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group d-flex ">

                                            <textarea required @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef; resize: none" @else style="resize: none" @endif 
                                            rows="2" id="intitule" type="text" class="form-control @error('intitule') is-invalid @enderror" name="intitule">{{ old('intitule') }}</textarea>
                                            @error('intitule')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row d-flex">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="montant">Montant @if($disponible != null) <span style="color: red"><sup> *</sup></span> @endif</label>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <input required @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef; text-align: right;" @else style="text-align: right;" @endif onkeypress="validate(event)" onkeyup="editMontant(this)" id="montant" type="text" class="form-control @error('montant') is-invalid @enderror" name="montant" value="{{ old('montant') }}"  autocomplete="off" >
                                        
                                        @error('montant')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col-md-2" style="text-align: right">
                                        <label class="label" for="observation">Observation</label>
                                    </div>
                                    <div class="col-md-6">
                                        <textarea @if($disponible === null) onfocus="this.blur()" style="background-color: #e9ecef; resize:none" @else style="resize: none" @endif rows="2" id="observation" type="text" class="form-control @error('observation') is-invalid @enderror" name="observation">{{ old('observation') }}</textarea>
                                        @error('observation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
        
                                </div>  
                            </div>
                        </div>
                        @if($disponible != null)
                            <table style="width:100%" id="tablePiece">
                                    
                                <tr>
                                    <td style="border: none;vertical-align: middle; text-align:right"  colspan="2">
                                        <a title="Rattacher un nouveau dépôt" onclick="myCreateFunction2()" href="#"><svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg></a> 
                                    </td>
                                </tr>

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
                                    

                                <tr>
                                    <td colspan="2" style="border: none;vertical-align: middle; text-align:right"> 
                                        <button onclick="return confirm('Faut-il enregistrer ?')" type="submit" class="btn btn-success" name="submit" value="enregistrer">
                                            {{ __('Enregistrer') }}
                                        </button>
                                    </td>
                                </tr>

                            </table>
                        @endif
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
                        document.location.replace('/demande_fonds/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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
                                document.location.replace('/demande_fonds/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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
                            document.location.replace('/demande_fonds/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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
