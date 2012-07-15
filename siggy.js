Object.size = function (obj)
{
	var size = 0,
		key;
	for (key in obj)
	{
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};

function roundNumber(num, dec)
{
	var result = String(Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec));
	if (result.indexOf('.') < 0)
	{
		result += '.';
	}
	while (result.length - result.indexOf('.') <= dec)
	{
		result += '0';
	}
	return result;
}

/**
* @constructor
*/
jQuery.fn.flash = function( color, duration )
{
		if( typeof this.data('flashing') =='undefined' )
		{
			this.data('flashing', false);
		}
		
		if( this.data('flashing') == true )
		{
			return;
		}
    var current = this.css( 'background-color' );

	this.data('flashing', true)
    this.animate( { backgroundColor: color }, duration / 2 );
    
    var $this = this;
    this.animate( { backgroundColor: current }, duration / 2, function() { $this.data('flashing', false); } );
}

jQuery.fn.fadeOutFlash = function( color, duration )
{
		if( typeof this.data('flashing') =='undefined' )
		{
			this.data('flashing', false);
		}
		
		if( this.data('flashing') == true )
		{
			return;
		}
    var current = this.css( 'background-color' );

	this.data('flashing', true)
    this.css( 'background-color', color )
    
    var $this = this;
    this.animate( { backgroundColor: current }, duration, function() { $this.data('flashing', false); } );
}

new function() {
		$.browser.eveIGB = false;
		if( navigator.appVersion.indexOf('EVE-IGB') != -1 )
		{
				$.browser.eveIGB = true;
		}
}();


function setCookie(name,value,days) {
		if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
}
			
			

function CountUp(initDate,selector)
{
	this.beginDate = new Date(initDate);
	this.container = $(selector);
	this.calculate();
}			


CountUp.prototype.addLeadingZero = function (value)
{
	return value < 10 ? ('0' + value) : value;
}

CountUp.prototype.calculate = function ()
{
	var currDate = new Date();
	var prevDate = this.beginDate;
	
	dd = currDate - prevDate;
    this.days = Math.floor(dd / (60 * 60 * 1000 * 24) * 1);
    this.hours = Math.floor((dd % (60 * 60 * 1000 * 24)) / (60 * 60 * 1000) * 1);
    if( this.days < 2 )
    {
		this.minutes = Math.floor(((dd % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) / (60 * 1000) * 1);
		this.seconds = Math.floor((((dd % (60 * 60 * 1000 * 24)) % (60 * 60 * 1000)) % (60 * 1000)) / 1000 * 1);
		
		this.seconds = this.addLeadingZero(this.seconds);
		this.minutes = this.addLeadingZero(this.minutes);
	}
	this.hours = this.addLeadingZero(this.hours);
	
	if( this.days == 0 )
	{
		this.container.text(this.hours + ":" + this.minutes + ":" + this.seconds);
	}
	else
	{
		this.container.text(this.days + "d " + this.hours + "h");
	}
	
	if( this.days < 2 )
	{
		var self = this;
		setTimeout(function ()
		{
			self.calculate();
		}, 1000);
	}
	delete currDate;
	currDate = null;
}
			

function pad(number, length)
{
	var str = '' + number;
	while (str.length < length)
	{
		str = '0' + str;
	}

	return str;
}

//first key is wh class, second is just unique for mag in the class
var magsLookup = {
	1: {
		0: "",
		1: "Forgotten Perimeter Coronation Platform",
		2: "Forgotten Perimeter Power Array"
	},
	2: {
		0: "",
		1: "Forgotten Perimeter Gateway",
		2: "Forgoten Perimeter Habitation Coils"
	},
	3: {
		0: "",
		1: "Forgotten Frontier Quarantine Outpost",
		2: "Forgotten Frontier Recursive Depot"
	},
	4: {
		0: "",
		1: "Forgotten Frontier Conversion Module",
		2: "Forgotten Frontier Evacuation Center"
	},
	5: {
		0: "",
		1: "Forgotten Core Data Field",
		2: "Forgotten Core Information Pen"
	},
	6: {
		0: "",
		1: "Forgotten Core Assembly Hall",
		2: "Forgotten Circuitry Disassembler"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};

var radarsLookup = {
	1: {
		0: "",
		1: "Unsecured Perimeter Amplifier",
		2: "Unsecured Perimeter Information Center"
	},
	2: {
		0: "",
		1: "Unsecured Perimeter Comms Relay",
		2: "Unsecured Transponder Farm"
	},
	3: {
		0: "",
		1: "Unsecured Frontier Database",
		2: "Unsecured Frontier Receiver"
	},
	4: {
		0: "",
		1: "Unsecured Frontier Digital Nexus",
		2: "Unsecured Frontier Trinary Hub"
	},
	5: {
		0: "",
		1: "Unsecured Frontier Enclave Relay",
		2: "Unsecured Frontier Server Bank"
	},
	6: {
		0: "",
		1: "Unsecured Core Backup Array",
		2: "Unsecured Core Emergence"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};

var gravsLookup = {
	0: "",
	1: "Average Frontier Deposit",
	2: "Unexceptional Frontier Deposit",
	3: "Common Perimeter Deposit",
	4: "Exceptional Core Deposit",
	5: "Infrequent Core Deposit",
	6: "Unusual Core Deposit",
	7: "Rarified Core Deposit",
	8: "Ordinary Perimeter Deposit",
	9: "Uncommon Core Deposit",
	10: "Isolated Core Deposit"
};

var ladarsLookup = {
	0: "",
	1: "Barren Perimeter Reservoir",
	2: "Minor Perimeter Reservoir",
	3: "Ordinary Perimeter Reservoir",
	4: "Sizable Perimeter Reservoir",
	5: "Token Perimeter Reservoir",
	6: "Bountiful Frontier Reservoir",
	7: "Vast Frontier Reservoir",
	8: "Instrumental Core Reservoir",
	9: "Vital Core Reservoir"
};

var whLookup = {
	1: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "H121 (to C1)",
		8: "C125  (to C2)",
		9: "O883 (to C3)",
		10: "  (to C4)",
		11: "L614 (to C5)",
		12: "S804 (to C6)",
		13: "Z060 (to Nullsec)",
		14: "J244 (to Lowsec)",
		15: "N110 (to Highsec)"
	},
	2: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z647 (to C1)",
		8: "D382 (to C2)",
		9: "O477 (to C3)",
		10: "Y683 (to C4)",
		11: "N062 (to C5)",
		12: "R474 (to C6)",
		13: "E545 (to Nullsec)",
		14: "A239 (to Lowsec)",
		15: "B274 (to Highsec)"
	},
	3: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "V301 (to C1)",
		8: "I182 (to C2)",
		9: "N968 (to C3)",
		10: "T405 (to C4)",
		11: "N770 (to C5)",
		12: "A982 (to C6)",
		13: "K346 (to Nullsec)",
		14: "U210 (to Lowsec)",
		15: "D845 (to Highsec)"
	},
	4: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "P060 (to C1)",
		8: "N766 (to C2)",
		9: "C247 (to C3)",
		10: "X877 (to C4)",
		11: "H900 (to C5)",
		12: "U574 (to C6)",
		13: " (to Nullsec)",
		14: "  (to Lowsec)",
		15: "  (to Highsec)"
	},
	5: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Y790 (to C1)",
		8: "D364 (to C2)",
		9: "M267 (to C3)",
		10: "E175 (to C4)",
		11: "H296 (to C5)",
		12: "V753 (to C6)",
		13: "Z142 (to Nullsec)",
		14: "C140 (to Lowsec)",
		15: "D792 (to Highsec)"
	},
	6: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Q317  (to C1)",
		8: "G024 (to C2)",
		9: "L477 (to C3)",
		10: "Z457 (to C4)",
		11: "V911 (to C5)",
		12: "W237 (to C6)",
		13: "Z142 (to Nullsec)",
		14: "C140 (to Lowsec)",
		15: "D792  (to Highsec)"
	},
	7: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z971 (to C1)",
		8: "R943 (to C2)",
		9: "X702 (to C3)",
		10: " (to C4)",
		11: "M555 (to C5)",
		12: "  (to C6)",
		13: " (to Nullsec)",
		14: " (to Lowsec)",
		15: "A641 (to Highsec)"
	},
	8: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: " (to C1)",
		8: "  (to C2)",
		9: "X702 (to C3)",
		10: "  (to C4)",
		11: "N432 (to C5)",
		12: "  (to C6)",
		13: "  (to Nullsec)",
		14: "  (to Lowsec)",
		15: "  (to Highsec)"
	},
	9: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z971 (to C1)",
		8: "R943 (to C2)",
		9: "X702 (to C3)",
		10: "  (to C4)",
		11: "N432 (to C5)",
		12: "  (to C6)",
		13: "S199 (to Nullsec)",
		14: "N944 (to Lowsec)",
		15: "B449 (to Highsec)"
	}
};

