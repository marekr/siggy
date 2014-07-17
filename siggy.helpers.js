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

function ucfirst(str) {
  //  discuss at: http://phpjs.org/functions/ucfirst/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  //   example 1: ucfirst('kevin van zonneveld');
  //   returns 1: 'Kevin van zonneveld'

  str += '';
  var f = str.charAt(0)
    .toUpperCase();
  return f + str.substr(1);
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