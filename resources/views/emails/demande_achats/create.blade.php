
<?php
// if ($datas['subject'] === "Validation de demande d'achats" or $datas['subject'] === "Demande de cotation fournisseur" or $datas['subject'] === "Cotation fournisseur" or $datas['subject'] === "Sélection du fournisseur" or $datas['subject'] === "Dossier annulé par le Responsable des achats" or $datas['subject'] === "Dossier transmis au Responsable DMP" or $datas['subject'] === "Validation du bon de commande (Responsable DMP)" or $datas['subject'] === "Dossier transmis au Directeur Général Adjoint")

$demande_achats = [];

if (isset($datas['demande_achats_id'])) {
    if ($datas['demande_achats_id']!=null) { 
        // récuperer le demandeur

        $demande_acha = DB::table('demande_achats as da') 
                ->join('familles as f','f.ref_fam','=','da.ref_fam')
                ->where('da.id',$datas['demande_achats_id'])
                ->first();
        if ($demande_acha!=null) {
            $num_bc = $demande_acha->num_bc;
            $intitule = $demande_acha->intitule;
            $design_fam = $demande_acha->design_fam;
        }

        if (isset($datas['subject'])) {

            if ($datas['subject'] === "Enregistrement d'une demande d'achats" or $datas['subject'] === "Modification de demande d'achats" or $datas['subject'] === "Transmission de demande d'achats au Responsable des achats") { 

                $demande_achats = DB::table('demande_achats as da') 
                    ->join('detail_demande_achats as d','d.demande_achats_id','=','da.id')
                    ->join('articles as a','a.ref_articles','=','d.ref_articles')
                    ->where('da.id',$datas['demande_achats_id'])
                    ->get();
            }else{

                $demande_achats = DB::table('demande_achats as da') 
                    ->join('detail_demande_achats as d','d.demande_achats_id','=','da.id')
                    ->join('valider_demande_achats as vda','vda.detail_demande_achats_id','=','d.id')
                    ->join('articles as a','a.ref_articles','=','d.ref_articles')
                    ->where('vda.flag_valide',1)
                    ->where('da.id',$datas['demande_achats_id'])
                    ->get();

                    if (isset($datas['email'])) {
                        if (isset($denomination)) {

                            unset($denomination);

                        }
                        $preselection_soumissionnaire = DB::table('demande_achats as da')
                                          ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
                                          ->join('criteres as c','c.id','=','ca.criteres_id')
                                          ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
                                          ->join('organisations as o','o.id','=','ps.organisations_id')
                                          ->join('statut_organisations as so','so.organisations_id','=','o.id')
                                          ->join('profils as p','p.id','=','so.profils_id')
                                          ->join('users as u','u.id','=','p.users_id')
                                          ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                                          ->where('da.id',$datas['demande_achats_id'])
                                          ->where('tso.libelle','ACTIVER')
                                          ->where('c.libelle','Fournisseurs Cibles')
                                          ->where('u.email',$datas['email'])
                                          ->select('u.email','o.denomination')
                                          ->first();
                        if ($preselection_soumissionnaire!=null) {
                            $denomination = $preselection_soumissionnaire->denomination;
                        }else{

                            if ($datas['subject'] === "Cotation fournisseur") {

                                $preselection_soumissionnaire = DB::table('demande_achats as da')
                                          ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
                                          ->join('criteres as c','c.id','=','ca.criteres_id')
                                          ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
                                          ->join('organisations as o','o.id','=','ps.organisations_id')
                                          ->join('statut_organisations as so','so.organisations_id','=','o.id')
                                          ->join('profils as p','p.id','=','so.profils_id')
                                          ->join('users as u','u.id','=','p.users_id')
                                          ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                                          ->join('cotation_fournisseurs as cf','cf.organisations_id','=','o.id')
                                          ->where('da.id',$datas['demande_achats_id'])
                                          ->where('tso.libelle','ACTIVER')
                                          ->where('c.libelle','Fournisseurs Cibles')
                                          ->orderByDesc('cf.updated_at')
                                          ->limit(1)
                                          ->select('u.email','o.denomination')
                                          ->first();
                                if ($preselection_soumissionnaire!=null) {
                                    $denomination2 = $preselection_soumissionnaire->denomination;
                                }

                            }elseif ($datas['subject'] === "Sélection du fournisseur") {

                                $preselection_soumissionnaire = DB::table('demande_achats as da')
                                        ->join('critere_adjudications as ca','ca.demande_achats_id','=','da.id')
                                        ->join('criteres as c','c.id','=','ca.criteres_id')
                                        ->join('preselection_soumissionnaires as ps','ps.critere_adjudications_id','=','ca.id')
                                        ->join('organisations as o','o.id','=','ps.organisations_id')
                                        ->join('statut_organisations as so','so.organisations_id','=','o.id')
                                        ->join('profils as p','p.id','=','so.profils_id')
                                        ->join('users as u','u.id','=','p.users_id')
                                        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
                                        ->join('cotation_fournisseurs as cf','cf.organisations_id','=','o.id')
                                        ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
                                        ->where('da.id',$datas['demande_achats_id'])
                                        ->where('tso.libelle','ACTIVER')
                                        ->where('c.libelle','Fournisseurs Cibles')
                                        ->orderByDesc('sa.updated_at')
                                        ->limit(1)
                                        ->select('u.email','o.denomination')
                                        ->first();
                                if ($preselection_soumissionnaire!=null) {
                                    $denomination2 = $preselection_soumissionnaire->denomination;
                                }

                            }

                        }
                    }

                    if ($datas['subject'] === "Demande de cotation fournisseur" or $datas['subject'] === "Cotation fournisseur") {
                        //écheance
                        $commande = DB::table('commandes')->where('demande_achats_id',$datas['demande_achats_id'])->first();
                        if ($commande!=null) {
                            $date_echeance = date("d/m/Y",strtotime($commande->date_echeance)) ;
                        }
                    }

            }


        }


        $statut_demande = DB::table('demande_achats as da')
                            ->join('cotation_fournisseurs as cf','cf.demande_achats_id','=','da.id')
                            ->join('selection_adjudications as sa','sa.cotation_fournisseurs_id','=','cf.id')
                            ->where('da.id',$datas['demande_achats_id'])
                            ->whereIn('da.id',function($query){
                                $query->select(DB::raw('da2.id'))
                                      ->from('demande_achats as da2')
                                      ->join('statut_demande_achats as sda','sda.demande_achats_id','=','da2.id')
                                      ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                                      ->where('tsda.libelle','Visé (Responsable DMP)')
                                      ->whereRaw('da.id = da2.id');
                            })
                            ->first();
        
        

    }
}
    
