
	<script id="template-sig-table-row" type="text/x-handlebars-template">?
		<tr id='sig-{{ sigID }}' class='type-{{ type }} sig' data-sig-id='{{ sigID }}'>
			<td class='center-text edit'>
				<i class='icon icon-pencil icon-large'></i>
			</td>
			<td class='center-text sig'>
				{{ sig }}
			</td>
			{{#if showSigSizeCol}}
			<td class='center-text size'>
				{{ sigSize }}
			</td>
			{{/if}}
			<td class='center-text type'>
				<p>
					{{ sigTypeToText type }}
				</p>
			</td>
			<td class='desc'>
				{{ siteIDToText sysClass type siteID }}
				<p>
					{{ description }}
				</p>
			</td>
			<td class='center-text moreinfo'>
				<i class='icon icon-info-sign icon-large icon-yellow'></i>
				<div id='creation-info-{{ sigID }}' class='tooltip'>
					<b>Added by </b>: {{ creator }}
					{{#if lastUpdater }}
						<br /><b>Updated by:</b> {{ lastUpdater }} 
						<br /><b>Updated at:</b> {{ displayTimestamp updated }}
					{{/if}}
				</div>
			</td>
			<td class='center-text age'>
				<span class='age-clock'>--</span>
				<span class='eol-clock'><br /></span>
				<div id='age-timestamp-{{ sigID }}' class='tooltip'>
					{{ displayTimestamp created }}
				</div>
			</td>
			<td class='center-text remove'>
				<i class='icon icon-remove-sign icon-large icon-red'></i>
			</td>
		</tr>
	</script>