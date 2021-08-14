<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use GuzzleHttp\Client as GuzzeClient;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Serializer as JsonSerializer;
use JMS\Serializer\SerializerBuilder;
use Mhujer\JmsSerializer\Uuid\UuidSerializerHandler;

abstract class AbstractClient
{
    public function __construct(
        protected ?GuzzeClient    $httpClient,
        protected ?JsonSerializer $serializer,
    ) {
        $this->httpClient = $httpClient ?? $this->createDefaultClient();
        $this->serializer = $serializer ?? $this->createDefaultSerializer();
    }

    public function getSerializer(): ?JsonSerializer
    {
        return $this->serializer;
    }

    protected function deserialize(string $data, string $type): mixed
    {
        return $this->serializer->deserialize($data, $type, 'json');
    }

    private function createDefaultClient(): GuzzeClient
    {
        return new GuzzeClient([
            'base_uri' => 'https://paper-trading.lemon.markets',
            'timeout' => 2.0,
        ]);
    }

    private function createDefaultSerializer(): JsonSerializer
    {
        return SerializerBuilder::create()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new UuidSerializerHandler());
            })
            ->build();
    }
}
