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
<div class="container" style="font-size: 10px; line-height:10px">
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

    <datalist id="list_structure">
        @foreach($structures as $structure)
            <option>{{ $structure->nom_structure }}</option>
        @endforeach
    </datalist>

    <datalist id="list_departement">
        @foreach($departements as $departement)
            <option>{{ $departement->nom_departement }}</option>
        @endforeach
    </datalist>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('DEMANDE D\'ARTICLE') }} 
                    <span style="float: right; margin-right: 20px;">DATE SAISIE : <strong>{{ date("d/m/Y") }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $exercice->exercice ?? '' }}</strong></span>
                    
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
                    <form method="POST" action="{{ route('requisitions.store') }}">
                        @csrf
                        @if(isset($ref_depot))
                            @if($ref_depot === 83)
                                @if( $type_profils_name === "Responsable des stocks" OR $type_profils_name === "Gestionnaire des stocks")
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="label">Structure bénéficiaire <span style="color: red"><sup> *</sup></span></label>
                                                <input list="list_structure" autofocus autocomplete="off" title="Entrez la structure bénéficiaire"  type="text" name="structure_demande" class="form-control @error('structure_demande') is-invalid @enderror" required placeholder="Saisissez la structure bénéficiaire" style="font-size: 10px;" value="{{ old('structure_demande') ?? '' }}">
                                                @error('structure_demande')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="label">Département/Service bénéficiaire</label>
                                                <input list="list_departement" autofocus autocomplete="off" title="Entrez le département ou le service bénéficiaire"  type="text" name="departement_demande" class="form-control @error('departement_demande') is-invalid @enderror" placeholder="Saisissez le département ou le service bénéficiaire" style="font-size: 10px;" value="{{ old('departement_demande') ?? '' }}">
                                                @error('departement_demande')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="label">Matrucle du Pilote AEE<span style="color: red"><sup> *</sup></span></label>
                                                <input autofocus autocomplete="off" title="Entrez le matricule du pilote AEE"  type="text" name="mle_pilote_demande" class="form-control @error('mle_pilote_demande') is-invalid @enderror" required placeholder="Saisissez le matricule du pilote AEE" style="font-size: 10px;" value="{{ old('mle_pilote_demande') ?? '' }}">
                                                @error('mle_pilote_demande')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="label">Date demande<span style="color: red"><sup> *</sup></span></label>
                                                <input autofocus autocomplete="off" title="Entrez la date de la demande"  type="date" name="date_demande" class="form-control @error('date_demande') is-invalid @enderror" required placeholder="Saisissez la date de la demande" style="font-size: 10px;" value="{{ old('date_demande') ?? '' }}">
                                                @error('date_demande')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            @endif
                        @endif
                        

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande <span style="color: red"><sup> *</sup></span></label>
                                    <input autofocus autocomplete="off" title="Entrez l'intitulé de votre demande"  type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror" required placeholder="Saisissez l'intitulé de votre demande" style="font-size: 10px;" value="{{ old('intitule') ?? '' }}">
                                    @error('intitule')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="label">Gestion</label>
                                    <input onfocus="this.blur()" autocomplete="off" title="Sélectionnez la gestion de votre demande" type="text" name="gestion" class="form-control @error('gestion') is-invalid @enderror" required placeholder="Saisissez la gestion" style="font-size: 10px; background-color: #e9ecef;" value="{{ old('gestion') ?? $gestion_defaults ?? '' }}">

                                    @error('gestion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                            </div>
                        </div>
                        

                        <div class="panel panel-footer"> <!--table-bordered-->
                            
                            <span>
                                <a style="cursor: pointer; color:cadetblue; font-style:italic; font-weight:bold" onclick="myCreateFunction()" class="addRow">
                                (Ajouter un nouvel article)
                                </a>
                            </span>
                            <br/>
                            <br/>
                            <table class="table  table-striped" id="myTable" style="width:100%; border-collapse: collapse; padding: 0; margin: 0;">
                                <thead>
                                    <tr style="background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:12%; text-align:center">RÉF. ARTICLE<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:40%; text-align:center">DÉSIGNATION ARTICLE</th>
                                        <th style="width:10%; text-align:center; display:none; text-align:center">PRIX U</th>
                                        <th style="width:10%; text-align:center">QUANTITÉ<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:13%; text-align:center; display:none">COÛT</th>
                                        <th style="text-align:center; width:1%">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-collapse: collapse; padding: 0; margin: 0;">
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input autocomplete="off" title="sélectionnez l'article en saisissant soit la référence ou la désignation" required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px; text-align:left;">
                                            <input  onfocus="this.blur()" autocomplete="off" style="display: none; font-size:10px;background-color: #e9ecef;" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id">
                                        </td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input autocomplete="off" title="Désignation de l'article sélectionné" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; text-shadow: 2px 5px 5px white ;" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article">
                                        </td>
                                        <td style="display: none; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input autocomplete="off" title="Prix unitaire de l'article sélectionné" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup">
                                        </td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center" name="qte_stock[]" class="form-control qte_stock">

                                            <input autocomplete="off" title="Entrez la quantité d'article souhaitée" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center; font-size:10px;" name="qte[]" class="form-control qte">

                                        </td>
                                        <td style="display:none; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input autocomplete="off" title="Montant de la demande de cet article" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" onkeypress="validate(event)" type="text" name="montant[]" class="form-control montant">
                                        </td>
                                        <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                            <a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)"  class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                              </svg></a>
                                        </td>
                                    </tr>
                                    <tr style="display: none">
                                        <td></td>
                                        <td></td>
                                        <td style="display: none"></td>
                                        <td></td>
                                        <td style="display: none"></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    
                                </tfoot>
                            </table>
                            <table style="width: 100%; margin-top:10px;">
                                <tr>
                                    <td style="border:  ; vertical-align:middle; width:90%">
                                        
                                        <textarea class="form-control" name="commentaire" rows="2" style="width: 100%; resize:none" placeholder="Saisissez un commentaire"></textarea>
                                    
                                    </td>

                                    <td  style="border:  ; vertical-align:middle; width:10%">
                                    
                                        <button title="Enregistrer votre demande"  type="submit" class="btn btn-success" style="font-size:10px; margin-top:1px; vertical-align:middle">
                                        Enregistrer
                                        </button>

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
            cmup = cmup * 1;
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
                tr.find('.qte_stock').val("");
                tr.find('.cmup').val("");
                tr.find('.qte').val(0);
                tr.find('.montant').val(0);
            }
            }
        }



    
    
    function myCreateFunction() {
      var table = document.getElementById("myTable");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;
        //if(nbre_rows<6){
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);

            cell1.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="sélectionnez l\'article en saisissant soit la référence ou la désignation" required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px; text-align:left"><input autocomplete="off" style="display: none; font-size:10px;" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id"></td>';
            cell2.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Désignation de l\'article sélectionné" required type="text" onkeyup="editMontant(this)" onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px;" id="design_article" name="design_article[]" class="form-control design_article"></td>';
            cell3.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Prix unitaire de l\'article sélectionné" required type="text" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup"></td>';
            cell4.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_stock[]" class="form-control qte_stock"><input autocomplete="off" title="Entrez la quantité d\'article souhaitée" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center; font-size:10px;" name="qte[]" class="form-control qte"></td>';
            cell5.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Montant de la demande de cet article" required type="text" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" name="montant[]" onkeypress="validate(event)" class="form-control montant"></td>';
            cell6.innerHTML = '<td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;"><a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)"  class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
        
        //}

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
