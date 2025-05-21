<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $titre ?? '' }}</title>

    <style>

        body {
            margin-top: 3cm;
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
            <tr>
                <td>Compte comptable</td>
                <td>:</td>
                <td><b>{{ $famille->ref_fam ?? '' }}</b></td>
                <td><b>{{ $famille->design_fam ?? '' }}</b></td>

            </tr>
        </table>
    </header>
    
    <br>

    <table border="1" cellspacing="0" style="margin: auto; font-size:11px;" width="100%">
        <thead>
            <tr style="background-color: #c4c0c0">
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap ; padding:10px; ">#</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap ; padding:10px; ">RÉFÉRENCE <br/>ARTICLE</th>
            <th style="vertical-align: middle; text-align:center" width="90%">DÉSIGNATION <br/>ARTICLE</th>
            <th style="vertical-align: middle; text-align:center" width="90%">CATÉGORIE</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap ; padding:10px; ">DATE <br/>CRÉATION</th>
            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap ; padding:10px; ">DATE <br/>MODIFICATION</th>
            </tr>
        </thead>
        <tbody style="font-size:12px;">
            <?php $i = 1; ?>
            @foreach($articles as $article)

                <?php 
                    $type_articles_libelle = null;
                    if(isset($article->type_articles_id)){
                        $type_article = $controller1->getTypeArticleById($article->type_articles_id);

                        if($type_article != null){

                            if($type_article->design_type != "Consommable"){
                                $type_articles_libelle = $type_article->design_type;
                            }
                        }
                    }

                    $created_at = null;
                    if(isset($article->articles_created_at)){
                        $created_at = date('d/m/Y H:i:s',strtotime($article->articles_created_at)); 
                    }

                    $updated_at = null;
                    if(isset($article->articles_updated_at)){
                        $updated_at = date('d/m/Y H:i:s',strtotime($article->articles_updated_at)); 
                    }
                ?>
                <tr>
                    <td class="td-center">{{ $i ?? '' }}</td>
                    <td class="td-left">{{ $article->ref_articles ?? '' }}</td>
                    <td class="td-left">{{ $article->design_article ?? '' }}</td>
                    <td class="td-left">{{ $type_articles_libelle ?? '' }}</td>
                    <td class="td-left">{{ $created_at ?? '' }}</td>
                    <td class="td-left">{{ $updated_at ?? '' }}</td>
                </tr>
                

                <?php $i++; ?>
            @endforeach
        </tbody>
    </table>
</body>
</html>