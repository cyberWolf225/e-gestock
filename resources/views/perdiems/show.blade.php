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
<br>
<div class="container">
    
    <datalist id="compte_impute">
        @foreach($credit_budgetaires as $credit_budgetaire)
            <option value="{{ $credit_budgetaire->ref_fam }}->{{ $credit_budgetaire->design_fam }}->{{ $credit_budgetaire->credit }}->{{ $credit_budgetaire->credit_budgetaires_id }}">{{ $credit_budgetaire->design_fam }}</option>
        @endforeach
    </datalist>

    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="card">
                <div class="card-header entete-table">{{ __(strtoupper('Details de perdiems')) }}
                    <span style="float: right; margin-right: 20px;">DATE : <strong>{{ date('d/m/Y',strtotime($perdiem->created_at ?? null)) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">EXERCICE : <strong>{{ $perdiem->exercice ?? '' }}</strong></span>
                    
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

                    <form enctype="multipart/form-data" method="POST" action="{{ route('perdiems.update') }}">
                        @csrf  
                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Compte à imputer </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled required onkeyup="editCompte(this)"  list="famille" autocomplete="off" type="text" name="ref_fam" id="ref_fam" value="{{ old('ref_fam') ?? $famille_perdiem->ref_fam ?? $perdiem->ref_fam ?? '' }}"  class="form-control @error('ref_fam') is-invalid @enderror ref_fam" >
                                        </div>
                                        @error('ref_fam')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="design_fam" id="design_fam" value="{{ old('design_fam') ?? $famille_perdiem->design_fam ?? $perdiem->design_fam ?? '' }}"  class="form-control griser design_fam">

                                            @error('design_fam')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Structure </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input disabled required onfocus="this.blur()" autocomplete="off" type="text" name="credit_budgetaires_id" id="credit_budgetaires_id" class="form-control @error('credit_budgetaires_id') is-invalid @enderror credit_budgetaires_id" style="background-color: #e9ecef; display:none" value="{{ old('credit_budgetaires_id') ?? $disponible->id ?? '' }}">

                                            <input disabled required onkeyup="editStructure(this)" list="structure" autocomplete="off" type="text" name="code_structure" id="code_structure" class="form-control @error('code_structure') is-invalid @enderror code_structure" value="{{ old('code_structure') ?? $structure_perdiem->code_structure ?? $perdiem->code_structure ?? '' }}">
                                        </div>
                                        @error('code_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled required onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" type="text" name="nom_structure" id="nom_structure" class="form-control griser @error('nom_structure') is-invalid @enderror nom_structure" value="{{ old('nom_structure') ??  $structure_perdiem->nom_structure ??   $perdiem->nom_structure ?? '' }}">
                                        </div>
                                        @error('nom_structure')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="label">Gestion  </label> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">
                                            <input disabled required  onkeyup="editGestion(this)" list="gestion" autocomplete="off" type="text" name="code_gestion" id="code_gestion" class="form-control @error('code_gestion') is-invalid @enderror code_gestion" value="{{ old('code_gestion') ?? $gestion_perdiem->code_gestion ?? $perdiem->code_gestion ??  '' }}">
                                        </div>
                                        @error('code_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 pl-0">
                                        <div class="form-group d-flex ">
                                            <input disabled required onfocus="this.blur()" autocomplete="off" type="text" name="libelle_gestion" id="libelle_gestion" class="form-control @error('libelle_gestion') is-invalid @enderror libelle_gestion" style="background-color: #e9ecef" value="{{ old('libelle_gestion') ?? $gestion_perdiem->libelle_gestion ?? $perdiem->libelle_gestion ?? '' }}">
                                        </div>
                                        @error('libelle_gestion')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
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
                                    <div class="col-sm-3" style="display: none;">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">N° Oracle </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1" style="display: none">
                                        <div class="form-group d-flex ">

                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef;color:red; font-weight:bold; margin-top:-7px; display:none" id="perdiems_id" type="text" class="form-control @error('perdiems_id') is-invalid @enderror" name="perdiems_id" value="{{ old('perdiems_id') ?? $perdiem->id ?? '' }}"  autocomplete="off" >

                                            <input disabled onfocus="this.blur()" style="background-color: #e9ecef;color:red; font-weight:bold; margin-top:-7px" id="num_or" type="text" class="form-control @error('num_or') is-invalid @enderror" name="num_or" value="{{ old('num_or') ?? $perdiem->num_or ?? '' }}"  autocomplete="off" >
                                        
                                        
                                        </div>
                                        @error('num_or')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">N° Perdiem</label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3 pr-1">
                                        <div class="form-group d-flex ">

                                            <input onfocus="this.blur()" style="background-color: transparent;color:red; font-weight:bold; margin-top:-px; border:none" id="num_pdm" type="text" class="form-control @error('num_pdm') is-invalid @enderror" name="num_pdm" value="{{ old('num_pdm') ?? $perdiem->num_pdm ?? '' }}"  autocomplete="off" >
                                        
                                        
                                        </div>
                                        @error('num_pdm')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-3">
                                        <div class="form-group" style="text-align: right">
                                            <label class="mt-1 label">Intitulé </label> 
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-9 pr-1">
                                        <div class="form-group d-flex ">

                                            <textarea disabled rows="3" id="perdiems_intitule" type="text" class="form-control @error('perdiems_intitule') is-invalid @enderror" name="perdiems_intitule" style="resize:none"  >{{ old('perdiems_intitule') ?? $perdiem->libelle ?? '' }}</textarea>
                                        
                                            @error('perdiems_intitule')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex" style="margin-top: -10px;">
                                    <div class="col-sm-6" style="text-align: right">

                                        
                                        
                                        <label class="label">Solde disponible avant opération : </label>
                                    
                                    </div>
                                    <div class="col-sm-3"><label class="label"> {{ strrev(wordwrap(strrev($perdiem->solde_avant_op ?? ''), 3, ' ', true)) ?? '' }} </label></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped" id="myTable" style="width:100%; border-collapse: collapse; padding: 0; margin: 0;">

                                    <thead>
                                        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                            <th style="text-align: center; vertical-align:middle; white-space:nowrap; width:1px">N°</th>
                                            <th style="text-align: center; vertical-align:middle">NOM ET PRÉNOM(S)</th>
                                            <th style="text-align: center; vertical-align:middle; width:20%">MONTANTS</th>
                                            <th style="text-align: center; vertical-align:middle ; white-space:nowrap; width:1px">PIÈCE D'IDENTITÉ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>

                                        @foreach($detail_perdiems as $detail_perdiem)

                                        <?php 
                                            $piece = null;
                                            if (isset($detail_perdiem->piece)) {
                                                $piece = $detail_perdiem->piece;
                                            }
                                        ?>
                                            
                                        
                                        <tr>
                                            <td style="text-align: center; vertical-align:middle; white-space:nowrap; width:1px; padding: 0; margin: 0;">{{ $i }}</td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"><input disabled style="background-color:transparent; border:none" list="agent" required class="form-control form-control-sm" name="nom_prenoms[]" autocomplete="off" value="{{ $detail_perdiem->nom_prenoms ?? '' }}">

                                                <input disabled style="display: none" onfocus="this.blur()" required class="form-control form-control-sm" name="detail_perdiems_id[]" autocomplete="off" value="{{ $detail_perdiem->id ?? '' }}">
                                            </td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0; display:flex"><input disabled required class="form-control form-control-sm montant" name="montant[]" autocomplete="off" onkeypress="validate(event)" style="text-align:right;background-color:transparent; border:none" onkeyup="editMontant(this)" value="{{ strrev(wordwrap(strrev($detail_perdiem->montant ?? ''), 3, ' ', true)) }}">
                                                
                                            <input disabled required class="form-control form-control-sm montant_bis" name="montant_bis[]" onkeypress="validate(event)" style="text-align:right; display:none" value="{{ $detail_perdiem->montant ?? '' }}"></td>
                                            <td style="text-align:center; vertical-align:middle; padding: 0; margin: 0;">


                                            @if(isset($piece))

                                                <!-- Modal -->

                                                    <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $detail_perdiem->id }}">
                                                            <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                            </svg>
                                                    </a>
                                                    <div class="modal fade" id="exampleModalCenter{{ $detail_perdiem->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="text-align: center">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Pièce d'identité de { <span style="color: red">{{ $detail_perdiem->nom_prenoms ?? '' }}</span> } </h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <img src='{{ asset('storage/'.$piece) }}' style='width:100%;'>
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
                                        <?php $i++; ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color:#aabcc6">
                                            <td colspan="2" style="text-align: ; vertical-align:middle; padding: 0; margin: 0; font-weight:bold; color:#033d88">TOTAL</td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"><input disabled onfocus="this.blur()" required class="form-control form-control-sm montant_total" name="montant_total" id="montant_total" style="text-align:right; background-color:transparent; border:none; color:#155724; font-weight:bold" value="{{ strrev(wordwrap(strrev($perdiem->montant_total ?? ''), 3, ' ', true)) }}"></td>
                                            <td style="text-align: center; vertical-align:middle; padding: 0; margin: 0;"></td>
                                        </tr>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                        <table style="width:100%; margin-bottom:10px;" id="tablePiece">
                                
                            <tr>
                                <td style="border: none;vertical-align: middle; text-align:right; border-collapse: collapse; padding: 0; margin: 0;"  colspan="2">
                                    @if(!isset($griser)) 
                                        
                                    @endif 
                                </td>
                            </tr>
                            @if(count($piece_jointes)>0)
                                @foreach($piece_jointes as $piece_jointe)
                                    <tr>
                                        <td>
                                             
                                        </td>
                                        <td style="border: none;vertical-align: middle; text-align:right">
                                            <span style="margin-right: 20px;">{{ $piece_jointe->name ?? '' }}</span>  
                                            <a @if(isset($griser)) disabled @endif style="margin-right: 20px;" target="_blank" href="/piece_jointes/show/{{ Crypt::encryptString($piece_jointe->id ?? 0 ) }}">
                                                <svg style="color: blue; cursor:pointer" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                  </svg>
                                            </a>
                                            <input disabled @if(isset($griser)) disabled @endif style="display: none" onfocus="this.blur()" class="form-control" name="piece_jointes_id[{{ $piece_jointe->id ?? 0 }}]" value="{{ $piece_jointe->id ?? 0 }}">
                                            
                                            <input disabled @if(isset($griser)) disabled @endif name="piece_flag_actif[{{ $piece_jointe->id }}]" type="checkbox" style="margin-right: 20px; vertical-align: middle" 
                                            @if(isset($piece_jointe->flag_actif))
                                                @if($piece_jointe->flag_actif == 1)
                                                    checked
                                                @endif
                                            @endif >

                                            @if(!isset($griser)) 

                                                

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
                                    </td>
                                    <td style="border: none;vertical-align: middle; text-align:right">
                                        
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td colspan="2" style="border: none;vertical-align: middle; text-align:right"> 
                                </td>
                            </tr>
                            
                        </table>
                        
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
