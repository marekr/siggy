/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

siggy2.Map = function(core, options)
{
	var $this = this;

	this.core = core;
	this.defaults = {
		jumpTrackerEnabled: true,
		jumpTrackerShowNames: true,
		jumpTrackerShowTime: true,
		showActivesShips: false,
		allowMapHeightExpand: true,
		alwaysShowClass: false,
		maxCharactersShownInSystem: 7
	};

	this.settings = $.extend({}, this.defaults, options);

	this.systems = {};
	this.wormholes = {};
	this.stargates = {};
	this.jumpbridges = {};
	this.cynos = {};

	this.mapConnections = {};

	this.baseUrl = '';

	this.container = $('#chain-map-container');
	this.innerContainer = $('#chain-map-inner');

	this.loadingMessage = this.container.find('p.loading');
	this.editingMessage = this.container.find('p.editing');
	this.deletingMessage = this.container.find('p.deleting');

	this.buttonsContainer = this.container.find('div.buttons');

	this.updated = false;

	//map editing
	this.editing = false;
	this.massDelete = false;

	//wheditor
	this.editorOpen = false;
	this.editorMode = '';
	this.editingConnection = null;

	this.lastUpdate = 0;

	if( getCookie('broadcast') != null )
	{
		this.broadcast = parseInt(getCookie('broadcast'));
	}
	else
	{
		this.broadcast = 1;
	}

	this.massSelect = false;
	this.selectionInProgress = false;

	this.selectedSystemID = 0;

	//systemeditor
	this.editingSystem = 0;

    this.selectionBox = $('<div>').addClass('selection-box');

	this.blobTemplate = Handlebars.compile( $("#template-chainmap-system-blob").html() );
	this.templateJumpLogEntry = Handlebars.compile( $("#template-jump-log-entry").html() );

	$(document).on('click','.map-system-blob', function(e)
	{
		e.preventDefault();
        if( $this.editing || $this.massDelete )
        {
            return false;
        }
        var sysID = $(this).data('system-id');

		$(document).trigger('siggy.map.systemSelected', sysID );
    } );

	$(document).on('click','button.chainmap-dialog-cancel', function(e)
	{
		e.preventDefault();

		$('#chain-map-container').unblock();
	});

	$('#chain-map-table-button').click(function(e) {
		$this.core.loadActivity('chainmap', {chainMapID: $this.core.activities.siggy.chainMapID});
	});
}

siggy2.Map.prototype.showMessage = function(what)
{
	if( what == 'loading' )
	{
		this.loadingMessage.css({	'top': 150,
									'left': screen.width/2 - this.loadingMessage.width(),
									'position':'relative',
									'float':'left'
								});
		this.loadingMessage.show();
	}
	else if( what == 'editing' )
	{
		this.editingMessage.css({	'left': this.container.width()/2 - this.editingMessage.width()/2
								});
		this.editingMessage.show();
	}
	else if( what == 'deleting' )
	{
		this.deletingMessage.css({left: this.container.width()/2 - this.deletingMessage.width()/2});
		this.deletingMessage.show();
	}
}

siggy2.Map.prototype.hideMessage = function(what)
{
	if( what == 'loading' )
	{
		this.loadingMessage.hide();
	}
	else if( what == 'editing' )
	{
		this.editingMessage.hide();
	}
	else if( what == 'deleting' )
	{
		this.deletingMessage.hide();
	}
}

siggy2.Map.prototype.updateMessagePositions = function()
{

	if( this.loadingMessage.is(':visible') )
	{
		this.loadingMessage.css({	'top': this.container.height()/2 - this.loadingMessage.height()/2,
									'left': this.container.width()/2 - this.loadingMessage.width()/2,
									'position':'relative',
									'float':'left'
								});
	}
	if( this.editingMessage.is(':visible') )
	{
		this.editingMessage.css({left: this.container.width()/2 - this.editingMessage.width()/2});
	}
	if( this.deletingMessage.is(':visible') )
	{
		this.deletingMessage.css({left: this.container.width()/2 - this.deletingMessage.width()/2});
	}
}

siggy2.Map.prototype.centerButtons = function()
{
	var bottomOffset = 30;
	if( this.buttonsContainer != null )
	{
	//	if( $('#chainPanTrackX').is(':visible') )
		//{
		//	bottomOffset += 20;
		//}
	}
}

