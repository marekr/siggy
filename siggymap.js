Raphael.fn.fixNS = function(){
    var r = this;
    for (var ns_name in Raphael.fn) {
        var ns = Raphael.fn[ns_name];
        if (typeof ns == 'object') for (var fn in ns) {
            var f = ns[fn];
            ns[fn] = function(){ return f.apply(r, arguments); }
        }
    }
}

Raphael.fn.connection = function (obj1, obj2, line, bg) {
	
    if (obj1.line && obj1.from && obj1.to) {
        line = obj1;
        obj1 = line.from;
        obj2 = line.to;
    }
    var bb1 = obj1.getBBox(),
        bb2 = obj2.getBBox(),
        p = [{x: bb1.x + bb1.width / 2, y: bb1.y - 1},
        {x: bb1.x + bb1.width / 2, y: bb1.y + bb1.height + 1},
        {x: bb1.x - 1, y: bb1.y + bb1.height / 2},
        {x: bb1.x + bb1.width + 1, y: bb1.y + bb1.height / 2},
        {x: bb2.x + bb2.width / 2, y: bb2.y - 1},
        {x: bb2.x + bb2.width / 2, y: bb2.y + bb2.height + 1},
        {x: bb2.x - 1, y: bb2.y + bb2.height / 2},
        {x: bb2.x + bb2.width + 1, y: bb2.y + bb2.height / 2}],
        d = {}, dis = [];
    for (var i = 0; i < 4; i++) {
        for (var j = 4; j < 8; j++) {
            var dx = Math.abs(p[i].x - p[j].x),
                dy = Math.abs(p[i].y - p[j].y);
            if ((i == j - 4) || (((i != 3 && j != 6) || p[i].x < p[j].x) && ((i != 2 && j != 7) || p[i].x > p[j].x) && ((i != 0 && j != 5) || p[i].y > p[j].y) && ((i != 1 && j != 4) || p[i].y < p[j].y))) {
                dis.push(dx + dy);
                d[dis[dis.length - 1]] = [i, j];
            }
        }
    }
    if (dis.length == 0) {
        var res = [0, 4];
    } else {
        res = d[Math.min.apply(Math, dis)];
    }
    var x1 = p[res[0]].x,
        y1 = p[res[0]].y,
        x4 = p[res[1]].x,
        y4 = p[res[1]].y;
    dx = Math.max(Math.abs(x1 - x4) / 2, 10);
    dy = Math.max(Math.abs(y1 - y4) / 2, 10);
    var x2 = [x1, x1, x1 - dx, x1 + dx][res[0]].toFixed(3),
        y2 = [y1 - dy, y1 + dy, y1, y1][res[0]].toFixed(3),
        x3 = [0, 0, 0, 0, x4, x4, x4 - dx, x4 + dx][res[1]].toFixed(3),
        y3 = [0, 0, 0, 0, y1 + dy, y1 - dy, y4, y4][res[1]].toFixed(3);
    var path = ["M", x1.toFixed(3), y1.toFixed(3), "C", x2, y2, x3, y3, x4.toFixed(3), y4.toFixed(3)].join(",");
    if (line && line.line) {
        line.bg && line.bg.attr({path: path});
        line.line.attr({path: path});
    } else {
        var color = typeof line == "string" ? line : "#000";
        return {
            bg: bg && bg.split && this.path(path).attr({stroke: bg.split("|")[0], fill: "none", "stroke-width": bg.split("|")[1] || 3}),
            line: this.path(path).attr({stroke: color, fill: "none", "stroke-width": 8}),
            from: obj1,
            to: obj2
        };
    }
};

Raphael.el.tooltip = function (tp) {
    this.tp = tp;
    this.tp.ox = 0;
    this.tp.oy = 0;
    this.tp.disabled = false;
    this.tp.hide();
    this.hover(
        function(event)
				{
						if( this.tp.disabled != true )
						{
								this.mousemove(function(event){ 
										var offset = $(event.target.ownerSVGElement).offset();
										this.tp.translate(event.pageX - offset.left - this.tp.ox, event.pageY - offset.top - this.tp.oy);
										this.tp.ox = event.pageX - offset.left;
										this.tp.oy = event.pageY  - offset.top;
								});
								this.tp.show().toFront();
						}
        }, 
        function(event){
            this.tp.hide();
            this.unmousemove();
            });
   
    return this;
}


function siggyMap(options)
{

	this.defaults = {
			jumpTrackerEnabled: true,
			jumpTrackerShowNames: true,
			jumpTrackerShowTime: true
	};


	this.settings = $.extend(this.defaults, options);


	this.systems = {};
	this.wormholes = {};
	
	this.drawnSystems = {};
	this.drawnConnections = [];
			
	this.baseUrl = '';
	this.container = null;
	this.canvas = null;
	this.ctx = null;
	this.svg = null;
	this.r = null;
	this.siggymain = null;
	
	this.loadingMessage = null;
	this.editingMessage = null;
	this.deletingMessage = null;
	
	this.buttonsContainer = null;
	
	this.updated = false;
	
	//holds the actual pixel values of origin
	this.originX = 0;
	this.originY = 0;
	
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
		this.mapOpen = 0;
	}
	
	if( getCookie('broadcast') != null )
	{
		this.broadcast = parseInt(getCookie('broadcast'));
	}
	else
	{
		this.broadcast = 1;
	}
	
	//panning stuff
	this.maxX = 0;
	this.maxBarX = 0;
	this.panXactive = false;
	this.panX = 0;
	
	
	this.massSelect = false;
	this.massSelectBox = null;
	
	
	this.selectedSystemRect = null;
	this.selectedSystemID = 0;
	this.infoicon = null;
	
	//systemeditor
	this.editingSystem = 0;

}

