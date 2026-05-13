# Jarchi AI Agent Guide

## Architecture Overview
Jarchi is a lightweight PHP webhook handler that forwards notifications from GitLab, GitHub, Jira, Crisp, and Sentry to Telegram, Bale, or Rocket.Chat bots. No framework; custom PSR-4 autoloading with helpers in `app/hepers.php`.

- **Entry Point**: `public/index.php` routes by `?provider=` param, parses payloads, formats messages, sends via `BotManager`.
- **Core Flow**: Webhook data → Parser (`app/Webhooks/`) → MessageDTO → Bot Driver (`app/Bots/Drivers/`) → API call.
- **Config**: Dot-notation via `ConfigProvider` (`app/Providers/ConfigProvider.php`), loaded from `config/` files and `.env`.
- **Debugging**: All payloads saved to `storage/` as JSON (e.g., `gitlab_abc123.json`); enable with `DEBUG=true` in `.env`.

## Key Patterns
- **Webhook Parsers**: Classes in `app/Webhooks/` take `object $data`, implement `parseMessage(): string` with HTML formatting (e.g., `<b>user</b> pushed to <a href="...">repo</a>`).
- **Bot Drivers**: Extend `Base` (`app/Bots/Drivers/Base.php`), implement `sendMessage(MessageDTO $dto)` using `postRequest()` with Guzzle.
- **Routing**: Use `match()` in `public/index.php` for provider handling; add new providers by extending cases and parsers.
- **Config Access**: Use `config('key.subkey')` helper; env vars via `env('KEY')`.
- **Payload Storage**: Always saved via `str_random($provider)` for inspection; disable by commenting line 28 in `public/index.php`.

## Workflows
- **Local Dev**: Run `php -S localhost:8000 -t public/`; expose publicly with `expose share http://jarchi.local:80 --server-host=lt.iranserver.dev --server-port=8080 --subdomain=jarchi --auth=mahdi.bagheri:password` (see `expose.yml`).
- **Env Setup**: Copy `.env.example` to `.env`; set tokens/chat IDs.
- **Adding Providers**: Create parser in `app/Webhooks/`, add case in `public/index.php`, config in `config/webhooks.php`.
- **Bot Support**: Add driver in `app/Bots/Drivers/`, config in `config/bot.php`, factory in `BotManager.php`.
- **No Tests**: Manual testing via webhooks; inspect saved JSON in `storage/` for payload structure.

## Examples
- **Add Jira Issue Parser**: In `app/Webhooks/Jira.php`, format: `"<b>{$data->issue->fields->summary}</b> updated by {$data->user->displayName}"`.
- **Telegram Send**: `postRequest('https://api.telegram.org/bot'.$token.'/sendMessage', ['chat_id' => $dto->chatId, 'text' => $dto->text, 'parse_mode' => 'HTML'])`.
- **Config Usage**: `config('bot.drivers.telegram.token')` pulls from `.env` TELEGRAM_TOKEN.</content>
<parameter name="filePath">C:\Users\mahdi.bagheri\Projects\jarchi\AGENTS.md
