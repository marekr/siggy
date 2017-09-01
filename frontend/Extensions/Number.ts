

interface Number {
	lpad(len: number, c: string): string;
	lpadZero(len: number): string;
}

Number.prototype.lpad = function(len: number, c: string)
{ 
	return String(this).lpad(len, c); 
} 

Number.prototype.lpadZero = function(len: number)
{ 
	return this.lpad(len,'0'); 
}