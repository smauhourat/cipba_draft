/* @ds-bundle: {"format":4,"namespace":"CIPBADistritoVIIDesignSystem_1a5d5e","components":[{"name":"AuthorityCard","sourcePath":"components/cards/AuthorityCard.jsx"},{"name":"Card","sourcePath":"components/cards/Card.jsx"},{"name":"NewsCard","sourcePath":"components/cards/NewsCard.jsx"},{"name":"QuickAccessCard","sourcePath":"components/cards/QuickAccessCard.jsx"},{"name":"SedeCard","sourcePath":"components/cards/SedeCard.jsx"},{"name":"StatCard","sourcePath":"components/cards/StatCard.jsx"},{"name":"SubcomisionCard","sourcePath":"components/cards/SubcomisionCard.jsx"},{"name":"Badge","sourcePath":"components/core/Badge.jsx"},{"name":"Button","sourcePath":"components/core/Button.jsx"},{"name":"Icon","sourcePath":"components/core/Icon.jsx"},{"name":"IconBadge","sourcePath":"components/core/IconBadge.jsx"},{"name":"SectionHeader","sourcePath":"components/core/SectionHeader.jsx"},{"name":"CourseRow","sourcePath":"components/data/CourseRow.jsx"},{"name":"DocumentRow","sourcePath":"components/data/DocumentRow.jsx"},{"name":"PartidoChip","sourcePath":"components/data/PartidoChip.jsx"},{"name":"Breadcrumb","sourcePath":"components/navigation/Breadcrumb.jsx"},{"name":"Footer","sourcePath":"components/navigation/Footer.jsx"},{"name":"Navbar","sourcePath":"components/navigation/Navbar.jsx"},{"name":"TopBar","sourcePath":"components/navigation/TopBar.jsx"}],"sourceHashes":{"components/cards/AuthorityCard.jsx":"b44645679c63","components/cards/Card.jsx":"296a972448cb","components/cards/NewsCard.jsx":"e9eaffbd71ee","components/cards/QuickAccessCard.jsx":"dff380b9cf7a","components/cards/SedeCard.jsx":"dffb432c938e","components/cards/StatCard.jsx":"cb2dcd2b98af","components/cards/SubcomisionCard.jsx":"fdd47c9654d2","components/core/Badge.jsx":"a521ef04a3b7","components/core/Button.jsx":"84737a926154","components/core/Icon.jsx":"36a1be426fad","components/core/IconBadge.jsx":"c8040dda94ba","components/core/SectionHeader.jsx":"7fc52b70e3c0","components/data/CourseRow.jsx":"48ceccb7a883","components/data/DocumentRow.jsx":"83c31ef8fffa","components/data/PartidoChip.jsx":"e844d5529a50","components/navigation/Breadcrumb.jsx":"d2c64e3be436","components/navigation/Footer.jsx":"7dc3d6e7bf57","components/navigation/Navbar.jsx":"206d7cdc1145","components/navigation/TopBar.jsx":"f5fb37268151","ui_kits/sitio-web/HomePage.jsx":"c6ab19bead3e","ui_kits/sitio-web/InstitucionalPage.jsx":"a56c9223e683","ui_kits/sitio-web/SubcomisionesPage.jsx":"4ded0f380571","ui_kits/sitio-web/data.js":"bafa85984a19"},"inlinedExternals":[],"unexposedExports":[]} */