siggy2.Map.prototype.initialize = function()
{
	var that = this;

	this.showMessage('loading');

	if( this.broadcast )
	{
		$('#chainmap-broadcast-button').html('<i class="fa fa-wifi"></i> Disable location broadcasting');
	}
	else
	{
		$('#chainmap-broadcast-button').html('<i class="fa fa-eye-slash"></i> Enable location broadcasting');
	}
	$('#chainmap-broadcast-button').click( function(){
		if( that.broadcast == 1)
		{
			that.broadcast = 0;
			$('#chainmap-broadcast-button').html('<i class="fa fa-eye-slash"></i> Enable location broadcasting');
		}
		else
		{
			that.broadcast = 1;
			$('#chainmap-broadcast-button').html('<i class="fa fa-wifi"></i> Disable location broadcasting');
		}
		setCookie('broadcast', that.broadcast, 365);
	});

	$('#chain-map-add-wh').click( function() {
		that.resetWormholeEditor();
		that.openWHEditor('add');
	});

	$('#chain-map-edit').click( function() {
		that.startMapEdit();
	});

	$('#chain-map-delete-whs').click( function() {
		that.showMessage('deleting');
		that.massSelect = true;
		$('body').addClass('no-select');

		$('#chain-map-mass-delete-confirm').show();
		$('#chain-map-mass-delete-cancel').show();
		that.centerButtons();
	});


	this.registerEvents();
	this.initializeTabs();
	this.setupEditor();
	this.setupSystemEditor();

	$(window).bind('resize', function()
	{
		that.updateMessagePositions();
		that.centerButtons();
		$("#chain-map").width($("#chain-map-scrolltainer")[0].scrollWidth);
	});

	if( this.core.displayStates.map.open )
	{
		this.mapShow();
	}
	else
	{
		this.mapHide();
	}

	var $container = $("#chain-map");

	this.innerContainer.height(that.core.displayStates.map.height);

	if( this.settings.allowMapHeightExpand )
	{
		this.innerContainer.resizable({
								handles:'s',
								maxHeight:800,
								minHeight:400 })
							.on( "resizestop", function( event, ui )
							{
								that.core.displayStates.map.height = that.innerContainer.height();
								that.core.saveDisplayState();
							} );
	}
	$container.on('mousedown', function(e) {
		if( !that.massSelect )
		{
			return;
		}
		//ulgy hack to make the mass delete selection work for now
		$("#chain-map").width($("#chain-map-scrolltainer")[0].scrollWidth);

		that.selectionInProgress = true;

		var chainmapOffset = $(this).offset();
		var click_y = e.pageY-chainmapOffset.top-5,
			click_x = e.pageX-chainmapOffset.left-5;

		that.selectionBox.css({
		  'top':    click_y,
		  'left':   click_x,
		  'width':  0,
		  'height': 0,
		  'z-index': 9999
		});
		that.selectionBox.hide();

		that.selectionBox.appendTo($container);

		$container.on('mousemove', function(e) {
			if ( that.selectionInProgress  )
			{
				var chainmapOffset = $(this).offset();
				var move_x = e.pageX-chainmapOffset.left,
				  move_y = e.pageY-chainmapOffset.top,
				  width  = Math.abs(move_x - click_x),
				  height = Math.abs(move_y - click_y),
				  new_x, new_y;

				new_x = (move_x < click_x) ? (click_x - width) : click_x;
				new_y = (move_y < click_y) ? (click_y - height) : click_y;

				new_x -= 5;
				new_y -= 5;

				that.selectionBox.show();

				that.selectionBox.css({
				'width': width,
				'height': height,
				'top': new_y,
				'left': new_x
				});
		  }
		}).on('mouseup', function(e) {
			if ( that.selectionInProgress )
			{
				//ulgy hack to make the mass delete selection work for now
				$("#chain-map").width("auto");

				that.selectionInProgress = false;
				$container.off('mousemove');

				var bb = {
					 w: that.selectionBox.width(),
					 h: that.selectionBox.height(),
					 x: that.selectionBox.position().left,
					 y: that.selectionBox.position().top
				};

				that.selectionBox.remove();

				if( bb.w < 1 || bb.h < 1 )
				{
					return;
				}

				for(var i in that.mapConnections)
				{
					var conn = that.mapConnections[i];
					var ele = $( conn.id );

					var internalconn = conn.connection.getConnector();
					var compareBB = {
						h: internalconn.h,
						x: internalconn.x,
						y: internalconn.y,
						w: internalconn.w
					};

					var inside = Biltong.intersects( bb, compareBB );

					if( inside )
					{
						conn.selected = !conn.selected;
					}

					conn.refresh();
				}
			}
		});
	});

	$(document).bind('siggy.systemSwitched', function(e, systemID) {
		that.setSelectedSystem( systemID );
		e.stopPropagation();
	});

	$('#jump-log-refresh').click( function() {
        that.updateJumpLog(that.editingConnection.settings.hash);
	});

	this.initializeHotkeys();
	this.initializeSystemBlobContextMenu();
	this.initializeConnectionContextMenu();
    this.initializeExitFinder();
}


siggy2.Map.prototype.initializeExitFinder = function()
{
    var $this = this;

    $("#exit-finder-button").click( function(e) {
		e.preventDefault();

		var sel = siggy2.Maps.getSelectDropdown(siggy2.Maps.selected, "(current map)");
		$('#exit-finder select[name=chainmap]').html(sel.html());
		$('#exit-finder select[name=chainmap]').val(sel.val());

        $this.core.openBox('#exit-finder');
        $("#exit-finder-results-wrap").hide();
    } );

    $('#exit-finder button[name=current_location]').click( function(e) {
		e.preventDefault();

        $("#exit-finder-loading").show();
        $("#exit-finder-results-wrap").hide();
        $.post($this.baseUrl + 'chainmap/find_nearest_exits',
        {
			current_system: 1,
			chainmap: $('#exit-finder select[name=chainmap]').val()
		},
        function (data)
        {
            $("#exit-finder-loading").hide();
            $('#exit-finder-list').empty();
            $this.populateExitData(data);
            $("#exit-finder-results-wrap").show();
        });
    });

    var submitHandler = function(e) {
		e.preventDefault();

	    var target = $("#exit-finder input[name=target_system]").val();

	    $("#exit-finder-loading").show();
	    $("#exit-finder-results-wrap").hide();

	    $.post($this.baseUrl + 'chainmap/find_nearest_exits',
	        {
				target: target,
				chainmap: $('#exit-finder select[name=chainmap]').val()
			},
	        function (data)
	        {
	            $("#exit-finder-loading").hide();
	            $('#exit-finder-list').empty();
	            $this.populateExitData(data);
	            $("#exit-finder-results-wrap").show();
	        });
	        return false;
    };

    $('#exit-finder form').submit(submitHandler);

}

