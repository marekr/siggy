
function siggyMap(options)
{
	this.defaults = {
			jumpTrackerEnabled: true,
			jumpTrackerShowNames: true,
			jumpTrackerShowTime: true,
            showActivesShips: false
	};


	this.settings = $.extend({}, this.defaults, options);


	this.systems = {};
	this.wormholes = {};
	this.stargates = {};
	this.jumpbridges = {};
	this.cynos = {};

	this.mapConnections = {};

	this.baseUrl = '';
	this.container = null;
	this.siggymain = null;

	this.loadingMessage = null;
	this.editingMessage = null;
	this.deletingMessage = null;

	this.buttonsContainer = null;

	this.updated = false;

	//map editing
	this.editing = false;
	this.massDelete = false;

	//wheditor
	this.editorOpen = false;
	this.editorMode = '';
	this.editingConnection = null;

	this.lastUpdate = 0;


	if( getCookie('mapOpen') != null )
	{
		this.mapOpen = parseInt(getCookie('mapOpen'));
	}
	else
	{
		this.mapOpen = 1;
	}

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

	this.selectedSystemRect = null;
	this.selectedSystemID = 0;

	//systemeditor
	this.editingSystem = 0;

    this.selectionBox = $('<div>').addClass('selection-box');
    this.selectedSystemBox = $('<div>').addClass('selected-system');
}

siggyMap.prototype.showMessage = function(what)
{
	if( what == 'loading' )
	{
		this.loadingMessage.css({	'top': this.container.height()/2 - this.loadingMessage.height()/2, 
									'left': this.container.width()/2 - this.loadingMessage.width()/2,
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

siggyMap.prototype.hideMessage = function(what)
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

siggyMap.prototype.updateMessagePositions = function()
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

siggyMap.prototype.centerButtons = function()
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

siggyMap.prototype.initialize = function()
{
	var that = this;
	this.container = $('#chain-map-container');

	this.loadingMessage = this.container.find('p.loading');
	this.editingMessage = this.container.find('p.editing');
	this.deletingMessage = this.container.find('p.deleting');

	this.buttonsContainer = this.container.find('div.buttons');

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
	});

	var $container = $("#chain-map");
	$container.on('mousedown', function(e) {
		if( !that.massSelect )
		{
			return;
		}
		//ulgy hack to make the mass delete selection work for now
		$("#chain-map").width($("#chain-map-scrolltainer")[0].scrollWidth);
		
		that.selectionInProgress = true;
		
		var chainmapOffset = $(this).offset();
		var click_y = e.pageY-chainmapOffset.top,
			click_x = e.pageX-chainmapOffset.left;
			
		that.selectionBox.css({
		  'top':    click_y,
		  'left':   click_x,
		  'width':  0,
		  'height': 0,
		  'z-index': 9999
		});

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
					
					var inside = jsPlumbGeom.intersects( bb, compareBB );
					
					if( inside )
					{
						conn.selected = !conn.selected;
					}
					
					conn.refresh();
				}
			}
		});
	});

	$(document).bind('siggy.switchSystem', function(e, systemID) {
		that.setSelectedSystem( systemID );
		e.stopPropagation();
	});
	
	$('#jump-log-refresh').click( function() {
        that.updateJumpLog(that.editingConnection.settings.hash);
	});
	
	this.initializeHotkeys();
}

siggyMap.prototype.startMapEdit = function()
{
	this.editing = true;
	$('#chain-map-save').show();
	this.centerButtons();

	$('div.map-system-blob').qtip('disable');

	jsPlumb.setDraggable($('.map-system-blob'), true);

	this.showMessage('editing');
}

siggyMap.prototype.initializeHotkeys = function()
{
	var $this = this;
	
	$(document).bind('keydown', 'ctrl+m', function(){
		$(document).scrollTop( 0 );  
	});
	
	this.siggymain.hotkeyhelper.registerHotkey('Ctrl+M', 'Jump to map');
}

siggyMap.prototype.setSelectedSystem = function( systemID )
{
	if( this.selectedSystemID != systemID )
	{
		$( "#"+this.selectedSystemID ).removeClass('map-system-blob-selected');

		$("#"+systemID ).addClass('map-system-blob-selected');

		this.selectedSystemID = systemID;
	}
}