siggyMap.prototype.showMessage = function(what)
{
	if( what == 'loading' )
	{
		this.loadingMessage.css({'top': this.container.height()/2, left: this.container.width()/2 - this.loadingMessage.width()/2});
		this.loadingMessage.show();
	}
	else if( what == 'editing' )
	{
		this.editingMessage.css({left: this.container.width()/2 - this.editingMessage.width()/2});
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
		this.loadingMessage.css({'top': this.container.height()/2 - this.loadingMessage.height()/2, left: this.container.width()/2 - this.loadingMessage.width()/2});
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
		if( $('#chainPanTrackX').is(':visible') )
		{
			bottomOffset += 20;
		}
	}
}

siggyMap.prototype.initialize = function()
{
		var that = this;
		this.container = $('#chainMapContainer');
		
		this.r = Raphael("chainMap", this.container.width(), 400);
	//	$(this.r.canvas).attr({'viewBox': '0 0 '+this.container.width()+' 400'});
		this.r.setViewBox(0, 0, this.container.innerWidth(), 400, true);
	
		$("#chainMap").mousedown( function(e) {
		//this.massDelete && 
				if ( that.massDelete && !that.massSelect )
				{
						that.massSelect = true;
						var offset = $("#chainMap").offset(); 
						that.initialCoordX = e.pageX-offset.left+that.panX;
						that.initialCoordY = e.pageY-offset.top;
				}
		} );
	
		$("#chainMap").mousemove( function(e) {
				if ( ( that.massSelect )  )
				{
						var offset = $("#chainMap").offset(); 
						
						var currentPageX = e.pageX - offset.left+that.panX;
						var currentPageY = e.pageY - offset.top;
						
						var anchorY = 0;
						var anchorX = 0;
						
						var width = 0;
						var height = 0;
						
						if( currentPageX > that.initialCoordX )
						{
							anchorX = that.initialCoordX;
							width = currentPageX - that.initialCoordX;
						}
						else
						{
							anchorX = currentPageX;
							width = that.initialCoordX - currentPageX;
						}
						
						if( currentPageY > that.initialCoordY )
						{
							anchorY = that.initialCoordY;
							height = currentPageY - that.initialCoordY;
						}
						else
						{
							anchorY = currentPageY;
							height = that.initialCoordY - currentPageY;
						}						
						

						if( !that.massSelectBox )
						{
								that.massSelectBox = that.r.rect( anchorX, anchorY, width, height, 5 );
								that.massSelectBox.attr( {
																					'fill': '#000000',
																					'fill-opacity': 0.4
																				} );
						}
						else
						{
								that.massSelectBox.attr( {
																					'x': anchorX,
																					'y': anchorY,
																					'width': width,
																					'height': height
																				} );
						}
						
						
					
				}
		} );
			
		$(window).mouseup( function(e) {
				if ( that.massSelect )
				{
					that.massSelect = false;
					
					var bb = that.massSelectBox.getBBox();
					for (var j = that.drawnConnections.length - 1; j >= 0; j--)
					{
							var inside = false;
							var wormhole = that.drawnConnections[j];                      
							var compareBB = wormhole.line.bgRef.getBBox();    
							
							if( (compareBB.x < bb.x && (compareBB.x+compareBB.width) > bb.x) || (bb.x < compareBB.x && (bb.x+bb.width) > compareBB.x))
							{
									if( (compareBB.y > bb.y && compareBB.y < bb.y+bb.height) ||  (bb.y > compareBB.y && bb.y < compareBB.y+compareBB.height) )
									inside = true;
							} 
							
							if( inside )
							{
									if( wormhole.line.deleteMe )
									{
										wormhole.line.deleteMe = false;
										wormhole.line.attr('stroke', that.getMassColor((that.wormholes[ wormhole.line.hash ].mass)) );
										wormhole.line.bgRef.attr('stroke', that.getTimeColor((that.wormholes[ wormhole.line.hash ].eol)) );
									}
									else
									{
										wormhole.line.attr('stroke', '#006AFE');
										wormhole.line.bgRef.attr('stroke', '#006AFE');
										wormhole.line.deleteMe = true;
									}
							}

					}					
					
					
					that.massSelectBox.remove();
					that.massSelectBox = null;
				}     
		} );
		
		this.loadingMessage = this.container.find('p.loading');
		this.editingMessage = this.container.find('p.editing');
		this.deletingMessage = this.container.find('p.deleting');
		
		this.buttonsContainer = this.container.find('div.buttons');
		
		this.showMessage('loading');
		
		$(window).bind('resize', function() 
		{
			that.r.setSize(that.container.innerWidth(), 400);
			that.updatePan();
			that.updateMessagePositions();
			that.centerButtons();
		});
		var that = this;
		$('#chainMapSave').click( function() {
			var saveSystemData = [];
			for (var i in that.drawnSystems) 
			{
				var saveSystem = {};
				saveSystem.id = that.drawnSystems[i].systemID;
				saveSystem.x = that.drawnSystems[i].attr('x');
				saveSystem.y = that.drawnSystems[i].attr('y');
				
				saveSystemData.push(saveSystem);
			}
			
			$.post(that.baseUrl + 'dochainMapSave', {
				systemData: JSON.stringify(saveSystemData)
			});
			
			that.editing = false;
			$(this).hide();
			//$('#chainMapEdit').show();
			$('#chainMapOptions').data('disabled',false);
			that.hideMessage('editing');
			if( that.infoicon != null )
			{
					that.infoicon.disabled = false;
			}
			
		} );
		
		$('#chainMapContainer h2').click( function() {
			if( that.mapOpen == 1 )
			{
				that.mapOpen = 0;
				$('#chainMapInner').hide();
				$('#chainMapec').text('Click to show');
				$('#chainPanTrackX').hide();
				that.r.clear();
				that.lastUpdate = 0;
				setCookie('mapOpen', 0, 365);
			}
			else
			{
				that.mapOpen = 1;
				$('#chainMapInner').show();
				$('#chainMapec').text('Click to hide');
				setCookie('mapOpen', 1, 365);
				that.showMessage('loading');
			}
		} );
		
		$('#chainMapBroadcast').click( function() {
			if( that.broadcast == 1 )
			{
				that.broadcast = 0;
				setCookie('broadcast', 0, 365);
				$('#broadcastText').text('Location broadcasting is disabled.');
				$(this).text('Enable');
			}
			else
			{
				that.broadcast = 1;
				setCookie('broadcast', 1, 365);
				$('#broadcastText').text('Location broadcasting is enabled.');
				$(this).text('Disable');
			}
		});
		

		
		this.setupEditor();
		
		menu = new siggyMenu(
		{	 
				ele: 'chainMapOptions', 
				dir: 'up',
				optionCallbacks:  
				{ 

					'addWHManual': function() {
							that.resetWormholeEditor();
							that.openWHEditor('add');
					 },
					 'massDeleteWHs': function() {
							that.showMessage('deleting');
							that.massDelete = true;
							
							$('#chainMapOptions').data('disabled',true);
							
							$('#chainMapMassDeleteConfirm').show();
							$('#chainMapMassDeleteCancel').show();
							that.centerButtons();
					 },
					 'editWHMap': function() {
							that.editing = true;
							$('#chainMapSave').show();
							that.centerButtons();
							
							$('#chainMapOptions').data('disabled',true);
							if( that.infoicon != null )
							{
									that.infoicon.disabled = true;
							}
							
							that.showMessage('editing');
					 }

				}  
		});
		
		menu.initialize();
		
		$('#chainMapMassDeleteConfirm').click( function() {
			var deleteHashes = [];
			
			for (var j = that.drawnConnections.length - 1; j >= 0; j--)
			{
					if( that.drawnConnections[j].line.deleteMe )
					{
						deleteHashes.push( that.drawnConnections[j].line.hash );
						that.drawnConnections[j].line.deleteMe = false;
						that.drawnConnections[j].line.remove();
						that.drawnConnections[j].bg.remove();
						
						continue;
					}
			}
			
			if( deleteHashes.length > 0 )
			{
				$.post(that.baseUrl + 'dochainMapWHMassDelete', { hashes: JSON.stringify(deleteHashes) }, 
					function() {
					that.siggymain.updateNow();
				});
			}
			
			that.hideMessage('deleting');
			that.massDelete = false;
			
			
			$(this).hide();
			$('#chainMapMassDeleteCancel').hide();
			$('#chainMapOptions').data('disabled',false);
			
		});
		
		$('#chainMapMassDeleteCancel').click( function() {
			for(var j = 0; j < that.drawnConnections.length; j++)
			{
					that.drawnConnections[j].line.deleteMe = false;
					that.drawnConnections[j].line.attr('stroke', that.getMassColor(parseInt(that.wormholes[ that.drawnConnections[j].line.hash ].mass)) );
					that.drawnConnections[j].bg.attr('stroke', that.getTimeColor(parseInt(that.wormholes[ that.drawnConnections[j].line.hash ].eol)) );
			}
			
			that.hideMessage('deleting');
			that.massDelete = false;
			
			$(this).hide();
			$('#chainMapMassDeleteConfirm').hide();
			$('#chainMapOptions').data('disabled', false);
		});

		this.initializeHorizontalPan();
		
		this.initializeTabs();
		
		
		if( this.settings.jumpTrackerEnabled )
		{
      $('#refreshJumpLog').click( function() {
        that.updateJumpLog(that.editingHash);
      } );
		}
		
		$(document).bind('siggy.switchSystem', function(e, systemID) {
				that.setSelectedSystem( systemID );
				e.stopPropagation();
		});
		
		
		this.setupSystemEditor();
}

