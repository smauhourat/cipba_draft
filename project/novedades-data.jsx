/* Datos y piezas compartidas de Eventos y Novedades */

const CAT = {
  'Eventos':        { ink: '#484586', bg: '#f1f0f9' },
  'Capacitación':   { ink: '#00705e', bg: '#e8f5f1' },
  'Institucional':  { ink: '#14484a', bg: '#eef2f1' },
  'Beneficios':     { ink: '#484586', bg: '#f1f0f9' },
  'Normativa':      { ink: '#8a3a2a', bg: '#fbf0ed' },
  'Comisiones':     { ink: '#484586', bg: '#f1f0f9' },
};
const CATS = Object.keys(CAT);

const NOVEDADES = [
  {
    id: 'jornada-bim-cype',
    cat: 'Eventos',
    fecha: '2026-04-09',
    fechaTxt: '09 abr 2026',
    title: 'Actualización profesional 2026: Jornada BIM en CYPE',
    bajada: 'Integración y flujo de trabajo entre modelado estructural, instalaciones y documentación de obra.',
    destacado: true,
    evento: { dia: 'Jueves 9 de abril de 2026', hora: '18:00 a 21:00 h', lugar: 'Sede San Justo — Salón de actos', modalidad: 'Presencial con transmisión en vivo', cupo: '80 matriculados', costo: 'Sin cargo para matriculados al día' },
    cuerpo: [
      { t: 'p', c: 'La Subcomisión de Obras Civiles organiza una jornada de actualización sobre metodología BIM aplicada al proyecto y la documentación de obra, con foco en el uso de CYPE para el modelado estructural y de instalaciones.' },
      { t: 'h', c: 'Contenidos' },
      { t: 'ul', c: ['Flujo de trabajo IFC entre disciplinas y control de versiones del modelo federado.', 'Modelado estructural y verificación según reglamentos CIRSOC.', 'Instalaciones sanitarias, eléctricas y de incendio sobre el mismo modelo.', 'Generación de planos, cómputos y presupuesto desde el modelo.'] },
      { t: 'p', c: 'La actividad está dirigida a ingenieros matriculados en el Distrito VII que trabajen en proyecto, dirección o inspección de obra. No se requieren conocimientos previos de BIM, aunque sí manejo básico de software de dibujo asistido.' },
      { t: 'h', c: 'Certificación' },
      { t: 'p', c: 'Se entrega certificado de asistencia digital a quienes registren ingreso y egreso. La jornada acredita horas de capacitación continua a efectos del registro de actualización profesional del Distrito.' },
    ],
    cta: { label: 'Inscribirme a la jornada', href: '#' },
  },
  {
    id: 'seguridad-e-higiene',
    cat: 'Capacitación',
    fecha: '2026-04-14',
    fechaTxt: '14 abr 2026',
    title: 'Jornada de seguridad e higiene: inscripción abierta',
    bajada: 'Actividad presencial en la sede de San Justo, con cupo limitado y certificado de asistencia.',
    evento: { dia: 'Martes 14 de abril de 2026', hora: '17:30 a 20:30 h', lugar: 'Sede San Justo — Aula 2', modalidad: 'Presencial', cupo: '40 matriculados', costo: 'Sin cargo para matriculados al día' },
    cuerpo: [
      { t: 'p', c: 'Encuentro de actualización sobre las obligaciones del profesional en materia de higiene y seguridad en obra, con revisión de la normativa vigente y de los criterios de control aplicados por las ART y los municipios del Distrito.' },
      { t: 'h', c: 'Temario' },
      { t: 'ul', c: ['Decreto 911/96 y resoluciones complementarias de la SRT.', 'Programa de seguridad: contenido, aprobación y actualización.', 'Responsabilidad del profesional firmante y documentación respaldatoria.', 'Casos de inspección y observaciones frecuentes.'] },
      { t: 'p', c: 'El cupo es limitado y se asigna por orden de inscripción. Quienes queden fuera del cupo pasan automáticamente a lista de espera para la próxima edición.' },
    ],
    cta: { label: 'Reservar mi lugar', href: '#' },
  },
  {
    id: 'reunion-municipio',
    cat: 'Institucional',
    fecha: '2026-04-08',
    fechaTxt: '08 abr 2026',
    title: 'Reunión de avance con el municipio de La Matanza',
    bajada: 'El Consejo Directivo se reunió con el Intendente para revisar el circuito de visado de obra privada.',
    cuerpo: [
      { t: 'p', c: 'El Consejo Directivo del Distrito VII mantuvo una reunión de trabajo con autoridades del municipio de La Matanza para revisar el circuito de presentación y visado de expedientes de obra privada.' },
      { t: 'p', c: 'Se acordó un esquema de trabajo conjunto para reducir los tiempos de aprobación, con una mesa técnica mensual entre el área de Obras Particulares del municipio y la Subcomisión de Obras Civiles del Distrito.' },
      { t: 'h', c: 'Puntos acordados' },
      { t: 'ul', c: ['Digitalización completa de la presentación de expedientes durante el segundo semestre.', 'Criterio unificado de documentación mínima exigible al profesional.', 'Canal de consulta directo para casos observados.'] },
    ],
  },
  {
    id: 'promociones-tarjeta',
    cat: 'Beneficios',
    fecha: '2026-03-30',
    fechaTxt: 'Vigente todo 2026',
    title: 'Promociones con tarjeta de crédito para el pago de matrícula',
    bajada: 'Planes en cuotas sin interés y descuentos para el pago anual adelantado.',
    cuerpo: [
      { t: 'p', c: 'Durante 2026 el Distrito mantiene acuerdos con entidades bancarias para el pago de la matrícula anual en cuotas sin interés, además del descuento vigente por pago anual adelantado.' },
      { t: 'p', c: 'Las promociones se aplican sobre los medios de pago habilitados en la sección de pago de matrícula. El descuento por pago adelantado se acredita en el momento de la emisión de la boleta.' },
    ],
    cta: { label: 'Ver medios de pago', href: 'pago-matricula.html' },
  },
  {
    id: 'honorarios-minimos-abril',
    cat: 'Normativa',
    fecha: '2026-04-01',
    fechaTxt: '01 abr 2026',
    title: 'Honorarios mínimos vigentes desde el 1 de abril',
    bajada: 'Resolución 1553 del Consejo Superior. Tabla actualizada y criterios de aplicación.',
    cuerpo: [
      { t: 'p', c: 'El Consejo Superior aprobó la actualización de la tabla de honorarios mínimos sugeridos, con vigencia a partir del 1 de abril de 2026 (Resolución 1553).' },
      { t: 'p', c: 'La tabla alcanza a todas las tareas profesionales sujetas a visado en el ámbito de la provincia de Buenos Aires. Los trabajos presentados con anterioridad a la fecha de vigencia conservan los valores de la tabla anterior.' },
    ],
    cta: { label: 'Descargar la tabla', href: '#' },
  },
  {
    id: 'peritos-scjba',
    cat: 'Comisiones',
    fecha: '2026-03-28',
    fechaTxt: '28 mar 2026',
    title: 'Peritos auxiliares: inscripción anual ante la SCJBA',
    bajada: 'Plazos, documentación requerida y acompañamiento del Distrito durante la inscripción.',
    cuerpo: [
      { t: 'p', c: 'Se encuentra abierta la inscripción anual al registro de peritos auxiliares de la justicia ante la Suprema Corte de Justicia de la Provincia de Buenos Aires.' },
      { t: 'h', c: 'Documentación' },
      { t: 'ul', c: ['Constancia de matrícula vigente emitida por el Distrito.', 'Certificado de libre deuda de matrícula.', 'Declaración de departamentos judiciales en los que solicita inscribirse.'] },
      { t: 'p', c: 'La Subcomisión de Peritos asesora a los matriculados durante el proceso de inscripción y publica el instructivo actualizado con el calendario oficial.' },
    ],
  },
  {
    id: 'asamblea-anual',
    cat: 'Institucional',
    fecha: '2026-03-20',
    fechaTxt: '20 mar 2026',
    title: 'Convocatoria a Asamblea Anual Ordinaria',
    bajada: 'Tratamiento de memoria y balance del ejercicio 2025 y presupuesto 2026.',
    evento: { dia: 'Viernes 29 de mayo de 2026', hora: '18:00 h', lugar: 'Sede San Justo — Salón de actos', modalidad: 'Presencial', cupo: 'Abierta a todos los matriculados', costo: 'Sin cargo' },
    cuerpo: [
      { t: 'p', c: 'Se convoca a los matriculados del Distrito VII a la Asamblea Anual Ordinaria, en la que se tratará la memoria y el balance del ejercicio 2025 y el presupuesto para el ejercicio 2026.' },
      { t: 'h', c: 'Orden del día' },
      { t: 'ul', c: ['Designación de dos asambleístas para firmar el acta.', 'Consideración de la memoria y el balance del ejercicio 2025.', 'Consideración del presupuesto para el ejercicio 2026.', 'Informe del Consejo Directivo.'] },
      { t: 'p', c: 'Podrán participar con voz y voto los matriculados con la cuota al día. La documentación queda a disposición en la sede del Distrito con quince días de anticipación.' },
    ],
  },
  {
    id: 'auxiliar-justicia',
    cat: 'Institucional',
    fecha: '2026-05-15',
    fechaTxt: '15 may 2026',
    title: 'Inscripción para Auxiliar de Justicia',
    bajada: 'Apertura del período de inscripción para el listado de auxiliares de justicia del Distrito.',
    cuerpo: [
      { t: 'p', c: 'Queda abierto el período de inscripción al listado de auxiliares de justicia del Distrito VII. La inscripción se realiza por vía electrónica y requiere matrícula vigente sin deuda.' },
      { t: 'p', c: 'El listado se eleva al organismo de contralor una vez cerrado el período. Las altas fuera de término se incorporan recién en el ciclo siguiente.' },
    ],
    cta: { label: 'Iniciar la inscripción', href: '#' },
  },
  {
    id: 'curso-instalaciones-gas',
    cat: 'Capacitación',
    fecha: '2026-05-06',
    fechaTxt: '06 may 2026',
    title: 'Curso de instalaciones de gas: nueva cohorte',
    bajada: 'Cuatro encuentros semanales, modalidad mixta, con evaluación final y certificado.',
    evento: { dia: 'Miércoles, del 6 al 27 de mayo', hora: '18:00 a 20:00 h', lugar: 'Sede Haedo y aula virtual', modalidad: 'Mixta (presencial y virtual)', cupo: '35 matriculados', costo: 'Arancel reducido para matriculados' },
    cuerpo: [
      { t: 'p', c: 'Nueva cohorte del curso de instalaciones de gas, con revisión de la normativa del distribuidor, cálculo de cañerías y criterios de ventilación y evacuación de gases.' },
      { t: 'h', c: 'Cronograma' },
      { t: 'ul', c: ['Encuentro 1 — Marco normativo y documentación exigible.', 'Encuentro 2 — Cálculo y dimensionamiento de cañerías.', 'Encuentro 3 — Ventilaciones, conductos y artefactos.', 'Encuentro 4 — Casos de obra y evaluación final.'] },
    ],
    cta: { label: 'Inscribirme al curso', href: '#' },
  },
];

