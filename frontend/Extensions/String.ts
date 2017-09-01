interface String {
	lpad(len: number, c: string): string;
	format(...args): string;
}

String.prototype.lpad = function(len: number, c: string): string
{ 
	var s= '', c= c || ' ', len= (len || 2)-this.length;
	while(s.length<len) s+= c; 
	return s+this; 
} 

String.prototype.format = function() {
	var args = arguments;
	return this.replace(/{(\d+)}/g, function(match, number) {
		return typeof args[number] != 'undefined'
		? args[number]
		: match
		;
	});
};