(() => {

const __ds_ns = (window.CIPBADistritoVIIDesignSystem_1a5d5e = window.CIPBADistritoVIIDesignSystem_1a5d5e || {});

const __ds_scope = {};

(__ds_ns.__errors = __ds_ns.__errors || []);

// components/cards/AuthorityCard.jsx
try { (() => {
function initials(name) {
  const parts = name.trim().split(' ');
  return (parts[0][0] + (parts[parts.length - 1][0] || '')).toUpperCase();
}
function AuthorityCard({
  name,
  role,
  title,
  featured
}) {
  if (featured) {
    return /*#__PURE__*/React.createElement("div", {
      style: {
        gridColumn: 'span 2',
        background: 'linear-gradient(135deg, var(--brand-primary) 0%, var(--blue-700) 100%)',
        borderRadius: 'var(--radius-card-lg)',
        padding: 28,
        color: '#fff',
        position: 'relative'
      }
    }, /*#__PURE__*/React.createElement("span", {
      style: {
        position: 'absolute',
        top: 16,
        right: 16,
        background: 'var(--brand-accent)',
        fontSize: 11,
        fontWeight: 700,
        padding: '4px 10px',
        borderRadius: 'var(--radius-badge)'
      }
    }, "Presidencia"), /*#__PURE__*/React.createElement("div", {
      style: {
        width: 72,
        height: 72,
        borderRadius: '50%',
        background: 'linear-gradient(135deg, var(--brand-accent), var(--green-700))',
        border: '2px solid rgba(255,255,255,.4)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontFamily: 'var(--font-display)',
        fontWeight: 900,
        fontSize: 22,
        marginBottom: 16
      }
    }, initials(name)), /*#__PURE__*/React.createElement("div", {
      style: {
        color: 'var(--green-100)',
        fontSize: 13,
        fontWeight: 700,
        marginBottom: 4
      }
    }, role), /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: 19,
        fontWeight: 800
      }
    }, name), /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: 13,
        fontStyle: 'italic',
        opacity: .85,
        marginTop: 4
      }
    }, title));
  }
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      border: '1px solid var(--border-card)',
      borderRadius: 'var(--radius-card-lg)',
      padding: 20,
      display: 'flex',
      gap: 14,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 56,
      height: 56,
      borderRadius: '50%',
      background: 'linear-gradient(135deg, var(--blue-100), #c0e2ca)',
      color: 'var(--blue-700)',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      fontFamily: 'var(--font-display)',
      fontWeight: 700,
      fontSize: 18,
      flexShrink: 0
    }
  }, initials(name)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12,
      fontWeight: 700,
      color: 'var(--brand-accent)'
    }
  }, role), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 15,
      fontWeight: 800,
      color: 'var(--text-primary)'
    }
  }, name), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12.5,
      color: 'var(--text-secondary)',
      fontStyle: 'italic'
    }
  }, title)));
}
Object.assign(__ds_scope, { AuthorityCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/AuthorityCard.jsx", error: String((e && e.message) || e) }); }

// components/cards/Card.jsx
try { (() => {
function Card({
  children,
  hoverable = true,
  padding = 24,
  style = {}
}) {
  const [hover, setHover] = React.useState(false);
  return /*#__PURE__*/React.createElement("div", {
    onMouseEnter: () => hoverable && setHover(true),
    onMouseLeave: () => hoverable && setHover(false),
    style: {
      background: 'var(--surface-card)',
      border: '1px solid var(--border-card)',
      borderRadius: 'var(--radius-card)',
      padding,
      boxShadow: hover ? 'var(--shadow-hover)' : 'var(--shadow-card)',
      transform: hover ? 'translateY(-4px)' : 'none',
      transition: 'all .25s ease',
      ...style
    }
  }, children);
}
Object.assign(__ds_scope, { Card });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/Card.jsx", error: String((e && e.message) || e) }); }

// components/cards/StatCard.jsx
try { (() => {
function StatCard({
  number,
  label,
  bordered
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      padding: '20px 16px',
      textAlign: 'center',
      borderRight: bordered ? '1px solid var(--border-card)' : 'none'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 26,
      fontWeight: 900,
      color: 'var(--brand-primary)',
      fontFamily: 'var(--font-display)'
    }
  }, number), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12,
      color: 'var(--text-muted)',
      marginTop: 3,
      letterSpacing: .3
    }
  }, label));
}
Object.assign(__ds_scope, { StatCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/StatCard.jsx", error: String((e && e.message) || e) }); }

// components/core/Badge.jsx
try { (() => {
const catVar = {
  Institucional: '--cat-institucional',
  Capacitación: '--cat-capacitacion',
  Normativa: '--cat-normativa',
  Visado: '--cat-visado',
  Honorarios: '--cat-honorarios',
  Matrícula: '--cat-matricula',
  Comisiones: '--cat-comisiones'
};
function Badge({
  children,
  category,
  tone = 'solid'
}) {
  const colorVar = catVar[category] || '--brand-accent';
  if (tone === 'tint') {
    return /*#__PURE__*/React.createElement("span", {
      style: {
        background: `color-mix(in srgb, var(${colorVar}) 12%, white)`,
        color: `var(${colorVar})`,
        fontSize: 12,
        fontWeight: 700,
        padding: '4px 10px',
        borderRadius: 'var(--radius-badge)',
        display: 'inline-block'
      }
    }, children);
  }
  return /*#__PURE__*/React.createElement("span", {
    style: {
      background: `var(${colorVar})`,
      color: '#fff',
      fontSize: 12,
      fontWeight: 700,
      padding: '6px 12px',
      borderRadius: 'var(--radius-badge)',
      display: 'inline-block',
      letterSpacing: .2
    }
  }, children);
}
Object.assign(__ds_scope, { Badge });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/Badge.jsx", error: String((e && e.message) || e) }); }

// components/cards/NewsCard.jsx
try { (() => {
function NewsCard({
  category,
  date,
  title,
  description,
  highlight
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      borderRadius: 'var(--radius-card)',
      overflow: 'hidden',
      border: '1px solid var(--border-card)',
      borderTop: `3px solid var(${{
        Institucional: '--cat-institucional',
        Capacitación: '--cat-capacitacion',
        Normativa: '--cat-normativa',
        Visado: '--cat-visado'
      }[category] || '--brand-accent'})`,
      boxShadow: 'var(--shadow-card)',
      display: 'flex',
      flexDirection: 'column'
    }
  }, highlight && /*#__PURE__*/React.createElement("div", {
    style: {
      background: 'var(--brand-primary)',
      color: '#fff',
      fontSize: 12,
      fontWeight: 700,
      padding: '6px 16px'
    }
  }, "Destacado"), /*#__PURE__*/React.createElement("div", {
    style: {
      padding: 20,
      display: 'flex',
      flexDirection: 'column',
      gap: 10
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 8,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Badge, {
    category: category
  }, category), /*#__PURE__*/React.createElement("span", {
    style: {
      fontSize: 12.5,
      color: 'var(--text-muted)'
    }
  }, date)), /*#__PURE__*/React.createElement("div", {
    style: {
      fontWeight: 800,
      fontSize: 16,
      color: 'var(--text-primary)',
      lineHeight: 1.35
    }
  }, title), /*#__PURE__*/React.createElement("p", {
    style: {
      fontSize: 13.5,
      color: 'var(--text-secondary)',
      lineHeight: 1.55
    }
  }, description)));
}
Object.assign(__ds_scope, { NewsCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/NewsCard.jsx", error: String((e && e.message) || e) }); }

// components/core/Button.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
const sizeMap = {
  sm: {
    padding: '9px 18px',
    fontSize: 13
  },
  md: {
    padding: '13px 28px',
    fontSize: 15
  }
};
const variants = {
  primary: {
    background: 'var(--brand-accent)',
    color: '#fff',
    border: 'none',
    boxShadow: '0 4px 16px rgba(42,158,42,.4)'
  },
  primaryDark: {
    background: 'var(--brand-primary)',
    color: '#fff',
    border: 'none'
  },
  outline: {
    background: 'transparent',
    color: '#fff',
    border: '1px solid rgba(255,255,255,.4)'
  },
  outlineDark: {
    background: 'transparent',
    color: 'var(--brand-primary)',
    border: '1.5px solid var(--border-input)'
  },
  whatsapp: {
    background: 'var(--whatsapp)',
    color: '#fff',
    border: 'none'
  }
};
const hoverBg = {
  primary: 'var(--brand-accent-hover)',
  primaryDark: 'var(--brand-primary-hover)',
  whatsapp: 'var(--whatsapp-hover)'
};
function Button({
  children,
  variant = 'primary',
  size = 'md',
  icon,
  disabled,
  onClick,
  href
}) {
  const v = variants[variant] || variants.primary;
  const s = sizeMap[size] || sizeMap.md;
  const style = {
    ...v,
    ...s,
    fontFamily: 'var(--font-body)',
    fontWeight: 700,
    borderRadius: 'var(--radius-btn)',
    display: 'inline-flex',
    alignItems: 'center',
    gap: 8,
    cursor: disabled ? 'not-allowed' : 'pointer',
    opacity: disabled ? 0.5 : 1,
    transition: 'all .2s ease'
  };
  const handlers = disabled ? {} : {
    onMouseEnter: e => {
      if (hoverBg[variant]) e.currentTarget.style.background = `var(${hoverBg[variant] === 'var(--brand-accent-hover)' ? '--brand-accent-hover' : hoverBg[variant] === 'var(--brand-primary-hover)' ? '--brand-primary-hover' : '--whatsapp-hover'})`;
      if (variant === 'primary' || variant === 'primaryDark') e.currentTarget.style.transform = 'translateY(-2px)';
      if (variant === 'outline') e.currentTarget.style.background = 'rgba(255,255,255,.1)';
    },
    onMouseLeave: e => {
      e.currentTarget.style.background = v.background;
      e.currentTarget.style.transform = 'none';
    }
  };
  const Tag = href ? 'a' : 'button';
  return /*#__PURE__*/React.createElement(Tag, _extends({
    href: href,
    onClick: onClick,
    disabled: disabled,
    style: style
  }, handlers), children, icon);
}
Object.assign(__ds_scope, { Button });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/Button.jsx", error: String((e && e.message) || e) }); }

// components/core/Icon.jsx
try { (() => {
/* Thin wrapper around Lucide's global UMD build (window.lucide) loaded via CDN by consumers. */
function Icon({
  name,
  size = 20,
  color = 'currentColor',
  strokeWidth = 2
}) {
  const ref = React.useRef(null);
  React.useEffect(() => {
    if (ref.current && window.lucide) {
      ref.current.innerHTML = '';
      const el = document.createElement('i');
      el.setAttribute('data-lucide', name);
      ref.current.appendChild(el);
      window.lucide.createIcons({
        nameAttr: 'data-lucide',
        attrs: {
          width: size,
          height: size,
          color,
          'stroke-width': strokeWidth
        }
      });
    }
  }, [name, size, color]);
  return /*#__PURE__*/React.createElement("span", {
    ref: ref,
    style: {
      display: 'inline-flex',
      width: size,
      height: size
    }
  });
}
Object.assign(__ds_scope, { Icon });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/Icon.jsx", error: String((e && e.message) || e) }); }

// components/cards/SedeCard.jsx
try { (() => {
function SedeCard({
  name,
  tag,
  featured,
  rows = [],
  contacts
}) {
  if (featured) {
    return /*#__PURE__*/React.createElement("div", {
      style: {
        gridColumn: '1 / -1',
        background: 'linear-gradient(135deg, var(--brand-primary) 0%, var(--blue-700) 100%)',
        borderRadius: 'var(--radius-card-lg)',
        padding: 32,
        color: '#fff',
        boxShadow: 'var(--shadow-hero-card)',
        position: 'relative',
        overflow: 'hidden'
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'flex-start',
        marginBottom: 20
      }
    }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
      style: {
        color: 'var(--green-100)',
        fontSize: 12,
        fontWeight: 700,
        textTransform: 'uppercase',
        letterSpacing: 1
      }
    }, tag), /*#__PURE__*/React.createElement("div", {
      style: {
        fontFamily: 'var(--font-display)',
        fontWeight: 900,
        fontSize: 28
      }
    }, name)), /*#__PURE__*/React.createElement("span", {
      style: {
        background: 'var(--brand-accent)',
        fontSize: 12,
        fontWeight: 700,
        padding: '5px 12px',
        borderRadius: 'var(--radius-badge)'
      }
    }, "\u2605 Casa Central")), /*#__PURE__*/React.createElement("div", {
      style: {
        display: 'grid',
        gridTemplateColumns: contacts ? '1fr 1fr' : '1fr',
        gap: 24
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        display: 'flex',
        flexDirection: 'column',
        gap: 10
      }
    }, rows.map((r, i) => /*#__PURE__*/React.createElement("div", {
      key: i,
      style: {
        display: 'flex',
        gap: 10,
        alignItems: 'flex-start',
        paddingBottom: 10,
        borderBottom: i < rows.length - 1 ? '1px solid rgba(255,255,255,.1)' : 'none'
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        width: 28,
        height: 28,
        borderRadius: 6,
        background: 'rgba(255,255,255,.1)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        flexShrink: 0
      }
    }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
      name: r.icon,
      size: 14,
      color: "#fff"
    })), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: 10.5,
        textTransform: 'uppercase',
        opacity: .6,
        letterSpacing: .5
      }
    }, r.label), /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: 14
      }
    }, r.value))))), contacts && /*#__PURE__*/React.createElement("div", {
      style: {
        display: 'flex',
        flexDirection: 'column',
        gap: 8,
        maxHeight: 220,
        overflow: 'auto'
      }
    }, contacts.map((c, i) => /*#__PURE__*/React.createElement("div", {
      key: i,
      style: {
        background: 'rgba(255,255,255,.06)',
        borderRadius: 8,
        padding: '10px 12px'
      }
    }, /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: 13,
        fontWeight: 700
      }
    }, c.name, " ", /*#__PURE__*/React.createElement("span", {
      style: {
        background: 'var(--brand-accent)',
        fontSize: 10,
        fontWeight: 700,
        padding: '2px 6px',
        borderRadius: 10,
        marginLeft: 6
      }
    }, c.role)), /*#__PURE__*/React.createElement("div", {
      style: {
        fontSize: 12,
        opacity: .8
      }
    }, c.tel, " \xB7 ", c.email))))));
  }
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      border: '1px solid var(--border-card)',
      borderRadius: 'var(--radius-card)',
      padding: '24px 26px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      color: 'var(--brand-accent)',
      fontSize: 12,
      fontWeight: 700,
      textTransform: 'uppercase',
      marginBottom: 6
    }
  }, tag), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: 'var(--font-display)',
      fontWeight: 900,
      fontSize: 19,
      marginBottom: 12
    }
  }, name), rows.map((r, i) => /*#__PURE__*/React.createElement("div", {
    key: i,
    style: {
      display: 'flex',
      gap: 10,
      alignItems: 'flex-start',
      padding: '8px 0',
      borderBottom: i < rows.length - 1 ? '1px dashed var(--border-card)' : 'none'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: r.icon,
    size: 15,
    color: "var(--blue-600)"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 13.5,
      color: 'var(--text-secondary)'
    }
  }, r.value))));
}
Object.assign(__ds_scope, { SedeCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/SedeCard.jsx", error: String((e && e.message) || e) }); }

// components/cards/SubcomisionCard.jsx
try { (() => {
function initials(name) {
  const p = name.trim().split(' ');
  return (p[0][0] + (p[p.length - 1][0] || '')).toUpperCase();
}
function SubcomisionCard({
  tag,
  name,
  referentes = []
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      border: '1px solid var(--border-card)',
      borderRadius: 'var(--radius-card-lg)',
      boxShadow: 'var(--shadow-card)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      padding: '18px 22px 16px',
      borderBottom: '1px solid var(--surface-alt)',
      background: 'linear-gradient(135deg, #f7faf9, #fff)',
      display: 'flex',
      gap: 14,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 44,
      height: 44,
      borderRadius: 8,
      background: 'var(--blue-100)',
      color: 'var(--blue-700)',
      fontFamily: 'var(--font-display)',
      fontWeight: 700,
      fontSize: tag.length > 3 ? 11 : 13,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexShrink: 0
    }
  }, tag), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      color: 'var(--brand-accent)',
      fontSize: 11,
      fontWeight: 700,
      textTransform: 'uppercase'
    }
  }, "Subcomisi\xF3n"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: 'var(--font-display)',
      fontWeight: 800,
      fontSize: 16,
      color: 'var(--brand-primary)',
      lineHeight: 1.25
    }
  }, name))), /*#__PURE__*/React.createElement("div", {
    style: {
      padding: '6px 22px 20px'
    }
  }, referentes.map((r, i) => /*#__PURE__*/React.createElement("div", {
    key: i,
    style: {
      display: 'flex',
      gap: 12,
      alignItems: 'center',
      padding: '10px 0',
      borderTop: i > 0 ? '1px dashed var(--border-card)' : 'none'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 38,
      height: 38,
      borderRadius: '50%',
      background: 'linear-gradient(135deg, var(--blue-100), #c0e2ca)',
      color: 'var(--blue-700)',
      fontFamily: 'var(--font-display)',
      fontWeight: 700,
      fontSize: 13,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexShrink: 0
    }
  }, initials(r.name)), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 13.5,
      fontWeight: 800
    }
  }, "Ing. ", r.name), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 11.5,
      color: 'var(--text-muted)',
      fontFamily: 'var(--font-display)'
    }
  }, "MAT. ", r.mat), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 10,
      marginTop: 2
    }
  }, /*#__PURE__*/React.createElement("a", {
    href: `tel:${r.tel}`,
    style: {
      fontSize: 12,
      color: 'var(--text-secondary)',
      display: 'flex',
      gap: 4,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "phone",
    size: 12
  }), r.tel), /*#__PURE__*/React.createElement("a", {
    href: `mailto:${r.email}`,
    style: {
      fontSize: 12,
      color: 'var(--blue-600)',
      display: 'flex',
      gap: 4,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "mail",
    size: 12
  }), r.email)))))));
}
Object.assign(__ds_scope, { SubcomisionCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/SubcomisionCard.jsx", error: String((e && e.message) || e) }); }

// components/core/IconBadge.jsx
try { (() => {
function IconBadge({
  icon,
  color = 'var(--brand-primary)',
  size = 44
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      width: size,
      height: size,
      borderRadius: 'var(--radius-icon)',
      background: `color-mix(in srgb, ${color} 15%, white)`,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexShrink: 0
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: icon,
    size: Math.round(size * 0.5),
    color: color
  }));
}
Object.assign(__ds_scope, { IconBadge });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/IconBadge.jsx", error: String((e && e.message) || e) }); }

// components/cards/QuickAccessCard.jsx
try { (() => {
function QuickAccessCard({
  icon,
  color,
  label,
  description,
  href = '#'
}) {
  const [hover, setHover] = React.useState(false);
  return /*#__PURE__*/React.createElement("a", {
    href: href,
    onMouseEnter: () => setHover(true),
    onMouseLeave: () => setHover(false),
    style: {
      background: '#fff',
      borderRadius: 'var(--radius-card)',
      padding: '24px 18px',
      border: `1px solid ${hover ? color : 'var(--border-card)'}`,
      display: 'flex',
      flexDirection: 'column',
      gap: 12,
      transition: 'all .25s',
      boxShadow: hover ? 'var(--shadow-hover)' : 'var(--shadow-card)',
      transform: hover ? 'translateY(-4px)' : 'none',
      textDecoration: 'none'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.IconBadge, {
    icon: icon,
    color: color
  }), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      fontWeight: 800,
      fontSize: 14.5,
      color: 'var(--text-primary)',
      marginBottom: 5
    }
  }, label), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12.5,
      color: 'var(--text-secondary)',
      lineHeight: 1.5
    }
  }, description)), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: 'auto',
      display: 'flex',
      alignItems: 'center',
      gap: 4,
      color,
      fontSize: 12.5,
      fontWeight: 700
    }
  }, "Acceder ", /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "chevron-right",
    size: 13,
    color: color
  })));
}
Object.assign(__ds_scope, { QuickAccessCard });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/cards/QuickAccessCard.jsx", error: String((e && e.message) || e) }); }

