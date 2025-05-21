<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $demande_fond->num_dem ?? '' }}</title>
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
            background-color:gray;
            height: 30px;
        }

        .td-left{
            text-align: left;
            vertical-align: middle;
            height: 30px;
        }

        .td-center{
            text-align: center;
            vertical-align: middle;
            height: 30px;
        }

        .td-right{
            text-align: right;
            vertical-align: middle;
            height: 30px;
        }

        .td-border-none{
            border:none;
            vertical-align: middle;
            height: 30px;
            font-weight: bold;
        }

        .td-foot{
            border-left-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            height: 30px;
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

    </style>
</head>
<body>

     <!-- Define header and footer blocks before your content -->
     <header>
        <table border="1" cellspacing="0" width="100%">


            <tr>
                <td width="25%" style="vertical-align: middle; border-color: black; text-align: center; width: 25%; vertical-align: middle;" rowspan="2">
                    <img src="{{ public_path('images/logo2.png') }}" width="180">
                </td>
                <td width="50%" style="vertical-align: middle; border-color: black; text-align: center; width: 50%; vertical-align: middle; background-color: #82a0ba; font-size:14px;" colspan="2"><strong>ENREGISTREMENT</strong></td>
                <td width="25%" style="vertical-align: middle; border-color: black; text-align: center; width: 25%; vertical-align: middle; color: blue; font-size:12px;" rowspan="2"><strong>Réf : EN-AEE-11<br/>Version : 02<br/>Page 1/1</strong></td>
                </tr>
                <tr>
                <td width="50%" style="vertical-align: middle; border-color: black; text-align: center; width: 50%" colspan="2"><strong>DEMANDE DE FONDS</strong></td>

            </tr>


        </table> 
    </header>

    <!-- Wrap the content of your PDF inside a main tag -->


    <table width="100%">
        <tr>
            <td style="font-weight: bold">Type de la demande : </td>
            <td>Demande d'espèces</td>
            <td>
                <table cellspacing="0" border="1" width="20px" style="border-color: black; <?php if(isset($demande_fond->moyen_paiements_libelle)){if ($demande_fond->moyen_paiements_libelle === 'Espèce') { ?> background-color:black; <?php }} ?> "><tr><td>&nbsp;</td></tr></table> 
            </td>
            <td>&nbsp;&nbsp;Demande de chèque </td>
            <td>
                <table cellspacing="0" border="1" width="20px" style="border-color: black; <?php if(isset($demande_fond->moyen_paiements_libelle)){if ($demande_fond->moyen_paiements_libelle === 'Chèque') { ?> background-color:black; <?php }} ?> "><tr><td>&nbsp;</td></tr></table> 
            </td>
            
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="text-align: center">N° {{ $demande_fond->num_dem ?? '' }} du </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="font-weight: bold; width:20%">Service émetteur : </td>
            <td>{{ $demande_fond->nom_structure ?? '' }}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="font-weight: bold; width:25%;">Structure bénéficiaire : </td>
            <td>{{ $demande_fond->nom_structure ?? '' }} / {{ $demande_fond->code_structure ?? '' }} <span style="font-style: italic ; font-size:12px">(code structure bénéficiaire)</span></td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="font-weight: bold; width:30%;"><?php if(isset($fournisseur)){?>Nom du fournisseur : <?php }else{ ?>Nom et Prénoms du bénéficiaire : <?php } ?></td>
            <td>{{ $nom_prenoms ?? '' }}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="font-weight: bold; width:30%;"><?php if(isset($fournisseur)){?>N° CNPS du fournisseur : <?php }else{ ?>Matricule du bénéficiaire : <?php } ?></td>
            <td>{{ $mle ?? '' }}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="width:40%;"><span style="font-weight: bold;">Chargé du suivi</span> <span style="font-style: italic ; font-size:12px">(en cas de demande de chèque)</span> : </td>
            <td>{{ $charge_suivi->nom_prenoms ?? '' }}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="width:40%;"><span style="font-weight: bold;">Matricule du chargé du suivi</span> : </td>
            <td>{{ $charge_suivi->mle ?? '' }}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="width:40%; font-weight: bold;">N° du compte budgétaire à imputer : </td>
            <td>{{ $demande_fond->ref_fam ?? '' }} / {{ $demande_fond->code_structure.'01' ?? '' }} <span style="font-style: italic ; font-size:12px">(code structure supportant la dépense)</span></td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="width:40%; font-weight: bold;">Gestion : </td>
            <td>{{ $demande_fond->code_gestion ?? '' }}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td style="width:40%; font-weight: bold;">Solde du compte avant opération : </td>
            <td> {{ strrev(wordwrap(strrev($demande_fond->solde_avant_op ?? ''), 3, ' ', true)) }} F CFA</td>
        </tr>
    </table>

    <?php 
        $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
        $montant_en_lettre = $f->format($demande_fond->montant);
        $intitule = null;
        if (isset($demande_fond->intitule)) {

            $intitule = mb_strtoupper($demande_fond->intitule);
        }
        

    ?>
    <br>
    <br>
    <table border="1" cellspacing="0" width="100%">
        <tr style="font-weight: bold">
            <td width="60%" style="text-align: center; padding:10px">OBJET DE LA DEMANDE</td>
            <td width="20%" style="text-align: center; padding:10px">MONTANT</td>
            <td width="20%" style="text-align: center; padding:10px">OBSERVATIONS</td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold">{{ strtoupper($intitule ?? '') }}</td>
            <td style="text-align: center; padding: 10px; font-weight: bold">{{ strrev(wordwrap(strrev($demande_fond->montant ?? ''), 3, ' ', true)) }} F CFA</td>
            <td  style="padding: 10px">{{ $demande_fond->observation ?? '' }}</td>
        </tr>
        <tr style="font-size: 17px;">
            <td colspan="3" style="padding:10px">Arrêté la présente demande de fonds à la somme de (en lettres) : 
            <br/>
            <span style="font-weight: bold">{{ ucwords($montant_en_lettre ?? '') }} FRANCS CFA</span></td>
        </tr>
    </table>

    

    @if($cotation_service != null)
        <div class="page-break"></div>
        <header>
            <table width="100%" style="background-color: white">
                <tr>
                    <td class="img">
                        <img src="{{ asset('images/capturelogos.jpg') }}" width="200">
                    </td>
                </tr>
            </table>
        </header>

        <table style="margin: auto; font-size:11px;" width="100%" >
            <tr>
                <td>
                    <table style="margin-top: 0px;">
                        <tr>
                            <td>
                                <strong>BON DE COMMANDE</strong>
                            </td>
                            <td>
                                <strong>N° {{ $cotation_service->num_bc ?? '' }}</strong>
                            </td>
                        </tr>
                    </table>
                
                    <table width="100%">
                        <tr>
                            <td style="margin-top:5px;">
                                EXERCICE</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                <strong>{{ $demande_fond->exercice ?? '' }}</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                DATE COMMANDE</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                <strong>
                                    @if(isset($cotation_service->created_at))
                                        {{ date('d/m/Y',strtotime($cotation_service->created_at)) }}
                                    @endif
                                </strong> 
                            </td>
                        </tr>
                        <tr>
                            <td style="margin-top:5px;">
                                COMPTE BUDGETAIRE 
                            </td>
                            <td style="margin-top:5px;">
                                <strong>{{ $demande_fond->ref_fam ?? '' }}</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                DATE DE RETRAIT 
                            </td>
                            <td style="margin-top:5px;">
                                <strong>
                                    @if(isset($cotation_service->date_retrait))
                                        {{ date('d/m/Y',strtotime($cotation_service->date_retrait)) }}
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="margin-top:5px;">
                                STRUCTURE
                            </td>
                            <td style="margin-top:5px;">
                                <strong>{{ $demande_fond->code_structure ?? '' }}</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                DATE DE LIVRAISON PREVUE
                            </td>
                            <td style="margin-top:5px;">
                                <strong>
                                    @if(isset($cotation_service->date_livraison_prevue))
                                        {{ date('d/m/Y',strtotime($cotation_service->date_livraison_prevue)) }}
                                    @endif
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="margin-top:5px;">
                                GESTION
                            </td>
                            <td style="margin-top:5px;">
                                <strong>{{ $demande_fond->code_gestion ?? '' }} {{ $demande_fond->libelle_gestion ?? '' }}</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                FOURNISSEUR
                            </td>
                            <td style="margin-top:5px;">
                                <strong> @if(isset($fournisseur)) {{ $organisation->denomination ?? '' }} @endif </strong> 
                            </td>
                        </tr>
                        <tr>
                            <td style="margin-top:5px;">
                                N° CNPS
                            </td>
                            <td style="margin-top:5px;">
                                <strong> @if(isset($fournisseur)) {{ $organisation->entnum ?? '' }} @endif </strong> 
                            </td>
                            <td style="margin-top:5px;">
                                COMPTE CONTRIBUABLE
                            </td>
                            <td style="margin-top:5px;">
                                <strong> @if(isset($fournisseur)) {{ $organisation->num_contribuable ?? '' }} @endif</strong> 
                            </td>
                        </tr>
                        <tr>
                            <td style="margin-top:5px;">
                                ADRESSE
                            </td>
                            <td style="margin-top:5px;">
                                <strong> @if(isset($fournisseur)) {{ $organisation->adresse ?? '' }} @endif</strong> 
                            </td>
                            <td style="margin-top:5px;">
                                TELEPHONE
                            </td>
                            <td style="margin-top:5px;">
                                <strong> @if(isset($fournisseur)) {{ $organisation->contacts ?? '' }} @endif </strong> 
                            </td>
                        </tr>
                    </table>
    
                    <table width="100%" style="margin-top: 20px;">
                        <tr>
                            <td>
                                {{ $demande_fond->intitule ?? '' }}
                            </td>
                        </tr>
                    </table>
    
                    <table cellspacing="0" width="100%" border="1" style="margin-top: 20px;">
                        <thead>
                            <tr>
                            <td class="tr td-width">REF</td>
                            <td class="tr">DESIGNATION</td>
                            <td class="tr td-width">UNITE</td>
                            <td class="tr td-width2">QTE</td>
                            <td class="tr td-width3">P.UHT</td>
                            <td class="tr td-width">REMISE</td>
                            <td class="tr td-width">PRIX TOTAL</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=0 ?>
                            @foreach($detail_demande_fonds as $detail_demande_fond)
                            <?php $i++ ?>
                                <?php 
                                $libelle_unite = null;
    
                                if (isset($detail_demande_fond->code_unite)) {
                                    $unite = DB::table('unites')->where('code_unite',$detail_demande_fond->code_unite)->first();
                                    if ($unite!=null) {
                                        $libelle_unite = $unite->unite;
                                    }
                                }
                                ?>
                                <tr>
                                    <td class="td-center">{{ $detail_demande_fond->id ?? '' }}</td>
                                    <td class="td-left">{{ $detail_demande_fond->libelle ?? '' }}</td>
                                    <td class="td-center">{{ $libelle_unite ?? '' }}</td>
                                    <td class="td-center">{{ strrev(wordwrap(strrev($detail_demande_fond->qte ?? ''), 3, ' ', true)) }}</td>
                                    <td class="td-right">{{ strrev(wordwrap(strrev($detail_demande_fond->prix_unit ?? ''), 3, ' ', true)) }}</td>
                                    <td class="td-center">{{ $detail_demande_fond->remise ?? '' }}</td>
                                    <td class="td-right">{{ strrev(wordwrap(strrev($detail_demande_fond->montant_ht ?? ''), 3, ' ', true)) }}</td>
                                </tr>
                                <?php
                                    if( ($i % 15 == 0) ){ 
                                        //echo '<div class="page-break"></div>';
                                    }
                                ?>
                                
                            @endforeach
                            
                        </tbody>
                        
                        <tfoot>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none" width="80">MONTANT HT</td>
                                <td class="td-border-none"></td>
                                <td class="td-right td-border-non" style="border-bottom-color: white" width="50">{{ strrev(wordwrap(strrev($cotation_service->montant_total_brut ?? ''), 3, ' ', true)) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none">REMISE</td>
                                <td class="td-border-none"></td>
                                <td class="td-right td-border-non" style="border-bottom-color: white">{{ strrev(wordwrap(strrev($cotation_service->remise_generale ?? ''), 3, ' ', true)) }}</td>
                            </tr>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none">MONTANT NET HT</td>
                                <td class="td-border-none"></td>
                                <td class="td-right td-border-non" style="border-bottom-color: white">{{ strrev(wordwrap(strrev($cotation_service->montant_total_net ?? ''), 3, ' ', true)) }}</td>
                            </tr>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none">TVA</td>
                                <td class="td-center td-border-none">{{ $cotation_service->tva ?? '' }}%</td>
                                <?php
                                    if (isset($cotation_service->tva)) {
        
                                        $montant_tva = ($cotation_service->montant_total_net/100)* $cotation_service->tva;
                                        
                                    }
                                ?>
                                <td class="td-right td-border-non" style="border-bottom-color: white">{{ strrev(wordwrap(strrev($montant_tva ?? 0), 3, ' ', true)) }}</td>
                            </tr>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none">MONTANT TTC</td>
                                <td class="td-border-none"></td>
                                <td class="td-right td-border-non" style="border-bottom-color: white">{{ strrev(wordwrap(strrev($cotation_service->montant_total_ttc ?? ''), 3, ' ', true)) }}</td>
                            </tr>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none">ACOMPTE</td>
                                <td class="td-center td-border-none">{{ $cotation_service->taux_acompte ?? '' }}%</td>
                                <td class="td-right td-border-non" style="border-bottom-color: white">{{ strrev(wordwrap(strrev((int) $cotation_service->montant_acompte ?? ''), 3, ' ', true)) }}</td>
                            </tr>
                            <tr>
                                <td class="td-border-none" colspan="4"></td>
                                <td class="td-border-none">RESTE A PAYER</td>
                                <td class="td-border-none"></td>
                                <?php
                                    if (isset($cotation_service->montant_acompte)) {
        
                                        $montant_reste_a_payer = ($cotation_service->montant_total_ttc - $cotation_service->montant_acompte);
                                        
                                    }else{
                                        $montant_reste_a_payer = $cotation_service->montant_total_ttc;
                                    }
                                ?>
                                <td class="td-right td-border-non" >{{ strrev(wordwrap(strrev((int)$montant_reste_a_payer ?? ''), 3, ' ', true)) }}</td>
                            </tr>
                        </tfoot> 
                    </table>
                    
                              
    
                    <table width="100%" style="margin-top: 20px;">
                        <tr>
                            <td>
                                <?php 
                                $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
                                ?>
                                {{ 'MONTANT TOTAL ' }}
                                <strong>{{ strtoupper($f->format($cotation_service->montant_total_ttc)) }}</strong>
                                {{ $cotation_service->libelle ?? '' }}
                            </td>
                        </tr>
                    </table>                    
                </td>
            </tr>
        </table>
    @endif

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
        <table width="100%">
            <tr>
                <td style="font-weight: bold; font-size:14px">N.B. : La facture définitive et autres justificatifs doivent être déposés à la DFC ou les structures comptables dans les 72 heures qui suivent la réception. </td>
            </tr>
        </table>
    </footer>

</body>
</html>
