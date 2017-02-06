<script id="template-dialog-base" type="text/x-handlebars-template">
	<div class="dialog" style="display:none;">
		<div class='dialog-header'>{{title}}</div>
		<div class='dialog-content'>
			{{#is type "==" 'confirm'}}
				{{message}}
			{{/is}}
			{{#is type "==" 'alert'}}
				{{message}}
			{{/is}}
		</div>
		<div class="dialog-footer">
			
		</div>
	</div>
</script>