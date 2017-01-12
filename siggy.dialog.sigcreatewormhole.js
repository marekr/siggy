
siggy2.Dialog = siggy2.Dialog || {};

siggy2.Dialog.SigCreateWormhole = function(core, systemID, sigs)
{
	this.templateDialog = Handlebars.compile( $("#template-sig-create-new-wormhole").html() );
    this.sigs = sigs;
    this.core = core;

    this.system = siggy2.StaticData.getSystemByID( systemID );

	var $this = this;
	$(document).on('submit','.sig-create-new-wormhole form', function(e)
	{
		var formData = $(this).serializeObject();

		var list = [];
		for(var i in formData.sig_id)
		{
			list.push({id: formData.sig_id[i], site_id: formData.sig_site_id[i], wh_destination: formData.sig_wh_destination[i]});
		}

		var postData = {
			sigs: list,
			system_id: $this.system.id
		}

		$.ajax({
				type: 'post',
				url: $this.core.settings.baseUrl + 'sig/create_wormholes',
				data: JSON.stringify(postData),
				contentType: 'application/json',
				success: function (sigs)
						{
						},
				dataType: 'json'
			})
			.fail(function(){
				alert('Error creating wormholes');
			})
			.always(function(){
				$.unblockUI();
			});

		e.stopPropagation();
		return false;
	});
}

siggy2.Dialog.SigCreateWormhole.prototype.show = function()
{
	var list = siggy2.StaticData.getWormholesForListHandlebars( this.system.class );
    var dialog = this.templateDialog( { system: this.system, sigs: this.sigs, wormholeTypes: list } );

    this.core.openBox( dialog );

	siggy2.Helpers.setupSystemTypeAhead('.sig-system-typeahead');
}

siggy2.Dialog.SigCreateWormhole.prototype.destroy = function()
{
	$('.sig-system-typeahead').typeahead('destroy');
}
