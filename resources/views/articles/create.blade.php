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
<div class="container">

    <datalist id="ref_fam">
        @foreach($familles as $famille)
        <option value="{{ $famille->ref_fam }}->{{ $famille->design_fam }}->{{ $famille->id }}">{{ $famille->design_fam }}</option>
        @endforeach
    </datalist>

    <datalist id="list_type_article">
        @foreach($type_articles as $type_article)
            <option>{{ $type_article->design_type }}</option>
        @endforeach
    </datalist>

    <datalist id="list_unite">
        @foreach($unites as $unite)
            <option>{{ $unite->unite }}</option>
        @endforeach
    </datalist>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('ENREGISTREMENT D\'UN ARTICLE') }}</div>
                <div class="card-body">
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
                <form method="POST" action="{{ route('articles.store') }}">
                @csrf
                <table style="width: 100%">
                    <tbody>
                    <tr>
                    <td>


                    <div class="row mb-5">
                    <div class="col-md-12">                    

                        <div class="form-group row">  

                            <div class="col-md-2">
                                <label class="label" for="ref_fam">Référence famille <sup style="color: red">*</sup></label>
                                <input autocomplete="off" onkeyup="editFamille(this)" list="ref_fam" id="ref_fam" type="text" class="form-control @error('ref_fam') is-invalid @enderror" name="ref_fam" value="{{ old('ref_fam') ?? $famille_article->ref_fam ?? '' }}"  autocomplete="ref_fam">

                                @error('ref_fam')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="label" for="design_fam">Famille <sup style="color: red">*</sup></label>
                                <input onfocus="this.blur()" style="background-color: #e9ecef" list="design_fam" autocomplete="off" id="design_fam" type="text" class="form-control @error('design_fam') is-invalid @enderror" name="design_fam" value="{{ old('design_fam') ?? $famille_article->design_fam ?? '' }}"  autocomplete="design_fam">

                                @error('design_fam')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="label" for="design_type">Catégorie <sup style="color: red">*</sup></label>
                                <input list="list_type_article" id="design_type" type="text" class="form-control @error('design_type') is-invalid @enderror" name="design_type" value="{{ old('design_type') }}"  autocomplete="off">

                                @error('design_type')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                        </div>

                        <div class="form-group row">  

                            <div class="col-md-2">
                                <label class="label" for="ref_articles">Référence article <sup style="color: red">*</sup></label>
                                <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" onkeyup="editFamille(this)" list="ref_articles" id="ref_articles" type="text" class="form-control @error('ref_articles') is-invalid @enderror" name="ref_articles" value="{{ old('ref_articles') ?? $ref_articles ?? '' }}"  autocomplete="ref_articles">

                                @error('ref_articles')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="label" for="design_article">Désignation article <sup style="color: red">*</sup></label>
                                {{-- <input list="design_article" autocomplete="off" id="design_article" type="text" class="form-control @error('design_article') is-invalid @enderror" name="design_article" value="{{ old('design_article') }}"  autocomplete="design_article"> --}}

                                <textarea list="design_article" autocomplete="off" id="design_article" type="text" class="form-control @error('design_article') is-invalid @enderror" name="design_article">{{ old('design_article') ?? '' }}</textarea>

                                @error('design_article')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="label" for="unite">Unité</label>
                                <input list="list_unite" autocomplete="off" id="unite" type="text" class="form-control @error('unite') is-invalid @enderror" name="unite" value="{{ old('unite') }}">

                                @error('unite')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="label" for="flag_actif">Statut </label><br>
                                <input style="margin-top: 8px;" list="flag_actif" id="flag_actif" type="checkbox" name="flag_actif">

                                @error('flag_actif')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-2"><br>
                                <button style="margin-top: 8px;" type="submit" class="btn btn-success">{{ __('Enregistrer') }}</button>
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
<script>

editExercice = function(e){

const saisie = document.getElementById('annee').value;
const opts = document.getElementById('annee_list').childNodes;


for (var i = 0; i < opts.length; i++) {
    if (opts[i].value === saisie) {

        if(saisie != ''){

            var annee = saisie;

        }else{
            annee = "";
        }

        if (annee === undefined) {

        }else{


            var url = window.location.href;            	

            document.location.replace(url+'&m='+annee);
        }

        break;
    }else{

    }
}

}



    editFamille = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('#ref_fam').val();
        if(value != ''){
            const block = value.split('->');
            var ref_fam = block[0];
            var design_fam = block[1];
            var id = block[2];
            
        }else{
            ref_fam = "";
            design_fam = "";
            id = "";
        }

        
        tr.find('#ref_fam').val(ref_fam);
        

        if (id === undefined) {
            
        }else if (id === '') {
            
        }else{
            tr.find('#design_fam').val(design_fam);
            document.location.replace('/articles/creates/'+id);
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
