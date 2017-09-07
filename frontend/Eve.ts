/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import { Dialogs } from './Dialogs';


export default class Eve {

	private static baseUrl: string = '';

	public static Initialize(baseUrl) { 
		this.baseUrl = baseUrl;
		
		var $this = this;
		$(document).on('click','.eve-set-destination', function(e) {
			$this.SetDestination($(this).data('system-id'));
		});
		
		$(document).on('click','.eve-show-system-info-by-id', function(e) {
			$this.ShowSystemInfoById($(this).data('system-id'));
		});
		
		$(document).on('click','.eve-evewho', function(e) {
			$this.EveWho($(this).data('character-name'));
		});
	}
	
	public static SetDestination(systemId)
	{
		systemId = parseInt(systemId);
	
		var postData = {
			system_id: systemId,
			waypoint: false
		}
	
		this.addWaypointCall(postData);
	}

	public static AddWaypoint(systemId)
	{
		systemId = parseInt(systemId);
	
		var postData = {
			system_id: systemId,
			waypoint: true
		}
	
		this.addWaypointCall(postData);
	}

	public static ShowSystemInfo(systemName)
	{
		window.open('http://evemaps.dotlan.net/system/'+ systemName , '_blank');
	}

	public static ShowSystemInfoById(systemId)
	{
		window.open('http://evemaps.dotlan.net/system/'+ systemId , '_blank');
	}

	public static EveWho(name)
	{
		window.open('https://evewho.com/pilot/'+ name , '_blank');
	}

	private static addWaypointCall(data)
	{			
		$.post(this.baseUrl + 'crest/waypoint', JSON.stringify(data))
			.done(function(response)
			{
				if(response.status == "error")
				{
					Dialogs.alertActionError(response.error);
				}
			})
			.fail(function(jqXHR)
			{
				if(jqXHR.status >= 500)
				{
					Dialogs.alertServerError("setting waypoint");
				}
			});
	}
}