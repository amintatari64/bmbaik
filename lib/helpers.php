<?php
declare(strict_types=1);

/** خواندن تنظیمات */
function cfg(?string $key = null)
{
    static $config = null;
    if ($config === null) {
        $config = require dirname(__DIR__) . '/config.php';
    }
    if ($key === null) {
        return $config;
    }
    return $config[$key] ?? null;
}

/** escape خروجی HTML */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * آدرس پایهٔ پروژه را حتی اگر داخل زیرپوشه باشد (مثلاً http://localhost/ai)
 * به‌درستی تشخیص می‌دهد. همین موضوع دلیل خطای 404 در لینک‌های کوتاه بود.
 */
function base_url(): string
{
    $https = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443);

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = str_replace('\\', '/', dirname($script));
    $dir = ($dir === '/' || $dir === '.') ? '' : rtrim($dir, '/');

    return ($https ? 'https' : 'http') . '://' . $host . $dir;
}

/** آیا mod_rewrite فعال است؟ (توسط .htaccess علامت‌گذاری می‌شود) */
function pretty_urls(): bool
{
    foreach (['ASKAI_REWRITE', 'REDIRECT_ASKAI_REWRITE', 'REDIRECT_REDIRECT_ASKAI_REWRITE'] as $k) {
        if (($_SERVER[$k] ?? getenv($k)) === '1') {
            return true;
        }
    }
    if (function_exists('apache_get_modules')) {
        return in_array('mod_rewrite', apache_get_modules(), true);
    }
    return false;
}

/** آدرس فایل‌های استاتیک — همیشه مطلق تا در مسیر /s/CODE هم درست باشد */
function asset(string $path): string
{
    return base_url() . '/assets/' . ltrim($path, '/');
}

/** ساخت لینک کوتاه نهایی */
function short_url(string $code): string
{
    return pretty_urls()
        ? base_url() . '/s/' . $code
        : base_url() . '/s.php?c=' . rawurlencode($code);
}

/** تولید کد تصادفی امن */
function random_code(int $length = 6): string
{
    $alphabet = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $max = strlen($alphabet) - 1;
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $out .= $alphabet[random_int(0, $max)];
    }
    return $out;
}

/** امضای زمان فرم برای ضدّاسپم */
function form_signature(int $ts): string
{
    return hash_hmac('sha256', (string)$ts, (string)cfg('secret'));
}

/** IP کاربر (فقط برای آمار ساده) */
function client_ip(): string
{
    return (string)($_SERVER['REMOTE_ADDR'] ?? '');
}