// components/core/SectionHeader.jsx
try { (() => {
function SectionHeader({
  eyebrow,
  title,
  description,
  align = 'left',
  dark
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      textAlign: align,
      maxWidth: align === 'center' ? 640 : 'none',
      margin: align === 'center' ? '0 auto' : 0
    }
  }, eyebrow && /*#__PURE__*/React.createElement("div", {
    style: {
      color: 'var(--brand-accent)',
      fontSize: 12,
      fontWeight: 700,
      letterSpacing: 2,
      textTransform: 'uppercase',
      marginBottom: 8
    }
  }, eyebrow), title && /*#__PURE__*/React.createElement("h2", {
    style: {
      fontSize: 'var(--text-xl)',
      fontWeight: 900,
      color: dark ? '#fff' : 'var(--brand-primary)',
      fontFamily: 'var(--font-display)'
    }
  }, title), description && /*#__PURE__*/React.createElement("p", {
    style: {
      fontSize: 15,
      lineHeight: 1.6,
      color: dark ? 'rgba(255,255,255,.8)' : 'var(--text-secondary)',
      marginTop: 10
    }
  }, description));
}
Object.assign(__ds_scope, { SectionHeader });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/core/SectionHeader.jsx", error: String((e && e.message) || e) }); }

// components/data/CourseRow.jsx
try { (() => {
function CourseRow({
  title,
  date,
  modality,
  vacantes
}) {
  const low = vacantes < 10;
  return /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      alignItems: 'center',
      gap: 16,
      padding: '16px 20px',
      background: '#fff',
      borderRadius: 'var(--radius-card)',
      border: '1px solid var(--border-card)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 44,
      height: 44,
      borderRadius: 'var(--radius-icon)',
      background: 'var(--blue-100)',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexShrink: 0
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "book",
    size: 20,
    color: "var(--blue-700)"
  })), /*#__PURE__*/React.createElement("div", {
    style: {
      flex: 1
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontWeight: 800,
      fontSize: 14.5
    }
  }, title), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12.5,
      color: 'var(--text-muted)',
      display: 'flex',
      gap: 6,
      alignItems: 'center',
      marginTop: 2
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "calendar",
    size: 12
  }), date)), /*#__PURE__*/React.createElement("span", {
    style: {
      fontSize: 12,
      fontWeight: 700,
      padding: '5px 12px',
      borderRadius: 'var(--radius-badge)',
      background: modality === 'Virtual' ? 'var(--green-100)' : 'var(--purple-100)',
      color: modality === 'Virtual' ? 'var(--green-700)' : 'var(--purple-500)'
    }
  }, modality), /*#__PURE__*/React.createElement("span", {
    style: {
      fontSize: 12.5,
      fontWeight: 700,
      color: low ? '#b06a12' : 'var(--green-700)'
    }
  }, vacantes, " vacantes"), /*#__PURE__*/React.createElement("button", {
    style: {
      background: 'var(--brand-accent)',
      color: '#fff',
      fontSize: 12.5,
      fontWeight: 700,
      padding: '8px 16px',
      borderRadius: 'var(--radius-btn)'
    }
  }, "Inscribirse"));
}
Object.assign(__ds_scope, { CourseRow });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/data/CourseRow.jsx", error: String((e && e.message) || e) }); }

// components/data/DocumentRow.jsx
try { (() => {
const catToken = c => '--cat-' + c.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
function DocumentRow({
  category,
  number,
  title,
  date,
  pages,
  size
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: '52px 1fr auto auto auto',
      gap: 16,
      padding: '16px 20px',
      alignItems: 'center',
      borderBottom: '1px solid var(--border-card)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 44,
      height: 52,
      borderRadius: 6,
      background: `color-mix(in srgb, var(${catToken(category)}, var(--brand-primary)) 12%, white)`,
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
      justifyContent: 'center',
      gap: 2
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "file-text",
    size: 16
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      fontSize: 9,
      fontWeight: 700
    }
  }, "PDF")), /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 8,
      alignItems: 'center',
      marginBottom: 4
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Badge, {
    category: category,
    tone: "tint"
  }, category), /*#__PURE__*/React.createElement("span", {
    style: {
      fontFamily: 'var(--font-display)',
      fontSize: 12.5,
      color: 'var(--text-muted)'
    }
  }, number)), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 14.5,
      fontWeight: 700
    }
  }, title)), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 13,
      color: 'var(--text-secondary)',
      display: 'flex',
      gap: 5,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "calendar",
    size: 13
  }), date), /*#__PURE__*/React.createElement("div", {
    style: {
      fontFamily: 'var(--font-display)',
      fontSize: 12,
      color: 'var(--text-muted)'
    }
  }, pages, " p\xE1g \xB7 ", size), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("button", {
    style: {
      background: 'transparent',
      border: '1.5px solid var(--blue-600)',
      color: 'var(--blue-600)',
      fontSize: 12.5,
      fontWeight: 700,
      padding: '7px 14px',
      borderRadius: 'var(--radius-btn)'
    }
  }, "Ver"), /*#__PURE__*/React.createElement("button", {
    style: {
      background: 'var(--brand-primary)',
      color: '#fff',
      fontSize: 12.5,
      fontWeight: 700,
      padding: '7px 14px',
      borderRadius: 'var(--radius-btn)'
    }
  }, "PDF")));
}
Object.assign(__ds_scope, { DocumentRow });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/data/DocumentRow.jsx", error: String((e && e.message) || e) }); }

// components/data/PartidoChip.jsx
try { (() => {
function PartidoChip({
  name,
  active
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 6,
      alignItems: 'center',
      background: active ? '#e8f5f1' : 'var(--surface-page)',
      border: `1px solid ${active ? 'var(--brand-accent)' : 'transparent'}`,
      borderRadius: 8,
      padding: '8px 12px',
      fontSize: 13.5,
      color: active ? 'var(--brand-primary)' : 'var(--text-primary)'
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      width: 6,
      height: 6,
      borderRadius: '50%',
      background: 'var(--brand-accent)',
      flexShrink: 0
    }
  }), name);
}
Object.assign(__ds_scope, { PartidoChip });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/data/PartidoChip.jsx", error: String((e && e.message) || e) }); }

