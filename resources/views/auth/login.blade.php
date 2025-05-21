<?php $page_login = 1; ?>
@extends('layouts.app')

@section('content')
<style>


    .div-hearder {

  border-radius: 50% , 50%;

        color:white;  
        /* background-image: var(--degrade); */
        background: linear-gradient(180deg, #808cbc 0%, #111f54 60%);
        position: relative;
        display: block;
        background-clip: padding-box, border-box;
        background-origin: border-box;
        border: double 4px transparent;
        /* border-radius: 0.25rem; */

        box-sizing: content-box;
        border-width: 2px;
        border-style: solid;
        border-image: linear-gradient(to right bottom, #474848, #e1e1e1,#474848, #e1e1e1,#474848);
        border-image-slice: 1;
    }

    .card-style{
        -moz-box-shadow: 0 0 3px #000;
        -webkit-box-shadow: 0 0 3px #000;
        box-shadow: 0 0 3px #000;
    }

    
</style>
<div class="container">
    
    <div class="row" style="margin-top:150px;">
        <div class="col-md-7">
            <div class="card card-style">
                <div class="card-header div-hearder">
                <img src="{{ asset('images/logo.png') }}" width="35px" class="pr-3" 
                   >e-GESTOCK TEST</div>
                   
                <div class="card-body">

                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif

                    @if(Session::has('error'))
                        <div class="alert alert-danger" style="background-color: #f8d7da; color:#721c24">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    @if ($errors->has('g-recaptcha-response'))
                        <div class="alert alert-danger">
                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="input-group mb-3">
                            <div class="input-group-prepend" style="height: 30px; font-size: 11px;">
                              <span class="input-group-text" id="basic-addon1" style="height: 30px; font-size: 11px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                </svg>
                            </span>
                            </div>
                            <input autocomplete="off" onkeyup="editUsername(this)" placeholder="Identifiant" aria-label="Username" aria-describedby="basic-addon1" id="usernamec" type="text" class="form-control @error('usernamec') is-invalid @enderror" value="{{ old('usernamec') }}" required autofocus style="height: 30px; font-size: 11px;">

                            <input autocomplete="off" onfocus="this.blur()" style="display:none;" placeholder="Identifiant" aria-label="Username" aria-describedby="basic-addon1" id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required style="height: 30px; font-size: 11px;">

                            @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend" style="height: 30px; font-size: 11px;">
                              <span class="input-group-text" id="basic-addon2" style="height: 30px; font-size: 11px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                              </svg></span>
                            </div>
                            <input autocomplete="off" onkeyup="editPassword(this)" placeholder="Mot de passe" aria-label="Password" aria-describedby="basic-addon2" id="passwordc" type="password" class="form-control @error('passwordc') is-invalid @enderror" required style="height: 30px; font-size: 11px; ">

                            <input autocomplete="off" onfocus="this.blur()" style="display:none" placeholder="Mot de passe" aria-label="Password" aria-describedby="basic-addon2" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required style="height: 30px; font-size: 11px; ">

                            @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 offset-md-0">
                                <div class="form-check">
                                    <input autocomplete="off" class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember" style="font-size: 10px; ">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-1">
                            {{-- !!NoCaptcha::renderJs()!! --}}
                            {{-- {!! NoCaptcha::renderJs('fr', false, 'onloadCallback') !!}
                            {!! NoCaptcha::display() !!}--}}
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12 offset-md-0">

                                
                                <button type="submit" class="btn btn-primary" style="font-size: 10px; ">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}" style="font-size: 10px; ">
                                        {{ __('Mot de passe oublié ?') }}
                                    </a>
                                @endif
                                <img src="{{ asset('images/infographe/logo-E-GESTOCK.png') }}" width="100px" style="float:right">
                            </div>
                        </div>
                    </form>
                </div>

                

            </div>
            
        </div>
        
    </div>
</div>

@endsection

<script type="text/javascript">
    var onloadCallback = function() {
      alert("grecaptcha is ready!");
    };
</script>

<script>
    editUsername = function(a){

        const saisie=document.getElementById('usernamec').value;
        
        if(saisie != ''){

            var username = saisie ;

            var CryptoJSAesJson = {
                stringify: function (cipherParams) {
                    var j = {ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)};
                    if (cipherParams.iv) j.iv = cipherParams.iv.toString();
                    if (cipherParams.salt) j.s = cipherParams.salt.toString();
                    return JSON.stringify(j);
                },
                parse: function (jsonStr) {
                    var j = JSON.parse(jsonStr);
                    var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(j.ct)});
                    if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv)
                    if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s)
                    return cipherParams;
                }
            }
            var key = "071234567890";
            var encrypted = CryptoJS.AES.encrypt(JSON.stringify(username), key, {format: CryptoJSAesJson}).toString();
            console.log(encrypted);

            
        }else{
            var username = "";
            var encrypted = "";
        }
        
        if (username === undefined) {

            document.getElementById('username').value = "";

        }else{

            document.getElementById('username').value = encrypted;

        }
        
    }

    editPassword = function(a){

        const saisie=document.getElementById('passwordc').value;
        
        if(saisie != ''){

            var password = saisie ;

            var CryptoJSAesJson = {
                stringify: function (cipherParams) {
                    var j = {ct: cipherParams.ciphertext.toString(CryptoJS.enc.Base64)};
                    if (cipherParams.iv) j.iv = cipherParams.iv.toString();
                    if (cipherParams.salt) j.s = cipherParams.salt.toString();
                    return JSON.stringify(j);
                },
                parse: function (jsonStr) {
                    var j = JSON.parse(jsonStr);
                    var cipherParams = CryptoJS.lib.CipherParams.create({ciphertext: CryptoJS.enc.Base64.parse(j.ct)});
                    if (j.iv) cipherParams.iv = CryptoJS.enc.Hex.parse(j.iv)
                    if (j.s) cipherParams.salt = CryptoJS.enc.Hex.parse(j.s)
                    return cipherParams;
                }
            }
            var key = "071234567890";
            var encrypted = CryptoJS.AES.encrypt(JSON.stringify(password), key, {format: CryptoJSAesJson}).toString();
            console.log(encrypted);
            var decrypted = JSON.parse(CryptoJS.AES.decrypt(encrypted, key, {format: CryptoJSAesJson}).toString(CryptoJS.enc.Utf8));
            console.log("decryyepted: "+decrypted);

            
        }else{
            var password = "";
            var encrypted = "";
        }
        
        if (password === undefined) {

            document.getElementById('password').value = "";

        }else{

            document.getElementById('password').value = encrypted;

        }
        
    }
</script>
