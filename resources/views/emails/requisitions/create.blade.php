<?php

    if (isset($datas['requisitions_id'])) {

        $requisition_info = DB::table('requisitions as r')
        ->join('profils as p','p.id','=','r.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('agents as ag','ag.id','=','u.agents_id')
        ->join('structures as s','s.code_structure','=','r.code_structure')
        ->join('demandes as d','d.requisitions_id','=','r.id')
        ->select('ag.mle','ag.nom_prenoms','u.email','s.code_structure','s.nom_structure','r.num_bc','r.exercice','r.intitule','r.updated_at')
        ->where('r.id',$datas['requisitions_id'])
        ->first();
        if ($requisition_info!=null) {

            $mle = $requisition_info->mle;
            $nom_prenoms = $requisition_info->nom_prenoms;
            $email = $requisition_info->email;
            $code_structure = $requisition_info->code_structure;
            $nom_structure = $requisition_info->nom_structure;
            $num_bc = $requisition_info->num_bc;
            $exercice = $requisition_info->exercice;
            $intitule = $requisition_info->intitule;
            if (isset($requisition_info->updated_at)) {
                $updated_at = date("d/m/Y H:i:s",strtotime($requisition_info->updated_at)) ;
            }
            

        }
        $requisitions = [];

        if (isset($datas['subject'])) {

            if ($datas['subject'] === "Enregistrement de demande d'articles" or $datas['subject'] === "Modification de demande d'articles" or $datas['subject'] === "Annulation de demande d'articles" or $datas['subject'] === "Transmission de demande d'articles au Responsable directe" or $datas['subject'] === "Demande d'article annulée" or $datas['subject'] === "Demande d'article validée" or $datas['subject'] === "Demande d'article transmise au Responsable des stocks") {

                $requisitions = DB::table('requisitions as r')
                    ->join('demandes as d','d.requisitions_id','=','r.id')
                    ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->where('r.id',$datas['requisitions_id'])
                    ->select('a.ref_articles','a.design_article','d.qte')
                    ->get();
            }elseif($datas['subject'] === "Demande d'article [ Validé (Responsable des stocks) ]" or $datas['subject'] === "Demande d'article [ Partiellement validé (Responsable des stocks) ]" or $datas['subject'] === "Demande d'article soumise pour livraison" or $datas['subject'] === "Demande d'article [ Livraison totale [ Confirmé ] ]" or $datas['subject'] === "Demande d'article [ Livraison partielle [ Confirmé ] ]"){
                $requisitions = DB::table('requisitions as r')
                    ->join('demandes as d','d.requisitions_id','=','r.id')
                    ->join('magasin_stocks as ms','ms.id','=','d.magasin_stocks_id')
                    ->join('magasins as m','m.ref_magasin','=','ms.ref_magasin')
                    ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                    ->where('r.id',$datas['requisitions_id'])
                    ->select('a.ref_articles','a.design_article','d.qte')
                    ->get();
            }
        }
        
    }

?>

<!DOCTYPE html>
<html>
    <body>
        @if(isset($datas['subject']))
            <p style="color: black; color:black">{{ $datas['subject'] }}</p>
        @endif
        <p style="color: black">Exercice : <strong>{{ $exercice ?? '' }}</strong></p>
        <p style="color: black">Matricule du Demandeur : <strong>{{ $mle ?? '' }}</strong></p>
        <p style="color: black">Nom & Prénom(s) du Demandeur : <strong>{{ $nom_prenoms ?? '' }}</strong></p>
        <p style="color: black">N° Bon de commande : <strong>{{ $num_bc ?? '' }}</strong></p>
        <p style="color: black">Intitulé de la demande : <strong>{{ $intitule ?? '' }}</strong></p>
        <p style="color: black">Date : <strong>{{ $updated_at ?? '' }}</strong></p>
        
        
        <p> <strong style="color:black"> Détail de la Demande </strong> </p>

        <table border="1" cellspacing="0" class="table table-bordeblack" style="width: 100%"  >
            <thead>
                <tr style="color: black;">
                    <th style="width: 3%">#</th>
                    <th style="width: 20%; text-align: left">Ref article</th>
                    <th style="width: 67%; text-align: left">Désignation de l'article</th>
                    <th style="width: 10%">Quantité</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @foreach($requisitions as $requisition)
                    <tr style="color: black">
                        <td style="text-align: center">{{ $i ?? '' }}</td>
                        <td>{{ $requisition->ref_articles ?? '' }}</td>
                        <td>{{ $requisition->design_article ?? '' }}</td>
                        <td style="text-align: center">{{ $requisition->qte ?? '' }}</td>
                    </tr>
                    <?php $i++; ?>
                @endforeach
                @if(!isset($i))
                <tr style="color: black">
                    <td colspan="4" style="text-align: center; color:black">{{ 'Aucun article' }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        
        
        <br/>
        <br/>
        @if(isset($datas['link']))
        
            <p>Cliquez ici {{ $datas['link'] ?? '' }} pour plus de détails.</p>


        @endif
    </body>
</html>