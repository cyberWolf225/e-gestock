<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\ValiderRequisition;
use App\Models\Demande;
use App\Models\Livraison;
use App\Models\Mouvement;
use App\Models\MagasinStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Session;

class AnnulationController extends Controller
{
    public function livraisonRequisition($requisitions_id_crypt){

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($requisitions_id_crypt);
        } catch (DecryptException $e) {
            //
        }
        //  SELECT * FROM `requisitions` WHERE `num_bc` LIKE '24/9136BCI1031';
        //  SELECT * FROM `demandes` as d WHERE d.`requisitions_id` IN (SELECT r.id FROM `requisitions` as r WHERE r.`num_bc` LIKE '24/9136BCI1031');
        //  SELECT * FROM `valider_requisitions` as vr WHERE vr.`demandes_id` IN (SELECT d.id FROM `demandes` as d WHERE d.`requisitions_id` IN (SELECT r.id FROM `requisitions` as r WHERE r.`num_bc` LIKE '24/9136BCI1031'));
        //  SELECT * FROM `livraisons` as l WHERE l.`demandes_id` IN (SELECT d.id FROM `demandes` as d WHERE d.`requisitions_id` IN (SELECT r.id FROM `requisitions` as r WHERE r.`num_bc` LIKE '24/9136BCI1031'));
        //  SELECT * FROM `mouvements` WHERE `id` IN (SELECT l.mouvements_id FROM `livraisons` as l WHERE l.`demandes_id` IN (SELECT d.id FROM `demandes` as d WHERE d.`requisitions_id` IN (SELECT r.id FROM `requisitions` as r WHERE r.`num_bc` LIKE '24/9136BCI1031')));
        //  SELECT * FROM `magasin_stocks` WHERE `id` IN (SELECT d.magasin_stocks_id FROM `demandes` as d WHERE d.`requisitions_id` IN (SELECT r.id FROM `requisitions` as r WHERE r.`num_bc` LIKE '24/9136BCI1031'));
        //  SELECT * FROM `statut_requisitions` WHERE `requisitions_id` IN (SELECT r.id FROM `requisitions` as r WHERE r.`num_bc` LIKE '24/9136BCI1031');
        //  SELECT * FROM `type_statut_requisitions` WHERE 1;


        $requisition = Requisition::findOrFail($decrypted);
        $demandes = $this->getAnnulationDemandes($requisition);
        $valider_requisitions = $this->getAnnulationValiderRequisitions($requisition);
        $livraisons = $this->getAnnulationLivraisons($requisition);
        $mouvements = $this->getAnnulationMouvements($requisition);
        $magasin_stocks = [];
        $magasin_stocks_apres_modif = [];
        

        foreach ($livraisons as $livraison) {
            $data = [
                'qte'            => 0,
                'qte_recue'      => 0,
                'montant'        => 0,
                'commentaire'    => 'Livaison annulée. Quantité : ' . $livraison->qte,
            ];

            $this->setAnnulationLivraison($livraison->id,$data); 
        }
        $tableau_mouvement = [];
        foreach ($mouvements as $mouvement) {
            $type_mouvements_libelle = 'Annulation de BCI';
            $type_mouvements_id = $this->storeTypeMouvement($type_mouvements_libelle);
            $data2 = [
                'type_mouvements_id' => $type_mouvements_id,
                'magasin_stocks_id'  => $mouvement->magasin_stocks_id,
                'profils_id'         => Session::get('profils_id'),
                'qte'                => -1 * $mouvement->qte,
                'prix_unit'          => $mouvement->prix_unit,
                'montant_ht'         => -1 * $mouvement->montant_ht,
                'taxe'               => $mouvement->taxe,
                'montant_ttc'        => -1 * $mouvement->montant_ttc,
                'date_mouvement'     => date('Y-m-d'),
            ];

            $mouvement_new = $this->setAnnulationMouvement($data2);
            $tableau_mouvement[] = $mouvement_new;

            if($mouvement_new != null){
                $magasin_stock = $this->getAnnulationMagasinStocks($mouvement_new->magasin_stocks_id,$requisition);
                $magasin_stocks[] = $magasin_stock;

                if($magasin_stock != null){
                    $data3 = [
                        'qte'            => $mouvement_new->qte + $magasin_stock->qte,
                        'montant'        => ($mouvement_new->qte + $magasin_stock->qte) * $magasin_stock->cmup
                    ];

                    $this->setAnnulationMagasinStock($magasin_stock->id,$data3);
                    $magasin_stock2 = $this->getAnnulationMagasinStocks($mouvement_new->magasin_stocks_id,$requisition);
                    $magasin_stocks_apres_modif[] = $magasin_stock2;
                }
                
            }
            
        }
        
