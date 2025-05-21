<a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $u.$i.$e }}">
    <svg style="cursor: pointer; color:orange; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
    </svg>
</a>
<div class="modal fade" id="exampleModalCenter{{ $u.$i.$e }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
    <div class="modal-header" style="text-align:">
    <h5 class="modal-title" id="exampleModalLongTitle">
        <table class="table bg-white" style="font-size:10px; width:100% ">
            <tr>
                <td>RÉFÉRENCE</td>
                <td>DÉSIGNATION</td>
                <td>QUANTITÉ</td>
                
            </tr>
            <tr>
                <td><span style="font-weight:bold; color:darkcyan; font-size:12px">{{ $detail_demande_cotation->ref_articles ?? '' }}</span></td>
                <td><span style="font-weight:bold; color:darkcyan; font-size:12px">{{ $detail_demande_cotation->design_article ?? '' }}</span></td>
                <td><span style="font-weight:bold; color:darkcyan; font-size:12px">{{ strrev(wordwrap(strrev($detail_demande_cotation->qte_accordee ?? ''), 3, ' ', true)) }}</span></td>
            </tr>
        </table>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
        <table>
            <thead>
                <tr>
                    <th class="label" style="width:10%; text-align:left; vertical-align:middle">N° CNPS</th>
                    <th class="label" style="width:30%; text-align:left; vertical-align:middle">RAISON SOCIALE</span></th>
                    <th class="label" style="width:10%; text-align:center; vertical-align:middle">QTÉ</th>
                    <th class="label" style="width:15%; text-align:center; vertical-align:middle">PRIX <br/>UNITAIRE</th>
                    <th class="label" style="width:10%; text-align:center; vertical-align:middle">REMISE (%)</th>
                    <th class="label" style="width:15%; text-align:center; vertical-align:middle">MONTANT HT</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($detail_reponse_cotations))
                @foreach($detail_reponse_cotations as $detail_reponse_cotation)
                <?php 
                $response_detail_reponse_cotation = $controller2->procedureSetDetailReponseCotationFormat2($detail_reponse_cotation);
                ?>
                <tr>
                    <td>{{ $detail_reponse_cotation->entnum ?? '' }}</td>
                    <td title="{{ $detail_reponse_cotation->denomination ?? '' }}">
                        {{ substr($detail_reponse_cotation->denomination ?? '', 0, 15).'.' }}
                    </td>
                    <td style="text-align:right">
                        {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['qte'] ?? ''), 3, ' ', true)) }}
                    </td>
                    <td style="text-align:right">
                        @if(isset($response_detail_reponse_cotation['prix_unit_partie_decimale']))

                            {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['prix_unit_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($response_detail_reponse_cotation['prix_unit_partie_decimale'] ?? ''), 3, ' ', true)) }}

                        @else

                            {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['prix_unit_partie_entiere'] ?? ''), 3, ' ', true)) }}

                        @endif
                    </td>
                    <td style="text-align:right">
                        @if(isset($response_detail_reponse_cotation['remise_partie_decimale']))

                            {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['remise_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($response_detail_reponse_cotation['remise_partie_decimale'] ?? ''), 3, ' ', true)) }}

                        @else

                            {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['remise_partie_entiere'] ?? ''), 3, ' ', true)) }}

                        @endif
                    </td>
                    <td style="text-align:right">
                        @if(isset($response_detail_reponse_cotation['montant_ht_partie_decimale']))

                            {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['montant_ht_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($response_detail_reponse_cotation['montant_ht_partie_decimale'] ?? ''), 3, ' ', true)) }}

                        @else

                            {{ strrev(wordwrap(strrev($response_detail_reponse_cotation['montant_ht_partie_entiere'] ?? ''), 3, ' ', true)) }}

                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>
</div>
</div>
<!-- Modal -->