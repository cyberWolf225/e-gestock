<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Inventaire;
use App\Models\Agent;
use App\Models\Critere;
use App\Models\Demande;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Mouvement;
use App\Models\DemandeAchat;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use App\Models\DetailCotation;
use App\Models\DetailLivraison;
use App\Models\LivraisonValider;
use App\Models\InventaireArticle;
use App\Models\DetailDemandeAchat;
use App\Models\StatutDemandeAchat;
use App\Models\ValiderRequisition;
use Illuminate\Support\Facades\DB;
use App\Models\CotationFournisseur;
use App\Models\CritereAdjudication;
use App\Models\ValiderDemandeAchat;
use App\Models\SelectionAdjudication;
use App\Models\SignataireDemandeAchat;
use App\Models\PreselectionSoumissionnaire;
use App\Models\StatutSignataireDemandeAchat;

class IntegrationInventaire extends Controller
{
    public function create(){

        $inventaires_en_masses = DB::select("SELECT * FROM inventaires_en_masse");

        $inventaires_id = 5;
        $profils_id = 2293;
        $ref_magasin = 11;

        foreach ($inventaires_en_masses as $inventaires_en_masse) {
            
            $articles = DB::select("SELECT * FROM articles WHERE ref_articles = '".$inventaires_en_masse->ref_articles."' ");

            foreach ($articles as $article) {

                $qte_stock_total = 0;

                $magasin_stocks_id = $this->getMagasinStock($ref_magasin,$article->ref_articles);

                $mouvement_qte = $this->getQteMagasinStock($magasin_stocks_id);

                if($mouvement_qte != null){
                    $qte_stock_total = (int) $mouvement_qte->qte_stock;
                }

                //$qte_theo = $inventaires_en_masse->qte;
                $qte_theo = $qte_stock_total;
                $qte_phys = $inventaires_en_masse->qte;
                //$ecart = $qte_theo - $qte_phys;
                $ecart = $qte_phys - $qte_theo;
                $justificatif = null;
                $flag_valide = 1;
                $flag_integre = 1;
                $mouvements_id = null;

                $magasin_stocks_id = $this->getMagasinStockIntegrationEnMasse($ref_magasin,$article->ref_articles,$inventaires_en_masse->qte,$inventaires_en_masse->pu);
                
                if ($magasin_stocks_id != null) {

                    $inventaire_articles_id = $this->storeInventaireArticle($magasin_stocks_id,$inventaires_id,$qte_theo,$qte_phys,$ecart,$justificatif,$flag_valide,$flag_integre,$profils_id,$inventaires_en_masse->pu);

                    if (isset($inventaire_articles_id)) {

                        $this->setInventaire($inventaire_articles_id); 
                        
                        
                        //statut inventaire (Inventorié)
                        $libelle = 'Inventorié';

                        $commentaire = "Importation de l'inventaire physique par la DSI";

                        $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);

                        
                        if ($flag_integre === 1) {


                            //Type de movement

                            $libelle_mvt = 'Intégration d\'inventaire';
                            $type_mouvements_id = $this->storeTypeMouvement($libelle_mvt);
                            
                            $this->storeMagasinStock($type_mouvements_id,$magasin_stocks_id,$ecart,$qte_phys,$profils_id,$inventaire_articles_id);

                            //statut inventaire (Validé)
                            $libelle = 'Validé';

                            $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);

                            //statut inventaire (Intégré)
                            $libelle = 'Intégré';

                            $this->storeStatutInventaire($libelle,$inventaire_articles_id,$profils_id,$commentaire);


                        }


                    }

                }

            }

