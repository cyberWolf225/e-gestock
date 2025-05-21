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
    <datalist id="fam_rattache">
        @foreach($familles as $famille)
            <option value="{{ $famille->design_fam }}">{{ $famille->ref_fam }} - {{ $famille->design_fam }}</option>
        @endforeach
    </datalist>
    <datalist id="depot">
        @foreach($depots as $depot)
            <option value="{{ $depot->design_dep }}">{{ $depot->ref_depot }} - {{ $depot->design_dep }}</option>
        @endforeach
    </datalist>
    <div class="row justify-content-center">
        <div class="col-md-12">
           
            <div class="card">
                <div class="card-header entete-table">{{ __('MODIFICATION DES INFORMATIONS DU FOURNISSEUR') }}</div>

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
                    <form method="POST" action="{{ route('organisations.update') }}">
                        @csrf

                                    
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label class="label" for="entnum">N° CNPS</label>
                                        <input onkeyup="editMle(this)" onblur="getEntreprise()"  onkeypress="validate(event)" id="entnum" type="text" class="form-control input-valide @error('entnum') is-invalid @enderror" name="entnum" value="{{ old('entnum') ?? $organisations->entnum ?? $organisation_secu['entnum'] ?? '' }}"  autocomplete="entnum">
        
                                        @error('entnum')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-5">
                                        <label class="label" for="denomination">Raison sociale <sup style="color: red">*</sup></label>
                                        <input id="denomination" type="text" class="form-control input-valide @error('denomination') is-invalid @enderror" name="denomination" value="{{ old('denomination') ?? $organisations->denomination ?? $organisation_secu['entraisoc'] ?? '' }}"  autocomplete="denomination">
        
                                        @error('denomination')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="label" for="email">E-mail <sup style="color: red">*</sup></label>
                                        <input  id="email" type="email" class="form-control input-valide @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $organisations->email ?? $organisation_secu['ent_email_1'] ?? '' }}"  autocomplete="email">
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <?php 
                                        $contacts = null;

                                        if(isset($organisation_secu['tel_portable'])) {
                                            $contacts = $organisation_secu['tel_portable'];
                                        }

                                        if(isset($organisation_secu['entteleph'])){
                                            if (isset($contacts)) {
                                                $contacts = $contacts.' / '.$organisation_secu['entteleph'];
                                            }else{
                                                $contacts = $organisation_secu['entteleph'];
                                            }
                                            
                                        }
                                    ?>

                                    <div class="col-md-2">
                                        <label class="label" for="contacts">Téléphone <sup style="color: red">*</sup></label>
                                        <input onkeyup="editMle(this)" id="contacts" type="text" class="form-control input-valide @error('contacts') is-invalid @enderror" name="contacts" value="{{ old('contacts') ?? $organisations->contacts ?? $contacts ?? '' }}"  autocomplete="contacts">
        
                                        @error('contacts')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <?php
                                    $mle = null;
                                    if (isset($organisation_secu['entnum'])) {
                                        $mle = 'frs'.$organisation_secu['entnum'];
                                    }
                                ?>
                                 
                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label class="label" for="mle">Matricule <sup style="color: red">*</sup></label>
                                        <input onfocus="this.blur()" style="background-color: #e9ecef;" id="mle" type="text" class="form-control input-valide @error('mle') is-invalid @enderror" name="mle" value="{{ old('mle') ?? $organisations->mle ?? $mle ?? '' }}"  autocomplete="mle" >

                                        <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" id="agents_id" type="text" class="form-control input-valide @error('agents_id') is-invalid @enderror" name="agents_id" value="{{ old('agents_id') ?? $organisations->agents_id ?? '' }}"  autocomplete="agents_id" >

                                        <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" id="mle_precedent" type="text" class="form-control input-valide @error('mle_precedent') is-invalid @enderror" value="{{ old('mle_precedent') ?? $organisations->mle ?? $mle ?? '' }}"  autocomplete="mle_precedent" >


                                        <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" id="num_fournisseur" type="text" class="form-control input-valide @error('num_fournisseur') is-invalid @enderror" name="num_fournisseur" value="{{ old('num_fournisseur') ?? $num_fournisseur ?? '' }}"  autocomplete="num_fournisseur" >

                                        <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" id="organisations_id" type="text" class="form-control input-valide @error('organisations_id') is-invalid @enderror" name="organisations_id" value="{{ old('organisations_id') ?? $organisations->organisations_id ?? '' }}"  autocomplete="organisations_id">
        
                                        @error('mle')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-5">
                                        <label class="label" for="nom_prenoms">Sigle <sup style="color: red">*</sup></label>
                                        <input  id="nom_prenoms" type="text" class="form-control input-valide @error('nom_prenoms') is-invalid @enderror" name="nom_prenoms" value="{{ old('nom_prenoms') ?? $organisations->nom_prenoms ?? $organisation_secu['entsigle'] ?? $organisation_secu['entraisoc'] ?? '' }}"  autocomplete="nom_prenoms" >
        
                                        @error('nom_prenoms')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
        
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-2">
                                        <label class="label" for="num_contribuable">N° Contribuable </label>
                                        <input id="num_contribuable" type="text" class="form-control input-valide @error('num_contribuable') is-invalid @enderror" name="num_contribuable" value="{{ old('num_contribuable') ?? $organisations->num_contribuable ?? $organisation_secu['entcptcont'] ?? '' }}"  autocomplete="num_contribuable" >
        
                                        @error('num_contribuable')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-5">
                                        <label class="label" for="adresse">Adresse </label>
                                        <input  id="adresse" type="text" class="form-control input-valide @error('adresse') is-invalid @enderror" name="adresse" value="{{ old('adresse') ?? $organisations->adresse ?? $organisation_secu['entadresphy'] ?? '' }}"  autocomplete="adresse" >
        
                                        @error('adresse')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <br><br>
                                        <label class="label" for="statut">Activer </label> &nbsp;&nbsp;&nbsp;
                                        <input id="statut" type="checkbox" name="statut" autocomplete="statut" 
                                        
                                        @if(isset($organisations->libelle))
                                            @if($organisations->libelle === 'Activé')
                                                checked
                                            @endif
                                        @endif 
                                        >
                                    </div>
                                    <div class="col-md-2">
                                        <button onclick="return confirm('Faut-il enregistrer ?')" style="margin-top: 15px;" type="submit" class="btn sm btn-success">
                                            {{ __('Modifier') }}
                                        </button>
                                    </div>
        
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-6">
                                        <label class="label" for="depot">Dépôt d'article &nbsp; <a title="Rattacher un nouveau dépôt" onclick="myCreateFunction2()" href="#"><svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg></a> 
                                            <label class="label" for="all_depot" style="margin-left: 100px;"> Tous les dépôts </label>

                                            <input style="margin-left: 10px;" id="all_depot" type="checkbox" name="all_depot" autocomplete="all_depot" 
                                            
                                            @if(isset($all_depot))
                                                @if($all_depot == 1)
                                                    checked
                                                @endif
                                            @endif

                                            >
                                        </label>
                                        
                                        <table id="tableDepot" style="width: 100%">
                                            @if(count($organisation_depots)>0)
                                            
                                                @foreach($organisation_depots as $organisation_depot)
                                                    <tr>
                                                        <td>
                                                            <input onkeyup="editDepot(this)" list="depot" id="depot" type="text" class="form-control input-valide" name="depot[]" value="{{ old('depot[]') ?? $organisation_depot->design_dep ?? '' }}"  >   
                                                        </td>
                                                        <td>&nbsp;&nbsp;
                                                            <a title="Retirer ce dépôt" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg></a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                <tr style="display: none">
                                                    <td></td>
                                                    <td></td>
                                                </tr>

                                            @else
                                                <tr>
                                                    <td>
                                                        <input onkeyup="editDepot(this)" list="depot" id="depot" type="text" class="form-control input-valide" name="depot[]" value="{{ old('depot[]') }}"  >   
                                                    </td>
                                                    <td>&nbsp;&nbsp;
                                                        <a title="Retirer ce dépôt" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                        </svg></a>
                                                    </td>
                                                </tr>
                                                <tr style="display: none">
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            @endif

                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="label" for="fam_rattache">Famille d'articles &nbsp; <a title="Rattacher ce fournisseur à une nouvelle famille d'article" onclick="myCreateFunction()" href="#"><svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg></a> 
                                        <label class="label" for="all_famille" style="margin-left: 100px;"> Toutes les familles </label>

                                        <input style="margin-left: 10px;" id="all_famille" type="checkbox" name="all_famille" autocomplete="all_famille" 
                                        
                                        @if(isset($all_famille))
                                            @if($all_famille == 1)
                                                checked
                                            @endif
                                        @endif

                                        >
                                        </label>
                                        
                                        <table id="tableFamille" style="width: 100%">
                                            @if(count($organisation_articles)>0)
                                            
                                                @foreach($organisation_articles as $organisation_article)
                                                    <tr>
                                                        <td>
                                                            <input onkeyup="editFamille(this)" list="fam_rattache" id="fam_rattache" type="text" class="form-control input-valide" name="fam_rattache[]" value="{{ old('fam_rattache[]') ?? $organisation_article->design_fam ?? '' }}"  >   
                                                        </td>
                                                        <td>&nbsp;&nbsp;
                                                            <a title="Retirer cette famille d'article des offres de ce fournisseur" onclick="removeRow(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg></a>
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
                                                    <input onkeyup="editFamille(this)" list="fam_rattache" id="fam_rattache" type="text" class="form-control input-valide" name="fam_rattache[]" value="{{ old('fam_rattache[]') }}"  >   
                                                </td>
                                                <td>&nbsp;&nbsp;
                                                    <a title="Retirer cette famille d'article des offres de ce fournisseur" onclick="removeRow(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                      </svg></a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    
    editMle = function(a){
        const saisie=document.getElementById('entnum').value;
        const num_fournisseur=document.getElementById('num_fournisseur').value;
        if(saisie != ''){
            var mle = 'frs'+saisie;
            
        }else{
            mle = "";
        }
        
        if (mle === undefined) {
            document.getElementById('mle').value = "";
        }else{
            document.getElementById('mle').value = mle;
        }
        
    }

    editStructure = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('#code_structure').val();
        if(value != ''){
            const block = value.split('->');
            var code_structure = block[0];
            var structure = block[1];
            
        }else{
            code_structure = "";
            structure = "";
        }
        
        tr.find('#code_structure').val(code_structure);
        tr.find('#structure').val(structure);
    }

    function myCreateFunction() {
        var table = document.getElementById("tableFamille");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length-1;

        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        cell1.innerHTML = '<td><input onkeyup="editFamille(this)" list="fam_rattache" id="fam_rattache" type="text" class="form-control input-valide" name="fam_rattache[]" value="{{ old("fam_rattache[]") }}"></td>';
        cell2.innerHTML = '<td>&nbsp;&nbsp;<a title="Retirer cette famille d\'article des offres de ce fournisseur" onclick="removeRow(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></td>';
    }

    function myCreateFunction2() {
      var table = document.getElementById("tableDepot");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            cell1.innerHTML = '<td><input onkeyup="editDepot(this)" list="depot" id="depot" type="text" class="form-control input-valide" name="depot[]" value="{{ old("depot[]") }}"></td>';
            cell2.innerHTML = '<td>&nbsp;&nbsp;<a title="Retirer ce dépot" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></td>';
            
        
      
    }

    removeRow = function(el) {
        var table = document.getElementById("tableFamille");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;

            if(nbre_rows == 1){
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Vous ne pouvez pas supprimer la dernière ligne de famille d\article',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });

            }else{
                $(el).parents("tr").remove(); 
            }  
    }

    removeRow2 = function(el) {
        var table = document.getElementById("tableDepot");
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

    function getEntreprise() {

        var organisation = window.location.href.split('edit/')[1];

        var valeur = document.getElementById('entnum').value;
        if (valeur != '') {
            entnum = '69i57j0i512l4j69i65j69i60l2.2468j0j781045'+valeur+'y65300i512l4j69i65j6';

            document.location.replace('/organisations/edits/'+organisation+'/'+entnum);
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
