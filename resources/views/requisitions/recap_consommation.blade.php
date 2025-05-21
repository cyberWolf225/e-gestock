@extends('layouts.admin')

@section('styles_datatable')
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('../plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('../plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('../dist/css/adminlte.min.css') }}">

  <link rel="shortcut icon" href="{{ asset('../dist/img/logo.png') }}">

  <style>
    .fond{
        background: url('../../dist/img/sante2.jpg') no-repeat;
        background-size: 100% 100%;
        font-size: 11px;
    }

    .td-center{
        text-align: center;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }

    .td-center-bold{
        text-align: center;
        vertical-align: middle;
        font-weight: bold
    }

    .td-left{
        text-align: left;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }

    .td-right{
        text-align: right;
        vertical-align: middle;
        width: 1%; white-space: nowrap;
    }
  </style>



@endsection
@section('content') 

<datalist id="liste_cst">
    @if(isset($structures))
        @foreach($structures as $list_structure)
            <option value="{{ $list_structure->code_structure ?? '' }}->{{ $list_structure->nom_structure ?? '' }}">{{ $list_structure->nom_structure ?? '' }}</option>
        @endforeach
    @endif
</datalist>

<datalist id="liste_art">
    @if(isset($articles))
        @foreach($articles as $list_article)
            <option value="{{ $list_article->ref_articles ?? '' }}->{{ $list_article->design_article ?? '' }}">{{ $list_article->design_article ?? '' }}</option>
        @endforeach
    @endif
</datalist>

<datalist id="liste_rf">
    @if(isset($familles))
        @foreach($familles as $list_famille)
            <option value="{{ $list_famille->ref_fam ?? '' }}->{{ $list_famille->design_fam ?? '' }}">{{ $list_famille->design_fam ?? '' }}</option>
        @endforeach
    @endif
