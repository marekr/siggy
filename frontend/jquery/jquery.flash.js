

;(function() {
	
	"use strict";

	function setup($) {
		$.fn.flash = function( color, duration )
		{
			if( typeof this.data('flashing') =='undefined' )
			{
				this.data('flashing', false);
			}

			if( this.data('flashing') == true )
			{
				return;
			}
			var current = this.css( 'background-color' );

			this.data('flashing', true)
			this.animate( { backgroundColor: color }, duration / 2 );

			var $this = this;
			this.animate( { backgroundColor: current }, duration / 2, function() { $this.data('flashing', false); } );
		}

		$.fn.fadeOutFlash = function( color, duration )
		{
				if( typeof this.data('flashing') =='undefined' )
				{
					this.data('flashing', false);
				}

				if( this.data('flashing') == true )
				{
					return;
				}
			var current = this.css( 'background-color' );

			this.data('flashing', true)
			this.css( 'background-color', color )

			var $this = this;
			this.animate( { backgroundColor: current }, duration, function() { $this.data('flashing', false); } );
		}
	}


	/*global define:true */
	if (typeof define === 'function' && define.amd && define.amd.jQuery) {
		define(['jquery'], setup);
	} else {
		setup(jQuery);
	}

})();