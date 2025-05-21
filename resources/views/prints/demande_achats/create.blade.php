<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $demande_achat->num_bc ?? '' }}</title>
    <style>

        /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
             @page {
                /* margin: 0cm 0cm; */
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 3cm;
                margin-left: 0cm;
                margin-right: 0cm;
                margin-bottom: 5cm;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 3cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed;
                bottom: 0cm; 
                left: 0cm; 
                right: 0cm;
                height: 5cm;
            }

        .tr{
            text-align: center;
            vertical-align: middle;
            /*background-color:gray;*/
            font-weight : bold;
            padding: 5px;
        }

        .td-left{
            text-align: left;
            vertical-align: middle;
            padding: 5px;
        }

        .td-center{
            text-align: center;
            vertical-align: middle;
            padding: 5px;
        }

        .td-right{
            text-align: right;
            vertical-align: middle;
            padding: 5px;
        }

        .td-border-none{
            border:none;
            vertical-align: middle;
            padding: 5px;
            font-weight: bold;
        }

        .td-foot{
            border-left-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            padding: 5px;
            font-size: 10px;
        }
        
        .td-width{
            width: 10%;
            white-space: nowrap;
        }

        .td-width3{
            width: 15%;
            white-space: nowrap;
        }

        .td-width2{
            width: 10%;
            white-space: nowrap;
        }

        .page-break {
            page-break-after: always;
        }

        table.menu {
            width: auto;
            float: right;
        }

    </style>
