<?php

namespace App\Http\Controllers;

use App\Models\MagasinStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Encryption\DecryptException;

class MagasinStockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $depots_id = null;

        if (Session::has('profils_id')) { 

            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks','Gestionnaire des achats','Responsable des achats'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $depots_id = $infoUserConnect->depots_id;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }


        if ($type_profils_name === 'Administrateur fonctionnel' or $type_profils_name === 'Administrateur technique') {

            $magasin_stocks = $this->getMagasinStocksByDepot();

        }else{
            
            $magasin_stocks = $this->getMagasinStocksByDepot($depots_id);
            
        }
        
        foreach ($magasin_stocks as $magasin_stock) {
            $this->correctionInventaireArticle($magasin_stock->id);
        }

        if ($type_profils_name === 'Administrateur fonctionnel' or $type_profils_name === 'Administrateur technique') {

            $magasin_stocks = $this->getMagasinStocksByDepot();

        }else{
            
            $magasin_stocks = $this->getMagasinStocksByDepot($depots_id);
            
        }

        return view('magasin_stocks.index',[
            'magasin_stocks'=>$magasin_stocks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MagasinStock  $magasinStock
     * @return \Illuminate\Http\Response
     */
    public function show($magasinStock, Request $request)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($magasinStock);
        } catch (DecryptException $e) {
            //
        }

        $magasinStock = MagasinStock::findOrFail($decrypted);

        if (Session::has('profils_id')) { 

            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks','Gestionnaire des achats','Responsable des achats'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $depots_id = $infoUserConnect->depots_id;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $mouvement = $this->getMagasinStockById($magasinStock->id);
        
        $magasin_stocks = $this->getgetMagasinStocksById($magasinStock->id);

        //dd($magasin_stocks);

        $qte_stock_total = 0;

        $mouvement_qte = $this->getQteMagasinStock($magasinStock->id);

        if($mouvement_qte != null){
            $qte_stock_total = (int) $mouvement_qte->qte_stock;
        }
        //dd($magasin_stocks);
        return view('magasin_stocks.show',[
            'mouvement'=>$mouvement,
            'magasin_stocks'=>$magasin_stocks,
            'type_profils_name'=>$type_profils_name,
            'qte_stock_total'=>$qte_stock_total
        ]);
    }

    public function shows($magasinStock)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($magasinStock);
        } catch (DecryptException $e) {
            //
        }

        $magasinStock = MagasinStock::findOrFail($decrypted);

        if (Session::has('profils_id')) {

            $etape = "index";
            $type_profils_name = null;
            $type_profil = $this->getTypeProfilByProfilId(Session::get('profils_id'));

            if($type_profil != null){
                $type_profils_name = $type_profil->name;
            }
            $type_profils_lists = ['Administrateur fonctionnel','Administrateur technique','Gestionnaire des stocks','Responsable des stocks','Gestionnaire des achats','Responsable des achats'];
            
            $profil = $this->controllerAcces($etape,$type_profils_lists,$type_profils_name);

            if ($profil === null) {
                return redirect()->back()->with('error','Accès refusé');
            }

            $infoUserConnect = $this->getInfoUserConnect(Session::get('profils_id'),auth()->user()->id);
            if ($infoUserConnect != null) {
                $depots_id = $infoUserConnect->depots_id;
            }

        }else{
            return redirect()->back()->with('error','Accès refusé');
        }

        $mouvement = $this->getMagasinStockById($magasinStock->id);

        $magasin_stocks = $this->getgetMagasinStocksById($magasinStock->id);

        

        return view('magasin_stocks.shows',[
            'mouvement'=>$mouvement,
            'magasin_stocks'=>$magasin_stocks
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MagasinStock  $magasinStock
     * @return \Illuminate\Http\Response
     */
    public function edit(MagasinStock $magasinStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MagasinStock  $magasinStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MagasinStock $magasinStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MagasinStock  $magasinStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(MagasinStock $magasinStock)
    {
        //
    }
}
