@extends('layouts.admin')

@section('autres_scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>
@endsection


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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>

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
                <div class="card-header entete-table"> {{ __(mb_strtoupper('Livraison commande')) }} 

                    <span style="float: right; margin-right: 20px;">DATE LIVRAISON : <strong>{{ date("d/m/Y",strtotime($livraison_commandes_info->created_at ?? '')) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $livraison_commandes_info->exercice ?? '' }}</strong></span>
                    
                    
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

                    <div class="row d-flex">
                        <div class="col-md-6">
                            
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">N° Bon Cde. </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="color:red; font-weight:bold; margin-top:-7px;" autocomplete="off" type="text" name="num_bc" class="form-control griser" value="{{ $livraison_commandes_info->num_bc ?? '' }}">
                                        
                                        <input onfocus="this.blur()" style="display: none" name="cotation_fournisseurs_id" value="{{ $cotation_fournisseurs_id ?? '' }}"/>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">N° Bon Liv. </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="color:green; font-weight:bold; margin-top:-7px;" autocomplete="off" type="text" name="num_bl" class="form-control griser" value="{{ $livraison_commandes_info->num_bl ?? '' }}">
                                        
                                        <input onfocus="this.blur()" style="display: none" name="livraison_commandes_id" value="{{ $livraison_commandes_info->livraison_commandes_id ?? '' }}"/>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">Intitulé </label> 
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group d-flex ">
                                        <textarea onfocus="this.blur()" style="background-color: #e9ecef; resize:none" autocomplete="off" type="text" name="intitule" class="form-control">{{ $livraison_commandes_info->intitule ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">Compte budg. </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="ref_fam" class="form-control" value="{{ $livraison_commandes_info->ref_fam ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="intitule" class="form-control"
                                        value="{{ $livraison_commandes_info->design_fam ?? '' }}"
                                            >
                                    </div>
                                </div>
                            </div>
                            @if(!isset($profil_fournisseur))
                            @if($profil_fournisseur === null)
                            @if(isset($signataires))
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">

                                            <label class="label" class="mt-1 ">Signataire</label> 

                                        </div>
                                    </div>
                                    <div class="col-md-9 pr-1">
                                        <div class="form-group d-flex ">
                                            <table id="tableSignataire" width="100%">
                                                @if(count($signataires) > 0)
                                                    @foreach($signataires as $signataire)

                                                    <tr>

                                                        <td style="width: 32%; padding-right:3px;">
                                                                <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                style="display: none"
                                                                required
                                                                value="{{ $signataire->profil_fonctions_id ?? '' }}"
                                                                >
                
                                                                <input
                                                                onkeyup="editAgent(this)"
                                                                list="agent_list" autocomplete="off" type="text" name="mle[]" class="form-control mle"
                                                                required
                                                                value="{{ 'M'.$signataire->mle ?? '' }}"

                                                                @if(!isset($edit_signataire))
                                                                    disabled
                                                                @endif
                                                                >

                                                        </td>

                                                        <td>
                                                            
                                                            <input onfocus="this.blur()" 
                                                            style="background-color: #e9ecef"
                                                            autocomplete="off" type="text" name="nom_prenoms[]" class="form-control nom_prenoms" 
                                                            required
                                                            value="{{ $signataire->nom_prenoms ?? '' }}"
                                                            >

                                                        </td>

                                                        <td style="width: 1px; white-space:nowrap">
                                                            @if(isset($edit_signataire))
                                                                <a title="Ajouter un signataire" onclick="myCreateFunction()" href="#" class="addRow">
                                                                    <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                    </svg>
                                                                </a>
                                                            @endif
                                                            
                                                    
                                                        </td>

                                                    </tr>
                                                    @endforeach
                                                    @else
                                                    <tr>

                                                        <td style="width: 32%; padding-right:3px;">
                                                                <input autocomplete="off" type="text" name="profil_fonctions_id[]" class="form-control profil_fonctions_id"
                                                                style="display: none"
                                                                required
                                                                >
                
                                                                <input
                                                                onkeyup="editAgent(this)"
                                                                list="agent_list" autocomplete="off" type="text" name="mle[]" class="form-control mle"
                                                                required
                                                                >

                                                        </td>

                                                        <td>
                                                            
                                                            <input onfocus="this.blur()" 
                                                            
                                                            style="background-color: transparent; border-color:transparent; 
                                                            font-weight:normal" 
                                                            autocomplete="off" type="text" name="nom_prenoms[]" class="form-control nom_prenoms" 
                                                            required
                                                            >

                                                        </td>

                                                        <td style="width: 1px; white-space:nowrap">
                                                            <a title="Ajouter un signataire" onclick="myCreateFunction()" href="#" class="addRow">
                                                                <svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                </svg>
                                                            </a>
                                                    
                                                        </td>

                                                    </tr>
                                                @endif
                                                

                                                
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @endif
                            @endif
                            
                            
                        </div>
                        <div class="col-md-6">
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">Gestion </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="code_gestion" class="form-control" value="{{ $livraison_commandes_info->code_gestion ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_gestion" class="form-control" value="{{ $livraison_commandes_info->libelle_gestion ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">Fournisseur </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $livraison_commandes_info->entnum ?? $livraison_commandes_info->organisations_id ?? '' }}" autocomplete="off" type="text" name="organisations_id" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $livraison_commandes_info->denomination ?? '' }}" autocomplete="off" type="text" name="denomination" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">Échéance Frs. </label> 
                                    </div>
                                </div>
                                <div class="col-md-3 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="libelle_periode" class="form-control" value="{{ $livraison_commandes_info->libelle_periode ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3 pl-0 pr-1">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="delai" class="form-control" value="{{ $livraison_commandes_info->delai ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3 pl-0">
                                    <div class="form-group d-flex ">
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="date_echeance" class="form-control" @if(isset($livraison_commandes_info->date_echeance))
                                        value="{{  date("d/m/Y", strtotime($livraison_commandes_info->date_echeance)) }}"
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
                                        <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ old('code_devise') ?? $livraison_commandes_info->code_devise ?? '' }}" onkeyup="editDevise(this)" list="list_devise" autocomplete="off" type="text" id="code_devise" name="code_devise" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6 pl-0">
                                    <div class="form-group d-flex ">
                                        <input value="{{ old('libelle_devise') ?? $livraison_commandes_info->libelle_devise ?? '' }}" onfocus="this.blur()" autocomplete="off" type="text" id="libelle_devise" name="libelle_devise" class="form-control" style="background-color: #e9ecef">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex" style="margin-top: -10px;">
                                <div class="col-sm-3">
                                    <div class="form-group" style="text-align: right">
                                        <label class="label" class="mt-1 font-weight-bold">Date livraison </label> 
                                    </div>
                                </div>
                                <div class="col-md-6 pr-1">
                                    <div class="form-group">
                                        <input disabled autocomplete="off" type="date" name="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror" @if(isset($livraison_commandes_info->date_livraison_prevue))
                                        value="{{  $livraison_commandes_info->date_livraison_prevue }}"
                                        @endif >
                                        @error('date_livraison_prevue')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>                        

                    <div class="panel panel-footer">
                        <table class="table table-bordered table-striped" id="myTable" style="width:100%">
                            <thead>
                                <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                    <th style="; vertical-align:middle;text-align:center; width: 1%; white-space: nowrap;">RÉF.</th>
                                    <th style="width:50%; text-align:center; vertical-align:middle">DÉSIGNATION ARTICLE</th>
                                    <th style="; vertical-align:middle;text-align:center; width: 10%;">QTÉ COMMANDÉE</th>
                                    <th style="; vertical-align:middle;text-align:center; width: 10%;">QTÉ LIVRÉE</th>
                                    <th style="; vertical-align:middle;text-align:center; width: 10%;">PU HT</th>
                                    <th style="; vertical-align:middle;text-align:center; width: 1%; white-space: nowrap;">REMISE %</th>
                                    <th style="; vertical-align:middle;text-align:center; width: 15%;">MONTANT HT</th>
                                    <th style="; vertical-align:middle;text-align:center; width: 1%; white-space: nowrap;">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($detail_livraisons as $detail_livraison)

                                    <?php 
                                        $echantillon_cnps = null;
                                        if (isset($detail_livraison->echantillon_cnps)) {
                                            $echantillon_cnps = $detail_livraison->echantillon_cnps;
                                        }

                                        $echantillon = $detail_livraison->echantillon;

                                        $prix_unit = number_format((float)$detail_livraison->prix_unit, 2, '.', '');

                                        $block_prix_unit = explode(".",$prix_unit);
                                        
                                        $prix_unit_partie_entiere = null;
                                        
                                        if (isset($block_prix_unit[0])) {
                                            $prix_unit_partie_entiere = $block_prix_unit[0];
                                        }

                                        $prix_unit_partie_decimale = null;
                                        if (isset($block_prix_unit[1])) {
                                            $prix_unit_partie_decimale = $block_prix_unit[1];
                                        }


                                        $remise = number_format((float)$detail_livraison->remise, 2, '.', '');

                                        $block_remise = explode(".",$remise);
                                        
                                        $remise_partie_entiere = null;
                                        
                                        if (isset($block_remise[0])) {
                                            $remise_partie_entiere = $block_remise[0];
                                        }

                                        $remise_partie_decimale = null;
                                        if (isset($block_remise[1])) {
                                            $remise_partie_decimale = $block_remise[1];
                                        }

                                        

                                        $montant_ht = $detail_livraison->montant_ht;

                                        $montant_ht = number_format((float)$montant_ht, 2, '.', '');

                                        $block_montant_ht = explode(".",$montant_ht);
                                        
                                        $montant_ht_partie_entiere = null;
                                        
                                        if (isset($block_montant_ht[0])) {
                                            $montant_ht_partie_entiere = $block_montant_ht[0];
                                        }

                                        $montant_ht_partie_decimale = null;
                                        if (isset($block_montant_ht[1])) {
                                            $montant_ht_partie_decimale = $block_montant_ht[1];
                                        }

                                        //dd($montant_ht_partie_entiere,$montant_ht_partie_decimale,$montant_ht);


                                        $description_articles_libelle = null;

                                        $description_article = DB::table('detail_demande_achats as dda')
                                        ->join('description_articles as da','da.id','=','dda.description_articles_id')
                                        ->select('da.libelle')
                                        ->where('dda.demande_achats_id',$detail_livraison->demande_achats_id)
                                        ->where('dda.ref_articles',$detail_livraison->ref_articles)
                                        ->first();

                                        if ($description_article != null) {
                                            $description_articles_libelle = $description_article->libelle;
                                        }

                                        //Qte en cours de livraison
                                        $qte_en_cours_de_livraison = 0;

                                        $qte_livraison_commande = DB::table('livraison_commandes as lc')
                                        ->join('detail_livraisons as dl','dl.livraison_commandes_id','=','lc.id')
                                        ->where('dl.detail_cotations_id',$detail_livraison->detail_cotations_id)
                                        ->where('lc.cotation_fournisseurs_id',$detail_livraison->cotation_fournisseurs_id)
                                        ->select(DB::raw('SUM(dl.qte_frs) as qte_en_cours_de_livraison'))
                                        ->first();

                                        if($qte_livraison_commande != null){
                                            $qte_en_cours_de_livraison = $qte_livraison_commande->qte_en_cours_de_livraison;
                                        }
                                        
                                    ?>
                                    <tr>
                                        <td style="text-align: left; vertical-align:middle; width: 1%; white-space: nowrap; font-weight:bold; color: #7d7e8f">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $detail_livraison->detail_cotations_id ?? '' }}" autocomplete="off" type="text" name="detail_cotations_id[]" class="form-control detail_cotations_id">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $detail_livraison->detail_livraisons_id ?? '' }}" autocomplete="off" type="text" name="detail_livraisons_id[]" class="form-control detail_livraisons_id">

                                            
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $detail_livraison->ref_articles ?? '' }}" autocomplete="off" required list="list_article" type="text" name="ref_articles[]" class="form-control ref_articles">

                                            {{ $detail_livraison->ref_articles ?? '' }}
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <input value="{{ $detail_livraison->design_article ?? '' }}" autocomplete="off" required onfocus="this.blur()" style="background-color: #e9ecef; display:none" type="text" name="design_article[]" class="form-control design_article">

                                            {{ $detail_livraison->design_article ?? '' }}

                                            @if(isset($description_articles_libelle))
                                                <br/>
                                                <br/>
                                                <span style="color:red; font-weight:bold" >{{ 'Description de l\'article' }}</span>
                                                <br/>
                                                {{ $description_articles_libelle ?? '' }}
                                                
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align:middle;">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" value="{{ $qte_en_cours_de_livraison ?? '' }}"  autocomplete="off" required type="text" name="qte_en_cours_de_livraison[]" class="form-control qte_en_cours_de_livraison">

                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" value="{{ strrev(wordwrap(strrev($detail_livraison->qte_cmd ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" name="qte_cotation[]" class="form-control qte_cotation">

                                            {{ strrev(wordwrap(strrev($detail_livraison->qte_cmd ?? ''), 3, ' ', true)) }}

                                        </td>
                                        <td style="text-align: center; vertical-align:middle; 
                                        
                                        @if($griser_qte === null) 
                                        
                                            background-color:white 
                                        
                                        @endif 
                                        
                                        ">

                                            <input onfocus="this.blur()" style="text-align:center; border:none; display:none" value="{{ strrev(wordwrap(strrev($detail_livraison->qte ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_actuelle[]" class="form-control qte_actuelle">

                                            <input 
                                            @if($griser_qte === 1)
                                                onfocus="this.blur()"
                                            @endif

                                            style="text-align:center; border:none ; @if($griser_qte === 1 ) background-color:transparent @endif" value="{{ strrev(wordwrap(strrev($detail_livraison->qte ?? ''), 3, ' ', true)) }}"  autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte[]" class="form-control qte">

                                        </td>
                                        <td style="text-align: right; vertical-align:middle; white-space: nowrap;">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; display:none" 
                                            
                                            @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                                            value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                            value="{{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                        
                                            autocomplete="off" required type="text" onkeyup="editMontant(this)" oninput="validateNumber(this);" name="prix_unit[]" class="form-control prix_unit">

                                            @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                                                {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                                {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif

                                        </td>
                                        <td style="text-align: center; vertical-align:middle; white-space: nowrap;">
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none" 
                                            
                                            @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                                value="{{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                            
                                            autocomplete="off" type="text" onkeyup="editMontant(this)" oninput="validateNumber(this);" name="remise[]" class="form-control remise">

                                            @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                                                {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                                {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}
                                            @endif

                                        </td>
                                        <td style="vertical-align: middle">
                                        

                                            <input 

                                            @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)
                                                value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}"
                                            @else
                                                value="{{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}"
                                            @endif
                                        
                                            autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: transparent; border-color:transparent; text-align:right; " type="text" name="montant_ht[]" class="form-control montant_ht">

                                            <input value="{{ $montant_ht ?? '' }}"

                                            autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis">

                                        </td>
                                        <td style="vertical-align: middle;">

                                            @if(isset($echantillon_cnps))
                                                <!-- Modal -->

                                                    <a title="Echantillon CNPS : {{ $detail_livraison->design_article ?? '' }}" href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $detail_livraison->ref_articles }}">
                                                        <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                        </svg>
                                                    </a>
                                                    <div class="modal fade" id="exampleModalCenter{{ $detail_livraison->ref_articles }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="text-align: center">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Échantillon CNPS { <span style="color: orange">{{ $detail_livraison->design_article ?? '' }}</span> } </h5>
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

                                                    <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $detail_livraison->detail_cotations_id }}">
                                                            <svg style="cursor: pointer; color:blue; margin-left:5px;  margin-right:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                            </svg>
                                                    </a>
                                                    <div class="modal fade" id="exampleModalCenter{{ $detail_livraison->detail_cotations_id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="text-align: center">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Échantillon { <span style="color: red">{{ $detail_livraison->design_article ?? '' }}</span> } </h5>
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
                                            &nbsp; &nbsp;

                                        </td>
                                    </tr>

                                @endforeach
                                
                            </tbody>
                            <?php

                                $montant_total_brut_partie_entiere = null;
                                $montant_total_brut_partie_decimale = null;
                                $taux_remise_generale_partie_entiere = null;
                                $taux_remise_generale_partie_decimale = null;
                                $remise_generale_partie_entiere = null;
                                $remise_generale_partie_decimale = null;
                                $montant_total_net_partie_entiere = null;
                                $montant_total_net_partie_decimale = null;
                                $montant_total_ttc_partie_entiere = null;
                                $net_a_payer_partie_entiere = null;
                                $montant_total_ttc_partie_decimale = null;
                                $net_a_payer_partie_decimale = null;
                                $montant_acompte_partie_entiere = null;
                                $montant_acompte_partie_decimale = null;
                                
                                if (isset($livraison_commandes_info)) {
                                    
                                    
                                    $montant_total_brut = number_format((float)$livraison_commandes_info->montant_total_brut, 2, '.', '');

                                    $block_montant_total_brut = explode(".",$montant_total_brut);
                                    
                                    
                                    
                                    if (isset($block_montant_total_brut[0])) {
                                        $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
                                    }

                                    
                                    if (isset($block_montant_total_brut[1])) {
                                        $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
                                    }

                                    $taux_remise_generale = number_format((float)$livraison_commandes_info->taux_remise_generale, 2, '.', '');

                                    $block_taux_remise_generale = explode(".",$taux_remise_generale);
                                    
                                    
                                    
                                    if (isset($block_taux_remise_generale[0])) {
                                        $taux_remise_generale_partie_entiere = $block_taux_remise_generale[0];
                                    }

                                    
                                    if (isset($block_taux_remise_generale[1])) {
                                        $taux_remise_generale_partie_decimale = $block_taux_remise_generale[1];
                                    }


                                    $remise_generale = number_format((float)$livraison_commandes_info->remise_generale, 2, '.', '');

                                    $block_remise_generale = explode(".",$remise_generale);
                                    
                                    
                                    
                                    if (isset($block_remise_generale[0])) {
                                        $remise_generale_partie_entiere = $block_remise_generale[0];
                                    }

                                    
                                    if (isset($block_remise_generale[1])) {
                                        $remise_generale_partie_decimale = $block_remise_generale[1];
                                    }

                                    $montant_total_net = number_format((float)$livraison_commandes_info->montant_total_net, 2, '.', '');

                                    $block_montant_total_net = explode(".",$montant_total_net);
                                    
                                    
                                    
                                    if (isset($block_montant_total_net[0])) {
                                        $montant_total_net_partie_entiere = $block_montant_total_net[0];
                                    }

                                    
                                    if (isset($block_montant_total_net[1])) {
                                        $montant_total_net_partie_decimale = $block_montant_total_net[1];
                                    }
                                    

                                    $montant_total_ttc = number_format((float)$livraison_commandes_info->montant_total_ttc, 2, '.', '');

                                    $block_montant_total_ttc = explode(".",$montant_total_ttc);
                                    
                                
                                    
                                    if (isset($block_montant_total_ttc[0])) {
                                        $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
                                    }

                                    
                                    if (isset($block_montant_total_ttc[1])) {
                                        $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
                                    }


                                    $net_a_payer = number_format((float)$livraison_commandes_info->net_a_payer, 2, '.', '');

                                    $block_net_a_payer = explode(".",$net_a_payer);
                                    
                                    
                                    
                                    if (isset($block_net_a_payer[0])) {
                                        $net_a_payer_partie_entiere = $block_net_a_payer[0];
                                    }
                                    
                                    if (isset($block_net_a_payer[1])) {
                                        $net_a_payer_partie_decimale = $block_net_a_payer[1];
                                    }

                                    $montant_acompte = number_format((float)$livraison_commandes_info->montant_acompte, 2, '.', '');

                                    $block_montant_acompte = explode(".",$montant_acompte);
                                    
                                    
                                    
                                    if (isset($block_montant_acompte[0])) {
                                        $montant_acompte_partie_entiere = $block_montant_acompte[0];
                                    }

                                    
                                    if (isset($block_montant_acompte[1])) {
                                        $montant_acompte_partie_decimale = $block_montant_acompte[1];
                                    }
                                }
                            ?>
                            <tfoot>
                                <tr>
                                    <td colspan="8" style="border: none">
                                        <div class="row d-flex pl-3">
                                                            
                                                <div class="pr-0" style="text-align:center"><label class="label" class=" mt-1 mr-1">Montant total brut</label><br>  <input 
                                                    
                                                @if($livraison_commandes_info!= null) 

                                                    @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                    
                                                @endif 
                                                
                                                required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut">
                                            
                                                </div>

                                                <div class="pl-1" style="text-align:center"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>  <input oninput ="validateNumber(this);" onfocus="this.blur()"onkeyup="editTauxRemiseGenerale(this)" autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale" style="width:90px; text-align:center; background-color: #e9ecef;" 
                                                    
                                                    @if(isset($taux_remise_generale_partie_decimale) && $taux_remise_generale_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($taux_remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                    @else
                                                    value="{{ strrev(wordwrap(strrev(old('taux_remise') ?? $taux_remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif
                                                >
                                                </div>
                    
                                                <div class="pl-1" style="text-align:center"><label class="label" class="  mt-1 mr-1">Remise</label><br>  <input 
                                                    
                                                    @if($livraison_commandes_info!= null)

                                                        @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif

                                                    @endif 
                                                    
                                                    onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" onfocus="this.blur()" autocomplete="off"  style="width:110px; text-align:right; background-color: #e9ecef;" type="text" name="remise_generale" class="form-control remise_generale"></div>
                    
                                                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total net ht</label><br>  <input 
                                                    
                                                    @if($livraison_commandes_info!= null) 

                                                        @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                    
                                                    @endif 
                                                    
                                                    required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"></div>

                                                
                    
                                                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">TVA (%)</label><br>  <input list="list_taxe_tva" @if($livraison_commandes_info!= null) value="{{ $livraison_commandes_info->tva }}" @endif onkeyup="editRemiseGenerale(this)" onfocus="this.blur()" oninput="validateNumber(this);" autocomplete="off" style=" width:80px; text-align:center; background-color: #e9ecef;" type="text" name="tva" class="form-control tva"></div>
                                                
                                                <?php 
                                                if(isset($livraison_commandes_info->montant_total_net)){
                                                

                                                        $montant_tva = number_format((float)(($livraison_commandes_info->montant_total_net * ($livraison_commandes_info->tva)/100)), 2, '.', '');

                                                        $montant_tva = number_format((float)$montant_tva, 2, '.', '');

                                                        $block_montant_tva = explode(".",$montant_tva);
                                                        
                                                        $montant_tva_partie_entiere = null;
                                                        
                                                        if (isset($block_montant_tva[0])) {
                                                            $montant_tva_partie_entiere = $block_montant_tva[0];
                                                        }

                                                        $montant_tva_partie_decimale = null;
                                                        if (isset($block_montant_tva[1])) {
                                                            $montant_tva_partie_decimale = $block_montant_tva[1];
                                                        }

                                                }
                                                ?>
                                                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input 
                                                    
                                                    @if($livraison_commandes_info!= null) 

                                                        @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                    
                                                    @endif  
                                                    
                                                    autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva"></div>
                    
                                                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total ttc</label><br>  <input 
                                                    
                                                    @if($livraison_commandes_info!= null) 

                                                        @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                                        value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                            @else
                                                            value="{{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                        @endif
                                                    @endif
                                                    
                                                    required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"></div>
                                        </div>
                                        <div class="row d-flex pl-3">
                
                                            <div class="pr-0"><label class="label" class=" mt-1 mr-2">Assiette BNC</label><br>  <input @if($livraison_commandes_info!= null) value="{{ $livraison_commandes_info->assiete_bnc }}" @endif  autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="assiette" class="form-control assiette"></div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Taux BNC</label><br>  <input @if($livraison_commandes_info!= null) value="{{ $livraison_commandes_info->taux_bnc }}" @endif autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:60px;" oninput="validateNumber(this);" type="text" name="taux_bnc" class="form-control"></div>
                
                                            <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input @if($livraison_commandes_info!= null) value="{{ ($livraison_commandes_info->assiete_bnc * ($livraison_commandes_info->taux_bnc)/100) }}" @endif autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="montant_bnc" class="form-control montant_bnc"></div>
                
                                            <div class="pl-1" style="text-align:center" style="margin-left:0px;"><label class="label" class=" mt-1 mr-2">Net à payer</label><br>  <input 
                                                @if($livraison_commandes_info!= null) 

                                                    @if(isset($net_a_payer_partie_decimale) && $net_a_payer_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($net_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($net_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif

                                                @endif 
                                                
                                                required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"> </div>

                                                

                                                    <div style="padding-left:3px; text-align:center" ><label class="label" class=" mt-1 mr-2">Acompte</label><br>  <input style="vertical-align:middle;" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)" disabled @if($livraison_commandes_info!= null) @if($livraison_commandes_info->acompte === 1) checked  @endif  @endif ></div>

                                                    @if(isset($display_acompte))
                                                    <div style="padding-left: 10px; text-align: center;"id="acompte_taux_div" ><label class="label" class=" mt-1 mr-2">(%)</label><br>  <input onfocus="this.blur()" maxlength="3" onkeyup="editTauxAcompte(this)" @if($livraison_commandes_info!= null) value="{{ $livraison_commandes_info->taux_acompte ?? $livraison_commandes_info->taux_acompte ?? 0 }}" @endif autocomplete="off" oninput="validateNumber(this);" style="background-color: #e9ecef; width:70px; text-align:center;" type="text" name="taux_acompte" class="form-control taux_acompte"></div>

                                                    <div class="pl-1" style="text-align:center;" id="acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>  <input onfocus="this.blur()" 
                                            
                                                    @if($livraison_commandes_info!= null) 

                                                        @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                                    value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}"
                                                        @else
                                                        value="{{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}"
                                                    @endif 

                                                    @endif 
                                                    
                                                    autocomplete="off" onkeyup="editAcompte(this)" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_acompte" class="form-control montant_acompte"></div>

                                                    <div style="padding-left: 10px;" ><label class="label" class=" mt-1 mr-2">&nbsp;</label><br></div>
                                                @endif
                                        </div>
                                    </td>
                    
                                </tr>
                                
                            </tfoot>
                        </table>
                        
                        <table style="width:100%; margin-bottom:10px;">
                        <?php
                            $bon_livraisons_id = null;

                            $bon_livraison = DB::table('detail_livraisons as dl')
                            ->join('bon_livraisons as bl','bl.livraison_commandes_id','=','dl.livraison_commandes_id')
                            ->whereRaw('dl.sequence = bl.sequence')
                            ->where('dl.livraison_commandes_id',$livraison_commandes_info->livraison_commandes_id)
                            ->select('bl.id','bl.name')
                            ->first();

                            if ($bon_livraison != null) {
                                $bon_livraisons_id = Crypt::encryptString($bon_livraison->id);

                                ?>
                                    
                                    <tr>
                                        <td style="text-align:right; font-weight:bold">
                                            <a href="/bon_livraisons/shows/{{ $bon_livraisons_id ?? 0 }}" target="_blank">
                                                {{ $bon_livraison->name ?? '' }}
                                            </a>
                                        </td>
                                    </tr>
                                    
                                <?php
                            }

                            $piece_jointe_livraison = DB::table('livraison_commandes as lc')
                            ->join('piece_jointe_livraisons as pjl','pjl.subject_id','=','lc.id')
                            ->join('type_operations as to','to.id','=','pjl.type_operations_id')
                            ->where('to.libelle','PV de réception')
                            ->where('pjl.subject_id',$livraison_commandes_info->livraison_commandes_id)
                            ->select('pjl.id','pjl.name')
                            ->first();

                            if ($piece_jointe_livraison != null) {
                                $piece_jointe_livraisons_id = Crypt::encryptString($piece_jointe_livraison->id);

                                ?>
                                    
                                    <tr>
                                        <td style="text-align:right; font-weight:bold">
                                            <a href="/pv_reception/{{ $piece_jointe_livraisons_id ?? 0 }}" target="_blank">
                                                {{ $piece_jointe_livraison->name ?? '' }}
                                            </a>
                                        </td>
                                    </tr>
                                    
                                <?php
                            }
                            
                        ?>
                        </table>                     

                    </div>
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