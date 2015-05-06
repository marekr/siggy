<script id="template-notification-resident-found" type="text/x-handlebars-template">
	{{hidden "type" notifier.type}}
	<div class="form-group">
        {{label_validation 'resident_name' 'Resident Name' errors}}

        {{input_validation 'resident_name' notifier.resident_name errors}}
        {{field_errors 'resident_name' errors class="help-block text-error"}}
	</div>

	<div class="form-group">
        {{label_validation 'scope' 'Scope' errors}}

        {{select_validation "scope" scopes notifier.scope errors}}
        {{field_errors 'scope' errors class="help-block text-error"}}
	</div>
</script>
