<?php

declare(strict_types=1);

namespace Tests;

use App\Webhooks\Sentry;

class SentryWebhookTest extends TestCase
{
    public function testFormatSentryEventForTelegram(): void
    {
        $mockData = [
            'data' => [
                'error' => [
                    'project' => 'Test Project',
                    'exception' => [
                        'values' => [
                            [
                                'type' => 'TestError',
                                'value' => 'This is a test error.',
                                'stacktrace' => [
                                    'frames' => [
                                        [
                                            'abs_path' => '/path/to/file.php',
                                            'filename' => 'file.php',
                                            'lineno' => 42,
                                            'in_app' => true
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'environment' => 'production',
                    'datetime' => '2026-05-18T12:00:00Z',
                    'web_url' => 'https://sentry.io/test-event'
                ]
            ]
        ];

        $sentry = new Sentry($mockData);
        $result = $sentry->formatSentryEventForTelegram();

        $this->assertStringContainsString('Test Project', $result);
        $this->assertStringContainsString('TestError', $result);
        $this->assertStringContainsString('This is a test error.', $result);
        $this->assertStringContainsString('file.php', $result);
        $this->assertStringContainsString('line 42', $result);
        $this->assertStringContainsString('production', $result);
        $this->assertStringContainsString('https://sentry.io/test-event', $result);
    }
}
