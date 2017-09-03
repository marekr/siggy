/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import { StaticData } from './StaticData';
import time from 'locutus/php/datetime/time';


export enum NotifierType {
	SystemMapped = 'system_mapped',
	SystemResidentFound = 'system_resident_found',
	SiteFound = 'site_found',
	SiggyAnnouncement = 'siggy_announcement'
}

export default class Notifications
{
	private core;
	private counter;
	private lastRead: number;
	public newestTimestamp: number;
		
	constructor (core) {
		this.core = core;
		this.counter = $('#notification-count');

		this.lastRead = 0;
		this.newestTimestamp = 0;

		var $this = this;

		$('#notification-header-dropdown').on('show.bs.dropdown', function () {
			$this.lastRead = time();
			$this.setNotificationCount(0);
			$.ajax({
				url: $this.core.settings.baseUrl + 'notifications/read',
				cache: false,
				method: 'post',
				success: function (data)
				{
				}
			});
		})

		$('.notification-dropdown-view-link').click( function() {
			$this.core.loadActivity('notifications');
		})


		$(document).on('click','.notification-link', function(e)
		{
			e.preventDefault();

			$this.handleNotificationLinkClick($(this));
		});
	}

	public handleNotificationLinkClick(ele)
	{
		var $this = this;
		var type = $(ele).data('type');

		if( type == NotifierType.SiggyAnnouncement )
		{
			$.ajax({
				url: $this.core.settings.baseUrl + 'announcements/view',
				cache: false,
				dataType: 'json',
				method: 'get',
				data: {id: $(ele).data('id')},
				success: function (data)
				{
					$('#dialog-notice .box-header').html(data.title);
					$('#dialog-notice .notice-content').html(data.content);
					$this.core.openBox($('#dialog-notice'));
				}
			});
		}
	}

	public setNotificationCount(counter)
	{
		if( counter > 0 )
		{
			this.counter.text( counter );
		}
		else
		{
			this.counter.text('');
		}
	}

	public update(data)
	{
		this.lastRead = data.last_read;

		var size = Object.size(data.items);
		if( size > 0 )
		{
			this.setNotificationCount(0);

			var items = $('li.notification-dropdown-item');

			if( items.length >= 5 )
			{
				items.slice(-1*size).remove();
			}


			var counter = 0;

			data.items = data.items.reverse();
			for( var i in data.items )
			{
				var n = data.items[i];

				var ele = $('<li>');
				ele.html( Notifications.getNotificationString(n.type, n.data) );
				ele.addClass('notification-dropdown-item');

				$('#notifications-menu').prepend( ele );

				if( n.created_at > this.lastRead )
					counter++;

				if( n.created_at > this.newestTimestamp )
					this.newestTimestamp = n.created_at;
			}

			if( counter > 0 )
				this.setNotificationCount(counter);
		}
	}


	public static getNotificationString(type, data)
	{
		if( type == NotifierType.SystemMapped )
		{
			if(typeof(data.number_jumps) != 'undefined' && data.number_jumps > 0)
			{
				return window._('<b>{0}</b> found system <b>{1}</b>, {2} jumps from <b>{3}</b>').format(data.character_name, data.nearby_system_name, data.number_jumps, data.system_name);
			}
			else
			{
				return window._('<b>{0}</b> found system <b>{1}</b>').format(data.character_name, data.system_name);
			}
		}
		else if( type == NotifierType.SystemResidentFound )
		{
			return window._('<b>{0}</b> found residents <b>{1}</b> in system <b>{2}</b>').format(data.discoverer_name, data.resident_name, data.system_name);
		}
		else if( type == NotifierType.SiteFound )
		{
			var name = StaticData.getSiteNameByID(data.site_id);
			return window._('<b>{0}</b> found {1} in system {2} as signature {3}').format(data.discoverer_name, window._(name), data.system_name, data.signature);
		}
		else if( type == NotifierType.SiggyAnnouncement )
		{
			return window._('<b>siggy</b> <a class="notification-link" data-type="siggy_announcement" data-id="{1}">{0}</a>').format(data.announcement_title, data.announcement_id);
		}
	}

	public static getNotifierString(type: string, data)
	{
		if( type == NotifierType.SystemMapped )
		{
			if(typeof(data.num_jumps) != 'undefined' && data.num_jumps > 0)
			{
				return window._('Trigger a system is found within <b>{0}</b> jumps of system <b>{1}</b>.').format(data.num_jumps, data.system_name);
			}
			else
			{
				return window._('Trigger when system <b>{0}</b> mapped via jump.').format(data.system_name);
			}
		}
		else if( type == NotifierType.SystemResidentFound )
		{
			return window._('Trigger when POS belonging to <b>{0}</b> is present in a newly mapped system.').format(data.resident_name);
		}
		else if( type == NotifierType.SiteFound )
		{
			var name = StaticData.getSiteNameByID(data.site_id);
			return window._('Trigger when site <b>{0}</b> is added as a signature.').format(window._(name));
		}
	}
}