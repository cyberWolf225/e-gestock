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

    <div class="row"> 
        <div class="col-md-12">

            <div class="card">
                <div class="card-header entete-table">{{ __('ENREGISTREMENT DE DEMANDE DE COTATION') }} 
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y") }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $exercice ?? '' }}</strong></span>
                    
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
                    
                    <form enctype="multipart/form-data" method="POST" action="{{ route('demande_cotations.store') }}">
                        @csrf
                        
                            
                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Compte budg. <sup style="color: red">*</sup></label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="display:none" onfocus="this.blur()" autocomplete="off" type="text" name="nbre_ligne" id="nbre_ligne" value="{{ old('nbre_ligne') ?? $nbre_ligne }}" class="form-control" >

                                            <input required onkeyup="editCompte(this)"  list="famille" autocomplete="off" type="text" name="ref_fam" id="ref_fam" class="form-control @error('ref_fam') is-invalid @enderror ref_fam" value="{{ old('ref_fam') ?? $famille_select->ref_fam ?? '' }}">
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
                                            <label class="mt-1 label">Structure <span style="color: red"><sup> *</sup></span></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input required onkeyup="editStructure(this)" list="structure" autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure_select->code_structure ?? '' }}">
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

                                            <input onclick="handleClick(this);" required autocomplete="off" type="radio" name="type_operations_libelle" id="type_operations_libelle_bcs" class=" @error('type_operations_libelle') is-invalid @enderror type_operations_libelle" value="BCS">

                                            <label  style="cursor: pointer" class="mt-1 ml-5 mr-3 label" for="type_operations_libelle_bcn">Commande non stockable</label>

                                            <input onclick="handleClick(this);" required autocomplete="off" type="radio" name="type_operations_libelle" id="type_operations_libelle_bcn" class="@error('type_operations_libelle') is-invalid @enderror type_operations_libelle" value="BCN">
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
                            </div>
                            <div class="col-md-6">

                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Gestion <span style="color: red"><sup> *</sup></span></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required onkeyup="editGestion(this)" list="gestion" autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion_select->code_gestion ?? '' }}">
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
                                            <label class="mt-1 label">Intitulé @if($griser === null)<span style="color: red"><sup> *</sup></span> @endif </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea  
                                            required
                                            @if($griser != null)  style="background-color: #e9ecef; resize:none" onfocus="this.blur()" @endif 

                                            @if($griser === null)  style="resize:none" @endif
                                            
                                            autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ old('intitule') }}</textarea>
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
                        </div>

                        @if($credit_budgetaires_credit != null && $credit_budgetaires_credit > 0)
                            <div class="panel panel-footer">
                                <div id="div_detail_bcs" style="@if($display_div_detail_bcs === null) display: none @endif">
                                    @include('demande_cotations.partial_create_bcs')
                                </div>
                                <div id="div_detail_bcn" style="@if($display_div_detail_bcn === null) display: none @endif">
                                    @include('demande_cotations.partial_create_bcn')
                                </div>
                                
                                <div id="div_save" style="@if($display_div_save === null) display: none @endif">
                                    <table style="width:100%" id="tablePiece">
            
                                        <tr>
                                            <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">
                                                <a title="Rattacher un nouveau dépôt" onclick="myCreateFunction2()" ><svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg></a> 
                                            </td>
                                        </tr>
                                    
                                        <tr>
                                            
                                            <td>
                                                <input type="file" name="piece[]" class="form-control-file" id="piece" >   
                                            </td>
                                            <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;">
                                                <a title="Retirer ce fichier" onclick="removeRow2(this)" ><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                    </svg></a>
                                            </td>
                                        </tr>
                                    </table>
                                    <br/>
                                    <table style="width: 100%">
                                        <tr>
                                            <td colspan="4" style="border-bottom: none">
                                                <span style="font-weight: bold">Ecrivez votre commentaire </span> <span style="color: brown; margin-left:3px;"> {{ $nom_prenoms_commentaire ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $profil_commentaire ?? '' }} </span>)

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
                                                
                                                    <div class="col-md-11">

                                                        <textarea @if(isset($griser)) disabled style="width: 100%; resize:none" @else style="width: 100%; resize:none" @endif class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                                        @error('commentaire')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    

                                                    <div class="col-md-1" style="margin-top:px">
                                                    
                                                        <button type="submit" class="btn btn-success">
                                                            Enregistrer
                                                        </button>

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
                            document.location.replace('/demande_cotations/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
                        }
                    }
                }


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
                        document.location.replace('/demande_cotations/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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
                            document.location.replace('/demande_cotations/crypt/'+ref_fam+'/'+code_structure+'/'+code_gestion);
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

            cell1.innerHTML = '<td><input required <?php if ($griser!=null) { ?> style="background-color: transparent; border: none;" onfocus="this.blur()" <?php }else{ ?> style="background-color: ; border:  ;" onkeyup="editDesign(this)" list="list_article" <?php } ?> autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles"></td>';
            cell2.innerHTML = '<td><textarea rows="1" required autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article"></textarea></td>';
            cell3.innerHTML = '<td><textarea style="border-color:transparent; resize:none" rows="1" list="description_articles" autocomplete="off" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle"></textarea></td>';
            cell4.innerHTML = '<td><input style="border-color:transparent;" list="list_unite" autocomplete="off" type="text" id="unites_libelle_bcs" name="unites_libelle_bcs[]" class="form-control unites_libelle_bcs"/></td>';
            cell5.innerHTML = '<td><textarea rows="1" maxlength="12" onkeyup="editQteBcs(this)" onkeypress="validate(event)" <?php if ($griser==null) { ?> style="text-align:center; border-color:transparent; resize:none" <?php } ?> required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; text-align:center; border-color:transparent; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text" id="qte_bcs" name="qte_bcs[]" class="form-control qte_bcs"></textarea></td>';
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
            cell6.style.textAlign = "center";

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
                $(el).parents("tr").remove(); 
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