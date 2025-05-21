<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $titre ?? '' }}</title>

    <style>

        body {
            margin-top: 5cm;
            margin-left: 0cm;
            margin-right: 0cm;
            margin-bottom: 0cm;
        }

        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        .fond{
            background: url('../../dist/img/sante2.jpg') no-repeat;
            background-size: 100% 100%;
            font-size: 11px;
        }
    
        .td-center{
            text-align: center;
            vertical-align: middle;
            width: 1%; white-space: nowrap;
            padding:10px
        }
    
        .td-center-bold{
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            padding:10px
        }
    
        .td-left{
            text-align: left;
            vertical-align: middle;
            width: 1%; white-space: nowrap;
            padding:10px
        }
    
        .td-right{
            text-align: right;
            vertical-align: middle;
            width: 1%; white-space: nowrap;
            padding:10px
        }
      </style>

</head>
<body style="font-size:12px;">

    <header>
        <table width="100%" border="0" cellspacing="0">
            <tr>
                <td width="95%"><span style="text-align:center">
                    <h2>{{ $titre ?? '' }}</h2>
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
            @if($famille != null)
                <tr>
                    <td>Compte comptable</td>
                    <td>:</td>
                    <td><b>{{ $famille->ref_fam ?? '' }}</b></td>
                    <td><b>{{ $famille->design_fam ?? '' }}</b></td>

                </tr>
            @endif
            @if($depot != null)
                <tr>
                    <td>Dépôt</td>
                    <td>:</td>
                    <td><b>{{ $depot->ref_depot ?? '' }}</b></td>
                    <td><b>{{ $depot->design_dep ?? '' }}</b></td>

                </tr>
            @endif

        </table>
    </header>
    
    <br>

    <table border="1" cellspacing="0" style="margin: auto; font-size:11px;" width="100%">
        <thead>
            <tr style="background-color: #c4c0c0">
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">#</th>
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">RÉF. ARTICLE</th>
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 40%; white-space: nowrap;">DÉSIGNATION ARTICLE</th>
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">QTÉ <br>EN STOCK</th>
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">CMUP</th>
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">MONTANT TTC</th>
                <th style="padding:5px; vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">DATE</th>
            </tr>
        </thead>
        <tbody style="font-size:12px;">
            <?php $i = 1; ?>
            @foreach($magasin_stocks as $magasin_stock)
                <?php 
                    $prix_unit = (int) $magasin_stock->prix_unit;
                    $qte = $magasin_stock->qte;
                    if($qte < 0){ $qte = -1 * $qte; }

                    $montant = $qte * $prix_unit;
                ?>
                <tr>
                    <td class="td-center">{{ $i ?? '' }}</td>
                    <td class="td-left" style="font-weight: bold">{{ $magasin_stock->ref_articles ?? '' }}</td>
                    <td class="td-left">{{ $magasin_stock->design_article ?? '' }}</td>
                    <td class="td-center">{{ strrev(wordwrap(strrev($qte ?? ''), 3, ' ', true)) }}</td>
                    <td class="td-right">{{ strrev(wordwrap(strrev($prix_unit ?? ''), 3, ' ', true)) }}</td>
                    <td class="td-right">{{ strrev(wordwrap(strrev($montant ?? ''), 3, ' ', true)) }}</td>
                    <td class="td-right">{{ date('d/m/Y',strtotime($magasin_stock->date_mouvement)) }}</td>
                </tr>

                <?php $i++; ?>
            @endforeach
        </tbody>
    </table>
</body>
</html>