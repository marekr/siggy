
import $ from 'jquery';

export class Maps {

	public static available:any = {};
	public static selected:number = 0;
	
	public static getSelectDropdown(selected, selectedNote: string = null)
	{
		var sel = $('<select>');
		$.each(this.available, function(key, c) {
			var value = c.name;
			if( selected == key && selectedNote != null && selectedNote != '' )
			{
				value += ' ' + selectedNote;
			}

			sel.append($("<option>", {
				value: key,
				text: value
			}));
		});

		if( typeof(selected) != "undefined" )
		{
			sel.val(selected);
		}

		return sel;
	}
}
