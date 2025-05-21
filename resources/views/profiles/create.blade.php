@extends('layouts.app')

@section('content')
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</head>
<div class="container">
    <datalist id="list_agent">
        @foreach($agents as $agent) 
           <option value="{{ $agent->mle . ' -> ' . $agent->nom_prenoms. ' -> ' . $agent->email }}">{{ $agent->mle }}</option>
        @endforeach   
    </datalist>

    <datalist id="list_profil">
        @foreach($type_profils as $type_profil)
           <option value="{{ $type_profil->name }}">{{ $type_profil->name }}</option>
        @endforeach   
    </datalist>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Créer un profil') }} </div>

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
                    <form method="POST" action="{{ route('store') }}">
                        @csrf                     
                        <div class="panel panel-footer">
                            <table class="table table-bordered" id="myTable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:12%">Matricule</th>
                                        <th style="width:40%">Nom & Prénom(s)</th>
                                        <th style="width:20%">E-mail</th>
                                        <th style="width:20%">Profil</th>
                                        <th style="text-align:center; width:5%"><a onclick="myCreateFunction()" href="#" class="addRow"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                          </svg></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input autocomplete="off" required list="list_agent" type="text" onkeyup="selectAgent(this)" id="mle" name="mle[]" class="form-control mle">
                                        </td>
                                        <td>
                                            <input autocomplete="off" required disabled type="text"  id="nom_prenoms" name="nom_prenoms[]" class="form-control nom_prenoms">
                                        </td>
                                        <td>
                                            <input autocomplete="off" required disabled type="text" name="email[]" class="form-control email">
                                        </td>
                                        <td>
                                            <input autocomplete="off" list="list_profil" required type="text" name="name[]" class="form-control name">
                                        </td>
                                        <td>
                                            <a onclick="removeRow(this)" href="#" class="btn btn-danger remove"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                              </svg></a>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
                                        <td style="border: none"></td>
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

        selectAgent = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('.mle').val();
            if(value != ''){
                const block = value.split('->');
                var ref = block[0];
                var nom_prenoms = block[1];
                var email = block[2];
                
            }else{
                ref = "";
                nom_prenoms = "";
                email = "";
            }
            
            tr.find('.mle').val(ref);
            tr.find('.nom_prenoms').val(nom_prenoms);
            tr.find('.email').val(email);
            tr.find('.profil').val("");
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
            cell1.innerHTML = '<td><input autocomplete="off" required list="list_agent" type="text" onkeyup="selectAgent(this)" id="mle" name="mle[]" class="form-control mle"></td>';
            cell2.innerHTML = '<td><input autocomplete="off" required disabled type="text"  id="nom_prenoms" name="nom_prenoms[]" class="form-control nom_prenoms"></td>';
            cell3.innerHTML = '<td><input autocomplete="off" required disabled type="text" name="email[]" class="form-control email"></td>';
            cell4.innerHTML = '<td><input autocomplete="off" list="list_profil" required type="text" name="name[]" class="form-control name"></td>';
            cell5.innerHTML = '<td><a onclick="removeRow(this)" href="#" class="btn btn-danger remove"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            
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
