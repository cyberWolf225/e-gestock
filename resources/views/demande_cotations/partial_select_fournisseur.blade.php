<div class="row" style="margin-top: -10px;">
    <div class="col-md-3 pr-1">
        <div class="form-group">
            <input style="display: none" autocomplete="off" type="text" name="fournisseur" id="fournisseur" class="form-control @error('fournisseur') is-invalid @enderror fournisseur">

            @error('fournisseur')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>
    </div>
</div>

<table class="table table-striped" id="myTableOrg" width="100%">
    <thead>
        <tr style=" background-color:#aabcc6; text-align: center; color:#033d88; text-shadow: 2px 5px 5px white ;">
            @if($griser === null)
            <th style="text-align:center; white-space:nowrap; width:1%">IDENTIFIANT <span style="color: red"><sup> *</sup></span> </th>
            @endif
            <th style="text-align:left; white-space:nowrap; width:15%">N° CNPS</th>
            <th style="text-align:left">RAISON SOCIALE @if($griser === null)<span style="color: red"><sup> *</sup></span> @endif</th>
            <th style="text-align:left; white-space:nowrap; width:25%">SIGLE</th>
            @if($griser === null)
            <th style="text-align:center; width:1%">
                &nbsp;
            </th>
            @endif
        </tr>
    </thead>
    <tbody>
        <?php $display_default_tr = 1 ;?>
        @if(isset($fournisseur_demande_cotations))
            @if(count($fournisseur_demande_cotations) > 0)
                <?php $display_default_tr = 0;?>
                @foreach ($fournisseur_demande_cotations as $fournisseur_demande_cotation)
                    <tr>
                        @if($griser === null)
                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                            <input style="text-align:center; display:none" autocomplete="off" type="text"  id="fournisseur_demande_cotations_id" name="fournisseur_demande_cotations_id[]" class="form-control fournisseur_demande_cotations_id"
                                style="background-color:transparent; border:none" onfocus="this.blur()"
                            value="{{ $fournisseur_demande_cotation->fournisseur_demande_cotations_id ?? '' }}"
                            >

                            <input autocomplete="off" type="text"  id="organisations_id" name="organisations_id[]" class="form-control organisations_id" 
                            
                            @if($griser === 1)
                                style="background-color:transparent; border:none; text-align:center" onfocus="this.blur()"
                            @endif
            
                            @if($griser === null)
                            style="text-align:center" list="list_organisation" onkeyup="editOrganisation(this)"
                            @endif
                            value="{{ $fournisseur_demande_cotation->organisations_id ?? '' }}"
                            >
                        </td>
                        @endif
                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                            <input autocomplete="off" type="text"  id="entnum" name="entnum[]" class="form-control entnum" style="background-color:transparent; border:none" onfocus="this.blur()" value="{{ $fournisseur_demande_cotation->entnum ?? '' }}" >
                        </td>
                        <td style="border-collapse: collapse; padding: 0; margin: 0;">
                            <input autocomplete="off" type="text" id="denomination" name="denomination[]" class="form-control denomination" style="background-color:transparent; border:none;" onfocus="this.blur()" value="{{ $fournisseur_demande_cotation->denomination ?? '' }}" >
                        </td>
                        <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                            <input autocomplete="off" type="text" id="sigle" name="sigle[]" class="form-control sigle" style="background-color:transparent; border:none;" onfocus="this.blur()" value="{{ $fournisseur_demande_cotation->sigle ?? '' }}">
                        </td>
                        @if($griser === null)
                        <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                            <a style="cursor:pointer;" onclick="removeRowOrg(this)" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                            </svg></a>
                        </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        @endif
        @if($display_default_tr === 1 && $griser === null)
            <tr>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <input style="text-align:center" autocomplete="off" type="text"  id="organisations_id" name="organisations_id[]" class="form-control organisations_id" 
                    
                    @if($griser === 1)
                        style="background-color:transparent; border:none" onfocus="this.blur()"
                    @endif

                    @if($griser === null)
                        list="list_organisation" onkeyup="editOrganisation(this)"
                    @endif
                    >
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <input autocomplete="off" type="text"  id="entnum" name="entnum[]" class="form-control entnum" style="background-color:transparent; border:none" onfocus="this.blur()">
                </td>
                <td style="border-collapse: collapse; padding: 0; margin: 0;">
                    <input autocomplete="off" type="text" id="denomination" name="denomination[]" class="form-control denomination" style="background-color:transparent; border:none;" onfocus="this.blur()">
                </td>
                <td style="vertical-align: middle; text-align:center; padding: 0; margin: 0;">
                    <input autocomplete="off" type="text" id="sigle" name="sigle[]" class="form-control sigle" style="background-color:transparent; border:none;" onfocus="this.blur()">
                </td>
                @if($griser === null)
                <td style="vertical-align: middle; text-align:center; border-collapse: collapse; padding: 0; margin: 0;">
                    <a style="cursor:pointer;" onclick="removeRowOrg(this)" class="remove" title="Retirer cet article"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x" style="color: red; font-weight:bold; font-size:15px;" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg></a>
                </td>
                @endif
            </tr>
        @endif
    </tbody>
    
</table>