</datalist>

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

                    <form action="{{ route('requisitions.crypt_post_recap') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <table width="100%" id="tableFamille">
                                                @if(isset($familles_consernees))
                                                    @if(count($familles_consernees) > 0)

                                                        @foreach ($familles_consernees as $key0 => $familles_consernee0)

                                                            <?php 
                                                                $famille = DB::table('familles')->where('ref_fam',$familles_consernee0)->first();
                                                            ?>

                                                            <tr>
                                                                <td width="16%">
                                                                    @if($key0 === 0)
                                                                        
                                                                        <label class="label" for="rf"><u><b>F</b></u>amille d'article <sup style="color: red">*</sup> </label>
                                                                        
                                                                    @endif
                                                                    
                                                                
                                                                </td>
                                                                <td width="17%">
                                                                    <input required style="margin-left: px; margin-top: px; width:86%" onkeyup="editCompte(this)" list="liste_rf"  id="rf" name="rf[]" class="form-control form-control-sm @error('rf') is-invalid @enderror rf" autocomplete="off" value="{{ $famille->ref_fam ?? '' }}">
                                                                </td>
                                                                <td width="60%">
                                                                    <input required style="margin-top: px; background-color: #e9ecef; width:100%" onfocus="this.blur()" id="df" name="df[]" class="form-control form-control-sm @error('df') is-invalid @enderror df" autocomplete="off" value="{{ mb_strtoupper($famille->design_fam ?? '') }}">
                                                                </td>
                                                                <td width="1%" style="text-align:left">

                                                                    @if($key0 === 0)
                                                                        <a title="Ajouter un nouvelle compte" onclick="myCreateFunction()" href="#" class="addRow"><svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                        </svg></a>
                                                                        @else
                                                                        <a title="Retirer ce compte" onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a>
                                                                    @endif

                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        @else
                                                        
                                                        <tr>
                                                            <td width="16%"><label class="label" for="rf"><u><b>F</b></u>amille d'article <sup style="color: red">*</sup> </label></td>
                                                            <td width="17%">
                                                                <input required style="margin-left: px; margin-top: px; width:86%" onkeyup="editCompte(this)" list="liste_rf"  id="rf" name="rf[]" class="form-control form-control-sm @error('rf') is-invalid @enderror rf" autocomplete="off" value="">
                                                            </td>
                                                            <td width="60%">
                                                                <input required style="margin-top: px; background-color: #e9ecef; width:100%" onfocus="this.blur()" id="df" name="df[]" class="form-control form-control-sm @error('df') is-invalid @enderror df" autocomplete="off" value="">
                                                            </td>
                                                            <td width="1%" style="text-align:left">
                                                                <a title="Ajouter un nouvelle compte" onclick="myCreateFunction()" href="#" class="addRow"><svg style="font-weight: bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                                                    </svg></a>
                                                            </td>
                                                        </tr>

                                                    @endif
                                                @endif
                                                
                                                
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="pd"><u><b>P</b></u>ériode du <sup style="color: red">*</sup></label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input required style="margin-top: px;" type="date" id="pd" name="pd" class="form-control form-control-sm @error('pd') is-invalid @enderror pd" autocomplete="off" value="{{ old('pd') ?? $periode_debut ?? '' }}">

                                            @error('pd')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-sm-1" style="text-align: center">
                                            <label class="label" for="pf" style="text-align: center">au</label>
                                        </div>

                                        <div class="col-sm-2">
                                            <input required style="margin-top: px;" type="date" id="pf" name="pf" class="form-control form-control-sm @error('pf') is-invalid @enderror pf" autocomplete="off" value="{{ old('pf') ?? $periode_fin ?? '' }}">

                                            @error('pf')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>

                                        <div class="col-sm-2">
                                            <button class="btn btn-dark btn-sm" type="submit" name="submit" value="soumettre">Soumettre</button>
                                            @if(count($familles_consernees) > 0)
                                                
                                                <button class="btn btn-dark btn-sm" type="submit" name="submit" value="imprimer" formtarget="_blank">Imprimer</button>

                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(isset($familles_consernees))
                    @if(count($familles_consernees) > 0)

                        <table id="" class="table table-striped table-bordered bg-white data-table" style="width: 100%">
                            <thead>
                                <tr style="background-color: #c4c0c0;">
                                <th style="vertical-align: middle; text-align:center;">CODE <br>STRUCTURE</th>
                                <th style="vertical-align: middle; text-align:center;">NOM STRUCTURE</th>

                                @foreach ( $familles_consernees as $key => $familles_consernee)
                                    <?php 
                                        $famille = DB::table('familles')->where('ref_fam',$familles_consernee)->first();
                                    ?>
                                    <th style="vertical-align: middle; text-align:center">{{ $famille->ref_fam ?? '' }} {{ mb_strtoupper($famille->design_fam ?? '') }}</th>
                                @endforeach                          
                                </tr>
                            </thead>
                            <tbody>
                                
                                <?php $i = 1; $consommation_totals = []; ?>
                                @foreach($structures as $structure)

                                    <tr>
                                        
                                        <td style="vertical-align: middle;" class="td-center" style="font-weight: bold">{{ $structure->code_structure ?? '' }}</td>
                                        <td class="td-left" style="font-weight: bold">{{ $structure->nom_structure ?? '' }}</td>

                                        @foreach ( $familles_consernees as $key2 => $familles_consernee2)
                                            <?php 
                                                $consommation = '-';
                                                $consommation_totals[$i][$key2] = 0;
                                                $famille2 = DB::table('familles')->where('ref_fam',$familles_consernee2)->first();
                                                if($famille2 != null){
                                                    $consommations = DB::select("SELECT SUM(l.montant) montant FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s WHERE l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$structure->code_structure."' AND a.ref_fam = '".$famille2->ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' GROUP BY r.code_structure,a.ref_fam ");

                                                    foreach ($consommations as $consommation_key) {
                                                        $consommation = $consommation_key->montant;

                                                        $consommation_totals[$i][$key2] = $consommation_totals[$i][$key2] + $consommation;
                                                    }
                                                }
                                            ?>
                                            <td class="td-right" style="font-weight: bold">{{ strrev(wordwrap(strrev($consommation ?? ''), 3, ' ', true)) }}</td>
                                        @endforeach
                                        
                                    </tr>

                                    @if(count($structures) === $i)
                                        <tr  style="text-align:center; font-weight:bold; background-color: #c4c0c0;">
                                            <td></td>
                                            <td>TOTAL</td>
                                            
                                            @foreach ($familles_consernees as $key3 => $familles_consernee3)
                                                <?php 
                                                    $consommation_total_famille = 0;

                                                    foreach ($consommation_totals as $key => $values) {

                                                        foreach ($values as $key_value => $value_conso) {
                                                            if($key_value === $key3){
                                                                $consommation_total_famille = $consommation_total_famille + $value_conso;
                                                            }
                                                        }

                                                    }
                                                    
                                                ?>

                                                <td class="td-right" style="font-weight: bold">{{ strrev(wordwrap(strrev($consommation_total_famille ?? ''), 3, ' ', true)) }}</td>

                                            @endforeach
                                        </tr>
                                    @endif
                                    <?php $i++; ?>
                                @endforeach

                            </tbody>
                        </table>

                    @endif
                    @endif

                </div>
            </div>
        </div>
    </div>



