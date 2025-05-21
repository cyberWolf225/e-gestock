@if($griser === null)
    <br/>
    <span>
    <a style="cursor: pointer; color:cadetblue; font-style:italic; font-weight:bold" onclick="myCreateFunctionBcn()" class="addRow">
    (Ajouter une nouvelle ligne)
    </a>
    </span>
    <br/><br/>
@endif
<table class="table table-striped" id="myTableBcn" width="100%">
    <thead>
        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
            <th style="width:50%; text-align:left">DÉSIGNATION @if($griser === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th style="width:20%; text-align:left">UNITÉ</th>
            <th style="width:10%; text-align:center">QTÉ @if($griser === null)<span style="color: red"><sup> *</sup></span> @endif </th>
            <th class="label" style="width:1%; text-align:center; white-space:nowrap">@if($griser === null) ÉCHANTILLON @endif</th>
            @if($griser === null)
                <th style="text-align:center; width:1%">&nbsp;</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if(isset($demande_cotation))
            @if($demande_cotation->libelle === "Commande non stockable")
            <?php $i = 1; ?>
            @if(isset($detail_demande_cotations))
            @foreach($detail_demande_cotations as $detail_demande_cotation)

                <?php
                    $echantillon = $detail_demande_cotation->echantillon;
                    $unites_libelle = null;

                    if(isset($detail_demande_cotation->code_unite)){
                        $get_unite = DB::table('unites')
                        ->where('code_unite',$detail_demande_cotation->code_unite)
                        ->first();

                        if($get_unite != null){
                            $unites_libelle = $get_unite->unite;
                        }
                    }
                ?>
                <tr>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">

                        <input required style="background-color: transparent; border: none; display:none" onfocus="this.blur()" id="detail_demande_cotations_id" name="detail_demande_cotations_id[]" class="form-control detail_demande_cotations_id" value="{{ $detail_demande_cotation->detail_demande_cotations_id ?? '' }}"/>

                        <input autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service" 
                        
                        @if($griser === 1)
                            style="background-color:transparent; border:none" onfocus="this.blur()"
                        @endif

                        @if($griser === null)
                            list="list_service"
                        @endif

                        value="{{ $detail_demande_cotation->services_libelle ?? '' }}"
                        >
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" type="text"  id="unites_libelle_bcn" name="unites_libelle_bcn[]" class="form-control unites_libelle_bcn" 
                        
                        @if($griser === 1)
                            style="background-color:transparent; border:none" onfocus="this.blur()"
                        @endif

                        @if($griser === null)
                            list="list_unite"
                        @endif

                        value="{{ $unites_libelle ?? '' }}"
                        >
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" required type="text"  id="qte_bcn" name="qte_bcn[]" class="form-control qte_bcn" 
                        
                        @if(isset($griser))
                            style="background-color:transparent; border:none; text-align:center" onfocus="this.blur()"
                        @endif

                        @if($griser === null)
                            style="text-align:center" onkeyup="editQteBcn(this)" onkeypress="validate(event)"
                        @endif

                        value="{{ $detail_demande_cotation->qte_accordee ?? '' }}"
                        >
                    </td>
                    <td style="vertical-align: middle; text-align:left; padding: 0; margin: 0; white-space:nowrap">
                        @if($griser === null)
                            <input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcn[]">

                            <input style="@if(isset($echantillon)) display: ; @else display:none; @endif " type="checkbox" name="echantillon_bcn_flag[]" checked value="{{ $detail_demande_cotation->detail_demande_cotations_id ?? '' }}" >
                        @endif
                        
                        @if(isset($echantillon))

                            <!-- Modal -->

                                <a href="#" data-toggle="modal" data-target="#exampleModalCenter{{ $i }}">
                                        <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                </a>
                                <div class="modal fade" id="exampleModalCenter{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="text-align: center">
                                        <h5 class="modal-title" id="exampleModalLongTitle">{{ $detail_demande_cotation->services_libelle ?? 'Échantillon CNPS' }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <img src='{{ asset('storage/'.$echantillon) }}' style='width:100%;'>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            <!-- Modal -->
                            
                        @endif

                    </td>
                    @if($griser === null)
                        <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                            <a style="cursor: pointer" onclick="removeRowBcn(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg></a>
                        </td>
                    @endif
                </tr>
                <?php $i++; ?>
            @endforeach
            @endif
            @endif

            @if($demande_cotation->libelle === "Demande d'achats")
            <tr>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">

                    <input list="list_service" autocomplete="off" type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service" 
                    
                    @if(isset($griser))
                        style="background-color: #e9ecef" onfocus="this.blur()"
                    @endif
                    >
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <input list="list_unite" autocomplete="off" type="text"  id="unites_libelle_bcn" name="unites_libelle_bcn[]" class="form-control unites_libelle_bcn" 
                    
                    @if(isset($griser))
                        style="background-color: #e9ecef" onfocus="this.blur()"
                    @endif
                    >
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <input autocomplete="off" type="text" onkeyup="editQteBcn(this)" onkeypress="validate(event)" id="qte_bcn" name="qte_bcn[]" class="form-control qte_bcn" 
                    
                    @if(isset($griser))
                        style="background-color: #e9ecef; text-align:center" onfocus="this.blur()"
                        @else
                        style="text-align:center"
                    @endif
    
                    >
                </td>
                <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                    <input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcn[]">
                </td>
                <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                    @if(!isset($griser))
                        
                    
                    <a onclick="removeRowBcn(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg></a>
                    
                    @endif
                </td>
            </tr>
            @endif
        @endif
    </tbody>
    
</table>
