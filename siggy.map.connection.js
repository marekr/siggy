/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

siggy2.MapConnection = function(plumb, options)
{
	this.jsPlumb = plumb;
	
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
				totalTrackedMass: 0,
				typeInfo: {
					name: '',
					mass: 0,
					lifetime: 0,
					maxJumpMass: 0,
					regen: 0
				}
			},
			type: 'wormhole'
	};
	
	this.settings = $.extend({}, this.defaults, options);
	if( this.settings.type == 'wormhole' )
	{
		this.settings.wormhole = $.extend({}, this.defaults.wormhole, options.wormhole);
		this.settings.wormhole.typeInfo = $.extend({}, this.defaults.wormhole.typeInfo, options.wormhole.typeInfo);
	}
	
	this.map = null;
	
	this.label = '';
	
	this.connection = null;
	
	this.selected = false;
}

siggy2.MapConnection.prototype.refresh = function()
{
	if( !this.selected )
	{
		this.connection.setPaintStyle(this.getDefaultPaintStyle());
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

siggy2.MapConnection.prototype.getDefaultPaintStyle = function()
{
	if( this.settings.type == 'wormhole' )
	{
		return {
				   lineWidth:6,
				   strokeStyle: this.getMassColor(this.settings.wormhole.mass),
				   outlineColor: this.getTimeColor(this.settings.wormhole.eol,
													this.settings.wormhole.frigateSized),
				   outlineWidth:3,
				   dashstyle: this.getDashStyle(this.settings.wormhole.frigateSized)
				};
	}
	else if( this.settings.type == 'stargate' )
	{
		return {
				   lineWidth:6,
				   strokeStyle: '#fff',
				   outlineColor: 'transparent',
				   outlineWidth:1,
					dashstyle: "0.5 1"
				};
	}
	else if( this.settings.type == 'jumpbridge' )
	{
		return {
				   lineWidth:6,
				   strokeStyle: '#3fafaf',
				   outlineColor: 'transparent',
				   outlineWidth:1,
					dashstyle: "0.9 2"
				};
	}
	else if( this.settings.type == 'cyno' )
	{
		return {
				   lineWidth:6,
				   strokeStyle: '#F2B672',
				   outlineColor: 'transparent',
				   outlineWidth:1,
					dashstyle: "0.9 3"
				};
	}
}

siggy2.MapConnection.prototype.create = function()
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

						paintStyle: this.getDefaultPaintStyle(),
						endpointStyle:{ fillStyle:"#a7b04b" },
						parameters: { hash: this.settings.hash, deleteMe: false }
					};

	this.setupOverlay(connectionOptions);

	var connection = this.jsPlumb.connect(connectionOptions);
	
		
	
	connection.bind("click", function(conn)
	{
		if( $this.map.massSelect )
		{
			$this.selected = !$this.selected;
			
			$this.refresh();
		}
		else if( !$this.map.editing )
		{
			$this.map.editWormhole($this);
		}
		return false;
	});
	
	this.connection = connection;
	
	$(connection.canvas).data('siggy_connection', this);
	
	 $(connection.canvas).qtip({
		content: {
			text: this.label
		},
		show: {
			delay: 500
		},
		position: {
			target: 'mouse',
			adjust: { x: 5, y: 5 },
			viewport: $(window)
		}
	});
}

siggy2.MapConnection.prototype.contextMenuHandler = function(action)
{
	var $this = this;
	var saveData = {};
	
	switch( action )
	{
		case 'setstage1':
			saveData.mass = 0;
			break;
		case 'setstage2':
			saveData.mass = 1;
			break;
		case 'setstage3':
			saveData.mass = 2;
			break;
		case 'seteol':
			saveData.eol = 1;
			break;
		case 'cleareol':
			saveData.eol = 0;
			break;
		case 'setfrigate':
			saveData.frigate_sized = 1;
			break;
		case 'clearfrigate':
			saveData.frigate_sized = 0;
			break;
	}
	
	if( Object.size(saveData) > 0 )
	{
		saveData.hash = this.settings.hash;
		
		$.post($this.map.baseUrl + 'chainmap/connection_edit', saveData, function()
		{
			$this.map.siggymain.updateNow();
		});
	}
}


