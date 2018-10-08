var multiModelSearch = function(selector, debug) {
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
			field.on('keyup', function() {
				clearTimeout(timer);
				timer = setTimeout(function() {
					callback(field);
				}, interval);
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
			choices.push({
				type: 'email',
				value: pps.field.val(),
				display: pps.field.val(),
				icon: 'far fa-envelope',
			});
			if ('object' == typeof(choices) && choices.length > 0) {
				for (var i = 0; i < choices.length; i++) {
					var choice = choices[i],
						cobj = jQuery('<li></li>'),
						link = jQuery('<a href="#"></a>'),
						field = jQuery(sprintf('<input type="hidden" name="%s[]" value="%s.%s" />', pps.fieldName, choice.type, choice.value));
						console.log(choice);
						cobj.append(field);
						link.append(sprintf('<i class="%s mr-1"></i> %s', choice.icon, choice.display));
						cobj.append(link);
						list.append(cobj);
				}
			}
			dropdown.append(list);
			pps.obj.find('.choices-dropdown').remove();
			pps.obj.append(dropdown);
		}
	};
	// Public Properties
	// Public Methods
	this.getObject = function() {
		return pps.obj;
	}
	this.getField = function() {
		return pps.field;
	}
	pps.fieldName = pms.getFieldName();
	pps.field.removeAttr('name');
	pms.runWhenDoneTyping(pps.field, function(field) {
		ajax(
			'/multi-model-search',
			'GET',
			{"search": field.val()},
			function(data) {
				pms.showChoicesDropDown(data);
			},
			function(error) {
				pms.showChoicesDropDown();
			}
		);
	});
	var obj = this;
	if (true === debug) {
		obj.pps = pps;
	}
	return obj;
}

window.mms = new multiModelSearch('.multi-model-search', false);