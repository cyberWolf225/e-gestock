
@guest
@else
@endguest

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title> 

    @yield('styles')

    <!-- Scripts -->
    @yield('scripts')
    {{--  <script src="{{ asset('js/app.js') }}" defer></script>  --}}
    {{--  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>  --}}
    {{--  <script src="{{ asset('js/jquery-3.3.1.slim.min.js') }}"></script>  --}}

    @yield('autres_scripts')
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ url('/images/logo.png') }}">

    

    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

    

   

    <style>
        

        :root{
                --blue : #eeeeee;
                --dark-blue : #eeeeee;
                --background : #081118;
                --grey : #333;
                --light-grey :#3b3b3b;
                --dark-blue2 : #172a45;
                --orange-cnps : #dc7222; 
                --vert-cnps : #009b47;
                --blue-cnps : #033d88 ;
                --jaune-cnps : #ffcd4e;
                --degrade : linear-gradient(180deg, #626b8f 0%, #033d88 80%);
            }

            .numberCircle {
            border-radius: 50%;
            /* padding: 5px; */

            background: transparent;
            border: 2px solid #333;
            color: var(--orange-cnps); 
            text-align: center;
            font-size: 10px;
        }
        
        nav ul {
            
            z-index: 10;
            list-style-type: none;
            /* margin-right: 40px; */
            margin-right: 0px;
            position: relative;
        }
        nav ul li {
            float: left;
            display: inline-block;
            background:var(--blue-cnps);
            /* margin: 0 5px; */
        }
        nav ul li a {
            color: #fff;
            padding: 9px 15px;
            
        }
        nav ul li a:hover{
            color: #fff;
            font-weight: bold;
        }

        nav ul ul li:hover{
            color: #fff;
            font-weight: bold;
            background:#e9ecef;
            cursor: pointer;
        }
    
        
        nav ul ul {
            position: absolute;
            top:30px;
            visibility: hidden;
            opacity:0;
        }
    
        nav ul li:hover > ul{
            opacity: 1;
            visibility: visible;
            transition-delay: .5s;
            height: auto;
        }
        nav ul ul li {
            position: relative;
            margin: 0px;
            width: 250px;
            float: none;
            display: list-item;
        }
        nav ul ul li a{
            line-height: 30px;
        }
        nav ul ul ul li{
            position: relative;
            top:-30px;
            left: 210px;
            font-weight: normal;
        }
        .caret{
            float: right;
            margin-top: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .color{
            background-color: var(--blue-cnps) /* dc7222*/;
            font-weight: bold;
        }

        .flex-center {
            display: flex;
            /* justify-content: center; */
            /* min-height: 100vh; */
        }


        

            /* body {
                display: flex;
                align-items : center;
                justify-content: center;
                min-height: 100vh;
            } */

            .vertical-nav {
            min-width: 17rem;
            width: 17rem;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.4s;
            
            margin-top: 50px; 
            background: var(--dark-blue);
            text-decoration: none;
            text-align: left;
            color: #fff;
            font-weight: bold;
            }

            .menu {
                overflow: hidden;
                display: block;
            }

            .menu a {
                text-decoration: none;
                text-align: left;
                color: #fff;
                font-weight: bold;
            }

            .menu-item {
                list-style: none;
                border-top: 1px solid var(--dark-blue);
                overflow: hidden;
                
            }

            .bouton {
                display: block;
                padding: 1rem 1.2rem; /* la taille de champs */
                background : var(--blue);
                color : white;
                position: relative;
            }

            .bouton::before {
                content: '';
                position: absolute;
                width: 1rem;
                height: 1rem;
                background : var(--blue);
                left: 1.5rem;
                bottom: -0.5rem;
                transform: rotate(45deg);
            }

            .bouton i {
                margin-right: 1rem;
            }

            .menu-item__sub {
                background : #fff /*var(--blue-cnps)*/; color : black;
                overflow: hidden;
                transition: max-height 0.3s; 
                max-height: 0;
            }

            .menu-item__sub a {
                display: block;
                padding: 1rem 1.6rem;
                /* color:white; */ color : black;
                font-size: 0.9rem;
                position: relative;
                 border-bottom: 1px solid #fff; /*var(--light-grey); */
            }

            .menu-item__sub a::before {
                content : '';
                position: absolute;
                left: 0;
                top:0;
                width: 0.4rem;
                height: 100%;
                background: var(--blue);
                transform: translateX(-0.4rem);
                transition: 0.3s;
                opacity: 0;
            }

            .menu-item__sub a:hover::before {
                opacity: 1;
                transform: translateX(0);
            }

            .menu-item:target .menu-item__sub {
                max-height: 100%;
            }


                        /*
        *
        * ==========================================
        * CUSTOM UTIL CLASSES
        * ==========================================
        *
        */

        .vertical-nav {
        min-width: 17rem;
        width: 17rem;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.4s;
        
        margin-top: 50px; 
        background: var(--dark-blue);
        text-decoration: none;
        text-align: left;
        color: #fff;
        font-weight: bold;
        }

        .page-content {
        width: calc(100% - 17rem);
        margin-left: 17rem;
        transition: all 0.4s;

        
        
        }

        /* for toggle behavior */

        #sidebar.active {
        margin-left: -17rem;
        }

        #content.active {
        width: 100%;
        margin: 0;
        }

        @media (max-width: 768px) {
        #sidebar {
            margin-left: -17rem;
        }
        #sidebar.active {
            margin-left: 0;
        }
        #content {
            width: 100%;
            margin: 0;
        }
        #content.active {
            margin-left: 17rem;
            width: calc(100% - 17rem);
        }
        }

        /*
        *
        * ==========================================
        * FOR DEMO PURPOSE
        * ==========================================
        *
        */

        body {
        /* background: #599fd9;
        background: -webkit-linear-gradient(to right, #599fd9, #c2e59c);
        background: linear-gradient(to right, #599fd9, #c2e59c); */
        min-height: 100vh;
        overflow-x: hidden;
        }

        .separator {
        margin: 3rem 0;
        border-bottom: 1px dashed #fff;
        }

        .text-uppercase {
        letter-spacing: 0.1em;
        }

        .text-gray {
        color: #333;
        }

        .form-control {
            font-size: 10px;
        }

        .btn{
            font-size: 10px;
        }

        .container{
            font-size: 10px;
        }

        

        .container-login {
            background: url('images/gestion-des-stocks2.jpg') center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }

        .container-sidebar {
            background: url("../../images/infographe/image8.png") no-repeat;
            height: 250px;
            text-decoration: none;
            background-size: 100% 100%;
            /* margin-top:320px;  */
            
        }

        .sigle{
            font-size: 130%;
        }


        .ancre-style{
            background: linear-gradient(315deg, #ffd166 0%, var(--orange-cnps) 74%);
            color:#fff;
            font-size: 11px;
            width: 212px;

            box-sizing: content-box;
            border-width: 2px;
            border-style: solid;
            border-image: linear-gradient(to right bottom, #474848, #fff,#474848, #fff,#474848);
            border-image-slice: 1;

        }

        .entete-table{
            background: linear-gradient(315deg, #3bde71 0%, #0da050 70%);
            color:#fff;
            
            box-sizing: content-box;
            border-width: 2px;
            border-style: solid;
            border-image: linear-gradient(to right bottom, #474848, #fff,#474848, #fff,#474848);
            border-image-slice: 1;
        }


        

        .container-infographie {
            background: url("../../images/infographe/image7.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
            /* animation: fondu 30s ease-in-out infinite both; */
            /* -webkit-animation-name: fondu;
            -webkit-animation-duration: 15s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: normal;

            -moz-animation-name: fondu;
            -moz-animation-duration: 15s;
            -moz-animation-timing-function: linear;
            -moz-animation-iteration-count: infinite;
            -moz-animation-direction: normal;

            animation-name: fondu;
            animation-duration: 15s;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
            animation-direction:normal; */

        }

        @-webkit-keyframes fondu{
            0%{background-image: url("../../images/infographe/image2.png");}
            33%{background-image: url("../../images/infographe/image3.png");}
            66%{background-image: url("../../images/infographe/image7.png");}
            

        }

        @-moz-keyframes fondu{
            0%{background-image: url("../../images/infographe/image2.png");}
            33%{background-image: url("../../images/infographe/image3.png");}
            66%{background-image: url("../../images/infographe/image7.png");}
            

        }

        @keyframes fondu{
            0%{background-image: url("../../images/infographe/image2.png");}
            33%{background-image: url("../../images/infographe/image3.png");}
            66%{background-image: url("../../images/infographe/image7.png");}
            

        }

    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
</head>
<body 

@if(isset($page_login))
    @if($page_login === 1)
        class="container-login"
    @else
        class="container-infographie"
    @endif 
@else
    class="container-infographie"
@endif
>
@guest
@else
    @if(!isset($page_auth))
        
    
    <div class="vertical-nav bg-white menu" id="sidebar" style="overflow:hidden;overflow-y:scroll">

        <div class="py-4 px-3 mb-0" style="position: fixed; z-index: 1; background:#eae8e8">
            
            <div class="media d-flex align-items-center"><img src="{{ asset('../../images/infographe/logo.png') }}" alt="..." width="65" onclick="document.location='{{ url('/') }}'" style="cursor: pointer">
            <div class="media-body" style="margin-top: 38px; margin-left:5px;">
                <h4 class="m-0" style="color: var(--blue-cnps); font-weight:bold">CNPS</h4>
            </div>
            </div>
            <div class="media-body">
            <p style="font-size: 10px; color: var(--blue-cnps);"><span class="sigle">C</span>AISSE <span class="sigle">N</span>ATIONALE DE <span class="sigle">P</span>REVOYANCE <span class="sigle">S</span>OCIALE</p>
            </div>
        </div>
        <ul class="nav flex-column mb-0" style="margin-top:150px;">
            {{-- style="margin-top:120px; position: fixed;" --}}
                <?php
                $dashboards = DB::table('dashboards as d')
                ->where('d.status','Activer')
                ->orderBy('d.position')
                ->get();
                $i = 0;
                ?>    
                @foreach($dashboards as $dashboard)  
                    <?php 
                    $i++;
                    $ancre = "";
                    if ($i==1) {$ancre="a";}elseif ($i==2) {$ancre="b";}elseif ($i==3) {$ancre="c";}elseif ($i==4) {$ancre="d";}elseif ($i==5) {$ancre="e";}elseif ($i==6) {$ancre="f";}elseif ($i==7) {$ancre="g";}elseif ($i==8) {$ancre="h";}elseif ($i==9) {$ancre="i";}elseif ($i==10) {$ancre="j";}
                    ?>
                    <li class="menu-item" id="{{ $ancre }}">
                        <a href="#{{ $ancre }}" class="btn bouton ancre-style">
                            @if(isset($dashboard->name))
                                @if($dashboard->name=== 'Sécurité et privilèges')
                                    <svg style="margin-top: -3px; margin-right:5px;color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
                                    </svg>
                                @endif
                            @endif

                            @if(isset($dashboard->name))
                                @if($dashboard->name=== 'Paramètres')
                                    <svg style="margin-top: -3px; margin-right:5px;color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                                    </svg>
                                @endif
                            @endif

                            @if(isset($dashboard->name))
                                @if($dashboard->name=== 'Exploitation')
                                    <svg style="margin-top: -3px; margin-right:5px; color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
                                    </svg>
                                @endif
                            @endif
                            {{ $dashboard->name ?? '' }}
                        </a>
                        <div class="menu-item__sub">
                            <?php
                                $sub_dashboards = DB::table('sub_dashboards as sd')
                                ->where('sd.status','Activer')
                                ->where('sd.dashboards_id',$dashboard->id)
                                ->orderBy('sd.position')
                                ->get();
                            ?>
                            @foreach($sub_dashboards as $sub_dashboard)
                                @if($sub_dashboard->link != '#')
                                    <a href="{{ $sub_dashboard->link ?? '#' }}" style="font-size: 11px;">{{ $sub_dashboard->name ?? '' }}</a>
                                @endif
                                
            
                                <?php
                                $sub_sub_dashboards = DB::table('sub_sub_dashboards as ssd')
                                ->where('ssd.status','Activer')
                                ->where('ssd.sub_dashboards_id',$sub_dashboard->id)
                                ->orderBy('ssd.position')
                                ->get();
                                ?>
                                @foreach($sub_sub_dashboards as $sub_sub_dashboard)
                                    @if($sub_sub_dashboard->link != '#')
                                        <a href="{{ $sub_sub_dashboard->link ?? '#' }}" style="font-size: 11px;">{{ $sub_sub_dashboard->name ?? '' }}</a>
                                    @endif
                                    
                                @endforeach
            
                            @endforeach
            
            
                            
                            
                        </div>
                    </li>
                @endforeach  
                <li class="container-sidebar" style="list-style: none">
                    <div>
    
                    </div>
                </li>
        </ul>
        
    </div>

    @endif

      <!-- End vertical navbar -->
@endguest
    <div class="page-content p-5" id="content">
        <!-- Toggle button -->
        
  
        @guest
            @else
        <div class="row text-white">
            <div class="col-lg-7">
                @guest
                    @else
                <nav class="navbar fixed-top navbar-expand-md navbar-light  shadow-sm color ">
                    <div class="container">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <!-- Left Side Of Navbar -->
                            @guest
                                @else
                            
                            <ul class="navbar-nav mr-auto">
                                <li style="font-size: 12px; margin-left:-80px;">
                                    <a> Date : {{ date("d/m/Y H:i:s") }}</a>
                                </li>
                            </ul>
                            

                            @endguest
                            <!-- Right Side Of Navbar -->
                            <ul class="navbar-nav ml-auto"> 
                                <!-- Authentication Links -->
                                @guest
                                    
                                @else
                                
                                    <li class="nav-item dropdown" style="margin-right: -60px;">
                                        <a style="text-decoration: none;" id="navbarDropdown" class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                            <?php $users_id = auth()->user()->id;
                                            
                                                $agent = DB::table('agents')
                                                        ->join('users', 'agents.id', '=', 'users.agents_id')
                                                        ->select('agents.*', 'users.email')
                                                        ->where('users.id',$users_id)
                                                        ->first();
                                                        
                                            ?>
                                            @if($agent!=null)
                                            <svg style="color: #fff; margin-top:-3px; margin-right:3px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                            </svg>  <strong style="color: #fff;  font-size:10px;"> 
                                                <?php 
                                                // if (isset($agent->nom_prenoms)) {
                                                //     if(strlen($agent->nom_prenoms)>17)
                                                //     {
                                                //     $agent->nom_prenoms = substr($agent->nom_prenoms, 0, 17).'.';
                                                //     }  
                                                // }
                                                       
                                                ?>
                                                {{ $agent->nom_prenoms ?? '' }}</strong>
                                                
                                                
                                                <svg title="Se déconnecter" href="{{ route('logout') }}" role="button" v-pre onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();"  style="color:#fff; cursor:pointer;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
                                                    <path d="M7.5 1v7h1V1h-1z"/>
                                                    <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>
                                                  </svg>
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            @endif
                                        </a>
                                    </li>
                                    
                                @endguest
                            </ul>
                        </div>
                    </div>
                </nav>
                @endguest
            </div>
        </div>
        @endguest

        <!-- Toggle button -->
        @guest
            @else
        <button id="sidebarCollapse" type="button" style="background: transparent; border-color:transparent; margin-top:10px; margin-left:-40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>

        <button onclick="document.location='{{ url()->previous() }}'" style="background: transparent; border-color:transparent;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
            </svg>
        </button> 

        <img src="{{ asset('../../images/infographe/logo-E-GESTOCK.png') }}" width="100px" style="float:right; margin-top:10px;">
        <br>
        <br>
        
        @endguest 
        
        @yield('content') 
    </div>

    <script>
    function onSubmit(token) {
        document.getElementById("demo-form").submit();
    }
    </script>



    <script type="text/javascript">
    $(function() {
    // Sidebar toggle behavior
    $('#sidebarCollapse').on('click', function() {
    $('#sidebar, #content').toggleClass('active');
    });
    });
    </script>


    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="sweetalert2.all.min.js"></script>
    <!-- Optional: include a polyfill for ES6 Promises for IE11 -->
    <script src="//cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
    <script src="sweetalert2.min.js"></script>
    <link rel="stylesheet" href="sweetalert2.min.css">
    

    
    
    
</body>
    @yield('javascripts')
</html>
<!-- Vertical navbar -->