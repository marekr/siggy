/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */
 
siggy2.Timer = function(initDate, endDate, selector)
{
	this.beginDate = new Date(initDate);
	this.endDate = null;
	
	if( endDate != null )
	{
		this.endDate = new Date(endDate);
	}
	
	this.container = $(selector);
	this.calculate();
	this.timeout = null;
}			

siggy2.Timer.prototype.destroy = function()
{
	clearTimeout(this.timeout);
	this.container = null;
	delete this.beginDate;
}


siggy2.Timer.prototype.addLeadingZero = function (value)
{
	return value < 10 ? ('0' + value) : value;
}

siggy2.Timer.prototype.calculate = function ()
{
	if( this.container == null )
	{
		return;
	}

	var currDate;
	var prevDate;
	if( this.endDate != null )
	{
		currDate = new Date();
		prevDate = this.endDate;
	}
	else
	{
		currDate = new Date();
		prevDate = this.beginDate;
	}
	
	dd = currDate - prevDate;
	
	if( this.endDate != null )
	{
		dd *= -1;
		
		if( dd < 0 )
			dd = 0;
	}
	
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
			