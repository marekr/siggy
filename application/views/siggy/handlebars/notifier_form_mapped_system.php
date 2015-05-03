<script id="template-notification-mapped-system" type="text/x-handlebars-template">
	<div class="form-group">
        {{label_validation 'system_name' 'System Name' errors}}

        {{input_validation 'system_name' notifier.system_name errors}}
        {{field_errors 'system_name' errors class="help-block text-error"}}
	</div>

	<div class="form-group">
        {{label_validation 'scope' 'Scope' errors}}

        {{select_validation "scope" scopes notifier.scope errors}}
        {{field_errors 'scope' errors class="help-block text-error"}}
	</div>
</script>
