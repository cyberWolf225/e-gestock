
<?php
    //
    if (isset($datas['demande_fonds_id'])) {

        $demande_fond = DB::table('demande_fonds as df')
                      ->where('df.id',$datas['demande_fonds_id'])
                      ->first();
        if ($demande_fond!=null) {
            $num_dem = $demande_fond->num_dem;
            $intitule = $demande_fond->intitule;
            $montant = strrev(wordwrap(strrev($demande_fond->montant ?? ''), 3, ' ', true));
            $observation = $demande_fond->observation;
        }
        
    }
    
?>

<!DOCTYPE html>
<html>
<head>
	<title>Demande de fonds</title>
</head>
<body>
<p>
    @if(isset($datas['subject']))
        {{ $datas['subject'] }}
    @endif
</p>
<p>
    @if(isset($num_dem)) N° : <br>&nbsp;&nbsp;<strong style="color: brown">{{ $num_dem }}</strong> @endif
</p>
<p>
    @if(isset($intitule)) Objet : <br>&nbsp;&nbsp;{{ $intitule }} @endif
</p>
<p>
    @if(isset($montant)) Montant : <br>&nbsp;&nbsp;<strong>{{ $montant }}</strong> @endif
</p>
<p>
    @if(isset($observation)) Observation : <br>&nbsp;&nbsp;{{ $observation }} @endif
</p>

    <br/>
    <br/>
    @if(isset($datas['link']))
    
        <p>Cliquez ici {{ $datas['link'] ?? '' }} pour plus de détails.</p>


    @endif
</body>
</html>