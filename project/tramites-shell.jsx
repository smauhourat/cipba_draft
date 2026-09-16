const { useState, useEffect } = React;

/* ─── ICONS ─── */
const Icon = ({ name, size = 20, color = "currentColor" }) => {
  const icons = {
    menu: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>,
    copy: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>,
    close: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>,
    phone: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.01 1.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92v2z"/></svg>,
    mail: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>,
    location: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>,
    shield: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>,
    award: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>,
    clock: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>,
    chevronRight: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2.5"><polyline points="9 18 15 12 9 6"/></svg>,
    chevronDown: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2.5"><polyline points="6 9 12 15 18 9"/></svg>,
    externalLink: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>,
    home: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>,
    users: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>,
    whatsapp: <svg width={size} height={size} viewBox="0 0 24 24" fill={color}><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>,
    building: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg>,
    calendar: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>,
    dollar: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>,
    file: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>,
    check: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>,
    search: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>,
    instagram: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.1" fill={color} stroke="none"/></svg>,
    facebook: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>,
    linkedin: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-4 0v7h-4V9h4v1.5A6 6 0 0116 8z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>,
    map: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><polygon points="1 6 8 3 16 6 23 3 23 18 16 21 8 18 1 21"/><line x1="8" y1="3" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="21"/></svg>,
    scale: <svg width={size} height={size} viewBox="0 0 24 24" fill="none" stroke={color} strokeWidth="2"><path d="M12 3v18M5 7l-3 8a4 4 0 008 0l-3-8M19 7l-3 8a4 4 0 008 0l-3-8M5 7h6M13 7h6M5 21h14"/></svg>,
  };
  return icons[name] || null;
};

/* ─── TOP BAR ─── */
const HeaderStyles = () => (
  <style>{`
    @media (max-width: 1160px) { .desktop-nav { display: none !important; } .mobile-burger { display: flex !important; } }
    @media (max-width: 700px) { .tb-hours, .tb-cs { display: none !important; } }
  `}</style>
);

const TopBar = () => (
  <div style={{ background: '#14484a', color: 'rgba(255,255,255,0.85)', fontSize: 12.5, borderBottom: '1px solid rgba(255,255,255,0.1)' }}>
    <div style={{ maxWidth: 1200, margin: '0 auto', padding: '7px 24px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: 8 }}>
      <div style={{ display: 'flex', gap: 22, alignItems: 'center', flexWrap: 'wrap' }}>
        <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}><Icon name="phone" size={12}/> (011) 4651-0064</span>
        <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}><Icon name="mail" size={12}/> info@cipba.org</span>
        <span className="tb-hours" style={{ display: 'flex', alignItems: 'center', gap: 6 }}><Icon name="clock" size={12}/> Lun–Vie 9 a 16 h (San Justo)</span>
      </div>
      <div style={{ display: 'flex', gap: 18, alignItems: 'center' }}>
        <a href="#" style={{ display: 'flex', alignItems: 'center', gap: 5, opacity: .8 }} onMouseEnter={e=>e.currentTarget.style.opacity=1} onMouseLeave={e=>e.currentTarget.style.opacity=.8}>
          <Icon name="whatsapp" size={13} color="white"/> WhatsApp
        </a>
        <a href="http://www.colegioingenieros.org.ar/" target="_blank" className="tb-cs" style={{ display: 'flex', alignItems: 'center', gap: 4, opacity: .8 }} onMouseEnter={e=>e.currentTarget.style.opacity=1} onMouseLeave={e=>e.currentTarget.style.opacity=.8}>
          Consejo Superior <Icon name="externalLink" size={11} color="white"/>
        </a>
      </div>
    </div>
  </div>
);

