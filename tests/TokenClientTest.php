<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class TokenClientTest extends TestCase
{
    private TokenClient $subject;

    private MockHandler $mock;

    private array $requests;

    protected function setUp(): void
    {
        $this->requests = [];
        $this->mock = new MockHandler();
        $handlerStack = HandlerStack::create($this->mock);
        $handlerStack->push(Middleware::history($this->requests));
        $client = new GuzzleClient(['handler' => $handlerStack]);
        $this->subject = new TokenClient('some-client-id', 'some-secret', $client);
    }

    /**
     * @test
     */
    public function authenticate_sends_correct_request()
    {
        $this->mock->append(new Response(200, [], $this->getAccessTokenAsJson()));

        $this->subject->authenticate();

        /** @var RequestInterface $request */
        $request = $this->requests[0]['request'];
        parse_str((string) $request->getBody(), $params);
        $this->assertEquals('https://auth.lemon.markets/oauth2/token', $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('some-client-id', $params['client_id']);
        $this->assertEquals('some-secret', $params['client_secret']);
        $this->assertEquals('client_credentials', $params['grant_type']);
    }

    /**
     * @test
     */
    public function authenticate_decodes_response()
    {
        $this->mock->append(new Response(200, [], $this->getAccessTokenAsJson()));

        $token = $this->subject->authenticate();

        $this->assertEquals('some-access-token', $token->accessToken);
        $this->assertEquals(2591999, $token->expiresIn);
        $this->assertEquals('portfolio:read space:f28a807d-ea5f-4235-9b75-dff62e3dd529', $token->scope);
        $this->assertEquals('bearer', $token->tokenType);
        $this->assertEquals('f28a807d-ea5f-4235-9b75-dff62e3dd529', $token->spaceUuid);
    }

    private function getAccessTokenAsJson(): string
    {
        return trim('
            {
                "access_token": "some-access-token",
                "expires_in": 2591999,
                "scope": "portfolio:read space:f28a807d-ea5f-4235-9b75-dff62e3dd529",
                "token_type": "bearer"
            }
        ');
    }
}
