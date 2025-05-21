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

<style type="css">
    .selected {background: coral;} 
</style>
</head>
<br>
<div class="container" style="font-size:10; line-height:10px;"> 

    <div class="row" style="font-size:10px;">
        <div class="col-md-12">
            <div class="card">
                <?php 
                    $entete = "LIVRAISON D'ARTICLES";
                    $entete_th1 = "QTÉ VALIDÉE";
                    $entete_th2 = "QTÉ SERVIE";
                    $display_date = null;

                    if (isset($valider_reception)) {
                        if ($valider_reception === 1) {
                            $entete = "CONFIRMATION DE LA RÉCEPTION D'ARTICLES";
                            $entete_th1 = "QTÉ LIVRÉE";
                            $entete_th2 = "QTÉ REÇUE";
                            $display_date = 1;
                        } 
                    }    
                ?>
                <div class="card-header entete-table">{{ strtoupper($entete ?? '') }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($requisitions->created_at ?? '')) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">STRUCTURE : <strong>{{ $requisitions->nom_structure ?? '' }}</strong></span>
                    
                    
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
                    <form method="POST" action="{{ route('livraisons.store')  }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="label">N° Bon de commande</label>
                                    <input value="{{ $requisitions->num_bc ?? '' }}" onfocus="this.blur()" style=" color:red; text-align:left; font-weight:bold" style="border:none" type="text" class="form-control griser">

                                    <input onfocus="this.blur()" value="{{ $requisitions->id ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; color:red; text-align:center; font-weight:bold; display:none" type="text" class="form-control" name="requisitions_id">

                                    <input style="display:none" class="form-control" id="type_action" 
                                    
                                    @if(isset($valider_reception) && count($demandes) != 0 )
                                        @if($valider_reception === 1) 

                                            value="reception"

                                            @elseif($valider_reception === 0)

                                            value="livrer"

                                        @endif
                                    @endif
                                    >

                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; font-weight:bold" value="{{ $requisitions->intitule ?? '' }}" required type="text" name="intitule" class="form-control" placeholder="Intitulé d'ARTICLES">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label">Gestion</label>
                                <div class="form-group">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:left; font-weight:bold" value="{{ $requisitions->code_gestion. ' - '.$requisitions->libelle_gestion }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion">
                                </div>
                            </div>
                        </div>

                        @if(isset($type_profils_name))
                            @if($type_profils_name === "Responsable des stocks" or $type_profils_name === "Gestionnaire des stocks")
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="label">Date livraison<span style="color: red"><sup> *</sup></span></label>
                                            <input autofocus autocomplete="off" title="Entrez la date de la demande"  type="date" name="date_livraison" class="form-control @error('date_livraison') is-invalid @enderror" required placeholder="Saisissez la date de la demande" style="font-size: 10px;" value="{{ old('date_livraison') ?? '' }}">
                                            @error('date_livraison')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            @endif
                        @endif

                        

                        <div class="panel panel-footer"><!--table-bordered-->
                            <table class="table table-striped" id="myTable" width="100%">
                                <thead>
                                    <tr style="background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="vertical-align:middle; text-align:center; width:1px; white-space:nowrap">RÉF. ARTICLE</th>
                                        <th style="vertical-align:middle; text-align:center; width:80%">DÉSIGNATION ARTICLE</th>
                                        @if(isset($display_date))
                                        <th style="vertical-align:middle; text-align:center; width:1px; white-space:nowrap">DATE LIVRAISON</th>
                                        @endif
                                        <th style="vertical-align:middle; text-align:center; width:12%; display:none">PRIX U</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">{{ strtoupper($entete_th1 ?? '') }}</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">{{ strtoupper($entete_th2 ?? '') }}
                                            @if(isset($valider_reception)) 
                                                @if($valider_reception === 1)  
                                                
                                                @else
                                                <span style="color: red"><sup> *</sup></span>
                                                @endif
                                                @else 
                                                <span style="color: red"><sup> *</sup></span>
                                            @endif
                                            
                                        </th>
                                        <th style="vertical-align:middle; text-align:center; width:15%; display:none">COÛT</th>
                                        <th style="text-align:center; width:1%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; $index = 0;  ?>
                                    @foreach($demandes as $demande)
                                    <?php   
                                    
                                            $color = "";
                                            $i++;
                                            $statut = 0;
                                            if(isset($qte_restant)){
                                                unset($qte_restant);
                                            }
                                            $check='';
                                            $montant = $demande->qte_validee * $demande->cmup;

                                            if(isset($valider_reception)){
                                                if($valider_reception === 0){

                                                    

                                                    

                                                   if ($demande->qte < $demande->qte_validee) {
                                                        $color = 1;
                                                        $montant = $demande->qte * $demande->cmup;
                                                    }

                                                    
                                                    
                                                    
                                                    

                                                    $livraison = DB::table('livraisons as l')
                                                    ->where('l.demandes_id',$demande->id)
                                                    ->select(DB::raw('SUM(l.qte) as qte'))
                                                    ->groupBy('l.demandes_id')
                                                    ->first();
                                                    if ($livraison!=null) {

                                                        $qte_restant = $demande->qte_validee - $livraison->qte;


                                                        $montant = $qte_restant * $demande->cmup;

                                                        if ($demande->qte < $qte_restant) {
                                                            $color = 1;
                                                            $montant = $demande->qte * $demande->cmup;                     
                                                        }else{
                                                            unset($color);
                                                        }

                                                        

                                                        if(isset($statut)){
                                                            if($statut == 1){
                                                            $check='checked';
                                                            }else{
                                                            $check='';
                                                            }
                                                        }

                                                    }else{
                                                        $check='';
                                                        if(isset($qte)){
                                                            unset($qte);
                                                        }

                                                        if(isset($observation)){
                                                            unset($observation);
                                                        }
                                                    }


                                                    



                                                    
                                                }

                                                if($valider_reception === 1){
                                                        
                                                        $qte_restant = $demande->qte_validee - $demande->qte_recue;


                                                        $montant = $qte_restant * $demande->cmup;

                                                        if($demande->qte_recue != null){
                                                            $check='checked';
                                                        }else{
                                                            $check='';
                                                        }


                                                }
                                            }  
                                            
                                    ?>
                                    <tr 
                                    @if(isset($color)) 
                                    
                                        @if($color === 1)

                                            title="Bénéficiaire : {{ $demande->nom_prenoms ?? '' }}... La quantité disponible en stock est inférieure à la quantité à livrer... (Quantité disponible en stock : {{ $demande->qte ?? '' }} )"

                                            @else

                                            title="Bénéficiaire : {{ $demande->nom_prenoms ?? '' }}"

                                        @endif 

                                        @else 

                                        title="Bénéficiaire : {{ $demande->nom_prenoms ?? '' }}"

                                    @endif

                                                                            
                                    style=" @if(isset($qte_restant)) @if($qte_restant <= 0)display: none; @endif @endif @if(isset($color)) @if($color ===1 ) background-color: #efa0a0; @endif @endif "
                                        
                                    >
                                        <td style="text-align:left; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">

                                            @if(isset($valider_reception))
                                                @if($valider_reception === 1)
                                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" class="form-control" name="livraisons_id[{{ $index }}]" type="text" value="{{ $demande->livraisons_id ?? '' }}"/>
                                                @endif
                                            @endif

                                            <input onfocus="this.blur()" style="background-color: #e9ecef ; text-align:center; font-weight:bold; display: none 
                                            
                                                @if(isset($color)) @if($color===1)
                                                ;color:#721c24; background-color:transparent; border-color:transparent;
                                                @endif @endif
                                            
                                            " required value="{{ $demande->ref_articles ?? '' }}" list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[{{ $index }}]" class="form-control ref_articles" >

                                            
                                            <input onfocus="this.blur()" style="background-color: #e9ecef ; display: none"
                                            
                                            @if(isset($qte_restant))
                                                @if($qte_restant <= 0)
                                                    
                                                @else
                                                        value="{{ $demande->id ?? '' }}"
                                                @endif
                                                   
                                            @else
                                                value="{{ $demande->id ?? '' }}"
                                            @endif
                                            
                                            
                                             type="text" id="demandes_id" name="demandes_id[{{ $index }}]" class="form-control demandes_id">

                                             &nbsp;&nbsp;&nbsp;{{ $demande->ref_articles ?? '' }}&nbsp;&nbsp;&nbsp;

                                        </td>
                                        <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input required value="{{ $demande->design_article ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; display:none;
                                            
                                                @if(isset($color)) @if($color===1)
                                                color:#721c24; background-color:transparent; border-color:transparent;
                                                @endif @endif
                                            
                                            " type="text" id="design_article" name="design_article[{{ $index }}]" class="form-control design_article">

                                            &nbsp;&nbsp;&nbsp;{{ $demande->design_article ?? '' }}

                                        </td>
                                        @if(isset($display_date))
                                        <td style="vertical-align:middle; text-align:center; width:1%; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0;">
                                            @if(isset($demande->created_at))
                                                {{ date("d/m/Y",strtotime($demande->created_at)) }}
                                            @endif
                                            
                                        
                                        </td>
                                        @endif
                                        <td style="vertical-align:middle; text-align:right; display:none; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input value="{{ strrev(wordwrap(strrev($demande->cmup ?? 0), 3, ' ', true)) }}" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right;  
                                            
                                                @if(isset($color)) @if($color===1)
                                                color:#721c24; background-color:transparent; border-color:transparent;
                                                @endif @endif
                                            
                                            " type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[{{ $index }}]" class="form-control cmup">
                                        </td>
                                        <td style="text-align:center; vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" value="{{ $demande->qte ?? 0 }}" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_stock[{{ $index }}]" class="form-control qte_stock">

                                            <input autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; 
                                            
                                                @if(isset($color)) @if($color===1)
                                                color:#721c24; background-color:transparent; border-color:transparent;
                                                @endif @endif
                                            
                                            " 
                                            
                                            value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? 0), 3, ' ', true)) }}" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_validee[{{ $index }}]" class="form-control qte_validee">
                                        
                                            {{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? 0), 3, ' ', true)) }}

                                        </td>
                                        <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                            @if(isset($valider_reception)) 
                                                @if($valider_reception === 1)

                                                <input onfocus="this.blur()" autocomplete="off" style="text-align:center; border:none; background-color:transparent" 
                                                
                                                @if(isset($color)) @if($color===1)
                                                    value="{{ strrev(wordwrap(strrev($demande->qte ?? $qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                
                                                required type="text" name="" class="form-control">

                                                <input autocomplete="off" style="text-align:center;display:none;" 
                                                
                                                @if(isset($color)) @if($color===1)
                                                    value="{{ strrev(wordwrap(strrev($demande->qte ?? $qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                
                                                required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[{{ $index }}]" class="form-control qte">

                                                @else
                                                    <input autocomplete="off" style="text-align:center;" 
                                                
                                                    @if(isset($color)) @if($color===1)
                                                        value="{{ strrev(wordwrap(strrev($demande->qte ?? $qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                    
                                                    required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[{{ $index }}]" class="form-control qte">
                                                @endif
                                                @else 
                                                <input autocomplete="off" style="text-align:center;" 
                                                
                                                @if(isset($color)) @if($color===1)
                                                    value="{{ strrev(wordwrap(strrev($demande->qte ?? $qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev($qte_restant ?? $demande->qte_validee ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                
                                                required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[{{ $index }}]" class="form-control qte">
                                            @endif
                                            
                                        </td>
                                        <td style="vertical-align:middle; text-align:right; display:none; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input value="{{ strrev(wordwrap(strrev($montant ?? 0), 3, ' ', true)) }}" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; 
                                            
                                                @if(isset($color)) @if($color===1)
                                                color:#721c24; background-color:transparent; border-color:transparent;
                                                @endif @endif
                                            
                                            " type="text" name="montant[{{ $index }}]" onkeypress="validate(event)" class="form-control montant">
                                        </td>
                                        <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                            <input type="checkbox"  name="approvalcd[{{ $index }}]" class="approvalcd"/>
                                        </td>
                                    </tr>
                                    <?php $index++;  ?>
                                    @endforeach

                                    @if(count($demandes) === 0)
                                        <tr>
                                            <td colspan="8" style="text-align:center; color: brown;">
                                                <span>Réquisition déjà livrée</span> ( <span style="font-style: italic; color:grey"> <a href="/requisitions/show/{{ Crypt::encryptString($requisitions->id ?? 0) }}">Cliquez ici pour voir les détails</a> </span> )
                                            </td>
                                        </tr>
                                    @endif
                                    
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

                                            <div class="col-md-3" style="margin-top:30px">
                                                @if(isset($valider_reception) && count($demandes) != 0 )
                                                    @if($valider_reception === 0)
                                                        <button onclick="return confirm('Faut-il enregistrer ?')" style="margin-top: -20px;" type="submit" name="submit" value="livrer" class="btn btn-success">
                                                        Livrer
                                                        </button>

                                                        <a href="{{ route('annulation.livraison_requisition', ['requisitions_id' => Crypt::encryptString($requisitions->id ?? 0)]) }}"
                                                            onclick="return confirm('Faut-il annuler ?')"
                                                            class="btn btn-danger" 
                                                            style="margin-top: -20px;">
                                                            Annuler le BCI
                                                         </a>

                                                    @endif
                                                @endif

                                                @if(isset($valider_reception) && count($demandes) != 0 )
                                                    @if($valider_reception === 1)
                                                        <button onclick="return confirm('Êtes-vous sûr de vouloir confirmer la réception de cette demande ?')" style="margin-top: -20px;" type="submit" name="submit" value="reception" class="btn btn-success">
                                                        Confirmer
                                                        </button>
                                                    @endif
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
            qte_stock = qte_stock * 1;

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
            
            var type_action = document.getElementById('type_action').value;

            if(type_action == "livrer"){
                if(qte > qte_validee) {

                    Swal.fire({
                    title: '<strong>e-GESTOCK</strong>',
                    icon: 'error',
                    html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité validée',
                    focusConfirm: false,
                    confirmButtonText:
                    'Compris'
                    });

                    qte = 0;

                }else if(qte > qte_stock) {

                    Swal.fire({
                    title: '<strong>e-GESTOCK</strong>',
                    icon: 'error',
                    html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité disponible en stock... (Quantité disponible en stock : '+ qte_stock +' )',
                    focusConfirm: false,
                    confirmButtonText:
                    'Compris'
                    });

                    qte = qte_stock;
                } 
            }else if(type_action == "reception"){

                if(qte > qte_validee) {

                    Swal.fire({
                    title: '<strong>e-GESTOCK</strong>',
                    icon: 'error',
                    html: 'Attention!!! : la quantité reçue ne peut être supérieure à la quantité livrée',
                    focusConfirm: false,
                    confirmButtonText:
                    'Compris'
                    });

                    qte = qte_validee;

                }
            }

             

            var montant=(cmup*qte);


            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte').val(int3.format(qte));
            tr.find('.montant').val(int3.format(montant));
            
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
