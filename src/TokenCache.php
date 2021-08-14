<?php
declare(strict_types=1);

namespace LemonMarketsClient;

use LemonMarketsClient\Model\Response\AccessToken;

class TokenCache
{
    private static int $safetyBufferInSecs = 10;

    private ?AccessToken $token = null;

    private ?int $expiresAt = null;

    public function __construct(private TokenClient $tokenClient)
    {
    }

    public function getToken(): AccessToken
    {
        if (null === $this->expiresAt || $this->expiresAt <= time()) {
            $this->token = $this->tokenClient->authenticate();
            $this->expiresAt = time() + $this->token->expiresIn - self::$safetyBufferInSecs;
        }

        return $this->token;
    }
}
