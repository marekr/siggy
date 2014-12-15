/**
* @constructor
*/
function sigtable( options )
{
	this.sigData = {};
	this.sigClocks = {};
	this.siggyMain = null;
	this.systemID = 0;
	this.systemClass = 0;
	this.editingSig = false;

	this.defaults = {
		showSigSizeCol: false,
		baseUrl:''
	};

	this.settings = $.extend(this.defaults, options);

	this.templateSigRow = Handlebars.compile( $("#template-sig-table-row").html() );
	this.setupHandlebars();
}

sigtable.prototype.initialize = function()
{
	var $this = this;
	if( this.settings.showSigSizeCol )
	{
		var tableSorterHeaders = {
			0: {
				sorter: false
			},
			1: {
				sortInitialOrder: 'desc'
			},
			5: {
				sorter: false
			},
			7: {
				sorter: false
			}
		};
	}
	else
	{
		var tableSorterHeaders = {
			0: {
				sorter: false
			},
			4: {
				sorter: false
			},
			6: {
				sorter: false
			}
		};
	}
	
		
	$( document ).on('click', 'td.edit i', function (e)
										{
											$this.editSigForm($(this).parent().parent().data('sig-id'));
										});
										
	$( document ).on('click', 'td.remove i', function (e)
										{
											$this.removeSig($(this).parent().parent().data('sig-id'));
										});

	$('#sig-table').tablesorter(
	{
		headers: tableSorterHeaders
	});
	
	$('#sig-table').trigger("sorton", [ [[1,0]] ]);

	$('#sig-table').bind('sortEnd', function() {
		$this.colorizeSigRows();
	});


	$('#checkbox-show-anomalies').prop('checked', this.siggyMain.displayStates.showAnomalies);
	$('#checkbox-show-anomalies').change( function()
	{
		$this.changeAnomState($(this).is(':checked'));
		$this.updateSigTotal();
	});
	
	$this.initializeHotkeys();
}

sigtable.prototype.initializeHotkeys = function()
{
	var $this = this;
	
	$(document).bind('keydown', 'ctrl+q', function(){
		$(document).scrollTop( $('#sig-table').offset().top-100 );  
	});
	
	$(document).bind('keydown', 'ctrl+b', function(){
		$(document).scrollTop( $("#sig-add-box textarea[name=mass_sigs]").offset().top-100);  
		$("#sig-add-box textarea[name=mass_sigs]").focus();
	});
	
	this.siggyMain.hotkeyhelper.registerHotkey('Ctrl+Q', 'Jump to signatue table');
	this.siggyMain.hotkeyhelper.registerHotkey('Ctrl+B', 'Focus on signature adder');
}

sigtable.prototype.clear = function()
{
	this.sigData = {};
	
	for(var i in this.sigClocks)
	{
		this.sigClocks[i].destroy();
		delete this.sigClocks[i];
	}
	
    $('td.moreinfo img').qtip('destroy');
    $('td.age span').qtip('destroy');

	$("#sig-table tbody").empty();
	this.editingSig = false;
}

sigtable.prototype.refreshAnomState = function()
{
	this.changeAnomState(this.siggyMain.displayStates.showAnomalies);
}

sigtable.prototype.changeAnomState = function(visible)
{
	this.siggyMain.displayStates.showAnomalies = visible;
	this.siggyMain.saveDisplayState();

	if( visible )
	{
		$('#sig-table tbody tr').show();
	}
	else
	{
		$('#sig-table tbody tr.type-combat').hide();
	}
	
	this.colorizeSigRows();
}

sigtable.prototype.colorizeSigRows = function()
{
	var i = 0;
	$('#sig-table tbody tr').each( function() {
		$( this ).removeClass('alt');
		if( $(this).is(':visible') )
		{
			if( i % 2 != 0 )
				$( this ).addClass('alt');
			i++;
		}
	});
}

sigtable.prototype.convertType = function (type)
{
	//unknown null case, either way this should surpress it
	if (type == 'wh')
		return _("WH");
	else if (type == 'grav')
		return _("Ore");
	else if (type == 'ladar')
		return _("Gas");
	else if (type == 'radar')
		return _("Data");
	else if (type == 'mag')
		return "Relic";
	else if (type == 'combat')
		return _("Combat");
	else
		return "";
}

