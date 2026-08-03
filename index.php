<?php
declare(strict_types=1);

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/store.php';

// راه دوم برای باز کردن لینک کوتاه (اگر mod_rewrite نباشد): index.php?c=CODE
if (isset($_GET['c']) && $_GET['c'] !== '') {
    require __DIR__ . '/s.php';
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$store   = new Store((string)cfg('data_dir'));
$errors  = [];
$old     = '';
$created = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $prompt = trim((string)($_POST['prompt'] ?? ''));
    $alias  = trim((string)($_POST['alias'] ?? ''));
    $old    = $prompt;

    // --- ضدّاسپم ۱: تلهٔ عسل (honeypot) ---
    if (trim((string)($_POST['website'] ?? '')) !== '' || trim((string)($_POST['company'] ?? '')) !== '') {
        $errors[] = 'درخواست نامعتبر است.';
    }

    // --- ضدّاسپم ۲: زمان پر کردن فرم ---
    if (!$errors) {
        $ts  = (int)($_POST['ts'] ?? 0);
        $sig = (string)($_POST['sig'] ?? '');
        if ($ts <= 0 || !hash_equals(form_signature($ts), $sig)) {
            $errors[] = 'فرم منقضی شده است؛ صفحه را تازه کنید.';
        } elseif ((time() - $ts) < (float)cfg('min_fill_time')) {
            $errors[] = 'کمی آهسته‌تر! دوباره تلاش کنید.';
        }
    }

    // --- ضدّاسپم ۳: محدودیت سرعت ---
    if (!$errors) {
        $last = (int)($_SESSION['askai_last'] ?? 0);
        if ($last > 0 && (time() - $last) < (int)cfg('rate_limit')) {
            $errors[] = 'خیلی سریع! چند ثانیه صبر کنید.';
        }
    }

    // --- اعتبارسنجی ---
    if (!$errors) {
        if ($prompt === '') {
            $errors[] = 'متن سؤال را بنویسید.';
        } elseif (mb_strlen($prompt, 'UTF-8') > (int)cfg('max_len')) {
            $errors[] = 'متن طولانی‌تر از حد مجاز است.';
        }
        if ($alias !== '' && !preg_match('/^[A-Za-z0-9_-]{3,32}$/', $alias)) {
            $errors[] = 'کد دلخواه فقط حروف انگلیسی، عدد، - و _ (۳ تا ۳۲ کاراکتر).';
        } elseif ($alias !== '' && $store->exists($alias)) {
            $errors[] = 'این کد قبلاً استفاده شده است.';
        }
    }

    if (!$errors) {
        $code = $alias;
        if ($code === '') {
            do {
                $code = random_code((int)cfg('code_length'));
            } while ($store->exists($code));
        }
        $store->save($code, $prompt, client_ip());
        $_SESSION['askai_last'] = time();

        // الگوی POST/Redirect/GET
        header('Location: ' . base_url() . '/?created=' . rawurlencode($code), true, 303);
        exit;
    }
}

if (isset($_GET['created'])) {
    $code = (string)$_GET['created'];
    if (preg_match('/^[A-Za-z0-9_-]{3,32}$/', $code)) {
        $row = $store->find($code);
        if ($row) {
            $created = [
                'code'   => $code,
                'prompt' => (string)$row['prompt'],
                'url'    => short_url($code),
            ];
        }
    }
}

$ts        = time();
$sig       = form_signature($ts);
$pageTitle = (string)cfg('app_name') . ' | ChatGPT';
$bodyClass = 'page-home';
include __DIR__ . '/partials/head.php';
?>

<section class="chat-area">
  <div class="center-wrap">

    <div class="notice fade-in" role="note">
      <span class="notice-badge">از AI بپرس</span>
      <p>
        یکی به‌جای پرسیدن از هوش مصنوعی، از شما پرسیده؟ سؤالش را اینجا بنویسید و دکمهٔ ارسال را بزنید؛
        یک لینک کوتاه می‌گیرید. لینک را که باز کند، خودش می‌بیند که چطور باید از ChatGPT بپرسد 🙂
      </p>
    </div>

    <?php if ($created): ?>
      <div class="result-card fade-in">
        <div class="result-title">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="m5 13 4 4 10-10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          لینک اختصاصی شما آماده است
        </div>
        <div class="result-link">
          <input type="text" id="shortLink" value="<?= e($created['url']) ?>" readonly dir="ltr">
          <button class="btn btn-white" type="button" id="copyBtn" data-copy="#shortLink">کپی</button>
        </div>
        <div class="result-actions">
          <a class="btn btn-ghost" href="<?= e($created['url']) ?>" target="_blank" rel="noopener">پیش‌نمایش</a>
          <a class="btn btn-ghost" href="<?= e(base_url()) ?>/">ساخت لینک جدید</a>
        </div>
        <p class="result-prompt">متن سؤال: «<?= e(mb_substr($created['prompt'], 0, 160, 'UTF-8')) ?><?= mb_strlen($created['prompt'], 'UTF-8') > 160 ? '…' : '' ?>»</p>
      </div>
    <?php endif; ?>

    <h1 class="hero-title fade-in">امروز چه کمکی از دستم برمی‌آید؟</h1>

    <?php if ($errors): ?>
      <div class="alert fade-in">
        <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form class="composer-form fade-in" method="post" action="<?= e(base_url()) ?>/" id="askForm" autocomplete="off">
      <input type="hidden" name="ts" value="<?= (int)$ts ?>">
      <input type="hidden" name="sig" value="<?= e($sig) ?>">

      <!-- honeypot: این دو فیلد برای کاربر دیده نمی‌شوند -->
      <div class="hp-field" aria-hidden="true">
        <label>وب‌سایت<input type="text" name="website" value="" tabindex="-1" autocomplete="off"></label>
        <label>شرکت<input type="text" name="company" value="" tabindex="-1" autocomplete="off"></label>
      </div>

      <?php $readonly = false; include __DIR__ . '/partials/composer.php'; ?>

      <details class="alias-box">
        <summary>کد دلخواه برای لینک (اختیاری)</summary>
        <div class="alias-row">
          <span dir="ltr"><?= e(rtrim(base_url(), '/')) ?>/s/</span>
          <input type="text" name="alias" dir="ltr" placeholder="my-question" pattern="[A-Za-z0-9_-]{3,32}">
        </div>
      </details>
    </form>

    <div class="suggestions fade-in">
      <button class="chip" type="button" data-fill="یک تصویر بساز از ">✨ ساخت تصویر</button>
      <button class="chip" type="button" data-fill="کمک کن این متن را بنویسم: ">📝 کمک به نوشتن</button>
      <button class="chip" type="button" data-fill="این متن را خلاصه کن: ">📄 خلاصه‌سازی متن</button>
      <button class="chip only-desktop" type="button" data-fill="برایم کد بنویس که ">💻 کدنویسی</button>
      <button class="chip only-desktop" type="button" data-fill="دربارهٔ این موضوع توضیح بده: ">💡 بررسی یک موضوع</button>
    </div>

    <p class="disclaimer">ChatGPT می‌تواند اشتباه کند. اطلاعات مهم را بررسی کنید.</p>
  </div>
</section>

<?php include __DIR__ . '/partials/foot.php'; ?>