@endsection

@section('javascripts_datatable') 
    <!-- jQuery -->
    <script src="{{ asset('../plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('../plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('../plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('../plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('../plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('../plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('../dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('../dist/js/demo.js') }}"></script>
    <!-- Page specific script -->
    <script>
    $(function () {
        $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        });
    });
    </script>

    <script>

        editCompte = function(a){

            var tr=$(a).parents("tr");
            const saisie =tr.find('#rf').val();
            const opts = document.getElementById('liste_rf').childNodes;

            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {

                    if(saisie != ''){
                        const block = saisie.split('->');
                        var rf = block[0];
                        var df = block[1];
                        
                    }else{

                        rf = "";
                        df = "";

                    }

                    if (rf === undefined) {
                        tr.find('#rf').val(saisie);
                    }else{
                        tr.find('#rf').val(rf);
                    }

                    if (df === undefined) {
                        tr.find('#df').val("");
                    }else{
                        tr.find('#df').val(df);
                    }
                    
                    break
                }else{
                    tr.find('#df').val("");
                }
            }
            
            
        }

        


        function myCreateFunction() {
            var table = document.getElementById("tableFamille");
            var rows = table.querySelectorAll("tr");
            var nbre_rows = rows.length;
            if (nbre_rows < 5) {
                var row = table.insertRow(nbre_rows);
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                cell1.innerHTML = '<td width="16%">&nbsp;</td>';
                cell2.innerHTML = '<td width="17%"><input required style="margin-left: px; margin-top: px; width:86%" onkeyup="editCompte(this)" list="liste_rf"  id="rf" name="rf[]" class="form-control form-control-sm rf" autocomplete="off" value=""></td>';
                cell3.innerHTML = '<td width="60%"><input style="margin-top: px; background-color: #e9ecef; width:100%" onfocus="this.blur()" id="df" name="df[]" class="form-control form-control-sm df" autocomplete="off" value=""></td>';
                cell4.innerHTML = '<td width="1%" style="vertical-align: middle; text-align:center"><a title="Retirer ce compte" onclick="removeRow(this)" href="#" class="remove"><svg style="color: red; font-weight:bold; font-size:15px;" width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg></a></td>';
            }else{
                Swal.fire({
                title: '<strong>e-GESTOCK</strong>',
                icon: 'error',
                html: 'Nombre de ligne maximale atteint',
                focusConfirm: false,
                confirmButtonText:
                    'Compris'
                });
            }
            
            
        
        }

        removeRow = function(el) {
            var table = document.getElementById("tableFamille");
            var rows = table.querySelectorAll("tr");
            var nbre_rows = rows.length;
                if(nbre_rows<2){
                    Swal.fire({
                    title: '<strong>e-GESTOCK</strong>',
                    icon: 'error',
                    html: 'Vous ne pouvez pas supprimer la dernière ligne',
                    focusConfirm: false,
                    confirmButtonText:
                        'Compris'
                    });

                }else{
                    $(el).parents("tr").remove(); 
                }  
        }

    </script>

@endsection
