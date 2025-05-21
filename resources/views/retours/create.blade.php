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
                <div class="card-header">{{ __('Retourner une réquisition') }} 
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
                    <form method="POST" action="{{ route('retours.store')  }}">
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
                                    <tr style="background-color: #e9ecef;">
                                        <th style="vertical-align:middle; text-align:left; width:8%">Réf</th>
                                        <th style="vertical-align:middle; text-align:left; width:30%">Désignation</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">Prix U</th>
                                        <th style="vertical-align:middle; text-align:center; width:8%">Qté</th>
                                        <th style="vertical-align:middle; text-align:center; width:10%">Qté Retournée<span style="color: red"><sup> *</sup></span></th>
                                        <th style="vertical-align:middle; text-align:center; width:15%">Coût</th>
                                        <th style="vertical-align:middle; text-align:center; width:26%">Observation</th>
                                        <th style="text-align:center; width:5%"><a href="http://e-gestock.test/livraisons/create/{{ $requisitions->num_dem }}"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-counterclockwise" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
                                            <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
                                          </svg></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; ?>
                                    @foreach($demandes as $demande)
                                    <?php $montant = $demande->qte_demandee * $demande->cmup;
                                            $i++;

                                            $retours = DB::select("SELECT * FROM retours WHERE livraisons_id = '".$demande->livraisons_id."' ");
                                    

                                            if (count($retours)>0) {

                                                foreach ($retours as $retour) {
                                                    $qte_retour = $retour->qte_retour;
                                                    $observation_retour = $retour->observation;
                                                    $check = 'checked';
                                                    $montant = $retour->qte_retour * $demande->cmup;
                                                }

                                            }else{
                                                if (isset($qte_retour)) {
                                                    unset($qte_retour);
                                                }
                                                if (isset($observation_retour)) {
                                                    unset($observation_retour);
                                                }
                                                if (isset($check)) {
                                                    unset($check);
                                                }
                                            }
                                    ?>
                                    <tr>
                                        <td style="vertical-align:middle; text-align:center">
                                            {{ $demande->ref_articles ?? '' }}
                                            <input style="display: none" value="{{ $demande->id ?? '' }}" required type="text" id="demandes_id" name="demandes_id[]" class="form-control demandes_id">
                                        </td>
                                        <td style="vertical-align:middle; text-align:left">
                                            {{ $demande->design_article ?? '' }}
                                        </td>
                                        <td style="vertical-align:middle; text-align:right">
                                            <input value="{{ strrev(wordwrap(strrev($demande->cmup ?? 0), 3, ' ', true)) }}" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right" type="text" onkeyup="editMontant(this)" name="cmup[]" class="form-control cmup">
                                        </td>
                                        <td style="vertical-align:middle; text-align:center">
                                            {{ strrev(wordwrap(strrev($demande->qte ?? 0), 3, ' ', true)) }} 
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <input autocomplete="off" required style="text-align:center" value="{{ strrev(wordwrap(strrev(old('qte[]') ?? $qte_retour ?? $demande->qte ?? 0), 3, ' ', true)) }}" type="text" onkeyup="editMontant(this)" name="qte[]" class="form-control qte">
                                        </td>
                                        <td style="vertical-align:middle; text-align:right">
                                            <input value="{{ strrev(wordwrap(strrev($montant ?? 0), 3, ' ', true)) }}" required onfocus="this.blur()" style="background-color: #e9ecef; text-align:right" type="text" name="montant[]" class="form-control montant">
                                        </td>
                                        <td style="vertical-align:middle;">
                                            <input autocomplete="off" required type="text" name="observation[]" value="{{  old('observation[]') ?? $observation_retour ?? '' }}" class="form-control observation">
                                        </td>
                                        <td style="vertical-align: middle; text-align:center">
                                            <input <?php if(isset($check)){?> {{ $check }} <?php } ?> type="checkbox" id="approvalcd_{{ $demande->id }}" name="approvalcd[{{ $demande->id }}]" class="approvalcd_{{ $demande->id }}" />
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
            var qte=tr.find('.qte').val();

            cmup = cmup.replace(' ','');
            qte = qte.replace(' ','');
            tr.find('.qte').val(numStr(qte));
            qte = qte.replace(' ','');


            var montant=(cmup*qte);
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
                var demandes_id = block[3];
                
            }else{
                ref_articles = "";
                design_article = "";
                cmup = "";
                demandes_id = "";
            }
            
            tr.find('.demandes_id').val(demandes_id);
            tr.find('.ref_articles').val(ref_articles);
            tr.find('.design_article').val(design_article);
            tr.find('.cmup').val(cmup);
            tr.find('.qte').val(0);
            tr.find('.montant').val(0);
        }

    
    


    

</script>


@endsection