siggyMap.prototype.registerEvents = function()
{
    var that = this;


    $('#chain-map-tabs .minimize').click( function() {
        if( that.mapOpen == 1 )
        {
            that.mapOpen = 0;
            $('#chain-map-inner').hide();
            $('#chain-map-ec').text('Click to show');
            $('#chainPanTrackX').hide();
            that.lastUpdate = 0;
            setCookie('mapOpen', 0, 365);
        }
        else
        {
            that.mapOpen = 1;
            $('#chain-map-inner').show();
            $('#chain-map-ec').text('Click to hide');
            setCookie('mapOpen', 1, 365);
            that.showMessage('loading');

			that.siggymain.updateNow();
        }
    } );

    $('#chain-map-save').click( function() {
        var saveSystemData = [];
        for (var i in that.systems)
        {
            var sysID = that.systems[i].systemID;

            var saveSystem = {};
            saveSystem.id = parseInt(sysID);


            var offset = $( '#'+sysID ).position();
            saveSystem.x = offset.left;
            saveSystem.y = offset.top;

            saveSystemData.push(saveSystem);
        }

        $.post(that.baseUrl + 'chainmap/save', {
            systemData: JSON.stringify(saveSystemData)
        });

        that.editing = false;
        $(this).hide();
        that.hideMessage('editing');
		
        $('div.map-system-blob').qtip('enable');
    } );


    $('#chain-map-mass-delete-confirm').click( function() {
		that.processConnectionDelete();

        that.hideMessage('deleting');
        that.massDelete = false;
		that.massSelect = false;

        $(this).hide();
        $('#chain-map-mass-delete-cancel').hide();

    });

    $('#chain-map-mass-delete-cancel').click( function() {

        for (i in that.mapConnections)
        {
			that.mapConnections[i].selected = false;
			that.mapConnections[i].refresh();
        }

        that.hideMessage('deleting');
        that.massDelete = false;
		that.massSelect = false;

        $(this).hide();
        $('#chain-map-mass-delete-confirm').hide();
    });
}

siggyMap.prototype.processConnectionDelete = function(hashes)
{
	var $this = this;
	
	var hashes = this.getSelectedHashes();
	
	if( hashes.count > 0 )
	{
		$.post(this.baseUrl + 'chainmap/connection_delete', 
			{ 
				wormhole_hashes: JSON.stringify(hashes.wormholes),
				stargate_hashes:JSON.stringify(hashes.stargates),
				jumpbridge_hashes:JSON.stringify(hashes.jumpbridges),
				cyno_hashes: JSON.stringify(hashes.cynos)
			},
			function() {
				$this.siggymain.updateNow();
		});
	}
}

siggyMap.prototype.getSelectedHashes = function()
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


siggyMap.prototype.update = function(timestamp, systems, wormholes, stargates, jumpbridges, cynos)
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

	this.hideMessage('loading');
}

