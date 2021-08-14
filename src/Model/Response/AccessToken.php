<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

class AccessToken
{
    public string $accessToken;

    public int $expiresIn;

    public string $scope;

    public string $tokenType;
}
