<?php

namespace App\Http\Controllers;

use App\Models\TauxChange;
use Illuminate\Http\Request;

class TauxChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('taux_changes.index');
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
     * @param  \App\TauxChange  $tauxChange
     * @return \Illuminate\Http\Response
     */
    public function show(TauxChange $tauxChange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TauxChange  $tauxChange
     * @return \Illuminate\Http\Response
     */
    public function edit(TauxChange $tauxChange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TauxChange  $tauxChange
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TauxChange $tauxChange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TauxChange  $tauxChange
     * @return \Illuminate\Http\Response
     */
    public function destroy(TauxChange $tauxChange)
    {
        //
    }
}