siggyMap.prototype.updateActives = function( activesData )
{
	if(  typeof( activesData ) == 'undefined' )
	{
		return;
	}

    for( var i in this.systems )
    {
        var sysID = this.systems[i].systemID;

        var ele =  $('#' + sysID + ' p.map-system-blob-actives');
        ele.empty();

        var fullActives = $("#fullactives"+sysID);
        fullActives.empty();

        if( typeof(activesData[sysID]) != 'undefined' )
        {
            var actives = activesData[sysID];
            var text = '';

            //setup our lengths
            //TBH, make the max length configurable
            var maxDisplayLen = 7;
            var len = actives.length;
            var displayLen = len > maxDisplayLen ? maxDisplayLen : len;

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
                text += ' +' + (len-displayLen) + ' other...<br \>';
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

siggyMap.prototype.isMapOpen = function()
{
	return this.mapOpen;
}

siggyMap.prototype.draw = function()
{
    var that = this;

    $('div.map-system-blob').qtip('destroy');
    $('div.map-system-blob').destroyContextMenu();
    $('div.map-full-actives').remove();
	
	for( var i  in this.mapconnections )
	{
		this.mapconnections[i].destroy();
		delete this.mapconnections[i];
	}
	
    jsPlumb.deleteEveryEndpoint();
    $('#chain-map').empty();
	
    for( var i in this.systems )
    {
        //local variable assignment
        var systemData = this.systems[i];

        var sysBlob = $("<div>").addClass('map-system-blob').offset({ top: systemData.y, left: systemData.x}).attr("id", systemData.systemID);

        //blob time for the title
        var systemName = $("<span>").text(systemData.displayName == "" ? systemData.name : systemData.displayName).addClass('map-system-blob-sysname');

        var titleClassBit = "";
        if( systemData.sysClass >= 7 || ( systemData.sysClass < 7 && systemData.displayName == "") )
        {
            titleClassBit = $("<span>").addClass('map-system-blob-class').addClass( this.getClassColor( parseInt(systemData.sysClass) ) ).text( this.getClassText( parseInt(systemData.sysClass) ) );
        }

        //effect stuff
        var effectBit = $("<span>");
        var effectClass = this.getEffectColor( parseInt(systemData.effect) );
        if(effectClass != "" )
        {
            effectBit.addClass('map-effect');
            effectBit.addClass(effectClass);
            effectBit.attr('title', this.getEffectText( parseInt(systemData.effect) ) );
        }

        //show info on the name
        systemName.click( function(ele)
        {
            if( that.editing || that.massDelete )
            {
                return false;
            }

            var sysID = $(this).parent().parent().attr("id");
            if( typeof(CCPEVE) != "undefined" )
            {
                    CCPEVE.showInfo(5, sysID );
            }
            else
            {
                    window.open('http://evemaps.dotlan.net/system/'+that.systems[sysID].name , '_blank');
            }
        });

		var killBit = $("<span>");

		if( systemData.sysClass != 7 &&  systemData.sysClass != 8 )
		{
			if( parseInt(systemData.kills_in_last_2_hours) > 0 )
			{
				killBit.append($("<img>").attr("src", this.baseUrl + "public/images/evekill.png").addClass('map-system-blob-mini-icon').attr('title','Kills in last 2 hours'));
			}


			if( parseInt(systemData.npcs_kills_in_last_2_hours) > 0 )
			{
				killBit.append($("<img>").attr("src", this.baseUrl + "public/images/carebear.gif").addClass('map-system-blob-mini-icon').attr('title','NPC Kills in last 2 hours'));
			}
		}

        var systemBlobTitle = $("<p>").addClass('map-system-blob-title').append(titleClassBit).append(effectBit).append(killBit).append(systemName);

        //add empty paragraph for the active chars
        var systemBlobActives = $("<p>").addClass('map-system-blob-actives');
        sysBlob.append(systemBlobTitle).append(systemBlobActives);

        if( this.selectedSystemID == systemData.systemID )
        {
            sysBlob.addClass('map-system-blob-selected');
        }

        // get the activity color class
        var activityClass = this.getActivityColor( parseInt(systemData.activity) );
        sysBlob.addClass( activityClass) ;

        $("#chain-map").append( sysBlob );

        sysBlob.contextMenu( { menu: 'systemMenu' },
            function(action, el, pos) {
                if( action == "edit" )
                {
                    that.openSystemEdit( el[0].id );
                }
                else if( action == "setdest" )
                {
                    if( typeof(CCPEVE) != "undefined" )
                    {
                        CCPEVE.setDestination(el[0].id);
                    }
                }
                else if( action == "showinfo" )
                {
                    if( typeof(CCPEVE) != "undefined" )
                    {
                            CCPEVE.showInfo(5, el[0].id );
                    }
                    else
                    {
                            window.open('http://evemaps.dotlan.net/system/'+ that.systems[el[0].id].name , '_blank');
                    }
                }
        });

        sysBlob.click( function() {
            if( that.editing || that.massDelete )
            {
                return false;
            }
            var sysID = $(this).attr("id");
            that.siggymain.switchSystem(sysID, that.systems[sysID].name);
        } );

        var tst = $("<div>").attr("id","fullactives"+systemData.systemID).addClass('tooltip').addClass('map-full-actives').text("");
        $("#chain-map-container").append(tst);

        var res = sysBlob.qtip({
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
			to: wormhole.to,
			from: wormhole.from,
			hash: wormhole.hash,
			type: 'wormhole',
			wormhole: {
				mass: parseInt(wormhole.mass),
				eolDateSet: parseInt(wormhole.eol_date_set),
				eol:  parseInt(wormhole.eol),
				frigateSized: parseInt(wormhole.frigate_sized),
				totalTrackedMass: wormhole.total_tracked_mass
			}
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
		
		var connection = new mapconnection(jsPlumb,options);
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
			type: 'stargate'
		};
		
		var connection = new mapconnection(jsPlumb,options);
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
			type: 'cyno'
		};
		
		var connection = new mapconnection(jsPlumb,options);
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
			type: 'jumpbridge'
		};
		
		var connection = new mapconnection(jsPlumb,options);
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

		jsPlumb.setDraggable($('.map-system-blob'), false);
	}
}

siggyMap.prototype.openSystemEdit = function( sysID )
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

	$('#system-editor input[name=label]').val( this.systems[ sysID ].displayName );
	$('#system-editor select[name=activity]').val(this.systems[ sysID ].activity);
}

siggyMap.prototype.getEffectText = function(effect)
{
    var effText = '';
    switch( effect )
    {
            case 30574:
                effText = 'Magnetar';
                break;
            case 30575:
                effText = 'Black Hole';
                break;
            case 30576:
                effText = 'Red Giant';
                break;
            case 30577:
                effText = 'Pulsar';
                break;
            case 30669:
                effText = 'Wolf-Rayet';
                break;
            case 30670:
                effText = 'Cataclysmic Variable';
                break;
            default:
                effText = 'No effect';
                break;
    }

    return effText;
}

siggyMap.prototype.getEffectColor = function(effect)
{
    var eff = effect;
    switch( effect )
    {
		case 30574:
			eff = 'map-effect-magnetar'; //magnetar
			break;
		case 30575:	//black hole
			eff = 'map-effect-blackhole';
			break;
		case 30576:
			eff = 'map-effect-red-giant'; //red giant
			break;
		case 30577:
			eff = 'map-effect-pulsar'; //pulsar
			break;
		case 30669:
			eff = 'map-effect-wolf-rayet'; //wolf-rayet
			break;
		case 30670:
			eff = 'map-effect-catalysmic'; //catalysmic
			break;
		default:
			eff = '';
			break;
    }

    return eff;
}

siggyMap.prototype.getClassText = function(sysClass)
{
    var text = "";

    if( sysClass == 7 )
    {
		text = 'H';
    }
    else if( sysClass == 8 )
    {
		text = 'L';
    }
    else if( sysClass == 9 )
    {
		text = '0.0';
    }
    else
    {
		text = 'C'+sysClass;
    }

    return text;
}

siggyMap.prototype.getClassColor = function(sysClass)
{
    var classColor = '';
    switch( sysClass )
    {
		case 1:
		case 2:
		case 3:
				classColor = 'map-class-unknown';
				break;
		case 4:
		case 5:
				classColor = 'map-class-dangerous';
				break;
		case 6:
				classColor = 'map-class-deadly';
				break;
		case 7:
				classColor = 'map-class-high';
				break;
		case 8:
				classColor = 'map-class-low';
				break;
		case 9:
				classColor = 'map-class-null';
				break;
		default:
				classColor = '';
				break;
    }

    return classColor;
}

siggyMap.prototype.getActivityColor = function(activity)
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


siggyMap.prototype.setupEditor = function()
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

	var fromSysInput = this.registerSystemAutoComplete("#connection-editor input[name=from-sys]");
	var toSysInput = this.registerSystemAutoComplete("#connection-editor input[name=to-sys]");

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
				that.siggymain.updateNow();
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
					that.siggymain.updateNow();
				}
				else
				{
					that.displayEditorErrors( resp.dataErrorMsgs );
				}
			});
		}
	} );

	$('#jumpLogClose').click( function() {
		$('#chain-map-container').unblock();
	});

	$('#connection-editor-cancel').click( function() {
		$('#chain-map-container').unblock();
	});
	
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