/* ─── NAVBAR ─── */
const navLinks = [
  { label: 'Inicio', href: 'index.html' },
  { label: 'Trámites', href: 'index.html#tramites', groups: [
    { title: 'Matrícula', items: [
      { label: 'Inscripción', href: 'inscripcion.html' },
      { label: 'Rehabilitación', href: 'rehabilitacion.html' },
      { label: 'Baja', href: 'baja.html' },
      { label: 'Solicitud de credenciales', href: 'solicitud-credenciales.html' },
      { label: 'Medios de pago', href: 'pago-matricula.html' },
    ] },
    { title: 'Sistemas', items: [
      { label: 'Visado online', href: '#', ext: true },
      { label: 'SIGMA', href: '#', ext: true },
      { label: 'Otros trámites', href: '#', ext: true },
    ] },
  ] },
  { label: 'Normativa', href: 'normativa.html', groups: [
    { title: 'Marco legal', items: [
      { label: 'Ley Colegial 10.416', href: 'docs/ley-10416.pdf' },
      { label: 'Ley Previsional 12.490', href: 'docs/ley-previsional-12490.pdf' },
      { label: 'Código de Ética', href: 'docs/codigo-de-etica.pdf' },
      { label: 'Reglamento Interno', href: 'docs/reglamento-interno.pdf' },
      { label: 'Incumbencias', href: 'http://www.colegioingenieros.org.ar/incumbencias/', ext: true },
      { label: 'Ver toda la normativa', href: 'normativa.html' },
    ] },
    { title: 'Consulta', items: [
      { label: 'Resoluciones', href: 'http://www.colegioingenieros.org.ar/category/resoluciones/', ext: true },
      { label: 'Honorarios mínimos vigentes', href: 'honorarios.html' },
      { label: 'Modelos de contrato', href: '#' },
      { label: 'Vademécum', href: '#', ext: true },
    ] },
  ] },
  { label: 'Novedades', href: 'novedades.html' },
  { label: 'Institucional', href: 'institucional.html', groups: [
    { items: [
      { label: 'Subcomisiones', href: 'subcomisiones.html' },
      { label: 'Autoridades', href: 'institucional.html#autoridades' },
      { label: 'Partidos del Distrito', href: 'institucional.html#partidos' },
      { label: 'Sedes y delegaciones', href: 'institucional.html#sedes' },
    ] },
  ] },
  { label: 'Contacto', href: 'contacto.html' },
];
const PAGE = (typeof location !== 'undefined' ? (location.pathname.split('/').pop() || 'index.html') : '');
const isActive = (href) => !!href && !href.startsWith('#') && href.split('#')[0] === PAGE;

