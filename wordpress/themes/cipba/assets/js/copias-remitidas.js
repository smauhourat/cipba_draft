/* Copias remitidas: zona para arrastrar el PDF, nombre y peso del archivo
   elegido, chequeo de tipo/tamaño antes de enviar y botón "Enviando…". */
(function () {
	function formatSize(bytes) {
		return bytes >= 1048576 ? (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
	}

	function init() {
		var form = document.querySelector('.cipba-copias__card');
		if (!form) { return; }
		var input = form.querySelector('#cr_archivo');
		var drop = form.querySelector('.cipba-copias__drop');
		var empty = drop.querySelector('.cipba-copias__drop-empty');
		var chosen = drop.querySelector('.cipba-copias__drop-file');
		var err = form.querySelector('.cipba-copias__drop-error');
		var btn = form.querySelector('.cipba-copias__btn');
		var max = parseInt(form.getAttribute('data-max'), 10) || 0;

		function check() {
			var f = input.files && input.files[0];
			var msg = '';
			if (f && !/\.pdf$/i.test(f.name)) {
				msg = 'El archivo tiene que ser un PDF.';
			} else if (f && max && f.size > max) {
				msg = 'El archivo pesa ' + formatSize(f.size) + '; el máximo es ' + form.getAttribute('data-max-label') + '.';
			}
			input.setCustomValidity(msg);
			err.textContent = msg;
			err.hidden = !msg;
			drop.classList.toggle('is-invalid', !!msg);
			drop.classList.toggle('has-file', !!f && !msg);
			empty.hidden = !!f && !msg;
			chosen.hidden = !f || !!msg;
			if (f) {
				chosen.querySelector('.cipba-copias__drop-name').textContent = f.name;
				chosen.querySelector('.cipba-copias__drop-size').textContent = formatSize(f.size);
			}
		}

		input.addEventListener('change', check);

		// Avisos de campos faltantes o inválidos en español (los del navegador
		// salen en el idioma del navegador).
		var MSGS = {
			cr_nombre: { missing: 'Completá tu apellido y nombre.' },
			cr_email: { missing: 'Completá tu mail.', type: 'Ingresá un mail válido, por ejemplo nombre@correo.com.' },
			cr_matricula: { missing: 'Completá tu número de matrícula.' },
			cr_archivo: { missing: 'Adjuntá el contrato firmado en PDF.' }
		};
		Object.keys(MSGS).forEach(function (id) {
			var el = form.querySelector('#' + id);
			if (!el) { return; }
			el.addEventListener('invalid', function () {
				if (el.validity.valueMissing) {
					el.setCustomValidity(MSGS[id].missing);
				} else if (el.validity.typeMismatch && MSGS[id].type) {
					el.setCustomValidity(MSGS[id].type);
				}
			});
			if (el !== input) {
				el.addEventListener('input', function () { el.setCustomValidity(''); });
			}
		});

		['dragenter', 'dragover'].forEach(function (ev) {
			drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('is-over'); });
		});
		['dragleave', 'dragend', 'drop'].forEach(function (ev) {
			drop.addEventListener(ev, function () { drop.classList.remove('is-over'); });
		});
		drop.addEventListener('drop', function (e) {
			e.preventDefault();
			if (e.dataTransfer && e.dataTransfer.files.length) {
				var dt = new DataTransfer();
				dt.items.add(e.dataTransfer.files[0]);
				input.files = dt.files;
				check();
			}
		});

		form.addEventListener('submit', function () {
			btn.disabled = true;
			btn.querySelector('span').textContent = 'Enviando…';
		});
		// Al volver con "Atrás" el navegador puede restaurar el botón deshabilitado.
		window.addEventListener('pageshow', function () {
			btn.disabled = false;
			btn.querySelector('span').textContent = 'Enviar copia';
		});
	}

	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
