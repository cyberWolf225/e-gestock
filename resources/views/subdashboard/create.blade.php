@extends('layouts.app')

@section('content')
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Ajouter un sous-menu') }}</div>

                <div class="card-body">
                    @if(Session::has('success')) 
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    @if(Session::has('error')) 
                        <div class="alert alert-danger">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('subdashboard.store') }}">
                        <datalist id="dashboard">
                            @foreach($dashboards as $dashboard)
                            <?php 
                            
                            $sub_dashboards = DB::select("SELECT * FROM sub_dashboards WHERE dashboards_id = '".$dashboard->id."' ORDER BY position DESC LIMIT 1");
                                
                                if ($sub_dashboards != null) {
                                    foreach ($sub_dashboards as $sub_dashboard) {
                                       $position = $sub_dashboard->position + 1;
                                    }
                                }else{
                                    $position = 1;
                                }
                            ?>
                            <option value="{{ $dashboard->name }}->{{ $position }}">{{ $dashboard->name }}</option>
                            @endforeach
                            
                        </datalist>
                        @csrf

                        <div class="form-group row">
                            <label for="dashboard_name" class="col-md-4 col-form-label text-md-right">{{ __('Nom du Menu') }}</label>

                            <div class="col-md-6">
                                <input onkeyup="editPosition(this)" list="dashboard" id="dashboard_name" type="text" class="form-control @error('dashboard_name') is-invalid @enderror" name="dashboard_name" value="{{ old('dashboard_name') }}" autocomplete="dashboard_name" autofocus>

                                @error('dashboard_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="position" class="col-md-4 col-form-label text-md-right">{{ __('Position') }}</label>

                            <div class="col-md-2">
                                <input id="position" type="number" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') }}" autocomplete="position" autofocus>

                                @error('position')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nom du Sous-Menu') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                

                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Statut du Sous-Menu') }}</label>

                            <div class="col-md-6">
                                <select class="form-control  @error('status') is-invalid @enderror" id="status" name="status" autocomplete="new-status">
                                    <option value="Activer">Activer</option>
                                    <option value="Désactiver">Désactiver</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="link" class="col-md-4 col-form-label text-md-right">{{ __('Lien du Sous-Menu') }}</label>

                            <div class="col-md-6">
                                <input id="link" type="link" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') }}" autocomplete="link">

                                @error('link')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Ajouter') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
        editPosition = function(a){
        var dashboard_name = "";
        var position = "";
    
    const v=document.getElementById('dashboard_name').value;
    if(v != ''){
        const block = v.split('->');
        dashboard_name = block[0];
        position = block[1];
    }else{
        dashboard_name = "";
        position = "";
    }
    
    document.getElementById('dashboard_name').value=dashboard_name;
    document.getElementById('position').value=position;

}
</script>
@endsection
