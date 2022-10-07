(async () => {
	// Récupération des paramètres du plugin
	const params_request = await fetch('/wp-content/plugins/kc-mobile-manager/get_settings.php');
	const params = await params_request.json();
	params.search_placeholder = 'Rechercher';
	console.log(params);

	// Récupération du menu mobile
	const menu_request = await fetch('/wp-admin/admin-ajax.php?action=kc_mobile_manager_get_menu');
	const menu_response = await menu_request.json();

	if ('error' in menu_response) {
		alert('KC Mobile Manager : ' + menu_response.error);
		throw new Error('KC Mobile Manager : ' + menu_response.error);
	}

	const template = {
		menu_mobile: '<div class="kc_mobile-manager-menu"><div class="gradient"></div></div>',
		burger: `<div class="kc_mobile-manager-burger ${params.burger_position}">
					<div class="burger-bar before"></div>
					<div class="burger-bar middle"></div>
					<div class="burger-bar after"></div>
				</div>`,
		search: `<button type="button" form="kc_mobile-manager-search-form" class="kc_mobile-manager-search ${params.search_position}">
					${params.search_custom_icon.replace(/\\/g, '')}
				</button>
				<div class="kc_mobile-manager-search-modal">
					<form role="search" id="kc_mobile-manager-search-form">
						<label>
							<span class="screen-reader-text">${params.search_placeholder}</span>
							<input type="search" class="search-field" placeholder="${params.search_placeholder}" name="s" title="${params.search_placeholder}" />
						</label>
					</form>
				</div>`,
	};

	document.body.insertAdjacentHTML(
		'beforeend',
		`
	${params.burger_position != 'shortcode' ? template.burger : ''}
	${params.search == 1 ? template.search : ''}
	${template.menu_mobile}
	`
	);

	const menu = menu_response.menu;
	const mobile_burger = document.querySelector('.kc_mobile-manager-burger');
	const burger_bar = document.querySelectorAll('.kc_mobile-manager-burger .burger-bar');
	const mobile_menu = document.querySelector('.kc_mobile-manager-menu');
	const gradient = document.querySelector('.gradient');
	const desktop_menu = params.selector_type === 'class' ? document.querySelector('.' + params.selector) : document.querySelector('#' + params.selector);
	if (params.search == 1) {
		let search_visible = false;
		const search = document.querySelector('.kc_mobile-manager-search');
		const search_modal = document.querySelector('.kc_mobile-manager-search-modal');
		const search_input = document.querySelector('#kc_mobile-manager-search-form input');

		search.addEventListener('click', (event) => {
			if (!search_visible) {
				search_modal.classList.toggle('visible');
				mobile_burger.classList.add('active');
				search_visible = true;
				search_input.focus();
				event.preventDefault();
				search.setAttribute('type', 'submit');
			}
		});

		mobile_burger.addEventListener('click', () => {
			if (search_visible) {
				search_modal.classList.remove('visible');
				search_visible = false;
				mobile_burger.classList.remove('active');
				search.setAttribute('type', 'button');
			} else {
				mobile_burger.classList.toggle('active');
				mobile_menu.classList.toggle('active');
				gradient.classList.toggle('active');
			}
		});
	} else {
		mobile_burger.addEventListener('click', () => {
			mobile_burger.classList.toggle('active');
			mobile_menu.classList.toggle('active');
			gradient.classList.toggle('active');
		});
	}

	mobile_menu.insertAdjacentHTML('beforeend', menu);
	mobile_menu.insertAdjacentHTML('afterbegin', params.content_before.replace(/\\/g, ''));
	mobile_menu.insertAdjacentHTML(
		'afterbegin',
		`<a href="/">
        	<img class="kc_logo" src="` +
			params.logo +
			`">
    	</a>`
	);
	mobile_menu.insertAdjacentHTML('beforeend', params.content_after.replace(/\\/g, ''));

	// Annimation
	document.querySelectorAll('.kc_mobile-manager-menu .menu-item-has-children').forEach((item) => {
		item.querySelector('.sub-menu').style.display = 'none';
		item.addEventListener('click', (event) => {
			if (event.currentTarget.querySelector('.sub-menu').style.display === 'none') {
				event.currentTarget.querySelector('.sub-menu').style.display = 'block';
			} else {
				event.currentTarget.querySelector('.sub-menu').style.display = 'none';
			}
		});
	});

	// Applications des paramètres de style
	gradient.style.background = params.back_color;
	gradient.style.opacity = params.back_opacity;
	burger_bar.forEach((bar) => {
		bar.style.background = params.burger_color;
	});
	mobile_menu.style.background = 'url("' + params.back_img + '") center / cover no-repeat';
	document.querySelector('.kc_mobile-manager-menu a[href="/"]').style.alignSelf = params.logo_position;

	const responsive = () => {
		if ('matchMedia' in window) {
			if (window.matchMedia('(max-width: ' + params.breakpoint + 'px)').matches) {
				desktop_menu.classList.add('kc_mobile-manager');
				mobile_burger.classList.remove('kc_mobile-manager');
			} else {
				desktop_menu.classList.remove('kc_mobile-manager');
				mobile_burger.classList.add('kc_mobile-manager');
				mobile_menu.classList.remove('active');
			}
		}
	};

	window.addEventListener('resize', responsive, false);
	responsive();
})();