siggy2.MapConnection.prototype.contextMenuBuildItems = function()
{
	var items = {};
	
	if( this.settings.type != 'wormhole' )
	{
		return items;
	}
	
	switch( this.settings.wormhole.mass )
	{
		case 0:
			items.setstage2 = { name: "Set Stage 2" };
			items.setstage3 = { name: "Set Stage 3" };
			break;
		case 1:
			items.setstage1 = { name: "Set Stage 1" };
			items.setstage3 = { name: "Set Stage 3" };
			break;
		case 2:
			items.setstage1 = { name: "Set Stage 1" };
			items.setstage2 = { name: "Set Stage 2" };
			break;
	}
	
	if( this.settings.wormhole.eol )
	{
		items.cleareol = { name: "Clear EOL" };
	}
	else
	{
		items.seteol = { name: "Set EOL" };
	}
	
	if( this.settings.wormhole.frigateSized )
	{
		items.clearfrigate = { name: "Unmark as Frigate Hole" };
	}
	else
	{
		items.setfrigate = { name: "Mark as Frigate Hole" };
	}
	
	return items;
}

siggy2.MapConnection.prototype.destroy = function()
{
	$(this.connection.canvas).qtip('destroy');
	//remove data to avoid refs, normally jquery clears on remove()
	//but we dont get to use remove()
	$(this.connection.canvas).removeData();
	//remove any other events
	$(this.connection.canvas).off();
	
	this.connection = null;
	this.jsPlumb = null;
	this.map = null;
}

siggy2.MapConnection.prototype.setupOverlay = function(connectionOptions)
{
	if( this.settings.type == 'wormhole' )
	{
		if( this.settings.wormhole.typeInfo.name )
		{
			this.label += "<b>" + this.settings.wormhole.typeInfo.name + "</b><br />";
			this.label += number_format(this.settings.wormhole.typeInfo.mass*1000000000,0) + " kg +-10% mass<br />";
			this.label += this.settings.wormhole.typeInfo.lifetime + " hr lifetime<br />";
			this.label += number_format(this.settings.wormhole.typeInfo.maxJumpMass*1000000,0) + " kg max jump<br />";
		
			if( this.settings.wormhole.typeInfo.regen != 0 )
			{
				this.label += number_format(this.settings.wormhole.typeInfo.regen*1000000,0) + " kg mass regen<br />";
			}
		}
		
		if( this.settings.wormhole.totalTrackedMass )
		{
			this.label += "Approx." + number_format(this.settings.wormhole.totalTrackedMass,0) + " kg jumped<br />"
		}
		
		if( this.settings.wormhole.eol != 0 )
		{
			this.label += 'EOL set at: '+ siggy2.Helpers.displayTimeStamp(this.settings.wormhole.eolDateSet) + "<br />";
		}

		if( parseInt(this.settings.wormhole.frigateSized) == 1 )
		{
			this.label += 'Frigate sized wormhole';
		}
	}
	else if( this.settings.type == 'stargate' )
	{
		this.label = 'Stargate';
	}
	else if( this.settings.type == 'jumpbridge' )
	{
		this.label = 'Jumpbridge';
	}
	else if( this.settings.type == 'cyno' )
	{
		this.label = 'Cyno';
	}

	if( this.label != '' )
	{
		connectionOptions.overlays = [
										["Label", {
											cssClass:"map-connection-overlay",
											label : this.label,
											location:0.25,
											id:"label"
										}]
									];
	}
}

siggy2.MapConnection.prototype.getMassColor = function(mass)
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

siggy2.MapConnection.prototype.getTimeColor = function(eol,frig)
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

siggy2.MapConnection.prototype.getDashStyle = function(frig)
{
	return '0';
}