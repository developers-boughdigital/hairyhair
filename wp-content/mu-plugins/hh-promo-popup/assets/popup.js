/**
 * HH Promo Popup — popup.js
 * Handles: show-after-delay, dismiss cookie, close, copy-code, email form, AJAX tracking.
 * No jQuery dependency.
 */

(function () {
  'use strict';

  if (typeof hhPopupData === 'undefined') return;

  var data        = hhPopupData;
  var DISMISS_KEY = data.dismissCookie || 'hh_popup_dismissed';
  var ENABLED_KEY = data.enabledCookie || 'hh_popup_enabled';
  var VARIANT     = data.variant || 'a';
  var VARIANT_ENABLED = !!data.variantEnabled;
  var SESSION_SHOWN_KEY = 'hh_popup_shown';
  var DEFAULT_DELAY_MIN = 10;
  var DEFAULT_DELAY_MAX = 15;

  // ── Cookie helpers ─────────────────────────────────────────────────────────

  function getCookie(name) {
    var match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
  }

  function setCookie(name, value, days) {
    var expires = '';
    if (days) {
      var d = new Date();
      d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
      expires = '; expires=' + d.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
  }

  function deleteCookie(name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
  }

  function isPopupEnabled() {
    return getCookie(ENABLED_KEY) !== '0';
  }

  function setPopupEnabled(enabled) {
    setCookie(ENABLED_KEY, enabled ? '1' : '0', 365);
  }

  // ── AJAX helpers ───────────────────────────────────────────────────────────

  function ajaxPost(action, extraParams) {
    if (!data.ajaxUrl || !data.nonce) return;
    var params = 'action=' + encodeURIComponent(action) +
                 '&nonce='   + encodeURIComponent(data.nonce) +
                 '&variant=' + encodeURIComponent(VARIANT);
    if (extraParams) {
      Object.keys(extraParams).forEach(function (k) {
        params += '&' + encodeURIComponent(k) + '=' + encodeURIComponent(extraParams[k]);
      });
    }
    var xhr = new XMLHttpRequest();
    xhr.open('POST', data.ajaxUrl, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    xhr.send(params);
  }

  // ── Show / hide ────────────────────────────────────────────────────────────

  function showPopup(overlay) {
    overlay.style.display = 'flex';
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    trapFocus(overlay);
    ajaxPost('hh_popup_impression');
  }

  function hidePopup(overlay, persistDismiss) {
    overlay.style.display = 'none';
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (persistDismiss !== false) {
      setCookie(DISMISS_KEY, '1'); // session cookie — expires when browser closes
    }
  }

  function shouldShowPopup() {
    if (!isPopupEnabled()) return false;
    if (getCookie(DISMISS_KEY) === '1') return false;
    try {
      if (sessionStorage.getItem(SESSION_SHOWN_KEY) === '1') return false;
    } catch (e) {
      // If sessionStorage is unavailable, fall back to cookie check only.
    }
    return true;
  }

  function markPopupShown() {
    try {
      sessionStorage.setItem(SESSION_SHOWN_KEY, '1');
    } catch (e) {
      // Ignore storage errors.
    }
  }

  function getDelayMs() {
    var delaySeconds = parseInt(data.delay, 10);
    if (Number.isFinite(delaySeconds) && delaySeconds >= DEFAULT_DELAY_MIN && delaySeconds <= DEFAULT_DELAY_MAX) {
      return delaySeconds * 1000;
    }
    var range = DEFAULT_DELAY_MAX - DEFAULT_DELAY_MIN + 1;
    return (DEFAULT_DELAY_MIN + Math.floor(Math.random() * range)) * 1000;
  }

  function getScrollPercent() {
    var doc = document.documentElement;
    var scrollTop = window.pageYOffset || doc.scrollTop || 0;
    var scrollHeight = Math.max(doc.scrollHeight, document.body.scrollHeight);
    var clientHeight = doc.clientHeight || window.innerHeight || 0;
    var track = scrollHeight - clientHeight;
    if (track <= 0) return 100;
    return (scrollTop / track) * 100;
  }

  function initPreferenceToggle(overlay) {
    var toggleWrap = document.getElementById('hh-popup-pref');
    if (!toggleWrap) return;

    var switchInput = toggleWrap.querySelector('#hh-popup-enabled-switch');
    var stateBadge  = toggleWrap.querySelector('.hh-popup-pref-state');
    if (!switchInput) return;

    if (!VARIANT_ENABLED) {
      switchInput.checked = false;
      switchInput.disabled = true;
      if (stateBadge) {
        stateBadge.textContent = 'Admin Off';
        stateBadge.classList.remove('text-bg-light');
        stateBadge.classList.add('text-bg-secondary');
      }
      return;
    }

    function syncStateUI() {
      var enabled = !!switchInput.checked;
      if (!stateBadge) return;
      stateBadge.textContent = enabled ? 'On' : 'Off';
      stateBadge.classList.toggle('text-bg-light', enabled);
      stateBadge.classList.toggle('text-bg-secondary', !enabled);
    }

    switchInput.checked = isPopupEnabled();
    syncStateUI();

    switchInput.addEventListener('change', function () {
      var enabled = !!switchInput.checked;
      setPopupEnabled(enabled);

      if (!enabled) {
        if (overlay) hidePopup(overlay, false);
        setCookie(DISMISS_KEY, '1', 30);
      } else {
        deleteCookie(DISMISS_KEY);
        if (overlay && overlay.style.display !== 'flex') {
          setTimeout(function () {
            if (!isPopupEnabled() || getCookie(DISMISS_KEY) === '1') return;
            showPopup(overlay);
          }, 300);
        }
      }

      syncStateUI();
    });
  }

  // ── Focus trap ────────────────────────────────────────────────────────────

  function trapFocus(container) {
    var focusable = container.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    if (!focusable.length) return;
    focusable[0].focus();

    container.addEventListener('keydown', function onKey(e) {
      if (e.key !== 'Tab') {
        if (e.key === 'Escape') {
          hidePopup(container.closest('.hh-popup-overlay') || container);
          container.removeEventListener('keydown', onKey);
        }
        return;
      }
      var first = focusable[0];
      var last  = focusable[focusable.length - 1];
      if (e.shiftKey) {
        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
      } else {
        if (document.activeElement === last)  { e.preventDefault(); first.focus(); }
      }
    });
  }

  // ── Copy-to-clipboard ─────────────────────────────────────────────────────

  function copyCode(btn) {
    var code = btn.getAttribute('data-code') || '';
    if (!code) return;

    var done = function () {
      var original = btn.innerHTML;
      btn.classList.add('hh-copied');
      btn.innerHTML = 'COPIED ✓';
      ajaxPost('hh_popup_conversion');
      setTimeout(function () {
        btn.classList.remove('hh-copied');
        btn.innerHTML = original;
      }, 2200);
    };

    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(code).then(done).catch(function () {
        legacyCopy(code);
        done();
      });
    } else {
      legacyCopy(code);
      done();
    }
  }

  function legacyCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    try { document.execCommand('copy'); } catch (e) { /* silent */ }
    document.body.removeChild(ta);
  }

  // ── Email form (Variant C) ─────────────────────────────────────────────────

  function initEmailForm() {
    var form   = document.getElementById('hh-popup-email-form');
    var reveal = document.querySelector('.hh-vc-reveal');
    if (!form || !reveal) return;

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var emailInput = form.querySelector('input[type="email"]');
      if (!emailInput) return;
      var email = emailInput.value.trim();

      // Basic format validation (OWASP: validate at boundary)
      var emailRe = /^[^\s@]{1,64}@[^\s@]{1,255}$/;
      if (!emailRe.test(email)) {
        emailInput.focus();
        emailInput.classList.add('hh-input-error');
        return;
      }
      emailInput.classList.remove('hh-input-error');

      // Show reveal
      form.style.display = 'none';
      reveal.style.display = 'block';
      ajaxPost('hh_popup_conversion', { email_submitted: 1 });
    });
  }

  // ── Boot ──────────────────────────────────────────────────────────────────

  function init() {
    var overlay = document.getElementById('hh-promo-popup');
    initPreferenceToggle(overlay);

    if (!overlay) return;

    if (!shouldShowPopup()) return;

    // Close button
    var closeBtn = overlay.querySelector('.hh-popup-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () { hidePopup(overlay, false); });
    }

    // Click outside container
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) hidePopup(overlay, false);
    });

    // Copy buttons (includes CTA amber button)
    overlay.addEventListener('click', function (e) {
      var btn = e.target.closest('.hh-copy-btn');
      if (btn) copyCode(btn);
    });

    var shown = false;
    var delayTimer = null;

    function clearTriggers() {
      if (delayTimer) {
        clearTimeout(delayTimer);
        delayTimer = null;
      }
      window.removeEventListener('scroll', onScroll, { passive: true });
    }

    function triggerShow() {
      if (shown || !shouldShowPopup()) return;
      shown = true;
      markPopupShown();
      clearTriggers();
      showPopup(overlay);
    }

    function onScroll() {
      if (getScrollPercent() >= 50) {
        triggerShow();
      }
    }

    delayTimer = setTimeout(triggerShow, getDelayMs());
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
