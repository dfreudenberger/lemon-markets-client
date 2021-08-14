<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use LemonMarketsClient\Model\Response\AccessToken;
use PHPUnit\Framework\TestCase;

class TokenCacheTest extends TestCase
{
    public static int $timestampOffset;

    private TokenClient $tokenClient;

    private TokenCache $subject;

    protected function setUp(): void
    {
        self::$timestampOffset = 0;
        $this->tokenClient = $this->createMock(TokenClient::class);
        $this->subject = new TokenCache($this->tokenClient);
    }

    /**
     * @test
     */
    public function getToken_requests_token_when_called_for_the_first_time()
    {
        $expectedToken = $this->createToken();
        $this->tokenClient->expects($this->once())
            ->method('authenticate')
            ->willReturn($expectedToken);

        $token = $this->subject->getToken();

        $this->assertSame($expectedToken, $token);
    }

    /**
     * @test
     */
    public function getToken_returns_same_token_when_token_is_still_valid()
    {
        $expectedToken = $this->createToken();
        $this->tokenClient->expects($this->once())
            ->method('authenticate')
            ->willReturn($expectedToken);

        $token1 = $this->subject->getToken();
        $token2 = $this->subject->getToken();

        $this->assertSame($expectedToken, $token1);
        $this->assertSame($expectedToken, $token2);
    }

    /**
     * @test
     */
    public function getToken_returns_new_token_when_token_is_expired()
    {
        $expiredToken = $this->createToken(-5);
        $newToken = $this->createToken();

        $this->tokenClient->expects($this->exactly(2))
            ->method('authenticate')
            ->willReturn($expiredToken, $newToken);

        $token1 = $this->subject->getToken();
        $token2 = $this->subject->getToken();

        $this->assertSame($expiredToken, $token1);
        $this->assertSame($newToken, $token2);
    }

    /**
     * @test
     */
    public function getToken_returns_new_token_when_token_is_about_to_expire()
    {
        // token that expires within the next 10 seconds will be considered as expired
        $aboutToExpire = $this->createToken(9);
        $newToken = $this->createToken();

        $this->tokenClient->expects($this->exactly(2))
            ->method('authenticate')
            ->willReturn($aboutToExpire, $newToken);

        $token1 = $this->subject->getToken();
        $token2 = $this->subject->getToken();

        $this->assertSame($aboutToExpire, $token1);
        $this->assertSame($newToken, $token2);
    }

    private function createToken(int $expiresIn = 30): AccessToken
    {
        $token = new AccessToken();
        $token->expiresIn = $expiresIn;

        return $token;
    }
}