siggyMap.prototype.displayEditorErrors = function(errors)
{
	var errorsUL = $('#connection-editor ul.errors');
	errorsUL.empty();
	errorsUL.show();

	for( var i = 0; i < errors.length; i++ )
	{
		errorsUL.append( $('<li>').text( errors[i] ) );
	}
}


siggyMap.prototype.setupSystemEditor = function()
{
	var that = this;
	$('#system-editor-cancel').click( function() {
		$('#chain-map-container').unblock();
		that.editingSystem = 0;
	});

	$('#system-editor-save').click( function() {
		var label = $('#system-editor input[name=label]').val();
		var activity = $('#system-editor select[name=activity]').val();


		that.siggymain.saveSystemOptions(that.editingSystem, label, activity);
		$('#chain-map-container').unblock();
	});
}
siggyMap.prototype.updateJumpLog = function( hash )
{
		var logList = $('#jumpLogList');
		logList.empty();

		if( !this.settings.jumpTrackerEnabled )
		{
            return;
		}

		var request = {
			wormhole_hash: hash
		}

		var that = this;
		$.get(this.baseUrl + 'chainmap/jump_log', request, function (data)
		{
			data.totalMass = parseInt(data.totalMass);
			var displayMass = roundNumber(data.totalMass/1000000,2);
			$('#totalJumpedMass').text(displayMass);

			if( data.totalMass > 0 )
			{
				console.log(data);
				console.log(that.systems);
				for( var i in data.jumpItems )
				{
					var item = data.jumpItems[i];

					var mass = roundNumber(parseInt(item.mass)/1000000,2);

					var charName = '';
					if( that.settings.jumpTrackerShowNames )
					{
						charName = ' - '+item.charName;
					}

					var time = '';
					if( that.settings.jumpTrackerShowTime )
					{
						time =  siggymain.displayTimeStamp(item.time);
					}

					var direction = '';
					var fromDName = '';

					if( that.systems[item.origin].displayName != '' )
					{
						fromDName = ' ('+that.systems[item.origin].displayName+')';
					}
					direction += that.systems[item.origin].name+fromDName + ' -> ';

					var toDName = '';
					if( that.systems[item.destination].displayName != '' )
					{
						toDName = ' ('+that.systems[item.destination].displayName+')';
					}
					direction += that.systems[item.destination].name+toDName;


					var s = $('<li><p style="float:left"><b>' + item.shipName +'</b>' + charName + '<br />' +
						item.shipClass + '<br />' +
						time +
						'</p>' +
						'<p style="float:right">' +
						'Mass: ' + mass + 'mil' +
						'</p><div class="clear"></div>' +
						'<div class="center">' + direction +'</div></li>');

					logList.append(s);

				}
			}
			else
			{
				logList.append( $('<li><b>No jumps recorded<b/></li>') );
			}
		} );
}

