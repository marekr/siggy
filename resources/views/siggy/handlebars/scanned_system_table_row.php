
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
							<a class='eve-set-destination' data-system-id='{{ id }}'>Set Destination</a>
						</li>
						{{#isIGB}}
						<li>
						{{else}}
						<li class='disabled'>
						{{/isIGB}}
							<a class='eve-show-system-info-by-id' data-system-id='{{ id }}'>Show Info</a>
						</li>
						<li><a target="_blank" href="http://evemaps.dotlan.net/system/{{system_name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='text-center'>
				{{ last_scan }}
			</td>
			<td class='text-center'>
				<a href="/system/{{ system_name }}" data-navigo class='btn btn-primary'>View</button>
			</td>
		</tr>
	</script>
