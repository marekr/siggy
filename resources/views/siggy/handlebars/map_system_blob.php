
<script id="template-chainmap-system-blob" type="text/x-handlebars-template">
	<div class="map-system-blob {{systemNameExtraClasses system}}" id="map-system-{{system.id}}" data-system-id="{{system.id}}" data-system-name="{{system.name}}" data-hasqtip="82" style="top: 100px; left: 15px;">
		<div class="map-system-blob-title">

			{{#if system.showClass}}
				<span class="map-system-blob-class {{systemClassTextColor system.system_class }}">{{systemClassShortText system.system_class}}</span>
			{{/if}}
			<span></span>
			{{#is system.effect "!=" 0 }}
			<span class='map-effect {{ systemEffectIDToColor system.effect }}' title='{{ systemEffectIDToText system.effect }}'></span>
			{{/is}}
			<span>
				{{#is system.system_class "==" 9 }}
					{{#is system.kills_in_last_2_hours ">" 0 }}
						<img src="images/evekill.png" class="map-system-blob-mini-icon" title="Kills in last 2 hours"
					{{/is}}
					{{#is system.npcs_kills_in_last_2_hours ">" 0 }}
						<img src="images/carebear.gif" class="map-system-blob-mini-icon" title="NPC Kills in last 2 hours"
					{{/is}}
				{{/is}}

			</span>
			{{#is system.rally "==" 1 }}
			<i class="fa fa-exclamation-triangle"></i>
			{{/is}}
			<span class="map-system-blob-sysname">
				{{#if system.display_name }}
					{{{system.display_name}}}
				{{else}}
					{{system.name}}
				{{/if}}
			</span>
			<span>
				{{#isKSpaceClass system.system_class}}
					{{#if system.region_name }}
					<i style="font-size:9px">
						{{system.region_name}}
					</i>
					{{/if}}
				{{/isKSpaceClass}}
			</span>
			{{#is system.rally "==" 1 }}
			<i class="fa fa-exclamation-triangle"></i>
			{{/is}}
		</div>
		<div class="map-system-blob-actives"></div>
	</div>
</script>
