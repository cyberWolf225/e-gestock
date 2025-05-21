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
  </style>

@endsection 

@section('content')
<div class="container">

    <datalist id="design_fam">
        @foreach($liste_familles as $liste_famille)
        <option value="{{ $liste_famille->design_fam }}">{{ $liste_famille->design_fam }}</option>
        @endforeach
    </datalist>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header entete-table">{{ __('Modification de famille d\'article') }}</div>
                <div class="card-body">
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
                <form method="POST" action="{{ route('familles.update') }}">
                @csrf
                <table style="width: 100%">
                    <tbody>
                    <tr>
                    <td>


                    <div class="row">
                    <div class="col-md-12">

                    <div class="form-group row">  

                        <div class="col-md-2">
                            <label for="ref_fam">Référence Famille <sup style="color: red">*</sup></label>
                            <input value="{{ $familles->id ?? 0 }}" name="id" class="form-control" required onfocus="this.blur()" style="background-color: #e9ecef; display:none" />
                            <input onfocus="this.blur()" style="background-color: #e9ecef" autocomplete="off" onkeyup="editFamille(this)" list="ref_fam" id="ref_fam" type="text" class="form-control @error('ref_fam') is-invalid @enderror" name="ref_fam" value="{{ old('ref_fam') ?? $familles->ref_fam ?? '' }}"  autocomplete="ref_fam">

                            @error('ref_fam')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="design_fam">Famille <sup style="color: red">*</sup></label>
                            <input list="design_fam" autocomplete="off" id="design_fam" type="text" class="form-control @error('design_fam') is-invalid @enderror" name="design_fam" value="{{ old('design_fam') ?? $familles->design_fam ?? '' }}"  autocomplete="design_fam">

                            @error('design_fam')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="flag_actif">Statut </label><br>
                            <input style="margin-top: 8px;" list="flag_actif" id="flag_actif" type="checkbox" name="flag_actif"
                            @if(isset($familles->flag_actif))
                                @if($familles->flag_actif === 1)
                                    checked
                                @endif
                            @endif 
                            
                            {{ old('flag_actif') ? 'checked' : '' }}

                            >

                            @error('flag_actif')
                            <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="col-md-2"><br>
                            <button style="margin-top: 8px;" type="submit" class="btn btn-success">{{ __('Modifier') }}</button>
                        </div>

                    </div>
                    </div>


                    </div>

                    </td>
                    </tr>
                    </tbody>
                </table>
                </form>

                <div class="row bg-white">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered bg-white data-table" style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="text-align: center; vertical-align:middle; width:1px; white-space:nowrap">#</th>
                                    <th style="text-align: center ; vertical-align:middle; width:1px; white-space:nowrap">Réf Famille</th>
                                    <th style="text-align: center ; vertical-align:middle">Désignation Famille</th>
                                    <th style="text-align: center ; vertical-align:middle; width:1px; white-space:nowrap">Statut</th>
                                    <th style="text-align: center ; vertical-align:middle; width:1px; white-space:nowrap">Date</th>
                                    <th style="text-align: center ; vertical-align:middle; width:1px; white-space:nowrap">&nbsp;</th>
                                  </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach($datas as $data)
                                  <?php
                                      $updated_at = null;
  
                                      if (isset($data->updated_at)) {
                                          $updated_at = date('d/m/Y',strtotime($data->updated_at));
                                      }
  
                                      $statut = null;
  
                                      if ($data->flag_actif === 1) {
                                          $statut = 'Activé';
                                      }else {
                                          $statut = 'Désactivé';
                                      }
  
                                      $familles_id = Crypt::encryptString($data->id);
                                      $title_edit = "Modifier la famille d'article";
                                      $href_edit = "edit/".$familles_id;
                                      $display_edit = "";
                                  ?>
                                  <tr>
                                      <td style="text-align: center ; vertical-align:middle; width:1px; white-space:nowrap">{{ $i ?? '' }}</td>
                                      <td style="text-align: left ; vertical-align:middle; width:1px; white-space:nowrap">{{ $data->ref_fam ?? '' }}</td>
                                      <td style="text-align: left ; vertical-align:middle">{{ $data->design_fam ?? '' }}</td>
                                      <td style="text-align: center; width:1px; white-space:nowrap">{{ $statut ?? '' }}</td>
                                      <td style="text-align: center ; vertical-align:middle; width:1px; white-space:nowrap">{{ $updated_at ?? '' }}</td>
                                      <td style="text-align: center ; vertical-align:middle; vertical-align:middle; width:1px; white-space:nowrap">
                                      
                                          <a style="display:{{ $display_edit }};" title="{{ $title_edit }}" href="{{ $href_edit }}" class="btn btn-warning btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg></a>   
                                          
                                      </td>
                                  </tr>
                                  <?php $i++; ?>
                                @endforeach
                            </tbody>
                          </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
            
</div>
<script>
    editFamille = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('#ref_fam').val();
            if(value != ''){
                const block = value.split('->');
                var ref_fam = block[0];
                var design_fam = block[1];
                var qte_fam = block[2];
                var compte_stock = block[3];
                
            }else{
                ref_fam = "";
                design_fam = "";
                qte_fam = "";
                compte_stock = "";
            }
            
            tr.find('#ref_fam').val(ref_fam);
            tr.find('#design_fam').val(design_fam);
            tr.find('#qte_fam').val(qte_fam);
            tr.find('#compte_stock').val(compte_stock);
        }


    editStructure = function(a){
            var tr=$(a).parents("tr");
            const value=tr.find('#code_structure').val();
            if(value != ''){
                const block = value.split('->');
                var code_structure = block[0];
                var structure = block[1];
                
            }else{
                code_structure = "";
                structure = "";
            }
            
            tr.find('#code_structure').val(code_structure);
            tr.find('#structure').val(structure);
        }

    
</script>
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
@endsection
