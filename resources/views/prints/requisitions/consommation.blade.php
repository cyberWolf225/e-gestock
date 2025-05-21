<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $titre ?? '' }}</title>

    <style>
        .fond{
            background: url('../../dist/img/sante2.jpg') no-repeat;
            background-size: 100% 100%;
            font-size: 11px;
        }
    
        .td-center{
            text-align: center;
            vertical-align: middle;
            width: 1%; white-space: nowrap;
        }
    
        .td-center-bold{
            text-align: center;
            vertical-align: middle;
            font-weight: bold
        }
    
        .td-left{
            text-align: left;
            vertical-align: middle;
            width: 1%; white-space: nowrap;
        }
    
        .td-right{
            text-align: right;
            vertical-align: middle;
            width: 1%; white-space: nowrap;
        }
      </style>

</head>
<body style="font-size:12px;">

    <table width="100%" border="0" cellspacing="0">
        <tr>
            <td width="95%"><span style="text-align:center"><h2>Consommation par structure par famille <br>d'article et par article</h2></span>
                <p style="text-align:center">Du: <b>{{ date('d/m/Y',strtotime($periode_debut ?? '')) }}</b> Au: <b>{{ date('d/m/Y',strtotime($periode_fin ?? '')) }}</b></p>
            </td>
            <td width="5%">
            </td>
        </tr>
    </table>
    
    <table width="100%" border="0" cellspacing="0" style="font-size:12px;">
        <tr>
            <td>Imprimé le</td>
            <td>:</td>
            <td><b>{{ date('d/m/Y') }}</b></td>
            <td></td>
        </tr>
        <tr>
            <td>Structure</td>
            <td>:</td>
            <td><b>{{ $structure->code_structure ?? '' }}</b></td>
            <td><b>{{ $structure->nom_structure ?? '' }}</b></td>

        </tr>
        <tr>
            <td>Section</td>
            <td>:</td>
            <td><b>{{ $section_structure->code_section ?? '' }}</b></td>
            <td><b>{{ $section_structure->nom_section ?? '' }}</b></td>
        </tr>
        <tr>
            <td>Famille</td>
            <td>:</td>
            <td><b>{{ $famille->ref_fam ?? '' }}</b></td>
            <td><b>{{ $famille->design_fam ?? '' }}</b></td>
        </tr>
    </table>
    
    <br>

    <table border="1" cellspacing="0" style="margin: auto; font-size:11px;" width="100%">
        <thead>
            <tr style="background-color: #c4c0c0">
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap; padding:10px">Ref</th>
            <th style="vertical-align: middle; text-align:center; padding:10px" width="90%">Désignation</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap; padding:10px">Qté cdé</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap; padding:10px">Qté liv</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap; padding:10px">Prix unit</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap; padding:10px">Montant ttc</th>
            
            </tr>
        </thead>
        <tbody>
            <?php $i=1; $u=1; $section[0] = "sec_0"; $montant_old[0] = 0; $qte_cde_old[0] = 0; $qte_old[0] = 0; $montant_section = 0; $qte_cde_section = 0; $qte_section = 0; $montant_sections[$u] = 0; $qte_cde_sections[$u] = 0; $qte_sections[$u] = 0; $montant_total = 0; $qte_cde_total = 0; $qte_total = 0;?>
            @foreach($consommations as $consommation)
            
            <?php 
                $qte_reelle_cde = 0;
                $qte_cde_old[$i] = 0;

                if(isset($consommation->ref_articles)){

                    if ($consommation->departements_id != null) {
                        $consommation_qte_cdes =  DB::select("SELECT SUM(dd.qte) qte_cde 
                    
                        FROM demandes dd, magasin_stocks mms
                        
                        WHERE 
                        mms.id = dd.magasin_stocks_id 
                        
                        AND 
                        dd.id IN (
                            SELECT l.demandes_id FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s
                        
                            WHERE

                            l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$consommation->code_structure."' AND a.ref_fam = '".$famille->ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' AND ms.ref_articles = '".$consommation->ref_articles."' AND r.departements_id = '".$consommation->departements_id."'

                        )
                        AND mms.ref_articles = '".$consommation->ref_articles."'
                        ");
                    }else{
                        $consommation_qte_cdes =  DB::select("SELECT SUM(dd.qte) qte_cde 
                    
                        FROM demandes dd, magasin_stocks mms
                        
                        WHERE 
                        mms.id = dd.magasin_stocks_id 
                        
                        AND 
                        dd.id IN (
                            SELECT l.demandes_id FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s
                        
                            WHERE

                            l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$consommation->code_structure."' AND a.ref_fam = '".$famille->ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' AND ms.ref_articles = '".$consommation->ref_articles."' AND r.departements_id is NULL

                        )
                        AND mms.ref_articles = '".$consommation->ref_articles."'
                        ");
                    }
                    
                    foreach ($consommation_qte_cdes as $consommation_qte_cde) {
                        $qte_cde_old[$i] = $consommation_qte_cde->qte_cde;
                        $qte_reelle_cde = $consommation_qte_cde->qte_cde;
                    }
                }
                
                
                $montant_old[$i] = $consommation->montant;
                
                $qte_old[$i] = $consommation->qte;

                $section[$i] = $consommation->nom_departement;
                $cmup = number_format((float)$consommation->cmup, 2, '.', '');

                $block_cmup = explode(".",$cmup);



                if (isset($block_cmup[0])) {
                    $cmup_partie_entiere = $block_cmup[0];
                }


                if (isset($block_cmup[1])) {
                    $cmup_partie_decimale = $block_cmup[1];
                }

                $montant_section = $montant_section + $montant_old[$i-1];
                    
                $qte_cde_section = $qte_cde_section + $qte_cde_old[$i-1];

                
                
                $qte_section = $qte_section + $qte_old[$i-1];

                if(count($consommations) != $i){

                    $affiche_block_section_total = 0;
                    $affiche_block_structure_total = 0;

                }elseif(count($consommations) === $i){
                    
                    /*$montant_section = $montant_section + $montant_old[$i-1] + $montant_old[$i];

                    $qte_cde_section = $qte_cde_section + $qte_cde_old[$i-1] + $qte_cde_old[$i];

                    $qte_section = $qte_section + $qte_old[$i-1] + $qte_old[$i];*/

                    $affiche_block_section_total = 1;
                    $affiche_block_structure_total = 1;
                }

                if($section[$i-1] != $section[$i]){
                    $affiche_block_section = 1;
                }else {
                    $affiche_block_section = 0;
                }
                
            ?>
                
                @if($affiche_block_section === 1)
                    @if($section[$i-1] != "sec_0")

                    <?php
                        
                            $montant_sections[$u] = $montant_section - $montant_sections[$u-1];
                            
                            $qte_cde_sections[$u] = $qte_cde_section - $qte_cde_sections[$u-1]; 

                            $qte_sections[$u] = $qte_section - $qte_sections[$u-1]; 

                            if ($u > 2) {

                                for ($v=2; $v < $u; $v++) { 

                                    $montant_sections[$u] = $montant_sections[$u] - $montant_sections[$u-$v];

                                    $qte_cde_sections[$u] = $qte_cde_sections[$u] - $qte_cde_sections[$u-$v];

                                    $qte_sections[$u] = $qte_sections[$u] - $qte_sections[$u-$v];

                                }
                            }
                            
                            $montant_total = $montant_total + $montant_sections[$u];

                            $qte_cde_total = $qte_cde_total + $qte_cde_sections[$u];

                            $qte_total = $qte_total + $qte_sections[$u];
                        
                        
                    ?>
                        
                        <tr style="background-color: #c4c0c0; font-weight:bold">
                            <td colspan="2" style="padding:10px">Total par section</td>
                            <td style="text-align: center; font-weight:bold; padding:10px">{{ strrev(wordwrap(strrev($qte_cde_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                            <td style="text-align: center; font-weight:bold; padding:10px">{{ strrev(wordwrap(strrev($qte_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                            <td style="text-align: center; font-weight:bold; padding:10px"></td>
                            <td style="text-align: right; font-weight:bold; padding:10px;">{{ strrev(wordwrap(strrev($montant_sections[$u] ?? ''), 3, ' ', true)) }}
                            </td>
                        </tr>
                        
                    @endif
                    
                    
                    @if(isset($consommation->nom_departement))
                            
                        <tr><td colspan="6" style="text-align: center; font-weight:bold; padding:10px; color:red">{{ mb_strtoupper($consommation->nom_departement ?? '') }}</td></tr>
                        
                        
                    @else
                        <tr><td colspan="6" style="text-align: center; font-weight:bold;padding:10px; color:red">{{ mb_strtoupper('SECTION COMMUNE') }}</td></tr>
                    @endif
                    
                    <?php $u++; ?>
                @endif
               
                

                <tr>
                    <td style="padding:10px" class="td-left">{{ $consommation->ref_articles ?? '' }}</td>
                    <td style="padding:10px" class="td-left">{{ $consommation->design_article ?? '' }}</td>
                    <td style="padding:10px" class="td-center">{{ strrev(wordwrap(strrev($qte_reelle_cde ?? ''), 3, ' ', true)) }}</td>
                    <td style="padding:10px" class="td-center">{{ strrev(wordwrap(strrev($consommation->qte ?? ''), 3, ' ', true)) }}</td>
                    <td style="padding:10px" class="td-right">
                        @if(isset($cmup_partie_decimale) && $cmup_partie_decimale != 0)
                            {{ strrev(wordwrap(strrev($cmup_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($cmup_partie_decimale ?? ''), 3, ' ', true)) }}
                        @else
                            {{ strrev(wordwrap(strrev($cmup_partie_entiere ?? ''), 3, ' ', true)) }}
                        @endif
                    </td>
                    <td style="padding:10px" class="td-right">{{ strrev(wordwrap(strrev($consommation->montant ?? ''), 3, ' ', true)) }}</td>
                </tr>

                @if($affiche_block_section_total === 1 && $affiche_block_structure_total === 1)
                    
                    <?php
                            
                            if(count($consommations) === $i){

                                $montant_section = $montant_section + $montant_old[$i];

                                $qte_cde_section = $qte_cde_section + $qte_cde_old[$i];

                                $qte_section = $qte_section + $qte_old[$i];

                            }
                            
                            $montant_sections[$u] = $montant_section - $montant_sections[$u-1];

                            $qte_cde_sections[$u] = $qte_cde_section - $qte_cde_sections[$u-1]; 

                            $qte_sections[$u] = $qte_section - $qte_sections[$u-1];

                            if ($u > 2) {

                                for ($v=2; $v < $u; $v++) { 
                                    
                                    $montant_sections[$u] = $montant_sections[$u] - $montant_sections[$u-$v];

                                    $qte_cde_sections[$u] = $qte_cde_sections[$u] - $qte_cde_sections[$u-$v];

                                    $qte_sections[$u] = $qte_sections[$u] - $qte_sections[$u-$v];
                                    


                                }

                            }
                            
                            $montant_total = $montant_total + $montant_sections[$u];

                            $qte_cde_total = $qte_cde_total + $qte_cde_sections[$u];

                            $qte_total = $qte_total + $qte_sections[$u]; 
                        
                    
                    ?>
                    <tr style="background-color: #c4c0c0; ; font-weight:bold">
                        <td colspan="2" style="padding:10px">Total par section</td>
                        <td style="text-align: center; font-weight:bold; padding:10px">{{ strrev(wordwrap(strrev($qte_cde_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                        <td style="text-align: center; font-weight:bold; padding:10px">{{ strrev(wordwrap(strrev($qte_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                        <td style="text-align: center; font-weight:bold; padding:10px"></td>
                        <td style="text-align: right; font-weight:bold; padding:10px;">{{ strrev(wordwrap(strrev($montant_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                    </tr>
                    
                    <tr style="background-color: #c4c0c0; font-weight:bold">
                        <td colspan="2" style="padding:10px">Total par structure</td>
                        <td style="text-align: center; font-weight:bold; padding:10px">{{ strrev(wordwrap(strrev($qte_cde_total ?? ''), 3, ' ', true)) }}</td>
                        <td style="text-align: center; font-weight:bold; padding:10px">{{ strrev(wordwrap(strrev($qte_total ?? ''), 3, ' ', true)) }}</td>    
                        <td style="text-align: center; font-weight:bold; padding:10px"></td>    
                        <td style="text-align: right; font-weight:bold; padding:10px;">{{ strrev(wordwrap(strrev($montant_total ?? ''), 3, ' ', true)) }}</td>
                    </tr>
                    
                @endif
                

                <?php $i++; ?>
            @endforeach
        </tbody>
    </table>
</body>
</html>