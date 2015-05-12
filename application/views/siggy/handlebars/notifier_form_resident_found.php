<script id="template-notification-system-resident-found" type="text/x-handlebars-template">
	{{hidden "type" notifier.type}}
	<div class="form-group">
        {{label_validation 'notifier[resident_name]' 'Resident Name' errors}}

        {{input_validation 'notifier[resident_name]' notifier.resident_name errors}}
        {{field_errors 'notifier[resident_name]' errors class="help-block text-error"}}
	</div>

	<div class="form-group">
        {{label_validation 'notifier[include_offline]' 'Include offline towers?' errors}}
		{{checkbox 'notifier[include_offline]' '1' false}}
        {{field_errors 'notifier[include_offline]' errors class="help-block text-error"}}
	</div>
	<div class="form-group">
        {{label_validation 'scope' 'Scope' errors}}

        {{select_validation "scope" scopes notifier.scope errors}}
        {{field_errors 'scope' errors class="help-block text-error"}}
	</div>
</script>
