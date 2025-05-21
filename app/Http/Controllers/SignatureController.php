<?php

namespace App\Http\Controllers;

use App\Models\Perdiem;
use App\Models\Travaux;
use App\Models\DemandeFond;
use App\Models\DemandeAchat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Encryption\DecryptException;

class SignatureController extends Controller
{
    public function urlPostDocument()
    {
       return 'https://www.dkbsigns.com/API4/Api_dkbsign4/v1/Cnpssign';
    }
    public function urlGetDocument()
    {
        return 'https://www.dkbsigns.com/API4/Api_dkbsign4/include/DOCSIGN_CNPS';
    }
    public function apiKey()
    {
        return 'cnps@dkbsign';
    }
    public function url_to()
    {
        return URL::to('/');
    }
    public function headers(){
        return [
                'key'=>'authorizations',
                'type'=>'text',
                'value'=>'DH9QYgZ0VBkEAbwY3BytXNgAnVjwHYgyQNSVRrAzMAeQYwVSYLNA=='
                ];
    }
    public function signDocuments($config = [])
    {
        
        $params = [
            'Key_Api' => $config['apiKey'],
            'Id_cl' => $config['Id_cl'],
            'signataire' => $config['signataire'],
            'Code_ctr' => $config['Code_ctr'],
            'ctr' => str_replace('\\','/',$config['url_fichier']),
            'nom_ctr' => $config['nom_fichier'],
            'posX' => $config['posX'],
            'posY' => $config['posY'],
            'page_sign' => $config['page_sign'] ?? 1,
            'img_signataire_png' => $config['user_signature'],
            'Largeur_img_signataire_png' => $config['lg_img_png'] ?? 50,
            'Hauteur_img_signataire_png' => $config['lng_img_png'] ?? 50,
            'initial' => $config['initial'] ?? '',
            'qrcodeyes' => $config['qrcodeyes'] ?? '',
            "posX_Imgsign" => $config['posX_Img'] ?? 130,
            "posY_Imgsign" => $config['posY_Img'] ?? 153,
        ];

        try {
            $sign = Http::withHeaders($config['headers'])->post($config['urlPostDocument'],$params);
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
        
        if ($sign->status() === 201){
            $url = $config['urlGetDocument'] .'/'. $config['nom_fichier'].'.pdf';
        }else{
            $url = false;
        }
        
        return $url;
    }
    public function proceduresignDocuments($data){
        
        $path = 'public/documents/'.$data['type_operations_libelle'].'/'.$data['reference'].'.pdf';

        $path2 = 'storage/documents/'.$data['type_operations_libelle'].'/'.$data['reference'].'.pdf';

        Storage::put($path, $data['pdf']->output());
        
        $url_fichier = asset($path);
        $public_path = str_replace("/","\\",public_path($path2));
        
        $nombre_de_signataire = count($data['signataires']);
        
        $nom_signataire = ""; 
        $Code_ctr = explode("/",$data['reference'])[1];
        $nom_fichier = explode("/",$data['reference'])[1]; 
        $initial = "";
        $qrcodeyes = "";
        $page_sign = ""; 
        $lg_img_png = 40;
        $lng_img_png = 40;
        $posX = 230; 
        $posY = 40;

        $i = 1;
        foreach ($data['signataires'] as $signataire) {

            if($nombre_de_signataire === 1){
                $posX_Img = 219; 
                $posY_Img = 150; 

                if($data['orientation'] === "Paysage"){
                    $posX_Img = 219; 
                    $posY_Img = 150; 
                }

                if($data['orientation'] === "Portrait_bon_de_commande"){
                    $posX_Img = 145; 
                    $posY_Img = 235; 
                }
            }

            if($nombre_de_signataire === 2){
                $posX_Img = 41; 
                $posY_Img = 150; 

                if($i === 2){
                    $posX_Img = 219; 
                    $posY_Img = 150; 
                }

                if($data['orientation'] === "Paysage"){
                    $posX_Img = 41; 
                    $posY_Img = 150; 

                    if($i === 2){
                        $posX_Img = 219; 
                        $posY_Img = 150; 
                    }
                }

                if($data['orientation'] === "Portrait_bon_de_commande"){
                    $posX_Img = 25; 
                    $posY_Img = 235; 

                    if($i === 2){
                        $posX_Img = 145; 
                        $posY_Img = 235; 
                    }
                }
            }

            if($nombre_de_signataire === 3){
                $posX_Img = 41; 
                $posY_Img = 150; 

                if($i === 2){
                    $posX_Img = 130; 
                    $posY_Img = 150; 
                }

                if($i === 3){
                    $posX_Img = 219; 
                    $posY_Img = 150; 
                }

                if($data['orientation'] === "Paysage"){
                    $posX_Img = 41; 
                    $posY_Img = 150; 

                    if($i === 2){
                        $posX_Img = 130; 
                        $posY_Img = 150; 
                    }

                    if($i === 3){
                        $posX_Img = 219; 
                        $posY_Img = 150; 
                    }
                }

                if($data['orientation'] === "Portrait_bon_de_commande"){
                    $posX_Img = 25; 
                    $posY_Img = 235; 

                    if($i === 2){
                        $posX_Img = 85; 
                        $posY_Img = 235; 
                    }

                    if($i === 3){
                        $posX_Img = 145; 
                        $posY_Img = 235; 
                    }
                }
            }

            $user_signature = $data['url_to'] . "/public/emargements/" . $signataire->mle;
            $copie = ' - Copie';
            $extention_img = '.png';
            $path_img = 'storage/emargements/m' . $signataire->mle . $copie . $extention_img;

            $public_path_img = str_replace("/","\\",public_path($path_img));

            if(file_exists($public_path_img)){
                $Id_cl = $signataire->mle; 

                $config = [
                    'Id_cl'=>$Id_cl,
                    'signataire'=>$nom_signataire,
                    'Code_ctr'=>$Code_ctr,
                    'url_fichier'=>$url_fichier,
                    'nom_fichier'=>$nom_fichier,
                    'initial'=>$initial,
                    'qrcodeyes'=>$qrcodeyes,
                    'page_sign'=>$page_sign,
                    'user_signature'=>$user_signature,
                    'lg_img_png'=>$lg_img_png,
                    'lng_img_png'=>$lng_img_png,
                    'posX'=>$posX,
                    'posY'=>$posY,
                    'posX_Img'=>$posX_Img,
                    'posY_Img'=>$posY_Img,
                    'apiKey'=>$data['apiKey'],
                    'urlPostDocument'=>$data['urlPostDocument'],
                    'urlGetDocument'=>$data['urlGetDocument'],
                    'headers'=>$data['headers']
                ];
    
                $documentSigne = $this->signDocuments($config);
                try {
                    $docDistant = file_get_contents($documentSigne);
                    $save = file_put_contents($public_path,$docDistant);
                } catch (\Throwable $th) {
                    dd($th->getMessage());
                }
            }

            $i++;
        }
    }
    public function viewDocumentSign($operations_id,$type_operations_libelle){

        $reference = null;

        $decrypted_type_operations_libelle = null;
        try {
            $decrypted_type_operations_libelle = Crypt::decryptString($type_operations_libelle);
        } catch (DecryptException $e) {
            //
        }

        $perdiem = null;

        if($decrypted_type_operations_libelle === "Perdiems"){
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($operations_id);
            } catch (DecryptException $e) {
                //
            }
            
            $perdiem = Perdiem::findOrFail($decrypted);

            $reference = $perdiem->num_pdm;
        }

        if($decrypted_type_operations_libelle === "bcn"){
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($operations_id);
            } catch (DecryptException $e) {
                //
            }
            
            $travaux = Travaux::findOrFail($decrypted);

            $reference = $travaux->num_bc;
        }

