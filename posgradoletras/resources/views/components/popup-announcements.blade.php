@props([
    'anuncios'              => [],
    'open_delay'            => 1200,
    'cookie_hours'          => 24,
    'show_once_per_session' => true,
    'auto_advance'          => false,
    'auto_advance_ms'       => 4000,
])

@php
    $anuncios              = (array) $anuncios;
    $open_delay            = max(0, (int) $open_delay);
    $cookie_hours          = max(1, (int) $cookie_hours);
    $show_once_per_session = filter_var($show_once_per_session, FILTER_VALIDATE_BOOLEAN);
    $auto_advance          = filter_var($auto_advance, FILTER_VALIDATE_BOOLEAN);
    $auto_advance_ms       = max(1000, (int) $auto_advance_ms);
    $total                 = count($anuncios);
@endphp

@if($total > 0)

{{-- ============================================================
     STYLES — scoped con prefijo .posgrado-popup-
     ============================================================ --}}
@push('styles')
<style>
/* ── Variables ───────────────────────────────────────────────── */
:root {
    --pp-primary      : #6B1E20;
    --pp-accent       : #C9AA36;
    --pp-bg           : #FFFFFF;
    --pp-bg-alt       : #F9F9F9;
    --pp-text         : #1F2937;
    --pp-muted        : #6B7280;
    --pp-border       : #E5E7EB;
    --pp-radius       : 16px;
    --pp-radius-sm    : 8px;
    --pp-shadow       : 0 24px 64px rgba(15, 23, 42, 0.20), 0 4px 16px rgba(15, 23, 42, 0.10);
    --pp-ease         : cubic-bezier(0.4, 0, 0.2, 1);
    --pp-duration     : 0.32s;
}

/* ── Overlay / Backdrop ──────────────────────────────────────── */
.posgrado-popup-overlay {
    position         : fixed;
    inset            : 0;
    z-index          : 9990;
    display          : flex;
    align-items      : center;
    justify-content  : center;
    padding          : 1rem;
    background       : rgba(15, 23, 42, 0.50);
    backdrop-filter  : blur(5px);
    -webkit-backdrop-filter: blur(5px);
    opacity          : 0;
    pointer-events   : none;
    transition       : opacity var(--pp-duration) var(--pp-ease);
}
.posgrado-popup-overlay.posgrado-popup-open {
    opacity          : 1;
    pointer-events   : all;
}

/* ── Modal ───────────────────────────────────────────────────── */
.posgrado-popup-modal {
    position         : relative;
    background       : var(--pp-bg);
    border-radius    : var(--pp-radius);
    box-shadow       : var(--pp-shadow);
    width            : 100%;
    max-width        : 540px;
    max-height       : 88vh;
    display          : flex;
    flex-direction   : column;
    overflow         : hidden;
    outline          : none;
    border-top       : 4px solid var(--pp-primary);
    transform        : translateY(20px) scale(0.975);
    transition       : transform var(--pp-duration) var(--pp-ease);
}
.posgrado-popup-overlay.posgrado-popup-open .posgrado-popup-modal {
    transform        : translateY(0) scale(1);
}

/* ── Drag handle (mobile only — hidden on desktop) ───────────── */
.posgrado-popup-handle {
    display          : none;
}

