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
    this.tp.hide();
    this.hover(
        function(event){ 
            this.mousemove(function(event){ 
								var offset = $(event.target.ownerSVGElement).offset();
                this.tp.translate(event.pageX - offset.left - this.tp.ox, event.pageY - offset.top - this.tp.oy);
                this.tp.ox = event.pageX - offset.left;
                this.tp.oy = event.pageY  - offset.top;
            });
            this.tp.show().toFront();
        }, 
        function(event){
            this.tp.hide();
            this.unmousemove();
            });
    return this;
}


function siggyMap()
{
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
}

siggyMap.prototype.showMessage = function(what)
{
	if( what == 'loading' )
	{
		this.loadingMessage.css({'top': this.container.height()/2 - this.loadingMessage.height()/2, left: this.container.width()/2 - this.loadingMessage.width()/2});
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
		this.buttonsContainer.css({left: this.container.width()/2 - this.buttonsContainer.width()/2, bottom: bottomOffset+'px'});
	}
}

siggyMap.prototype.initialize = function()
{
		var that = this;
		this.container = $('#chainMapContainer');
		
		this.r = Raphael("chainMap", this.container.width(), 400);
		$(this.r.canvas).attr({'viewBox': '0 0 '+this.container.width()+' 400'});
	
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
										wormhole.line.attr('stroke', that.getMassColor(parseInt(that.wormholes[ wormhole.line.hash ].mass)) );
										wormhole.line.bgRef.attr('stroke', that.getTimeColor(parseInt(that.wormholes[ wormhole.line.hash ].eol)) );
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
		
		this.buttonsContainer = this.container.find('p.buttons');
		
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
		
		$('#wormholeEditorCancel').click( function() {
			$('#chainMapContainer').unblock();
		});	
		
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
}

function cancelMapPan()
{
	$('html').unbind('dragstart.pb selectstart.pb mousemove.pb mouseup.pb mouseleave.pb');
}

siggyMap.prototype.updatePan = function()
{
	$(this.r.canvas).attr({'viewBox': this.panX+' 0 '+this.container.innerWidth()+' 400'});
	
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
		$(this.r.canvas).attr({'viewBox': this.panX+' 0 '+this.container.innerWidth()+' 400'});
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
		$(this.r.canvas).attr({'viewBox':'0 0 '+this.container.innerWidth()+' 400'});
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
	this.r.clear();
	this.drawnConnections = [];
	this.drawnSystems = {};
	this.draw();
	this.updated = true;
	
	this.hideMessage('loading');
}

siggyMap.prototype.updateActives = function( activesData )
{
	if( typeof( activesData ) == 'undefined' )
	{
		return;
	}
	
	for( var i in this.drawnSystems )
	{
		if( typeof( activesData[i] ) != 'undefined' )
		{
			var a = activesData[i].split(',');
			var activesText = '';
			var heightBonus = 0;
			for(var j = 0; j < a.length; j++)
			{
				heightBonus += 11;
				activesText += a[j] + '\n';
			}
			
			if( activesText != '' )
			{		
					this.drawnSystems[i].charText.attr({'text': activesText});
					this.drawnSystems[i].charText.attr({y: (this.drawnSystems[i].attr("y")+(this.drawnSystems[i].charText.getBBox().height/2)+15) });
					
					this.drawnSystems[i].attr({'height':30+heightBonus});
					if( this.drawnSystems[i].charText.getBBox().width > 70-4 )
					{
						this.drawnSystems[i].attr({'width': this.drawnSystems[i].charText.getBBox().width+10});
					}
			}
		}
		else
		{
			this.drawnSystems[i].charText.attr({'text': ''});
			this.drawnSystems[i].attr({'width': 70, 'height': 30});
			
		}
	}
	
	for (var i = this.drawnConnections.length; i--;) {
			this.r.connection(this.drawnConnections[i]);
	}
/*
	for(var i in activesData )
	{
			var a = activesData[i].split(',');
			var activesText = '';
			
			for(var j = 0; j < a.length; j++)
			{
				heightBonus += 11;
				activesText += a[j] + '\n';
			}
			
			if( activesText != '' )
			{		
					systemBlob.charText = this.r.text(systemBlob.attr("x")+4, systemBlob.attr("y")+27, activesText);
					systemBlob.charText.attr({'text-anchor': 'start'});
					systemBlob.charText.attr({'font-size': 10});
					systemBlob.charText.attr({y: (systemBlob.attr("y")+(systemBlob.charText.getBBox().height/2)+15) });
					
					if( systemBlob.charText.getBBox().width > 70-4 )
					{
						systemBlob.attr({'width': systemBlob.charText.getBBox().width+10});
					}
			}
	}
	*/
}