var blackHoleEffects = {
	1: [
		['Inertia', '+25%'],
		['Targeting Range', '-10%'],
		['Falloff', '-10%'],
		['Missile Velocity', '-10%'],
		['Ship Velocity', '+25%'],
		['Drone control Range', '-10%']
	],
	2: [
		['Inertia', '+44%'],
		['Targeting Range', '-19%'],
		['Falloff', '-19%'],
		['Missile Velocity', '-19%'],
		['Ship Velocity', '+44%'],
		['Drone control Range', '-19%']
	],
	3: [
		['Inertia', '+55%'],
		['Targeting Range', '-27%'],
		['Falloff', '-27%'],
		['Missile Velocity', '-27%'],
		['Ship Velocity', '+55%'],
		['Drone control Range', '-27%']
	],
	4: [
		['Inertia', '+68%'],
		['Targeting Range', '-34%'],
		['Falloff', '-34%'],
		['Missile Velocity', '-34%'],
		['Ship Velocity', '+68%'],
		['Drone control Range', '-34%']
	], 
	5: [
		['Inertia', '+85%'],
		['Targeting Range', '-41%'],
		['Falloff', '-41%'],
		['Missile Velocity', '-41%'],
		['Ship Velocity', '+85%'],
		['Drone control Range', '-41%']
	],
	6: [
		['Inertia', '+100%'],
		['Targeting Range', '-50%'],
		['Falloff', '-50%'],
		['Missile Velocity', '-50%'],
		['Ship Velocity', '+66%'],
		['Drone control Range', '-50%']
	]
};

var wolfRayetEffects = {
	1: [
		['Armor Resists', '+10%'],
		['Shield Resists', '-10%'],
		['Small Weapon Damage', '+25%'],
		['Signature Size', '-10%']
	],
	2: [
		['Armor Resists', '+18%'],
		['Shield Resists', '-18%'],
		['Small Weapon Damage', '+44%'],
		['Signature Size', '-19%']
	],
	3: [
		['Armor Resists', '+22%'],
		['Shield Resists', '-22%'],
		['Small Weapon Damage', '+55%'],
		['Signature Size', '-27%']
	],
	4: [
		['Armor Resists', '+27%'],
		['Shield Resists', '-27%'],
		['Small Weapon Damage', '+68%'],
		['Signature Size', '-34%']
	], 
	5: [
		['Armor Resists', '+34%'],
		['Shield Resists', '-34%'],
		['Small Weapon Damage', '+85%'],
		['Signature Size', '-41%']
	],
	6: [
		['Armor Resists', '+50%'],
		['Shield Resists', '-50%'],
		['Small Weapon Damage', '+100%'],
		['Signature Size', '-50%']
	]
};

var redGiantEffects = {
	1: [
		['Heat Damage', '+10%'],
		['Overheat Bonus', '+25%'],
		['Smart Bomb Range', '+25%'],
		['Smart Bomb Damage', '+25%']
	],
	2: [
		['Heat Damage', '+18%'],
		['Overheat Bonus', '+44%'],
		['Smart Bomb Range', '+44%'],
		['Smart Bomb Damage', '+44%']
	],
	3: [
		['Heat Damage', '+22%'],
		['Overheat Bonus', '+55%'],
		['Smart Bomb Range', '+55%'],
		['Smart Bomb Damage', '+55%']
	],
	4: [
		['Heat Damage', '+27%'],
		['Overload Bonus', '+68%'],
		['Smart Bomb Range', '+68%'],
		['Smart Bomb Damage', '+68%']
	], 
	5: [
		['Heat Damage', '+34%'],
		['Overheat Bonus', '+85%'],
		['Smart Bomb Range', '+85%'],
		['Smart Bomb Damage', '+85%']
	],
	6: [
		['Heat Damage', '+50%'],
		['Overheat Bonus', '+100%'],
		['Smart Bomb Range', '+100%'],
		['Smart Bomb Damage', '+100%']
	]
};

var catacylsmicEffects = {
	1: [
		['Armor Repair', '-10%'],
		['Shield Boost', '-10%'],
		['Shield Transfer', '+25%'],
		['Remote Repair', '+25%'],
		['Capacitor capacity', '+25%'],
		['Capacitor recharge time', '+25%']
	],
	2: [
		['Armor Repair', '-19%'],
		['Shield Boost', '-19%'],
		['Shield Transfer', '+44%'],
		['Remote Repair', '+44%'],
		['Capacitor capacity', '+44%'],
		['Capacitor recharge time', '+44%']
	],
	3: [
		['Armor Repair', '-27%'],
		['Shield Boost', '-27%'],
		['Shield Transfer', '+55%'],
		['Remote Repair', '+55%'],
		['Capacitor capacity', '+55%'],
		['Capacitor recharge time', '+55%']
	],
	4: [
		['Armor Repair', '-34%'],
		['Shield Boost', '-34%'],
		['Shield Transfer', '+68%'],
		['Remote Repair', '+68%'],
		['Capacitor capacity', '+68%'],
		['Capacitor recharge time', '+68%']
	], 
	5: [
		['Armor Repair', '-41%'],
		['Shield Boost', '-41%'],
		['Shield Transfer', '+85%'],
		['Remote Repair', '+85%'],
		['Capacitor capacity', '+85%'],
		['Capacitor recharge time', '+85%']
	],
	6: [
		['Armor Repair', '-50%'],
		['Shield Boost', '-50%'],
		['Shield Transfer', '+100%'],
		['Remote Repair', '+100%'],
		['Capacitor capacity', '+100%'],
		['Capacitor recharge time', '+100%']
	]
};

var magnetarEffects = {
	1: [
		['Damage', '+25%'],
		['Missile Explosion Velocity', '-10%'],
		['Drone Velocity', '-10%'],
		['Targeting Range', '-10%'],
		['Targeting Speed', '-10%']
	], 
	2: [
		['Damage', '+44%'],
		['Missile Explosion Velocity', '-19%'],
		['Drone Velocity', '-19%'],
		['Targeting Range', '-19%'],
		['Targeting Speed', '-19%']
	], 
	3: [
		['Damage', '+55%'],
		['Missile Explosion Velocity', '-27%'],
		['Drone Velocity', '-27%'],
		['Targeting Range', '-27%'],
		['Targeting Speed', '-27%']
	],
  4: [
		['Damage', '+68%'],
		['Missile Explosion Velocity', '-34%'],
		['Drone Velocity', '-34%'],
		['Targeting Range', '-34%'],
		['Targeting Speed', '-34%']
  ], 
  5: [
		['Damage', '+85%'],
		['Missile Explosion Velocity', '-41%'],
		['Drone Velocity', '-41%'],
		['Targeting Range', '-41%'],
		['Targeting Speed', '-41%']
  ], 
  6 :[
		['Damage', '+100%'],
		['Missile Explosion Velocity', '-50%'],
		['Drone Velocity', '-50%'],
		['Targeting Range', '-50%'],
		['Targeting Speed', '-50%']
  ]
};

