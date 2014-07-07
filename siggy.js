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

function array_unique(inputArr) {
  //  discuss at: http://phpjs.org/functions/array_unique/
  // original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
  //    input by: duncan
  //    input by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Nate
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // improved by: Michael Grier
  //        note: The second argument, sort_flags is not implemented;
  //        note: also should be sorted (asort?) first according to docs
  //   example 1: array_unique(['Kevin','Kevin','van','Zonneveld','Kevin']);
  //   returns 1: {0: 'Kevin', 2: 'van', 3: 'Zonneveld'}
  //   example 2: array_unique({'a': 'green', 0: 'red', 'b': 'green', 1: 'blue', 2: 'red'});
  //   returns 2: {a: 'green', 0: 'red', 1: 'blue'}

  var key = '',
    tmp_arr2 = {},
    val = '';

  var __array_search = function(needle, haystack) {
    var fkey = '';
    for (fkey in haystack) {
      if (haystack.hasOwnProperty(fkey)) {
        if ((haystack[fkey] + '') === (needle + '')) {
          return fkey;
        }
      }
    }
    return false;
  };

  for (key in inputArr) {
    if (inputArr.hasOwnProperty(key)) {
      val = inputArr[key];
      if (false === __array_search(val, tmp_arr2)) {
        tmp_arr2[key] = val;
      }
    }
  }

  return tmp_arr2;
}

function implode(glue, pieces) {
  //  discuss at: http://phpjs.org/functions/implode/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Waldo Malqui Silva
  // improved by: Itsacon (http://www.itsacon.net/)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //   example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
  //   returns 1: 'Kevin van Zonneveld'
  //   example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
  //   returns 2: 'Kevin van Zonneveld'

  var i = '',
    retVal = '',
    tGlue = '';
  if (arguments.length === 1) {
    pieces = glue;
    glue = '';
  }
  if (typeof pieces === 'object') {
    if (Object.prototype.toString.call(pieces) === '[object Array]') {
      return pieces.join(glue);
    }
    for (i in pieces) {
      retVal += tGlue + pieces[i];
      tGlue = glue;
    }
    return retVal;
  }
  return pieces;
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
	/*	$.browser.eveIGB = false;
		if( navigator.appVersion.indexOf('EVE-IGB') != -1 )
		{
				$.browser.eveIGB = true;
		}*/
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
	this.timeout = null;
}			

CountUp.prototype.destroy = function()
{
	clearTimeout(this.timeout);
	this.container = null;
	delete this.beginDate;
}


CountUp.prototype.addLeadingZero = function (value)
{
	return value < 10 ? ('0' + value) : value;
}

CountUp.prototype.calculate = function ()
{
	if( this.container == null )
	{
		return;
	}

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
		this.timeout = setTimeout(function ()
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
		10: "M609 (to C4)",
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
		13: "K329 (to Nullsec)",
		14: "N290 (to Lowsec)",
		15: "S047 (to Highsec)"
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
		10: "O128 (to C4)",
		11: "M555 (to C5)",
		12: "B041 (to C6)",
		13: "V283 (to Nullsec)",
		14: "R051 (to Lowsec)",
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
		7: "Z971 (to C1)",
		8: "R943  (to C2)",
		9: "X702 (to C3)",
		10: "O128 (to C4)",
		11: "N432 (to C5)",
		12: "B041 (to C6)",
		13: "S199  (to Nullsec)",
		14: "N944  (to Lowsec)",
		15: "B499  (to Highsec)"
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
		10: "O128  (to C4)",
		11: "N432 (to C5)",
		12: "B041 (to C6)",
		13: "S199 (to Nullsec)",
		14: "N944 (to Lowsec)",
		15: "B449 (to Highsec)"
	}
};

var blackHoleEffects = {
	1: [
		['Lock range', '-10%'],
		['Inertia', '+25%'],
		['Falloff', '-10%'],
		['Missile Velocity', '-10%'],
		['Ship Velocity', '+25%'],
		['Drone control Range', '-10%']
	],
	2: [
		['Lock range', '-19%'],
		['Inertia', '+44%'],
		['Falloff', '-19%'],
		['Missile Velocity', '-19%'],
		['Ship Velocity', '+44%'],
		['Drone control Range', '-19%']
	],
	3: [
		['Lock range', '-27%'],
		['Inertia', '+55%'],
		['Falloff', '-27%'],
		['Missile Velocity', '-27%'],
		['Ship Velocity', '+55%'],
		['Drone control Range', '-27%']
	],
	4: [
		['Lock range', '-34%'],
		['Inertia', '+68%'],
		['Falloff', '-34%'],
		['Missile Velocity', '-34%'],
		['Ship Velocity', '+68%'],
		['Drone control Range', '-34%']
	], 
	5: [
		['Lock range', '-41%'],
		['Inertia', '+85%'],
		['Falloff', '-41%'],
		['Missile Velocity', '-41%'],
		['Ship Velocity', '+85%'],
		['Drone control Range', '-41%']
	],
	6: [
		['Lock range', '-50%'],
		['Inertia', '+100%'],
		['Falloff', '-50%'],
		['Missile Velocity', '-50%'],
		['Ship Velocity', '+100%'],
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
	this.ajaxErrors = 0;


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
	this.publicMode = false;
	this.map = null;
	this.acsid = 0;
	this.acsname = '';
	
	//collasped sysInfo
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
		baseUrl: '',
		initialSystemID: 0,
		initialSystemName: '',
		showSigSizeCol: false,
		sessionID: '',
		map: {
		  jumpTrackerEnabled: true
		}
	};
	
	this.settings = $.extend(this.defaults, options);
    
	this.systemName = this.settings.initialSystemName;
    this.setSystemID(this.settings.initialSystemID);
	
	/* POSes */
	this.poses = {};
}

