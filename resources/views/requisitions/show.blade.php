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
            font-size:10px;

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
<div class="container" style="font-size:10px;line-height: 10px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('DISTRIBUTION') }} 
                    <span style="float: right; margin-right: 20px;">DATE DEMANDE : <strong>{{ date("d/m/Y",strtotime($requisitions->created_at)) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">STRUCTURE : <strong>{{ $requisitions->nom_structure ?? '' }}</strong></span>
                    
                </div>

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
                    <form>
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="label">N° Bon de commande</label>
                                    <input value="{{ $requisitions->num_bc ?? '' }}" onfocus="this.blur()" style=" color:red; text-align:left; font-weight:bold" style="border:none" type="text" class="form-control griser">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; font-weight:bold" value="{{ $requisitions->intitule ?? '' }}" required type="text" name="intitule" class="form-control" placeholder="Intitulé de la réquisition">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="label">Gestion</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:left; font-weight:bold" value="{{ $requisitions->code_gestion. ' - '.$requisitions->libelle_gestion }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion">
                                </div>
                            </div>
                        </div>                        

                        <div class="panel panel-footer"><!--table-bordered-->
                            <table class="table  table-striped" id="myTable" style="width:100%">
                                <thead>
                                    <tr style="background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="text-align:center; vertical-align:middle; width: 1%; white-space: nowrap;">RÉF. ARTICLE</th>
                                        <th style="text-align:center; vertical-align:middle;" width="80%">DÉSIGNATION ARTICLE</th>
                                        <th style=" text-align:center; display:none; width: 1%; white-space: nowrap; vertical-align:middle;">PRIX U</th>
                                        <th style="text-align:center; width: 1%; white-space: nowrap; vertical-align:middle;">QUANTITÉ <br/>DEMANDÉE</th>
                                        @if(isset($requisition_validee))
                                            <th style="text-align:center; vertical-align:middle; width: 1%; white-space: nowrap;">QUANTITÉ <br/>VALIDÉE</th>
                                        @endif
                                        
                                        @if(isset($requisition_livree))
                                            <th style="text-align:center;width: 1%; white-space: nowrap; vertical-align:middle;">QUANTITÉ <br/>LIVRÉE</th>

                                            <th style="text-align:center;width: 1%; white-space: nowrap; vertical-align:middle;">QUANTITÉ <br/>REÇUE</th>

                                        @endif
                                        
                                        <th style="text-align:center; display:none; width: 1%; white-space: nowrap; vertical-align:middle;">COÛT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="display: none">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    
                                    @foreach($demandes as $demande)
                                    <?php 
                                            
                                            $montant = $demande->qte * $demande->cmup;
                                            $montant = strrev(wordwrap(strrev($montant), 3, ' ', true));
                                            $demande->qte = strrev(wordwrap(strrev($demande->qte), 3, ' ', true));
                                            $demande->cmup = strrev(wordwrap(strrev($demande->cmup), 3, ' ', true));

                                            if (isset($qte_validee)) {
                                                unset($qte_validee);
                                            }

                                            if (isset($qte_livree)) {
                                                unset($qte_livree);
                                            }

                                            

                                            
                                            if (isset($requisition_validee)) {
                                                $qte_validee = 0;
                                            }

                                            if (isset($requisition_livree)) {
                                                $qte_livree = 0;

                                                $qte_recue = 0;
                                            }
                                            
                                            if (isset($demande->flag_consolide)) {

                                                if($demande->flag_consolide === 1){
                                                    $valider_requisitions = DB::table('requisitions as r')
                                                    ->join('demandes as d','d.requisitions_id','=','r.id')
                                                    ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                                                    ->join('profils as p', 'p.id', '=', 'vr.profils_id')
                                                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                                                    ->where('tp.name', 'Responsable des stocks')
                                                    ->where('r.id',$demande->requisitions_id)
                                                    ->where('d.id',$demande->id)
                                                    ->select(DB::raw('SUM(vr.qte) as qte_validee'))
                                                    ->where('vr.flag_valide',1)
                                                    ->first();

                                                    if ($valider_requisitions!=null) {
                                                        $qte_validee = $valider_requisitions->qte_validee;
                                                    }else{
                                                        $valider_requisition = DB::table('requisitions as r')
                                                        ->join('demandes as d','d.requisitions_id','=','r.id')
                                                        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                                                        ->select('dc.qte as qte_validee')
                                                        ->where('dc.demandes_id',$demande->id)
                                                        ->where('r.id',$demande->requisitions_id)
                                                        ->first();
                                                        if ($valider_requisition!=null) {
                                                            $qte_validee = $valider_requisition->qte_validee;
                                                        }
                                                    }

                                                    $livraisons = DB::table('requisitions as r')
                                                        ->join('demandes as d','d.requisitions_id','=','r.id')
                                                        ->join('livraisons as l','l.demandes_id','=','d.id')
                                                        ->where('r.id',$demande->requisitions_id)
                                                        ->where('d.id',$demande->id)
                                                        ->select(DB::raw('SUM(l.qte) as qte_livree'),DB::raw('SUM(l.qte_recue) as qte_recue'))
                                                        ->first();

                                                    if ($livraisons!=null) {
                                                        $qte_livree = $livraisons->qte_livree;
                                                        $qte_recue = $livraisons->qte_recue;
                                                    }else{
                                                        $consommation = DB::table('requisitions as r')
                                                        ->join('demandes as d','d.requisitions_id','=','r.id')
                                                        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                                                        ->join('distributions as di','di.demande_consolides_id','=','dc.id')
                                                        ->join('consommations as c','c.distributions_id','=','di.id')
                                                        ->groupBy('di.demande_consolides_id')
                                                        ->select(DB::raw('SUM(c.qte) as qte_livree'))
                                                        ->where('dc.demandes_id',$demande->id)
                                                        ->where('r.id',$demande->requisitions_id)
                                                        ->first();
                                                        if ($consommation!=null) {
                                                            $qte_livree = $consommation->qte_livree;
                                                        }
                                                    }
                                                }elseif($demande->flag_consolide === 0){

                                                    

                                                    $valider_requisitions = DB::table('requisitions as r')
                                                    ->join('demandes as d','d.requisitions_id','=','r.id')
                                                    ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                                                    ->join('profils as p', 'p.id', '=', 'vr.profils_id')
                                                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                                                    ->where('tp.name', 'Pilote AEE')
                                                    ->where('r.id',$demande->requisitions_id)
                                                    ->where('d.id',$demande->id)
                                                    ->select('vr.qte as qte_validee')
                                                    ->where('vr.flag_valide',1)
                                                    ->first();

                                                    if ($valider_requisitions!=null) {

                                                        $qte_validee = $valider_requisitions->qte_validee;

                                                        $demande_consolides = DB::table('requisitions as r')
                                                        ->join('demandes as d','d.requisitions_id','=','r.id')
                                                        ->join('demande_consolides as dc','dc.demandes_id','=','d.id')
                                                        ->join('distributions as di','di.demande_consolides_id','=','dc.id')
                                                        ->where('r.id',$demande->requisitions_id)
                                                        ->where('d.id',$demande->id)
                                                        ->select(DB::raw('SUM(di.qte) as qte_livree'),DB::raw('SUM(di.qte_recue) as qte_recue'))
                                                        ->first();

                                                        if ($demande_consolides!=null) {
                                                            $qte_livree = $demande_consolides->qte_livree;
                                                            $qte_recue = $demande_consolides->qte_recue;
                                                        }

                                                    }

                                                    if ($valider_requisitions === null) {
                                                        $valider_requisitions = DB::table('requisitions as r')
                                                        ->join('demandes as d','d.requisitions_id','=','r.id')
                                                        ->join('valider_requisitions as vr','vr.demandes_id','=','d.id')
                                                        ->join('profils as p', 'p.id', '=', 'vr.profils_id')
                                                        ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                                                        ->where('tp.name', 'Responsable des stocks')
                                                        ->where('r.id',$demande->requisitions_id)
                                                        ->where('d.id',$demande->id)
                                                        ->select(DB::raw('SUM(vr.qte) as qte_validee'))
                                                        ->where('vr.flag_valide',1)
                                                        ->first();

                                                        if ($valider_requisitions!=null) {
                                                            $qte_validee = $valider_requisitions->qte_validee;


                                                            $livraison = DB::table('requisitions as r')
                                                            ->join('demandes as d','d.requisitions_id','=','r.id')
                                                            ->join('livraisons as l','l.demandes_id','=','d.id')
                                                            ->where('r.id',$demande->requisitions_id)
                                                            ->where('d.id',$demande->id)
                                                            ->select(DB::raw('SUM(l.qte) as qte_livree'),DB::raw('SUM(l.qte) as qte_recue'))
                                                            ->first();

                                                            if ($livraison!=null) {
                                                                $qte_livree = $livraison->qte_livree;
                                                                $qte_recue = $livraison->qte_recue;
                                                            }

                                                        }

                                                        //dd($livraison,$valider_requisitions,$demande,$demande->flag_consolide);
                                                    }

                                                    

                                                }

                                            }

                                            
                                            
                                    ?>
                                    
                                    <tr>
                                        <td class="td-left">
                                            <input autocomplete="off" style="background-color: #e9ecef;font-size:10px; text-align:center; font-weight:bold; display:none" required value="{{ $demande->ref_articles ?? '' }}" onfocus="this.blur()" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px;">

                                            <input style="display: none; font-size:10px;" value="{{ $demande->id ?? '' }}" type="text" id="demandes_id" name="demandes_id[]" class="form-control demandes_id">

                                            <input style="display:none ; font-size:10px;" value="{{ $demande->magasin_stocks_id ?? '' }}" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id">

                                            {{ $demande->ref_articles ?? '' }}

                                        </td>
                                        <td class="td-left">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef;font-size:10px; display:none" required value="{{ $demande->design_article ?? '' }}" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article">
                                            {{ $demande->design_article ?? '' }}
                                        </td>
                                        <td style="display: none">
                                            <input onfocus="this.blur()" required value="{{ $demande->cmup ?? '' }}" style="background-color: #e9ecef; text-align: right; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup">
                                        </td>
                                        <td class="td-center">
                                            <input onfocus="this.blur()" required value="{{ $demande->qte ?? '' }}" style="text-align: center; font-size:10px; background-color: #e9ecef; display:none" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte">

                                            {{ $demande->qte ?? '' }}
                                        </td>
                                        @if(isset($requisition_validee))
                                            <td class="td-center" style="vertical-align: middle">
                                                <input onfocus="this.blur()" required value="{{ $qte_validee ?? 0 }}" style="text-align: center; font-size:10px; background-color: #e9ecef; display:none" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_validee[]" class="form-control qte_validee">
                                                
                                                {{ $qte_validee ?? 0 }}
                                            </td>
                                        @endif
                                        @if(isset($requisition_livree))
                                            <td class="td-center" style="vertical-align: middle">
                                                <input onfocus="this.blur()" required value="{{ $qte_livree ?? 0 }}" style="text-align: center; font-size:10px; background-color: #e9ecef; display:none" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_livree[]" class="form-control qte_livree">

                                                {{ $qte_livree ?? 0 }}

                                            </td>

                                            <td class="td-center" style="vertical-align: middle">
                                                <input onfocus="this.blur()" required value="{{ $qte_recue ?? 0 }}" style="text-align: center; font-size:10px; background-color: #e9ecef; display:none" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_recue[]" class="form-control qte_recue">

                                                {{ $qte_recue ?? 0 }}

                                            </td>
                                        @endif

                                        <td style="display: none">
                                            <input onfocus="this.blur()" required value="{{ $montant ?? '' }}" style="background-color: #e9ecef; text-align: right;font-size:10px;" type="text" onkeypress="validate(event)" name="montant[]" class="form-control montant">
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                            <table style="width:100%;"> 
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
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



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