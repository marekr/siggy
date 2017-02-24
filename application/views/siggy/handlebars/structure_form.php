<script id="template-structure-form" type="text/x-handlebars-template">
    <div class='box sig-create-new-wormhole' style="display:none;width: 500px;">
        <div class="box-header">Create new wormholes for sig(s)</div>
        <div class="box-content">
            <p>You may leave destinations blank if you do not want to create a wormhole for a sig</p>
            <form>
                <div class="form-content">
                    <table class="siggy-table">
                        <tr>
                            <th width="15%">Sig</th>
                            <th>Type</th>
                            <th>Destination</th>
                        </tr>
                        {{#each sigs}}
                        <tr>
                        	   {{hidden 'sig_id' id}}
                                <td>{{ sig }}</td>
                                <td>
                                    {{select_validation "sig_site_id" ../wormholeTypes site_id errors}}
                                </td>
                                <td>
                                    {{input_validation 'sig_wh_destination' wh_destination errors class="form-control typeahead sig-system-typeahead"}}
                                </td>
                        </tr>
                        {{/each}}
                    </table>
                </div>
                <div class="text-center form-actions">
                    <button type="submit" class="btn btn-primary btn-xs">Create</button>
                    <button type="reset" class="btn btn-default btn-xs dialog-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</script>
