/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Astrolabe = function(core)
{
	var $this = this;
	this.key = 'astrolabe';
	this.core = core;

	this.routeTableRow = Handlebars.compile( $("#template-astrolabe-route-table-row").html() );
	this.waypointHTML = Handlebars.compile( $("#template-astrolabe-waypoint").html() );
	this.input = $('#activity-search-input');

	this.results = $('#activity-search-results');

	$('#activity-search-go').click( function() {
		$this.search();
	});


	$( '#astrolabe-waypoints' ).sortable({
		revert: true,
		placeholder: "astrolabe-waypoint-placeholder",
		update: function( event, ui ) {
			$this.search();
		}
	});
	$( "ul, li" ).disableSelection();


	var points = [
		'Amarr',
		'Huola',
		'Rens',
		'Dodixie'
	];

	for(i = 0; i < points.length; i++)
	{
		var html = this.waypointHTML({name: points[i]});
		$('#astrolabe-waypoints').append(html);
	}

	$this.search();

	$('#astrolabe-new-waypoint-form').submit( function(e)
	{
		e.preventDefault();

		var systemName = $('#astrolabe-new-waypoint-system').val();


		var html = $this.waypointHTML({name: systemName});
		$('#astrolabe-waypoints').append(html);
		$this.search();
	});

	$('#activity-astrolabe').on('click','.astrolabe-waypoint-option-close', function(e) {
		e.preventDefault();

		$(this).parent().parent().parent().remove();
		$this.search();
	});
}

siggy2.Activity.Astrolabe.prototype.search = function()
{
	var $this = this;

	var waypoints = [];

	$('.astrolabe-waypoint').each( function()
	{
		waypoints.push({system_name: $(this).data('system-name')});
	});

	this.totalJumps = 0;

	$.ajax({
		url: this.core.settings.baseUrl + 'astrolabe/route',
		dataType: 'json',
		cache: false,
		async: true,
		method: 'get',
		data: {waypoints: JSON.stringify(waypoints)},
		success: function (data)
		{
			var waypoints = $('.astrolabe-waypoint');

			var paths = data.paths;
						console.log(data.paths);

			var len = waypoints.length;

			$this.totalJumps = 0;

			for(var i = 0; i < len; i++)
			{
				var headerPosition = $(waypoints[i]).children().children('.astrolabe-waypoint-position').text(++$this.totalJumps);

				var tableSelector = $(waypoints[i]).children().children('.astrolabe-waypoint-route');
				$this.populateRouteTable(tableSelector, paths[i]);
			}
		}
	});
}


siggy2.Activity.Astrolabe.prototype.populateRouteTable = function(table, route)
{
	table.empty();

	if( typeof(route) == 'undefined' )
		return;

	for(i = 1; i < route.length-1; i++)
	{
		var row = this.routeTableRow({position: ++this.totalJumps, system: route[i]});
		table.append(row);
	}
}

siggy2.Activity.Astrolabe.prototype.start = function()
{
	$('#activity-' + this.key).show();
}

siggy2.Activity.Astrolabe.prototype.stop = function()
{
	$('#activity-' + this.key).hide();
}