siggy2.Map.prototype.populateExitData = function(data)
{
    if( typeof(data.result) != "undefined" )
    {
        for(var i in data.result)
        {
            var item = $("<li>");
            item.html("<span class='faux-link'>"+data.result[i].system_name + "</span> - " + data.result[i].number_jumps + " jumps");
            $('#exit-finder-list').append(item);

            item.data("system-id", data.result[i].system_id);
            item.data("system-name",data.result[i].system_name);
            item.addClass('basic-system-context');
        }
    }
    else
    {
        var item = $("<li>");
        item.text("Invalid system or no exits");
        $('#exit-finder-list').append(item);
    }
}

siggy2.Map.prototype.initializeConnectionContextMenu = function()
{
	var $this = this;
	$(document).contextMenu({
		selector: '._jsPlumb_connector ',

        build: function($trigger, e) {
			var connection = $($trigger).data('siggy_connection');

			if( typeof(connection) == "undefined" )
				return false;

			var items = connection.contextMenuBuildItems();

			if( Object.size(items) != 0 )
			{
				return {
					callback: function(key, options) {
						var connection = $(this).data('siggy_connection');
						connection.contextMenuHandler(key);
					},
					items: items
				};
			}
			else
			{
				return false;
			}

        }
	});
}


siggy2.Map.prototype.initializeSystemBlobContextMenu = function()
{
	var $this = this;
	$(document).contextMenu({
		selector: '.map-system-blob',

        build: function($trigger, e) {
			var sysID = $($trigger).data('system-id');

			var sysData = $this.systems[sysID];

			if( typeof(sysData) == "undefined" )
				return false;

			var items = { 'edit': {name: 'Edit' },
						  'showinfo': {name: 'Show Info'}
						};

			items.sep1 = "---------";
			items.setdest = {name:'Set Destination'};
			items.addwaypoint = {name: 'Add Waypoint'};
			items.sep2 = "---------";

			if( parseInt(sysData.rally) == 1 )
			{
				items.clearrally = {name:'Clear Rally'};
			}
			else
			{
				items.setrally = {name:'Set Rally'};
			}

			if( parseInt(sysData.hazard) == 1 )
			{
				items.clearhazard = {name:'Clear Hazard'};
			}
			else
			{
				items.sethazard = {name:'Set Hazard'};
			}

            return {
                callback: function(key, options) {
					var sysID = $(this).data('system-id');
					var sysData = $this.systems[sysID];
					$this.systemContextMenuHandler(key,sysData);
                },
                items: items
            };
        }
	});
}


siggy2.Map.prototype.systemContextMenuHandler = function(action, system)
{
	var $this = this;

	if( action == "edit" )
	{
		$this.openSystemEdit( system.systemID );
	}
	else if( action == "setdest" )
	{
		siggy2.Eve.SetDestination(system.systemID);
	}
	else if( action == "addwaypoint" )
	{
		siggy2.Eve.AddWaypoint(system.systemID);
	}
	else if( action == "showinfo" )
	{
		window.open('http://evemaps.dotlan.net/system/'+ system.name , '_blank');
	}
	else if( action == "setrally" )
	{
		var data = {
			rally: 1
		};

		$this.saveSystemOptions(system.systemID, data);
	}
	else if( action == "clearrally" )
	{
		var data = {
			rally: 0
		};

		$this.saveSystemOptions(system.systemID, data);
	}
	else if( action == "sethazard" )
	{
		var data = {
			hazard: 1
		};

		$this.saveSystemOptions(system.systemID, data);
	}
	else if( action == "clearhazard" )
	{
		var data = {
			hazard: 0
		};

		$this.saveSystemOptions(system.systemID, data);
	}
}


siggy2.Map.prototype.startMapEdit = function()
{
	this.editing = true;
	$('#chain-map-edit-save').show();
	$('#chain-map-edit-cancel').show();
	this.centerButtons();

	$('div.map-system-blob').qtip('disable');

	$('.map-system-blob').each( function()
	{
		jsPlumb.setDraggable($(this), true);
	});

	this.showMessage('editing');
}

siggy2.Map.prototype.initializeHotkeys = function()
{
	var $this = this;

	$(document).bind('keydown', 'ctrl+m', function(){
		$(document).scrollTop( 0 );
	});

	this.core.hotkeyhelper.registerHotkey('Ctrl+M', 'Jump to map');
}

