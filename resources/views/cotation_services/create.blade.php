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
            <option value="{{ $organisation->entnum }}->{{ $organisation->denomination }}">{{ $organisation->denomination }}</option>
        @endforeach
    </datalist>

    <datalist id="list_service">
        @foreach($services as $service)
            <option>{{ $service->libelle }}</option>
        @endforeach
    </datalist>

    <datalist id="list_unite">
        @foreach($unites as $unite)
            <option>{{ $unite->unite }}</option>
        @endforeach
    </datalist>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('BON DE COMMANDE DE DEMANDE DE FONDS') }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($cotation_service->created_at ?? date("Y-m-d"))) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $cotation_service->exercice ?? $demande_fond->exercice ?? date("Y") }}</strong></span>
                    
                </div>
                
                <form method="POST" action="{{ route('cotation_services.store') }}">
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
                                            <label class="label" class="mt-1 font-weight-bold">N° Bon Cde. </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group d-flex ">
                                            
                                            <input style="background-color: #e9ecef; color:red; font-weight:bold; display:none" onfocus="this.blur()" value="{{  $demande_fond->id ?? '' }}" autocomplete="off" type="text" name="demande_fonds_id" class="form-control">

                                            <input style="color:red; font-weight:bold; margin-top:-7px" onfocus="this.blur()" value="{{ $cotation_service->num_bc ?? $num_bc ?? '' }}" autocomplete="off" type="text" name="num_bc" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 font-weight-bold">Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $cotation_service->intitule ?? $demande_fond->intitule ?? '' }}" autocomplete="off" type="text" name="intitule" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 font-weight-bold">Code échéance<span style="color: red"><sup> *</sup></span> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required value="{{ $commande->libelle_periode ?? '' }}" onkeyup="editPeriode(this)" list="list_periode" autocomplete="off" type="text" id="periodes_id" name="periodes_id" class="form-control">
                                            <input value="{{ $commande->periodes_id ?? '' }}" style="display: none" onfocus="this.blur()" type="text" id="valeur" name="valeur" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onkeypress="validate(event)" required value="{{ $commande->delai ?? '' }}" onkeyup="editPeriode2(this)" autocomplete="off" type="text" id="delai" name="delai" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required @if(isset($commande->date_echeance)) value="{{ date("d/m/Y",strtotime($commande->date_echeance)) }}" @endif style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="date_echeance" name="date_echeance" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Date retrait </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_retrait" class="form-control @error('date_retrait') is-invalid @enderror" @if(isset($cotation_service->date_retrait))
                                            value="{{  $cotation_service->date_retrait}}"
                                            @endif >
                                            @error('date_retrait')
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
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 font-weight-bold">Structure </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $demande_fond->code_structure ?? '' }}" autocomplete="off" type="text" name="code_structure" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $demande_fond->nom_structure ?? '' }}" autocomplete="off" type="text" name="nom_structure" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 font-weight-bold">Compte budg. </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $cotation_service->ref_fam ?? $demande_fond->ref_fam ?? ''}}" autocomplete="off" type="text" name="ref_fam" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input style="background-color: #e9ecef" onfocus="this.blur()" value="{{ $cotation_service->design_fam ?? $demande_fond->design_fam ?? ''}}" autocomplete="off" type="text" name="intitule" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 font-weight-bold">Fournisseur </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onkeyup="editFr(this)" list="list_organisation" autocomplete="off" type="text" name="entnum" id="entnum" class="form-control">
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
                                            <label class="label" class="mt-1 ">Date livraison </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-6 pr-1">
                                        <div class="form-group">
                                            <input autocomplete="off" type="date" name="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror" @if(isset($cotation_service->date_livraison_prevue))
                                            value="{{  $cotation_service->date_livraison_prevue}}"
                                            @endif >
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
                            <tr style="background-color: #e9ecef; color: #7d7e8f">
                                <th style="width:40%; text-align:left">DÉSIGNATION</th>
                                <th style="width:10%; text-align:center">UNITÉ<span style="color: red"><sup> *</sup></span></th>
                                <th style="width:10%; text-align:center">QTÉ<span style="color: red"><sup> *</sup></span></th>
                                <th style="width:13%; text-align:center">PU HT<span style="color: red"><sup> *</sup></span></th>
                                <th style="width:10%; text-align:center">REMISE (%)</th>
                                <th style="width:15%; text-align:center">MONTANT HT</th>
                                <th style="text-align:center; width:1%"><a onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                  </svg></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input list="list_service" autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service">
                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input list="list_unite" style="text-align:left" autocomplete="off" required type="text" onkeyup="editUnite(this)" name="unite[]" class="form-control unite">
                                </td>

                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input style="text-align:center" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte">
                                </td>
                                
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input style="text-align:right" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="prix_unit[]" class="form-control prix_unit">

                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input autocomplete="off" type="text" style="text-align: center" onkeyup="editMontant(this)" onkeypress="validate(event)" name="remise[]" class="form-control remise">
                                </td>
                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                    <input autocomplete="off" required onfocus="this.blur()" onkeypress="validate(event)" style="border-color: transparent;background-color: transparent; text-align:right" type="text" name="montant_ht[]" class="form-control montant_ht">

                                    <input autocomplete="off" required onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis">

                                </td>
                                <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                    <a onclick="removeRow(this)" href="#" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg></a>
                                </td>
                            </tr>
                            
                        </tbody>
                        
                    </table>
                    <table width="100%" style="margin-bottom:10px;">
                        <tr>
                            <td colspan="6" style="border: none">
                                <div class="row d-flex pl-3">
                                                       
                                        <div class="pr-0"><label class="label" class="font-weight-bold mt-1 mr-1">Montant total brut</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut">
                                    
                                        </div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold  mt-1 mr-1">Remise</label><br>  <input onkeyup="editRemiseGenerale(this)" onkeypress="validate(event)" autocomplete="off"  style="width:110px; text-align:right;" type="text" name="remise_generale" class="form-control remise_generale"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total net ht</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">TVA (%)</label><br>  <input list="list_taxe_tva" onkeyup="editRemiseGenerale(this)" onkeypress="validate(event)" autocomplete="off" style=" width:80px; text-align:center" type="text" name="tva" class="form-control tva"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva"></div>
            
                                        <div class="pl-1"><label class="label" class="font-weight-bold mt-1 mr-2">Montant total ttc</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"></div>

                                        <div class="pl-1" style="margin-left:0px;"><label class="label" class="font-weight-bold mt-1 mr-2">Net à payer</label><br>  <input required autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"> </div>

                                </div>
                                <div class="row d-flex pl-3">

                                    <div class="pl-1" style="padding-left:3px; text-align:center" ><label class="label" class="font-weight-bold mt-1 mr-2">Acompte</label><br>  <input style="" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)"></div>

                                    <div 
                                    
                                    
                                        @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                                style="padding-left: 10px; text-align: center" 
                                            @else
                                                style="padding-left: 10px; text-align: center; display:none"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center; display:none"
                                        @endif
                                    
                                    
                                    id="acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">(%)</label><br>  <input maxlength="3" onkeyup="editTauxAcompte(this)" autocomplete="off" onkeypress="validate(event)" style=" width:70px; text-align:center;" type="text" name="taux_acompte" class="form-control taux_acompte"></div>

                                    <div 
                                    
                                        @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                                style="padding-left: 10px; text-align: center; display:none" 
                                            @else
                                                style="padding-left: 10px; text-align: center"
                                            @endif

                                        @else
                                            style="padding-left: 10px; text-align: center"
                                        @endif
                                    
                                    id="i_acompte_taux_div" ><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                    <div class="pl-1" 
                                    
                                    
                                        @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                            
                                            @else
                                            style="display:none"
                                            @endif

                                        @else
                                        style="display:none"
                                        @endif

                                    
                                    id="acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input autocomplete="off" onkeyup="editAcompte(this)" onkeypress="validate(event)" style=" width:110px; text-align:right" type="text" name="montant_acompte" class="form-control montant_acompte"></div>

                                    <div class="pl-1" 
                                    
                                    
                                        @if($cotation_service!= null)
                                            
                                            @if($cotation_service->acompte === 1) 
                                                style="display:none"
                                            @else
                                            
                                            @endif

                                        @else
                                        
                                        @endif

                                    
                                    id="i_acompte_div"><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control"></div>

                                    <div style="padding-left: 10px;" ><label class="label" class="font-weight-bold mt-1 mr-2">&nbsp;</label><br><button onclick="return confirm('Faut-il enregistrer ?')" type="submit" class="btn btn-success">Enregistrer</button></div>
                                
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
        
    editFr = function(a){
        const saisie=document.getElementById('entnum').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var entnum = block[0];
            var denomination = block[1];

        }else{
            entnum = "";
            denomination = "";
        }
        
        document.getElementById('entnum').value = entnum;

        if (denomination === undefined) {
            document.getElementById('denomination').value = "";
        }else{
            document.getElementById('denomination').value = denomination;
        }
        
    }

    editPeriode = function(a){
        const saisie=document.getElementById('periodes_id').value;
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
        
        document.getElementById('periodes_id').value = periodes_id;
        if (valeur === undefined) {
            document.getElementById('valeur').value = "";
        }else{
            document.getElementById('valeur').value = valeur;
        }

        if (date_echeance === 'Invalid date') {
            document.getElementById('date_echeance').value = "";
        }else{
            document.getElementById('date_echeance').value = date_echeance;
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

        
        
        
        var montant_ht=( ( prix_unit * qte ) - ((( prix_unit * qte ) * remise)/100) );
        

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        tr.find('.qte').val(int3.format(qte));
        tr.find('.prix_unit').val(int3.format(prix_unit));
        tr.find('.remise').val(int3.format(remise));
        tr.find('.montant_ht').val(int3.format(montant_ht));
        tr.find('.montant_ht_bis').val(montant_ht);

        total();
    }

    function total(){
            var montant_total_brut=0;
            $('.montant_ht_bis').each(function(i,e){
                var montant_ht_bis =$(this).val()-0;
                montant_total_brut +=montant_ht_bis;
            });

            
            
            var remise_generale=$('.remise_generale').val();
            remise_generale = remise_generale.trim();
            remise_generale = remise_generale.replace(' ','');
            remise_generale = reverseFormatNumber(remise_generale,'fr');



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

                    $('.montant_acompte').val(int3.format(montant_acompte)); 
                }

            }else{
                var taux_acompte = (montant_acompte / net_a_payer) * 100;
                    
                $('.taux_acompte').val(taux_acompte);
                $('.montant_acompte').val(int3.format(montant_acompte));
            }

            





            


            



            
    
            

            
            


            

            
            $('.remise_generale').val(int3.format(remise_generale)); 
            $('.montant_total_brut').val(int3.format(montant_total_brut)); 
            $('.montant_total_net').val(int3.format(montant_total_net)); 
            $('.montant_total_ttc').val(int3.format(montant_total_ttc)); 
            $('.net_a_payer').val(int3.format(net_a_payer));  
            
            $('.montant_tva').val(int3.format(montant_tva));      
    
    }

    editRemiseGenerale = function(e){
        var tr=$(e).parents("tr");
        
        var remise_generale=tr.find('.remise_generale').val();
        remise_generale = remise_generale.trim();
        remise_generale = remise_generale.replace(' ','');
        remise_generale = reverseFormatNumber(remise_generale,'fr');
        remise_generale = remise_generale.replace(' ','');
        
        var montant_total_brut=tr.find('.montant_total_brut').val();  
        montant_total_brut = montant_total_brut.trim();
        montant_total_brut = montant_total_brut.replace(' ','');
        montant_total_brut = reverseFormatNumber(montant_total_brut,'fr');  
        montant_total_brut = montant_total_brut.replace(' ','');

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

                $('.montant_acompte').val(int3.format(montant_acompte)); 
            }else{
                alert("Attention !!!  Taux acompte invalide : Le taux d'acompte ne peut être supérieur à 100%.");
                tr.find('.taux_acompte').val("");
                tr.find('.montant_acompte').val(0);
            }

        }else{
            var taux_acompte = (montant_acompte / net_a_payer) * 100;

            if ((taux_acompte >= 0) && (taux_acompte <= 100)) {
                $('.taux_acompte').val(taux_acompte);
                $('.montant_acompte').val(int3.format(montant_acompte));
            }else{
                alert("Attention !!! Montant acompte invalide : L'acompte ne peut être supérieur au montant net à payer.");
                tr.find('.taux_acompte').val('');
                tr.find('.montant_acompte').val(0);
            }

        }

        

        
            
        tr.find('.montant_total_net').val(int3.format(montant_total_net));
        tr.find('.montant_tva').val(int3.format(montant_tva));
        tr.find('.montant_total_ttc').val(int3.format(montant_total_ttc));
        tr.find('.net_a_payer').val(int3.format(net_a_payer));
        tr.find('.remise_generale').val(int3.format(remise_generale));
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
                tr.find('.taux_acompte').val(taux_acompte);
                tr.find('.montant_acompte').val(int3.format(montant_acompte));
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

                $('.montant_acompte').val(int3.format(montant_acompte)); 
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
            var cell7 = row.insertCell(6);
            cell1.innerHTML = '<td><input list="list_service" autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service"></td>';
            cell2.innerHTML = '<td><input list="list_unite" style="text-align:left" autocomplete="off" required type="text" onkeyup="editUnite(this)" name="unite[]" class="form-control unite"></td>';
            cell3.innerHTML = '<td><input style="text-align:center" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte"></td>';
            cell4.innerHTML = '<td><input style="text-align:right" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="prix_unit[]" class="form-control prix_unit"></td>';
            cell5.innerHTML = '<td><input autocomplete="off" type="text" style="text-align: center" onkeyup="editMontant(this)" onkeypress="validate(event)" name="remise[]" class="form-control remise"></td>';
            cell6.innerHTML = '<td><input autocomplete="off" required onfocus="this.blur()" onkeypress="validate(event)" style="border-color: transparent;background-color: transparent; text-align:right" type="text" name="montant_ht[]" class="form-control montant_ht"><input autocomplete="off" required onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis"></td>';
            cell7.innerHTML = '<td><a onclick="removeRow(this)" href="#" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';


                                
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

            cell7.style.borderCollapse = "collapse";
            cell7.style.padding = "0";
            cell7.style.margin = "0";
            cell7.style.verticalAlign = "middle";
            cell7.style.textAlign = "center";
            
        
      
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