</head>
<body>
     <!-- Define header and footer blocks before your content -->
    <header>
        <table width="100%">
            <tr>
                <td class="img">
                    <img src="{{ public_path('images/capturelogos.jpg') }}" width="200">
                </td>
            </tr>
        </table>
    </header>
    <!-- Wrap the content of your PDF inside a main tag -->
    
    <table style="margin: auto; font-size:11px;" width="100%" >
        <tr>
            <td>
                <table style="margin-top: 0px;">
                    <tr>
                        <td>
                            <strong>BON DE COMMANDE</strong>
                        </td>
                        <td>
                            <strong>N° {{ $demande_achat->num_bc ?? '' }}</strong>
                        </td>
                    </tr>
                </table>
            
                <table width="100%">
                    <tr>
                        <td style="margin-top:5px;">
                            EXERCICE</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $demande_achat->exercice ?? '' }}</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            DATE COMMANDE</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            <strong>
                                @if(isset($demande_achat->created_at))
                                    {{ date('d/m/Y',strtotime($demande_achat->created_at)) }}
                                @endif
                            </strong> 
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            COMPTE BUDGETAIRE 
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $demande_achat->ref_fam ?? '' }}</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            DATE DE RETRAIT 
                        </td>
                        <td style="margin-top:5px;">
                            <strong>
                                @if(isset($statut_demande_achat->date_retrait))
                                    {{ date('d/m/Y',strtotime($statut_demande_achat->date_retrait)) }}
                                @endif
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            STRUCTURE
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $demande_achat->code_structure ?? '' }}</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            DATE DE LIVRAISON PREVUE
                        </td>
                        <td style="margin-top:5px;">
                            <strong>
                                @if(isset($cotation_fournisseur->date_livraison_prevue))
                                    {{ date('d/m/Y',strtotime($cotation_fournisseur->date_livraison_prevue)) }}
                                @endif
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            GESTION
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $demande_achat->code_gestion ?? '' }} {{ $demande_achat->libelle_gestion ?? '' }}</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            FOURNISSEUR
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $cotation_fournisseur->denomination ?? '' }}</strong> 
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            N° CNPS
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $cotation_fournisseur->entnum ?? '' }}</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            COMPTE CONTRIBUABLE
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $cotation_fournisseur->num_contribuable ?? '' }}</strong> 
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            ADRESSE
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $cotation_fournisseur->adresse ?? '' }}</strong> 
                        </td>
                        <td style="margin-top:5px;">
                            TELEPHONE
                        </td>
                        <td style="margin-top:5px;">
                            <strong>{{ $cotation_fournisseur->contacts ?? '' }}</strong> 
                        </td>
                    </tr>
                </table>

                <table width="100%" style="margin-top: 20px;">
                    <tr>
                        <td>
                            {{ mb_strtoupper($demande_achat->intitule ?? '') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table cellspacing="0" width="100%;" border="1" style="margin: auto; font-size:11px;">
        <thead>
            <tr>
            <td class="tr td-width" style="text-align: center">REF.</td>
            <td class="tr" style="text-align: center">DÉSIGNATION ARTICLE</td>
            <td class="tr td-width">UNITE</td>
            <td class="tr td-width2">QTE</td>
            <td class="tr td-width3">P.UHT</td>
            <td class="tr td-width">REMISE</td>
            <td class="tr td-width">PRIX TOTAL</td>
            </tr>
        </thead>
        <tbody>
            <?php $i=0 ?>
            @foreach($detail_cotations as $detail_cotation)
            <?php $i++ ?>
                <?php 
                $libelle_unite = null;

                if (isset($detail_cotation->code_unite)) {
                    $unite = DB::table('unites')->where('code_unite',$detail_cotation->code_unite)->first();
                    if ($unite!=null) {
                        $libelle_unite = $unite->unite;
                    }
                }

                // affichage decimal
                    $detail_cotation->prix_unit = number_format((float)$detail_cotation->prix_unit, 2, '.', '');
                    $block_prix_unit = explode(".",$detail_cotation->prix_unit);
                    
                    $prix_unit_partie_entiere = null;
                    
                    if (isset($block_prix_unit[0])) {
                        $prix_unit_partie_entiere = $block_prix_unit[0];
                    }

                    $prix_unit_partie_decimale = null;
                    if (isset($block_prix_unit[1])) {
                        $prix_unit_partie_decimale = $block_prix_unit[1];
                    }

                    $detail_cotation->remise = number_format((float)$detail_cotation->remise, 2, '.', '');
                    $block_remise = explode(".",$detail_cotation->remise);
                    
                    $remise_partie_entiere = null;
                    
                    if (isset($block_remise[0])) {
                        $remise_partie_entiere = $block_remise[0];
                    }

                    $remise_partie_decimale = null;
                    if (isset($block_remise[1])) {
                        $remise_partie_decimale = $block_remise[1];
                    }

                    $montant_ht_partie_entiere = null;

                    $detail_cotation->montant_ht = number_format((float)$detail_cotation->montant_ht, 2, '.', '');
                    
                    $block_montant_ht = explode(".",$detail_cotation->montant_ht);

                    if (isset($block_montant_ht[0])) {
                        $montant_ht_partie_entiere = $block_montant_ht[0];
                    }

                    $montant_ht_partie_decimale = null;
                    if (isset($block_montant_ht[1])) {
                        $montant_ht_partie_decimale = $block_montant_ht[1];
                    }

                //
                ?>
                <tr>
                    <td class="td-left">{{ $detail_cotation->ref_articles ?? '' }}</td>
                    <td class="td-left">{{ $detail_cotation->design_article ?? '' }}</td>
                    <td class="td-center">{{ $libelle_unite ?? '' }}</td>
                    <td class="td-center">{{ strrev(wordwrap(strrev($detail_cotation->qte ?? ''), 3, ' ', true)) }}</td>
                    <td class="td-right">
                        @if(isset($prix_unit_partie_decimale) && $prix_unit_partie_decimale != 0)
                            {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($prix_unit_partie_decimale ?? ''), 3, ' ', true)) }}
                            @else
                            {{ strrev(wordwrap(strrev($prix_unit_partie_entiere ?? ''), 3, ' ', true)) }}
                        @endif
                        </td>
                    <td class="td-center">
                        @if(isset($remise_partie_decimale) && $remise_partie_decimale != 0)
                            {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_partie_decimale ?? ''), 3, ' ', true)) }}
                            @else
                            {{ strrev(wordwrap(strrev($remise_partie_entiere ?? ''), 3, ' ', true)) }}
                        @endif    
                    </td>
                    <td class="td-right">
                        @if(isset($montant_ht_partie_decimale) && $montant_ht_partie_decimale != 0)
                            {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_ht_partie_decimale ?? ''), 3, ' ', true)) }}
                            @else
                            {{ strrev(wordwrap(strrev($montant_ht_partie_entiere ?? ''), 3, ' ', true)) }}
                        @endif  
                    </td>
                </tr>
                <?php
                    if( ($i % 15 == 0) ){ 
                        //echo '<div class="page-break"></div>';
                    }
                ?>

                
            @endforeach
            
        </tbody>
                            
    </table>

    <?php

        $cotation_fournisseur->montant_total_brut = number_format((float)$cotation_fournisseur->montant_total_brut, 2, '.', '');
        $block_montant_total_brut = explode(".",$cotation_fournisseur->montant_total_brut);

        $montant_total_brut_partie_entiere = null;
        
        if (isset($block_montant_total_brut[0])) {
            $montant_total_brut_partie_entiere = $block_montant_total_brut[0];
        }

        $montant_total_brut_partie_decimale = null;
        if (isset($block_montant_total_brut[1])) {
            $montant_total_brut_partie_decimale = $block_montant_total_brut[1];
        }

        $cotation_fournisseur->remise_generale = number_format((float)$cotation_fournisseur->remise_generale, 2, '.', '');
        $block_remise_generale = explode(".",$cotation_fournisseur->remise_generale);

        $remise_generale_partie_entiere = null;
        
        if (isset($block_remise_generale[0])) {
            $remise_generale_partie_entiere = $block_remise_generale[0];
        }

        $remise_generale_partie_decimale = null;
        if (isset($block_remise_generale[1])) {
            $remise_generale_partie_decimale = $block_remise_generale[1];
        }

        $cotation_fournisseur->montant_total_net = number_format((float)$cotation_fournisseur->montant_total_net, 2, '.', '');
        $block_montant_total_net = explode(".",$cotation_fournisseur->montant_total_net);

        $montant_total_net_partie_entiere = null;
        
        if (isset($block_montant_total_net[0])) {
            $montant_total_net_partie_entiere = $block_montant_total_net[0];
        }

        $montant_total_net_partie_decimale = null;
        if (isset($block_montant_total_net[1])) {
            $montant_total_net_partie_decimale = $block_montant_total_net[1];
        }

        $cotation_fournisseur->montant_total_ttc = number_format((float)$cotation_fournisseur->montant_total_ttc, 2, '.', '');
        $block_montant_total_ttc = explode(".",$cotation_fournisseur->montant_total_ttc);

        $montant_total_ttc_partie_entiere = null;
        
        if (isset($block_montant_total_ttc[0])) {
            $montant_total_ttc_partie_entiere = $block_montant_total_ttc[0];
        }

        $montant_total_ttc_partie_decimale = null;
        if (isset($block_montant_total_ttc[1])) {
            $montant_total_ttc_partie_decimale = $block_montant_total_ttc[1];
        }

        $cotation_fournisseur->montant_acompte = number_format((float)$cotation_fournisseur->montant_acompte, 2, '.', '');
        $block_montant_acompte = explode(".",$cotation_fournisseur->montant_acompte);

        $montant_acompte_partie_entiere = null;
        
        if (isset($block_montant_acompte[0])) {
            $montant_acompte_partie_entiere = $block_montant_acompte[0];
        }

        $montant_acompte_partie_decimale = null;
        if (isset($block_montant_acompte[1])) {
            $montant_acompte_partie_decimale = $block_montant_acompte[1];
        }

        
    ?>

    <table width="100%" style="width: 100%; margin:auto; margin-top:2px; font-size: 11px;" cellspacing="0" border="1">
        <tr>
            <td width="50%" style="width: 50%; border:0px; border-color:transparent; border:none" >
                
            </td>
            <td width="50%" style="width: 50%;  border:0px; border-color:transparent; border:none">
                <table cellspacing="0" width="100%" style="width: 100%" style="font-size: 11px;  border:0px; border-color:transparent; border:none">
                    <tr>
                        <td style="border:0px; border-color:transparent; border:none">
                            <table cellspacing="0" width="100%" style="width: 100% ; border:0px; border-color:transparent; border:none">
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">MONTANT HT</td>
                                    <td class="td-border-none"></td>
                                    <td class="td-right td-border-none">
                                        @if(isset($montant_total_brut_partie_decimale) && $montant_total_brut_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_brut_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($montant_total_brut_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif 
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">REMISE</td>
                                    <td class="td-border-none"></td>
                                    <td class="td-right td-border-none">
                                        @if(isset($remise_generale_partie_decimale) && $remise_generale_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($remise_generale_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($remise_generale_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif 
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">MONTANT NET HT</td>
                                    <td class="td-border-none"></td>
                                    <td class="td-right td-border-none">
                                        @if(isset($montant_total_net_partie_decimale) && $montant_total_net_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_net_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($montant_total_net_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">TVA</td>
                                    <td class="td-center td-border-none">{{ $cotation_fournisseur->tva ?? '' }}%</td>
                                    <?php
                                        $montant_tva_partie_entiere = null;
                                        $montant_tva_partie_decimale = null;
                                        if (isset($cotation_fournisseur->tva)) {
            
                                            $montant_tva = ($cotation_fournisseur->montant_total_net/100)* $cotation_fournisseur->tva;
            
                                            
                                            $montant_tva = number_format((float)$montant_tva, 2, '.', '');
            
                                            $block_montant_tva = explode(".",$montant_tva);
                                            if (isset($block_montant_tva[0])) {
                                                $montant_tva_partie_entiere = $block_montant_tva[0];
                                            }
            
                                            
                                            if (isset($block_montant_tva[1])) {
                                                $montant_tva_partie_decimale = $block_montant_tva[1];
                                            }
                                        }
                                    ?>
                                    <td class="td-right td-border-none">
                                        @if(isset($montant_tva_partie_decimale) && $montant_tva_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_tva_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($montant_tva_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">MONTANT TTC</td>
                                    <td class="td-border-none"></td>
                                    <td class="td-right td-border-none">
            
                                        @if(isset($montant_total_ttc_partie_decimale) && $montant_total_ttc_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_total_ttc_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($montant_total_ttc_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif
            
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">ACOMPTE</td>
                                    <td class="td-center td-border-none">
                                        
                                        @if(isset($cotation_fournisseur->taux_acompte))
                                            {{ number_format($cotation_fournisseur->taux_acompte,2) }}%
                                        @endif

                                    </td>
                                    <td class="td-right td-border-none">
                                        @if(isset($montant_acompte_partie_decimale) && $montant_acompte_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_acompte_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($montant_acompte_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-border-none" ></td>
                                    <td class="td-border-none">RESTE A PAYER</td>
                                    <td class="td-border-none"></td>
                                    <?php
                                        $montant_reste_a_payer_partie_entiere = null;
                                        $montant_reste_a_payer_partie_decimale = null;
            
                                        if (isset($cotation_fournisseur->montant_acompte)) {
            
                                            $montant_reste_a_payer = ($cotation_fournisseur->montant_total_ttc - $cotation_fournisseur->montant_acompte);
                                            
                                        }else{
                                            $montant_reste_a_payer = $cotation_fournisseur->montant_total_ttc;
                                        }
            
                                        $montant_reste_a_payer = number_format((float)$montant_reste_a_payer, 2, '.', '');
                                        $block_montant_reste_a_payer = explode(".",$montant_reste_a_payer);
                                        if (isset($block_montant_reste_a_payer[0])) {
                                            $montant_reste_a_payer_partie_entiere = $block_montant_reste_a_payer[0];
                                        }
            
                                        
                                        if (isset($block_montant_reste_a_payer[1])) {
                                            $montant_reste_a_payer_partie_decimale = $block_montant_reste_a_payer[1];
                                        }
            
                                    ?>
                                    <td class="td-right td-border-none">
                                        @if(isset($montant_reste_a_payer_partie_decimale) && $montant_reste_a_payer_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($montant_reste_a_payer_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($montant_reste_a_payer_partie_decimale ?? ''), 3, ' ', true)) }}
                                            @else
                                            {{ strrev(wordwrap(strrev($montant_reste_a_payer_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" style="width: 100%; margin:auto; margin-top:2px; font-size: 11px;" border="1" cellspacing="0">
        <tr>
            <td style="padding:10px">
                

                <?php 
                $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
                ?>
                {{ 'MONTANT TOTAL ' }}
                @if(isset($montant_total_ttc_partie_entiere))
                    <strong>{{ strtoupper($f->format($montant_total_ttc_partie_entiere)) }}</strong>

                    {{ mb_strtoupper(str_replace('euro', 'euros', str_replace('Franc', 'Francs', $cotation_fournisseur->libelle ?? ''))) }}
                @endif
                
                @if(isset($montant_total_ttc_partie_decimale))
                    @if($montant_total_ttc_partie_decimale != 0)
                        <strong>{{ strtoupper($f->format($montant_total_ttc_partie_decimale)) }}</strong>

                        {{ mb_strtoupper('centimes') }}
                    @endif
                @endif
            </td>
        </tr> 
    </table>

    <footer>
        <table width="100%" style="width: 100%; margin:auto; margin-top:3px; font-size: 11px; font-weight:bold" border="1" cellspacing="0">
            
            <tr>
                <td>
                    <table width="100%" style="width: 100%; margin:auto; margin-top:3px; font-size: 11px; font-weight:bold" border="0" cellspacing="0">
        
                        <tr>
                            <td>
                                <table width="100%" style="margin:auto; margin-top:3px; font-size: 11px; font-weight:bold" border="0" cellspacing="0">
                            
                                <tr>
                                    <?php $i = 1; ?>
                                    @if(isset($signataires))
                                        
                                        @if(count($signataires) === 1)
                                            <?php $nombre_signataire_manquant = 2 - count($signataires); ?>
                                            @for ( $u = $nombre_signataire_manquant; $u > 0 ; $u--)
                                            <td width="50%" style="text-align:right">
                                                <table width="260px" border="0" style="margin:;">
                                                    <tr>
                                                        <td style="text-align: center">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                
                                                    <tr>
                                                        <td style="height: 80px;">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                
                                                    <tr>
                                                        <td style="text-align: center">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                                                    
                                                </table>                    
                                            </td>
                                            @endfor                
                                        @endif
                                        @foreach($signataires as $signataire)

                                            <?php 
                                                $article_genre = null;
                                                $fonctions_libelle = $signataire->description_fonction;

                                                if(isset($signataire->genres_id)){
                                                    if($signataire->genres_id != null){

                                                        $genre = DB::table('genres')->where('id',$signataire->genres_id)->first();

                                                        if($genre != null){
                                                            $article_genre = "LE ";

                                                            if($genre->libelle === "F"){
                                                                $article_genre = "LA ";
                                                            }
                                                        }
                                                    } 
                                                }
                                                
                                                if($fonctions_libelle === null){
                                                    $fonctions_libelle = $signataire->libelle_fonction;
                                                }

                                                $vocals = "a-e-i-o-u-A-E-I-O-U-";

                                                if(isset(explode($fonctions_libelle[0],$vocals)[1])){
                                                    $article_genre = "L'";
                                                }
                                                
                                                $fonctions_libelle = $article_genre . $fonctions_libelle;
                                            ?>
                                            
                                            <td width="50%" style="text-align:right;">
                                                <table width="260px" border="0" style=" @if(count($signataires) === 2 && $i === 2) float:right;  @elseif(count($signataires) === 1 && $i === 1) float:right; @if(strlen($fonctions_libelle) > 35) margin-top:-10px; @endif  @endif ">
                                                    <tr>
                                                        <td style="text-align: center">
                                                           <!-- @if($signataire->libelle_fonction === 'DGAAF') 
                                                                {{ "LE DIRECTEUR GENERAL ADJOINT CHARGE" }}
                                                                <br>
                                                                {{ "DE L'ADMINISTRATION ET DES FINANCES" }}
                                                            @elseif($signataire->libelle_fonction === 'DIRECTEUR GENERAL') 
                                                                {{ "LE DIRECTEUR GENERAL" }}
                                                            @else
                                                                {{ $signataire->libelle_fonction }}
                                                            @endif -->

                                                            <!--{{ $article_genre }} {{ $signataire->description_fonction ?? $signataire->libelle_fonction ?? '' }}-->
                                                            {{ $fonctions_libelle ?? '' }}
                                                        </td>
                                                    </tr>
                
                                                    <tr>
                                                        <td style=" @if(strlen($fonctions_libelle) <= 35) 
                                                        @if(count($signataires) > 1)
                                                        height: 93px; @else height: 80px; @endif @else height: 80px; @endif ">
                                                            &nbsp;
                                                        </td>
                                                    </tr>
                
                                                    <tr>
                                                        <td style="text-align: center">
                                                            <?php  
                                                                /*
                                                                $nom_prenoms = null;
                
                                                                if(isset($signataire->nom_prenoms)){
                                                                    
                                                                    $inpStrArray = explode(" ", $signataire->nom_prenoms);
                                                                    $revArr = array_reverse($inpStrArray);
                                                                    $nom_prenoms = implode(" ", $revArr);
                
                                                                    $nom_prenoms = $signataire->nom_prenoms;
                                                                }*/
                                                                
                                                            ?>
                
                                                            {{ $signataire->nom_signataire ?? $signataire->nom_prenoms ?? '' }}
                                                        </td>
                                                    </tr>
                                                    
                                                </table>                    
                                            </td>
                                            <?php $i++; ?>
                                        @endforeach
                
                                        
                
                                    @endif
                                </tr>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>


        <table class="td-foot" cellspacing="0" width="100%" style="margin-top: 20px; text-align:center; ">
            <tr>
                <td>
                    <hr>
                </td>
            </tr>
            <tr >
                <td>
                <strong>Immeuble « La Prévoyance » sis à la rue du Commerce, Avenue du Général DE GAULLE à Abidjan-Plateau
                    <br>
                    01 B.P. 317 Abidjan Côte d'Ivoire * Tél.: (225) 20 252 100 * Fax : (225) 20 327 994 * E-mail : info@cnps.ci
                    <br>
                    </strong>
                    Fonds d'établissement : 10 000 000 000 FCFA CC : 5000810F-DGE
                </td>
            </tr>
            
        </table>
    </footer>
</body>
</html>