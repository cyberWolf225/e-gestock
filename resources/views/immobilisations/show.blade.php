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

    <datalist id="list_agent">
        @foreach($agents as $agent) 
            <option value="{{ $agent->mle ?? '' }}->{{ $agent->nom_prenoms ?? '' }}->{{ 'Agent' }}">{{ $agent->nom_prenoms ?? '' }}</option>
        @endforeach

        @if($infoUserConnect != null) 
            <option value="{{ $infoUserConnect->code_structure ?? '' }}->{{ $infoUserConnect->nom_structure ?? '' }}->{{ 'Structure' }}">{{ $infoUserConnect->nom_structure ?? '' }}</option>
        @endif
    </datalist>

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
                <div class="card-header entete-table">{{ __('DEMANDE D\'EQUIPEMENT') }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($immobilisation->created_at ?? date("Y-m-d") )) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $immobilisation->exercice ?? date("Y") }}</strong></span>
                    
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
                        @csrf
                        <div class="row">
                            <div class="col-md-9"> 
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande </label>

                                    <input onfocus="this.blur()" autocomplete="off" type="text" name="id" class="form-control" required style="font-size: 10px; display:none" value="{{ old('id') ?? $immobilisation->id ?? '' }}">

                                    <input onfocus="this.blur()" autocomplete="off" title="Entrez l'intitulé de votre demande"  type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror" required placeholder="Saisissez l'intitulé de votre demande" style="font-size: 10px; background-color: #e9ecef;" value="{{ old('intitule') ?? $immobilisation->intitule ?? '' }}">
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
                        

                        <div class="panel panel-footer">
                            <table class="table table-bordered table-striped" id="myTable" style="width:100%; border-collapse: collapse; padding: 0; margin: 0;">
                                <thead>
                                    <tr style="background-color: #e9ecef; color: #7d7e8f">
                                        <th style="width:7%; text-align:center">BENEFICIAIRE</th>
                                        <th style="width:20%; text-align:left">DESCRIPTION DU BENEFICIAIRE</th>
                                        <th style="width:7%; text-align:left">RÉF.</th>
                                        <th style="width:20%; text-align:left">DÉSIGNATION DE L'ÉQUIPEMENT</th>
                                        <th style="width:10%; text-align:center; display:none; text-align:center">PRIX U</th>
                                        <th style="width:7%; text-align:center">QTÉ</th>
                                        <th style="width:13%; text-align:center; display:none">COÛT</th>
                                        <th style="width:1%; text-align:center">ÉCHANTILLON</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($immobilisations as $immobilisat)

                                        <?php 

                                            $echantillon = null;
                                            if (isset($immobilisat->echantillon)) {
                                                $echantillon = $immobilisat->echantillon;
                                            }

                                            $beneficiaire = null;
                                            $description_beneficiaire = null;
                                        
                                            if ($immobilisat->type_beneficiaire === 'Agent') {
                                                
                                                $user = DB::table('agents as a')
                                                ->where('a.mle',$immobilisat->beneficiaire)
                                                ->first();

                                                if ($user != null) {

                                                    $beneficiaire = $user->mle;
                                                    $description_beneficiaire = $user->nom_prenoms;
                                                    
                                                }

                                            }elseif ($immobilisat->type_beneficiaire === 'Structure') {
                                                $structure = DB::table('structures as s')
                                                ->where('s.code_structure',$immobilisat->beneficiaire)
                                                ->first();

                                                if ($structure != null) {

                                                    $beneficiaire = $structure->code_structure;
                                                    $description_beneficiaire = $structure->nom_structure;
                                                    
                                                }
                                            }
                                        
                                        ?>
                                    
                                        <tr style="color: #7d7e8f">
                                            <td style="text-align: center">

                                                {{ $beneficiaire ?? '' }}
                                            </td>

                                            <td>

                                                {{ $description_beneficiaire ?? '' }}

                                            </td>

                                            <td>

                                                {{ $immobilisat->ref_articles ?? '' }}

                                            </td>
                                            <td>
                                                
                                                {{ $immobilisat->design_article ?? '' }}

                                            </td>
                                            <td style="display: none; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input autocomplete="off" title="Prix unitaire de l'article sélectionné" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup" value="{{ $immobilisat->prixu ?? '' }}">
                                            </td>
                                            <td style="text-align: center">

                                                <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center" name="qte_stock[]" class="form-control qte_stock" value="{{ $immobilisat->qte_stock ?? '' }}">

                                                {{ $immobilisat->qte ?? '' }}

                                            </td>

                                            <td style="display:none; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input autocomplete="off" title="Montant de la demande de cet article" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" onkeypress="validate(event)" type="text" name="montant[]" class="form-control montant" value="{{ $immobilisat->montant ?? '' }}">
                                            </td>

                                            <td style="text-align: center; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">

                                                @if(isset($echantillon))
                                                

                                                    <!-- Modal -->

                                                        <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $immobilisat->ref_articles }}">
                                                                <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                </svg>
                                                        </a>
                                                        <div class="modal fade" id="exampleModalCenter{{ $immobilisat->ref_articles }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true"> 
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="text-align: center">
                                                                <h5 class="modal-title" id="exampleModalLongTitle">Échantillon  { <span style="color: orange">{{ $immobilisat->design_article ?? '' }}</span> } </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <img src='{{ asset('storage/'.$echantillon) }}' style='width:100%;'>
                                                                </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    <!-- Modal -->
                                                    
                                                @endif

                                            </td>                                        
                                            
                                        </tr>

                                    @endforeach

                                    <tr style="display: none">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="display: none"></td>
                                        <td></td>
                                        <td style="display: none"></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                </tbody>
                                <tfoot>
                                    
                                </tfoot>
                            </table>
                            <br>
                            <table style="width:100%" id="tablePiece">
                                

                                @if(count($piece_jointes)>0)
                                    @foreach($piece_jointes as $piece_jointe)
                                        <tr>
                                            <td style="border: none;vertical-align: middle; text-align:right">
                                                <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>  
                                                <a @if(isset($griser)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 ) }}">
                                                    <svg style="color: blue; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                      </svg>
                                                </a>
                                                <input @if(isset($griser)) disabled @endif style="display: none" onfocus="this.blur()" class="form-control" name="piece_jointes_id[{{ $piece_jointe->id ?? 0 }}]" value="{{ $piece_jointe->id ?? 0 }}">
                                                
                                            </td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <td></td>
                                        </tr>
                                @else
                                    <tr>
                                        
                                        <td>

                                        </td>
                                    </tr>
                                @endif

                                    
                            </table>
                            <table style="width: 100%; margin-top:10px;">
                                <tr>
                                    <td colspan="2" style="border-bottom: none">
                                        <span style="font-weight: bold">Dernier commentaire du dossier </span> : écrit par <span style="color: brown; margin-left:3px;"> {{ $statut_immobilisation->nom_prenoms ?? '' }} </span>&nbsp; (<span style="font-style: italic; color:grey"> {{ $statut_immobilisation->name ?? '' }} </span>)
                                    </td>
                                </tr>
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr>
                                    <td style="border:  ; vertical-align:middle; width:100%">
                                        
                                        <textarea onfocus="this.blur()" class="form-control" name="commentaire" rows="2" style="width: 100%; resize:none; background-color: #e9ecef " placeholder="Saisissez un commentaire">{{ $statut_immobilisation->commentaire ?? '' }}</textarea>
                                    
                                    </td>

                                </tr>
                            </table>
                        </div>
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
            } 

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

        editAgent = function(a){

            var tr=$(a).parents("tr");
            const value=tr.find('.beneficiaire').val();
            const opts = document.getElementById('list_agent').childNodes;


            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === value) {
                    
                    if(value != ''){
                        const block = value.split('->');
                        var beneficiaire = block[0];
                        var description_beneficiaire = block[1];
                        var type_beneficiaire = block[2];
                    
                    }else{
                        beneficiaire = "";
                        description_beneficiaire = "";
                        type_beneficiaire = "";
                    }

                    tr.find('.beneficiaire').val(beneficiaire);
                    tr.find('.description_beneficiaire').val(description_beneficiaire);
                    tr.find('.type_beneficiaire').val(type_beneficiaire);

                    break;
                }else{
                    tr.find('.description_beneficiaire').val("");
                    tr.find('.type_beneficiaire').val("");
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
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);

                cell1.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0"><input autocomplete="off" title="Saisissez le matricule ou le nom du bénéficiare" required  type="text" onkeyup="editAgent(this)" id="beneficiaire" name="beneficiaire[]" class="form-control beneficiaire" style="font-size:10px; text-align:center; border:none;"></td>';

                cell2.innerHTML = '<td><input autocomplete="off" title="Nom & Prénoms du bénéficiaire" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; " type="text" id="description_beneficiaire" name="description_beneficiaire[]" class="form-control description_beneficiaire"><input autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; display:none" type="text" id="type_beneficiaire" name="type_beneficiaire[]" class="form-control type_beneficiaire"></td>';

                cell3.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0"><input autocomplete="off" title="sélectionnez l\'article en saisissant soit la référence ou la désignation" required  type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px; text-align:left; border:none;"><input autocomplete="off" style="display: none; font-size:10px;" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id"></td>';

                cell4.innerHTML = '<td><input autocomplete="off" title="Désignation de l\'article sélectionné" required type="text" onkeyup="editMontant(this)" onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px;" id="design_article" name="design_article[]" class="form-control design_article"></td>';

                cell5.innerHTML = '<td><input autocomplete="off" title="Prix unitaire de l\'article sélectionné" required type="text" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup"></td>';

                cell6.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0"><input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_stock[]" class="form-control qte_stock"><input autocomplete="off" title="Entrez la quantité d\'article souhaitée" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center; font-size:10px;border:none;" name="qte[]" class="form-control qte"></td>';

                cell7.innerHTML = '<td><input autocomplete="off" title="Montant de la demande de cet article" required type="text" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" name="montant[]" onkeypress="validate(event)" class="form-control montant"></td>';

                cell8.innerHTML = '<td><input style="height: 30px;" accept="image/*" type="file" name="echantillon[]"></td>';

                cell9.innerHTML = '<td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;"><a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
            //}

            cell5.style.display = "none";
            cell7.style.display = "none";

            cell1.style.borderCollapse = "collapse";
            cell1.style.BackgroundColor = "white";
            cell1.style.padding = "0";
            cell1.style.margin = "0";

            cell2.style.borderCollapse = "collapse";
            cell2.style.padding = "0";
            cell2.style.margin = "0";

            cell3.style.borderCollapse = "collapse";
            cell1.style.BackgroundColor = "white";
            cell3.style.padding = "0";
            cell3.style.margin = "0";

            cell4.style.borderCollapse = "collapse";
            cell4.style.padding = "0";
            cell4.style.margin = "0";

            cell5.style.borderCollapse = "collapse";
            cell5.style.padding = "0";
            cell5.style.margin = "0";

            cell6.style.borderCollapse = "collapse";
            cell1.style.BackgroundColor = "white";
            cell6.style.padding = "0";
            cell6.style.margin = "0";

            cell7.style.borderCollapse = "collapse";
            cell7.style.padding = "0";
            cell7.style.margin = "0";

            cell8.style.borderCollapse = "collapse";
            cell8.style.padding = "0";
            cell8.style.margin = "0";

            cell9.style.borderCollapse = "collapse";
            cell9.style.padding = "0";
            cell9.style.margin = "0";
            cell9.style.verticalAlign = "middle";
            cell9.style.textAlign = "center";
        
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

        function myCreateFunction2() {
      var table = document.getElementById("tablePiece");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            cell1.innerHTML = '<td><input type="file" name="piece[]" class="form-control-file" id="piece" ></td>';
            cell2.innerHTML = '<td><a title="Retirer ce fichier" onclick="removeRow2(this)" href="#"><svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
            cell2.style.textAlign = "right";
        
      
    }

    removeRow2 = function(el) {
        var table = document.getElementById("tablePiece");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;

            if(nbre_rows == 1){
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Vous ne pouvez pas supprimer cette ligne',
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
