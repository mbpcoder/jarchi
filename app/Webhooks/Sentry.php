<?php
declare(strict_types=1);

namespace App\Webhooks;

class Sentry
{
    public function __construct(private $data)
    {
    }

    /**
     * Parse Sentry error event from webhook JSON and format a concise, helpful message.
     *
     * @return string Formatted message for messaging platforms.
     */
    public function formatSentryEventForTelegram(): string
    {
        $payload = is_array($this->data) ? $this->data : json_decode(json_encode($this->data), true);
        $event = $payload['data']['error'] ?? [];

        if (empty($event)) {
            return "⚠️ Sentry: Received an empty or invalid event payload.";
        }

        // --- Extract key information ---
        $project = $event['project'] ?? 'Unknown';
        $errorType = $event['exception']['values'][0]['type'] ?? 'Unknown Error';
        $errorValue = $event['exception']['values'][0]['value'] ?? 'No description';
        $environment = $event['environment'] ?? 'unknown';
        
        // DateTime formatting
        $dateTimeString = $event['datetime'] ?? null;
        $dateTime = $dateTimeString ? date('Y-m-d H:i:s', strtotime((string) $dateTimeString)) : 'unknown';

        // --- Extract file and line from stack trace (prefer in_app frames) ---
        $file = null;
        $line = null;
        $stacktrace = $event['exception']['values'][0]['stacktrace']['frames'] ?? [];
        
        // First, try to find an in_app frame
        foreach (array_reverse($stacktrace) as $frame) {
            if ($frame['in_app'] ?? false) {
                $file = basename($frame['abs_path'] ?? $frame['filename'] ?? '');
                $line = $frame['lineno'] ?? null;
                break;
            }
        }
        
        // If no in_app frame, use the last frame
        if (!$file && !empty($stacktrace)) {
            $lastFrame = end($stacktrace);
            $file = basename((string) ($lastFrame['abs_path'] ?? $lastFrame['filename'] ?? ''));
            $line = $lastFrame['lineno'] ?? null;
        }

        // --- Request details ---
        $requestUrl = $event['request']['url'] ?? 'N/A';
        $requestMethod = $event['request']['method'] ?? 'GET';
        $userIp = $this->extractUserIp($event) ?? 'N/A';

        // --- Web URL ---
        $webUrl = $event['web_url'] ?? 'https://sentry.io';

        // --- Build concise message ---
        $message = "<b>🚨 Sentry Error</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "<b>Project:</b> {$project}\n";
        $message .= "<b>Time:</b> {$dateTime}\n";
        $message .= "<b>Error:</b> {$errorType}\n";
        $message .= "<b>Message:</b> {$errorValue}\n";
        
        if ($file) {
            $message .= "<b>File:</b> {$file}";
            if ($line) {
                $message .= " (line {$line})";
            }
            $message .= "\n";
        }
        
        $message .= "<b>Request:</b> {$requestMethod} " . $this->truncateUrl($requestUrl, 40) . "\n";
        $message .= "<b>User IP:</b> {$userIp}\n";
        $message .= "<b>Environment:</b> {$environment}\n";
        $message .= "━━━━━━━━━━━━━━━━━\n";
        $message .= "<a href=\"{$webUrl}\">🔗 View in Sentry</a>\n";

        return $message;
    }

    public function getActionUrl(): string
    {
        $payload = is_array($this->data) ? $this->data : json_decode(json_encode($this->data), true);
        return $payload['data']['error']['web_url'] ?? 'https://sentry.io';
    }

    public function getActionLabel(): string
    {
        return 'View in Sentry';
    }

    /**
     * Extract user IP from the event
     */
    private function extractUserIp(array $event): ?string
    {
        // Try to get from request headers
        $headers = $event['request']['headers'] ?? [];
        if (is_array($headers)) {
            foreach ($headers as $header) {
                if (is_array($header) && count($header) >= 2) {
                    if (strtolower((string) $header[0]) === 'x-forwarded-for') {
                        return trim(explode(',', (string) $header[1])[0]);
                    }
                }
            }
        }
        
        // Try to get from user context
        $contexts = $event['contexts'] ?? [];
        if (isset($contexts['request']['env']['REMOTE_ADDR'])) {
            return $contexts['request']['env']['REMOTE_ADDR'];
        }
        
        // Try from tags
        $tags = $event['tags'] ?? [];
        foreach ($tags as $tag) {
            if (is_array($tag) && isset($tag[0]) && $tag[0] === 'ip' && isset($tag[1])) {
                return $tag[1];
            }
        }
        
        return null;
    }

    /**
     * Truncate URL to a maximum length
     */
    private function truncateUrl(string $url, int $maxLength = 40): string
    {
        if (strlen($url) <= $maxLength) {
            return $url;
        }
        return substr($url, 0, $maxLength - 3) . '...';
    }
}
