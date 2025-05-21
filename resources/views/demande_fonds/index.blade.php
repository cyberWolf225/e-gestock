@extends('layouts.admin')

@section('styles_datatable')
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('../plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('../dist/css/adminlte.min.css') }}">

  <link rel="shortcut icon" href="{{ asset('../dist/img/logo.png') }}">

  <style>
    .fond{
        background: url('../../dist/img/sante2.jpg') no-repeat;
        background-size: 100% 100%;
        font-size: 11px;
    }
  </style>

@endsection
 
@section('content')
  
  <div class="container">
    
    @if(isset($acces_create))
      <div style="margin-bottom: 10px; margin-top:-20px;" class="row">
          <div class="col-lg-12">
              <a class="btn btn-sm" href="{{ route("demande_fonds.create") }}" style="color: #033d88; background-color:orange; font-weight:bold">
                  Créer une demande de fonds
              </a>
          </div>
      </div>
    @else
      <br>
    @endif
    
      <div class="card">
        <div class="card-header entete-table">{{ __(strtoupper('Liste des demandes de fonds')) }}
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-12">

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
                
                <table id="example1" class="table table-striped bg-white" style="width: 100%">
                  <thead>
                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                      <th style="vertical-align: middle; text-align: center; width:1px; white-space:nowrap">#</th>
                      <th style="vertical-align: middle; text-align: left; width:1px; white-space:nowrap">N° DEMANDE</th>
                      <th style="vertical-align: middle; text-align: left">INTITULÉ</th>
                      <th style="vertical-align: middle ;text-align: center; width:12%; white-space:nowrap text-align:center">SOLDE AVANT OPT.</th>
                      <th style="vertical-align: middle ;text-align: center; width:1px; white-space:nowrap text-align:center">MONTANT</th>
                      <th style="vertical-align: middle; text-align: left;">COMPTE À IMPUTER</th>
                      <th style="vertical-align: middle; text-align: left;">STATUT</th>
                      @if(isset($colonne_beneficiaire))
                        @if($colonne_beneficiaire===true)
                          <th style="vertical-align: middle; text-align: left; width:1px; white-space:nowrap">BÉNÉFICIAIRE</th>
                        @endif
                      @endif
                      
                      
                      <th style="vertical-align: middle; text-align:center; width: 1px;
                    white-space: nowrap;"> 
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; ?>
                    @foreach($demande_fonds as $demande_fond) 

                        <?php

                        $demande_fonds_id = Crypt::encryptString($demande_fond->id);

                        $libelle_moyen_paiement = null;
                        $terminer = null;

                        $moyen_paiement = DB::table('demande_fonds as df')
                        ->join('moyen_paiements as mp','mp.id','=','df.moyen_paiements_id')
                        ->where('df.id',$demande_fond->id)
                        ->select('mp.libelle','df.terminer')
                        ->first();

                        if ($moyen_paiement!=null) {
                            $libelle_moyen_paiement = $moyen_paiement->libelle;
                            $terminer = $moyen_paiement->terminer;
                        }

                        $title_show = "Voir les details de la demande de fonds";
                        $title_edit = "";
                        $title_transfert = "";
                        $title_create = "";
                        

                        $href_show = "show/".$demande_fonds_id;
                        $href_edit = "";
                        $href_transfert = "";
                        $href_create = "";

                        $display_show = "";
                        $display_edit = "none";
                        $display_transfert = "none";
                        $display_create = "none";

                        $title_print = "Imprimer la demande de fonds";
                        $href_print = "";
                        $display_print = "none";

                          $libelle = null;

                          $statut_demande_fond = DB::table('statut_demande_fonds as sdf')
                          ->join('type_statut_demande_fonds as tsdf','tsdf.id','=','sdf.type_statut_demande_fonds_id')
                          ->where('sdf.demande_fonds_id',$demande_fond->id)
                          ->orderByDesc('sdf.id')
                          ->limit(1)
                          ->first();

                          if ($statut_demande_fond!=null) {
                              $libelle = $statut_demande_fond->libelle;
                          }

                          if ($libelle === 'Soumis pour validation' or $libelle === 'Annulé' or $libelle === 'Annulé (Gestionnaire des achats)') {

                            if ($type_profils_name === 'Pilote AEE') {

                              $title_edit = "Modifier la demande de fonds";
                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              $title_transfert = "Transmettre la demande de fonds au Responsable des achats";
                              $href_transfert = "send/".$demande_fonds_id;
                              $display_transfert = "";

                            }elseif ($type_profils_name === 'Gestionnaire des achats') {
                              if ($libelle === 'Annulé (Gestionnaire des achats)') {
                                  $title_edit = "Etablir la demande de fonds (espèces ou chèque)";
                                  $href_edit = "edit/".$demande_fonds_id;
                                  $display_edit = "";
                              }
                            }
                            
                          }elseif ($libelle === 'Imputé (Gestionnaire des achats)' or $libelle === 'Édité (Gestionnaire des achats)' or $libelle === 'Annulé (Responsable des achats)') {

                            if ($type_profils_name === 'Gestionnaire des achats') {
                              $title_edit = "Etablir la demande de fonds (espèces ou chèque)";
                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";
                            }


                          }elseif($libelle === 'Transmis (Responsable des achats)' or $libelle === 'Visé (Responsable des achats)' or $libelle === 'Demande de cotation' or $libelle === 'Demande de cotation (Annulé)' or $libelle === 'Annulé (Responsable DMP)'){
                            if ($type_profils_name === 'Responsable des achats') {
                              
                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = "Transmettre la demande de fonds au Responsable DMP";
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Demande de cotation (Validé)') {
                            if ($type_profils_name === 'Responsable des achats') {
                              

                              if ($libelle_moyen_paiement === 'Chèque') {

                                $title_edit = "Soumettre la demande cotation aux fourmisseurs présélectionnés";

                                $href_edit = "edit/".$demande_fonds_id;
                                $display_edit = "";

                              }

                              

                            }
                          }elseif ($libelle === 'Transmis pour cotation') {
                            if ($type_profils_name === 'Responsable des achats') {
                              

                              if ($libelle_moyen_paiement === 'Chèque') {

                                $title_edit = "Modifier la demande cotation transmise aux fourmisseurs";

                                $href_edit = "edit/".$demande_fonds_id;
                                $display_edit = "";

                              }

                              

                            }
                          }elseif ($libelle === 'Coté') {
                            if ($type_profils_name === 'Responsable des achats') {
                              

                              if ($libelle_moyen_paiement === 'Chèque') {

                                $title_edit = "Sélectionner le fournisseur";

                                $href_edit = "edit/".$demande_fonds_id;
                                $display_edit = "";

                              }

                              

                            }
                          }elseif ($libelle === "Fournisseur sélectionné" or $libelle==='Rejeté (Responsable DMP)') {
                                        
                            if ($type_profils_name === "Responsable des achats") {
                                
                                if ($libelle_moyen_paiement === 'Chèque') {

                                  $title_edit = "Transférer la cotation du mieux disant au Responsable DMP pour validation";

                                  $href_edit = "edit/".$demande_fonds_id;
                                  $display_edit = "";

                                } 
                                

                            }

                        }
                          elseif ($libelle === 'Transmis (Responsable DMP)' or $libelle === 'Demande de cotation (Transmis Responsable DMP)' or $libelle === 'Signé (Responsable DMP)' or $libelle === 'Annulé (Responsable contrôle budgetaire)') {
                            if ($type_profils_name === 'Responsable DMP') {
                              
                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = "Transmettre le dossier à la Direction en charge du Contrôle de Gestion";
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Transmis (Responsable Contrôle Budgétaire)' or $libelle === 'Visé (Responsable Contrôle Budgétaire)' or $libelle ===  'Annulé (Chef Département Contrôle Budgétaire)') {

                            if ($type_profils_name === 'Responsable contrôle budgetaire') {
                              
                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = "Transmettre le dossier au Chef du Département Contrôle de Gestion";
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Transmis (Chef Département Contrôle Budgétaire)' or $libelle === 'Visé (Chef Département Contrôle Budgétaire)' or $libelle === 'Annulé (Responsable DCG)') {

                            if ($type_profils_name === 'Chef Département DCG') {
                              
                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = "Transmettre le dossier au Directeur en charge du Contrôle de Gestion";
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Transmis (Responsable DCG)' or $libelle === 'Signé (Responsable DCG)' or $libelle === 'Annulé (Directeur Général Adjoint)' or $libelle === 'Annulé (Responsable DFC)') {

                            if ($type_profils_name === 'Responsable DCG') {
                              
                              if ($demande_fond->montant <= 250000) {
                                $message_transmission = 'Transmettre le dossier à la DFC';
                              }else{
                                $message_transmission = 'Transmettre le dossier au DGAAF';
                              }

                              $title_edit = "Signer la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Signer la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = $message_transmission;
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Transmis (Directeur Général Adjoint)' or $libelle === 'Visé (Directeur Général Adjoint)' or $libelle === 'Annulé (Directeur Général)') {

                            if ($type_profils_name === 'Directeur Général Adjoint') {
                              
                              if ($demande_fond->montant <= 5000000) {
                                $message_transmission = 'Transmettre le dossier au DFC';
                              }else{
                                $message_transmission = 'Transmettre le dossier au DG';
                              }

                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = $message_transmission;
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Transmis (Directeur Général)' or $libelle === 'Visé (Directeur Général)') {

                            if ($type_profils_name === 'Directeur Général') {

                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = 'Transmettre le dossier au DFC';
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Transmis (Responsable DFC)' or $libelle === 'Visé (Responsable DFC)') {

                            if ($type_profils_name === 'Responsable DFC') {

                              $title_edit = "Viser la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Viser la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                              // $title_transfert = 'Transmettre au Secrétariat du Département Comptabilité de la DFC la demande de fonds pour traitement';
                              // $href_transfert = "send/".$demande_fonds_id;
                              // $display_transfert = "";

                            }
                          }elseif ($libelle === 'Validé') {

                            if ($type_profils_name === 'Gestionnaire des achats') {

                              $title_edit = "Éditer la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Éditer la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                            }
                          }elseif ($libelle === 'Édité') {

                            if ($type_profils_name === 'Pilote AEE') {

                              $title_edit = "Retirer la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Retirer la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                            }elseif ($type_profils_name === 'Gestionnaire des achats') {

                              $title_edit = "Éditer la demande de fonds et le bon commande";

                              if ($libelle_moyen_paiement != 'Chèque') {

                                $title_edit = "Éditer la demande de fonds";

                              }

                              $href_edit = "edit/".$demande_fonds_id;
                              $display_edit = "";

                            }
                          }elseif ($libelle === 'Accordé' or $libelle === 'Dépense justifiée') {

                            if ($type_profils_name === 'Agent Cnps') {


                              if ($terminer === 0) {
                                $title_edit = "Justifier l'utilisation des fonds accordés";

                                $href_edit = "edit/".$demande_fonds_id;
                                $display_edit = "";
                              }

                            }
                          }

                          $statut_demande_fond_edit = DB::table('statut_demande_fonds as sda')
                          ->join('type_statut_demande_fonds as tsda','tsda.id','=','sda.type_statut_demande_fonds_id')
                          ->where('tsda.libelle','Validé') // Édité Accordé
                          ->orderByDesc('sda.id')
                          ->limit(1)
                          ->select('sda.id')
                          ->where('sda.demande_fonds_id',$demande_fond->id)
                          ->first();
                          if ($statut_demande_fond_edit!=null) {
                              $statut_demande_fond_edit_id = $statut_demande_fond_edit->id;


                              $statut_demande_fond_annule = DB::table('statut_demande_fonds as sda')
                                  ->join('type_statut_demande_fonds as tsda','tsda.id','=','sda.type_statut_demande_fonds_id')
                                  ->whereIn('tsda.libelle',['Annulé (Responsable des achats)'])
                                  ->orderByDesc('sda.id')
                                  ->limit(1)
                                  ->select('sda.id')
                                  ->where('sda.demande_fonds_id',$demande_fond->id)
                                  ->first();
                                  if ($statut_demande_fond_annule!=null) {
                                      $statut_demande_fond_annule_id = $statut_demande_fond_annule->id;
                                  }else{
                                      $statut_demande_fond_annule_id = 0;
                                  }

                                  if ($statut_demande_fond_edit_id < $statut_demande_fond_annule_id) {
                                      $statut_demande_fond_edit_id = null;
                                  }

                          }else{
                              $statut_demande_fond_edit_id = null;
                          }

                          if ($statut_demande_fond_edit_id !=null) {
                            $title_print = "Imprimer le bon de commande";
                            

                            $path = 'storage/documents/demande_fonds/'.$demande_fond->num_dem.'.pdf';
                            $public_path = str_replace("/","\\",public_path($path));

                            $type_operations_libelle = Crypt::encryptString('demande_fonds');

                            if(file_exists($public_path)){

                                $href_print = '/documents/'.$demande_fonds_id.'/'.$type_operations_libelle;
                                $display_print = "";
                                
                            }else{
                                $href_print = "/print/df/".$demande_fonds_id;
                                $display_print = "none";
                            }

                          }
                        ?>
                      <tr>
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap">{{ $demande_fond->num_dem ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:left; ">{{ $demande_fond->intitule ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ strrev(wordwrap(strrev($demande_fond->solde_avant_op ?? ''), 3, ' ', true)) }}</td>
                        <td style="vertical-align: middle; text-align:right; width:1px; white-space:nowrap">{{ strrev(wordwrap(strrev($demande_fond->montant ?? ''), 3, ' ', true)) }}</td>
                        <td style="vertical-align: middle; text-align:left;">{{ $demande_fond->ref_fam ?? '' }} - {{ $demande_fond->design_fam ?? '' }}</td>
                        <td style="vertical-align: middle; text-align:left; color:red; font-weight:bold">{{ $libelle ?? '' }}</td>
                        @if(isset($colonne_beneficiaire))
                          @if($colonne_beneficiaire===true)
                            <td style="vertical-align: middle; text-align:left; width:1px; white-space:nowrap"></td>
                          @endif
                        @endif
                        <td style="vertical-align: middle; text-align:center; width:1px; white-space:nowrap">

                          <a style="display:{{ $display_show }}" title="{{ $title_show }}" href="{{ $href_show }}" class="btn btn-info btn-sm"><svg style="color:black;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                          <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                          <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                          </svg></a>

                          <a style="display:{{ $display_edit }}" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                          <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                          </svg></a>

                          <a style="display:{{ $display_transfert }}" title="{{ $title_transfert }}" href="{{ $href_transfert }}" class="btn btn-success btn-sm"><svg style="color:black" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                          <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                          </svg></a>

                          <a style="display:{{ $display_create }}" title="{{ $title_create }}" href="{{ $href_create }}" class="btn btn-secondary btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                          <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                          <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/>
                          <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/>
                          </svg></a>

                          <a target="_blank" style="display:{{ $display_print }};" title="{{ $title_print }}" href="{{ $href_print }}" class="btn btn-secondary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer-fill" viewBox="0 0 16 16">
                                <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5zm6 8H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1z"/>
                                <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                              </svg>
                        </a>

                        </td>
                      </tr>
                      <?php $i++; ?>
                    @endforeach
                    
                  </tbody>
                </table>
                
          </div>
        </div>
          </div>
      </div>
  </div>
@endsection

@section('javascripts_datatable') 
    <!-- jQuery -->
    <script src="{{ asset('../plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('../plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('../plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('../dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('../dist/js/demo.js') }}"></script>
    <!-- Page specific script -->
    <script>
    $(function () {
        $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });
    </script>
@endsection