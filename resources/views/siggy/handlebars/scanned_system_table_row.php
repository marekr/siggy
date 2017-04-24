
	<script id="template-scanned-system-table-row" type="text/x-handlebars-template">?
		<tr id='scanned-system-{{ id }}' data-id='{{ system_id }}' class='exit-row'>
			<td class='text-center'>
				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="region-drop" data-toggle="dropdown" aria-expanded="true">
						{{ region_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://evemaps.dotlan.net/map/{{region_name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='text-center'>
				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="constellation-drop" data-toggle="dropdown" aria-expanded="true">
						{{ constellation_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://evemaps.dotlan.net/map/{{region_name}}/{{constellation_name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='text-center'>
				<div class="dropdown">
					<button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
						{{ system_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a onclick="javascript:siggy2.Eve.SetDestination({{ system_id }})">Set Destination</a>
						</li>
						{{#isIGB}}
						<li>
						{{else}}
						<li class='disabled'>
						{{/isIGB}}
							<a onclick="javascript:siggy2.Eve.ShowSystemInfoById({{ system_id }})">Show Info</a>
						</li>
						<li><a target="_blank" href="http://evemaps.dotlan.net/system/{{system.name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='text-center'>
				{{ last_scan }}
			</td>
			<td class='text-center'>
				<button data-id='{{ id }}' class='scanned-system-view btn btn-primary'>View</button>
			</td>
		</tr>
	</script>