        $type_statut_requisitions_libelle = 'Annulé (Responsable des stocks)';

        $this->storeTypeStatutRequisition($type_statut_requisitions_libelle);
        $type_statut_requisition = $this->getTypeStatutRequisition($type_statut_requisitions_libelle);

        $this->storeStatutRequisition($requisition->id,Session::get('profils_id'),$type_statut_requisition->id,$commentaire=null);

        return redirect()->route('requisitions.show', Crypt::encryptString($requisition->id))->with('success', "L'annulation du BCI a été effectuée avec succès.");

    }

    public function getAnnulationDemandes($requisition){
        return Demande::whereIn('requisitions_id', function ($query) use($requisition) {
            $query->select('id')
                  ->from('requisitions')
                  ->where('num_bc', $requisition->num_bc);
        })->get();        
    }

    public function getAnnulationValiderRequisitions($requisition){
        return ValiderRequisition::whereIn('demandes_id', function ($query) use($requisition) {
            $query->select('id')
                  ->from('demandes')
                  ->whereIn('requisitions_id', function ($subQuery) use($requisition) {
                      $subQuery->select('id')
                               ->from('requisitions')
                               ->where('num_bc', $requisition->num_bc);
                  });
        })->get();
        
    }

    public function getAnnulationLivraisons($requisition){
        return Livraison::whereIn('demandes_id', function ($query) use($requisition) {
            $query->select('id')
                  ->from('demandes')
                  ->whereIn('requisitions_id', function ($subQuery) use($requisition) {
                      $subQuery->select('id')
                               ->from('requisitions')
                               ->where('num_bc', $requisition->num_bc);
                  });
        })->get();
        
    }

    public function getAnnulationMouvements($requisition){

        return Mouvement::whereIn('id', function ($query) use($requisition) {
            $query->select('mouvements_id')
                  ->from('livraisons')
                  ->whereIn('demandes_id', function ($subQuery) use($requisition) {
                      $subQuery->select('id')
                               ->from('demandes')
                               ->whereIn('requisitions_id', function ($nestedQuery) use($requisition) {
                                   $nestedQuery->select('id')
                                               ->from('requisitions')
                                               ->where('num_bc', $requisition->num_bc);
                               });
                  });
        })->get();
        
    }
    public function getAnnulationMagasinStocks($magasin_stocks_id,$requisition){
        return MagasinStock::whereIn('id', function ($query) use($requisition) {
            $query->select('magasin_stocks_id')
                  ->from('demandes')
                  ->whereIn('requisitions_id', function ($subQuery) use($requisition) {
                      $subQuery->select('id')
                               ->from('requisitions')
                               ->where('num_bc', $requisition->num_bc);
                  });
        })
        ->where('id',$magasin_stocks_id)
        ->first();
        
    }

    public function setAnnulationLivraison($livraisons_id,$data){
        Livraison::where('id', $livraisons_id)->update($data);
    }

    public function setAnnulationMouvement($data){
        return Mouvement::create($data);
    }

    public function setAnnulationMagasinStock($magasin_stocks_id,$data){
        MagasinStock::where('id', $magasin_stocks_id)->update($data);
    }

    
}