siggy2.Map.prototype.setSelectedSystem = function( systemID )
{
	if( this.selectedSystemID != systemID )
	{
		$( "#map-system-"+this.selectedSystemID ).removeClass('map-system-blob-selected');

		$("#map-system-"+systemID ).addClass('map-system-blob-selected');

		this.selectedSystemID = systemID;
	}
}

siggy2.Map.prototype.mapHide = function()
{
	$('#chain-map-inner').hide();
	$('#chain-map-ec').text('Click to show');
	$('#chainPanTrackX').hide();
	this.lastUpdate = 0;

	$('#chain-map-tabs i.expand-collapse-indicator').removeClass('fa-caret-down').addClass('fa-caret-up');
}

siggy2.Map.prototype.mapShow = function()
{
	$('#chain-map-inner').show();
	$('#chain-map-ec').text('Click to hide');
	this.showMessage('loading');

	$('#chain-map-tabs i.expand-collapse-indicator').removeClass('fa-caret-up').addClass('fa-caret-down');
}

siggy2.Map.prototype.registerEvents = function()
{
    var $this = this;

    $('#chain-map-tabs .minimize').click( function() {
        if( $this.core.displayStates.map.open == true )
        {
			$this.mapHide();
        }
        else
        {
			$this.mapShow();
			$(document).trigger('siggy.updateRequested', false );
        }
		$this.core.displayStates.map.open = !$this.core.displayStates.map.open;
		$this.core.saveDisplayState();
    } );

    $('#chain-map-edit-cancel').click( function() {
		$this.editing = false;
        $(this).hide();
		$this.hideMessage('editing');

        $('div.map-system-blob').qtip('enable');

		$(document).trigger('siggy.updateRequested', true );
	} );

    $('#chain-map-edit-save').click( function() {
        var saveSystemData = [];
        for (var i in $this.systems)
        {
            var sysID = $this.systems[i].systemID;

            var saveSystem = {};
            saveSystem.id = parseInt(sysID);


            var offset = $( '#map-system-'+sysID ).position();
            saveSystem.x = offset.left;
            saveSystem.y = offset.top;

            saveSystemData.push(saveSystem);
        }

		$('.map-system-blob').each( function()
		{
			jsPlumb.setDraggable($(this), false);
		});

        $.post($this.baseUrl + 'chainmap/save', {
            systemData: JSON.stringify(saveSystemData)
        });

		$this.editing = false;
        $(this).hide();
		$this.hideMessage('editing');

        $('div.map-system-blob').qtip('enable');
    } );


    $('#chain-map-mass-delete-confirm').click( function() {
		$this.processConnectionDelete();

		$this.hideMessage('deleting');
		$this.massDelete = false;
		$('body').removeClass('no-select');
		$this.massSelect = false;

        $(this).hide();
        $('#chain-map-mass-delete-cancel').hide();

    });

    $('#chain-map-mass-delete-cancel').click( function() {

        for (i in $this.mapConnections)
        {
			$this.mapConnections[i].selected = false;
			$this.mapConnections[i].refresh();
        }

		$this.hideMessage('deleting');
		$this.massDelete = false;
		$('body').removeClass('no-select');
		$this.massSelect = false;

        $(this).hide();
        $('#chain-map-mass-delete-confirm').hide();
    });
}

siggy2.Map.prototype.processConnectionDelete = function(hashes, chainMapID)
{
	var $this = this;

	if( typeof(hashes) == 'undefined' )
		hashes = this.getSelectedHashes();

	hashes = $.extend({
							wormholes: [],
							stargates: [],
							jumpbridges: [],
							cynos: [],
							count: 0
						}, hashes);

	if( hashes.count > 0 )
	{
		var postData = {
			wormhole_hashes: hashes.wormholes,
			stargate_hashes: hashes.stargates,
			jumpbridge_hashes: hashes.jumpbridges,
			cyno_hashes: hashes.cynos
		};

		if( typeof(chainMapID) == 'undefined' )
			postData.chainmap = chainMapID;

		$.ajax({
				type: 'post',
				url: $this.baseUrl + 'chainmap/connection_delete',
				data: JSON.stringify(postData),
				contentType: 'application/json',
				dataType: 'json'
			})
			.fail(function(){
				alert('Error deleting connection');
			})
			.always(function(){
				$(document).trigger('siggy.updateRequested', false );
			});
	}
}

siggy2.Map.prototype.getSelectedHashes = function()
{
	var hashes = {	wormholes: [],
					stargates: [],
					jumpbridges: [],
					cynos: [],
					count: 0
				};

	for (var i in this.mapConnections)
	{
		if( this.mapConnections[i].selected )
		{
			switch( this.mapConnections[i].settings.type )
			{
				case 'wormhole':
					hashes.wormholes.push( this.mapConnections[i].settings.hash );
					hashes.count++;
					break;
				case 'stargate':
					hashes.stargates.push( this.mapConnections[i].settings.hash );
					hashes.count++;
					break;
				case 'jumpbridge':
					hashes.jumpbridges.push( this.mapConnections[i].settings.hash );
					hashes.count++;
					break;
				case 'cyno':
					hashes.cynos.push( this.mapConnections[i].settings.hash );
					hashes.count++;
					break;

			}
		}
	}

	return hashes;
}


