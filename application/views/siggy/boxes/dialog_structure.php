<script id="template-dialog-structure" type="text/x-handlebars-template">
		<form>
			{{hidden "id" model.id}}
			<div class="form-group {{#field_errors 'corporation_name' errors}}has-error{{/field_errors}}">
				{{label_validation 'corporation_name' 'Corporation' errors class="control-label"}}
				
				{{input_validation 'corporation_name' model.corporation_name errors class="form-control typeahead"}}
				{{field_errors 'corporation_name' errors class="help-block text-error"}}
			</div>
			<div class="form-group {{#field_errors 'type_id' errors}}has-error{{/field_errors}}">
				{{label_validation 'type_id' 'Structure' errors class="control-label"}}

				{{select_validation 'type_id' structureTypes model.type_id errors class="form-control"}}
				{{field_errors 'type_id' errors class="help-block text-error"}}
			</div>
			<div class="form-group {{#field_errors 'notes' errors}}has-error{{/field_errors}}">
				{{label_validation 'notes' 'Notes' errors class="control-label"}}

				{{textarea_validation 'notes' model.notes errors class="form-control"}}
				{{field_errors 'notes' errors class="help-block text-error"}}
			</div>
		</form>
</script>