// components/navigation/Breadcrumb.jsx
try { (() => {
function Breadcrumb({
  items
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 8,
      alignItems: 'center',
      fontSize: 13,
      color: 'rgba(255,255,255,.65)'
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "home",
    size: 13
  }), items.map((it, i) => /*#__PURE__*/React.createElement(React.Fragment, {
    key: i
  }, i > 0 && /*#__PURE__*/React.createElement("span", null, "\u203A"), it.href ? /*#__PURE__*/React.createElement("a", {
    href: it.href,
    style: {
      color: 'rgba(255,255,255,.65)'
    }
  }, it.label) : /*#__PURE__*/React.createElement("span", {
    style: {
      color: '#fff',
      fontWeight: 700
    }
  }, it.label))));
}
Object.assign(__ds_scope, { Breadcrumb });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/Breadcrumb.jsx", error: String((e && e.message) || e) }); }

// components/navigation/Footer.jsx
try { (() => {
const cols = [{
  title: 'Trámites',
  links: ['Nueva Matrícula', 'Visado Online', 'Honorarios Mínimos', 'Certificados']
}, {
  title: 'Institucional',
  links: ['Autoridades', 'Partidos comprendidos', 'Sedes y delegaciones', 'Subcomisiones']
}, {
  title: 'Servicios',
  links: ['Capacitación', 'Beneficios', 'Seguro Profesional', 'Normativa']
}];
function Footer({
  logo = 'assets/logo-cipba-vii.png'
}) {
  return /*#__PURE__*/React.createElement("footer", {
    style: {
      background: 'var(--surface-darker)',
      color: 'rgba(255,255,255,.7)',
      padding: '56px 24px 0'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 'var(--max-width)',
      margin: '0 auto',
      display: 'grid',
      gridTemplateColumns: 'repeat(4, 1fr)',
      gap: 32
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("img", {
    src: logo,
    style: {
      width: 230,
      height: 'auto',
      filter: 'brightness(0) invert(1)',
      opacity: .9,
      marginBottom: 12
    }
  }), /*#__PURE__*/React.createElement("p", {
    style: {
      fontSize: 13,
      lineHeight: 1.6
    }
  }, "Colegio de Ingenieros de la Provincia de Buenos Aires \u2014 Distrito VII")), cols.map(c => /*#__PURE__*/React.createElement("div", {
    key: c.title
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      color: '#fff',
      fontWeight: 800,
      fontSize: 13,
      textTransform: 'uppercase',
      letterSpacing: .5,
      marginBottom: 14
    }
  }, c.title), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      flexDirection: 'column',
      gap: 10
    }
  }, c.links.map(l => /*#__PURE__*/React.createElement("a", {
    key: l,
    href: "#",
    style: {
      fontSize: 13.5,
      color: 'rgba(255,255,255,.7)'
    }
  }, l)))))), /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 'var(--max-width)',
      margin: '40px auto 0',
      borderTop: '1px solid rgba(255,255,255,.1)',
      padding: '18px 0',
      fontSize: 12.5,
      textAlign: 'center'
    }
  }, "\xA9 ", new Date().getFullYear(), " Colegio de Ingenieros de la Provincia de Buenos Aires \u2014 Distrito VII"));
}
Object.assign(__ds_scope, { Footer });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/Footer.jsx", error: String((e && e.message) || e) }); }

// components/navigation/Navbar.jsx
try { (() => {
const defaultLinks = [{
  label: 'Inicio',
  href: '#inicio'
}, {
  label: 'Matrícula',
  href: '#tramites',
  sub: ['Nueva Matrícula', 'Reinscripción', 'Baja Temporal', 'Certificados']
}, {
  label: 'Visado',
  href: '#visado',
  sub: ['Visado Online', 'Tasas y Aranceles', 'Honorarios Mínimos', 'Visadores']
}, {
  label: 'Capacitación',
  href: '#capacitacion'
}, {
  label: 'Normativa',
  href: '#normativa'
}, {
  label: 'Institucional',
  href: '#institucional',
  sub: ['Autoridades', 'Partidos comprendidos', 'Sedes y delegaciones', 'Subcomisiones']
}, {
  label: 'Noticias',
  href: '#noticias'
}, {
  label: 'Contacto',
  href: '#contacto'
}];
function Navbar({
  logo = 'assets/logo-cipba-vii.png',
  links = defaultLinks,
  activeLabel,
  scrolled = false
}) {
  const [open, setOpen] = React.useState(false);
  const [activeMenu, setActiveMenu] = React.useState(null);
  return /*#__PURE__*/React.createElement("nav", {
    style: {
      position: 'sticky',
      top: 0,
      zIndex: 100,
      background: scrolled ? 'rgba(20,72,74,.98)' : 'var(--blue-700)',
      backdropFilter: 'blur(8px)',
      boxShadow: scrolled ? 'var(--shadow-nav)' : 'none',
      transition: 'all .3s ease',
      borderBottom: '3px solid var(--brand-accent)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 'var(--max-width)',
      margin: '0 auto',
      padding: '8px 20px',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
      flexWrap: 'wrap',
      gap: 8,
      minHeight: 64
    }
  }, /*#__PURE__*/React.createElement("a", {
    href: "#inicio",
    style: {
      display: 'flex',
      alignItems: 'center',
      flexShrink: 0
    }
  }, /*#__PURE__*/React.createElement("img", {
    src: logo,
    alt: "CIPBA Distrito VII",
    style: {
      width: 155,
      maxWidth: 'none',
      height: 'auto',
      flexShrink: 0,
      filter: 'brightness(0) invert(1)'
    }
  })), /*#__PURE__*/React.createElement("div", {
    className: "nav-links",
    style: {
      display: 'flex',
      gap: 2,
      alignItems: 'center',
      flexWrap: 'wrap',
      justifyContent: 'flex-end',
      flex: '1 1 auto',
      minWidth: 0
    }
  }, links.map(link => {
    const isActive = link.label === activeLabel;
    return /*#__PURE__*/React.createElement("div", {
      key: link.label,
      style: {
        position: 'relative'
      },
      onMouseEnter: () => setActiveMenu(link.label),
      onMouseLeave: () => setActiveMenu(null)
    }, /*#__PURE__*/React.createElement("a", {
      href: link.href,
      style: {
        color: isActive ? '#fff' : 'rgba(255,255,255,.88)',
        fontSize: 13.5,
        fontWeight: isActive ? 800 : 600,
        padding: '8px 9px',
        display: 'flex',
        alignItems: 'center',
        gap: 3,
        borderRadius: 4,
        background: isActive ? 'rgba(0,164,138,.28)' : activeMenu === link.label ? 'rgba(255,255,255,.12)' : 'transparent',
        borderBottom: isActive ? '2px solid var(--brand-accent)' : 'none'
      }
    }, link.label, link.sub && /*#__PURE__*/React.createElement(__ds_scope.Icon, {
      name: "chevron-down",
      size: 13,
      color: "rgba(255,255,255,.6)"
    })), link.sub && activeMenu === link.label && /*#__PURE__*/React.createElement("div", {
      style: {
        position: 'absolute',
        top: '100%',
        left: 0,
        minWidth: 200,
        background: '#fff',
        boxShadow: '0 8px 32px rgba(0,0,0,.15)',
        borderRadius: 6,
        overflow: 'hidden',
        borderTop: '3px solid var(--brand-accent)'
      }
    }, link.sub.map(s => /*#__PURE__*/React.createElement("a", {
      key: s,
      href: "#",
      style: {
        display: 'block',
        padding: '10px 16px',
        fontSize: 13.5,
        color: 'var(--text-primary)',
        borderBottom: '1px solid var(--surface-alt)'
      }
    }, s))));
  }), /*#__PURE__*/React.createElement("a", {
    href: "#",
    style: {
      background: 'var(--brand-accent)',
      color: '#fff',
      fontSize: 13,
      fontWeight: 700,
      padding: '8px 16px',
      borderRadius: 5,
      marginLeft: 8,
      display: 'flex',
      alignItems: 'center',
      gap: 5
    }
  }, "Visado Online ", /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "external-link",
    size: 13,
    color: "#fff"
  }))), open && /*#__PURE__*/React.createElement("div", {
    className: "nav-drawer",
    style: {
      flexBasis: '100%',
      display: 'flex',
      flexDirection: 'column',
      gap: 2,
      padding: '8px 0 12px'
    }
  }, links.map(link => /*#__PURE__*/React.createElement("a", {
    key: link.label,
    href: link.href,
    style: {
      color: 'rgba(255,255,255,.92)',
      fontSize: 15,
      fontWeight: link.label === activeLabel ? 800 : 600,
      padding: '11px 4px',
      borderBottom: '1px solid rgba(255,255,255,.12)'
    }
  }, link.label)), /*#__PURE__*/React.createElement("a", {
    href: "#",
    style: {
      background: 'var(--brand-accent)',
      color: '#fff',
      fontSize: 14,
      fontWeight: 700,
      padding: '12px 16px',
      borderRadius: 5,
      marginTop: 8,
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      gap: 6
    }
  }, "Visado Online ", /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "external-link",
    size: 14,
    color: "#fff"
  }))), /*#__PURE__*/React.createElement("button", {
    onClick: () => setOpen(!open),
    style: {
      background: 'none',
      color: '#fff',
      display: 'none'
    },
    className: "mobile-burger"
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: open ? 'x' : 'menu',
    size: 26,
    color: "#fff"
  }))));
}
Object.assign(__ds_scope, { Navbar });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/Navbar.jsx", error: String((e && e.message) || e) }); }

