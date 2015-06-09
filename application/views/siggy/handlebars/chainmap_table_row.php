<script id="template-chainmap-table-row" type="text/x-handlebars-template">?
	<tr id='notification-history-{{ id }}' data-id='{{ id }}' class='exit-row'>
		<td class='text-center'>
			<div class="dropdown">
				<button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					{{toSystem.name}}
				</button>
				<ul class="dropdown-menu" role="menu">
					{{#isIGB}}
					<li>
					{{else}}
					<li class='disabled'>
					{{/isIGB}}
						<a onclick="javascript:CCPEVE.setDestination({{ toSystem.id }})">Set Destination</a>
					</li>
					{{#isIGB}}
					<li>
					{{else}}
					<li class='disabled'>
					{{/isIGB}}
						<a onclick="javascript:CCPEVE.showInfo(5,{{ toSystem.id }})">Show Info</a>
					</li>
					<li><a target="_blank" href="http://evemaps.dotlan.net/system/{{toSystem.name}}">DOTLAN</a></li>
				</ul>
			</div>
		</td>
		<td class='text-center'>
			<div class="dropdown">
				<button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
					{{fromSystem.name}}
				</button>
				<ul class="dropdown-menu" role="menu">
					{{#isIGB}}
					<li>
					{{else}}
					<li class='disabled'>
					{{/isIGB}}
						<a onclick="javascript:CCPEVE.setDestination({{ fromSystem.id }})">Set Destination</a>
					</li>
					{{#isIGB}}
					<li>
					{{else}}
					<li class='disabled'>
					{{/isIGB}}
						<a onclick="javascript:CCPEVE.showInfo(5,{{ fromSystem.id }})">Show Info</a>
					</li>
					<li><a target="_blank" href="http://evemaps.dotlan.net/system/{{fromSystem.name}}">DOTLAN</a></li>
				</ul>
			</div>
		</td>
		<td class='text-center'>
			{{connection.type}}
		</td>
		<td class='text-center'>
			{{ connection.created_at }}
		</td>
		<td class='text-center'>
			<button data-hash='{{ connection.hash }}' data-type='{{ connection.type }}' class='chainmap-connection-delete btn btn-danger'>Delete</button>
		</td>
	</tr>
</script>
