
	<script id="template-thera-table-row" type="text/x-handlebars-template">?
		<tr id='thera-sig-{{ id }}' data-id='{{ id }}' class='data-row'>
			<td class='center-text'>
				
				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
						{{ system.region_name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a>DOTLAN</a></li>
					</ul>
				</div>
			</td>
			<td class='center-text'>
				<div class="dropdown">
					 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
						{{ system.name }}
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a>Set Destination</a></li>
						<li><a>Show Info</a></li>
						<li><a>DOTLAN</a></li>
					</ul>
				</div>
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