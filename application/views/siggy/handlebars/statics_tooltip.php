<script id="template-statics-tooltip" type="text/x-handlebars-template">
	<div id='static-info-{{ id }}' class='tooltip'>
	<b>{{name}} {{destBlurb}}</b><br /><br />
	
	<b>Mass:</b> {{ numberFormat mass }} kg<br />
	<b>Max Jumpable Mass:</b> {{ numberFormat jump_mass }} kg<br />
	<b>Max Lifetime:</b> {{ lifetime }} hrs<br />
	{{#if sig_size}}
	<b>Signature Size:</b> {{ sig_size }} <br />
	{{/if}}
	</div>
</script>