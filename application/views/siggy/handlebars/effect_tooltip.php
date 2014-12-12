<script id="template-effect-tooltip" type="text/x-handlebars-template">
	<div id='system-effects' class='tooltip'>
	<b>Class {{sysClass}} Effects</b><br /><br />
	
	{{#each effects}}
	<b>{{this.[0]}}</b>: {{this.[1]}}<br />
	{{/each}}
	</div>
</script>