	<script id="template-sig-table-row" type="text/x-handlebars-template">
		<tr id='sig-{{ id }}' class='type-{{ type }} sig' data-sig-id='{{ id }}'>
			<td class='text-center edit'>
				<i class='icon icon-pencil icon-large'></i>
			</td>
			<td class='text-center sig'>
				{{ sig }}
			</td>
			{{#if showSigSizeCol}}
			<td class='text-center size'>
				{{ sigSize }}
			</td>
			{{/if}}
			<td class='text-center type'>
				<p>
					{{ sigTypeToText type }}
				</p>
			</td>
			<td class='description'>
				
				{{ siteIDToText sysClass type siteID }}
				{{#if showWormhole}}
					{{ whHashToDestination chainmap_wormhole }}
				{{/if}}
				<p>
					{{{ description }}}
				</p>
			</td>
			<td class='text-center moreinfo'>
				<i class='icon icon-info-sign icon-large icon-yellow'></i>
				<div id='creation-info-{{ id }}' class='tooltip'>
					<b>Added by:</b> {{ creator }}
					{{#if lastUpdater }}
						<br /><b>Updated by:</b> {{ lastUpdater }}
						<br /><b>Updated at:</b> {{ updated_at }}
					{{/if}}
				</div>
			</td>
			<td class='text-center age'>
				<span class='age-clock'>--</span>
				<p class='eol-clock'></p>
				<div id='age-timestamp-{{ id }}' class='tooltip'>
					{{ created_at }}
				</div>
			</td>
			<td class='text-center remove'>
				<i class='icon icon-remove-sign icon-large icon-red'></i>
			</td>
		</tr>
	</script>
