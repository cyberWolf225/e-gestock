@extends('layouts.admin')

@include('etats.articles_compte_comptables.partial_header')

    <br>
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="card-header entete-table">{{ mb_strtoupper($titre) }}
                </div>
                <div class="card-body bg-white">
                    @if(Session::has('success'))
                        <div class="alert alert-success" style="background-color: #d4edda; color:#155724">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    @if(Session::has('error'))
                        <div class="alert alert-danger" style="background-color: #f8d7da; color:#721c24">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    <form action="{{ route('etats.crypt_post') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="cst"><u><b>D</b></u>épôt :</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input onkeyup="editDepot(this)" list="liste_depot" style="margin-top: -5px;" id="cst" name="cst" class="form-control form-control-sm @error('cst') is-invalid @enderror cst" autocomplete="off" value="{{ old('cst') ?? $depot->ref_depot ?? '' }}">

                                            @error('cst')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                        <div class="col-sm-8">
                                            <input style="margin-top: -5px; background-color: #e9ecef" onfocus="this.blur()" id="nst" name="nst" class="form-control form-control-sm @error('nst') is-invalid @enderror nst" autocomplete="off" value="{{ old('nst') ?? $depot->design_dep ?? '' }}">

                                            @error('nst')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="rf"><u><b>F</b></u>amille d'article <sup style="color: red">*</sup> :</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input style="margin-top: -5px;" onkeyup="editCompteStock(this)" list="liste_rf"  id="rf" name="rf" class="form-control form-control-sm @error('rf') is-invalid @enderror rf" autocomplete="off" value="{{ old('rf') ?? $famille->ref_fam ?? '' }}">

                                            @error('rf')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                        <div class="col-sm-8">
                                            <input style="margin-top: -5px; background-color: #e9ecef" onfocus="this.blur()" id="df" name="df" class="form-control form-control-sm @error('df') is-invalid @enderror df" autocomplete="off" value="{{ old('df') ?? mb_strtoupper($famille->design_fam ?? '')  }}">

                                            @error('df')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="art"><u><b>A</b></u>rticle :</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input @if(isset($articles)) @if(count($articles) === 0) onfocus="this.blur()" style="margin-top: -5px;background-color:#e9ecef" @else style="margin-top: -5px;" @endif @else style="margin-top: -5px;" @endif onkeyup="editArticle(this)" list="liste_art" id="art" name="art" class="form-control form-control-sm" autocomplete="off" value="{{ old('art') ?? $article->ref_articles ?? '' }}">
                                        </div>
                                        <div class="col-sm-8">
                                            <input style="margin-top: -5px; background-color: #e9ecef" onfocus="this.blur()" id="dart" name="dart" class="form-control form-control-sm" autocomplete="off" value="{{ old('dart') ?? $article->design_article ?? '' }}">
                                        </div>

                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="pd"><u><b>D</b></u>ate</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input style="margin-top: -5px;" type="date" id="date_stock" name="date_stock" class="form-control form-control-sm @error('date_stock') is-invalid @enderror date_stock" autocomplete="off" value="{{ old('date_stock') ?? $date_stock ?? '' }}">

                                            @error('date_stock')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-2">
                                            <button class="btn btn-dark btn-sm" type="submit" name="submit" value="soumettre">Soumettre</button>
                                            @if(count($magasin_stocks) > 0)
                                                
                                                <button class="btn btn-dark btn-sm" type="submit" name="submit" value="imprimer" formtarget="_blank">Imprimer</button>

                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <table id="example1" class="table table-striped table-bordered bg-white data-table" style="width: 100%">
                        <thead>
                            <tr style="background-color: #c4c0c0">
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">#</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">RÉF. ARTICLE</th>
                            <th style="vertical-align: middle; text-align:center; width: 40%; white-space: nowrap;">DÉSIGNATION ARTICLE</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">QTÉ EN STOCK</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">CMUP</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">MONTANT TTC</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; ?>
                            @foreach($magasin_stocks as $magasin_stock)
                                <?php 
                                    $cmup = (int) $magasin_stock->cmup;
                                    $montant = $magasin_stock->qte * $cmup;
                                ?>
                                <tr>
                                    <td class="td-center">{{ $i ?? '' }}</td>
                                    <td class="td-left" style="font-weight: bold">{{ $magasin_stock->ref_articles ?? '' }}</td>
                                    <td class="td-left">{{ $magasin_stock->design_article ?? '' }}</td>
                                    <td class="td-center">{{ strrev(wordwrap(strrev($magasin_stock->qte ?? ''), 3, ' ', true)) }}</td>
                                    <td class="td-right">{{ strrev(wordwrap(strrev($cmup ?? ''), 3, ' ', true)) }}</td>
                                    <td class="td-right">{{ strrev(wordwrap(strrev($montant ?? ''), 3, ' ', true)) }}</td>
                                </tr>

                                <?php $i++; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('etats.articles_compte_comptables.partial_footer')