siggymain.prototype.getCurrentTime = function ()
{
	var date = new Date();
	var time = pad(date.getUTCHours(), 2) + ':' + pad(date.getUTCMinutes(), 2) + ':' + pad(date.getUTCSeconds(), 2);

	delete date;

	return time;
}

siggymain.displayTimeStamp = function (unixTimestamp)
{
	var date = new Date(unixTimestamp * 1000);
	var time = pad(date.getUTCDate(), 2) + '/' + pad(date.getUTCMonth() + 1, 2) + '/' + date.getUTCFullYear().toString().substr(2,2)  + ' ' + pad(date.getUTCHours(), 2) + ':' + pad(date.getUTCMinutes(), 2) + ':' + pad(date.getUTCSeconds(), 2);

	delete date;

	return time;
}

siggymain.prototype.update = function ()
{

	var request = {
		systemID: this.systemID,
		lastUpdate: this.lastUpdate,
		lastGlobalNotesUpdate: this.lastGlobalNotesUpdate,
		systemName: this.systemName,
		freezeSystem: this.freezeSystem,
		acsid: this.acsid,
		acsname: this.acsname,
		mapOpen: this.map.isMapOpen(),
		mapLastUpdate: this.map.lastUpdate,
		forceUpdate: this.forceUpdate
	};

	var that = this;
	$.ajax({
		url: this.settings.baseUrl + 'update',
		data: request,
		dataType: 'json',
        cache: false,
        async: true,
		method: 'post',
		beforeSend : function(xhr, opts){
			if(that.fatalError == true) //just an example
			{
				xhr.abort();
			}
		},
		success: function (data)
			{
                //try
                //{
                    if( data.redirect != undefined )
                    {
                        window.location = that.settings.baseUrl + data.redirect;
                        return;
                    }
			
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
                            
                            if( !that.globalNotesEle.is(':visible') && data.lastGlobalNotesUpdate > nlu && nlu != 0 )
                            {
                                that.blinkNotes();
                            }
                            
                            that.lastGlobalNotesUpdate = data.lastGlobalNotesUpdate;
                            
                            setCookie('notesUpdate', data.lastGlobalNotesUpdate, 365);
                            
                            that.globalNotes = data.globalNotes;
                            $('#global-notes-content').html(that.globalNotes.replace(/\n/g, '<br />'));
                            $('#global-notes-time').text( siggymain.displayTimeStamp(that.lastGlobalNotesUpdate) );
                        }
                    }
                    
                    if( that.map.isMapOpen()  )
                    {
                        if( parseInt(data.mapUpdate) == 1  )
                        {
                        
                            //use temp vars or else chrome chokes badly with async requests
                            var timestamp = data.chainMap.lastUpdate;
                            var systems = data.chainMap.systems;
                            var whs = data.chainMap.wormholes;
                            that.map.update(timestamp, systems, whs);
                        }
                        if( typeof(data.chainMap) != 'undefined' && typeof(data.chainMap.actives) != '' )
                        {
                            var actives =  data.chainMap.actives;
                            that.map.updateActives(data.chainMap.actives);
                        }
                    }

                    that.lastUpdate = data.lastUpdate;
                    //  $.unblockUI();
                    
                    delete data;
                //}
                //catch(err)
               // {
                //    console.log(err.message);
                //}
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
	for(var i in this.sigClocks)
	{
		this.sigClocks[i].destroy();
		delete clock;
	}
    
    $('td.moreinfo img').qtip('destroy');
    $('td.age span').qtip('destroy');
    
	$("#sig-table tbody").empty();
	this.editingSig = false;
	this.sigData = {};
	
	
	$('#sig-add-box select[name=type]').val(0);
	this.updateSiteSelect('#sig-add-box select[name=site]',this.systemClass, 0, 0);
	
    
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
		}
	}

	for (var i in sigData)
	{
		if (sigData[i].exists != true)
		{
			this.addSigRow(sigData[i],flashSigs);
			this.sigData[i] = sigData[i];
		}
	}
	
	if (!this.editingSig)
	{
		$('#sig-table').trigger('update');
	}
}

