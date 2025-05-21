<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Réquisitions</title>
  <!-- Styles -->

  <style>
        #req{
            border-collapse: collapse;
            width: 100%;
        }
        #req td,#req th{
            border: 1px solid #ddd;
            padding: 8px;
        }

        #req th{
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color:#ccc;
            color:black;
        }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="row mt-0">
      
          <table style="background-color: #fff;" border="1px" id="req" class="table table-bordered" >
            <tr height="15px" style="height: 15px">
              <td height="15px" style="background-color: #fff; width: 20%; height: 15px;" rowspan="2">
                <img style="width: 100%;" src="{{ url('/images/capturelogo.png') }}">
              </td>
              <td height="15px" style="width: 60%; text-align: center; height: 15px">ENREGISTREMENT</td>
              <td height="15px" style="width: 20%; height: 15px" rowspan="2">Réf : EN-AEE-29<br/>Version : 01<br/>page : 1/1</td>
            </tr>
            <tr>
              <td height="15px" style="text-align: center; height: 15px">BON DE COMMANDE INTERNE<br/>(Fourniture)</td>
            </tr>
          </table>
      </div>
      <div class="row">
        <table style=" width: 100%;" >
          <tr>
            <td>
              <span>Date de la demande :  <strong>{{ date("d/m/Y",strtotime($requisitions->created_at)) }}</strong></span>
            </td>
            <td>
              <span>N° Bon de commande : <strong style="color: red">{{ $requisitions->num_bc ?? '' }}</strong></span>
            </td>
            <td><button onclick="document.location='download-pdf/{{ $requisitions->num_dem }}'" class="btn btn-link"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-printer" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
              <path d="M11 2H5a1 1 0 0 0-1 1v2H3V3a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2h-1V3a1 1 0 0 0-1-1zm3 4H2a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h1v1H2a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1z"/>
              <path fill-rule="evenodd" d="M11 9H5a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zM5 8a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H5z"/>
              <path d="M3 7.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
            </svg></button></td>
          </tr>
        </table>
      </div>
      <br> 
      <div class="row mt-3">
        <table style="width: 100%; text-align:center" >
          <tr>
            <td>
              <span>IDENTIFICATION DU DEMANDEUR</span>
            </td>
          </tr>
        </table>
      </div>
      <br>
      <div class="row">
        <table style="width: 100%;" >
          <tr>
            <td>
              <span>Structure : <strong>Direction du Système d'Informations</strong></span>
              <span style="padding-left: 50px;">Département/Service : <strong>MOMAS</strong></span>
            </td>
          </tr>
          <tr>
            <td>
              <span>Nom & Prénom(s) du demandeur : <strong>{{ $requisitions->nom_prenoms ?? '' }}</strong></span>
              <span style="padding-left: 50px;">Mle : <strong>{{ $requisitions->mle ?? '' }}</strong></span>
            </td>
          </tr>
          <tr>
            <td>
              <span>Fournisseur interne : Dépot <strong>{{ $magasins->ref_depot ?? '' }} -- {{ $magasins->design_dep ?? '' }} -- {{ $magasins->design_magasin ?? '' }}</strong></span>
            </td>
          </tr>
        </table>
      </div>
      <br> <br>
      <div class="row">
          <div class="col-12">
              <table id="req" class="table table-bordered">
                  <thead>
                    <tr style="vertical-align: middle">
                      <th style="vertical-align: middle; width: 2%;" scope="col">N°</th>
                      <th style="vertical-align: middle" scope="col">Désignations des articles</th>
                      <th style="width: 5%; text-align:center; vertical-align: middle" scope="col">Quantité demandée</th>
                      <th style="width: 5%; text-align:center; vertical-align: middle" scope="col">Quantité Validée</th>
                      <th style="width: 5%; text-align:center; vertical-align: middle" scope="col">Quantité Servie</th>
                      <th style="vertical-align: middle; text-align:center;" scope="col">Observations</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1; ?>
                    @foreach($demandes as $demande)
                        <?php $livraisons = DB::select("SELECT * FROM livraisons WHERE demandes_id = '".$demande->id."' ");
                              $valider_requisitions = DB::select("SELECT * FROM valider_requisitions WHERE demandes_id = '".$demande->id."' ");
  
                        ?>
                        @foreach($livraisons as $livraison)
                          <?php
                          //dd($livraison);
                          $qte = $livraison->qte;
                          $observation = $livraison->observation;
                          $date_livraison = $livraison->created_at;
                           ?>
                        @endforeach
  
                        @foreach($valider_requisitions as $valider_requisition)
                          <?php
                            $qte_validee = $valider_requisition->qte_validee;
                          ?>
                        @endforeach
                    <tr style="cursor: pointer">
                      <td scope="row">{{ $i }}</td>
                      <td>{{ $demande->design_article ?? '' }}</td> 
                      <td style="text-align:center">{{ $demande->qte ?? '' }}</td>
                      <td style="text-align:center">{{ $qte_validee ?? '' }}</td>
                      <td style="text-align:center">{{ $qte ?? '' }}</td>
                      <td>{{ $observation ?? '' }}</td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                  </tbody>
                </table>
                {{ $demandes->links() }}
          </div>
      </div>
      <div class="row">
        <table id="req" >
          <tr>
            <td style="text-align: center">
              <span>Signature du demandeur</span>
              <br>
              <br>
              <br>
            </td>
            <td>
              <span>Signature Service Administration et Budget (ou Comptabilité)</span>
              <br/>
              <span>Secrétariat / Offset / Magasin : <strong>{{ $magasins->design_dep ?? '' }}</strong></span>
              <br/>
              <span>Date de la livraison : <strong> <?php if(isset($date_livraison)){?> {{ date("d/m/Y",strtotime($date_livraison ?? '')) }} <?php } ?> </strong></span>
            </td>
          </tr>
        </table>
      </div>
      </div>
    </div>

</div>
</body>
</html>


