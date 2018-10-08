window.openCreateAppointmentDialog = function(e) {
	e.preventDefault();
	jQuery.fancybox.open({
		closeExisting: true,
		type: 'html',
		src: sprintf(
			'<div class="container"><form class="card" action="#" method="POST" id="schedule-appointment-form"><div class="card-header bg-dark text-white"><h4>%s</h4></div><div class="card-body"><div class="form-group"><label>%s</label><input name="subject" type="text" class="form-control form-control-sm" required /></div><div class="row"><div class="col-md-6"><div class="form-group"><label>%s</label><div class="input-group"><input name="from[date]" type="date" class="form-control form-control-sm" required /><input name="from[time]" type="time" class="form-control form-control-sm" required /></div></div></div><div class="col-md-6"><div class="form-group"><label>%s</label><div class="input-group"><input name="to[date]" type="date" class="form-control form-control-sm" required /><input name="to[time]" type="time" class="form-control form-control-sm" required /></div></div></div></div><div class="form-group"><label>%s</label><input type="hidden" name="participants" /><input id="participant-display" type="text" class="form-control form-control-sm"/></div><div class="form-group"><label>%s</label><textarea name="description" class="form-control"></textarea></div></div><div class="card-footer text-right"><input type="submit" class="btn btn-success" value="%s" /></div></form></div>',
			__('Schedule an Appointment'),
			__('Subject'),
			__('Start'),
			__('Ends'),
			__('Participants'),
			__('Description'),
			__('Schedule Appointment')
		),
		afterShow: function() {
			jQuery('#schedule-appointment-form').on('submit', function(e) {
				e.preventDefault();
				Swal('Submitted!');
			});
		}
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
			html += sprintf('<button class="btn btn-success btn-block add-appointment-button"><i class="far fa-calendar-plus mr-2"></i>%s</button>', __('New Appointment'));
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