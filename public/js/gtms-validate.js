/* ==========================================================================
   GTMS FIELD VALIDATION
   Declarative, reusable input rules. Add data-v="<rule>" to any input:

     <input name="mobile_num" data-v="mobile" required>
     <input name="secondary_mobile_num" data-v="mobile" data-v-different="mobile_num">

   While typing : characters the rule does not allow are removed, the length
                  is capped, and a short hint says why ("Numbers only").
   On blur      : the value is checked and an error shows under the field.
   On submit    : every [data-v] / [required] field in the form is checked;
                  the submit (including AJAX handlers bound to the form) is
                  stopped if anything is invalid and the first bad field gets
                  focus.

   Error text goes into an existing `.<name>_error` element when the page has
   one (Laravel-style), otherwise into a generated `.invalid-feedback`.
   Server-side rules must still match: this only improves the experience.
   ========================================================================== */
(function () {
    'use strict';

    var RULES = {
        mobile: {
            label: 'Mobile number',
            inputmode: 'numeric',
            max: 10,
            filter: /[^0-9]/g,
            filterHint: 'Numbers only',
            test: function (v) { return /^[6-9][0-9]{9}$/.test(v); },
            msg: function (v) {
                if (!/^[6-9]/.test(v)) return 'Mobile number must start with 6, 7, 8 or 9.';
                return 'Enter a 10-digit mobile number (' + v.length + '/10 entered).';
            }
        },
        pan: {
            label: 'PAN',
            upper: true,
            max: 10,
            filter: /[^A-Z0-9]/g,
            filterHint: 'Letters and numbers only',
            test: function (v) { return /^[A-Z]{5}[0-9]{4}[A-Z]$/.test(v); },
            msg: function () { return 'PAN must be 5 letters, 4 numbers, 1 letter (e.g. ABCDE1234F).'; }
        },
        gstin: {
            label: 'GSTIN',
            upper: true,
            max: 15,
            filter: /[^A-Z0-9]/g,
            filterHint: 'Letters and numbers only',
            test: function (v) { return /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(v); },
            msg: function (v) {
                if (v.length !== 15) return 'GSTIN must be exactly 15 characters (' + v.length + '/15 entered).';
                return 'Invalid GSTIN format (e.g. 33ABCDE1234F1Z5).';
            }
        },
        aadhaar: {
            label: 'Aadhaar',
            inputmode: 'numeric',
            max: 14,
            filter: /[^0-9]/g,
            filterHint: 'Numbers only',
            format: function (v) {
                var d = v.replace(/\D/g, '').slice(0, 12);
                return (d.match(/.{1,4}/g) || []).join('-');
            },
            test: function (v) { return /^[2-9][0-9]{11}$/.test(v.replace(/\D/g, '')); },
            msg: function (v) {
                var d = v.replace(/\D/g, '');
                if (d.length !== 12) return 'Aadhaar must be 12 digits (' + d.length + '/12 entered).';
                return 'Aadhaar cannot start with 0 or 1.';
            }
        },
        name: {
            label: 'Name',
            max: 100,
            filter: /[^A-Za-z .'\-]/g,
            filterHint: 'Letters, space, dot and hyphen only',
            test: function (v) { return /^[A-Za-z][A-Za-z .'\-]*[A-Za-z.]$/.test(v.trim()) && v.trim().length >= 2; },
            msg: function () { return 'Enter a valid name (letters only, at least 2 characters).'; }
        },
        company: {
            label: 'Company',
            max: 150,
            filter: /[^A-Za-z0-9 &.,'()\/\-]/g,
            filterHint: 'Letters, numbers and & . , \' ( ) / - only',
            test: function (v) { return v.trim().length >= 2; },
            msg: function () { return 'Company name must be at least 2 characters.'; }
        },
        code: {
            // IDs like CUST-001, TN-MMS-2026/0482
            label: 'ID',
            upper: true,
            max: 50,
            filter: /[^A-Z0-9\/\-]/g,
            filterHint: 'Letters, numbers, / and - only',
            test: function (v) { return /^[A-Z0-9][A-Z0-9\/\-]*$/.test(v) && v.length >= 3; },
            msg: function () { return 'Use letters, numbers, / or - (at least 3 characters).'; }
        },
        text: {
            label: 'Text',
            max: 100,
            filter: /[^A-Za-z0-9 \/\-.,()]/g,
            filterHint: 'Special characters are not allowed',
            test: function (v) { return v.trim().length > 0; },
            msg: function () { return 'Enter a valid value.'; }
        },
        email: {
            label: 'Email',
            max: 254,
            lower: true,
            filter: /\s/g,
            filterHint: 'Spaces are not allowed',
            test: function (v) { return /^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/.test(v); },
            msg: function () { return 'Enter a valid email address (e.g. name@example.com).'; }
        },
        address: {
            label: 'Address',
            max: 500,
            test: function (v) { return v.trim().length >= 5; },
            msg: function () { return 'Address is too short.'; }
        },
        pincode: {
            label: 'Pincode',
            inputmode: 'numeric',
            max: 6,
            filter: /[^0-9]/g,
            filterHint: 'Numbers only',
            test: function (v) { return /^[1-9][0-9]{5}$/.test(v); },
            msg: function () { return 'Pincode must be 6 digits.'; }
        },
        decimal: {
            label: 'Number',
            inputmode: 'decimal',
            max: 12,
            filter: /[^0-9.]/g,
            filterHint: 'Numbers and a decimal point only',
            test: function (v) { return /^[0-9]+(\.[0-9]{1,4})?$/.test(v); },
            msg: function () { return 'Enter a valid number (e.g. 1.25).'; }
        }
    };

    /* ------------------------------------------------------------------ */

    function ruleOf(el) { return RULES[el.getAttribute('data-v')] || null; }

    function errorEl(el) {
        var form = el.form || el.closest('form') || document;
        var name = el.getAttribute('name');
        var key = name ? name.replace(/[^\w-]/g, '') : '';
        // Laravel-style spans: ".mobile_num_error" (add forms) or ".edit_mobile_num_error" (edit forms)
        var box = key ? (form.querySelector('.' + key + '_error') || form.querySelector('.edit_' + key + '_error')) : null;
        if (!box) {
            var host = el.closest('.input-group') || el;
            box = host.parentElement.querySelector(':scope > .gt-v-error');
            if (!box) {
                box = document.createElement('div');
                box.className = 'invalid-feedback d-block gt-v-error';
                host.insertAdjacentElement('afterend', box);
            }
        }
        if (!box.id) box.id = 'gtv-' + Math.random().toString(36).slice(2, 9);
        return box;
    }

    function setError(el, message) {
        var box = errorEl(el);
        box.textContent = message || '';
        box.setAttribute('role', message ? 'alert' : '');
        el.classList.toggle('is-invalid', !!message);
        el.classList.toggle('is-valid', !message && el.value.trim() !== '');
        el.setAttribute('aria-invalid', message ? 'true' : 'false');
        if (message) el.setAttribute('aria-describedby', box.id);
        var group = el.closest('.input-group');
        if (group) group.classList.toggle('gt-v-invalid', !!message);
    }

    function check(el) {
        if (el.disabled || el.type === 'hidden') return true;
        var rule = ruleOf(el);
        var v = (el.value || '').trim();
        var label = el.getAttribute('data-v-label') || (rule && rule.label) || 'This field';

        if (v === '') {
            if (el.required) { setError(el, label + ' is required.'); return false; }
            setError(el, ''); el.classList.remove('is-valid'); return true;
        }
        if (rule && rule.test && !rule.test(v)) { setError(el, rule.msg(v)); return false; }

        var other = el.getAttribute('data-v-different');
        if (other) {
            var form = el.form || document;
            var o = form.querySelector('[name="' + other + '"]');
            if (o && o.value.trim() !== '' && o.value.trim() === v) {
                setError(el, 'Must be different from the primary number.'); return false;
            }
        }
        setError(el, '');
        return true;
    }

    var hintTimers = new WeakMap();
    function flashHint(el, text) {
        var box = errorEl(el);
        if (el.classList.contains('is-invalid')) return; // a real error is already shown
        box.textContent = text;
        box.classList.add('gt-v-hint');
        el.classList.add('gt-v-blocked');
        clearTimeout(hintTimers.get(el));
        hintTimers.set(el, setTimeout(function () {
            if (!el.classList.contains('is-invalid')) box.textContent = '';
            box.classList.remove('gt-v-hint');
            el.classList.remove('gt-v-blocked');
        }, 1400));
    }

    function sanitize(el) {
        var rule = ruleOf(el);
        if (!rule) return;
        var before = el.value;
        var v = before;
        if (rule.upper) v = v.toUpperCase();
        if (rule.lower) v = v.toLowerCase();

        var blocked = false;
        if (rule.format) {
            var raw = v.replace(/-/g, '');
            if (rule.filter && rule.filter.test(raw)) blocked = true;
            v = rule.format(v);
        } else if (rule.filter) {
            var cleaned = v.replace(rule.filter, '');
            if (cleaned !== v) blocked = true;
            v = cleaned;
        }
        if (rule.filter) rule.filter.lastIndex = 0;
        if (rule.max && v.length > rule.max) { v = v.slice(0, rule.max); blocked = true; }

        if (v !== before) {
            var pos = el.selectionStart - (before.length - v.length);
            el.value = v;
            try { el.setSelectionRange(Math.max(pos, 0), Math.max(pos, 0)); } catch (e) {}
        }
        if (blocked) {
            var over = rule.max && before.replace(rule.filter || /$^/, '').length > rule.max;
            flashHint(el, over ? 'Maximum ' + (rule.format ? 12 : rule.max) + ' characters' : rule.filterHint);
        }
    }

    function prepare(root) {
        (root || document).querySelectorAll('[data-v]').forEach(function (el) {
            if (el.dataset.vReady) return;
            el.dataset.vReady = '1';
            var rule = ruleOf(el);
            if (!rule) return;
            // No native maxlength: it cuts pasted text BEFORE disallowed characters
            // are filtered out (e.g. "98ab7654321" would lose digits). sanitize()
            // filters first, then caps the length.
            el.removeAttribute('maxlength');
            if (rule.inputmode) el.setAttribute('inputmode', rule.inputmode);
            if (el.getAttribute('data-v') === 'mobile') el.setAttribute('autocomplete', 'tel-national');
            if (el.getAttribute('data-v') === 'email') el.setAttribute('autocomplete', 'email');
        });
    }

    /* ------------------------------------------------------------------ */

    document.addEventListener('input', function (e) {
        var el = e.target;
        if (!el.matches || !el.matches('[data-v]')) return;
        sanitize(el);
        // once an error is showing, re-check live so it clears as soon as fixed
        if (el.classList.contains('is-invalid')) check(el);
    });

    document.addEventListener('focusout', function (e) {
        var el = e.target;
        if (!el.matches) return;
        if (el.matches('[data-v]') || (el.required && el.closest('form[data-validate]'))) {
            if (el.value.trim() !== '' || el.dataset.vTouched) check(el);
            el.dataset.vTouched = '1';
        }
    });

    document.addEventListener('change', function (e) {
        var el = e.target;
        if (el.tagName === 'SELECT' && el.required && el.closest('form[data-validate]')) check(el);
    });

    function focusFirst(first) {
        first.focus({ preventScroll: true });
        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        first.classList.remove('gt-shake'); void first.offsetWidth; first.classList.add('gt-shake');
    }

    /** Check every [data-v] / [required] field; show errors under fields. Returns true when valid. */
    function validateForm(form) {
        var first = null;
        form.querySelectorAll('[data-v], [required]').forEach(function (el) {
            if (el.type === 'checkbox' || el.type === 'radio') return; // groups are page-specific
            if (!check(el) && !first) first = el;
        });
        if (first) focusFirst(first);   // no popup: each error is shown under its own field
        return !first;
    }

    /** Put Laravel 422 errors ({field: [msg]}) under their fields. Returns true if any matched. */
    function showErrors(form, errors) {
        var first = null;
        Object.keys(errors || {}).forEach(function (key) {
            var base = key.split('.')[0];
            var el = form.querySelector('[name="' + base + '"]') || form.querySelector('[name="' + base + '[]"]');
            if (!el) return;
            var msg = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
            setError(el, msg);
            el.dataset.vTouched = '1';
            if (!first) first = el;
        });
        if (first) focusFirst(first);
        return !!first;
    }

    // Capture phase: runs before handlers bound on the form (jQuery AJAX submit)
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!form.matches('form[data-validate]')) return;
        if (!validateForm(form)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);

    // Reset clears validation state (forms are reused in modals)
    document.addEventListener('reset', function (e) {
        var form = e.target;
        if (!form.matches || !form.matches('form[data-validate]')) return;
        setTimeout(function () {
            form.querySelectorAll('[data-v], [required]').forEach(function (el) {
                setError(el, ''); el.classList.remove('is-valid'); delete el.dataset.vTouched;
            });
        }, 0);
    }, true);

    // Modals reuse the same form: clear old errors each time one opens
    document.addEventListener('show.bs.modal', function (e) {
        e.target.querySelectorAll('form[data-validate] [data-v], form[data-validate] [required]').forEach(function (el) {
            setError(el, ''); el.classList.remove('is-valid'); delete el.dataset.vTouched;
        });
        prepare(e.target);
    });

    window.GTMSValidate = { check: check, prepare: prepare, validateForm: validateForm, showErrors: showErrors, rules: RULES };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { prepare(); });
    else prepare();
})();
