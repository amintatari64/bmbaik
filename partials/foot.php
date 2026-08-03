  <footer class="site-footer">
    <span class="footer-name"><?= e(cfg('app_name')) ?></span>
    <span class="footer-sep">•</span>
    <span class="footer-credit">
      الهام گرفته شده از
      <a href="<?= e((string)cfg('credit_url')) ?>" target="_blank" rel="noopener" dir="ltr">bmbgk.ir</a>
    </span>
  </footer>
  </main>
</div>

<script src="<?= e(asset('js/app.js')) ?>" defer></script>
<?php if (!empty($pageScript)): ?>
<script src="<?= e($pageScript) ?>" defer></script>
<?php endif; ?>
</body>
</html>
