@if($griser === null)
    <br/>
    <span>
    <a style="cursor: pointer; color:cadetblue; font-style:italic; font-weight:bold" onclick="myCreateFunctionBcn()" class="addRow">
    (Ajouter une nouvelle ligne)
    </a>
    </span>
    <br/><br/>
@endif
<table class="table table-bordered table-striped" id="myTableBcn" width="100%">
    <thead>
        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
            <th style="width:50%; text-align:center">DÉSIGNATION <span style="color: red"><sup> *</sup></span></th>
            <th style="width:20%; text-align:center">UNITÉ</th>
            <th style="width:10%; text-align:center">QTÉ<span style="color: red"><sup> *</sup></span></th>
            <th class="label" style="width:1%; text-align:center">ÉCHANTILLON</th>
            <th style="text-align:center; width:1%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        
        <tr>
            <td style="border-collapse: collapse; padding: 0; margin: 0;">
                <input list="list_service" autocomplete="off" required type="text"  id="libelle_service" name="libelle_service[]" class="form-control libelle_service" 
                
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
                <input autocomplete="off" required type="text" onkeyup="editQteBcn(this)" onkeypress="validate(event)" id="qte_bcn" name="qte_bcn[]" class="form-control qte_bcn" 
                
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
        
    </tbody>
    
</table>
