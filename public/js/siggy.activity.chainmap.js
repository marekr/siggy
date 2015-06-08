/*
* @license Proprietary
* @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
*/

siggy2.Activity = siggy2.Activity || {};

siggy2.Activity.Chainmap = function(core)
{
	var $this = this;
	this.key = 'chainmap';
	this.core = core;

	this.templateResultPOS = Handlebars.compile( $("#template-search-result-pos").html() );
	this.templateResultLegacyPOS = Handlebars.compile( $("#template-search-result-legacy-pos").html() );
	this.input = $('#activity-search-input');

	this.results = $('#activity-search-results');

	$('#activity-search-go').click( function() {
		$this.search();
	});
}

siggy2.Activity.Chainmap.prototype.search = function()
{
	var $this = this;
	$.ajax({
		url: this.core.settings.baseUrl + 'search/everything',
		dataType: 'json',
		cache: false,
		async: true,
		method: 'get',
		data: {query: this.input.val() },
		success: function (data)
		{
			$this.results.empty();
			for( var i in data )
			{
				var result = data[i];
				if( result.type == 'pos' )
				{
					var html = $this.templateResultPOS(result);

					$this.results.append($(html));
				}
				else if( result.type == 'legacy_pos' )
				{
					var html = $this.templateResultLegacyPOS(result);

					$this.results.append($(html));
				}
			}
		}
	});
}

siggy2.Activity.Chainmap.prototype.start = function()
{
	$('#activity-' + this.key).show();
}

siggy2.Activity.Chainmap.prototype.stop = function()
{
	$('#activity-' + this.key).hide();
}