sigtable.prototype.convertSiteID = function (whClass, type, siteID)
{
	if( siteID == 0 || whClass > 9 )
		return "";
	if (type == 'combat')
		return _(anomsLookup[whClass][siteID]);
	else if (type == 'wh')
		return siggy2.StaticData.getWormholeFancyNameByID(siteID);
	else if (type == 'mag')
		return _(magsLookup[whClass][siteID]);
	else if (type == 'radar')
		return _(radarsLookup[whClass][siteID]);
	else if (type == 'ladar')
		return _(ladarsLookup[siteID]);
	else if (type == 'grav')
		return _(gravsLookup[siteID]);
	else
		return "";
}

sigtable.prototype.updateSigs = function (sigData, flashSigs)
{
	for (var i in this.sigData)
	{
		if (typeof(sigData[i]) !== undefined && typeof(sigData[i]) != "undefined" && sigData[i] !== null)
		{
			sigData[i].exists = true;
			if (!this.sigData[i].editing)
			{
				this.sigData[i] = sigData[i];
				this.updateSigRow(this.sigData[i], flashSigs);
			}
		}
		else
		{
			if (this.sigData[i].editing)
			{
				continue;
			}

			this.removeSigRow(this.sigData[i]);
			delete this.sigData[i];
		}
	}

	for (var i in sigData)
	{
		if (sigData[i].exists != true)
		{
			this.addSigRow(sigData[i],flashSigs);
			this.sigData[i] = sigData[i];
		}
	}

	if (!this.editingSig)
	{
		$('#sig-table').trigger('update');
	}
	
	this.refreshAnomState();
	this.updateSigTotal();
}

sigtable.prototype.updateSigTotal = function()
{
	$('#number-sigs').text(	$('#sig-table tr.sig:visible').length );
}

sigtable.prototype.removeSig = function (sigID)
{
	this.removeSigRow(
	{
		sigID: sigID
	});
	$('#sig-table').trigger('update');
	this.updateSigTotal();

	$.post(this.settings.baseUrl + 'sig/remove', {
		systemID: this.systemID,
		sigID: sigID
	});
}

sigtable.prototype.updateSiteSelect = function( ele, whClass, type, siteID )
{
	var elem = $( ele );
	elem.empty();

	var options = [];
	switch( type )
	{
		case 'wh':
			options = siggy2.StaticData.getWormholesForList(whClass);
			break;
		case 'ladar':
			options = ladarsLookup;
			break;
		case 'mag':
			options = magsLookup[whClass];
			break;
		case 'grav':
			options = gravsLookup;
			break;
		case 'radar':
			options = radarsLookup[whClass];
			break;
		case 'combat':
			options = anomsLookup[whClass];
			break;
		default:
			options = { 0: '--'};
			break;
	}

	
	for (var i in options)
	{
		elem.append($('<option>').attr('value', i).text(_(options[i])));
	}

	elem.val(siteID);
}

sigtable.prototype.removeSigRow = function (sigData)
{
	if(this.sigClocks[sigData.sigID] != undefined )
	{
		this.sigClocks[sigData.sigID].destroy();
		delete this.sigClocks[sigData.sigID];
	}

	$('#sig-' + sigData.sigID + ' td.moreinfo img').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.age span').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.desc').qtip('destroy');

	$('#sig-' + sigData.sigID).remove();
	this.colorizeSigRows();
}

sigtable.prototype.setupHandlebars = function()
{
	var $this = this;
	

	Handlebars.registerHelper('displayTimestamp', function(stamp) {
		return siggymain.displayTimeStamp(stamp);
	});
	
	Handlebars.registerHelper('sigTypeToText', function(type) {
		return $this.convertType(type);
	});
	
	Handlebars.registerHelper('siteIDToText', function(sysClass, type, siteID) {
		return $this.convertSiteID(sysClass, type, siteID);
	});
	
	Handlebars.registerHelper('notEqual', function(lvalue, rvalue, options) {
		if (arguments.length < 3)
			throw new Error("Handlebars Helper not equal needs 2 parameters");
		if( lvalue==rvalue ) {
			return options.inverse(this);
		} else {
			return options.fn(this);
		}
	});
	
}

