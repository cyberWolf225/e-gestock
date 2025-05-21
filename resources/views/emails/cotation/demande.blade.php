<?php 
    $demande_achats_info = DB::table('demande_achats')
        ->join('detail_demande_achats','detail_demande_achats.demande_achats_id','=','demande_achats.id')
        ->join('articles','articles.ref_articles','=','detail_demande_achats.ref_articles')
        ->join('familles','familles.ref_fam','=','articles.ref_fam')
        ->join('profils','profils.id','=','demande_achats.profils_id')
        ->join('users','users.id','=','profils.users_id')
        ->join('agent_sections','agent_sections.agents_id','=','users.agents_id')
        ->join('sections','sections.id','=','agent_sections.sections_id')
        ->join('structures','structures.code_structure','=','sections.code_structure')
        ->select('articles.ref_articles','articles.design_article','detail_demande_achats.qte_demandee','detail_demande_achats.qte_accordee','detail_demande_achats.flag_valide','detail_demande_achats.id','demande_achats.num_bc')
        ->where('demande_achats.id',$datas['demande_achats_id'])
        ->where('detail_demande_achats.flag_valide',1)
        ->first();

        

        $demande_achats = DB::table('demande_achats')
        ->join('detail_demande_achats','detail_demande_achats.demande_achats_id','=','demande_achats.id')
        ->join('articles','articles.ref_articles','=','detail_demande_achats.ref_articles')
        ->join('familles','familles.ref_fam','=','articles.ref_fam')
        ->join('profils','profils.id','=','demande_achats.profils_id')
        ->join('users','users.id','=','profils.users_id')
        ->join('agent_sections','agent_sections.agents_id','=','users.agents_id')
        ->join('sections','sections.id','=','agent_sections.sections_id')
        ->join('structures','structures.code_structure','=','sections.code_structure')
        ->select('articles.ref_articles','articles.design_article','detail_demande_achats.qte_demandee','detail_demande_achats.qte_accordee','detail_demande_achats.flag_valide','detail_demande_achats.id')
        ->where('demande_achats.id',$datas['demande_achats_id'])
        ->where('detail_demande_achats.flag_valide',1)
        ->get();

?>

<!DOCTYPE html>
<html>
    <body>

            @if($datas['denomination'] != "")
            <p style="color: black">Bonjour, <strong>{{ $datas['denomination'] ?? '' }}</strong></p>
            @endif
            <p style="color: black"> 
                @if($datas['denomination'] === "")
                    Demande de cotation au fournisseur.
                @else
                    Vous avez été présélectionné par la CNPS pour une demande de cotation.
                @endif 
            </p>


        

        <p>&nbsp;</p>

        <p style="color: black">N° Bon de commande : <strong style="color:red">{{ $demande_achats_info->num_bc ?? '' }}</strong></p>
        <p style="color: black">Famille d'article : <strong>{{ $datas['design_fam'] }}</strong></p>
        <p style="color: black">Intitulé : <strong>{{ $datas['intitule'] }}</strong></p>
        <p style="color: black">Date échéance : <strong>{{ $datas['date_echeance'] }}</strong></p>
        @if(isset($datas['date_echeance_frs']))
            <p style="color: black">Date échéance Frs : <strong>{{ $datas['date_echeance_frs'] }}</strong></p>
        @endif

        <p> <strong style="color:red"> Détail de la Demande </strong> </p>

        <table border="1" cellspacing="0" class="table table-bordered" style="width: 100%"  >
            <thead>
                <tr style="color: black;">
                    <th style="width: 3%">#</th>
                    <th style="width: 20%; text-align: center">Ref article</th>
                    <th style="width: 67%; text-align: center">Désignation de l'article</th>
                    <th style="width: 10%; text-align: center">Quantité</th>

                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @foreach($demande_achats as $demande_achat)

                    <tr style="color: black">
                        <td style="text-align: center">{{ $i ?? '' }}</td>
                        <td style="text-align: center">{{ $demande_achat->ref_articles ?? '' }}</td>
                        <td>{{ $demande_achat->design_article ?? '' }}</td>
                        <td style="text-align: center">{{ strrev(wordwrap(strrev($demande_achat->qte_accordee ?? $qte_accordee ?? ''), 3, ' ', true)) }}</td>

                    </tr>
                    <?php $i++; ?>
                @endforeach

                @if(!isset($i))
                <tr style="color: black">
                    <td colspan="4" style="text-align: center; color:red">{{ 'error' }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <br/>
        <br/>
        @if(isset($datas['link']))
        
            Cliquez ici {{ $datas['link'] ?? '' }} pour plus de détails.

        @endif
        
    </body>
</html>