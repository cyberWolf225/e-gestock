<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FournisseurDemandeCotationController extends Controller
{
    private $controller1;
    public function __construct(Controller1 $controller1)
    {
        $this->middleware('auth');
        $this->controller1 = $controller1;
    }
    public function store($request){
        foreach ($request->organisations_id as $key => $organisations_id) {
            $fournisseur_demande_cotations_id = null;

            if(isset($request->fournisseur_demande_cotations_id[$key])){
                $fournisseur_demande_cotations_id = $request->fournisseur_demande_cotations_id[$key];
            }
            
            $data = [
                'demande_cotations_id'=>$request->demande_cotations_id,
                'organisations_id'=>$organisations_id,
                'denomination'=>$request->denomination[$key],
                'fournisseur_demande_cotations_id'=>$fournisseur_demande_cotations_id
            ];
            $this->controller1->controllProcedurestoreFournisseurDemandeCotation($data);
        }
    }
}
