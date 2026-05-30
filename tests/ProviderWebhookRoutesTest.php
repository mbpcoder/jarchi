<?php

declare(strict_types=1);

namespace Tests;

class ProviderWebhookRoutesTest extends TestCase
{
    public function testWebhookRoutesAreRegistered(): void
    {
        $routes = [
            '/webhooks/gitlab',
            '/webhooks/github',
            '/webhooks/jira',
            '/webhooks/crisp',
            '/webhooks/sentry',
            '/webhooks/trello',
            '/webhook',
        ];

        $registeredRoutes = collect(app('router')->getRoutes())
            ->pluck('uri')
            ->toArray();

        foreach ($routes as $route) {
            $this->assertContains($route, $registeredRoutes, "Route {$route} not found");
        }
    }

    public function testGitlabWebhookController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\GitlabController'));
    }

    public function testGithubWebhookController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\GithubController'));
    }

    public function testJiraWebhookController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\JiraController'));
    }

    public function testCrispWebhookController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\CrispController'));
    }

    public function testSentryWebhookController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\SentryController'));
    }

    public function testTrelloWebhookController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\TrelloController'));
    }

    public function testWebhookBaseController(): void
    {
        $this->assertTrue(class_exists('App\Http\Controllers\Webhooks\WebhookBaseController'));
    }
}

