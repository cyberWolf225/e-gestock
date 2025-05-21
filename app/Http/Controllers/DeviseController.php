<?php

namespace App\Http\Controllers;

use App\Models\Devise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use AmrShawky\LaravelCurrency\Facade\Currency;

class DeviseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $devises = $this->getDevises();

        $cotation_fournisseurs = $this->getCotationFournisseursDevises();

        return view('devises.create',[
            'devises'=>$devises,
            'cotation_fournisseurs'=>$cotation_fournisseurs
        ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'cotation_fournisseurs_id'=>['required','numeric'],
            'num_bc'=>['required','string'],
            'fournisseur'=>['required','string'],
            'devises_libelle'=>['required','string'],
            'devises_libelle_new'=>['required','string'],
        ]);

        if ($request->devises_libelle_new != $request->devises_libelle) {
            
            $taux_de_change = null;

            $devise = $this->getDeviseByLibelle($request->devises_libelle_new);

            $devise_old = $this->getDeviseByLibelle($request->devises_libelle);
            $now = date('Y-m-d');

            if ($devise != null && $devise_old != null) {

                try {
                    $taux_de_change = Currency::convert()
                    ->from($devise_old->code)
                    ->to($devise->code)
                    // ->withoutVerifying()
                    // ->date($now)
                    ->get();
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error',$th->getMessage());
                }

                if ($taux_de_change != null) {
                    $cotation_fournisseur = $this->getCotationFournisseursDevise($request->cotation_fournisseurs_id);

                    if ($cotation_fournisseur != null) {

                        $data_cotation_fournisseurs = [
                            'devises_id'=>$devise->id,
                            'montant_total_brut'=>$cotation_fournisseur->montant_total_brut * $taux_de_change,
                            'remise_generale'=>$cotation_fournisseur->remise_generale * $taux_de_change,
                            'montant_total_net'=>$cotation_fournisseur->montant_total_net * $taux_de_change,
                            'montant_total_ttc'=>$cotation_fournisseur->montant_total_ttc * $taux_de_change,
                            'net_a_payer'=>$cotation_fournisseur->net_a_payer * $taux_de_change,
                            'montant_acompte'=>$cotation_fournisseur->montant_acompte * $taux_de_change
                        ];
                        
                        $this->setCotationFournisseurDevise($cotation_fournisseur->id,$data_cotation_fournisseurs);

                        $detail_cotations = $this->getDetailCotationDevise($cotation_fournisseur->id);

                        foreach ($detail_cotations as $detail_cotation) {

                            $data_detail_cotations = [
                                'prix_unit'=>$detail_cotation->prix_unit * $taux_de_change,
                                'remise'=>$detail_cotation->remise * $taux_de_change,
                                'montant_ht'=>$detail_cotation->montant_ht * $taux_de_change,
                                'montant_ttc'=>$detail_cotation->montant_ttc * $taux_de_change
                            ];

                            $this->setDetailCotationDevise($detail_cotation->id,$cotation_fournisseur->id,$data_detail_cotations);

                        }

                        return redirect()->back()->with('success','Modification de devises reussie');                        

                    }else{
                        return redirect()->back()->with('error','Veuillez saisir une cotation sélectionnée');
                    }
                    
                }else{
                    return redirect()->back()->with('error','Veuillez saisir une devise valide 1');
                }
                
            }else{
                return redirect()->back()->with('error','Veuillez saisir une devise valide 2');
            }
        }else{
            return redirect()->back()->with('error','Veuillez saisir une autre devise');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function show(Devise $devise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function edit(Devise $devise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Devise $devise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Devise  $devise
     * @return \Illuminate\Http\Response
     */
    public function destroy(Devise $devise)
    {
        //
    }
}
