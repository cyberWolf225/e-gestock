<?php

namespace App\Http\Controllers;

use App\Models\Mouvement;
use Illuminate\Http\Request;
use App\Models\StatutMouvement;
use App\Models\InventaireArticle;
use Illuminate\Support\Facades\DB;
use App\Models\TypeStatutMouvement;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ControllerMouvement extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function validateRequest($request){
        return $request->validate([
            'ref_articles' => ['required','string'],
            'design_article' => ['required','string'],
            'qte' => ['required','string'],
            'prixu' => ['required','string'],
            'date_entree' => ['required','date'],
        ]);
    }
    function validateRequestUpdate($request){
        return $request->validate([
            'mouvements_id' => ['required','numeric'],
            'ref_articles' => ['required','string'],
            'design_article' => ['required','string'],
            'qte' => ['required','string'],
            'prixu' => ['required','string'],
            'date_entree' => ['required','date'],
        ]);
    }
    function setMouvement($mouvements_id,$data){
        Mouvement::where('id', $mouvements_id)->update($data);
    }
    function getMouvementById($mouvements_id){
        return DB::table('mouvements as m')
        ->join('magasin_stocks as ms','ms.id','=','m.magasin_stocks_id')
        ->join('articles as a','a.ref_articles','=','ms.ref_articles')
        ->join('type_mouvements as tm','tm.id','=','m.type_mouvements_id')
        ->where('m.id',$mouvements_id)
        ->select('tm.libelle as type_mouvements_libelle','a.*','m.*')
        ->first();
    }
    function storeTypeStatutMouvement($libelle){
        $type_statut_mouvement = TypeStatutMouvement::where('libelle',$libelle)->first();
        if($type_statut_mouvement === null){
            TypeStatutMouvement::create(['libelle'=>$libelle]);
        }
    }
    function getTypeStatutMouvement($libelle){
        return TypeStatutMouvement::where('libelle',$libelle)->first();
    }
    function setLastStatutMouvement($mouvements_id){
        $statut_mouvement = StatutMouvement::where('mouvements_id',$mouvements_id)
        ->orderByDesc('id')
        ->limit(1)
        ->first();

        if ($statut_mouvement!=null) {
            StatutMouvement::where('id',$statut_mouvement->id)->update([
                'date_fin'=>date('Y-m-d'),
            ]);
        }
    }
    function storeStatutMouvement($data){ return StatutMouvement::create($data); }

    function setAuditMouvement($type_statut_mouvement_libelle,$mouvements_id,$profils_id){
        $this->storeTypeStatutMouvement($type_statut_mouvement_libelle);

            $type_statut_mouvement = $this->getTypeStatutMouvement($type_statut_mouvement_libelle);

            if($type_statut_mouvement != null){
                $data_statut = [
                    'mouvements_id'=>$mouvements_id,
                    'type_statut_mouvements_id'=>$type_statut_mouvement->id,
                    'date_debut'=>date('Y-m-d'),
                    'date_fin'=>date('Y-m-d'),
                    'profils_id'=>$profils_id,
                ];
                $this->setLastStatutMouvement($mouvements_id);
                $this->storeStatutMouvement($data_statut);
            }
    }

    function setInventaireArticleByMouvement($mouvements_id,$qte_phys,$cmup_inventaire){

        $inventaire_article = DB::table('inventaire_articles')
                                ->where('mouvements_id',$mouvements_id)
                                ->first();
        
        if($inventaire_article != null){

            $montant_inventaire = $qte_phys * $cmup_inventaire;
            $ecart = $inventaire_article->qte_theo - $qte_phys;
            $justificatif = "Modification de l'inventaire intégré";
            
            $data = [
                'qte_phys'=>$qte_phys,
                'cmup_inventaire'=>$cmup_inventaire,
                'montant_inventaire'=>$montant_inventaire,
                'ecart'=>$ecart,
                'justificatif'=>$justificatif,
            ];
            $this->setInventaireArticle($inventaire_article->id,$data);
        }

    }

    function setInventaireArticle($inventaire_articles_id,$data){
        InventaireArticle::where('id',$inventaire_articles_id)->update($data);
    }
}
