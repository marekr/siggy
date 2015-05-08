<script id="template-notification-site-found" type="text/x-handlebars-template">
	{{hidden "type" notifier.type}}
	<div class="form-group">
        {{label_validation 'notifier[site_id]' 'Site' errors}}

        {{select_validation "notifier[site_id]" sites notifier.site_id errors}}
        {{field_errors 'notifier[site_id]' errors class="help-block text-error"}}
	</div>

	<div class="form-group">
        {{label_validation 'scope' 'Scope' errors}}

        {{select_validation "scope" scopes notifier.scope errors}}
        {{field_errors 'scope' errors class="help-block text-error"}}
	</div>
</script>