var pulsarEffects = {
	1: [
		['Shield HP', '+25%'],
		['Armor Resists', '-10%'],
		['Capacitor recharge time', '-10%'],
		['Targeting Range', '+25%'],
		['Signature Size', '+25%']
	], 
	2: [
		['Shield HP', '+44%'],
		['Armor Resists', '-18%'],
		['Capacitor recharge time', '-19%'],
		['Targeting Range', '+44%'],
		['Signature Size', '+44%']
	], 
	3: [
		['Shield HP', '+58%'],
		['Armor Resists', '-22%'],
		['Capacitor recharge time', '-27%'],
		['Targeting Range', '+58%'],
		['Signature Size', '+58%']
	], 
	4: [
		['Shield HP', '+68%'],
		['Armor Resists', '-27%'],
		['Capacitor recharge time', '-34%'],
		['Targeting Range', '+68%'],
		['Signature Size', '+68%']
	], 
	5: [
		['Shield HP', '+85%'],
		['Armor Resists', '-34%'],
		['Capacitor recharge time', '-41%'],
		['Targeting Range', '+85%'],
		['Signature Size', '+85%']
	], 
	6: [
		['Shield HP', '+100%'],
		['Armor Resists', '-50%'],
		['Capacitor recharge time', '-50%'],
		['Targeting Range', '+100%'],
		['Signature Size', '+100%']
	]
}

//when opened with system in url
if (typeof(CCPEVE) != "undefined")
{
	CCPEVE.requestTrust('http://siggy.borkedlabs.com/*');
}


jQuery.extend(
{
	scope: function (fn, scope)
	{
		return function ()
		{
			return fn.apply(scope, arguments);
		}
	}
});



/**
* @constructor
*/
function siggymain( options )
{
	this.fatalError = false;


	this.systemID = 0;
	this.systemClass = 9;
	this.systemName = '';
	this.systemStats = [];
	this.freezeSystem = 0;
	this.lastUpdate = 0;
	this.sigData = {};
	this.editingSig = false;
	this.sigClocks = {};
	this.systemList = {};
	this.forceUpdate = true;
	this._updateTimeout = null;
	this.baseUrl = '';
	this.publicMode = false;
	this.map = null;
	this.acsid = 0;
	this.acsname = '';
	
	//collasped sysInfo
	this.sysInfoCollasped  = 0;
	this.statsOpened = 0;
	
	//afk stuff
	this.idleMax = 10*60;
	this.idleTimeout = 0;
	this.afked = false;
	
	//gnotes
	this.globalNotesEle = null;
	this._blinkNotesInterval = null;
	this.lastGlobalNotesUpdate = 0;
	this.globalNotes = '';
	this.editingGlobalNotes = false;
	
	this.defaults = {
			showSigSizeCol: false,
			map: {}
	};
	
	this.settings = $.extend(this.defaults, options);
}

siggymain.prototype.getCurrentTime = function ()
{
	var date = new Date();
	var time = pad(date.getUTCHours(), 2) + ':' + pad(date.getUTCMinutes(), 2) + ':' + pad(date.getUTCSeconds(), 2);

	date = null;
	delete date;

	return time;
}

siggymain.displayTimeStamp = function (unixTimestamp)
{
	var date = new Date(unixTimestamp * 1000);
	var time = pad(date.getUTCDate(), 2) + '/' + pad(date.getUTCMonth() + 1, 2) + ' ' + pad(date.getUTCHours(), 2) + ':' + pad(date.getUTCMinutes(), 2) + ':' + pad(date.getUTCSeconds(), 2);

	date = null;
	delete date;

	return time;
}

siggymain.prototype.update = function ()
{
	if( this.idleTimeout >= this.idleMax )
	{
		if( !this.afked )
		{
			$.blockUI({message: "Please move your mouse to reactivate siggy."});
			this.afked = true;
		}
		
		var silentRequest = {
			acsid: this.acsid, 
			acsname: this.acsname 
		}
		var that = this;
		$.ajax({
			url: this.baseUrl + 'updateSilent',
			data: silentRequest,
			beforeSend : function(xhr, opts){
				if(that.fatalError == true) //just an example
				{
					xhr.abort();
				}
			},
			success: function (data)
			{
				if( parseInt( data.acsid ) != 0 )
				{
					that.acsid = data.acsid;
				}
				if( data.acsname != '' )
				{
					that.acsname = data.acsname;
				}
			},
			dataType: 'json'
			});
		
		this._updateTimeout = setTimeout(function (thisObj)
		{
			thisObj.update(0)
		}, 20000, this);
		return;
	}

	var request = {
		systemID: this.systemID,
		lastUpdate: this.lastUpdate,
		lastGlobalNotesUpdate: this.lastGlobalNotesUpdate,
		systemName: this.systemName,
		freezeSystem: this.freezeSystem,
		acsid: this.acsid,
		acsname: this.acsname,
		mapOpen: this.map.isMapOpen(),
		mapLastUpdate: this.map.lastUpdate
	};
	request['forceUpdate'] = this.forceUpdate;

	var that = this;
	$.ajax({
		url: this.baseUrl + 'update',
		data: request,
		dataType: 'json',
		beforeSend : function(xhr, opts){
			if(that.fatalError == true) //just an example
			{
				xhr.abort();
			}
		},
		success: function (data)
			{
				if( parseInt( data.acsid ) != 0 )
				{
					that.acsid = data.acsid;
				}
				if( data.acsname != '' )
				{
					that.acsname = data.acsname;
					$('#acsname b').text(that.acsname);
				}
				if (data.systemUpdate)
				{
					that.updateSystemInfo(data.systemData);
					that.updateSystemOptionsForm(data.systemData);
				}
				if (data.sigUpdate)
				{
					var flashSigs = ( data.systemUpdate ? false : true );
					that.updateSigs(data.sigData, flashSigs);
				}
				if (data.systemListUpdate)
				{
					that.systemList = data.systemList;
					that.updateSystemList(that.systemList);
				}
				if (data.globalNotesUpdate)
				{
					if (!that.editingGlobalNotes)
					{
						
						if( getCookie('notesUpdate') != null )
						{
							var nlu = parseInt(getCookie('notesUpdate'));
						}				
						else
						{
							var nlu = that.lastGlobalNotesUpdate;
						}
						
						//console.log('nlu:'+nlu);
						if( !that.globalNotesEle.is(':visible') && data.lastGlobalNotesUpdate > nlu && nlu != 0 )
						{
							that.blinkNotes();
						}
						
						that.lastGlobalNotesUpdate = data.lastGlobalNotesUpdate;
						
						setCookie('notesUpdate', data.lastGlobalNotesUpdate, 365);
						
						that.globalNotes = data.globalNotes;
						$('#thegnotes').html(that.globalNotes.replace(/\n/g, '<br />'));
						$('#gNotesTime').text( siggymain.displayTimeStamp(that.lastGlobalNotesUpdate) );
					}
				}
				
				if( that.map.isMapOpen()  )
				{
					if( data.mapUpdate )
					{
						that.map.update(data.chainMap.lastUpdate, data.chainMap.systems, data.chainMap.wormholes);
					}
					if( typeof(data.chainMap) != 'undefined' && typeof(data.chainMap.actives) != '' )
					{
						that.map.updateActives(data.chainMap.actives);
					}
				}

				that.lastUpdate = data.lastUpdate;
				//  $.unblockUI();
			}
		});
	


	this.forceUpdate = false;
	$('span.updateTime').text(this.getCurrentTime());
	this._updateTimeout = setTimeout(function (thisObj)
	{
		thisObj.update(0)
	}, 10000, this);
	
	return true;
}


siggymain.prototype.sortSystemListInUse = function (a, b)
{
	if (a[1] > b[1])
	{
		return -1;
	}
	else
	{
		return 1;
	}
}

