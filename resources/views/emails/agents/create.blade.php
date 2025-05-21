
<?php
    if (isset($datas['agents_id'])) {

        $agent = DB::table('agents as a')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                ->where('tsase.libelle','Activé')
                ->where('a.id',$datas['agents_id'])
                ->first();
        if ($agent!=null) {
            $nom_prenoms = $agent->nom_prenoms;

            $agent_create = DB::table('agents as a')
                ->join('users as u','u.agents_id','=','a.id')
                ->join('agent_sections as ase','ase.agents_id','=','a.id')
                ->join('sections as s','s.id','=','ase.sections_id')
                ->join('structures as st','st.code_structure','=','s.code_structure')
                ->join('statut_agent_sections as sase','sase.agent_sections_id','=','ase.id')
                ->join('type_statut_agent_sections as tsase','tsase.id','=','sase.type_statut_agent_sections_id')
                ->where('tsase.libelle','Activé')
                ->where('a.id',$datas['agents_id'])
                ->where('u.email',$datas['email'])
                ->first();
                

        }

    }
    
?>

<!DOCTYPE html>
<html>
    <body> 
        
        <p><strong style="color: brown; font-weight:bold">{{ $datas['subject'] }}</strong></p>

        @if(isset($agent_create))
            <p>Bonjour M./Mme <strong>{{ $nom_prenoms ?? '' }},</strong></p>
            <p>&nbsp;</p>

            <p>Votre compte utilisateur e-GESTOCK a été créé</p>
            <!--<p><strong>Vos paramètres d'accès</strong></p>
            <p>Nom utilisateur : <strong>{{ '' /*$datas['param_acces_login']*/ }}</strong></p>
            <p>Mot de passe : <strong>{{ '' /*$datas['param_acces_password']*/ }}</strong></p>-->

        @else
            <p>Bonjour M./Mme,</p>
            <p>Le compte utilisateur de M./Mme <strong>{{ $nom_prenoms ?? '' }},</strong> a été créé.</p>
        @endif
        
    </body>
</html>