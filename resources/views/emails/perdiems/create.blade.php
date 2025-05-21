<?php

    if (isset($datas['perdiems_id'])) {

        $perdiem_info = DB::table('perdiems as p')
            ->join('structures as s','s.code_structure','=','p.code_structure')
            ->join('gestions as g','g.code_gestion','=','p.code_gestion')
            ->join('familles as f','f.ref_fam','=','p.ref_fam')
            ->where('p.id',$datas['perdiems_id'])
            ->select('f.*','g.*','s.*','p.*')
            ->first();

        if ($perdiem_info!=null) {

            
            $code_structure = $perdiem_info->code_structure;
            $nom_structure = $perdiem_info->nom_structure;
            $num_or = $perdiem_info->num_or;
            $num_pdm = $perdiem_info->num_pdm;
            $exercice = $perdiem_info->exercice;
            $libelle = $perdiem_info->libelle;
            if (isset($perdiem_info->updated_at)) {
                $updated_at = date("d/m/Y H:i:s",strtotime($perdiem_info->updated_at)) ;
            }
            

        }
        $perdiems = [];

        if (isset($datas['subject'])) {

            $perdiems = DB::table('perdiems as p')
                ->join('detail_perdiems as dp','dp.perdiems_id','=','p.id')
                ->where('dp.perdiems_id',$datas['perdiems_id'])
                ->orderBy('dp.id')
                ->select('dp.*','p.*')
                ->get();
            
        }
        
    }

?>

<!DOCTYPE html>
<html>
    <body>
        @if(isset($datas['subject']))
            <p style="color:red">{{ $datas['subject'] }}</p>
        @endif
        <p style="color: black">N° : <strong style="color:red">{{ $num_pdm ?? '' }}</strong></p>
        <p style="color: black">Exercice : <strong>{{ $exercice ?? '' }}</strong></p>
        <p style="color: black">Intitulé de la demande : <strong>{{ $libelle ?? '' }}</strong></p>
        <p style="color: black">Date : <strong>{{ $updated_at ?? '' }}</strong></p>
        
        
        <p> <strong style="color:black"> Détail de la Demande </strong> </p>

        <table border="1" cellspacing="0" class="table table-bordeblack" style="width: 100%"  >
            <thead>
                <tr style="background-color: #e9ecef; color: #7d7e8f">
                    <th style="width: 3%">#</th>
                    <th style="width: 70%; text-align: left">NOM & PRENOM(S)</th>
                    <th style="width: 30%; text-align: right">MONTANT</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; ?>
                @foreach($perdiems as $perdiem)

                    <tr style="color: black">
                        <td style="text-align: center">{{ $i ?? '' }}</td>
                        <td>{{ $perdiem->nom_prenoms ?? '' }}</td>
                        <td style="text-align: right">{{ strrev(wordwrap(strrev($perdiem->montant ?? ''), 3, ' ', true)) }}</td>
                    </tr>
                    <?php $i++; ?>
                @endforeach
                @if(!isset($i))
                <tr style="color: black">
                    <td colspan="2" style="text-align: center; color:black">{{ 'Aucun article' }}</td>
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