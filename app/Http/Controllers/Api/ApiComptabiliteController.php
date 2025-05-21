<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiComptabiliteController extends Controller
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($paramettre)
    {
        if($paramettre != null){
            if (isset(explode('->',$paramettre)[1])) {
                $code_structure = explode('->',$paramettre)[0];
                $date_transaction = explode('->',$paramettre)[1];

                $code_structure_convert_int = (int) $code_structure;
                $error_code_structure = null;
                try {
                    $re = $code_structure - $code_structure_convert_int;
                } catch (\Throwable $th) {
                    $error_code_structure = $th->getMessage();
                }
                
                if ($error_code_structure != null) {
                    $reponse = [];
                    $status = 400;
                    $message = "Veuillez saisir un code structure valide";
                }else{ 

                    if (DateTime::createFromFormat('Y-m-d', $date_transaction) !== false) {
                        
                        
                        
                        $reponse = $this->getComptabilisationEcritures($code_structure,$date_transaction);
                        $status = 200;
                        $message = "OK";

                    }else{
                        $reponse = [];
                        $status = 400;
                        $message = "Veuillez saisir une date valide";
                    }

                    

                }

            }else{
                $reponse = [];
                $status = 400;
                $message = "Veuillez saisir un paramettre valide";
            }
        }else{
            $reponse = [];
            $status = 400;
            $message = "Veuillez saisir un paramettre valide";
            
        }

        return $this->deliver_response($status,$message,$reponse);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
