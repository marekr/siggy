<div id="activity-astrolabe" class="wrapper" style="display:none">
<style type='text/css'>
	.ui-sortable-helper .astrolabe-waypoint-body
	{
		display:none;

	}

	.ui-sortable-helper
	{
		height: 40px !important;
	}

	.astrolabe-waypoint,
	.astrolabe-waypoint-placeholder
	{
		background-color: #1C1C1C;
		min-height: 40px;
		padding:10px;
		margin-top:20px;
		cursor: pointer;
	}

	.astrolabe-waypoint-placeholder
	{
		border: 2px dashed #fff;
	}

	.astrolabe-waypoint-header
	{
		font-weight: bold;
	}

	.astrolabe-waypoint-body
	{
		padding-left: 20px;
	}

	.astrolabe-waypoint-route td
	{
		padding: 5px;
	}
	</td>
</style>
	<script>
		$(function() {
			$(document).on('click', '.dropdown-menu li a', function(){
					console.log( $(this) );
					$(this).parents(".btn-group").find(".btn:first-child span.label").text($(this).text());
					$(this).parents(".btn-group").find(".btn:first-child").val($(this).data('value'));
			});
		});
	</script>
	<form class="form-inline" id='astrolabe-new-waypoint-form'>
		<div class="form-group">
			<label for='astrolabe-new-waypoint-system'>New Waypoint </label>
			<input id='astrolabe-new-waypoint-system' type='text' name='astrolabe-new-waypoint-system' class='typeahead system-typeahead form-control' /><br />
		</div>
		<!--
		<div class="btn-group">
			<button type="button" class="btn btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="stargates">
				<span class='label'>Use Stargates</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a data-value="stargates" href="javascript:void(0)">Use Stargates</a></li>
				<li><a data-value="cyno" href="javascript:void(0)">Use Cyno</a></li>
			</ul>
		</div>
		-->
		<div class="btn-group">
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="safest">
				<span class='label'>Shortest</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a data-value="safest" href="javascript:void(0)">Safest</a></li>
				<li><a data-value="shortest" href="javascript:void(0)">Shortest</a></li>
				<li><a data-value="prefer_low_sec" href="javascript:void(0)">Prefer Low/Null Sec</a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="wormholes">
			<span class='label'>Use wormholes</span> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li><a data-value="wormholes" href="javascript:void(0)">Use wormholes</a></li>
				<li><a data-value="no_wormholes" href="javascript:void(0)">Don't use wormholes</a></li>
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
			<!--
				<div class="btn-group">
					<button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="stargates">
						<span class='label'>Use Stargates</span> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a data-value="stargates" href="javascript:void(0)">Use Stargates</a></li>
						<li><a data-value="cyno" href="javascript:void(0)">Use Cyno</a></li>
					</ul>
				</div>
				-->
				<div class="btn-group">
					<button type="button" name="sec-filter" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="shortest">
						<span class='label'>Shortest</span> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a data-value="safest" href="javascript:void(0)">Safest</a></li>
						<li><a data-value="shortest" href="javascript:void(0)">Shortest</a></li>
						<li><a data-value="prefer_low_sec" href="javascript:void(0)">Prefer Low/Null Sec</a></li>
					</ul>
				</div>
				<div class="btn-group">
					<button type="button" class="btn-xs btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" value="wormholes">
					<span class='label'>Use wormholes</span> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a data-value="wormholes" href="javascript:void(0)">Use wormholes</a></li>
						<li><a data-value="no_wormholes" href="javascript:void(0)">Don't use wormholes</a></li>
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
			<table class='astrolabe-waypoint-route'>
			</table>
		</div>
	</li>
</script>

<script id="template-astrolabe-route-table-row" type="text/x-handlebars-template">
	<tr data-system-id='{{ system.id }}'>
		<td>{{position}}</td>
		<td>{{system.name}}</td>
	 	<td>{{system.sec}}</td>
		<td>{{system.region_name}}</td>
	</tr>
</script>
