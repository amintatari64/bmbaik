/* از AI بپرس — اسکریپت عمومی */
(function () {
  'use strict';

  var sidebar = document.getElementById('sidebar');
  var backdrop = document.getElementById('backdrop');
  var openBtn = document.getElementById('openSidebar');
  var closeBtn = document.getElementById('closeSidebar');

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('is-open');
    if (backdrop) backdrop.hidden = false;
  }
  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('is-open');
    if (backdrop) backdrop.hidden = true;
  }
  if (openBtn) openBtn.addEventListener('click', openSidebar);
  if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  /* بزرگ شدن خودکار کادر متن — مثل ChatGPT */
  var ta = document.getElementById('prompt');
  function autoGrow() {
    if (!ta) return;
    ta.style.height = 'auto';
    ta.style.height = Math.min(ta.scrollHeight, 200) + 'px';
  }
  if (ta) {
    ta.addEventListener('input', autoGrow);
    autoGrow();
  }

  /* Enter = ارسال، Shift+Enter = خط جدید */
  var form = document.getElementById('askForm');
  if (form && ta) {
    ta.addEventListener('keydown', function (ev) {
      if (ev.key === 'Enter' && !ev.shiftKey) {
        ev.preventDefault();
        if (ta.value.trim() !== '') form.requestSubmit();
      }
    });
    form.addEventListener('submit', function (ev) {
      if (ta.value.trim() === '') {
        ev.preventDefault();
        ta.focus();
      }
    });
    setTimeout(function () { ta.focus(); }, 250);
  }

  /* چیپ‌های پیشنهادی */
  Array.prototype.forEach.call(document.querySelectorAll('.chip[data-fill]'), function (chip) {
    chip.addEventListener('click', function () {
      if (!ta) return;
      ta.value = chip.getAttribute('data-fill');
      ta.focus();
      autoGrow();
    });
  });

  /* کپی کردن لینک */
  var copyBtn = document.getElementById('copyBtn');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var input = document.querySelector(copyBtn.getAttribute('data-copy'));
      if (!input) return;
      var done = function () {
        var old = copyBtn.textContent;
        copyBtn.textContent = 'کپی شد ✓';
        setTimeout(function () { copyBtn.textContent = old; }, 1600);
      };
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(done);
      } else {
        input.removeAttribute('readonly');
        input.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        input.setAttribute('readonly', 'readonly');
      }
    });
  }
})();
