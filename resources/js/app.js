
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
					if (null !== select.val()) {
						field.trigger('setCountry', select.val().toLowerCase());
					}
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
					if (null !== select.val()) {
						field.trigger('setCountry', select.val().toLowerCase());
					}
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
	jQuery('[reveal-password]').on('click', function(e) {
		e.preventDefault();
		var btn = jQuery(this),
			group = btn.closest('.input-group'),
			field = group.find('input'),
			type = field.attr('type');
			if ('password' == type) {
				field.attr('type', 'text');
				btn.find('span, i').removeClass('fa-eye');
				btn.find('span, i').addClass('fa-eye-slash');
			} else {
				field.attr('type', 'password');
				btn.find('span, i').addClass('fa-eye');
				btn.find('span, i').removeClass('fa-eye-slash');
			}
	});
	jQuery('[generate-password]').on('click', function(e) {
		e.preventDefault();
		var btn = jQuery(this),
			group = btn.closest('.input-group'),
			field = group.find('input'),
			fieldname = field.attr('name'),
			confirmation_name = sprintf('%s_confirmation', fieldname),
			confirmation_field = group.closest('form').find(sprintf('[name="%s"]', confirmation_name)),
			pw = generatePassword();
			field.val(pw);
			confirmation_field.val(pw)
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

window.generatePassword = function(length, countofuppers, countoflowers, countofnumbers, countofspecials) {
	if ('number' !== typeof(length)) {
		length = 12;
	}
	if (length < 6 ) {
		length = 6;
	}
	if ('number' !== typeof(countofuppers)) {
		countofuppers = 1;
	}
	if (countofuppers < 0 ) {
		countofuppers = 0;
	}
	if ('number' !== typeof(countoflowers)) {
		countoflowers = 1;
	}
	if (countoflowers < 0 ) {
		countoflowers = 0;
	}
	if ('number' !== typeof(countofnumbers)) {
		countofnumbers = 1;
	}
	if (countofnumbers < 0 ) {
		countofnumbers = 0;
	}
	if ('number' !== typeof(countofspecials)) {
		countofspecials = 1;
	}
	if (countofspecials < 0 ) {
		countofspecials = 0;
	}
	var remainder = (length - countofuppers - countoflowers - countofnumbers - countofspecials),
		specials = '!@#$%^&*()_+{}:"<>?\|[];\',./`~',
		lowercase = 'abcdefghijklmnopqrstuvwxyz',
		uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		numbers = '0123456789',
		all = specials + lowercase + uppercase + numbers,
		password = '';
	password += specials.pick(countofspecials);
	password += lowercase.pick(countoflowers);
	password += uppercase.pick(countofuppers);
	if (remainder < 0) {
		remainder = 0;
	}
	password += all.pick(remainder);
	return password;
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