<script id="template-effect-tooltip" type="text/x-handlebars-template">
	<div class='siggy-tooltip'>
		<b>Class {{system.class}} Effects</b><br /><br />
		
		{{#each effect_details}}
		<b>{{this.[0]}}</b>: {{this.[1]}}<br />
		{{/each}}
	</div>
</script>