siggy2.Map.prototype.update = function(timestamp, systems, wormholes, stargates, jumpbridges, cynos)
{
	if( this.editing || this.massDelete )
	{
		return;
	}

	this.lastUpdate = parseInt(timestamp);

	this.systems = systems;
	this.wormholes = wormholes;
	this.stargates = stargates;
	this.jumpbridges = jumpbridges;
	this.cynos = cynos;

	this.draw();
	$("#chain-map").width($("#chain-map-scrolltainer")[0].scrollWidth);

	this.hideMessage('loading');
}

siggy2.Map.prototype.updateActives = function( activesData )
{
	if(  typeof( activesData ) == 'undefined' )
	{
		return;
	}

    for( var i in this.systems )
    {
        var sysID = this.systems[i].systemID;

        var ele =  $('#map-system-' + sysID + ' .map-system-blob-actives');
        ele.empty();

        var fullActives = $("#fullactives"+sysID);
        fullActives.empty();

        if( typeof(activesData[sysID]) != 'undefined' )
        {
            var actives = activesData[sysID];
            var text = '';

            //setup our lengths
            //TBH, make the max length configurable
            var len = actives.length;
            var displayLen = len > this.settings.maxCharactersShownInSystem
									? this.settings.maxCharactersShownInSystem : len;

            var fullText = '';
            for(var j in actives)
            {
                var person = actives[j];

                if( j < displayLen )
                {
                    text += actives[j].name + '<br \>';
                }

                if( this.settings.showActivesShips )
                {
                    fullText += actives[j].name + " - " + actives[j].ship +'<br \>';
                }
                else
                {
                    fullText += actives[j].name + '<br \>';
                }
            }

            if( len > displayLen )
            {
				if( displayLen == 0 )
				{
					text += (len-displayLen) + ' characters...<br \>';
				}
				else
				{
					text += ' +' + (len-displayLen) + ' others...<br \>';
				}
            }


            ele.html(text);
            fullActives.html(fullText);
        }
        else
        {
            fullActives.html("No actives");
        }
    }
}

siggy2.Map.prototype.draw = function()
{
    var that = this;

	//reset jsplumb to nuke all events
	jsPlumb.reset();

    $('div.map-system-blob').qtip('destroy');
    $('div.map-system-blob').off();
    $('div.map-full-actives').remove();

	for( var i  in this.mapConnections )
	{
		this.mapConnections[i].destroy();
		delete this.mapConnections[i];
	}

    $('#chain-map').empty();

    for( var i in this.systems )
    {
        //local variable assignment
        var systemData = this.systems[i];

		var newTypeBlob = $(this.blobTemplate({system: {
															id: systemData.systemID,
															region_name: systemData.region_name,
															name:systemData.name,
															display_name: systemData.displayName,
															system_class: parseInt(systemData.sysClass),
															kills_in_last_2_hours: parseInt(systemData.kills_in_last_2_hours),
															npcs_kills_in_last_2_hours: parseInt(systemData.npcs_kills_in_last_2_hours),
															showClass: ( this.settings.alwaysShowClass || systemData.sysClass >= 7 || ( systemData.sysClass < 7 && systemData.displayName == "") ),
															rally: systemData.rally,
															hazard: true,
															effect: parseInt(systemData.effect)
														}
												})
							);

		/* We use left and top instead of offset because the initial parent is funky */
		(function(x,y){
			newTypeBlob.css('left', x+"px");
			newTypeBlob.css('top', y+"px");
		})(systemData.x, systemData.y);

        if( this.selectedSystemID == systemData.systemID )
        {
            newTypeBlob.addClass('map-system-blob-selected');
        }

        // get the activity color class
		var activityClass = '';
		if( !parseInt(systemData.rally) )
		{
			activityClass = this.getActivityColor( parseInt(systemData.activity) );
		}
		else
		{
			activityClass = 'map-activity-rally-here';
		}
		newTypeBlob.addClass( activityClass);

		if( parseInt(systemData.hazard) )
		{
			newTypeBlob.addClass('map-activity-hazard');
		}


		$("#chain-map").append( newTypeBlob );

        var tst = $("<div>").attr("id","fullactives"+systemData.systemID).addClass('tooltip').addClass('map-full-actives').text("");
        $("#chain-map-container").append(tst);

        var res = newTypeBlob.qtip({
            content: {
                text: $("#fullactives"+systemData.systemID) // Use the "div" element next to this for the content
            },
            show: {
                delay: 1000
            },
            position: {
                target: 'mouse',
                adjust: { x: 5, y: 5 },
                viewport: $(window)
            }
        });
    }

    for( var w in this.wormholes )
    {
        //local variable to make code smaller
        var wormhole = this.wormholes[w];

		var options = {
			to: wormhole.to_system_id,
			from: wormhole.from_system_id,
			hash: wormhole.hash,
			type: 'wormhole',
			wormhole: {
				mass: parseInt(wormhole.mass),
				eolDateSet: parseInt(wormhole.eol_date_set),
				eol:  parseInt(wormhole.eol),
				frigateSized: parseInt(wormhole.frigate_sized),
				totalTrackedMass: wormhole.total_tracked_mass
			},
			createdAt: wormhole.created_at,
			updatedAt: wormhole.updated_at
		};

		if( wormhole.wh_name != null )
		{
			options.wormhole.typeInfo = {
				name: wormhole.wh_name,
				mass: wormhole.wh_mass,
				lifetime: wormhole.wh_lifetime,
				maxJumpMass: wormhole.wh_jump_mass,
				regen: wormhole.wh_regen
			}
		}

		var connection = new siggy2.MapConnection(jsPlumb,options);
		connection.map = this;
		connection.create();

		this.mapConnections['wormhole-'+wormhole.hash] = connection;
    }

    for( var s in this.stargates )
    {
        //local variable to make code smaller
        var stargate = this.stargates[s];

		var options = {
			to: stargate.to_system_id,
			from: stargate.from_system_id,
			hash: stargate.hash,
			type: 'stargate',
			createdAt: stargate.created_at,
			updatedAt: stargate.updated_at
		};

		var connection = new siggy2.MapConnection(jsPlumb,options);
		connection.map = this;
		connection.create();

		this.mapConnections['stargate-'+stargate.hash] = connection;
    }

    for( var s in this.cynos )
    {
        //local variable to make code smaller
        var cyno = this.cynos[s];

		var options = {
			to: cyno.to_system_id,
			from: cyno.from_system_id,
			hash: cyno.hash,
			type: 'cyno',
			createdAt: cyno.created_at,
			updatedAt: cyno.updated_at
		};

		var connection = new siggy2.MapConnection(jsPlumb,options);
		connection.map = this;
		connection.create();

		this.mapConnections['cyno-'+cyno.hash] = connection;
    }


    for( var s in this.jumpbridges )
    {
        //local variable to make code smaller
        var jumpbridge = this.jumpbridges[s];

		var options = {
			to: jumpbridge.to_system_id,
			from: jumpbridge.from_system_id,
			hash: jumpbridge.hash,
			type: 'jumpbridge',
			createdAt: jumpbridge.created_at,
			updatedAt: jumpbridge.updated_at
		};

		var connection = new siggy2.MapConnection(jsPlumb,options);
		connection.map = this;
		connection.create();

		this.mapConnections['jumpbridges'+jumpbridge.hash] = connection;
    }

    if( Object.size(this.systems) > 0 )
	{
		jsPlumb.draggable($('.map-system-blob'), {
			containment: 'parent',
			stack: "div"
		});

		$('.map-system-blob').each( function()
		{
			jsPlumb.setDraggable($(this), false);
		});
	}
}

