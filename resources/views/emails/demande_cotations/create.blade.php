<?php
$demande_cotations = [];
$denomination = null;
$date_echeance = null;

if (isset($datas['demande_cotations_id'])) {
    if ($datas['demande_cotations_id'] != null) { 
        // récuperer le demandeur

        $demande_cotation = DB::table('demande_cotations as da') 
                ->join('familles as f','f.ref_fam','=','da.ref_fam')
                ->where('da.id',$datas['demande_cotations_id'])
                ->first();
        if ($demande_cotation!=null) {
            $num_bc = $demande_cotation->num_bc;
            $intitule = $demande_cotation->intitule;
            $design_fam = $demande_cotation->design_fam;
            if($demande_cotation->date_echeance != null){
                $date_echeance = date('d/m/Y H:i:s',strtotime($demande_cotation->date_echeance));
            }
            
        }

        $detail_demande_cotations = DB::table('demande_cotations as da') 
            ->join('detail_demande_cotations as ddc','ddc.demande_cotations_id','=','da.id')
            ->where('da.id',$datas['demande_cotations_id'])
            ->get();  
        $type_statut_demande_cotations_libelle = null;
        $statut_demande_cotation = DB::table('statut_demande_cotations as sda')
            ->join('type_statut_demande_cotations as tsda','tsda.id','=','sda.type_statuts_id')
            ->join('profils as p','p.id','=','sda.profils_id')
            ->join('type_profils as tp','tp.id','=','p.type_profils_id')
            ->join('users as u','u.id','=','p.users_id')
            ->join('agents as a','a.id','=','u.agents_id')
            ->where('sda.demande_cotations_id',$demande_cotations_id)
            ->orderByDesc('sda.id')
            ->limit(1)
            ->first();
        if($statut_demande_cotation != null){
            $type_statut_demande_cotations_libelle = $statut_demande_cotation->libelle;
        }

        $fournisseur_demande_cotation = DB::table('fournisseur_demande_cotations as fdc')
        ->join('demande_cotations as dc','dc.id','=','fdc.demande_cotations_id')
        ->join('organisations as o','o.id','=','fdc.organisations_id')

        ->join('statut_organisations as so','so.organisations_id','=','o.id')
        ->join('profils as p','p.id','=','so.profils_id')
        ->join('users as u','u.id','=','p.users_id')
        ->join('type_statut_organisations as tso','tso.id','=','so.type_statut_organisations_id')
        ->where('tso.libelle','Activé')

        ->where('fdc.demande_cotations_id',$datas['demande_cotations_id'])
        ->where('fdc.flag_actif',1)
        ->where('u.email',$datas['email'])
        ->select('u.email','o.*','dc.*','fdc.*','fdc.id as fournisseur_demande_cotations_id')
        ->orderBy('fdc.id')
        ->first();
        if($fournisseur_demande_cotation != null){
            if($type_statut_demande_cotations_libelle === 'Transmis pour cotation'){
                $denomination = $fournisseur_demande_cotation->denomination;
            }
        }
    }
}
    
?>

<!DOCTYPE html>
<html>
    <body>
        @if(isset($datas['subject']))
            <p style="color: black; color:red">{{ $datas['subject'] ?? '' }}</p>
        @endif

        @if($denomination != null)
            <p style="color: black">Bonjour <strong>{{ $denomination }}</strong>,</p>
            @if ($datas['subject'] === "Demande de cotation transférée aux fournisseurs présélectionnés")
                <p>&nbsp;&nbsp; Vous êtes présélectionné pour soumissionner à cette demande d'achats.</p>
            @elseif ($datas['subject'] === "Cotation fournisseur")
                <p>&nbsp;&nbsp; Votre cotation pour cette demande d'achats a été enregistrée.</p>
            @endif
        @endif
        
        <p style="color: black"><strong style="color: brown">{{ $num_bc ?? '' }}</strong></p>
        <p style="color: black">Famille : <strong>{{ $design_fam ?? '' }}</strong></p>
        <p style="color: black">Objet : <strong>{{ $intitule ?? '' }}</strong></p>  
        @if($date_echeance != null)
            <p style="color: black">Date écheance : <strong style="color: brown">{{ $date_echeance ?? '' }}</strong></p>
        @endif      
        
        <br/>
        @if(isset($datas['link']))
            <p>Cliquez ici {{ $datas['link'] ?? '' }} pour plus de détails.</p>
        @endif
    </body>
</html>