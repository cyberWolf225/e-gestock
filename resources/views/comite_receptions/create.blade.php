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
    <datalist id="list_agent">
        @foreach($agents as $agent)
            <option value="{{ $agent->mle ?? '' }}->{{ $agent->nom_prenoms ?? '' }}->{{ $agent->nom_structure ?? '' }}">{{ $agent->nom_prenoms ?? '' }}</option>
        @endforeach
    </datalist>

    <datalist id="list_bc">
        @foreach($demande_achats as $demande_achat)
        <option value="{{ $demande_achat->num_bc ?? '' }}->{{ $demande_achat->intitule ?? '' }}->{{ $demande_achat->design_dep ?? '' }}">{{ $demande_achat->intitule ?? '' }}</option>
    @endforeach
    </datalist>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __(strtoupper('ENREGISTREMENT DU RESPONSABLE DU COMITE DE RÉCEPTION')) }}</div>

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
                    <form method="POST" action="{{ route('comite_receptions.store') }}">
                        @csrf
                        <div class="row">

                        <div class="col-md-12">
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label class="label" for="mle">Matricule <sup style="color: red">*</sup></label>
                                    <input list="list_agent" onkeyup="editAgent()" id="mle" type="text" class="form-control @error('mle') is-invalid @enderror" name="mle" value="{{ old('mle') }}" required autocomplete="off" autofocus>
    
                                    @error('mle')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="label" for="nom_prenoms">Nom & Prénom(s)</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" id="nom_prenoms" type="text" class="form-control @error('nom_prenoms') is-invalid @enderror" name="nom_prenoms" value="{{ old('nom_prenoms') }}" required autocomplete="nom_prenoms" autofocus>
    
                                    @error('nom_prenoms')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="label" for="nom_structure">Structure</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" id="nom_structure" type="text" class="form-control @error('nom_structure') is-invalid @enderror" name="nom_structure" value="{{ old('nom_structure') }}" required autocomplete="nom_structure" autofocus>
    
                                    @error('nom_structure')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
    
                            </div>  
                            <div class="form-group row">
                                <div class="col-md-2">
                                    <label class="label" for="num_bc">N° Bon de commande <sup style="color: red">*</sup></label>
                                    <input onkeyup="editDemandeAchat()" list="list_bc" id="num_bc" type="text" class="form-control @error('num_bc') is-invalid @enderror" name="num_bc" value="{{ old('num_bc') }}" required autocomplete="off" autofocus>
    
                                    @error('num_bc')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="label" for="intitule">Intitulé </label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" id="intitule" type="text" class="form-control @error('intitule') is-invalid @enderror" name="intitule" value="{{ old('intitule') }}" required autocomplete="intitule" autofocus>
    
                                    @error('intitule')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="label" for="design_dep">Dépôt</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" id="design_dep" type="text" class="form-control @error('design_dep') is-invalid @enderror" name="design_dep" value="{{ old('design_dep') }}" required autocomplete="design_dep" autofocus>
    
                                    @error('design_dep')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div> 

                            <div class="form-group row" style="float: right">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success">Enregistrer</button>
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
    editAgent = function(){

        const value = document.getElementById('mle').value;

        if(value != ''){
            const block = value.split('->');
            var mle = block[0];
            var nom_prenoms = block[1];
            var nom_structure = block[2];
            
        }else{
            mle = "";
            nom_prenoms = "";
            nom_structure = "";
        }
        
        document.getElementById('mle').value = mle;

        if (nom_prenoms !== undefined) {
            document.getElementById('nom_prenoms').value = nom_prenoms;
        }else{
            document.getElementById('nom_prenoms').value = '';
        }

        if (nom_structure !== undefined) {
            document.getElementById('nom_structure').value = nom_structure;
        }else{
            document.getElementById('nom_structure').value = '';
        }
        
        

    }

    editDemandeAchat = function(){

        const value = document.getElementById('num_bc').value;

        if(value != ''){
            const block = value.split('->');
            var num_bc = block[0];
            var intitule = block[1];
            var design_dep = block[2];
            
        }else{
            num_bc = "";
            intitule = "";
            design_dep = "";
        }

        document.getElementById('num_bc').value = num_bc;

        if (intitule !== undefined) {
            document.getElementById('intitule').value = intitule;
        }else{
            document.getElementById('intitule').value = '';
        }

        if (design_dep !== undefined) {
            document.getElementById('design_dep').value = design_dep;
        }else{
            document.getElementById('design_dep').value = '';
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