siggy2.Map.prototype.openSystemEdit = function( sysID )
{
	this.editingSystem = sysID;

	$('#chain-map-container').block({
		message: $('#system-options-popup'),
		css: {
				border: 'none',
				padding: '15px',
				background: 'transparent',
				color: 'inherit',
				cursor: 'auto',
				textAlign: 'left',
				width: 'auto'
		},
		overlayCSS: {
				cursor: 'auto'
		},
		centerX: true,
		fadeIn:  0,
		fadeOut:  0
	});

	$('#editingSystemName').text(this.systems[ sysID ].name);

	var label = this.systems[ sysID ].displayName == '' ? this.systems[ sysID ].name : this.systems[ sysID ].displayName;

	$('#system-editor input[name=label]').val( label );
	$('#system-editor input[name=label]').select();
	$('#system-editor select[name=activity]').val(this.systems[ sysID ].activity);
}

siggy2.Map.prototype.getActivityColor = function(activity)
{
    var color = '';


    switch( activity )
    {
        case 1:
            color = 'map-activity-empty';
			break;
        case 2:
            color = 'map-activity-occupied';
			break;
        case 3:
            color = 'map-activity-active';
			break;
        case 4:
            color = 'map-activity-friendly';
			break;
        default:    //default is grey
            color = '';
        break;

    }

    return color;
}