siggyMap.prototype.setupSystemEditor = function()
{
	var that = this;
	$('#systemEditorCancel').click( function() {
		$('#chainMapContainer').unblock();
		that.editingSystem = 0;
	});	
	
	$('#systemEditorSave').click( function() {
		var label = $('#systemEditor input[name=label]').val();
		var inUse = $('#systemEditor input[name=inUse]:checked').val();
		var activity = $('#systemEditor select[name=activity]').val();


		that.siggymain.saveSystemOptions(that.editingSystem, label, inUse, activity);
		$('#chainMapContainer').unblock();
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

siggyMap.prototype.setupEditor = function()
{
		var that = this;
		$('#wormholeEditorDisconnect').click( function() {
			$.post(that.baseUrl + 'dochainMapWHDisconnect', {
				hash: that.editingHash
			}, function() {
				that.siggymain.updateNow();
			});
			$('#chainMapContainer').unblock();
		} );
		
		
		var fromSysInput = $("#wormholeEditor input[name=fromSys]");
		//resets cause fucking browsers
		fromSysInput.val('');
		fromSysInput.attr('disabled',false);
		fromSysInput.autocomplete({url: that.baseUrl+'doautocompleteWH', minChars: 2, 
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
		
		var toSysInput = $("#wormholeEditor input[name=toSys]");
		//resets cause fucking browsers
		toSysInput.val('');
		toSysInput.attr('disabled',false);
		toSysInput.autocomplete({url: that.baseUrl+'doautocompleteWH', minChars: 2, 
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
		
		var fromCurrentInput = $('#wormholeEditor input[name=fromCurrent]');
		//resets cause fucking browsers
		fromCurrentInput.attr('disabled', false);
		fromCurrentInput.attr('checked', false);
		
		var toCurrentInput = $('#wormholeEditor input[name=toCurrent]');
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
		} );
		
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
		} );
		

		$('#wormholeEditorSave').click( function() {
			
			var data = {};
			if( that.editorMode == 'edit' )
			{
				data = { 
					mode: 'edit',
					hash: that.editingHash,
					eol: $('#wormholeEditor input[name=eol]:checked').val(),
					mass: $('#wormholeEditor select[name=mass]').val()
				};
				
				$.post(that.baseUrl + 'dochainMapWHSave', data, function() 
				{
						that.siggymain.updateNow();
				});
			
			
				that.editorOpen = false;
				$('#chainMapContainer').unblock();
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
					eol: $('#wormholeEditor input[name=eol]:checked').val(),
					mass: $('#wormholeEditor select[name=mass]').val()
				};
				
				$.post(that.baseUrl + 'dochainMapWHSave', data, function(resp)
				{
					if( parseInt(resp.success) == 1 )
					{
						that.editorOpen = false;
						$('#chainMapContainer').unblock();
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
			$('#chainMapContainer').unblock();
		});	
		
		$('#wormholeEditorCancel').click( function() {
			$('#chainMapContainer').unblock();
		});	
		
}

siggyMap.prototype.setSelectedSystem = function( systemID )
{
		if( this.selectedSystemID != systemID )
		{
				if( this.selectedSystemRect != null )
				{
						this.selectedSystemRect.remove();
						this.selectedSystemRect = null;
				}
				
				if( typeof( this.drawnSystems[ systemID ] ) != 'undefined' )
				{
						var coords = this.drawnSystems[ systemID ].getBBox();
						this.selectedSystemRect = this.r.rect( coords.x-5, coords.y-5, coords.width+10, coords.height+10,2 );
						this.selectedSystemRect.attr({stroke: "#fff", "fill-opacity": 0, "stroke-width": 1, "stroke-dasharray": "."});
						this.selectedSystemRect.toBack();
				}
				else
				{
						this.selectedSystemRect = null;
				}
				
				this.selectedSystemID = systemID;
		}
}

function cancelMapPan()
{
	$('html').unbind('dragstart.pb selectstart.pb mousemove.pb mouseup.pb mouseleave.pb');
}

siggyMap.prototype.updatePan = function()
{
	//$(this.r.canvas).attr({'viewBox': this.panX+' 0 '+this.container.innerWidth()+' 400'});
	this.r.setViewBox(this.panX, 0, this.container.innerWidth(), 400, true);
	
	this.dragPanX(this.panX);
	this.computeScrollbars();
}

siggyMap.prototype.initializeHorizontalPan = function()
{	
	
	var that = this;
	var horizontalDrag = $('#chainPanBarX');
	horizontalDrag.bind(
		'mousedown.pb',
		function(e)
		{
			// Stop IE from allowing text selection
			$('html').bind('dragstart.pb selectstart.pb', function() {return false; });

			horizontalDrag.addClass('panActive');

			var startX = e.pageX - horizontalDrag.position().left;

			$('html').bind(
				'mousemove.pb',
				function(e)
				{
					that.dragPanX(e.pageX - startX, false);
				}
			).bind('mouseup.pb mouseleave.pb', cancelMapPan);
			return false;
		}
	);
}

siggyMap.prototype.dragPanX = function( destX )
{
		var horizontalDrag = $('#chainPanBarX');
		if( destX < 0 )
		{
			destX = 0;
		}
		else if( destX > this.maxBarX )
		{
			destX = this.maxBarX;
		}
		
		horizontalDrag.css('left', destX);
		
		
		var XratioShown = this.container.innerWidth()/(this.maxX+this.container.innerWidth() );
		this.panX = destX/XratioShown;
		this.r.setViewBox(this.panX, 0, this.container.innerWidth(), 400, true);
}


siggyMap.prototype.computeScrollbars = function()
{
	var containerWidth = this.container.innerWidth()-1;
	this.maxX = this.getMaxX();
	var panBarX = $('#chainPanBarX');
	
	if( containerWidth < this.maxX+70 )
	{
		$('#chainPanTrackX').show();
		
		var inView = containerWidth/(this.maxX+70);
		
		panBarX.width(containerWidth*inView);
		
		this.maxBarX = containerWidth - panBarX.width();
		this.panXactive = true;
	}
	else
	{
		$('#chainPanTrackX').hide();
		this.panXactive = false;
		this.panX = 0;
	//	$(this.r.canvas).attr({'viewBox':'0 0 '+this.container.innerWidth()+' 400'});
		this.r.setViewBox(0, 0, this.container.innerWidth(), 400, true);
	}
	
	if( panBarX.width() + panBarX.position().left > $('#chainPanTrackX').innerWidth() )
	{
		this.dragPanX( $('#chainPanTrackX').innerWidth() - panBarX.width() );
	}
}

siggyMap.prototype.getMaxX = function()
{
	var maxX = 0;
	for( var i in this.drawnSystems )
	{
		if( this.drawnSystems[i].attr('x') > maxX )
		{
			maxX = this.drawnSystems[i].attr('x');
		}
	}
	
	return maxX;
}

siggyMap.prototype.displayEditorErrors = function(errors)
{
	var errorsUL = $('#wormholeEditor ul.errors');
	errorsUL.empty();
	errorsUL.show();
	
	for( var i = 0; i < errors.length; i++ )
	{
		errorsUL.append( $('<li>').text( errors[i] ) );
	}
}

siggyMap.prototype.resetWormholeEditor = function()
{
	var errorsUL = $('#wormholeEditor ul.errors');
	errorsUL.empty();
	errorsUL.hide();
	
	var fromCurrentInput = $('#wormholeEditor input[name=fromCurrent]');
	//resets cause fucking browsers
	fromCurrentInput.attr('disabled', false);
	fromCurrentInput.attr('checked', false);

	var toCurrentInput = $('#wormholeEditor input[name=toCurrent]');
	//resets cause fucking browsers
	toCurrentInput.attr('disabled', false);
	toCurrentInput.attr('checked', false);
	
	var fromSysInput = $("#wormholeEditor input[name=fromSys]");
	//resets cause fucking browsers
	fromSysInput.val('');
	fromSysInput.attr('disabled',false);
	
	var toSysInput = $("#wormholeEditor input[name=toSys]");
	//resets cause fucking browsers
	toSysInput.val('');
	toSysInput.attr('disabled',false);
	
	$('#wormholeEditor select[name=mass]').val(0);
	$('#wormholeEditor input[name=eol]').filter('[value=0]').attr('checked', true);
}

siggyMap.prototype.update = function(timestamp, systems, wormholes)
{
	if( this.editing || this.massDelete )
	{
		return;
	}
	
	this.lastUpdate = timestamp;
	
	this.systems = systems;
	this.wormholes = wormholes;
	this.selectedSystemRect = null;
	this.selectedSystem = 0;
	for( var i in this.drawnConnections )
	{
		if( this.drawnConnections[i].line.bgref != undefined )
		{
			this.drawnConnections[i].line.bgref = null;
		}
		
		if( this.drawnConnections[i].line != undefined )
		{
			this.drawnConnections[i].line.remove();
		}
		
		if( this.drawnConnections[i].bg != undefined )
		{
			this.drawnConnections[i].bg.remove();
		}
		delete this.drawnConnections[i];
	}
	this.drawnConnections = [];
	
	for( var i in this.drawnSystems )
	{
		if( this.drawnSystems[i].charText != undefined )
		{
			this.cleanupJqueryEvents(this.drawnSystems[i].charText);
			this.drawnSystems[i].charText.remove();
			delete this.drawnSystems[i].charText;
			this.drawnSystems[i].charText =  null;
		}
		
		if( this.drawnSystems[i].nameText != undefined )
		{		
			this.cleanupJqueryEvents(this.drawnSystems[i].nameText);
			this.drawnSystems[i].nameText.remove();
			this.drawnSystems[i].nameText = null;
		}
		
		if( this.drawnSystems[i].classText != undefined )
		{
			this.drawnSystems[i].classText.remove();
			delete this.drawnSystems[i].classText
			this.drawnSystems[i].classText = null;
		}
		
		if( this.drawnSystems[i].effectIndicator != undefined )
		{
			this.drawnSystems[i].effectIndicator.remove();
			delete this.drawnSystems[i].effectIndicator
			this.drawnSystems[i].effectIndicator = null;
		}
		
		if( this.drawnSystems[i].test != undefined )
		{
			this.drawnSystems[i].test.remove();
			delete this.drawnSystems[i].test
		}
		
		if( this.drawnSystems[i] != undefined )
		{
			this.cleanupJqueryEvents(this.drawnSystems[i]);
		//	this.drawnSystems[i].undrag();
			this.drawnSystems[i].remove();
			delete this.drawnSystems[i];
		}
		
		
	}
	this.drawnSystems = {};
	
	if( this.infoicon != undefined )
	{
		this.infoicon.remove();
		this.infoicon = null;
	}
	//clear r after we nuke the js objects
	this.r.clear();
	this.draw();
	this.updated = true;
	
	this.hideMessage('loading');
}

siggyMap.prototype.updateActives = function( activesData )
{
	if(  typeof( activesData ) == 'undefined' )
	{
			return;
	}

	
	for( var i in this.drawnSystems )
	{
		if( typeof( activesData[i] ) != 'undefined' )
		{
				this.populateBlobBody( this.drawnSystems[i], i, activesData[i] );
		}
		else
		{
				this.populateBlobBody( this.drawnSystems[i], i, '' );
		}
		this.populateBlobTitle( this.drawnSystems[i],  this.systems[i].name, this.systems[i].displayName, this.systems[i].sysClass,this.systems[i].systemID, this.systems[i].effect, this.systems[i].special );
	}
	
	for (var i = this.drawnConnections.length; i--;) {
			this.r.connection(this.drawnConnections[i]);
	}
}

siggyMap.prototype.isMapOpen = function()
{
	return this.mapOpen;
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
					if( that.drawnSystems[item.origin].displayName != '' )
					{
						fromDName = ' ('+that.drawnSystems[item.origin].displayName+')';
					}
					direction += that.drawnSystems[item.origin].systemName+fromDName + ' -> ';
					
					var toDName = '';
					if( that.drawnSystems[item.destination].displayName != '' )
					{
						toDName = ' ('+that.drawnSystems[item.destination].displayName+')';
					}
					direction += that.drawnSystems[item.destination].systemName+toDName;					
					
					
					
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

siggyMap.prototype.editWormhole = function(hash)
{
	this.editingHash = hash;
	
	this.resetWormholeEditor();
					
	var fromDName = '';
	if( this.drawnSystems[this.wormholes[ this.editingHash ].from].displayName != '' )
	{
		fromDName = ' ('+this.drawnSystems[this.wormholes[ this.editingHash ].from].displayName+')';
	}
	$('#whEditFrom').text(this.drawnSystems[this.wormholes[ this.editingHash ].from].systemName+fromDName);
	
	var toDName = '';
	if( this.drawnSystems[this.wormholes[ this.editingHash ].to].displayName != '' )
	{
		toDName = ' ('+this.drawnSystems[this.wormholes[ this.editingHash ].to].displayName+')';
	}
	$('#whEditTo').text(this.drawnSystems[this.wormholes[ this.editingHash ].to].systemName+toDName);
	
	$('#wormholeEditor select[name=mass]').val(this.wormholes[ this.editingHash ].mass);
	$('#wormholeEditor input[name=eol]').filter('[value=' + this.wormholes[ this.editingHash ].eol + ']').attr('checked', true);
	
	this.openWHEditor('edit');

}

siggyMap.prototype.setWHPopupTab = function (state)
{
	if( state == 'editor' )
	{
		$('#wormholeEditor').show();
		$('#jumpLogViewer').hide();
	}
	else if( state =='jumpLog' )
	{
		$('#wormholeEditor').hide();
		$('#jumpLogViewer').show();
	}
	$('#wormholeTabs').show();
	return;
}

siggyMap.prototype.openWHEditor = function(mode)
{
	this.setWHPopupTab('editor');
	$('#chainMapContainer').block({
		message: $('#wormholePopup'),
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
		$('#whEditorAdd').hide();
		$('#whEditorEdit').show();
		this.editorMode = 'edit';
		this.editorOpen = true;
	}
	else
	{
		$('#wormholeTabs').hide();
		$('#whEditorAdd').show();
		$('#whEditorEdit').hide();
		this.editorMode = 'add';
		this.editorOpen = true;
	}
	
}

siggyMap.prototype.draw = function()
{
		if( typeof( this.infoicon ) == 'undefined' ||  this.infoicon == null )
		{
				if( $.browser.eveIGB )
				{
						var infoX = 6;
						var infoY = -10;
				}
				else if( $.browser.mozilla)
				{
						var infoX = 50;
						var infoY = -25;
				}
				else
				{
						var infoX = 10;
						var infoY = -10;
				}
				this.infoicon = this.r.image(this.baseUrl+'/public/images/information.png',infoX,infoY,16,16);
		}

		var that = this;
		
		
		var dragger = function () {
				if( !that.editing )
				{
					return;
				}
				this.ox = this.type == 'rect' ? this.attr('x') : this.attr('cx');
				this.oy = this.type == 'rect' ? this.attr('y') : this.attr('cy');
				
				this.nameText.ox = this.nameText.attr('x');
				this.nameText.oy = this.nameText.attr('y');
				
				this.classText.ox = this.classText.attr('x');
				this.classText.oy = this.classText.attr('y');
				
				if (typeof(this.charText) != 'undefined' && typeof(this.charText) != 'undefined' && this.charText !== null)
				{
					this.charText.ox = this.charText.attr('x');
					this.charText.oy = this.charText.attr('y');
				}
				
				if( typeof(this.effectIndicator) != 'undefined' )
				{
						this.effectIndicator.ox = this.effectIndicator.attr('cx');
						this.effectIndicator.oy = this.effectIndicator.attr('cy');
				}
				
				this.animate({"fill-opacity": .5}, 500);
		};
		move = function (dx, dy) {
				if( !that.editing )
				{
					return;
				}
				var att = this.type == 'rect' ? {x: this.ox + dx, y: this.oy + dy} : {cx: this.ox + dx, cy: this.oy + dy};
				this.attr(att);
				
				if( this.systemID == that.selectedSystemID )
				{
						that.selectedSystemRect.attr( {x: att.x-5, y: att.y-5} );
				}
				
				this.nameText.attr({x: this.nameText.ox + dx, y: this.nameText.oy + dy});
				this.classText.attr({x: this.classText.ox + dx, y: this.classText.oy + dy});
				
				this.test.attr({x: this.test.ox + dx, y: this.test.oy + dy});
				
				
				if (typeof(this.charText) != 'undefined' && typeof(this.charText) != 'undefined' && this.charText !== null)
				{
					this.charText.attr({x: this.charText.ox + dx, y: this.charText.oy + dy});
				}
	
				this.test.attr({path: 'M'+ this.getBBox().x +','+ (this.nameText.getBBox().y+14)+'L'+( this.getBBox().x+this.getBBox().width)+','+ (this.nameText.getBBox().y+14) });
				
				if( typeof(this.effectIndicator) != 'undefined' )
				{
					this.effectIndicator.attr({cx: this.effectIndicator.ox + dx, cy: this.effectIndicator.oy + dy});
				}	
				
				
				for (var i = that.drawnConnections.length; i--;) {
						that.r.connection(that.drawnConnections[i]);
				}
				that.r.safari();
		};
		up = function () {
				if( !that.editing )
				{
					return;
				}
				that.updatePan();
				this.animate({"fill-opacity": 1}, 500);
		};
		
		for( var i in this.systems )
		{
			var x = this.originX+parseInt(this.systems[i].x);
			var y = this.originY+parseInt(this.systems[i].y);
			
			if( x > this.maxX )
			{
				this.maxX = x;
			} 
			
			var heightBonus = 0;
			var activesText = '';
			
			var systemBlob = this.r.rect(x, y,  70, 30, 4);
			this.populateBlobBody( systemBlob, this.systems[i].systemID, this.systems[i].actives );
			
			this.populateBlobTitle( systemBlob,  this.systems[i].name, this.systems[i].displayName, this.systems[i].sysClass,this.systems[i].systemID, this.systems[i].effect, this.systems[i].special );
			
			systemBlob.drag(move, dragger, up);
			
			//var color = Raphael.getColor();
			
			var color = this.getActivityColor( parseInt(this.systems[i].activity) );
			
			systemBlob.attr({fill: '#fff', stroke: color, "fill-opacity": 1, "stroke-width": 3, cursor: "pointer"});
				
			
			
			this.drawnSystems[ this.systems[i].systemID ] = systemBlob;
			this.drawnSystems[ this.systems[i].systemID ].systemID = this.systems[i].systemID;
			this.drawnSystems[ this.systems[i].systemID ].systemName = this.systems[i].name;
			this.drawnSystems[ this.systems[i].systemID ].displayName = this.systems[i].displayName;

			systemBlob.click( function() {
				if( that.editing || that.massDelete )
				{
					return false;
				}
				that.siggymain.switchSystem(this.systemID, this.systemName);
			} );
			
			$(systemBlob.node).contextMenu( { menu: 'systemMenu' },
				function(action, el, pos) {
					if( action == "edit" )
					{
						that.openSystemEdit( el[0].raphael.systemID );
					}
			});
		}
		
		for( var w in this.wormholes )
		{
			//local variable to make code smaller
			var wormhole = this.wormholes[w];
			
			//check to see if both exists were drawn(or else error will occur)
			if( typeof( this.drawnSystems[wormhole.from] ) == 'undefined' || typeof( this.drawnSystems[wormhole.to] ) == 'undefined' )
			{
				continue;
			}
			
			var connection = this.r.connection(this.drawnSystems[wormhole.from], this.drawnSystems[wormhole.to], this.getMassColor(parseInt(wormhole.mass)), this.getTimeColor(parseInt(wormhole.eol))+"|12");
			connection.line.hash = w;
			connection.line.bgRef = connection.bg;
			connection.line.deleteMe = false;
			connection.line.click( function(e) 
			{	
				if( that.massDelete )
				{
					if( this.deleteMe )
					{
						this.deleteMe = false;
						this.attr('stroke', that.getMassColor(parseInt(that.wormholes[ this.hash ].mass)) );
						this.bgRef.attr('stroke', that.getTimeColor(parseInt(that.wormholes[ this.hash ].eol)) );
					}
					else
					{
						this.attr('stroke', '#006AFE');
						this.bgRef.attr('stroke', '#006AFE');
						this.deleteMe = true;
					}
				}
				else
				{
					that.editWormhole(this.hash);
				}
			});
			
			wormhole.eolToggled = parseInt(wormhole.eolToggled);
			if( wormhole.eolToggled != 0 )
			{
				this.setUpWHToolTip( connection,  wormhole.eolToggled );
			}
			
			this.drawnConnections.push(connection);
			
		}
		
		that.updatePan();
}

siggyMap.prototype.openSystemEdit = function( sysID )
{
	this.editingSystem = sysID;
	
	$('#chainMapContainer').block({
		message: $('#systemOptionsPopup'),
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
	
	$('#systemEditor input[name=label]').val( this.systems[ sysID ].displayName );
	$('#systemEditor select[name=activity]').val(this.systems[ sysID ].activity);
	$('#systemEditor input[name=inUse]').filter('[value=' + this.systems[ sysID ].inUse + ']').attr('checked', true);
	
}

siggyMap.prototype.populateBlobTitle = function( blob, sysName, dispName, sysClass, sysID, effect, homeSys )
{
		effect = parseInt(effect);
		var that = this;
		
		if( typeof(blob) == 'undefined' )
		{
				return;
		}
		

		//update the text code
		if( typeof(blob.classText) == 'undefined' )
		{
					sysClass = parseInt(sysClass);
					
					var classColor = '#000000';
					switch( sysClass )
					{
							case 1:
							case 2:
							case 3:
									classColor = '#2783D9';
									break;
							case 4:
							case 5:
									classColor = '#FF9308';
									break;
							case 6:
									classColor = '#FF0D0D';
									break;
							case 7:
									classColor = '#259900';
									break;
							case 8:
									classColor = '#FF9308';
									break;
							case 9:
									classColor = '#FF0D0D';
									break;
							default:
									classColor = '#000000';
									break;
					}
					
					if( sysClass == 7 )
					{
							sysClass = 'H';
					}
					else if( sysClass == 8 )
					{
							sysClass = 'L';
					}
					else if( sysClass == 9 )
					{
							sysClass = '0.0';
					}
					else if( dispName == '' )
					{
							sysClass = 'C'+sysClass;
					}
					else
					{
							sysClass = '';
					}
					
					blob.classText = this.r.text(blob.attr("x")+3, blob.attr("y")+8, sysClass );
					blob.classText.attr({'font-size': 11, 'font-weight': 'bold', 'font-style': 'italic'});
					blob.classText.attr({'text-anchor': 'start'});
					blob.classText.attr({'fill': classColor});
					
					
		}		
		cBB = blob.classText.getBBox();
		var requiredWidth = cBB.width;
		/* chrome retardness fix where blank text elements dont return anything*/
		if( cBB.x == 0 )
		{
				cBB.x = blob.attr("x")+3;
		}
		
		if( typeof(blob.nameText) == 'undefined' )
		{
				blob.nameText = this.r.text( cBB.x+cBB.width+2, blob.attr('y')+8, ( (dispName != '' ) ? dispName : sysName) );
				blob.nameText.attr({'font-size': 12, 'font-weight': 'bold'});
				blob.nameText.attr({'text-anchor': 'start','cursor': 'pointer'});
				blob.nameText.systemID = sysID;
				
				blob.nameText.click( function() {
					if( that.editing )
					{
							return;
					}
					if( $.browser.eveIGB ) 
					{
							CCPEVE.showInfo(5, this.systemID);
					}
					else
					{
							window.open('http://evemaps.dotlan.net/system/'+sysName, '_blank');
					}
				} );
				
				this.passAllMouseEvents( blob.nameText.node, blob.node );
				$(blob.nameText.node).bind('contextmenu', function() { return false; })

				
				requiredWidth += blob.nameText.getBBox().width;
		}
		
		if( effect != 0 && typeof(blob.effectIndicator) == 'undefined' )
		{
				blob.effectIndicator = this.r.circle( blob.nameText.getBBox().x+blob.nameText.getBBox().width+8, blob.attr('y')+8, 4);
				
				switch( effect )
				{
						case 30574:
							var eff = 'pink'; //magnetar
							var effText = 'Magnetar';
							break;
						case 30575:	//black hole
							var eff = '#000';
							var effText = 'Black Hole';
							break;
						case 30576:
							var eff = 'red'; //red giant
							var effText = 'Red Giant';
							break;
						case 30577:
							var eff = 'blue'; //pulsar
							var effText = 'Pulsar';
							break;
						case 30669:
							var eff = 'orange'; //wolf-rayet
							var effText = 'Wolf-Rayet';
							break;
						case 30670:
							var eff = 'yellow'; //catalysmic
							var effText = 'Cataclysmic Variable';
							break;
						default:
							var eff = '#fff';
							var effText = 'No effect';
							break;
				}
				blob.effectIndicator.attr({'stroke': '#000', 'fill': eff});
				requiredWidth += blob.effectIndicator.getBBox().width+2;
				
				this.setUpBoxedToolTip( blob.effectIndicator,  effText )
		}
		
		if( blob.nameText.infoIconSet == undefined )
		{
			blob.nameText.tooltip(this.infoicon);	
			blob.nameText.infoIconSet = true;
		}
		if( requiredWidth > blob.getBBox().width-7 )
		{
					blob.attr({'width': requiredWidth+10});
		}
		
		
		if( typeof(blob.test) != 'undefined' )
		{
			blob.test.remove();
		}
		blob.test = this.r.path('M'+ blob.getBBox().x +','+ (blob.nameText.getBBox().y+14)+'L'+( blob.getBBox().x+blob.getBBox().width)+','+ (blob.nameText.getBBox().y+14) );
		blob.test.attr({'stroke': 'grey', 'stroke-width': '1px'});
}


siggyMap.prototype.populateBlobBody = function( blob, sysID, body )
{
		if( typeof(blob) == 'undefined' )
		{
				return;
		}

		var that = this;
		
		//console.log("generating blob body for sysID:"+sysID);
		//console.log("body is:"+body);

		var heightBonus = 0;
		var bodyText = '';
		
		if( typeof(body) != 'undefined' && body != '' )
		{
				var actives = body.split(',');
				for(var j = 0; j < actives.length; j++)
				{
						heightBonus += 11;
						bodyText += actives[j] + '\n';
				}
			//	console.log("bodyText:"+bodyText);
		}
		
		var bb = blob.getBBox();
		
		if( bodyText != '' && typeof(blob.charText) == 'undefined' )
		{		
				//console.log("generating new body object");
				blob.charText = this.r.text(blob.attr("x")+4, blob.attr("y")+27, '');
				blob.charText.attr({'text-anchor': 'start'});
				blob.charText.attr({'font-size': 10});
				
				blob.charText.attr({'text': bodyText});
				blob.charText.attr({y: (blob.attr("y")+(blob.charText.getBBox(true).height/2)+15) });
				
				this.passAllMouseEvents( blob.charText.node, blob.node );

				$(blob.charText.node).bind('contextmenu', function() { return false; })

				
				blob.attr({'height':30+heightBonus});
				if( blob.charText.getBBox().width > bb.width-4 )
				{
					blob.attr({'width': blob.charText.getBBox().width+10});
				}
		}	
		else if( typeof(blob.charText) != 'undefined' )
		{
				//console.log("updating existing body object");
				if( bodyText != '' )
				{
						blob.charText.attr({'text': bodyText});
						blob.charText.attr({y: (blob.attr("y")+(blob.charText.getBBox(true).height/2)+15) });		
						//console.log("text updated");
						
						blob.attr({'height':30+heightBonus});
						if( blob.charText.getBBox().width > bb.width-4 )
						{
							blob.attr({'width': blob.charText.getBBox().width+10});
						}
						
				}
				else
				{
						//blob.charText.attr({'text':''});
						this.cleanupJqueryEvents(blob.charText.node);
						
						blob.charText.remove();
						delete blob.charText;			/*ensures the actual charText reference is undefined because :CCP:*/
						//console.log(blob.charText);
						blob.attr({'width': 70, 'height': 30});
				}
		}
		

		if( sysID != "" && this.selectedSystemID == sysID )
		{
				var coords = blob.getBBox();
				if( this.selectedSystemRect != null )
				{
						this.selectedSystemRect.attr( {x: coords.x-5, y: coords.y-5, width: coords.width+10, height: coords.height+10} );
				}
				else
				{
						this.selectedSystemRect = this.r.rect( coords.x-5, coords.y-5, coords.width+10, coords.height+10, 2 );
				}
				this.selectedSystemRect.attr({stroke: "#fff", "fill-opacity": 0, "stroke-width": 1, "stroke-dasharray": "."});
				this.selectedSystemRect.toBack();
		}		
}

siggyMap.prototype.cleanupJqueryEvents = function(el)
{
	$(el).off();
}

siggyMap.prototype.passAllMouseEvents = function(el, targetEl)
{
	var that = this;
	$(el).mousedown(function (e) {
		return that.passMouseEvent(targetEl, e, 'mousedown');
	});
	$(el).mouseup(function (e) {
		return that.passMouseEvent(targetEl, e, 'mouseup');
	});
	$(el).mousemove(function (e) {
		return that.passMouseEvent(targetEl, e, 'mousemove');
	});
}

siggyMap.prototype.passMouseEvent = function(targetEl, e, type)
{
	if( $.browser.msie && parseInt($.browser.version) < 9)
	{
		//e.stopPropagation();
		// base this new event on the existing event object, e
		var myEvt = document.createEventObject(e);

		targetEl.fireEvent('on' + type, myEvt);
		
		return false;
	}
	else
	{
	//	e.stopPropagation();
		var myEvt = document.createEvent('MouseEvents');
		myEvt.initMouseEvent(e.type, e.bubbles, e.cancelable, window, e.detail,
		  e.screenX, e.screenY, e.clientX, e.clientY, e.ctrlKey, e.altKey, e.shiftKey,
		  e.metaKey, e.button, e.relatedTarget);

		targetEl.dispatchEvent(myEvt);
		return false;
	}
}



siggyMap.prototype.setUpBoxedToolTip = function(trigger, displayText)
{
				var baseX = 0
				var baseY = -8;
				if( $.browser.eveIGB ) 
				{
						baseX = 6;
				}
				else if( $.browser.mozilla )
				{
						baseX = 40;
				}
				else
				{
						baseX = 55;
				}
				
				var text = this.r.text(baseX, baseY, displayText ).attr("fill", "#fff");
				
				var box = this.r.rect(baseX-2, baseY*2, text.getBBox().width+4, 16).attr({'fill': '#262626'});
				text.attr({'text-anchor': 'start'});

				var st = this.r.set();
				st.push( box, text );
				trigger.tooltip(st);
}

siggyMap.prototype.setUpWHToolTip = function(connection, eolAt )
{
				//var text = this.r.text(6, -8, 'EOL set at: '+ siggymain.displayTimeStamp(eolAt) ).attr("fill", "#fff");
				
				//var box = this.r.rect(4, -16, text.getBBox().width+4, 16).attr({'fill': '#262626'});
				//text.attr({'text-anchor': 'start'});

				//var st = this.r.set();
				//st.push( box, text );
				//connection.line.tooltip(st);
				this.setUpBoxedToolTip( connection.line,  'EOL set at: '+ siggymain.displayTimeStamp(eolAt) )
}

siggyMap.prototype.getActivityColor = function(activity)
{
		var color = '';
		
		
		switch( activity )
		{
			case 1:
				color = '#03B807';
			break;
			case 2:
				color = '#FFE205';
			break;
			case 3:
				color = '#DE4444';
			break;
			case 4:
				color = '#092665';
			break;
			default:
				color = '#676767';
			break;
			
		}
		
		
		return color;
}

siggyMap.prototype.getMassColor = function(mass)
{
		var inner = '#676767';
		if( mass == 1 )
		{
			inner = '#e2cb06';
		}
		else if( mass == 2 )
		{
			inner = '#9a0808';
		}
		return inner;
}

siggyMap.prototype.getTimeColor = function(eol)
{
		var outer = '#3d3d3d';
		if( eol == 1 )
		{
			outer = '#FF17FE';
		}
		return outer;
}