const byId = (id) => NOVEDADES.find(n => n.id === id) || NOVEDADES[0];

/* ─── Card de novedad ─── */
const CatBadge = ({ cat, size = 11 }) => {
  const c = CAT[cat] || CAT['Institucional'];
  return <span style={{ background: c.bg, color: c.ink, fontSize: size, fontWeight: 800, letterSpacing: 0.8, textTransform: 'uppercase', padding: '4px 10px', borderRadius: 20, whiteSpace: 'nowrap' }}>{cat}</span>;
};

const NovCard = ({ n, ratio = '16 / 10' }) => {
  const [h, setH] = React.useState(false);
  return (
    <a href={`novedad.html?id=${n.id}`} onMouseEnter={() => setH(true)} onMouseLeave={() => setH(false)} style={{
      display: 'flex', flexDirection: 'column', background: 'white', border: `1px solid ${h ? '#00a48a' : '#e8f5f1'}`,
      borderRadius: 12, overflow: 'hidden', transition: 'all .22s ease',
      transform: h ? 'translateY(-4px)' : 'none', boxShadow: h ? '0 12px 28px rgba(20,72,74,0.12)' : '0 2px 8px rgba(0,0,0,0.04)',
    }}>
      <div style={{ aspectRatio: ratio, background: '#eef2f1', position: 'relative' }}>
        <image-slot id={`nov-${n.id}`} shape="rect" placeholder="Imagen"></image-slot>
      </div>
      <div style={{ padding: '16px 18px 20px', display: 'flex', flexDirection: 'column', gap: 9, flex: 1 }}>
        <div style={{ display: 'flex', alignItems: 'center', gap: 10, flexWrap: 'wrap' }}>
          <CatBadge cat={n.cat}/>
          <span style={{ fontSize: 12, color: '#9aa8a9', fontWeight: 600 }}>{n.fechaTxt}</span>
        </div>
        <h3 style={{ fontFamily: "'Roboto Condensed', sans-serif", fontSize: 19, fontWeight: 700, color: '#14484a', lineHeight: 1.25, textWrap: 'pretty' }}>{n.title}</h3>
        <p style={{ fontSize: 14, color: '#607a7c', lineHeight: 1.6, textWrap: 'pretty' }}>{n.bajada}</p>
        <span style={{ marginTop: 'auto', paddingTop: 6, fontSize: 13.5, fontWeight: 800, color: '#00705e', display: 'flex', alignItems: 'center', gap: 4 }}>
          Leer más <Icon name="chevronRight" size={13} color="#00705e"/>
        </span>
      </div>
    </a>
  );
};

Object.assign(window, { CAT, CATS, NOVEDADES, byId, CatBadge, NovCard });
