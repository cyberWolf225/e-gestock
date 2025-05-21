@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12"><strong>Liste des retours livrés</strong></div>
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
                   <!-- <th style="vertical-align: middle" scope="col">Intitulé</th>
                    <th style="vertical-align: middle" scope="col">Réf</th>-->
                    <th style="vertical-align: middle" scope="col">Article</th>
                    <th style="vertical-align: middle" scope="col">Qté Retournée</th>
                    <th style="vertical-align: middle" scope="col">Qté Livrée</th>
                    <th style="vertical-align: middle" scope="col">Demandeur</th>
                    <th style="vertical-align: middle" scope="col">Livreur</th>
                    <th style="vertical-align: middle; text-align:center" colspan="2" style="text-align: center" scope="col">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=0; ?>
                  @foreach($livraison_retours as $livraison_retour)
                  <?php $i++; 
                  
                  $livreur_retours = DB::select("SELECT * FROM livraison_retours v, profils p, users u, agents a  WHERE v.profils_id = p.id AND p.users_id = u.id AND u.agents_id = a.id AND v.profils_id = '".$livraison_retour->profils_id_livreur."' ");
                  foreach ($livreur_retours as $livreur_retour) {
                    $nom_prenoms_livreur = $livreur_retour->nom_prenoms;
                    $mle_livreur = $livreur_retour->mle;
                  }

                  if(isset($livraison_retour->flag_valide)){
                          if($livraison_retour->flag_valide == 1){
                            //$color = '#1d643b';
                          }else{
                            //$color = '#8b1b18';
                          }
                      }else{
                          //$color = '#f2d879';
                      }

                  
                  
                  ?>
                    <tr style="cursor: pointer; color:{{ $color ?? '' }}">
                      <th style="vertical-align: middle; text-align:center;" scope="row">
                        {{ $i }}</th>
                      <td style="vertical-align: middle">{{ $livraison_retour->num_bc }}</td>
                      <!--<td style="vertical-align: middle"> /*$livraison_retour->intitule*/ </td>
                      <td style="vertical-align: middle"> /*$livraison_retour->ref_articles*/ </td>-->
                      <td style="vertical-align: middle">{{ $livraison_retour->design_article }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ $livraison_retour->qte_retour }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ $livraison_retour->qte_livree }}</td>
                      <td style="vertical-align: middle"><strong>{{ $livraison_retour->mle }}</strong> - {{ $livraison_retour->nom_prenoms }}</td>
                      <td style="vertical-align: middle"><strong>{{ $mle_livreur }}</strong> - {{ $nom_prenoms_livreur ?? '' }}</td>
                      
                      <td style="vertical-align: middle" onclick="document.location='create/{{ $livraison_retour->valider_retours_id }}'" style="text-align: center"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-counterclockwise" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                      </svg>
                    </td>
                      
                      <td style="vertical-align: middle" onclick="if (confirm('Êtes-vous sûr de bien vouloir supprimer cette livraison ?')){document.location='destroy/{{ $livraison_retour->livraison_retours_id }}'}" style="text-align: center"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                      </svg></td>
                    </tr>
                  @endforeach
                  
                  
                </tbody>
              </table>
              {{ $livraison_retours->links() }}
        </div>
    </div>
</div>
@endsection