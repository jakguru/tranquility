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
				if ('number' == typeof(data.messages)) {
					jQuery('#messages-indicator').find('.indicator-label').text(data.messages);
					if ( data.messages > 0 ) {
						jQuery('#messages-indicator').find('.indicator-label').addClass('indicator-label-danger');
					} else {
						jQuery('#messages-indicator').find('.indicator-label').removeClass('indicator-label-danger');
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

window.playSound = function(name) {
	document.getElementById(name).play();
}

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
		var notifications = obj.getNotifications();
		notifications.push(info);
		obj.obj.attr('items', JSON.stringify(notifications));
		obj.saveToLocalStorage(JSON.stringify(notifications))
		obj.obj.find('.indicator-label').text(notifications.length);
		if ( notifications.length > 0 ) {
			obj.obj.find('.indicator-label').addClass('indicator-label-danger');
		}
		Push.create(info.content.message, {
			tag: info.hash,
			vibrate: 200,
			silent: false,
			onClick: function() {
				window.focus();
				obj.obj.popover('show');
			},
			fallback: function(payload) {
				console.log(payload);
			}
		});
		if (jQuery('.notification-popover').is(':visible')) {
			jQuery('.notification-popover').find('span.text-primary').remove();
			html = jQuery(sprintf(
				'<div class="notification-area-item alert alert-%s"><a href="%s"><span class="notification-area-item-message"><span class="%s"></span>%s</span><span class="notification-area-item-date">%s</span></a><a href="#" class="notification-area-item-dismiss" data-hash="%s" title="Dismiss Notification"><span class="fas fa-times"></span></a></div>',
				info.content.type,
				info.content.url,
				info.content.icon_class,
				info.content.message,
				info.created_at.date,
				info.hash,
			));
			dismissButton = html.find('[data-hash]');
			dismissButton.on('click', function(e) {
				e.preventDefault();
				var popover = jQuery(this).closest('.popover');
				var removed = obj.removeNotification(jQuery(this).attr('data-hash'));
				if ( true == removed ) {
					Push.close(info.hash);
					var item = jQuery(this).closest('.notification-area-item');
					item.fadeOut(300, function() {
						item.slideUp(300, function() {
							item.remove();
						});
					});
					notifications = obj.getNotifications();
					if ( 0 === notifications.length ) {
						setTimeout(function() {
							popover.find('.notification-popover').append('<span class="text-primary">You do not have any notifications.</span>');
						}, 300);
					}
				}
			});
			jQuery('.notification-popover').append(html);
		}
	}
	this.removeNotification = function(hash) {
		var notifications = obj.getNotifications(),
			filtered_notifications = [],
			success = false;
		for (var i = 0; i < notifications.length; i++) {
			if (notifications[i].hash !== hash ) {
				filtered_notifications.push(notifications[i])
			} else {
				success = true;
			}
		}
		notifications = filtered_notifications;
		obj.obj.attr('items', JSON.stringify(notifications));
		obj.saveToLocalStorage(JSON.stringify(notifications))
		obj.obj.find('.indicator-label').text(notifications.length);
		if ( notifications.length > 0 ) {
			obj.obj.find('.indicator-label').addClass('indicator-label-danger');
		} else {
			obj.obj.find('.indicator-label').removeClass('indicator-label-danger');
		}
		return success;
	}
	this.showNotifications = function() {
		html = '';
		var notifications = obj.getNotifications();
		if ( 0 === notifications.length ) {
			html += '<span class="text-primary">You do not have any notifications.</span>';
		} else {
			for (var i = 0; i < notifications.length; i++) {
				var n = notifications[i];
				html += sprintf(
					'<div class="notification-area-item alert alert-%s"><a href="%s"><span class="notification-area-item-message"><span class="%s"></span>%s</span><span class="notification-area-item-date">%s</span></a><a href="#" class="notification-area-item-dismiss" data-hash="%s" title="Dismiss Notification"><span class="fas fa-times"></span></a></div>',
					n.content.type,
					n.content.url,
					n.content.icon_class,
					n.content.message,
					n.created_at.date,
					n.hash,
				);
			}
		}
		return html;
	}
	this.saveToLocalStorage = function(json) {
		if (typeof(Storage) !== "undefined") {
			localStorage.setItem("notifications_json", json);
		}
	}
	this.getFromStorage = function() {
		if (typeof(Storage) !== "undefined") {
			return localStorage.getItem("notifications_json");
		}
		return '[]';
	}
	this.updateIconNumber = function() {
		var notifications_raw = obj.obj.attr('items'),
			notifications = ('string' == typeof(notifications_raw) && notifications_raw.length > 0 ) ? JSON.parse(notifications_raw) : [];
		obj.obj.find('.indicator-label').text(notifications.length);
		if ( notifications.length > 0 ) {
			obj.obj.find('.indicator-label').addClass('indicator-label-danger');
		} else {
			obj.obj.find('.indicator-label').removeClass('indicator-label-danger');
		}
	}
	var obj = this;
	this.obj.popover({
		content: obj.showNotifications,
		placement: 'bottom',
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body notification-popover"></div></div>',
		trigger: 'click',
		html: true,
	});
	this.obj.on('inserted.bs.popover', function() {
		var id = jQuery(this).attr('aria-describedby'),
			popover = jQuery('#' + id),
			notifications = popover.find('[data-hash]');
			notifications.on('click', function(e) {
				e.preventDefault();
				var hash = jQuery(this).attr('data-hash'),
					removed = obj.removeNotification(hash);
				if ( true == removed ) {
					Push.close(hash);
					var item = jQuery(this).closest('.notification-area-item');
					item.fadeOut(300, function() {
						item.slideUp(300, function() {
							item.remove();
						});
					});
					notifications = obj.getNotifications();
					if ( 0 === notifications.length ) {
						setTimeout(function() {
							popover.find('.notification-popover').append('<span class="text-primary">You do not have any notifications.</span>');
						}, 300);
					}
				}
			});
	});
	this.obj.attr('items', obj.getFromStorage);
	this.updateIconNumber();
	jQuery(window).on('alert', obj.addNotification);
	jQuery(window).on('login', function(event) {
		playSound('loaded');
	});
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
	if (Push.Permission.get() == Push.Permission.DEFAULT) {
		Push.Permission.request(function(){
			// on granted
		}, function() {
			// on denied
		});
	}
	return obj;
}
window.ni = new notificationIndicator('#notifications-indicator');