@extends('layouts.app')

@section('content')
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<div class="container" style="color:black">
    <datalist id="list_magasin">            
        @foreach($magasins as $magasin)
            <option value="{{ $magasin->ref_magasin }}->{{ $magasin->design_magasin }}->{{ $magasin->ref_depot }}->{{ $magasin->nom_structure }}">{{ $magasin->design_magasin }}</option>
        @endforeach  
    </datalist>


    <?php
    if ($magasin_stock->code_unite!=null) {
        $list_unites = DB::select("SELECT * FROM unites WHERE code_unite = '".$magasin_stock->code_unite."' ");
        foreach ($list_unites as $list_unite) {
            $unite = $list_unite->unite;
        } 
    }

    if ($magasin_stock->code_structure!=null) {
        $liste_structures = DB::select("SELECT * FROM structures WHERE code_structure = '".$magasin_stock->code_structure."' ");
        foreach ($liste_structures as $liste_structure) {
            $nom_structure = $liste_structure->nom_structure;
        } 
    }

    if ($magasin_stock->ref_taxe!=null) {
        $list_taxes = DB::select("SELECT * FROM taxes WHERE ref_taxe = '".$magasin_stock->ref_taxe."' ");
        foreach ($list_taxes as $list_taxe) {
            $taux = $list_taxe->taux;
        } 
    }

    if ($magasin_stock->ref_fam!=null) {
        $list_familles = DB::select("SELECT * FROM familles WHERE ref_fam = '".$magasin_stock->ref_fam."' ");
        foreach ($list_familles as $list_famille) {
            $design_fam = $list_famille->design_fam;
            $qte_fam = $list_famille->qte_fam;
            $compte_stock = $list_famille->compte_stock;
        } 
    }
    ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Modifier le stock d\'un article') }} 
                    <span style="float: right; margin-right: 20px;">Date : <strong>{{ date("d/m/Y") }}</strong></span>
                </div>

                <div class="card-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('magasin_stocks.update') }}">
                        @csrf
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ref_magasin">Référence Magasin<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="ref_magasin" name="ref_magasin" class="form-control @error('ref_magasin') is-invalid @enderror" name="ref_magasin" value="{{ old('ref_magasin') ?? $magasin_stock->ref_magasin ?? '' }}" autocomplete="ref_magasin" autofocus>
                                    <input onfocus="this.blur()" style="display: none" type="text" id="id" name="id" class="form-control @error('id') is-invalid @enderror" name="id" value="{{ old('id') ?? $magasin_stock->id ?? '' }}" autocomplete="id" autofocus>
                                    
                                </div>
                                @error('ref_magasin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="design_magasin">Désignation Magasin<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="design_magasin" name="design_magasin" class="form-control @error('design_magasin') is-invalid @enderror" name="design_magasin" value="{{ old('design_magasin') ?? $magasin_stock->design_magasin ?? '' }}" autocomplete="design_magasin" autofocus>
                                </div>
                                @error('libelle')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ref_depot">Référence Dépôt<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="ref_depot" name="ref_depot" class="form-control @error('ref_depot') is-invalid @enderror" name="ref_depot" value="{{ old('ref_depot') ?? $magasin_stock->ref_depot ?? '' }}" autocomplete="ref_depot" autofocus>
                                </div>
                                @error('ref_depot')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nom_structure">Structure<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="nom_structure" name="nom_structure" class="form-control @error('nom_structure') is-invalid @enderror" name="nom_structure" value="{{ old('nom_structure') ?? $nom_structure ?? '' }}" autocomplete="nom_structure" autofocus>
                                </div>
                                @error('nom_structure')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <a href="{{ route('magasins.create') }}" class="btn">
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ref_articles">Référence Article<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="ref_articles" name="ref_articles" class="form-control @error('ref_articles') is-invalid @enderror" value="{{ old('ref_articles') ?? $magasin_stock->ref_articles ?? '' }}" autocomplete="ref_articles" autofocus>
                                </div>
                                @error('ref_articles')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="design_article">Désignation Article<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="design_article" name="design_article" class="form-control @error('design_article') is-invalid @enderror"  value="{{ old('design_article') ?? $magasin_stock->design_article ?? '' }}" autocomplete="design_article" autofocus>
                                </div>
                                @error('design_article')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unite">Unite</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="unite" name="unite" class="form-control @error('unite') is-invalid @enderror"  value="{{ old('unite') ?? $unite ?? '' }}" autocomplete="unite" autofocus>
                                </div>
                                @error('unite')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ref_fam">Référence Famille<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" list="list_famille" type="text" id="ref_fam" class="form-control @error('ref_fam') is-invalid @enderror" name="ref_fam" value="{{ old('ref_fam') ?? $magasin_stock->ref_fam ?? '' }}" autocomplete="ref_fam" autofocus>
                                </div>
                                @error('ref_fam')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="design_fam">Désignation Famille<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="design_fam" class="form-control @error('design_fam') is-invalid @enderror" name="design_fam" value="{{ old('design_fam') ?? $design_fam ?? '' }}" autocomplete="design_fam" autofocus>
                                </div>
                                @error('design_fam')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="qte_fam">Quantité Famille</label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="qte_fam" class="form-control @error('qte_fam') is-invalid @enderror" name="qte_fam" value="{{ old('qte_fam') ?? $qte_fam ?? '' }}" autocomplete="qte_fam" autofocus>
                                </div>
                                @error('qte_fam')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="compte_stock">Compte Stock<span style="color: red"><sup> *</sup></span></label>
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="compte_stock" class="form-control @error('compte_stock') is-invalid @enderror" name="compte_stock" value="{{ old('compte_stock') ?? $compte_stock ?? '' }}" autocomplete="compte_stock" autofocus>
                                </div>
                                @error('compte_stock')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="moy_requisition">Moy. de réquisitions hebdo.<span style="color: red"><sup> *</sup></span></label>
                                    <input autocomplete="off" onkeyup="editStock(this)" type="number" id="moy_requisition" class="form-control @error('moy_requisition') is-invalid @enderror" name="moy_requisition" value="{{ old('moy_requisition') ?? $moy_requisition->valeur ?? '' }}" autocomplete="moy_requisition" autofocus>
                                </div>
                                @error('moy_requisition')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="delai_livraison">Délai de livraison du fournisseur<span style="color: red"><sup> *</sup></span></label>
                                    <input autocomplete="off" onkeyup="editStock(this)" type="number" id="delai_livraison" class="form-control @error('delai_livraison') is-invalid @enderror" name="delai_livraison" value="{{ old('delai_livraison') ?? $delai_livraison->valeur ?? '' }}" autocomplete="delai_livraison" autofocus>
                                </div>
                                @error('delai_livraison')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="retard_livraison">Marge de retard de livraison<span style="color: red"><sup> *</sup></span></label>
                                    <input autocomplete="off" onkeyup="editStock(this)" type="number" id="retard_livraison" class="form-control @error('retard_livraison') is-invalid @enderror" name="retard_livraison" value="{{ old('retard_livraison') ?? $retard_livraison->valeur ?? '' }}" autocomplete="retard_livraison" autofocus>
                                </div>
                                @error('retard_livraison')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        

                        <div class="panel panel-footer">
                            <table class="table table-bordered" id="myTable" style="width:100%">
                                <thead>
                                    <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
                                        <th style="width:5%">Quantité<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:7%">Prix unitaire<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:7%">Montant HT<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:5%">Taxe<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:8%">Montant ttc<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:6%">Stock Sécurité<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:6%">Stock Alert<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:5%">Stock Mini<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:1%">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input onkeyup="calculMontantHT(this)" required type="number" id="qte" name="qte" class="form-control qte" value="{{ old('qte') ?? $magasin_stock->qte ?? '' }}">
                                        </td>
                                        <td>
                                            <input onkeyup="calculMontantHT(this)" required type="number" id="cmup" name="cmup" class="form-control cmup" value="{{ old('cmup') ?? $magasin_stock->cmup ?? '' }}">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" required type="text" id="montant" name="montant" class="form-control montant" value="{{ old('montant') ?? $magasin_stock->montant ?? '' }}">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" required type="text" id="taux" name="taxe" class="form-control taxe" value="{{ old('taxe') ?? $taux ?? '' }}">
                                        </td>
                                        <?php if(isset($magasin_stock->montant) && isset($taux)){ $montant_ttc = $magasin_stock->montant * (1 + $taux / 100); } ?>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" required type="text" id="montant_ttc" name="montant_ttc" class="form-control montant_ttc" value="{{ old('montant_ttc') ?? $montant_ttc ?? '' }}">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="stock_securite" name="stock_securite" required class="form-control" value="{{ old('stock_securite') ?? $magasin_stock->stock_securite ?? '' }}">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="stock_alert" name="stock_alert" required class="form-control" value="{{ old('stock_alert') ?? $magasin_stock->stock_alert ?? '' }}">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" type="text" id="stock_mini" name="stock_mini" required class="form-control" value="{{ old('stock_mini') ?? $magasin_stock->stock_mini ?? '' }}">
                                        </td>
                                        <td><button type="submit" class="btn btn-success">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                            </svg>
                                        </button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    selectMagasin = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('#ref_magasin').val();
            if(value != ''){
                const block = value.split('->');
                var ref_magasin = block[0];
                var design_magasin = block[1];
                var ref_depot = block[2];
                var nom_structure = block[3];
                
            }else{
                ref_magasin = "";
                design_magasin = "";
                ref_depot = "";
                nom_structure = "";
            }
            
            tr.find('#ref_magasin').val(ref_magasin);
            tr.find('#design_magasin').val(design_magasin);
            tr.find('#ref_depot').val(ref_depot);
            tr.find('#nom_structure').val(nom_structure);
            
        }

        calculMontantHT = function(e){
            var tr=$(e).parents("tr");
            var qte=tr.find('.qte').val();
            var cmup=tr.find('.cmup').val();
            var montant=(qte*cmup);
            tr.find('.montant').val(montant);
            
            var montant=tr.find('.montant').val();
            var taxe=tr.find('.taxe').val();
            if(taxe === null){
                taxe = 1;
            }else{
                taxe = 1 + (taxe/100);
            }
            var montant_ttc=(montant*taxe);
            tr.find('.montant_ttc').val(montant_ttc);
            total();
        }

    selectArticle = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('#ref_articles').val();
            if(value != ''){
                const block = value.split('->');
                var ref_articles = block[0];
                var design_article = block[1];
                var ref_fam = block[2];
                var design_fam = block[3];
                var qte_fam = block[4];
                var compte_stock = block[5];
                var unite = block[6];
                var taux = block[7];
                
            }else{
                ref_articles = "";
                design_article = "";
                ref_fam = "";
                design_fam = "";
                qte_fam = "";
                compte_stock = "";
                unite = "";
                taux = "";
            }
            
            tr.find('#ref_articles').val(ref_articles);
            tr.find('#design_article').val(design_article);
            tr.find('#ref_fam').val(ref_fam);
            tr.find('#design_fam').val(design_fam);
            tr.find('#qte_fam').val(qte_fam);
            tr.find('#compte_stock').val(compte_stock);
            tr.find('#unite').val(unite);
            tr.find('#taux').val(taux);
            
            
        }
    
    editStock = function(a){
            var moy_requisition = document.getElementById('moy_requisition').value;
            var delai_livraison = document.getElementById('delai_livraison').value;
            var retard_livraison = document.getElementById('retard_livraison').value;

            if (moy_requisition != '' && delai_livraison != '') {
                var stock_mini = delai_livraison * moy_requisition;

                if (stock_mini === undefined) {
                }else{
                    document.getElementById('stock_mini').value = stock_mini;
                }

            }

            if (moy_requisition != '' && retard_livraison != '') {
                var stock_securite = retard_livraison * moy_requisition;

                if (stock_securite === undefined) {

                }else{
                    document.getElementById('stock_securite').value = stock_securite;
                } 
            }

            if (moy_requisition != '' && retard_livraison != '' && moy_requisition != '' && delai_livraison != '') {
                var stock_alert = stock_securite + stock_mini;

                if (stock_alert === undefined) {

                }else{
                    document.getElementById('stock_alert').value = stock_alert;
                }
            }

        
    }

</script>


@endsection
