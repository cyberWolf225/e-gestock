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
<div class="container" style="font-size:10px;line-height: 10px;">
    <br>
    <datalist id="list_article">
        @foreach($articles as $article)
            <option value="{{ $article->ref_articles }}->{{ $article->design_article }}->{{ $article->cmup }}->{{ $article->id }}->{{ $article->qte }}">{{ $article->design_article }}</option>
        @endforeach
    </datalist>

    <datalist id="list_gestion">
        @foreach($gestions as $gestion)
            <option value="{{ $gestion->code_gestion }} - {{ $gestion->libelle_gestion }}">{{ $gestion->libelle_gestion }}</option>
        @endforeach
    </datalist>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ strtoupper($entete ?? '')  }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($requisitions->created_at)) }}</strong></span>
                    
                    
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
                    <form method="POST" action="{{ route('requisitions.update') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="label">N° Bon de commande</label>
                                    

                                    <input required onfocus="this.blur()" style="color:red; text-align:left; font-weight:bold" type="text" class="form-control griser" value="{{ $requisitions->num_bc ?? '' }}">

                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none; font-size:10px;" value="{{ $requisitions->num_bc ?? '' }}" required type="text" name="num_bc" class="form-control">

                                    <input name="requisitions_id" onfocus="this.blur()" style="background-color: #e9ecef; display: none ; font-size:10px;" value="{{ $requisitions->id ?? '' }}" required type="text" class="form-control">
                                    
                                    <input value="{{ $requisitions->intitule ?? '' }}" required type="text" name="intitule" class="form-control" placeholder="Intitulé de la réquisition" @if(isset($vue)) onfocus="this.blur()" style="font-size:10px; background-color: #e9ecef; font-weight:bold" @else style="font-size:10px;; font-weight:bold" @endif >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="label">Gestion</label>
                                    <input value="{{ $requisitions->code_gestion ?? '' }} {{ ' - '.$requisitions->libelle_gestion ?? '' }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion" onfocus="this.blur()" style="font-size:10px; background-color: #e9ecef; font-weight:bold" >
                                </div>
                            </div>
                        </div>
                        

                        <div class="panel panel-footer">
                            <table class="table table-striped" id="myTable" style="width:100%">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:12%; text-align:left">RÉF. ARTICLE</th>
                                        <th style="width:40%; text-align:left">DÉSIGNATION ARTICLE</th>
                                        <th style="width:10%; text-align:center; display:none; text-align:center">PRIX U</th>
                                        <th style="width:10%; text-align:center">QUANTITÉ DEMANDÉE<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:13%; text-align:center; display:none">COÛT</th>
                                        
                                        @if(!isset($vue))
                                            <th style="text-align:center; width:1%"><a onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                            </svg></a>
                                            </th>
                                        @endif
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandes as $demande)
                                    <?php $montant = $demande->qte * $demande->cmup;
                                            $montant = strrev(wordwrap(strrev($montant), 3, ' ', true));
                                            $demande->qte = strrev(wordwrap(strrev($demande->qte), 3, ' ', true));
                                            $demande->cmup = strrev(wordwrap(strrev($demande->cmup), 3, ' ', true));
                                    ?>
                                    <tr>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input autocomplete="off" required value="{{ $demande->ref_articles ?? '' }}" list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles"  @if(isset($vue)) onfocus="this.blur()" style="font-size:10px; text-align:left; background-color: #e9ecef" @else style="font-size:10px; text-align:left;" @endif >

                                            <input style="display: none; font-size:10px;" value="{{ $demande->id ?? '' }}" type="text" id="demandes_id" name="demandes_id[]" class="form-control demandes_id">

                                            <input style="display:none ; font-size:10px;" value="{{ $demande->magasin_stocks_id ?? '' }}" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id">

                                        </td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input onfocus="this.blur()" style="background-color: transparent; border-color: transparent;font-size:10px;" required value="{{ $demande->design_article ?? '' }}" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article">
                                        </td>
                                        <td style="display: none; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input onfocus="this.blur()" required value="{{ $demande->cmup ?? '' }}" style="background-color: #e9ecef; text-align: right; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup">
                                        </td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input value="{{ $demande->qte_stock ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center" name="qte_stock[]" class="form-control qte_stock">

                                            <input autocomplete="off" required value="{{ $demande->qte ?? '' }}" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte" @if(isset($vue)) onfocus="this.blur()" style="text-align: center; font-size:10px; background-color: #e9ecef" @else style="text-align: center; font-size:10px;" @endif >
                                        </td>
                                        <td style="display: none; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input onfocus="this.blur()" required value="{{ $montant ?? '' }}" style="background-color: #e9ecef; text-align: right;font-size:10px;" type="text" onkeypress="validate(event)" name="montant[]" class="form-control montant">
                                        </td>
                                        @if(!isset($vue))
                                            <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                            <a onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                            </svg></a>
                                            </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    <tr style="display: none">
                                        <td></td>
                                        <td></td>
                                        <td style="display: none"></td>
                                        <td></td>
                                        <td style="display: none"></td>
                                        <td></td>
                                    </tr>
                                    
                                </tbody>
                                
                            </table>
                            <br>

                            <table style="width: 100%">
                                <tr>
                                    <td colspan="4" style="border-bottom: none">
                                        <span style="font-weight: bold">Dernier commentaire du dossier </span> : écrit par <span style="color: brown; margin-left:3px;"> {{ $nom_prenoms_commentaire ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $profil_commentaire ?? '' }} </span>)

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

                                    <td colspan="5" style="border: none;vertical-align: middle; text-align:right">

                                        <div class="row justify-content-center">
                                        
                                            <div class="col-md-9">
                                                <textarea class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2" style="width: 100%; resize:none">{{ old('commentaire') ?? '' }}</textarea> 

                                                @error('commentaire')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3" style="margin-top:0px">
                                                @if(isset($value_bouton2))
                                                    <button onclick="return confirm('Êtes-vous sûr d\'annuler votre demande ?')" style="margin-top: 0px; font-size:10px; width:70px" type="submit" name="submit" value="{{ $value_bouton2 }}" class="btn btn-danger">
                                                    {{ $bouton2 }}
                                                    </button> 
                                                @endif
                                                
                                                @if(isset($value_bouton))
                                                    <button onclick="return confirm('Faut-il enregistrer ?')" style="margin-top: 0px; font-size:10px; width:70px" type="submit" name="submit" value="{{ $value_bouton }}" class="btn btn-success">
                                                    {{ $bouton }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

   // Empecher la saisie de lettre
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

    // annuler le séparateur de millier

    function reverseFormatNumber(val,locale){
        var group = new Intl.NumberFormat(locale).format(1111).replace(/1/g, '');
        var decimal = new Intl.NumberFormat(locale).format(1.1).replace(/1/g, '');
        var reversedVal = val.replace(new RegExp('\\' + group, 'g'), '');
        reversedVal = reversedVal.replace(new RegExp('\\' + decimal, 'g'), '.');
        return Number.isNaN(reversedVal)?0:reversedVal;
    }


    
        editMontant = function(e){
            var tr=$(e).parents("tr");
            
            var qte=tr.find('.qte').val();
            qte = qte.trim();
            qte = qte.replace(' ','');
            qte = reverseFormatNumber(qte,'fr');
            qte = qte.replace(' ','');
            qte = qte * 1;

            var qte_stock=tr.find('.qte_stock').val();
            qte_stock = qte_stock.trim();
            qte_stock = qte_stock.replace(' ','');
            qte_stock = reverseFormatNumber(qte_stock,'fr');
            qte_stock = qte_stock.replace(' ','');
            qte_stock = qte_stock * 1;


            var cmup=tr.find('.cmup').val();
            cmup = cmup.trim();
            cmup = cmup.replace(' ','');
            cmup = reverseFormatNumber(cmup,'fr');
            cmup = cmup.replace(' ','');
            /*
            if(qte_stock < qte) {
                
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité demandée ne peut être supérieure à la quantité disponible en stock... (Quantité disponible en stock : '+ qte_stock +' )',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });


                qte = "";
            } */

            var montant=(cmup*qte);


            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte').val(int3.format(qte));
            tr.find('.montant').val(int3.format(montant));
            
        }
            editDesign = function(a){

            var tr=$(a).parents("tr");
            const value=tr.find('.ref_articles').val();
            const opts = document.getElementById('list_article').childNodes;

        

            
            for (var i = 0; i < opts.length; i++) {
            if (opts[i].value === value) {
                
                if(value != ''){
                const block = value.split('->');
                var ref_articles = block[0];
                var design_article = block[1];
                var cmup = block[2];
                var magasin_stocks_id = block[3];
                var qte_stock = block[4];
                
                }else{
                    ref_articles = "";
                    design_article = "";
                    cmup = 0;
                    magasin_stocks_id = "";
                    qte_stock = "";
                }

                tr.find('.magasin_stocks_id').val(magasin_stocks_id);
                tr.find('.ref_articles').val(ref_articles);
                tr.find('.design_article').val(design_article);
                tr.find('.qte_stock').val(qte_stock);

                var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
                
                tr.find('.cmup').val(int3.format(cmup));
                tr.find('.qte').val(0);
                tr.find('.montant').val(0);

                break;
            }else{
                tr.find('.magasin_stocks_id').val("");
                //tr.find('.ref_articles').val("");
                tr.find('.design_article').val("");
                tr.find('.cmup').val("");
                tr.find('.qte_stock').val("");
                tr.find('.qte').val(0);
                tr.find('.montant').val(0);
            }
            }
        }



    
    
    function myCreateFunction() {
      var table = document.getElementById("myTable");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;
        
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            cell1.innerHTML = '<td><input autocomplete="off" required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px; text-align:left"><input style="display: none; " type="text" id="demandes_id" name="demandes_id[]" class="form-control demandes_id" style="font-size:10px;"><input style="display: none; font-size:10px;" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id"></td>';
            cell2.innerHTML = '<td><input onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px;" required type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article"></td>';
            cell3.innerHTML = '<td><input autocomplete="off" onfocus="this.blur()" required style="background-color: #e9ecef; text-align: right; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup"></td>';
            cell4.innerHTML = '<td><input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center" name="qte_stock[]" class="form-control qte_stock"><input required style="text-align: center; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte" autocomplete="off"></td>';
            cell5.innerHTML = '<td><input onfocus="this.blur()" required style="background-color: #e9ecef; text-align: right; font-size:10px;" onkeypress="validate(event)" type="text" name="montant[]" class="form-control montant"></td>';
            cell6.innerHTML = '<td style="vertical-align: middle; text-align:center"><a onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';

            cell3.style.display = "none";
            cell5.style.display = "none";

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
            
        
      
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<4){
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