sigtable.prototype.addSigRow = function (sigData, flashSig)
{
	var $this = this;
	sigData.showSigSizeCol = this.settings.showSigSizeCol;
	sigData.sysClass = this.systemClass;
	
	var row = this.templateSigRow(sigData);
	$("#sig-table tbody").append( row );
	
	this.sigRowMagic(sigData);
	
	this.colorizeSigRows();

	if( flashSig )
	{
		$('#sig-' + sigData.sigID).fadeOutFlash("#A46D00", 20000);
	}
}

sigtable.prototype.sigRowMagic = function(sigData)
{	
	delete this.sigClocks[sigData.sigID];
	this.sigClocks[sigData.sigID] = new CountUp(sigData.created * 1000, '#sig-' + sigData.sigID + ' td.age span', "test");

	$('#sig-' + sigData.sigID + ' td.moreinfo i').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.moreinfo i').qtip({
		content: {
			text: $('#creation-info-' + sigData.sigID) // Use the "div" element next to this for the content
		}
	});
	
	$('#sig-' + sigData.sigID + ' td.age span').qtip('destroy');
	$('#sig-' + sigData.sigID + ' td.age span').qtip({
		content: {
			text: $('#age-timestamp-' + sigData.sigID) // Use the "div" element next to this for the content
		}
	});
	
	if( sigData.type == 'wh' )
	{
		$('#sig-' + sigData.sigID + ' td.desc').qtip('destroy');
		
		var wh = siggy2.StaticData.getWormholeByID(sigData.siteID);
		if( wh != null )
		{
			var whTooltip = siggy2.StaticData.templateWormholeInfoTooltip(wh);
			$('#sig-' + sigData.sigID + ' td.desc').append(whTooltip);
			
			
			$('#sig-' + sigData.sigID + ' td.desc').qtip({
				content: {
					text: $('#static-info-' + sigData.siteID)
				},
				position: {
					target: 'mouse',
					adjust: { x: 5, y: 5 },
					viewport: $(window)
				}
			});
		}
		
		console.log(whTooltip);
	}
}

sigtable.prototype.editSigForm = function (sigID)
{
	if (this.editingSig)
	{
		return;
	}
	
	/*disable the tooltip or it will be annoying */
	$('#sig-' + sigID + ' td.desc').qtip('disable');

	this.sigData[sigID].editing = true;
	this.editingSig = true;

	var controlEle = $("#sig-" + sigID + " td.edit");
	controlEle.text('');

	var that = this;
	controlEle.append($('<img>').attr('src', this.settings.baseUrl + 'public/images/accept.png').click(function (e)
	{
		that.editSig(sigID)
	}));

	var sigEle = $("#sig-" + sigID + " td.sig");
	sigEle.text('');

	var sigInput = $('<input>').val(this.sigData[sigID].sig)
							.attr('maxlength', 3)
							.keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } );
	sigEle.append(sigInput);

	if( this.settings.showSigSizeCol )
	{
			var sizeEle = $("#sig-" + sigID + " td.size");
			sizeEle.text('');

			sizeEle.append(this.generateOrderedSelect( [
														['', '--'],
														['1', '1'],
														['2.2','2.2'],
														['2.5','2.5'],
														['4','4'],
														['5', '5'],
														['6.67','6.67'],
														['10','10']
														], this.sigData[sigID].sigSize));
	}

	var typeEle = $("#sig-" + sigID + " td.type");
	typeEle.text('');

	typeEle.append(this.generateSelect({
											none: '--',
											wh: 'WH',
											ladar: 'Gas',
											radar: 'Data',
											mag: 'Relic',
											combat: 'Combat',
											grav: 'Ore'
										}, this.sigData[sigID].type)
										.change(function ()
												{
													that.editTypeSelectChange(sigID)
												}));

	var descEle = $('#sig-' + sigID + ' td.desc');
	descEle.text('');
	descEle.append(this.generateSiteSelect(this.systemClass, this.sigData[sigID].type, this.sigData[sigID].siteID)).append($('<br />'))
		.append( $('<input>').val(this.sigData[sigID].description)
								.keypress( function(e) { if(e.which == 13){ that.editSig(sigID)  } } )
								.css('width', '100%')
					);

	sigInput.focus();
}

