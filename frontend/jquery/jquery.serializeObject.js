
;(function() {
	
	"use strict";

	function setup($) {
		$.fn.serializeObject = function() {
		var o = Object.create(null),
			elementMapper = function(element) {
				element.name = $.camelCase(element.name);
				return element;
			},
			appendToResult = function(i, element) {
				var node = o[element.name];

				if ('undefined' != typeof node && node !== null) {
					o[element.name] = node.push ? node.push(element.value) : [node, element.value];
				} else {
					o[element.name] = element.value;
				}
			};

		$.each($.map(this.serializeArray(), elementMapper), appendToResult);
		return o;
		};
	}

	/*global define:true */
	if (typeof define === 'function' && define.amd && define.amd.jQuery) {
		define(['jquery'], setup);
	} else {
		setup(jQuery);
	}

})();