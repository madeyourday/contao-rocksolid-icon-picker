;(function($, window) {

var open = function(iconsWrap) {

	var ctrlElement = $('ctrl_' + iconsWrap.substr(10));
	var iconElement = $('rip_selected_' + iconsWrap.substr(10));
	var searchElement = $('rip_search_' + iconsWrap.substr(10));
	iconsWrap = $(iconsWrap);
	var closeElement = iconsWrap.getElements('.rip_icons_toolbar_close')[0];

	if (!iconsWrap.hasClass('rip_collapsed')) {
		return;
	}

	var updateSearch = function(event){
		if (event && event.key === 'enter' && event.preventDefault) {
			event.preventDefault();
		}
		var value = this.get('value').replace(/[^a-z0-9_-]/gi, '');
		if (value) {
			var searchRegExp = new RegExp(value.split('').join('.*?'), 'i');
			iconsWrap.getChildren('a').each(function(el){
				if (el.get('data-name') && searchRegExp.test(el.get('data-name'))) {
					el.setStyle('display', '');
				}
				else {
					el.setStyle('display', 'none');
				}
			});
		}
		else {
			iconsWrap.getChildren('a').each(function(el){
				el.setStyle('display', '');
			});
		}
	};

	if (searchElement) {
		searchElement
			.addEvent('keydown', updateSearch)
			.addEvent('keyup', updateSearch)
			.addEvent('click', updateSearch)
			.addEvent('change', updateSearch);
	}

	var close = function(event) {
		if (event && event.preventDefault) {
			event.preventDefault();
		}
		iconsWrap.addClass('rip_collapsed');
		closeElement.removeEvent('close', close);
		if (searchElement) {
			searchElement
				.removeEvent('keydown', updateSearch)
				.removeEvent('keyup', updateSearch)
				.removeEvent('click', updateSearch)
				.removeEvent('change', updateSearch);
		}
		iconsWrap.getElements('[data-code]').destroy();
	};

	closeElement.addEvent('click', close);

	var icons = JSON.decode(
		iconsWrap.getFirst('[data-rip-codes]').get('data-rip-codes')
	);

	var elements = [];

	var clickIcon = function(event) {
		if (event && event.preventDefault) {
			event.preventDefault();
		}
		ctrlElement.set('value', this.get('data-code'));
		iconElement.set('html', this.get('html'));
		close();
	};

	icons.each(function(icon) {
		elements.push(new Element('a', {
			'href': '',
			'data-code': icon.code,
			'class': 'rip_icon',
			'html': icon.code ? '&#x' + icon.code + ';' : '',
			events: {click: clickIcon}
		}));
		if (icon.name) {
			elements.getLast().set('data-name', icon.name);
		}
	});

	iconsWrap.adopt(elements);

	iconsWrap.removeClass('rip_collapsed');

};

// public objects
window.ripOpen = open;

})(document.id, window);
