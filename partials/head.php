<?php
declare(strict_types=1);
if (!function_exists('cfg')) {
    require_once dirname(__DIR__) . '/lib/helpers.php';
}
$pageTitle = $pageTitle ?? (string)cfg('app_name');
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#212121">
<meta name="color-scheme" content="dark">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="از AI بپرس — به‌جای جواب دادن به کسی که می‌توانست خودش از هوش مصنوعی بپرسد، لینک بفرست.">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:type" content="website">
<link rel="icon" type="image/svg+xml" href="<?= e(asset('img/openai.svg')) ?>">
<link rel="apple-touch-icon" href="<?= e(asset('img/openai.svg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
<div class="app">

  <aside class="sidebar" id="sidebar" aria-label="نوار کناری">
    <div class="sidebar-top">
      <button class="icon-btn" id="closeSidebar" title="بستن نوار کناری" aria-label="بستن نوار کناری">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 5.5h18M3 12h18M3 18.5h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      </button>
      <a class="icon-btn" href="<?= e(base_url()) ?>/" title="چت جدید" aria-label="چت جدید">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10a2.5 2.5 0 0 0-3.5-3.5L4.5 16.5 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
      </a>
    </div>

    <nav class="sidebar-nav">
      <a class="side-item" href="<?= e(base_url()) ?>/">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 20h4l10-10a2.5 2.5 0 0 0-3.5-3.5L4.5 16.5 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        <span>چت جدید</span>
      </a>
      <a class="side-item" href="<?= e(base_url()) ?>/">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        <span>جست‌وجو در گفتگوها</span>
      </a>
      <a class="side-item" href="<?= e(base_url()) ?>/">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3.5" y="4" width="17" height="16" rx="2.5" stroke="currentColor" stroke-width="1.7"/><path d="M8 4v16" stroke="currentColor" stroke-width="1.7"/></svg>
        <span>کتابخانه</span>
      </a>
    </nav>

    <div class="sidebar-section">
      <div class="side-label">گفتگوها</div>
      <div class="side-chat">لینک‌های طعنه‌آمیز من</div>
      <div class="side-chat">چرا خودت نمی‌پرسی؟</div>
      <div class="side-chat">راهنمای پرسیدن از AI</div>
    </div>

    <div class="sidebar-bottom">
      <a class="side-item" href="https://github.com/amintatari64/askai" target="_blank" rel="noopener">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/><path d="M12 17v-4M12 8.5v.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span>دربارهٔ این پروژه</span>
      </a>
    </div>
  </aside>

  <div class="sidebar-backdrop" id="backdrop" hidden></div>

  <main class="main">
    <header class="topbar">
      <div class="topbar-start">
        <button class="icon-btn only-mobile" id="openSidebar" aria-label="باز کردن نوار کناری">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 5.5h18M3 12h18M3 18.5h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <button class="model-btn" type="button">
          <span>ChatGPT</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
      <div class="topbar-end"></div>
    </header>
