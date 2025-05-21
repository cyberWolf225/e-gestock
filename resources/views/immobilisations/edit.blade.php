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
                <div class="card-header entete-table">{{ $title ?? '' }} 
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
                    <form enctype="multipart/form-data" method="POST"
                    
                    @if($route_action === 'store_analyse')
                        action="{{ route('immobilisations.store_analyse') }}"
                        @elseif($route_action === 'update')
                        action="{{ route('immobilisations.update') }}"
                    @endif
                    
                    
                    >
                        @csrf
                        <div class="row">
                            <div class="col-md-9"> 
                                <div class="form-group">
                                    <label class="label">Intitulé de la demande @if($verrou_saisie === null)<span style="color: red"><sup> *</sup></span> @endif </label>

                                    <input onfocus="this.blur()" autocomplete="off" type="text" name="id" class="form-control" required style="font-size: 10px; display:none" value="{{ old('id') ?? $immobilisation->id ?? '' }}">

                                    <input autofocus autocomplete="off" title="Entrez l'intitulé de votre demande"  type="text" name="intitule" class="form-control @error('intitule') is-invalid @enderror" required placeholder="Saisissez l'intitulé de votre demande" style="font-size: 10px; @if($verrou_saisie === 1) background-color: #e9ecef; @endif " value="{{ old('intitule') ?? $immobilisation->intitule ?? '' }}" 
                                    
                                    @if($verrou_saisie === 1)
                                        onfocus="this.blur()"
                                    @endif

                                    >
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
                                    <tr style="background-color: #e9ecef; color: #7d7e8f; vertical-align:middle">
                                        <th style="; vertical-align:middle; width:1%; text-align:center; white-space: nowrap;">BENEFICIAIRE @if($verrou_saisie === null)<span style="; vertical-align:middle; color: red"><sup> *</sup></span> @endif </th>
                                        <th style="; vertical-align:middle; width:20%; text-align:left">DESCRIPTION DU BENEFICIAIRE</th>
                                        <th style="; vertical-align:middle; width:1%; text-align:left; white-space: nowrap;">RÉF. ÉQUIPEMENT @if($verrou_saisie === null)<span style="; vertical-align:middle; color: red"><sup> *</sup></span> @endif </th>
                                        <th style="; vertical-align:middle; width:20%; text-align:left">DÉSIGNATION DE L'ÉQUIPEMENT</th>
                                        <th style="; vertical-align:middle; width:10%; text-align:center; display:none; text-align:center">PRIX U</th>
                                        <th style="; vertical-align:middle; width:7%; text-align:center">QTÉ @if($verrou_saisie === null)<span style="; vertical-align:middle; color: red"><sup> *</sup></span> @endif </th>
                                        @if(isset($champ_qte_sortie))
                                            @if($champ_qte_sortie === 1)
                                            <th style="; vertical-align:middle; width:7%; text-align:center">QTÉ À SORTIR @if($champ_qte_sortie_actif === 1)<span style="; vertical-align:middle; color: red"><sup> *</sup></span> @endif </th>
                                            <th style="; vertical-align:middle; width:7%; text-align:center">QTÉ SORTIE </th>
                                            
                                            @endif
                                        @endif

                                        <th style="; vertical-align:middle; width:13%; text-align:center; display:none">COÛT</th>
                                        <th style="; vertical-align:middle; width:1%; text-align:center">ÉCHANTILLON</th>
                                        @if(isset($champ_observation))
                                            @if($champ_observation === 1)
                                            <th style="; vertical-align:middle; width:20%; text-align:left">OBSERVATIONS</th>
                                            @endif
                                        @endif
                                        
                                        <th style="; vertical-align:middle; text-align:center; width:1%; @if($button_remove_row === 0) display:none; @endif "><a title="Ajouter un nouvel article à votre demande" onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                          </svg></a></th>

                                          @if(isset($champ_stiker))
                                            @if($champ_stiker === 1)
                                            <th style="; vertical-align:middle; text-align:center; width:1%; " title="Saisie de N° Sticker et N° de serie  ">
                                                <a href="../stiker/{{ Crypt::encryptString($immobilisation->id ?? 0) }}">
                                                    <svg style="color: black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upc" viewBox="0 0 16 16">
                                                    <path d="M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-7zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0v-7z"/>
                                                    </svg>
                                                </a>
                                            </th>
                                            @endif
                                          @endif

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($immobilisations as $immobilisat)

                                        <?php 

                                            $detail_immobilisations_id = Crypt::encryptString($immobilisat->detail_immobilisations_id);

                                            $qte_restant = $immobilisat->qte - $immobilisat->qte_sortie;

                                            $sortie_totale = null;

                                            if ($immobilisat->qte === $immobilisat->qte_sortie) {
                                                $sortie_totale = 1;
                                            }

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
                                    
                                        <tr style="border-collapse: collapse; padding: 0; margin: 0;">
                                            <td style="border-collapse: collapse; padding: 0; margin: 0; @if($verrou_saisie === null) background-color:white @endif ">

                                                <input onfocus="this.blur()" autocomplete="off" required type="text" id="detail_immobilisations_id" name="detail_immobilisations_id[]" class="form-control detail_immobilisations_id" style="font-size:10px; text-align:center; border:none; border-color:transparent; display:none" value="{{ $immobilisat->detail_immobilisations_id ?? '' }}">

                                                <input autocomplete="off" title="Saisissez le matricule ou le nom du bénéficiare" required type="text" onkeyup="editAgent(this)" id="beneficiaire" name="beneficiaire[]" class="form-control beneficiaire" style="font-size:10px; text-align:center; border:none; border-color:transparent; @if ($verrou_saisie === 1) background-color:transparent; @endif" value="{{ $beneficiaire ?? '' }}" 
                                                
                                                @if($verrou_saisie === null)
                                                    list="list_agent" 
                                                    @elseif($verrou_saisie === 1)
                                                    onfocus="this.blur()"
                                                @endif

                                                >
                                            </td>

                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                <input autocomplete="off" title="Nom & Prénoms du bénéficiaire" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; " type="text" id="description_beneficiaire" name="description_beneficiaire[]" class="form-control description_beneficiaire" value="{{ $description_beneficiaire ?? '' }}">

                                                <input autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; display:none" type="text" id="type_beneficiaire" name="type_beneficiaire[]" class="form-control type_beneficiaire" value="{{ $immobilisat->type_beneficiaire ?? '' }}">
                                            </td>

                                            <td style="border-collapse: collapse; padding: 0; margin: 0; @if($verrou_saisie === null) background-color:white @endif ">
                                                <input autocomplete="off" title="sélectionnez l'article en saisissant soit la référence ou la désignation" required type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px; text-align:left; border:none; border-color:transparent; @if ($verrou_saisie === 1) background-color:transparent; @endif" value="{{ $immobilisat->ref_articles ?? '' }}" 
                                                
                                                @if($verrou_saisie === null)
                                                    list="list_article" 
                                                    @elseif($verrou_saisie === 1)
                                                    onfocus="this.blur()"
                                                @endif

                                                >
                                                <input autocomplete="off" style="display: none; font-size:10px;" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id" value="{{ $immobilisat->magasin_stocks_id ?? '' }}">
                                            </td>
                                            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                <input autocomplete="off" title="Désignation de l'article sélectionné" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; " type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article" value="{{ $immobilisat->design_article ?? '' }}">
                                            </td>
                                            <td style="display: none; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input autocomplete="off" title="Prix unitaire de l'article sélectionné" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup" value="{{ $immobilisat->prixu ?? '' }}">
                                            </td>
                                            <td style="border-collapse: collapse; padding: 0; margin: 0; @if($verrou_saisie === null) background-color:white @endif ">

                                                <input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center" name="qte_stock[]" class="form-control qte_stock" value="{{ $immobilisat->qte_stock ?? '' }}">

                                                <input autocomplete="off" title="Entrez la quantité d'article souhaitée" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center; font-size:10px; border:none; border-color:transparent; @if ($verrou_saisie === 1) background-color:transparent; @endif" name="qte[]" class="form-control qte" value="{{ $immobilisat->qte ?? '' }}" 
                                                
                                                @if($verrou_saisie === 1)
                                                    onfocus="this.blur()"
                                                @endif
                                                
                                                >
                                            </td>

                                            @if(isset($champ_qte_sortie))
                                                @if($champ_qte_sortie === 1)

                                                    <td style="border-collapse: collapse; padding: 0; margin: 0; @if($champ_qte_sortie_actif === 1) background-color:white @else background-color:transparent @endif @if($sortie_totale === 1) background-color:transparent @endif ">

                                                    <input class="form-control qte_restant" onfocus="this.blur()" style="display:none; border:none" value="{{ $qte_restant ?? '' }}">

                                                    <input autocomplete="off" title="Entrez la quantité d'articles sortis" required type="text" onkeypress="validate(event)" onkeyup="editQteSortie(this)" style="text-align:center; font-size:10px; border:none; border-color:transparent; @if ($champ_qte_sortie_actif === null) background-color:transparent; @endif @if ($sortie_totale === 1) background-color:transparent; @endif " name="qte_sortie[]" class="form-control qte_sortie" value="{{ $qte_restant ?? '' }}"
                                                    
                                                    @if($champ_qte_sortie_actif === null)
                                                        onfocus="this.blur()"
                                                    @endif

                                                    @if($sortie_totale === 1)
                                                        onfocus="this.blur()"
                                                    @endif
                                                    
                                                    >
                                                    </td>

                                                    <td style="border-collapse: collapse; padding: 0; margin: 0; background-color:transparent">

                                                        <input autocomplete="off" title="Entrez la quantité d'articles sortis" required type="text" onkeypress="validate(event)" style="text-align:center; font-size:10px; border:none; border-color:transparent; background-color:transparent;" name="qte_deja_sortie[]" class="form-control qte_deja_sortie" value="{{ $immobilisat->qte_sortie ?? '' }}" onfocus="this.blur()">
                                                    </td>
                                                
                                                @endif
                                            @endif

                                            <td style="display:none; border-collapse: collapse; padding: 0; margin: 0;">
                                                <input autocomplete="off" title="Montant de la demande de cet article" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" onkeypress="validate(event)" type="text" name="montant[]" class="form-control montant" value="{{ $immobilisat->montant ?? '' }}">
                                            </td>

                                            <td style="text-align: center; vertical-align:middle;  @if($champ_echantillon === 1) display:flex; border-collapse: collapse; padding: 0; margin: 0; @endif ">
                                                @if($champ_echantillon === 1)
                                                
                                                    <input style="height: 30px;" accept="image/*" type="file" name="echantillon[]">

                                                @endif
                                                

                                                <input style="display: none" type="checkbox" name="echantillon_flag[]" checked @if(isset($echantillon)) value="1" @else value="0" @endif >

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
                                            
                                            @if(isset($champ_observation))
                                                @if($champ_observation === 1)
                                                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                                                    <textarea 
                                                    @if(isset($champ_observation_actif)) @if($champ_observation_actif === 0) onfocus="this.blur()" @endif  @endif
                                                    
                                                    autocomplete="off" title="Saisissez vos observations" type="text" name="observations[]" class="form-control observations" style="resize: none; @if(isset($champ_observation_actif)) @if($champ_observation_actif === 0) border:none; background-color:transparent @endif @endif ">{{ $immobilisat->observations ?? '' }}</textarea>
                                                </td>
                                                @endif
                                            @endif
                                            
                                            <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0; @if($button_remove_row === 0) display:none; @endif">
                                                <a title="Retirer cet article de la liste de votre demande" onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                </svg></a>
                                            </td>

                                            @if(isset($champ_stiker))
                                                @if($champ_stiker === 1)
                                                <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                                                
                                                </td>
                                                @endif
                                            @endif
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
                                
                                <tr>
                                    <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">

                                        @if($champ_piece_jointe === 1) 
                                            <a title="Rattacher une nouvelle pièce" onclick="myCreateFunction2()" href="#"><svg style="color: green; margin-top:-3px; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                </svg></a> 
                                        @endif

                                    </td>
                                </tr>

                                @if(count($piece_jointes)>0)
                                    @foreach($piece_jointes as $piece_jointe)
                                        <tr>
                                            <td>
                                                @if($champ_piece_jointe === 1) 
                                                    @if(!isset($griser))
                                                        <input type="file" name="piece[]" class="form-control-file" id="piece" >
                                                    @endif
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
                                                
                                                @if($champ_piece_jointe === 1) 
                                                    <input @if(isset($griser)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                                    @if(isset($piece_jointe->flag_actif))
                                                        @if($piece_jointe->flag_actif == 1)
                                                            checked
                                                        @endif
                                                    @endif >
                                                @endif

                                                @if(!isset($griser)) 
                                                    @if($champ_piece_jointe === 1) 
                                                        <a  title="Retirer ce fichier" onclick="removeRow2(this)" href="#">
                                                            
                                                            <svg style="color: red; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                @endif
                                                

                                            </td>
                                        </tr>
                                    @endforeach
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                @else
                                    <tr>
                                        
                                        <td>
                                            @if($champ_piece_jointe === 1) 
                                                <input type="file" name="piece[]" class="form-control-file" id="piece" >  
                                            @endif 
                                        </td>
                                        <td style="border: none;vertical-align: middle; text-align:right">
                                            
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
                                    <td style="border:  ; vertical-align:middle; width:90%">
                                        
                                        <textarea class="form-control" name="commentaire" rows="2" style="width: 100%;" placeholder="Saisissez un commentaire">{{ $statut_immobilisation->commentaire ?? '' }}</textarea>
                                    
                                    </td>

                                    <td  style="border:  ; vertical-align:middle; width:10%; display:flex">
                                    
                                        <button onclick="return confirm('{{ $confirm_button1 ?? '' }}')" title="{{ $title_button1 ?? '' }}"  type="submit" name="submit" value="{{ $value_button1 ?? '' }}" class="btn btn-success" style="font-size:10px; margin-top:1px; vertical-align:middle; display:{{ $display_button1 ?? '' }}">
                                            {{ $button1 ?? '' }}
                                        </button> &nbsp;&nbsp;

                                        <button onclick="return confirm('{{ $confirm_button2 ?? '' }}')" title="{{ $title_button2 ?? '' }}"  type="submit" name="submit" value="{{ $value_button2 ?? '' }}" class="btn btn-danger" style="font-size:10px; margin-top:1px; vertical-align:middle; display:{{ $display_button2 ?? '' }}">
                                            {{ $button2 ?? '' }}
                                        </button> &nbsp;&nbsp;
                                        
                                        <button onclick="return confirm('{{ $confirm_button3 ?? '' }}')" title="{{ $title_button3 ?? '' }}"  type="submit" name="submit" value="{{ $value_button3 ?? '' }}" class="btn btn-warning" style="font-size:10px; margin-top:1px; vertical-align:middle; display:{{ $display_button3 ?? '' }}">
                                            {{ $button3 ?? '' }}
                                        </button>

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

        editQteSortie = function(e){
            var tr=$(e).parents("tr");

            var qte_sortie=tr.find('.qte_sortie').val();
            qte_sortie = qte_sortie.trim();
            qte_sortie = qte_sortie.replace(' ','');
            qte_sortie = reverseFormatNumber(qte_sortie,'fr');
            qte_sortie = qte_sortie.replace(' ','');
            qte_sortie = qte_sortie * 1;

            var qte_stock=tr.find('.qte_stock').val();
            qte_stock = qte_stock.trim();
            qte_stock = qte_stock * 1;

            var qte_restant=tr.find('.qte_restant').val();
            qte_restant = qte_restant.trim();
            qte_restant = qte_restant.replace(' ','');
            qte_restant = reverseFormatNumber(qte_restant,'fr');
            qte_restant = qte_restant.replace(' ','');
            qte_restant = qte_restant * 1;            

            if(qte_sortie > qte_restant) {

                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité sortie ne peut être supérieure à la quantité demendée',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
                });

                qte_sortie = 0;

            }else if(qte_restant > qte_stock) {

                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Attention!!! : la quantité sortie ne peut être supérieure à la quantité disponible en stock... (Quantité disponible en stock : '+ qte_stock +' )',
                focusConfirm: false,
                confirmButtonText:
                'Compris'
                });

                qte_sortie = 0;
            }  



            var int3=new Intl.NumberFormat("fr-FR", {maximumFractionDigits: 0});
            tr.find('.qte_sortie').val(int3.format(qte_sortie));
            
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

                cell1.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;background-color:white"><input autocomplete="off" title="Saisissez le matricule ou le nom du bénéficiare" required list="list_agent" type="text" onkeyup="editAgent(this)" id="beneficiaire" name="beneficiaire[]" class="form-control beneficiaire" style="font-size:10px; text-align:center; border:none;"></td>';

                cell2.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Nom & Prénoms du bénéficiaire" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; " type="text" id="description_beneficiaire" name="description_beneficiaire[]" class="form-control description_beneficiaire"><input autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px; display:none" type="text" id="type_beneficiaire" name="type_beneficiaire[]" class="form-control type_beneficiaire"></td>';

                cell3.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;background-color:white"><input autocomplete="off" title="sélectionnez l\'article en saisissant soit la référence ou la désignation" required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" style="font-size:10px; text-align:left; border:none;"><input autocomplete="off" style="display: none; font-size:10px;" required type="text" id="magasin_stocks_id" name="magasin_stocks_id[]" class="form-control magasin_stocks_id"></td>';

                cell4.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Désignation de l\'article sélectionné" required type="text" onkeyup="editMontant(this)" onfocus="this.blur()" style="background-color: transparent; border-color: transparent; font-size:10px;" id="design_article" name="design_article[]" class="form-control design_article"></td>';

                cell5.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Prix unitaire de l\'article sélectionné" required type="text" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" onkeyup="editMontant(this)" onkeypress="validate(event)" name="cmup[]" class="form-control cmup"></td>';

                cell6.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;background-color:white"><input onfocus="this.blur()" style="background-color: #e9ecef; text-align:center; display:none; font-size:10px;" autocomplete="off" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" name="qte_stock[]" class="form-control qte_stock"><input autocomplete="off" title="Entrez la quantité d\'article souhaitée" required type="text" onkeyup="editMontant(this)" onkeypress="validate(event)" style="text-align:center; font-size:10px;border:none;" name="qte[]" class="form-control qte"></td>';

                cell7.innerHTML = '<td style="border-collapse: collapse; padding: 0; margin: 0;"><input autocomplete="off" title="Montant de la demande de cet article" required type="text" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right; font-size:10px;" name="montant[]" onkeypress="validate(event)" class="form-control montant"></td>';

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
