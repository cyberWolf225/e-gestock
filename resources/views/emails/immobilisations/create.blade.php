<?php

    if (isset($datas['immobilisations_id'])) {

        $immobilisation_info = DB::table('immobilisations as i')
            ->join('structures as s','s.code_structure','=','i.code_structure')
            ->join('gestions as g','g.code_gestion','=','i.code_gestion')
            ->join('profils as p','p.id','=','i.profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('i.id',$datas['immobilisations_id'])
            ->select('a.mle','a.nom_prenoms','u.email','s.code_structure','s.nom_structure','i.num_bc','i.exercice','i.intitule','i.updated_at')
            ->first();

        if ($immobilisation_info!=null) {

            $mle = $immobilisation_info->mle;
            $nom_prenoms = $immobilisation_info->nom_prenoms;
            $email = $immobilisation_info->email;
            $code_structure = $immobilisation_info->code_structure;
            $nom_structure = $immobilisation_info->nom_structure;
            $num_bc = $immobilisation_info->num_bc;
            $exercice = $immobilisation_info->exercice;
            $intitule = $immobilisation_info->intitule;
            if (isset($immobilisation_info->updated_at)) {
                $updated_at = date("d/m/Y H:i:s",strtotime($immobilisation_info->updated_at)) ;
            }
            

        }
        $immobilisations = [];

        if (isset($datas['subject'])) {

            $immobilisations = DB::table('immobilisations as i')
                ->join('detail_immobilisations as di','di.immobilisations_id','=','i.id')
                ->join('magasin_stocks as ms','ms.id','=','di.magasin_stocks_id')
                ->join('articles as a','a.ref_articles','=','ms.ref_articles')
                ->join('familles as f','f.ref_fam','=','a.ref_fam')
                ->where('di.immobilisations_id',$datas['immobilisations_id'])
                ->orderBy('di.id')
                ->select('f.*','a.*','ms.qte as qte_stock','di.*','di.id as detail_immobilisations_id','di.qte','i.*')
                ->get();
            
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
        <p style="color: black">Matricule du Pilote AEE : <strong>{{ $mle ?? '' }}</strong></p>
        <p style="color: black">Nom & Prénom(s) du Pilote AEE : <strong>{{ $nom_prenoms ?? '' }}</strong></p>
        <p style="color: black">N° Bon de commande : <strong>{{ $num_bc ?? '' }}</strong></p>
        <p style="color: black">Intitulé de la demande : <strong>{{ $intitule ?? '' }}</strong></p>
        <p style="color: black">Date : <strong>{{ $updated_at ?? '' }}</strong></p>
        
        
        <p> <strong style="color:black"> Détail de la Demande </strong> </p>

        <table border="1" cellspacing="0" class="table table-bordeblack" style="width: 100%"  >
            <thead>
                <tr style="background-color: #e9ecef; color: #7d7e8f">
                    <th style="width: 3%">#</th>
                    <th style="width: 10%; text-align: left">BENEFICIAIRE</th>
                    <th style="width: 35%; text-align: left">DESCRIPTION DU BENEFICIAIRE</th>
                    <th style="width: 10%; text-align: left">RÉF.</th>
                    <th style="width: 35%; text-align: left">DÉSIGNATION DE L'ÉQUIPEMENT</th>
                    <th style="width: 7%">QUANTITÉ</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @foreach($immobilisations as $immobilisation)

                    <?php 
                        $beneficiaire = null;
                        $description_beneficiaire = null;
                    
                        if ($immobilisation->type_beneficiaire === 'Agent') {
                            
                            $user = DB::table('agents as a')
                            ->where('a.mle',$immobilisation->beneficiaire)
                            ->first();

                            if ($user != null) {

                                $beneficiaire = $user->mle;
                                $description_beneficiaire = $user->nom_prenoms;
                                
                            }

                        }elseif ($immobilisation->type_beneficiaire === 'Structure') {
                            $structure = DB::table('structures as s')
                            ->where('s.code_structure',$immobilisation->beneficiaire)
                            ->first();

                            if ($structure != null) {

                                $beneficiaire = $structure->code_structure;
                                $description_beneficiaire = $structure->nom_structure;
                                
                            }
                        }
                    ?>
                    <tr style="color: black">
                        <td style="text-align: center">{{ $i ?? '' }}</td>
                        <td>{{ $beneficiaire ?? '' }}</td>
                        <td>{{ $description_beneficiaire ?? '' }}</td>
                        <td>{{ $immobilisation->ref_articles ?? '' }}</td>
                        <td>{{ $immobilisation->design_article ?? '' }}</td>
                        <td style="text-align: center">{{ $immobilisation->qte ?? '' }}</td>
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
        
        

    </body>
</html>