/* Página Medios de pago: ventanas emergentes (datos bancarios, Red Link,
   instructivo de Pagomiscuentas) y botones "Copiar". */
(function () {
	var openModal = null;
	var lastTrigger = null;

	function open(name, trigger) {
		var modal = document.getElementById('cipba-modal-' + name);
		if (!modal) { return; }
		lastTrigger = trigger || null;
		modal.hidden = false;
		document.body.classList.add('cipba-modal-open');
		// El iframe de Red Link recién se carga al abrir (no en cada visita a la página).
		var frame = modal.querySelector('iframe[data-src]');
		if (frame && !frame.getAttribute('src')) { frame.setAttribute('src', frame.getAttribute('data-src')); }
		var close = modal.querySelector('.cipba-modal__close');
		if (close) { close.focus(); }
		openModal = modal;
	}

	function close() {
		if (!openModal) { return; }
		openModal.hidden = true;
		document.body.classList.remove('cipba-modal-open');
		if (lastTrigger) { lastTrigger.focus(); }
		openModal = null;
	}

	function copy(btn) {
		var text = btn.getAttribute('data-copy');
		var label = btn.querySelector('span');
		var done = function () {
			btn.classList.add('is-ok');
			if (label) { label.textContent = 'Copiado'; }
			setTimeout(function () { btn.classList.remove('is-ok'); if (label) { label.textContent = 'Copiar'; } }, 1600);
		};
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(text).then(done, done);
			return;
		}
		// Respaldo para navegadores sin API de portapapeles.
		var ta = document.createElement('textarea');
		ta.value = text;
		ta.style.position = 'fixed';
		ta.style.opacity = '0';
		document.body.appendChild(ta);
		ta.select();
		try { document.execCommand('copy'); } catch (e) { /* nada */ }
		document.body.removeChild(ta);
		done();
	}

	document.addEventListener('click', function (e) {
		var opener = e.target.closest('[data-cipba-modal]');
		if (opener) { open(opener.getAttribute('data-cipba-modal'), opener); return; }
		if (e.target.closest('[data-cipba-close]') || e.target.classList.contains('cipba-modal')) { close(); return; }
		var cp = e.target.closest('[data-copy]');
		if (cp) { copy(cp); }
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { close(); }
	});
})();
