@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mt-4">
        <div class="col-4 text-center">
            <img src="{{ asset('avatar/avatar.webp') }}" width="100px">
        </div>
        <div class="col-8">
            <div class="d-flex">
                <div class="h4 mr-3 pt-2">{{ $user->name }}</div>
            </div>
            <div class="d-flex mt-3">
                <div class="mr-3"><strong>25</strong> demandes </div>
                <div class="mr-3"><strong>5</strong> demandes annulées </div>
                <div class="mr-3"><strong>10</strong> demandes encours de traitement </div>
                <div class="mr-3"><strong>10</strong> demandes traitées </div>
            </div>
            <div class="mt-3">
                <div class="font-weight-bold">{{ $user->profils[0]->type_profil->name  }}</div>
                <div class="">Fonction</div>
                <div class="">Structure</div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">Réf</th>
                    <th scope="col">Désignation</th>
                    <th scope="col">Unité</th>
                    <th scope="col">Qte</th>
                    <th scope="col">Cmup</th>
                    <th scope="col">Montant ttc</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                    <td>Sumu</td>
                    <td>@sumu</td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>@fat</td>
                    <td>Romy</td>
                    <td>@romy</td>
                  </tr>
                  <tr>
                    <th scope="row">3</th>
                    <td>Larry</td>
                    <td>the Bird</td>
                    <td>@twitter</td>
                    <td>Dze</td>
                    <td>@dze</td>
                  </tr>
                  
                </tbody>
              </table>
        </div>
    </div>
</div>
@endsection
