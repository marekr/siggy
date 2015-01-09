
	<script id="template-thera-table-row" type="text/x-handlebars-template">?
		<tr id='thera-sig-{{ id }}' data-id='{{ id }}' class='exit-row'>
			<td class='center-text'>

				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
						{{ system.region_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a target="_blank" href="http://evemaps.dotlan.net/map/{{system.region_name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='center-text'>
				<div class="dropdown">
					<button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
						{{ system.name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						{{#isIGB}}
						<li>
						{{else}}
						<li class='disabled'>
						{{/isIGB}}
							<a onclick="javascript:CCPEVE.setDestination(system.id )">Set Destination</a>
						</li>
						{{#isIGB}}
						<li>
						{{else}}
						<li class='disabled'>
						{{/isIGB}}
							<a onclick="javascript:CCPEVE.showInfo(5,{{ system.id }})">Show Info</a>
						</li>
						<li><a target="_blank" href="http://evemaps.dotlan.net/system/{{system.name}}">DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='center-text'>
				{{ system.sec }}
			</td>
			<td class='wormhole-type center-text'>
				{{ wormhole_name }}
			</td>
			<td class='center-text'>
				{{ out_signature }}
			</td>
			<td class='center-text'>
				{{ in_signature }}
			</td>
			<td class='center-text age'>
				<span class='age-clock'>--</span>
				<p class='eol-clock'></p>
			</td>
		</tr>
	</script>