            $delete_inv = DB::select("DELETE FROM inventaires_en_masse WHERE ref_articles = '".$inventaires_en_masse->ref_articles."' ");

        }

    }

    public function EntreEnStockEnMasse($prix_unit,$montant_ht,$montant_ttc,$cotation_fournisseurs_id,$detail_livraisons_id,$profils_id_ra,$QTELIV,$TVA,$livraison_valider){                           
            //recupération de la quantité coté

            //Entrée en stock

                //Type de movement
                $libelle_mvt = 'Entrée en stock';

                $type_mouvements_id = $this->getTypeMouvement($libelle_mvt);


                //determiner la demande d'achats
                try {

                    $demande_achats_id = CotationFournisseur::where('id', $cotation_fournisseurs_id)
                    ->first()
                    ->demande_achats_id;

                } catch (\Throwable $th) {
                    //throw $th;
                }
                    
                // fin determiner la demande d'achats

                //determiner le magasin
                    $ref_magasin = $this->getMagasin($demande_achats_id);
                //fin magasin


                // determiner l'articles
                    $ref_articles = null;
                    $date2 = null;

                    $livraison_valid = $this->getLivraisonValider($detail_livraisons_id);
                    

                    if ($livraison_valid!=null) {

                        $ref_articles = $livraison_valid->ref_articles;
                        $livraison_commandes_id = $livraison_valid->livraison_commandes_id;
                        $date2 = date("Y-m-d", strtotime($livraison_valid->created_at)) ;
                        $date2 = new DateTime($date2);
                        $date_clone2 = $date2;
                        
                    }        

                // fin determiner l'article

                // determiner le magasin_stocks
                    $magasin_stocks_id = null;

                    if ($ref_magasin!=null && $ref_articles) {

                        $magasin_stocks_id = $this->getMagasinStock($ref_magasin,$ref_articles);

                    }
                //fin determiner le magasin_stocks

                // enregistrer le mouvement d'article

                    if (isset($mouvements_id)) {

                        unset($mouvements_id);

                    }

                    try {
                        $taux_de_change = CotationFournisseur::where('id', $cotation_fournisseurs_id)
                        ->first()
                        ->taux_de_change;
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    $mouvements_id = $this->storeMouvement($type_mouvements_id,$magasin_stocks_id,$profils_id_ra,$QTELIV,$prix_unit,$montant_ht,$TVA,$montant_ttc,$taux_de_change);

                    if (isset($mouvements_id)) {
                        
                        if ($livraison_valider!=null) {
                            
                            $this->setLivraisonValider($livraison_valider->id,$mouvements_id);
                            
                        }


                        // récupération du montant du stock actuel
                            $montant_apres_stock = $this->getMontantMagasinStock($magasin_stocks_id);
                        //

                        // récupération de la quantité du stock actuel
                            $mouvements_qte = $this->getQteMagasinStock($magasin_stocks_id);
                            

                            // Quantité après eventuel stockage
                            if ($mouvements_qte!=null) {
                                $qte_apres_stockage = $mouvements_qte->qte_stock;
                                $qte_stock = $qte_apres_stockage;
                            } else {
                                $qte_apres_stockage = 1;
                                $qte_stock = 0;
                            }
                        //

                        //
                            if (isset($qte_apres_stockage) && isset($montant_apres_stock)) {
                                $cmup = $montant_apres_stock / $qte_apres_stockage;
                                $montant_stock = $cmup * $qte_stock;
                            }

                            $this->setMagasinStock($magasin_stocks_id,$cmup,$qte_stock,$montant_stock);
                            
                        //
                        

                        // stock_alert, stock_securite, stock_mini

                            //delai de livraison de cet article
                            //determiner la derniere livraison de cet article
                            $date1 = null;

                            $livraison_valid = $this->getLivraisonValider2($detail_livraisons_id,$ref_articles,$livraison_commandes_id);
                            
                        
                            if ($livraison_valid!=null) {
                                $date1 = date("Y-m-d", strtotime($livraison_valid->created_at)) ;

                                $date1 = new DateTime($date1);
                            }

                            if ($date2!=null && $date1!=null) {

                                // $date1 = new DateTime($date1);
                                // $date2 = new DateTime($date2);
                                $diff = $date2->diff($date1)->format("%a");


                                $delai_livraison = number_format((($diff/30)/3), 2) ;
                            } else {
                                $delai_livraison = 1;
                            }

                            //


                            // determiner la consommation moyenne du trimestre ecoulé

                            $date0 = $date_clone2->sub(new DateInterval('P3M'))->format('Y-m-d'); // P1D means a period of 1 day

                            $date3 = $date_clone2->add(new DateInterval('P3M'))->format('Y-m-d');
                            
                            $mouvement = $this->getMouvement($magasin_stocks_id,$date3);                  

                            if ($mouvement!=null) {
                                $moy_requisition = $mouvement->qte_sortie;
                            } else {
                                $moy_requisition = 0;
                            }
                    

                            //

                            // determiner le retard de livraison

                            //date de livraison prévue

                            $date_livraison_prevue = null;

                            $commande = $this->getCommande($demande_achats_id);

                            if ($commande != null) {
                                $date_livraison_prevue = $commande->date_livraison_prevue;
                            }

                            $date_livraison_prevue = new DateTime($date_livraison_prevue);

                            $diff_retard = $date2->diff($date_livraison_prevue)->format("%a");

                            $retard_livraison = number_format((($diff_retard/30)/3), 2);


                            //

                            //

                            $stock_mini = $delai_livraison * $moy_requisition;
                            $stock_securite = $retard_livraison * $moy_requisition;
                            $stock_alert = $stock_securite + $stock_mini;

                            $this->setMagasinStock2($magasin_stocks_id,$stock_mini,$stock_securite,$stock_alert);

                           
                        //
                    }
                    

                //

                
            //fin entrée en stock

        
    }

    /*
    Tables impactée : 
        - `magasin_stocks`.`cmup`.qte.montant.ref_articles
        - `mouvements`.`prix_unit`.qte.montant_ht.montant_ttc.magasin_stocks_id 
        - `inventaire_articles`.`cmup_inventaire`.qte_phys.montant_inventaire
        - `demandes`.`prixu`.qte.montant.magasin_stocks_id
        - `valider_requisitions`.`prixu`.qte.montant.demandes_id
        - `livraisons`.`prixu`.qte.montant.demandes_id
    */
    public function storePu(){
        $updated_at = date('Y-m-d H:i:s');
        $import_prix_unitaires = DB::table('import_prix_unitaires as ipu')->get();
        foreach ($import_prix_unitaires as $import_prix_unitaire) {

            $ref_articles = (int)$import_prix_unitaire->ref_articles;

            $magasin_stock = DB::table('magasin_stocks as ms')
            ->where('ms.ref_articles',$ref_articles)
            ->first();

            if($magasin_stock != null){

                $cmup = (int)$import_prix_unitaire->pu;
                $montant_ms = $cmup * $magasin_stock->qte;

                MagasinStock::where('id',$magasin_stock->id)->update([
                    'cmup'=>$cmup,
                    'montant'=>$montant_ms,
                    'updated_at'=>$updated_at
                ]);

                $mouvements = DB::table('mouvements as m')
                ->where('m.magasin_stocks_id',$magasin_stock->id)
                ->get();

                foreach($mouvements as $mouvement){
                    $montant_ht_mvt = $cmup * $mouvement->qte;
                    Mouvement::where('id',$mouvement->id)->update([
                                        'prix_unit'=>$cmup,
                                        'montant_ht'=>$montant_ht_mvt,
                                        'montant_ttc'=>$montant_ht_mvt,
                                        'updated_at'=>$updated_at
                                    ]);
                }

                $demandes = DB::table('demandes as d')
                ->where('d.magasin_stocks_id',$magasin_stock->id)
                ->get();
                foreach($demandes as $demande){
                    $montant_d = $cmup * $demande->qte;
                    Demande::where('id',$demande->id)->update([
                                        'prixu'=>$cmup,
                                        'montant'=>$montant_d,
                                        'updated_at'=>$updated_at
                                    ]);

                    $valider_requisitions = DB::table('valider_requisitions as vr')
                        ->where('vr.demandes_id',$demande->id)
                        ->get();
                    foreach($valider_requisitions as $valider_requisition){
                        $montant_vr = $cmup * $valider_requisition->qte;
                        ValiderRequisition::where('id',$valider_requisition->id)->update([
                                            'prixu'=>$cmup,
                                            'montant'=>$montant_vr,
                                            'updated_at'=>$updated_at
                                        ]);
                    }

                    $livraisons = DB::table('livraisons as l')
                        ->where('l.demandes_id',$demande->id)
                        ->get();
                    foreach($livraisons as $livraison){
                        $montant_l = $cmup * $livraison->qte;
                        Livraison::where('id',$livraison->id)->update([
                                            'prixu'=>$cmup,
                                            'montant'=>$montant_l,
                                            'updated_at'=>$updated_at
                                        ]);
                    }
                }

                $inventaire_articles = DB::table('inventaire_articles as ia')
                ->where('ia.magasin_stocks_id',$magasin_stock->id)
                ->get();

                foreach($inventaire_articles as $inventaire_article){
                    $montant_inventaire = $cmup * $inventaire_article->qte_phys;
                    InventaireArticle::where('id',$inventaire_article->id)->update([
                                        'cmup_inventaire'=>$cmup,
                                        'montant_inventaire'=>$montant_inventaire,
                                        'updated_at'=>$updated_at
                                    ]);
                }
            }

            DB::table('import_prix_unitaires')->where('id',$import_prix_unitaire->id)->delete();
        }

        $magasin_stocks = DB::table('magasin_stocks as ms')->get();

        foreach($magasin_stocks as $magasin_stock){
            $inventaire_articles = DB::table('inventaire_articles as ia')
                ->where('ia.magasin_stocks_id',$magasin_stock->id)
                ->get();

                foreach($inventaire_articles as $inventaire_article){
                    $montant_inventaire = $magasin_stock->cmup * $inventaire_article->qte_phys;
                    InventaireArticle::where('id',$inventaire_article->id)->update([
                                        'cmup_inventaire'=>$magasin_stock->cmup,
                                        'montant_inventaire'=>$montant_inventaire,
                                        'updated_at'=>$updated_at
                                    ]);
                }
        }

        if (count(DB::table('import_prix_unitaires')->get()) === 0) {
            ?><script>alert("Succès : Mise à jour effectuée")</script><?php
        }else{
            ?><script>alert("Echec : Mise à jour non réussie")</script><?php
        }
    }

}
