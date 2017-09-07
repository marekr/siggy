<script id="template-dialog-dscan" type="text/x-handlebars-template">
		<form>
			<div class="form-group {{#field_errors 'dscan_title' errors}}has-error{{/field_errors}}">
				{{label_validation 'dscan_title' 'Title' errors class="control-label"}}
				
				{{input_validation 'dscan_title' model.dscan_title errors class="form-control"}}
				{{field_errors 'dscan_title' errors class="help-block text-error"}}
			</div>
			<div class="form-group {{#field_errors 'blob' errors}}has-error{{/field_errors}}">
				{{label_validation 'blob' 'Scan' errors class="control-label"}}

				{{textarea_validation 'blob' model.blob errors class="form-control" rows="10"}}
				{{field_errors 'blob' errors class="help-block text-error"}}
			</div>
		</form>
</script>
