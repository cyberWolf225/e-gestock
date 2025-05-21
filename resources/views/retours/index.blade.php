@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12"><strong>Liste des réquisitions retournées</strong></div>
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
                    <th style="vertical-align: middle" scope="col">Article</th>
                    <th style="vertical-align: middle" scope="col">Qté Livrée</th>
                    <th style="vertical-align: middle" scope="col">Qté Retournée</th>
                    <th style="vertical-align: middle" scope="col">Demandeur</th>
                    <th style="vertical-align: middle" scope="col">Livreur</th>
                    <th style="vertical-align: middle; text-align:center" colspan="3" style="text-align: center" scope="col">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=0; ?>
                  @foreach($livraisons as $livraison)
                  <?php $i++; 

                  $nom_prenoms_livreur = "";
                  $mle_livreur = "";
                  

                if ($livraison->profils_id_livreur!=null) {
                  $livreur_requisitions = DB::select("SELECT * FROM livraisons v, profils p, users u, agents a  WHERE v.profils_id = p.id AND p.users_id = u.id AND u.agents_id = a.id AND v.profils_id = '".$livraison->profils_id_livreur."' AND v.id = '".$livraison->livraisons_id."' ");
                  foreach ($livreur_requisitions as $livreur_requisition) {
                    $nom_prenoms_livreur = $livreur_requisition->nom_prenoms;
                    $mle_livreur = $livreur_requisition->mle;
                  }
                }

                $flag_valide = null;
                $valider_retours = DB::select("SELECT * FROM valider_retours WHERE retours_id = '".$livraison->retours_id."' ");
                  foreach ($valider_retours as $valider_retour) {
                    $flag_valide = $valider_retour->flag_valide;
                  }

                  
                  ?>
                    <tr style="cursor: pointer;">
                      <th style="vertical-align: middle; text-align:center;" scope="row"> 
                        {{ $i }}</th>
                      <td style="vertical-align: middle">{{ $livraison->num_bc }}</td>
                      <td style="vertical-align: middle">Réf : <strong>{{ $livraison->ref_articles }}</strong> : {{ $livraison->design_article }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($livraison->qte ?? 0), 3, ' ', true)) }}</td>
                      <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($livraison->qte_retour ?? 0), 3, ' ', true)) }}</td>
                      <td style="vertical-align: middle"><strong>{{ $livraison->mle }}</strong> - {{ $livraison->nom_prenoms }}</td>
                      <td style="vertical-align: middle"><strong>{{ $mle_livreur }}</strong> - {{ $nom_prenoms_livreur ?? '' }}</td>
                      <td title="Valider de cette réquisition retournée" style="vertical-align: middle" onclick="document.location='../valider_retours/create/{{ $livraison->retours_id }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-check" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M5.443 1.991a60.17 60.17 0 0 0-2.725.802.454.454 0 0 0-.315.366C1.87 7.056 3.1 9.9 4.567 11.773c.736.94 1.533 1.636 2.197 2.093.333.228.626.394.857.5.116.053.21.089.282.11A.73.73 0 0 0 8 14.5c.007-.001.038-.005.097-.023.072-.022.166-.058.282-.111.23-.106.525-.272.857-.5a10.197 10.197 0 0 0 2.197-2.093C12.9 9.9 14.13 7.056 13.597 3.159a.454.454 0 0 0-.315-.366c-.626-.2-1.682-.526-2.725-.802C9.491 1.71 8.51 1.5 8 1.5c-.51 0-1.49.21-2.557.491zm-.256-.966C6.23.749 7.337.5 8 .5c.662 0 1.77.249 2.813.525a61.09 61.09 0 0 1 2.772.815c.528.168.926.623 1.003 1.184.573 4.197-.756 7.307-2.367 9.365a11.191 11.191 0 0 1-2.418 2.3 6.942 6.942 0 0 1-1.007.586c-.27.124-.558.225-.796.225s-.526-.101-.796-.225a6.908 6.908 0 0 1-1.007-.586 11.192 11.192 0 0 1-2.417-2.3C2.167 10.331.839 7.221 1.412 3.024A1.454 1.454 0 0 1 2.415 1.84a61.11 61.11 0 0 1 2.772-.815z"/>
                        <path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                      </svg></td>
                      <?php if($flag_valide == 1){ ?>
                      <td title="Livrer le retour de cette réquisition" style="vertical-align: middle" onclick="document.location='../livraison_retours/create/{{ $livraison->retours_id }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l.5 2H5V5H3.14zM6 5v2h2V5H6zm3 0v2h2V5H9zm3 0v2h1.36l.5-2H12zm1.11 3H12v2h.61l.5-2zM11 8H9v2h2V8zM8 8H6v2h2V8zM5 8H3.89l.5 2H5V8zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                      </svg></td>
                      <?php } ?>
                                            
                      <td title="Supprimer la validation de cette réquisition retournée" style="vertical-align: middle" onclick="if (confirm('Êtes-vous sûr de bien vouloir supprimer ce retour d\'article ?')){document.location='destroy/{{ $livraison->retours_id }}'}" style="text-align: center"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                      </svg></td>
                    </tr>
                  @endforeach
                  
                  
                </tbody>
              </table>
              {{ $livraisons->links() }}
        </div>
    </div>
</div>
@endsection