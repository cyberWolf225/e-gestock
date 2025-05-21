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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>


    </head>
<div class="container" style="color:black"> 
    @include('partials.workflow')
    <datalist id="list_type_achat">
        @foreach($type_achats as $type_achat)
            <option value="{{ $type_achat->libelle }}">{{ $type_achat->libelle }}</option>
        @endforeach
    </datalist>

    <datalist id="list_taux_acompte">

        @for($i = 0; $i < 101; $i++)
            <option value="{{ $i }}">{{ $i.'%' }}</option>
        @endfor
        
    </datalist>

    <datalist id="organisation">
        @foreach($organisations as $organisation)
            <option value="{{ $organisation->id .' - '.$organisation->denomination }}">{{ $organisation->denomination }}</option>
        @endforeach
    </datalist>
    

    <datalist id="list_periode">            
        @foreach($periodes as $periode)
            <option value="{{ $periode->id }}->{{ $periode->libelle_periode }}->{{ $periode->valeur }}">{{ $periode->libelle_periode }}</option>
        @endforeach  
    </datalist>

    <div class="row"> 
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ strtoupper($entete ?? '') }} 
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
                    
                    <form name="myForm" method="POST" action="{{ route('demande_achats.cotation_store') }}">
                        @csrf
                        
                            
                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;"> 
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">
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
                                            <input required 

                                            @if ($griser!=null) 
                                                style="color:red; font-weight:bold; margin-top:-7px;" onfocus="this.blur()" 
                                            @endif
                                            
                                            autocomplete="off" type="text" name="num_bc" class="form-control griser @error('num_bc') is-invalid @enderror" value="{{ $num_bc ?? '' }}">
                                        </div>
                                        @error('num_bc')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Compte budg. </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input value="{{ $demande_achat_info->ref_fam ?? '' }}" required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php } ?> onkeyup="editCompte(this)" list="credit" autocomplete="off" type="text" name="ref_fam" id="ref_fam" <?php if ($credit_budgetaires_select!=null) { ?> value="{{ old('ref_fam') ?? $credit_budgetaires_select->ref_fam }}" <?php } ?>  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >
                                            <input onfocus="this.blur()" style="display: none" name="id" value="{{ $demande_achat_info->id ?? '' }}"/>
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" value="{{ $demande_achat_info->design_fam ?? '' }}"  class="form-control griser design_fam">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Structure <?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editStructure(this)" list="structure" <?php } ?>  autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $credit_budgetaire_structure->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ?? $credit_budgetaire_structure->nom_structure ?? '' }}">
                                        </div>
                                        @error('nom_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3" style="text-align: right"><label class="label" @if(isset($disponible->credit)) @if($disponible->credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >Solde disponible : </label></div>
                                    <div class="col-sm-3"><label class="label" @if(isset($disponible->credit)) @if($disponible->credit > 0) style="color:green" @else style="color:red" @endif @else style="color:red" @endif >@if(isset($disponible->credit)) {{ strrev(wordwrap(strrev($disponible->credit ?? 'aucun budget'), 3, ' ', true)) ?? '' }} @else <svg style="color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.498 3.498 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.498 4.498 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"/>
                                      </svg> &nbsp; Oups! pas de budget disponible @endif</label></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Gestion </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input  required <?php if ($griser!=null) { ?> style="background-color: #e9ecef" onfocus="this.blur()" <?php }else{ ?> onkeyup="editGestion(this)" list="gestion" <?php } ?>  autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ $demande_achat_info->code_gestion ?? '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_gestion" id="nom_gestion" class="form-control griser @error('nom_gestion') is-invalid @enderror nom_gestion" value="{{ $demande_achat_info->libelle_gestion ?? '' }}">
                                        </div>
                                        @error('nom_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Intitulé </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group d-flex ">
                                            <textarea required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror">{{ $demande_achat_info->intitule ?? '' }}</textarea>
                                        </div>
                                        @error('intitule')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Code échéance<span style="color: red"><sup> *</sup></span> </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input required value="{{ old('periode_id') ?? $commande->libelle_periode ?? '' }}" onkeyup="editPeriode(this)"  autocomplete="off" type="text" id="periode_id" name="periode_id" class="form-control" 
                                            
                                            @if (isset($verrou)) 
                                                style="background-color: #e9ecef;" onfocus="this.blur()" 
                                                @else
                                                list="list_periode"
                                            @endif

                                            >

                                            <input value="{{ old('valeur') ?? $commande->periodes_id ?? '' }}" style="display: none" onfocus="this.blur()" type="text" id="valeur" name="valeur" class="form-control" 
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        <div class="form-group d-flex ">
                                            <input onkeypress="validate(event)" required value="{{ old('delai') ?? $commande->delai ?? '' }}" onkeyup="editPeriode2(this)" autocomplete="off" type="text" id="delai" name="delai" class="form-control" 
                                            
                                            @if (isset($verrou)) 
                                                style="background-color: #e9ecef;" onfocus="this.blur()" 
                                            @endif

                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group d-flex ">
                                            <input required @if(isset($commande->date_echeance)) value="{{ old('date_echeance') ?? date("d/m/Y",strtotime($commande->date_echeance)) }}" @endif style="background-color: #e9ecef" onfocus="this.blur()" type="text" id="date_echeance" name="date_echeance" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        
                                    </div>
                                    <div class="col-md-3 pl-0 pr-1">
                                        
                                    </div>
                                    <div class="col-md-3 pl-0">
                                        <div class="form-group d-flex ">
                                            <input class="form-control @error('time_echeance') is-invalid @enderror" type="time" name="time_echeance"  
                                            
                                            @if(isset($commande->date_echeance)) 
                                            
                                            value="{{ date("H:i",strtotime($commande->date_echeance)) }}"
                                            
                                            @else
                                                value="12:00"
                                            @endif

                                            id="example-time-input" 
                                            
                                            
                                            @if (isset($verrou)) 
                                                style="background-color: #e9ecef;" onfocus="this.blur()" 
                                            @endif
                                            >

                                            @error('time_echeance')
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
                                        <th style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">RÉF.</th>
                                        <th style="width:100%; vertical-align:middle; text-align:center">DÉSIGNATION ARTICLE</span></th>
                                        <th style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">QUANTITÉ</th>
                                        <th style="width:1%; text-align:center">ÉCHANTILLON</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demande_achats as $demande_achat)
                                        
                                        <?php 
                                            $check = null;
                                            if ($demande_achat->flag_valide == 1) {
                                                $check = 'checked';
                                            }

                                            $echantillon_cnps = null;
                                            if (isset($demande_achat->echantillon_cnps)) {
                                                $echantillon_cnps = $demande_achat->echantillon_cnps;
                                            }

                                            $description_articles_libelle = null;

                                            $description_article = DB::table('detail_demande_achats as dda')
                                            ->join('description_articles as da','da.id','=','dda.description_articles_id')
                                            ->select('da.libelle')
                                            ->where('dda.demande_achats_id',$demande_achat->demande_achats_id)
                                            ->where('dda.id',$demande_achat->id)
                                            ->first();

                                            if ($description_article != null) {
                                                $description_articles_libelle = $description_article->libelle;
                                            }
                                        ?>
                                        <tr>

                                            <td style="width:1px; white-space:nowrap; vertical-align:middle; text-align:left; font-weight:bold; color: #7d7e8f">
                                                <input  required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; text-align:left; font-weight:bold ;display: none;" onfocus="this.blur()" <?php }else{ ?> onkeyup="editDesign(this)" list="list_article" <?php } ?> autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" value="{{ $demande_achat->ref_articles ?? '' }}">

                                                <input style="display: none;" onfocus="this.blur()" name="detail_demande_achat_id[]" value="{{ $demande_achat->id ?? '' }}"/>

                                                {{ $demande_achat->ref_articles ?? '' }}

                                            </td>
                                            <td>
                                                <input required autocomplete="off" required onfocus="this.blur()" style="background-color: #e9ecef; display:none" type="text" id="design_article" name="design_article[]" class="form-control design_article" value="{{ $demande_achat->design_article ?? '' }}">

                                                {{ $demande_achat->design_article ?? '' }}

                                                @if(isset($description_articles_libelle))
                                                    <br/>
                                                    <br/>
                                                    <span style="color:red; font-weight:bold" >{{ 'Description de l\'article' }}</span>
                                                    <br/>
                                                    {{ $description_articles_libelle ?? '' }}
                                                    
                                                @endif

                                            </td>
                                            <td style="width:1px; white-space:nowrap; vertical-align:middle; text-align:center">
                                                <input required <?php if ($griser!=null) { ?> style="background-color: #e9ecef; text-align:center; display:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text"  id="qte_validee" name="qte_validee[]" class="form-control qte_validee" value="{{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? $demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}">

                                                {{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? $demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}
                                            </td>
                                            <td style="text-align: center; vertical-align:middle;">
                                                @if(isset($echantillon_cnps))
                                                

                                                    <!-- Modal -->

                                                        <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $demande_achat->ref_articles }}">
                                                                <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                </svg>
                                                        </a>
                                                        <div class="modal fade" id="exampleModalCenter{{ $demande_achat->ref_articles }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                                
                            </table>

                            
                        </div>

                        <div class="row d-flex">
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">

                                    <div class="col-md-3">
                                        <div class="form-group" style="text-align: right">
                                            <input style="display: none" disabled id="libelle_type_achats" class="form-control"/>
                                            <label class="label" class="mt-1" style="">Achat direct </label> 
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                            <input onclick="handleClick(this);" required autocomplete="off" type="radio" name="libelle_type_achat" id="libelle_type_achat" style="margin-top: 6px" value="Achat direct"

                                            @if(isset($libelle_type_achat))
                                                @if($libelle_type_achat === "Achat direct" )
                                                    checked
                                                @endif
                                            @endif
                                            
                                            @if (isset($verrou)) 
                                                disabled
                                            @endif
                                            >
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1" style="">Appel d'offre </label> 
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                            <input onclick="handleClick(this);" required autocomplete="off" type="radio" name="libelle_type_achat" id="libelle_type_achat" style="margin-top: 6px" value="Appel d'offre"
                                            @if(isset($libelle_type_achat))
                                                @if($libelle_type_achat === "Appel d'offre" )
                                                    checked
                                                @endif
                                            @endif 
                                            
                                            @if (isset($verrou)) 
                                                disabled 
                                            @endif

                                            >
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div id="div_fournisseur" class="col-sm-3" style="display: @if(!isset($libelle_type_achat)) none @endif ">
                                        <div class="form-group" style="text-align: right;">
                                            
                                            <input style="display: none" id="nbre_organisations" value="{{ $nbre_organisations ?? 0 }}" name="nbre_organisations" class="form-control nbre_organisations">

                                            <label class="label" class="mt-1"> @if($all_preselection!=null) <span style="color: green; font-weight:bold" title="Demande de cotation adressé à l'ensemble des fournisseurs rattachés à cette famille d'articles">@endif Fournisseur @if($all_preselection!=null) <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                                              </svg> </span> @endif </label> 
                                        </div>
                                    </div>
                                    <div id="div_fournisseur2" class="col-md-8 pr-0" style="display: @if(!isset($libelle_type_achat)) none @endif ">
                                        <div class="form-group d-flex ">
                                            
                                            <table id="myTable2" style="width: 100%">
                                                    @if($preselections!=null)
                                                        @foreach($preselections as $preselection)
                                                            <tr>
                                                            <td>
                                                                <input autocomplete="off" type="text" name="designation_org[]" id="designation_org" value="{{ old('designation_org[]') ?? $preselection->organisations_id.' - '.$preselection->denomination ?? ''  }}"  class="form-control designation_org" placeholder="Sélectionner un fournisseur" 
                                                                
                                                                @if (isset($verrou)) 
                                                                    style="background-color: #e9ecef;" onfocus="this.blur()"
                                                                    @else
                                                                    list="organisation"
                                                                @endif
                                                                >
                                                            </td>
                                                            <td>
                                                                <a onclick="deleteOrganisation(this)"  
                                                                
                                                                @if (isset($verrou)) 
                                                                    style="display:none"
                                                                    @else
                                                                    style="background-color:transparent; border-color:transparent"  
                                                                @endif
                                                                >
                                                                <svg style=" color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-x" viewBox="0 0 16 16">
                                                                <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                                                <path fill-rule="evenodd" d="M12.146 5.146a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z"/>
                                                                </svg>
                                                            </a>
                                                            </td>
                                                            </tr>
                                                        @endforeach
                                                        
                                                    @else
                                                        <tr>
                                                            <td>
                                                                <input required list="organisation" autocomplete="off" type="text" name="designation_org[]" id="designation_org" value="{{ old('designation_org') }}"  class="form-control designation_org" placeholder="Sélectionner un fournisseur">
                                                            </td>
                                                            <td>
                                                                <a onclick="deleteOrganisation(this)" style="background-color:transparent; border-color:transparent">
                                                                <svg style=" color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-x" viewBox="0 0 16 16">
                                                                <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                                                                <path fill-rule="evenodd" d="M12.146 5.146a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z"/>
                                                                </svg>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    
                                                    
                                            </table>

                                            
                                        </div>
                                    </div>
                                    <div id="div_fournisseur3" class="col-md-1 pl-1" style="display: @if(!isset($libelle_type_achat)) none @endif ">
                                        <div class="form-group d-flex">
                                            <a onclick="addOrganisation(this)" 
                                            
                                            @if (isset($verrou)) 
                                                style="display:none"
                                                @else
                                                style="margin-top: 5px; background-color:transparent; border-color:transparent" 
                                            @endif
                                            >
                                                <svg  style=" color:green" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg>
                                            </a>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label" class="mt-1">Taux acompte </label> 
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <input maxlength="3" onkeypress="validate(event)" class="form-control @error('taux_acompte') is-invalid @enderror" name="taux_acompte" id="taux_acompte" value="{{ old('taux_acompte') ?? $demande_achat_info->taux_acompte }}" 
                                        
                                        
                                        @if (isset($verrou)) 
                                            style="background-color: #e9ecef; text-align: center" onfocus="this.blur()"
                                            @else
                                            list="list_taux_acompte"
                                            style="text-align: center"
                                        @endif
                                        >

                                        @error('taux_acompte')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror

                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                                    <tr>
                                        <td style="border: none;vertical-align: middle; text-align:right"  colspan="2">
                                            @if(!isset($griser)) 
                                                <a title="Rattacher un nouveau dépôt" onclick="myCreateFunction2()" >
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
                                                        <input type="file" name="piece[]" class="form-control-file" id="piece" >
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
                                                    <input @if(isset($griser)) disabled @endif style="display: none" onfocus="this.blur()" class="form-control" name="piece_jointes_id[{{ $piece_jointe->id ?? 0 }}]" value="{{ $piece_jointe->id ?? 0 }}">
                                                    
                                                    <input @if(isset($griser)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                                    @if(isset($piece_jointe->flag_actif))
                                                        @if($piece_jointe->flag_actif == 1)
                                                            checked
                                                        @endif
                                                    @endif >
    
                                                    @if(!isset($griser)) 
    
                                                        <a  title="Retirer ce fichier" onclick="removeRow2(this)" >
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
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="border: none; vertical-align:middle; text-align:right">
    
                                            <div class="row justify-content-center">
                                            
                                                <div class="col-md-9">

                                                    <textarea style="width: 100%; resize:none" class="form-control @error('commentaire') is-invalid @enderror commentaire" name="commentaire" rows="2"  placeholder="Saisissez votre commentaire">{{ old('commentaire') ?? '' }}</textarea>

                                                    @error('commentaire')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                

                                                <div class="col-md-3" style="margin-top:40px"> 

                                                    @if(isset($value_bouton2))
                                                        <button onclick="return confirm('Faut il confirmer l\'annulation ?')" style="margin-top: -60px; width: 90px;" name="submit" value="{{ $value_bouton2 ?? '' }}" type="submit" class="btn btn-danger">
                                                        {{ $bouton2 }}
                                                        </button>
                                                    @endif

                                                    @if(isset($value_bouton))
                                                        <button onclick="return confirm('Faut il confirmer l\'enregistrement ?')" style="margin-top: -60px; width: 90px;" name="submit" value="{{ $value_bouton ?? '' }}" type="submit" class="btn btn-success">
                                                        {{ $bouton }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                        </td>
    
                                    
    
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>



    var currentValue = 0;
    function handleClick(myRadio) {
        // alert('Old value: ' + currentValue);
        // alert('New value: ' + myRadio.value);
        currentValue = myRadio.value;

        if(currentValue === "Appel d'offre" || currentValue === "Achat direct"){
                
                var div_fournisseur = document.getElementById("div_fournisseur");
                div_fournisseur.style.display = "";

                var div_fournisseur2 = document.getElementById("div_fournisseur2");
                div_fournisseur2.style.display = "";

                var div_fournisseur3 = document.getElementById("div_fournisseur3");
                div_fournisseur3.style.display = "";

                $("#myTable2 tr").remove(); 

                document.getElementById("libelle_type_achats").value = currentValue;


            }else{

                var div_fournisseur = document.getElementById("div_fournisseur");
                div_fournisseur.style.display = "none";

                var div_fournisseur2 = document.getElementById("div_fournisseur2");
                div_fournisseur2.style.display = "none";

                var div_fournisseur3 = document.getElementById("div_fournisseur3");
                div_fournisseur3.style.display = "none";

            }

    }

        editPeriode = function(a){
            const saisie=document.getElementById('periode_id').value;
            if(saisie != ''){
                const block = saisie.split('->');
                var periode_id = block[1];
                var valeur = block[2];

                var delai = document.getElementById('delai').value;
                if(delai==""){
                    delai = 1;
                }


                var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
                
                var date_echeance = moment(d).format('DD/MM/YYYY');
            }else{
                periode_id = "";
                valeur = "";
            }
            
            if (periode_id === undefined) {
                document.getElementById('periode_id').value = "";
            }else{
                document.getElementById('periode_id').value = periode_id;
            }

            if (valeur === undefined) {
                document.getElementById('valeur').value = "";
            }else{
                document.getElementById('valeur').value = valeur;
            }

            if (date_echeance === 'Invalid date') {
                document.getElementById('date_echeance').value = "";
            }else{
                document.getElementById('date_echeance').value = date_echeance;
            }
            
        }  
    
        editPeriode2 = function(a){
                var periode_id = document.getElementById('periode_id').value;
                var valeur = document.getElementById('valeur').value;
                var delai = document.getElementById('delai').value;
                if (periode_id != '') {
                    if (valeur != undefined) {
                        if(delai==""){
                            delai = 1;
                        }


                        var d = new Date(Date.now() + (valeur*delai) * 24*60*60*1000);
                        
                        var date_echeance = moment(d).format('DD/MM/YYYY');
                    }
                }
            
            
            
            if (periode_id === undefined) {
                
            }else{
                document.getElementById('periode_id').value = periode_id;
            }

            if (valeur === undefined) {
                
            }else{
                document.getElementById('valeur').value = valeur;
            }

            if (date_echeance === 'Invalid date') {
                document.getElementById('date_echeance').value = "";
            }else{
                document.getElementById('date_echeance').value = date_echeance;
            }

            if (date_echeance === undefined) {
                document.getElementById('date_echeance').value = "";
            }else{
                document.getElementById('date_echeance').value = date_echeance;
            }
            
        }
    
        addOrganisation = function(a){

            var table = document.getElementById("myTable2");
            var rows = table.querySelectorAll("tr");
            var nbre_organisations = document.getElementById("nbre_organisations").value;
            const libelle_type_achats=document.getElementById('libelle_type_achats').value;
            
            if (libelle_type_achats === "Appel d'offre") {
                nbre_organisations = 1;
            }

            var nbre_rows = rows.length;

            if (nbre_rows<nbre_organisations) {

                var row = table.insertRow(nbre_rows);
                
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                cell1.innerHTML = '<td><input  list="organisation" autocomplete="off" type="text" name="designation_org[]" id="designation_org" class="form-control designation_org" placeholder="Sélectionner un fournisseur"></td>';
                cell2.innerHTML = '<td><a onclick="deleteOrganisation(this)"  style="background-color:transparent; border-color:transparent"><svg style=" color:red" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-x" viewBox="0 0 16 16"><path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/><path fill-rule="evenodd" d="M12.146 5.146a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            }

        }
    
        deleteOrganisation = function(el) {
            var table = document.getElementById("myTable2");
            var rows = table.querySelectorAll("tr");
            var nbre_rows = rows.length;
            $(el).parents("tr").remove(); 
                
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
