<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'Laravel') }}</title> 

  <link rel="shortcut icon" href="{{ asset('/images/logo.png') }}">

  @yield('styles')
  @yield('styles_datatable')
  <style>    
    .table-entete{
      text-align: center;
      vertical-align: middle;
    }
    .ancre-style{
      background: linear-gradient(315deg, #ffd166 0%, var(--orange-cnps) 74%);
      color:#fff;
      font-size: 11px;
      border-width: 2px;
      border-style: solid;
      border-image: linear-gradient(to right bottom, #aabcc6, #fff,#aabcc6, #fff,#aabcc6);
      border-image-slice: 1;

    }

    .entete-table{
        background: linear-gradient(315deg, #3bde71 0%, #0da050 70%);
        color:#fff;
        
        box-sizing: content-box;
        border-width: 2px;
        border-style: solid;
        border-image: linear-gradient(to right bottom, #aabcc6, #fff,#aabcc6, #fff,#aabcc6);
        border-image-slice: 1;
        font-weight: bold;
    }
      
    .icon-color{
      color: #fff;
    }

    .form-control{
      font-size: 11px;
    }

    .sigle{
            font-size: 130%;
        }
    
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

            .container-sidebar {
            /*background: url("../../images/infographe/image8.png") no-repeat;*/
            height: 250px;
            text-decoration: none;
            background-size: 100% 100%;
            
        }

        .container-infographie {
            background: url("../../images/infographe/image1.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie-bis1 {
            background: url("../../../images/infographe/image1.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie-bis2 {
            background: url("../../../../images/infographe/image1.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie-bis3 {
            background: url("../../../../../images/infographe/image1.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie-bis4 {
            background: url("../../../../../../images/infographe/image1.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie-bis5 {
            background: url("../../../../../../images/infographe/image1.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie2 {
            background: url("../../images/infographe/image2.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie3 {
            background: url("../../images/infographe/image3.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie4 {
            background: url("../../images/infographe/image4.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie5 {
            background: url("../../images/infographe/image5.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie6 {
            background: url("../../images/infographe/image6.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .container-infographie7 {
            background: url("../../images/infographe/image7.png") center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: 100% 100%;
        }

        .form-control {
            font-size: 10px;
            height: 30px;;
        }

        .btn{
            font-size: 10px;
        }

        .container{
            font-size: 10px;
        }

        .label{
          /*color:#7d7e8f;*/
          font-weight: bold;
          font-size: 12px;
          color:#033d88; 
        }

        .input-valide{
          font-weight: normal;
          color:gray;
          font-size: 10px;
        }

        .griser{
          font-weight: normal;
          color:gray;
          font-size: 10px;
          background-color:transparent;
          border-color:transparent;
        }

  </style>

  <style type="text/css">
    .loader{
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f7f9fb;
      transition: opacity 0.75s, visibility 0.75s;
    }

    .loader-hidden{
      opacity: 0;
      visibility: hidden;
    }

    .loader::after{
      content: "";
      width: 75px;
      height: 75px;
      border: 15px solid #dddddd;
      border-top-color: green;
      border-radius: 50%;
      animation: loading 0.75s ease infinite;
    }

    @keyframes loading {
      from{
        transform: rotate(0turn);
      }
      to{
        transform: rotate(1turn);
      }
    }
  </style>

  <script type="text/javascript">
    window.addEventListener("load", () => {
      const loader = document.querySelector(".loader");
      loader.classList.add("loader-hidden");

      loader.addEventListener("transitionend", () => {
        document.body.removeChild("loader");
      });
    });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

</head>

  <?php 
      $type_profils = [];

      if (isset(auth()->user()->id)) {
        $agent = DB::table('agents')
            ->join('users', 'agents.id', '=', 'users.agents_id')
            ->select('agents.*', 'users.email')
            ->where('users.id',auth()->user()->id)
            ->first();

            if ($agent!=null) {
              $nom_prenoms = $agent->nom_prenoms;
            }


        $type_profils = DB::table('type_profils as tp')
        ->join('profils as p','p.type_profils_id','=','tp.id')
        ->join('users as u','u.id','=','p.users_id')
        ->where('u.id',auth()->user()->id)
        ->where('p.flag_actif',1)
        ->whereNotIn('p.id',[Session::get('profils_id')])
        ->select('tp.name','p.id as profils_id')
        ->get();
      }
      

  ?>
  </div>
<body class="hold-transition sidebar-mini layout-fixed" onload="javascript:total()" 
>
<div class="wrapper">

  <!-- Preloader 
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('../../images/infographe/logo.png') }}" alt="cnpsLogo" height="60" width="60">
  </div>
  -->

  <div class="preloader flex-column justify-content-center align-items-center">
    <div class="loader">
      Veuillez patienter un instant, traitement en cours...
    </div>
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background-color: #033d88; font-size:11px; ">
    <!-- Left navbar links -->
    
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" style="color:#fff" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto"> 

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" style="color:#fff;  font-weight:bold" data-toggle="dropdown" href="#">
          {{-- <i class="far fa-user"></i> --}}
          
          {{ $nom_prenoms ?? '' }}
          <img style="margin-top: -10px" width="20" class="img-profile rounded-circle" src="{{ asset('images/undraw_profile.svg') }}">
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="color:#033d88;  background-color:#d4edda">

          @foreach($type_profils as $type_profil)

            <a href="/session/set/{{ $type_profil->profils_id ?? '' }}" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <svg style="color:#fbc75f" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-award-fill" viewBox="0 0 16 16">
                  <path d="m8 0 1.669.864 1.858.282.842 1.68 1.337 1.32L13.4 6l.306 1.854-1.337 1.32-.842 1.68-1.858.282L8 12l-1.669-.864-1.858-.282-.842-1.68-1.337-1.32L2.6 6l-.306-1.854 1.337-1.32.842-1.68L6.331.864 8 0z"/>
                  <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1 4 11.794z"/>
                </svg>
                &nbsp;&nbsp;
                <div class="media-body">
                  <p class="text-sm" style="color:#033d88; font-weight:bold">{{ $type_profil->name ?? '' }}</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>

          @endforeach
          
        
          <a role="button" v-pre onclick="event.preventDefault();
          document.getElementById('logout-form').submit();" href="{{ route('logout') }}" href="#" class="dropdown-item dropdown-footer">
          
          <svg title="Se déconnecter" href="{{ route('logout') }}" role="button" v-pre onclick="event.preventDefault();
            document.getElementById('logout-form').submit();"  style="color:red; cursor:pointer;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-power" viewBox="0 0 16 16">
            <path d="M7.5 1v7h1V1h-1z"/>
            <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>
            </svg> Se déconnecter</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </div>
      </li>
      
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #fff; color:#033d88; ">
    <!-- Brand Logo -->
    <a href="#" class="brand-link" style="background-color: #033d88;">
      <span class="brand-text" style="font-size: 12px; background-color: #033d88; font-weight:bold;">Date : {{ date("d/m/Y H:i:s") }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" style="background-color: #eae8e8">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex" >
        <div class="image">
          <img src="{{ asset('../../images/infographe/logo.png') }}" alt="..." width="65" onclick="document.location='{{ url('/') }}'" style="cursor: pointer">
        </div>
        <div class="info mt-1">
          <h4 class="m-1" style="color: var(--blue-cnps); font-weight:bold; font-family: 'Arial Black', 'Arial Bold', Gadget, sans-serif; font-size:20px; margin-left:-5px">CNPS</h4>
          <p style="font-size: 9px; color: var(--blue-cnps); margin-left:-45px; position:absolute"><span class="sigle">C</span>AISSE <span class="sigle">N</span>ATIONALE DE <span class="sigle">P</span>REVOYANCE <span class="sigle">S</span>OCIALE</p>
        </div>
      </div>

      <!-- SidebarSearch Form -->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          @if(Session::has('profils_id'))
            
            <?php 
            $type_profils_name_actif = null;
              try {
                $type_profils_name_actif = DB::table('profils as p')
                ->join('type_profils as tp','tp.id','=','p.type_profils_id')
                ->where('p.id',Session::get('profils_id'))
                ->first()
                ->name;
              } catch (\Throwable $th) {
                //throw $th;
              }
            ?>
            
            <span style="font-size:12px; color: #033d88; font-weight:bold; margin-left:10px; margin-bottom:10px;">
              {{ $type_profils_name_actif }} 
              <sup>
                <svg style="color: green; font-weight:bold" xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                  <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                </svg>
              </sup>
              
            </span>
            
          @endif
          
          
         
        @if (Session::has('profils_id'))
          <?php
          $dashboards = DB::table('dashboards as d')
          ->where('d.status','Activer')
          ->orderBy('d.position')
          ->whereIn('d.id',function($query){
            $query->select(DB::raw('gu.dashboards_id'))
                  ->from('groupe_users as gu')
                  ->join('type_profils as tp','tp.id','=','gu.type_profils_id')
                  ->join('profils as p','p.type_profils_id','=','tp.id')
                  ->whereRaw('gu.dashboards_id = d.id')
                  ->where('p.id',Session::get('profils_id'));
          })
          ->get();
          $i = 0;
          ?>    
          @foreach($dashboards as $dashboard)
            

              <li class="nav-item">
                <a href="#" class="nav-link ancre-style" style="color:white; ">
                  @if(isset($dashboard->name))
                    @if($dashboard->name=== 'Sécurité et privilèges')
                        <svg style="margin-top: -3px; margin-right:5px; color:#fff; " xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 0c-.69 0-1.843.265-2.928.56-1.11.3-2.229.655-2.887.87a1.54 1.54 0 0 0-1.044 1.262c-.596 4.477.787 7.795 2.465 9.99a11.777 11.777 0 0 0 2.517 2.453c.386.273.744.482 1.048.625.28.132.581.24.829.24s.548-.108.829-.24a7.159 7.159 0 0 0 1.048-.625 11.775 11.775 0 0 0 2.517-2.453c1.678-2.195 3.061-5.513 2.465-9.99a1.541 1.541 0 0 0-1.044-1.263 62.467 62.467 0 0 0-2.887-.87C9.843.266 8.69 0 8 0zm0 5a1.5 1.5 0 0 1 .5 2.915l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 0 1 8 5z"/>
                        </svg>
                    @endif
                    @if($dashboard->name=== 'Paramètres')
                          <svg style="margin-top: -3px; margin-right:5px;color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill" viewBox="0 0 16 16">
                          <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                          </svg>
                    @endif
                    @if($dashboard->name=== 'Exploitation')
                        <svg style="margin-top: -3px; margin-right:5px; color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
                        </svg>
                    @endif
                    @if($dashboard->name=== 'États de gestion')
                        <svg  style="margin-top: -3px; margin-right:5px; color:#fff" width="16" height="16" viewBox="0 0 16 16" class="bi bi-book" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" d="M1 2.828v9.923c.918-.35 2.107-.692 3.287-.81 1.094-.111 2.278-.039 3.213.492V2.687c-.654-.689-1.782-.886-3.112-.752-1.234.124-2.503.523-3.388.893zm7.5-.141v9.746c.935-.53 2.12-.603 3.213-.493 1.18.12 2.37.461 3.287.811V2.828c-.885-.37-2.154-.769-3.388-.893-1.33-.134-2.458.063-3.112.752zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                        </svg>
                    @endif
                  @endif
                  <p>
                    {{ $dashboard->name ?? '' }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php
                    $sub_dashboards = DB::table('sub_dashboards as sd')
                    ->where('sd.status','Activer')
                    ->where('sd.dashboards_id',$dashboard->id)
                    ->orderBy('sd.position')
                    ->whereIn('sd.id',function($query) use($dashboard){
                        $query->select(DB::raw('gu.sub_dashboards_id'))
                              ->from('groupe_users as gu')
                              ->join('type_profils as tp','tp.id','=','gu.type_profils_id')
                              ->join('profils as p','p.type_profils_id','=','tp.id')
                              ->whereRaw('gu.sub_dashboards_id = sd.id')
                              ->where('p.id',Session::get('profils_id'))
                              ->where('gu.dashboards_id',$dashboard->id);
                      })
                    ->get();
                  ?>
                  @foreach($sub_dashboards as $sub_dashboard)
                    @if($sub_dashboard->link != '#')

                      <li class="nav-item">
                        <a href="{{ $sub_dashboard->link ?? '#' }}"  class="nav-link" style="color:black; font-size:11px;">
                          <svg style="color: #009b47" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                            <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                          </svg>
                          <p style="color:#033d88;">{{ $sub_dashboard->name ?? '' }}</p>
                        </a>
                      </li>
                      
                    @endif

                    <?php
                      $sub_sub_dashboards = DB::table('sub_sub_dashboards as ssd')
                      ->where('ssd.status','Activer')
                      ->where('ssd.sub_dashboards_id',$sub_dashboard->id)
                      ->orderBy('ssd.position')
                      ->whereIn('ssd.id',function($query) use($dashboard,$sub_dashboard){
                        $query->select(DB::raw('gu.sub_sub_dashboards_id'))
                              ->from('groupe_users as gu')
                              ->join('type_profils as tp','tp.id','=','gu.type_profils_id')
                              ->join('profils as p','p.type_profils_id','=','tp.id')
                              ->whereRaw('gu.sub_sub_dashboards_id = ssd.id')
                              ->where('p.id',Session::get('profils_id'))
                              ->where('gu.dashboards_id',$dashboard->id)
                              ->where('gu.sub_dashboards_id',$sub_dashboard->id);
                      })
                      ->get();
                    ?>

                      @foreach($sub_sub_dashboards as $sub_sub_dashboard)
                        @if($sub_sub_dashboard->link != '#')

                          <li class="nav-item">
                            <a href="{{ $sub_sub_dashboard->link ?? '#' }}"  class="nav-link" style="color:black; font-size:11px;">
                              <svg style="color: #009b47" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                                <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                              </svg>
                              <p style="color:#033d88;">{{ $sub_sub_dashboard->name ?? '' }}</p>
                            </a>
                          </li>

                        @endif
                      @endforeach
                  @endforeach
                </ul>
              </li>
          @endforeach


        @endif


        <li class="container-sidebar" style="list-style: none">
            <div>
              <img src="{{ asset('../../images/infographe/image8.png') }}" style="height: 200px;
              text-decoration: none;
              background-size: 100% 100%;">
            </div>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper fond 
    @if(Session::has('backgroundImage'))
      {{ Session::get('backgroundImage') }}
    @else
      container-infographie container-infographie-bis1 container-infographie-bis2 container-infographie-bis3 container-infographie-bis4 container-infographie-bis5
    @endif

    ">
    <img src="{{ asset('../../images/infographe/logo-E-GESTOCK.png') }}" width="100px" style="float:right; margin-top:10px; margin-right:6px">
      <br><br>
        @yield('content')
    </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->



  <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="sweetalert2.all.min.js"></script>
  <!-- Optional: include a polyfill for ES6 Promises for IE11 -->
  <script src="//cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.js"></script>
  <script src="sweetalert2.min.js"></script>
  <link rel="stylesheet" href="sweetalert2.min.css">

</body>
@yield('javascripts')
@yield('javascripts_datatable')
</html>
