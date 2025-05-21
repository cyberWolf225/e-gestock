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
    <datalist id="code_section">
        @foreach($sections as $section) 
            <option value="{{ $section->nom_section }}">{{ $section->nom_section }}</option>
        @endforeach
    </datalist>
    <datalist id="code_structure">
        @foreach($structures as $structure)
            <option value="{{ $structure->code_structure }}->{{ $structure->nom_structure }}">{{ $structure->nom_structure }}</option>
        @endforeach
    </datalist>
    <datalist id="type_profils">
        @foreach($type_profils as $type_profil)
            <option value="{{ $type_profil->name }}">{{ $type_profil->name }}</option>
        @endforeach
    </datalist>
    <div class="row justify-content-center">
        
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('Modification de compte') }}</div>
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

                    <form method="POST" action="{{ route('agents.update') }}">
                        @csrf
                        

                                    
                        <div class="row">
                        <div class="col-md-4">
                            
        
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="label" for="mle">Matricule <sup style="color: red">*</sup></label>

                                        <input style="display: none" id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ old('id') ?? $agents->id ?? '' }}" onfocus="this.blur()" autocomplete="off">

                                        <input style="display: none" id="users_id" type="text" class="form-control @error('users_id') is-invalid @enderror" name="users_id" value="{{ old('users_id') ?? $agents->users_id ?? '' }}" onfocus="this.blur()" autocomplete="off">

                                        <input style="display: none" id="email_old" type="email" class="form-control @error('email_old') is-invalid @enderror" name="email_old" value="{{ old('email_old') ?? $agents->email ?? '' }}" onfocus="this.blur()" autocomplete="off">

                                        <input  id="mle" type="text" class="form-control @error('mle') is-invalid @enderror" name="mle" value="{{ old('mle') ?? $agents->mle ?? '' }}"  autocomplete="mle" autofocus>

                                        <input style="display: none" onfocus="this.blur()" id="agents_id" type="text" class="form-control @error('agents_id') is-invalid @enderror" name="agents_id" value="{{ $agents->id ?? '' }}">
        
                                        @error('mle')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
        
                                </div>  
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="label" for="email">E-mail <sup style="color: red">*</sup></label>
                                        <input  id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $agents->email ?? '' }}"  autocomplete="off">
        
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="label" for="type_profil">Profils <sup style="color: red">*</sup> 
                                                
                                                <a style="margin-left: 17px; font-style:italic; color:gray; cursor: pointer;" title="Ajouter un nouveau profil" onclick="myCreateFunction()"  class="addRow">
                                                (Ajouter un nouveau profil)
                                                </a>

                                            </label>
                                            <table id="tableProfile" width="100%">
                                                <?php
                                                    $affiche_bouton = 1;
                                                ?>
                                                @foreach($profils as $profil)
                                                
                                                   <tr>
                                                    <td>
                                                        <input list="type_profils" style="width: 250px;"  id="type_profil" type="text" class="form-control @error('type_profil') is-invalid @enderror" name="type_profil[]" value="{{ old('type_profil') ?? $profil->name ?? '' }}"  autocomplete="off">
                                                    </td>
                                                    <td style="vertical-align: middle; text-align:">
                                                        <a 
                                                        @if($affiche_bouton === 0)
                                                            style="margin-left: 0px; cursor: pointer;"
                                                        @endif
                                                        title="Retirer ce profil" onclick="removeRow(this)"  class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                          </svg></a>

                                                    </td>
                                                    </tr> 
                                                    <?php
                                                        $affiche_bouton = 0;
                                                    ?>
                                                @endforeach

                                                @if(count($profils)===0)
                                                
                                                    <tr>
                                                        <td>
                                                            <input list="type_profils" style="width: 250px;"  id="type_profil" type="text" class="form-control @error('type_profil') is-invalid @enderror" name="type_profil[]" value="{{ old('type_profil') }}"  autocomplete="off">
                                                        </td>
                                                        <td style="vertical-align: middle; text-align:left">
                                                            <a title="Retirer ce profil" onclick="removeRow(this)"  class="remove" style="cursor: pointer;"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg></a>

                                                            <button style="margin-left: 10px;" type="submit" class="btn btn-success">
                                                                {{ 'Enregistrer' }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endif
                                                
                                            </table>
                                          </div>
                                        
                                    </div>
        
                                </div>
                                </div>
                                <div class="col-md-8">
                                    
                                    <div class="form-group row">
                                    
                                        <div class="col-md-12">
                                            <label class="label" for="nom_prenoms">Nom & Prénom(s) <sup style="color: red">*</sup></label>
                                            <input  id="nom_prenoms" type="text" class="form-control @error('nom_prenoms') is-invalid @enderror" name="nom_prenoms" value="{{ old('nom_prenoms') ?? $agents->nom_prenoms ?? '' }}"  autocomplete="off" autofocus>
            
                                            @error('nom_prenoms')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                    </div>
                            
                                    <div class="form-group row">  
        
                                        <div class="col-md-3">
                                            <label class="label" for="code_structure">Code Structure</label>
                                            <input  onfocus="this.blur()" style="background-color:  #e9ecef" onkeyup="editStructure(this)" list="code_structure" id="code_structure" type="text" class="form-control @error('code_structure') is-invalid @enderror" name="code_structure" value="{{ old('code_structure') ?? $agents->code_structure ?? '' }}"  autocomplete="off">
            
                                            @error('code_structure')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-9">
                                            <label class="label" for="structure">Structure</label>
                                            <input  onfocus="this.blur()" style="background-color:  #e9ecef" id="structure" type="text" class="form-control @error('structure') is-invalid @enderror" name="structure" value="{{ old('structure') ?? $agents->nom_structure ?? '' }}"  autocomplete="off">
            
                                            @error('structure')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    
                                    </div>
                                    <div class="form-group row">  
        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="label" for="nom_section">Section <sup style="color: red">*</sup> 
                                                    
                                                    <a style="margin-left: 17px; font-style:italic; color:gray; cursor: pointer;" title="Ajouter une nouvelle section" onclick="myCreateFunction2()"  class="addRow">
                                                    (Ajouter une nouvelle section)
                                                    </a>

                                                </label>
                                                <table id="tableSection" width="100%">
                                                    <?php
                                                        $affiche_bouton = 1;
                                                    ?>
                                                    @foreach($agent_sections as $agent_section)
                                                    
                                                       <tr>
                                                        <td>
                                                            <input list="code_section" style="width: 100%;"  id="nom_section" type="text" class="form-control @error('nom_section') is-invalid @enderror" name="nom_section[]" value="{{ old('nom_section') ?? $agent_section->nom_section ?? '' }}"  autocomplete="off">
                                                        </td>
                                                        <td style="vertical-align: middle; text-align:">
                                                            <a 
                                                            @if($affiche_bouton === 0)
                                                                style="margin-left: 0px;"
                                                            @endif
                                                            title="Retirer cette section" onclick="removeRow2(this)"  class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                              </svg></a>

                                                              @if($affiche_bouton === 1)
                                                                <button style="margin-left: 10px;" type="submit" class="btn btn-success">
                                                                {{ 'Enregistrer' }}
                                                                </button>
                                                              @endif

                                                        </td>
                                                        </tr> 
                                                        <?php
                                                            $affiche_bouton = 0;
                                                        ?>
                                                    @endforeach

                                                    @if(count($agent_sections)===0)
                                                    
                                                        <tr>
                                                            <td>
                                                                <input list="code_section" style="width: 250px;"  id="nom_section" type="text" class="form-control @error('nom_section') is-invalid @enderror" name="nom_section[]" value="{{ old('nom_section') }}"  autocomplete="off">
                                                            </td>
                                                            <td style="vertical-align: middle; text-align:left">
                                                                <a title="Retirer cette section" onclick="removeRow(this)"  class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                                </svg></a>

                                                                <button style="margin-left: 10px;" type="submit" class="btn btn-success">
                                                                    {{ 'Enregistrer' }}
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    
                                                </table>
                                              </div>
                                            
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

    editSection = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('#code_section').val();
        if(value != ''){
            const block = value.split('->');
            var code_section = block[0];
            var section = block[1];
            var code_structure = block[2];
            var structure = block[3];
            
        }else{
            code_section = "";
            section = "";
            code_structure = "";
            structure = "";
        }
        
        tr.find('#code_section').val(code_section);
        tr.find('#section').val(section);
        tr.find('#code_structure').val(code_structure);
        tr.find('#structure').val(structure);
    }

    function myCreateFunction() {
        var table = document.getElementById("tableProfile");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        cell1.innerHTML = '<td><input list="type_profils" style="width: 250px;"  id="type_profil" type="text" class="form-control" name="type_profil[]"  autocomplete="off"></td>';
        cell2.innerHTML = '<td style="vertical-align: middle; text-align:center"><a title="Retirer ce profil" onclick="removeRow(this)"  class="remove" style="cursor: pointer;"><svg style="color: red; font-weight:bold; font-size:15px; margin-right:10px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
        cell2.style.marginRight = "20px;"
      
    }

    removeRow = function(el) {
        var table = document.getElementById("tableProfile");
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

    function myCreateFunction2() {
        var table = document.getElementById("tableSection");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
        var row = table.insertRow(nbre_rows);
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        cell1.innerHTML = '<td><input list="code_section" style="width: 100%;"  id="nom_section" type="text" class="form-control" name="nom_section[]"  autocomplete="off"></td>';
        cell2.innerHTML = '<td style="vertical-align: middle; text-align:center"><a title="Retirer cette section" onclick="removeRow2(this)"  class="remove"><svg style="color: red; font-weight:bold; font-size:15px; margin-right:10px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
        cell2.style.marginRight = "20px;"
      
    }

    removeRow2 = function(el) {
        var table = document.getElementById("tableSection");
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
