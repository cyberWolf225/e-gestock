<table class="table table-striped" id="myTableBcn" width="100%">
    <thead>
        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
            <th style="width:30%; text-align:left">DÉSIGNATION @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th style="width:10%; text-align:left">UNITÉ</th>
            <th style="width:10%; text-align:center">QTÉ CDE. @if($griser_demande === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th style="width:10%; text-align:center">QTÉ @if($griser_offre === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th style="width:15%; text-align:center">PRIX UNITAIRE @if($griser_offre === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th style="width:10%; text-align:center">REMISE (%)</th>
            <th style="width:15%; text-align:center">MONTANT HT @if($griser_offre === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th class="label" style="width:1%; text-align:center; white-space:nowrap">@if($griser_offre === null) ÉCHANTILLON @endif</th>
            @if($griser_selection === null)
                <th class="label" style="width:1%; text-align:center; white-space:nowrap"></th>
            @endif
            <th class="label" style="width:1%; text-align:center; white-space:nowrap"></th>
        </tr>
    </thead>
    <tbody>
        @if(isset($demande_cotation))
            @if($demande_cotation->libelle === "Commande non stockable")
            <?php $i = 1; $u = 1; $e = 1; ?>
            @if(isset($detail_demande_cotations))
            @foreach($detail_demande_cotations as $detail_demande_cotation)

                <?php
                    $echantillon_cnps = $detail_demande_cotation->echantillon;
                    $unites_libelle = null;

                    if(isset($detail_demande_cotation->code_unite)){
                        $get_unite = DB::table('unites')
                        ->where('code_unite',$detail_demande_cotation->code_unite)
                        ->first();

                        if($get_unite != null){
                            $unites_libelle = $get_unite->unite;
                        }
                    }

                    $reponse_data = $controller1->procedureSetDetailReponseCotationFormat($detail_demande_cotation->detail_demande_cotations_id,$reponse_cotation->id);

                    $detail_reponse_cotations = [];
                    if(isset($reponse_data['detail_reponse_cotations_id'])){
                        if($reponse_data['detail_reponse_cotations_id'] != null){
                            $detail_reponse_cotations = $controller4->getAllDetailReponseCotationByDetailDemandeCotationId($detail_demande_cotation->detail_demande_cotations_id,$reponse_data['detail_reponse_cotations_id']);
                        }    
                    }
                ?>
                <tr>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">

                        <input required style="background-color: transparent; border: none; display:none" onfocus="this.blur()" id="detail_reponse_cotations_id" name="detail_reponse_cotations_id[]" class="form-control detail_reponse_cotations_id" value="{{ $reponse_data['detail_reponse_cotations_id'] ?? '' }}"/>

                        <input required style="background-color: transparent; border: none; display:none" onfocus="this.blur()" id="detail_demande_cotations_id" name="detail_demande_cotations_id[]" class="form-control detail_demande_cotations_id" value="{{ $detail_demande_cotation->detail_demande_cotations_id ?? '' }}"/>

                        <input autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service" 
                        
                        @if($griser_demande === 1)
                            style="background-color:transparent; border:none" onfocus="this.blur()"
                        @endif

                        @if($griser_demande === null)
                            list="list_service"
                        @endif

                        value="{{ $detail_demande_cotation->services_libelle ?? '' }}"
                        >
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" type="text"  id="unites_libelle" name="unites_libelle[]" class="form-control unites_libelle" 
                        
                        @if($griser_demande === 1)
                            style="background-color:transparent; border:none" onfocus="this.blur()"
                        @endif

                        @if($griser_demande === null)
                            list="list_unite"
                        @endif

                        value="{{ $unites_libelle ?? '' }}"
                        >
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">

                        <input autocomplete="off" required type="text"  id="qte_cde" name="qte_cde[]" class="form-control qte_cde" 
                        
                        style="background-color:transparent; border:none; text-align:center" onfocus="this.blur()"

                        value="{{ $detail_demande_cotation->qte_accordee ?? '' }}"
                        >
                    </td>      
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" required type="text"  id="qte" name="qte[]" class="form-control qte" 
                        
                        @if($griser_offre === 1)
                            style="background-color:transparent; border:none; text-align:center" onfocus="this.blur()"
                        @endif

                        @if($griser_offre === null)
                            style="text-align:center; border:" onkeyup="editMontant(this)" onkeypress="validate(event)"
                        @endif

                        value="{{ $reponse_data['qte'] ?? '' }}">
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" required type="text"  id="prix_unit" name="prix_unit[]" class="form-control prix_unit" 
                        
                        @if($griser_offre === 1)
                            style="background-color:transparent; border:none; text-align:right" onfocus="this.blur()"
                        @endif

                        @if($griser_offre === null)
                            style="text-align:right; border:" onkeyup="editMontant(this)" oninput="validateNumber(this);"
                        @endif 

                        @if(isset($reponse_data['prix_unit_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($reponse_data['prix_unit_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($reponse_data['prix_unit_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                        @else

                        value="{{ strrev(wordwrap(strrev($reponse_data['prix_unit_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                        @endif


                        >
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" type="text"  id="remise" name="remise[]" class="form-control remise" 
                        
                        @if($griser_offre === 1)
                            style="background-color:transparent; border:none; text-align:center" onfocus="this.blur()"
                        @endif

                        @if($griser_offre === null)
                            style="text-align:center; border:" onkeyup="editMontant(this)" oninput="validateNumber(this);"
                        @endif 
                        
                        @if(isset($reponse_data['remise_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($reponse_data['remise_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($reponse_data['remise_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                        @else

                        value="{{ strrev(wordwrap(strrev($reponse_data['remise_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                        @endif

                        >
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" required type="text"  id="montant_ht" name="montant_ht[]" class="form-control montant_ht" 
                        
                        onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: transparent; text-align:right; border-color:transparent"
                        
                        @if(isset($reponse_data['montant_ht_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($reponse_data['montant_ht_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($reponse_data['montant_ht_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                        @else

                        value="{{ strrev(wordwrap(strrev($reponse_data['montant_ht_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                        @endif

                        >

                        <input autocomplete="off" required onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; text-align:right; display:none" type="text" name="montant_ht_bis[]" class="form-control montant_ht_bis"
                        
                        @if(isset($reponse_data['montant_ht_partie_decimale']))

                        value="{{ $reponse_data['montant_ht_partie_entiere'] .'.'.$reponse_data['montant_ht_partie_decimale'] }}"

                        @else

                        value="{{ $reponse_data['montant_ht_partie_entiere'] ?? '' }}"

                        @endif

                        >
                    </td>               
                    <td style="vertical-align: middle; text-align:left; padding: 0; margin: 0; white-space:nowrap">
                        @if($griser_offre === null)
                            <input style="height: 30px;" accept="image/*" type="file" name="echantillon[]">

                            <input style="@if(isset($reponse_data['echantillon'])) display: ; @else display:none; @endif " type="checkbox" name="echantillon_flag[]" checked value="{{ $reponse_data['detail_reponse_cotations_id'] ?? '' }}" >
                        @endif

                        @if(isset($reponse_data['echantillon']))

                            <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $u.$i }}">
                                    <svg style="cursor: pointer; color:orange; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                            </a>
                            <div class="modal fade" id="exampleModalCenter{{ $u.$i }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="text-align: center">
                                    <h5 class="modal-title" id="exampleModalLongTitle">{{ $detail_demande_cotation->services_libelle ?? 'Échantillon CNPS' }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <img src='{{ asset('storage/'.$reponse_data['echantillon']) }}' style='width:100%;'>
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <!-- Modal -->
                            
                        @endif
                        
                        @if(isset($echantillon_cnps))

                            <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $i }}">
                                    <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                    </svg>
                            </a>
                            <div class="modal fade" id="exampleModalCenter{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="text-align: center">
                                    <h5 class="modal-title" id="exampleModalLongTitle">{{ $detail_demande_cotation->services_libelle ?? 'Échantillon CNPS' }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    </div>
                                    <div class="modal-body">
                                        <img src='{{ asset('storage/'.$echantillon_cnps) }}' style='width:100%;'>
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
                    @if($griser_selection === null)
                        <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                            <a style="cursor: pointer" onclick="removeRowBcn(this)"  class="remove" title="Retirer cet article">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="cursor: pointer; color: red; font-weight:bold; font-size:15px; " fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                            </a>                        
                        </td>
                    @endif
                    <td style="vertical-align: middle; text-align:left; padding: 0; margin: 0; white-space:nowrap">
                        @if(count($detail_reponse_cotations) > 0)
                            @include('mieux_disants.partial_compare_mieux_disant_bcn')
                        @endif
                    </td>
                </tr>
                <?php $i++; $u++; ?>
            @endforeach
            @endif
            @endif
        @endif
    </tbody>
    
</table>
