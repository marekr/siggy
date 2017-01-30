
siggy2.Socket = siggy2.Socket || {};

siggy2.Socket.Initialize = function(socketUrl)
{
	this.socketUrl = socketUrl;
	this.socket = null;
}

siggy2.Socket.Open = function()
{
	if(this.socket != null)
	{
		return false;
	}

	var $this = this;
	this.socket = new WebSocket(this.socketUrl);

	this.socket.onopen = function (event) {
		
	};
	
	this.socket.onmessage = function (event) {
		var msg = JSON.parse(event.data);
		console.log(msg);
	}

	this.socket.onclose = function(event) {
		$this.socket = null;
	}
}


siggy2.Socket.Send = function(msg)
{
	this.socket.send(JSON.stringify(msg));
}