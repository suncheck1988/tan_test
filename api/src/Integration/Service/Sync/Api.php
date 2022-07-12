<?php

declare(strict_types=1);

namespace App\Integration\Service\Sync;

use App\Application\Exception\DomainException;
use App\Application\ValueObject\Amount;
use App\Application\ValueObject\Quantity;
use App\Application\ValueObject\Uuid;
use App\Data\RedisWrapper;
use App\Integration\Dto\Sync\ProductDto;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Throwable;

use function json_decode;

class Api
{
    private string $host;
    private ?Client $client = null;

    public function __construct(
        private RedisWrapper $redis,
        private LoggerInterface $logger
    ) {
        $this->host = (string)getenv('SYNC_API_HOST');
    }

    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     */
    public function getProduct(string $productId): ProductDto
    {
        $product = null;
        $key = 'product_' . $productId;

        if ($this->redis->exists($key)) {
            $json = (string)$this->redis->get($key);
            /** @var array $data */
            $product = json_decode($json, true);
        }

        if ($product === null) {
            try {
//                $product = $this->request(
//                    'GET',
//                    '/v1/product/' . $productId . '/sync',
//                    []
//                );

                $products = [];

                $products['00000000-0000-0000-0000-000000000001'] = [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'name' => 'Product 1',
                    'price' => 5,
                    'quantity' => 10,
                    'is_active' => true,
                ];
                $products['00000000-0000-0000-0000-000000000002'] = [
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'name' => 'Product 2',
                    'price' => 5,
                    'quantity' => 10,
                    'is_active' => true,
                ];
                $products['00000000-0000-0000-0000-000000000003'] = [
                    'id' => '00000000-0000-0000-0000-000000000003',
                    'name' => 'Product 2',
                    'price' => 5,
                    'quantity' => 10,
                    'is_active' => true,
                ];

                $product = $products[$productId];

                $this->redis->set($key, json_encode($product), ['ex' => 60]);
            } catch (Throwable $e) {
                if ($e instanceof ClientException) {
                    $messArr = json_decode($e->getResponse()->getBody()->getContents(), true);
                    if (isset($messArr['errors'][0]['message'])) {
                        throw new DomainException($messArr['errors'][0]['message']);
                    } else {
                        throw new DomainException('Undefined error');
                    }
                }
            }
        }

        /**
         * @var array{
         *     id: string,
         *     name: string,
         *     price: float,
         *     quantity: int,
         *     is_active: bool,
         * } $product
         */
        return new ProductDto(
            new Uuid($product['id']),
            $product['name'],
            Amount::fromRub($product['price']),
            new Quantity($product['quantity']),
            $product['is_active']
        );
    }

    private function request(string $method, string $endpoint, ?array $data = null): array
    {
        $response = $this->getClient()
            ->request(
                $method,
                $endpoint,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer 12345',
                    ],
                    'json' => $data,
                ]
            );

        $response = (string)$response->getBody();

        $this->logger->debug('sync request', [
            'method' => $method,
            'url' => $this->host . $endpoint,
            'request' => $data,
            'response' => $response,
        ]);

        /** @var array $result */
        $result = json_decode($response, true);

        return $result;
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client(['base_uri' => $this->host]);
        }

        return $this->client;
    }
}
