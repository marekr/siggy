/*
 * @license Proprietary
 * @copyright Copyright (c) 2016 borkedLabs - All Rights Reserved
 */

siggy2.Eve = siggy2.Eve || {};

siggy2.Eve.Initialize = function(baseUrl)
{
	this.baseUrl = baseUrl;
}

siggy2.Eve.SetDestination = function(systemId)
{
	systemId = parseInt(systemId);

	var postData = {
		system_id: systemId,
		waypoint: false
	}

	this.addWaypointCall(postData);
}

siggy2.Eve.AddWaypoint = function(systemId)
{
	systemId = parseInt(systemId);

	var postData = {
		system_id: systemId,
		waypoint: true
	}

	this.addWaypointCall(postData);
}

siggy2.Eve.ShowSystemInfo = function(systemName)
{
	window.open('http://evemaps.dotlan.net/system/'+ systemName , '_blank');
}

siggy2.Eve.ShowSystemInfoById = function(systemId)
{
	window.open('http://evemaps.dotlan.net/system/'+ systemId , '_blank');
}

siggy2.Eve.EveWho = function(name)
{
	window.open('https://evewho.com/pilot/'+ name , '_blank');
}

siggy2.Eve.addWaypointCall = function(data)
{			
	$.post(this.baseUrl + 'crest/waypoint', JSON.stringify(data))
			.done(function(response)
			{
				if(response.status == "error")
				{
					siggy2.Dialogs.alertActionError(response.error);
				}
			})
			.fail(function(jqXHR)
			{
				if(jqXHR.status >= 500)
				{
					siggy2.Dialogs.alertServerError("setting waypoint");
				}
			});
}