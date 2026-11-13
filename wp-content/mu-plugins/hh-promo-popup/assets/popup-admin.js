/* hh-promo-popup — Admin toggle script
 * Handles the enable/disable switch rendered by hh_popup_render_admin_toggle()
 * in the WP back-office dashboard pages.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    var toggleWrap = document.getElementById('hh-popup-pref');
    if (!toggleWrap) return;

    var switchInput = document.getElementById('hh-popup-enabled-switch');
    var stateEl     = document.getElementById('hh-popup-pref-state');
    var savingEl    = document.getElementById('hh-popup-pref-saving');
    if (!switchInput) return;

    function setStateUI(enabled) {
      if (stateEl) {
        stateEl.innerHTML = enabled
          ? '<span style="color:#2f8f57;font-weight:600">Enabled</span>'
          : '<span style="color:#b32d2e;font-weight:600">Disabled</span>';
      }
    }

    switchInput.addEventListener('change', function () {
      var enabled = switchInput.checked;

      if (savingEl) savingEl.style.display = 'inline';
      switchInput.disabled = true;

      var data = new FormData();
      data.append('action',  'hh_popup_admin_toggle');
      data.append('nonce',   hhPopupAdminData.nonce);
      data.append('enabled', enabled ? '1' : '0');

      fetch(hhPopupAdminData.ajaxUrl, { method: 'POST', body: data })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.success) {
            setStateUI(res.data.enabled);
          } else {
            // Revert on failure.
            switchInput.checked = !enabled;
            setStateUI(!enabled);
            alert('Failed to save popup setting. Please try again.');
          }
        })
        .catch(function () {
          switchInput.checked = !enabled;
          setStateUI(!enabled);
          alert('Network error. Please try again.');
        })
        .finally(function () {
          switchInput.disabled = false;
          if (savingEl) savingEl.style.display = 'none';
        });
    });
  });
}());
