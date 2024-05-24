<?php

namespace App\Service\ExternalApi;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpOptions;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RestfulApiDev
{
    private const TIMEOUT = 5;
    private const BASE_URL = 'https://api.restful-api.dev/';

    private HttpClientInterface $client;
    private LoggerInterface $logger;

    public function __construct(
        HttpClientInterface $client,
        LoggerInterface     $logger
    )
    {
        $this->client = $client->withOptions(
            (new HttpOptions())
                ->setBaseUri($this::BASE_URL)
                ->setTimeout($this::TIMEOUT)
                ->toArray()
        );
        $this->logger = $logger;
    }

    public function getAllProducts(): ?array
    {
        try {
            $response = $this->client->request('GET', 'objects')->toArray();

            if (empty($response))
                throw new Exception('Fetching products error');

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function addProduct(): ?array
    {
        try {
            $response = $this->client->request('POST', 'objects', [
                'json' => [
                    'name' => 'Apple MacBook Pro 16',
                    'data' => [
                        'year' => 2019,
                        'price' => 81849.99,
                        'color' => 'green'
                    ]
                ],
            ])->toArray();

            if (empty($response))
                throw new Exception('Adding product error');

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }
}