const Navbar = ({ scrolled }) => {
  const [open, setOpen] = useState(false);
  const [activeMenu, setActiveMenu] = useState(null);
  const [search, setSearch] = useState(false);

  return (
    <nav style={{
      position: 'sticky', top: 0, zIndex: 100,
      background: scrolled ? 'rgba(255,255,255,0.97)' : '#ffffff',
      backdropFilter: 'blur(8px)',
      boxShadow: scrolled ? '0 2px 18px rgba(13,50,52,0.14)' : 'none',
      transition: 'all 0.3s ease',
      borderBottom: '3px solid #00a48a',
    }}>
      <div style={{ maxWidth: 1200, margin: '0 auto', padding: '0 24px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', height: 66, gap: 16 }}>
        <a href="index.html" style={{ display: 'flex', alignItems: 'center', flexShrink: 0 }}>
          <img src="uploads/logo-cipba-navbar.png" alt="CIPBA" style={{ height: 34, width: 'auto' }}/>
          <span style={{ marginLeft: 10, paddingLeft: 10, borderLeft: '1px solid #c0e2ca', fontFamily: "'Roboto Condensed', sans-serif", fontSize: 16, fontWeight: 900, color: '#14484a', lineHeight: 1, whiteSpace: 'nowrap' }}>DISTRITO VII</span>
        </a>

        <div style={{ display: 'flex', gap: 2, alignItems: 'center' }} className="desktop-nav">
          {navLinks.map(link => (
            <div key={link.label} style={{ position: 'relative' }}
              onMouseEnter={() => setActiveMenu(link.label)} onMouseLeave={() => setActiveMenu(null)}>
              <a href={link.href} style={{
                color: isActive(link.href) ? '#008c75' : '#14484a', fontSize: 14, fontWeight: isActive(link.href) ? 800 : 600, padding: '9px 13px',
                display: 'flex', alignItems: 'center', gap: 4, borderRadius: 4, transition: 'all .2s',
                background: activeMenu === link.label || isActive(link.href) ? '#e8f5f1' : 'transparent',
              }}>
                {link.label}
                {link.groups && <Icon name="chevronDown" size={12} color="#607a7c"/>}
              </a>
              {link.groups && activeMenu === link.label && (
                <div style={{
                  position: 'absolute', top: '100%', left: 0, minWidth: 250,
                  background: 'white', boxShadow: '0 12px 34px rgba(13,50,52,0.18)',
                  borderRadius: 8, overflow: 'hidden', border: '1px solid #e8f5f1',
                  borderTop: '3px solid #00a48a', padding: '8px 0',
                }}>
                  {link.groups.map((g, gi) => (
                    <div key={gi} style={{ paddingTop: gi ? 8 : 0, marginTop: gi ? 6 : 0, borderTop: gi ? '1px solid #eef2f1' : 'none' }}>
                      {g.title && <div style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: 1.4, textTransform: 'uppercase', color: '#9aa8a9', padding: '6px 18px 7px' }}>{g.title}</div>}
                      {g.items.map(s => (
                        <a key={s.label} href={s.href} target={s.ext || /\.pdf$/i.test(s.href || '') ? '_blank' : undefined} rel={s.ext || /\.pdf$/i.test(s.href || '') ? 'noopener' : undefined} style={{
                          display: 'flex', alignItems: 'center', gap: 6, padding: '9px 18px', fontSize: 13.5,
                          color: '#373d3e', transition: 'background .15s, color .15s',
                        }}
                        onMouseEnter={e => { e.currentTarget.style.background = '#e8f5f1'; e.currentTarget.style.color = '#14484a'; }}
                        onMouseLeave={e => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = '#373d3e'; }}>
                          {s.label}{s.ext && <Icon name="externalLink" size={11} color="#00a48a"/>}
                        </a>
                      ))}
                    </div>
                  ))}
                </div>
              )}
            </div>
          ))}

          <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginLeft: 10 }}>
            {search
              ? <input autoFocus placeholder="Buscar en el sitio…" onBlur={() => setSearch(false)} style={{ width: 170, padding: '8px 12px', fontFamily: 'inherit', fontSize: 13, border: '1px solid #c0e2ca', borderRadius: 20, outline: 'none', color: '#373d3e' }}/>
              : <button aria-label="Buscar" onClick={() => setSearch(true)} style={{ background: 'none', width: 34, height: 34, borderRadius: '50%', display: 'grid', placeItems: 'center', transition: 'background .2s' }}
                  onMouseEnter={e => e.currentTarget.style.background = '#e8f5f1'} onMouseLeave={e => e.currentTarget.style.background = 'transparent'}>
                  <Icon name="search" size={17} color="#14484a"/>
                </button>}
            <a href="index.html#tramites" style={{ display: 'flex', alignItems: 'center', gap: 6, background: '#14484a', color: 'white', fontSize: 13.5, fontWeight: 700, padding: '10px 18px', borderRadius: 6, transition: 'background .2s', whiteSpace: 'nowrap' }}
              onMouseEnter={e => e.currentTarget.style.background = '#0d3234'} onMouseLeave={e => e.currentTarget.style.background = '#14484a'}>
              Matricularse <Icon name="externalLink" size={12} color="white"/>
            </a>
          </div>
        </div>

        <button onClick={() => setOpen(!open)} style={{ background: 'none', color: '#14484a', display: 'none' }} className="mobile-burger">
          <Icon name={open ? 'close' : 'menu'} size={26}/>
        </button>
      </div>

      {open && (
        <div style={{ background: 'white', borderTop: '1px solid #e8f5f1', padding: '6px 0 18px', maxHeight: '75vh', overflowY: 'auto' }}>
          {navLinks.map(link => (
            <div key={link.label}>
              <a href={link.href} onClick={() => setOpen(false)} style={{ display: 'block', padding: '12px 24px', color: '#14484a', fontSize: 15, fontWeight: 700, borderBottom: '1px solid #eef2f1' }}>{link.label}</a>
              {link.groups && link.groups.map((g, gi) => (
                <div key={gi}>
                  {g.title && <div style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: 1.4, textTransform: 'uppercase', color: '#9aa8a9', padding: '10px 32px 4px' }}>{g.title}</div>}
                  {g.items.map(s => (
                    <a key={s.label} href={s.href} onClick={() => setOpen(false)} style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '9px 32px', fontSize: 14, color: '#607a7c', borderBottom: '1px solid #f7faf9' }}>
                      {s.label}{s.ext && <Icon name="externalLink" size={11} color="#00a48a"/>}
                    </a>
                  ))}
                </div>
              ))}
            </div>
          ))}
          <a href="index.html#tramites" onClick={() => setOpen(false)} style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 6, margin: '16px 24px 0', background: '#14484a', color: 'white', fontSize: 14.5, fontWeight: 700, padding: '13px', borderRadius: 6 }}>
            Matricularse <Icon name="externalLink" size={13} color="white"/>
          </a>
        </div>
      )}
    </nav>
  );
};