siggyMap.prototype.isMapOpen = function()
{
	return this.mapOpen;
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

siggyMap.prototype.openWHEditor = function(mode)
{
	$('#chainMapContainer').block({
		message: $('#wormholeEditor'),
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
		$('#whEditorAdd').show();
		$('#whEditorEdit').hide();
		this.editorMode = 'add';
		this.editorOpen = true;
	}
	
}

siggyMap.prototype.draw = function()
{
		var that = this;
		var dragger = function () {
				if( !that.editing )
				{
					return;
				}
				this.ox = this.type == "rect" ? this.attr("x") : this.attr("cx");
				this.oy = this.type == "rect" ? this.attr("y") : this.attr("cy");
				
				this.nameText.ox = this.nameText.attr("x");
				this.nameText.oy = this.nameText.attr("y");
				
				if (typeof(this.charText) !== undefined && typeof(this.charText) != "undefined" && this.charText !== null)
				{
					this.charText.ox = this.charText.attr("x");
					this.charText.oy = this.charText.attr("y");
				}
				
				this.animate({"fill-opacity": .2}, 500);
		};
		move = function (dx, dy) {
				if( !that.editing )
				{
					return;
				}
				var att = this.type == "rect" ? {x: this.ox + dx, y: this.oy + dy} : {cx: this.ox + dx, cy: this.oy + dy};
				this.attr(att);
				
				this.nameText.attr({x: this.nameText.ox + dx, y: this.nameText.oy + dy});
				
				if (typeof(this.charText) !== undefined && typeof(this.charText) != "undefined" && this.charText !== null)
				{
					this.charText.attr({x: this.charText.ox + dx, y: this.charText.oy + dy});
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
			
		//	this.systems[i].actives = '';
			if( this.systems[i].actives != '' && typeof(this.systems[i].actives) != 'undefined' )
			{
				var actives = this.systems[i].actives.split(',');
				for(var j = 0; j < actives.length; j++)
				{
					heightBonus += 11;
					activesText += actives[j] + '\n';
				}
			}
			
			var systemBlob = this.r.rect(x, y,  70, 30+heightBonus, 5);
			systemBlob.nameText = this.r.text(systemBlob.attr("x")+4, systemBlob.attr("y")+7, ( (this.systems[i].displayName != '') ? this.systems[i].displayName : this.systems[i].name) );
			systemBlob.nameText.attr({'font-size': 12, 'font-weight': 'bold'});
			systemBlob.nameText.attr({'text-anchor': 'start'});
			
			systemBlob.nameText.systemID = this.systems[i].systemID;
			systemBlob.nameText.click( function() {
				if( that.editing )
				{
					return;
				}
				CCPEVE.showInfo(5, this.systemID);
			} );
			
			systemBlob.drag(move, dragger, up);
			//var color = Raphael.getColor();
			var color = '#676767';
			if( parseInt(this.systems[i].activity) == 1 )
			{
				color = '#03B807';
			}
			else if( parseInt(this.systems[i].activity) == 2 )
			{
				color = '#FFE205';
			}
			else if( parseInt(this.systems[i].activity) == 3 )
			{
				color = '#DE4444';
			}
			
			systemBlob.attr({fill: '#fff', stroke: color, "fill-opacity": 1, "stroke-width": 3, cursor: "pointer"});
				
			systemBlob.charText = this.r.text(systemBlob.attr("x")+4, systemBlob.attr("y")+27, '');
			systemBlob.charText.attr({'text-anchor': 'start'});
			systemBlob.charText.attr({'font-size': 10});
			
			
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
		}
		
		for( var w in this.wormholes )
		{
			var connection = this.r.connection(this.drawnSystems[this.wormholes[w].from], this.drawnSystems[this.wormholes[w].to], this.getMassColor(parseInt(this.wormholes[w].mass)), this.getTimeColor(parseInt(this.wormholes[w].eol))+"|12");
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
			
			if( this.wormholes[w].eolToggled != 0 )
			{
				this.setUpWHToolTip( connection,  parseInt(this.wormholes[w].eolToggled) );
			}
			
			this.drawnConnections.push(connection);
		}
		
		that.updatePan();
}

siggyMap.prototype.setUpWHToolTip = function(connection, eolAt )
{
				var text = this.r.text(6, -8, 'EOL set at: '+ siggymain.displayTimeStamp(eolAt) ).attr("fill", "#fff");
				
				var box = this.r.rect(4, -16, text.getBBox().width+4, 16).attr({'fill': '#262626'});
				text.attr({'text-anchor': 'start'});

				var st = this.r.set();
				st.push( box, text )
				connection.line.tooltip(st);
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

