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

    @if(isset($articles))
        <datalist id="liste_article">
            @foreach($articles as $article)
                <option value="{{ $article->ref_articles }}->{{ $article->design_article }}">{{ $article->design_article }}</option>
            @endforeach
        </datalist>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ mb_strtoupper($entete ?? '') }}</div>
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
                <form method="POST" action="{{ route('mouvements.update') }}">
                @csrf
                <table style="width: 100%">
                    <tbody>
                    <tr>
                    <td>


                    <div class="row mb-5">
                    <div class="col-md-12">                    

                        <div class="form-group row">  

                            <div class="col-md-2">
                                <label class="label" for="ref_articles">Référence article</label>
                                <input onfocus="this.blur()" style="display: none" name="mouvements_id" value="{{ $mouvement->id ?? ''}}">
                                <input autocomplete="off" style="background-color: #e9ecef" onfocus="this.blur()" id="ref_articles" type="text" class="form-control @error('ref_articles') is-invalid @enderror" name="ref_articles" value="{{ old('ref_articles') ?? $mouvement->ref_articles ?? '' }}"  autocomplete="ref_articles">

                                @error('ref_articles')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="label" for="design_article">Désignation article</label>
                                <input onfocus="this.blur()" style="background-color: #e9ecef" list="design_article" autocomplete="off" id="design_article" type="text" class="form-control @error('design_article') is-invalid @enderror" name="design_article" value="{{ old('design_article') ?? $mouvement->design_article ?? '' }}"  autocomplete="design_article">

                                @error('design_article')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-1">
                                <label class="label" for="qte">Quantité <sup style="color: red">*</sup></label>
                                <input onkeypress="validate(event)" onkeyup="editQte(this)" list="list_type_article" id="qte" type="text" class="form-control @error('qte') is-invalid @enderror" name="qte" value="{{ old('qte') ?? $mouvement->qte ?? '' }}"  autocomplete="off">

                                @error('qte')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="label" for="prixu">Prix unitaire <sup style="color: red">*</sup></label>
                                <input onkeypress="validate(event)" onkeyup="editPrixU(this)" list="list_type_article" id="prixu" type="text" class="form-control @error('prixu') is-invalid @enderror" name="prixu" value="{{ old('prixu') ?? $mouvement->prix_unit ?? '' }}"  autocomplete="off">

                                @error('prixu')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="label" for="date_entree">Date <sup style="color: red">*</sup></label>
                                <input id="date_entree" type="date" class="form-control @error('date_entree') is-invalid @enderror" name="date_entree" value="{{ old('date_entree') ?? date("Y-m-d",strtotime($mouvement->date_mouvement ?? '')) ?? date("Y-m-d") ?? '' }}"  autocomplete="off">

                                @error('date_entree')
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-1">
                                <button style="margin-top:25px" type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Faut-il modifier ?')">Modifier</button>
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
    editArticle = function(a){

        const value = document.getElementById("ref_articles").value;

        if(value != ''){

            const block = value.split('->');
            var ref_articles = block[0];
            var design_article = block[1];
            
        }else{

            ref_articles = "";
            design_article = "";

        }
        document.getElementById("design_article").value = design_article;
        if(design_article === undefined){
            document.getElementById("design_article").value = "";
        }

        document.getElementById("ref_articles").value = ref_articles;
        if(ref_articles === undefined){
            document.getElementById("ref_articles").value = ref_articles;
        }
        
    }

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
        
    editQte = function(e){
        
        var qte = document.getElementById("qte").value;

        qte = qte.trim();
        qte = reverseFormatNumber(qte,'fr');

        qte = qte * 1;

        if (qte <= 0) {
            document.getElementById("qte").value = "";
        }else{
            
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            
            document.getElementById("qte").value = int3.format(qte);
        }            
    }

    editPrixU = function(e){
        
        var prixu = document.getElementById("prixu").value;

        prixu = prixu.trim();
        prixu = reverseFormatNumber(prixu,'fr');

        prixu = prixu * 1;

        if (prixu <= 0) {
            document.getElementById("prixu").value = "";
        }else{
            
            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            
            document.getElementById("prixu").value = int3.format(prixu);
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