/* ── Header ──────────────────────────────────────────────────── */
.posgrado-popup-header {
    display          : flex;
    align-items      : center;
    justify-content  : space-between;
    gap              : 12px;
    padding          : 13px 18px 11px;
    border-bottom    : 1px solid var(--pp-border);
    flex-shrink      : 0;
}
.posgrado-popup-label {
    font-family      : 'Inter', system-ui, sans-serif;
    font-size        : 0.7rem;
    font-weight      : 700;
    letter-spacing   : 0.14em;
    text-transform   : uppercase;
    color            : var(--pp-primary);
}
.posgrado-popup-header-right {
    display          : flex;
    align-items      : center;
    gap              : 10px;
}
.posgrado-popup-counter {
    font-size        : 0.72rem;
    font-weight      : 600;
    color            : var(--pp-muted);
    letter-spacing   : 0.04em;
    white-space      : nowrap;
}
.posgrado-popup-close {
    width            : 28px;
    height           : 28px;
    border-radius    : 50%;
    border           : none;
    background       : #F3F4F6;
    color            : #4B5563;
    cursor           : pointer;
    display          : flex;
    align-items      : center;
    justify-content  : center;
    flex-shrink      : 0;
    transition       : background 0.18s, color 0.18s;
    padding          : 0;
}
.posgrado-popup-close:hover { background: #E5E7EB; color: #111827; }
.posgrado-popup-close:focus-visible {
    outline          : 2px solid var(--pp-primary);
    outline-offset   : 2px;
}

/* ── Slider track ────────────────────────────────────────────── */
.posgrado-popup-track-wrap {
    flex             : 1;
    min-height       : 0;
    overflow         : hidden;
    position         : relative;
}
.posgrado-popup-track {
    display          : flex;
    height           : 100%;
    transition       : transform 0.42s var(--pp-ease);
    will-change      : transform;
}
.posgrado-popup-slide {
    flex             : 0 0 100%;
    width            : 100%;
    display          : flex;
    flex-direction   : column;
    overflow-y       : auto;
    overscroll-behavior: contain;
}

/* ── Image ───────────────────────────────────────────────────── */
.posgrado-popup-img-box {
    position         : relative;
    overflow         : hidden;
    background       : #E5E7EB;
    aspect-ratio     : 16 / 9;
    flex-shrink      : 0;
}
.posgrado-popup-img-link {
    display          : block;
    text-decoration  : none;
    width            : 100%;
    height           : 100%;
}
.posgrado-popup-img {
    width            : 100%;
    height           : 100%;
    object-fit       : cover;
    display          : block;
    transition       : transform 0.4s var(--pp-ease);
}
.posgrado-popup-img-link:hover .posgrado-popup-img,
.posgrado-popup-img-link:focus-visible .posgrado-popup-img {
    transform        : scale(1.04);
}
.posgrado-popup-img-link:focus-visible {
    outline          : 3px solid var(--pp-accent);
    outline-offset   : -3px;
}

/* ── Slide footer / CTA ──────────────────────────────────────── */
.posgrado-popup-slide-foot {
    display          : flex;
    align-items      : center;
    justify-content  : flex-end;
    gap              : 10px;
    padding          : 14px 20px;
    border-top       : 1px solid var(--pp-border);
    background       : var(--pp-bg-alt);
    flex-shrink      : 0;
}
.posgrado-popup-cta {
    display          : inline-flex;
    align-items      : center;
    gap              : 7px;
    padding          : 9px 22px;
    border           : 2px solid var(--pp-accent);
    border-radius    : var(--pp-radius-sm);
    background       : transparent;
    color            : var(--pp-accent);
    font-family      : 'Inter', system-ui, sans-serif;
    font-size        : 0.84rem;
    font-weight      : 700;
    letter-spacing   : 0.02em;
    text-decoration  : none;
    cursor           : pointer;
    transition       : background var(--pp-duration) var(--pp-ease),
                       color var(--pp-duration) var(--pp-ease),
                       box-shadow var(--pp-duration) var(--pp-ease),
                       transform 0.15s;
}
.posgrado-popup-cta:hover,
.posgrado-popup-cta:focus-visible {
    background       : var(--pp-accent);
    color            : #fff;
    box-shadow       : 0 4px 14px rgba(201, 170, 54, 0.40);
    transform        : translateY(-1px);
    outline          : none;
}

/* ── Dot navigation ──────────────────────────────────────────── */
.posgrado-popup-nav {
    display          : flex;
    align-items      : center;
    justify-content  : center;
    gap              : 6px;
    padding          : 9px 16px;
    border-top       : 1px solid var(--pp-border);
    flex-shrink      : 0;
}
.posgrado-popup-arrow {
    width            : 30px;
    height           : 30px;
    border           : none;
    background       : transparent;
    color            : var(--pp-primary);
    cursor           : pointer;
    border-radius    : 50%;
    display          : flex;
    align-items      : center;
    justify-content  : center;
    padding          : 0;
    flex-shrink      : 0;
    transition       : background 0.18s;
}
.posgrado-popup-arrow:hover { background: #F9F0F0; }
.posgrado-popup-arrow:focus-visible {
    outline          : 2px solid var(--pp-primary);
    outline-offset   : 2px;
}
.posgrado-popup-dots {
    display          : flex;
    gap              : 7px;
    align-items      : center;
}
.posgrado-popup-dot {
    width            : 8px;
    height           : 8px;
    border-radius    : 50%;
    border           : 2px solid var(--pp-primary);
    background       : transparent;
    cursor           : pointer;
    padding          : 0;
    transition       : background 0.2s, transform 0.2s, border-color 0.2s;
}
.posgrado-popup-dot.pp-active,
.posgrado-popup-dot[aria-selected="true"] {
    background       : var(--pp-primary);
    transform        : scale(1.3);
}
.posgrado-popup-dot:focus-visible {
    outline          : 2px solid var(--pp-accent);
    outline-offset   : 2px;
}

/* ── Footer / Dismiss ────────────────────────────────────────── */
.posgrado-popup-footer {
    display          : flex;
    justify-content  : center;
    padding          : 8px 20px 14px;
    flex-shrink      : 0;
}
.posgrado-popup-dismiss {
    background       : none;
    border           : none;
    color            : var(--pp-muted);
    font-family      : 'Inter', system-ui, sans-serif;
    font-size        : 0.76rem;
    cursor           : pointer;
    text-decoration  : underline;
    text-underline-offset: 3px;
    padding          : 4px 8px;
    border-radius    : 4px;
    transition       : color 0.18s, background 0.18s;
}
.posgrado-popup-dismiss:hover { color: var(--pp-primary); background: #F3F4F6; }
.posgrado-popup-dismiss:focus-visible {
    outline          : 2px solid var(--pp-primary);
    outline-offset   : 2px;
}

/* ── Screen-reader only live region ──────────────────────────── */
.posgrado-popup-sr {
    position         : absolute;
    width            : 1px;
    height           : 1px;
    overflow         : hidden;
    clip             : rect(0 0 0 0);
    white-space      : nowrap;
    border           : 0;
}

/* ── Mobile: bottom sheet (≤480px) ──────────────────────────── */
@media (max-width: 480px) {
    .posgrado-popup-overlay {
        align-items  : flex-end;
        padding      : 0;
    }
    .posgrado-popup-modal {
        max-width    : 100%;
        border-radius: var(--pp-radius) var(--pp-radius) 0 0;
        max-height   : 88vh;
        transform    : translateY(100%);
    }
    .posgrado-popup-overlay.posgrado-popup-open .posgrado-popup-modal {
        transform    : translateY(0);
    }
    .posgrado-popup-handle {
        display      : block;
        width        : 40px;
        height       : 4px;
        background   : #D1D5DB;
        border-radius: 2px;
        margin       : 10px auto 0;
        flex-shrink  : 0;
    }
    .posgrado-popup-img-box {
        aspect-ratio : 3 / 2;
    }
}

/* ── Reduced motion ──────────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
    .posgrado-popup-overlay,
    .posgrado-popup-modal,
    .posgrado-popup-track,
    .posgrado-popup-img,
    .posgrado-popup-dot { transition: none !important; }
}
</style>
@endpush

{{-- ============================================================
     HTML — Live region + Dialog
     ============================================================ --}}

{{-- Screen-reader live region (fuera del dialog para máxima compatibilidad) --}}
<div id="posgrado-popup-live"
     class="posgrado-popup-sr"
     aria-live="polite"
     aria-atomic="true"></div>

<div id="posgrado-popup-overlay"
     class="posgrado-popup-overlay"
     role="dialog"
     aria-modal="true"
     aria-labelledby="posgrado-popup-title"
     aria-hidden="true">

    <div id="posgrado-popup-modal"
         class="posgrado-popup-modal"
         tabindex="-1">

        {{-- Drag handle visible solo en móvil --}}
        <div class="posgrado-popup-handle" aria-hidden="true"></div>

        {{-- Header --}}
        <div class="posgrado-popup-header">
            <span id="posgrado-popup-title" class="posgrado-popup-label">Anuncios</span>
            <div class="posgrado-popup-header-right">
                @if($total > 1)
                <span id="posgrado-popup-counter"
                      class="posgrado-popup-counter"
                      aria-hidden="true">1 / {{ $total }}</span>
                @endif
                <button id="posgrado-popup-close"
                        class="posgrado-popup-close"
                        type="button"
                        aria-label="Cerrar anuncios">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                        <path d="M1 1L11 11M11 1L1 11"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Slider track --}}
        <div class="posgrado-popup-track-wrap">
            <div id="posgrado-popup-track" class="posgrado-popup-track">

                @foreach($anuncios as $i => $anuncio)
                @php
                    $hasLink  = !empty($anuncio['link']);
                    $hasLabel = !empty($anuncio['link_texto']) && $hasLink;
                    $isFirst  = $i === 0;
                @endphp
                <div class="posgrado-popup-slide"
                     id="posgrado-popup-slide-{{ $i }}"
                     role="group"
                     aria-label="Anuncio {{ $i + 1 }} de {{ $total }}"
                     aria-hidden="{{ $isFirst ? 'false' : 'true' }}">

                    {{-- Image box --}}
                    <div class="posgrado-popup-img-box">
                        @if($hasLink)
                        <a href="{{ $anuncio['link'] }}"
                           class="posgrado-popup-img-link"
                           data-pp-cta="1"
                           tabindex="{{ $isFirst ? '0' : '-1' }}"
                           aria-label="{{ $anuncio['link_texto'] ?? ($anuncio['alt'] ?? 'Ver anuncio') }}">
                        @endif

                            <img src="{{ $anuncio['imagen'] }}"
                                 alt="{{ $anuncio['alt'] ?? 'Anuncio ' . ($i + 1) }}"
                                 class="posgrado-popup-img"
                                 loading="{{ $isFirst ? 'eager' : 'lazy' }}"
                                 decoding="async"
                                 width="540"
                                 height="304">

                        @if($hasLink)
                        </a>
                        @endif
                    </div>

                    {{-- CTA footer --}}
                    @if($hasLabel)
                    <div class="posgrado-popup-slide-foot">
                        <a href="{{ $anuncio['link'] }}"
                           class="posgrado-popup-cta"
                           data-pp-cta="1"
                           tabindex="{{ $isFirst ? '0' : '-1' }}">
                            {{ $anuncio['link_texto'] }}
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                <path d="M2 6.5h9M8 2.5l4 4-4 4"
                                      stroke="currentColor"
                                      stroke-width="1.8"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    @endif

                </div>
                @endforeach

            </div>{{-- /track --}}
        </div>{{-- /track-wrap --}}

        {{-- Dot navigation (solo con múltiples slides) --}}
        @if($total > 1)
        <div class="posgrado-popup-nav" aria-label="Navegación de anuncios">
            <button id="posgrado-popup-prev"
                    class="posgrado-popup-arrow"
                    type="button"
                    aria-label="Anuncio anterior">
                <svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true">
                    <path d="M8 1L1 7.5L8 14"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>

            <div class="posgrado-popup-dots" role="tablist" aria-label="Diapositivas">
                @foreach($anuncios as $i => $anuncio)
                <button class="posgrado-popup-dot{{ $i === 0 ? ' pp-active' : '' }}"
                        type="button"
                        role="tab"
                        aria-label="Ir al anuncio {{ $i + 1 }}"
                        aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                        tabindex="{{ $i === 0 ? '0' : '-1' }}"
                        data-pp-idx="{{ $i }}"></button>
                @endforeach
            </div>

            <button id="posgrado-popup-next"
                    class="posgrado-popup-arrow"
                    type="button"
                    aria-label="Siguiente anuncio">
                <svg width="9" height="15" viewBox="0 0 9 15" fill="none" aria-hidden="true">
                    <path d="M1 1L8 7.5L1 14"
                          stroke="currentColor"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
        @endif

        {{-- Dismiss footer --}}
        <div class="posgrado-popup-footer">
            <button id="posgrado-popup-dismiss"
                    class="posgrado-popup-dismiss"
                    type="button"
                    aria-label="No mostrar este anuncio hoy">
                No mostrar hoy
            </button>
        </div>

    </div>{{-- /modal --}}
</div>{{-- /overlay --}}

{{-- ============================================================
     JAVASCRIPT — vanilla, prefijo posgradoPopup
     ============================================================ --}}
@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Configuración ──────────────────────────────────────── */
    var CFG = {
        openDelay   : {{ $open_delay }},
        cookieHours : {{ $cookie_hours }},
        session     : {{ $show_once_per_session ? 'true' : 'false' }},
        auto        : {{ $auto_advance ? 'true' : 'false' }},
        autoMs      : {{ $auto_advance_ms }},
        total       : {{ $total }},
        sessionKey  : 'posgrado_anuncio_seen',
        cookieName  : 'posgrado_anuncio_dismiss',
    };

    /* ── Utilidades de cookie ───────────────────────────────── */
    function getCookie(name) {
        return document.cookie.split('; ').reduce(function (acc, pair) {
            var parts = pair.split('=');
            return parts[0] === name ? decodeURIComponent(parts[1] || '') : acc;
        }, null);
    }
    function setCookie(name, value, hours) {
        var exp = new Date(Date.now() + hours * 3600000).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value)
            + '; expires=' + exp + '; path=/; SameSite=Lax';
    }

    /* ── ¿Debe mostrarse? ───────────────────────────────────── */
    function shouldShow() {
        if (getCookie(CFG.cookieName))                           return false;
        if (CFG.session && sessionStorage.getItem(CFG.sessionKey)) return false;
        return true;
    }
    if (!shouldShow() || CFG.total === 0) return;

    /* ── Referencias al DOM ─────────────────────────────────── */
    var overlay  = document.getElementById('posgrado-popup-overlay');
    var modal    = document.getElementById('posgrado-popup-modal');
    var track    = document.getElementById('posgrado-popup-track');
    var closeBtn = document.getElementById('posgrado-popup-close');
    var dismiss  = document.getElementById('posgrado-popup-dismiss');
    var prevBtn  = document.getElementById('posgrado-popup-prev');
    var nextBtn  = document.getElementById('posgrado-popup-next');
    var counter  = document.getElementById('posgrado-popup-counter');
    var liveRgn  = document.getElementById('posgrado-popup-live');
    var dots     = document.querySelectorAll('.posgrado-popup-dot');
    var slides   = document.querySelectorAll('.posgrado-popup-slide');

    if (!overlay || !modal) return;

    /* ── Estado ─────────────────────────────────────────────── */
    var current    = 0;
    var autoTimer  = null;
    var isOpen     = false;
    var prevActive = null; /* elemento enfocado antes de abrir */

    /* ── Analytics ──────────────────────────────────────────── */
    function emit(name, detail) {
        try {
            document.dispatchEvent(new CustomEvent('posgrado:popup:' + name, {
                bubbles: true,
                detail : Object.assign({ ts: Date.now() }, detail || {})
            }));
        } catch (e) { /* navegadores muy viejos */ }
    }

    /* ── Focus trap ─────────────────────────────────────────── */
    function getFocusable() {
        return Array.prototype.slice.call(
            modal.querySelectorAll(
                'a[href]:not([tabindex="-1"]),' +
                'button:not([disabled]):not([tabindex="-1"]),' +
                '[tabindex]:not([tabindex="-1"])'
            )
        ).filter(function (el) {
            return !el.closest('[aria-hidden="true"]');
        });
    }
    function trapFocus(e) {
        var els   = getFocusable();
        if (!els.length) return;
        var first = els[0];
        var last  = els[els.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault(); last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault(); first.focus();
        }
    }

    /* ── Abrir ──────────────────────────────────────────────── */
    function openPopup() {
        prevActive = document.activeElement;
        overlay.classList.add('posgrado-popup-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        isOpen = true;

        /* Foco al modal tras la transición */
        setTimeout(function () { modal.focus(); }, 50);

        if (CFG.session) sessionStorage.setItem(CFG.sessionKey, '1');
        if (CFG.auto && CFG.total > 1) startAuto();

        emit('open', { totalSlides: CFG.total });
    }

    /* ── Cerrar ─────────────────────────────────────────────── */
    function closePopup() {
        overlay.classList.remove('posgrado-popup-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        isOpen = false;
        stopAuto();

        /* Devolver foco al elemento previo */
        if (prevActive && typeof prevActive.focus === 'function') {
            prevActive.focus();
        }

        emit('close', { lastSlide: current });
    }

    /* ── Ir a slide N ───────────────────────────────────────── */
    function goTo(idx, isInit) {
        if (idx < 0)           idx = CFG.total - 1;
        if (idx >= CFG.total)  idx = 0;
        if (idx === current && !isInit) return;

        var prev = current;
        current  = idx;

        /* Mover track */
        if (track) track.style.transform = 'translateX(-' + (idx * 100) + '%)';

        /* Actualizar slides */
        slides && slides.forEach(function (slide, i) {
            var active = i === idx;
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            /* Tabindex de interactivos dentro del slide */
            slide.querySelectorAll('a[href], button').forEach(function (el) {
                el.setAttribute('tabindex', active ? '0' : '-1');
            });
        });

        /* Actualizar dots */
        dots && dots.forEach(function (dot, i) {
            var active = i === idx;
            dot.classList.toggle('pp-active', active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
            dot.setAttribute('tabindex', active ? '0' : '-1');
        });

        /* Contador */
        if (counter) counter.textContent = (idx + 1) + ' / ' + CFG.total;

        /* Live region (no en init para evitar anuncio duplicado al abrir) */
        if (!isInit && liveRgn) {
            liveRgn.textContent = 'Anuncio ' + (idx + 1) + ' de ' + CFG.total;
        }

        if (!isInit) emit('slide_change', { from: prev, to: idx });
    }

    /* ── Auto-avance ────────────────────────────────────────── */
    function startAuto() {
        stopAuto();
        autoTimer = setInterval(function () { goTo(current + 1); }, CFG.autoMs);
    }
    function stopAuto() {
        if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    /* ── Swipe táctil ───────────────────────────────────────── */
    var tx = 0, ty = 0, tt = 0;
    function onTouchStart(e) {
        var t = e.changedTouches[0];
        tx = t.clientX; ty = t.clientY; tt = Date.now();
        stopAuto();
    }
    function onTouchEnd(e) {
        if (CFG.total < 2) return;
        var t    = e.changedTouches[0];
        var dx   = t.clientX - tx;
        var dy   = t.clientY - ty;
        var dt   = Date.now() - tt || 1;
        var spd  = Math.abs(dx) / dt; /* px/ms */

        /* Swipe horizontal con velocidad mínima */
        if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy) * 1.5 && spd > 0.18) {
            goTo(current + (dx < 0 ? 1 : -1));
        }
        if (CFG.auto) startAuto();
    }

    /* ── Listeners ──────────────────────────────────────────── */
    if (closeBtn) {
        closeBtn.addEventListener('click', closePopup);
    }
    if (dismiss) {
        dismiss.addEventListener('click', function () {
            setCookie(CFG.cookieName, '1', CFG.cookieHours);
            closePopup();
        });
    }

    /* Clic en backdrop */
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closePopup();
    });

    /* Flechas de navegación */
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            stopAuto(); goTo(current - 1);
            if (CFG.auto) startAuto();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            stopAuto(); goTo(current + 1);
            if (CFG.auto) startAuto();
        });
    }

    /* Dots */
    dots && dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            stopAuto();
            goTo(parseInt(this.getAttribute('data-pp-idx'), 10));
            if (CFG.auto) startAuto();
        });
    });

    /* Teclado */
    document.addEventListener('keydown', function (e) {
        if (!isOpen) return;
        switch (e.key) {
            case 'Escape':
                closePopup();
                break;
            case 'Tab':
                trapFocus(e);
                break;
            case 'ArrowLeft':
                if (CFG.total > 1) { stopAuto(); goTo(current - 1); if (CFG.auto) startAuto(); }
                break;
            case 'ArrowRight':
                if (CFG.total > 1) { stopAuto(); goTo(current + 1); if (CFG.auto) startAuto(); }
                break;
        }
    });

    /* Pausa auto al hover (desktop) */
    if (CFG.auto) {
        modal.addEventListener('mouseenter', stopAuto);
        modal.addEventListener('mouseleave', function () { if (isOpen) startAuto(); });
    }

    /* Swipe (siempre que haya más de 1 slide) */
    if (CFG.total > 1) {
        modal.addEventListener('touchstart', onTouchStart, { passive: true });
        modal.addEventListener('touchend',   onTouchEnd,   { passive: true });
    }

    /* Analytics en CTAs */
    modal.querySelectorAll('[data-pp-cta]').forEach(function (el) {
        el.addEventListener('click', function () {
            emit('cta_click', { slide: current, href: this.getAttribute('href') });
        });
    });

    /* ── Init ───────────────────────────────────────────────── */
    goTo(0, true);
    setTimeout(openPopup, CFG.openDelay);

}());
</script>
@endpush

@endif
