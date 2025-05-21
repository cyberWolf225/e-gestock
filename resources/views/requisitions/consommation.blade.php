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

                    <form action="{{ route('requisitions.crypt_post') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-2">
                                            <label class="label" for="cst"><u><b>S</b></u>tructure <sup style="color: red">*</sup> :</label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input onkeyup="editStructure(this)" list="liste_cst" style="margin-top: -5px;" id="cst" name="cst" class="form-control form-control-sm @error('cst') is-invalid @enderror cst" autocomplete="off" value="{{ old('cst') ?? $structure->code_structure ?? '' }}">

                                            @error('cst')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>
                                        <div class="col-sm-8">
                                            <input style="margin-top: -5px; background-color: #e9ecef" onfocus="this.blur()" id="nst" name="nst" class="form-control form-control-sm @error('nst') is-invalid @enderror nst" autocomplete="off" value="{{ old('nst') ?? $structure->nom_structure ?? '' }}">

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
                                            <input style="margin-top: -5px;" onkeyup="editCompte(this)" list="liste_rf"  id="rf" name="rf" class="form-control form-control-sm @error('rf') is-invalid @enderror rf" autocomplete="off" value="{{ old('rf') ?? $famille->ref_fam ?? '' }}">

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
                                            <label class="label" for="pd"><u><b>P</b></u>ériode du <sup style="color: red">*</sup></label>
                                        </div>
                                        <div class="col-sm-2">
                                            <input required style="margin-top: -5px;" type="date" id="pd" name="pd" class="form-control form-control-sm @error('pd') is-invalid @enderror pd" autocomplete="off" value="{{ old('pd') ?? $periode_debut ?? '' }}">

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
                                            <input required style="margin-top: -5px;" type="date" id="pf" name="pf" class="form-control form-control-sm @error('pf') is-invalid @enderror pf" autocomplete="off" value="{{ old('pf') ?? $periode_fin ?? '' }}">

                                            @error('pf')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror

                                        </div>

                                        <div class="col-sm-2">
                                            <button class="btn btn-dark btn-sm" type="submit" name="submit" value="soumettre">Soumettre</button>
                                            @if(count($consommations) > 0)
                                                
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
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">Ref</th>
                            <th style="vertical-align: middle; text-align:center" width="90%">Désignation</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">Qté cdé</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">Qté liv</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">Prix unit</th>
                            <th style="vertical-align: middle; text-align:center; width: 1%; white-space: nowrap;">Montant ttc</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; $u=1; $section[0] = "sec_0"; $montant_old[0] = 0; $qte_cde_old[0] = 0; $qte_old[0] = 0; $montant_section = 0; $qte_cde_section = 0; $qte_section = 0; $montant_sections[$u] = 0; $qte_cde_sections[$u] = 0; $qte_sections[$u] = 0; $montant_total = 0; $qte_cde_total = 0; $qte_total = 0;?>
                            @foreach($consommations as $consommation)
                            
                                <?php 
                                    $qte_reelle_cde = 0;
                                    $qte_cde_old[$i] = 0;

                                    if(isset($consommation->ref_articles)){

                                        if ($consommation->departements_id != null) {
                                            $consommation_qte_cdes =  DB::select("SELECT SUM(dd.qte) qte_cde 
                                        
                                            FROM demandes dd, magasin_stocks mms
                                            
                                            WHERE 
                                            mms.id = dd.magasin_stocks_id 
                                            
                                            AND 
                                            dd.id IN (
                                                SELECT l.demandes_id FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s
                                            
                                                WHERE

                                                l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$consommation->code_structure."' AND a.ref_fam = '".$famille->ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' AND ms.ref_articles = '".$consommation->ref_articles."' AND r.departements_id = '".$consommation->departements_id."'

                                            )
                                            AND mms.ref_articles = '".$consommation->ref_articles."'
                                            ");
                                        }else{
                                            $consommation_qte_cdes =  DB::select("SELECT SUM(dd.qte) qte_cde 
                                        
                                            FROM demandes dd, magasin_stocks mms
                                            
                                            WHERE 
                                            mms.id = dd.magasin_stocks_id 
                                            
                                            AND 
                                            dd.id IN (
                                                SELECT l.demandes_id FROM `livraisons` l, demandes d, requisitions r, magasin_stocks ms, articles a, structures s
                                            
                                                WHERE

                                                l.demandes_id = d.id AND d.requisitions_id = r.id AND ms.id = d.magasin_stocks_id AND a.ref_articles = ms.ref_articles AND s.code_structure = r.code_structure AND r.code_structure = '".$consommation->code_structure."' AND a.ref_fam = '".$famille->ref_fam."' AND l.created_at BETWEEN '".$periode_debut."' AND '".$periode_fin."' AND ms.ref_articles = '".$consommation->ref_articles."' AND r.departements_id is NULL

                                            )
                                            AND mms.ref_articles = '".$consommation->ref_articles."'
                                            ");
                                        }
                                        
                                        foreach ($consommation_qte_cdes as $consommation_qte_cde) {
                                            $qte_cde_old[$i] = $consommation_qte_cde->qte_cde;
                                            $qte_reelle_cde = $consommation_qte_cde->qte_cde;
                                        }
                                    }
                                    
                                    
                                    $montant_old[$i] = $consommation->montant;
                                    
                                    $qte_old[$i] = $consommation->qte;

                                    $section[$i] = $consommation->nom_departement;
                                    $cmup = number_format((float)$consommation->cmup, 2, '.', '');

                                    $block_cmup = explode(".",$cmup);



                                    if (isset($block_cmup[0])) {
                                        $cmup_partie_entiere = $block_cmup[0];
                                    }


                                    if (isset($block_cmup[1])) {
                                        $cmup_partie_decimale = $block_cmup[1];
                                    }

                                    $montant_section = $montant_section + $montant_old[$i-1];
                                        
                                    $qte_cde_section = $qte_cde_section + $qte_cde_old[$i-1];

                                    
                                    
                                    $qte_section = $qte_section + $qte_old[$i-1];

                                    if(count($consommations) != $i){

                                        $affiche_block_section_total = 0;
                                        $affiche_block_structure_total = 0;

                                    }elseif(count($consommations) === $i){
                                        
                                        /*$montant_section = $montant_section + $montant_old[$i-1] + $montant_old[$i];

                                        $qte_cde_section = $qte_cde_section + $qte_cde_old[$i-1] + $qte_cde_old[$i];

                                        $qte_section = $qte_section + $qte_old[$i-1] + $qte_old[$i];*/

                                        $affiche_block_section_total = 1;
                                        $affiche_block_structure_total = 1;
                                    }

                                    if($section[$i-1] != $section[$i]){
                                        $affiche_block_section = 1;
                                    }else {
                                        $affiche_block_section = 0;
                                    }
                                    
                                ?>
                                
                                @if($affiche_block_section === 1)
                                    @if($section[$i-1] != "sec_0")

                                    <?php
                                        
                                            $montant_sections[$u] = $montant_section - $montant_sections[$u-1];
                                            
                                            $qte_cde_sections[$u] = $qte_cde_section - $qte_cde_sections[$u-1]; 

                                            

                                            $qte_sections[$u] = $qte_section - $qte_sections[$u-1]; 

                                            if ($u > 2) {

                                                for ($v=2; $v < $u; $v++) { 

                                                    $montant_sections[$u] = $montant_sections[$u] - $montant_sections[$u-$v];

                                                    $qte_cde_sections[$u] = $qte_cde_sections[$u] - $qte_cde_sections[$u-$v];

                                                    $qte_sections[$u] = $qte_sections[$u] - $qte_sections[$u-$v];

                                                }
                                            }
                                            
                                            $montant_total = $montant_total + $montant_sections[$u];

                                            $qte_cde_total = $qte_cde_total + $qte_cde_sections[$u];

                                            $qte_total = $qte_total + $qte_sections[$u];
                                        
                                        
                                    ?>
                                        
                                        <tr style="background-color: #c4c0c0; font-weight:bold">
                                            <td colspan="3">Total par section de {{ $section[$i-1] ?? '' }}</td>
                                            <td style="text-align: center; font-weight:bold">{{ strrev(wordwrap(strrev($qte_cde_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                                            <td style="text-align: center; font-weight:bold">{{ strrev(wordwrap(strrev($qte_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                                            <td style="text-align: center; font-weight:bold"></td>
                                            <td style="text-align: right; font-weight:bold;">{{ strrev(wordwrap(strrev($montant_sections[$u] ?? ''), 3, ' ', true)) }}
                                            </td>
                                        </tr>
                                        
                                    @endif
                                    
                                    
                                    @if(isset($consommation->nom_departement))
                                            
                                        <tr><td colspan="7" style="text-align: center; font-weight:bold; color:red">{{ mb_strtoupper($consommation->nom_departement ?? '') }}</td></tr>
                                        
                                        
                                    @else
                                        <tr><td colspan="7" style="text-align: center; font-weight:bold; color:red">{{ mb_strtoupper('SECTION COMMUNE') }}</td></tr>
                                    @endif
                                    
                                    <?php $u++; ?>
                                @endif
                               
                                

                                <tr>
                                    <td class="td-center">{{ $i ?? '' }}</td>
                                    <td class="td-left" style="font-weight: bold">{{ $consommation->ref_articles ?? '' }}</td>
                                    <td class="td-left">{{ $consommation->design_article ?? '' }}</td>
                                    <td class="td-center">{{ strrev(wordwrap(strrev($qte_reelle_cde ?? ''), 3, ' ', true)) }}</td>
                                    <td class="td-center">{{ strrev(wordwrap(strrev($consommation->qte ?? ''), 3, ' ', true)) }}</td>
                                    <td class="td-right">
                                        @if(isset($cmup_partie_decimale) && $cmup_partie_decimale != 0)
                                            {{ strrev(wordwrap(strrev($cmup_partie_entiere ?? ''), 3, ' ', true)).'.'.strrev(wordwrap(strrev($cmup_partie_decimale ?? ''), 3, ' ', true)) }}
                                        @else
                                            {{ strrev(wordwrap(strrev($cmup_partie_entiere ?? ''), 3, ' ', true)) }}
                                        @endif
                                    </td>
                                    <td class="td-right">{{ strrev(wordwrap(strrev($consommation->montant ?? ''), 3, ' ', true)) }}</td>
                                </tr>

                                @if($affiche_block_section_total === 1 && $affiche_block_structure_total === 1)
                                    
                                    <?php
                                            
                                            if(count($consommations) === $i){

                                                $montant_section = $montant_section + $montant_old[$i];

                                                $qte_cde_section = $qte_cde_section + $qte_cde_old[$i];

                                                $qte_section = $qte_section + $qte_old[$i];

                                            }
                                            
                                            $montant_sections[$u] = $montant_section - $montant_sections[$u-1];

                                            $qte_cde_sections[$u] = $qte_cde_section - $qte_cde_sections[$u-1]; 

                                            $qte_sections[$u] = $qte_section - $qte_sections[$u-1];

                                            if ($u > 2) {

                                                for ($v=2; $v < $u; $v++) { 
                                                    
                                                    $montant_sections[$u] = $montant_sections[$u] - $montant_sections[$u-$v];

                                                    $qte_cde_sections[$u] = $qte_cde_sections[$u] - $qte_cde_sections[$u-$v];

                                                    $qte_sections[$u] = $qte_sections[$u] - $qte_sections[$u-$v];
                                                    


                                                }

                                            }
                                            
                                            $montant_total = $montant_total + $montant_sections[$u];

                                            $qte_cde_total = $qte_cde_total + $qte_cde_sections[$u];

                                            $qte_total = $qte_total + $qte_sections[$u]; 
                                        
                                    
                                    ?>
                                    <tr style="background-color: #c4c0c0; ; font-weight:bold">
                                        <td colspan="3">Total par section {{ $section[$i] ?? '' }}</td>
                                        <td style="text-align: center; font-weight:bold">{{ strrev(wordwrap(strrev($qte_cde_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                                        <td style="text-align: center; font-weight:bold">{{ strrev(wordwrap(strrev($qte_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                                        <td style="text-align: center; font-weight:bold"></td>
                                        <td style="text-align: right; font-weight:bold;">{{ strrev(wordwrap(strrev($montant_sections[$u] ?? ''), 3, ' ', true)) }}</td>
                                    </tr>
                                    
                                    <tr style="background-color: #c4c0c0; font-weight:bold">
                                        <td colspan="3">Total par structure</td>
                                        <td style="text-align: center; font-weight:bold">{{ strrev(wordwrap(strrev($qte_cde_total ?? ''), 3, ' ', true)) }}</td>
                                        <td style="text-align: center; font-weight:bold">{{ strrev(wordwrap(strrev($qte_total ?? ''), 3, ' ', true)) }}</td>    
                                        <td style="text-align: center; font-weight:bold"></td>    
                                        <td style="text-align: right; font-weight:bold;">{{ strrev(wordwrap(strrev($montant_total ?? ''), 3, ' ', true)) }}</td>
                                    </tr>
                                    
                                @endif
                                

                                <?php $i++; ?>
                            @endforeach
                        </tbody>
                    </table>
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
            const saisie = document.getElementById('rf').value;
            const opts = document.getElementById('liste_rf').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var rf = block[0];
                    var df = block[1];
                    
                    }else{
                        df = "";
                        rf = "";
                    }

                    
                    if (rf === undefined) {
                        document.getElementById('rf').value = saisie;

                    }else{
                        
                        document.getElementById('rf').value = rf;

                        

                    }

                    if (df === undefined) {
                        document.getElementById('rf').value = saisie;

                        document.getElementById('df').value = "";

                    }else{

                        document.getElementById('df').value = df;


                        rf = document.getElementById('rf').value;
                        if (rf === '') {
                            rf = null;
                        }
                        cst = document.getElementById('cst').value;
                        if (cst === '') {
                            cst = null;
                        }
                        pd = document.getElementById('pd').value;
                        if (pd === '') {
                            pd = null;
                        }
                        pf = document.getElementById('pf').value;
                        if (pf === '') {
                            pf = null;
                        }

                        document.location.replace('/requisitions/crypt/'+rf+'/'+cst+'/'+null+'/'+pd+'/'+pf);

                    }
                    
                    break;
                }else{
                    document.getElementById('df').value = "";
                }
            }
        }

        editStructure = function(a){
            const saisie = document.getElementById('cst').value;
            const opts = document.getElementById('liste_cst').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var cst = block[0];
                    var nst = block[1];
                    
                    }else{
                        nst = "";
                        cst = "";
                    }

                    
                    if (cst === undefined) {
                        document.getElementById('cst').value = saisie;
                    }else{
                        
                        document.getElementById('cst').value = cst;

                    }

                    if (nst === undefined) {
                        document.getElementById('cst').value = saisie;

                        document.getElementById('nst').value = "";

                    }else{

                        document.getElementById('nst').value = nst;

                    }
                    
                    
                    break;
                }else{
                    document.getElementById('nst').value = "";
                }
            }
        }

        editArticle = function(a){
            const saisie = document.getElementById('art').value;
            const opts = document.getElementById('liste_art').childNodes;

            
            for (var i = 0; i < opts.length; i++) {
                if (opts[i].value === saisie) {
                    
                    if(saisie != ''){
                    const block = saisie.split('->');
                    
                    var art = block[0];
                    var dart = block[1];
                    
                    }else{
                        dart = "";
                        art = "";
                    }

                    
                    if (art === undefined) {
                        document.getElementById('art').value = saisie;

                    }else{
                        
                        document.getElementById('art').value = art;

                    }

                    if (dart === undefined) {
                        document.getElementById('art').value = saisie;

                        document.getElementById('dart').value = "";

                    }else{

                        document.getElementById('dart').value = dart;

                    }
                    
                    break;
                }else{
                    document.getElementById('dart').value = "";
                }
            }
        }

    </script>

@endsection
