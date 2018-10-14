window.setMinimumFromDateTimeForForm = function(form) {
	var mindatetime = moment().format('YYYY-MM-DD\\THH:mm'),
		fromfield = form.find('input[name="from"]');
		fromfield.attr('min', mindatetime);
}

window.showAppointmentDialog = function(subject, start, end, participants, description)
{
	jQuery.fancybox.open({
		closeExisting: true,
		type: 'html',
		src: sprintf(
			'<div class="container"><form class="card" action="#" method="POST" id="schedule-appointment-form"><div class="card-header bg-dark text-white"><h4>%s</h4></div><div class="card-body"><div class="alert alert-info">%s</div><div class="form-group"><label>%s</label><input name="subject" type="text" class="form-control form-control-sm" required /></div><div class="row"><div class="col-md-6"><div class="form-group"><label>%s</label><input name="from" type="text" psuedo-type="datetime-local" class="form-control form-control-sm" required /></div></div><div class="col-md-6"><div class="form-group"><label>%s</label><input name="to" type="text" psuedo-type="datetime-local" class="form-control form-control-sm" required /></div></div></div><div class="form-group"><label>%s</label><div class="multi-model-search multi-model-search-sm"><div class="selected-results"></div><input type="search" name="participants" class="form-control" /></div></div><div class="form-group"><label>%s</label><textarea name="description" class="form-control twohundredtall"></textarea></div></div><div class="card-footer text-right"><a href="#" class="btn btn-secondary btn-close mr-1">%s</a><input type="submit" class="btn btn-success" value="%s" /></div></form></div>',
			__('Schedule an Appointment'),
			__('All times are in the time zone set in your preferences.'),
			__('Subject'),
			__('Start'),
			__('Ends'),
			__('Participants'),
			__('Description'),
			__('Cancel'),
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
				var newdate = e.date.add(15, 'minutes');
				tofield.data('DateTimePicker').minDate(newdate);
				if (tofield.data('DateTimePicker').date().isBefore(newdate)) {
					tofield.data('DateTimePicker').date(newdate);
				}
			})
			setMinimumFromDateTimeForForm(form);
			var recipientsField = new multiModelSearch(form.find('.multi-model-search'), false)
			if ('undefined' !== typeof(subject)) {
				form.find('[name="subject"]').val(subject);
			}
			if ('undefined' !== typeof(start)) {
				form.find('[name="start"]').val(start);
			}
			if ('undefined' !== typeof(end)) {
				form.find('[name="end"]').val(end);
			}
			if ('object' == typeof(participants)) {
				recipientsField.addPreselectedChoices(participants);
			}
			if ('undefined' !== typeof(description)) {
				form.find('[name="description"]').val(description);
			}
			form.find('.btn-close').on('click', function(e) {
				e.preventDefault();
				jQuery.fancybox.close();
			});
			form.on('submit', function(e) {
				e.preventDefault();
				var participants = [];
				form.find('[name="participants[]"]').each(function() {
					participants.push(jQuery(this).val());
				});
				ajax(
					route('create-appointment'),
					'POST',
					form.serialize(),
					function(data) {
						Swal.close();
						var html = jQuery(sprintf('<div><p>%s</p></div>', __('You may need to reload the page to view your new appointment.'))),
							viewButton = jQuery('<button class="btn btn-sm btn-dark mr-1 d-inline-block" class="button" role="button" data-action="view"></button>'),
							reloadButton = jQuery('<button class="btn btn-sm btn-dark mr-1 d-inline-block" class="button" role="button" data-action="reload"></button>'),
							closeButton = jQuery('<button class="btn btn-sm btn-success d-inline-block" class="button" role="button" data-action="close"></button>');
						viewButton.text(__('View Appointment'));
						reloadButton.text(__('Reload Current Page'));
						closeButton.text(__('OK'));
						html.append(viewButton);
						html.append(reloadButton);
						html.append(closeButton);
						Swal({
							title: __('Success'),
							type: 'success',
							showConfirmButton: false,
      						showCancelButton: false,
      						cancelButtonText: __('OK'),
							html: html,
							onOpen: function(model) {
								jQuery(model).find('[data-action="view"]').on('click', function(e) {
									window.location.href = data;
								});
								jQuery(model).find('[data-action="reload"]').on('click', function(e) {
									Swal.close();
									window.location.reload();
								});
								jQuery(model).find('[data-action="close"]').on('click', function(e) {
									Swal.close();
								});
							}
						});
					},
					function(error) {
						Swal.close();
						if ('object' == typeof(error)) {
							if ( error.length == 1 ) {
								text = error[0];
							} else {
								var text = __('Could not create your meeting due to the following errors:') + "\n";
								for (var i = 0; i < error.length; i++) {
									text += error[i] + "\n";
								}
							}
							Swal({
								title: __('Error'),
								showCancelButton: true,
								allowOutsideClick: true,
								showConfirmButton: true,
								type: 'error',
								allowEscapeKey: true,
								allowEnterKey: true,
								confirmButtonText: __('Retry'),
								text: text,
							}).then((retry) => {
								if ( true == retry.value ) {
									showAppointmentDialog(
										form.find('[name="subject"]').val(),
										form.find('[name="start"]').val(),
										form.find('[name="end"]').val(),
										participants,
										form.find('[name="description"]').val()
									);
								}
							});
						}
						else {
							Swal({
								title: __('Error'),
								showCancelButton: true,
								allowOutsideClick: true,
								showConfirmButton: true,
								type: 'error',
								allowEscapeKey: true,
								allowEnterKey: true,
								confirmButtonText: __('Retry'),
								text: __('An unknown error occured while trying to create your meeting.')
							}).then((retry) => {
								if ( true == retry.value ) {
									showAppointmentDialog(
										form.find('[name="subject"]').val(),
										form.find('[name="start"]').val(),
										form.find('[name="end"]').val(),
										participants,
										form.find('[name="description"]').val()
									);
								}
							});	
						}
					},
					function() {
						jQuery.fancybox.close();
						processingmodal = Swal({
							title: __('Processing'),
							showCancelButton: false,
							allowOutsideClick: false,
							showConfirmButton: false,
							type: 'info',
							allowEscapeKey: false,
							allowEnterKey: false,
						});
					}
				);
			});
		}
	});
}

