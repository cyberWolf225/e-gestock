
@if($griser === null)
    <br/>
    <span>
    <a style="cursor: pointer; color:cadetblue; font-style:italic; font-weight:bold" onclick="myCreateFunction()" class="addRow">
    (Ajouter un nouvel article)
    </a>
    </span>
    <br/><br/>
@endif

<table class="table table-striped" id="myTable" style="width:100%">
    <thead>
        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
            <th class="label" style="width:15%; text-align:left">RÉF.<?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></th>
            <th class="label" style="width:40%; text-align:left">DÉSIGNATION ARTICLE</span></th>
            <th class="label" style="width:20%; text-align:left">DESCRIPTION ARTICLE</span></th>
            <th style="width:12%; text-align:left">UNITÉ</th>
            <th class="label" style="width:12%; text-align:center">QTÉ<?php if ($griser==null) { ?><span style="color: red"><sup>*</sup></span> <?php } ?></th>
            <th class="label" style="width:1%; text-align:center">@if($griser === null)ÉCHANTILLON @endif</th>
            <th class="label" style="text-align:center; width:1%; @if($griser === 1) display:none @endif ">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($demande_cotation))
            @if($demande_cotation->libelle === "Demande d'achats")
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

                    $description_articles_libelle = null;

                    if(isset($detail_demande_cotation->description_articles_id)){
                        $get_description_article = DB::table('description_articles')
                        ->where('id',$detail_demande_cotation->description_articles_id)
                        ->first();

                        if($get_description_article != null){
                            $description_articles_libelle = $get_description_article->libelle;
                        }
                    }
                ?>
                <tr>
                    <!--vertical-align:middle-->
                    <td style="border-collapse: collapse; padding: 0; margin: 0"> 

                        <input required style="background-color: transparent; border: none; display:none" onfocus="this.blur()" id="detail_demande_cotations_id" name="detail_demande_cotations_id[]" class="form-control detail_demande_cotations_id" value="{{ $detail_demande_cotation->detail_demande_cotations_id ?? '' }}"/>

                        <input required 
                        
                        @if ($griser === 1) 
                        style="background-color: transparent; border: none;" onfocus="this.blur()" 
                        @else
                        onkeyup="editDesign(this)" list="list_article" 
                        @endif 
                        
                        autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles" value="{{ $detail_demande_cotation->ref_articles ?? '' }}">

                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <textarea rows="1" required autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article"                        
                        >{{ $detail_demande_cotation->design_article ?? '' }}</textarea>
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <textarea rows="1" autocomplete="off" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle"
                        
                        @if ($griser === 1) 
                        style="background-color: transparent; resize:none; border: none;" onfocus="this.blur()" 
                        @endif

                        @if ($griser === null) 
                        style="resize:none;"
                        list="description_articles" 
                        @endif

                        >{{ $description_articles_libelle ?? '' }}</textarea>
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <input autocomplete="off" type="text" id="unites_libelle_bcs" name="unites_libelle_bcs[]" class="form-control unites_libelle_bcs" value="{{ $unites_libelle ?? '' }}" 
                        
                        @if ($griser === 1) 
                        style="background-color: transparent; border: none;" onfocus="this.blur()" 
                        @else list="list_unite"  style="border-color:transparent;"
                        @endif 

                        />
                    </td>
                    <td style="border-collapse: collapse; padding: 0; margin: 0;">
                        <textarea rows="1" required maxlength="12"
                        
                        @if ($griser === null) style="text-align:center; border-color:transparent; resize:none" onkeyup="editQteBcs(this)" onkeypress="validate(event)"
                        @endif 
                        
                        @if ($griser === 1) 
                            style="background-color: transparent; border:none; text-align:center; border-color:transparent; resize:none" onfocus="this.blur()" 
                        @endif
                        
                        autocomplete="off" required type="text" id="qte_bcs"  name="qte_bcs[]" class="form-control qte_bcs">{{ $detail_demande_cotation->qte_accordee ?? '' }}</textarea>
                    </td>

                    <td style="vertical-align: middle; text-align:; padding: 0; margin: 0; white-space:nowrap;">
                        @if ($griser === null)
                            <input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcs[]">

                            <input style="@if($echantillon != null) display: ; @else display:none; @endif " type="checkbox" name="echantillon_bcs_flag[]" checked value="{{ $detail_demande_cotation->detail_demande_cotations_id ?? '' }}" >
                        @endif

                        @if($echantillon != null)

                            <!-- Modal -->

                                <a data-toggle="modal" data-target="#exampleModalCenter{{ $i }}">
                                        <svg style="cursor: pointer; color:green; margin-left:5px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                        </svg>
                                </a>
                                <div class="modal fade" id="exampleModalCenter{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="text-align: center">
                                        <h5 class="modal-title" id="exampleModalLongTitle">{{ $detail_demande_cotation->design_article ?? 'Échantillon CNPS' }}</h5>
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
                    <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0; @if($griser === 1) display:none @endif ">
                        <a style="cursor: pointer" onclick="removeRow(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg></a>
                    </td>
                </tr>
                <?php $i++; ?>
            @endforeach
            @endif
            @endif

            @if($demande_cotation->libelle === "Commande non stockable")
            <tr>
                <td style="border-collapse: collapse; padding: 0; margin: 0"> 
                    
                    <input <?php if ($griser!=null) { ?> style="background-color: transparent; border: none;" onfocus="this.blur()" <?php }else{ ?> style="background-color: ; border:  ;" onkeyup="editDesign(this)" list="list_article" <?php } ?> autocomplete="off"  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">
    
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <textarea rows="1" autocomplete="off" onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article"></textarea>
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <textarea rows="1" style="border-color:transparent; resize:none" list="description_articles" autocomplete="off" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle"></textarea>
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <input style="border-color:transparent;" list="list_unite" autocomplete="off" type="text" id="unites_libelle_bcs" name="unites_libelle_bcs[]" class="form-control unites_libelle_bcs"/>
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <textarea rows="1" maxlength="12" onkeyup="editQteBcs(this)" onkeypress="validate(event)" <?php if ($griser==null) { ?> style="text-align:center; border-color:transparent; resize:none" <?php } ?> <?php if ($griser!=null) { ?> style="background-color: #e9ecef; text-align:center; border-color:transparent; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" type="text" id="qte_bcs"  name="qte_bcs[]" class="form-control qte_bcs"></textarea>
                </td>
                <?php $i = 0; ?>
                <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                    <input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcs[]">
                </td>
                <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                    <a style="cursor: pointer" onclick="removeRow(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg></a>
                </td>
            </tr>
            @endif
        @endif
        <tr style="display: none">
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
        </tr>
    </tbody>
    
</table> 
