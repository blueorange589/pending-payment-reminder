(function($) {
	"use strict";

	var prfwjs = {
		siteurl: '',
		symbol: '',
		init: function() {
			this.siteurl = ajax_object.prfw_url;
			this.symbol = ajax_object.symbol;
		},
		xhr: function(params, callback) {
			params.nonce = ajax_object.nonce;
			params.action = "prfw";
			return $.ajax({
				url: ajax_object.ajaxurl,
				type: 'POST',
				data: params,
				success: function( response ) {
					var data = JSON.parse(response);
					data.message ? data.success ? toastr.success(data.message) : toastr.error(data.message) : '';
					if(data.payload) {
						if(data.payload.reload) { location.reload(); }
						if(data.payload.redirect) { window.location.href = data.payload.redirect; }
					}
					callback(data); // JSON data parsed by `data.json()` call
				}
			});
		},
		isValidDate: function(dateString) {
			var regEx = /^\d{4}-\d{2}-\d{2}$/;
			if(!dateString.match(regEx)) return false;  // Invalid format
			var d = new Date(dateString);
			var dNum = d.getTime();
			if(!dNum && dNum !== 0) return false; // NaN value, Invalid date
			return d.toISOString().slice(0,10) === dateString;
		},
		validateForm: function(f) {
			var proceed = true;
			var inputs = $('#'+f).find('[data-validate]');
			$.each(inputs, function(k, obj){
				var valtype = $(obj).attr('data-validate');
				var value = $(obj).val();
				
				if(proceed && valtype=='date' && !prfw.isValidDate(value)) {
					toastr.error('Date format provided is invalid');
					proceed = false;
					$(obj).addClass('invalid').on('focus', function(){$(this).removeClass('invalid'); });
				}

				var moneyRegex = /^(?!0\.00)\d{1,3}(,\d{3})*(\.\d\d)?$/
				if(proceed && valtype=='money' && (value!='0.00' && !moneyRegex.test(value))) {
					toastr.error('Money format provided is invalid');
					proceed = false;
					$(obj).addClass('invalid').on('focus', function(){$(this).removeClass('invalid'); });
				}

				if(valtype=='required') {
					if(proceed && typeof value == 'undefined' || (typeof value !== 'undefined' && !value.length)) {
						toastr.error('Field can not be empty');
						proceed = false;
						$(obj).addClass('invalid').on('focus', function(){$(this).removeClass('invalid'); });
					}
				}

				if(proceed && valtype=='name' && value.length<4 || value.length>32) {
					toastr.error('Name should be between 4-32 characters');
					proceed = false;
					$(obj).addClass('invalid').on('focus', function(){$(this).removeClass('invalid'); });
				}

			});
		return proceed;
		}
	}

	prfwjs.init();
	window.prfw = prfwjs;
})( jQuery );