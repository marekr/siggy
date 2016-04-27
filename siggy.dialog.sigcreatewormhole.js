
siggy2.Dialog = siggy2.Dialog || {};

siggy2.Dialog.SigCreateWormhole = function(core, systemID, sigs)
{
	this.templateDialog = Handlebars.compile( $("#template-sig-create-new-wormhole").html() );
    this.sigs = sigs;
    this.core = core;
    
    this.system = siggy2.StaticData.getSystemByID( systemID );
}

siggy2.Dialog.SigCreateWormhole.prototype.show = function()
{
    var dialog = this.templateDialog( { system: this.system, sigs: this.sigs } );
    
    this.core.openBox( dialog );
}

