function mapconnection(plumb, options)
{
	this.jsPlumb = plumb;
	
	this.whMenuID = '#wh-menu';
	
	this.defaults = {
			to: '',
			from: '',
			hash: '',
			creator: '',
			creatorCharID: 0,
			wormhole: {
				mass: 0,
				eol: false,
				eolDateSet: 0,
				frigateSized: false,
				staticInfo: {
					name: '',
					mass: 0,
					lifetime: 0,
					maxJumpMass: 0
				}
			},
			type: 'wh'
	};

	this.settings = $.extend(this.defaults, options);
	
	this.map = null;
	
	this.label = '';
	
	this.connection = null;
	
	this.selected = false;
}

mapconnection.prototype.refresh = function()
{
	if( !this.selected )
	{
		this.connection.setPaintStyle( {
			   lineWidth:6,
			   strokeStyle: this.getMassColor(this.settings.wormhole.mass),
			   outlineColor: this.getTimeColor(this.settings.wormhole.eol,
												this.settings.wormhole.frigateSized),
			   outlineWidth:3,
			   dashstyle: this.getDashStyle(this.settings.wormhole.frigateSized)
		});
	}
	else
	{
		this.connection.setPaintStyle( {
			   lineWidth:6,
			   strokeStyle: "#006AFE",
			   outlineColor: "#006AFE",
			   outlineWidth:3,
			   dashstyle: 0
		});
	}
}

mapconnection.prototype.create = function()
{
	var $this = this;
	
	var connectionOptions = { source: this.settings.from,
						target: this.settings.to,
						anchor:"Continuous",
						endpointsOnTop:false,
						endpoint:"Blank",
						detachable:false,
						connector:["StateMachine", { curviness:0.001 }],
						connectorTooltip: "aSDASDA",
						tooltip: "aSDASDA",
						anchor:[ "Perimeter", { shape:"Ellipse" } ],

						paintStyle:{
						   lineWidth:6,
						   strokeStyle: this.getMassColor(this.settings.wormhole.mass),
						   outlineColor: this.getTimeColor(this.settings.wormhole.eol,
															this.settings.wormhole.frigateSized),
						   outlineWidth:3,
						   dashstyle: this.getDashStyle(this.settings.wormhole.frigateSized)
						},
						endpointStyle:{ fillStyle:"#a7b04b" },
						parameters: { hash: this.settings.hash, deleteMe: false }
					};

	this.setupOverlay(connectionOptions);

	var connection = this.jsPlumb.connect(connectionOptions);
	
    var _listeners = function(e) {
        e.bind("mouseenter", function(c) {
            if( $this.map.editing || $this.map.massSelect )
            {
                return false;
            }
            c.showOverlay("label");
        });
        e.bind("mouseexit", function(c) {
            c.hideOverlay("label");
        });
    };
	
	if( this.label != '' )
	{
		_listeners(connection);
	}
	
	connection.bind("click", function(conn)
	{
		if( $this.map.massSelect )
		{
			$this.selected = !$this.selected;
			
			$this.refresh();
		}
		else
		{
			$this.map.editWormhole($this.settings.hash);
		}
		return false;
	});
	
	this.connection = connection;

	$(connection.canvas).contextMenu( { menu: 'wh-menu' },
		function(action, el, pos) {
			$this.whContextMenuHandler(action);
			
	}, function(el) {
		$this.contextMenuOpenHandler(el);
	});
}

mapconnection.prototype.whContextMenuHandler = function(action)
{
	var $this = this;
	var saveData = {};
	
	switch( action )
	{
		case 'set-stage-1':
			saveData.mass = 0;
			break;
		case 'set-stage-2':
			saveData.mass = 1;
			break;
		case 'set-stage-3':
			saveData.mass = 2;
			break;
		case 'set-eol':
			saveData.eol = 1;
			break;
		case 'clear-eol':
			saveData.eol = 0;
			break;
		case 'set-frigate':
			saveData.frigate_sized = 1;
			break;
		case 'unmark-frigate':
			saveData.frigate_sized = 0;
			break;
	}
	
	if( Object.size(saveData) > 0 )
	{
		saveData.mode = 'edit';
		saveData.hash = this.settings.hash;
		

		$.post($this.map.baseUrl + 'chainmap/wh_save', saveData, function()
		{
			$this.map.siggymain.updateNow();
		});
	}
}


mapconnection.prototype.contextMenuOpenHandler = function(el)
{
	var stage1 = $(this.whMenuID).find('li.set-stage-1');
	var stage2 = $(this.whMenuID).find('li.set-stage-2');
	var stage3 = $(this.whMenuID).find('li.set-stage-3');
	var setEOL = $(this.whMenuID).find('li.set-eol');
	var clearEOL = $(this.whMenuID).find('li.clear-eol');
	var setFrigate = $(this.whMenuID).find('li.set-frigate');
	var unmarkFrigate = $(this.whMenuID).find('li.unmark-frigate');
	
	switch( this.settings.wormhole.mass )
	{
		case 0:
			stage1.hide();
			stage2.show();
			stage3.show();
			break;
		case 1:
			stage1.show();
			stage2.hide();
			stage3.show();
			break;
		case 2:
			stage1.show();
			stage2.show();
			stage3.hide();
			break;
	}
	
	if( this.settings.wormhole.eol )
	{
		setEOL.hide();
		clearEOL.show();
	}
	else
	{
		setEOL.show();
		clearEOL.hide();
	}
	
	if( this.settings.wormhole.frigateSized )
	{
		setFrigate.hide();
		unmarkFrigate.show();
	}
	else
	{
		setFrigate.show();
		unmarkFrigate.hide();
	}
}

mapconnection.prototype.destroy = function()
{
	$(connection.canvas).destroyContextMenu();
}

mapconnection.prototype.setupOverlay = function(connectionOptions)
{
	if( this.settings.wormhole.eol != 0 )
	{
		this.label += 'EOL set at: '+ siggymain.displayTimeStamp(this.settings.wormhole.eolDateSet);
	}

	if( parseInt(this.settings.wormhole.frigateSized) == 1 )
	{
		if( this.label != '' )
		{
			this.label += '<br />';
		}
		this.label += 'Frigate sized wormhole';
	}

	if( this.label != '' )
	{
		connectionOptions.overlays = [
										["Label", {
											cssClass:"map-eol-overlay",
											label : this.label,
											location:0.5,
											id:"label"
										}]
									];
	}
}

mapconnection.prototype.getMassColor = function(mass)
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

mapconnection.prototype.getTimeColor = function(eol,frig)
{
	frig = parseInt(frig);
	eol = parseInt(eol);

	var outer = '#3d3d3d';
	if(frig == 1)
	{
		if( eol == 1 )
		{
			outer = '#00F5FF';
		}
		else
		{
			outer = '#FFFFFF';
		}
	}
	else if( eol == 1 )
	{
		outer = '#FF17FE';
	}
	return outer;
}

mapconnection.prototype.getDashStyle = function(frig)
{
	return '0';
}