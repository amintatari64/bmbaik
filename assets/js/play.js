/* از AI بپرس — انیمیشن موس و تایپ خودکار و انتقال به ChatGPT (ریتم آرام) */
(function () {
  'use strict';

  var stage = document.getElementById('stage');
  if (!stage) return;

  var prompt = stage.getAttribute('data-prompt') || '';
  var target = stage.getAttribute('data-target') || 'https://chatgpt.com/';
  var speed = parseInt(stage.getAttribute('data-speed') || '95', 10);

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
    }, 260);
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
    setTimeout(function () { window.location.href = target; }, 900);
  }

  if (skip) skip.addEventListener('click', go);

  async function run() {
    if (!ta || !cursor) { go(); return; }

    cursor.style.transform = 'translate(' + (window.innerWidth * 0.5) + 'px, ' + (window.innerHeight * 0.18) + 'px)';
    cursor.classList.add('is-visible');
    await sleep(1000);

    // موس به آرامی روی کادر متن می‌رود و کلیک می‌کند
    moveTo(ta);
    await sleep(1750);
    click(ta);
    ta.focus({ preventScroll: true });
    await sleep(650);

    // تایپ کردن متن (با مکث بیشتر بعد از فاصله و نقطه)
    var chars = Array.from(prompt);
    for (var i = 0; i < chars.length; i++) {
      ta.value += chars[i];
      grow();
      ta.scrollTop = ta.scrollHeight;
      var extra = (chars[i] === ' ' ? 60 : 0) + ('.،!؟?\n'.indexOf(chars[i]) > -1 ? 220 : 0);
      await sleep(reduced ? 0 : speed + extra + Math.random() * 55);
    }

    await sleep(950);

    // موس به سمت دکمهٔ ارسال می‌رود و می‌زند
    moveTo(send);
    await sleep(1600);
    click(send);
    await sleep(500);

    go();
  }

  window.addEventListener('load', function () {
    setTimeout(run, 500);
  });

  // اگر به هر دلیلی انیمیشن گیر کرد، حداکثر پس از ۳ دقیقه منتقل می‌شویم
  setTimeout(go, 180000);
})();
