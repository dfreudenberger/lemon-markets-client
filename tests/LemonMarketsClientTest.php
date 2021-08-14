<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use JMS\Serializer\SerializationContext;
use LemonMarketsClient\Model\Request\PlaceOrderCommand;
use LemonMarketsClient\Model\Response\AccessToken;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;

class LemonMarketsClientTest extends TestCase
{
    private static string $accessToken = '0a1349be-2d87-4540-a2b6-e3e4c21b5adb';

    private LemonMarketsClient $subject;

    private MockHandler $mock;

    private array $requests;

    protected function setUp(): void
    {
        $this->requests = [];
        $this->mock = new MockHandler();
        $handlerStack = HandlerStack::create($this->mock);
        $handlerStack->push(Middleware::history($this->requests));
        $client = new GuzzleClient([
            'base_uri' => 'https://api.example.org',
            'handler' => $handlerStack,
        ]);

        $this->subject = new LemonMarketsClient($this->mockTokenCache(), $client);
    }

    /**
     * @test
     */
    public function getSpaces_sends_correct_request_and_decodes_response()
    {
        $response = $this->mockResponse('get-spaces.json');

        $spaces = $this->subject->getSpaces(['limit' => 50]);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces?limit=50', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($spaces, $response);
    }

    /**
     * @test
     */
    public function getSpace_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $response = $this->mockResponse('get-space.json');

        $spaces = $this->subject->getSpace($spaceUuid);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid, $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($spaces, $response);
    }

    /**
     * @test
     */
    public function getSpaceState_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $response = $this->mockResponse('get-space-state.json');

        $spaceState = $this->subject->getSpaceState($spaceUuid);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/state', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($spaceState, $response);
    }

    /**
     * @test
     */
    public function getOrders_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $response = $this->mockResponse('get-orders.json');

        $orders = $this->subject->getOrders($spaceUuid, ['side' => 'buy']);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/orders?side=buy', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($orders, $response);
    }

    /**
     * @test
     */
    public function getOrder_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $orderUuid = Uuid::uuid4();
        $response = $this->mockResponse('get-order.json');

        $order = $this->subject->getOrder($spaceUuid, $orderUuid);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/orders/' . $orderUuid, $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($order, $response);
    }

    /**
     * @test
     */
    public function placeOrder_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $response = $this->mockResponse('place-order.json');
        $command = new PlaceOrderCommand(
            isin: 'US29786A1060',
            validUntil: strval(time() + 3600),
            side: PlaceOrderCommand::SIDE_BUY,
            quantity: 1
        );

        $order = $this->subject->placeOrder($spaceUuid, $command);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/orders', $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->compareJson($order, $response);
    }

    /**
     * @test
     */
    public function activateOrder_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $orderUuid = Uuid::uuid4();
        $response = $this->mockResponse('activate-order.json');

        $activation = $this->subject->activateOrder($spaceUuid, $orderUuid);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/orders/' . $orderUuid . '/activate', $request->getUri());
        $this->assertEquals('PUT', $request->getMethod());
        $this->compareJson($activation, $response);
    }

    /**
     * @test
     */
    public function deleteOrder_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $orderUuid = Uuid::uuid4();
        $this->mock->append(new Response(204));

        $this->subject->deleteOrder($spaceUuid, $orderUuid);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/orders/' . $orderUuid, $request->getUri());
        $this->assertEquals('DELETE', $request->getMethod());
    }

    /**
     * @test
     */
    public function getPortfolio_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $response = $this->mockResponse('get-portfolio.json');

        $portfolio = $this->subject->getPortfolio($spaceUuid);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/portfolio', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($portfolio, $response);
    }

    /**
     * @test
     */
    public function getTransactions_sends_correct_request_and_decodes_response()
    {
        $spaceUuid = Uuid::uuid4();
        $response = $this->mockResponse('get-transactions.json');

        $transactions = $this->subject->getTransactions($spaceUuid, ['limit' => 50]);

        $request = $this->getSentRequest();
        $this->assertEquals('https://api.example.org/rest/v1/spaces/' . $spaceUuid . '/transactions?limit=50', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($transactions, $response);
    }

    /**
     * @test
     */
    public function getVenues_sends_correct_request_and_decodes_response()
    {
        $response = $this->mockResponse('get-venues.json');

        $venues = $this->subject->getVenues(['mic' => 'mic1,mic2']);

        $request = $this->getSentRequest();
        $this->assertEquals('https://paper-data.lemon.markets/v1/venues?mic=mic1%2Cmic2', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($venues, $response);
    }

    /**
     * @test
     */
    public function getInstruments_sends_correct_request_and_decodes_response()
    {
        $response = $this->mockResponse('get-instruments.json');

        $instruments = $this->subject->getInstruments(['search' => 'US29786A1060', 'type' => 'stock']);

        $request = $this->getSentRequest();
        $this->assertEquals('https://paper-data.lemon.markets/v1/instruments?search=US29786A1060&type=stock', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($instruments, $response);
    }

    /**
     * @test
     */
    public function getQuotes_sends_correct_request_and_decodes_response()
    {
        $response = $this->mockResponse('get-quotes.json');

        $quotes = $this->subject->getQuotes(['search' => 'US29786A1060']);

        $request = $this->getSentRequest();
        $this->assertEquals('https://paper-data.lemon.markets/v1/quotes?search=US29786A1060', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($quotes, $response);
    }

    /**
     * @test
     */
    public function getTrades_sends_correct_request_and_decodes_response()
    {
        $response = $this->mockResponse('get-trades.json');

        $trades = $this->subject->getTrades(['search' => 'US29786A1060']);

        $request = $this->getSentRequest();
        $this->assertEquals('https://paper-data.lemon.markets/v1/trades?search=US29786A1060', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->compareJson($trades, $response);
    }

    private function getSentRequest(): RequestInterface
    {
        return $this->requests[0]['request'];
    }

    private function mockTokenCache(): TokenCache
    {
        $token = new AccessToken();
        $token->accessToken = 'some-access-token';

        $tokenCache = $this->createMock(TokenCache::class);
        $tokenCache->expects($this->any())
            ->method('getToken')
            ->willReturn($token);

        return $tokenCache;
    }

    private function loadFile(string $filename): string
    {
        $path = sprintf('%s/MockResponse/%s', __DIR__, $filename);

        return file_get_contents(realpath($path));
    }

    private function compareJson(mixed $object, ResponseInterface $response)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);
        $serialized = $this->subject->getSerializer()->serialize($object, 'json', $context);
        $json = (string) $response->getBody();

        $this->assertEquals(json_decode($json), json_decode($serialized));
    }

    protected function mockResponse(string $filename): ResponseInterface
    {
        $response = new Response(200, [], $this->loadFile($filename));
        $this->mock->append($response);

        return $response;
    }
}
