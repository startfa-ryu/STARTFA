/**
 * FAmiCal Custom Theme - เมนูมือถือ + เงา header ตอนเลื่อน
 * Vanilla JS ไม่ต้องใช้ jQuery
 */
(function () {
	'use strict';

	var header = document.getElementById('site-header');
	var toggle = header ? header.querySelector('.menu-toggle') : null;
	var nav = document.getElementById('site-navigation');

	if (toggle && nav) {
		var setOpen = function (open) {
			header.classList.toggle('is-open', open);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		};

		toggle.addEventListener('click', function () {
			setOpen(!header.classList.contains('is-open'));
		});

		// ปิดเมนูเมื่อกด Esc หรือคลิกลิงก์ (เช่นลิงก์ #features)
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				setOpen(false);
			}
		});
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) {
				setOpen(false);
			}
		});
	}

	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}
})();
