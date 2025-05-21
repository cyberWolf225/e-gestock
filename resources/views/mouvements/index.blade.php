@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12"><strong>Mouvements</strong></div>
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
                    <th style="vertical-align: middle; text-align:center" scope="col">#</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Mouvement</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Date</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Quantité</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Cmup</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Montant HT</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Taxe</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Montant TTC</th>
                    <th style="vertical-align: middle; text-align:center" scope="col">Acteur</th>
                    <th style="vertical-align: middle; text-align:center" style="text-align: center" scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                    <?php $i=0; ?>
                    @foreach($mouvements as $mouvement)
                        <?php $i++; 

                            
                        ?>
                        <tr style="cursor: pointer">
                        <th scope="row">{{ $i }}</th>
                        <td style="vertical-align: middle; color:red; text-align:center">
                            @if (isset($mouvement->libelle)) 
                                @if ($mouvement->libelle === 'Entrée en stock') 
                                        <svg style="font-weight: bold; color:green; vertical-align:middle; text-align:center" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5z"/>
                                        </svg>
                                    @elseif ($mouvement->libelle === 'Sortie de stock')
                                        <svg style="font-weight: bold; color:red; vertical-align:middle; text-align:center" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1z"/>
                                        </svg>
                                    @elseif ($mouvement->libelle === 'Entrée en stock, après modification de la quantité livrée')

                                    @elseif ($mouvement->libelle === 'Sortie de stock, après modification de la quantité livrée')

                                    @elseif ($mouvement->libelle === 'Entrée en stock, après inventaire')
                                        <svg style="font-weight: bold; color:green; vertical-align:middle; text-align:center" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
                                        </svg>
                                    @elseif ($mouvement->libelle === 'Sortie de stock, après inventaire')
                                        <svg style="font-weight: bold; color:red; vertical-align:middle; text-align:center" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-down-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                    @elseif ($mouvement->libelle === 'Aucun mouvement de stock')

                                    @else

                                @endif
                            @endif    
                        </td>
                        <td style="vertical-align: middle">
                            @if($mouvement->updated_at)
                                {{ date('d/m/Y',strtotime($mouvement->updated_at)) }}
                            @endif
                        </td>
                        <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($mouvement->qte ?? ''), 3, ' ', true)) }}</td>
                        <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($mouvement->prix_unit ?? ''), 3, ' ', true))  }} </td>
                        <td style="vertical-align: middle; text-align:center">{{ strrev(wordwrap(strrev($mouvement->montant_ht ?? ''), 3, ' ', true))  }}</td>
                        <td style="vertical-align: middle; text-align:right">{{ strrev(wordwrap(strrev($mouvement->taxe ?? ''), 3, ' ', true)) }}</td>
                        <td style="vertical-align: middle; text-align:right">{{ strrev(wordwrap(strrev($mouvement->montant_ttc ?? ''), 3, ' ', true)) }}</td>
                        <td style="vertical-align: middle; text-align:right"> {{ $mouvement->nom_prenoms ?? '' }} ( <strong style="font-style: italic"> {{ 'M'.$mouvement->mle ?? '' }} </strong> )</td>
                        
                        <td title="Consulter le detail du mouvement" style="vertical-align: middle" style="text-align: center">
                            <svg style="font-weight: bold; color:blue; vertical-align:middle; text-align:center" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.134 13.134 0 0 0 1.66 2.043C4.12 11.332 5.88 12.5 8 12.5c2.12 0 3.879-1.168 5.168-2.457A13.134 13.134 0 0 0 14.828 8a13.133 13.133 0 0 0-1.66-2.043C11.879 4.668 10.119 3.5 8 3.5c-2.12 0-3.879 1.168-5.168 2.457A13.133 13.133 0 0 0 1.172 8z"/>
                            <path fill-rule="evenodd" d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </td>
                        </tr>
                    @endforeach
                  
                  
                </tbody>
              </table>
              {{ $mouvements->links() }}
        </div>
    </div>
</div>
@endsection