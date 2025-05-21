<?php

namespace App\Http\Controllers;

use App\Models\PieceJointe;
use App\Models\BonLivraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PieceJointeLivraison;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PieceJointeController extends Controller
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
     * @param  \App\Models\PieceJointe  $pieceJointe
     * @return \Illuminate\Http\Response
     */
    public function show($pieceJointe)
    {

        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($pieceJointe);
        } catch (DecryptException $e) {
            //
        }

        $pieceJointe = PieceJointe::findOrFail($decrypted);

        $piece_jointe = DB::table('piece_jointes')
        ->where('id',$pieceJointe->id)
        ->first();

        if ($piece_jointe!=null) {
            
            try {

                return response()->download(storage_path('app/public/' . $piece_jointe->piece),$piece_jointe->name);
                
            } catch (\Throwable $th) {
                //throw $th;
            }
            
            // return response()->file('storage/'.$piece_jointe->piece);
        }

        return view('piece_jointes.show');
        
    }

    public function shows($bonLivraison, Request $request)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($bonLivraison);
        } catch (DecryptException $e) {
            //
        }

        $bonLivraison = BonLivraison::findOrFail($decrypted);
        
        $piece_jointe = DB::table('bon_livraisons')
        ->where('id',$bonLivraison->id)
        ->first();

        // dd($piece_jointe);

        if ($piece_jointe!=null) {
            
            try {

                return response()->download(storage_path('app/public/' . $piece_jointe->piece),$piece_jointe->name);

            } catch (\Throwable $th) {
                //throw $th;
            }
          
        }

        return view('piece_jointes.show');
        
    }

    public function pv_reception($pieceJointeLivraison, Request $request)
    {
        $decrypted = null;
        try {
            $decrypted = Crypt::decryptString($pieceJointeLivraison);
        } catch (DecryptException $e) {
            //
        }

        $pieceJointeLivraison = PieceJointeLivraison::findOrFail($decrypted);
        
        $piece_jointe = DB::table('piece_jointe_livraisons')
        ->where('id',$pieceJointeLivraison->id)
        ->first();

        // dd($piece_jointe);

        if ($piece_jointe!=null) {
            
            try {

                return response()->download(storage_path('app/public/' . $piece_jointe->piece),$piece_jointe->name);

            } catch (\Throwable $th) {
                //throw $th;
            }
          
        }

        return view('piece_jointes.show');
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PieceJointe  $pieceJointe
     * @return \Illuminate\Http\Response
     */
    public function edit(PieceJointe $pieceJointe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PieceJointe  $pieceJointe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PieceJointe $pieceJointe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PieceJointe  $pieceJointe
     * @return \Illuminate\Http\Response
     */
    public function destroy(PieceJointe $pieceJointe)
    {
        //
    }
}