// components/navigation/TopBar.jsx
try { (() => {
function TopBar({
  phone = '(011) 4651-0064 / 4482-2231',
  email = 'info@cipba.org',
  hours = 'Lun–Vie 9 a 16 hs'
}) {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: 'var(--surface-dark)',
      color: 'rgba(255,255,255,.85)',
      fontSize: 13,
      borderBottom: '1px solid rgba(255,255,255,.1)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 'var(--max-width)',
      margin: '0 auto',
      padding: '7px 20px',
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'center',
      flexWrap: 'wrap',
      gap: 8
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 24,
      alignItems: 'center',
      flexWrap: 'wrap'
    }
  }, /*#__PURE__*/React.createElement("span", {
    style: {
      display: 'flex',
      alignItems: 'center',
      gap: 6
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "phone",
    size: 13
  }), phone), /*#__PURE__*/React.createElement("span", {
    style: {
      display: 'flex',
      alignItems: 'center',
      gap: 6
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "mail",
    size: 13
  }), email), /*#__PURE__*/React.createElement("span", {
    style: {
      display: 'flex',
      alignItems: 'center',
      gap: 6
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "clock",
    size: 13
  }), hours)), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 12,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("a", {
    href: "#",
    style: {
      opacity: .7
    }
  }, /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "message-circle",
    size: 15,
    color: "#fff"
  })), /*#__PURE__*/React.createElement("a", {
    href: "http://www.colegioingenieros.org.ar/",
    target: "_blank",
    rel: "noreferrer",
    style: {
      fontSize: 12,
      opacity: .7,
      display: 'flex',
      gap: 4,
      alignItems: 'center',
      color: '#fff'
    }
  }, "Consejo Superior ", /*#__PURE__*/React.createElement(__ds_scope.Icon, {
    name: "external-link",
    size: 11,
    color: "#fff"
  })))));
}
Object.assign(__ds_scope, { TopBar });
})(); } catch (e) { __ds_ns.__errors.push({ path: "components/navigation/TopBar.jsx", error: String((e && e.message) || e) }); }

// ui_kits/sitio-web/HomePage.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
const {
  useState,
  useEffect
} = React;
const NS = window.CIPBADistritoVIIDesignSystem_1a5d5e;
const {
  Button,
  Badge,
  SectionHeader,
  IconBadge,
  Icon
} = NS;
const {
  Card,
  StatCard,
  QuickAccessCard,
  NewsCard
} = NS;
const {
  DocumentRow,
  CourseRow
} = NS;
const {
  TopBar,
  Navbar,
  Footer
} = NS;
const D = window.CIPBA_DATA;
const slides = [{
  tag: 'Trámites digitales',
  title: 'Visado Online para Ingenieros Matriculados',
  desc: 'Realizá el visado de tus trabajos profesionales en línea, cualquier día del año, con descuento del 20% sobre la tasa presencial.',
  cta: 'Acceder al sistema',
  cta2: 'Cómo funciona',
  bg: 'linear-gradient(135deg, #14484a 0%, #1a5f5c 60%, #00a48a 100%)'
}, {
  tag: 'Matrícula profesional',
  title: 'Habilitá tu ejercicio profesional en el Distrito VII',
  desc: 'Inscribite al Colegio de Ingenieros y ejercé legalmente tu profesión en los 24 partidos del Distrito VII.',
  cta: 'Solicitar matrícula',
  cta2: 'Requisitos',
  bg: 'linear-gradient(135deg, #0a2247 0%, #14484a 50%, #14458a 100%)'
}];
function Hero() {
  const [slide, setSlide] = useState(0);
  useEffect(() => {
    const t = setInterval(() => setSlide(p => (p + 1) % slides.length), 6000);
    return () => clearInterval(t);
  }, []);
  const s = slides[slide];
  return /*#__PURE__*/React.createElement("section", {
    id: "inicio",
    style: {
      position: 'relative',
      minHeight: 480,
      background: s.bg,
      transition: 'background .8s',
      display: 'flex',
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      position: 'absolute',
      inset: 0,
      opacity: .06,
      backgroundImage: "url(../../assets/pattern.svg)",
      backgroundSize: '60px'
    }
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto',
      padding: '56px 24px',
      position: 'relative',
      width: '100%'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 640
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'inline-flex',
      gap: 8,
      alignItems: 'center',
      background: 'rgba(42,158,42,.2)',
      border: '1px solid rgba(42,158,42,.4)',
      borderRadius: 20,
      padding: '5px 14px',
      marginBottom: 18
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 6,
      height: 6,
      borderRadius: '50%',
      background: 'var(--brand-accent)'
    }
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      color: 'var(--green-100)',
      fontSize: 13,
      fontWeight: 600
    }
  }, s.tag)), /*#__PURE__*/React.createElement("h1", {
    style: {
      color: '#fff',
      fontSize: 'var(--text-hero)',
      fontWeight: 900,
      lineHeight: 1.15,
      marginBottom: 18,
      fontFamily: 'var(--font-display)'
    }
  }, s.title), /*#__PURE__*/React.createElement("p", {
    style: {
      color: 'rgba(255,255,255,.78)',
      fontSize: 17,
      lineHeight: 1.65,
      marginBottom: 30,
      maxWidth: 560
    }
  }, s.desc), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 14,
      flexWrap: 'wrap'
    }
  }, /*#__PURE__*/React.createElement(Button, {
    variant: "primary",
    icon: /*#__PURE__*/React.createElement(Icon, {
      name: "chevron-right",
      size: 16,
      color: "#fff"
    })
  }, s.cta), /*#__PURE__*/React.createElement(Button, {
    variant: "outline"
  }, s.cta2)))), /*#__PURE__*/React.createElement("div", {
    style: {
      position: 'absolute',
      bottom: 20,
      left: '50%',
      transform: 'translateX(-50%)',
      display: 'flex',
      gap: 8
    }
  }, slides.map((_, i) => /*#__PURE__*/React.createElement("button", {
    key: i,
    onClick: () => setSlide(i),
    style: {
      width: i === slide ? 28 : 8,
      height: 8,
      borderRadius: 4,
      background: i === slide ? 'var(--brand-accent)' : 'rgba(255,255,255,.35)',
      border: 'none'
    }
  }))));
}
function StatsStrip() {
  const stats = [{
    n: '+8.500',
    l: 'Matriculados activos'
  }, {
    n: '24',
    l: 'Partidos del Distrito VII'
  }, {
    n: '3',
    l: 'Sedes de atención'
  }, {
    n: '+30',
    l: 'Años de trayectoria'
  }];
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      boxShadow: '0 4px 20px rgba(0,0,0,.06)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto',
      display: 'grid',
      gridTemplateColumns: 'repeat(4,1fr)'
    }
  }, stats.map((s, i) => /*#__PURE__*/React.createElement(StatCard, {
    key: i,
    number: s.n,
    label: s.l,
    bordered: i < 3
  }))));
}
function QuickAccess() {
  return /*#__PURE__*/React.createElement("section", {
    id: "tramites",
    style: {
      background: 'var(--surface-alt)',
      padding: 'var(--section-padding-y)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      marginBottom: 32,
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'flex-end',
      flexWrap: 'wrap',
      gap: 12
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Acceso r\xE1pido",
    title: "Tr\xE1mites y servicios"
  }), /*#__PURE__*/React.createElement("a", {
    href: "#",
    style: {
      color: 'var(--blue-600)',
      fontSize: 14,
      fontWeight: 600
    }
  }, "Ver todos los tr\xE1mites \u2192")), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))',
      gap: 16
    }
  }, D.quickItems.map(it => /*#__PURE__*/React.createElement(QuickAccessCard, _extends({
    key: it.label
  }, it))))));
}
function Noticias() {
  return /*#__PURE__*/React.createElement("section", {
    id: "noticias",
    style: {
      background: '#fff',
      padding: 'var(--section-padding-lg)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Novedades",
    title: "Noticias"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))',
      gap: 20,
      marginTop: 28
    }
  }, D.noticias.map((n, i) => /*#__PURE__*/React.createElement(NewsCard, {
    key: i,
    category: n.cat,
    date: n.date,
    title: n.title,
    description: n.desc,
    highlight: n.highlight
  })))));
}
function VisadoBanner() {
  return /*#__PURE__*/React.createElement("section", {
    id: "visado",
    style: {
      background: 'linear-gradient(135deg, var(--brand-primary), var(--blue-700))',
      padding: '56px 24px',
      color: '#fff'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto',
      display: 'grid',
      gridTemplateColumns: '1.2fr 1fr',
      gap: 40,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    style: {
      color: 'var(--green-100)',
      fontSize: 12,
      fontWeight: 700,
      textTransform: 'uppercase',
      letterSpacing: 2,
      marginBottom: 8
    }
  }, "Visado Online"), /*#__PURE__*/React.createElement("h2", {
    style: {
      fontFamily: 'var(--font-display)',
      fontWeight: 900,
      fontSize: 'var(--text-xl)',
      marginBottom: 14
    }
  }, "Vis\xE1 tus trabajos profesionales sin salir de tu estudio"), /*#__PURE__*/React.createElement("p", {
    style: {
      color: 'rgba(255,255,255,.8)',
      lineHeight: 1.6,
      marginBottom: 24,
      maxWidth: 460
    }
  }, "Sistema disponible las 24 horas, los 365 d\xEDas del a\xF1o, con 20% de descuento sobre la tasa presencial."), /*#__PURE__*/React.createElement(Button, {
    variant: "primary"
  }, "Acceder al sistema")), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      flexDirection: 'column',
      gap: 12
    }
  }, ['Carga de planos en formato DWG', 'Notificaciones automáticas por e-mail', 'Seguimiento de estado en tiempo real', 'Descuento del 20% sobre la tasa presencial'].map((f, i) => /*#__PURE__*/React.createElement("div", {
    key: i,
    style: {
      display: 'flex',
      gap: 10,
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 22,
      height: 22,
      borderRadius: '50%',
      background: 'var(--brand-accent)',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      flexShrink: 0
    }
  }, /*#__PURE__*/React.createElement(Icon, {
    name: "check",
    size: 13,
    color: "#fff"
  })), /*#__PURE__*/React.createElement("span", {
    style: {
      fontSize: 14.5
    }
  }, f))))));
}
function Capacitacion() {
  return /*#__PURE__*/React.createElement("section", {
    id: "capacitacion",
    style: {
      background: 'var(--surface-alt)',
      padding: 'var(--section-padding-lg)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Formaci\xF3n continua",
    title: "Capacitaci\xF3n"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      flexDirection: 'column',
      gap: 12,
      marginTop: 28
    }
  }, D.cursos.map((c, i) => /*#__PURE__*/React.createElement(CourseRow, _extends({
    key: i
  }, c))))));
}
function Normativa() {
  const [q, setQ] = useState('');
  const [cat, setCat] = useState('Todas');
  const cats = ['Todas', ...Array.from(new Set(D.documentos.map(d => d.category)))];
  const filtered = D.documentos.filter(d => (cat === 'Todas' || d.category === cat) && (d.title.toLowerCase().includes(q.toLowerCase()) || d.number.toLowerCase().includes(q.toLowerCase())));
  return /*#__PURE__*/React.createElement("section", {
    id: "normativa",
    style: {
      background: '#fff',
      padding: 'var(--section-padding-lg)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Marco normativo",
    title: "Reglamentaciones y Resoluciones",
    description: "Consult\xE1 y descarg\xE1 las resoluciones, reglamentos y disposiciones vigentes del distrito."
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 12,
      alignItems: 'center',
      flexWrap: 'wrap',
      margin: '24px 0 8px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      alignItems: 'center',
      gap: 8,
      border: '1.5px solid var(--border-input)',
      borderRadius: 8,
      padding: '8px 14px',
      minWidth: 260
    }
  }, /*#__PURE__*/React.createElement(Icon, {
    name: "search",
    size: 15,
    color: "var(--text-muted)"
  }), /*#__PURE__*/React.createElement("input", {
    value: q,
    onChange: e => setQ(e.target.value),
    placeholder: "Buscar por t\xEDtulo o n\xFAmero\u2026",
    style: {
      border: 'none',
      outline: 'none',
      fontSize: 13.5,
      flex: 1,
      fontFamily: 'var(--font-body)'
    }
  })), cats.map(c => /*#__PURE__*/React.createElement("button", {
    key: c,
    onClick: () => setCat(c),
    style: {
      background: cat === c ? 'var(--brand-primary)' : '#fff',
      color: cat === c ? '#fff' : 'var(--text-secondary)',
      border: `1px solid ${cat === c ? 'var(--brand-primary)' : 'var(--border-input)'}`,
      borderRadius: 20,
      padding: '6px 14px',
      fontSize: 12.5,
      fontWeight: 600
    }
  }, c))), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12.5,
      color: 'var(--text-muted)',
      marginBottom: 12
    }
  }, filtered.length, " documentos encontrados"), /*#__PURE__*/React.createElement("div", {
    style: {
      border: '1px solid var(--border-card)',
      borderRadius: 'var(--radius-card)',
      overflow: 'hidden'
    }
  }, filtered.map((d, i) => /*#__PURE__*/React.createElement(DocumentRow, _extends({
    key: i
  }, d))))));
}
function Contacto() {
  const [sent, setSent] = useState(false);
  return /*#__PURE__*/React.createElement("section", {
    id: "contacto",
    style: {
      background: 'var(--surface-alt)',
      padding: 'var(--section-padding-lg)'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto',
      display: 'grid',
      gridTemplateColumns: '1fr 1fr',
      gap: 40
    }
  }, /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Estamos para ayudarte",
    title: "Contacto"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      flexDirection: 'column',
      gap: 14,
      marginTop: 24
    }
  }, /*#__PURE__*/React.createElement(Card, {
    padding: 18
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontWeight: 800,
      marginBottom: 6
    }
  }, "Administrativa"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 13.5,
      color: 'var(--text-secondary)'
    }
  }, "info@cipba.org \xB7 (011) 3535-0751")), /*#__PURE__*/React.createElement(Card, {
    padding: 18
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      fontWeight: 800,
      marginBottom: 6
    }
  }, "T\xE9cnica"), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 13.5,
      color: 'var(--text-secondary)'
    }
  }, "tecnica@cipba.org")), /*#__PURE__*/React.createElement(Button, {
    variant: "whatsapp",
    icon: /*#__PURE__*/React.createElement(Icon, {
      name: "message-circle",
      size: 15,
      color: "#fff"
    })
  }, "Escribinos por WhatsApp"))), /*#__PURE__*/React.createElement(Card, {
    padding: 28
  }, sent ? /*#__PURE__*/React.createElement("div", {
    style: {
      textAlign: 'center',
      padding: 20
    }
  }, /*#__PURE__*/React.createElement(Icon, {
    name: "check-circle",
    size: 40,
    color: "var(--brand-accent)"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      fontWeight: 800,
      marginTop: 10
    }
  }, "\xA1Mensaje enviado!")) : /*#__PURE__*/React.createElement("form", {
    onSubmit: e => {
      e.preventDefault();
      setSent(true);
    },
    style: {
      display: 'flex',
      flexDirection: 'column',
      gap: 14
    }
  }, /*#__PURE__*/React.createElement("input", {
    required: true,
    placeholder: "Nombre y apellido",
    style: {
      border: '1px solid var(--border-input)',
      borderRadius: 6,
      padding: '11px 14px',
      fontSize: 14,
      fontFamily: 'var(--font-body)'
    }
  }), /*#__PURE__*/React.createElement("input", {
    required: true,
    type: "email",
    placeholder: "Email",
    style: {
      border: '1px solid var(--border-input)',
      borderRadius: 6,
      padding: '11px 14px',
      fontSize: 14,
      fontFamily: 'var(--font-body)'
    }
  }), /*#__PURE__*/React.createElement("textarea", {
    placeholder: "Mensaje",
    rows: 4,
    style: {
      border: '1px solid var(--border-input)',
      borderRadius: 6,
      padding: '11px 14px',
      fontSize: 14,
      fontFamily: 'var(--font-body)',
      resize: 'vertical'
    }
  }), /*#__PURE__*/React.createElement(Button, {
    variant: "primaryDark"
  }, "Enviar mensaje")))));
}
function HomePage() {
  const [scrolled, setScrolled] = useState(false);
  useEffect(() => {
    const f = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', f);
    return () => window.removeEventListener('scroll', f);
  }, []);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(TopBar, null), /*#__PURE__*/React.createElement(Navbar, {
    scrolled: scrolled,
    logo: "../../assets/logo-cipba-vii.png"
  }), /*#__PURE__*/React.createElement(Hero, null), /*#__PURE__*/React.createElement(StatsStrip, null), /*#__PURE__*/React.createElement(QuickAccess, null), /*#__PURE__*/React.createElement(Noticias, null), /*#__PURE__*/React.createElement(VisadoBanner, null), /*#__PURE__*/React.createElement(Capacitacion, null), /*#__PURE__*/React.createElement(Normativa, null), /*#__PURE__*/React.createElement(Contacto, null), /*#__PURE__*/React.createElement(Footer, {
    logo: "../../assets/logo-cipba-vii.png"
  }));
}
window.HomePage = HomePage;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/sitio-web/HomePage.jsx", error: String((e && e.message) || e) }); }