siggy2.Map.prototype.setupEditor = function()
{
	var that = this;
	$('#connection-editor-disconnect').click( function() {
		//we select the connection to trick the delete function
		//to reduce code
		that.editingConnection.selected = true;
		that.editingConnection.refresh();
		that.processConnectionDelete();
		$('#chain-map-container').unblock();
	} );

	var fromSysInput = $("#connection-editor input[name=from-sys]");
	var toSysInput = $("#connection-editor input[name=to-sys]");
	var fromCurrentInput = $('#connection-editor input[name=from-current-location]');
	var toCurrentInput = $('#connection-editor input[name=to-current-location]');

	fromCurrentInput.change( function() {
		fromSysInput.prop('disabled',$(this).is(':checked'));
		toCurrentInput.prop('disabled',$(this).is(':checked'));
	});

	toCurrentInput.change( function() {
		toSysInput.prop('disabled',$(this).is(':checked'));
		fromCurrentInput.prop('disabled',$(this).is(':checked'));
	});


	$('#connection-editor-save').click( function() {

		var data = {};
		if( that.editorMode == 'edit' )
		{
			data = {
				hash: that.editingConnection.settings.hash,
				type: that.editingConnection.settings.type
			};

			if( data.type == 'wormhole' )
			{
				data.eol = $('#connection-editor input[name=eol]:checked').val();
				data.frigate_sized = $('#connection-editor input[name=frigate_sized]:checked').val();
				data.mass = $('#connection-editor select[name=mass]').val();
				data.wh_type_name = $('#connection-editor input[name=wh_type_name]').val();
			}

			$.post(that.baseUrl + 'chainmap/connection_edit', data, function()
			{
				$(document).trigger('siggy.updateRequested', false );
			});


			that.editorOpen = false;
			$('#chain-map-container').unblock();
		}
		else
		{
			var errors = [];

			var type = $('select[name=connection-editor-type]').val();

			data = {
				fromSys: fromSysInput.val(),
				fromSysCurrent: ( fromCurrentInput.is(':checked') ? 1 : 0 ),
				toSys: toSysInput.val(),
				toSysCurrent: ( toCurrentInput.is(':checked') ? 1 : 0 ),
				type: type
			};

			if( type == 'wormhole' )
			{
				data.eol = $('#connection-editor input[name=eol]:checked').val();
				data.frigate_sized = $('#connection-editor input[name=frigate_sized]:checked').val();
				data.mass = $('#connection-editor select[name=mass]').val();
				data.wh_type_name = $('#connection-editor input[name=wh_type_name]').val();
			}

			$.post(that.baseUrl + 'chainmap/connection_add', data, function(resp)
			{
				if( parseInt(resp.success) == 1 )
				{
					that.editorOpen = false;
					$('#chain-map-container').unblock();
					$(document).trigger('siggy.updateRequested', false );
				}
				else
				{
					that.displayEditorErrors( resp.dataErrorMsgs );
				}
			});
		}
	} );


	$('select[name=connection-editor-type]').change( function() {
		switch( $(this).val() )
		{
			case 'wormhole':
				$('#connection-editor-options-wh').show();
				break;
			case 'stargate':
			case 'jumpbridge':
			case 'cyno':
				$('#connection-editor-options-wh').hide();
				break;
		}
	});
}


siggy2.Map.prototype.displayEditorErrors = function(errors)
{
	var errorsUL = $('#connection-editor ul.errors');
	errorsUL.empty();
	errorsUL.show();

	for( var i = 0; i < errors.length; i++ )
	{
		errorsUL.append( $('<li>').text( errors[i] ) );
	}
}


siggy2.Map.prototype.setupSystemEditor = function()
{
	var that = this;
	$('#system-editor-cancel').click( function() {
		$('#chain-map-container').unblock();
		that.editingSystem = 0;
	});

	$('#system-editor-save').click( function() {
		that.systemEditorSave();
	});

	$('#system-options-popup').on('keypress', function(e) {
		if(e.which == 13)
		{
			that.systemEditorSave();
			e.stopPropagation();
		}
	});
}

siggy2.Map.prototype.systemEditorSave = function()
{
	var that = this;
	var label = $('#system-editor input[name=label]').val();

	/* don't save the label if they didn't change the name */
	if( that.systems[that.editingSystem].name == label )
	{
		label = '';
	}

	var data = {
		label: label,
		activity: $('#system-editor select[name=activity]').val()
	};

	that.saveSystemOptions(that.editingSystem, data);
	$('#chain-map-container').unblock();
}

siggy2.Map.prototype.updateJumpLog = function( hash )
{
		var $this = this;
		var logList = $('#jumpLogList');
		logList.empty();

		if( !this.settings.jumpTrackerEnabled )
		{
            return;
		}

		var request = {
			wormhole_hash: hash
		}

		$.get(this.baseUrl + 'chainmap/jump_log', request, function (data)
		{
			if( typeof(data.totalMass) != 'undefined' )
			{
				data.totalMass = parseInt(data.totalMass);
				var displayMass = roundNumber(data.totalMass/1000000,2);
				$('#totalJumpedMass').text(displayMass);

				for( var i in data.jumpItems )
				{
					var item = data.jumpItems[i];

					var mass = 0;
					if(item.mass != null)
					{
						mass = roundNumber(parseInt(item.mass)/1000000,2);
					}

					var charName = '';
					if( $this.settings.jumpTrackerShowNames )
					{
						charName = ' - '+item.character_name;
					}

					var time = '';
					if( $this.settings.jumpTrackerShowTime )
					{
						time =  item.jumped_at;
					}

					var shipName = 'Unknown';
					if(item.ship_id != 0)
					{
						shipName = item.shipName;
					}

					var shipClass = 'unknown';
					if(item.ship_id != 0)
					{
						shipClass = item.shipClass;
					}


					var direction = '';

					var originSystem = $this.systems[item.origin_id];
					var fromDName = '';
					if( originSystem.displayName != '' )
					{
						fromDName = ' ('+originSystem.displayName+')';
					}
					direction += originSystem.name+fromDName + ' -> ';

					var destinationSystem = $this.systems[item.destination_id];
					var toDName = '';
					if( destinationSystem.displayName != '' )
					{
						toDName = ' ('+destinationSystem.displayName+')';
					}
					direction += destinationSystem.name+toDName;


					var entry = $($this.templateJumpLogEntry({
						mass: mass,
						direction: direction,
						ship_name: shipName,
						character_name: charName,
						jumped_at: time,
						ship_class: shipClass
					}));

					logList.append(entry);
				}
			}
			else
			{
				logList.append( $('<li><b>No jumps recorded<b/></li>') );
			}
		} );
}

