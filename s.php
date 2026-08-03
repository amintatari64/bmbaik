<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/store.php';

$code  = (string)($_GET['c'] ?? '');
$store = new Store((string)cfg('data_dir'));
$row   = preg_match('/^[A-Za-z0-9_-]{3,32}$/', $code) ? $store->find($code) : null;

if (!$row) {
    $errorCode  = 404;
    $errorTitle = 'این لینک پیدا نشد';
    $errorText  = 'شاید کد اشتباه تایپ شده باشد یا لینک پاک شده باشد.';
    $errorHint  = 'می‌توانی در چند ثانیه یک لینک تازه بسازی.';
    require __DIR__ . '/error.php';
    exit;
}

$store->hit($code);

$prompt = (string)$row['prompt'];
$target = (string)cfg('chatgpt_url') . rawurlencode($prompt);

$pageTitle  = 'ChatGPT';
$bodyClass  = 'page-play';
$pageScript = asset('js/play.js');
include __DIR__ . '/partials/head.php';
?>

<section class="chat-area"
         id="stage"
         data-prompt="<?= e($prompt) ?>"
         data-target="<?= e($target) ?>"
         data-speed="<?= (int)cfg('typing_speed') ?>">
  <div class="center-wrap">

    <div class="notice notice-tease fade-in">
      <span class="notice-badge"><?= e((string)cfg('app_name')) ?></span>
      <p>خبر خوب: لازم نیست این را از کسی بپرسی — هوش مصنوعی همیشه بیدار است. بیا دفعهٔ بعد خودت امتحان کنی، دقیقاً همین‌قدر ساده است 😌</p>
    </div>

    <h1 class="hero-title fade-in">امروز چه کمکی از دستم برمی‌آید؟</h1>

    <form class="composer-form fade-in" id="playForm" onsubmit="return false;">
      <?php $readonly = true; include __DIR__ . '/partials/composer.php'; ?>
    </form>

    <div class="suggestions fade-in">
      <span class="chip is-static">✨ ساخت تصویر</span>
      <span class="chip is-static">📝 کمک به نوشتن</span>
      <span class="chip is-static only-desktop">📄 خلاصه‌سازی متن</span>
    </div>

    <p class="disclaimer">ChatGPT می‌تواند اشتباه کند. اطلاعات مهم را بررسی کنید.</p>

    <button class="skip-btn" type="button" id="skipBtn">رد کردن انیمیشن و رفتن به ChatGPT ←</button>

    <noscript>
      <p class="disclaimer">جاوااسکریپت غیرفعال است.
        <a href="<?= e($target) ?>">اینجا بزنید تا به ChatGPT بروید</a>.
      </p>
    </noscript>
  </div>

  <div class="fake-cursor" id="fakeCursor" aria-hidden="true">
    <svg width="22" height="22" viewBox="0 0 24 24"><path d="M5 2.5 19.5 11l-6.4 1.6L10.6 19 5 2.5Z" fill="#fff" stroke="#111" stroke-width="1.1" stroke-linejoin="round"/></svg>
  </div>

  <div class="overlay" id="overlay" aria-hidden="true">
    <img src="<?= e(asset('img/openai.svg')) ?>" alt="" width="40" height="40" class="overlay-logo">
    <p>در حال انتقال به ChatGPT…</p>
  </div>
</section>

<?php include __DIR__ . '/partials/foot.php'; ?>
