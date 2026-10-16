/* Eventos y novedades: filtro por categoría + buscador del listado, y el botón
   "Copiar enlace" del detalle. */
(function () {
	/* ----- Listado ----- */
	var root = document.querySelector('.cipba-nov');
	if (root) {
		var chips = root.querySelectorAll('[data-nov-cat]');
		var input = root.querySelector('[data-nov-search]');
		var items = root.querySelectorAll('[data-nov-item]');
		var count = root.querySelector('[data-nov-count]');
		var empty = root.querySelector('[data-nov-empty]');
		var cat = '';
		var catName = '';

		var apply = function () {
			var term = input.value.trim().toLowerCase();
			var shown = 0;
			items.forEach(function (el) {
				var ok = (!cat || el.getAttribute('data-cat') === cat) &&
					(!term || el.getAttribute('data-search').indexOf(term) !== -1);
				el.hidden = !ok;
				if (ok) { shown++; }
			});
			count.textContent = shown + (shown === 1 ? ' publicación' : ' publicaciones') + (cat ? ' en ' + catName : '');
			empty.hidden = shown !== 0;
		};

		chips.forEach(function (chip) {
			chip.addEventListener('click', function () {
				chips.forEach(function (c) { c.classList.remove('is-active'); });
				chip.classList.add('is-active');
				cat = chip.getAttribute('data-nov-cat');
				catName = chip.getAttribute('data-nov-name') || '';
				apply();
			});
		});
		input.addEventListener('input', apply);
	}

	/* ----- Detalle: copiar enlace ----- */
	var copy = document.querySelector('[data-copy-url]');
	if (copy) {
		copy.addEventListener('click', function () {
			var url = copy.getAttribute('data-copy-url');
			var label = copy.querySelector('span');
			var done = function () {
				copy.classList.add('is-ok');
				if (label) { label.textContent = 'Copiado'; }
				setTimeout(function () { copy.classList.remove('is-ok'); if (label) { label.textContent = 'Copiar enlace'; } }, 1800);
			};
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done, done);
				return;
			}
			var ta = document.createElement('textarea');
			ta.value = url;
			ta.style.position = 'fixed';
			ta.style.opacity = '0';
			document.body.appendChild(ta);
			ta.select();
			try { document.execCommand('copy'); } catch (e) { /* nada */ }
			document.body.removeChild(ta);
			done();
		});
	}

	/* ----- Detalle: galería de fotos (visor) ----- */
	var gal = document.querySelector('[data-galeria]');
	if (gal) {
		var fotos = Array.prototype.slice.call(gal.querySelectorAll('[data-galeria-item]'));
		var ico = function (d) { return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + d + '</svg>'; };
		var chev = ico('<polyline points="9 18 15 12 9 6"/>');
		var lb = document.createElement('div');
		lb.className = 'cipba-lb';
		lb.hidden = true;
		lb.setAttribute('role', 'dialog');
		lb.setAttribute('aria-modal', 'true');
		lb.setAttribute('aria-label', 'Galería de fotos');
		lb.innerHTML = '<span class="cipba-lb__count"></span>' +
			'<figure class="cipba-lb__fig"><img alt=""><figcaption class="cipba-lb__cap"></figcaption></figure>' +
			'<button type="button" class="cipba-lb__btn cipba-lb__close" aria-label="Cerrar">' + ico('<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>') + '</button>' +
			'<button type="button" class="cipba-lb__btn cipba-lb__prev" aria-label="Foto anterior">' + chev + '</button>' +
			'<button type="button" class="cipba-lb__btn cipba-lb__next" aria-label="Foto siguiente">' + chev + '</button>';
		document.body.appendChild(lb);
		var img = lb.querySelector('img');
		var cap = lb.querySelector('.cipba-lb__cap');
		var cnt = lb.querySelector('.cipba-lb__count');
		var btnClose = lb.querySelector('.cipba-lb__close');
		var multi = fotos.length > 1;
		lb.querySelector('.cipba-lb__prev').hidden = !multi;
		lb.querySelector('.cipba-lb__next').hidden = !multi;
		var actual = 0;
		var origen = null;

		var mostrar = function (i) {
			actual = (i + fotos.length) % fotos.length;
			var a = fotos[actual];
			var thumb = a.querySelector('img');
			img.src = a.getAttribute('href');
			img.alt = thumb ? thumb.alt : '';
			cap.textContent = a.getAttribute('data-caption') || '';
			cap.hidden = !cap.textContent;
			cnt.textContent = multi ? (actual + 1) + ' / ' + fotos.length : '';
		};
		var cerrar = function () {
			lb.hidden = true;
			document.documentElement.style.overflow = '';
			img.removeAttribute('src');
			if (origen) { origen.focus(); }
		};

		fotos.forEach(function (a, i) {
			a.addEventListener('click', function (e) {
				e.preventDefault();
				origen = a;
				mostrar(i);
				lb.hidden = false;
				document.documentElement.style.overflow = 'hidden';
				btnClose.focus();
			});
		});
		lb.querySelector('.cipba-lb__prev').addEventListener('click', function () { mostrar(actual - 1); });
		lb.querySelector('.cipba-lb__next').addEventListener('click', function () { mostrar(actual + 1); });
		btnClose.addEventListener('click', cerrar);
		lb.addEventListener('click', function (e) { if (e.target === lb) { cerrar(); } });
		document.addEventListener('keydown', function (e) {
			if (lb.hidden) { return; }
			if (e.key === 'Escape') { cerrar(); }
			else if (e.key === 'ArrowLeft' && multi) { mostrar(actual - 1); }
			else if (e.key === 'ArrowRight' && multi) { mostrar(actual + 1); }
		});
		/* Deslizar en celulares. */
		var x0 = null;
		lb.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		lb.addEventListener('touchend', function (e) {
			if (x0 === null || !multi) { return; }
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 50) { mostrar(actual + (dx < 0 ? 1 : -1)); }
			x0 = null;
		});
	}
})();
