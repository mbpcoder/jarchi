<?php
declare(strict_types=1);

namespace App\Bots\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Bots\DTOs\MessageDTO;

abstract class Base
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->client = new Client();
        $this->config = $config;
    }

    abstract public function sendMessage(MessageDTO $dto): bool;

    protected function postRequest(string $url, array $data): array
    {
        try {
            $response = $this->client->post($url, [
                'json' => $data,
                'verify' => false,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            throw new \Exception('Request failed: ' . $e->getMessage());
        }
    }
}
