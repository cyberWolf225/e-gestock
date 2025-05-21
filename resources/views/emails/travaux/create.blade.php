
<?php

$travauxes = [];

if (isset($datas['travauxes_id'])) {
    if ($datas['travauxes_id']!=null) {  
        // récuperer le demandeur

        $trava = DB::table('travauxes as da') 
                ->join('familles as f','f.ref_fam','=','da.ref_fam')
                ->where('da.id',$datas['travauxes_id'])
                ->first();
        if ($trava!=null) {
            $num_bc = $trava->num_bc;
            $intitule = $trava->intitule;
            $design_fam = $trava->design_fam;
        }

        if (isset($datas['subject'])) {

            $travauxes = DB::table('travauxes as da') 
                ->join('detail_travauxes as d','d.travauxes_id','=','da.id')
                ->join('services as s','s.id','=','d.services_id')
                ->where('da.id',$datas['travauxes_id'])
                ->where('d.flag_valide',1)
                ->get();

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

        
        <p style="color: black">
            
            
        N° bon de commande :  

                
            
            
        <strong style="color: brown">{{ $num_bc ?? '' }}</strong></p>
        <p style="color: black">Famille : <strong>{{ $design_fam ?? '' }}</strong></p>
        <p style="color: black">Objet : <strong>{{ $intitule ?? '' }}</strong></p>
        @if(isset($date_echeance))
            <p style="color: black">Date écheance : <strong style="color: brown">{{ $date_echeance ?? '' }}</strong></p>
        @endif

        
        

        <br/>
        <br/>
        @if(isset($datas['link']))
        
            <p>Cliquez ici {{ $datas['link'] ?? '' }} pour plus de détails.</p>


        @endif
        
    </body>
</html>