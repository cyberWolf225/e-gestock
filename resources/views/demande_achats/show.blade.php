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
<div class="container" style="color:black">
    @if(isset($type_profils_name))
        @if($type_profils_name != 'Fournisseur')
            @include('partials.workflow')
            @else
            <br>
        @endif
    @else
    <br>
    @endif
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table"> {{ __('APERÇUE DU BON DE COMMANDE') }} 

                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date("d/m/Y",strtotime($demande_achat_info->created_at ?? '')) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $demande_achat_info->exercice ?? '' }}</strong></span>                    
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
                    <form>
                        @csrf
                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 "> 
                                            
                                                @if(isset($statut))

                                                    @if($statut === "bon_commande")
                                                        N° Bon Cde.

                                                        @if($demande_achat_info!=null)
                                                            <?php
                                                            $num_bc = $demande_achat_info->num_bc;
                                                            ?>
                                                        @endif 

                                                    @elseif($statut === "demande_cotation")
                                                        N° Demande
                                                        @if($demande_achat_info!=null)
                                                            <?php
                                                            $num_bc = str_replace('BC','',$demande_achat_info->num_bc);
                                                            ?>
                                                        @endif 
                                                    @endif

                                                @endif
                                            
                                            </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="margin-top : -5px; color:red; font-weight:bold" autocomplete="off" type="text" name="num_bc" class="form-control griser" value="{{ $num_bc ?? '' }}">
                                            
                                            
                                        </div>
                                    </div>
                                    @if(isset($href_print))
                                        <div class="col-md-3">
                                            
                                                <div class="row d-flex" style="margin-top: 0px;">
                                                    <div class="col-sm-3">
                                                        <div class="form-group" style="text-align: right">
                                                            <a target="_blank" style="display:{{ $display_print }};" title="{{ $title_print }}" href="{{ $href_print }}" class="btn btn-secondary btn-sm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                                                                    <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                                                    <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                                                                    </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                        </div>
                                    @endif
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea onfocus="this.blur()" style="background-color: #e9ecef;resize:none" autocomplete="off" type="text" name="intitule" class="form-control griser">{{ $demande_achat_info->intitule ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Compte budg. </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="ref_fam" class="form-control griser" value="{{ $demande_achat_info->ref_fam ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" class="form-control griser" 
                                            value="{{ $demande_achat_info->design_fam ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                @if(isset($affiche_zone_struture))
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">
                                                <label class="label" class="mt-1">Structure <?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                            </div>
                                        </div>
                                        <div class="col-md-3 pr-1">
                                            <div class="form-group d-flex ">
                                                <input required style="background-color: #e9ecef" onfocus="this.blur()" autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control griser @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $credit_budgetaire_structure->code_structure ?? '' }}">
                                            </div>
                                            @error('code_structure')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 pl-0">
                                            <div class="form-group d-flex ">
                                                <input style="background-color: #e9ecef" required onfocus="this.blur()" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $credit_budgetaire_structure->nom_structure ?? '' }}">
                                            </div>
                                            @error('nom_structure')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                @endif
                                @if(isset($signataires))
                                    <?php $nbr_signataire = count($signataires); $compteur_signateur = 1;  ?>
                                    @foreach($signataires as $signataire)
                                        <?php $classement = null; if($compteur_signateur === 1){ $classement = 'er';}else{ $classement = 'eme';} ?>
                                        <div class="row d-flex" style="margin-top: -10px;">
                                            <div class="col-sm-3">
                                                <div class="form-group" style="text-align: right">
                                                    <label class="label" class="mt-1 "><?php if($nbr_signataire > 1){ if(isset($classement)){echo $compteur_signateur . '<sup>' . $classement . '</sup>'; } } ?> Signataire</label> 
                                                </div>
                                            </div>
                                            <div class="col-md-3 pr-1">
                                                <div class="form-group d-flex ">
                                                    <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="mle_signataire_dg" class="form-control griser" value="{{ 'M'.$signataire->mle ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 pl-0">
                                                <div class="form-group d-flex ">
                                                    <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_prenoms_signataire_dg" class="form-control griser" 
                                                    value="{{ $signataire->nom_prenoms ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <?php $compteur_signateur++; ?>
                                    @endforeach
                                @endif
                                
                                
                                

                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Gestion </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="code_gestion" class="form-control griser" value="{{ $demande_achat_info->code_gestion ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_gestion" class="form-control griser" value="{{ $demande_achat_info->libelle_gestion ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Fournisseur </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $demande_achat_info->entnum ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control griser">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $demande_achat_info->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1 ">Échéance Frs.</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_periode" class="form-control griser" value="{{ $demande_achat_info->libelle_periode ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="delai" class="form-control griser" value="{{ $demande_achat_info->delai ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-0">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="date_echeance" class="form-control griser" @if(isset($demande_achat_info->date_echeance))
                                            value="{{  date("d/m/Y H:i:s", strtotime($demande_achat_info->date_echeance)) }}"
                                            @endif >
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Devise</label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ old('code_devise') ?? $demande_achat_info->code_devise ?? '' }}" onkeyup="editDevise(this)" list="list_devise" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control griser">
                                        </div>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input value="{{ old('libelle_devise') ?? $demande_achat_info->libelle_devise ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control griser">
                                        </div>
                                    </div>
                                </div>
                                @if(isset($demande_achat_info->date_livraison_prevue))
                                    <div class="row d-flex" style="margin-top: -10px;">
                                        <div class="col-sm-3">
                                            <div class="form-group" style="text-align: right">
                                                <label class="label" class="mt-1 ">Date livraison prévue </label> 
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <input disabled autocomplete="off" type="date" name="date_livraison_prevue" class="form-control griser @error('date_livraison_prevue') is-invalid @enderror" @if(isset($demande_achat_info->date_livraison_prevue))
                                                value="{{  $demande_achat_info->date_livraison_prevue}}"
                                                @endif >
                                                @error('date_livraison_prevue')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                            
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        

                        <div class="panel panel-footer">
                            <table class="table table-bordered table-striped" id="myTable" style="width:100%; border-collapse: collapse; padding: 0; margin: 0;">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:1%; white-space:nowrap;   vertical-align:middle;text-align:center">RÉF.</th>
                                        <th style="width:30%;   vertical-align:middle;text-align:center">DÉSIGNATION ARTICLE</th>
                                        <th  
                                        @if(count($livraison_commandes) > 0)
                                            style="display: none"
                                        @else
                                            style="width:1%; white-space:nowrap;   vertical-align:middle;text-align:center"
                                        @endif
                                        >QTÉ DEMANDÉE</th>
                                        <th  
                                            @if($libelle === null )
                                                style="display: none"
                                            @else
                                                style="width:10%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >
                                            @if(count($livraison_commandes) > 0)
                                                QTÉ DEMANDÉE
                                            @else
                                                QTÉ COTATION
                                            @endif
                                        </th>

                                        <th  
                                            @if(count($livraison_commandes) <= 0)
                                                style="display: none"
                                            @else
                                                style="width:10%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >QTÉ LIVRÉE</th>

                                        <th  
                                            @if(count($livraison_commandes) <= 0)
                                                style="display: none"
                                            @else
                                                style="width:10%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >QTÉ LIVRÉE VALIDÉE</th>

                                        <th  
                                            @if($libelle === null )
                                                style="display: none"
                                            @else
                                                style="width:13%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >PU HT</th>
                                        <th  
                                            @if($libelle === null )
                                                style="display: none"
                                            @else
                                                style="width:8%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >REMISE (%)</th>
                                        <th  
                                            @if($libelle === null )
                                                style="display: none"
                                            @else
                                                style="width:15%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >MONTANT HT</th>

                                        <th  
                                            @if($libelle === null && $echantillonnage_cnps === null )
                                                style="display: none"
                                            @else
                                                style="width:1%;   vertical-align:middle;text-align:center"
                                            @endif
                                        >&nbsp;</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demande_achats as $demande_achat)

                                    <?php 

                                        $echantillon = null;
                                        if (isset($demande_achat->echantillon)) {
                                            $echantillon = $demande_achat->echantillon;
                                        }

                                        $echantillon_cnps = null;
                                        if (isset($demande_achat->echantillon_cnps)) {
                                            $echantillon_cnps = $demande_achat->echantillon_cnps;
                                        }
                                        
                                        
                                        if(count($livraison_commandes) > 0){
                                            $qte_livree = 0;
                                            $qte_livree_validee = null;
                                            $detail_livraisons_id = null;

                                            $livraison_commande = DB::table('demande_achats')
                                            ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
                                            ->join('livraison_commandes','livraison_commandes.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            
                                            ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('detail_cotations','detail_cotations.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                            ->join('detail_livraisons','detail_livraisons.detail_cotations_id','=','detail_cotations.id')
                                            ->where('demande_achats.id',$demande_achat->demande_achats_id)
                                            ->where('detail_cotations.id',$demande_achat->detail_cotations_id)
                                            ->select('detail_livraisons.qte as qte_livree','detail_livraisons.id as detail_livraisons_id')
                                            ->first();
                                            

                                            if ($livraison_commande!=null) {
                                                
                                                $detail_livraisons_id = $livraison_commande->detail_livraisons_id;

                                                $livraison_commande_sum = DB::table('demande_achats as da')

                                                ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')

                                                ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')

                                                ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')
                                                
                                                ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')

                                                //->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')

                                                

                                                ->where('da.id',$demande_achat->demande_achats_id)

                                                ->where('dl.detail_cotations_id',$demande_achat->detail_cotations_id)
                                                ->select(DB::raw('sum(dl.qte_frs) as qte_livree'))
                                                ->groupBy('dl.detail_cotations_id')
                                                ->first();
                                                
                                                if ($livraison_commande_sum!=null) {
                                                    $qte_livree = $livraison_commande_sum->qte_livree;
                                                }

                                                
                                                /*
                                                $livraison_valider = DB::table('demande_achats')
                                                ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
                                                ->join('livraison_commandes','livraison_commandes.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                                ->join('selection_adjudications','selection_adjudications.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                                ->join('detail_cotations','detail_cotations.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
                                                ->join('detail_livraisons','detail_livraisons.detail_cotations_id','=','detail_cotations.id')
                                                ->join('livraison_validers','livraison_validers.detail_livraisons_id','=','detail_livraisons.id')
                                                ->where('demande_achats.id',$demande_achat->demande_achats_id)
                                                ->where('detail_cotations.id',$demande_achat->detail_cotations_id)
                                                //->select(DB::raw('sum(livraison_validers.qte) as qte_livree_validee'))
                                                //->groupBy('detail_cotations.id')
                                                
                                                ->get();*/

                                                $livraison_valider = DB::table('demande_achats as da')

                                                ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')

                                                ->join('livraison_commandes as lc','lc.cotation_fournisseurs_id','=','cf.id')

                                                ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')
                                                
                                                ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')

                                                //->join('detail_cotations as dc','dc.cotation_fournisseurs_id','=','cf.id')

                                                

                                                ->where('da.id',$demande_achat->demande_achats_id)

                                                ->where('dl.detail_cotations_id',$demande_achat->detail_cotations_id)
                                                ->select(DB::raw('sum(dl.qte) as qte_livree_validee'))
                                                ->groupBy('dl.detail_cotations_id')
                                                ->where('lc.validation',1)
                                                ->first();

                                                if ($livraison_valider!=null) {
                                                    $qte_livree_validee = $livraison_valider->qte_livree_validee;
                                                }

                                            }
                                            
                                        }

                                        $prix_unit_partie_entiere = null;
                                        $prix_unit_partie_decimale = null;

                                        if (isset($demande_achat->prix_unit)) {
                                            $prix_unit = number_format((float)$demande_achat->prix_unit, 2, '.', '');

                                            $block_prix_unit = explode(".",$prix_unit);
                                            
                                            
                                            
                                            if (isset($block_prix_unit[0])) {
                                                $prix_unit_partie_entiere = $block_prix_unit[0];
                                            }

                                            
                                            if (isset($block_prix_unit[1])) {
                                                $prix_unit_partie_decimale = $block_prix_unit[1];
                                            }
                                        }

                                        $remise_partie_entiere = null;
                                        $remise_partie_decimale = null;
                                        
                                        if (isset($demande_achat->remise)) {
                                            $remise = number_format((float)$demande_achat->remise, 2, '.', '');

                                            $block_remise = explode(".",$remise);
                                            
                                            
                                            
                                            if (isset($block_remise[0])) {
                                                $remise_partie_entiere = $block_remise[0];
                                            }

                                            
                                            if (isset($block_remise[1])) {
                                                $remise_partie_decimale = $block_remise[1];
                                            }
                                        }

                                        $montant_ht_partie_entiere = null;
                                        $montant_ht_partie_decimale = null;
                                        
                                        if (isset($demande_achat->montant_ht)) {
                                            $montant_ht = number_format((float)$demande_achat->montant_ht, 2, '.', '');

                                            $block_montant_ht = explode(".",$montant_ht);
                                            
                                            
                                            
                                            if (isset($block_montant_ht[0])) {
                                                $montant_ht_partie_entiere = $block_montant_ht[0];
                                            }

                                            
                                            if (isset($block_montant_ht[1])) {
                                                $montant_ht_partie_decimale = $block_montant_ht[1];
                                            }
                                        }

                                        

                                        $description_articles_libelle = null;

                                        if(isset($demande_achat->cotation_fournisseurs_id)){
                                            $description_article = DB::table('detail_demande_achats as dda')
                                            ->join('description_articles as da','da.id','=','dda.description_articles_id')
                                            ->select('da.libelle')
                                            ->where('dda.demande_achats_id',$demande_achat->demande_achats_id)
                                            ->where('dda.ref_articles',$demande_achat->ref_articles)
                                            ->first();

                                            if ($description_article != null) {
                                                $description_articles_libelle = $description_article->libelle;
                                            }
                                        }else{
                                            $description_article = DB::table('detail_demande_achats as dda')
                                            ->join('description_articles as da','da.id','=','dda.description_articles_id')
                                            ->select('da.libelle')
                                            ->where('dda.demande_achats_id',$demande_achat->demande_achats_id)
                                            ->where('dda.id',$demande_achat->id)
                                            ->first();

                                            if ($description_article != null) {
                                                $description_articles_libelle = $description_article->libelle;
                                            }
                                        }

                                        
                                        
                                    ?>
                                    <tr>
                                        <td style="text-align: left; vertical-align:middle; width:1%; white-space:nowrap; border-collapse: collapse; padding: 0; margin: 0; font-weight:bold">

                                            &nbsp;&nbsp;{{ $demande_achat->ref_articles ?? '' }}&nbsp;&nbsp;
                                            <input class="form-control griser" type="text" style="display: none">

                                        </td>
                                        <td style="vertical-align:middle">

                                            {{ $demande_achat->design_article ?? '' }}

                                            @if(isset($description_articles_libelle))
                                                <br/>
                                                <br/>
                                                <span style="color:red; font-weight:bold" >{{ 'Description de l\'article' }}</span>
                                                <br/>
                                                {{ $description_articles_libelle ?? '' }}
                                                
                                            @endif
                                        </td>
                                        <td 
                                            @if(count($livraison_commandes) > 0)
                                            style="display: none"
                                            @else 
                                            style="text-align:center; vertical-align:middle"
                                            @endif 

                                        >
                                            {{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? $demande_achat->qte_demandee ?? $demande_achat->qte ?? ''), 3, ' ', true)) }}
                                        </td>
                                        <td 
                                            @if($libelle === null )
                                                style="display: none"
                                                @else 
                                            style="text-align:center; vertical-align:middle"
                                            @endif
                                        >

                                            {{ strrev(wordwrap(strrev($demande_achat->qte ?? ''), 3, ' ', true)) }}
                                        </td>
                                        <td 
                                            @if(count($livraison_commandes) <= 0)
                                                style="display: none"
                                                @else 
                                            style="text-align:center; vertical-align:middle"
                                            @endif
                                        >

                                            {{ strrev(wordwrap(strrev($qte_livree ?? ''), 3, ' ', true)) }}
                                        </td>
                                        <td 
                                            @if(count($livraison_commandes) <= 0)
                                                style="display: none"
                                                @else 
                                            style="text-align:center; vertical-align:middle"
                                            @endif
                                        >
                                            {{ strrev(wordwrap(strrev($qte_livree_validee ?? ''), 3, ' ', true)) }}

                                        </td>
                                        <td 
                                            @if($libelle === null )
                                                style="display: none"
                                                @else 
                                            style="text-align:right; vertical-align:middle"
                                            @endif
                                        >

                                            {{-- {{ strrev(wordwrap(strrev($demande_achat->prix_unit ?? ''), 3, ' ', true)) }} --}}

                                            @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}
                                                @else
                                                {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif


                                        </td>
                                        <td 
                                            @if($libelle === null )
                                                style="display: none"
                                                @else 
                                            style="text-align:center; vertical-align:middle"
                                            @endif
                                        >

                                            {{-- {{ $demande_achat->remise ?? '' }} --}}

                                            @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}
                                                @else
                                                {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif

                                        </td>
                                        <td 
                                            @if($libelle === null )
                                                style="display: none"
                                                @else 
                                            style="text-align:right; vertical-align:middle"
                                            @endif
                                        >

                                            {{-- {{ strrev(wordwrap(strrev($demande_achat->montant_ht ?? ''), 3, ' ', true)) }} --}}

                                            @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}
                                                @else
                                                {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif

                                        </td>
                                        <td 
                                            @if($libelle === null && $echantillonnage_cnps === null )
                                                style="display: none; vertical-align:middle;"
                                            @else
                                                style="text-align: center; vertical-align:middle;"
                                            @endif
                                        >
                                        @if(isset($echantillon_cnps))
                                                

                                            <!-- Modal -->

                                                <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->ref_articles }}">
                                                        <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                        </svg>
                                                </a>
                                                <div class="modal fade" id="exampleModalCenter{{ $demande_achat->ref_articles }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="border:none; border-color:transparent">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="text-align: center">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Échantillon CNPS { <span style="color: orange">{{ $demande_achat->design_article ?? '' }}</span> } </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <img src='{{ asset('storage/'.$echantillon_cnps) }}' style='width:100%;'>
                                                        </div>
                                                        <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            <!-- Modal -->
                                            
                                        @endif
                                    
                                        @if(isset($echantillon))

                                            <!-- Modal -->

                                                <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->detail_cotations_id }}">
                                                        <svg style="cursor: pointer; color:blue; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                        </svg>
                                                </a>
                                                <div class="modal fade" id="exampleModalCenter{{ $demande_achat->detail_cotations_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="text-align: center">
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Échantillon { <span style="color: red">{{ $demande_achat->design_article ?? '' }}</span> } </h5>
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
                                    
                                </tbody>

                                <?php
                                        $montant_total_brut_partie_entiere = null;
                                        $montant_total_brut_partie_decimale = null;
                                        
                                        if (isset($demande_achat->montant_total_brut)) {
                                            $montant_total_brut = number_format((float)$demande_achat->montant_total_brut, 2, '.', '');

                                            $block_montant_total_brut = explode(".",$montant_total_brut);
                                            
                                            
                                            
                                            if (isset($block_montant_total_brut[0])) {
                                                $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
                                            }

                                            
                                            if (isset($block_montant_total_brut[1])) {
                                                $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
                                            }
                                        }

                                        $remise_generale_partie_entiere = null;
                                        $remise_generale_partie_decimale = null;
                                        
                                        if (isset($demande_achat->remise_generale)) {
                                            $remise_generale = number_format((float)$demande_achat->remise_generale, 2, '.', '');

                                            $block_remise_generale = explode(".",$remise_generale);
                                            
                                            
                                            
                                            if (isset($block_remise_generale[0])) {
                                                $remise_generale_partie_entiere = $block_remise_generale[0];
                                            }

                                            
                                            if (isset($block_remise_generale[1])) {
                                                $remise_generale_partie_decimale = $block_remise_generale[1];
                                            }
                                        }

                                        $montant_total_net_partie_entiere = null;
                                        $montant_total_net_partie_decimale = null;
                                        
                                        if (isset($demande_achat->montant_total_net)) {
                                            $montant_total_net = number_format((float)$demande_achat->montant_total_net, 2, '.', '');

                                            $block_montant_total_net = explode(".",$montant_total_net);
                                            
                                            
                                            
                                            if (isset($block_montant_total_net[0])) {
                                                $montant_total_net_partie_entiere = $block_montant_total_net[0];
                                            }

                                            
                                            if (isset($block_montant_total_net[1])) {
                                                $montant_total_net_partie_decimale = $block_montant_total_net[1];
                                            }
                                        }

                                        $montant_total_ttc_partie_entiere = null;
                                        $montant_total_ttc_partie_decimale = null;
                                        
                                        if (isset($demande_achat->montant_total_ttc)) {
                                            $montant_total_ttc = number_format((float)$demande_achat->montant_total_ttc, 2, '.', '');

                                            $block_montant_total_ttc = explode(".",$montant_total_ttc);
                                            
                                            
                                            
                                            if (isset($block_montant_total_ttc[0])) {
                                                $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
                                            }

                                            
                                            if (isset($block_montant_total_ttc[1])) {
                                                $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
                                            }
                                        }

                                        $net_a_payer_partie_entiere = null;
                                        $net_a_payer_partie_decimale = null;
                                        
                                        if (isset($demande_achat->net_a_payer)) {
                                            $net_a_payer = number_format((float)$demande_achat->net_a_payer, 2, '.', '');

                                            $block_net_a_payer = explode(".",$net_a_payer);
                                            
                                            
                                            
                                            if (isset($block_net_a_payer[0])) {
                                                $net_a_payer_partie_entiere = $block_net_a_payer[0];
                                            }

                                            
                                            if (isset($block_net_a_payer[1])) {
                                                $net_a_payer_partie_decimale = $block_net_a_payer[1];
                                            }
                                        }

                                        $montant_acompte_partie_entiere = null;
                                        $montant_acompte_partie_decimale = null;

                                        
                                        
                                        if (isset($demande_achat->montant_acompte)) {
                                            $montant_acompte = number_format((float)$demande_achat->montant_acompte, 2, '.', '');

                                            $block_montant_acompte = explode(".",$montant_acompte);
                                            
                                            
                                            
                                            if (isset($block_montant_acompte[0])) {
                                                $montant_acompte_partie_entiere = $block_montant_acompte[0];
                                            }

                                            
                                            if (isset($block_montant_acompte[1])) {
                                                $montant_acompte_partie_decimale = $block_montant_acompte[1];
                                            }
                                        }

                                        
                                ?>
                                <tfoot 
                                        @if($libelle === null )
                                            style="display: none"
                                        @endif
                                >
                                    <tr>
                                        <td colspan="9">
                                            <div class="row d-flex pl-3">
                                                    <div class="pr-0"><label class="label" class=" mt-1 mr-2">Montant total brut</label><br>  <input
                                                        
                                                        @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                        required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="montant_total_brut" class="form-control griser montant_total_brut"></div>

                                                    <div class="pl-1"><label class="label" class=" mt-1 mr-2">Remise</label><br>  <input onfocus="this.blur()"
                                                        
                                                        @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                        onkeyup="editRemiseGenerale(this)" autocomplete="off"  style="width:110px; background-color: #e9ecef;text-align:right" type="text" name="remise_generale" class="form-control griser remise_generale"></div>

                                                    <div class="pl-1"><label class="label" class=" mt-1 mr-2">Montant total net ht</label><br>  <input
                                                        
                                                        @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                        required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;;text-align:right" type="text" name="montant_total_net" class="form-control griser montant_total_net"></div>

                                                    <div class="pl-1"><label class="label" class=" mt-1 mr-2">TVA (%)</label><br>  <input onfocus="this.blur()" value="{{ $demande_achat->tva ?? '' }}" onkeyup="editRemiseGenerale(this)" autocomplete="off" style=" width:80px; background-color: #e9ecef;text-align:center" type="text" name="tva" class="form-control griser tva"></div>

                                                    <?php 
                                                    $montant_tva = 0;
                                                    $montant_tva_partie_entiere = null;
                                                    $montant_tva_partie_decimale = null;
                                                    if (isset($demande_achat->tva)) {

                                                        $montant_tva = round(number_format((float)(($demande_achat->montant_total_net * ($demande_achat->tva)/100)), 2, '.', ''));

                                                        $montant_tva = number_format((float)$montant_tva, 2, '.', '');

                                                        $block_montant_tva = explode(".",$montant_tva);
                                                        
                                                        
                                                        
                                                        if (isset($block_montant_tva[0])) {
                                                            $montant_tva_partie_entiere = $block_montant_tva[0];
                                                        }

                                                        
                                                        if (isset($block_montant_tva[1])) {
                                                            $montant_tva_partie_decimale = $block_montant_tva[1];
                                                        }
                                                    }
                                                    
    
                                                    ?>

                                                    <div class="pl-1"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input
                                                        
                                                        @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                                            value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                                @else
                                                                value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                            @endif
                                                        
                                                        autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="montant_tva" class="form-control griser montant_tva"></div>

                                                    <div class="pl-1"><label class="label" class=" mt-1 mr-2">Montant total ttc</label><br>  <input                                                         

                                                        @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                        
                                                        required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="montant_total_ttc" class="form-control griser montant_total_ttc"></div>
                                            </div>
                                            <div class="row d-flex pl-3">
                                                
                                                <div class="pr-0"><label class="label" class=" mt-1 mr-2">Assiette BNC</label><br>  <input value="{{ $demande_achat->assiete_bnc ?? '' }}" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="assiette" class="form-control griser assiette"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Taux BNC</label><br>  <input onfocus="this.blur()" value="{{ $demande_achat->taux_bnc ?? '' }}" autocomplete="off" style=" width:60px; background-color: #e9ecef" type="text" name="taux_bnc" class="form-control griser"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input value="{{ ($demande_achat->assiete_bnc ?? 0 * ($demande_achat->taux_bnc ?? 0)/100) }}" autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;" type="text" name="montant_bnc" class="form-control griser montant_bnc"></div>

                                                <div class="pl-1"><label class="label" class=" mt-1 mr-2">Net à payer</label><br>  <input
                                                    
                                                @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                @else
                                                    value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                @endif
                                                    
                                                    required autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:110px;text-align:right" type="text" name="net_a_payer" class="form-control griser net_a_payer"></div> 

                                                <div style="padding-left:3px; text-align:center" ><label class="label" class=" mt-1 mr-2">Acompte</label><br>  <input disabled style="" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" @if(isset($demande_achat)) @if($demande_achat!= null) @if(isset($demande_achat->acompte)) @if($demande_achat->acompte === 1) checked  @endif  @endif @endif @endif ></div>

                                                @if(isset($demande_achat->acompte))
                                                @if($demande_achat->acompte === 1)
                                                    <div style="padding-left: 10px; text-align: center" id="acompte_taux_div" ><label class="label" class=" mt-1 mr-2">(%)</label><br>  <input onfocus="this.blur()" onkeyup="editRemiseGenerale(this)" @if(isset($demande_achat)) @if($demande_achat!= null) value="{{ $demande_achat->taux_acompte ?? '' }}" @endif @endif autocomplete="off" onkeypress="validate(event)" style=" width:70px; text-align:center; background-color: #e9ecef;" type="text" name="taux_acompte" class="form-control griser taux_acompte"></div>


                                                    <div class="pl-1" 

                                                    @if(isset($demande_achat))
                                                    @if($demande_achat!= null)
                                                    
                                                        @if(isset($demande_achat->acompte))
                                                            @if($demande_achat->acompte === 1) 
                                                            
                                                            @else
                                                                style="display:none"
                                                            @endif

                                                        @endif


                                                    @else
                                                    style="display:none"
                                                    @endif
                                                    @endif
                                                    
                                                    id="acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input  @if(isset($demande_achat)) @if($demande_achat!= null) 
                                                    
                                                    @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                        value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                    
                                                    @endif @endif autocomplete="off" onfocus="this.blur()" onkeypress="validate(event)" style=" width:110px; text-align:right; background-color: #e9ecef;" type="text" name="montant_acompte" class="form-control griser montant_acompte"></div>

                                                    <div class="pl-1"

                                                    @if(isset($demande_achat))
                                                    @if($demande_achat!= null)
                                                    
                                                        @if(isset($demande_achat->acompte)) 
                                                            @if($demande_achat->acompte === 1) 
                                                                style="display:none"
                                                            @else
                                                        
                                                            @endif
                                                        @endif

                                                    @else
                                                    
                                                    @endif
                                                    @endif

                                                    id="i_acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control griser"></div>

                                                @endif
                                                @endif
                                            </div>


                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                                <tr>
                                    <td style="border: none;vertical-align: middle; text-align:right"  colspan="2">
                                        @if(!isset($griser)) 
                                            <a title="Rattacher un nouveau dépôt" onclick="myCreateFunction2()" href="#">
                                                <svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg>
                                            </a> 
                                        @endif
                                    </td>
                                </tr>
                                @if(count($piece_jointes)>0)
                                    @foreach($piece_jointes as $piece_jointe)
                                        <tr>
                                            <td>
                                                @if(!isset($griser))
                                                    <input type="file" name="piece[]" class="form-control griser-file" id="piece" >
                                                @endif
                                                 
                                            </td>
                                            <td style="border: none;vertical-align: middle; text-align:right">
                                                <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>  
                                                <a @if(isset($griser)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 ) }}">
                                                    <svg style="color: blue; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                      </svg>
                                                </a>
                                                <input @if(isset($griser)) disabled @endif style="display: none" onfocus="this.blur()" class="form-control griser" name="piece_jointes_id[{{ $piece_jointe->id ?? 0 }}]" value="{{ $piece_jointe->id ?? 0 }}">
                                                
                                                <input @if(isset($griser)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                                @if(isset($piece_jointe->flag_actif))
                                                    @if($piece_jointe->flag_actif == 1)
                                                        checked
                                                    @endif
                                                @endif >

                                                @if(!isset($griser)) 

                                                    <a  title="Retirer ce fichier" onclick="removeRow2(this)" href="#">
                                                        <svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                        </svg>
                                                    </a>

                                                @endif
                                                

                                            </td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                @endif
                                
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
