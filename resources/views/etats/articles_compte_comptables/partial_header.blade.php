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

@if(isset($structures))
    <datalist id="liste_cst">
        @foreach($structures as $list_structure)
            <option value="{{ $list_structure->code_structure ?? '' }}->{{ $list_structure->nom_structure ?? '' }}">{{ $list_structure->nom_structure ?? '' }}</option>
        @endforeach
    </datalist>
@endif

@if(isset($articles))
    <datalist id="liste_art">
        @foreach($articles as $list_article)
            <option value="{{ $list_article->ref_articles ?? '' }}->{{ $list_article->design_article ?? '' }}">{{ $list_article->design_article ?? '' }}</option>
        @endforeach
    </datalist>
@endif
@if(isset($familles))
    <datalist id="liste_rf">
        @foreach($familles as $list_famille)
            <option value="{{ $list_famille->ref_fam ?? '' }}->{{ $list_famille->design_fam ?? '' }}">{{ $list_famille->design_fam ?? '' }}</option>
        @endforeach
    </datalist>
@endif
@if(isset($structures))
    <datalist id="liste_cst">
        @foreach($structures as $list_structure)
            <option value="{{ $list_structure->code_structure ?? '' }}->{{ $list_structure->nom_structure ?? '' }}">{{ $list_structure->nom_structure ?? '' }}</option>
        @endforeach
    </datalist>
@endif
@if(isset($depots))
    <datalist id="liste_depot">
        @foreach($depots as $list_depot)
            <option value="{{ $list_depot->ref_depot ?? '' }}->{{ $list_depot->design_dep ?? '' }}">{{ $list_depot->design_dep ?? '' }}</option>
        @endforeach
    </datalist>
@endif