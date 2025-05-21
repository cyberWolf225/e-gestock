<?php

namespace App\Jobs;

use App\Mail\SendPerdiem;
use App\Mail\SendMailTravaux;
use Illuminate\Bus\Queueable;
use App\Mail\EmailDemandeFond;
use App\Mail\SendDemandeAchat;
use App\Mail\SendImmobilisation;
use App\Mail\Agent\SendWelcomAgent;
use Illuminate\Support\Facades\URL;
use App\Mail\SendDemandeCotationNew;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendMailCreateRequisition;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $details; 
    public $tries = 5;
    public $timeout = 20;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (isset($this->details['demande_fonds_id'])) {

            if(isset($this->details['link'])){
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'demande_fonds_id' => $this->details['demande_fonds_id'],
                    'link' => $this->details['link'],
                ];

            }else{
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'demande_fonds_id' => $this->details['demande_fonds_id'],
                ];
            }

            $email = new EmailDemandeFond($datas);
            Mail::to($this->details['email'])->send($email);

        }
        
        if (isset($this->details['demande_achats_id'])) {

            if(isset($this->details['link'])){
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'demande_achats_id' => $this->details['demande_achats_id'],
                    'link' => $this->details['link'],
                ];
            }else{
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'demande_achats_id' => $this->details['demande_achats_id'],
                    
                ];
            }

            $email = new SendDemandeAchat($datas);
            Mail::to($this->details['email'])->send($email);
            
        }
        
        if (isset($this->details['requisitions_id'])) { 

            if(isset($this->details['link'])){
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'requisitions_id' => $this->details['requisitions_id'],
                    'link' => $this->details['link'],
                ];
            }else{
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'requisitions_id' => $this->details['requisitions_id'],
                ];
            }

            $email = new SendMailCreateRequisition($datas);
            Mail::to($this->details['email'])->send($email);


        }
        
        if (isset($this->details['agents_id'])) { 

            $datas = [
                'email' => $this->details['email'],
                'subject' => $this->details['subject'],
                'agents_id' => $this->details['agents_id'],
                'param_acces_login'=>$this->details['param_acces_login'],
                'param_acces_password'=>$this->details['param_acces_password'],
            ];
            

            $email = new SendWelcomAgent($datas);
            Mail::to($this->details['email'])->send($email);
        }
        
        if (isset($this->details['travauxes_id'])) {

            if(isset($this->details['link'])){
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'travauxes_id' => $this->details['travauxes_id'],
                    'link' => $this->details['link'],
                ];
            }else{
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'travauxes_id' => $this->details['travauxes_id'],
                ];
            }

            $email = new SendMailTravaux($datas);
            Mail::to($this->details['email'])->send($email);


        }
        
        if (isset($this->details['immobilisations_id'])) { 

            if(isset($this->details['link'])){
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'immobilisations_id' => $this->details['immobilisations_id'],
                    'link' => $this->details['link'],
                ];
            }else{
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'immobilisations_id' => $this->details['immobilisations_id'],
                ];
            }
            

            $email = new SendImmobilisation($datas);
            Mail::to($this->details['email'])->send($email);


        }
        
        if (isset($this->details['perdiems_id'])) { 

            $datas = [
                'email' => $this->details['email'],
                'subject' => $this->details['subject'],
                'perdiems_id' => $this->details['perdiems_id'],
                'link' => $this->details['link'], 
            ];

            $email = new SendPerdiem($datas);
            Mail::to($this->details['email'])->send($email);


        }
        
        if (isset($this->details['demande_cotations_id'])) {

            if(isset($this->details['link'])){
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'demande_cotations_id' => $this->details['demande_cotations_id'],
                    'link' => $this->details['link'],
                ];
            }else{
                $datas = [
                    'email' => $this->details['email'],
                    'subject' => $this->details['subject'],
                    'demande_cotations_id' => $this->details['demande_cotations_id'],
                ];
            }

            $email = new SendDemandeCotationNew($datas);
            Mail::to($this->details['email'])->send($email);
            
        }
    }
}