        if($decrypted_type_operations_libelle === "bcs"){
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($operations_id);
            } catch (DecryptException $e) {
                //
            }
            
            $demandeAchat = DemandeAchat::findOrFail($decrypted);

            $reference = $demandeAchat->num_bc;
        }

        if($decrypted_type_operations_libelle === "demande_fonds"){
            $decrypted = null;
            try {
                $decrypted = Crypt::decryptString($operations_id);
            } catch (DecryptException $e) {
                //
            }
            
            $demandeFond = DemandeFond::findOrFail($decrypted);

            $reference = $demandeFond->num_dem;
        }

        if($reference != null){
            $path = 'storage/documents/' . $decrypted_type_operations_libelle. '/'. $reference.'.pdf';

            $public_path = str_replace("/","\\",public_path($path));

            if(file_exists($public_path)){
                $path2 = 'public/documents/' . $decrypted_type_operations_libelle. '/'. $reference.'.pdf';
                
                $url_fichier = asset($path2);
                return redirect($url_fichier);
            }else{
                dd('Fichier introuvable');
            } 
        }
    }
    public function OrdreDeSignature($data){

        $controllerPerdiem = new ControllerPerdiem();
        $signature = $data['signature'];

        $public_path = $controllerPerdiem->publicPath($data);
        
        if(@file_exists($public_path) === false){
            $signature = 1; // le document n'est pas encore signé. Alors signez
        }
        
        if($signature != 0){
            
            $printController = new PrintController();

            if($data['type_operations_libelle'] === 'Perdiems'){

                $printController->printPerdiem(Crypt::encryptString($data['operations_id']));

            }

            if($data['type_operations_libelle'] === 'Commande non stockable'){

                $printController->printTravaux(Crypt::encryptString($data['operations_id']));

            }

            if($data['type_operations_libelle'] === "Demande d'achats"){

                $printController->printDemandeAchat(Crypt::encryptString($data['operations_id']));

            }

            if($data['type_operations_libelle'] === "Demande de fonds"){

                $printController->printDemandeFond(Crypt::encryptString($data['operations_id']));

            }

        }

    }


}
