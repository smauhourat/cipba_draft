/* Buscador del listado de subcomisiones: filtra las tarjetas en el navegador. */
(function () {
	var input = document.querySelector('[data-subcom-search]');
	if (!input) { return; }
	var cards = document.querySelectorAll('.cipba-subcom-full');
	var status = document.querySelector('[data-subcom-status]');
	var empty = document.querySelector('[data-subcom-empty]');

	input.addEventListener('input', function () {
		var raw = input.value.trim();
		var q = raw.toLowerCase();
		var shown = 0;
		cards.forEach(function (c) {
			var match = !q || c.getAttribute('data-search').indexOf(q) !== -1;
			c.hidden = !match;
			if (match) { shown++; }
		});
		empty.hidden = shown !== 0;
		status.textContent = !q ? '' : (shown === 0 ? 'Sin resultados' : shown + (shown === 1 ? ' resultado' : ' resultados') + ' para "' + raw + '"');
	});
})();
