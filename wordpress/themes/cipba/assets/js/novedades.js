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
})();
