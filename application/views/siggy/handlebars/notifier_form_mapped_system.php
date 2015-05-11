<script id="template-notification-mapped-system" type="text/x-handlebars-template">
	{{hidden 'type' notifier.type}}
	<div class="form-group">
        {{label_validation 'notifier[system_name]' 'System Name' errors}}

        {{input_validation 'notifier[system_name]' notifier.system_name errors}}
        {{field_errors 'notifier[system_name]' errors class="help-block text-error"}}
	</div>
	<div class="form-group">
        {{label_validation 'notifier[num_jumps]' 'Within number of jumps' errors}}

        {{input_validation 'notifier[num_jumps]' notifier.num_jumps errors}}
        {{field_errors 'notifier[num_jumps]' errors class="help-block text-error"}}
	</div>
	<div class="form-group">
        {{label_validation 'scope' 'Scope' errors}}

        {{select_validation "scope" scopes notifier.scope errors}}
        {{field_errors 'scope' errors class="help-block text-error"}}
	</div>
</script>
