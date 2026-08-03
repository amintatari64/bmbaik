/* از AI بپرس — انیمیشن موس و تایپ خودکار و انتقال به ChatGPT */
(function () {
  'use strict';

  var stage = document.getElementById('stage');
  if (!stage) return;

  var prompt = stage.getAttribute('data-prompt') || '';
  var target = stage.getAttribute('data-target') || 'https://chatgpt.com/';
  var speed = parseInt(stage.getAttribute('data-speed') || '55', 10);

  var ta = document.getElementById('prompt');
  var send = document.getElementById('sendBtn');
  var cursor = document.getElementById('fakeCursor');
  var overlay = document.getElementById('overlay');
  var skip = document.getElementById('skipBtn');

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finished = false;

  function sleep(ms) {
    return new Promise(function (r) { setTimeout(r, ms); });
  }

  function moveTo(el) {
    if (!el || !cursor) return;
    var r = el.getBoundingClientRect();
    cursor.style.transform = 'translate(' + (r.left + r.width / 2) + 'px, ' + (r.top + r.height / 2) + 'px)';
  }

  function click(el) {
    if (cursor) cursor.classList.add('is-clicking');
    if (el) el.classList.add('is-clicked');
    setTimeout(function () {
      if (cursor) cursor.classList.remove('is-clicking');
      if (el) el.classList.remove('is-clicked');
    }, 220);
  }

  function grow() {
    if (!ta) return;
    ta.style.height = 'auto';
    ta.style.height = Math.min(ta.scrollHeight, 200) + 'px';
  }

  function go() {
    if (finished) return;
    finished = true;
    if (overlay) overlay.classList.add('is-visible');
    setTimeout(function () { window.location.href = target; }, 700);
  }

  if (skip) skip.addEventListener('click', go);

  async function run() {
    if (!ta || !cursor) { go(); return; }

    cursor.style.transform = 'translate(' + (window.innerWidth * 0.5) + 'px, ' + (window.innerHeight * 0.18) + 'px)';
    cursor.classList.add('is-visible');
    await sleep(700);

    // موس می‌رود روی کادر متن و کلیک می‌کند
    moveTo(ta);
    await sleep(1050);
    click(ta);
    ta.focus({ preventScroll: true });
    await sleep(380);

    // تایپ کردن متن
    var chars = Array.from(prompt);
    for (var i = 0; i < chars.length; i++) {
      ta.value += chars[i];
      grow();
      ta.scrollTop = ta.scrollHeight;
      await sleep(reduced ? 0 : speed + Math.random() * 45);
    }

    await sleep(650);

    // موس می‌رود روی دکمهٔ ارسال و می‌زند
    moveTo(send);
    await sleep(900);
    click(send);
    await sleep(320);

    go();
  }

  window.addEventListener('load', function () {
    setTimeout(run, 350);
  });

  // اگر به هر دلیلی انیمیشن گیر کرد، حداکثر پس از ۹۰ ثانیه منتقل می‌شویم
  setTimeout(go, 90000);
})();