// ui_kits/sitio-web/InstitucionalPage.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
const {
  useState
} = React;
const NS = window.CIPBADistritoVIIDesignSystem_1a5d5e;
const {
  SectionHeader,
  Icon,
  Button
} = NS;
const {
  AuthorityCard,
  SedeCard
} = NS;
const {
  PartidoChip
} = NS;
const {
  TopBar,
  Navbar,
  Breadcrumb,
  Footer
} = NS;
const D = window.CIPBA_DATA;
function PageHero() {
  return /*#__PURE__*/React.createElement("div", {
    style: {
      background: 'linear-gradient(135deg, #14484a 0%, #1a5f5c 60%, #00a48a 100%)',
      padding: '44px 24px 52px',
      position: 'relative'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(Breadcrumb, {
    items: [{
      label: 'Inicio',
      href: '../../ui_kits/sitio-web/index.html'
    }, {
      label: 'Institucional'
    }]
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'inline-flex',
      gap: 8,
      alignItems: 'center',
      background: 'rgba(42,158,42,.22)',
      borderRadius: 20,
      padding: '5px 14px',
      margin: '18px 0 14px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      width: 6,
      height: 6,
      borderRadius: '50%',
      background: 'var(--brand-accent)'
    }
  }), /*#__PURE__*/React.createElement("span", {
    style: {
      color: 'var(--green-100)',
      fontSize: 13,
      fontWeight: 600
    }
  }, "Qui\xE9nes somos")), /*#__PURE__*/React.createElement("h1", {
    style: {
      color: '#fff',
      fontFamily: 'var(--font-display)',
      fontWeight: 900,
      fontSize: 'clamp(28px,4vw,48px)'
    }
  }, "Distrito VII"), /*#__PURE__*/React.createElement("p", {
    style: {
      color: 'rgba(255,255,255,.82)',
      fontSize: 17,
      maxWidth: 720,
      marginTop: 12,
      lineHeight: 1.6
    }
  }, "Autoridades, jurisdicci\xF3n y sedes del Colegio de Ingenieros de la Provincia de Buenos Aires en la zona oeste y norte del Gran Buenos Aires."), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 10,
      marginTop: 22,
      flexWrap: 'wrap'
    }
  }, ['Autoridades', 'Partidos comprendidos', 'Sedes y delegaciones'].map(l => /*#__PURE__*/React.createElement("a", {
    key: l,
    href: `#${l.toLowerCase().replace(/ /g, '-')}`,
    style: {
      background: 'rgba(255,255,255,.1)',
      border: '1px solid rgba(255,255,255,.25)',
      borderRadius: 20,
      padding: '7px 16px',
      color: '#fff',
      fontSize: 13
    }
  }, l)))));
}
function Autoridades() {
  return /*#__PURE__*/React.createElement("section", {
    id: "autoridades",
    style: {
      background: '#fff',
      padding: '56px 24px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Consejo Directivo",
    title: "Autoridades",
    description: "Per\xEDodo 2024 \u2013 2027",
    align: "center"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fill, minmax(260px,1fr))',
      gap: 18,
      marginTop: 28
    }
  }, D.autoridades.map((a, i) => /*#__PURE__*/React.createElement(AuthorityCard, _extends({
    key: i
  }, a))))));
}
function Partidos() {
  const [q, setQ] = useState('');
  const filtered = D.partidos.filter(p => p.toLowerCase().includes(q.toLowerCase()));
  return /*#__PURE__*/React.createElement("section", {
    id: "partidos-comprendidos",
    style: {
      background: 'var(--surface-alt)',
      padding: '56px 24px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Jurisdicci\xF3n",
    title: "Partidos comprendidos",
    description: "El Distrito VII abarca 24 partidos de la zona oeste y norte del Gran Buenos Aires.",
    align: "center"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: '1fr 1.4fr',
      gap: 24,
      marginTop: 28
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      borderRadius: 12,
      padding: 20,
      boxShadow: 'var(--shadow-card)',
      textAlign: 'center'
    }
  }, /*#__PURE__*/React.createElement(Icon, {
    name: "map",
    size: 64,
    color: "var(--blue-600)"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      fontSize: 12,
      fontStyle: 'italic',
      color: 'var(--text-muted)',
      marginTop: 10
    }
  }, "Distribuci\xF3n aproximada \xB7 No a escala"), /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: 12,
      background: 'var(--green-100)',
      color: 'var(--green-700)',
      fontSize: 12,
      fontWeight: 700,
      padding: '6px 10px',
      borderRadius: 20,
      display: 'inline-block'
    }
  }, "Sede principal: San Justo \xB7 La Matanza")), /*#__PURE__*/React.createElement("div", {
    style: {
      background: '#fff',
      borderRadius: 12,
      padding: 20,
      boxShadow: 'var(--shadow-card)'
    }
  }, /*#__PURE__*/React.createElement("input", {
    value: q,
    onChange: e => setQ(e.target.value),
    placeholder: "Buscar partido\u2026",
    style: {
      width: '100%',
      border: '1px solid var(--border-input)',
      borderRadius: 8,
      padding: '9px 14px',
      fontSize: 13.5,
      marginBottom: 14,
      fontFamily: 'var(--font-body)'
    }
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fill, minmax(160px,1fr))',
      gap: 8
    }
  }, filtered.map(p => /*#__PURE__*/React.createElement(PartidoChip, {
    key: p,
    name: p,
    active: p === 'San Justo'
  })))))));
}
function Sedes() {
  return /*#__PURE__*/React.createElement("section", {
    id: "sedes-y-delegaciones",
    style: {
      background: '#fff',
      padding: '56px 24px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "D\xF3nde estamos",
    title: "Sedes y delegaciones",
    align: "center"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fill, minmax(280px,1fr))',
      gap: 18,
      marginTop: 28
    }
  }, D.sedes.map((s, i) => /*#__PURE__*/React.createElement(SedeCard, _extends({
    key: i
  }, s))))));
}
function CTAFinal() {
  return /*#__PURE__*/React.createElement("section", {
    style: {
      background: 'linear-gradient(135deg, var(--brand-accent), var(--green-700))',
      padding: '48px 24px',
      textAlign: 'center'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 700,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement("h2", {
    style: {
      color: '#fff',
      fontFamily: 'var(--font-display)',
      fontWeight: 900,
      fontSize: 'clamp(20px,3vw,28px)',
      marginBottom: 10
    }
  }, "\xBFTen\xE9s alguna consulta?"), /*#__PURE__*/React.createElement("p", {
    style: {
      color: 'rgba(255,255,255,.85)',
      marginBottom: 22
    }
  }, "Escribinos por el formulario de contacto o directamente por WhatsApp."), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      gap: 12,
      justifyContent: 'center',
      flexWrap: 'wrap'
    }
  }, /*#__PURE__*/React.createElement("a", {
    href: "../../ui_kits/sitio-web/index.html#contacto",
    style: {
      background: '#fff',
      color: 'var(--green-700)',
      fontWeight: 700,
      padding: '13px 24px',
      borderRadius: 6,
      fontSize: 15
    }
  }, "Formulario de contacto"), /*#__PURE__*/React.createElement("a", {
    href: "#",
    style: {
      background: 'rgba(255,255,255,.15)',
      color: '#fff',
      fontWeight: 700,
      padding: '13px 24px',
      borderRadius: 6,
      fontSize: 15
    }
  }, "WhatsApp"))));
}
function InstitucionalPage() {
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(TopBar, null), /*#__PURE__*/React.createElement(Navbar, {
    activeLabel: "Institucional",
    logo: "../../assets/logo-cipba-vii.png"
  }), /*#__PURE__*/React.createElement(PageHero, null), /*#__PURE__*/React.createElement(Autoridades, null), /*#__PURE__*/React.createElement(Partidos, null), /*#__PURE__*/React.createElement(Sedes, null), /*#__PURE__*/React.createElement(CTAFinal, null), /*#__PURE__*/React.createElement(Footer, {
    logo: "../../assets/logo-cipba-vii.png"
  }));
}
window.InstitucionalPage = InstitucionalPage;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/sitio-web/InstitucionalPage.jsx", error: String((e && e.message) || e) }); }