?>

<!DOCTYPE html>
<html>
    <body>
        @if(isset($datas['subject']))
            <p style="color: black; color:red">{{ $datas['subject'] ?? '' }}</p>
        @endif

        @if ($datas['subject'] === "Demande de cotation fournisseur" or $datas['subject'] === "Cotation fournisseur")
            @if(isset($denomination))
                <p style="color: black">Bonjour <strong>{{ $denomination }}</strong>,</p>
                @if ($datas['subject'] === "Demande de cotation fournisseur")
                    <p>&nbsp;&nbsp; Vous êtes présélectionné pour soumissionner à cette demande d'achats.</p>
                @elseif ($datas['subject'] === "Cotation fournisseur")
                    <p>&nbsp;&nbsp; Votre cotation pour cette demande d'achats a été enregistrée.</p>
                @endif
            @endif
        @endif

        @if ($datas['subject'] === "Cotation fournisseur")
            @if(isset($denomination2))
                <p>&nbsp;&nbsp; Le fournisseur <strong>{{ $denomination2 }}</strong>, a repondu à cette demande d'achats.</p>
            @endif
        @endif

        @if ($datas['subject'] === "Sélection du fournisseur")
            @if(isset($denomination2))
                <p>&nbsp;&nbsp; Le fournisseur <strong>{{ $denomination2 }}</strong>, est sélectioné pour cette demande d'achats.</p>
            @endif
        @endif

        
        <p style="color: black">
            
            
            @if(isset($statut_demande))
                @if ($statut_demande!=null) 
                    N° bon de commande : 

                @else
                    N° demande : 

                    <?php
                        $num_bc = str_replace('BC','',$num_bc);
                    ?>
                @endif
            @else
                N° demande : 

                <?php
                    $num_bc = str_replace('BC','',$num_bc);
                ?>
            @endif
            
        <strong style="color: brown">{{ $num_bc ?? '' }}</strong></p>
        <p style="color: black">Famille : <strong>{{ $design_fam ?? '' }}</strong></p>
        <p style="color: black">Objet : <strong>{{ $intitule ?? '' }}</strong></p>
        @if(isset($date_echeance))
            <p style="color: black">Date écheance : <strong style="color: brown">{{ $date_echeance ?? '' }}</strong></p>
        @endif
        
        <p> <strong style="color:red"> Détail de la Demande </strong> </p>

        <table border="1" cellspacing="0" class="table table-bordered" style="width: 100%"  >
            <thead>
                <tr style="color: black;">
                    <th style="width: 3%">#</th>
                    <th style="width: 20%; text-align: center">Ref article</th>
                    <th style="width: 57%; text-align: center">Désignation de l'article</th>
                    <th style="width: 10%; text-align: center">Qté</th>
                    @if ($datas['subject'] === "Validation de demande d'achats")
                        <th style="width: 10%; text-align: center">Qté validée</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @foreach($demande_achats as $demande_achat)
                
                    <tr style="color: black">
                        <td style="text-align: center">{{ $i ?? '' }}</td>
                        <td style="text-align: center">{{ $demande_achat->ref_articles ?? '' }}</td>
                        <td>{{ $demande_achat->design_article ?? '' }}</td>
                        @if ($datas['subject'] === "Enregistrement d'une demande d'achats" or $datas['subject'] === "Modification de demande d'achats" or $datas['subject'] === "Transmission de demande d'achats au Responsable des achats")
                            <td style="text-align: center">{{ strrev(wordwrap(strrev($demande_achat->qte_demandee ?? ''), 3, ' ', true)) }}</td>
                            @else
                            <td style="text-align: center">{{ strrev(wordwrap(strrev($demande_achat->qte_validee ?? ''), 3, ' ', true)) }}</td>
                        @endif
                        @if ($datas['subject'] === "Validation de demande d'achats")
                            <td style="text-align: center">{{ strrev(wordwrap(strrev($demande_achat->qte_validee ?? ''), 3, ' ', true)) }}</td>
                        @endif
                        
                    </tr>
                    <?php $i++; ?>
                @endforeach
                @if(!isset($i))
                <tr style="color: black">
                    <td colspan="4" style="text-align: center; color:red">{{ 'Aucune donnée' }}</td>
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