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
                <div class="card-header entete-table">{{ strtoupper("Redistribution des articles") }} 
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
                    <form method="POST" action="{{ route('livraisons.distributions_store')  }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="label">N° Bon de commande</label>
                                    <input value="{{ $requisitions->num_bc ?? '' }}" onfocus="this.blur()" style="color:red; text-align:left; font-weight:bold" style="border:none" type="text" class="form-control griser" 
                                    
                                    >

                                    <input value="{{ $requisitions->id ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; color:red; text-align:center; font-weight:bold; display:none" type="text" class="form-control" name="requisitions_id">

                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; font-weight:bold" value="{{ $requisitions->intitule ?? '' }}" required type="text" name="intitule" class="form-control" placeholder="Intitulé de la réquisition">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label">Gestion</label>
                                <div class="form-group">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; font-weight:bold" value="{{ $requisitions->code_gestion. ' - '.$requisitions->libelle_gestion }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">

                                @foreach($distributions_annulees as $distributions_annulee)
                                        
                                    <sup title="Article retourné dans votre stock"><svg style="color: #f86c6b" xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                    </svg></sup>
                                    
                                    <strong style="font-size:px; color:">
                                        {{ $distributions_annulee->nom_prenoms ?? '' }} a infirmé la réception de l'article «{{ $distributions_annulee->design_article ?? '' }}». Quantité : {{ str_pad($distributions_annulee->qte, 2, "0", STR_PAD_LEFT) }}.
                                    </strong> 
                                    <br/><br/>

                                @endforeach
                                
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <?php $statut_distribution = 0; ?>
                                @foreach($stock_pilotes as $stock_pilote)
                                    <?php 
                                    
                                        $qte_distribuee = 0;
                                        $distribution_annulees = null;

                                        foreach ($stock_distribues as $stock_distribue) {

                                            if ($stock_distribue->demandes_id === $stock_pilote->demandes_id) {

                                                $qte_distribuee = $stock_distribue->qte_distribuee;

                                            }

                                        }
                                        
                                        if (isset($stock_pilote->qte_recue) && isset($qte_distribuee)) {
                                            $qte_disponible = $stock_pilote->qte_recue - $qte_distribuee;
                                        }

                                      $statut_distribution = $statut_distribution + ( $stock_pilote->qte_recue - $qte_distribuee );
                                        
                                    ?>
                                    
                                    {{ $stock_pilote->design_article ?? '' }} : 
                                    
                                    <strong style="font-size:15px">
                                        <span title="Quantité déjà distribuée" style="color:#155724; font-size:15px; cursor: pointer;">{{ $qte_distribuee ?? '' }}</span> 
                                        &nbsp;&nbsp;
                                        / 
                                        &nbsp;&nbsp;
                                        <span title="Quantité à distribuée" style="color:#721c24; font-size:15px; cursor: pointer;">{{ $stock_pilote->qte_recue ?? '' }}</span>
                                    </strong> 
                                    
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                    <input 
                                    class="form-control {{ 'art'.$stock_pilote->demandes_id }}" 
                                    name="{{ 'art'.$stock_pilote->demandes_id }}" 
                                    id="{{ 'art'.$stock_pilote->demandes_id }}"
                                    value="{{ $qte_disponible ?? 0 }}" 
                                    onfocus="this.blur()"
                                    style="background-color: #e9ecef; display:none"
                                    >

                                    <input 
                                    class="form-control {{ 'art_reel'.$stock_pilote->demandes_id }}" 
                                    name="{{ 'art_reel'.$stock_pilote->demandes_id }}" 
                                    id="{{ 'art_reel'.$stock_pilote->demandes_id }}"
                                    value="{{ $qte_disponible ?? 0 }}" 
                                    onfocus="this.blur()"
                                    style="background-color: #e9ecef; display:none"
                                    >

                                @endforeach
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                @foreach($valider_requisitions as $valider_requisition)
                        
                                    <input 
                                    class="form-control {{ 'actual_qte_validee_stock'.$valider_requisition->demandes_ids }}" 
                                    name="{{ 'actual_qte_validee_stock'.$valider_requisition->demandes_ids }}" 
                                    id="{{ 'actual_qte_validee_stock'.$valider_requisition->demandes_ids }}"
                                    value="{{ $valider_requisition->qte ?? 0 }}" 
                                    onfocus="this.blur()"
                                    style="background-color: #e9ecef; display:none"
                                    >
                        
                                    <input 
                                    class="form-control {{ 'qte_validee_stock_reel'.$valider_requisition->demandes_ids }}" 
                                    name="{{ 'qte_validee_stock_reel'.$valider_requisition->demandes_ids }}" 
                                    id="{{ 'qte_validee_stock_reel'.$valider_requisition->demandes_ids }}"
                                    value="{{ $valider_requisition->qte ?? 0 }}" 
                                    onfocus="this.blur()"
                                    style="background-color: #e9ecef; display:none"
                                    >
                        
                                @endforeach
                                
                            </div>
                        </div>

                        <div class="panel panel-footer">
                            <table class="table table-striped" id="myTable" style="width:100%">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="vertical-align:middle; text-align:left; width:15%">BÉNÉFICIAIRE</th>
                                        <th style="vertical-align:middle; text-align:left; width:8%">RÉF. ARTICLE</th>
                                        <th style="vertical-align:middle; text-align:left; width:30%">DÉSIGNATION ARTICLE</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">QTÉ DEMANDÉE</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">QTÉ VALIDÉE<br/>(STOCK)</th>
                                        <th style="vertical-align:middle; text-align:center; width:9%">QTÉ VALIDÉE<br/>(PILOTE AEE)</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%"> @if($statut_distribution === 0) QTÉ LIVRÉE @else QTÉ DÉJÀ <br/>LIVRÉE @endif </th>
                                        <th style="vertical-align:middle; text-align:center; width:15%; @if($statut_distribution === 0) display:none; @endif">QTÉ LIVRÉE<span style="color: red"><sup> *</sup></span></th>
                                        <th style="text-align:center; width:1%; @if($statut_distribution === 0) display:none; @endif"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  $index = 0;  ?>
                                    @foreach($demandes as $demande)

                                        <?php 

                                            //quantité deja livrée
                                            if (isset($qte_recue)) {
                                               unset($qte_recue);
                                            } 

                                            

                                            if (isset($qte_reste)) {
                                                unset($qte_reste);
                                            }

                                            

                                            $verou = 1;

                                            foreach ($stock_pilotes as $stock_pilote) {

                                                if ($stock_pilote->demandes_id === $demande->demandes_ids) {

                                                    $verou = null;

                                                }

                                            }
                                            
                                            $qte_validee_stock = 0;
                                            $valider_requisition = DB::table('valider_requisitions as vr')
                                            ->join('demandes as d','d.id','=','vr.demandes_id')
                                            ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                                            ->join('articles as ar','ar.ref_articles','=','ms.ref_articles')
                                            ->where('vr.demandes_id',$demande->demandes_ids)
                                            ->select('vr.qte as qte_validee','ar.ref_articles','ar.design_article')
                                            ->first();

                                            if($valider_requisition != null){
                                                $qte_validee_stock = $valider_requisition->qte_validee;                                           
                                            }

                                            $qte_validee_pilote = null;

                                            $valider_requisition_pilote = DB::table('valider_requisitions as vr')
                                            ->where('vr.demandes_id',$demande->demandes_id)
                                            ->select('vr.qte as qte_validee_pilote')
                                            ->first();

                                            if($valider_requisition_pilote != null){

                                                $qte_validee_pilote = $valider_requisition_pilote->qte_validee_pilote;                                           
                                            }

                                            $distribution = DB::table('distributions')
                                            ->where('demande_consolides_id',$demande->demande_consolides_id)
                                            ->groupBy('demande_consolides_id')
                                            ->select(DB::raw('SUM(qte) as qte_recue'))
                                            ->where('flag_reception', '!=' , 2)
                                            ->first();

                                            

                                            if ($distribution!=null) {
                                                $qte_recue = (int) $distribution->qte_recue;
                                                //$qte_reste = $demande->qte - $qte_recue;
                                                $qte_reste = $qte_validee_stock - $qte_recue;

                                                //dd($qte_recue, $demande->qte,$demande->demande_consolides_id,$distribution,$demandes , $demande);
                                            }

                                            $info = "ATTENTION !!! pour l'article «".$demande->design_article."», le stock a validé «".str_pad($qte_validee_stock, 2, "0", STR_PAD_LEFT)."» en quantité. Veuillez répartir cette quantité aux différents demandeurs de votre structure. Répartition non modifiable";

                                            
                                        
                                        ?>
                                        <tr>
                                            <td style="vertical-align:middle; border-collapse: collapse; padding: 10px; margin: 0;">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control demande_consolides_id" value="{{ $demande->demande_consolides_id ?? '' }}" id="demande_consolides_id" name="demande_consolides_id[{{ $index }}]"
                                                
                                                @if(isset($verou))
                                                    disabled
                                                @endif
                                                
                                                >


                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control demandes_ids" value="{{ $demande->demandes_ids ?? '' }}" id="demandes_ids" name="demandes_ids[{{ $index }}]" 
                                                
                                                @if(isset($verou))
                                                    disabled
                                                @endif
                                                
                                                >

                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control demandes_id" value="{{ $demande->demandes_id ?? '' }}" id="demandes_id" name="demandes_id[{{ $index }}]" 
                                                
                                                @if(isset($verou))
                                                    disabled
                                                @endif

                                                >


                                                <span>{{ 'M'.$demande->mle ?? '' }} - <span>{{ $demande->nom_prenoms ?? '' }}</span></span>                                       
                                            </td>
                                            <td style="vertical-align:middle; text-align:left; border-collapse: collapse; padding: 0; margin: 0;">
                                                &nbsp;&nbsp;&nbsp;{{ $demande->ref_articles ?? '' }}&nbsp;&nbsp;&nbsp;
                                            </td>
                                            <td style="vertical-align:middle; border-collapse: collapse; padding: 0; margin: 0;">
                                                &nbsp;&nbsp;&nbsp;{{ $demande->design_article ?? '' }}&nbsp;&nbsp;&nbsp;</td>
                                            <td style="vertical-align:middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">

                                                <input onfocus="this.blur()" onkeypress="validate(event)" style="text-align: center; display:none" class="form-control qte_demandee" value="{{ old('qte_demandee') ?? $demande->qte ?? '' }}" id="qte_demandee" name="qte_demandee[{{ $index }}]" 
                                                
                                                @if(isset($verou))
                                                    disabled
                                                    @else
                                                    required
                                                @endif

                                                >

                                                {{ $demande->qte ?? '' }}
                                            </td>
                                            <td title="{{ $info ?? '' }}" style="vertical-align:middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                                {{ $qte_validee_stock ?? '' }} &nbsp;
                                                
                                                <sup>
                                                    <svg style="color:#f86c6b" xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                                                    </svg>
                                                </sup>

                                                <input autocomplete="off" required onkeypress="validate(event)" style="text-align: center; display:none" class="form-control qte_validee_stock" value="{{ $qte_validee_stock ?? '' }}" id="qte_validee_stock" name="qte_validee_stock[{{ $index }}]"
                                                
                                                @if(isset($verou))
                                                    disabled
                                                    @else
                                                    required
                                                @endif

                                                >
                                                

                                            </td>

                                            <td style="vertical-align:middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">

                                                <input autocomplete="off" onkeyup="editQte(this)" onkeypress="validate(event)" style="text-align: center; display:; @if(isset($qte_validee_pilote)) background-color:transparent; border:none; @endif @if(isset($verou)) background-color:transparent; border:none; @endif " class="form-control qte_validee {{ 'tr_art_validee'.$demande->demandes_ids }}" value="{{ $qte_validee_pilote ?? '' }}" id="qte_validee" name="qte_validee[{{ $index }}]"

                                                @if(isset($qte_validee_pilote))         
                                                onfocus="this.blur()" 
                                                @endif
                                                
                                                @if(isset($verou))
                                                    onfocus="this.blur()"
                                                    @else
                                                    required
                                                @endif

                                                >

                                                
                                            </td>
                                                
                                            <td style="vertical-align:middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">

                                                <input autocomplete="off" onkeypress="validate(event)" style="text-align: center; display:none" class="form-control qte_recue" value="{{ $qte_recue ?? 0 }}" id="qte_recue" name="qte_recue[{{ $index }}]" disabled>

                                                {{ $qte_recue ?? 0 }}
                                            </td>
                                            <td style="vertical-align:middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0; @if($statut_distribution === 0) display:none; @endif">
                                                <input onkeypress="validate(event)" onkeyup="editDistribution(this)" class="form-control qte_livree {{ 'tr_art'.$demande->demandes_ids }}" 
                                                autocomplete="off"
                                                
                                                {{-- value="{{ old('') ?? $qte_reste ?? $demande->qte ?? '' }}"  --}}
                                                
                                                id="qte_livree" name="qte_livree[{{ $index }}]" 
                                                
                                                @if(isset($qte_reste))
                                                    @if(($qte_reste === 0))
                                                        onfocus="this.blur()"
                                                        style="text-align: center; background-color:transparent; border:none"
                                                        @else
                                                        style="text-align: center; @if(isset($verou)) background-color:transparent; border:none @endif "
                                                    @endif
                                                    @else
                                                    style="text-align: center; @if(isset($verou)) background-color:transparent; border:none @endif"
                                                @endif
                                                

                                                @if(isset($verou))
                                                    onfocus="this.blur()"
                                                @endif

                                                >
                                            </td>
                                            <td style="vertical-align:middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0; @if($statut_distribution === 0) display:none; @endif ">
                                                <input type="checkbox" name="approvalcd[{{ $index }}]"
                                                
                                                @if(isset($qte_reste))
                                                    @if(($qte_reste === 0))
                                                        disabled
                                                    @endif
                                                @endif

                                                @if(isset($verou))
                                                    disabled
                                                @endif

                                                >
                                            </td>
                                        </tr>
                                        <?php  $index++;  ?>
                                    @endforeach
                                    
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
                                <tr 
                                @if($statut_distribution === 0)
                                    style="border: none; display:none;"
                                @endif >

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
                                                @if(count($demandes) > 0 && $statut_distribution!=0)
                                                    <button onclick="return confirm('Êtes-vous sûr de vouloir valider cette distribution ?')" style="margin-top: 5px;" type="submit" name="submit" value="distribution" class="btn btn-success">Distribuer</button>
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


    editQte = function(e){
        var tr=$(e).parents("tr");  
        
        var demandes_ids=tr.find('.demandes_ids').val();
        demandes_ids = demandes_ids.trim();
        demandes_ids = demandes_ids.replace(' ','');
        demandes_ids = reverseFormatNumber(demandes_ids,'fr');
        demandes_ids = demandes_ids.replace(' ','');
        demandes_ids = demandes_ids * 1;

        var qte_demandee=tr.find('.qte_demandee').val();
        qte_demandee = qte_demandee.trim();
        qte_demandee = qte_demandee.replace(' ','');
        qte_demandee = reverseFormatNumber(qte_demandee,'fr');
        qte_demandee = qte_demandee.replace(' ','');
        qte_demandee = qte_demandee * 1;

        var qte_validee_stock=tr.find('.qte_validee_stock').val();
        qte_validee_stock = qte_validee_stock.trim();
        qte_validee_stock = qte_validee_stock.replace(' ','');
        qte_validee_stock = reverseFormatNumber(qte_validee_stock,'fr');
        qte_validee_stock = qte_validee_stock.replace(' ','');
        qte_validee_stock = qte_validee_stock * 1;

        var qte_validee=tr.find('.qte_validee').val();
        qte_validee = qte_validee.trim();
        qte_validee = qte_validee.replace(' ','');
        qte_validee = reverseFormatNumber(qte_validee,'fr');
        qte_validee = qte_validee.replace(' ','');
        qte_validee = qte_validee * 1;   
        
        if(qte_demandee < qte_validee) {

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Attention!!! : la quantité validée par le Pilote AEE ne peut être supérieure à la quantité demandée par le bénéficiaire',
            focusConfirm: false,
            confirmButtonText:
            'Compris'
            });

            qte_validee = 0;

        }

        if(qte_validee_stock < qte_validee) {

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Attention!!! : la quantité validée par le Pilote AEE ne peut être supérieure à la quantité validée par le stock',
            focusConfirm: false,
            confirmButtonText:
            'Compris'
            });

            qte_validee = 0;

        }

        var qte_livree=tr.find('.qte_livree').val();
        qte_livree = qte_livree.trim();
        qte_livree = qte_livree.replace(' ','');
        qte_livree = reverseFormatNumber(qte_livree,'fr');
        qte_livree = qte_livree.replace(' ','');
        qte_livree = qte_livree * 1;

        if(qte_livree > qte_validee) {

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité validée',
            focusConfirm: false,
            confirmButtonText:
            'Compris'
            });

            qte_livree = 0;

        }

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});

        tr.find('.qte_validee').val(int3.format(qte_validee));
        totalQte(demandes_ids.toString(),qte_validee,tr);



        tr.find('.qte_livree').val(int3.format(qte_livree));
        totalDistribution(demandes_ids.toString(),qte_livree,tr);
        
        
    }

    function totalQte(demandes_ids,qte_validee,tr){

        var totat_qte_validee=0;
        var class_name_validee = '.tr_art_validee'+demandes_ids;
        $(class_name_validee).each(function(i,e){
            var tr_art_validee =$(this).val()-0;
            totat_qte_validee +=tr_art_validee;
        });

        totat_qte_validee = totat_qte_validee * 1;
        
        var qte_validee_stock_reel = 'qte_validee_stock_reel'+demandes_ids;
        var total_qte_validee_stock_reel = document.getElementById(qte_validee_stock_reel).value;
        total_qte_validee_stock_reel = total_qte_validee_stock_reel * 1;
        
        var qte_validee_disponible = total_qte_validee_stock_reel - totat_qte_validee;
        qte_validee_disponible = qte_validee_disponible * 1;

        //alert(totat_qte_validee);
        //alert(qte_validee+' '+qte_validee_disponible);
        
        if((qte_validee > qte_validee_disponible) && (0 > qte_validee_disponible)) {

            Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité totale validée par le Pilote AEE ne peut être supérieure à la quantité totale validée par le stock',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
            });

            qte_validee = 0;
            tr.find('.qte_validee').val(qte_validee);

        }
        
        var actual_qte_validee_stock = 'actual_qte_validee_stock'+demandes_ids;
        document.getElementById(actual_qte_validee_stock).value = qte_validee_disponible;

    }
    
    editDistribution = function(e){
        var tr=$(e).parents("tr");

        var demandes_ids=tr.find('.demandes_ids').val();
        demandes_ids = demandes_ids.trim();
        demandes_ids = demandes_ids.replace(' ','');
        demandes_ids = reverseFormatNumber(demandes_ids,'fr');
        demandes_ids = demandes_ids.replace(' ','');
        demandes_ids = demandes_ids * 1;

        var qte_livree=tr.find('.qte_livree').val();
        qte_livree = qte_livree.trim();
        qte_livree = qte_livree.replace(' ','');
        qte_livree = reverseFormatNumber(qte_livree,'fr');
        qte_livree = qte_livree.replace(' ','');
        qte_livree = qte_livree * 1;

        var qte_recue=tr.find('.qte_recue').val();
        qte_recue = qte_recue.trim();
        qte_recue = qte_recue.replace(' ','');
        qte_recue = reverseFormatNumber(qte_recue,'fr');
        qte_recue = qte_recue.replace(' ','');
        qte_recue = qte_recue * 1;

        var qte_validee=tr.find('.qte_validee').val();
        qte_validee = qte_validee.trim();
        qte_validee = qte_validee.replace(' ','');
        qte_validee = reverseFormatNumber(qte_validee,'fr');
        qte_validee = qte_validee.replace(' ','');
        qte_validee = qte_validee * 1;

        qte_potentielle_a_livrer = qte_recue + qte_livree;

        if(qte_livree > qte_validee) {

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité validée',
            focusConfirm: false,
            confirmButtonText:
            'Compris'
            });

            qte_livree = 0;

        }

        if(qte_potentielle_a_livrer > qte_validee) {

            Swal.fire({
            title: '<strong>e-GESTOCK</strong>',
            icon: 'error',
            html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité validée par le pilote AEE',
            focusConfirm: false,
            confirmButtonText:
            'Compris'
            });

            qte_livree = 0;

        }

        var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
        tr.find('.qte_livree').val(int3.format(qte_livree));
        
        totalDistribution(demandes_ids.toString(),qte_livree,tr);
    }

    function totalDistribution(demandes_ids,qte_livree,tr){

        var totat_servi=0;
        var class_name = '.tr_art'+demandes_ids;
        $(class_name).each(function(i,e){
            var tr_art =$(this).val()-0;
            totat_servi +=tr_art;
        });

        totat_servi = totat_servi * 1;

        var id_name_reel = 'art_reel'+demandes_ids;
        var stock_reel = document.getElementById(id_name_reel).value;
        stock_reel = stock_reel * 1;

        
        var stock_disponible = stock_reel - totat_servi;
        stock_disponible = stock_disponible * 1;

        // alert(qte_livree+' '+stock_disponible);
        if((qte_livree > stock_disponible) && (0 > stock_disponible)) {

            Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité disponible en stock',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
            });

            qte_livree = 0;
            tr.find('.qte_livree').val(qte_livree);

            var totat_servi=0;
            var class_name = '.tr_art'+demandes_ids;
            $(class_name).each(function(i,e){
                var tr_art =$(this).val()-0;
                totat_servi +=tr_art;
            });

            totat_servi = totat_servi * 1;

            var id_name_reel = 'art_reel'+demandes_ids;
            var stock_reel = document.getElementById(id_name_reel).value;
            stock_reel = stock_reel * 1;

            
            var stock_disponible = stock_reel - totat_servi;
            stock_disponible = stock_disponible * 1;

        }

        var id_name = 'art'+demandes_ids;
        document.getElementById(id_name).value = stock_disponible;

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