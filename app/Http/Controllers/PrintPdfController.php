<?php

namespace App\Http\Controllers;

use App\Agent;
//use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PrintPdfController extends Controller
{
    public function print(){

        // correct
            try {
                $membersOfTeam = Agent::all();
                $num_bc = 'BC20211';
                
                $pdf = PDF::loadView('print', $membersOfTeam);
                return $pdf->download($num_bc.'.pdf');
            } catch (\Throwable $th) {
                //throw $th;
            }
        //
        
    }
}