siggymain.prototype.sortSystemListLastActive = function (a, b)
{
	if (a[2] > b[2])
	{
		return -1;
	}
	else
	{
		return 1;
	}
}

siggymain.prototype.sortSystemList = function (a, b)
{
	if (a[1] == b[1])
	{
		if (a[2] > b[2])
		{
			return -1;
		}
		else
		{
			return 1;
		}
	}
	//if 1 and 0
	else if (a[1] > b[1])
	{
		return -1;
	}
	else if (a[1] < b[1])
	{
		return 1;
	}
}

siggymain.prototype.updateSystemList = function (systemList)
{
	var sortable = [];
	for (var i in systemList)
	{
		sortable.push([i, systemList[i].inUse, systemList[i].lastActive]);
	}
	sortable.sort(this.sortSystemList);

	var listContainer = $('#systemList ul');
	listContainer.empty();
	for (var i in sortable)
	{
		var key = sortable[i][0];
		var sysClass = Number(systemList[key].sysClass);
		switch (sysClass)
		{
		case 1:
		case 2:
		case 3:
			var colorClass = 'classUnknown';
			break;
		case 4:
		case 5:
			var colorClass = 'classDangerous';
			break;
		case 6:
			var colorClass = 'classDeadly';
			break;
		case 7:
			var colorClass = 'classHigh';
			sysClass = 'H';
			break;
		case 8:
			var colorClass = 'classLow';
			sysClass = 'L';
			break;
		case 9:
			var colorClass = 'classNull';
			sysClass = 'N';
			break;
		default:
			var colorClass = 'classUnknown';
			break;
		}
		//  var that = this;
		var displayName = '';
		if (systemList[key].displayName != "")
		{
			displayName = systemList[key].displayName;
		}
		else
		{
			displayName = systemList[key].name;
		}

		var item = $('<li>').text(displayName).prepend($('<span>').addClass('sysClass').addClass(colorClass).text(sysClass));
		this.registerSwitchHandler(item, systemList[key].systemID, systemList[key].name);
		if (systemList[key].name == this.systemName)
		{
			item.addClass('sysSelected');
		}

		if (systemList[key].inUse == 1)
		{
			item.addClass('inUse');
		}
		else
		{
			item.addClass('notInUse');
		}
		listContainer.append(item);
	}
}

siggymain.prototype.registerSwitchHandler = function (item, systemID, systemName)
{
	var that = this;
	item.click(function ()
	{
		//$.blockUI({ message: '<h1 style="font-size:1.2em;"><strong>Loading...</strong></h1>' }); 
		that.switchSystem(systemID, systemName);

	});
}

siggymain.prototype.updateNow = function()
{
	clearTimeout(this._updateTimeout);
	return this.update();
	
}

siggymain.prototype.switchSystem = function(systemID, systemName)
{
	this.setSystemID(systemID);
	this.systemName = systemName;
	this.forceUpdate = true;
	this.freeze();
	clearTimeout(this._updateTimeout);
	$("#sigTable tbody").empty();
	this.editingSig = false;
	this.sigData = {};
	
	
	$('#sigAddBox select[name=type]').val(0);
  //$('#sigAddBox select[name=site]').replaceWith(this.generateSiteSelect(this.systemClass, 0, 0).attr('name', 'site'));
	this.updateSiteSelect('#sigAddBox select[name=site]',this.systemClass, 0, 0);
	
	if( this.updateNow() )
	{
			$(document).trigger('siggy.switchSystem', systemID );
	}
}

siggymain.prototype.updateSigs = function (sigData, flashSigs)
{
	for (var i in this.sigData)
	{
		if (typeof(sigData[i]) !== undefined && typeof(sigData[i]) != "undefined" && sigData[i] !== null)
		{
			sigData[i].exists = true;
			if (!this.sigData[i].editing)
			{
				this.sigData[i] = sigData[i];
				this.updateSigRow(this.sigData[i], flashSigs);
			}
		}
		else
		{
			if (this.sigData[i].editing)
			{
				continue;
			}
			this.removeSigRow(this.sigData[i]);
			delete this.sigData[i];
			delete this.sigClocks[i];
		}
	}

	for (var i in sigData)
	{
		if (sigData[i].exists != true)
		{
			// console.log(sigData);
			this.addSigRow(sigData[i],flashSigs);
			this.sigData[i] = sigData[i];
		}
	}
	//this.colorizeSigRows();
	$('#sigTable').trigger('update');
}

