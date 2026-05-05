<?php
declare(strict_types=1);

return [
    'gitlab' => [
        'chat_id' => env('GITLAB_CHAT_ID'),
    ],
    'github' => [
        'chat_id' => env('GITHUB_CHAT_ID'),
    ],
    'jira' => [
        'chat_id' => env('JIRA_CHAT_ID'),
    ],
    'crisp' => [
        'chat_id' => env('CRISP_CHAT_ID'),
        'topic_id' => env('CRISP_TOPIC_ID'),
    ],
    'sentry' => [
        'chat_id' => env('SENTRY_CHAT_ID'),
    ],
];
