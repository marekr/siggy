
export default class Socket {

	private static socketUrl;
	private static socket;

	public static Initialize (socketUrl)
	{
		this.socketUrl = socketUrl;
		this.socket = null;
	}

	public static Open()
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

	public static Send(msg)
	{
		this.socket.send(JSON.stringify(msg));
	}
}