siggymain.prototype.updateSystemInfo = function (systemData)
{
	$('#region').text(systemData.regionName);
	$('#constellation').text(systemData.constellationName);
	$('#planetsmoons').text(systemData.planets + "/" + systemData.moons);
	$('#truesec').text(systemData.truesec.substr(0,8));
	$('#radius').text(systemData.radius + ' AU');
	$('#belts').text(systemData.belts);

	var collaspedInfoEffectStatic = $('#collaspedInfoEffectStatic');
	collaspedInfoEffectStatic.empty();
	$('#systemInfo-collasped p.spacer').hide();
	
	//effect info
	$('#systemEffect').empty();
	
	
	var effectTitle = $("<p>").text(systemData.effectTitle);
	var effect = $('#systemEffect').append(effectTitle);
	var effectInfo = '';
	
	var collaspedEffectTitle = $("<span>").text( (systemData.effectTitle == 'None' ? '': systemData.effectTitle) );
	
	if( systemData.effectTitle != 'None' )
	{
		effectInfo += '<b>Class '+systemData.sysClass+' Effects</b><br /><br />';
		
		if( systemData.effectTitle == 'Black Hole' )
		{	
			var effData = blackHoleEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Wolf-Rayet Star')
		{
			var effData = wolfRayetEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Red Giant')
		{
			var effData = redGiantEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Cataclysmic Variable')
		{
			var effData = catacylsmicEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Magnetar')
		{
			var effData = magnetarEffects[systemData.sysClass];
		}
		else if(systemData.effectTitle == 'Pulsar')
		{
			var effData = pulsarEffects[systemData.sysClass];
		}
		
		for( var i = 0; i < effData.length; i++ )
		{
			effectInfo += '<b>'+effData[i][0]+':&nbsp;</b>'+effData[i][1]+'<br />';
		}
		
		effect.append( $("<div>").attr('id', 'systemEffects').addClass('tooltip').html(effectInfo) );
		collaspedInfoEffectStatic.append( $("<div>").attr('id', 'systemEffectsCollasped').addClass('tooltip').html(effectInfo) );
		
		effectTitle.ezpz_tooltip(
		{
			contentId: 'systemEffects'
		});
		
		collaspedEffectTitle.ezpz_tooltip(
		{
			contentId: 'systemEffectsCollasped'
		});
		
		$('#systemInfo-collasped p.spacer').show();
		collaspedInfoEffectStatic.append( collaspedEffectTitle );
		collaspedInfoEffectStatic.append( ' - ' );
	}
	
	//
	$('#staticInfo').empty();
	var staticCount = Object.size(systemData.staticData);
	if( staticCount > 0 )
	{
		collaspedInfoEffectStatic.append('[');
		var counter = 0;
		for (var i in systemData.staticData)
		{
			var theStatic = systemData.staticData[i];
			var destBlurb = '';
			theStatic.staticDestClass = Number(theStatic.staticDestClass);

			if (theStatic.staticDestClass <= 6)
			{
				destBlurb = " (to C" + theStatic.staticDestClass + ")";
			}
			else if (theStatic.staticDestClass == 7)
			{
				destBlurb = " (to Highsec)";
			}
			else if (theStatic.staticDestClass == 8)
			{
				destBlurb = " (to Lowsec)";
			}
			else
			{
				destBlurb = " (to Nullsec)";
			}
			var staticBit = $("<p>").text(theStatic.staticName + destBlurb);
			var staticInfo = "<b>" + theStatic.staticName  + destBlurb + "</b><br />" + "Max Mass: " + theStatic.staticMass + " billion<br />" + "Max Jumpable Mass: " + theStatic.staticJumpMass + " million<br />" + "Max Lifetime: " + theStatic.staticLifetime + " hrs<br />" + "Signature Size: " + theStatic.staticSigSize + " <br />";

			$('#staticInfo').append(staticBit).append( $("<div>").attr('id', 'static-info-' + theStatic.staticID).addClass('tooltip').html( staticInfo ) );
			staticBit.ezpz_tooltip(
			{
				contentId: 'static-info-' + theStatic.staticID
			});
			
			if( staticCount > 1 && counter+1 != staticCount)
			{
				var collaspedStaticBit = $("<span>").text(theStatic.staticName+' ');
			}
			else
			{
				var collaspedStaticBit = $("<span>").text(theStatic.staticName);
			}
			collaspedInfoEffectStatic.append( collaspedStaticBit ).append( $("<div>").attr('id', 'static-info-collasped-' + theStatic.staticID).addClass('tooltip').html( staticInfo ) );
			
			collaspedStaticBit.ezpz_tooltip(
			{
				contentId: 'static-info-collasped-' + theStatic.staticID
			});
			
			
			counter++;
		}
		$('#systemInfo-collasped p.spacer').show();
		collaspedInfoEffectStatic.append(']');
	}
	
	var sysName = systemData.name;
	
	if ( systemData.displayName != '' )
	{
		sysName += " (" + systemData.displayName + ")";
	}
	
	systemData.sysClass = parseInt(systemData.sysClass);
	
	if ( systemData.sysClass <= 6 )
	{
		sysName += " - [C" + systemData.sysClass + "]";
	}
	else if( systemData.sysClass <= 8 )
	{
		sysName += " - ["+systemData.sec+"]";
	}
	else
	{
		sysName += " - [0.0]";
	}
	$('.systemName').text(sysName);
  
	
	$('a.dotlan').attr('href', 'http://evemaps.dotlan.net/system/'+systemData.name);
	
	$('a.evekill').attr('href','http://whkills.info/?a=system_detail&sys_id='+systemData.id);
	this.setSystemID(systemData.id);
	this.setSystemClass(systemData.sysClass);
	this.systemName = systemData.name;
	
	$('#currentsystem b').text(this.systemName);
	
	
	
	if( systemData.stats.length > 0 )
	{
		this.systemStats = systemData.stats;
		if ( this.statsOpened ) 
		{
			this.renderStats();
		}
	}
	else
	{
		this.systemStats = [];
	}
}

siggymain.prototype.renderStats = function()
{
		var options = {
			lines: { show: true },
			points: { show: false },
			xaxis: { mode: 'time',minTickSize: [1, 'hour'], ticks: 13, labelAngle: 45, color: '#fff' },
			yaxis: {color: '#fff', tickDecimals: 0}
		};
    
	
		var jumps = [];
		var sjumps = [];
		var kills = [];
		var npcKills = [];
		for( var i = 0; i < this.systemStats.length; i++ )
		{
			jumps.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][1]) ] );
			sjumps.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][4]) ] );
			kills.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][2]) ] );
			npcKills.push([parseInt(this.systemStats[i][0]), parseInt(this.systemStats[i][3]) ] );
		}

		
		$.plot( $('#jumps'),  [
        {
            data: jumps,
            lines: { show: true, fill: true }
        },
        {
            data: sjumps,
            lines: { show: true, fill: true }
         }], options);
        
		$.plot( $('#shipKills'),  [
        {
            data: kills,
            lines: { show: true, fill: true }
        }],options);
		$.plot( $('#npcKills'),  [
        {
            data: npcKills,
            lines: { show: true, fill: true }
        }],options);
}

siggymain.prototype.setBearTab = function( bearClass )
{
		$('#bearClassLinks a').each(function(index) 
		{
			if( $(this).text() == 'C'+bearClass )
			{
				$(this).addClass('active');
			}
			else
			{
				$(this).removeClass('active');
			}
		});	
		$('#bearInfoSets div').each(function(index) 
		{
			if( $(this).attr('id') == 'bearClass'+bearClass )
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
}



siggymain.prototype.updateSystemOptionsForm = function (systemData)
{
	$('#systemOptions table th').text('System Options for '+systemData.name);
	$('#systemOptions input[name=label]').val(systemData.displayName);
	$('#systemOptions input[name=inUse]').filter('[value=' + systemData.inUse + ']').attr('checked', true);
	$('#systemOptions select[name=activity]').val(systemData.activity);
}


siggymain.prototype.updateSigRow = function (sigData, flashSig)
{
	var creationInfo = '<b>Added by:</b> '+sigData.creator;
	if( sigData.lastUpdater != '' && typeof(sigData.lastUpdater) != "undefined" )
	{
		creationInfo += '<br /><b>Updated by:</b> '+sigData.lastUpdater;
		creationInfo += '<br /><b>Updated at:</b> '+siggymain.displayTimeStamp(sigData.updated);
	}
	
	$('#sig-' + sigData.sigID + ' td.sig').text(sigData.sig);
	$('#sig-' + sigData.sigID + ' td.type').text(this.convertType(sigData.type));
	
	if( this.settings.showSigSizeCol )
	{
			$('#sig-' + sigData.sigID + ' td.size').text(sigData.sigSize);
	}

	//stupidity part but ah well
	$('#sig-' + sigData.sigID + ' td.desc').text(this.convertSiteID(this.systemClass, sigData.type, sigData.siteID));
	$('#sig-' + sigData.sigID + ' td.desc p').remove();
	$('#sig-' + sigData.sigID + ' td.desc').append($('<p>').text(sigData.description));
	$('creation-info-' + sigData.sigID).html(creationInfo);
	
	
	//if( flashSig )
	///{
		//$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 10000);
	//}
}

siggymain.prototype.removeSigRow = function (sigData)
{
	$('#sig-' + sigData.sigID).remove();
	this.colorizeSigRows();
}

siggymain.prototype.addSigRow = function (sigData, flashSig)
{
	var that = this;
	
	var descTD = $('<td>').addClass('desc');
	
	descTD.text(this.convertSiteID(this.systemClass, sigData.type, sigData.siteID));
	descTD.append($('<p>').text(sigData.description));
	
	var creationInfo = '<b>Added by:</b> '+sigData.creator;
	if( sigData.lastUpdater != '' && typeof(sigData.lastUpdater) != "undefined" )
	{
		creationInfo += '<br /><b>Updated by:</b> '+sigData.lastUpdater;
		creationInfo += '<br /><b>Updated at:</b> '+siggymain.displayTimeStamp(sigData.updated);
	}
	
	var row = $('<tr>').attr('id', 'sig-' + sigData.sigID)
	.append($('<td>').addClass('center').addClass('edit') .append($('<img>').attr('src', this.baseUrl + 'public/images/pencil.png').click(function (e)
		{
			that.editSigForm(sigData.sigID)
		})
	))
	.append($('<td>').addClass('center').addClass('sig').text(sigData.sig));
	
	if( this.settings.showSigSizeCol )
	{
			row.append( $('<td>').addClass('center').addClass('size').text(sigData.sigSize) );
	}
	
	row.append($('<td>').addClass('center').addClass('type').text(this.convertType(sigData.type)))
	.append(descTD)
	.append($('<td>').addClass('center').addClass('moreinfo')
			.append($('<img>').attr('src', this.baseUrl + 'public/images/information.png'))
			.append($("<div>").addClass('tooltip').attr('id', 'creation-info-' + sigData.sigID).html(creationInfo))
			)
	.append($('<td>').addClass('center').addClass('age').append($("<span>").text("--")).append($("<div>").addClass('tooltip').attr('id', 'age-timestamp-' + sigData.sigID).text(siggymain.displayTimeStamp(sigData.created))))
	.append($('<td>').addClass('center').addClass('remove').append($('<img>').attr('src', this.baseUrl + 'public/images/delete.png')).click(function (e)
	{
		that.removeSig(sigData.sigID)
	}));
	
	$("#sigTable tbody").append( row );
	
	this.sigClocks[sigData.sigID] = new CountUp(sigData.created * 1000, '#sig-' + sigData.sigID + ' td.age span', "test");
	$('#sig-' + sigData.sigID + ' td.moreinfo img').ezpz_tooltip(
	{
		contentId: 'creation-info-' + sigData.sigID
	});
	$('#sig-' + sigData.sigID + ' td.age span').ezpz_tooltip(
	{
		contentId: 'age-timestamp-' + sigData.sigID
	});
	
	this.colorizeSigRows();
	
	if( flashSig )
	{
		$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 20000);
	}
}


siggymain.prototype.editSigForm = function (sigID)
{
	if (this.editingSig)
	{
		return;
	}

	this.sigData[sigID].editing = true;
	this.editingSig = true;

	var controlEle = $("#sig-" + sigID + " td.edit");
	controlEle.text('');

	var that = this;
	controlEle.append($('<img>').attr('src', this.baseUrl + 'public/images/accept.png').click(function (e)
	{
		that.editSig(sigID)
	}));

	var sigEle = $("#sig-" + sigID + " td.sig");
	sigEle.text('');

	var sigInput = $('<input>').val(this.sigData[sigID].sig).addClass('sigEdit').attr('maxlength', 3).keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } );
	sigEle.append(sigInput);
	
	if( this.settings.showSigSizeCol )
	{
			var sizeEle = $("#sig-" + sigID + " td.size");
			sizeEle.text('');
			
			sizeEle.append(this.generateOrderedSelect( 
			[ ['', '--'], ['1', '1'], ['2.2','2.2'], ['2.5','2.5'], ['4','4'], ['5', '5'], ['6.67','6.67'], ['10','10'] ]
			, this.sigData[sigID].sigSize));	
	}

	var typeEle = $("#sig-" + sigID + " td.type");
	typeEle.text('');

	typeEle.append(this.generateSelect(
	{
		none: '--',
		wh: 'WH',
		ladar: 'Ladar',
		radar: 'Radar',
		mag: 'Mag',
		grav: 'Grav'
	}, this.sigData[sigID].type).change(function ()
	{
		that.editTypeSelectChange(sigID)
	}));

	var descEle = $('#sig-' + sigID + ' td.desc');
	descEle.text('');
	descEle.append(this.generateSiteSelect(this.systemClass, this.sigData[sigID].type, this.sigData[sigID].siteID)).append($('<br />')).append( $('<input>').val(this.sigData[sigID].description).keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } ).css('width', '100%') );

	sigInput.focus();
}

