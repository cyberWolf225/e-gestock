@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12"><strong>Liste des réquisitions livrées</strong></div>
    </div>

    <div class="row mt-5"> 
        <div class="col-12">
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
            <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th style="vertical-align: middle" scope="col">#</th>
                    <th style="vertical-align: middle" scope="col">N° Bon de Commande</th>
                    <th style="vertical-align: middle" scope="col">Intitulé</th>
                    <th style="vertical-align: middle" scope="col">Article</th>
                    <th style="vertical-align: middle" scope="col">Qté Demandée</th>
                    <th style="vertical-align: middle" scope="col">Qté Valideée</th>
                    <th style="vertical-align: middle" scope="col">Demandeur</th>
                    <th style="vertical-align: middle" scope="col">Validateur</th>
                    <th style="vertical-align: middle; text-align:center" colspan="4" style="text-align: center" scope="col">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=0; ?>
                  @foreach($valider_requisitions as $valider_requisition)
                  <?php $i++; 
                  
                  $validateur_requisitions = DB::select("SELECT * FROM valider_requisitions v, profils p, users u, agents a  WHERE v.profils_id = p.id AND p.users_id = u.id AND u.agents_id = a.id AND v.profils_id = '".$valider_requisition->profils_id."' ");
                  foreach ($validateur_requisitions as $validateur_requisition) {
                    $nom_prenoms_validateur = $validateur_requisition->nom_prenoms;
                    $mle_validateur = $validateur_requisition->mle;
                  }
                  $charette = null;
                  if(isset($valider_requisition->flag_valide)){
                          if($valider_requisition->flag_valide == 1){
                            $charette = 1;
                          }
                      }

                  
                  
                  ?>
                    <tr style="cursor: pointer">
                      <th style="vertical-align: middle; text-align:center;" scope="row">
                        {{ $i }}</th>
                      <td style="vertical-align: middle">{{ $valider_requisition->num_bc }}</td>
                      <td style="vertical-align: middle">{{ $valider_requisition->intitule }}</td>
                      <td style="vertical-align: middle">Réf : <strong>{{ $valider_requisition->ref_articles }}</strong> : {{ $valider_requisition->design_article }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ $valider_requisition->qte_demandee }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ $valider_requisition->qte_validee }}</td>
                      <td style="vertical-align: middle"><strong>{{ $valider_requisition->mle }}</strong> - {{ $valider_requisition->nom_prenoms }}</td>
                      <td style="vertical-align: middle"><strong>{{ $mle_validateur }}</strong> - {{ $nom_prenoms_validateur ?? '' }}</td>
                      <td title="Modifier cette livraison" style="vertical-align: middle" onclick="document.location='create/{{ $valider_requisition->num_dem }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                        <path d="M9.079 11.9l4.568-3.281a.719.719 0 0 0 0-1.238L9.079 4.1A.716.716 0 0 0 8 4.719V6c-1.5 0-6 0-7 8 2.5-4.5 7-4 7-4v1.281c0 .56.606.898 1.079.62z"/>
                      </svg></td>
                      <td title="Retourner cette réquisition" style="vertical-align: middle" onclick="document.location='../retours/create/{{ $valider_requisition->livraisons_id }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                      </svg>
                    </td>
                      
                      <td title="Supprimer cette livraison" style="vertical-align: middle" onclick="if (confirm('Êtes-vous sûr de bien vouloir supprimer le traitement de cette requisition ?')){document.location='destroy/{{ $valider_requisition->valider_requisitions_id }}'}" style="text-align: center"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                      </svg></td>
                    </tr>
                  @endforeach
                  
                  
                </tbody>
              </table>
              {{ $valider_requisitions->links() }}
        </div>
    </div>
</div>
@endsection