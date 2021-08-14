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
        $this->mock->append(new Response(200, [], '{}'));

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
    public function getSpaces_sends_correct_request()
    {
        $body = <<<BODY
            {
             "access_token": "some-access-token",
             "expires_in": 2591999,
             "scope": "some-scope",
             "token_type": "bearer"
            }
        BODY;

        $this->mock->append(new Response(200, [], $body));

        $token = $this->subject->authenticate();
        $this->assertEquals('some-access-token', $token->accessToken);
        $this->assertEquals(2591999, $token->expiresIn);
        $this->assertEquals('some-scope', $token->scope);
        $this->assertEquals('bearer', $token->tokenType);
    }
}
