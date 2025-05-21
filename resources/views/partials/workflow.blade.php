<table>
    @foreach($statut_demande_achats as $statut_demande_achat)

        <?php 


            $statut_demande_achat_last = DB::table('statut_demande_achats as sda')
                ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                ->join('profils as p','p.id','=','sda.profils_id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->where('sda.demande_achats_id',$statut_demande_achat->demande_achats_id)
                ->where('sda.id',$statut_demande_achat->statut_demande_achats_id)
                ->select('tsda.libelle','sda.created_at','sda.commentaire','a.nom_prenoms','tp.name','sda.id as statut_demande_achats_id','sda.type_statut_demande_achats_id')
                ->limit(1)
                ->first();
        if ($statut_demande_achat_last != null) {
            


            $created_at = date("d/m/Y H:i:s",strtotime($statut_demande_achat_last->created_at ?? null));
        
            if(!empty($statut_demande_achat_last->commentaire)){

            $title = "Date        : ".$created_at."\n". 
                     "Statut      : ".$statut_demande_achat_last->libelle."\n". 
                     "Auteur      : ".$statut_demande_achat_last->nom_prenoms."\n".
                     "Commentaire : ".$statut_demande_achat_last->commentaire;

            }else{

            $title = "Date         : ".$created_at."\n". 
                     "Statut       : ".$statut_demande_achat_last->libelle."\n". 
                     "Auteur      : ".$statut_demande_achat_last->nom_prenoms."\n";

            }

            $masque = 0;

            $statut_demande_achat_precedent = DB::table('statut_demande_achats as sda')
                ->join('type_statut_demande_achats as tsda','tsda.id','=','sda.type_statut_demande_achats_id')
                ->join('profils as p','p.id','=','sda.profils_id')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->join('users as u','u.id','=','p.users_id')
                ->join('agents as a','a.id','=','u.agents_id')
                ->where('sda.demande_achats_id',$statut_demande_achat->demande_achats_id)
                ->select('tsda.libelle','sda.created_at','sda.commentaire','a.nom_prenoms','tp.name','sda.id as statut_demande_achats_id','sda.type_statut_demande_achats_id')
                ->orderByDesc('sda.id')
                ->where('sda.id','<',$statut_demande_achat_last->statut_demande_achats_id)
                ->limit(1)
                ->first();

                if ($statut_demande_achat_precedent != null) {

                    if ($statut_demande_achat_precedent->type_statut_demande_achats_id === $statut_demande_achat_last->type_statut_demande_achats_id) {
                        $masque = 1;
                    }

                }

                if ($masque === 0) {
                    
        ?>
        <td>
            <span title= "{{ $title ?? '' }}" style="cursor: pointer">

                <svg style="color:#ccc; margin-top: -35px; 
                @if($statut_demande_achat_last->libelle === "Annulé (Gestionnaire des achats)" or $statut_demande_achat_last->libelle === "Rejeté (Responsable des achats)" or $statut_demande_achat_last->libelle === "Annulé (Responsable des achats)" or $statut_demande_achat_last->libelle === "Demande de cotation (Annulé)") 
                color:red; 
                @elseif($statut_demande_achat_last->libelle === "Transmis (Responsable des achats)" or $statut_demande_achat_last->libelle === "Transmis pour cotation" or $statut_demande_achat_last->libelle === "Transmis (Responsable DMP)" or $statut_demande_achat_last->libelle === "Transmis (Responsable Contrôle Budgétaire)" or $statut_demande_achat_last->libelle === "Transmis (Chef Département DCG)" or $statut_demande_achat_last->libelle === "Transmis (Responsable DCG)" or $statut_demande_achat_last->libelle === "Transmis (Directeur Général Adjoint)" or $statut_demande_achat_last->libelle === "Transmis (Directeur Général Adjoint)") 
                color:orange; 
                @elseif($statut_demande_achat_last->libelle === "Coté") 
                color:blue; 
                @else 
                color:green;  
                @endif " xmlns="http://www.w3.org/2000/svg" width="7" height="7" fill="currentColor" class="bi bi-circle-fill" viewBox="0 0 16 16">
                    <circle cx="8" cy="8" r="8"/>
                  </svg>
            </span>   
        </td>

        <td>
            <svg style="color:#ccc; margin-top: -35px; margin-left:-4px; margin-right:-4px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
            </svg>
            {{-- <svg style="color:#ccc; margin-top: -35px; margin-left:-6px; margin-right:-5px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
            </svg> --}}
            {{-- <svg style="color:#ccc; margin-top: -35px; margin-left:-6px; margin-right:-5px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
            </svg> --}}
        </td>

        <?php 
            }
        }
        ?>

    @endforeach

    {{-- @for($i = 1; $i < 10; $i++)

        <td>
            <span style="cursor: pointer">
                <svg style="color:#ccc; margin-top: -35px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                </svg>
            </span>   
        </td>

        @if($i != 9)
        <td>
            <svg style="color:#ccc; margin-top: -35px; margin-left:-5px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
            </svg>
            <svg style="color:#ccc; margin-top: -35px; margin-left:-6px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
            </svg>
            <svg style="color:#ccc; margin-top: -35px; margin-left:-6px; margin-right:-5px" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
            </svg>
        </td>
        @endif
        
    @endfor --}}
    
</table>