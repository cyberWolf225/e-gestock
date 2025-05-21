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
                <div class="card-header entete-table">{{ "CONFIRMATION DE LA RÉCEPTION DES ARTICLES" }} 
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
                    <form method="POST" action="{{ route('livraisons.receptions_store')  }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="label">N° Bon de commande</label>
                                    <input value="{{ $requisitions->num_bc ?? '' }}" onfocus="this.blur()" style="color:red; text-align:left; font-weight:bold" style="border:none" type="text" class="form-control griser">

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
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:left; font-weight:bold" value="{{ $requisitions->code_gestion. ' - '.$requisitions->libelle_gestion }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion">
                                </div>
                            </div>
                        </div>
                        

                        <div class="panel panel-footer">
                            <table class="table table-striped" id="myTable" style="width:100%">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="vertical-align:middle; text-align:center; width:8%">RÉF. ARTICLE</th>
                                        <th style="vertical-align:middle; text-align:center; width:30%">DÉSIGNATION ARTICLE</th>
                                        <th style="vertical-align:middle; text-align:center; width:1%; white-space:nowrap">DATE LIVRAISON</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%; display:none">QTÉ VALIDÉE</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">QTÉ LIVRÉE</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">QTÉ DÉJÀ REÇUE</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">QTÉ REÇUE<span style="color: red"><sup> *</sup></span></th>
                                        <th style="text-align:center; width:1%; display:"><a href="/livraisons/distribution/{{ $requisitions->id }}"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-counterclockwise" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                                        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                                        </svg></a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $index = 0; ?>
                                    @foreach($demandes as $demande)

                                        <?php 

                                            //quantité deja livrée
                                            if (isset($qte_recue)) {
                                               unset($qte_recue);
                                            } 

                                            if (isset($qte_reste)) {
                                                unset($qte_reste);
                                            }

                                            if (isset($qte_consomme)) {
                                                unset($qte_consomme);
                                            }

                                            if (isset($qte_restant)) {
                                                unset($qte_restant);
                                            }

                                            $qte_recue = $demande->qte;

                                            $consommation = DB::table('distributions as d')
                                            ->join('consommations as c','c.distributions_id','=','d.id')
                                            ->where('d.id',$demande->distributions_id)
                                            ->groupBy('d.demande_consolides_id')
                                            ->select(DB::raw('SUM(c.qte) as qte_consomme'))
                                            ->first();

                                            if ($consommation!=null) {
                                                $qte_consomme = $consommation->qte_consomme;
                                                $qte_reste = $qte_recue - $qte_consomme;
                                                // $qte_reste = $demande->qte - $qte_consomme;
                                            }else{
                                                $qte_consomme = 0;
                                            }

                                            $qte_restant = $qte_recue - $qte_consomme;


                                            $distributions_id = $demande->distributions_id;
                                            $demandes_ids = $demande->demandes_ids;
                                            $ref_articles = $demande->ref_articles;
                                            $design_article = $demande->design_article;
                                            $created_at = $demande->created_at;
                                        ?>
                                        <tr>
                                            <td style="vertical-align:middle; text-align:left;  border-collapse: collapse; padding: 0; margin: 0;">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control distributions_id" value="{{ $distributions_id ?? '' }}" id="distributions_id" name="distributions_id[{{ $index }}]">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control demande_consolides_id" value="{{ $demande->demande_consolides_id ?? '' }}" id="demande_consolides_id" name="demande_consolides_id[{{ $index }}]">


                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control demandes_ids" value="{{ $demandes_ids ?? '' }}" id="demandes_ids" name="demandes_ids[{{ $index }}]">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none; background-color:#e9ecef" onfocus="this.blur()" class="form-control demandes_id" value="{{ $demande->demandes_id ?? '' }}" id="demandes_id" name="demandes_id[{{ $index }}]">

                                                &nbsp;&nbsp;&nbsp;{{ $ref_articles ?? '' }}
                                            </td>
                                            <td style="vertical-align:middle ; border-collapse: collapse; padding: 0; margin: 0;">
                                                &nbsp;&nbsp;&nbsp;{{ $design_article ?? '' }}</td>
                                            <td style="vertical-align:middle; text-align:center; width:1%; white-space:nowrap ; border-collapse: collapse; padding: 0; margin: 0;">
                                                @if(isset($created_at))
                                                    {{ date("d/m/Y H:i:s",strtotime($created_at)) }}
                                                @endif
                                                
                                            
                                            </td>
                                            <td style="vertical-align:middle; text-align:center; display:none ; border-collapse: collapse; padding: 0; margin: 0;">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none" class="form-control qte_validee" value="{{ $qte_reste ?? $demande->qte ?? '' }}" id="qte_validee" name="qte_validee[{{ $index }}]">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none" 
                                                onfocus="this.blur()"
                                                class="form-control qte_restant" value="{{ $qte_restant ?? 0 }}" id="qte_restant" name="qte_restant[{{ $index }}]">

                                                

                                                {{ $demande->qte ?? '' }}</td>
                                            <td style="vertical-align:middle; text-align:center;  border-collapse: collapse; padding: 0; margin: 0;">

                                                <input onkeypress="validate(event)" style="text-align: center; display:none" class="form-control qte_recue" value="{{ $qte_recue ?? '' }}" id="qte_recue" name="qte_recue[{{ $index }}]">

                                                {{ $qte_recue ?? 0 }}
                                            </td>
                                            <td style="vertical-align:middle; text-align:center;   border-collapse: collapse; padding: 0; margin: 0;">
                                                {{ $qte_consomme ?? 0 }}
                                            </td>

                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">

                                                

                                                <input onkeypress="validate(event)" onkeyup="editDistribution(this)" class="form-control qte_livree" value="{{ $qte_reste ?? $qte_recue ?? '' }}" id="qte_livree" name="qte_livree[{{ $index }}]" onfocus="this.blur()" style="text-align: center; background-color:transparent; border:none">
                                            </td>
                                            
                                            <td style="vertical-align:middle; text-align:center;   border-collapse: collapse; padding: 0; margin: 0; display:">
                                                <input checked type="checkbox" name="approvalcd[{{ $index }}]"
                                                
                                                @if(isset($qte_reste))
                                                    @if(($qte_reste === 0))
                                                        disabled
                                                    @endif
                                                @endif

                                                >
                                            </td>
                                        </tr>
                                        <?php $index++; ?>
                                    @endforeach
                                    
                                </tbody>
                                
                            </table>
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
                                                <textarea class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2" 
                                                
                                                @if(count($demandes) === 0)
                                                    onfocus="this.blur()"
                                                    style="width: 100%; font-size:10px; background-color: #e9ecef; resize:none"
                                                    @else
                                                    style="width: 100%; font-size:10px; resize:none" 
                                                @endif

                                                >{{ old('commentaire') ?? '' }}</textarea> 

                                                @error('commentaire')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="col-md-3" style="margin-top:;">
                                                @if(count($demandes) > 0)
                                                    <button onclick="return confirm('Êtes-vous sûr de vouloir valider cette distribution ?')" style="margin-top: 5px; width:70px" type="submit" name="submit" value="consommation" class="btn btn-success">Reçu</button>

                                                    <button onclick="return confirm('Êtes-vous sûr de vouloir annuler cette distribution ?')" style="margin-top: 5px; width:70px" type="submit" name="submit" value="annuler_consommation" class="btn btn-danger">Non reçu</button>
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


    
        editDistribution = function(e){
            var tr=$(e).parents("tr");

            var qte_recue=tr.find('.qte_recue').val();
            qte_recue = qte_recue.trim();
            qte_recue = qte_recue.replace(' ','');
            qte_recue = reverseFormatNumber(qte_recue,'fr');
            qte_recue = qte_recue.replace(' ','');
            qte_recue = qte_recue * 1;

            var qte_restant=tr.find('.qte_restant').val();
            qte_restant = qte_restant.trim();
            qte_restant = qte_restant.replace(' ','');
            qte_restant = reverseFormatNumber(qte_restant,'fr');
            qte_restant = qte_restant.replace(' ','');
            qte_restant = qte_restant * 1;

            var qte_livree=tr.find('.qte_livree').val();
            qte_livree = qte_livree.trim();
            qte_livree = qte_livree.replace(' ','');
            qte_livree = reverseFormatNumber(qte_livree,'fr');
            qte_livree = qte_livree.replace(' ','');
            qte_livree = qte_livree * 1;

            // var qte_stock=tr.find('.qte_stock').val();
            // qte_stock = qte_stock.trim();
            // qte_stock = qte_stock * 1;

            var qte_validee=tr.find('.qte_validee').val();
            qte_validee = qte_validee.trim();
            qte_validee = qte_validee.replace(' ','');
            qte_validee = reverseFormatNumber(qte_validee,'fr');
            qte_validee = qte_validee.replace(' ','');
            qte_validee = qte_validee * 1;

            
            

            

            if(qte_livree > qte_restant) {

                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité reçue ne peut être supérieure à la quantité livrée',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
                });

                qte_livree = qte_restant;

            }


            if(qte_livree > qte_recue) {

                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité reçue ne peut être supérieure à la quantité livrée',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
                });

                qte_livree = qte_recue;

            }
            // else if(qte > qte_stock) {

            //     Swal.fire({
            //     title: '<strong>e-GESTOCK</strong>',
            //     icon: 'error',
            //     html: 'Attention!!! : la quantité livrée ne peut être supérieure à la quantité disponible en stock... (Quantité disponible en stock : '+ qte_stock +' )',
            //     focusConfirm: false,
            //     confirmButtonText:
            //     'Compris'
            //     });

            //     qte = qte_stock;
            // }  


            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte_livree').val(int3.format(qte_livree));
            
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
