<script id="template-jump-log-entry" type="text/x-handlebars-template">

	<li>
		<p style="float:left">
			<b>{{ship_name}}</b> {{character_name}}
			<br />
			{{ship_class}}
			<br />
			{{jumped_at}}
		</p>
		<p style="float:right">
			Mass: {{mass}} mil
		</p>
		
		<div class="clear"></div>
		<div class="center" >{{direction}} </div>
	</li>
</script>
