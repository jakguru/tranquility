try {
	window.rtus = [];
    window.runRTU = function( url ) {
		ajax(
			url,
			'GET',
			{},
			function(data) {
				if ('object' == typeof(data.events) && data.events.length > 0) {
					for (var i = 0; i < data.events.length; i++) {
						var e = data.events[i];
						if (-1 == jQuery.inArray(e.hash, window.rtus)) {
							window.rtus.push(e.hash);
							jQuery(window).trigger(e.type, {
								content: e.content,
								created_at: e.created_at,
								hash: e.hash,
							});
						}
					}
				}
				if ('string' == typeof(data.poll)) {
					setTimeout(function() {
						runRTU(data.poll);
					},1000);
				}
			}
		);
	}
} catch (e) {}

var notificationIndicator = function(identifier) {
	this.obj = jQuery(identifier);
	this.find = function(identifier) {
		return obj.obj.find(identifier);
	}
	this.getNotifications = function() {
		var notification_json = obj.obj.attr('items');
		try {
			notifications = JSON.parse(notification_json)
		} catch (e) {
			notifications = [];
		}
		return notifications;
	}
	this.addNotification = function(event, info) {
		notifications = obj.getNotifications();
		notifications.push(info);
		obj.obj.attr('items', JSON.stringify(notifications));
		obj.obj.find('.indicator-label').text(notifications.length);
		if ( notifications.length > 0 ) {
			obj.obj.find('.indicator-label').addClass('indicator-label-danger');
		}
	}
	this.showNotifications = function() {
		html = '';
		return 'test' + Math.floor( Math.random() * 100000 );
	}
	var obj = this;
	this.obj.popover({
		content: obj.showNotifications,
		placement: 'bottom',
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>',
		trigger: 'click',
		html: true,
	});
	jQuery(window).on('notification', obj.addNotification);
	jQuery(window).on('alert', obj.addNotification);
	jQuery( document ).on( 'mouseup', function(e) {
		var tgt = e.target,
			descby = obj.obj.attr('aria-describedby'),
			popover = ('undefined' == typeof(descby)) ? false : jQuery(sprintf('#%s', descby));
			if (
				false !== popover
				&& ! obj.obj.is(tgt)
				&& ! popover.is(tgt)
				&& 0 === popover.has(tgt).length
			) {
				obj.obj.popover('hide');
			}
	});
	return obj;
}
var ni = new notificationIndicator('#notifications-indicator');