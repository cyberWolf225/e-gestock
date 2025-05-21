<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $demande_achat->num_pdm ?? '' }}</title>
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
                margin-top: 4cm;
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
                position:fixed; 
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
            padding: 15px;
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
                    <img src="{{ public_path('images/logo2.png') }}" width="200">
                </td>
            </tr>
            <tr>
                <td style="font-size:12px">
                    --------------------- <br>
                    CELLULE DES MOYENS <br>
                    ET DU PATRIMOINE  <br>
                    ---------------------
                </td>
            </tr>
        </table>
    </header>

    

    <!-- Wrap the content of your PDF inside a main tag -->
    <br/>
    <table width="100%" style="margin: auto; font-size:11px; font-weight:bold">
        <tr>
            <td width="75%">
                <table style="margin-top: 0px;">
                    <tr>
                        <td width="70%">
                            <strong>
                                {{ mb_strtoupper($perdiem->libelle ?? '') }}
                            </strong>
                        </td>
                        <td width="30%">
                            &nbsp;
                        </td>
                    </tr>
                </table>
            </td>
            <td width="25%">
                <table width="100%">
                    <tr>
                        <td style="margin-top:5px;">
                            N° : {{ $perdiem->num_pdm ?? '' }} / Gestion : {{ $perdiem->code_gestion ?? '' }} / {{ $perdiem->exercice ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            Compte : {{ $perdiem->ref_fam ?? '' }} / {{ $perdiem->code_structure ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="margin-top:5px;">
                            Solde avant opération : {{ strrev(wordwrap(strrev($perdiem->solde_avant_op), 3, ' ', true)) }} FCFA
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br/>
    <table cellspacing="0" width="100%" border="1" style="margin: auto; font-size:11px;">
        <thead>
            <tr>
            <td class="tr" style="text-align: center; width:10px">N°</td>
            <td class="tr td-width" style="text-align: center">NOM ET PRENOMS</td>
            <td class="tr td-width">MONTANTS</td>
            <td class="tr" style="width:30%">EMARGEMENT</td>
            </tr>
        </thead>
        <tbody>
            <?php $i=0 ?>
            @foreach($detail_perdiems as $detail_perdiem)
            <?php $i++ ?>
                <tr>
                    <td class="td-center" style="; width:10px">{{ $i ?? '' }}</td>
                    <td class="td-center td-width">{{ $detail_perdiem->nom_prenoms ?? '' }}</td>
                    <td class="td-center td-width">{{ strrev(wordwrap(strrev($detail_perdiem->montant ?? ''), 3, ' ', true)) }}</td>
                    <td class="td-center" style="width:30%"></td>
                </tr>
                
            @endforeach
            
        </tbody>
        <tfoot>
            <tr style="font-weight: bold">
                <td style="border-left: 1px solid white; border-bottom: 1px solid white"></td>
                <td class="td-center">TOTAL</td>
                <td class="td-center">{{ strrev(wordwrap(strrev($perdiem->montant_total ?? ''), 3, ' ', true)) }}</td>
                <td style="border-right: 1px solid white; border-bottom: 1px solid white"></td>
            </tr>
        </tfoot>
                            
    </table>

    <footer>
        <table class="td-foot" cellspacing="0" width="100%" style="margin-top: 0px; text-align:center; ">
            <tr >
                <td>
                    <table width="100%" style="width: 100%; margin:auto; margin-top:3px; font-size: 11px; font-weight:bold" border="0" cellspacing="0">
        
                        <tr>
                            <td>
                                <table width="100%" style="margin:auto; margin-top:3px; font-size: 11px; font-weight:bold" border="0" cellspacing="0">
                            
                                <tr>
                                    <?php $i = 1; ?>
                                    @if(isset($signataires))
                                        
                                        @if(count($signataires) === 1)
                                            <?php $nombre_signataire_manquant = 3 - count($signataires); ?>
                                            @for ( $u = $nombre_signataire_manquant; $u > 0 ; $u--)
                                            <td width="50%" style="text-align:right">
                                                <table width="260px" border="0" style="margin:auto;">
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
                                                <table width="260px" border="0" style="margin:auto;">
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

                                                            {{ $fonctions_libelle ?? '' }}
                                                        </td>
                                                    </tr>
                
                                                    <tr>
                                                        <td style="height: 80px;">
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

                                            @if(count($signataires) === 2 && $i === 1)
                                                <td width="50%" style="text-align:right">
                                                    <table width="260px" border="0" style="margin:auto;">
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
                                            @endif

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
    </footer>

    
    
</body>
</html>