siggymain.prototype.updateSystemInfo = function (systemData)
{
    //general info
	$('#region').text(systemData.regionName + " / " + systemData.constellationName);
	$('#constellation').text(systemData.constellationName);
	$('#planetsmoons').text(systemData.planets + "/" + systemData.moons + "/" + systemData.belts);
	$('#truesec').text(systemData.truesec.substr(0,8));
	$('#radius').text(systemData.radius + ' AU');
	
    //HUB JUMPS
	var hubJumpsStr = '';
    $('div.hub-jump').destroyContextMenu();
    $('#hub-jumps').empty();
	for(var index in systemData.hubJumps)
	{
        var hub = systemData.hubJumps[index];
        
        var hubDiv = $("<div>").addClass('hub-jump').text(hub.destination_name + " (" + hub.num_jumps + " jumps)").data("sysID", hub.system_id).data("sysName", hub.destination_name);
        hubDiv.contextMenu( { menu: 'system-simple-context' },
            function(action, el, pos) {
                var sysID = $(el[0]).data("sysID");
                var sysName  = $(el[0]).data("sysName");
                if( action == "setdest" )
                {
                    if( typeof(CCPEVE) != "undefined" )
                    {
                        CCPEVE.setDestination(sysID);
                    }
                }
                else if( action == "showinfo" )
                {
                    if( typeof(CCPEVE) != "undefined" )
                    {
                            CCPEVE.showInfo(5, sysID );
                    }
                    else
                    {
                            window.open('http://evemaps.dotlan.net/system/'+sysName , '_blank');
                    }
                }
        });
        
        $('#hub-jumps').append(hubDiv);
	}
    
    //EFFECT STUFF
	//effect info
    $('#system-effect > p').qtip('destroy');
	$('#system-effect').empty();
	
	
	var effectTitle = $("<p>").text(systemData.effectTitle);
	var effect = $('#system-effect').append(effectTitle);
	var effectInfo = '';
	
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
		
		effect.append( $("<div>").attr('id', 'system-effects').addClass('tooltip').html(effectInfo) );
		
        effectTitle.qtip({
            content: {
                text: $("#system-effects") // Use the "div" element next to this for the content
            },
            position: {
                target: 'mouse',
                adjust: { x: 5, y: 5 },
                viewport: $(window)
            }
        });
	}
	
	//
	$('#static-info').empty();
	var staticCount = Object.size(systemData.staticData);
	if( staticCount > 0 )
	{
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

            
            var staticTooltip = $("<div>").attr('id', 'static-info-' + theStatic.staticID).addClass('tooltip').html( staticInfo );
			$('#static-info').append(staticBit).append( staticTooltip );

			
            staticBit.qtip({
                content: {
                    text: $('#static-info-' + theStatic.staticID) // Use the "div" element next to this for the content
                },
                position: {
                    target: 'mouse',
                    adjust: { x: 5, y: 5 },
                    viewport: $(window)
                }
            });
			
			counter++;
		}
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
	$('#system-name').text(sysName);
  
	
	$('a.site-dotlan').attr('href', 'http://evemaps.dotlan.net/system/'+systemData.name);
	$('a.site-wormholes').attr('href', 'http://wh.pasta.gg/'+systemData.name);
	$('a.site-evekill').attr('href','http://eve-kill.net/?a=system_detail&sys_name='+systemData.name);
	
	this.setSystemID(systemData.id);
	this.setSystemClass(systemData.sysClass);
	this.systemName = systemData.name;
	
	$('#currentsystem b').text(this.systemName);
	
	if( systemData.stats.length > 0 )
	{
		this.systemStats = systemData.stats;
        this.renderStats();
	}
	else
	{
		this.systemStats = [];
	}
	
	this.updatePOSList( systemData.poses );
	this.updateDScan( systemData.dscans );
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
		$('#bear-class-links a').each(function(index) 
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
		$('#bear-info-sets div').each(function(index) 
		{
			if( $(this).attr('id') == 'bear-class-'+bearClass )
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
	$('#system-options table th').text('System Options for '+systemData.name);
	$('#system-options input[name=label]').val(systemData.displayName);
	$('#system-options select[name=activity]').val(systemData.activity);
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
	if(this.sigClocks[sigData.sigID] != undefined )
	{
		this.sigClocks[sigData.sigID].destroy();
		delete this.sigClocks[sigData.sigID];
	}
    
    $('#sig-' + sigData.sigID + ' td.moreinfo img').qtip('destroy');
    $('#sig-' + sigData.sigID + ' td.age span').qtip('destroy');
    
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
	.append($('<td>').addClass('center-text').addClass('edit') .append($('<i>').addClass('icon-pencil').addClass('icon-large').click(function (e)
		{
			that.editSigForm(sigData.sigID);
		})
	))
	.append($('<td>').addClass('center-text').addClass('sig').text(sigData.sig));
	
	if( this.settings.showSigSizeCol )
	{
			row.append( $('<td>').addClass('center-text').addClass('size').text(sigData.sigSize) );
	}
	
	row.append($('<td>').addClass('center-text').addClass('type').text(this.convertType(sigData.type)))
	.append(descTD)
	.append($('<td>').addClass('center-text').addClass('moreinfo')
			.append($('<i>').addClass('icon-info-sign').addClass('icon-large').addClass('icon-yellow'))
			.append($("<div>").addClass('tooltip').attr('id', 'creation-info-' + sigData.sigID).html(creationInfo))
			)
	.append($('<td>').addClass('center-text').addClass('age').append($("<span>").text("--")).append($("<div>").addClass('tooltip').attr('id', 'age-timestamp-' + sigData.sigID).text(siggymain.displayTimeStamp(sigData.created))))
	.append($('<td>').addClass('center-text').addClass('remove').append($('<i>').addClass('icon-remove-sign').addClass('icon-large').addClass('icon-red')).click(function (e)
	{
		that.removeSig(sigData.sigID)
	}));
	
	$("#sig-table tbody").append( row );
	
	this.sigClocks[sigData.sigID] = new CountUp(sigData.created * 1000, '#sig-' + sigData.sigID + ' td.age span', "test");

    $('#sig-' + sigData.sigID + ' td.moreinfo i').qtip({
        content: {
            text: $('#creation-info-' + sigData.sigID) // Use the "div" element next to this for the content
        }
    });
    $('#sig-' + sigData.sigID + ' td.age span').qtip({
        content: {
            text: $('#age-timestamp-' + sigData.sigID) // Use the "div" element next to this for the content
        }
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
	controlEle.append($('<img>').attr('src', this.settings.baseUrl + 'public/images/accept.png').click(function (e)
	{
		that.editSig(sigID)
	}));

	var sigEle = $("#sig-" + sigID + " td.sig");
	sigEle.text('');

	var sigInput = $('<input>').val(this.sigData[sigID].sig).attr('maxlength', 3).keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } );
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
		ladar: 'Gas',
		radar: 'Data',
		mag: 'Relic',
		combat: 'Combat',
		grav: 'Ore'
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
	$.post(this.settings.baseUrl + 'dosigEdit', postData, function ( data )
	{
		that.editingSig = false;
		that.sigData[sigID].editing = false;

	},"json").fail( function(xhr, textStatus, errorThrown) { alert(xhr.responseText) } );

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
	controlEle.append($('<i>').addClass('icon-pencil').addClass('icon-large').click(function (e)
																						{
																							that.editSigForm(sigID)
																						})
																					);

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
	this.removeSigRow(
	{
		sigID: sigID
	});
	$('#sig-table').trigger('update');

	$.post(this.settings.baseUrl + 'dosigRemove', {
		systemID: this.systemID,
		sigID: sigID
	});
}

siggymain.prototype.convertType = function (type)
{
	//unknown null case, either way this should surpress it
	if (type == 'wh')
        return "WH";
	else if (type == 'grav')
        return "Ore";
	else if (type == 'ladar')
        return "Gas";
	else if (type == 'radar')
        return "Data";
	else if (type == 'mag')
        return "Relic";
	else if (type == 'combat')
        return "Combat";
    else
        return "";
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
	$('#sig-table tbody tr').each( function() {
		$( this ).removeClass('alt');
		if( i % 2 != 0 )
			$( this ).addClass('alt');
		i++;
	});
}

siggymain.prototype.setupAddBox = function ()
{
	var $this = this;
	var massAddBlob = $('#mass-add-sig-box textarea[name=blob]');
	massAddBlob.val('');
	$('#mass-add-sig-box button[name=add]').click( function() 
	{
		var postData = {
			systemID: $this.systemID,
			blob: massAddBlob.val()
		};
		
		$.post( $this.settings.baseUrl + 'domassSigs', postData, function (newSig)
		{
			for (var i in newSig)
			{
				$this.addSigRow(newSig[i]);
			}
			$.extend($this.sigData, newSig);
			$('#sig-table').trigger('update');

		}, 'json');
		
		massAddBlob.val('');
		
		
		$.unblockUI();
		return false;
	} );
	
	$('#mass-add-sig-box button[name=cancel]').click( function() 
	{
      $.unblockUI();
      return false;
	} );
	
	$('#mass-add-sigs').click(function ()
	{
		$.blockUI({ 
				message: $('#mass-add-sig-box'),
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
		return false;
	});



	//override potential form memory
	$('#sig-add-box select[name=type]').val('none');

	$('#sig-add-box form').submit(function ()
	{
		var sigEle = $('#sig-add-box input[name=sig]');
		var typeEle = $('#sig-add-box select[name=type]');
		var descEle = $('#sig-add-box input[name=desc]');
		var siteEle = $('#sig-add-box select[name=site]');
		
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
		
		var postData = {
			systemID: $this.systemID,
			sig: sigEle.val(),
			type: type,
			desc: descEle.val(),
			siteID: siteEle.val()
		};

		if( $this.settings.showSigSizeCol )
		{
				var sizeEle = $('#sig-add-box select[name=size]');
				postData.sigSize = sizeEle.val();
		}

		$.post($this.settings.baseUrl + 'dosigAdd', postData, function (newSig)
		{
			for (var i in newSig)
			{
				$this.addSigRow(newSig[i]);
			}
			$.extend($this.sigData, newSig);
			$('#sig-table').trigger('update');

		}, 'json');

		sigEle.val('');
		if( $this.settings.showSigSizeCol )
		{
				sizeEle.val('');
		}		
		typeEle.val('none');
		descEle.val('');
		siteEle.replaceWith($('<select>').attr('name', 'site'));
		
		sigEle.focus();
		
		return false;

	});
	

	$('#sig-add-box select[name=type]').change(function ()
	{
		newType = $(this).val();
		
		//$('#sigAddBox select[name=site]').replaceWith($this.generateSiteSelect($this.systemClass, newType, 0).attr('name', 'site'));
		$this.updateSiteSelect( '#sig-add-box select[name=site]', $this.systemClass, newType, 0);
		// $('#sigAddBox select[name=site]').empty();
	 // $('#sigAddBox select[name=site]').append($this.generateSiteSelect($this.systemClass, newType, 0).attr('name', 'site'));
		//$('#sigAddBox select[name=site]').focus();
	}).keypress(this.addBoxEnterHandler);	
	
	if( this.settings.showSigSizeCol )
	{
			$('#sig-add-box select[name=size]').keypress(this.addBoxEnterHandler);	
	}
	//$('#sigAddBox select[name=site]').live('keypress', this.addBoxEnterHandler);	
	$( document ).on('keypress', '#sig-add-box select[name=site]', this.addBoxEnterHandler); 
	
}

siggymain.prototype.addBoxEnterHandler = function(e)
{
			if(e.which == 13) {
					$('button[name=add]').focus().click();
			}
}

siggymain.prototype.displayFatalError = function(message)
{

		$('#fatal-error-message').html(message);

		$.blockUI({ 
				message: $('#fatal-error'),
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
	
	$(document).ajaxError( function(ev, jqxhr, settings, exception) {
		that.ajaxErrors += 1;
		if( that.ajaxErrors >= 5 )
		{
			that.displayFatalError('Communication error. ');
			that.fatalError = true;
		}
	} );
	$(document).ajaxSuccess( function() {
		that.ajaxErrors = 0;
	} );	
	
	
	$('#fatal-error-refresh').click( function() {
		location.reload(true);
	} );
}

siggymain.prototype.initialize = function ()
{
	var that = this;
	this.setupFatalErrorHandler();
	
      $(document).ajaxStart( function() {
        $(this).show();
      });
      
      $(document).ajaxStop( function() {
        $(this).hide();
      } );	


	if( getCookie('system_stats_open') != null )
	{
		this.statsOpened = parseInt( getCookie('system_stats_open') );
	}		

	this.map = new siggyMap(this.settings.map);
	this.map.baseUrl = this.settings.baseUrl;
	this.map.siggymain = this;
	this.map.initialize();
	
	this.initializeGNotes();
	
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

	$('#sig-table').tablesorter(
	{
		headers: tableSorterHeaders
	});
	
	$('#sig-table').bind('sortEnd', function() {
		that.colorizeSigRows();
	});
	

	this.setupAddBox();

	$('#system-options-save').click(function ()
	{
		var label = $('#system-options input[name=label]').val();
		var activity = $('#system-options select[name=activity]').val();
		
		that.saveSystemOptions(that.systemID, label, activity);
	});

	

	$('#system-options-reset').click(function ()
	{
		$('#system-options input[name=label]').val('');
		$('#system-options select[name=activity]').val(0);

		$.post(that.settings.baseUrl + 'dosaveSystemOptions', {
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
			}
		});
	});	
	
	
	$('#bear-C1').click(function () { that.setBearTab(1); return false; });
	$('#bear-C2').click(function () { that.setBearTab(2); return false; });
	$('#bear-C3').click(function () { that.setBearTab(3); return false; });
	$('#bear-C4').click(function () { that.setBearTab(4); return false; });
	$('#bear-C5').click(function () { that.setBearTab(5); return false; });
	$('#bear-C6').click(function () { that.setBearTab(6); return false; });

	
    
    $('#system-stats h2').click( function() {
		var content = $('#system-stats > div');
		if( content.is(":visible") )
		{
			content.hide();
			that.statsOpened = 0;
			setCookie('system_stats_open', 0, 365); 
		}
		else
		{
			content.show();
			that.renderStats();
			that.statsOpened = 1;
			setCookie('system_stats_open', 1 , 365);
		}
	});	
	
	
	this.initializeTabs();
	
	
	this.initializeDScan();
	this.initializePOSes();
	
	this.initializeExitFinder();
}

siggymain.prototype.saveSystemOptions = function(systemID, label, activity)
{
		var that = this;
		$.post(that.settings.baseUrl + 'dosaveSystemOptions', {
			systemID: systemID,
			label: label,
			activity: activity
		}, function (data)
		{
			if (that.systemList[systemID])
			{
				that.systemList[systemID].displayName = label;
				that.systemList[systemID].activity = activity;
			}
		});
}

siggymain.prototype.initializeTabs = function()
{
	var that = this;
    
    $('#system-advanced ul.tabs li a').click( function()
    {
        that.changeTab( $(this).attr('href') );
        return false;
    } );
    
    this.changeTab( '#sigs' );
	
}

siggymain.prototype.changeTab = function( selectedTab )
{
    var that = this;
    $.each( $('#system-advanced ul.tabs li a'), function()
    {
        var href = $(this).attr('href');
        
        if( href == selectedTab )
        {
            $( this ).parent().addClass('active');
            $( href ).show();
        }
        else
        {
            $( this ).parent().removeClass('active');
            $( href ).hide();
        }
        
        if( href == "#system-info" )
        {
			that.renderStats();
        }
        
		setCookie('system-tab', href, 365);
    } );

}


siggymain.prototype.initializeGNotes = function()
{
	var that = this;

    
	$('#settings-button').click(function ()
	{
        $.blockUI({ 
            message: $('#settings-dialog'),
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
    
    $('#settings-cancel').click( function() {
		$.unblockUI(); 
    });
    
    
	this.globalNotesEle = $('#global-notes');
	$('#global-notes-button').click(function ()
	{
		if ( that.globalNotesEle.is(":visible") )
		{
			that.globalNotesEle.hide();
			$('#global-notes-button').html('Notes &#x25BC;');
		}
		else
		{
			that.globalNotesEle.show();
			$('#global-notes-button').html('Notes &#x25B2;');
			that.stopBlinkingNotes();
		}
	});

	$('#global-notes-edit').click(function ()
	{
		$(this).hide();
		$('#global-notes-content').hide();
		$('#global-notes-edit-box').val(that.globalNotes).show();
		$('#global-notes-save').show();
		$('#global-notes-cancel').show();
	});

	$('#global-notes-save').click(function ()
	{
		that.globalNotes = $('#global-notes-edit-box').val();
		$.post(that.settings.baseUrl + 'doglobalNotesSave', {
			notes: that.globalNotes
		}, function (data)
		{
			that.editingGlobalNotes = false;
			that.lastGlobalNotesUpdate = data;
			setCookie('notesUpdate', that.lastGlobalNotesUpdate, 365);
			$('#global-notes-time').text(siggymain.displayTimeStamp(that.lastGlobalNotesUpdate));
		});

		$('#global-notes-content').html(that.globalNotes.replace(/\n/g, '<br />')).show();
		$('#global-notes-edit-box').hide();
		$('#global-notes-edit').show();
		$('#global-notes-cancel').hide();
		$(this).hide();
	});
	
	
	$('#global-notes-cancel').click(function ()
	{
		this.editingGlobalNotes = false;
		$('#thegnotes').show();
		$('#global-notes-edit-box').hide();
		$('#global-notes-edit').show();
		$('#global-notes-save').hide();
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


siggymain.prototype.freeze = function()
{
	this.freezeSystem = 1;
	$('#freezeOpt').hide();
	$('#unfreezeOpt').show();
}

siggymain.prototype.unfreeze = function()
{
	this.freezeSystem = 0;
	$('#unfreezeOpt').hide();
	$('#freezeOpt').show();
}

siggymain.prototype.populateExitData = function(data)
{

	if( typeof(data.result) != "undefined" )
	{
		for(var i in data.result)
		{
			var item = $("<li>");
			item.html("<span class='faux-link'>"+data.result[i].system_name + "</span> - " + data.result[i].number_jumps + " jumps");
			$('#exit-finder-list').append(item);
			
			item.data("sysID", data.result[i].system_id);
			item.data("sysName",data.result[i].system_name);
						
			item.contextMenu( { menu: 'system-simple-context', leftMouse: true },
				function(action, el, pos) {
					var sysID = $(el[0]).data("sysID");
					var sysName  = $(el[0]).data("sysName");
					if( action == "setdest" )
					{
						if( typeof(CCPEVE) != "undefined" )
						{
							CCPEVE.setDestination(sysID);
						}
					}
					else if( action == "showinfo" )
					{
						if( typeof(CCPEVE) != "undefined" )
						{
								CCPEVE.showInfo(5, sysID );
						}
						else
						{
								window.open('http://evemaps.dotlan.net/system/'+sysName , '_blank');
						}
					}
			});
		}
	}
	else
	{
		var item = $("<li>");
		item.text("Invalid system or no exits");
		$('#exit-finder-list').append(item);
	}
}


siggymain.prototype.initializeExitFinder = function()
{
	var $this = this;
	
	$("#exit-finder-button").click( function() {
		$this.openBox('#exit-finder');
		$("#exit-finder-results-wrap").hide();
		return false;
	} );
	
	$('#exit-finder button[name=current_location]').click( function() {
		$("#exit-finder-loading").show();
		$("#exit-finder-results-wrap").hide();
		$.post($this.settings.baseUrl + 'chainmap/findNearestExits', {current_system: 1}, function (data)
		{
			$("#exit-finder-loading").hide();
			$('#exit-finder-list').empty();
			$this.populateExitData(data);
			$("#exit-finder-results-wrap").show();
		});
		return false;
	});
	
	var submitHandler = function() {
		var target = $("#exit-finder input[name=target_system]").val();
		$("#exit-finder-loading").show();
		$("#exit-finder-results-wrap").hide();
		$.post($this.settings.baseUrl + 'chainmap/findNearestExits', {target: target}, function (data)
		{
			$("#exit-finder-loading").hide();
			$('#exit-finder-list').empty();
			$this.populateExitData(data);
			$("#exit-finder-results-wrap").show();
		});
		return false;
	};
	
	$('#exit-finder form').submit(submitHandler);
	
	$('#exit-finder button[name=cancel]').click( function() {
		$.unblockUI();
		return false;
	} );
}


siggymain.prototype.initializeDScan = function()
{
	var $this = this;
	
	
	$('#system-intel-add-dscan').click( function() {
		//$this.openPOSForm();
		$this.openBox('#dscan-form');
		$this.setupDScanForm('add');
		return false;
	} );
	
	
	$('#dscan-form button[name=cancel]').click( function() {
		$.unblockUI();
		return false;
	} );
}

siggymain.prototype.openBox = function(ele)
{
	var $this = this;
	
	$.blockUI({ 
		message: $(ele),
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
	
	return false;
}


siggymain.prototype.setupDScanForm = function(mode, posID)
{
	var $this = this;
	
	var title = $("#dscan-form input[name=dscan_title]");
	var scan = $("#dscan-form textarea[name=blob]");
	
	var data = {};
	var action = '';
	if( mode == 'edit' )
	{
		//data = this.poses[posID];
		action = $this.settings.baseUrl + 'dscan/edit';
	}
	else
	{
		data = {
					dscan_title: '',
					blob: ''
				};
		action = $this.settings.baseUrl + 'dscan/add';
	}
	
	title.val( data.dscan_title );
	scan.val( data.blob );
	
	$('#dscan-form button[name=submit]').off('click');
	$('#dscan-form button[name=submit]').click( function() {
		var data = {
			dscan_title: title.val(),
			blob: scan.val(),
			system_id: $this.systemID
		};
		
		if( data.dscan_title != "" && data.blob != "" )
		{
			$.post(action, data, function ()
			{
				$this.forceUpdate = true;
				$.unblockUI();
			});
		}
		
		return false;
	} );
}

siggymain.prototype.updateDScan = function( data )
{
	var $this = this;

	var body = $('#system-intel-dscans tbody');
	body.empty();
		
	if( typeof data != "undefined" && Object.size(data) > 0 )
	{
		for(var i in data)
		{
			var dscan_id = data[i].dscan_id;
			var row = $("<tr>").attr('id', 'dscan-'+dscan_id);
			
			row.append( $("<td>").text( data[i].dscan_title ) );
			row.append( $("<td>").text(siggymain.displayTimeStamp(data[i].dscan_date)) );
			row.append( $("<td>").text( data[i].dscan_added_by ) );
			
			(function(dscan_id){
				var view = $("<a>").addClass("btn btn-default btn-xs").text("View").attr("href",$this.settings.baseUrl + 'dscan/view/'+dscan_id).attr("target","_blank");
				var remove = $("<a>").addClass("btn btn-default btn-xs").text("Remove").click( function() {
						$this.removeDScan( dscan_id );
					}
				);
				row.append( $("<td>").append(view).append(remove) ).addClass('center-text');
			})(dscan_id);
			
			
			
			body.append(row);
		}
		
		$this.dscans = data;
	}
	else
	{
		$this.dscans = {};
	}
}

siggymain.prototype.removeDScan = function(dscanID)
{
	this.confirmDialog("Are you sure you want to delete the dscan entry?", function() {
		$.post(this.settings.baseUrl + 'dscan/remove', {dscan_id: dscanID}, function ()
		{
			$('#dscan-'+dscanID).remove();
		});
	});
}

siggymain.prototype.confirmDialog = function(message, yesCallback, noCallback)
{
	var $this = this;
	
	$this.openBox("#confirm-dialog");
	
	
	$("#confirm-dialog-message").text(message);
	$("#confirm-dialog-yes").unbind('click').click( function() {
		$.unblockUI();
		if (typeof(yesCallback) != "undefined" ) {
			yesCallback.call($this);
		}
	});
	$("#confirm-dialog-no").unbind('click').click( function() {
		$.unblockUI();
		if (noCallback && $.isFunction(noCallback)) {
			noCallback.call($this);
		}
	});
}


siggymain.prototype.initializePOSes = function()
{
	var $this = this;
	$('#system-intel-poses tbody').empty();
	
	$('#system-intel-add-pos').click( function() {
		$this.openBox('#pos-form');
		$this.addPOS();
		return false;
	} );
	
	$('#pos-form button[name=cancel]').click( function() {
		$.unblockUI();
		return false;
	} );
}

siggymain.prototype.getPOSStatus = function( online )
{
	online = parseInt(online);
	if( online )
	{
		return "Online";
	}
	else
	{
		return "Offline";
	}
}

siggymain.prototype.updatePOSList = function( data )
{
	var $this = this;

	var body = $('#system-intel-poses tbody');
	body.empty();
	
	var online = 0;
	var offline = 1;
	var summary = '';
	
	var owner_names = [];

	if( typeof data != "undefined" && Object.size(data) > 0 )
	{
		for(var i in data)
		{
			var pos_id = data[i].pos_id;
			var row = $("<tr>").attr('id', 'pos-'+pos_id);
			
			row.append( $("<td>").text( data[i].pos_location_planet + " - " + data[i].pos_location_moon ) );
			row.append( $("<td>").text( data[i].pos_owner ) );
			row.append( $("<td>").text( $this.getPOSStatus(data[i].pos_online) ) );
			row.append( $("<td>").text( data[i].pos_type_name ) );
			row.append( $("<td>").text( data[i].pos_size ) );
			row.append( $("<td>").text(siggymain.displayTimeStamp(data[i].pos_added_date)) );
			row.append( $("<td>").text( data[i].pos_notes ) );
			
			(function(pos_id){
				var edit = $("<a>").addClass("btn btn-default btn-xs").text("Edit").click( function() {
						$this.openBox('#pos-form');
						$this.editPOS( pos_id );
					}
				);
				var remove = $("<a>").addClass("btn btn-default btn-xs").text("Remove").click( function() {
						$this.removePOS( pos_id );
					}
				);
			
				row.append( $("<td>").append(edit).append(remove) ).addClass('center-text');
			})(pos_id);
			
			body.append(row);
			
			owner_names.push(data[i].pos_owner);
			if( parseInt(data[i].pos_online) == 1 )
			{
				online++;
			}
			else
			{
				offline++;
			}
		}
		
		
		owner_names = array_unique(owner_names);
		var owner_string = "<b>Residents:</b> "+implode(",",owner_names);
		
		summary = "<b>Total:</b> " + online + " online towers, " + offline + " offline towers" + "<br />" + owner_string;
		$this.poses = data;
	}
	else
	{
		$this.poses = {};
		summary = "No POS data added for this system";
	}
	
	$("#pos-summary").html( summary );
}

siggymain.prototype.addPOS = function()
{
	this.setupPOSForm('add');
	
}

siggymain.prototype.setupPOSForm = function(mode, posID)
{
	var $this = this;
	
	var planet = $("#pos-form input[name=pos_location_planet]");
	var moon = $("#pos-form input[name=pos_location_moon]");
	var owner = $("#pos-form input[name=pos_owner]");
	var type = $("#pos-form select[name=pos_type]");
	var size = $("#pos-form select[name=pos_size]");
	var status = $("#pos-form select[name=pos_status]");
	var notes = $("#pos-form textarea[name=pos_notes]");
	
	var data = {};
	var action = '';
	if( mode == 'edit' )
	{
		data = this.poses[posID];
		action = $this.settings.baseUrl + 'pos/edit';
	}
	else
	{
		data = {
					pos_location_planet: '',
					pos_location_moon: '',
					pos_owner: '',
					pos_type: 1,
					pos_size: 'small',
					pos_status: 0,
					pos_notes: '',
					pos_system_id: 0
				};
		action = $this.settings.baseUrl + 'pos/add';
	}
	
	planet.val( data.pos_location_planet );
	moon.val( data.pos_location_moon );
	owner.val( data.pos_owner );
	type.val( data.pos_type );
	size.val( data.pos_size );
	status.val( data.pos_online );
	notes.val( data.pos_notes );
	
	$("#pos-form button[name=submit]").off('click');
	$("#pos-form button[name=submit]").click( function() {

		var posData = {
			pos_location_planet: planet.val(),
			pos_location_moon: moon.val(),
			pos_owner: owner.val(),
			pos_type: type.val(),
			pos_size: size.val(),
			pos_online: status.val(),
			pos_notes: notes.val(),
			pos_system_id: $this.systemID
		};
		
		if(mode == 'edit')
		{
			posData.pos_id = posID;
		}

		if( posData.pos_location_moon != "" && posData.pos_location_planet != "" )
		{
			$.post(action, posData, function ()
			{
				$this.forceUpdate = true;
				$.unblockUI();
			});
		}

		return false;
	});
}

siggymain.prototype.editPOS = function(posID)
{
	this.setupPOSForm('edit', posID);
}

siggymain.prototype.removePOS = function(posID)
{
	this.confirmDialog("Are you sure you want to delete the POS?", function() {
		$.post(this.settings.baseUrl + 'pos/remove', {pos_id: posID}, function ()
		{
			$('#pos-'+posID).remove();
		});
	});
}