siggyMap.prototype.registerSystemAutoComplete = function(inputSelector)
{
    var that = this;
    var input = $(inputSelector);
    //resets cause fucking browsers
    input.val('');
    input.prop('disabled',false);
    input.autocomplete({url: that.baseUrl+'chainmap/autocomplete_wh', minChars: 2,
        showResult: function(value, data) {
            if( data[0] != '' )
            {
                return  value + ' (' + data[0] + ')';
            }
            else
            {
                return  value;
            }
        }
    });

    return input;
}

siggyMap.prototype.resetWormholeEditor = function()
{
	var errorsUL = $('#connection-editor ul.errors');
	errorsUL.empty();
	errorsUL.hide();

	var fromCurrentInput = $('#connection-editor input[name=fromCurrent]');
	//resets cause fucking browsers
	fromCurrentInput.prop('disabled', false);
	fromCurrentInput.prop('checked', false);

	var toCurrentInput = $('#connection-editor input[name=toCurrent]');
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

	$('#connection-editor select[name=mass]').val(0);
	$('#connection-editor input[name=eol]').filter('[value=0]').prop('checked', true);
	$('#connection-editor input[name=frigate_sized]').filter('[value=0]').prop('checked', true);
	$('#connection-editor input[name=wh_type_name]').val('');
	
	$('select[name=connection-editor-type]').val('wormhole');
}

siggyMap.prototype.editWormhole = function(conn)
{
	this.editingConnection = conn;

	this.resetWormholeEditor();

	var fromDName = '';

	if( this.systems[conn.settings.from].displayName != '' )
	{
		fromDName = ' ('+this.systems[conn.settings.from].displayName+')';
	}
	$('#connection-editor-from').text(this.systems[conn.settings.from].name+fromDName);

	var toDName = '';
	if( this.systems[conn.settings.to].displayName != '' )
	{
		toDName = ' ('+this.systems[conn.settings.to].displayName+')';
	}
	$('#connection-editor-to').text(this.systems[conn.settings.to].name+toDName);

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

siggyMap.prototype.initializeTabs = function()
{
	var $this = this;

    $('#connection-popup ul.box-tabs li a').click(function()
    {
        $this.setWHPopupTab( $(this).attr('href') );
        return false;
    });

    this.setWHPopupTab( '#connection-editor' );
}


siggyMap.prototype.setWHPopupTab = function( selectedTab )
{
	if( selectedTab == "#jump-log" )
	{
		if( this.settings.jumpTrackerEnabled )
		{
			this.updateJumpLog(this.editingConnection.settings.hash);
		}
	}
	
    var $this = this;
    $('#connection-popup ul.box-tabs li a').each(function()
    {
        var href = $(this).attr('href');

		
        if( href == selectedTab )
        {
            $(this).parent().addClass('active');
            $(href).show();
        }
        else
        {
            $(this).parent().removeClass('active');
            $(href).hide();
        }
    } );
}


siggyMap.prototype.openWHEditor = function(mode)
{
	this.setWHPopupTab('#connection-editor');
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
