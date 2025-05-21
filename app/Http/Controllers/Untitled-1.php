/*
    {

        dd($request);

        $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Pilote AEE','Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->first();
        if($profil!=null){
            $profils_id = $profil->id;
        }else{
            return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
        }


        
    
        $requisition = DB::table('requisitions')
                            ->where('num_bc',$request->num_bc)
                            ->first();
        if ($requisition!=null) {
            $requisitions_id = $requisition->id;
        }else{
            return redirect()->back()->with('error','Demande introuvable');
        }



        $valider_requi = null;
        $libelle_type_notification = null;
        
        if (isset($request->submit)) {
            if ($request->submit==="transfert_respo_stock" or $request->submit==="valider_stock" or $request->submit==="transfert_gestionnaire_stock" or $request->submit==="annuler_respo_stock") {

                

                if ($request->submit==="transfert_respo_stock") {

                    $libelle_type_notification = "Transmission de la demande de Réquisitions au Responsable des stocks";

                    $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->where('type_profils.name', 'Pilote AEE')
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->first();
                    if($profil!=null){
                        $profils_id = $profil->id;
                    }else{
                        return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                    }

                    $libelle_fusion = 'Consolidée (Pilote AEE)';
                    $libelle = 'Transmis (Responsable des stocks)'; 

                    

                }elseif ($request->submit==="valider_stock") {

                    $libelle_type_notification = "Validation de la demande de Réquisitions";

                    $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->first();
                    if($profil!=null){
                        $profils_id = $profil->id;
                    }else{
                        return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                    }

                    $libelle_fusion = 'Consolidée (Responsable stock)';
                    $libelle = 'Validé (Responsable des stocks)';
                }elseif ($request->submit==="transfert_gestionnaire_stock") {

                    $libelle_type_notification = "Transmission de la demande de Réquisitions pour livraison";

                    $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->first();
                    if($profil!=null){
                        $profils_id = $profil->id;
                    }else{
                        return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                    }

                    $libelle_fusion = 'Consolidée (Responsable stock)';
                    $libelle = 'Soumis pour livraison';
                }elseif ($request->submit==="annuler_respo_stock") {

                    $libelle_type_notification = "Annulation de la demande de Réquisitions";

                    $profil = DB::table('profils')
                      ->join('type_profils','type_profils.id', '=', 'profils.type_profils_id')
                      ->where('profils.users_id', auth()->user()->id)
                      ->whereIn('type_profils.name', (['Responsable des stocks']))
                      ->limit(1)
                      ->select('profils.id')
                      ->where('profils.flag_actif',1)
                      ->first();
                    if($profil!=null){
                        $profils_id = $profil->id;
                    }else{
                        return redirect()->back()->with('error','Vous n\'avez pas le profil requis pour effectuer cette action');
                    }

                    $libelle_fusion = 'Consolidée (Responsable stock)';
                    $libelle = 'Annulé (Responsable des stocks)';
                }
                
    
                
                if($request->demandes_id != null){    
                    
    
                    if (count($request->demandes_id) > 0) {
                        foreach ($request->demandes_id as $item => $value) {

                            


                            $qte_validee[$item] = filter_var($request->qte_validee[$item], FILTER_SANITIZE_NUMBER_INT);
                    
                            try {
                                $qte_validee[$item] = $qte_validee[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }

                            if (gettype($qte_validee[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir des quantités d\'article entier numérique');
                            }
                            



                            $cmup[$item] = filter_var($request->cmup[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $cmup[$item] = $cmup[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir cmup entier numérique');
                            }

                            if (gettype($cmup[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir cmup entier numérique');
                            }


                            $montant[$item] = filter_var($request->montant[$item], FILTER_SANITIZE_NUMBER_INT);
                            
                            try {
                                $montant[$item] = $montant[$item] * 1;
                            } catch (\Throwable $th) {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir un montant entier numérique');
                            }

                            if (gettype($montant[$item])!='integer') {
                                return redirect()->back()->with('error','Une valeur non numérique rencontrée. Veuillez saisir un montant entier numérique');
                            }

                            if ($qte_validee[$item] === null) {
                                $qte_validee[$item] = 0;
                            }

                            /*
                            
                            //FUSIONNER
                            
                                $requisitions_fusionnable = Requisition::where('num_bc',$request->num_bc)->first();
                                if ($requisitions_fusionnable!=null) {




                                    // si la demande n'est pas rattachée à cette demande
                                    $v_requisition = Demande::where('id',$request->demandes_id[$item])
                                    ->where('requisitions_id',$requisitions_fusionnable->id)
                                    ->first();

                                    if ($v_requisition===null) {
                                        

                                        $requisitions_fusionnable_id = $requisitions_fusionnable->id;

                                        $requisitions_num_bc = $requisitions_fusionnable->num_bc;

                                        
                                        
        
                                        // recuperation de l'ancien id requisition 
                                        $requisition_id = Demande::where('id',$request->demandes_id[$item])->first()->requisitions_id;

                                        $requisition_num_bc = Requisition::where('id',$requisition_id)->first()->num_bc;

                                        $requisitions_intitule = null;

                                        $requisitions_intitule = Requisition::where('d.id',$request->demandes_id[$item])
                                        ->join('demandes as d','d.requisitions_id','=','requisitions.id')
                                        ->first()
                                        ->intitule;

        
                                        $demande_fusionnee = Demande::where('id',$request->demandes_id[$item])->update([
                                            'requisitions_id' => $requisitions_fusionnable_id,
                                            'intitule' => $requisitions_intitule,
                                        ]);

                                        $requisition_controle = Demande::where('requisitions_id',$requisition_id)->get();

                                        if (count($requisition_controle)>=0) {

                                            $type_statut_requisition = TypeStatutRequisition::where('libelle', $libelle_fusion)->first();
                                                
                                            if ($type_statut_requisition===null) {
                                                $type_statut_requisitions_id = TypeStatutRequisition::create([
                                                    'libelle'=>$libelle_fusion
                                                ])->id;
                                            } else {
                                                $type_statut_requisitions_id = $type_statut_requisition->id;
                                            }

                                            $statut_requisitions = StatutRequisition::where('requisitions_id',$requisition_id)
                                            ->orderByDesc('id')
                                            ->limit(1)
                                            ->first();

                                            if ($statut_requisitions!=null) {
                                                    StatutRequisition::where('id',$statut_requisitions->id)->update([
                                                        'date_fin'=>date('Y-m-d'),
                                                    ]);
                                            }


                                            StatutRequisition::create([
                                                'profils_id'=>$profils_id,
                                                'requisitions_id'=>$requisition_id,
                                                'type_statut_requisitions_id'=>$type_statut_requisitions_id,
                                                'date_debut'=>date('Y-m-d'),
                                                'date_fin'=>date('Y-m-d'),
                                                'commentaire'=>"Demande N°: ".$request->demandes_id[$item]." de la réquisition N°: ".$requisition_num_bc." Consolidée à la réquisition N° : ".$requisitions_num_bc,
                                            ]);



                                            // mise à jour du profil rattaché à la requisition

                                            $maj_requisition = Requisition::where('id',$requisitions_id)->first();
                                            if ($maj_requisition != null) {
                                                Requisition::where('id',$maj_requisition->id)->update([
                                                    'profils_id'=>$profils_id,
                                                ]);
                                            }



                                        }





                                        if ($demande_fusionnee!=null) {

                    

                                            // Type statut requisition
                                            $type_statut_requisition = TypeStatutRequisition::where('libelle', $libelle_fusion)->first();
                                                
                                            if ($type_statut_requisition===null) {
                                                $type_statut_requisitions_id = TypeStatutRequisition::create([
                                                    'libelle'=>$libelle_fusion
                                                ])->id;
                                            } else {
                                                $type_statut_requisitions_id = $type_statut_requisition->id;
                                            }
                            
                                            $statut_requisitions = StatutRequisition::where('requisitions_id',$requisitions_id)
                                            ->orderByDesc('id')
                                            ->limit(1)
                                            ->first();
                            
                                            if ($statut_requisitions!=null) {
                                                    StatutRequisition::where('id',$statut_requisitions->id)->update([
                                                        'date_fin'=>date('Y-m-d'),
                                                    ]);
                                            }
                            
                            
                                            StatutRequisition::create([
                                                'profils_id'=>$profils_id,
                                                'requisitions_id'=>$requisitions_id,
                                                'type_statut_requisitions_id'=>$type_statut_requisitions_id,
                                                'date_debut'=>date('Y-m-d'),
                                                'date_fin'=>date('Y-m-d'),
                                                'commentaire'=>$request->commentaire,
                                            ]);
                            
                                        }



                                    }

                                    

                                    
                                    
                                }





                            //FIN FUSIONNER
                            
                            */

                           if ($request->submit==="annuler_respo_stock") {
                                $flag_valide = false;
                           }else{
                                if (isset($request->approvalcd[$request->demandes_id[$item]])) {
                                    $flag_valide = true; 
                                }else{
                                    $flag_valide = false; 
                                }
                           }
    
                            $data = [
                                    'demandes_id' => $request->demandes_id[$item],
                                    'qte' => $qte_validee[$item],
                                    'profils_id' => $profils_id,
                                    'prixu' => $cmup[$item],
                                    'montant' => $montant[$item],
                                    'flag_valide' => $flag_valide,
                            ];

                            $dem_verif = Demande::where('id', $request->demandes_id[$item])->first();
                            if ($dem_verif!=null) {
                                $montant_demande[$item] = $cmup[$item] * $dem_verif->qte;
                            }else{
                                $montant_demande[$item] = 0;
                            }

                            $data_demande = [
                                'prixu' => $cmup[$item],
                                'montant' => $montant_demande[$item],
                            ];

                            

                            $valider_requisition = ValiderRequisition::where('demandes_id', $request->demandes_id[$item])
                            ->first();
                            if ($valider_requisition === null) {
                                $valider_requi = ValiderRequisition::create($data);

                                Demande::where('id', $request->demandes_id[$item])->update($data_demande);

                            } else {

                                //préciser laquelle des validations mettre à jour

                                    //pilote
                                    if ($request->submit==="transfert_respo_stock") {

                                        $valider_req = ValiderRequisition::where('demandes_id', $request->demandes_id[$item])
                                        ->join('profils as p','p.id','=','valider_requisitions.profils_id')
                                        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                        ->where('tp.name','Pilote AEE')
                                        ->select('valider_requisitions.id')
                                        ->first();
                                        if ($valider_req!=null) {
                                            $valider_requi = ValiderRequisition::where('id', $valider_req->id)->update($data);
                                        }else{
                                            $valider_requi = ValiderRequisition::create($data);
                                        }

                                    }


                                    //responsable des stocks
                                    if ($request->submit==="valider_stock" or $request->submit==="transfert_gestionnaire_stock" or $request->submit==="annuler_respo_stock") {

                                        $valider_req = ValiderRequisition::where('demandes_id', $request->demandes_id[$item])
                                        ->join('profils as p','p.id','=','valider_requisitions.profils_id')
                                        ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                        ->where('tp.name','Responsable des stocks')
                                        ->select('valider_requisitions.id')
                                        ->first();
                                        if ($valider_req!=null) {
                                            $valider_requi = ValiderRequisition::where('id', $valider_req->id)->update($data);
                                        }else{
                                            $valider_requi = ValiderRequisition::create($data);
                                        }
                                        
                                    }

                                
                                
                                Demande::where('id', $request->demandes_id[$item])->update($data_demande);

                            }
                        }
        
                    }
    
    
                }
    
                if ($valider_requi != null) {

                    // Type statut requisition
                    $type_statut_requisition = TypeStatutRequisition::where('libelle', $libelle)->first();
                    
                    if ($type_statut_requisition===null) {
                        $type_statut_requisitions_id = TypeStatutRequisition::create([
                            'libelle'=>$libelle
                        ])->id;
                    } else {
                        $type_statut_requisitions_id = $type_statut_requisition->id;
                    }

                    $statut_requisitions = StatutRequisition::where('requisitions_id', $requisitions_id)
                    ->orderByDesc('id')
                    ->limit(1)
                    ->first();

                    if ($statut_requisitions!=null) {
                        StatutRequisition::where('id', $statut_requisitions->id)->update([
                                'date_fin'=>date('Y-m-d'),
                            ]);
                    }


                    StatutRequisition::create([
                        'profils_id'=>$profils_id,
                        'requisitions_id'=>$requisitions_id,
                        'type_statut_requisitions_id'=>$type_statut_requisitions_id,
                        'date_debut'=>date('Y-m-d'),
                        'date_fin'=>date('Y-m-d'),
                        'commentaire'=>$request->commentaire,
                    ]);
    
                    


                    // identification des acteurs à notifier

                    
                    $type_notification = TypeNotification::where('libelle', $libelle_type_notification)->first();
                    if ($type_notification!=null) {
                        $type_notifications_id = $type_notification->id;
                    } else {
                        $type_notifications_id = TypeNotification::create([
                                'libelle'=>$libelle_type_notification,
                            ])->id;
                    }

                    if (isset($type_notifications_id)) {
                        $notification = Notification::where('type_notifications_id', $type_notifications_id)->first();
                            
                        if ($notification!=null) {
                            $notifications_id = $notification->id;
                        } else {
                            $title = "Réquisition";
                            $subject = "Réquisition";
                            $description = "Réquisition";
                            $notifications_id = Notification::create([
                                    'title'=>$title,
                                    'subject'=>$subject,
                                    'description'=>$description,
                                    'type_notifications_id'=>$type_notifications_id,
                                ])->id;
                        }
                    }

                    // l'initiateur de la demande ($profils_id)
                        
                    if (isset($notifications_id)) {
                        $libelle_type_statut_notification = "En attente d'envoi";
                            
                        $type_statut_notification = TypeStatutNotification::where('libelle', $libelle_type_statut_notification)
                            ->first();

                        if ($type_statut_notification!=null) {
                            $type_statut_notifications_id = $type_statut_notification->id;
                        } else {
                            $type_statut_notifications_id = TypeStatutNotification::create([
                                    'libelle'=>$libelle_type_statut_notification,
                                ])->id;
                        }

                        if (isset($type_statut_notifications_id)) {
                            StatutNotification::create([
                                    'notifications_id'=>$notifications_id,
                                    'type_statut_notifications_id'=>$type_statut_notifications_id,
                                    'date_debut'=>date('Y-m-d'),
                                    'date_fin'=>date('Y-m-d'),
                                    'profils_id'=>$profils_id,
                                    'subject_id'=>$requisitions_id,

                                ]);
                        }
                    }

                    

                    if ($request->submit === "transfert_respo_stock") {
                            //le Responsable des stocks                        
                                $responsable_depots = DB::table('users as u')
                                ->join('profils as p','p.users_id','=','u.id')
                                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                                ->join('responsable_depots as rd','rd.profils_id','=','p.id')
                                ->join('statut_responsable_depots as srd','srd.responsable_depots_id','=','rd.id')
                                ->join('type_statut_responsable_depots as tsrd','tsrd.id','=','srd.type_statut_r_dep_id')
                                ->join('structures as st','st.ref_depot','=','rd.ref_depot')
                                ->join('requisitions as r','r.code_structure','=','st.code_structure')
                                ->where('tp.name','Responsable des stocks')
                                ->where('tsrd.libelle','Activé')
                                ->where('r.id',$requisitions_id)
                                ->select('p.id as profils_id')
                                ->get();
        
                                foreach ($responsable_depots as $responsable_depot) {
        
                                    if (isset($notifications_id)) {
                                        
                                        $libelle_type_statut_notification = "En attente d'envoi";
                                        
                                        $type_statut_notification = TypeStatutNotification::where('libelle',$libelle_type_statut_notification)
                                        ->first();
                    
                                        if ($type_statut_notification!=null) {
                                            $type_statut_notifications_id = $type_statut_notification->id;
                                        }else{
                                            $type_statut_notifications_id = TypeStatutNotification::create([
                                                'libelle'=>$libelle_type_statut_notification,
                                            ])->id;
                                        }
                    
                                        if (isset($type_statut_notifications_id)) {
                                            StatutNotification::create([
                                                'notifications_id'=>$notifications_id,
                                                'type_statut_notifications_id'=>$type_statut_notifications_id,
                                                'date_debut'=>date('Y-m-d'),
                                                'date_fin'=>date('Y-m-d'),
                                                'profils_id'=>$responsable_depot->profils_id,
                                                'subject_id'=>$requisitions_id,
                    
                                            ]);
                                        }
                                    }
                                }
                            //

                            //le gestionnaire des stocks
                                $gestionnaire_stocks = DB::table('users as u')
                                ->join('profils as p', 'p.users_id', '=', 'u.id')
                                ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                                ->join('agents as a', 'a.id', '=', 'u.agents_id')
                                ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                                ->join('sections as s', 's.id', '=', 'ase.sections_id')
                                ->join('structures as st', 'st.code_structure', '=', 's.code_structure')
                                ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                                ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                                ->where('tp.name', 'Gestionnaire des stocks')
                                ->where('tsas.libelle', 'Activé')
                                ->select('p.id as profils_id')
                                ->where('p.flag_actif', 1)
                                ->where('st.ref_depot', function($query) use($requisitions_id){
                                    $query->select(DB::raw('sst.ref_depot'))
                                        ->from('requisitions as rr')
                                        ->join('structures as sst','sst.code_structure','=','rr.code_structure')
                                        ->whereRaw('st.ref_depot = sst.ref_depot')
                                        ->where('rr.id',$requisitions_id);
                                })
                                ->get();
                                foreach ($gestionnaire_stocks as $gestionnaire_stock) {
        
                                    if (isset($notifications_id)) {
                                        
                                        $libelle_type_statut_notification = "En attente d'envoi";
                                        
                                        $type_statut_notification = TypeStatutNotification::where('libelle',$libelle_type_statut_notification)
                                        ->first();
                    
                                        if ($type_statut_notification!=null) {
                                            $type_statut_notifications_id = $type_statut_notification->id;
                                        }else{
                                            $type_statut_notifications_id = TypeStatutNotification::create([
                                                'libelle'=>$libelle_type_statut_notification,
                                            ])->id;
                                        }
                    
                                        if (isset($type_statut_notifications_id)) {
                                            StatutNotification::create([
                                                'notifications_id'=>$notifications_id,
                                                'type_statut_notifications_id'=>$type_statut_notifications_id,
                                                'date_debut'=>date('Y-m-d'),
                                                'date_fin'=>date('Y-m-d'),
                                                'profils_id'=>$gestionnaire_stock->profils_id,
                                                'subject_id'=>$requisitions_id,
                    
                                            ]);
                                        }
                                    }
                                }
                            //
                        
                    }

                    if ($request->submit === "valider_stock" or $request->submit === "transfert_gestionnaire_stock" or $request->submit==="annuler_respo_stock") {
                        //le pilote AEE
                            $profil_pilote_aees = DB::table('users as u')
                                    ->join('profils as p', 'p.users_id', '=', 'u.id')
                                    ->join('type_profils as tp', 'tp.id', '=', 'p.type_profils_id')
                                    ->join('agents as a', 'a.id', '=', 'u.agents_id')
                                    ->join('agent_sections as ase', 'ase.agents_id', '=', 'a.id')
                                    ->join('sections as s', 's.id', '=', 'ase.sections_id')
                                    ->join('statut_agent_sections as sas', 'sas.agent_sections_id', '=', 'ase.id')
                                    ->join('type_statut_agent_sections as tsas', 'tsas.id', '=', 'sas.type_statut_agent_sections_id')
                                    ->join('requisitions as r', 'r.code_structure', '=', 's.code_structure')
                                    ->where('tp.name', 'Pilote AEE')
                                    ->where('tsas.libelle', 'Activé')
                                    ->select('p.id as profils_id')
                                    ->where('p.flag_actif', 1)
                                    ->where('r.id',$requisitions_id)
                                    ->get();

                            foreach ($profil_pilote_aees as $profil_pilote_aee) {
                                if (isset($notifications_id)) {
                                    $libelle_type_statut_notification = "En attente d'envoi";
                                                
                                    $type_statut_notification = TypeStatutNotification::where('libelle', $libelle_type_statut_notification)
                                                ->first();
                            
                                    if ($type_statut_notification!=null) {
                                        $type_statut_notifications_id = $type_statut_notification->id;
                                    } else {
                                        $type_statut_notifications_id = TypeStatutNotification::create([
                                                        'libelle'=>$libelle_type_statut_notification,
                                                    ])->id;
                                    }
                            
                                    if (isset($type_statut_notifications_id)) {
                                        StatutNotification::create([
                                                        'notifications_id'=>$notifications_id,
                                                        'type_statut_notifications_id'=>$type_statut_notifications_id,
                                                        'date_debut'=>date('Y-m-d'),
                                                        'date_fin'=>date('Y-m-d'),
                                                        'profils_id'=>$profil_pilote_aee->profils_id,
                                                        'subject_id'=>$requisitions_id,
                            
                                                    ]);
                                    }
                                }
                            }
                        //
                    }

                    //les bénéficiaires
                        $beneficiaires = DB::table('requisitions as r')
                        ->join('demandes as d','d.requisitions_id','=','r.id')
                        ->where('r.id',$requisitions_id)
                        ->distinct('d.profils_id')
                        ->select('d.profils_id')
                        ->get();

                        foreach ($beneficiaires as $beneficiaire) {
        
                            if (isset($notifications_id)) {
                                
                                $libelle_type_statut_notification = "En attente d'envoi";
                                
                                $type_statut_notification = TypeStatutNotification::where('libelle',$libelle_type_statut_notification)
                                ->first();
            
                                if ($type_statut_notification!=null) {
                                    $type_statut_notifications_id = $type_statut_notification->id;
                                }else{
                                    $type_statut_notifications_id = TypeStatutNotification::create([
                                        'libelle'=>$libelle_type_statut_notification,
                                    ])->id;
                                }
            
                                if (isset($type_statut_notifications_id)) {
                                    StatutNotification::create([
                                        'notifications_id'=>$notifications_id,
                                        'type_statut_notifications_id'=>$type_statut_notifications_id,
                                        'date_debut'=>date('Y-m-d'),
                                        'date_fin'=>date('Y-m-d'),
                                        'profils_id'=>$beneficiaire->profils_id,
                                        'subject_id'=>$requisitions_id,
            
                                    ]);
                                }
                            }
                        }

                    //
                    //

                    
                    return redirect('/requisitions/index')->with('success',$libelle_type_notification);
                }else{

                    $message = null;

                    if ($request->submit==="annuler_respo_stock") {

                        return redirect("/requisitions/index")->with('error',$libelle_type_notification);

                    }else{

                        $message = "L'opération : ".$libelle_type_notification." a echoué";
                        return redirect()->back()->with('error',$message);

                    }
                    
                }
            }
        }

            
            

        
    }
    */