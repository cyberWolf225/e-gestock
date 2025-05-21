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
    <datalist id="ref_depot">
        @foreach($depots as $depot)
        <?php

            $info_depot = DB::table('users as u')
                ->join('profils as p','p.users_id','=','u.id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('depots as d','d.ref_depot','=','st.ref_depot')
                ->join('villes as v','v.code_ville','=','d.code_ville')
                ->join('statut_agent_sections as sas','sas.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsas','tsas.id','=','sas.type_statut_agent_sections_id')
                ->where('tp.name','Responsable des stocks')
                ->where('tsas.libelle','Activé')
                ->where('p.flag_actif',1)
                ->orderByDesc('p.updated_at')
                ->select('d.id','v.nom_ville','a.nom_prenoms','a.mle','d.tel_dep','d.adr_dep','d.principal','d.ref_depot','d.design_dep')
                ->first();
            if ($info_depot!=null) {
                $nom_ville = $info_depot->nom_ville;
                $nom_prenoms = $info_depot->mle.' - '.$info_depot->nom_prenoms;
            
        ?>
            <option value="{{ $depot->ref_depot }}->{{ $depot->design_dep }}->{{ $depot->tel_dep }}->{{ $depot->adr_dep }}->{{ $nom_ville ?? '' }}->{{ $nom_prenoms ?? '' }}->{{ $depot->principal }}->{{ $depot->code_ville }}">{{ $depot->design_dep }}</option>

        <?php
            }
        ?>
        @endforeach
    </datalist>

    <datalist id="profils_id">
        @foreach($agents as $agent)
            <option value="{{ $agent->mle }} - {{ $agent->nom_prenoms }}">{{ $agent->nom_prenoms }}</option>
        @endforeach
    </datalist>

    <datalist id="ville_dep">
        @foreach($liste_villes as $liste_ville)
            <option>{{ $liste_ville->nom_ville }}</option>
        @endforeach
    </datalist>

    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(Session::has('success'))
              <div class="alert alert-success">
                  {{ Session::get('success') }}
              </div>
          @endif
          @if(Session::has('error'))
              <div class="alert alert-danger">
                  {{ Session::get('error') }}
              </div>
          @endif
            <div class="card">
                <div class="card-header entete-table">{{ __('Enregistrer un Depôt') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('depots.store') }}">
                        @csrf
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td>

                                    
                         <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group row">  
        
                                        <div class="col-md-2">
                                            <label for="ref_depot">Réf. depôt <sup style="color: red">*</sup></label>

                                            {{-- onkeyup="editFamille(this)" list="ref_depot" --}}
                                            <input autocomplete="off" id="ref_depot" type="text" class="form-control @error('ref_depot') is-invalid @enderror" name="ref_depot" value="{{ old('ref_depot') }}">
            
                                            @error('ref_depot')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="design_dep">Désignation depôt <sup style="color: red">*</sup></label>
                                            <input autocomplete="off" id="design_dep" type="text" class="form-control @error('design_dep') is-invalid @enderror" name="design_dep" value="{{ old('design_dep') }}">
            
                                            @error('design_dep')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label for="principal">Depôt Principal <sup style="color: red">*</sup></label>
                                            <br>
                                            <span>
                                                <label for="principal0" style="margin-top: 10px">Non</label>  <input autocomplete="off" list="principal" id="principal0" type="radio" name="principal" value="0" checked style="margin-left: 10px">
                                            </span>
                                            <span style="margin-left: 30px;">
                                                <label for="principal1" style="margin-top: 10px">Oui</label> <input autocomplete="off" list="principal" id="principal1" type="radio" name="principal" value="1" style="margin-left: 10px">
                                            </span>
                                            
                                        </div>

                                        <div class="col-md-3">
                                            <label for="tel_dep">Téléphone <sup style="color: red">*</sup></label>
                                            <input autocomplete="off" list="tel_dep" id="tel_dep" type="text" class="form-control @error('tel_dep') is-invalid @enderror" name="tel_dep" value="{{ old('tel_dep') }}">
            
                                            @error('tel_dep')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="adr_dep">Adresse</label>
                                            <input autocomplete="off" list="adr_dep" id="adr_dep" type="text" class="form-control @error('adr_dep') is-invalid @enderror" name="adr_dep" value="{{ old('adr_dep') }}">
            
                                            @error('adr_dep')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
    
                                        <div class="col-md-4">
                                            <label for="ville_dep">Ville <sup style="color: red">*</sup></label>
                                            <input autocomplete="off" list="ville_dep" id="ville_dep" type="text" class="form-control @error('ville_dep') is-invalid @enderror" name="ville_dep" value="{{ old('ville_dep') }}">
            
                                            @error('ville_dep')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
    
                                        <div class="col-md-4">
                                            <label for="profils_id">Responsable <sup style="color: red">*</sup></label>
                                            <input autocomplete="off" list="profils_id" id="profils_id" type="text" class="form-control @error('profils_id') is-invalid @enderror" name="profils_id" value="{{ old('profils_id') }}">
            
                                            @error('profils_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group row" style="text-align: right">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success">
                                                {{ __('Enregistrer') }}
                                            </button>
                                        </div>
                                    </div>
                        </div>
                        </div>

                        
                    </td>
                </tr>
            </tbody>
        </table>
                    </form>

                </div>
                
            </div>
        </div>
    </div>
</div>
<script>
    editFamille = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('#ref_depot').val();
            if(value != ''){
                const block = value.split('->');
                var ref_depot = block[0];
                var design_dep = block[1];
                var tel_dep = block[2];
                var adr_dep = block[3];
                var ville_dep = block[4];
                var profils_id = block[5];
                var principal = block[6];
                
            }else{
                ref_depot = "";
                design_dep = "";
                adr_dep = "";
                tel_dep = "";
                ville_dep = "";
                profils_id ="";
                principal ="";
            }
            
            tr.find('#ref_depot').val(ref_depot);
            tr.find('#design_dep').val(design_dep);
            tr.find('#tel_dep').val(tel_dep);
            tr.find('#adr_dep').val(adr_dep);
            tr.find('#ville_dep').val(ville_dep);
            tr.find('#profils_id').val(profils_id);
            tr.find('#principal').val(principal);
            
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
