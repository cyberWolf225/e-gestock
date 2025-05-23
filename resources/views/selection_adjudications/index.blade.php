@extends('layouts.app')

@section('content')
<div class="container" style="color:black">
    <div class="row">
        <div class="col-md-12"><strong>Liste des Adjudications sélectionnées</strong></div>
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
                    <th style="vertical-align: middle" scope="col">Organisation</th>
                    <th style="vertical-align: middle" scope="col">Net à payer</th>
                    <th style="vertical-align: middle; text-align: center" colspan="2" scope="col">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=0; ?>
                  @foreach($selection_adjudications as $selection_adjudication)
                  <?php $i++;

                    $detail_adjudications = DB::select("SELECT * FROM detail_adjudications da, cotation_fournisseurs cf, critere_adjudications ca, criteres c WHERE  cf.id = da.cotation_fournisseurs_id AND c.id = ca.criteres_id AND da.cotation_fournisseurs_id = '".$selection_adjudication->id."' AND cf.demande_achats_id = '".$selection_adjudication->demande_achats_id."' ");

                    

                  
                  ?>
                    <tr style="cursor: pointer">
                      <th style="vertical-align: middle" scope="row">{{ $i }}</th>
                      <td style="vertical-align: middle" scope="row">{{ $selection_adjudication->denomination }}</td>
                      <td style="vertical-align: middle">{{ $selection_adjudication->net_a_payer }}</td>
                      
                      

                      <td title="Générer le bon de commande" style="vertical-align: middle; text-align:center" onclick="document.location='/../adjudication_commandes/create/{{ $selection_adjudication->selection_adjudications_id }}'" style="text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                      </svg></td>
                      
                      <td title="Supprimer cette cotation" style="vertical-align: middle; text-align: center" onclick="if (confirm('Êtes-vous sûr de bien vouloir supprimer cet élément?')){document.location='destroy/{{ $selection_adjudication->id }}'}"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                      </svg></td>
                    </tr>
                  @endforeach
                  
                  
                </tbody>
              </table>
              {{ $selection_adjudications->links() }}
        </div>
    </div>
</div>
@endsection