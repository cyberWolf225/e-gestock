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

                    <form action="{{ route('etats.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="rf"><u><b>F</b></u>amille d'article <sup style="color: red">*</sup> :</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input style="margin-top: -5px;" onkeyup="editCompte(this)" list="liste_rf"  id="rf" name="rf" class="form-control form-control-sm @error('rf') is-invalid @enderror rf" autocomplete="off" value="{{ old('rf') ?? $famille->ref_fam ?? '' }}">

                                            @error('rf')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                        <div class="col-sm-6">
                                            <input style="margin-top: -5px; background-color: #e9ecef" onfocus="this.blur()" id="df" name="df" class="form-control form-control-sm @error('df') is-invalid @enderror df" autocomplete="off" value="{{ old('df') ?? mb_strtoupper($famille->design_fam ?? '')  }}">

                                            @error('df')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="date_debut"><u><b>P</b></u>ériode du </label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input style="margin-top: -5px;" type="date" id="date_debut" name="date_debut" class="form-control form-control-sm @error('date_debut') is-invalid @enderror date_debut" autocomplete="off" value="{{ old('date_debut') ?? $date_debut ?? '' }}">

                                            @error('date_debut')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-1" style="text-align: center">
                                            <label class="label" for="date_fin" style="text-align: center">au</label>
                                        </div>

                                        <div class="col-sm-2">
                                            <input style="margin-top: -5px;" type="date" id="date_fin" name="date_fin" class="form-control form-control-sm @error('date_fin') is-invalid @enderror date_fin" autocomplete="off" value="{{ old('date_fin') ?? $date_fin ?? '' }}">

                                            @error('date_fin')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>

                                        <div class="col-sm-2">
                                            <input name="famille_crypt" value="{{ $famille_crypt ?? '' }}" style="display:"/>
                                            <button class="btn btn-dark btn-sm" type="submit" name="submit" value="soumettre">Soumettre</button>
                                            @if(count($articles) > 0)
                                                
                                                <button class="btn btn-dark btn-sm" type="submit" name="submit" value="imprimer" formtarget="_blank">Imprimer</button>

                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table id="example1" class="table table-striped bg-white data-table" style="width: 100%">
                        <thead>
                            <tr style="background-color: #c4c0c0">
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">#</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">RÉFÉRENCE ARTICLE</th>
                            <th style="vertical-align: middle; text-align:" width="90%">DÉSIGNATION ARTICLE</th>
                            <th style="vertical-align: middle; text-align:center" width="90%">CATÉGORIE</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">DATE CRÉATION</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">DATE MODIFICATION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            @foreach($articles as $article)

                                <?php 
                                    $type_articles_libelle = null;
                                    if(isset($article->type_articles_id)){
                                        $type_article = $controller1->getTypeArticleById($article->type_articles_id);

                                        if($type_article != null){

                                            if($type_article->design_type != "Consommable"){
                                                $type_articles_libelle = $type_article->design_type;
                                            }
                                        }
                                    }

                                    $created_at = null;
                                    if(isset($article->articles_created_at)){
                                        $created_at = date('d/m/Y H:i:s',strtotime($article->articles_created_at)); 
                                    }

                                    $updated_at = null;
                                    if(isset($article->articles_updated_at)){
                                        $updated_at = date('d/m/Y H:i:s',strtotime($article->articles_updated_at)); 
                                    }
                                ?>
                                <tr>
                                    <td class="td-center">{{ $i ?? '' }}</td>
                                    <td class="td-left" style="font-weight: bold">{{ $article->ref_articles ?? '' }}</td>
                                    <td class="td-left">{{ $article->design_article ?? '' }}</td>
                                    <td class="td-left">{{ $type_articles_libelle ?? '' }}</td>
                                    <td class="td-left">{{ $created_at ?? '' }}</td>
                                    <td class="td-left">{{ $updated_at ?? '' }}</td>
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
