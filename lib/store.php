<?php
declare(strict_types=1);

/**
 * ذخیره‌سازی لینک‌های کوتاه.
 * اگر SQLite در دسترس باشد از آن استفاده می‌شود، در غیر این صورت
 * به‌صورت خودکار روی یک فایل JSON کار می‌کند تا روی هر هاست اشتراکی اجرا شود.
 */
final class Store
{
    /** @var PDO|null */
    private $pdo = null;
    private string $dir;
    private string $jsonFile;

    public function __construct(string $dir)
    {
        $this->dir = rtrim($dir, '/');
        if (!is_dir($this->dir)) {
            @mkdir($this->dir, 0775, true);
        }
        $this->jsonFile = $this->dir . '/links.json';

        if (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            try {
                $pdo = new PDO('sqlite:' . $this->dir . '/links.sqlite');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->exec('CREATE TABLE IF NOT EXISTS links (
                    code TEXT PRIMARY KEY,
                    prompt TEXT NOT NULL,
                    created_at INTEGER NOT NULL,
                    views INTEGER NOT NULL DEFAULT 0,
                    ip TEXT
                )');
                $this->pdo = $pdo;
            } catch (Throwable $e) {
                $this->pdo = null;
            }
        }
    }

    public function exists(string $code): bool
    {
        return $this->find($code) !== null;
    }

    /** @return array<string,mixed>|null */
    public function find(string $code): ?array
    {
        if ($this->pdo) {
            $st = $this->pdo->prepare('SELECT * FROM links WHERE code = ? LIMIT 1');
            $st->execute([$code]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }
        $all = $this->readJson();
        return $all[$code] ?? null;
    }

    public function save(string $code, string $prompt, string $ip = ''): void
    {
        if ($this->pdo) {
            $st = $this->pdo->prepare('INSERT OR REPLACE INTO links (code, prompt, created_at, views, ip) VALUES (?, ?, ?, 0, ?)');
            $st->execute([$code, $prompt, time(), $ip]);
            return;
        }
        $all = $this->readJson();
        $all[$code] = [
            'code' => $code,
            'prompt' => $prompt,
            'created_at' => time(),
            'views' => 0,
            'ip' => $ip,
        ];
        $this->writeJson($all);
    }

    /** شمارش بازدید */
    public function hit(string $code): void
    {
        if ($this->pdo) {
            $st = $this->pdo->prepare('UPDATE links SET views = views + 1 WHERE code = ?');
            $st->execute([$code]);
            return;
        }
        $all = $this->readJson();
        if (isset($all[$code])) {
            $all[$code]['views'] = (int)$all[$code]['views'] + 1;
            $this->writeJson($all);
        }
    }

    /** @return array<string,array<string,mixed>> */
    private function readJson(): array
    {
        if (!is_file($this->jsonFile)) {
            return [];
        }
        $raw = (string)@file_get_contents($this->jsonFile);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /** @param array<string,array<string,mixed>> $data */
    private function writeJson(array $data): void
    {
        $fp = @fopen($this->jsonFile, 'c+');
        if (!$fp) {
            return;
        }
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, (string)json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
}