/* ─── ANNOUNCEMENT BAR ─── */
const AnnouncementBar = () => (
  <div style={{ background: '#e8f5f1', borderBottom: '1px solid #c0e2ca' }}>
    <div style={{ maxWidth: 1200, margin: '0 auto', padding: '10px 24px', display: 'flex', alignItems: 'center', gap: 10, flexWrap: 'wrap' }}>
      <Icon name="scale" size={14} color="#14484a"/>
      <span style={{ fontSize: 13.5, color: '#373d3e', fontWeight: 600 }}>Honorarios mínimos vigentes desde 01/04/2026 — Res. 1553</span>
      <a href="normativa.html" style={{ fontSize: 13.5, fontWeight: 800, color: '#008c75', display: 'flex', alignItems: 'center', gap: 3 }}>
        Ver tabla <Icon name="chevronRight" size={13} color="#008c75"/>
      </a>
    </div>
  </div>
);


/* ─── PAGE HERO ─── */
const PageHero = ({ eyebrow, titulo, intro, breadcrumb }) => (
  <section style={{ background: 'linear-gradient(135deg, #14484a 0%, #1a5f5c 60%, #00a48a 100%)', position: 'relative', overflow: 'hidden' }}>
    <div style={{ position: 'absolute', inset: 0, opacity: 0.06, backgroundImage: "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")" }}/>
    <div style={{ maxWidth: 1200, margin: '0 auto', padding: '44px 24px 52px', position: 'relative' }}>
      <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 20, fontSize: 13, color: 'rgba(255,255,255,0.65)', flexWrap: 'wrap' }}>
        <a href="index.html" style={{ display: 'flex', alignItems: 'center', gap: 5 }}>
          <Icon name="home" size={13}/> Inicio
        </a>
        <Icon name="chevronRight" size={12} color="rgba(255,255,255,0.35)"/>
        <span>Trámites</span>
        <Icon name="chevronRight" size={12} color="rgba(255,255,255,0.35)"/>
        <span style={{ color: 'white', fontWeight: 700 }}>{breadcrumb || titulo}</span>
      </div>
      <div style={{ display: 'inline-flex', alignItems: 'center', gap: 8, background: 'rgba(0,164,138,0.22)', border: '1px solid rgba(0,164,138,0.4)', borderRadius: 20, padding: '5px 14px', marginBottom: 16 }}>
        <div style={{ width: 6, height: 6, borderRadius: '50%', background: '#00a48a' }}/>
        <span style={{ color: '#c0e2ca', fontSize: 12, fontWeight: 700, letterSpacing: 1, textTransform: 'uppercase' }}>{eyebrow}</span>
      </div>
      <h1 style={{ color: 'white', fontSize: 'clamp(28px, 5vw, 46px)', fontWeight: 900, lineHeight: 1.1, marginBottom: 16, fontFamily: "'Roboto Condensed', sans-serif", letterSpacing: -0.5 }}>{titulo}</h1>
      <p style={{ color: 'rgba(255,255,255,0.82)', fontSize: 16.5, lineHeight: 1.6, maxWidth: 760, textWrap: 'pretty' }}>{intro}</p>
    </div>
  </section>
);

