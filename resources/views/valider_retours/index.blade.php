@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12"><strong>Liste des retours validés</strong></div>
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
                    <th style="vertical-align: middle" scope="col">N° Demande</th>
                    <th style="vertical-align: middle" scope="col">Article</th>
                    <th style="vertical-align: middle" scope="col">Qté retournée</th>
                    <th style="vertical-align: middle" scope="col">Qté Valideée</th>
                    <th style="vertical-align: middle" scope="col">Demandeur</th>
                    <th style="vertical-align: middle" scope="col">Validateur</th>
                    <th style="vertical-align: middle; text-align:center" colspan="4" style="text-align: center" scope="col">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=0; ?>
                  @foreach($valider_retours as $valider_retour)
                  <?php $i++; 
                  
                  $validateur_requisitions = DB::select("SELECT * FROM valider_retours v, profils p, users u, agents a  WHERE v.profils_id = p.id AND p.users_id = u.id AND u.agents_id = a.id AND v.profils_id = '".$valider_retour->profils_id_validateur_retour."' ");
                  foreach ($validateur_requisitions as $validateur_requisition) {
                    $nom_prenoms_validateur = $validateur_requisition->nom_prenoms;
                    $mle_validateur = $validateur_requisition->mle;
                    $flag_valide = $validateur_requisition->flag_valide;
                  }
                  ?>
                    <tr style="cursor: pointer">
                      <th style="vertical-align: middle; text-align:center;" scope="row">
                        {{ $i }}</th>
                      <td style="vertical-align: middle">{{ $valider_retour->num_bc }}</td>
                      <td style="vertical-align: middle">Réf : <strong>{{ $valider_retour->ref_articles }}</strong> : {{ $valider_retour->design_article }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($valider_retour->qte_retour ?? 0), 3, ' ', true)) }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($valider_retour->qte_validee ?? 0), 3, ' ', true)) }}</td>
                      <td style="vertical-align: middle"><strong>{{ $valider_retour->mle }}</strong> - {{ $valider_retour->nom_prenoms }}</td>
                      <td style="vertical-align: middle"><strong>{{ $mle_validateur ?? '' }}</strong> - {{ $nom_prenoms_validateur ?? '' }}</td> 
                      <td style="vertical-align: middle" onclick="document.location='../livraison_retours/create/{{ $valider_retour->valider_retours_id }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                      </svg></td>
                      <td style="vertical-align: middle" onclick="document.location='create/{{ $valider_retour->retours_id }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                        <path d="M9.079 11.9l4.568-3.281a.719.719 0 0 0 0-1.238L9.079 4.1A.716.716 0 0 0 8 4.719V6c-1.5 0-6 0-7 8 2.5-4.5 7-4 7-4v1.281c0 .56.606.898 1.079.62z"/>
                      </svg></td>
                      
                      <td style="vertical-align: middle" onclick="if (confirm('Êtes-vous sûr de bien vouloir supprimer cet élément ?')){document.location='destroy/{{ $valider_retour->valider_retours_id }}'}" style="text-align: center"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                      </svg></td>
                    </tr>
                  @endforeach
                  
                  
                </tbody>
              </table>
              {{ $valider_retours->links() }}
        </div>
    </div>
</div>
@endsection