siggymain.prototype.editTypeSelectChange = function (sigID)
{
	var newType = $("#sig-" + sigID + " td.type select").val();
	if (this.sigData[sigID].type != newType)
	{
		//$('#sig-' + sigID + ' td.desc select').replaceWith(this.generateSiteSelect(this.systemClass, newType, 0));
		this.updateSiteSelect( '#sig-' + sigID + ' td.desc select', this.systemClass, newType, 0 );
	}
}

siggymain.prototype.editSig = function (sigID)
{
	var sigEle = $("#sig-" + sigID + " td.sig input");
	if( this.settings.showSigSizeCol )
	{
			var sizeEle = $("#sig-" + sigID + " td.size select");
			console.log( sizeEle );
	}
	var typeEle = $("#sig-" + sigID + " td.type select");
	var descEle = $("#sig-" + sigID + " td.desc input");
	var siteEle = $("#sig-" + sigID + " td.desc select");

	if (sigEle.val().length != 3)
	{
		return false;
	}

	var sigUpdate = {};
	this.sigData[sigID].sig = sigEle.val().toUpperCase();
	this.sigData[sigID].type = typeEle.val();
	this.sigData[sigID].siteID = siteEle.val();
	this.sigData[sigID].description = descEle.val();
	
	var postData = {
		sigID: sigID,
		sig: this.sigData[sigID].sig,
		type: this.sigData[sigID].type,
		desc: this.sigData[sigID].description,
		siteID: this.sigData[sigID].siteID,
		systemID: this.systemID
	};
	
	if( this.settings.showSigSizeCol )
	{
			this.sigData[sigID].sigSize = sizeEle.val();
			postData.sigSize = this.sigData[sigID].sigSize;
	}
	
	

	
	var that = this;
	$.post(this.baseUrl + 'dosigEdit', postData, function ()
	{

		that.editingSig = false;
		that.sigData[sigID].editing = false;

	});

	sigEle.remove();
	if( this.settings.showSigSizeCol )
	{
			sizeEle.remove();
	}
	typeEle.remove();
	descEle.remove();
	siteEle.remove();

	this.updateSigRow(this.sigData[sigID]);

	var controlEle = $("#sig-" + sigID + " td.edit");
	controlEle.text('');
	controlEle.append($('<img>').attr('src', this.baseUrl + 'public/images/pencil.png').click(function (e)
	{
		that.editSigForm(sigID)
	}));

}

siggymain.prototype.updateSiteSelect = function( ele, whClass, type, siteID )
{
	var elem = $( ele );
	elem.empty();
	
	var options = [];
	switch( type )
	{
		case 'wh':
			options = whLookup[whClass];
			break;
		case 'ladar':
			options = ladarsLookup;
			break;
		case 'mag':
			options = magsLookup[whClass];
			break;
		case 'grav':
			options = gravsLookup;
			break;
		case 'radar':
			options = radarsLookup[whClass];
			break;
		default:
			options = { 0: '--'};
			break;
	}
	
	for (var i in options)
	{
		elem.append($('<option>').attr('value', i).text(options[i]));
	}
	
	elem.val(siteID);	
}

siggymain.prototype.generateSiteSelect = function (whClass, type, siteID)
{
	if (type == "wh") return this.generateSelect(whLookup[whClass], siteID);
	else if (type == "ladar") return this.generateSelect(ladarsLookup, siteID);
	else if (type == "mag") return this.generateSelect(magsLookup[whClass], siteID);
	else if (type == "grav") return this.generateSelect(gravsLookup, siteID);
	else if (type == "radar") return this.generateSelect(radarsLookup[whClass], siteID);
	else return this.generateSelect(
	{
		0: '--'
	}, 0);
}


siggymain.prototype.generateOrderedSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i=0; i < options.length; i++ )
	{
		newSelect.append($('<option>').attr('value', options[i][0]).text(options[i][1]));
	}

	newSelect.val(select);

	return newSelect;
}

siggymain.prototype.generateSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i in options)
	{
		newSelect.append($('<option>').attr('value', i).text(options[i]));
	}

	newSelect.val(select);

	return newSelect;
}

siggymain.prototype.removeSig = function (sigID)
{
	delete this.sigClocks[sigID];
	this.removeSigRow(
	{
		sigID: sigID
	});
	$('#sigTable').trigger('update');

	$.post(this.baseUrl + 'dosigRemove', {
		systemID: this.systemID,
		sigID: sigID
	});
}

siggymain.prototype.convertType = function (type)
{
	//unknown null case, either way this should surpress it
	if (type == 'none' || type == null || type == 'null' ) return "";
	else if (type == 'wh') return "WH";
	else return type.charAt(0).toUpperCase() + type.slice(1);
}

siggymain.prototype.convertSiteID = function (whClass, type, siteID)
{
	if (type == 'wh') return whLookup[whClass][siteID];
	else if (type == 'mag') return magsLookup[whClass][siteID];
	else if (type == 'radar') return radarsLookup[whClass][siteID];
	else if (type == 'ladar') return ladarsLookup[siteID];
	else if (type == 'grav') return gravsLookup[siteID];
	else return "";
}

siggymain.prototype.setSystemID = function (systemID)
{
	this.systemID = systemID;
}

