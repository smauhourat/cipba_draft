/* Consultor de partidos (home): busca en el listado de 23 partidos del
 * Distrito VII sin exigir coincidencia literal (acentos, mayúsculas,
 * abreviaturas y errores de tipeo). Ver template-parts/consultor-partidos.php. */
(function () {
	var input = document.querySelector('[data-consultor-input]');
	var resultado = document.querySelector('[data-consultor-resultado]');
	var data = document.getElementById('cipba-partidos-data');
	if (!input || !resultado || !data) { return; }

	var partidos = [];
	try { partidos = JSON.parse(data.textContent) || []; } catch (e) { partidos = []; }

	function normalizar(s) {
		return String(s)
			.normalize('NFD').replace(/\p{Diacritic}/gu, '')
			.toLowerCase()
			.replace(/[.,]/g, '')
			.replace(/\s+/g, ' ')
			.trim();
	}

	var normalizados = partidos.map(function (p) { return { nombre: p, norm: normalizar(p) }; });

	function distancia(a, b) {
		var m = a.length, n = b.length;
		var fila = [];
		for (var j = 0; j <= n; j++) { fila[j] = j; }
		for (var i = 1; i <= m; i++) {
			var prev = fila[0];
			fila[0] = i;
			for (var k = 1; k <= n; k++) {
				var temp = fila[k];
				fila[k] = a[i - 1] === b[k - 1] ? prev : 1 + Math.min(prev, fila[k], fila[k - 1]);
				prev = temp;
			}
		}
		return fila[n];
	}

	function limpiar() {
		resultado.hidden = true;
		resultado.className = 'cipba-consultor__resultado';
		resultado.textContent = '';
	}

	function pintar(clase, html) {
		resultado.hidden = false;
		resultado.className = 'cipba-consultor__resultado ' + clase;
		resultado.innerHTML = html;
	}

	function buscar(valorCrudo) {
		var q = normalizar(valorCrudo);
		if (q.length < 2) { limpiar(); return; }

		var exactos = normalizados.filter(function (p) { return p.norm === q; });
		if (exactos.length) {
			pintar('is-ok', 'Sí. <strong>' + exactos[0].nombre + '</strong> corresponde al Distrito VII.');
			return;
		}

		var parciales = normalizados.filter(function (p) { return p.norm.indexOf(q) !== -1 || q.indexOf(p.norm) !== -1; });
		if (parciales.length) {
			var nombres = parciales.map(function (p) { return p.nombre; }).join(', ');
			pintar('is-ok', 'Sí. <strong>' + nombres + '</strong> corresponde' + (parciales.length > 1 ? 'n' : '') + ' al Distrito VII.');
			return;
		}

		var mejor = null;
		normalizados.forEach(function (p) {
			var d = distancia(q, p.norm);
			if (!mejor || d < mejor.d) { mejor = { p: p, d: d }; }
		});
		var umbral = Math.max(1, Math.floor(mejor.p.norm.length * 0.3));
		if (mejor.d <= umbral) {
			pintar('is-sugerencia', '¿Quisiste decir <strong>' + mejor.p.nombre + '</strong>? Si es así, sí corresponde al Distrito VII.');
			return;
		}

		pintar('is-no', 'No encontramos "' + valorCrudo.trim() + '" entre los 23 partidos del Distrito VII. Revisá el listado completo.');
	}

	var t = null;
	input.addEventListener('input', function () {
		clearTimeout(t);
		var valor = input.value;
		t = setTimeout(function () { buscar(valor); }, 200);
	});
})();
