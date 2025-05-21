<?php
    $responseProcedureSet = $controller2->procedureSetReponseCotationFormat($mieux_disant);
?>
<table>
    <tr>
        <td colspan="8" style="border: none">
            <div class="row d-flex pl-3">
                                   
                <div class="pr-0" style="text-align:center"><label class="label" class=" mt-1 mr-1">Montant total brut</label><br>  
                    
                    <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_brut" class="form-control montant_total_brut"
                    
                    @if(isset($responseProcedureSet['montant_total_brut_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_total_brut_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['montant_total_brut_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_total_brut_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif
                    >
            
                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class="font-weight-bold  mt-1 mr-1">Taux remise (%)</label><br>

                    <input oninput ="validateNumber(this);" onkeyup="editTauxRemiseGenerale(this)" autocomplete="off" type="text" name="taux_remise_generale" class="form-control taux_remise_generale"
                    
                    @if(isset($responseProcedureSet['taux_remise_generale_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['taux_remise_generale_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['taux_remise_generale_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['taux_remise_generale_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    @if($griser_offre === 1)
                        style="background-color:#e9ecef; border:none; width:90px; text-align:center;" onfocus="this.blur()"
                    @endif

                    @if($griser_offre === null)
                        style="width:90px; text-align:center;"
                    @endif
                    
                    >

                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class="  mt-1 mr-1">Remise</label><br>  

                    <input onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off" type="text" name="remise_generale" class="form-control remise_generale"
                    
                    @if(isset($responseProcedureSet['remise_generale_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['remise_generale_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['remise_generale_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['remise_generale_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    @if($griser_offre === 1)
                        style="background-color:#e9ecef; border:none; width:110px; text-align:right;" onfocus="this.blur()"
                    @endif

                    @if($griser_offre === null)
                        style="width:110px; text-align:right;"
                    @endif

                    >

                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total net ht</label><br>  
                    
                    <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right;" type="text" name="montant_total_net" class="form-control montant_total_net"
                    
                    @if(isset($responseProcedureSet['montant_total_net_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_total_net_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['montant_total_net_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_total_net_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    >

                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">TVA (%)</label><br>  
                    
                    <input onkeyup="editRemiseGenerale(this)" oninput="validateNumber(this);" autocomplete="off" type="text" name="tva" class="form-control tva"
                    value="{{ $mieux_disant->tva ?? '' }}"

                    @if($griser_offre === 1)
                        style="background-color:#e9ecef; border:none; width:80px; text-align:center" onfocus="this.blur()"
                    @endif

                    @if($griser_offre === null)
                        style="width:80px; text-align:center" list="list_taxe_tva"
                    @endif

                    >

                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>
                    
                    <input autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_tva" class="form-control montant_tva"
                    
                    @if(isset($responseProcedureSet['montant_tva_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_tva_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['montant_tva_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_tva_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    >
                
                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Montant total ttc</label><br>
                    
                    <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="montant_total_ttc" class="form-control montant_total_ttc"
                    
                    @if(isset($responseProcedureSet['montant_total_ttc_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_total_ttc_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['montant_total_ttc_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_total_ttc_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    >
                
                </div>
            </div>

            <div class="row d-flex pl-3">

                <div class="pr-0"><label class="label" class=" mt-1 mr-2">Assiette BNC</label><br>
                    
                    <input autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="assiette" class="form-control assiette">
                
                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">Taux BNC</label><br>
                    
                    <input autocomplete="off" onfocus="this.blur()" style="background-color: #e9ecef; width:60px;" oninput="validateNumber(this);" type="text" name="taux_bnc" class="form-control">
                
                </div>

                <div class="pl-1" style="text-align:center"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>
                    
                    <input autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px;" type="text" name="montant_bnc" class="form-control montant_bnc">
                
                </div>

                <div class="pl-1" style="text-align:center" style="margin-left:0px;"><label class="label" class=" mt-1 mr-2">Net à payer</label><br>
                    
                    <input required autocomplete="off" onfocus="this.blur()" oninput="validateNumber(this);" style="background-color: #e9ecef; width:110px; text-align:right" type="text" name="net_a_payer" class="form-control net_a_payer"
                    
                    @if(isset($responseProcedureSet['net_a_payer_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['net_a_payer_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['net_a_payer_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['net_a_payer_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    >
                
                </div>
                <?php $display_acompte_taux_div = null; ?>
                <div style="padding-left:3px; text-align:center" ><label class="label" class=" mt-1 mr-2">Acompte</label><br>
                    
                    <input style="vertical-align:middle; display:none" type="checkbox" name="acompte" class="acompte" onchange="doalert(this)"
                    @if(isset($demande_cotation->taux_acompte) && isset($mieux_disant->acompte))

                    @if($demande_cotation->taux_acompte != null && $mieux_disant->acompte === 1)
                        checked

                        <?php $display_acompte_taux_div = 1; ?>

                    @endif

                    @endif

                    @if($griser_offre === 1)
                        
                    @endif
                    >

                    <input style="vertical-align:middle;" type="checkbox"
                    @if(isset($demande_cotation->taux_acompte) && isset($mieux_disant->acompte))

                    @if($demande_cotation->taux_acompte != null && $mieux_disant->acompte === 1)
                        checked

                        <?php $display_acompte_taux_div = 1; ?>

                    @endif

                    @endif

                    @if($griser_offre === 1)
                        disabled
                    @endif
                    >
                
                </div>

                <div style="padding-left: 10px; text-align: center; @if($display_acompte_taux_div === null) display:none @endif " id="acompte_taux_div" ><label class="label" class=" mt-1 mr-2">(%)</label><br>
                    
                    <input onfocus="this.blur()" maxlength="3" onkeyup="editTauxAcompte(this)" autocomplete="off" oninput="validateNumber(this);" style=" width:70px; text-align:center; background-color: #e9ecef;" type="text" name="taux_acompte" class="form-control taux_acompte" value="{{ old('taux_acompte') ?? $demande_cotation->taux_acompte ?? '' }}">
                
                </div>

                <div style="padding-left: 10px; text-align: center; display:none" id="i_acompte_taux_div" ><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>
                    
                    <input onfocus="this.blur()" style="width:70px; border:none; border-color:transparent; color:transparent" class="form-control">
                
                </div>

                <div class="pl-1" style="text-align:center; @if($display_acompte_taux_div === null) display:none @endif " id="acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>
                    
                    <input onfocus="this.blur()" autocomplete="off" onkeyup="editAcompte(this)" oninput="validateNumber(this);" style=" width:110px; text-align:right; background-color: #e9ecef;" type="text" name="montant_acompte" class="form-control montant_acompte"
                    
                    @if(isset($responseProcedureSet['montant_acompte_partie_decimale']))

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_acompte_partie_entiere'] ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($responseProcedureSet['montant_acompte_partie_decimale'] ?? ''), 3, ' ', true)) }}"

                    @else

                        value="{{ strrev(wordwrap(strrev($responseProcedureSet['montant_acompte_partie_entiere'] ?? ''), 3, ' ', true)) }}"

                    @endif

                    >
                
                </div>

                <div class="pl-1" style="text-align:center; display:none" id="i_acompte_div"><label class="label" class=" mt-1 mr-2">&nbsp;</label><br>
                    
                    <input onfocus="this.blur()" style=" width:110px; border:none; border-color:transparent; color:transparent" class="form-control">
                
                </div>

                <div style="padding-left: 10px;" >
                    
                    <label class="label" class=" mt-1 mr-2">&nbsp;</label><br>
                
                </div>
            
            </div>
        </td>

    </tr>
</table>