sigtable.prototype.editTypeSelectChange = function (sigID)
{
	var newType = $("#sig-" + sigID + " td.type select").val();

	this.updateSiteSelect( '#sig-' + sigID + ' td.desc select', this.systemClass, newType, 0 );
}

sigtable.prototype.editSig = function (sigID)
{
	var sigEle = $("#sig-" + sigID + " td.sig input");
	if( this.settings.showSigSizeCol )
	{
			var sizeEle = $("#sig-" + sigID + " td.size select");

	}
	var typeEle = $("#sig-" + sigID + " td.type select");
	var descEle = $("#sig-" + sigID + " td.desc input");
	var siteEle = $("#sig-" + sigID + " td.desc select");

	if (sigEle.val().length != 3)
	{
		return false;
	}

	var sigUpdate = {};
	this.sigData[sigID].sig = sigEle.val().toUpperCase();
	this.sigData[sigID].type = typeEle.val();
	this.sigData[sigID].siteID = siteEle.val();
	this.sigData[sigID].description = descEle.val();

	var postData = {
		sigID: sigID,
		sig: this.sigData[sigID].sig,
		type: this.sigData[sigID].type,
		desc: this.sigData[sigID].description,
		siteID: this.sigData[sigID].siteID,
		systemID: this.systemID
	};

	if( this.settings.showSigSizeCol )
	{
		this.sigData[sigID].sigSize = sizeEle.val();
		postData.sigSize = this.sigData[sigID].sigSize;
	}

	var that = this;
	$.post(this.settings.baseUrl + 'sig/edit', postData, function ( data )
	{
		that.editingSig = false;
		that.sigData[sigID].editing = false;

	},"json").fail( function(xhr, textStatus, errorThrown) { alert('Error') } );

	sigEle.remove();
	if( this.settings.showSigSizeCol )
	{
		sizeEle.remove();
	}
	typeEle.remove();
	descEle.remove();
	siteEle.remove();

	this.updateSigRow(this.sigData[sigID]);

	var controlEle = $("#sig-" + sigID + " td.edit");
	controlEle.text('');
	controlEle.append($('<i>').addClass('icon icon-pencil icon-large')
							.click(function (e)
									{
										that.editSigForm(sigID)
									})
								);

}

sigtable.prototype.generateSiteSelect = function (whClass, type, siteID)
{
	if (type == "wh") return this.generateSelect(siggy2.StaticData.getWormholesForList(whClass), siteID);
	else if (type == "ladar") return this.generateSelect(ladarsLookup, siteID);
	else if (type == "mag") return this.generateSelect(magsLookup[whClass], siteID);
	else if (type == "grav") return this.generateSelect(gravsLookup, siteID);
	else if (type == "radar") return this.generateSelect(radarsLookup[whClass], siteID);
	else if (type == "combat") return this.generateSelect(anomsLookup[whClass], siteID);
	else return this.generateSelect({
										0: '--'
									}, 0);
}


sigtable.prototype.generateOrderedSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i=0; i < options.length; i++ )
	{
		newSelect.append($('<option>').attr('value', options[i][0]).text(options[i][1]));
	}

	newSelect.val(select);

	return newSelect;
}

sigtable.prototype.generateSelect = function (options, select)
{
	var newSelect = $('<select>');

	for (var i in options)
	{
		newSelect.append($('<option>').attr('value', i).text(_(options[i])));
	}

	newSelect.val(select);

	return newSelect;
}

sigtable.prototype.updateSigRow = function (sigData, flashSig)
{
	var baseID = '#sig-' + sigData.sigID;
	sigData.showSigSizeCol = this.settings.showSigSizeCol;
	sigData.sysClass = this.systemClass;
	
	var row = this.templateSigRow(sigData);
	$(baseID).replaceWith(row);
	
	
	this.sigRowMagic(sigData);
	this.colorizeSigRows();
}
