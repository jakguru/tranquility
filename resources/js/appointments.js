window.openCreateAppointmentDialog = function(e) {
	e.preventDefault();
	jQuery.fancybox.open({
		closeExisting: true,
		type: 'html',
		src: '<p>Opened Modal</p>',
	});
}

var dashboardAppointmentManager = function(identifier) {
	this.obj = jQuery(identifier);
	this.find = function(identifier) {
		return obj.obj.find(identifier);
	}
	this.showAppointments = function() {
		html = '';
		if (obj.obj.hasClass('with-add')) {
			html += '<button class="btn btn-success btn-block add-appointment-button"><i class="far fa-calendar-plus mr-2"></i>New Appointment</button>';
		}
		return html;
	}
	var obj = this;
	this.obj.popover({
		content: obj.showAppointments,
		placement: 'bottom',
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body appointments-popover"></div></div>',
		trigger: 'click',
		html: true,
	});
	this.obj.on('inserted.bs.popover', function() {
		var id = jQuery(this).attr('aria-describedby'),
			popover = jQuery('#' + id);
			popover.find('.add-appointment-button').on('click', openCreateAppointmentDialog);
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
}

window.dam = new dashboardAppointmentManager('#appointments-button');
jQuery(function() {
	jQuery('.add-appointment-button').on('click', openCreateAppointmentDialog);
});