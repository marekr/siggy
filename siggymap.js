
function siggyMap(options)
{
	this.defaults = {
			jumpTrackerEnabled: true,
			jumpTrackerShowNames: true,
			jumpTrackerShowTime: true,
            showActivesShips: false
	};


	this.settings = $.extend(this.defaults, options);


	this.systems = {};
	this.wormholes = {};

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
	this.editingHash = 0;

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

	this.selectedSystemRect = null;
	this.selectedSystemID = 0;
	this.infoicon = null;

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
		$('#chainmap-broadcast-button').text('Disable location broadcasting');
	}
	else
	{
		$('#chainmap-broadcast-button').text('Enable location broadcasting');
	}
	$('#chainmap-broadcast-button').click( function(){
		if( that.broadcast == 1)
		{
			that.broadcast = 0;
			$(this).text('Enable location broadcasting');
		}
		else
		{
			that.broadcast = 1;
			$(this).text('Disable location broadcasting');
		}
		setCookie('broadcast', that.broadcast, 365);
	});

	$('#chain-map-add-wh').click( function() {
		that.resetWormholeEditor();
		that.openWHEditor('add');
	});

	$('#chain-map-edit').click( function() {
		that.editing = true;
		$('#chain-map-save').show();
		that.centerButtons();

		if( that.infoicon != null )
		{
				that.infoicon.disabled = true;
		}

		$('div.map-system-blob').qtip('disable');

		jsPlumb.setDraggable($('.map-system-blob'), true);

		that.showMessage('editing');
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
		
		that.massSelect = true;
		
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
			if ( ( that.massSelect )  )
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
			if ( that.massSelect )
			{
				//ulgy hack to make the mass delete selection work for now
				$("#chain-map").width("auto");
		
				that.massSelect = false;
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
					console.log("too small!");
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
        if( that.infoicon != null )
        {
			that.infoicon.disabled = false;
        }
        $('div.map-system-blob').qtip('enable');

    } );


    $('#chain-map-mass-delete-confirm').click( function() {
        var deleteHashes = [];
		
        for (var i in that.mapConnections)
        {
			if( that.mapConnections[i].selected )
			{
                deleteHashes.push( that.mapConnections[i].settings.hash );
				//jsPlumb.detach(conn);
			}
        }

        if( deleteHashes.length > 0 )
        {
            $.post(that.baseUrl + 'chainmap/wh_mass_delete', { hashes: JSON.stringify(deleteHashes) },
                function() {
                that.siggymain.updateNow();
            });
        }

        that.hideMessage('deleting');
        that.massDelete = false;

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

        $(this).hide();
        $('#chain-map-mass-delete-confirm').hide();
    });
}


