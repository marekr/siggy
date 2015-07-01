<div id="activity-astrolabe" class="wrapper" style="display:none">
<style type='text/css'>
</style>
	<form class="form-inline" id='astrolabe-new-waypoint-form'>
		<div class="form-group">
			<label for='astrolabe-new-waypoint-system'>New Waypoint </label>
			<input id='astrolabe-new-waypoint-system' type='text' name='astrolabe-new-waypoint-system' class='typeahead system-typeahead form-control' /><br />
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="shortest" id="astrolabe-new-waypoint-sec-filter">
				<span class='label'>Shortest</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a data-value="safest" href="javascript:void(0)">Safest</a></li>
				<li><a data-value="shortest" href="javascript:void(0)">Shortest</a></li>
				<li><a data-value="prefer_low_sec" href="javascript:void(0)">Prefer Low/Null Sec</a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="yes" id="astrolabe-new-waypoint-use-wormholes">
			<span class='label'>Use wormholes</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a data-value="yes" href="javascript:void(0)">Use wormholes</a></li>
				<li><a data-value="no" href="javascript:void(0)">Don't use wormholes</a></li>
			</ul>
		</div>
		<button type="submit" class="btn btn-primary">Add</button>
	</form>

	<div class="clear"></div>
	<ul id="astrolabe-waypoints">
	</ul>
	<div class="clear"></div>
</div>
<script id="template-astrolabe-waypoint" type="text/x-handlebars-template">
	<li class="astrolabe-waypoint" data-system-name="{{name}}" data-system-id="{{id}}" data-guid="{{guid}}" id="waypoint-{{guid}}">
		<div class='astrolabe-waypoint-header'>
			<span class='astrolabe-waypoint-position'>0</span> {{name}}
			<span class='astrolabe-waypoint-route-options'>
				<div class="btn-group">
					<button type="button" name="sec-filter" class="btn btn-xs btn-info dropdown-toggle waypoint-route-option" data-toggle="dropdown" aria-expanded="false" value="{{sec_filter}}">
						<span class='label'>
						{{#equal sec_filter 'safest'}}
						Safest
						{{else}}
							{{#equal sec_filter 'shortest'}}
							Shortest
							{{else}}
								{{#equal sec_filter 'prefer_low_sec'}}
								Prefer Low/Null Sec
								{{/equal}}
							{{/equal}}
						{{/equal}}
						</span>

						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a data-value="safest" href="javascript:void(0)">Safest</a></li>
						<li><a data-value="shortest" href="javascript:void(0)">Shortest</a></li>
						<li><a data-value="prefer_low_sec" href="javascript:void(0)">Prefer Low/Null Sec</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button type="button" name="use-wormholes" class="btn-xs btn btn-info dropdown-toggle waypoint-route-option" data-toggle="dropdown" aria-expanded="false" value="{{use_wormholes}}">
					<span class='label'>
					{{#equal use_wormholes 'yes'}}
					Use wormholes
					{{else}}
					Don't use wormholes
					{{/equal}}
					</span>

					<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a data-value="yes" href="javascript:void(0)">Use wormholes</a></li>
						<li><a data-value="no" href="javascript:void(0)">Don't use wormholes</a></li>
					</ul>
				</div>

			</span>
			<span class='astrolabe-waypoint-options pull-right'>
				<i class='astrolabe-waypoint-option-close fa fa-close'></i>
			</span>
		</div>
		<div class='astrolabe-waypoint-body'>
			<div class='astrolabe-waypoint-route-options'>
			</div>
			<table class='astrolabe-waypoint-route siggy-table siggy-table-striped table-with-dropdowns'>
			</table>
		</div>
	</li>
</script>

<script id="template-astrolabe-route-table-row" type="text/x-handlebars-template">
	<tr data-system-id='{{ system.id }}'>
		<td class='text-center'>{{position}}</td>
		<td class='text-center'>
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
						<a onclick="javascript:CCPEVE.setDestination({{ system.id }})">Set Destination</a>
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
	 	<td class='text-center {{securityClass system.sec}}'>{{system.sec}}</td>
		<td class='text-center'>
			<div class="dropdown">
				 <button class="btn btn-default btn-hidden dropdown-toggle" type="button" id="region-drop" data-toggle="dropdown" aria-expanded="true">
					{{ system.region_name }}
				</button>
				<ul class="dropdown-menu" role="menu">
					<li><a target="_blank" href="http://evemaps.dotlan.net/map/{{system.region_name}}">DOTLAN</a></li>
				</ul>
			</div>
		</td>
	</tr>
</script>
