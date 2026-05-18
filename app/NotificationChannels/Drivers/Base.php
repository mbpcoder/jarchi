<?php
declare(strict_types=1);

namespace App\Drivers\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Drivers\DTOs\MessageDTO;

abstract class Base
{
    protected Client $client;

    public function __construct(protected array $config = [])
    {
        $this->client = new Client();
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
