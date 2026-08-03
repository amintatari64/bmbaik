<?php
/**
 * کادر ورود پیام — دقیقاً مشابه composer صفحهٔ اصلی ChatGPT
 * متغیرها: $readonly (bool)
 */
$readonly = $readonly ?? false;
?>
<div class="composer">
  <div class="composer-inner">
    <textarea
      id="prompt"
      name="prompt"
      class="composer-input"
      rows="1"
      maxlength="<?= (int)cfg('max_len') ?>"
      placeholder="هر چیزی بپرسید"
      <?= $readonly ? 'readonly' : '' ?>
      autocomplete="off"
      spellcheck="false"></textarea>

    <div class="composer-bar">
      <div class="composer-left">
        <button class="round-btn" type="button" tabindex="-1" aria-label="افزودن">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
        </button>
        <button class="pill-btn" type="button" tabindex="-1">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h10M4 17h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          <span>ابزارها</span>
        </button>
      </div>
      <div class="composer-right">
        <button class="round-btn" type="button" tabindex="-1" aria-label="ضبط صدا">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="9" y="3" width="6" height="11" rx="3" stroke="currentColor" stroke-width="1.8"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
        <button class="send-btn" id="sendBtn" type="submit" aria-label="ارسال پیام">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 19V5.5M12 5.5 5.5 12M12 5.5 18.5 12" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>
  </div>
</div>
