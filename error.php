<?php
declare(strict_types=1);

/**
 * صفحهٔ خطای مشترک — بزار من برات AI کنم (bmbaik.ir)
 *
 * روش‌های استفاده:
 *   1) Apache: ErrorDocument 404 /error.php   یا قاعدهٔ rewrite در .htaccess
 *   2) لینک مستقیم: error.php?code=403
 *   3) از داخل PHP:
 *        $errorCode = 404; $errorTitle = '…'; $errorText = '…';
 *        require __DIR__ . '/error.php'; exit;
 */

require_once __DIR__ . '/lib/helpers.php';

$errorMap = [
    400 => ['درخواست نامعتبر',      'چیزی که فرستادی برای سرور قابل‌فهم نبود.',                    'فرم را دوباره و کامل پر کن.'],
    401 => ['ورود لازم است',        'برای دیدن این صفحه باید احراز هویت شوی.',                     'اگر لینک را از کسی گرفتی، دوباره ازش بخواه.'],
    403 => ['دسترسی ممنوع',        'این در برای تو باز نمی‌شود.',                                  'مسیر دیگری را امتحان کن.'],
    404 => ['این صفحه پیدا نشد',     'آدرسی که دنبالش بودی وجود ندارد یا پاک شده است.',        'شاید یک حرف اشتباه تایپ شده باشد.'],
    405 => ['روش مجاز نیست',       'این صفحه با این روش درخواست قابل دسترسی نیست.',           'از صفحهٔ اصلی شروع کن.'],
    408 => ['زمان تمام شد',          'درخواست بیش از حد طول کشید.',                                 'یک بار دیگر تلاش کن.'],
    413 => ['درخواست خیلی بزرگ است', 'متن یا فایلی که فرستادی بیش از حد مجاز است.',            'کمی کوتاه‌ترش کن و دوباره بفرست.'],
    429 => ['کمی آرام‌تر!',         'درخواست‌ها خیلی پشت سر هم بودند.',                          'چند ثانیه صبر کن و دوباره امتحان کن.'],
    500 => ['خطای داخلی سرور',    'از سمت ما مشکلی پیش آمد، نه از سمت تو.',                     'چند لحظه دیگر دوباره سر بزن.'],
    502 => ['دروازه خراب است',     'سرور پاسخ معتبری دریافت نکرد.',                             'کمی بعد دوباره تلاش کن.'],
    503 => ['سرویس در دسترس نیست', 'فعلاً در حال استراحت یا به‌روزرسانی هستیم.',              'به‌زودی برمی‌گردیم.'],
    504 => ['پاسخی نرسید',          'زمان انتظار سرور تمام شد.',                                  'یک بار دیگر صفحه را باز کن.'],
];

$code = 0;
if (isset($errorCode)) {
    $code = (int)$errorCode;
} elseif (isset($_GET['code'])) {
    $code = (int)$_GET['code'];
} elseif (!empty($_SERVER['REDIRECT_STATUS'])) {
    $code = (int)$_SERVER['REDIRECT_STATUS'];
}
if (!isset($errorMap[$code])) {
    $code = 404;
}

[$defTitle, $defText, $defHint] = $errorMap[$code];
$title = isset($errorTitle) ? (string)$errorTitle : $defTitle;
$text  = isset($errorText)  ? (string)$errorText  : $defText;
$hint  = isset($errorHint)  ? (string)$errorHint  : $defHint;

if (!headers_sent()) {
    http_response_code($code);
}

$askPrompt = sprintf('خطای %d در وب یعنی چی و چطور حلش کنم؟', $code);
$askUrl    = (string)cfg('chatgpt_url') . rawurlencode($askPrompt);

$pageTitle = $title . ' (' . $code . ') | ' . cfg('app_name');
$bodyClass = 'page-error';
include __DIR__ . '/partials/head.php';
?>

<section class="chat-area error-area">
  <div class="center-wrap error-wrap">

    <div class="error-orb error-orb-a" aria-hidden="true"></div>
    <div class="error-orb error-orb-b" aria-hidden="true"></div>

    <img class="brand-logo fade-in" src="<?= e(asset('img/openai.svg')) ?>" alt="" width="40" height="40">

    <div class="error-code fade-in" data-code="<?= (int)$code ?>" aria-hidden="true"><?= (int)$code ?></div>

    <h1 class="hero-title error-title fade-in"><?= e($title) ?></h1>
    <p class="error-desc fade-in"><?= e($text) ?></p>

    <div class="error-hint fade-in">
      <span class="notice-badge"><?= e((string)cfg('app_name')) ?></span>
      <p><?= e($hint) ?></p>
    </div>

    <div class="error-actions fade-in">
      <a class="btn btn-white" href="<?= e(base_url()) ?>/">بازگشت به صفحهٔ اصلی</a>
      <button class="btn btn-ghost" type="button" onclick="history.length&gt;1?history.back():location.assign('<?= e(base_url()) ?>/')">صفحهٔ قبلی</button>
      <a class="btn btn-ghost" href="<?= e($askUrl) ?>" target="_blank" rel="noopener">این خطا را از AI بپرس</a>
    </div>

    <p class="disclaimer error-path">
      کد وضعیت: <code><?= (int)$code ?></code>
      <?php if (!empty($_SERVER['REQUEST_URI'])): ?>
        — مسیر: <code><?= e(substr((string)$_SERVER['REQUEST_URI'], 0, 120)) ?></code>
      <?php endif; ?>
    </p>
  </div>
</section>

<?php include __DIR__ . '/partials/foot.php'; ?>
