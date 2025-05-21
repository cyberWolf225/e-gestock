<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Response;

class DocumentController extends Controller
{
    public function show($typeOperation,$exercice,$fichier){

        $url = URL::current();

        if(isset(explode('/public',$url)[1])){

            $path = str_replace("storage\public\\","public\storage\\",storage_path(str_replace("/","\\","public".explode('/public',$url)[1])));

            if($typeOperation === "perdiems"){

                $signataire_perdiems = [];
                $document = $this->getDocumentByUrl($url);
                if($document != null){
                    $signataire_perdiems = $this->getSignatairePerdiem($document->operations_id);
                }
                
                foreach ($signataire_perdiems as $signataire_perdiem) {
                    $this->ImageSignature($signataire_perdiem->mle);
                }

            }

            return Response::make(file_get_contents($path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$fichier.'"'
            ]);
        }
    }

    public function ImageSignature($mle){

        $filename = 'm'.$mle.' - Copie.png';
        $path = 'C:\wamp64\www\e-gestock\public\storage\emargements\\'.$filename;

        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/image',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
        
    }

    public function getSignatairePerdiem($perdiems_id){
        return DB::table('signataire_perdiems as sp')
        ->join('profil_fonctions as pf','pf.id','=','sp.profil_fonctions_id')
        ->join('agents as a','a.id','=','pf.agents_id')
        ->where('sp.perdiems_id',$perdiems_id)
        ->get();
    }

    public function getDocumentByUrl($url){
       return DB::table('documents')->where('url',$url)->first();
    }


}
