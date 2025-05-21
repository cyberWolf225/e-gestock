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
            <th class="label" style="width:15%; text-align:center">RÉF.<?php if ($griser==null) { ?><span style="color: red"><sup> *</sup></span> <?php } ?></th>
            <th class="label" style="width:40%; text-align:center">DÉSIGNATION ARTICLE</span></th>
            <th class="label" style="width:20%; text-align:center">DESCRIPTION ARTICLE</span></th>
            <th style="width:12%; text-align:center">UNITÉ</th>
            <th class="label" style="width:12%; text-align:center">QTÉ<?php if ($griser==null) { ?><span style="color: red"><sup>*</sup></span> <?php } ?></th>
            <th class="label" style="width:1%; text-align:center">ÉCHANTILLON</th>
            <th class="label" style="text-align:center; width:1%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <!--vertical-align:middle-->
            <td style="border-collapse: collapse; padding: 0; margin: 0"> 
                
                <input required <?php if ($griser!=null) { ?> style="background-color: transparent; border: none;" onfocus="this.blur()" <?php }else{ ?> style="background-color: ; border:  ;" onkeyup="editDesign(this)" list="list_article" <?php } ?> autocomplete="off" required  type="text" id="ref_articles" name="ref_articles[]" class="form-control ref_articles">

            </td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                <textarea rows="1" required autocomplete="off" required onfocus="this.blur()" style="background-color: transparent; border-color:transparent; resize:none" type="text" id="design_article" name="design_article[]" class="form-control design_article"></textarea>
            </td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                <textarea rows="1" style="border-color:transparent; resize:none" list="description_articles" autocomplete="off" type="text" id="description_articles_libelle" name="description_articles_libelle[]" class="form-control description_articles_libelle"></textarea>
            </td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                <input style="border-color:transparent;" list="list_unite" autocomplete="off" type="text" id="unites_libelle_bcs" name="unites_libelle_bcs[]" class="form-control unites_libelle_bcs"/>
            </td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                <textarea rows="1" required maxlength="12" onkeyup="editQteBcs(this)" onkeypress="validate(event)" <?php if ($griser==null) { ?> style="text-align:center; border-color:transparent; resize:none" <?php } ?> <?php if ($griser!=null) { ?> style="background-color: #e9ecef; text-align:center; border-color:transparent; resize:none" onfocus="this.blur()" <?php } ?> autocomplete="off" required type="text" id="qte_bcs"  name="qte_bcs[]" class="form-control qte_bcs"></textarea>
            </td>
            <?php $i = 0; ?>
            <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                <input style="height: 30px;" accept="image/*" type="file" name="echantillon_bcs[]">
            </td>
            <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                <a onclick="removeRow(this)"  class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg></a>
            </td>
        </tr>
        <tr style="display: none">
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
            <td style="border-collapse: collapse; padding: 0; margin: 0;"></td>
        </tr>
    </tbody>
    
</table> 
