/**
 * Renders a ISO8601 date + time from a unix timestamp. Except instead of the T
 * in between we have a space.
 */
siggy2.Helpers = siggy2.Helpers || {};

siggy2.Helpers.displayTimeStamp = function(unixTimestamp)
{
	var date = new Date(unixTimestamp * 1000);

	var day = pad(date.getUTCDate(), 2);
	var month = pad(date.getUTCMonth() + 1, 2);
	var year = date.getUTCFullYear().toString();

	var hours = pad(date.getUTCHours(), 2);
	var minutes = pad(date.getUTCMinutes(), 2);
	var seconds = pad(date.getUTCSeconds(), 2);

	var time = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;

	delete date;

	return time;
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

	Handlebars.registerHelper('equal', function(lvalue, rvalue, options) {
		if (arguments.length < 3)
			throw new Error("Handlebars Helper equal needs 2 parameters");
		if( lvalue != rvalue ) {
			return options.inverse(this);
		} else {
			return options.fn(this);
		}
	});

	Handlebars.registerHelper('notEqual', function(lvalue, rvalue, options) {
		if (arguments.length < 3)
			throw new Error("Handlebars Helper not equal needs 2 parameters");
		if( lvalue==rvalue ) {
			return options.inverse(this);
		} else {
			return options.fn(this);
		}
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
