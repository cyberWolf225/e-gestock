@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Modifier un élément de menu') }}</div>

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
                    <form method="POST" action="{{ route('dashboard.update') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nom du Menu') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $dashboards->name }}" autocomplete="name" autofocus>
                                <input style="display: none" id="id" type="text" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ old('id') ?? $dashboards->id }}" autocomplete="id" autofocus>

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
                                <input id="position" type="number" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') ?? $dashboards->position }}" autocomplete="position" autofocus>

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
                                    <option>{{ $dashboards->status }}</option>
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
                                <input id="link" type="link" class="form-control @error('link') is-invalid @enderror" name="link" value="{{ old('link') ?? $dashboards->link }}" autocomplete="link">

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
@endsection
