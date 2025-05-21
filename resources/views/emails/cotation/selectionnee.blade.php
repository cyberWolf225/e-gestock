<?php

    $demande_achat_mails = DB::table('demande_achats')
    ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
    ->join('detail_cotations','detail_cotations.cotation_fournisseurs_id','=','cotation_fournisseurs.id')
    ->join('articles','articles.ref_articles','=','detail_cotations.ref_articles')
    ->where('detail_cotations.cotation_fournisseurs_id',$datas['cotation_fournisseurs_id'])
    ->select('articles.ref_articles','articles.design_article','detail_cotations.qte','detail_cotations.prix_unit','detail_cotations.remise','detail_cotations.montant_ht','cotation_fournisseurs.montant_total_brut','cotation_fournisseurs.assiete_bnc','cotation_fournisseurs.montant_total_net','cotation_fournisseurs.tva','cotation_fournisseurs.montant_total_ttc','cotation_fournisseurs.taux_bnc','cotation_fournisseurs.net_a_payer','cotation_fournisseurs.montant_acompte','demande_achats.num_bc')
    ->get();


    $demande_achat_info = DB::table('demande_achats')
    ->join('detail_demande_achats','detail_demande_achats.demande_achats_id','=','demande_achats.id')
    ->join('articles','articles.ref_articles','=','detail_demande_achats.ref_articles')
    ->join('cotation_fournisseurs','cotation_fournisseurs.demande_achats_id','=','demande_achats.id')
    ->select('cotation_fournisseurs.montant_total_brut','cotation_fournisseurs.assiete_bnc','cotation_fournisseurs.montant_total_net','cotation_fournisseurs.tva','cotation_fournisseurs.montant_total_ttc','cotation_fournisseurs.taux_bnc','cotation_fournisseurs.net_a_payer','cotation_fournisseurs.montant_acompte','demande_achats.num_bc')
    ->where('cotation_fournisseurs.id',$datas['cotation_fournisseurs_id'])
    ->where('detail_demande_achats.flag_valide',1)
    ->first();

    if ($demande_achat_info!=null) {

        $num_bc = $demande_achat_info->num_bc;
        
    }

                
        
?>

<!DOCTYPE html>
<html>
    <head>
        <title>{{ $datas['subject'] ?? '' }}</title>
    </head>
    <body>
        <h4>{{ $datas['subject'] ?? '' }}</h4>
        <p>&nbsp;</p>
        Le fournisseur : <strong>{{ $datas['denomination'] ?? '' }}</strong> <span style="font-style:italic"> ( N° CNPS : {{ $datas['entnum'] ?? '' }} )</span>
         est sélectionné pour la prestation ci-dessous :
        <p>&nbsp;</p>

        <p style="color: black">N° Bon de commande : <strong style="color:red">{{ $num_bc ?? '' }}</strong></p>
        <p style="color: black">Famille d'article : <strong>{{ $datas['design_fam'] ?? '' }}</strong></p>
        <p style="color: black">Intitulé : <strong>{{ $datas['intitule'] ?? '' }}</strong></p>
        <p style="color: black">Date échéance : <strong>{{ $datas['date_echeance'] ?? '' }}</strong></p>
        <p style="color: black">Date échéance Frs : <strong>{{ $datas['date_echeance_frs'] ?? '' }}</strong></p>

        
        <br/>
        <br/>
        @if(isset($datas['link']))
        
            <p>Cliquez ici {{ $datas['link'] ?? '' }} pour plus de détails.</p>


        @endif

    </body>
</html>

