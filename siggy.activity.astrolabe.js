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


	this.waypointCache = [];

	$('#activity-search-go').click( function() {
		$this.search();
	});


	$( '#astrolabe-waypoints' ).sortable({
		revert: true,
		placeholder: "astrolabe-waypoint-placeholder",
		update: function( event, ui ) {
			$('.astrolabe-waypoint-route-options').show();
			$('.astrolabe-waypoint:last').find('.astrolabe-waypoint-route-options').hide();
			$('.astrolabe-waypoint:last').find('.astrolabe-waypoint-route').empty();

			$this.search();
		}
	});
	$( "ul, li" ).disableSelection();
/*

	var points = [
		{name: 'Amarr', id: 30002187, guid:'6ab8fb20-a31c-4a69-b266-f3c162ee5369', sec_filter: 'shortest', use_wormholes: 'yes'},
		{name: 'Huola', id: 30003067, guid:'71152bf5-5db4-4d63-baf4-353b8b8245f7', sec_filter: 'shortest', use_wormholes: 'no'},
		{name: 'Rens', id: 30002510, guid:'70a9525d-b188-4564-a783-e9281e93143b', sec_filter: 'shortest', use_wormholes: 'yes'},
		{name: 'Dodixie', id: 30002659, guid: 'bea3cbe8-9b9c-4aab-8a63-4d9764d7fbbe', sec_filter: 'shortest',  use_wormholes: 'yes'}
	];

	for(i = 0; i < points.length; i++)
	{
		var html = this.waypointHTML(points[i]);
		$('#astrolabe-waypoints').append(html);
			$('.astrolabe-waypoint-route-options').show();
			$('.astrolabe-waypoint:last').find('.astrolabe-waypoint-route-options').hide();
	}
*/
	$('#astrolabe-new-waypoint-form').submit( function(e)
	{
		e.preventDefault();

		var systemName = $('#astrolabe-new-waypoint-system').val();

		function sync(datums)
		{
			if( Object.size(datums) != 1 )
			{
				//error
				return;
			}

			var secFilter = $('#astrolabe-new-waypoint-sec-filter').val();
			var useWormholes = $('#astrolabe-new-waypoint-use-wormholes').val();

			var html = $this.waypointHTML({
											name: datums[0].name,
											id: datums[0].id,
											guid: guid(),
											sec_filter: secFilter,
											use_wormholes: useWormholes
										});

			$('#astrolabe-waypoints').append(html);

			$('.astrolabe-waypoint-route-options').show();
			$('.astrolabe-waypoint:last').find('.astrolabe-waypoint-route-options').hide();

			$this.search();
		}

		siggy2.StaticData.systemTypeAhead.search(systemName, sync, false);
	});


	$('#activity-astrolabe').on('click','.astrolabe-waypoint-option-close', function(e) {
		e.preventDefault();

		var guid = $(this).parent().parent().parent().data('guid');
		for(var i in $this.waypointCache)
		{
			if( $this.waypointCache[i].guid == guid )
			{
			console.log("deleted cached");
				delete $this.waypointCache[i];
			}
		}
		$(this).parent().parent().parent().remove();

		$this.search();
	});

	$('#activity-astrolabe').on('click', '.dropdown-menu li a', function(){
		$(this).parents(".btn-group").find(".btn:first-child span.label").text($(this).text());
		$(this).parents(".btn-group").find(".btn:first-child").val($(this).data('value'));

		if(  $(this).parents('form').length == 0 )
		{
			$this.search();
		}
	});
}

/*
{
  "method": "Route.Find",
  "params": [{
    "route": {
      "from": {
        "solarSystems": [30002553]
      },
      "to": {
        "solarSystem": 30002575
      }
    },
    "capabilities": {
      "jumpGate": {}
    },
    "rules": {
      "maxSecurity": {
        "priority": 0,
        "limit": 0.5
		},
      "transitCount": {
        "priority": 1
      }
    }
  }],
  "id": 1
}

*/


