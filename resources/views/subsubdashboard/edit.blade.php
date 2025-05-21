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
                <div class="card-header">{{ __('Modifier un élément du sous-menu --niveau 2') }}</div>

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
                    <form method="POST" action="{{ route('subsubdashboard.update') }}">
                        <datalist id="sub_dashboard">
                            @foreach($sub_dashboards as $sub_dashboard)
                            <?php 
                            
                            $ssub_sub_dashboards = DB::select("SELECT * FROM sub_sub_dashboards WHERE sub_dashboards_id = '".$sub_dashboard->id."' ORDER BY position DESC LIMIT 1");
                                
                                if ($ssub_sub_dashboards != null) {
                                    foreach ($ssub_sub_dashboards as $ssub_sub_dashboard) {
                                       $position = $ssub_sub_dashboard->position + 1;
                                    }
                                }else{
                                    $position = 1;
                                }
                            ?>
                            <option value="{{ $sub_dashboard->name }}->{{ $position }}">{{ $sub_dashboard->name }}</option>
                            @endforeach
                            
                        </datalist>

                        @csrf

                        <div class="form-group row">
                            <label for="sub_dashboard_name" class="col-md-4 col-form-label text-md-right">{{ __('Nom du Menu') }}</label>

                            <div class="col-md-6">
                                <input onkeyup="editPosition(this)" list="sub_dashboard" id="sub_dashboard_name" type="text" class="form-control @error('sub_dashboard_name') is-invalid @enderror" name="sub_dashboard_name" value="{{ old('sub_dashboard_name') ?? $sub_sub_dashboards->sub_dashboards_name }}" autocomplete="sub_dashboard_name" autofocus>

                                @error('sub_dashboard_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nom du Sous-Menu') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $sub_sub_dashboards->name }}" autocomplete="name" autofocus>
                                <input style="display: none" id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ old('id') ?? $sub_sub_dashboards->id }}" autocomplete="id" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="position" class="col-md-4 col-form-label text-md-right">{{ __('Position') }}</label>

                            <div class="col-md-2">
                                <input id="position" type="number" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') ?? $sub_sub_dashboards->position }}" autocomplete="position" autofocus>

                                @error('position')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Statut') }}</label>

                            <div class="col-md-6">
                                <select class="form-control  @error('status') is-invalid @enderror" id="status" name="status" autocomplete="new-status">
                                    <option>{{ $sub_sub_dashboards->status }}</option>
                                    <option>Activer</option>
                                    <option>Désactiver</option>
                                    
                                </select>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="link" class="col-md-4 col-form-label text-md-right">{{ __('Lien du Menu') }}</label>

                            <div class="col-md-6">
                                <input id="link" type="link" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') ?? $sub_sub_dashboards->link }}" autocomplete="link">

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
                                    {{ __('Modifier') }}
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
        var sub_dashboard_name = "";
        var position = "";
    
    const v=document.getElementById('sub_dashboard_name').value;
    if(v != ''){
        const block = v.split('->');
        sub_dashboard_name = block[0];
        position = block[1];
    }else{
        sub_dashboard_name = "";
        position = "";
    }
    
    document.getElementById('sub_dashboard_name').value=sub_dashboard_name;
    document.getElementById('position').value=position;

}
</script>
@endsection
