<script id="template-dialog-pos" type="text/x-handlebars-template">
		<form>
			{{hidden "id" model.id}}
			<div class="form-group {{#field_errors 'location_planet' errors}}has-error {{/field_errors}} {{#field_errors 'location_moon' errors}}has-error {{/field_errors}}">
				<div style="width:40%;display:inline-block">
				{{label_validation 'location_planet' 'Planet' errors class="control-label"}}
				{{input_validation 'location_planet' model.location_planet errors class="form-control" maxlength="3"}}
				</div>
				<div style="width:40%;display:inline-block">
					{{label_validation 'location_moon' 'Moon' errors class="control-label"}}
					{{input_validation 'location_moon' model.location_moon errors class="form-control" maxlength="3"}}
				</div>
				<div class="clear"></div>
				{{field_errors 'location_planet' errors class="help-block text-error"}}
				{{field_errors 'location_moon' errors class="help-block text-error"}}
			</div>
			<div class="form-group {{#field_errors 'owner' errors}}has-error{{/field_errors}}">
				{{label_validation 'owner' 'Owner' errors class="control-label"}}
				
				{{input_validation 'owner' model.owner errors class="form-control"}}
				{{field_errors 'owner' errors class="help-block text-error"}}
			</div>

			<div class="form-group {{#field_errors 'type_id' errors}}has-error{{/field_errors}}">
				{{label_validation 'type_id' 'Faction' errors class="control-label"}}

				{{select_validation 'type_id' posTypes model.type_id errors class="form-control"}}
				{{field_errors 'type_id' errors class="help-block text-error"}}
			</div>

			<div class="form-group {{#field_errors 'size' errors}}has-error{{/field_errors}}">
				{{label_validation 'size' 'Size' errors class="control-label"}}

				{{select_validation 'size' posSizes model.size errors class="form-control"}}
				{{field_errors 'size' errors class="help-block text-error"}}
			</div>
			
			<div class="form-group {{#field_errors 'online' errors}}has-error{{/field_errors}}">
				{{label_validation 'online' 'Status' errors class="control-label"}}

				{{select_validation 'online' posStatuses model.online errors class="form-control"}}
				{{field_errors 'online' errors class="help-block text-error"}}
			</div>

			<div class="form-group {{#field_errors 'notes' errors}}has-error{{/field_errors}}">
				{{label_validation 'notes' 'Notes' errors class="control-label"}}

				{{textarea_validation 'notes' model.notes errors class="form-control"}}
				{{field_errors 'notes' errors class="help-block text-error"}}
			</div>
		</form>
</script>