/* ─── BLOQUES DE CONTENIDO ─── */
const Aviso = ({ children, tono = 'verde', titulo }) => {
  const t = tono === 'ambar'
    ? { bg: '#fff8e6', border: '#f0d79a', ink: '#6b5417', icon: '#b98a1a' }
    : { bg: '#e8f5f1', border: '#c0e2ca', ink: '#14484a', icon: '#00a48a' };
  return (
    <div style={{ background: t.bg, border: '1px solid ' + t.border, borderRadius: 10, padding: '16px 18px', display: 'flex', gap: 12 }}>
      <Icon name="shield" size={18} color={t.icon}/>
      <div style={{ flex: 1, minWidth: 0 }}>
        {titulo && <div style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 15, fontWeight: 700, color: t.ink, marginBottom: 5 }}>{titulo}</div>}
        <div style={{ fontSize: 13.5, color: t.ink, lineHeight: 1.65, textWrap: 'pretty' }}>{children}</div>
      </div>
    </div>
  );
};

const Requisitos = ({ items, titulo, sub }) => (
  <div>
    {titulo && <h2 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 'clamp(20px, 2.6vw, 26px)', fontWeight: 900, color: '#14484a', marginBottom: sub ? 8 : 18 }}>{titulo}</h2>}
    {sub && <p style={{ fontSize: 14.5, color: '#607a7c', lineHeight: 1.65, marginBottom: 18, textWrap: 'pretty' }}>{sub}</p>}
    <ol style={{ listStyle: 'none', display: 'grid', gap: 10 }}>
      {items.map((it, i) => (
        <li key={i} style={{ background: 'white', border: '1px solid #e8f5f1', borderRadius: 10, padding: '14px 16px', display: 'flex', gap: 12, alignItems: 'flex-start' }}>
          <span style={{ flexShrink: 0, width: 24, height: 24, borderRadius: 6, background: '#e8f5f1', color: '#1a5f5c', fontSize: 12.5, fontWeight: 800, fontFamily: "'Roboto Condensed', sans-serif", display: 'grid', placeItems: 'center' }}>{i + 1}</span>
          <div style={{ flex: 1, minWidth: 0, fontSize: 14, color: '#4a6062', lineHeight: 1.6, textWrap: 'pretty' }}>
            <b style={{ color: '#14484a' }}>{it.t}</b>{it.d ? ' — ' + it.d : ''}
          </div>
        </li>
      ))}
    </ol>
  </div>
);

