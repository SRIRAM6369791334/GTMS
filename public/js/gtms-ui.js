/* ==========================================================================
   GTMS UI LAYER (behaviour)
   - Button ripple + loading state on form submit
   - Input focus label tint, invalid-field shake, focus first error
   - SweetAlert2 confirm dialogs for `return confirm('...')` handlers and
     for any element with a data-confirm attribute
   - SweetAlert2 toasts for session flash messages (window.GTMS_FLASH)
   Exposes window.GTMS.toast(), window.GTMS.confirm() for page scripts.
   ========================================================================== */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var hasSwal = typeof window.Swal !== 'undefined';
    var DANGER_WORDS = /\b(delete|remove|clear|discard|purge|cancel|reject|revoke|drop)\b/i;

    /* ------------------------------------------------------------------
       SweetAlert helpers
       ------------------------------------------------------------------ */
    function toast(icon, title, timer) {
        if (!hasSwal || !title) return;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon || 'success',
            title: String(title),
            showConfirmButton: false,
            showCloseButton: true,
            timer: timer || 4000,
            timerProgressBar: true,
            customClass: { popup: 'gt-swal' },
            didOpen: function (el) {
                el.addEventListener('mouseenter', Swal.stopTimer);
                el.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    function confirmDialog(message, opts) {
        opts = opts || {};
        var danger = opts.danger !== undefined ? opts.danger : DANGER_WORDS.test(message);
        if (!hasSwal) {
            return Promise.resolve(window.confirm(message));
        }
        return Swal.fire({
            title: opts.title || (danger ? 'Are you sure?' : 'Please confirm'),
            text: message,
            icon: danger ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: opts.confirmText || (danger ? 'Yes, continue' : 'Confirm'),
            cancelButtonText: opts.cancelText || 'Cancel',
            reverseButtons: true,
            focusCancel: danger,
            buttonsStyling: false,
            customClass: {
                popup: 'gt-swal' + (danger ? ' gt-swal-danger' : ''),
                confirmButton: 'swal2-confirm',
                cancelButton: 'swal2-cancel'
            },
            showClass: { popup: reduceMotion ? '' : 'swal2-show' }
        }).then(function (r) { return !!r.isConfirmed; });
    }

    window.GTMS = window.GTMS || {};
    window.GTMS.toast = toast;
    window.GTMS.confirm = confirmDialog;

    /* ------------------------------------------------------------------
       Portal credentials (Parivesh / Email passwords) are never printed
       into page HTML. Pages fetch them on demand:
         GTMS.credentials('customer'|'environment'|'ppt'|'ec_certificate', id)
       resolves to { parivesh_password, email_password } (empty strings when
       none are saved). Every fetch is logged on the server.
    ------------------------------------------------------------------ */
    var credCache = {};
    window.GTMS.credentials = function (type, id) {
        if (!type || !id) return Promise.resolve({ parivesh_password: '', email_password: '' });
        var key = type + ':' + id;
        if (credCache[key]) return credCache[key];
        var meta = document.querySelector('meta[name="csrf-token"]');
        credCache[key] = fetch('/credentials/' + encodeURIComponent(type) + '/' + encodeURIComponent(id), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': meta ? meta.getAttribute('content') : ''
            }
        }).then(function (res) {
            if (!res.ok) throw new Error('Could not load the saved portal passwords.');
            return res.json();
        }).then(function (data) {
            return {
                parivesh_password: data.parivesh_password || '',
                email_password: data.email_password || ''
            };
        }).catch(function (err) {
            delete credCache[key];
            throw err;
        });
        return credCache[key];
    };

    /* Show / Copy buttons for a saved portal password:
         <span id="x">••••••••</span>
         <button data-cred-action="toggle|copy" data-cred-type="environment"
                 data-cred-id="12" data-cred-field="parivesh_password"
                 data-cred-target="#x">...</button>                        */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest ? e.target.closest('[data-cred-action]') : null;
        if (!btn) return;
        e.preventDefault();
        var field = btn.getAttribute('data-cred-field');
        var target = btn.getAttribute('data-cred-target') ? document.querySelector(btn.getAttribute('data-cred-target')) : null;
        var action = btn.getAttribute('data-cred-action');

        if (action === 'toggle' && target && target.getAttribute('data-cred-shown') === '1') {
            target.textContent = target.getAttribute('data-cred-mask') || '••••••••••';
            target.setAttribute('data-cred-shown', '0');
            return;
        }

        window.GTMS.credentials(btn.getAttribute('data-cred-type'), btn.getAttribute('data-cred-id')).then(function (cred) {
            var value = cred[field] || '';
            if (!value) { toast('info', 'No password saved.'); return; }
            if (action === 'toggle' && target) {
                if (!target.getAttribute('data-cred-mask')) target.setAttribute('data-cred-mask', target.textContent);
                target.textContent = value;
                target.setAttribute('data-cred-shown', '1');
            } else if (action === 'copy') {
                var done = function () { toast('success', 'Password copied'); };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(value).then(done);
                } else {
                    var tmp = document.createElement('textarea');
                    tmp.value = value; document.body.appendChild(tmp); tmp.select();
                    try { document.execCommand('copy'); done(); } finally { tmp.remove(); }
                }
            }
        }).catch(function (err) { toast('error', err.message); });
    });

    /* ------------------------------------------------------------------
       Convert inline `return confirm('...')` handlers to data-confirm
       Only exact one-statement handlers are converted; anything more
       complex keeps the native dialog.
       ------------------------------------------------------------------ */
    var CONFIRM_RE = /^\s*return\s+confirm\(\s*(['"])([\s\S]*)\1\s*\)\s*;?\s*$/;

    function unescapeJs(s) {
        return s.replace(/\\(['"\\])/g, '$1').replace(/\\n/g, '\n');
    }

    function convertInlineConfirms(root) {
        (root || document).querySelectorAll('[onclick*="confirm("], form[onsubmit*="confirm("]').forEach(function (el) {
            var attr = el.tagName === 'FORM' ? 'onsubmit' : 'onclick';
            var src = el.getAttribute(attr);
            var m = src && src.match(CONFIRM_RE);
            if (!m) return;
            el.setAttribute('data-confirm', unescapeJs(m[2]));
            el.removeAttribute(attr);
        });
    }

    // Flags let the confirmed action run once without asking again.
    var bypass = new WeakSet();

    document.addEventListener('click', function (e) {
        var el = e.target.closest('[data-confirm]');
        if (!el || el.tagName === 'FORM' || bypass.has(el)) return;
        e.preventDefault();
        e.stopImmediatePropagation();

        confirmDialog(el.getAttribute('data-confirm'), {
            title: el.getAttribute('data-confirm-title') || undefined,
            confirmText: el.getAttribute('data-confirm-button') || undefined
        }).then(function (ok) {
            if (!ok) return;
            bypass.add(el);
            var form = el.form || (el.tagName === 'BUTTON' || el.tagName === 'INPUT' ? el.closest('form') : null);
            if (el.tagName === 'A' && el.getAttribute('href') && el.getAttribute('href') !== '#') {
                if (el.target === '_blank') window.open(el.href, '_blank', 'noopener');
                else window.location.href = el.href;
            } else if (form && (el.type === 'submit' || el.type === 'image')) {
                bypass.add(form);
                if (form.requestSubmit) form.requestSubmit(el);
                else form.submit();
            } else {
                el.click();
            }
            setTimeout(function () { bypass.delete(el); }, 0);
        });
    }, true);

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.hasAttribute('data-confirm')) return;
        if (bypass.has(form)) { bypass.delete(form); return; }
        e.preventDefault();
        e.stopImmediatePropagation();
        var submitter = e.submitter || null;
        confirmDialog(form.getAttribute('data-confirm')).then(function (ok) {
            if (!ok) return;
            bypass.add(form);
            if (form.requestSubmit) form.requestSubmit(submitter && submitter.form === form ? submitter : undefined);
            else form.submit();
        });
    }, true);

    /* ------------------------------------------------------------------
       Button ripple
       ------------------------------------------------------------------ */
    document.addEventListener('pointerdown', function (e) {
        if (reduceMotion) return;
        var btn = e.target.closest('.btn');
        if (!btn || btn.disabled || btn.classList.contains('disabled')) return;
        var rect = btn.getBoundingClientRect();
        var size = Math.max(rect.width, rect.height);
        var span = document.createElement('span');
        span.className = 'gt-ripple';
        span.style.width = span.style.height = size + 'px';
        span.style.left = (e.clientX - rect.left - size / 2) + 'px';
        span.style.top = (e.clientY - rect.top - size / 2) + 'px';
        btn.appendChild(span);
        setTimeout(function () { span.remove(); }, 560);
    });

    /* ------------------------------------------------------------------
       Submit loading state (prevents double submit)
       Runs after page handlers so a prevented submit is left alone.
       ------------------------------------------------------------------ */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        setTimeout(function () {
            if (e.defaultPrevented) return;
            if (form.hasAttribute('data-no-loading') || form.target === '_blank') return;
            var btn = e.submitter && e.submitter.classList && e.submitter.classList.contains('btn')
                ? e.submitter
                : form.querySelector('button[type="submit"].btn, input[type="submit"].btn');
            if (!btn || btn.hasAttribute('data-no-loading')) return;
            btn.classList.add('gt-loading');
            btn.setAttribute('aria-busy', 'true');
            // Downloads and validation failures keep the page; release the button.
            setTimeout(function () { releaseButton(btn); }, 8000);
        }, 0);
    });

    function releaseButton(btn) {
        btn.classList.remove('gt-loading');
        btn.removeAttribute('aria-busy');
    }

    window.addEventListener('pageshow', function () {
        document.querySelectorAll('.btn.gt-loading').forEach(releaseButton);
    });

    /* ------------------------------------------------------------------
       Input focus + validation feedback
       ------------------------------------------------------------------ */
    var FIELD_SEL = '.form-control, .form-select, .bootstrap-select';

    document.addEventListener('focusin', function (e) {
        var f = e.target.closest(FIELD_SEL);
        if (f && f.parentElement) f.parentElement.classList.add('gt-field-focus');
    });
    document.addEventListener('focusout', function (e) {
        var f = e.target.closest(FIELD_SEL);
        if (f && f.parentElement) f.parentElement.classList.remove('gt-field-focus');
    });

    function shake(el) {
        if (reduceMotion || !el) return;
        el.classList.remove('gt-shake');
        void el.offsetWidth; // restart animation
        el.classList.add('gt-shake');
        setTimeout(function () { el.classList.remove('gt-shake'); }, 400);
    }

    // Native HTML5 validation: shake the first invalid field only.
    var shookThisSubmit = false;
    document.addEventListener('invalid', function (e) {
        if (shookThisSubmit) return;
        shookThisSubmit = true;
        shake(e.target);
        setTimeout(function () { shookThisSubmit = false; }, 300);
    }, true);

    // Clear server-side error state as soon as the user edits the field.
    document.addEventListener('input', function (e) {
        var el = e.target;
        if (el.classList && el.classList.contains('is-invalid')) {
            el.classList.remove('is-invalid');
        }
    });

    /* ------------------------------------------------------------------
       Init
       ------------------------------------------------------------------ */
    function init() {
        convertInlineConfirms(document);

        // Modals written inside page wrappers can sit in a lower stacking
        // context than the backdrop (which Bootstrap appends to <body>), so the
        // backdrop covers the dialog. Move each modal to <body> before it opens.
        document.addEventListener('show.bs.modal', function (e) {
            var m = e.target;
            if (m && m.classList && m.classList.contains('modal') && m.parentElement !== document.body) {
                document.body.appendChild(m);
            }
        }, true);

        var page = document.querySelector('.content-body > .container-fluid, .content-body > .container');
        if (page && !reduceMotion) {
            page.classList.add('gt-enter');
            // A transform animation left "in effect" turns this wrapper into the
            // containing block for position:fixed children, so Bootstrap modals
            // inside the page render off-screen. Drop it once the entry is done.
            page.addEventListener('animationend', function done(e) {
                if (e.target !== page) return;
                page.classList.remove('gt-enter');
                page.removeEventListener('animationend', done);
            });
        }

        // Server-side validation errors: focus + shake first invalid field.
        var firstInvalid = document.querySelector('.content-body .is-invalid, .content-body .was-validated :invalid');
        if (firstInvalid) {
            firstInvalid.focus({ preventScroll: false });
            shake(firstInvalid);
        }

        // Session flash messages -> toast. Matching inline alerts are removed
        // so the user sees one message, not two.
        var flash = window.GTMS_FLASH || [];
        flash.forEach(function (f) {
            document.querySelectorAll('.content-body .alert').forEach(function (a) {
                if (a.textContent.replace(/\s+/g, ' ').trim().indexOf(String(f.text).trim()) !== -1) {
                    a.remove();
                }
            });
        });
        flash.forEach(function (f, i) {
            setTimeout(function () { toast(f.icon, f.text, f.icon === 'error' ? 6000 : 4000); }, i * 350);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Content injected later (AJAX tables, modals) may carry inline confirms.
    if (window.MutationObserver) {
        new MutationObserver(function (muts) {
            for (var i = 0; i < muts.length; i++) {
                for (var j = 0; j < muts[i].addedNodes.length; j++) {
                    var n = muts[i].addedNodes[j];
                    if (n.nodeType === 1 && n.querySelector) {
                        if (n.matches('[onclick*="confirm("], form[onsubmit*="confirm("]')) convertInlineConfirms(n.parentNode);
                        else if (n.querySelector('[onclick*="confirm("], form[onsubmit*="confirm("]')) convertInlineConfirms(n);
                    }
                }
            }
        }).observe(document.documentElement, { childList: true, subtree: true });
    }
})();