siggymain.prototype.setSystemClass = function (systemClass)
{
	this.systemClass = systemClass;
	if( systemClass <= 6 )
	{
		this.setBearTab(systemClass);
	}
	else
	{
		this.setBearTab(1);
	}
}

siggymain.prototype.colorizeSigRows = function()
{
	var i = 0;
	$('#sigTable tbody tr').each( function() {
		$( this ).removeClass('alt');
		if( i % 2 != 0 )
			$( this ).addClass('alt');
		i++;
	});
}

siggymain.prototype.setupAddBox = function ()
{
	//override potential form memory
	$('#sigAddBox select[name=type]').val('none');

	var that = this;
	$('#sigAddBox form').submit(function ()
	{
		var sigEle = $('#sigAddBox input[name=sig]');
		var typeEle = $('#sigAddBox select[name=type]');
		var descEle = $('#sigAddBox input[name=desc]');
		var siteEle = $('#sigAddBox select[name=site]');
		
		if (sigEle.val().length != 3)
		{
			return false;
		}
		
		//idiot proof for ccp
		if( typeEle.val() == null )
		{
			var type = 'none';
		}
		else
		{
			var type = typeEle.val();
		}
		
		postData = {
			systemID: that.systemID,
			sig: sigEle.val(),
			type: type,
			desc: descEle.val(),
			siteID: siteEle.val()
		};

		if( that.settings.showSigSizeCol )
		{
				var sizeEle = $('#sigAddBox select[name=size]');
				postData.sigSize = sizeEle.val();
		}

		$.post(that.baseUrl + 'dosigAdd', postData, function (newSig)
		{
			for (var i in newSig)
			{
				that.addSigRow(newSig[i]);
			}
			$.extend(that.sigData, newSig);
			$('#sigTable').trigger('update');

		}, 'json');

		sigEle.val('');
		if( that.settings.showSigSizeCol )
		{
				sizeEle.val('');
		}		
		typeEle.val('none');
		descEle.val('');
		siteEle.replaceWith($('<select>').attr('name', 'site'));
		
		sigEle.focus();
		
		return false;

	});
	

	$('#sigAddBox select[name=type]').change(function ()
	{
		newType = $(this).val();
		
		//$('#sigAddBox select[name=site]').replaceWith(that.generateSiteSelect(that.systemClass, newType, 0).attr('name', 'site'));
		that.updateSiteSelect( '#sigAddBox select[name=site]', that.systemClass, newType, 0);
		// $('#sigAddBox select[name=site]').empty();
	 // $('#sigAddBox select[name=site]').append(that.generateSiteSelect(that.systemClass, newType, 0).attr('name', 'site'));
		//$('#sigAddBox select[name=site]').focus();
	}).keypress(this.addBoxEnterHandler);	
	
	if( this.settings.showSigSizeCol )
	{
			$('#sigAddBox select[name=size]').keypress(this.addBoxEnterHandler);	
	}
	//$('#sigAddBox select[name=site]').live('keypress', this.addBoxEnterHandler);	
	$( document ).on('keypress', '#sigAddBox select[name=site]', this.addBoxEnterHandler); 
	
}

siggymain.prototype.addBoxEnterHandler = function(e)
{
			if(e.which == 13) {
					$('input[name=add]').focus().click();
			}
}

siggymain.prototype.displayFatalError = function(message)
{

		$('#fatalErrorMessage').html(message);

		$.blockUI({ 
				message: $('#fatalError'),
				css: { 
				border: 'none', 
				padding: '15px', 
				background: 'transparent', 
				color: 'inherit',
				cursor: 'auto',
				textAlign: 'left',
				top: '20%',
				width: 'auto',
				centerX: true,
				centerY: true
        },
        overlayCSS: {
            cursor: 'auto'
        },
		fadeIn:  0, 
		fadeOut:  0
		}); 
}

siggymain.prototype.setupFatalErrorHandler = function()
{
	var that = this;
	
	$(document).ajaxError( function() {
		that.displayFatalError('Communication error. <br />Siggy may be down.');
		that.fatalError = true;
	} );
	
	$('#refreshFromFatal').click( function() {
		location.reload(true);
	} );
}

siggymain.prototype.initialize = function ()
{
	var that = this;
	this.setupFatalErrorHandler();
	


	sigCalc = new siggyCalc();
	sigCalc.baseUrl = this.baseUrl;
	sigCalc.initialize();

	if( getCookie('sysInfoCollasped') != null )
	{
		this.sysInfoCollasped = parseInt( getCookie('sysInfoCollasped') );
	}				
	
	if( getCookie('statsOpened') != null )
	{
		this.statsOpened = parseInt( getCookie('statsOpened') );
	}		

	this.map = new siggyMap(this.settings.map);
	this.map.baseUrl = this.baseUrl;
	this.map.siggymain = this;
	this.map.initialize();
	
	
	this.forceUpdate = true;
	this.update();
	$(document).trigger('siggy.switchSystem', this.systemID );
	
	if( this.settings.showSigSizeCol )
	{
			var tableSorterHeaders = {
			0: {
				sorter: false
			},
			5: {
				sorter: false
			},
			7: {
				sorter: false
			}
		};
	}
	else
	{			
			var tableSorterHeaders = {
			0: {
				sorter: false
			},
			4: {
				sorter: false
			},
			6: {
				sorter: false
			}
		};
	}

	$('#sigTable').tablesorter(
	{
		headers: tableSorterHeaders
	});
	
	$('#sigTable').bind('sortEnd', function() {
		that.colorizeSigRows();
	});
	

	this.setupAddBox();

	$('#unfreezeLink').click(function ()
	{
		that.unfreeze();
	});
	$('#freezeLink').click(function ()
	{
		that.freeze();
	});




	$("#systemInfoButton").click(function ()
	{
		that.handleSystemAdvancedMenuClick('info');
	});


	$("#systemOptionsButton").click(function ()
	{
		that.handleSystemAdvancedMenuClick('options');
	});

	$('#systemOptions button.save').click(function ()
	{
		var label = $('#systemOptions input[name=label]').val();
		var inUse = $('#systemOptions input[name=inUse]:checked').val();
		var activity = $('#systemOptions select[name=activity]').val();
		
		that.saveSystemOptions(that.systemID, label, inUse, activity);
	});

	

	$('#systemOptions button.reset').click(function ()
	{
		$('#systemOptions input[name=label]').val('');
		$('#systemOptions input[name=inUse]').filter('[value=0]').attr('checked', true);
		$('#systemOptions select[name=activity]').val(0);

		$.post(that.baseUrl + 'dosaveSystemOptions', {
			systemID: that.systemID,
			label: '',
			inUse: 0,
			activity: 0
		}, function (data)
		{
			if (that.systemList[that.systemID])
			{
				that.systemList[that.systemID].displayName = '';
				that.systemList[that.systemID].inUse = 0;
				that.systemList[that.systemID].activity = 0;
				that.updateSystemList(that.systemList);
			}
		});
	});	
	
	this.initializeGNotes();
	
	$('#bearC1').click(function () { that.setBearTab(1); return false; });
	$('#bearC2').click(function () { that.setBearTab(2); return false; });
	$('#bearC3').click(function () { that.setBearTab(3); return false; });
	$('#bearC4').click(function () { that.setBearTab(4); return false; });
	$('#bearC5').click(function () { that.setBearTab(5); return false; });
	$('#bearC6').click(function () { that.setBearTab(6); return false; });

	$('.carebear').click(function ()
	{
		$.blockUI({ 
				message: $('#carebearBox'),
				css: { 
            border: 'none', 
            padding: '15px', 
            background: 'transparent', 
            color: 'inherit',
            cursor: 'auto',
            textAlign: 'left',
            top: '20%',
						width: 'auto',
						centerX: true,
						centerY: false
        },
        overlayCSS: {
            cursor: 'auto'
        },
        fadeIn:  0, 
        fadeOut:  0
		}); 
		$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI); 
	});
	
	
	accessMenu = new siggyMenu(
	{	 
			ele: 'accessMenu', 
			dir: 'down',
			callback: function( id )
			{
				window.location.replace( that.baseUrl + 'doswitchMembership/?k=' + id );
			},
			callbackMode: 'wildcard'
	});
	
	accessMenu.initialize();
	
	//default to class 1