const Descargas = ({ items, titulo = 'Formularios y documentación' }) => (
  <div>
    <h2 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 'clamp(20px, 2.6vw, 26px)', fontWeight: 900, color: '#14484a', marginBottom: 8 }}>{titulo}</h2>
    <p style={{ fontSize: 14.5, color: '#607a7c', lineHeight: 1.65, marginBottom: 18, textWrap: 'pretty' }}>
      Descargá y completá los formularios, y enviálos por mail junto con el resto de la documentación. Para editarlos conviene usar Adobe Acrobat Reader.
    </p>
    <div className="dl-grid" style={{ display: 'grid', gridTemplateColumns: 'repeat(2, minmax(0, 1fr))', gap: 12 }}>
      {items.map(d => (
        <a key={d.label} href={d.href} target="_blank" style={{
          background: 'white', border: '1px solid #e8f5f1', borderRadius: 10, padding: '15px 17px',
          display: 'flex', alignItems: 'center', gap: 12, transition: 'all .2s',
        }}
        onMouseEnter={e => { e.currentTarget.style.borderColor = '#00a48a'; e.currentTarget.style.boxShadow = '0 8px 22px rgba(20,72,74,0.10)'; }}
        onMouseLeave={e => { e.currentTarget.style.borderColor = '#e8f5f1'; e.currentTarget.style.boxShadow = 'none'; }}>
          <div style={{ width: 36, height: 36, borderRadius: 8, background: '#e8f5f1', display: 'grid', placeItems: 'center', flexShrink: 0 }}>
            <Icon name="file" size={17} color="#1a5f5c"/>
          </div>
          <div style={{ flex: 1, minWidth: 0 }}>
            <div style={{ fontSize: 14, fontWeight: 700, color: '#14484a', lineHeight: 1.35 }}>{d.label}</div>
            <div style={{ fontSize: 11.5, color: '#9aa8a9', textTransform: 'uppercase', letterSpacing: 0.6, marginTop: 2 }}>{d.tipo || 'PDF'}</div>
          </div>
          <Icon name="chevronRight" size={14} color="#00a48a"/>
        </a>
      ))}
    </div>
  </div>
);

const Costo = ({ filas, titulo = 'Costo del trámite' }) => (
  <div style={{ background: '#14484a', borderRadius: 12, padding: '22px 24px 24px', color: 'white' }}>
    <h3 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 19, fontWeight: 900, color: 'white', marginBottom: 16 }}>{titulo}</h3>
    <div style={{ display: 'grid', gap: 12 }}>
      {filas.map((f, i) => (
        <div key={i} style={{ paddingBottom: 12, borderBottom: i < filas.length - 1 ? '1px dashed rgba(255,255,255,0.18)' : 'none' }}>
          <div style={{ fontSize: 10.5, fontWeight: 700, letterSpacing: 1, textTransform: 'uppercase', color: 'rgba(255,255,255,0.5)', marginBottom: 3 }}>{f.label}</div>
          <div style={{ fontSize: 14, color: 'rgba(255,255,255,0.88)', lineHeight: 1.5 }}>{f.value}</div>
        </div>
      ))}
    </div>
  </div>
);

const Ayuda = ({ mail = 'info@cipba.org' }) => (
  <div style={{ background: 'white', border: '1px solid #e8f5f1', borderRadius: 12, padding: '22px 24px 24px' }}>
    <h3 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 18, fontWeight: 700, color: '#14484a', marginBottom: 8 }}>¿Dudas con el trámite?</h3>
    <p style={{ fontSize: 13.5, color: '#607a7c', lineHeight: 1.6, marginBottom: 16, textWrap: 'pretty' }}>
      El trámite se inicia por mail en el Distrito que corresponde a tu domicilio legal. Escribinos y te orientamos.
    </p>
    <div style={{ display: 'grid', gap: 9 }}>
      <a href={'mailto:' + mail} style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13.5, fontWeight: 700, color: '#14484a' }}>
        <Icon name="mail" size={15} color="#00a48a"/> {mail}
      </a>
      <a href="contacto.html" style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13.5, fontWeight: 700, color: '#14484a' }}>
        <Icon name="users" size={15} color="#00a48a"/> Contacto por área
      </a>
      <a href="https://wa.me/5491127133330" target="_blank" style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13.5, fontWeight: 700, color: '#14484a' }}>
        <Icon name="whatsapp" size={15} color="#25d366"/> Consultar por WhatsApp
      </a>
    </div>
  </div>
);

