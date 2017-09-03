/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';

export class Search extends Activity {
	public key:string = 'search';
	public title:string = 'Search';

	private templateResultPOS = null;
	private templateResultLegacyPOS = null;

	private input = null;
	private results = null;

	constructor (core) 
	{
		super(core);
		
		var $this = this;
		this.core = core;

		this.templateResultPOS = Handlebars.compile( $("#template-search-result-pos").html() );
		this.templateResultLegacyPOS = Handlebars.compile( $("#template-search-result-legacy-pos").html() );
		this.input = $('#activity-search-input');

		this.results = $('#activity-search-results');

		$('#activity-search-go').click( function() {
			$this.search();
		});
	}

	public search()
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

	public start(args): void
	{
		$('#activity-' + this.key).show();
	}

	public stop(): void
	{
		$('#activity-' + this.key).hide();
	}
}