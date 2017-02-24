/**
 * Renders a ISO8601 date + time from a unix timestamp. Except instead of the T
 * in between we have a space.
 */
siggy2.Helpers = siggy2.Helpers || {};

siggy2.Helpers.displayTimeStamp = function(unixTimestamp)
{
	// siggy2.Helpers.displayTimeStamp(1461973780)	-> 2016-04-29T23:49:40
	return moment.unix(unixTimestamp).utc().format('YYYY-MM-DD HH:mm:ss');
}

siggy2.Helpers.setupSystemTypeAhead = function(selector)
{
	$(selector).typeahead(null, {
		name: 'solar-systems',
		display: 'name',
		source: siggy2.StaticData.systemTypeAhead,

  		templates: {
			empty: [
			      '<div class="empty-message">',
			        '',
			      '</div>'
			    ].join('\n'),
			suggestion: Handlebars.compile('<div><strong>{{name}}</strong>{{#if display_name}}({{display_name}}){{/if}} – {{region_name}}</div>')
		}
	});
}

siggy2.Helpers.systemClassMediumText = function(sysClass)
{
	var text = "";
	sysClass = parseInt(sysClass);

	if( sysClass == 7 )
	{
		text = 'Highsec';
	}
	else if( sysClass == 8 )
	{
		text = 'Lowsec';
	}
	else if( sysClass == 9 )
	{
		text = 'Nullsec';
	}
	else
	{
		text = 'Class '+sysClass;
	}

	return text;
}

siggy2.Helpers.systemClassShortText = function(sysClass)
{
	var text = "";
	sysClass = parseInt(sysClass);

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

siggy2.Helpers.setupHandlebars = function()
{
	HandlebarsFormHelpers.register(Handlebars);

	Handlebars.registerHelper('_', function(str) {
		return _(str);
	});

	Handlebars.registerHelper('isIGB', function(options) {
		if (typeof(CCPEVE) != 'undefined')
		{
			return options.fn(this);
		}
		else
		{
			return options.inverse(this);
		}
	});

	Handlebars.registerHelper('isKSpaceClass', function(systemClass, options) {
		if ( siggy2.Helpers.isKSpaceClass(systemClass) )
		{
			return options.fn(this);
		}
		else
		{
			return options.inverse(this);
		}
	});

	Handlebars.registerHelper('securityClass', function(str) {
		var sec = parseFloat(str);
		if( sec >= 0 )
		{
			return "security-status-"+str.replace(".","_");
		}
		else if( sec < 0.0 && sec > -1.0 )
		{
			return "security-status-0_0";
		}
		else
		{
			return "security-status--1_0";
		}
	});

	Handlebars.registerHelper('structureTypeName', function(id) {
		var structure = siggy2.StaticData.getStructureTypeById(id);
		if(structure != null)
		{
			return structure.name;
		}
		return "UNKNOWN STRUCTURE";
	});

	Handlebars.registerHelper('systemClassTextColor', function(sysClass) {
	    var classColor = '';
		sysClass = parseInt(sysClass);

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
	});

	Handlebars.registerHelper('systemClassShortText', function(sysClass) {
		return siggy2.Helpers.systemClassShortText(sysClass);
	});

	Handlebars.registerHelper('systemEffectIDToText', function(effect) {
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
	});

	Handlebars.registerHelper('systemEffectIDToColor', function(effect) {
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
	});

	Handlebars.registerHelper('escapeSpaceWithUnderscores', function(str) {
		return str.replace(/ /g,"_");
	});

	Handlebars.registerHelper('displayTimestamp', function(stamp) {
		return siggy2.Helpers.displayTimeStamp(stamp);
	});

	Handlebars.registerHelper('numberFormat', function(number, decimals, dec_point, thousands_sep) {
		return number_format(number, decimals, dec_point, thousands_sep)
	});

	Handlebars.registerHelper('notificationToString', function(type, data) {
		return siggy2.Notifications.getNotificationString(type, data);
	});

	Handlebars.registerHelper('notifierToString', function(type, data) {
		return siggy2.Notifications.getNotifierString(type, data);
	});

	Handlebars.registerHelper('toLowerCase', function(str) {
		return str.toLowerCase();
	});

	Handlebars.registerHelper('capitalize', function(str) {
			return str.charAt(0).toUpperCase() + str.slice(1);
	});
}

siggy2.Helpers.isKSpaceClass = function( sysClass )
{
	if( sysClass >= 7 && sysClass <= 9 )
	{
		return true;
	}
	return false;
}

siggy2.isDefined = function(value) 
{
	return typeof value !== 'undefined';
}