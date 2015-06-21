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
			$this.search();
		}
	});
	$( "ul, li" ).disableSelection();


	var points = [
		{name: 'Amarr', id: 30002187, guid:'6ab8fb20-a31c-4a69-b266-f3c162ee5369'},
		{name: 'Huola', id: 30003067, guid:'71152bf5-5db4-4d63-baf4-353b8b8245f7'},
		{name: 'Rens', id: 30002510, guid:'70a9525d-b188-4564-a783-e9281e93143b'},
		{name: 'Dodixie', id: 30002659, guid: 'bea3cbe8-9b9c-4aab-8a63-4d9764d7fbbe'}
	];

	for(i = 0; i < points.length; i++)
	{
		var html = this.waypointHTML({name: points[i].name, id: points[i].id, guid: points[i].guid});
		$('#astrolabe-waypoints').append(html);
	}

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

			var html = $this.waypointHTML({name: datums[0].name, id: datums[0].id, guid: guid()});
			$('#astrolabe-waypoints').append(html);
			$this.search();
		}

		siggy2.StaticData.systemTypeAhead.search(systemName, sync, false);

	});

	$('#activity-astrolabe').on('click','.astrolabe-waypoint-option-close', function(e) {
		e.preventDefault();

		$(this).parent().parent().parent().remove();
		$this.search();
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

siggy2.Activity.Astrolabe.prototype.getRequestStruct = function(from, to, secFilter)
{
	var base = {
		"method": "Route.Find",
		"params": [{
			"route": {
				"from": {
					"solarSystems": [from]
				},
				"to": {
					"solarSystem": to
				}
			},
			"capabilities": {
				"jumpGate": {}
			},
			"rules": this.getRouteRules(secFilter)
		}],
		"id": 1
	};

	return base;
}


siggy2.Activity.Astrolabe.prototype.search = function()
{
	var $this = this;

	var waypoints = [];

	console.log(siggy2.StaticData.systems);

	$('.astrolabe-waypoint').each( function()
	{
		waypoints.push({
							system_name: $(this).data('system-name'),
							system_id:  $(this).data('system-id'),
							guid: $(this).data('guid'),
							sec_filter: $(this).find('button[name=sec-filter]').val()
						});
	});

	console.log(waypoints);

	numberWaypointChanges = 0;
	for(var i = 0; i < waypoints.length; i++)
	{
		var waypoint = waypoints[i];

		/* only update if the cache does not have our waypoint */
		if( i < waypoints.length - 1
			 && (typeof(this.waypointCache[i]) == 'undefined' || this.waypointCache[i].guid != waypoint.guid) )
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
			 && (typeof(this.waypointCache[i]) == 'undefined' || this.waypointCache[i].guid != waypoint.guid) )
		{
			(function(i,waypoint){
				$.ajax({
					url: 'https://siggy.borkedlabs.com:3000/',
					dataType: 'json',
					cache: false,
					async: true,
					method: 'post',
					contentType: 'application/json',
					data: JSON.stringify($this.getRequestStruct(waypoint.system_id, waypoints[i+1].system_id, waypoint.sec_filter)),
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
