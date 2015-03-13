
	<script id="template-scanned-system-table-row" type="text/x-handlebars-template">?
		<tr id='scanned-system-{{ system_id }}' data-id='{{ system_id }}' class='exit-row'>
			<td class='center-text'>
				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="region-drop" data-toggle="dropdown" aria-expanded="true">
						{{ region_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://evemaps.dotlan.net/map/{{region_name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='center-text'>
				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="constellation-drop" data-toggle="dropdown" aria-expanded="true">
						{{ constellation_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://evemaps.dotlan.net/map/{{region_name}}/{{constellation_name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='center-text'>
				<div class="dropdown">
					<button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
						{{ system_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						{{#isIGB}}
						<li>
						{{else}}
						<li class='disabled'>
						{{/isIGB}}
							<a onclick="javascript:CCPEVE.setDestination({{ system_id }})">Set Destination</a>
						</li>
						{{#isIGB}}
						<li>
						{{else}}
						<li class='disabled'>
						{{/isIGB}}
							<a onclick="javascript:CCPEVE.showInfo(5,{{ system_id }})">Show Info</a>
						</li>
						<li><a target="_blank" href="http://evemaps.dotlan.net/system/{{system.name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='center-text'>
				{{ displayTimestamp last_scan }}
			</td>
		</tr>
	</script>