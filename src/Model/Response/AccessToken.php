<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use InvalidArgumentException;
use JMS\Serializer\Annotation\PostDeserialize;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AccessToken
{
    public string $accessToken;

    public int $expiresIn;

    public string $scope;

    public string $tokenType;

    public UuidInterface $spaceUuid;

    #[PostDeserialize]
    public function initSpaceUuid()
    {
        if (!preg_match('#space:([A-F0-9\-]{36})#i', $this->scope, $matches)) {
            throw new InvalidArgumentException('Missing space in scope of the access token.');
        }

        $this->spaceUuid = Uuid::fromString($matches[1]);
    }
}