window.openCreateAppointmentDialog = function(e) {
	e.preventDefault();
	showAppointmentDialog();
}

var dashboardAppointmentManager = function(identifier) {
	this.obj = jQuery(identifier);
	this.find = function(identifier) {
		return obj.obj.find(identifier);
	}
	this.showAppointments = function() {
		var html = '',
			appts_json = obj.obj.attr('appts-list'),
			appts_obj = ('undefined' !== typeof(appts_json)) ? JSON.parse(appts_json) : [];
		if (obj.obj.hasClass('with-add')) {
			html += sprintf('<button class="squared-corners btn btn-success btn-block add-appointment-button mb-0 mt-0 text-left"><i class="far fa-calendar-plus mr-2"></i>%s</button>', __('New Appointment'));
		}
		for (var i = 0; i < appts_obj.length; i++) {
			var a = appts_obj[i];
			html += sprintf(
				'<a href="%s" class="squared-corners btn btn-secondary btn-block mb-0 mt-0 text-left"><i class="far fa-calendar-check mr-2"></i>%s<br /><small>%s %s<br />%s %s</small></a>',
				a.url,
				a.subject,
				__('From'),
				a.start,
				__('To'),
				a.end
			);
		}
		html += sprintf('<a href="%s" class="squared-corners btn btn-primary btn-block mb-0 mt-0 text-left"><i class="far fa-calendar-alt mr-2"></i>%s</a>', route('my-calendar'), __('View Calendar'));
		return html;
	}
	var obj = this;
	this.obj.popover({
		content: obj.showAppointments,
		placement: 'bottom',
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-body squared-corners appointments-popover"></div></div>',
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