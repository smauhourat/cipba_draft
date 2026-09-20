/* Formulario de Contacto: el mensaje de éxito o de error que Fluent Forms
   agrega debajo del botón "Enviar" se desvanece solo a los pocos segundos. */
(function () {
	var HIDE_AFTER = 6000; // ms visible
	var FADE = 400;        // ms de desvanecimiento (coincide con el CSS)
	var SELECTOR = '.ff-message-success, .ff-errors-in-stack';

	function schedule(el) {
		if (el.__cipbaTimer) { return; }
		el.__cipbaTimer = setTimeout(function () {
			el.classList.add('is-hiding');
			setTimeout(function () { if (el.parentNode) { el.parentNode.removeChild(el); } }, FADE);
		}, HIDE_AFTER);
	}

	function scan(root) {
		if (root.matches && root.matches(SELECTOR)) { schedule(root); }
		if (root.querySelectorAll) { root.querySelectorAll(SELECTOR).forEach(schedule); }
	}

	function init() {
		var box = document.querySelector('.cipba-contact-form');
		if (!box) { return; }
		new MutationObserver(function (muts) {
			muts.forEach(function (m) {
				m.addedNodes.forEach(function (n) { if (n.nodeType === 1) { scan(n); } });
			});
		}).observe(box, { childList: true, subtree: true });
	}

	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
})();
