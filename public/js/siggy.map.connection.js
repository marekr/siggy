function mapconnection(plumb, options)
{
	this.jsPlumb = plumb;
	
	this.defaults = {
			to: '',
			from: '',
			hash: '',
			wormhole: {
				mass: 0,
				eol: false,
				eolDateSet: 0,
				frigateSized: false
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
	/*
	$(connection.canvas).contextMenu( { menu: 'wh-menu' },
		function(action, el, pos) {
			
	}, function(el) {
		console.log(el);
	});*/
}

mapconnection.prototype.destroy = function()
{
	//$(connection.canvas).destroyContextMenu();
}

mapconnection.prototype.setupOverlay = function(connectionOptions)
{
	if( this.settings.wormhole.eol != 0 )
	{
		this.label += 'EOL set at: '+ siggymain.displayTimeStamp(this.settings.wormhole.eolDateSet);
	}

	if( parseInt(this.settings.wormhole.frigate_sized) == 1 )
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


mapconnection.resetDrawSettings = function()
{
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