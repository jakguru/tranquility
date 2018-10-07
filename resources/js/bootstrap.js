
window._ = require('lodash');
window.Popper = require('popper.js').default;

String.prototype.pick = function(min, max) {
    var n, chars = '';

    if (typeof max === 'undefined') {
        n = min;
    } else {
        n = min + Math.floor(Math.random() * (max - min + 1));
    }

    for (var i = 0; i < n; i++) {
        chars += this.charAt(Math.floor(Math.random() * this.length));
    }

    return chars;
};


String.prototype.shuffle = function() {
    var array = this.split('');
    var tmp, current, top = array.length;

    if (top) while (--top) {
        current = Math.floor(Math.random() * (top + 1));
        tmp = array[current];
        array[current] = array[top];
        array[top] = tmp;
    }

    return array.join('');
};

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
    require('pc-bootstrap4-datetimepicker');
    require('intl-tel-input/build/js/utils');
	window.intlTelInput = require("intl-tel-input/build/js/intlTelInput");
    window.jQuery.extend(true, window.jQuery.fn.datetimepicker.defaults, {
	    icons: {
	      time: 'far fa-clock',
	      date: 'far fa-calendar',
	      up: 'fas fa-arrow-up',
	      down: 'fas fa-arrow-down',
	      previous: 'fas fa-chevron-left',
	      next: 'fas fa-chevron-right',
	      today: 'fas fa-calendar-check',
	      clear: 'far fa-trash-alt',
	      close: 'far fa-times-circle'
	    }
	  });
    window.ClipboardJS = require('clipboard');
    require('@fancyapps/fancybox');
} catch (e) {}

try {
	window.PNotify = require('pnotify');
	window.PNotify.notice = function(options) {
		options.addclass = 'growl';
		options.icon = ('undefined' == typeof(options.icon)) ? 'fas fa-flag' : options.icon;
		return new PNotify(options);
	}
	window.PNotify.info = function(options) {
		options.addclass = 'alert alert-info';
		options.icon = 'fas fa-exclamation-circle';
		return new PNotify(options);	
	}
	window.PNotify.success = function(options) {
		options.addclass = 'alert alert-success';
		options.icon = 'fas fa-check';
		return new PNotify(options);
	}
	window.PNotify.warning = function(options) {
		options.addclass = 'alert alert-warning';
		options.icon = 'fas fa-exclamation-triangle';
		return new PNotify(options);
	}
	window.PNotify.danger = function(options) {
		options.addclass = 'alert alert-danger';
		options.icon = 'fas fa-times-circle';
		return new PNotify(options);
	}
	window.PNotify.alert = function(options) {
		options.addclass = 'growl';
		options.icon = 'fas fa-exclamation-triangle';
		return new PNotify(options);
	}
} catch (e) {
	console.warn(e);
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });


try {
	window.sprintf = require('sprintf-js').sprintf;
	window.vsprintf = require('sprintf-js').vsprintf;
}
catch (e) {}

import Modernizr from 'modernizr';

window.Modernizr = Modernizr;

window.moment = require('moment');
window['moment-timezone'] = require('moment-timezone');


require('cropperjs');

window.Swal = require('sweetalert2');
window.Uploader = require('html5-uploader');

try {
	window.ajax = function(url, method, data, success, error, pending, redirect, progress) {
	    if ( 'undefined' == typeof( url ) ) {
	        url = '';
	    }
	    if ( 'undefined' == typeof( method ) ) {
	        method = 'GET';
	    }
	    if ( 'undefined' == typeof( data ) ) {
	        data = {};
	    }
	    if ( 'undefined' == typeof( success ) ) {
	        success = function(){};
	    }
	    if ( 'undefined' == typeof( error ) ) {
	        error = function(){};
	    }
	    if ( 'undefined' == typeof( pending ) ) {
	        pending = function(){};
	    }
	    if ( 'undefined' == typeof( redirect ) ) {
	        redirect = function( location ){
	            window.location.href = location;
	        };
	    }
	    if ( 'undefined' == typeof( progress ) ) {
	        progress = function( decimal ){};
	    }
	    var fbh = function( fb ) {
	        switch ( fb.status ) {
	            case 'SUCCESS':
	                success( fb.data );
	                break;

	            case 'FAILURE':
	                error( fb.errors );
	                break;

	            case 'DEBUG':
	                console.log( fb );
	                success( fb.data );
	                break;

	            case 'REDIRECT':
	                redirect( fb.data );
	                break;
	        }
	    }
		jQuery.ajax({
			async: true,
			beforeSend: function() {
				pending();
			},
			cache: true,
			crossDomain: false,
			data: {},
			error: function(jqXHR, textStatus, errorThrown) {
				var redata = jqXHR.responseJSON;
	            if ( 'object' !== typeof( redata ) || 'undefined' == typeof( redata.status ) ) {
	                error( sprintf( 'AJAX Error: %s', errorThrown ) );
	                return;
	            }
	            fbh( redata );
			},
			headers: {
	            'Accept': 'application/json',
	        },
			method: 'GET',
			success: function(redata, textStatus, jqXHR) {
				if ( 'object' !== typeof( redata ) ) {
	                error( 'Invalid AJAX Feedback' );
	            }
	            if ( 'undefined' == typeof( redata.status ) ) {
	                error( 'Invalid AJAX Feedback' );
	            }
	            fbh( redata );
			},
			timeout: 10000,
			url: url,
			xhr: function() {
	            var xhr = new window.XMLHttpRequest();
	            var completeDouble = 0;
	            xhr.upload.addEventListener("progress", function(evt) {
	                if (evt.lengthComputable) {
	                    var decComplete = evt.loaded / evt.total;
	                    completeDouble = completeDouble + decComplete
	                    progress( completeDouble );
	                }
	            }, false );
	            xhr.addEventListener("progress", function(evt) {
	                if (evt.lengthComputable) {
	                    var decComplete = evt.loaded / evt.total;
	                    completeDouble = completeDouble + decComplete
	                    progress( completeDouble );
	                }
	            }, false );
	            return xhr;
	        },
		});   
	}
} catch (e) {}

try {
	window.Push = require('push.js');
}
catch (e) {}