siggy2.Map.prototype.resetWormholeEditor = function()
{
	var errorsUL = $('#connection-editor ul.errors');
	errorsUL.empty();
	errorsUL.hide();

	var fromCurrentInput = $('#connection-editor input[name=from-current-location]');
	//resets cause fucking browsers
	fromCurrentInput.prop('disabled', false);
	fromCurrentInput.prop('checked', false);


	var toCurrentInput = $('#connection-editor input[name=to-current-location]');
	//resets cause fucking browsers
	toCurrentInput.prop('disabled', false);
	toCurrentInput.prop('checked', false);

	var fromSysInput = $("#connection-editor input[name=from-sys]");
	//resets cause fucking browsers
	fromSysInput.val('');
	fromSysInput.prop('disabled',false);

	var toSysInput = $("#connection-editor input[name=to-sys]");
	//resets cause fucking browsers
	toSysInput.val('');
	toSysInput.prop('disabled',false);

	if( !this.core.igb )
	{
		fromCurrentInput.parent().hide();
		toCurrentInput.parent().hide();
	}

	$('#connection-editor select[name=mass]').val(0);
	$('#connection-editor input[name=eol]').filter('[value=0]').prop('checked', true);
	$('#connection-editor input[name=frigate_sized]').filter('[value=0]').prop('checked', true);
	$('#connection-editor input[name=wh_type_name]').val('');

	$('select[name=connection-editor-type]').val('wormhole');
}

siggy2.Map.prototype.editWormhole = function(conn)
{
	this.editingConnection = conn;

	this.resetWormholeEditor();

	var fromDName = '';

	if( this.systems[conn.settings.from].displayName != '' )
	{
		fromDName = ' ('+this.systems[conn.settings.from].displayName+')';
	}
	$('#connection-editor-from-text').text(this.systems[conn.settings.from].name+fromDName);

	var toDName = '';
	if( this.systems[conn.settings.to].displayName != '' )
	{
		toDName = ' ('+this.systems[conn.settings.to].displayName+')';
	}
	$('#connection-editor-to-text').text(this.systems[conn.settings.to].name+toDName);

	if( conn.settings.type == 'wormhole' )
	{
		$('#connection-editor-options-wh').show();
		$('#connection-editor-save').show();
		$('#connection-popup ul.box-tabs').show();
		$('#connection-editor select[name=mass]').val(conn.settings.wormhole.mass);
		$('#connection-editor input[name=eol]').filter('[value=' + conn.settings.wormhole.eol + ']').prop('checked', true);
		$('#connection-editor input[name=frigate_sized]').filter('[value=' + conn.settings.wormhole.frigateSized + ']').prop('checked', true);
		$('#connection-editor input[name=wh_type_name]').val(conn.settings.wormhole.typeInfo.name);
	}
	else
	{
		$('#connection-editor-save').hide();
		$('#connection-popup ul.box-tabs').hide();
		$('#connection-editor-options-wh').hide();
	}

	this.openWHEditor('edit');
}

siggy2.Map.prototype.initializeTabs = function()
{
	var $this = this;

	$('#connection-popup a[href="#connection-editor"]').tab('show');

	$('#connection-popup a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

		var href = $(e.target).attr('href');

		if( href == "#jump-log" )
		{
			if( $this.settings.jumpTrackerEnabled )
			{
				$this.updateJumpLog($this.editingConnection.settings.hash);
			}
		}
	});
}

siggy2.Map.prototype.openWHEditor = function(mode)
{
	$('#connection-popup a[href="#connection-editor"]').tab('show');
	$('#chain-map-container').block({
		message: $('#connection-popup'),
		css: {
				border: 'none',
				padding: '15px',
				background: 'transparent',
				color: 'inherit',
				cursor: 'auto',
				textAlign: 'left',
				width: 'auto'
		},
		overlayCSS: {
				cursor: 'auto'
		},
		centerX: true,
		centerY: true,
		fadeIn:  0,
		fadeOut:  0
	});

	if( mode == 'edit' )
	{
		$('#connection-editor-add').hide();
		$('#connection-editor-edit').show();
		this.editorMode = 'edit';
		this.editorOpen = true;
	}
	else
	{

		$('#connection-editor-save').show();
		$('#connection-editor-options-wh').show();
		$('#connection-popup ul.box-tabs').hide();
		$('#connection-editor-add').show();
		$('#connection-editor-edit').hide();

		$("#connection-editor input[name=from-sys]").focus();
		this.editorMode = 'add';
		this.editorOpen = true;
	}
}

siggy2.Map.prototype.saveSystemOptions = function(systemID, newData)
{
	var $this = this;

	newData.systemID = systemID;

	$.post(this.core.settings.baseUrl + 'siggy/save_system',
	newData,
	function (data)
	{
		$(document).trigger('siggy.updateRequested', true );
	});
}
