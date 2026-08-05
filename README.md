# Let Me AI That For You

**Domain:** [bmbaik.ir](https://bmbaik.ir)

The ChatGPT version of "Let Me Google That For You".

When someone asks you a question instead of asking an AI, you type their question on bmbaik.ir, get a short link, and send it to them.
When they open the link, they see a page that looks exactly like chatgpt.com: the mouse cursor clicks the text box, the question is typed out, the send button is pressed, and finally they are redirected to `https://chatgpt.com/?prompt=...`.

## Features

- Dark (default) and RTL design, just like the ChatGPT home page
- Fully responsive, with no horizontal scrolling
- OpenAI logo favicon
- Fade animation plus smooth cursor movement and automatic typing
- Link shortener with a random or custom code (e.g. `bmbaik.ir/s/aB3xY9`)
- Simple anti-spam: honeypot, form timestamp signature, and rate limiting
- Automatic storage: SQLite, falling back to a JSON file (ideal for shared hosting)
- No Composer or MySQL required

## Installation on shared hosting

1. Upload all files to the `public_html` directory of the `bmbaik.ir` domain (or any subfolder, such as `/ai`).
2. Set write permissions on the `data` folder to `755` or `775`.
3. Change the `secret` value in `config.php` (the site name, domain, and footer text can also be changed there).
4. Done! PHP 7.4 or later is all you need.

## About 404 errors on `/s/CODE` links

- The `.htaccess` file routes `…/s/CODE` to `s.php?c=CODE`, and the rules are relative, so it also works in a subfolder (e.g. `http://localhost/ai/`).
- If `mod_rewrite` is unavailable, the app automatically builds links in the `…/s.php?c=CODE` form.
- Links in the `…/index.php?c=CODE` form are supported as well.

### Example Nginx configuration

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ ^/s/([A-Za-z0-9_-]{3,32})$ {
    try_files $uri /s.php?c=$1;
}
```

## Project structure

```
index.php            Home page + link creation
s.php                Playback page (cursor + typing + redirect)
config.php           Settings (site name, domain, footer text, typing speed, etc.)
lib/                 Helper functions and storage
partials/            Shared page templates (header, text box, footer)
assets/              CSS / JS / logo
data/                Link database (protected)
```

## Inspiration

Inspired by [bmbgk.ir](https://bmbgk.ir/) and [jadijadi/re-lmgtfy](https://github.com/jadijadi/re-lmgtfy).

> This project is independent and has no official affiliation with OpenAI; the logo is used only for visual resemblance.
