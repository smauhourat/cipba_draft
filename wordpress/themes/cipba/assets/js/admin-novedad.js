/* Edición de una entrada: la caja "Datos de la actividad" solo se muestra si
   está tildado "Es una actividad con fecha". El editor de bloques arma las cajas
   de campos después de cargar, por eso se espera a que existan. */
(function () {
	function init() {
		var check = document.querySelector('input[name="es_actividad"]');
		var box = document.getElementById('datos-de-la-actividad');
		if (!check || !box) { return false; }
		var sync = function () { box.style.display = check.checked ? '' : 'none'; };
		check.addEventListener('change', sync);
		sync();
		return true;
	}

	var tries = 0;
	var timer = setInterval(function () {
		if (init() || ++tries > 60) { clearInterval(timer); }
	}, 250);
})();