// ui_kits/sitio-web/SubcomisionesPage.jsx
try { (() => {
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
const {
  useState
} = React;
const NS = window.CIPBADistritoVIIDesignSystem_1a5d5e;
const {
  SectionHeader,
  Icon
} = NS;
const {
  SubcomisionCard
} = NS;
const {
  TopBar,
  Navbar,
  Breadcrumb,
  Footer
} = NS;
const D = window.CIPBA_DATA;
function SubcomisionesPage() {
  const [q, setQ] = useState('');
  const filtered = D.subcomisiones.filter(s => (s.name + s.tag + s.referentes.map(r => r.name).join(' ')).toLowerCase().includes(q.toLowerCase()));
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(TopBar, null), /*#__PURE__*/React.createElement(Navbar, {
    activeLabel: "Institucional",
    logo: "../../assets/logo-cipba-vii.png"
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      background: 'linear-gradient(135deg, #14484a 0%, #1a5f5c 60%, #00a48a 100%)',
      padding: '44px 24px 52px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement(Breadcrumb, {
    items: [{
      label: 'Inicio',
      href: '../../ui_kits/sitio-web/index.html'
    }, {
      label: 'Institucional',
      href: '../../ui_kits/sitio-web/InstitucionalPage.jsx'
    }, {
      label: 'Subcomisiones'
    }]
  }), /*#__PURE__*/React.createElement("h1", {
    style: {
      color: '#fff',
      fontFamily: 'var(--font-display)',
      fontWeight: 900,
      fontSize: 'clamp(28px,4vw,48px)',
      marginTop: 16
    }
  }, "Subcomisiones"), /*#__PURE__*/React.createElement("p", {
    style: {
      color: 'rgba(255,255,255,.82)',
      fontSize: 17,
      maxWidth: 760,
      marginTop: 12,
      lineHeight: 1.6
    }
  }, "Espacios t\xE9cnicos de trabajo organizados por especialidad. Si quer\xE9s sumarte, contact\xE1 directamente al referente."))), /*#__PURE__*/React.createElement("section", {
    style: {
      background: 'var(--surface-page)',
      padding: '48px 24px 64px'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      maxWidth: 1200,
      margin: '0 auto'
    }
  }, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      justifyContent: 'space-between',
      alignItems: 'flex-end',
      gap: 16,
      flexWrap: 'wrap',
      marginBottom: 24
    }
  }, /*#__PURE__*/React.createElement(SectionHeader, {
    eyebrow: "Listado completo",
    title: `${D.subcomisiones.length} subcomisiones`
  }), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      alignItems: 'center',
      gap: 8,
      border: '1.5px solid var(--border-input)',
      borderRadius: 8,
      padding: '8px 14px',
      minWidth: 280
    }
  }, /*#__PURE__*/React.createElement(Icon, {
    name: "search",
    size: 15,
    color: "var(--text-muted)"
  }), /*#__PURE__*/React.createElement("input", {
    value: q,
    onChange: e => setQ(e.target.value),
    placeholder: "Buscar por especialidad o apellido\u2026",
    style: {
      border: 'none',
      outline: 'none',
      fontSize: 13.5,
      flex: 1,
      fontFamily: 'var(--font-body)'
    }
  }))), /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'grid',
      gridTemplateColumns: 'repeat(auto-fill, minmax(340px,1fr))',
      gap: 18
    }
  }, filtered.map((s, i) => /*#__PURE__*/React.createElement(SubcomisionCard, _extends({
    key: i
  }, s)))))), /*#__PURE__*/React.createElement(Footer, {
    logo: "../../assets/logo-cipba-vii.png"
  }));
}
window.SubcomisionesPage = SubcomisionesPage;
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/sitio-web/SubcomisionesPage.jsx", error: String((e && e.message) || e) }); }

