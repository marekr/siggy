

interface Object {
	size(obj): number;
}

Object.size = (obj) => {
	var size = 0,
		key;
	for (key in obj)
	{
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};