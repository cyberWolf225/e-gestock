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
                <div class="card-header">{{ __('Valider le retour d\'article') }} 
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
                    @if(Session::has('error'))
                        <div class="alert alert-danger">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('valider_retours.store') }}">
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
                                        <th style="width:12%">Réf</th>
                                        <th style="width:30%">Désignation</th>
                                        <th style="width:10%; text-align:center">Prix U</th>
                                        <th style="width:11%; text-align:center">Qté Rétournée</th>
                                        <th style="width:11%; text-align:center">Qté Validée <span style="color: red"><sup> *</sup></span></th>
                                        <th style="width:15%; text-align:center">Coût</th>
                                        <th style="text-align:center; width:5%"><a href="http://e-gestock.test/requisitions/validate/{{ $requisitions->num_dem }}"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-counterclockwise" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                                            <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                                          </svg></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($retours as $retour)
                                    <?php $montant = $retour->qte_retour * $retour->cmup;
                                            $montant = strrev(wordwrap(strrev($montant), 3, ' ', true));
                                            $valider_retours = DB::select("SELECT sum(qte_validee) qte_validee, flag_valide FROM valider_retours WHERE retours_id = '".$retour->id."' GROUP BY qte_validee, flag_valide ");
                                            
                                            
                                            $check = '';
                                            $color = 'orange';
                                            if (count($valider_retours)>0) {
                                                foreach ($valider_retours as $valider_retour) {
                                                $qte_validee = $valider_retour->qte_validee;
                                                $flag_valide = $valider_retour->flag_valide;

                                                if(isset($qte_validee)){
                                                    $montant = $qte_validee * $retour->cmup;
                                                }
                                                    if(isset($flag_valide)){
                                                    if($flag_valide == 0){
                                                        $check = '';
                                                        $color = 'red';
                                                    }else{
                                                        $check = 'checked';
                                                        $color = 'green';
                                                    }
                                                    }else{
                                                        $check = '';
                                                        $color = 'orange';
                                                    }
                                                }
                                            }
                                            

                                            
                                    ?>
                                    <tr>
                                        <td style="display: flex; ">
                                            <svg style="margin-top:15px; margin-left:-7px; margin-right:3px; color:{{ $color ?? '' }}" width="0.5em" height="0.5em" viewBox="0 0 16 16" class="bi bi-circle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="8" cy="8" r="8"/>
                                              </svg>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef" required value="{{ $retour->ref_articles ?? '' }}" list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">
                                            <input style="display: none" value="{{ $retour->id ?? '' }}" required type="text" id="retours_id" name="retours_id[]" class="form-control retours_id">
                                        </td>
                                        <td style="vertical-align:middle">
                                            {{ $retour->design_article ?? '' }}    
                                        </td>
                                        <td>
                                            <input required value="{{ strrev(wordwrap(strrev($retour->cmup ?? 0), 3, ' ', true)) }}" onfocus="this.blur()" style="background-color: #e9ecef; text-align:right" type="text" name="cmup[]" class="form-control cmup">
                                        </td>
                                        <td>
                                            <input onfocus="this.blur()" style="background-color: #e9ecef; text-align: center" required value="{{ strrev(wordwrap(strrev($retour->qte_retour ?? 0), 3, ' ', true)) }}" type="text" name="qte_retour[]" class="form-control qte_retour">
                                        </td>
                                        <td>
                                            <input style="text-align: center" required value="{{ strrev(wordwrap(strrev($qte_validee ?? $retour->qte_retour ?? 0), 3, ' ', true)) }}" onkeyup="editMontant(this)" type="text" name="qte_validee[]" class="form-control qte_validee">
                                        </td>
                                        <td>
                                            <input required value="{{ strrev(wordwrap(strrev($montant ?? 0), 3, ' ', true)) }}" onfocus="this.blur()" style="background-color: #e9ecef; text-align: right" type="text" name="montant[]" class="form-control montant">
                                        </td>
                                        <td style="vertical-align: middle; text-align:center">
                                            <input {{ $check ?? '' }} type="checkbox" id="approvalcd_{{ $retour->id }}" name="approvalcd[{{ $retour->id }}]" class="approvalcd_{{ $retour->id }}" />
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
                                        <td style="border: none"><strong></strong></td>
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
function numStr(a, b) {
    a = '' + a;
    b = b || ' ';
    var c = '',
        d = 0;
    while (a.match(/^0[0-9]/)) {
        a = a.substr(1);
    }
    for (var i = a.length-1; i >= 0; i--) {
        c = (d != 0 && d % 3 == 0) ? a[i] + b + c : a[i] + c;
        d++;
    }
    return c;
}
        editMontant = function(e){
            var tr=$(e).parents("tr");
            var cmup=tr.find('.cmup').val();
            var qte_validee=tr.find('.qte_validee').val();

            cmup = cmup.replace(' ','');
            qte_validee = qte_validee.replace(' ','');
            tr.find('.qte_validee').val(numStr(qte_validee));
            qte_validee = qte_validee.replace(' ','');


            var montant=(cmup*qte_validee);
            tr.find('.montant').val(numStr(montant));
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
                var cmup = block[2];
                var retours_id = block[3];
                
            }else{
                ref_articles = "";
                design_article = "";
                cmup = "";
                retours_id = "";
            }
            
            tr.find('.retours_id').val(retours_id);
            tr.find('.ref_articles').val(ref_articles);
            tr.find('.design_article').val(design_article);
            tr.find('.cmup').val(cmup);
            tr.find('.qte_retour').val(0);
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
            cell1.innerHTML = '<td><input required list="list_article" type="text" onkeyup="editDesign(this)" id="ref_articles" name="ref_articles[]" class="form-control ref_articles"><input style="display: none" required type="text" id="retours_id" name="retours_id[]" class="form-control retours_id"></td>';
            cell2.innerHTML = '<td><input required type="text" onkeyup="editMontant(this)" onfocus="this.blur()" style="background-color: #e9ecef" id="design_article" name="design_article[]" class="form-control design_article"></td>';
            cell3.innerHTML = '<td><input required type="text" onfocus="this.blur()" style="background-color: #e9ecef" onkeyup="editMontant(this)" name="cmup[]" class="form-control cmup"></td>';
            cell4.innerHTML = '<td><input required type="text" onkeyup="editMontant(this)" name="qte_retour[]" class="form-control qte_retour"></td>';
            cell5.innerHTML = '<td><input required value="" type="text" name="qte_validee[]" class="form-control qte_validee"></td>';
            cell6.innerHTML = '<td><input required type="text" onfocus="this.blur()" style="background-color: #e9ecef" name="montant[]" class="form-control montant"></td>';
            cell7.innerHTML = '<td><a onclick="removeRow(this)" href="#" class="btn btn-danger remove"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
        }
      
    }

    removeRow = function(el) {
        var table = document.getElementById("myTable");
        var rows = table.querySelectorAll("tr");
        var nbre_rows = rows.length;
            
        $(el).parents("tr").remove(); 
             

        /*
            if(nbre_rows<4){
            alert('Dernière ligne non supprimée');
        }else{
            $(el).parents("tr").remove(); 
        }  
        */
    }
</script>


@endsection
