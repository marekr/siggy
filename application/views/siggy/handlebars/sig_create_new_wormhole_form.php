<script id="template-sig-create-new-wormhole" type="text/x-handlebars-template">
    <div class="box" style="display:none;width: 500px;">
        <div class="box-header"></div>
        <div class="box-content">
            <form>
                <div class="form-content">
                    <table class="siggy-table">
                        <tr>
                            <th width="15%">Sig</th>
                            <th>Type</th>
                            <th>Destination</th>
                        </tr>
                        <tr>
                            {{#each sigs}}
                                <td>{{ sig }}</td>
                                <td>
                                    <select></select>
                                </td>
                                <td>
                                    {{input_validation 'sig[destination]' destination errors class="form-control typeahead system-typeahead"}}
                                </td>
                            {{/each}}
                        </tr>
                    </table>
                </div>
                <div class="text-center form-actions">
                    <button type="submit" class="btn btn-primary btn-xs">Submit</button>
                    <button type="reset" class="btn btn-default btn-xs dialog-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</script>
