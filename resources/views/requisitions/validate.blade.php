@extends('layouts.app')

@section('content')
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<div class="container">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Valider une réquisition') }} 
                    <span style="float: right; margin-right: 20px;">Date Saisie : <strong>{{ date("d/m/Y",strtotime($requisitions->created_at ?? '')) }}</strong></span>
                    <span style="float: right; margin-right: 20px;">Exercice : <strong>{{ $requisitions->exercice ?? '' }}</strong></span>
                    <span style="float: right; margin-right: 20px;">Service : <strong>912101 - Section commune DSI</strong></span>
                    <span style="float: right; margin-right: 20px;">N° Commande : <strong>{{ $requisitions->num_dem ?? '' }}</strong></span>
                    
                </div>

                <div class="card-body">
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('storevalidate') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef; display:none" value="{{ $requisitions->num_dem ?? '' }}" required type="text" name="num_dem" class="form-control">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $requisitions->intitule ?? '' }}" required type="text" name="intitule" class="form-control" placeholder="Intitulé de la réquisition">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" value="{{ $requisitions->code_gestion ?? '' }} {{ ' - '.$requisitions->libelle_gestion ?? '' }}" list="list_gestion" required type="text" name="gestion" class="form-control"  placeholder="Gestion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input onfocus="this.blur()" style="background-color: #e9ecef" style="border:none" type="text" class="form-control"  placeholder="83 - Depot principal du siège">
                                </div>
                            </div>
                        </div>
                        

                        <div class="panel panel-footer">
                            <table class="table table-bordered" id="myTable" style="width:100%">
                                <thead>
                                    <tr style="background-color: #e9ecef">
                                        <th style="width:12%">Réf<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:40%">Désignation<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:10%">Unité<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:10%">Quantité<span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:10%">Cmup</th>
                                        <th style="width:13%">Montant ttc<span style="color: red"><sup> *</sup></span></th>
                                        <th style="text-align:center; width:5%"><a href="http://e-gestock.test/requisitions/validate/{{ $requisitions->num_dem }}"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-counterclockwise" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                                            <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                                          </svg></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandes as $demande)
                                    <?php $montant = $demande->qte_demandee * $demande->prixu;
                                            $montant = strrev(wordwrap(strrev($montant), 3, ' ', true));
                                    ?>
                                    <tr>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" required value="{{ $demande->ref_articles ?? '' }}" list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">
                                            <input style="display: none" value="{{ $demande->id ?? '' }}" required type="text" id="demandes_id" name="demandes_id[]" class="form-control demandes_id">
                                        </td>
                                        <td>
                                            <input required value="{{ $demande->design_article ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" type="text" onkeyup="editMontant(this)" id="design_article" name="design_article[]" class="form-control design_article">
                                        </td>
                                        <td>
                                            <input required value="{{ $demande->prixu ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" type="text" onkeyup="editMontant(this)" name="prixu[]" class="form-control prixu">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" required value="{{ $demande->qte_demandee ?? '' }}" style="text-align: center" type="text" onkeyup="editMontant(this)" name="qte_demandee[]" class="form-control qte_demandee">
                                        </td>
                                        <td>
                                            <input value="{{ $demande->cmup ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef" type="text" name="cmup[]" class="form-control cmup">
                                        </td>
                                        <td>
                                            <input required value="{{ $montant ?? '' }}" onfocus="this.blur()" style="background-color: #e9ecef; text-align: right" type="text" name="montant[]" class="form-control montant">
                                        </td>
                                        <td>
                                            <a onclick="removeRow(this)" href="#" class="btn btn-danger remove"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                              </svg></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
                                        <td style="border: none"><strong class="total"></strong></td>
                                        <td><button type="submit" class="btn btn-success">
                                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                                            </svg>
                                        </button></td>

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

        editMontant = function(e){
            var tr=$(e).parents("tr");
            var prixu=tr.find('.prixu').val();
            var qte_demandee=tr.find('.qte_demandee').val();
            var montant=(prixu*qte_demandee);
            tr.find('.montant').val(montant);
            total();
        }

    function total(){
        var total=0;
        $('.montant').each(function(i,e){
            var montant =$(this).val()-0;
            total +=montant;
        });
        $('.total').html(total + " FCFA");  
    }

        editDesign = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('.ref_articles').val();
            if(value != ''){
                const block = value.split('->');
                var ref_articles = block[0];
                var design_article = block[1];
                var prixu = block[2];
                var demandes_id = block[3];
                
            }else{
                ref_articles = "";
                design_article = "";
                prixu = "";
                demandes_id = "";
            }
            
            tr.find('.demandes_id').val(demandes_id);
            tr.find('.ref_articles').val(ref_articles);
            tr.find('.design_article').val(design_article);
            tr.find('.prixu').val(prixu);
            tr.find('.qte_demandee').val(0);
            tr.find('.montant').val(0);
        }



    
    
    function myCreateFunction() {
      var table = document.getElementById("myTable");
      var rows = table.querySelectorAll("tr");
      var nbre_rows = rows.length-1;
        if(nbre_rows<6){
            var row = table.insertRow(nbre_rows);
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            var cell5 = row.insertCell(4);
            var cell6 = row.insertCell(5);
            var cell7 = row.insertCell(6);
            cell1.innerHTML = '<td><input required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles"><input style="display: none" required type="text" id="demandes_id" name="demandes_id[]" class="form-control demandes_id"></td>';
            cell2.innerHTML = '<td><input required type="text" onkeyup="editMontant(this)" onfocus="this.blur()" style="background-color: #e9ecef" id="design_article" name="design_article[]" class="form-control design_article"></td>';
            cell3.innerHTML = '<td><input required type="text" onfocus="this.blur()" style="background-color: #e9ecef" onkeyup="editMontant(this)" name="prixu[]" class="form-control prixu"></td>';
            cell4.innerHTML = '<td><input required type="text" onkeyup="editMontant(this)" name="qte_demandee[]" class="form-control qte_demandee"></td>';
            cell5.innerHTML = '<td><input type="text" onfocus="this.blur()" style="background-color: #e9ecef" onkeyup="editMontant(this)" name="cmup[]" class="form-control cmup"></td>';
            cell6.innerHTML = '<td><input required type="text" onfocus="this.blur()" style="background-color: #e9ecef" name="montant[]" class="form-control montant"></td>';
            cell7.innerHTML = '<td><a onclick="removeRow(this)" href="#" class="btn btn-danger remove"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
        }
      
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            if(nbre_rows<4){
                alert('Dernière ligne non supprimée');
            }else{
                $(el).parents("tr").remove(); 
            }  
    }
</script>


@endsection
