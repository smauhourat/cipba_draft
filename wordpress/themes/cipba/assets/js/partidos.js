/* Buscador de "Partidos comprendidos" (Institucional): filtra la lista en el navegador. */
(function () {
	var input = document.querySelector('[data-partidos-search]');
	if (!input) { return; }
	var items = document.querySelectorAll('.cipba-partidos-item');
	var empty = document.querySelector('[data-partidos-empty]');
	var count = document.querySelector('[data-partidos-count]');
	var total = count ? parseInt(count.getAttribute('data-total'), 10) || items.length : items.length;

	input.addEventListener('input', function () {
		var q = input.value.trim().toLowerCase();
		var shown = 0;
		items.forEach(function (el) {
			var match = !q || el.getAttribute('data-search').indexOf(q) !== -1;
			el.hidden = !match;
			if (match) { shown++; }
		});
		if (empty) { empty.hidden = shown !== 0; }
		if (count) { count.textContent = 'Mostrando ' + shown + ' de ' + total + ' partidos'; }
	});
})();
