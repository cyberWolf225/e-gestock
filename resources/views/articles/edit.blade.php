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
    <datalist id="design_fam">
        @foreach($familles as $famille)
            <option value="{{ $famille->design_fam }}">{{ $famille->ref_fam }} - {{ $famille->design_fam }}</option>
        @endforeach
    </datalist>
    <datalist id="unite">
        @foreach($unites as $unite)
            <option value="{{ $unite->unite }}">{{ $unite->code_unite }} - {{ $unite->unite }}</option>
        @endforeach
    </datalist>
    <datalist id="taxe">
        @foreach($taxes as $taxe)
            <option value="{{ $taxe->nom_taxe }} - {{ $taxe->taux }}">{{ $taxe->taux }}</option>
        @endforeach
    </datalist>
    <datalist id="design_type">
        @foreach($type_articles as $type_article)
            <option value="{{ $type_article->design_type }}">{{ $type_article->design_type }}</option>
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
                <?php 
                
                $type_articles = DB::select("SELECT * FROM type_articles WHERE id = '".$article->type_articles_id."' ");
                    foreach ($type_articles as $type_article) {
                        $design_type = $type_article->design_type;
                    }
                    $id = $article->id;
                    $ref_articles = $article->ref_articles;
                    $design_article = $article->design_article;
                    $design_fam = $article->design_fam;
                    $unite = $article->unite;
                    $ref_fam = $article->ref_fam;

                    $taxe = null;

                    if (isset($article->ref_taxe)) {

                        $article_taux = DB::table('taxes')
                        ->where('ref_taxe',$articles->ref_taxe)
                        ->first();

                        if($article_taux != null){
                            $taxe = $article_taux->nom_taxe.' - '.$article_taux->taux;
                        }

                    }

                    

                    
                    
                ?>
                <div class="card-header entete-table">{{ __('MODIFICATION D\'UN ARTICLE') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('articles.update') }}">
                        @csrf
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td>

                                    
                         <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group row">  
        
                                        <div class="col-md-2">
                                            <label for="ref_articles">Référence Article <sup style="color: red">*</sup></label>
                                            
                                            <input onkeyup="editFamille(this)" onfocus="this.blur()" style="background-color: #e9ecef"list="ref_articles" id="ref_articles" type="text" class="form-control @error('ref_articles') is-invalid @enderror" name="ref_articles" value="{{ old('ref_articles') ?? $ref_articles ?? '' }}"  autocomplete="ref_articles">
                                            
                                            <input style="display: none" onfocus="this.blur()" list="id" id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ old('id') ?? $id ?? '' }}"  autocomplete="id">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef;display: none" onfocus="this.blur()" id="old_ref_fam" type="text" class="form-control @error('old_ref_fam') is-invalid @enderror" name="old_ref_fam" value="{{ old('old_ref_fam') ?? $ref_fam ?? '' }}">
            
                                            @error('ref_articles')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="design_article">Article <sup style="color: red">*</sup></label>
                                            <input id="design_article" type="text" class="form-control @error('design_article') is-invalid @enderror" name="design_article" value="{{ old('design_article') ?? $design_article ?? '' }}"  autocomplete="design_article">
            
                                            @error('design_article')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>


                                        <div class="col-md-4">
                                            <label for="design_type">Catégorie <sup style="color: red">*</sup></label>
                                            <input list="design_type" id="design_type" type="text" class="form-control @error('design_type') is-invalid @enderror" name="design_type" value="{{ old('design_type') ?? $design_type ?? '' }}"  autocomplete="off">
            
                                            @error('design_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        
                                    
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label for="design_fam">Famille</label>
                                            <input list="design_fam" id="design_fam" type="text" class="form-control @error('design_fam') is-invalid @enderror" name="design_fam" value="{{ old('design_fam') ?? $design_fam ?? '' }}"  autocomplete="design_fam">
            
                                            @error('design_fam')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
    
                                        <div class="col-md-4">
                                            <label for="unite">Unite <sup style="color: red">*</sup></label>
                                            <input list="unite" id="unite" type="text" class="form-control @error('unite') is-invalid @enderror" name="unite" value="{{ old('unite') ?? $unite ?? '' }}"  autocomplete="unite">
            
                                            @error('unite')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label class="label" for="flag_actif">Statut </label><br>
                                            <input style="margin-top: 8px;" list="flag_actif" id="flag_actif" type="checkbox" name="flag_actif" @if(isset($article->flag_actif)) @if($article->flag_actif === 1) checked @endif @endif>
            
                                            @error('flag_actif')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
    
                                        
                                    </div>
                        </div>
                        
                        
                    
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success">
                                    {{ __('Modifier') }}
                                </button>
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
        const value=tr.find('#ref_articles').val();
        if(value != ''){
            const block = value.split('->');
            var ref_articles = block[0];
            var design_article = block[1];
            var design_type = block[2];
            var design_fam = block[3];
            var unite = block[4];
            var taxe = block[5];
            
        }else{
            ref_articles = "";
            design_article = "";
            design_fam = "";
            design_type = "";
            unite = "";
            taxe ="";
        }
        
        tr.find('#ref_articles').val(ref_articles);
        tr.find('#design_article').val(design_article);
        tr.find('#design_type').val(design_type);
        tr.find('#design_fam').val(design_fam);
        tr.find('#unite').val(unite);
        tr.find('#taxe').val(taxe);
        
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
