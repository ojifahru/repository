import './bootstrap';

const drawers = new Map();

function setDrawerOpen(name, open) {
	const drawer = drawers.get(name);
	if (!drawer) {
		return;
	}

	if (open) {
		drawer.classList.remove('hidden');
		document.body.classList.add('overflow-hidden');

		const firstFocusable = drawer.querySelector(
			'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
		);
		if (firstFocusable) {
			firstFocusable.focus();
		}
	} else {
		drawer.classList.add('hidden');
		document.body.classList.remove('overflow-hidden');
	}
}

function initDrawers() {
	document.querySelectorAll('[data-drawer]').forEach((el) => {
		drawers.set(el.getAttribute('data-drawer'), el);
	});

	document.querySelectorAll('[data-drawer-open]').forEach((btn) => {
		btn.addEventListener('click', () => {
			setDrawerOpen(btn.getAttribute('data-drawer-open'), true);
		});
	});

	document.querySelectorAll('[data-drawer-close]').forEach((btn) => {
		btn.addEventListener('click', () => {
			setDrawerOpen(btn.getAttribute('data-drawer-close'), false);
		});
	});

	document.addEventListener('keydown', (e) => {
		if (e.key !== 'Escape') {
			return;
		}

		for (const [name, drawer] of drawers.entries()) {
			if (!drawer.classList.contains('hidden')) {
				setDrawerOpen(name, false);
				break;
			}
		}
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initDrawers);
} else {
	initDrawers();
}
