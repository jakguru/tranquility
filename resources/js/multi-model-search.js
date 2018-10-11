window.multiModelSearch = function(selector, debug) {
	if (jQuery(selector).length > 1) {
		var ret = [];
		jQuery(selector).each(function() {
			ret.push(new multiModelSearch(this, debug));
		})
		return ret;
	}
	// Private Properties
	var pps = {
		obj: jQuery(selector),
		field: jQuery(selector).find('[name]'),
		searching: false,
	};
	// Private Methods
	var pms = {
		getFieldName: function() {
			var name = ('undefined' == typeof(pps.field) || 'undefined' == typeof(pps.field.attr('name'))) ? '' : pps.field.attr('name');
			if ('[]' == name.slice(-2)) {
				name = name.slice(0, name.length - 2);
			}
			return name;
		},
		runWhenDoneTyping: function(field, callback, interval) {
			if ('undefined' == typeof(interval)) {
				interval = 2000;
			}
			if ('function' !== typeof(callback)) {
				callback = function() {}
			}
			field = jQuery(field);
			var timer;
			field.on('click', function(e) {
				clearTimeout(timer);
				timer = setTimeout(function() {
					callback(field);
				}, interval);
			});
			field.on('focus', function(e) {
				clearTimeout(timer);
				if (field.val().length > 0) {
					timer = setTimeout(function() {
						callback(field);
					}, interval);
				}
			});
			field.on('keyup', function(e) {
				clearTimeout(timer);
				if (e.keyCode !== 13) {
					timer = setTimeout(function() {
						callback(field);
					}, interval);
				}
			});
			field.on('keydown', function() {
				clearTimeout(timer);
			});
		},
		showChoicesDropDown: function(choices) {
			var dropdown = jQuery('<div class="choices-dropdown"></div>'),
				list = jQuery('<ul></ul>');
			if ('object' !== typeof(choices)) {
				choices = [];
			}
			if (pms.isEmail(pps.field.val())) {
				choices.push({
					type: 'email',
					value: pps.field.val().toLowerCase(),
					display: pps.field.val().toLowerCase(),
				});
			}
			choices = pms.filterOutSelectedChoices(choices);
			if ('object' == typeof(choices) && choices.length > 0) {
				for (var i = 0; i < choices.length; i++) {
					var choice = choices[i],
						cobj = jQuery('<li></li>'),
						link = jQuery('<a href="#"></a>'),
						field = jQuery(sprintf('<input type="hidden" name="%s[]" />', pps.fieldName));
						field.val(JSON.stringify(choice));
						link.data('choice', choice);
						cobj.append(field);
						link.append(sprintf('<i class="%s mr-1"></i> %s', pms.getIconForType(choice.type), choice.display));
						cobj.append(link);
						list.append(cobj);
						link.on('click', function(e) {
							e.preventDefault();
							obj.addPreselectedChoice(jQuery(this).data('choice'));
							jQuery(this).closest('li').remove();
							if (dropdown.find('li').length == 0) {
								dropdown.remove();
							}
							pps.field.val('');
							pps.field.focus();
						});
				}
			}
			dropdown.append(list);
			jQuery('body').find('.choices-dropdown').remove();
			if (list.find('li').length > 0) {
				pms.addPositioningInfoToDropdown(dropdown);
				jQuery(document).on('resize', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				jQuery(window).on('resize', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				jQuery('body').on('resize', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				pps.obj.on('resize', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				jQuery('body').on('scroll', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				pps.obj.on('scroll', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				jQuery('#app>main').on('scroll', function(e) {
					pms.addPositioningInfoToDropdown(dropdown);
				});
				jQuery('body').append(dropdown);
				jQuery( document ).on( 'mouseup', function(e) {
					var tgt = e.target;
					if (! dropdown.is(tgt) && 0 === dropdown.has(tgt).length) {
						dropdown.remove();
					}
				});
			}
		},
		isEmail: function(value) {
			return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test( value );
		},
		addPositioningInfoToDropdown: function(dropdown) {
			var offset = pps.obj.offset(),
				width = pps.obj.width(),
				height = pps.obj.height(),
				css = {
					top: (offset.top + height + 1),
					left: offset.left + 1,
					width: width - 1,
				};
				if (pps.obj.closest('.fancybox-inner').length > 0) {
					css.zIndex = 99995;
				}
				dropdown.css(css);
		},
		filterOutSelectedChoices: function(choices) {
			var filtered = [];
			for (var i = 0; i < choices.length; i++) {
				var c = choices[i],
					existing = pps.obj.find('input[type="hidden"]').filter(function() {
						return jQuery(this).val() == JSON.stringify(c);
					});
				if (existing.length == 0) {
					filtered.push(c);
				}
			}
			return filtered;
		},
		getIconForType: function(type) {
			if ('undefined' == typeof(obj.icons[type])) {
				return obj.icons.unknown;
			}
			return obj.icons[type];
		},
		runSearch: function(field) {
			if (field.val().length > 0 && false == pps.searching) {
				ajax(
					route('multi-model-search') + '?' + jQuery.param({"search": field.val()}),
					'GET',
					{},
					function(data) {
						pms.showChoicesDropDown(data);
						pps.searching = false;
					},
					function(error) {
						pms.showChoicesDropDown();
						pps.searching = false;
					},
					function() {
						pps.searching = true;
					}
				);
			}
		}
	};
	// Public Properties
	this.icons = {
		email: 'far fa-envelope',
		user: 'fas fa-user-tie',
		lead: 'far fa-id-card',
		unknown: 'fas fa-question',
	}
	// Public Methods
	this.getObject = function() {
		return pps.obj;
	}
	this.getField = function() {
		return pps.field;
	}
	this.getIconForType = function(type) {
		return pms.getIconForType(type);
	}
	this.addPreselectedChoices = function(choices) {
		for (var i = 0; i < choices.length; i++) {
			var c = JSON.parse(choices[i]);
			obj.addPreselectedChoice(c);
		}
	}
	this.addPreselectedChoice = function(choice) {
		if ('object' !== typeof(choice)) {
			return;
		}
		var input = jQuery(sprintf('<input type="hidden" name="%s[]" />', pps.fieldName));
			badge = jQuery('<span class="badge badge-dark"></span>'),
			removelink = jQuery('<a href="#" class="ml-1"><i class="fas fa-times"></i></a>');
		input.val(JSON.stringify(choice));
		removelink.on('click', function(e) {
			e.preventDefault();
			jQuery(this).closest('span.badge').remove();
		});
		badge.css({
			marginTop: pps.field.css('padding-top'),
		})
		badge.append(input);
		badge.append(sprintf('<i class="%s mr-1"></i> %s', pms.getIconForType(choice.type), choice.display));
		badge.append(removelink);
		pps.obj.find('.selected-results').append(badge);
	}
	pps.fieldName = pms.getFieldName();
	pps.field.removeAttr('name');
	pps.field.on('keyup', function(e) {
		if (e.keyCode === 13) {
			pms.runSearch(pps.field);
		}
	});
	pps.field.on('keydown', function(e) {
		if (e.keyCode === 13) {
			e.preventDefault();
		}
	});
	jQuery(window).on('keydown', function(e){
		if (e.keyCode === 13 && pps.field.is(e.target)) {
			e.preventDefault();
		}
	});
	pms.runWhenDoneTyping(pps.field, pms.runSearch);
	var obj = this;
	if (true === debug) {
		obj.pps = pps;
	}
	return obj;
}

window.mms = new multiModelSearch('.multi-model-search', false);