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
            <td width="95%"><span style="text-align:center"><h2> <?php echo mb_strtoupper('Récapitulatif des consommations des fournitures par les structures'); ?> </h2></span>
                <p style="text-align:center">PERIODE : DU: <b>{{ date('d/m/Y',strtotime($periode_debut ?? '')) }}</b> AU: <b>{{ date('d/m/Y',strtotime($periode_fin ?? '')) }}</b></p>
            </td>
            <td width="5%">
            </td>
        </tr>
    </table>

    @if(isset($familles_consernees))
        @if(count($familles_consernees) > 0)

            <table border="1" cellspacing="0" style="margin: auto; font-size:11px;" width="100%">
                <thead>
                    <tr style="padding:10px;background-color: #c4c0c0;">
                    
                    <th style="padding:10px;" class="td-center">STRUCTURE</th>

                    @foreach ( $familles_consernees as $key => $familles_consernee)
                        <?php 
                            $famille = DB::table('familles')->where('ref_fam',$familles_consernee)->first();
                        ?>
                        <th style="padding:10px; text-align:center">{{ $famille->ref_fam ?? '' }} {{ mb_strtoupper($famille->design_fam ?? '') }}</th>
                    @endforeach                          
                    </tr>
                </thead>
                <tbody>
                    
                    <?php $i = 1; $consommation_totals = []; ?>
                    @foreach($structures as $structure)

                        <tr>
                            <td style="padding:10px; text-align:left">
                                {{ $structure->code_structure ?? '' }} - {{ $structure->nom_structure ?? '' }}
                            </td>

                            @foreach ( $familles_consernees as $key2 => $familles_consernee2)
                                <?php 
                                    $consommation = '-';
                                    $consommation_totals[$i][$key2] = 0;
                                    $famille2 = DB::table('familles')->where('ref_fam',$familles_consernee2)->first();
                                    if($famille2 != null){
                                        $consommations = DB::select("SELECT SUM(l.montant) montant FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s WHERE l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$structure->code_structure."' AND a.ref_fam = '".$famille2->ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' GROUP BY r.code_structure,a.ref_fam ");

                                        foreach ($consommations as $consommation_key) {
                                            $consommation = $consommation_key->montant;

                                            $consommation_totals[$i][$key2] = $consommation_totals[$i][$key2] + $consommation;
                                        }
                                    }
                                ?>
                                <td class="td-right" style="padding:10px;">{{ strrev(wordwrap(strrev($consommation ?? ''), 3, ' ', true)) }}</td>
                            @endforeach
                            
                        </tr>

                        @if(count($structures) === $i)
                            <tr  style="padding:10px;text-align:center; font-weight:bold; background-color: #c4c0c0;">
                                <td>TOTAL</td>
                                
                                @foreach ($familles_consernees as $key3 => $familles_consernee3)
                                    <?php 
                                        $consommation_total_famille = 0;

                                        foreach ($consommation_totals as $key => $values) {

                                            foreach ($values as $key_value => $value_conso) {
                                                if($key_value === $key3){
                                                    $consommation_total_famille = $consommation_total_famille + $value_conso;
                                                }
                                            }

                                        }
                                        
                                    ?>

                                    <td class="td-right" style="padding:10px;font-weight: bold">{{ strrev(wordwrap(strrev($consommation_total_famille ?? ''), 3, ' ', true)) }}</td>

                                @endforeach
                            </tr>
                        @endif
                        <?php $i++; ?>
                    @endforeach

                </tbody>
            </table>

        @endif
    @endif
</body>
</html>