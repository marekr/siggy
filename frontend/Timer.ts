/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */
 
import $ from 'jquery';
import * as moment from 'moment';

export default class Timer {

	private container = null;
	private timeout = null;
	private beginDate: moment.Moment = null;
	private endDate: moment.Moment = null;
	
	constructor(initDate, endDate, selector) {
		this.beginDate = moment.utc(initDate);
		this.endDate = null;
		
		if( endDate != null )
		{
			this.endDate = moment.utc(endDate);
		}
		
		this.container = $(selector);
		this.calculate();
		this.timeout = null;
	}

	public destroy() {
		clearTimeout(this.timeout);
		this.container = null;
		delete this.beginDate;
		delete this.endDate;
	}

	public calculate() {
		if( this.container == null )
		{
			return;
		}

		var currDate;
		var prevDate;
		if( this.endDate != null )
		{
			currDate = this.endDate;
			prevDate = moment.utc();
		}
		else
		{
			currDate = moment.utc();
			prevDate = this.beginDate;
		}
		
		var t = currDate.diff(prevDate);
		
		var days = moment.duration(t).days();
		var hours = moment.duration(t).hours().lpadZero(2);
		
		if( days == 0 )
		{
			var minutes = moment.duration(t).minutes().lpadZero(2);
			var seconds = moment.duration(t).seconds().lpadZero(2);
			this.container.text(hours + ":" + minutes + ":" + seconds);
		}
		else
		{
			this.container.text(days + "d " + hours + "h");
		}
		
		if( days < 2 )
		{
			var self = this;
			this.timeout = setTimeout(function ()
			{
				self.calculate();
			}, 1000);
		}

		currDate = null;
	}
}