const OtrosTramites = ({ actual }) => {
  const links = [
    { label: 'Inscripción', href: 'inscripcion.html', icon: 'award' },
    { label: 'Rehabilitación', href: 'rehabilitacion.html', icon: 'check' },
    { label: 'Baja', href: 'baja.html', icon: 'file' },
    { label: 'Solicitud de credenciales', href: 'solicitud-credenciales.html', icon: 'shield' },
    { label: 'Medios de pago', href: 'pago-matricula.html', icon: 'dollar' },
  ].filter(l => l.href !== actual);
  return (
    <section style={{ background: '#eef2f1', padding: '44px 24px 52px', borderTop: '1px solid #c0e2ca' }}>
      <div style={{ maxWidth: 1200, margin: '0 auto' }}>
        <h2 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 20, fontWeight: 900, color: '#14484a', marginBottom: 16 }}>Otros trámites</h2>
        <div className="otros-grid" style={{ display: 'grid', gridTemplateColumns: 'repeat(4, minmax(0, 1fr))', gap: 14 }}>
          {links.map(l => (
            <a key={l.href} href={l.href} style={{
              background: 'white', border: '1px solid #e8f5f1', borderRadius: 10, padding: '16px 18px',
              display: 'flex', alignItems: 'center', gap: 11, transition: 'all .2s',
            }}
            onMouseEnter={e => { e.currentTarget.style.borderColor = '#00a48a'; e.currentTarget.style.transform = 'translateY(-2px)'; }}
            onMouseLeave={e => { e.currentTarget.style.borderColor = '#e8f5f1'; e.currentTarget.style.transform = 'none'; }}>
              <div style={{ width: 34, height: 34, borderRadius: 8, background: '#e8f5f1', display: 'grid', placeItems: 'center', flexShrink: 0 }}>
                <Icon name={l.icon} size={16} color="#1a5f5c"/>
              </div>
              <span style={{ fontSize: 14, fontWeight: 700, color: '#14484a' }}>{l.label}</span>
            </a>
          ))}
        </div>
      </div>
    </section>
  );
};

const TramiteShellStyles = () => (
  <style>{`
    @media (max-width: 1160px) { .desktop-nav { display: none !important; } .mobile-burger { display: flex !important; } }
    @media (max-width: 900px) { .tram-grid { grid-template-columns: 1fr !important; } .otros-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; } }
    @media (max-width: 1000px) { .foot-grid { grid-template-columns: 1fr 1fr !important; } .foot-grid > div:first-child { grid-column: span 2; } }
    @media (max-width: 640px) { .dl-grid { grid-template-columns: 1fr !important; } .otros-grid { grid-template-columns: 1fr !important; } .foot-grid { grid-template-columns: 1fr !important; } .foot-grid > div:first-child { grid-column: auto; } }
  `}</style>
);

const TramitePage = ({ hero, actual, children, aside }) => {
  const [scrolled, setScrolled] = useState(false);
  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);
  return (
    <>
      <TramiteShellStyles/>
      <HeaderStyles/>
      <TopBar/>
      <Navbar scrolled={scrolled}/>
      <PageHero {...hero}/>
      <section style={{ background: '#f7faf9', padding: '48px 24px 56px' }}>
        <div className="tram-grid" style={{ maxWidth: 1200, margin: '0 auto', display: 'grid', gridTemplateColumns: '1.55fr 1fr', gap: 34, alignItems: 'start' }}>
          <div style={{ display: 'grid', gap: 28 }}>{children}</div>
          <div style={{ display: 'grid', gap: 16, position: 'sticky', top: 88 }}>{aside}</div>
        </div>
      </section>
      <OtrosTramites actual={actual}/>
      <Footer/>
    </>
  );
};


/* ─── REDES / FOOTER ─── */
const redes = [
  { icon: 'instagram', label: 'Instagram', href: 'https://www.instagram.com/cipba7/' },
  { icon: 'facebook', label: 'Facebook', href: '#' },
  { icon: 'linkedin', label: 'LinkedIn', href: '#' },
  { icon: 'whatsapp', label: 'WhatsApp', href: 'https://wa.me/5491127133330' },
];