// ui_kits/sitio-web/data.js
try { (() => {
window.CIPBA_DATA = {
  noticias: [{
    cat: 'Institucional',
    date: '18 Abr 2026',
    title: 'Asamblea General Ordinaria: convocatoria a matriculados',
    desc: 'Se convoca a todos los matriculados del Distrito VII a la Asamblea General Ordinaria del ejercicio 2025/2026 a realizarse el próximo 15 de mayo.',
    highlight: true
  }, {
    cat: 'Capacitación',
    date: '14 Abr 2026',
    title: 'Nuevo curso de Integración y Automatización Profesional',
    desc: 'Inscripciones abiertas para el módulo de Nivelación. Modalidad presencial, sede San Justo. Cupos limitados.'
  }, {
    cat: 'Normativa',
    date: '9 Abr 2026',
    title: 'Actualización de honorarios mínimos – 1° trimestre 2026',
    desc: 'El Consejo Superior aprobó la actualización del cuadro de honorarios mínimos para el primer trimestre de 2026.'
  }, {
    cat: 'Visado',
    date: '3 Abr 2026',
    title: 'Sistema de visado online: nuevas funcionalidades',
    desc: 'Se incorporaron mejoras al sistema: adjunto de planos en formato DWG y notificaciones automáticas por e-mail.'
  }],
  quickItems: [{
    icon: 'award',
    label: 'Matrícula',
    desc: 'Nueva inscripción, reinscripción y certificados',
    color: 'var(--blue-600)'
  }, {
    icon: 'file-text',
    label: 'Visado Online',
    desc: 'Visá tus trabajos con 20% de descuento',
    color: 'var(--green-800)'
  }, {
    icon: 'dollar-sign',
    label: 'Honorarios',
    desc: 'Planillas y calculadora de honorarios mínimos',
    color: 'var(--cat-honorarios)'
  }, {
    icon: 'shield',
    label: 'Seguro Profesional',
    desc: 'Póliza de responsabilidad civil para matriculados',
    color: 'var(--cat-comisiones)'
  }, {
    icon: 'book',
    label: 'Capacitación',
    desc: 'Cursos, seminarios y jornadas técnicas',
    color: 'var(--blue-700)'
  }, {
    icon: 'users',
    label: 'Beneficios',
    desc: 'Descuentos y convenios para matriculados',
    color: 'var(--blue-600)'
  }],
  cursos: [{
    title: 'Integración y Automatización Profesional',
    date: '12 May 2026',
    modality: 'Presencial',
    vacantes: 8
  }, {
    title: 'Actualización en Normativa de Seguridad e Higiene',
    date: '20 May 2026',
    modality: 'Virtual',
    vacantes: 24
  }, {
    title: 'Jornada de Peritos Auxiliares de la Justicia',
    date: '2 Jun 2026',
    modality: 'Presencial',
    vacantes: 5
  }],
  documentos: [{
    category: 'Honorarios',
    number: 'Resolución CS 187/2026',
    title: 'Actualización de honorarios mínimos – 1° trimestre 2026',
    date: '09/04/2026',
    pages: 12,
    size: '342 KB'
  }, {
    category: 'Visado',
    number: 'Reglamento 22/2026',
    title: 'Nuevas funcionalidades del sistema de visado online',
    date: '03/04/2026',
    pages: 4,
    size: '88 KB'
  }, {
    category: 'Institucional',
    number: 'Disposición 09/2026',
    title: 'Convocatoria a Asamblea General Ordinaria 2025/2026',
    date: '18/04/2026',
    pages: 2,
    size: '54 KB'
  }, {
    category: 'Matrícula',
    number: 'Resolución CS 145/2026',
    title: 'Requisitos actualizados para reinscripción de matrícula',
    date: '22/03/2026',
    pages: 6,
    size: '210 KB'
  }],
  autoridades: [{
    role: 'Presidente',
    name: 'Daniel Héctor Palacios',
    title: 'Ing. Electricista y Laboral',
    featured: true
  }, {
    role: 'Secretario',
    name: 'Fabián José Miguel Porcile',
    title: 'Ing. Civil'
  }, {
    role: 'Tesorero',
    name: 'Marina Helena Vaca',
    title: 'Inga. Civil'
  }, {
    role: 'Vocal Titular 1°',
    name: 'Gabriel Esteban Busnardo',
    title: 'Ing. en Alimentos'
  }, {
    role: 'Vocal Titular 2°',
    name: 'Mariano Alejandro Pralong',
    title: 'Ing. Civil'
  }, {
    role: 'Vocal Titular 3°',
    name: 'Fabián Roberto Montero',
    title: 'Ing. Civil'
  }, {
    role: 'Vocal Suplente 1°',
    name: 'Juan José Uranga',
    title: 'Ing. Mecánico'
  }, {
    role: 'Vocal Suplente 2°',
    name: 'Perla Beatriz Armagnac',
    title: 'Inga. Civil'
  }, {
    role: 'Vocal Suplente 3°',
    name: 'María Claudia Filipuzzi',
    title: 'Inga. en Ecología'
  }],
  partidos: ['San Justo', 'Escobar', 'Exaltación de la Cruz', 'General Las Heras', 'General Rodríguez', 'General San Martín', 'Hurlingham', 'Ituzaingó', 'José C. Paz', 'La Matanza', 'Luján', 'Malvinas Argentinas', 'Marcos Paz', 'Mercedes', 'Merlo', 'Moreno', 'Morón', 'Pilar', 'San Isidro', 'San Fernando', 'San Miguel', 'Tigre', 'Tres de Febrero', 'Vicente López'],
  sedes: [{
    name: 'Sede San Justo',
    tag: 'Sede principal · La Matanza',
    featured: true,
    rows: [{
      icon: 'map-pin',
      label: 'Dirección',
      value: 'Almafuerte N° 2868, San Justo (1754)'
    }, {
      icon: 'phone',
      label: 'Teléfono',
      value: '(011) 3535-0751'
    }, {
      icon: 'mail',
      label: 'Email',
      value: 'info@cipba.org'
    }, {
      icon: 'clock',
      label: 'Horario',
      value: 'Lunes a viernes de 9 a 16 hs'
    }],
    contacts: [{
      name: 'Administración',
      role: 'Adm.',
      tel: '15-1111-1111',
      email: 'admin@cipba.org'
    }, {
      name: 'Secretaría',
      role: 'Sec.',
      tel: '15-2222-2222',
      email: 'secretaria@cipba.org'
    }]
  }, {
    name: 'Haedo',
    tag: 'Parque Industrial DECA',
    rows: [{
      icon: 'map-pin',
      value: 'Valentín Gómez N° 577 1° of. 1, Haedo'
    }, {
      icon: 'phone',
      value: '(011) 5433-5344'
    }, {
      icon: 'clock',
      value: 'Lunes a viernes de 9 a 15 hs'
    }]
  }, {
    name: 'Olivos',
    tag: 'Vicente López',
    rows: [{
      icon: 'map-pin',
      value: 'Ricardo Gutiérrez N° 1834 (CP 1636)'
    }, {
      icon: 'phone',
      value: '(011) 7398-2119'
    }, {
      icon: 'clock',
      value: 'Sólo correspondencia'
    }]
  }, {
    name: 'General Rodríguez',
    tag: 'Delegación',
    rows: [{
      icon: 'map-pin',
      value: 'Av. España 493'
    }, {
      icon: 'phone',
      value: '(011) 2563-1616'
    }, {
      icon: 'clock',
      value: 'Lunes a viernes de 9 a 13 hs'
    }]
  }],
  subcomisiones: [{
    tag: 'HyS',
    name: 'Higiene y Seguridad en el trabajo',
    referentes: [{
      name: 'MARIA CLAUDIA FILIPUZZI',
      mat: '53.929',
      tel: '15-5181-9336',
      email: 'cfilipuzzi@hotmail.com'
    }]
  }, {
    tag: 'Mec',
    name: 'Ingeniería Mecánica',
    referentes: [{
      name: 'JAVIER VICTOR MANUEL TORRES',
      mat: '55.905',
      tel: '15-5024-8414',
      email: 'jt.ingenieriayservicios@gmail.com'
    }]
  }, {
    tag: 'Elec',
    name: 'Ingeniería Eléctrica',
    referentes: [{
      name: 'EDGARDO LEIBER',
      mat: '54.032',
      tel: '15-4498-4624',
      email: 'edgardoleiber@hotmail.com'
    }, {
      name: 'JUAN PABLO MAZZA',
      mat: '49.294',
      tel: '15-6123-7995',
      email: 'epaim@epaim.com.ar'
    }]
  }, {
    tag: 'Civ',
    name: 'Ingeniería Civil',
    referentes: [{
      name: 'PERLA BEATRIZ ARMAGNAC',
      mat: '53.929',
      tel: '15-3428-4268',
      email: 'parmagnac@gmaingenieria.com.ar'
    }]
  }, {
    tag: 'Amb',
    name: 'Ingeniería Ambiental',
    referentes: [{
      name: 'CARLOS ALBERTO LOSI',
      mat: '48.892',
      tel: '15-5644-0200',
      email: 'lca1srl.cal@gmail.com'
    }]
  }, {
    tag: 'JP',
    name: 'Jóvenes Profesionales de CAAITBA',
    referentes: [{
      name: 'DARIO MIGUEL KUBAR',
      mat: '51.785',
      tel: '15-5644-0200',
      email: 'dariokubar@hotmail.com.ar'
    }]
  }]
};
})(); } catch (e) { __ds_ns.__errors.push({ path: "ui_kits/sitio-web/data.js", error: String((e && e.message) || e) }); }

__ds_ns.AuthorityCard = __ds_scope.AuthorityCard;

__ds_ns.Card = __ds_scope.Card;

__ds_ns.NewsCard = __ds_scope.NewsCard;

__ds_ns.QuickAccessCard = __ds_scope.QuickAccessCard;

__ds_ns.SedeCard = __ds_scope.SedeCard;

__ds_ns.StatCard = __ds_scope.StatCard;

__ds_ns.SubcomisionCard = __ds_scope.SubcomisionCard;

__ds_ns.Badge = __ds_scope.Badge;

__ds_ns.Button = __ds_scope.Button;

__ds_ns.Icon = __ds_scope.Icon;

__ds_ns.IconBadge = __ds_scope.IconBadge;

__ds_ns.SectionHeader = __ds_scope.SectionHeader;

__ds_ns.CourseRow = __ds_scope.CourseRow;

__ds_ns.DocumentRow = __ds_scope.DocumentRow;

__ds_ns.PartidoChip = __ds_scope.PartidoChip;

__ds_ns.Breadcrumb = __ds_scope.Breadcrumb;

__ds_ns.Footer = __ds_scope.Footer;

__ds_ns.Navbar = __ds_scope.Navbar;

__ds_ns.TopBar = __ds_scope.TopBar;

})();
