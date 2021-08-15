<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use DomainException;
use GuzzleHttp\Client as GuzzeClient;
use GuzzleHttp\Exception\ClientException;
use JMS\Serializer\Serializer as JsonSerializer;
use LemonMarketsClient\Model\Request\PlaceOrderCommand;
use LemonMarketsClient\Model\Response\Instruments;
use LemonMarketsClient\Model\Response\Order;
use LemonMarketsClient\Model\Response\OrderActivation;
use LemonMarketsClient\Model\Response\Orders;
use LemonMarketsClient\Model\Response\PlacedOrder;
use LemonMarketsClient\Model\Response\Portfolio;
use LemonMarketsClient\Model\Response\Quotes;
use LemonMarketsClient\Model\Response\Space;
use LemonMarketsClient\Model\Response\Spaces;
use LemonMarketsClient\Model\Response\SpaceState;
use LemonMarketsClient\Model\Response\Trades;
use LemonMarketsClient\Model\Response\Transactions;
use LemonMarketsClient\Model\Response\Venues;
use Ramsey\Uuid\UuidInterface;

class LemonMarketsClient extends AbstractClient
{
    public function __construct(
        private TokenCache $tokenCache,
        ?GuzzeClient       $httpClient = null,
        ?JsonSerializer    $serializer = null
    ) {
        parent::__construct($httpClient, $serializer);
    }

    public function getSpaces(array $params = []): Spaces
    {
        $body = $this->request('get', '/rest/v1/spaces', ['query' => $params]);

        return $this->deserialize($body, Spaces::class);
    }

    public function getSpace(): Space
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('get', sprintf('/rest/v1/spaces/%s', $spaceUuid));

        return $this->deserialize($body, Space::class);
    }

    public function getSpaceState(): SpaceState
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('get', sprintf('/rest/v1/spaces/%s/state', $spaceUuid));

        return $this->deserialize($body, SpaceState::class);
    }

    public function getOrders(array $params = []): Orders
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('get', sprintf('/rest/v1/spaces/%s/orders', $spaceUuid), ['query' => $params]);

        return $this->deserialize($body, Orders::class);
    }

    public function getOrder(UuidInterface $orderUuid): Order
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('get', sprintf('/rest/v1/spaces/%s/orders/%s', $spaceUuid, $orderUuid));

        return $this->deserialize($body, Order::class);
    }

    public function placeOrder(PlaceOrderCommand $command): PlacedOrder
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('post', sprintf('/rest/v1/spaces/%s/orders', $spaceUuid), [
            'form_params' => $command->toArray(),
        ]);

        return $this->deserialize($body, PlacedOrder::class);
    }

    public function activateOrder(UuidInterface $orderUuid): OrderActivation
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('put', sprintf('/rest/v1/spaces/%s/orders/%s/activate', $spaceUuid, $orderUuid));

        return $this->deserialize($body, OrderActivation::class);
    }

    public function deleteOrder(UuidInterface $orderUuid): void
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;

        try {
            $this->request('delete', sprintf('/rest/v1/spaces/%s/orders/%s', $spaceUuid, $orderUuid));
        } catch (ClientException $ex) {
            $body = (string) $ex->getResponse()->getBody();
            $message = $this->deserialize($body, 'array', 'json');
            throw new DomainException($message['message']);
        }
    }

    public function getPortfolio(): Portfolio
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('get', sprintf('/rest/v1/spaces/%s/portfolio', $spaceUuid));

        return $this->deserialize($body, Portfolio::class);
    }

    public function getTransactions(array $params = []): Transactions
    {
        $spaceUuid = $this->tokenCache->getToken()->spaceUuid;
        $body = $this->request('get', sprintf('/rest/v1/spaces/%s/transactions', $spaceUuid), ['query' => $params]);

        return $this->deserialize($body, Transactions::class);
    }

    public function getVenues(array $params = []): Venues
    {
        $body = $this->request('get', 'https://paper-data.lemon.markets/v1/venues', ['query' => $params]);

        return $this->deserialize($body, Venues::class);
    }

    public function getInstruments(array $params = []): Instruments
    {
        $body = $this->request('get', 'https://paper-data.lemon.markets/v1/instruments', ['query' => $params]);

        return $this->deserialize($body, Instruments::class);
    }

    public function getQuotes(array $params = []): Quotes
    {
        $body = $this->request('get', 'https://paper-data.lemon.markets/v1/quotes', ['query' => $params]);

        return $this->deserialize($body, Quotes::class);
    }

    public function getTrades(array $params = []): Trades
    {
        $body = $this->request('GET', 'https://paper-data.lemon.markets/v1/trades', ['query' => $params]);

        return $this->deserialize($body, Trades::class);
    }

    private function request(string $method, string $uri, array $options = []): string
    {
        $options['headers'] = array_merge($options['headers'] ?? [], [
            'Authorization' => sprintf('Bearer %s', $this->tokenCache->getToken()->accessToken),
        ]);

        $response = $this->httpClient->request($method, $uri, $options);

        return (string) $response->getBody();
    }
}
