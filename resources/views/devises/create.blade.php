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
<datalist id="list_devise">
    @foreach($devises as $devise)
        <option>{{ $devise->libelle ?? '' }}</option>
    @endforeach
</datalist>

<datalist id="list_cotation_fournisseur">
    @foreach($cotation_fournisseurs as $cotation_fournisseur)
        <option value="{{ $cotation_fournisseur->num_bc ?? '' }}->{{ $cotation_fournisseur->denomination ?? '' }} ({{ $cotation_fournisseur->entnum ?? '' }})->{{ $cotation_fournisseur->devises_libelle ?? '' }}->{{ $cotation_fournisseur->id ?? '' }}">{{ $cotation_fournisseur->num_bc ?? '' }}</option>
    @endforeach
</datalist>

<div class="container">
    <br>    
    <div class="row"> 
        <div class="col-md-12">

            <div class="card">
                <div class="card-header entete-table">{{ __('MODIFICATION DE MONTANT EN LETTRE') }} 
                    <span style="float: right; margin-right: 20px;">Date : <strong>{{ date("d/m/Y") }}</strong></span>
                    {{-- <span style="float: right; margin-right: 20px;">Exercice : <strong>{{ $exercices->exercice ?? '' }}</strong></span> --}}
                    
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
                    
                    <form enctype="multipart/form-data" method="POST" action="{{ route('devises.store') }}">
                        @csrf
                        
                            
                        <div class="row d-flex">
                            <div class="col-md-12">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-md-2">
                                        <div class="form-group" style="text-align: ">
                                            <label class="mt-1 label">N° Bon de commande <sup style="color: red">*</sup></label> 

                                            <input name="cotation_fournisseurs_id" id="cotation_fournisseurs_id" class="form-control" onfocus="this.blur()" style="display: none">

                                            <input onkeyup="editDesign(this)" list="list_cotation_fournisseur" class="form-control form-control-sm @error('num_bc') is-invalid @enderror" name="num_bc" id="num_bc" type="text" value="{{ old('num_bc') }}" autocomplete="off">

                                            @error('num_bc')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" style="text-align: ">
                                            <label class="mt-1 label">Fournisseur</label> 

                                            <input onfocus="this.blur()" class="form-control form-control-sm @error('fournisseur') is-invalid @enderror" name="fournisseur" id="fournisseur" type="text" value="{{ old('fournisseur') }}" style="background-color: #e9ecef">

                                            @error('fournisseur')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" style="text-align: ">
                                            <label class="mt-1 label">Devise actuelle</label> 

                                            <input onfocus="this.blur()" class="form-control form-control-sm @error('devises_libelle') is-invalid @enderror" name="devises_libelle" id="devises_libelle" type="text" value="{{ old('devises_libelle') }}" style="background-color: #e9ecef">

                                            @error('devises_libelle')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" style="text-align: ">
                                            <label class="mt-1 label">Nouvelle devise <sup style="color: red">*</sup></label> 

                                            <input list="list_devise" class="form-control form-control-sm @error('devises_libelle_new') is-invalid @enderror" name="devises_libelle_new" id="devises_libelle_new" type="text" value="{{ old('devises_libelle_new') }}">

                                            @error('devises_libelle_new')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button onclick="return confirm('Faut-il enregistrer ?')" class="btn btn-success btn-sm" type="submit" style="margin-top: 35px; float: right;">Enregistrer</button>
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
    editDesign = function(a){
        const saisie=document.getElementById('num_bc').value;
        if(saisie != ''){
            const block = saisie.split('->');
            var num_bc = block[0];
            var fournisseur = block[1];
            var devises_libelle = block[2];
            var cotation_fournisseurs_id = block[3];
            
        }else{
            num_bc = "";
            fournisseur = "";
            devises_libelle = "";
            cotation_fournisseurs_id = "";
        }
        
        document.getElementById('num_bc').value = num_bc;

        if (fournisseur === undefined) {
            document.getElementById('fournisseur').value = "";
            document.getElementById('devises_libelle').value = "";
            document.getElementById('cotation_fournisseurs_id').value = "";
        }else{
            document.getElementById('fournisseur').value = fournisseur;
            document.getElementById('devises_libelle').value = devises_libelle;
            document.getElementById('cotation_fournisseurs_id').value = cotation_fournisseurs_id;
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