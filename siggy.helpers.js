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

siggy2.Helpers.setupHandlebars = function()
{
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

	Handlebars.registerHelper('displayTimestamp', function(stamp) {
		return siggy2.Helpers.displayTimeStamp(stamp);
	});

	Handlebars.registerHelper('numberFormat', function(number, decimals, dec_point, thousands_sep) {
		return number_format(number, decimals, dec_point, thousands_sep)
	});

	Handlebars.registerHelper('notificationToString', function(type, data) {
		return siggy2.Notifications.getNotificationString(type, data);
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