siggy2.Activity.Astrolabe.prototype.getRouteRules = function(secFilter)
{
	if( secFilter == 'safest' )
	{
		return {
			"minSecurity": {
				"priority": 0,
				"limit": 0.5
			},
			"transitCount": {
				"priority": 1
			}
		};
	}
	else if (secFilter == 'prefer_low_sec')
	{
		return {
			"maxSecurity": {
				"priority": 0,
				"limit": 0.5
			},
			"transitCount": {
				"priority": 1
			}
		};
	}
	else if( secFilter == 'nullsec' )
	{
		return {
			"maxSecurity": {
				"priority": 0,
				"limit": 0.1
			},
			"transitCount": {
				"priority": 1
			}
		};
	}
	else
	{
		return {};
	}
}

siggy2.Activity.Astrolabe.prototype.getRequestStruct = function(from, to)
{
	var base = {
		"method": "Route.Find",
		"params": [{
			"route": {
				"from": {
					"solarSystems": [from.system_id]
				},
				"to": {
					"solarSystem": to.system_id
				}
			},
			"capabilities": {
				"jumpGate": {}
			},
			"rules": this.getRouteRules(from.sec_filter),
			"useWormholes": from.use_wormholes
		}],
		"id": 1
	};

	return base;
}

siggy2.Activity.Astrolabe.prototype.needsCacheUpdate = function(cached, waypoint)
{
	if( typeof(cached) == 'undefined'
	|| cached.guid != waypoint.guid
	|| cached.sec_filter != waypoint.sec_filter
	|| cached.use_wormholes != waypoint.use_wormholes)
	{
		return true;
	}

	return false;
}

siggy2.Activity.Astrolabe.prototype.search = function()
{
	var $this = this;

	var waypoints = [];

	$('.astrolabe-waypoint').each( function()
	{
		waypoints.push({
							system_name: $(this).data('system-name'),
							system_id:  $(this).data('system-id'),
							guid: $(this).data('guid'),
							sec_filter: $(this).find('button[name=sec-filter]').val(),
							use_wormholes: $(this).find('button[name=use-wormholes]').val() == 'yes' ? true : false
						});
	});

	numberWaypointChanges = 0;
	for(var i = 0; i < waypoints.length; i++)
	{
		var waypoint = waypoints[i];

		/* only update if the cache does not have our waypoint */
		if( i < waypoints.length - 1
			 && this.needsCacheUpdate( this.waypointCache[i], waypoint ) )
		{
			numberWaypointChanges++;

			$('#waypoint-'+waypoint.guid).find('.astrolabe-waypoint-route').empty().text('Loading');
		}
	}

	for(var i = 0; i < waypoints.length; i++)
	{
		var waypoint = waypoints[i];

		/* only update if the cache does not have our waypoint */
		if( i < waypoints.length - 1
			 && this.needsCacheUpdate( this.waypointCache[i], waypoint ) )
		{
			(function(i,waypoint){
				$.ajax({
					url: 'https://siggy.borkedlabs.com:3000/',
					dataType: 'json',
					cache: false,
					async: true,
					method: 'post',
					contentType: 'application/json',
					data: JSON.stringify($this.getRequestStruct(waypoint, waypoints[i+1])),
					success: function (data)
					{
						$this.waypointCache[i] = waypoint;
						var path = data.result.path;

						path.splice(0,1);
						path.splice(path.length-1,1);

						$this.waypointCache[i].path = path;

						numberWaypointChanges--;

						if(numberWaypointChanges == 0)
						{
							$this.reprintRoute();
						}
					}
				})
			})(i,waypoint);
		}
	}
}

siggy2.Activity.Astrolabe.prototype.reprintRoute = function()
{
	var $this = this;

	this.totalJumps = 0;
	i = 0;

	$('.astrolabe-waypoint').each( function()
	{
		var waypoint = $this.waypointCache[i];

		$(this).find('.astrolabe-waypoint-position').text($this.totalJumps);

		if( typeof(waypoint) == 'undefined' )
			return;

		var tableSelector = $(this).find('.astrolabe-waypoint-route');


		$this.populateRouteTable(tableSelector, waypoint.path);

		$this.totalJumps++;
		i++;
	});
}


siggy2.Activity.Astrolabe.prototype.populateRouteTable = function(table, path)
{
	table.empty();

	if( typeof(path) == 'undefined' )
		return;

	for(var i in path)
	{
		var step = path[i];


		var system = siggy2.StaticData.getSystemByID(step.solarSystemID);

		var row = this.routeTableRow({position: ++this.totalJumps, system: system});
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
