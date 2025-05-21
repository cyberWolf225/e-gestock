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

    .td-center-white-space{
        text-align: center;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }

    .td-center{
        text-align: center;
        vertical-align: middle;
    }

    .td-center-bold{
        text-align: center;
        vertical-align: middle;
        font-weight: bold
    }

    .td-left{
        text-align: left;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
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
            <option value="{{ $famille->design_fam }}">{{ $famille->ref_fam }}->{{ $famille->design_fam }}</option>
        @endforeach
    </datalist>
    <datalist id="list_article">
        @foreach($datalist_magasin_stocks as $datalist_magasin_stock)
            <?php //quantité théorique
            
            if (isset($qte_theo)) {unset($qte_theo);}
            if (isset($qte_phys)) {unset($qte_phys);}
            if (isset($ecart)) {unset($ecart);}
            if (isset($justificatif)) {unset($justificatif);}
            if (isset($flag_valide)) {unset($flag_valide);}
            if (isset($flag_integre)) {unset($flag_integre);}

            $datalist_mouvements = DB::table('mouvements')
                ->where('magasin_stocks_id',$datalist_magasin_stock->id)
                ->whereDate(DB::raw('DATE(created_at)'),'<=', $fin_per)
                ->select([DB::raw("SUM(qte) as total_qte")])
                ->groupBy('magasin_stocks_id')
                ->get();
            foreach ($datalist_mouvements as $datalist_mouvement) {
                $qte_theo = $datalist_mouvement->total_qte;
            }

            // inventaire article
            $datalist_inventaire_article = DB::table('inventaire_articles')
                ->where('magasin_stocks_id',$datalist_magasin_stock->id)
                ->where('inventaires_id',$id)
                ->first();
            if ($datalist_inventaire_article!=null) {
                $qte_theo = $datalist_inventaire_article->qte_theo;
                $qte_phys = $datalist_inventaire_article->qte_phys;
                $ecart = $datalist_inventaire_article->ecart;
                $justificatif = $datalist_inventaire_article->justificatif;
                $flag_valide = $datalist_inventaire_article->flag_valide;
                $flag_integre = $datalist_inventaire_article->flag_integre;
            }

            ?>
            @if(!isset($qte_phys))
                <option value="{{ $datalist_magasin_stock->ref_articles }}->{{ $datalist_magasin_stock->design_article }}->{{ $datalist_magasin_stock->design_magasin }}->{{ $qte_theo ?? 0 }}->{{ $qte_phys ?? '' }}->{{ $ecart ?? '' }}->{{ $datalist_magasin_stock->id }}->{{ $justificatif ?? '' }}->{{ $flag_valide ?? '' }}->{{ $id ?? '' }}->{{ $flag_integre ?? '' }}">{{ $datalist_magasin_stock->design_article }}</option>
            @endif
            
        @endforeach
    </datalist>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('FAIRE UN INVENTAIRE') }} 
                    <span style="float: right; margin-right: 20px;">DÉPÔT : <strong>{{ $depot->design_dep ?? '' }}</strong></span>
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
                    <form method="GET" action="{{ route('inventaire_articles.create_inventaire_famille') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group pt-2 pl-4">
                                    Début de période : <strong>{{ date("d/m/Y", strtotime($debut_per ?? ''))   }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group pt-2 pl-4">
                                    Fin de période : <strong>{{ date("d/m/Y", strtotime($fin_per ?? ''))   }}</strong>
                                </div>
                            </div>
                                <input onfocus="this.blur()" style="display: none;" id="profil_responsable_stock" 
                                value="@if(isset($profil_responsable_stock)) 1 @else 0 @endif"
                                >

                                <input onfocus="this.blur()" style="display: none;" id="nbre_ligne_inventaire" 
                                value="{{ $nbre_ligne_inventaire +1 ?? 0 }}"
                                >

                                    
                                
                            <div class="col-md-5">
                                <div class="form-group pr-0">
                                    <input title="" style="display: none" required list="inventaires_id" value="{{ $id ?? '' }}" autocomplete="off" type="text" name="inventaires_id" class="form-control">
                                    <input placeholder="Sélectionnez la famille" title="" required list="design_fam" value="{{ $_GET['design_fam'] ?? '' }}" autocomplete="off" type="text" name="design_fam" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
                                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                      </svg></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('inventaire_articles.store') }}">
                        @csrf
                        <div class="panel panel-footer">
                            @if(isset($datalist_magasin_stocks))
                                @if(count($datalist_magasin_stocks) > 0)
                                <table id="example1" class="table table-bordered table-striped" width="100%">
                                    <thead>
                                        <tr style="background-color: #e9ecef">
                                            <th style="width:15%; text-align:center">Réf<span style="color: red"><sup> *</sup></span></th>
                                            <th style="width:37%; text-align:center">Article</th>
                                            <th style="width:12%; text-align:center">Qté Théo</th>
                                            <th style="width:12%; text-align:center">Qté Phys<span style="color: red"><sup> *</sup></span></th>
                                            <th style="width:10%; text-align:center">Ecart</th>
                                            <th style="width:15%; text-align:center">Justificatif</span></th>
                                            @if(isset($profil_responsable_stock))
                                                <th style="width:1%; text-align:center"><input title="Valider l'ensemble des articles inventoriés" autocomplete="off" type="checkbox" id="all_flag_valide" onClick="toggle(this)"></th>
                                            
                                            
                                                <th style="width:1%; text-align:center"><input title="Integrer l'ensemble des articles inventoriés" autocomplete="off" type="checkbox" id="all_flag_integre" onClick="toggle2(this)"></th>
                                            @endif
                                            
                                            <th style="text-align:center; width:1%"><a onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                              </svg></a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0;">

                                                <input 
                                                title="Sélectionnez l'article" autocomplete="off" 
                                                list="list_article" 
                                                type="text" 
                                                onkeyup="editDesign(this)" id="ref_articles" 
                                                name="ref_articles[0]" 
                                                class="form-control ref_articles"
                                                style="text-align: center; font-weight:bold">


                                                <input title="" autocomplete="off" style="display: none" type="text" id="magasin_stocks_id" name="magasin_stocks_id[0]" class="form-control magasin_stocks_id">


                                                <input title="" autocomplete="off" style="display: none" type="text" id="id" name="id[0]" class="form-control id">


                                            </td>
                                            <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input title="Désignation de l'article sélectionné" autocomplete="off" onfocus="this.blur()" style=" padding-left:2px; border:none; background-color:transparent; border-color:transparent; font-weight:bold" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[0]" class="form-control design_article">
                                            </td>
                                            <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input title="la quantité théorique de l'article en stock" autocomplete="off" onfocus="this.blur()" style="; background-color:transparent; border-color:transparent; text-align:center" type="text" onkeyup="editMontant(this)" name="qte_theo[0]" class="form-control qte_theo">
                                            </td>
                                            <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input onkeypress="validate(event)" style="text-align:center" title="la quantité physique de l'article en stock" autocomplete="off" type="text" onkeyup="editMontant(this)" name="qte_phys[0]" class="form-control qte_phys">
                                            </td>
                                            <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input style="text-align:center;background-color:transparent; border-color:transparent" title="Ecart" autocomplete="off" onfocus="this.blur()" type="text" name="ecart[0]" class="form-control ecart">
                                            </td>
                                            <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input title="Justifier l'écart" autocomplete="off" type="text" onkeyup="editMontant(this)" name="justificatif[0]" class="form-control justificatif" id="justificatif[0]">

                                                <input style="display: none" autocomplete="off" type="text" onkeyup="editMontant(this)" name="index[0]" class="form-control index" id="index[0]" value="0">
                                            </td>
                                            @if(isset($profil_responsable_stock))
                                                <td style="vertical-align: middle; border-collapse: collapse; padding: 0; margin: 0; text-align:center; white-space:nowrap">
                                                    <input title="Valider l'inventaire de cet article" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef" type="checkbox" name="flag_valide[0]" class="flag_valide" id="flag_valide">
                                                </td>
                                            
                                                <td style="vertical-align: middle; text-align:center; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">
                                                    <input title="Integrer l'inventaire de cet article" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef" type="checkbox" name="flag_integre[0]" class="flag_integre" id="flag_integre">
                                                </td>
                                            @endif
                                            <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                                <a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)" href="#" class="remove">
                                                    <svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    
                                </table>
                                <table width="100%">
                                    <tr>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;"
                                            @if(isset($profil_responsable_stock))
                                                colspan="9"
                                            @else 
                                                colspan="7"
                                            @endif  
                                        >
                                            @if(isset($_GET['design_fam']))
                                                Pour la famille : <span style="font-weight: bold; color:red">{{ $_GET['design_fam'] }} </span>, vous avez <span style="font-weight: bold; color:red"> {{ $nbre_ligne_inventaire ?? 0 }} </span> @if(isset($nbre_ligne_inventaire)) @if($nbre_ligne_inventaire > 1) articles @else article @endif @else article  @endif à inventorier. <span style="font-weight: bold; color:green">{{ count($magasin_stocks ?? []) }}</span>  @if(isset($magasin_stocks)) @if(count($magasin_stocks) > 1) articles déjà inventoriés @else article déjà inventorié @endif @else article déjà inventorié @endif
                                            @endif 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td 
                                            @if(isset($profil_responsable_stock))
                                                colspan="7"
                                            @else 
                                                colspan="5"
                                            @endif
                                        style="border: none">

                                            <textarea placeholder="Écrivez votre commentaire ici" rows="1" id="commentaire" type="text" class="form-control @error('commentaire') is-invalid @enderror" name="commentaire" style="width: 100%;" 
                                            
                                            @if(isset($flag_integre))
                                                @if($flag_integre === 1)
                                                    disabled
                                                @endif
                                            @endif
                                            
                                            ></textarea>
                                        </td>

                                        <td colspan="2" style="border: none">

                                            <button onclick="return confirm('Faut-il enregistrer ?')" title="Enregistrer l'inventaire" type="submit" name="submit" value="create" class="btn btn-success">
                                                Enregistrer
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                            @endif
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<script>

    function toggle(source) {
        checkboxes = document.getElementsByClassName('flag_valide');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function toggle2(source) {
        checkboxes = document.getElementsByClassName('flag_integre');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

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

        var qte_theo=tr.find('.qte_theo').val();

        var qte_phys=tr.find('.qte_phys').val();

        var index=tr.find('.index').val();

        var ecart=(qte_phys-qte_theo);

        tr.find('.ecart').val(ecart);

        var justificatif = document.getElementById("justificatif["+index+"]"); 

        if (ecart === 0) {

            if (justificatif.hasAttribute("required")) {
                justificatif.removeAttribute("required");
            }
            
            

        }else{

            if (justificatif.hasAttribute("required")) {
                
            }else{
                justificatif.setAttribute("required","");
            }
            
        }
        

        // var magasin_stocks_id=tr.find('.magasin_stocks_id').val();
        // var justificatif = document.getElementById("justificatif["+magasin_stocks_id+"]");

        // if (ecart === 0) {


        //     justificatif.removeAttribute("required");   
        //     justificatif.required = false;               
        //     jQuery(justificatif).removeAttr('required');
        //     $("#justificatif["+magasin_stocks_id+"]").removeAttr('required');
            

        // }else{

            
        //     justificatif.style.required = "required";
        //     justificatif.setAttribute("required", ""); 
        //     justificatif.required = true;               
        //     jQuery(justificatif).attr('required', ''); 
        //     $("#justificatif["+magasin_stocks_id+"]").attr('required', '');


        // }



        


    }

    editDesign = function(a){
        var tr=$(a).parents("tr");
        const value=tr.find('.ref_articles').val();
        if(value != ''){
            const block = value.split('->');
            var ref_articles = block[0];
            var design_article = block[1];
            var design_magasin = block[2];
            var qte_theo = block[3];
            var qte_phys = block[4];
            var ecart = block[5];
            var magasin_stocks_id = block[6];
            var justificatif = block[7];
            var flag_valide = block[8];
            var id = block[9];
            var flag_integre = block[10];
        }else{
            ref_articles = "";
            design_article = "";
            design_magasin = "";
            qte_theo = "";
            qte_phys = "";
            ecart = "";
            magasin_stocks_id = "";
            justificatif = "";
            flag_valide = 0;
            id = "";
            flag_integre = 0;
        }
        
        tr.find('.magasin_stocks_id').val(magasin_stocks_id);
        tr.find('.ref_articles').val(ref_articles);
        tr.find('.design_article').val(design_article);
        tr.find('.design_magasin').val(design_magasin);
        tr.find('.qte_theo').val(qte_theo);
        tr.find('.qte_phys').val(qte_phys);
        tr.find('.ecart').val(ecart);
        tr.find('.justificatif').val(justificatif);
        // tr.find('.flag_valide').val(flag_valide);
        tr.find('.id').val(id);
        // tr.find('.flag_integre').val(flag_integre);


        // var field = document.getElementById("flag_valide");
        //     field.id = "flag_valide["+magasin_stocks_id+"]";  
        //     field.setAttribute("name", "flag_valide["+magasin_stocks_id+"]");

        // flag_valide = flag_valide * 1;

        // var field = document.getElementById("flag_integre");
        //     field.id = "flag_integre["+magasin_stocks_id+"]";  
        //     field.setAttribute("name", "flag_integre["+magasin_stocks_id+"]");

        // var field = document.getElementById("justificatif");
        //     field.id = "justificatif["+magasin_stocks_id+"]";  
        //     field.setAttribute("name", "justificatif["+magasin_stocks_id+"]");


        flag_integre = flag_integre * 1;

        if (flag_valide === 1){
            tr.find('.flag_valide').checked = true;
        } else {
            tr.find('.flag_valide').checked = false;
        }

        flag_integre = flag_integre * 1;

        if (flag_integre === 1){
            tr.find('.flag_integre').checked = true;
        } else {
            tr.find('.flag_integre').checked = false;
        }

    }    
    
    function myCreateFunction() {
      var table = document.getElementById("example1");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length;
      var index = nbre_rows - 1;
        var profil_responsable_stock = document.getElementById("profil_responsable_stock").value * 1;

        var nbre_ligne_inventaire = document.getElementById("nbre_ligne_inventaire").value * 1;

        if(nbre_rows<nbre_ligne_inventaire){

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            
            
            if (profil_responsable_stock === 1) {
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
            }else{
                var cell7 = row.insertCell(6);
            }
            cell1.innerHTML = '<td> <input title="Sélectionnez l\'article" autocomplete="off" list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles['+index+']" class="form-control ref_articles" style="text-align:center; vertical-align:middle; font-weight:bold"> <input title="" autocomplete="off" style="display: none" type="text" id="magasin_stocks_id" name="magasin_stocks_id['+index+']" class="form-control magasin_stocks_id"> <input title="" value="{{ $id ?? '' }}" autocomplete="off" style="display: none" type="text" id="id" name="id['+index+']" class="form-control id"> </td>';
            cell2.innerHTML = '<td> <input title="Désignation de l\'article sélectionné" autocomplete="off" onfocus="this.blur()" style=" padding-left:2px; border:none; background-color:transparent; border-color:transparent; font-weight:bold" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article['+index+']" class="form-control design_article"> </td>';
            cell3.innerHTML = '<td> <input title="la quantité théorique de l\'article en stock" autocomplete="off" onfocus="this.blur()" style=" background-color:transparent; border-color:transparent;text-align:center" type="text" onkeyup="editMontant(this)" name="qte_theo['+index+']" class="form-control qte_theo"> </td>';
            cell4.innerHTML = '<td> <input onkeypress="validate(event)" style="text-align:center" title="la quantité physique de l\'article en stock" autocomplete="off" type="text" onkeyup="editMontant(this)" name="qte_phys['+index+']" class="form-control qte_phys"> </td>';
            cell5.innerHTML = '<td> <input style="text-align:center; background-color:transparent; border-color:transparent" title="Ecart" autocomplete="off" onfocus="this.blur()" type="text" name="ecart['+index+']" class="form-control ecart"> </td>';
            cell6.innerHTML = '<td> <input title="Justifier l\'écart" autocomplete="off" type="text" onkeyup="editMontant(this)" name="justificatif['+index+']" class="form-control justificatif" id="justificatif['+index+']"> <input style="display: none" autocomplete="off" type="text" onkeyup="editMontant(this)" name="index['+index+']" class="form-control index" id="index['+index+']" value="'+index+'"> </td>';
            
            

            if (profil_responsable_stock === 1) {
                cell7.innerHTML = '<td> <input title="Valider l\'inventaire de cet article" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef" type="checkbox" name="flag_valide['+index+']" class="flag_valide" id="flag_valide"> </td>';
                cell8.innerHTML = '<td> <input title="Intégrer l\'inventaire de cet article" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef" type="checkbox" name="flag_integre['+index+']" class="flag_integre" id="flag_integre"> </td>';
                cell9.innerHTML = '<td> <a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)" href="#" class="remove"> <svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/> </svg> </a> </td>';
            }else{
                cell7.innerHTML = '<td> <a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)" href="#" class="remove"> <svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/> </svg> </a> </td>';
            }

            cell1.style.verticalAlign = 'middle';
            cell2.style.verticalAlign = 'middle';
            cell3.style.verticalAlign = 'middle';
            cell4.style.verticalAlign = 'middle';
            cell5.style.verticalAlign = 'middle';
            cell6.style.verticalAlign = 'middle';
            
            

            if (profil_responsable_stock === 1) {
                cell7.style.textAlign = 'center';
                cell8.style.textAlign = 'center';
                cell9.style.textAlign = 'center';
            }else{
                cell7.style.textAlign = 'center';
            }

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

            cell7.style.borderCollapse = "collapse";
            cell7.style.padding = "0";
            cell7.style.margin = "0";
            cell7.style.verticalAlign = "middle";
            cell7.style.textAlign = "center";

            cell8.style.borderCollapse = "collapse";
            cell8.style.padding = "0";
            cell8.style.margin = "0";
            cell8.style.verticalAlign = "middle";
            cell8.style.textAlign = "center";

            cell9.style.borderCollapse = "collapse";
            cell9.style.padding = "0";
            cell9.style.margin = "0";
            cell9.style.verticalAlign = "middle";
            cell9.style.textAlign = "center";

            
        }
        
      
    }

    removeRow = function(el) {
        var table = document.getElementById("example1");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<3){
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