const SocialIcons = ({ size = 17 }) => {
  const [h, setH] = useState(null);
  return (
    <div style={{ display: 'flex', gap: 10 }}>
      {redes.map((r, i) => (
        <a key={r.label} href={r.href} target="_blank" aria-label={r.label} title={r.label}
          onMouseEnter={() => setH(i)} onMouseLeave={() => setH(null)} style={{
          width: 40, height: 40, borderRadius: 10, display: 'grid', placeItems: 'center', transition: 'all .2s',
          border: `1px solid ${h === i ? 'rgba(255,255,255,0.5)' : 'rgba(255,255,255,0.24)'}`,
          background: h === i ? 'rgba(255,255,255,0.14)' : 'rgba(255,255,255,0.06)',
        }}>
          <Icon name={r.icon} size={size} color="white"/>
        </a>
      ))}
    </div>
  );
};

/* ─── FOOTER ─── */
const footerCols = [
  { title: 'Trámites', items: ['Inscripción y bajas', 'Medios de pago', 'Certificados CAIE', 'Visado online', 'SIGMA'] },
  { title: 'Normativa', items: ['Ley 10.416', 'Código de Ética', 'Resoluciones', 'Honorarios mínimos', 'Vademécum'] },
  { title: 'Institucional', items: ['Autoridades', 'Subcomisiones', 'Partidos del Distrito', 'Sedes y delegaciones', 'Memoria y balance'] },
  { title: 'Links de interés', items: ['Autoridad del Agua (ADA)', 'Agencia de Recaudación (ARBA)', 'Caja de Previsión Social', 'Colegio de Escribanos PBA', 'Ministerio de Ambiente'] },
];

const Footer = () => (
  <footer style={{ background: '#14484a', color: 'rgba(255,255,255,0.78)' }}>
    <div style={{ maxWidth: 1200, margin: '0 auto', padding: '48px 24px 34px' }}>
      <div className="foot-grid" style={{ display: 'grid', gridTemplateColumns: '1.5fr repeat(4, minmax(0, 1fr))', gap: 30 }}>
        <div>
          <img src="uploads/Logo-blanco.png" alt="CIPBA Distrito VII" style={{ width: 200, maxWidth: '100%', height: 'auto', marginBottom: 16 }}/>
          <p style={{ fontSize: 13, lineHeight: 1.7, color: 'rgba(255,255,255,0.72)', maxWidth: 320, textWrap: 'pretty' }}>Entidad pública no estatal creada por Ley 10.416. Ejercicio profesional habilitado en 23 partidos de la zona oeste y norte del Gran Buenos Aires.</p>
          <div style={{ marginTop: 20 }}><SocialIcons/></div>
        </div>
        {footerCols.map(col => (
          <div key={col.title}>
            <h3 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 14, fontWeight: 700, letterSpacing: 1, textTransform: 'uppercase', color: 'white', marginBottom: 15 }}>{col.title}</h3>
            <div style={{ display: 'grid', gap: 9 }}>
              {col.items.map(it => (
                <a key={it} href="#" style={{ fontSize: 13.5, color: 'rgba(255,255,255,0.72)', transition: 'color .2s' }}
                  onMouseEnter={e => e.currentTarget.style.color = '#00a48a'} onMouseLeave={e => e.currentTarget.style.color = 'rgba(255,255,255,0.72)'}>{it}</a>
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
    <div style={{ borderTop: '1px solid rgba(255,255,255,0.12)' }}>
      <div style={{ maxWidth: 1200, margin: '0 auto', padding: '16px 24px', display: 'flex', justifyContent: 'space-between', flexWrap: 'wrap', gap: 10, fontSize: 12.5, color: 'rgba(255,255,255,0.6)' }}>
        <span>© 2026 Colegio de Ingenieros de la Provincia de Buenos Aires — Distrito VII</span>
        <span style={{ display: 'flex', gap: 16 }}>
          <a href="#" style={{ color: 'inherit' }}>Ley 10.416</a>
          <a href="#" style={{ color: 'inherit' }}>Política de privacidad</a>
        </span>
      </div>
    </div>
  </footer>
);


Object.assign(window, { Icon, HeaderStyles, TopBar, Navbar, PageHero, Aviso, Requisitos, Descargas, Costo, Ayuda, OtrosTramites, TramitePage, Footer, SocialIcons });
