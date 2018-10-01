
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./rtu');

var setMoment = function() {
	var obj = jQuery(this),
		mom = obj.attr('data-moment'),
		tz = obj.attr('data-moment-tz'),
		fm = obj.attr('data-moment-format');
	if ('now' == mom) {
		var mo = moment();
	} else {
		var mo = moment(mom);
	}
	if ('string' == typeof(tz)) {
		mo.tz(tz);
	}
	if ('string' == typeof(fm)) {
		obj.html(mo.format(fm));
	} else {
		obj.html(mo.format('HH:mm'));
	}
}

jQuery('#menu-toggle > a').on('click', function(e){
	e.preventDefault();
	if ( jQuery('#app').hasClass('expanded-sidebar') ) {
		jQuery('#app').removeClass('expanded-sidebar');
	} else {
		jQuery('#app').addClass('expanded-sidebar')
	}
});

jQuery(function(){
	runWhenTrue("'undefined' !== typeof(grecaptcha) && 'function' == typeof(grecaptcha.execute)", function() {
		grecaptcha.execute();
	}, 100);
	if (jQuery('#login-form').length > 0) {
		if (typeof(Storage) !== "undefined") {
			localStorage.removeItem("notifications_json");
		}
	}
	jQuery('[data-moment]').each(setMoment);
	setInterval(function() {
		jQuery('[data-moment]').each(setMoment);
	},1000);
	jQuery('input[type="tel"]').each(function(){
		var field = jQuery(this),
			name = field.attr('name'),
			country_field = field.closest('form').find(sprintf('[name="%s_country"]', name));
		if(country_field.val() == 'XX') {
			country_field.val('');
		}
		var iti = intlTelInput(this, {
    		autoPlaceholder: 'polite',
    		initialCountry: ('undefined' !== typeof(country_field)) ? ('string' !== typeof(country_field.val())) ? country_field.val() : country_field.val().toLowerCase() : '',
    		preferredCountries: [],
		});
		field.on('countrychange', function() {
			var iso = iti.getSelectedCountryData().iso2.toUpperCase();
			if ('object' == typeof(country_field) && 1 == country_field.length) {
				country_field.val(iso);
				console.log(country_field.val());
			}
		});
		field.on('setCountry', function(e, data){
			iti.setCountry(data);
		});
	});
	jQuery('select[name="country"]').each(function() {
		var select = jQuery(this);
		jQuery('input[type="tel"]').each(function(){
			var field = jQuery(this),
				name = field.attr('name'),
				country_field = field.closest('form').find(sprintf('[name="%s_country"]', name));
				if ('object' == typeof(country_field) && (0 == country_field.val().length || 'XX' == country_field.val())) {
					field.trigger('setCountry', select.val().toLowerCase());
				}
		});
	});
	jQuery('select[name="country"]').on('change', function() {
		var select = jQuery(this);
		jQuery('input[type="tel"]').each(function(){
			var field = jQuery(this),
				name = field.attr('name'),
				country_field = field.closest('form').find(sprintf('[name="%s_country"]', name));
				if ('object' == typeof(country_field) && (0 == country_field.val().length || 'XX' == country_field.val())) {
					field.trigger('setCountry', select.val().toLowerCase());
				}
		});
	});
	clipboard = new ClipboardJS('[data-clipboard-text]');
	clipboard.on('success', function(e) {
		var notice = PNotify.notice({
			title: 'Copied to Clipboard',
			text: sprintf('Copied "%s" to Clipboard', e.text),
			icon: 'fas fa-clipboard-check',
		});
		notice.get().click(function() {
		    notice.remove();
		});
		e.clearSelection();
	});
});

window.handleGoogleReCAPCHA = function(result) {
	jQuery('.g-recaptcha').each(function() {
		var btn = jQuery(this),
			form = btn.closest(form),
			field = form.find('[name="g-recaptcha"]');
			field.val(result);
		form.submit();
	});
}

jQuery('[psuedo-type="datetime-local"]').each(function() {
	jQuery(this).datetimepicker({
		showClear: true,
		showClose: true,
		showTodayButton: true,
		useCurrent: false,
	});
});

if (!Modernizr.inputtypes['datetime-local']) {
	jQuery('[type="datetime-local"]').each(function() {
		jQuery(this).datetimepicker({
			showClear: true,
			showClose: true,
			showTodayButton: true,
			useCurrent: false,
		});
	});
}

if (!Modernizr.inputtypes['date']) {
	jQuery('[type="date"]').each(function() {
		jQuery(this).datetimepicker({
			showClear: true,
			showClose: true,
			showTodayButton: true,
			useCurrent: false,
			format: 'L',
		});
	});
}

if (!Modernizr.inputtypes['time']) {
	jQuery('[type="time"]').each(function() {
		jQuery(this).datetimepicker({
			showClear: true,
			showClose: true,
			showTodayButton: true,
			useCurrent: false,
			format: 'LT',
		});
	});
}