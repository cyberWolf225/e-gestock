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

      .td-center{
        text-align: center;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
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
<div class="container" style="font-size:10; line-height:10px;">

<datalist id="list_article">
    @foreach($demandes_fusionnables as $demandes_fusionnable)
    
    <?php  
    $montant_fusionnable = $demandes_fusionnable->qte * $demandes_fusionnable->cmup;
    ?>
        <option value="{{ $demandes_fusionnable->ref_articles }}->{{ $demandes_fusionnable->design_article }}->{{ $demandes_fusionnable->cmup }}->{{ $demandes_fusionnable->id }}->{{ $demandes_fusionnable->qte }}->{{ $montant_fusionnable }}">{{ $demandes_fusionnable->design_article }}</option>
    @endforeach

</datalist>





    <div class="row" id="row" style="font-size: 10px;" >
        <div class="col-md-12">

            <div class="card">
                <div class="card-header entete-table"> {{ strtoupper($entete ?? '') }} 

                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($requisitions->created_at ?? '')) }}</strong></span>
                    
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
                    <form method="POST" action="{{ route('valider_requisitions.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="label">N° Bon de commande</label>
                                    <input value="{{ $requisitions->num_bc ?? '' }}" onfocus="this.blur()" style=" color:red; text-align:left; font-weight:bold" style="border:none" type="text" class="form-control griser">

                                    <input value="{{ $requisitions->id ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; font-weight:bold; display:none" name="requisitions_id" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" 
                                    
                                    @if(isset($demandes_fusionnables))
                                        

                                        value="{{ count($demandes_fusionnables) }}"
                                    
                                        @else

                                        value="{{ 0 }}"

                                    @endif
                                    type="text" name="nbre_ligne" id="nbre_ligne" class="form-control">


                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none"
                                     type="text" name="nbre_ligne_maxi" id="nbre_ligne_maxi" class="form-control">


                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $requisitions->num_bc ?? '' }}" required type="text" name="num_bc" class="form-control">

                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none"
                                    @if(isset($profil_respo_stock))
                                    @if($profil_respo_stock!=null)
                                        value="1"
                                    @endif
                                    @endif
                                    type="text" name="statut_profil" id="statut_profil" class="form-control">


                                    <input onfocus="this.blur()" style="background-color: #e9ecef; font-weight:bold" value="{{ $requisitions->intitule ?? '' }}" required type="text" name="intitule" class="form-control" placeholder="Intitulé de la réquisition">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label">Gestion</label>
                                <div class="form-group">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:left; font-weight:bold" value="{{ $requisitions->code_gestion }} - {{ $requisitions->libelle_gestion }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion">
                                </div>
                            </div>
                        </div>

                        @if(isset($profil_respo_stock))
                        @if($profil_respo_stock!=null)
                            <?php $statut_profil = 1; ?>
                        @endif
                        @endif

                        <div class="panel panel-footer"><!--table-bordered-->
                            <table class="table  table-striped" id="myTable" style="width:100%; border-collapse: collapse; padding: 0; margin: 0;">
                                <thead>
                                    <tr style="background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:10%; text-align:center;">RÉF. ARTICLE</th>
                                        <th style="width:55%; text-align:center;">DÉSIGNATION ARTICLE</th>
                                        <th style="width:1px; white-space:nowrap; text-align:center; display:none">PRIX U</th>

                                        @if(isset($value_bouton))
                                            @if( $value_bouton === "transmettre_respo_stock")
                                            <th style="width:1px; white-space:nowrap; text-align:center">
                                                QUANTITÉ <br>DEMANDÉE
                                            </th>
                                            @endif
                                        @endif

                                        

                                        <th style="width:1px; white-space:nowrap; text-align:center">

                                            @if(isset($value_bouton))
                                                @if( $value_bouton === "transmettre_respo_stock")
                                                    QUANTITÉ VALIDÉE
                                                @else
                                                    QUANTITÉ DEMANDÉE
                                                @endif
                                            @else
                                                QUANTITÉ DEMANDÉE
                                            @endif
                                            
                                        </th>

                                        @if(isset($value_bouton))
                                            @if( $value_bouton === "valider_respo_stock")
                                            <th style="width:1px; white-space:nowrap; text-align:center">
                                                RÉELLE QUANTITÉ <br>DEMANDÉE
                                            </th>
                                            @endif
                                        @endif
                                        
                                        <th style="width:10%; text-align:center; 
                                            @if(!isset($statut_profil)) 
                                                display:none;
                                            @endif">QUANTITÉ VALIDÉE <span style="color: red"><sup> *</sup></span>
                                        </th>
                                        
                                        <th style="width:1px; white-space:nowrap; text-align:center; display:none">COÛT</th>
                                        {{-- && $libelle != "Consolidée (Pilote AEE)" --}}

                                        @if($libelle != "Transmis (Responsable N+1)" && $libelle != "Transmis (Responsable N+2)" && $libelle != "Annulé (Responsable N+2)" && $libelle != "Annulé (Pilote AEE)")

                                            <th 
                                            
                                            style="text-align:center; vertical-align:middle; width:7%; 

                                            @if(isset($libelle))

                                                @if($libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)")
                                                    display:none;
                                                @endif 

                                            @endif  

                                            @if(isset($value_bouton))
                                                @if($value_bouton === 'transmettre_respo_stock')
                                                display:none;
                                                @endif
                                            @endif

                                        
                                        ">

                                                @if(isset($demandes_fusionnables))
                                                    @if(count($demandes_fusionnables)>0)

                                                    <a title="Consolider les demandes de réquisitions de votre structure" onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                    </svg></a>
                                                    @endif
                                                @endif

                                            </th>
                                        @endif
                                        

                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    <?php $index = 0; ?>
                                    @foreach($demandes as $demande)


                                        

                                    <?php $montant = $demande->qte * $demande->cmup;
                                            $montant = strrev(wordwrap(strrev($montant), 3, ' ', true));

                                            $qte_demande = null;

                                            $origine_demande = DB::table('demandes as d')
                                            ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                                            ->where('d.id',$demande->id)
                                            ->where('vr.flag_valide',1)
                                            ->select('d.qte')
                                            ->first();

                                            if($origine_demande != null){
                                                $qte_demande = $origine_demande->qte;
                                            }

                                            
                                    ?>
                                    <tr> <!--list="list_article" onkeyup="editDesign(this)"-->
                                        <td class="td-left" style="text-align:left; width:1px; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input onfocus="this.blur()" style="background-color:transparent; border-color:transparent; text-align:left;" required value="{{ $demande->ref_articles ?? '' }}"  type="text"  id="ref_articles" name="ref_articles[{{ $index }}]" class="form-control ref_articles">

                                            <input style="display: none" value="{{ $demande->id ?? '' }}" required type="text" id="demandes_id" name="demandes_id[{{ $index }}]" class="form-control demandes_id">
                                        </td>
                                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <input required value="{{ $demande->design_article ?? '' }}" onfocus="this.blur()" style="background-color:transparent; border-color:transparent;" type="text" id="design_article" name="design_article[{{ $index }}]" class="form-control design_article">

                                        </td>
                                        <td style="display: none; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input required value="{{ $demande->cmup ?? '' }}" onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; text-align:center; display: none" type="text" name="cmup[{{ $index }}]" class="form-control cmup">

                                            {{ $demande->cmup ?? '' }}

                                        </td>
                                        @if(isset($value_bouton))
                                            @if( $value_bouton === "transmettre_respo_stock")
                                            <td class="td-center" style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                {{ $qte_demande ?? '' }}
                                            </td>
                                            @endif
                                        @endif
                                        <td class="td-center" style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">

                                            <input onfocus="this.blur()" onkeypress="validate(event)" style="background-color: transparent; text-align:center; display: ; border:transparent" required value="{{ $demande->qte ?? '' }}" type="text" name="qte[{{ $index }}]" class="form-control qte">
                                        </td>

                                        @if(isset($value_bouton))
                                            @if( $value_bouton === "valider_respo_stock")
                                            <td  
                                                @if(!isset($statut_profil)) 
                                                style="display:none; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;"
                                                @else
                                                style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;"
                                                @endif
                                            >
                                                <input style="text-align:center; border:transparent" autocomplete="off" required value="{{ $qte_validee ?? $demande->qte ?? '' }}" onkeyup="editQteReelle(this)" onkeypress="validate(event)" type="text" name="qte_reelle[{{ $index }}]" class="form-control qte_reelle">
                                            </td>
                                            @endif
                                        @endif

                                        <td  
                                            @if(!isset($statut_profil)) 
                                            style="display:none; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;"
                                            @else
                                            style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;"
                                            @endif
                                        >
                                            <input style="text-align:center; border:transparent" autocomplete="off" required value="{{ $qte_validee ?? $demande->qte ?? '' }}" onkeyup="editMontant(this)" onkeypress="validate(event)" type="text" name="qte_validee[{{ $index }}]" class="form-control qte_validee">

                                        </td>
                                        <td style="display: none; text-align: right; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input required value="{{ $montant ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; text-align: right; display: none" type="text" name="montant[{{ $index }}]" onkeypress="validate(event)" class="form-control montant">

                                            {{ $montant ?? '' }}
                                        </td>
                                        {{-- && $libelle != "Consolidée (Pilote AEE)" --}}
                                        @if($libelle != "Transmis (Responsable N+1)" && $libelle != "Transmis (Responsable N+2)" && $libelle != "Annulé (Responsable N+2)" && $libelle != "Annulé (Pilote AEE)")
                                            
                                            <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;  
                                            @if(isset($libelle))
                                                @if($libelle === "Consolidée (Pilote AEE)" or $libelle === "Annulé (Responsable des stocks)")
                                                    display:none;
                                                @endif 
                                            @endif 

                                            @if(isset($value_bouton))
                                                @if($value_bouton === 'transmettre_respo_stock')
                                                display:none;
                                                @endif
                                            @endif
                                        ">
                                                <input 

                                                    @if(isset($value_bouton))
                                                        @if($value_bouton === 'transmettre_respo_stock')
                                                            checked
                                                        @endif
                                                    @endif
                                                    @if(isset($vue))
                                                    disabled  
                                                    @endif 
                                                {{ $check ?? '' }} type="checkbox" id="approvalcd" name="approvalcd[{{ $index }}]" class="approvalcd"
                                                @if($libelle === "Consolidée (Pilote AEE)")
                                                    checked
                                                @endif

                                                @if (isset($demande->requisitions_id_consolide))
                                                    checked
                                                @endif

                                                @if (isset($demande->flag_valide))
                                                    @if ($demande->flag_valide === 1)
                                                        checked
                                                    @endif
                                                @endif
                                                />
                                            </td>
                                        @endif
                                        
                                    </tr>

                                    <?php $index++; ?>
                                    @endforeach
                                    {{-- <tr style="display: none">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr> --}}
                                    
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
                                                    <button style="width:80px" type="submit" name="submit" value="{{ $value_bouton2 ?? '' }}" class="btn btn-danger">
                                                    {{ $bouton2 ?? ''}}
                                                    </button>
                                                @endif

                                                @if(isset($value_bouton))
                                                    <button style="width:80px" type="submit" name="submit" value="{{ $value_bouton ?? '' }}" class="btn btn-success">
                                                    {{ $bouton ?? ''}}
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

    editQteReelle = function(e){
        var tr=$(e).parents("tr");
        
        var qte_reelle=tr.find('.qte_reelle').val();
        qte_reelle = qte_reelle.trim();
        qte_reelle = qte_reelle.replace(' ','');
        qte_reelle = reverseFormatNumber(qte_reelle,'fr');
        qte_reelle = qte_reelle.replace(' ','');
        qte_reelle = qte_reelle * 1;

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
        tr.find('.qte_reelle').val(int3.format(qte_reelle));
        tr.find('.qte').val(int3.format(qte_reelle));
        
    }

    
        editMontant = function(e){
            var tr=$(e).parents("tr");

            var qte=tr.find('.qte').val();
            qte = qte.trim();
            qte = qte.replace(' ','');
            qte = reverseFormatNumber(qte,'fr');
            qte = qte.replace(' ','');
            qte = qte * 1;

            var qte_validee=tr.find('.qte_validee').val();
            qte_validee = qte_validee.trim();
            qte_validee = qte_validee.replace(' ','');
            qte_validee = reverseFormatNumber(qte_validee,'fr');
            qte_validee = qte_validee.replace(' ','');
            qte_validee = qte_validee * 1;


            var cmup=tr.find('.cmup').val();
            cmup = cmup.trim();
            cmup = cmup.replace(' ','');
            cmup = reverseFormatNumber(cmup,'fr');
            cmup = cmup.replace(' ','');
            cmup = cmup * 1;


            


            

            if(qte < qte_validee) {

                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité validée ne peut être supérieure à la quantité demandée',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
                });

                qte_validee = qte;
            } 

            var montant=(cmup*qte_validee);


            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte_validee').val(int3.format(qte_validee));
            tr.find('.montant').val(int3.format(montant));
            
        }




        editDesign = function(a){

            var tr=$(a).parents("tr");
            // var approvalcd = tr.find('.approvalcd').val();
            // alert(approvalcd);
            const value=tr.find('.ref_articles').val();
            
            if(value != ''){
            const block = value.split('->');
            var ref_articles = block[0];
            var design_article = block[1];
            var cmup = block[2];
            var demandes_id = block[3];
            var qte = block[4];
            var montant = block[5];
            }else{
                ref_articles = "";
                design_article = "";
                cmup = 0;
                demandes_id = "";
                qte = "";
                montant = "";

            }

            tr.find('.ref_articles').val(ref_articles);
            tr.find('.design_article').val(design_article);
            tr.find('.demandes_id').val(demandes_id);
            // tr.find('.approvalcd').val(demandes_id);
            

            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            
            tr.find('.cmup').val(int3.format(cmup));
            tr.find('.qte').val(int3.format(qte));
            tr.find('.qte_validee').val(int3.format(qte));
            tr.find('.montant').val(int3.format(montant));


            // var field = document.getElementById("approvalcd");
            // field.id = "approvalcd["+demandes_id+"]";  // using element properties
            // field.setAttribute("name", "approvalcd["+demandes_id+"]");  // using .setAttribute() method
        
        }

    
    
    function myCreateFunction() {
      var table = document.getElementById("tbody");
      var rows = table.querySelectorAll("tr");

      var nbre_ligne = document.getElementById("nbre_ligne").value;

      var nbre_ligne_maxi = document.getElementById("nbre_ligne_maxi").value;
      
      var nbre_rows = rows.length;
      var index = nbre_rows;

      nbre_ligne = reverseFormatNumber(nbre_ligne,'fr');

      nbre_ligne = parseInt(nbre_ligne, 10);


      if (nbre_ligne_maxi === '') {
          nbre_ligne_maxi = nbre_rows + nbre_ligne;
          document.getElementById("nbre_ligne_maxi").value = nbre_ligne_maxi;
      }

      var nbre_ligne_maxi = document.getElementById("nbre_ligne_maxi").value;

        if(nbre_rows<nbre_ligne_maxi){

            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            cell1.innerHTML = '<td><input style="text-align: left" autocomplete="off" required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles['+index+']" class="form-control ref_articles"><input style="display: none" required type="text" id="demandes_id" name="demandes_id['+index+']" class="form-control demandes_id"></td>';
            cell2.innerHTML = '<td><input required onfocus="this.blur()" style="background-color: transparent; border-color:transparent" type="text" id="design_article" name="design_article['+index+']" class="form-control design_article"></td>';
            cell3.innerHTML = '<td><input required onfocus="this.blur()" onkeypress="validate(event)" style="background-color: #e9ecef; text-align:center" type="text" name="cmup['+index+']" class="form-control cmup"></td>';
            cell4.innerHTML = '<td><input onfocus="this.blur()" onkeypress="validate(event)" style=" background-color: transparent; border-color:transparent; text-align:center" required style="text-align: center" type="text" name="qte['+index+']" class="form-control qte"></td>';
            cell5.innerHTML = '<input autocomplete="off" required onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center" type="text" name="qte_validee['+index+']" class="form-control qte_validee"></td>';
            cell6.innerHTML = '<td><input required onfocus="this.blur()" style="background-color: #e9ecef; text-align: right" type="text" name="montant['+index+']" onkeypress="validate(event)" class="form-control montant"></td>';
            cell7.innerHTML = '<td><input type="checkbox" id="approvalcd" name="approvalcd['+index+']" class="approvalcd" />&nbsp;&nbsp;&nbsp;<a onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px; margin-top:-10px; margin-left:10px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            cell7.style.textAlign = "center";
            cell7.style.verticalAlign = "middle";

            cell3.style.display = "none";
            cell6.style.display = "none";
            var statut_profil = document.getElementById("statut_profil").value;
            statut_profil = statut_profil * 1;
            if(statut_profil===0){
                cell5.style.display = "none";
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
            
        }
      
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            
        $(el).parents("tr").remove(); 
             

        
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