//	this.setBearTab(1);
	
	$(window).mousemove( function() {
		//console.log('hi');
		that.idleTimeout = 0;
		if( that.afked )
		{
			that.afked = false;
			$.unblockUI();
			that.updateNow();
		}
	} );
	
	setInterval( function() {
		if( that.idleTimeout >= that.idleMax || that.afked )
		{
			return;
		}
		that.idleTimeout += 1;
	//	console.log(that.idleTimeout);
	}, 1000 );	
	
	this.initializeSystemExpandCollaspe();
}

siggymain.prototype.saveSystemOptions = function(systemID, label, inUse, activity)
{
		var that = this;
		$.post(that.baseUrl + 'dosaveSystemOptions', {
			systemID: systemID,
			label: label,
			inUse: inUse,
			activity: activity
		}, function (data)
		{
			if (that.systemList[systemID])
			{
				that.systemList[systemID].displayName = label;
				that.systemList[systemID].inUse = inUse;
				that.systemList[systemID].activity = activity;
				that.updateSystemList(that.systemList);
			}
		});
}

siggymain.prototype.initializeSystemExpandCollaspe = function()
{
	var that = this;
	$('#systemInfo-collaspe').click( function() {
		$('#systemInfo-collasped').show();
		$('#systemInfo').hide();
		
		that.sysInfoCollasped = 1;
		setCookie('sysInfoCollasped', 1, 365); 
	});
	
	
	$('#systemInfo-expand').click( function() {
		$('#systemInfo-collasped').hide();
		$('#systemInfo').show();
		
		that.sysInfoCollasped = 0;
		setCookie('sysInfoCollasped', 0, 365); 
	});
	
	
	$('#stats h3').click( function() {
		var content = $('#stats > div');
		if( content.is(":visible") )
		{
			content.hide();
			that.statsOpened = 0;
			setCookie('statsOpened', 0, 365); 
		}
		else
		{
			content.show();
			that.renderStats();
			that.statsOpened = 1;
			setCookie('statsOpened', 1 , 365);
		}
	});	
}

siggymain.prototype.registerHeaderToolButton = function( button, callback, shownText, hiddenText  )
{
}

siggymain.prototype.initializeGNotes = function()
{
	var that = this;

	this.globalNotesEle = $('#globalNotes');
	$('#globalNotesButton').click(function ()
	{
		if ( that.globalNotesEle.is(":visible") )
		{
			that.globalNotesEle.hide();
			$('#globalNotesButton').html('Notes &#x25BC;');
		}
		else
		{
			that.globalNotesEle.show();
			$('#globalNotesButton').html('Notes &#x25B2;');
			that.stopBlinkingNotes();
		}
	});

	$('#gNotesEdit').click(function ()
	{
		$(this).hide();
		$('#thegnotes').hide();
		$('#gNotesEditBox').val(that.globalNotes).show();
		$('#gNotesSave').show();
		$('#gNotesCancel').show();
	});

	$('#gNotesSave').click(function ()
	{
		that.globalNotes = $('#gNotesEditBox').val();
		$.post(that.baseUrl + 'doglobalNotesSave', {
			notes: that.globalNotes
		}, function (data)
		{
			that.editingGlobalNotes = false;
			that.lastGlobalNotesUpdate = data;
			setCookie('notesUpdate', that.lastGlobalNotesUpdate, 365);
			$('#gNotesTime').text(siggymain.displayTimeStamp(that.lastGlobalNotesUpdate));
		});

		$('#thegnotes').html(that.globalNotes.replace(/\n/g, '<br />')).show();
		$('#gNotesEditBox').hide();
		$('#gNotesEdit').show();
		$('#gNotesCancel').hide();
		$(this).hide();
	});
	
	
	$('#gNotesCancel').click(function ()
	{
		this.editingGlobalNotes = false;
		$('#thegnotes').show();
		$('#gNotesEditBox').hide();
		$('#gNotesEdit').show();
		$('#gateNotesSave').hide();
		$(this).hide();
	});
}

siggymain.prototype.blinkNotes = function()
{
	if( this._blinkNotesInterval != null )
	{
		return;
	}
	
	$('#globalNotesButton').flash("#A46D00", 3000);
	this._blinkNotesInterval = setInterval( function() {
			//console.log('flash!');
			$('#globalNotesButton').flash("#A46D00", 3000);
	}, 4000 );		
}

siggymain.prototype.stopBlinkingNotes = function()
{
	if( this._blinkNotesInterval != null )
	{
		clearInterval(this._blinkNotesInterval);
		this._blinkNotesInterval = null;
	}
}

siggymain.prototype.handleSystemAdvancedMenuClick = function (what)
{
	var info = $('#systemInfoButton');
	var options = $('#systemOptionsButton');

	if (what == 'options')
	{
		info.removeClass('selected');
		options.addClass('selected');

		$('#systemInfo').hide();
		$('#systemOptions').show();
	}
	else
	{
		options.removeClass('selected');
		info.addClass('selected');
		
		if( this.sysInfoCollasped == 0 )
		{
			$('#systemInfo').show();
		}
		
		$('#systemOptions').hide();
	}
}

siggymain.prototype.freeze = function ()
{
	this.freezeSystem = 1;
	$('#freezeOpt').hide();
	$('#unfreezeOpt').show();
}

siggymain.prototype.unfreeze = function ()
{
	this.freezeSystem = 0;
	$('#unfreezeOpt').hide();
	$('#freezeOpt').show();
}


/**
* @constructor
*/
function siggyMenu( options )
{
	var defaults = {
		ele: '',
		dir: 'up',
		callbackMode: 'specific',
		optionCallbacks: null,
		callback: null
	};	
	
	this.settings = $.extend({}, defaults, options);
	
	this.eleobj = $( '#' + this.settings.ele );
	this.titletext = this.eleobj.find('span')
	this.menu = this.eleobj.find('ul.menu');
	this.menuItems = this.menu.find('li');
	
}

siggyMenu.prototype.initialize = function()
{
		this.eleobj.data('opened', false);
		this.eleobj.data('disabled', false);
		
		var that = this;
		this.eleobj.click(function(event) { //When trigger is clicked...  
		
				if( $(this).data('opened') || $(this).data('disabled') )
				{
					return false;
				}
				
				$(this).addClass('opened');
				
				
				//Following events are applied to the subnav itself (moving subnav up and down)  
				that.menu.show(); //Drop down the subnav on click  
				
				if( that.settings.dir == 'up' )
				{
					var offset = -1*( that.menu.height() )-1;
					that.menu.css('top', offset+'px');
				}
				else
				{
					var offset = that.eleobj.height()+1;
					that.menu.css('top', offset+'px' );
				}
				
				$('body').one('click',function() {
						that.menu.hide();
						that.menu.parent().removeClass('opened');
						that.menu.parent().opened  = false;
				} );
				
				$(this).opened = true;
				event.stopPropagation();
		});  


		if( this.settings.callbackMode == 'specific' )
		{
				for( var i in this.settings.optionCallbacks )
				{
						if( typeof this.settings.optionCallbacks[i] == 'function' )
						{
								$('#'+i).click( function( event )
										{
												that.menu.hide();
												that.eleobj.opened = false;
												that.eleobj.removeClass('opened');
												
												that.settings.optionCallbacks[ $(this).attr('id') ].call(this);
												
												event.stopPropagation();
										}
								);
						}
				}		
		}
		else
		{
				this.menuItems.click( function( event ) {
						that.menu.hide();
						that.eleobj.opened = false;
						that.eleobj.removeClass('opened');
												
						that.settings.callback.call(this, $(this).attr('id') );
						
						event.stopPropagation();
				} );
		}
}