document.addEventListener('DOMContentLoaded', () => {
	wp.codeEditor.initialize(document.getElementById('search_custom_icon'), cm_settings);
});

class Parameter {
	get(id) {
		const field = document.getElementById(id);
		if (field.checkValidity()) {
			return field.value;
		} else {
			return false;
		}
	}

	set(id) {
		return document.getElementById(id);
	}
}

class Burger extends Parameter {
	constructor() {
		super();
		this.color = super.get('burger_color');
	}

	updateColorPreview() {
		this.color = super.get('burger_color');
		super.set('burger_color_preview').style.backgroundColor = this.color;
	}
}

class Search extends Parameter {
	constructor() {
		super();
		this.color = super.get('search_color');
	}

	updateColorPreview() {
		this.color = super.get('search_color');
		super.set('search_color_preview').style.backgroundColor = this.color;
	}
}

class Background extends Parameter {
	constructor() {
		super();
		this.fields = ['color', 'opacity', 'img'];
		this.color = super.get('back_color');
		this.opacity = super.get('back_opacity');
		this.image = super.get('back_img');
	}

	static hexToRGB = (hex, alpha = 1) => {
		const [r, g, b] = hex.match(/\w\w/g).map((x) => parseInt(x, 16));
		return `rgba(${r},${g},${b},${alpha})`;
	};

	updatePreview() {
		this.color = super.get('back_color');
		this.opacity = super.get('back_opacity');
		super.set('back_opacity_text').innerText = this.opacity * 100 + ' %';
		super.set('back_preview').style.backgroundColor = Background.hexToRGB(this.color, this.opacity);
		if (this.image !== super.get('back_img') && super.get('back_img') !== false) {
			this.image = super.get('back_img');
			super.set('back_img_preview').src = this.image;
		}
	}
}

class Logo extends Parameter {
	constructor() {
		super();
		this.image = super.get('logo');
	}

	updateImgPreview() {
		if (this.image !== super.get('logo') && super.get('logo') !== false) {
			this.image = super.get('logo');
			super.set('logo_preview').src = this.image;
		}
	}
}

const burger = new Burger();
burger.updateColorPreview();

const search = new Search();
search.updateColorPreview();

const background = new Background();
background.updatePreview();

const logo = new Logo();

const form = document.getElementById('settings-form');
const otherFields = document.querySelectorAll('.mobile-manager input:not(#back_color,#back_opacity,#back_img,#logo,#burger_color), .mobile-manager select, .mobile-manager .CodeMirror-code');
const saveButtons = document.querySelectorAll('.mobile-manager button[type="submit"]');

const beforeUnloadListener = (event) => {
	event.preventDefault();
	return (event.returnValue = 'Voulez-vous vraiment quitter la page ? Vos modifications seront perdues.');
};

const manageSave = () => {
	saveButtons.forEach((button) => {
		button.disabled = false;
	});
	addEventListener('beforeunload', beforeUnloadListener, { capture: true });
	form.addEventListener('submit', () => {
		removeEventListener('beforeunload', beforeUnloadListener, { capture: true });
	});
};

background.fields.forEach((field) => {
	document.getElementById('back_' + field).addEventListener('change', (event) => {
		if (event.currentTarget.checkValidity()) {
			background.updatePreview();
			manageSave();
		}
	});
});

document.getElementById('burger_color').addEventListener('change', (event) => {
	if (event.currentTarget.checkValidity()) {
		burger.updateColorPreview();
		manageSave();
	}
});

document.getElementById('logo').addEventListener('change', (event) => {
	if (event.currentTarget.checkValidity()) {
		logo.updateImgPreview();
		manageSave();
	}
});

otherFields.forEach((field) => {
	field.addEventListener('change', (event) => {
		if (event.currentTarget.checkValidity()) {
			manageSave();
		}
	});
});
