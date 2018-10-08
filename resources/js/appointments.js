window.setMinimumFromDateTimeForForm = function(form) {
	var mindatetime = moment().format('YYYY-MM-DD\\THH:mm'),
		fromfield = form.find('input[name="from"]');
		fromfield.attr('min', mindatetime);
}

window.openCreateAppointmentDialog = function(e) {
	e.preventDefault();
	jQuery.fancybox.open({
		closeExisting: true,
		type: 'html',
		src: sprintf(
			'<div class="container"><form class="card" action="#" method="POST" id="schedule-appointment-form"><div class="card-header bg-dark text-white"><h4>%s</h4></div><div class="card-body"><div class="form-group"><label>%s</label><input name="subject" type="text" class="form-control form-control-sm" required /></div><div class="row"><div class="col-md-6"><div class="form-group"><label>%s</label><input name="from" type="text" psuedo-type="datetime-local" class="form-control form-control-sm" required /></div></div><div class="col-md-6"><div class="form-group"><label>%s</label><input name="to" type="text" psuedo-type="datetime-local" class="form-control form-control-sm" required /></div></div></div><div class="form-group"><label>%s</label><input type="hidden" name="participants" /><input id="participant-display" type="text" class="form-control form-control-sm"/></div><div class="form-group"><label>%s</label><textarea name="description" class="form-control twohundredtall"></textarea></div></div><div class="card-footer text-right"><input type="submit" class="btn btn-success" value="%s" /></div></form></div>',
			__('Schedule an Appointment'),
			__('Subject'),
			__('Start'),
			__('Ends'),
			__('Participants'),
			__('Description'),
			__('Schedule Appointment')
		),
		afterShow: function() {
			var form = jQuery('#schedule-appointment-form'),
				fromfield = form.find('input[name="from"]'),
				tofield = form.find('input[name="to"]');
			form.find('[psuedo-type="datetime-local"]').each(function() {
				var getdefaultdate = function() {
					var start = moment(),
						remainder = 15 - (start.minute() % 15),
						ret = moment(start).add(remainder, 'minutes');
					return ret;
				};
				var dd = getdefaultdate();
				if ('to' == jQuery(this).attr('name')) {
					dd.add(15, 'minutes');
				}
				jQuery(this).datetimepicker({
					showClear: false,
					showClose: false,
					showTodayButton: false,
					useCurrent: false,
					defaultDate: dd,
					minDate: moment(),
					sideBySide: false,
					stepping: 15,
				});
			});
			fromfield.on('dp.change', function(e) {
				tofield.data('DateTimePicker').minDate(e.date.add(15, 'minutes'));
			})
			setMinimumFromDateTimeForForm(form);
			form.on('submit', function(e) {
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