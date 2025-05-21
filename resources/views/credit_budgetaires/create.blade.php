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
<br>
<div class="container" style="color:black">

    <datalist id="list_ref_depot">
        @foreach($depots as $depot)
            <option value="{{ $depot->ref_depot ?? '' }}">{{ $depot->ref_depot ?? '' }} : {{ $depot->design_dep ?? '' }}</option>
        @endforeach
    </datalist>

    <datalist id="list_ref_fam">
        @foreach($familles as $famille)
            <option value="{{ $famille->ref_fam ?? '' }}">{{ $famille->ref_fam ?? '' }} : {{ $famille->design_fam ?? '' }}</option>
        @endforeach
    </datalist>

    <datalist id="list_code_structure">
        @foreach($structures as $structure)
            <option value="{{ $structure->code_structure ?? '' }}">{{ $structure->code_structure ?? '' }} : {{ $structure->nom_structure ?? '' }}</option>
        @endforeach
    </datalist>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('ENREGISTREMENT DU CREDIT BUDGETAIRE') }}</div>
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
                <form method="POST" action="{{ route('credit_budgetaires.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-2">
                        <label class="label">Ref. dépot <span style="color: red"><sup> *</sup></span></label>
                        <input autocomplete="off" list="list_ref_depot" class="form-control form-control-sm @error('ref_depot') is-invalid @enderror" name="ref_depot" type="text" value="{{ old('ref_depot') }}">

                        @error('ref_depot')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="label">Code structure <span style="color: red"><sup> *</sup></span></label>
                        <input autocomplete="off" list="list_code_structure" class="form-control form-control-sm @error('code_structure') is-invalid @enderror" name="code_structure" type="text" value="{{ old('code_structure') }}">

                        @error('code_structure')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="label">Ref. famille <span style="color: red"><sup> *</sup></span></label>
                        <input autocomplete="off" list="list_ref_fam" class="form-control form-control-sm @error('ref_fam') is-invalid @enderror" name="ref_fam" type="text" value="{{ old('ref_fam') }}">

                        @error('ref_fam')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <div class="col-md-2">
                        <label class="label">Exercice <span style="color: red"><sup> *</sup></span></label>
                        <input autocomplete="off" class="form-control form-control-sm @error('exercice') is-invalid @enderror" name="exercice" type="text" value="{{ old('exercice') }}">

                        @error('exercice')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        </div>
                        <div class="col-md-2">
                            <label class="label">Crédit <span style="color: red"><sup> *</sup></span></label>
                            <input autocomplete="off" class="form-control form-control-sm @error('credit') is-invalid @enderror" name="credit" type="text" value="{{ old('credit') }}">

                            @error('credit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <div class="col-md-2">
                            <button style="margin-top: 20px; float: right;" class="btn btn-sm btn-success" name="enregistrer" type="submit">Enregistrer</button>
                        </div>
                    </div>
                </form>

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table id="example1" class="table table-striped bg-white" style="width: 100%">
                            <thead>
                                <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">#</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">REF. DEPOT</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">DEPOT</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">REF. FAMILLE</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">FAMILLE</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">CODE STRUCTURE</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">STRUCTURE</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">EXERCICE</th>
                                    <th style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">CREDIT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach($credit_budgetaires as $credit_budgetaire)
                                    <tr>
                                        <td style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">{{ $i ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->ref_depot ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:left; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->design_dep ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->ref_fam ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:left; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->design_fam ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->code_structure ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:left; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->nom_structure ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:center; width: 1px; white-space: nowrap;">{{ $credit_budgetaire->exercice ?? '' }}</td>
                                        <td style="vertical-align: middle; text-align:right; width: 1px; white-space: nowrap;">{{ strrev(wordwrap(strrev($credit_budgetaire->credit ?? ''), 3, ' ', true)) }}
                                            
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
            
</div>
<script>


    
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
