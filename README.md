# notify-telegram-bot

Simple script to get notification from Gitlab, Jira, Github... push events in telegram

Just add your telegram bot token and chat id.

![image](https://user-images.githubusercontent.com/3877538/235918842-beceed5d-de55-43c1-8324-a6497f4d8edb.png)



## Available Notifications

### GitLab

 ✅ Commit push  
 
### Jira

✅ Issues

### Coming Soon 

⏳ Trello

⏳ Github


expose share http://jarchi.local:80 --server-host=5.182.46.55 --server-port=8080 --subdomain=jarchi --auth=mahdi.bagheri

## Upgrade to PHP 8.5

To upgrade this project to PHP 8.5:

- Update `composer.json` to require PHP `^8.5` (already applied).
- Run Composer to refresh dependencies:

```bash
composer update --no-interaction
```

- Run the test suite to verify everything passes:

```bash
php vendor/bin/phpunit
```

If your local PHP is older than 8.5, you can use Docker or WSL to run the commands with PHP 8.5:

```bash
docker run --rm -v "%CD%":/app -w /app php:8.5-cli composer update --no-interaction
docker run --rm -v "%CD%":/app -w /app php:8.5-cli php vendor/bin/phpunit
```

If you want, I can add platform-specific installation steps for Windows, WSL, or CI changes.