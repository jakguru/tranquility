
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./rtu');
require('./appointments');
import Cropper from 'cropperjs';

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
	var clipboard = new ClipboardJS('[data-clipboard-text]');
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
	jQuery("#open-file-picker").on('click', function(e){
		e.preventDefault();
		jQuery('#uploaded-image').click();
	});

	var cropper;

	if (jQuery('#dropbox').length > 0) {
		var dropbox = new Uploader({
			el: '#dropbox',
			url: '/upload',
		});
		dropbox.on('dragover', function(e) {
		    this.$el.className = 'drop hover';
		});

		dropbox.on('dragleave', function(e) {
		    this.$el.className = 'drop';
		});

		dropbox.on('dragend', function(e) {
		    this.$el.className = 'drop';
		});

		dropbox.on('drop', function(e) {
		    this.$el.className = 'drop';
		});

		dropbox.on('files:added', function(files) {
			// do nothing because we're not uploading!
		});

		dropbox.on('file:preview', function(file, $img) {
			if ($img) {
				$img.id = 'image-to-crop';
				jQuery('#image-preview').removeClass('d-none');
				jQuery('#image-preview').html($img);
				jQuery('#image-blob').val($img.src);
				cropper = new Cropper(jQuery('#image-to-crop')[0], {
					aspectRatio: 1,
				});
				jQuery('#image-to-crop')[0].addEventListener('ready', (event) => {
					jQuery("#save-uploaded-image").prop('disabled', false);
				});
				jQuery('#image-to-crop')[0].addEventListener('crop', (event) => {
					var canvas = cropper.getCroppedCanvas(),
						resizedCanvas = resizeCanvas(canvas, 512, 512),
						dataurl = resizedCanvas.toDataURL();
					jQuery('#image-blob').val(dataurl);
				});
			}
		});

		var single = new Uploader({
			el: '#uploaded-image',
			url: '/upload',
		});

		single.on('files:added', function(files) {
			// do nothing because we're not uploading!
		});

		single.on('file:preview', function(file, $img) {
			if ($img) {
				$img.id = 'image-to-crop';
				jQuery('#image-preview').removeClass('d-none');
				jQuery('#image-preview').html($img);
				jQuery('#image-blob').val($img.src);
				cropper = new Cropper(jQuery('#image-to-crop')[0], {
					aspectRatio: 1,
				});
				jQuery('#image-to-crop')[0].addEventListener('ready', (event) => {
					jQuery("#save-uploaded-image").prop('disabled', false);
				});
				jQuery('#image-to-crop')[0].addEventListener('crop', (event) => {
					var canvas = cropper.getCroppedCanvas(),
						resizedCanvas = resizeCanvas(canvas, 512, 512),
						dataurl = resizedCanvas.toDataURL();
					jQuery('#image-blob').val(dataurl);
				});
			}
		});

		jQuery('#save-uploaded-image').on('click', function(e) {
			e.preventDefault();
			var btn = jQuery(this),
				modal = btn.closest('.modal'),
				closebtn = modal.find('[data-dismiss="modal"]'),
				imageId = modal.attr('data-image-id'),
				fieldId = modal.attr('data-field-id');

				jQuery('#' + imageId).attr('src', jQuery('#image-blob').val());
				jQuery('#' + fieldId).val(jQuery('#image-blob').val());

				closebtn.click();
		});
	}
});

window.resizeCanvas = function( original, width, height ) {
	var canvas = document.createElement('canvas');
	canvas.width = width;
	canvas.height = height;
	var sw = getScalePercent(original.width, width),
		sh = getScalePercent(original.height, height),
		imgdata = original.getContext('2d').getImageData(0, 0, original.width, original.height),
		scaledimagedata = canvas.getContext('2d').createImageData(width, height);
		applyBilinearInterpolation(imgdata, scaledimagedata, sw);
		canvas.getContext('2d').putImageData(scaledimagedata, 0, 0);
		return canvas;
}

window.applyBilinearInterpolation = function(srcCanvasData, destCanvasData, scale) {
    function inner(f00, f10, f01, f11, x, y) {
        var un_x = 1.0 - x;
        var un_y = 1.0 - y;
        return (f00 * un_x * un_y + f10 * x * un_y + f01 * un_x * y + f11 * x * y);
    }
    var i, j;
    var iyv, iy0, iy1, ixv, ix0, ix1;
    var idxD, idxS00, idxS10, idxS01, idxS11;
    var dx, dy;
    var r, g, b, a;
    for (i = 0; i < destCanvasData.height; ++i) {
        iyv = i / scale;
        iy0 = Math.floor(iyv);
        // Math.ceil can go over bounds
        iy1 = (Math.ceil(iyv) > (srcCanvasData.height - 1) ? (srcCanvasData.height - 1) : Math.ceil(iyv));
        for (j = 0; j < destCanvasData.width; ++j) {
            ixv = j / scale;
            ix0 = Math.floor(ixv);
            // Math.ceil can go over bounds
            ix1 = (Math.ceil(ixv) > (srcCanvasData.width - 1) ? (srcCanvasData.width - 1) : Math.ceil(ixv));
            idxD = (j + destCanvasData.width * i) * 4;
            // matrix to vector indices
            idxS00 = (ix0 + srcCanvasData.width * iy0) * 4;
            idxS10 = (ix1 + srcCanvasData.width * iy0) * 4;
            idxS01 = (ix0 + srcCanvasData.width * iy1) * 4;
            idxS11 = (ix1 + srcCanvasData.width * iy1) * 4;
            // overall coordinates to unit square
            dx = ixv - ix0;
            dy = iyv - iy0;
            // I let the r, g, b, a on purpose for debugging
            r = inner(srcCanvasData.data[idxS00], srcCanvasData.data[idxS10], srcCanvasData.data[idxS01], srcCanvasData.data[idxS11], dx, dy);
            destCanvasData.data[idxD] = r;

            g = inner(srcCanvasData.data[idxS00 + 1], srcCanvasData.data[idxS10 + 1], srcCanvasData.data[idxS01 + 1], srcCanvasData.data[idxS11 + 1], dx, dy);
            destCanvasData.data[idxD + 1] = g;

            b = inner(srcCanvasData.data[idxS00 + 2], srcCanvasData.data[idxS10 + 2], srcCanvasData.data[idxS01 + 2], srcCanvasData.data[idxS11 + 2], dx, dy);
            destCanvasData.data[idxD + 2] = b;

            a = inner(srcCanvasData.data[idxS00 + 3], srcCanvasData.data[idxS10 + 3], srcCanvasData.data[idxS01 + 3], srcCanvasData.data[idxS11 + 3], dx, dy);
            destCanvasData.data[idxD + 3] = a;
        }
    }
}

window.getScalePercent = function(size, desired) {
	if ('number' !== typeof(desired) || desired <= 0) {
		desired = 512;
	}
	if ('number' !== typeof(size) || size <= 0) {
		return 1;
	}
	var fraction = desired / size;
	return fraction;
}

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