siggyMap.prototype.update = function(timestamp, systems, wormholes)
{
	if( this.editing || this.massDelete )
	{
		return;
	}

	this.lastUpdate = parseInt(timestamp);

	this.systems = systems;
	this.wormholes = wormholes;

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
			type: 'wh',
			wormhole: {
				mass: parseInt(wormhole.mass),
				eolDateSet: parseInt(wormhole.eolToggled),
				eol:  parseInt(wormhole.eol),
				frigateSized: parseInt(wormhole.frigate_sized)
			}
		};
		
		var connection = new mapconnection(jsPlumb,options);
		connection.map = this;
		connection.create();
		
		this.mapConnections[wormhole.hash] = connection;
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
	$('#wormhole-editor-disconnect').click( function() {
		$.post(that.baseUrl + 'chainmap/wh_disconnect',
		{
			hash: that.editingHash
		},
		function()
		{
			that.siggymain.updateNow();
		});
		$('#chain-map-container').unblock();
	} );

	var fromSysInput = this.registerSystemAutoComplete("#wormhole-editor input[name=from-sys]");
	var toSysInput = this.registerSystemAutoComplete("#wormhole-editor input[name=to-sys]");

	var fromCurrentInput = $('#wormhole-editor input[name=fromCurrent]');
	//resets cause fucking browsers
	fromCurrentInput.attr('disabled', false);
	fromCurrentInput.attr('checked', false);

	var toCurrentInput = $('#wormhole-editor input[name=toCurrent]');
	//resets cause fucking browsers
	toCurrentInput.attr('disabled', false);
	toCurrentInput.attr('checked', false);

	fromCurrentInput.change( function() {
		if( $(this).is(':checked') )
		{
			fromSysInput.attr('disabled',true);
			toCurrentInput.attr('disabled',true);
		}
		else
		{
			fromSysInput.attr('disabled',false);
			toCurrentInput.attr('disabled',false);
		}
	});

	toCurrentInput.change( function() {
		if( $(this).is(':checked') )
		{
			toSysInput.attr('disabled',true);
			fromCurrentInput.attr('disabled',true);
		}
		else
		{
			toSysInput.attr('disabled',false);
			fromCurrentInput.attr('disabled',false);
		}
	});

	$('#wormhole-editor-save').click( function() {

		var data = {};
		if( that.editorMode == 'edit' )
		{
			data = {
				mode: 'edit',
				hash: that.editingHash,
				eol: $('#wormhole-editor input[name=eol]:checked').val(),
				frigate_sized: $('#wormhole-editor input[name=frigate_sized]:checked').val(),
				mass: $('#wormhole-editor select[name=mass]').val()
			};

			$.post(that.baseUrl + 'chainmap/wh_save', data, function()
			{
				that.siggymain.updateNow();
			});


			that.editorOpen = false;
			$('#chain-map-container').unblock();
		}
		else
		{
			var errors = [];

			data = {
				mode: 'add',
				fromSys: fromSysInput.val(),
				fromSysCurrent: ( fromCurrentInput.is(':checked') ? 1 : 0 ),
				toSys: toSysInput.val(),
				toSysCurrent: ( toCurrentInput.is(':checked') ? 1 : 0 ),
				eol: $('#wormhole-editor input[name=eol]:checked').val(),
				frigate_sized: $('#wormhole-editor input[name=frigate_sized]:checked').val(),
				mass: $('#wormhole-editor select[name=mass]').val()
			};

			$.post(that.baseUrl + 'chainmap/wh_save', data, function(resp)
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

	$('#wormhole-editor-cancel').click( function() {
		$('#chain-map-container').unblock();
	});
}


siggyMap.prototype.displayEditorErrors = function(errors)
{
	var errorsUL = $('#wormhole-editor ul.errors');
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
siggyMap.prototype.initializeTabs = function()
{
	var that = this;
	$('#whEdit').click( function() {
      that.setWHPopupTab('editor');
	} );

	if( this.settings.jumpTrackerEnabled )
	{
      $('#jumpLog').click( function() {
        that.setWHPopupTab('jumpLog');
        that.updateJumpLog(that.editingHash);
      } );
	}
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
			whHash: hash
		}

		var that = this;
		$.get(this.baseUrl + 'getJumpLog', request, function (data)
		{
			data.totalMass = parseInt(data.totalMass);
			var displayMass = roundNumber(data.totalMass/1000000,2);
			$('#totalJumpedMass').text(displayMass);

			if( data.totalMass > 0 )
			{
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
    input.attr('disabled',false);
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
	var errorsUL = $('#wormhole-editor ul.errors');
	errorsUL.empty();
	errorsUL.hide();

	var fromCurrentInput = $('#wormhole-editor input[name=fromCurrent]');
	//resets cause fucking browsers
	fromCurrentInput.attr('disabled', false);
	fromCurrentInput.attr('checked', false);

	var toCurrentInput = $('#wormhole-editor input[name=toCurrent]');
	//resets cause fucking browsers
	toCurrentInput.attr('disabled', false);
	toCurrentInput.attr('checked', false);

	var fromSysInput = $("#wormhole-editor input[name=from-sys]");
	//resets cause fucking browsers
	fromSysInput.val('');
	fromSysInput.attr('disabled',false);

	var toSysInput = $("#wormhole-editor input[name=to-sys]");
	//resets cause fucking browsers
	toSysInput.val('');
	toSysInput.attr('disabled',false);

	$('#wormhole-editor select[name=mass]').val(0);
	$('#wormhole-editor input[name=eol]').filter('[value=0]').attr('checked', true);
	$('#wormhole-editor input[name=frigate_sized]').filter('[value=0]').attr('checked', true);
}

siggyMap.prototype.editWormhole = function(hash)
{
	this.editingHash = hash;

	this.resetWormholeEditor();

    var wormhole = this.wormholes[ this.editingHash ];

	var fromDName = '';


	if( this.systems[wormhole.from].displayName != '' )
	{
		fromDName = ' ('+this.systems[wormhole.from].displayName+')';
	}
	$('#whEditFrom').text(this.systems[wormhole.from].name+fromDName);

	var toDName = '';
	if( this.systems[wormhole.to].displayName != '' )
	{
		toDName = ' ('+this.systems[wormhole.to].displayName+')';
	}
	$('#whEditTo').text(this.systems[wormhole.to].name+toDName);

	$('#wormhole-editor select[name=mass]').val(wormhole.mass);
	$('#wormhole-editor input[name=eol]').filter('[value=' + wormhole.eol + ']').attr('checked', true);
	$('#wormhole-editor input[name=frigate_sized]').filter('[value=' + wormhole.frigate_sized + ']').attr('checked', true);

	this.openWHEditor('edit');

}

siggyMap.prototype.setWHPopupTab = function (state)
{
	if( state == 'editor' )
	{
		$('#wormhole-editor').show();
		$('#jumpLogViewer').hide();
	}
	else if( state =='jumpLog' )
	{
		$('#wormhole-editor').hide();
		$('#jumpLogViewer').show();
	}
	$('#wormholeTabs').show();
	return;
}


siggyMap.prototype.openWHEditor = function(mode)
{
	this.setWHPopupTab('editor');
	$('#chain-map-container').block({
		message: $('#wormhole-popup'),
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
	if( mode == 'edit' )
	{
		$('#wh-editor-add').hide();
		$('#wh-editor-edit').show();
		this.editorMode = 'edit';
		this.editorOpen = true;
	}
	else
	{
		$('#wormholeTabs').hide();
		$('#wh-editor-add').show();
		$('#wh-editor-edit').hide();
		this.editorMode = 'add';
		this.editorOpen = true;
	}
}
